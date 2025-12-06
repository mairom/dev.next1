<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
class Deposits extends BaseController
{
	function __construct()
	{
		parent::loadModels();
		$path = request()->getUri()->getSegment(1);
		if (!$this->Privileges_Model->has_privilege($path)) {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to('panel/');
		}
	}

	function index()
	{
		$data['title'] = lang('x_menu_deposits');
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$data['deposits'] = $this->Deposits_Model->get_all_deposits();
			$data['total_paid'] =  $this->Deposits_Model->total_deposits('1');
			$data['total_unPaid'] = $this->Deposits_Model->total_deposits('0');
			$data['total_internal'] = $this->Deposits_Model->total_deposits('2');
			$data['total_paid_num'] = $this->Deposits_Model->total_deposit_num('1');
			$data['total_unPaid_num'] = $this->Deposits_Model->total_deposit_num('0');
			$data['total_internal_num'] = $this->Deposits_Model->total_deposit_num('2');
			$data['fam'] = $this->Deposits_Model->depositsTotalAmount();
		} else {
			$data['deposits'] = $this->Deposits_Model->get_all_deposits(session()->usr_id);
			$data['total_paid'] =  $this->Deposits_Model->total_deposits_by_status(session()->usr_id, '1');
			$data['total_unPaid'] = $this->Deposits_Model->total_deposits_by_status(session()->usr_id, '0');
			$data['total_internal'] = $this->Deposits_Model->total_deposits_by_status(session()->usr_id, '2');
			$data['total_paid_num'] = $this->Deposits_Model->total_deposit_num_by_status('1', session()->usr_id);
			$data['total_unPaid_num'] = $this->Deposits_Model->total_deposit_num_by_status('0', session()->usr_id);
			$data['total_internal_num'] = $this->Deposits_Model->total_deposit_num_by_status('2', session()->usr_id);
			$data['fam'] = $this->Deposits_Model->total_deposits_by_status(session()->usr_id);
		}
		$data['ofx'] = (sizeof($data['deposits']) > 0 ? number_format(($data['total_paid_num'] * 100) / sizeof($data['deposits'])) : 0);
		$data['ofy'] = (sizeof($data['deposits']) > 0 ? number_format(($data['total_unPaid_num'] * 100) / sizeof($data['deposits'])) : 0);
		$data['vgy'] = (sizeof($data['deposits']) > 0 ? number_format(($data['total_internal_num'] * 100) / sizeof($data['deposits'])) : 0);
		return view('deposits/index', $data);
	}

	function get_content($type, $id = null)
	{
		if ($type == 'create') {
			if ($this->Privileges_Model->check_privilege('deposits', $type)) {
				$data['title'] = lang('deposit');
				$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
				$html = view('deposits/create', $data, TRUE);
			} else {
				$html = "<div class='text-center'><br><br><span class='text-danger'>" . lang2('you_dont_have_permission') . "</span></div>";
			}
		}
		echo $html;
	}

	function create()
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$title = request()->getPost('title');
				$category_id = request()->getPost('category');
				$date = request()->getPost('date');
				$customer_id = request()->getPost('customer');
				$account_id = request()->getPost('account');
				$amount = request()->getPost('amount');
				$internal = request()->getPost('internal');
				$internal = ($internal == 'true') ? '2' : '0';
				$recurring_period = request()->getPost('recurring_period');
				$recurring = request()->getPost('recurring');
				$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);
				$hasError = false;
				if ($title == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('title');
				} else if ($category_id == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('category');
				} else if (($customer_id == '') && ($internal == '0')) {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
				} else if (($internal == '2') && (request()->getPost('staff') == '')) {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('staff');
				} else if ($date == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('date');
				} else if ($account_id == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('account');
				} else if (($recurring == '1') && (request()->getPost('recurring_period') == '')) {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('recurring_period');
				} else if (((int)(request()->getPost('totalItems'))) == 0) {
					$hasError = true;
					$data['message'] = lang2('invalid_expense_items_value');
				} else if ($total == 0) {
					$hasError = true;
					$data['message'] = lang2('invalid_items_total');
				}
				if ($hasError) {
					$data['success'] = false;
					return response()->setJSON($data);
				}
				if (!$hasError) {
					$appconfig = get_appconfig();
					if ($internal == '2') {
						$staff = request()->getPost('staff');
						$customer = NULL;
					} else {
						$staff = session()->usr_id;
						$customer = $customer_id;
					}
					$params = array(
						'token' => md5(uniqid()),
						'relation_type' => 'deposit',
						'category_id' => request()->getPost('category'),
						'staff_id' => $staff,
						'customer_id' => request()->getPost('customer'),
						'account_id' => request()->getPost('account'),
						'title' => request()->getPost('title'),
						'date' => $date,
						'created' => date('Y-m-d'),
						'amount' => request()->getPost('total'),
						'total_tax' => request()->getPost('total_tax'),
						'sub_total' => request()->getPost('sub_total'),
						'description' => request()->getPost('description'),
						'status' => $internal,
						'last_recurring' => date('Y-m-d'),
						'deposits_created_by' => session()->usr_id,
					);
					$deposits_id = $this->Deposits_Model->create($params);
					$this->Settings_Model->create_process('pdf', $deposits_id, 'deposit', 'deposit_message');
					if (request()->getPost('recurring') == 'true'  || request()->getPost('recurring') == '1') {
						$SHXparams = array(
							'relation_type' => 'deposit',
							'relation' => $deposits_id,
							'period' => request()->getPost('recurring_period'),
							'end_date' => request()->getPost('end_recurring'),
							'type' => request()->getPost('recurring_type'),
						);
						$recurring_invoices_id = $this->Deposits_Model->recurring_add($SHXparams);
					}
					$data['id'] = $deposits_id;
					$data['success'] = true;
					$data['message'] = lang2('deposit') . ' ' . lang2('createmessage');
					if ($appconfig['deposit_series']) {
						$deposit_number = $appconfig['deposit_series'];
						$deposit_number = $deposit_number + 1;
						$this->Settings_Model->increment_series('deposit_series', $deposit_number);
					}
					return response()->setJSON($data);
				}
			} else {
				$data['title'] = lang2('new') . ' ' . lang2('deposit');
				return view('deposits/create', $data);
			}
		}
	}

	function update($id)
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$data['deposit'] = $this->Deposits_Model->get_deposit_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('deposits', 'own')) {
			$data['deposit'] = $this->Deposits_Model->get_deposit_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
		if ($data['deposit']) {
			// check if the deposits exists before trying to edit it
			if ($this->Privileges_Model->check_privilege('deposits', 'edit')) {
				if (isset($data['deposit']['id'])) {
					if (isset($_POST) && count($_POST) > 0) {
						$title = request()->getPost('title');
						$category_id = request()->getPost('category');
						$customer_id = request()->getPost('customer');
						$account_id = request()->getPost('account');
						$amount = request()->getPost('amount');
						$internal = request()->getPost('internal');
						$internal = ($internal == 'true') ? '2' : '0';
						$date = request()->getPost('date');
						$recurring_period = request()->getPost('recurring_period');
						$recurring_status = request()->getPost('recurring');
						$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);
						$hasError = false;
						if ($title == '') {
							$hasError = true;
							$data['message'] = lang2('invalidmessage') . ' ' . lang2('title');
						} else if ($category_id == '') {
							$hasError = true;
							$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('category');
						} else if ($date == '') {
							$hasError = true;
							$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('date');
						} else if ($account_id == '') {
							$hasError = true;
							$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('account');
						} else if (($recurring_status == 'true') && (request()->getPost('recurring_period') == '')) {
							$hasError = true;
							$data['message'] = lang2('invalidmessage') . ' ' . lang2('recurring_period');
						} else if (request()->getPost('totalItems') < 1) {
							$hasError = true;
							$data['message'] = lang2('invalid_deposit_items');
						} else if ((int)(request()->getPost('totalItems')) == 0) {
							$hasError = true;
							$data['message'] = lang2('invalid_deposit_items_value');
						} else if ($total == 0) {
							$hasError = true;
							$data['message'] = lang2('invalid_items_total');
						}

						if ($hasError) {
							$data['success'] = false;
							return response()->setJSON($data);
						}
						if (!$hasError) {
							if ($internal == '2') {
								$staff = request()->getPost('staff');
								$customer = NULL;
							} else {
								$staff = session()->usr_id;
								$customer = $customer_id;
							}
							$params = array(
								'category_id' => request()->getPost('category'),
								'customer_id' => $customer,
								'staff_id' => $staff,
								'account_id' => request()->getPost('account'),
								'title' => request()->getPost('title'),
								'date' => $date,
								'amount' => request()->getPost('total'),
								'total_tax' => request()->getPost('total_tax'),
								'sub_total' => request()->getPost('sub_total'),
								'last_recurring' => date('Y-m-d'),
								'description' => request()->getPost('description'),
								'pdf_status' => '0',
							);
							$this->Deposits_Model->update_deposit($id, $params);
							if (request()->getPost('recurring') == 'true'  || request()->getPost('recurring') == '1') {
								$SHXparams = array(
									'period' => request()->getPost('recurring_period'),
									'end_date' => request()->getPost('end_recurring'),
									'type' => request()->getPost('recurring_type'),
									'status' => 0,
								);
								$recurring_deposits_id = $this->Deposits_Model->recurring_update($id, $SHXparams);
							} else {
								$SHXparams = array(
									'period' => request()->getPost('recurring_period'),
									'end_date' => request()->getPost('end_recurring'),
									'type' => request()->getPost('recurring_type'),
									'status' => 1,
								);
								$recurring_deposits_id = $this->Deposits_Model->recurring_update($id, $SHXparams);
							}
							if (!is_numeric(request()->getPost('recurring_id')) && (request()->getPost('recurring') == 'true'  || request()->getPost('recurring') == '1')) {
								$SHXparams = array(
									'relation_type' => 'deposit',
									'relation' => $id,
									'period' => request()->getPost('recurring_period'),
									'end_date' => request()->getPost('end_recurring'),
									'type' => request()->getPost('recurring_type'),
								);
								$recurring_deposits_id = $this->Deposits_Model->recurring_add($SHXparams);
							}
							$data['success'] = true;
							$data['message'] = lang2('deposit') . ' ' . lang2('updatemessage');
							$data['id'] = $id;
							return response()->setJSON($data);
						}
					} else {
						$data['title'] = lang2('update') . '' . lang2('deposit');
						return view('deposits/update', $data);
					}
				}
			} else {
				session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
				return redirect()->to('deposits/deposit/' . $id);
			}
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
	}

	function deposit($id)
	{
		$data['title'] = lang('deposit');
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('deposits', 'own')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
		if ($deposit) {
			if (isset($deposit['id'])) {
				if (isset($_POST) && count($_POST) > 0) {
					$params = array(
						'category_id' => request()->getPost('category'),
						'staff_id' => request()->getPost('staff'),
						'customer_id' => request()->getPost('customer'),
						'account_id' => request()->getPost('account'),
						'title' => request()->getPost('title'),
						'date' => _pdate(request()->getPost('date')),
						'created' => request()->getPost('created'),
						'amount' => request()->getPost('amount'),
						'description' => request()->getPost('description'),
					);
					$this->Deposits_Model->update_deposit($id, $params);
					return redirect()->to('deposits/index');
				} else {
					$data['id'] = $deposit['id'];
					return view('deposits/deposit', $data);
				}
			} else {
				show_error('The deposits you are trying to edit does not exist.');
				return redirect()->to('deposits');
			}
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
	}

	function remove($id)
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('deposits', 'own')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id, session()->usr_id);
		} else {
			$data['success'] = false;
			$data['message'] = lang('you_dont_have_permission');
			return response()->setJSON($data);
		}
		if ($deposit) {
			if ($this->Privileges_Model->check_privilege('deposits', 'delete')) {
				// check if the deposits exists before trying to delete it
				if (isset($deposit['id'])) {
					$this->Deposits_Model->delete_deposits($id, get_number('deposits', $id, 'deposit', 'deposit'));
					$this->load->helper('file');
					$folder = './uploads/files/deposits/' . $id;
					if (is_dir($folder)) {
						delete_files($folder, true);
						rmdir($folder);
					}
					$data['success'] = true;
					$data['message'] = lang2('deposit') . ' ' . lang2('deletemessage');
					return response()->setJSON($data);
				} else {
					show_error(lang2('depositerror'));
				}
			} else {
				$data['success'] = false;
				$data['message'] = lang('you_dont_have_permission');
				return response()->setJSON($data);
			}
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
	}

	function add_category()
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$params = array(
					'name' => request()->getPost('name'),
					'description' => request()->getPost('description'),
				);
				$category = $this->Deposits_Model->add_category($params);
				$data['success'] = true;
				$data['message'] = lang2('deposit') . ' ' . lang2('category') . ' ' . lang2('createmessage');
				return response()->setJSON($data);
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}

	function update_category($id)
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'edit')) {
			if (isset($_POST) && count($_POST) > 0) {
				$params = array(
					'name' => request()->getPost('name'),
					'description' => request()->getPost('description'),
				);
				$this->Deposits_Model->update_category($id, $params);
				$data['success'] = true;
				$data['message'] = lang2('deposit') . ' ' . lang2('category') . ' ' . lang2('updatemessage');
				return response()->setJSON($data);
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}

	function remove_category($id)
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'delete')) {
			$depositcategory = $this->Deposits_Model->get_depositcategory($id);
			if (isset($depositcategory['id'])) {
				if ($this->Deposits_Model->check_category($id) == '0') {
					$this->Deposits_Model->delete_category($id);
					$data['success'] = true;
					$data['message'] = lang2('deposit') . ' ' . lang2('category') . ' ' . lang2('deletemessage');
					return response()->setJSON($data);
				} else {
					$data['success'] = false;
					$data['message'] = $data['message'] = lang2('category') . ' ' . lang2('is_linked') . ' ' . lang2('with') . ' ' . lang2('deposit') . ', ' . lang2('so') . ' ' . lang2('cannot_delete') . ' ' . lang2('category');
					return response()->setJSON($data);
				}
			} else {
				show_error('The depositcategory you are trying to delete does not exist.');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}

	function send_deposit_email($id)
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('deposits', 'own')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id, session()->usr_id);
		} else {
			$return['status'] = false;
			$return['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($return);
		}
		if ($deposit) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id);
			$template = $this->Emails_Model->get_template('deposit', 'deposit_message');
			if ($template['status'] == '1') {
				$settings = $this->Settings_Model->get_settings_ciuis();
				if ($deposit['individual']) {
					$customer = $deposit['individual'];
				} else {
					$customer = $deposit['customer'];
				}
				if ($deposit['deposit_status'] == '1') {
					$depositstatus = lang('paid');
					$email = $deposit['customeremail'];
				} else if ($deposit['deposit_status'] == '2') {
					$depositstatus = lang('internal');
					$email = $deposit['staffemail'];
				} else if ($deposit['deposit_status'] == '0') {
					$depositstatus = lang('unpaid');
					$email = $deposit['customeremail'];
				}
				$appconfig = get_appconfig();
				$link = base_url('share/deposit/' . $deposit['token'] . '');
				$message_vars = array(
					'{deposit_number}' => get_number('deposits', $deposit['id'], 'deposit', 'deposit'),
					'{customer_name}' => $customer,
					'{deposit_date}' => $deposit['date'],
					'{deposit_amount}' => $deposit['amount'],
					'{deposit_status}' => $depositstatus,
					'{deposit_link}' => $link,
					'{company_name}' =>  $settings['company'],
					'{company_email}' =>  $settings['email'],
				);
				$subject = strtr($template['subject'], $message_vars);
				$message = strtr($template['message'], $message_vars);
				$param = array(
					'from_name' => $template['from_name'],
					'email' =>  $email,
					'subject' => $subject,
					'message' => $message,
					'created' => date("Y.m.d H:i:s"),
					'status' => '0'
				);
				
				$data = $this->Emails_Model->send_email($email, $template['from_name'], $subject, $message);

				if ($data['success'] == true) {
					$return['status'] = true;
					$return['message'] = $data['message'];
					if ($email) {
						$this->db->table('email_queue')->insert($param);
					}
					return response()->setJSON($return);
				} else {
					$return['status'] = false;
					$return['message'] = lang2('errormessage');
					return response()->setJSON($return);
				}
			}
		} else {
			$return['status'] = false;
			$return['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($return);
		}
	}

	function mark_as_received($id)
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'edit')) {
			$deposit = $this->Deposits_Model->get_deposits($id, '');
			$loggedinuserid = session()->usr_id;
			if (isset($deposit['id'])) {
				$this->db->table('payments')->insert(array(
					'transactiontype' => '0',
					'is_transfer' => '0',
					'deposit_id' => $id,
					'staff_id' => $loggedinuserid,
					'amount' => $deposit['amount'],
					'account_id' => $deposit['account_id'],
					'customer_id' => $deposit['customer_id'],
					'not' => lang2('deposit') . ' ' . lang2('for') . ' ' . '<a href="' . base_url('deposits/deposit/' . $id . '') . '">' . lang('depositprefix') . '- ' . $id . '</a>',
					'date' => _pdate($deposit['date']),
				));
				$this->db->table('deposits')
					->where('id', $id)
					->update(['status' => '1']);

				$data['success'] = true;
				$data['message'] = lang2('paymentaddedsuccessfully');
				return response()->setJSON($data);
			} else {
				show_error(lang2('depositerror'));
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}

	function download_pdf($id)
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('deposits', 'own')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
		if ($deposit) {
			$appconfig = get_appconfig();
			$file_name = '' . get_number('deposits', $id, 'deposit', 'deposit') . '.pdf';
			if (is_file('./uploads/files/deposits/' . $id . '/' . $file_name)) {
				$this->load->helper('file');
				$this->load->helper('download');
				$data = file_get_contents('./uploads/files/deposits/' . $id . '/' . $file_name);
				force_download($file_name, $data);
			} else {
				session()->setFlashdata('ntf4', lang2('filenotexist'));
				return redirect()->to('deposits/deposit/' . $id);
			}
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
	}

	function create_pdf($id)
	{
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('deposits', 'own')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
		if ($deposit) {
			$result = $this->Deposits_Model->generate_pdf($id);
			if ($result == true) {
				$file_name = '' . get_number('deposits', $id, 'deposit', 'deposit') . '.pdf';
				return redirect()->to(base_url('deposits/pdf_generated/'));
			} else {
				return redirect()->to(base_url('expenses/pdf_fault/'));
			}
		}
	}

	function pdf_generated()
	{
		$result = array(
			'status' => true,
		);
		return response()->setJSON($result);
	}

	function pdf_fault()
	{
		$result = array(
			'status' => false,
		);
		return response()->setJSON($result);
	}

	function get_deposit($id)
	{
		$deposit = array();
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('deposits', 'own')) {
			$deposit = $this->Deposits_Model->get_deposit_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
		if ($deposit) {

			$items = $this->db->table('items')
				->select('*')
				->where('relation_type', 'deposit')
				->where('relation', $id)
				->get()
				->getResultArray();
			if ($deposit['recurring_endDate'] != 'Invalid date') {
				$recurring_endDate = date(DATE_ISO8601, strtotime($deposit['recurring_endDate']));
			} else {
				$recurring_endDate = '';
			}
			$appconfig = get_appconfig();
			$data_deposit = array(
				'id' => $deposit['id'],
				'prefix' => $appconfig['deposit_prefix'],
				'longid' => get_number('deposits', $deposit['id'], 'deposit', 'deposit'),
				'title' => $deposit['title'],
				'amount' => $deposit['amount'],
				'date' => date(get_dateFormat(), strtotime($deposit['date'])),
				'date_edit' => $deposit['date'],
				'category' => $deposit['category_id'],
				'created' => date(get_dateFormat(), strtotime($deposit['depositcreate'])),
				'customer' => $deposit['customer_id'],
				'customername' => $deposit['individual'] ? $deposit['individual'] : $deposit['customer'],
				'customeremail' => $deposit['customeremail'],
				'customer_phone' => $deposit['customer_phone'],
				'account' => $deposit['account_id'],
				'invoice_id' => $deposit['invoice_id'],
				'description' => $deposit['desc'],
				'pdf_status' => $deposit['pdf_status'],
				'category_name' => $deposit['category'],
				'staff_name' => $deposit['staff'],
				'staff_id' => $deposit['depositstaff'],
				'account_name' => $deposit['account'],
				'sub_total' => $deposit['sub_total'],
				'total_tax' => $deposit['total_tax'],
				'total' => $deposit['amount'],
				'status' => $deposit['deposit_status'],
				'internal' => $deposit['deposit_status'] == '2' ? true : false,
				'items' => $items,
				'EndRecurring' => $recurring_endDate,
				'recurring_id' => $deposit['recurring_id'],
				'recurring_status' => $deposit['recurring_status'] == '0' ? true : false,
				'recurring_period' => (int)$deposit['recurring_period'],
				'recurring_type' => $deposit['recurring_type'] ? $deposit['recurring_type'] : 0
			);
			return response()->setJSON($data_deposit);
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('deposits'));
		}
	}

	function get_deposits()
	{
		$deposits = array();
		if ($this->Privileges_Model->check_privilege('deposits', 'all')) {
			$deposits = $this->Deposits_Model->get_all_deposits();
		} else if ($this->Privileges_Model->check_privilege('deposits', 'own')) {
			$deposits = $this->Deposits_Model->get_all_deposits(session()->usr_id);
		}
		$data_deposits = array();
		foreach ($deposits as $deposit) {
			if ($deposit['status'] == '1') {
				$billstatus = lang('paid') and $color = 'success';
			} else if ($deposit['status'] == '0') {
				$billstatus = lang('unpaid') and $color = 'danger';
			} else {
				$billstatus = lang('internal') and $color = 'success';
			}
			$appconfig = get_appconfig();
			$data_deposits[] = array(
				'id' => $deposit['id'],
				'title' => $deposit['title'],
				'prefix' => $appconfig['deposit_prefix'],
				'longid' => get_number('deposits', $deposit['id'], 'deposit', 'deposit'),
				'amount' => (float)$deposit['amount'],
				'staff' => $deposit['staff'],
				'category' => $deposit['category'],
				'billstatus' => $billstatus,
				'color' => $color,
				'date' => date(get_dateFormat(), strtotime($deposit['date'])),
				'' . lang('filterbycategory') . '' => $deposit['category'],
				'' . lang('filterbystatus') . '' => $billstatus,
			);
		};
		return response()->setJSON($data_deposits);
	}
}
