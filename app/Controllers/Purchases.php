<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Purchases extends BaseController
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
		$data['title'] = lang2('purchases');
		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {
			$data['purchases'] = $this->Purchases_Model->get_all_purchases_by_privileges();
			$data['off'] = $this->Report_Model->pff_purchases();
			$data['ofv'] = $this->Report_Model->ofv_purchases();
			$data['oft'] = $this->Report_Model->oft_purchases();
			$data['vgf'] = $this->Report_Model->vgf_purchases();
			$data['tfa'] = $this->Report_Model->tfa_purchases();
			$data['pfs'] = $this->Report_Model->pfs_purchases();
			$data['otf'] = $this->Report_Model->otf_purchases();
			$data['tef'] = $this->Report_Model->tef_purchases();
			$data['vdf'] = $this->Report_Model->vdf_purchases();
			$data['fam'] = $this->Report_Model->fam_purchases();
		} else {
			$data['purchases'] = $this->Purchases_Model->get_all_purchases_by_privileges(session()->usr_id);
			$data['off'] = $this->Report_Model->total_amount_by_status('1', 'purchases');
			$data['ofv'] = $this->Report_Model->total_amount_by_status('2', 'purchases');
			$data['oft'] = $this->Report_Model->total_amount_by_status('3', 'purchases');
			$data['vgf'] = $this->Report_Model->total_amount_by_status('due', 'purchases');
			$data['tfa'] = $this->Report_Model->total_number_of_data_by_status('4', 'purchases');
			$data['pfs'] = $this->Report_Model->total_number_of_data_by_status('1', 'purchases');
			$data['otf'] = $this->Report_Model->total_number_of_data_by_status('2', 'purchases');
			$data['tef'] = $this->Report_Model->total_number_of_data_by_status('3', 'purchases');
			$data['vdf'] = $this->Report_Model->total_number_of_data_by_status('due', 'purchases');
			$data['fam'] = $this->Report_Model->total_amount_by_status('', 'purchases', 'purchases');
		}

		$data['ofy'] = ($data['tfa'] > 0 ? number_format(($data['tef'] * 100) / $data['tfa']) : 0);
		$data['ofx'] = ($data['tfa'] > 0 ? number_format(($data['otf'] * 100) / $data['tfa']) : 0);
		$data['vgy'] = ($data['tfa'] > 0 ? number_format(($data['vdf'] * 100) / $data['tfa']) : 0);

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		return view('purchases/index', $data);
	}


	function getDataGraphs()
	{
		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {
			$data['off'] = $this->Report_Model->pff_purchases();
			$data['ofv'] = $this->Report_Model->ofv_purchases();
			$data['oft'] = $this->Report_Model->oft_purchases();
			$data['vgf'] = $this->Report_Model->vgf_purchases();
			$data['tfa'] = $this->Report_Model->tfa_purchases();
			$data['pfs'] = $this->Report_Model->pfs_purchases();
			$data['otf'] = $this->Report_Model->otf_purchases();
			$data['tef'] = $this->Report_Model->tef_purchases();
			$data['vdf'] = $this->Report_Model->vdf_purchases();
			$data['fam'] = $this->Report_Model->fam_purchases();
		} else {
			$data['off'] = $this->Report_Model->total_amount_by_status('1', 'purchases');
			$data['ofv'] = $this->Report_Model->total_amount_by_status('2', 'purchases');
			$data['oft'] = $this->Report_Model->total_amount_by_status('3', 'purchases');
			$data['vgf'] = $this->Report_Model->total_amount_by_status('due', 'purchases');
			$data['fam'] = $this->Report_Model->total_amount_by_status('', 'purchases', 'purchases');

			$data['tfa'] = $this->Report_Model->total_number_of_data_by_status('4', 'purchases');
			$data['pfs'] = $this->Report_Model->total_number_of_data_by_status('1', 'purchases');
			$data['otf'] = $this->Report_Model->total_number_of_data_by_status('2', 'purchases');
			$data['tef'] = $this->Report_Model->total_number_of_data_by_status('3', 'purchases');
			$data['vdf'] = $this->Report_Model->total_number_of_data_by_status('due', 'purchases');
		}

		$data['ofy'] = ($data['tfa'] > 0 ? number_format(($data['tef'] * 100) / $data['tfa']) : 0);
		$data['ofx'] = ($data['tfa'] > 0 ? number_format(($data['otf'] * 100) / $data['tfa']) : 0);
		$data['vgy'] = ($data['tfa'] > 0 ? number_format(($data['vdf'] * 100) / $data['tfa']) : 0);

		$data['ofy_dec'] = $data['ofy'] < 100 ? '0.' . $data['ofy'] : $data['ofy'];
		$data['ofx_dec'] = $data['ofx'] < 100 ? '0.' . $data['ofx'] : $data['ofx'];
		$data['vgy_dec'] = $data['vgy'] < 100 ? '0.' . $data['vgy'] : $data['vgy'];

		return response()->setJSON($data);
	}


	function create()
	{


		if ($this->Privileges_Model->check_privilege('purchases', 'create')) {

			$data['title'] = lang2('newpurchase');

			$products = $this->Products_Model->get_all_products();

			$settings = $this->Settings_Model->get_settings_ciuis();

			if (isset($_POST) && count($_POST) > 0) {

				$vendor = request()->getPost('vendor');

				$created = request()->getPost('created');

				$duedate = request()->getPost('duedate');

				$datepayment = request()->getPost('datepayment');

				$account = request()->getPost('account');

				$totalItems = request()->getPost('totalItems');

				$recurring_period = request()->getPost('recurring_period');

				$recurring = request()->getPost('recurring');

				$status = request()->getPost('status');

				$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);

				$paid = ($status == 'true') ? '1' : '0';



				$hasError = false;



				if ($vendor == '') {

					$hasError = true;

					$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('vendor');
				} else if (($created == '')) {

					$hasError = true;

					$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('dateofissuance');
				} else if (($paid == '1') && (request()->getPost('account') == '')) {

					$hasError = true;

					$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('account');
				} else if (($paid == '1') && (request()->getPost('datepayment') == '')) {

					$hasError = true;

					$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('datepaid');
				} else if (($paid == '0') && ($duedate == '')) {

					$hasError = true;

					$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('duedate');
				} else if (($paid == '0') && (strtotime($duedate) < strtotime($created))) {

					$hasError = true;

					$datas['message'] = lang2('dateofissuance') . ' ' . lang2('date_error') . ' ' . lang2('duedate');
				} else if (((int)(request()->getPost('totalItems'))) == 0) {
					$hasError = true;
					$datas['message'] = lang2('invalid_items');
				} else if ($total == 0) {
					$hasError = true;
					$datas['message'] = lang2('invalid_total');
				}

				if ($hasError) {

					$datas['success'] = false;

					return response()->setJSON($datas);
				}

				if (!$hasError) {

					$appconfig = get_appconfig();

					$status_value = request()->getPost('status');
					$duedate = request()->getPost('duedate');

					if ($status_value == 'true') {

						$datepayment = request()->getPost('datepayment');

						//	$duenote = null;
						//$duedate = null;

						$status = 2;
					} else {

						//$duedate = request()->getPost('duedate');

						//	$duenote = request()->getPost( 'duenote' );

						$datepayment = null;

						$status = 3;
					}



					$params = array(

						'token' => md5(uniqid()),

						'no' => request()->getPost('no'),

						//'serie' => request()->getPost( 'serie' ),

						'vendor_id' => request()->getPost('vendor'),

						'staff_id' => session()->usr_id,

						'status_id' => $status,

						'created' => request()->getPost('created'),

						'last_recurring' => date('Y-m-d'),

						'duedate' => $duedate,

						'datepayment' => $datepayment,

						//'duenote' => $duenote,

						'sub_total' => request()->getPost('sub_total'),

						'total_discount' => request()->getPost('total_discount'),

						'total_tax' => request()->getPost('total_tax'),

						'total' => request()->getPost('total'),

					);

					$purchase_id = $this->Purchases_Model->purchases_add($params);

					if ($status == '2') {

						$params = array(

							'staff_id' => session()->usr_id,

							'account_id' => request()->getPost('account'),

							'title' => lang2('purchase'),

							'date' => $datepayment,

							'created' => date('Y-m-d H:i:s'),

							'amount' =>  request()->getPost('total'),

							'purchase_id' => $purchase_id,

							'internal' => '1',

							'sub_total' => request()->getPost('total'),

							'total_discount' => '0',

							'total_tax' => '0'

						);

						//$payments = $this->Expenses_Model->create_expense($params);

						//$payments_details = $this->Payments_Model->get_payment_details($payments);
					}

					$this->Settings_Model->create_process('pdf', $purchase_id, 'purchase', 'purchase_message');

					// START Recurring Purchase

					if (request()->getPost('recurring') == 'true' || request()->getPost('recurring') == '1') {

						$true = true;
						$dias = [
							'0' => 1,
							'1' => 7,
							'2' => 30,
							'3' => 365
						];
						$dueDataAtual = $duedate;
						$createDataAtual = request()->getPost('created');

						while ($true) {
							if (request()->getPost('recurring_type') == 0 || request()->getPost('recurring_type') == 1 || request()->getPost('recurring_type') == 3) {
								$newDuedate = date('Y-m-d', strtotime("+" . $dias[request()->getPost('recurring_type')] . " days", strtotime(date($dueDataAtual))));
							} elseif (request()->getPost('recurring_type') == 2) {
								$newDuedate = date('Y-m-d', strtotime("+1 month", strtotime(date($dueDataAtual))));
							}

							if (strtotime($newDuedate) <= strtotime(request()->getPost('end_recurring'))) {
								$createDataAtual = date('Y-m-d', strtotime("+1 month", strtotime(date($createDataAtual))));

								$params['duedate'] = $newDuedate;
								$params['compAtual'] = $purchase_id;
								$params['token'] = md5(uniqid());
								$params['created'] = $createDataAtual;

								$this->Purchases_Model->purchases_add($params);
								$dueDataAtual = $newDuedate;
							} else {
								$true = false;
							}
						}
					}

					$datas['success'] = true;

					$datas['id'] = $purchase_id;

					if ($appconfig['purchase_series']) {

						$purchase_number = $appconfig['purchase_series'];

						$purchase_number = $purchase_number + 1;

						$this->Settings_Model->increment_series('purchase_series', $purchase_number);
					}

					$datas['message'] = lang2('purchases') . ' ' . lang2('createmessasge');

					return response()->setJSON($datas);
				}
			} else {

				$data['all_vendors'] = $this->Vendors_Model->get_all_vendors();

				$data['all_accounts'] = $this->Accounts_Model->get_all_accounts();

				$data['settings'] = $this->Settings_Model->get_settings_ciuis();

				return view('purchases/create', $data);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}
	}



	function update($id = '')
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {

			$purchases = $this->Purchases_Model->get_purchase_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {

			$purchases = $this->Purchases_Model->get_purchase_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}

		if ($purchases) {

			if ($this->Privileges_Model->check_privilege('purchases', 'edit')) {

				$appconfig = get_appconfig();

				$data['title'] = '' . get_number('purchases', $id, 'purchase', 'purchase') . '';

				$data['purchases'] = $purchases;

				if (isset($purchases['id'])) {

					if (isset($_POST) && count($_POST) > 0) {

						//	$vendor = request()->getPost('vendor');

						$created = request()->getPost('created');

						$duedate = request()->getPost('duedate');

						$datepayment = request()->getPost('datepayment');

						$recurring_period = request()->getPost('recurring_period');

						$recurring_status = request()->getPost('recurring_status');

						$hasError = false;

						//if ($vendor == '') {

						//		$hasError = true;

						//		$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('vendor');
						//	} else
						if (($created == '')) {

							$hasError = true;

							$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('dateofissuance');
						} else if ($duedate == '') {
							$hasError = true;
							$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('duedate');
						} else if (($recurring_status == 'true') && (request()->getPost('recurring_period') == '')) {
							$hasError = true;
							$data['message'] = lang2('invalidmessage') . ' ' . lang2('recurring_period');
						} else if ((strtotime($duedate) < strtotime($created))) {
							$hasError = true;
							$data['message'] = lang2('dateofissuance') . ' ' . lang2('date_error') . ' ' . lang2('duedate');
						}

						if ($purchases['status_id'] == 2) {

							$datepayment = request()->getPost('datepayment');

							//	$duenote = null;

							//$duedate = null;
						} else {

							$duedate = request()->getPost('duedate');

							//	$duenote = request()->getPost( 'duenote' );

							$datepayment = null;
						}

						if ($hasError) {

							$data['success'] = false;

							return response()->setJSON($data);
						}

						if (!$hasError) {

							$params = array(

								'no' => request()->getPost('no'),

								//'serie' => request()->getPost( 'serie' ),

								//	'vendor_id' => request()->getPost('vendor'),
								'nm_vendor' => request()->getPost('nm_vendor'),

								'created' => request()->getPost('created'),

								'status_id' => request()->getPost('status') == 'true' ? 2 : 3,

								'last_recurring' => date('Y-m-d'),

								'duedate' => $duedate,

								//	'duenote' => $duenote,

								'sub_total' => request()->getPost('sub_total'),

								'total_discount' => request()->getPost('total_discount'),

								'total_tax' => request()->getPost('total_tax'),

								'total' => request()->getPost('total'),

							);

							$this->Purchases_Model->update_purchases($id, $params);

							// START Recurring Purchase

							if (request()->getPost('excluirCompras') == 1) {

								$this->db
									->where("duedate >= '" . date('Y-m-d') . "'")
									->delete('invoices', ['fatAtual' => $id]);

								$true = true;
								$dias = [
									'0' => 1,
									'1' => 7,
									'2' => 30,
									'3' => 365
								];
								$dueDataAtual = $duedate;
								while ($true) {
									if (request()->getPost('recurring_type') == 0 || request()->getPost('recurring_type') == 1 || request()->getPost('recurring_type') == 3) {
										$newDuedate = date('Y-m-d', strtotime("+" . $dias[request()->getPost('recurring_type')] . " days", strtotime(date($dueDataAtual))));
									} elseif (request()->getPost('recurring_type') == 2) {
										$newDuedate = date('Y-m-d', strtotime("+1 month", strtotime(date($dueDataAtual))));
									}

									if (strtotime($newDuedate) <= strtotime(request()->getPost('end_recurring'))) {
										$params['duedate'] = $newDuedate;
										$params['fatAtual'] = $id;
										$params['recurring'] = '0';

										$this->Invoices_Model->invoice_add($params);
										$dueDataAtual = $newDuedate;
									} else {
										$true = false;
									}
								}
							}


							$this->Purchases_Model->update_pdf_status($id, '0');

							$data['success'] = true;

							$data['id'] = $id;

							$data['message'] = lang2('purchase') . ' ' . lang2('updatemessage');

							return response()->setJSON($data);
						}

						// END Recurring Invoice

					} else {

						return view('purchases/update', $data);
					}
				} else {

					session()->setFlashdata('ntf3', '' . $id . lang2('error'));

					return redirect()->to('purchases');
				}
			} else {

				session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

				return redirect()->to(base_url('purchases/purchase/' . $id));
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}
	}



	function purchase($id)
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {

			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {

			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}

		if ($purchase) {

			if (isset($purchase['id'])) {

				$appconfig = get_appconfig();

				$data['title'] = '' . get_number('purchases', $id, 'purchase', 'purchase') . ' ' . lang2('detail') . '';

				$data['purchases'] = $purchase;

				return view('purchases/purchase', $data);
			} else {

				return redirect()->to(base_url('purchases'));
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}
	}



	function record_payment()
	{
		if ($this->Privileges_Model->check_privilege('purchases', 'edit')) {
			$amount = request()->getPost('total');
			$purchasetotal = request()->getPost('purchasetotal');

			if (isset($_POST) && count($_POST) > 0) {

				$amount = $amount;

				$not = request()->getPost('not');

				$account = request()->getPost('account');

				$purchase_id = request()->getPost('purchase');

				$hasError = false;

				if ($amount == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('amount');
				} else if ($not == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
				} else if ($account == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('account') . ' ' . lang2('type');
				} else if ($amount > $purchasetotal) {

					$hasError = true;

					$data['message'] = lang2('paymentamounthigh') . ' ' . lang2('purchase');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$params = array(

						'category_id' => $this->Purchases_Model->get_category_id(),

						'staff_id' => session()->usr_id,

						'account_id' => request()->getPost('account'),

						'title' => lang2('purchase'),

						'date' => request()->getPost('date'),

						'created' => date('Y-m-d H:i:s'),

						'amount' =>  $amount,

						'description' => request()->getPost('not'),

						'purchase_id' => request()->getPost('purchase'),

						'internal' => '1',

						'sub_total' => $amount,

						'total_discount' => '0',

						'total_tax' => '0'

					);

					$payments = $this->Expenses_Model->create_expense($params);
					$template = $this->Emails_Model->get_template('purchase', 'purchase_payment');

					if ($template['status'] == 1) {

						//	$payments_details = $this->Payments_Model->get_payment_details($payments);

						$settings = $this->Settings_Model->get_settings_ciuis();

						$appconfig = get_appconfig();

						$purchase = $this->Purchases_Model->get_purchases_detail($purchase_id);

						$purchase_number = get_number('purchases', $purchase['id'], 'purchase', 'purchase');

						if ($purchase['status_id'] == 1) {

							$purchasestatus = lang2('draft');
						}

						if ($purchase['status_id'] == 3) {

							$purchasestatus = lang2('unpaid');
						}

						if ($purchase['status_id'] == 4) {

							$purchasestatus = lang2('cancelled');
						}

						if ($purchase['status_id'] == 2) {

							$purchasestatus = lang2('partial');
						}

						$name = $purchase['vendorcompany'];
						$link = base_url('share/purchases/' . $purchase['token'] . '');


						$message_vars = array(
							'{purchase_number}' => $purchase_number,
							'{vendor_name}' => $name,
							'{issuance_date}' => $purchase['created'],
							'{due_date}' => $purchase['duedate'],
							//'{payment_date}' => $payments_details['date'],
							//'{payment_amount}' => $payments_details['amount'],
							//'{payment_account}' => $payments_details['name'],
							//'{payment_description}' => $payments_details['not'],
							//'{payment_made_by}' => $payments_details['staffname'],
							'{purchase_status}' => $purchasestatus,
							'{total_amount}' => $purchase['total'],
							'{company_name}' =>  $settings['company'],
							'{company_email}' =>  $settings['email'],
							//'{due_note}' => $purchase['duenote'],
							'{purchase_link}' => $link,
						);

						$subject = strtr($template['subject'], $message_vars);
						$message = strtr($template['message'], $message_vars);

						$param = array(
							'from_name' => $template['from_name'],
							'email' => $purchase['email'],
							'subject' => $subject,
							'message' => $message,
							'created' => date("Y.m.d H:i:s"),
						);

						if ($purchase['email']) {
							$this->db->table('email_queue')->insert($param);
						}
					}

					$data['success'] = true;
					$data['id'] = $purchase_id;
					$data['message'] = lang2('paymentaddedsuccessfully');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function print_($id)
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {

			$purchases = $this->Purchases_Model->get_purchase_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {

			$purchases = $this->Purchases_Model->get_purchase_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}

		if ($purchases) {

			ini_set('max_execution_time', 0);

			ini_set('memory_limit', '2048M');

			if (!is_dir('uploads/files/purchases/' . $id)) {

				mkdir('./uploads/files/purchases/' . $id, 0777, true);
			}

			$data['title'] = '' . get_number('purchases', $id, 'purchase', 'purchase') . '';

			$data['purchase'] = $purchases;

			$data['vendor_country'] = get_country($data['purchase']['vendorcountry']);

			$data['vendor_state'] = get_state_name(' ', $data['purchase']['vendorstate']);

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

			$data['country'] = get_country($data['settings']['country_id']);


			$data['items'] = $this->db->table('items')
				->select('*')
				->where(['relation_type' => 'purchase', 'relation' => $id])
				->get()->getResultArray();

			return view('purchases/pdf', $data);

			$file_name = '' . get_number('purchases', $id, 'purchase', 'purchase') . '.pdf';

			$html = $this->output->get_output();

			$this->load->library('dom');

			$this->dompdf->loadHtml($html);

			$this->dompdf->set_option('isRemoteEnabled', TRUE);

			$this->dompdf->setPaper('A4', 'portrait');

			$this->dompdf->render();

			$output = $this->dompdf->output();

			file_put_contents('uploads/files/purchases/' . $id . '/' . $file_name . '', $output);

			if ($output) {

				return redirect()->to(base_url('uploads/files/purchases/' . $id . '/' . $file_name . ''));

				//$this->dompdf->stream( '' . $file_name . '', array( "Attachment" => 0 ) );

			} else {

				return redirect()->to(base_url('purchases/pdf_falut/'));
			}

			$this->dompdf->stream('' . $file_name . '', array("Attachment" => 0));
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}
	}



	function create_pdf($id)
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {

			$purchases = $this->Purchases_Model->get_purchase_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {

			$purchases = $this->Purchases_Model->get_purchase_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}

		if ($purchases) {

			ini_set('max_execution_time', 0);

			ini_set('memory_limit', '2048M');

			if (!is_dir('uploads/files/purchases/' . $id)) {

				mkdir('./uploads/files/purchases/' . $id, 0777, true);
			}

			$data['title'] = '' . get_number('purchases', $id, 'purchase', 'purchase') . '';

			$data['purchase'] = $purchases;

			$data['vendor_country'] = get_country($data['purchase']['vendorcountry']);

			$data['vendor_state'] = get_state_name(' ', $data['purchase']['vendorstate']);

			// $data['vendor_country'] = get_country($data[ 'purchase' ]['country_id']);

			// $data['vendor_state'] = get_state_name(' ',$data['purchase']['state']);

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

			$data['country'] = get_country($data['settings']['country_id']);


			$data['items'] = $this->db->table('items')
				->select('*')
				->where(['relation_type' => 'purchase', 'relation' => $id])
				->get()->getResultArray();

			return view('purchases/pdf', $data);



			$file_name = '' . get_number('purchases', $id, 'purchase', 'purchase') . '.pdf';

			$html = $this->output->get_output();

			$this->load->library('dom');

			$this->dompdf->loadHtml($html);

			$this->dompdf->set_option('isRemoteEnabled', TRUE);

			$this->dompdf->setPaper('A4', 'portrait');

			$this->dompdf->render();

			$output = $this->dompdf->output();

			file_put_contents('uploads/files/purchases/' . $id . '/' . $file_name . '', $output);

			$this->Purchases_Model->update_pdf_status($id, '1');

			//$this->dompdf->stream( '' . $file_name . '', array( "Attachment" => 0 ) );

			if ($output) {

				return redirect()->to(base_url('purchases/pdf_generated/' . $file_name . ''));
			} else {

				return redirect()->to(base_url('purchases/pdf_fault/'));
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}
	}



	function send_purchase_email($id)
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {

			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {

			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}

		if ($purchase) {

			$settings = $this->Settings_Model->get_settings_ciuis();

			$template = $this->Emails_Model->get_template('purchase', 'purchase_message');

			$purchase_number = get_number('purchases', $id, 'purchase', 'purchase');

			$path = '';

			if ($template['attachment'] == '1') {

				if ($purchase['pdf_status'] == '0') {

					$this->Purchases_Model->generate_pdf($id);

					$file = get_number('purchases', $purchase['id'], 'purchase', 'purchase');

					$path = base_url('uploads/files/purchases/' . $id . '/' . $file . '.pdf');
				} else {

					$file = get_number('purchases', $purchase['id'], 'purchase', 'purchase');

					$path = base_url('uploads/files/purchases/' . $id . '/' . $file . '.pdf');
				}
			}

			if ($purchase['status_id'] == 1) {

				$purchasestatus = lang2('draft');
			}

			if ($purchase['status_id'] == 3) {

				$purchasestatus = lang2('unpaid');
			}

			if ($purchase['status_id'] == 4) {

				$purchasestatus = lang2('cancelled');
			}

			if ($purchase['status_id'] == 2) {

				$purchasestatus = lang2('partial');
			}

			$name = $purchase['vendorcompany'];

			$link = base_url('share/purchases/' . $purchase['token'] . '');



			$message_vars = array(

				'{purchase_number}' => $purchase_number,

				'{vendor_name}' => $name,

				'{issuance_date}' => $purchase['created'],

				'{due_date}' => $purchase['duedate'],

				'{purchase_status}' => $purchasestatus,

				'{total_amount}' => $purchase['total'],

				'{company_name}' =>  $settings['company'],

				'{company_email}' =>  $settings['email'],

				//'{due_note}' => $purchase['duenote'],

				'{purchase_link}' => $link,

			);

			$subject = strtr($template['subject'], $message_vars);

			$message = strtr($template['message'], $message_vars);



			$param = array(

				'from_name' => $template['from_name'],

				'email' => $purchase['email'],

				'subject' => $subject,

				'message' => $message,

				'created' => date("Y.m.d H:i:s"),

				'status' => 0,

				'attachments' => $path ? $path : NULL,

			);



			

			$data = $this->Emails_Model->send_email($purchase['email'], $template['from_name'], $subject, $message, $path);

			if ($data['success'] == true) {

				$return['status'] = true;

				$return['message'] = $data['message'];

				if ($purchase['email']) {

					$this->db->table('email_queue')->insert($param);
				}

				return response()->setJSON($return);
			} else {

				$return['status'] = false;

				$return['message'] = lang2('errormessage');

				return response()->setJSON($return);
			}
		} else {

			$return['status'] = false;

			$return['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($return);
		}
	}



	function share($id)
	{

		$inv = $this->Purchases_Model->get_purchases_detail($id);



		$invcustomer = $inv['vendorcompany'];

		$data = array(

			'customer' => $invcustomer,

			'customermail' => $inv['email'],

			'invoicelink' => '' . base_url('share/purchases/' . $inv['token'] . '') . ''

		);

		$body = view('email/invoices/sendpurchase.php', $data, TRUE);

		$subject = lang2('yourinvoicedetails');

		$to = $inv['email'];

		$result = send_email($subject, $to, $data, $body);



		if ($result) {



			$response = $this->db->table('purchases')->where('id', $id)->update(['datesend' => date('Y-m-d H:i:s')]);

			session()->setFlashdata('ntf1', '<b>' . $inv['email'], lang2('sendmailvendor') . '</b>');

			return redirect()->to('purchases/purchase/' . $id . '');
		} else {



			session()->setFlashdata('ntf4', '<b>' . lang2('sendmailcustomereror') . '</b>');

			return redirect()->to('purchases/purchase/' . $id . '');
		}
	}



	function mark_as_draft($id)
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'edit')) {

			$response = $this->db->table('purchases')->where('id', $id)->update(['status_id' => 1]);

			$data['success'] = true;

			$data['message'] = lang2('markedasdraft');
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function mark_as_cancelled($id)
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'edit')) {

			$response = $this->db->table('purchases')->where('id', $id)->update(['status_id' => 4]);
			$response = $this->db->table('payments')->delete(array('purchase_id' => $id));

			$data['success'] = true;

			$data['message'] = lang2('markedascancelled');
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function remove($id)
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {

			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {

			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id, session()->usr_id);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}

		if ($purchase) {

			if ($this->Privileges_Model->check_privilege('purchases', 'delete')) {

				if (isset($purchase['id'])) {

					$this->Purchases_Model->delete_purchases($id, get_number('purchases', $id, 'purchase', 'purchase'));

					$data['success'] = true;

					$data['message'] = lang2('purchase') . ' ' . lang2('deletemessage');

					return response()->setJSON($data);
				} else

					return redirect()->to(base_url('purchases'));
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}
	}



	function remove_item($id)
	{

		$response = $this->db->table('items')->delete(array('id' => $id));
	}



	function pdf_generated($file)
	{

		$result = array(

			'status' => true,

			'file_name' => $file,

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



	function download_pdf($id)
	{

		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {

			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {

			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}

		if ($purchase) {

			if (isset($id)) {

				$file_name = '' . get_number('purchases', $id, 'purchase', 'purchase') . '.pdf';

				if (is_file('./uploads/files/purchases/' . $id . '/' . $file_name)) {

					$this->load->helper('file');

					$this->load->helper('download');

					$data = file_get_contents('./uploads/files/purchases/' . $id . '/' . $file_name);

					force_download($file_name, $data);
				} else {

					session()->setFlashdata('ntf4', lang2('filenotexist'));

					return redirect()->to('purchases/purchase/' . $id);
				}
			} else {

				return redirect()->to('purchases/purchase/' . $id);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}
	}



	function get_purchase($id)
	{
		$purchase = array();
		$net_balance = 0;
		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {
			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {
			$purchase = $this->Purchases_Model->get_purchase_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('purchases'));
		}

		if ($purchase) {
			$fatop = $this->Purchases_Model->get_items_purchases($id);
			$tadtu = $this->Purchases_Model->get_paid_purchases($id);
			$appconfig = get_appconfig();
			$total = $purchase['total'];
			$today = time();
			$duedate = strtotime($purchase['duedate']);
			$created = strtotime($purchase['created']);
			$paymentday = $duedate - $created;
			$paymentx = $today - $created;
			$datepaymentnet = $paymentday - $paymentx;

			if ($purchase['duedate'] == 0) {
				$duedate_text = 'No Due Date';
			} else {
				if ($datepaymentnet < 0) {
					$duedate_text = lang2('overdue');
					$duedate_text = '' . floor($datepaymentnet / (60 * 60 * 24)) . ' days';
				} else {
					$duedate_text = lang2('payableafter') . floor($datepaymentnet / (60 * 60 * 24)) . ' ' . lang2('day') . '';
				}
			}

			if ($purchase['datesend'] == 0) {
				$mail_status = lang2('notyetbeensent');
				$net_balance = intval($total);
			} else {
				$mail_status = _adate($purchase['datesend']);
				$kalan = $total - $tadtu->amount;
				$net_balance = $kalan;
			}

			if ($tadtu->amount < $total && $tadtu->amount > 0) {
				$partial_is = true;
			} else $partial_is = false;

			$builder = $this->db->table('payments');
			$builder->select('*, accounts.name as accountname, payments.id as id');
			$builder->join('accounts', 'payments.account_id = accounts.id', 'left');
			$builder->where('purchase_id', $id);
			$payments = $builder->get()->getResultArray();

			$items = $this->db->table('items')
				->select('*')
				->where(['relation_type' => 'purchase', 'relation' => $id])
				->get()->getResultArray();

			foreach ($items as $index => $item) {
				$items[$index]['price'] = number_format($item['price'], 2, ',', '.');
			}

			$properties = array(
				'purchase_id' => '' . get_number("purchases", $purchase['id'], 'purchase', 'purchase') . '',
				'vendor_company' => $purchase['vendorcompany'],
				'vendor_address' => $purchase['vendoraddress'],
				'vendor_phone' => $purchase['vendor_phone'],
				'purchase_staff' => $purchase['staffmembername'],
			);

			if ($purchase['recurring_endDate'] != 'Invalid date') {

				$recurring_endDate = date(DATE_ISO8601, strtotime($purchase['recurring_endDate']));
			} else {

				$recurring_endDate = '';
			}

			$purchase_details = array(

				'id' => $purchase['id'],

				'sub_total' => $purchase['sub_total'],

				'total_discount' => $purchase['total_discount'],

				'total_tax' => $purchase['total_tax'],

				'total' => $purchase['total'],

				'no' => $purchase['no'],

				//	'serie' => $purchase[ 'serie' ],

				'created' => date(get_dateFormat(), strtotime($purchase['created'])),

				'duedate' => date(get_dateFormat(), strtotime($purchase['duedate'])),

				'created_edit' => $purchase['created'],

				'duedate_edit' => $purchase['duedate'],

				'nm_vendor' => $purchase['nm_vendor'],

				'datepayment' => $purchase['datepayment'],

				//	'duenote' => $purchase[ 'duenote' ],

				'status_id' => $purchase['status_id'],

				'duedate_text' => $duedate_text,

				'mail_status' => $mail_status,



				'partial_is' => $partial_is,

				'items' => $items,

				'payments' => $payments,

				// Recurring Purchase

				'recurring_endDate' => $recurring_endDate,

				'recurring_id' => $purchase['recurring_id'],

				'recurring_status' => $purchase['recurring_status'] == '0' ? true : false,

				'recurring_period' => (int)$purchase['recurring_period'],

				'recurring_type' => $purchase['recurring_type'] ? $purchase['recurring_type'] : 0,

				// END Recurring Purchase

				'payments' => $payments,

				'properties' => $properties,

				'purchase_number' => $purchase['purchase_number'],

				'pdf_status' => $purchase['pdf_status'],

			);



			if ($net_balance > 0) {

				$purchase_details['balance'] = $net_balance;
			}

			return response()->setJSON($purchase_details);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('purchases'));
		}
	}



	function get_purchases()
	{

		$purchases = array();
		$graph['total'] = 0;
		$graph['valor_total'] = 0;

		$graph['pago_total'] = 0;
		$graph['pago_valor'] = 0;

		$graph['pendente_total'] = 0;
		$graph['pendente_valor'] = 0;

		$graph['vencido_total'] = 0;
		$graph['vencido_valor'] = 0;


		$graph['pendente_porcet'] = 0;
		$graph['pago_porcet'] = 0;
		$graph['vencido_porcet'] = 0;

		if ($this->Privileges_Model->check_privilege('purchases', 'all')) {
			$purchases = $this->Purchases_Model->get_all_purchases_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('purchases', 'own')) {
			$purchases = $this->Purchases_Model->get_all_purchases_by_privileges(session()->usr_id);
		}

		$data_purchases = array();

		foreach ($purchases as $purchase) {

			$created =  date(get_dateFormat(), strtotime($purchase['created']));
			$duedate =  date(get_dateFormat(), strtotime($purchase['duedate']));

			$graph['total'] += 1;
			$graph['valor_total'] += $purchase['total'];


			if ($purchase['duedate'] == 0000 - 00 - 00) {
				$realduedate = 'No Due Date';
			} else $realduedate = $duedate;

			$totalx = $purchase['total'];
			// Calcular a soma da coluna 'amount'
			$paytotal = $this->db->table('payments')
				->selectSum('amount')
				->where('purchase_id', $purchase['id'])
				->get()
				->getRow();

			// Subtrair o total pago do total geral
			$balance = $totalx - ($paytotal->amount ?? 0);


			$color = 'success';

			if ($balance == 0) {
				$purchasesstatus = lang2('paidinv');
				$color = 'success';

				$graph['pago_total'] += 1;
				$graph['pago_valor'] += $purchase['total'];
			} else {

				if ($paytotal->amount >= $purchase['total'] || $purchase['status_id'] == '2') {
					$purchasesstatus = lang2('paidinv');
					$color = 'success';

					$graph['pago_total'] += 1;
					$graph['pago_valor'] += $purchase['total'];
				} else if (($purchase['status_id'] == 3 || $paytotal->amount < $purchase['total'])  &&  strtotime($purchase['duedate']) >= strtotime(date('Y-m-d'))) {
					$purchasesstatus = lang2('unpaid');
					$color = 'danger';

					$graph['pendente_total'] += 1;
					$graph['pendente_valor'] += $purchase['total'];
				} else if (strtotime($purchase['duedate']) < strtotime(date('Y-m-d')) && $paytotal->amount < $purchase['total']) {
					$purchasesstatus = lang2('overdue');
					$color = 'danger';

					$graph['vencido_total'] += 1;
					$graph['vencido_valor'] += $purchase['total'];
				} else if ($purchase['status_id'] == 1) {
					$purchasesstatus = lang2('draft');
					$color = 'muted';
				} else if ($purchase['status_id'] == 4) {
					$purchasesstatus = lang2('cancelled');
					$color = 'danger';
				}
			}

			$customer = $purchase['vendorcompany'];
			$appconfig = get_appconfig();
			$data_purchases[] = array(
				'id' => $purchase['id'],
				'prefix' => $appconfig['purchase_prefix'],
				'longid' => get_number("purchases", $purchase['id'], 'purchase', 'purchase'),
				'created' => $created,
				'duedate' => $realduedate,
				'customer' => $customer,
				'vendor_id' => $purchase['vendor_id'],
				'recurring_status' => $purchase['recurring_status'] == '0' ? true : false,
				'staff_id' => $purchase['staff_id'],
				'total' => (float)$purchase['total'],
				'status' => $purchasesstatus,
				'color' => $color,
				'' . lang2('filterbystatus') . '' => $purchasesstatus,
				'' . lang2('filterbyvendor') . '' => $customer,
			);
		};

		$graph['pendente_porcet'] = ($graph['total'] > 0 ? number_format(($graph['pendente_total'] * 100) / $graph['total']) : 0); //ofy
		$graph['pago_porcet'] = ($graph['total'] > 0 ? number_format(($graph['pago_total'] * 100) / $graph['total']) : 0); //ofx
		$graph['vencido_porcet'] = ($graph['total'] > 0 ? number_format(($graph['vencido_total'] * 100) / $graph['total']) : 0); //vgy



		return response()->setJSON(['purchases' => $data_purchases, 'dataGraph' => $graph]);
	}
}
