<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
class Expenses extends BaseController
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
		$data['title'] = lang2('expenses');
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$data['expenses'] = $this->Expenses_Model->get_all_expenses_by_privileges();
			$data['expensesAmount'] = $this->Expenses_Model->expensesTotalAmount();
			$data['billed_expenses'] = $this->Expenses_Model->billed_expenses();
			$data['not_billed_expenses'] = $this->Expenses_Model->not_billed_expenses();
			$data['overdue_expenses'] = $this->Expenses_Model->overdue_expenses();
			$data['expenses_num'] = $this->Expenses_Model->expenses_num();
			$data['billed_expenses_num'] = $this->Expenses_Model->billed_expenses_num();
			$data['not_billed_expenses_num'] = $this->Expenses_Model->not_billed_expenses_num();
			$data['overdue_expenses_num'] =  $this->Expenses_Model->overdue_expenses_num();
		} else {
			$data['expenses'] = $this->Expenses_Model->get_all_expenses_by_privileges(session()->usr_id);
			$data['expensesAmount'] = $this->Expenses_Model->expenses_total_amount_by_status();
			$data['billed_expenses'] = $this->Expenses_Model->expenses_total_amount_by_status('billed');
			$data['not_billed_expenses'] = $this->Expenses_Model->expenses_total_amount_by_status('notbilled');
			$data['overdue_expenses'] = $this->Expenses_Model->expenses_total_amount_by_status('overdue');
			$data['expenses_num'] = $this->Expenses_Model->expenses_num_by_type();
			$data['billed_expenses_num'] = $this->Expenses_Model->expenses_num_by_type('billed');
			$data['not_billed_expenses_num'] = $this->Expenses_Model->expenses_num_by_type('notbilled');
			$data['overdue_expenses_num'] = $this->Expenses_Model->expenses_num_by_type('overdue');
		}

		$data['billed'] = ($data['expenses_num'] > 0 ? number_format(($data['billed_expenses_num'] * 100) / $data['expenses_num']) : 0);
		$data['not_billed'] = ($data['expenses_num'] > 0 ? number_format(($data['not_billed_expenses_num'] * 100) / $data['expenses_num']) : 0);
		$data['overdue'] = ($data['expenses_num'] > 0 ? number_format(($data['overdue_expenses_num'] * 100) / $data['expenses_num']) : 0);

		return view('expenses/index', $data);
	}

	function getDataGraphs()
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$data['expensesAmount'] = amount_format($this->Expenses_Model->expensesTotalAmount(), true);
			$data['billed_expenses'] = amount_format($this->Expenses_Model->billed_expenses(), true);
			$data['not_billed_expenses'] = amount_format($this->Expenses_Model->not_billed_expenses(), true);
			$data['overdue_expenses'] = amount_format($this->Expenses_Model->overdue_expenses(), true);
			$data['expenses_num'] = $this->Expenses_Model->expenses_num();
			$data['billed_expenses_num'] = $this->Expenses_Model->billed_expenses_num();
			$data['not_billed_expenses_num'] = $this->Expenses_Model->not_billed_expenses_num();
			$data['overdue_expenses_num'] =  $this->Expenses_Model->overdue_expenses_num();
		} else {
			$data['expensesAmount'] = amount_format($this->Expenses_Model->expenses_total_amount_by_status(), true);
			$data['billed_expenses'] =  amount_format($this->Expenses_Model->expenses_total_amount_by_status('billed'), true);
			$data['not_billed_expenses'] =  amount_format($this->Expenses_Model->expenses_total_amount_by_status('notbilled'), true);
			$data['overdue_expenses'] = amount_format($this->Expenses_Model->expenses_total_amount_by_status('overdue'), true);
			$data['expenses_num'] = $this->Expenses_Model->expenses_num_by_type();
			$data['billed_expenses_num'] = $this->Expenses_Model->expenses_num_by_type('billed');
			$data['not_billed_expenses_num'] =  $this->Expenses_Model->expenses_num_by_type('notbilled');
			$data['overdue_expenses_num'] = $this->Expenses_Model->expenses_num_by_type('overdue');
		}

		$data['billed'] = ($data['expenses_num'] > 0 ? number_format(($data['billed_expenses_num'] * 100) / $data['expenses_num']) : 0);
		$data['not_billed'] = ($data['expenses_num'] > 0 ? number_format(($data['not_billed_expenses_num'] * 100) / $data['expenses_num']) : 0);
		$data['overdue'] = ($data['expenses_num'] > 0 ? number_format(($data['overdue_expenses_num'] * 100) / $data['expenses_num']) : 0);

		$data['billed_dec'] = $data['billed'] < 100 ? '0.' . $data['billed'] : $data['billed'];
		$data['not_billed_dec'] = $data['not_billed'] < 100 ? '0.' . $data['not_billed'] : $data['not_billed'];
		$data['overdue_dec'] = $data['overdue'] < 100 ? '0.' . $data['overdue'] : $data['overdue'];

		return response()->setJSON($data);
	}
	function create()
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$hash = '';
				$hash = ciuis_Hash();
				$category_id = request()->getPost('category');
				//$customer_id = request()->getPost('customer');
				$account_id = request()->getPost('account') ? request()->getPost('account') : null;
				//$title = request()->getPost('title');
				$date = request()->getPost('date');
				$amount = request()->getPost('amount');
				$description = request()->getPost('description');
				$internal = request()->getPost('internal');
				$internal = ($internal == 'true') ? '1' : '0';
				$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);
				$hasError = false;
				//	if ($title == '') {
				//		$hasError = true;
				//		$data['message'] = lang2('invalidmessage') . ' ' . lang2('title');
				//	} else
				if ($category_id == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('category');
				}
				//else if (($customer_id == '') && ($internal == '0')) {
				//		$hasError = true;
				//		$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
				//	}
				else if (($internal == '1') && (request()->getPost('staff') == '')) {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('staff');
				}
				// else if ($account_id == '') {
				//	$hasError = true;
				//	$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('account');
				//}
				else if (request()->getPost('totalItems') < 1) {
					$hasError = true;
					$data['message'] = lang2('invalid_expense_items');
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
					//if ($internal == '1') {
					$staff = request()->getPost('staff');
					//$customer = NULL;
					//	} else {
					//	$staff = session()->usr_id;
					//	$customer = $customer_id;
					//}
					$params = array(
						'hash' => $hash,
						'category_id' => $category_id,
						'staff_id' => $staff,
						//'customer_id' => $customer,
						'account_id' => $account_id,
						//	'title' => $title,
						'number' => request()->getPost('number'),
						'date' => $date,
						'created' => date('Y-m-d H:i:s'),
						'amount' => request()->getPost('total'),
						'total_tax' => request()->getPost('total_tax'),
						'total_discount' => request()->getPost('total_discount'),
						'sub_total' => request()->getPost('sub_total'),
						'description' => $description,
						'internal' => $internal,
						'last_recurring' => $date,
						'id_forma_pgt' => request()->getPost('frm_pagamento'),
						'duedate' => request()->getPost('duedate'),
						'pago' => request()->getPost('pago') == "true" ? '1' : '0',
						'expense_created_by' => session()->usr_id,
					);

					$expenses_id = $this->Expenses_Model->create($params);
					if (request()->getPost('custom_fields')) {
						$custom_fields = array(
							'custom_fields' => request()->getPost('custom_fields')
						);
						$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'expense', $expenses_id);
					}


					$this->Settings_Model->create_process('pdf', $expenses_id, 'expense', 'expense_created');
					if (request()->getPost('recurring') == 'true'  || request()->getPost('recurring') == '1') {
						$true = true;
						$dias = [
							'0' => 1,
							'1' => 7,
							'2' => 30,
							'3' => 365
						];
						$dueDataAtual = request()->getPost('duedate');
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
								$params['despAtual'] = $expenses_id;
								$params['created'] = $createDataAtual;

								$this->Expenses_Model->create($params);
								$dueDataAtual = $newDuedate;
							} else {
								$true = false;
							}
						}
					}
					$data['success'] = true;
					$data['message'] = lang2('expense') . ' ' . lang2('createmessage');
					$data['id'] = $expenses_id;

					if ($appconfig['expense_series']) {
						$expense_number = $appconfig['expense_series'];
						$expense_number = $expense_number + 1;
						$this->Settings_Model->increment_series('expense_series', $expense_number);
					}
					return response()->setJSON($data);
					exit;
				}
			} else {
				$data['title'] = lang2('newexpense');
				//$data['all_customers'] = $this->Customers_Model->get_all_customers();
				//$data['all_accounts'] = $this->Accounts_Model->get_all_accounts();
				//$data['settings'] = $this->Settings_Model->get_settings_ciuis();
				return view('expenses/create', $data);
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}
	function update($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$data['expenses'] = $this->Expenses_Model->get_expenses_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$data['expenses'] = $this->Expenses_Model->get_expenses_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
		if ($data['expenses']) {
			if ($this->Privileges_Model->check_privilege('expenses', 'edit')) {
				if (!session()->get('other')) {
					if (isset($data['expenses']['id'])) {
						if (isset($_POST) && count($_POST) > 0) {
							$category_id = request()->getPost('category');
							//	$customer_id = request()->getPost('customer');
							$account_id = request()->getPost('account') ? request()->getPost('account') : null;
							//	$title = request()->getPost('title');
							$date = request()->getPost('date');
							$amount = request()->getPost('amount');
							$description = request()->getPost('description');
							$internal = request()->getPost('internal');
							$internal = ($internal == 'true') ? '1' : '0';
							$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);
							$hasError = false;
							//if ($title == '') {
							//	$hasError = true;
							//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('title');
							//} else
							if ($category_id == '') {
								$hasError = true;
								$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('category');
							}
							//	else if (($customer_id == '') && ($internal == '0')) {
							//		$hasError = true;
							//		$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
							//	}
							else if (($internal == '1') && (request()->getPost('staff') == '')) {
								$hasError = true;
								$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('staff');
							}
							/// else if ($account_id == '') {
							//	$hasError = true;
							//	$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('account');
							//}
							else if (request()->getPost('totalItems') < 1) {
								$hasError = true;
								$data['message'] = lang2('invalid_expense_items');
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
								if ($internal == '1') {
									$staff = request()->getPost('staff');
									//		$customer = NULL;
								} else {
									$staff = session()->usr_id;
									//		$customer = $customer_id;
								}
								$params = array(
									'category_id' => $category_id,
									'staff_id' => $staff,
									//'customer_id' => $customer,
									'account_id' => $account_id,
									//'title' => $title,
									'number' => request()->getPost('number'),
									'date' => $date,
									'amount' => request()->getPost('total'),
									'total_tax' => request()->getPost('total_tax'),
									'sub_total' => request()->getPost('sub_total'),
									'id_forma_pgt' => request()->getPost('frm_pagamento'),
									'duedate' => request()->getPost('duedate'),
									'pago' => request()->getPost('pago') == "true" ? '1' : '0',
									'description' => $description,
									'internal' => $internal,
									'last_recurring' => date('Y-m-d'),
								);
								$this->Expenses_Model->update_expenses($id, $params);
								// Custom Field Post
								if (request()->getPost('custom_fields')) {
									$custom_fields = array(
										'custom_fields' => request()->getPost('custom_fields')
									);
									$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'expense', $id);
								}


								if (request()->getPost('excluirDespesas') == 1) {

									$this->db->table('invoices')
										->where('duedate >=', date('Y-m-d'))
										->where('despAtual', $id)
										->delete();



									$true = true;
									$dias = [
										'0' => 1,
										'1' => 7,
										'2' => 30,
										'3' => 365
									];
									$dueDataAtual = request()->getPost('duedate');
									while ($true) {
										if (request()->getPost('recurring_type') == 0 || request()->getPost('recurring_type') == 1 || request()->getPost('recurring_type') == 3) {
											$newDuedate = date('Y-m-d', strtotime("+" . $dias[request()->getPost('recurring_type')] . " days", strtotime(date($dueDataAtual))));
										} elseif (request()->getPost('recurring_type') == 2) {
											$newDuedate = date('Y-m-d', strtotime("+1 month", strtotime(date($dueDataAtual))));
										}

										if (strtotime($newDuedate) <= strtotime(request()->getPost('end_recurring'))) {
											$params['duedate'] = $newDuedate;
											$params['fatAtual'] = $id;
											$this->Invoices_Model->invoice_add($params);
											$dueDataAtual = $newDuedate;
										} else {
											$true = false;
										}
									}
								}


								$this->Expenses_Model->update_pdf_status($id, '0');
								$data['success'] = true;
								$data['message'] = lang2('expense') . ' ' . lang2('updatemessage');
								$data['id'] = $id;
								return response()->setJSON($data);
							}
						} else {
							$data['title'] = lang2('update') . ' ' . lang2('expense');
							$data['all_customers'] = $this->Customers_Model->get_all_customers();
							$data['all_accounts'] = $this->Accounts_Model->get_all_accounts();
							$data['settings'] = $this->Settings_Model->get_settings_ciuis();
							return view('expenses/update', $data);
						}
					} else {
						session()->setFlashdata('ntf3', '' . $id . lang2('error'));
						return redirect()->to('expenses');
					}
				} else {
					session()->setFlashdata('ntf3', '' . $id . lang2('you_dont_have_permission'));
					return redirect()->to('expenses');
				}
			} else {
				session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
				return redirect()->to('expenses/receipt/' . $id);
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}
	function receipt($id)
	{
		$data['title'] = lang2('expense');

		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$data['expenses'] = $this->Expenses_Model->get_expenses_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$data['expenses'] = $this->Expenses_Model->get_expenses_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}

		if ($data['expenses']) {
			if (isset($data['expenses']['id'])) {
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
					$this->Expenses_Model->update_expenses($id, $params);
					return redirect()->to('expenses/index');
				} else {
					return view('expenses/receipt', $data);
				}
			} else {
				show_error('The expenses you are trying to edit does not exist.');
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}

	function add_file($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'edit')) {
			if (isset($id)) {
				if (isset($_POST)) {
					// Define o caminho do diretório de upload
					$uploadPath = ROOTPATH . 'public/uploads/files/expenses/' . $id . '/';

					// Cria o diretório se não existir
					if (!is_dir($uploadPath)) {
						mkdir($uploadPath, 0777, true);
					}

					// Verifica se o arquivo foi enviado
					if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
						$file = $_FILES['file'];
						$allowedTypes = ['zip', 'rar', 'tar', 'gif', 'jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'ppt', 'opt'];
						$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

						// Verifica se a extensão do arquivo é permitida e o tamanho do arquivo
						if (in_array($fileExtension, $allowedTypes) && $file['size'] <= 9000000) {
							$newName = preg_replace("/[^a-z0-9_\-\.]/i", '', basename($file['name']));
							$filePath = $uploadPath . $newName;

							// Move o arquivo para o local desejado
							if (move_uploaded_file($file['tmp_name'], $filePath)) {
								$params = [
									'relation_type' => 'expense',
									'relation' => $id,
									'file_name' => $newName,
									'created' => date("Y.m.d H:i:s"),
								];
								$this->Expenses_Model->update_pdf_status($id, '0');
								$this->db->table('files')->insert($params);

								$data['success'] = true;
								$data['message'] = lang2('file') . ' ' . lang2('uploadmessage');
							} else {
								$data['success'] = false;
								$data['message'] = lang2('file_upload_error');
							}
						} else {
							$data['success'] = false;
							$data['message'] = lang2('file_type_or_size_error');
						}
					} else {
						$data['success'] = false;
						$data['message'] = lang2('no_file_uploaded');
					}

					return response()->setJSON($data);
				}
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}


	function delete_file($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'delete')) {
			if (isset($id)) {
				$fileData = $this->Expenses_Model->get_file($id);
				$response = $this->db->table('files')->delete(['id' => $id]);

				if (is_file('./uploads/files/expenses/' . $fileData['relation'] . '/' . $fileData['file_name'])) {
					unlink('./uploads/files/expenses/' . $fileData['relation'] . '/' . $fileData['file_name']);
				}
				$this->Expenses_Model->update_pdf_status($fileData['relation'], '0');
				if ($response) {
					$data['success'] = true;
					$data['message'] = lang2('file') . ' ' . lang2('deletemessage');
				} else {
					$data['success'] = false;
					$data['message'] = lang2('errormessage');
				}
				return response()->setJSON($data);
			} else {
				return redirect()->to('expenses');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}
	function download_file($id)
	{
		if (isset($id)) {
			$fileData = $this->Expenses_Model->get_file($id);
			if (is_file('./uploads/files/expenses/' . $fileData['relation'] . '/' . $fileData['file_name'])) {
				$this->load->helper('file');
				$this->load->helper('download');
				$data = file_get_contents('./uploads/files/expenses/' . $fileData['relation'] . '/' . $fileData['file_name']);
				force_download($fileData['file_name'], $data);
			} else {
				session()->setFlashdata('ntf4', lang2('filenotexist'));
				return redirect()->to('expenses/receipt/' . $fileData['relation']);
			}
		}
	}
	function download_pdf($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
		if ($expense) {
			if (isset($id)) {
				$file_name = '' . get_number('expenses', $id, 'expense', 'expense') . '.pdf';
				if (is_file('./uploads/files/expenses/' . $id . '/' . $file_name)) {
					$this->load->helper('file');
					$this->load->helper('download');
					$data = file_get_contents('./uploads/files/expenses/' . $id . '/' . $file_name);
					force_download($file_name, $data);
				} else {
					session()->setFlashdata('ntf4', lang2('filenotexist'));
					return redirect()->to('expenses/receipt/' . $id);
				}
			} else {
				return redirect()->to('expenses/receipt/' . $id);
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}
	function create_pdf($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
		if ($expense) {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '2048M');
			if (!is_dir('uploads/files/expenses/' . $id)) {
				mkdir('./uploads/files/expenses/' . $id, 0777, true);
			}
			$data['expense'] = $this->Expenses_Model->all_expenses($id);
			$data['settings'] = $this->Settings_Model->get_settings_ciuis();
			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);
			$data['billing_country'] = get_country($data['expense']['country_id']);
			$data['billing_state'] = get_state_name($data['expense']['billing_state'], $data['expense']['billing_state_id']);
			$data['country'] = get_country($data['settings']['country_id']);
			$data['items'] = $this->db->table('items')
				->select('*')
				->where('relation_type', 'expense')
				->where('relation', $id)
				->get()
				->getResultArray();
			$files = $this->Expenses_Model->get_files($id);
			$images = array();
			$otherFiles = array();
			foreach ($files as $file) {
				$ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {
					$display = true;
					$images[] = array(
						'id' => $file['id'],
						'expense_id' => $file['relation'],
						'file_name' => $file['file_name'],
						'created' => $file['created'],
						'display' => $display,
						'path' => base_url('uploads/files/expenses/' . $id . '/' . $file['file_name']),
					);
				} else {
					$display = false;
					$otherFiles[] = array(
						'id' => $file['id'],
						'expense_id' => $file['relation'],
						'file_name' => $file['file_name'],
						'created' => $file['created'],
						'display' => $display,
						'path' => base_url('uploads/files/expenses/' . $id . '/' . $file['file_name']),
					);
				}
			}
			$data['images'] = $images;
			$data['otherFiles'] = $otherFiles;
			return view('expenses/pdf', $data);
			$file_name = '' . get_number('expenses', $id, 'expense', 'expense') . '.pdf';
			$html = $this->output->get_output();
			$this->load->library('dom');
			$this->dompdf->loadHtml($html);
			$this->dompdf->set_option('isRemoteEnabled', TRUE);
			$this->dompdf->setPaper('A4', 'portrait');
			$this->dompdf->render();
			$output = $this->dompdf->output();
			file_put_contents('uploads/files/expenses/' . $id . '/' . $file_name . '', $output);
			$this->Expenses_Model->update_pdf_status($id, '1');
			if ($output) {
				return redirect()->to(base_url('expenses/pdf_generated/' . $file_name . ''));
			} else {
				return redirect()->to(base_url('expenses/pdf_fault/'));
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}
	function print_pdf($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
		if ($expense) {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '2048M');
			if (!is_dir('uploads/files/expenses/' . $id)) {
				mkdir('./uploads/files/expenses/' . $id, 0777, true);
			}
			$data['expense'] = $this->Expenses_Model->all_expenses($id);
			$data['settings'] = $this->Settings_Model->get_settings_ciuis();
			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);
			$data['billing_state'] = get_state_name($data['expense']['billing_state'], $data['expense']['billing_state_id']);
			$data['billing_country'] = get_country($data['expense']['country_id']);
			$data['country'] = get_country($data['settings']['country_id']);

			$data['items'] = $this->db->table('items')
				->select('*')
				->where('relation_type', 'expense')
				->where('relation', $id)
				->get()
				->getResultArray();

			$files = $this->Expenses_Model->get_files($id);
			$images = array();
			$otherFiles = array();
			foreach ($files as $file) {
				$ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {
					$display = true;
					$images[] = array(
						'id' => $file['id'],
						'expense_id' => $file['relation'],
						'file_name' => $file['file_name'],
						'created' => $file['created'],
						'display' => $display,
						'path' => base_url('uploads/files/expenses/' . $id . '/' . $file['file_name']),
					);
				} else {
					$display = false;
					$otherFiles[] = array(
						'id' => $file['id'],
						'expense_id' => $file['relation'],
						'file_name' => $file['file_name'],
						'created' => $file['created'],
						'display' => $display,
						'path' => base_url('uploads/files/expenses/' . $id . '/' . $file['file_name']),
					);
				}
			}
			$data['images'] = $images;
			$data['otherFiles'] = $otherFiles;
			return view('expenses/pdf', $data);
			$appconfig = get_appconfig();
			$file_name = '' . get_number('expenses', $id, 'expense', 'expense') . '.pdf';
			$html = $this->output->get_output();
			$this->load->library('dom');
			$this->dompdf->loadHtml($html);
			$this->dompdf->set_option('isRemoteEnabled', TRUE);
			$this->dompdf->setPaper('A4', 'portrait');
			$this->dompdf->render();
			$output = $this->dompdf->output();
			file_put_contents('uploads/files/expenses/' . $id . '/' . $file_name . '', $output);
			if ($output) {
				$this->dompdf->stream('' . $file_name . '', array("Attachment" => 0));
			} else {
				return redirect()->to(base_url('expenses/pdf_fault/'));
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}
	function generate_pdf($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
		if ($expenses) {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '2048M');
			if (!is_dir('uploads/files/expenses/' . $id)) {
				mkdir('./uploads/files/expenses/' . $id, 0777, true);
			}
			$data['expense'] = $this->Expenses_Model->all_expenses($id);
			$data['settings'] = $this->Settings_Model->get_settings_ciuis();
			$data['billing_state'] = get_state_name($data['expense']['billing_state'], $data['expense']['billing_state_id']);
			$data['country'] = get_country($data['settings']['country_id']);
			$data['billing_country'] = get_country($data['expense']['country_id']);
			$data['items'] = $this->db->table('items')
				->select('*')
				->where('relation_type', 'expense')
				->where('relation', $id)
				->get()
				->getResultArray();
			$files = $this->Expenses_Model->get_files($id);
			$images = array();
			$otherFiles = array();
			foreach ($files as $file) {
				$ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {
					$display = true;
					$images[] = array(
						'id' => $file['id'],
						'expense_id' => $file['relation'],
						'file_name' => $file['file_name'],
						'created' => $file['created'],
						'display' => $display,
						'path' => base_url('uploads/files/expenses/' . $id . '/' . $file['file_name']),
					);
				} else {
					$display = false;
					$otherFiles[] = array(
						'id' => $file['id'],
						'expense_id' => $file['relation'],
						'file_name' => $file['file_name'],
						'created' => $file['created'],
						'display' => $display,
						'path' => base_url('uploads/files/expenses/' . $id . '/' . $file['file_name']),
					);
				}
			}
			$data['images'] = $images;
			$data['otherFiles'] = $otherFiles;
			return view('expenses/pdf', $data);
			$appconfig = get_appconfig();
			$file_name = '' . get_number('expenses', $id, 'expense', 'expense') . '.pdf';
			$html = $this->output->get_output();
			$this->load->library('dom');
			$this->dompdf->loadHtml($html);
			$this->dompdf->set_option('isRemoteEnabled', TRUE);
			$this->dompdf->setPaper('A4', 'portrait');
			$this->dompdf->render();
			$output = $this->dompdf->output();
			file_put_contents('uploads/files/expenses/' . $id . '/' . $file_name . '', $output);
			$this->Expenses_Model->update_pdf_status($id, '1');
			return true;
			//redirect(base_url('expenses/pdf_generates/'.$file_name.''));
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}
	function send_expense_email($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id, session()->usr_id);
		} else {
			$return['status'] = false;
			$return['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($return);
		}
		if ($expense) {
			$template = $this->Emails_Model->get_template('expense', 'expense_created');
			$path = '';
			if ($template['attachment'] == '1') {
				$appconfig = get_appconfig();
				if ($expense['pdf_status'] == '0') {
					$this->Expenses_Model->generate_pdf($id);
					$file = get_number('expenses', $expense['id'], 'expense', 'expense');
					$path = base_url('uploads/files/expenses/' . $id . '/' . $file . '.pdf');
				} else {
					$file = get_number('expenses', $expense['id'], 'expense', 'expense');
					$path = base_url('uploads/files/expenses/' . $id . '/' . $file . '.pdf');
				}
			}
			$customer = '';
			if ($expense['namesurname'] || $expense['customer']) {
				if ($expense['namesurname']) {
					$customer = $expense['namesurname'];
				} else {
					$customer = $expense['customer'];
				}
			}
			$message_vars = array(
				'{customer}' => $customer,
				'{expense_number}' => get_number('expenses', $expense['id'], 'expense', 'expense'),
				'{expense_title}' => $expense['title'],
				'{expense_category}' => $expense['category'],
				'{expense_date}' => $expense['date'],
				'{expense_description}' => $expense['description'],
				'{expense_amount}' => $expense['amount'],
				'{name}' => session()->get('staffname'),
				'{email_signature}' => session()->get('email'),
			);
			$subject = strtr($template['subject'], $message_vars);
			$message = strtr($template['message'], $message_vars);
			$email = $expense['customeremail'] ? $expense['customeremail'] : $expense['staffemail'];
			if ($email) {
				$param = array(
					'from_name' => $template['from_name'],
					'email' => $email,
					'subject' => $subject,
					'message' => $message,
					'created' => date("Y.m.d H:i:s"),
					'status' => 0,
					'attachments' => $path ? $path : NULL
				);
				
				$data = $this->Emails_Model->send_email($email, $template['from_name'], $subject, $message, $path);
				if ($data['success'] == true) {
					$return['status'] = true;
					$return['message'] = $data['message'];
					$this->db->table('email_queue')->insert($param);
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
	function pdf_generates($file)
	{
		return true;
	}
	function pdf_generated($file)
	{
		$result = array(
			'status' => true,
			'file_name' => $file,
		);
		return response()->setJSON($result);
	}
	function files($id)
	{
		if (isset($id)) {
			$files = $this->Expenses_Model->get_files($id);
			$data = array();
			foreach ($files as $file) {
				$ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
				$type = 'file';
				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {
					$type = 'image';
				}
				if ($ext == 'pdf') {
					$type = 'pdf';
				}
				if ($ext == 'zip' || $ext == 'rar' || $ext == 'tar') {
					$type = 'archive';
				}
				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {
					$display = true;
				} else {
					$display = false;
				}
				if ($ext == 'pdf') {
					$pdf = true;
				} else {
					$pdf = false;
				}
				$data[] = array(
					'id' => $file['id'],
					'expense_id' => $file['relation'],
					'file_name' => $file['file_name'],
					'created' => $file['created'],
					'display' => $display,
					'pdf' => $pdf,
					'type' => $type,
					'path' => base_url('uploads/files/expenses/' . $id . '/' . $file['file_name']),
				);
			}
			return response()->setJSON($data);
		}
	}
	function convert($id)
	{
		if ($this->Privileges_Model->check_privilege('invoices', 'create')) {
			$expenses = $this->Expenses_Model->get_expenses($id);
			if (isset($id)) {
				$params = array(
					'staff_id' => $expenses['staff_id'],
					'customer_id' => $expenses['customer_id'],
					'created' => date('Y-m-d H:i:s'),
					'status_id' => 3,
					'total' => $expenses['amount'],
					'sub_total' => $expenses['sub_total'],
					'total_discount' => $expenses['total_discount'],
					'total_tax' => $expenses['total_tax'],
					'serie' => $expenses['number'],
					'expense_id' => $id,
				);
				$this->db->table('invoices')->insert($params);
				$invoice = $this->db->insertID();
				$appconfig = get_appconfig();
				$number = $appconfig['invoice_series'] ? $appconfig['invoice_series'] : $invoice;
				$invoice_number = $appconfig['inv_prefix'] . $number;
				$this->db->table('invoices')
					->where('id', $invoice)
					->update(['invoice_number' => $invoice_number]);

				if ($appconfig['invoice_series']) {
					$invoice_number = $appconfig['invoice_series'];
					$invoice_number = $invoice_number + 1;
					$this->Settings_Model->increment_series('invoice_series', $invoice_number);
				}

				$items =  $this->db->table('items')
					->select('*')
					->where('relation_type', 'expense')
					->where('relation', $id)
					->get()
					->getResultArray();
				foreach ($items as $item) {
					$this->db->table('items')->insert(array(
						'relation' => $invoice,
						'relation_type' => 'invoice',
						'name' => $item['name'],
						'total' => $item['total'],
						'price' => $item['price'],
						'tax' => $item['tax'],
						'discount' => $item['discount'],
						'quantity' => $item['quantity'],
						'unit' => $item['unit'],
						'description' => $item['description'],
					));
				}
				$loggedinuserid = session()->usr_id;
				$this->db->table('sales')->insert([
					'invoice_id' => $invoice,
					'status_id' => 3,
					'staff_id' => $loggedinuserid,
					'customer_id' => $expenses['customer_id'],
					'total' => $expenses['amount'],
					'date' => date('Y-m-d H:i:s')
				]);

				$staffname = session()->get('staffname');
				$this->db->table('logs')->insert([
					'date' => date('Y-m-d H:i:s'),
					'detail' => sprintf(lang2('coverttoinvoice'), $staffname, get_number('expenses', $expenses['id'], 'expense', 'expense')),
					'staff_id' => $loggedinuserid,
					'customer_id' => $expenses['customer_id'],
				]);

				$response = $this->db->table('expenses')
					->where('id', $id)
					->update(['invoice_id' => $invoice]);

				$data['id'] = $invoice;
				$data['success'] = true;
				return response()->setJSON($data);
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}
	function remove($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$expense = $this->Expenses_Model->get_expenses_by_privileges($id, session()->usr_id);
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
		if ($expense) {
			if ($this->Privileges_Model->check_privilege('expenses', 'delete')) {
				if (isset($expense['id'])) {
					$this->Expenses_Model->delete_expenses($id, get_number('expenses', $id, 'expense', 'expense'));
					$this->load->helper('file');
					$folder = './uploads/files/expenses/' . $id;
					if (is_dir($folder)) {
						delete_files($folder, true);
						rmdir($folder);
					}
					$data['success'] = true;
					$data['message'] = lang2('expense') . ' ' . lang2('deletemessage');
					return response()->setJSON($data);
				} else {
					show_error('The expenses you are trying to delete does not exist.');
				}
			} else {
				$data['success'] = false;
				$data['message'] = lang2('you_dont_have_permission');
				return response()->setJSON($data);
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}
	function add_category()
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$params = array(
					'name' => request()->getPost('name'),
					'description' => request()->getPost('description'),
					'id_vendor' => request()->getPost('id_vendor'),
				);
				$category = $this->Expenses_Model->add_category($params);
				$data['success'] = true;
				$data['message'] = lang2('expensecategory') . ' ' . lang2('createmessage');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}
		return response()->setJSON($data);
	}
	function update_category($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'edit')) {
			if (isset($_POST) && count($_POST) > 0) {
				$params = array(
					'name' => request()->getPost('name'),
					'description' => request()->getPost('description'),
					'id_vendor' => request()->getPost('id_vendor'),

				);
				$this->Expenses_Model->update_category($id, $params);
				$data['success'] = true;
				$data['message'] = lang2('expensecategory') . ' ' . lang2('updatemessage');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}
		return response()->setJSON($data);
	}
	function remove_category($id)
	{
		if ($this->Privileges_Model->check_privilege('expenses', 'delete')) {
			$expensecategory = $this->Expenses_Model->get_expensecategory($id);
			if (isset($expensecategory['id'])) {
				if ($this->Expenses_Model->check_category($id) == 0) {
					$this->Expenses_Model->delete_category($id);
					$data['success'] = true;
					$data['message'] = lang2('expensecategory') . ' ' . lang2('deletemessage');
				} else {
					$data['success'] = false;
					$data['message'] = $data['message'] = lang2('category') . ' ' . lang2('is_linked') . ' ' . lang2('with') . ' ' . lang2('expense') . ', ' . lang2('so') . ' ' . lang2('cannot_delete') . ' ' . lang2('category');
				}
			} else {
				$data['success'] = false;
				$data['message'] = 'The expensecategory you are trying to delete does not exist.';
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}
		return response()->setJSON($data);
	}
	function get_expense($id)
	{
		$expense = array();
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$expense = $this->Expenses_Model->all_expenses($id);
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$expense = $this->Expenses_Model->all_expenses($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
		if ($expense) {
			$items =  $this->db->table('items')
				->select('*')
				->where('relation_type', 'expense')
				->where('relation', $id)
				->get()
				->getResultArray();
			foreach ($items as $index => $item) {
				$items[$index]['price'] = number_format($item['price'], 2, ',', '.');
			}

			if ($expense['recurring_endDate'] != 'Invalid date') {
				$recurring_endDate = date(DATE_ISO8601, strtotime($expense['recurring_endDate']));
			} else {
				$recurring_endDate = '';
			}
			$appconfig = get_appconfig();
			$data_expense = array(
				'id' => $expense['id'],
				'prefix' => $appconfig['expense_prefix'],
				'longid' => get_number('expenses', $expense['id'], 'expense', 'expense'),
				'title' => $expense['title'],
				'amount' => $expense['amount'],
				'total' => $expense['amount'],
				'date' => date(get_dateFormat(), strtotime($expense['date'])),
				'date_edit' => $expense['date'],
				'created' =>  date(get_dateFormat(), strtotime($expense['created'])),
				'internal' => $expense['internal'] == '1' ? true : false,
				'category' => $expense['category_id'],
				'customer' => $expense['customer_id'],
				'customername' => $expense['individual'] ? $expense['individual'] : $expense['customer'],
				'customeremail' => $expense['customeremail'],
				'customer_phone' => $expense['customer_phone'],
				'account' => $expense['account_id'],
				'number' => $expense['number'],
				'invoice_id' => $expense['invoice_id'],
				'pdf_status' => $expense['pdf_status'],
				'description' => $expense['desc'],
				'category_name' => $expense['category'],
				'staff_name' => $expense['staff'],
				'staff_id' => $expense['staff_id'],
				'funcionario_name' => $expense['funcionario_name'],
				'funcionario_id' => $expense['funcionario_id'],
				'account_name' => $expense['account'],
				'sub_total' => $expense['sub_total'],
				'total_discount' => $expense['total_discount'],
				'total_tax' => $expense['total_tax'],
				'nm_forma' => $expense['nm_forma'],
				'id_forma_pgt' => $expense['id_forma_pgt'],
				'items' => $items,
				'EndRecurring' => $recurring_endDate,
				'recurring_id' => $expense['recurring_id'],
				'pago' => $expense['pago'] == '1' ? true : false,
				'duedate' => $expense['duedate'],
				'recurring_status' => $expense['recurring_status'] == '0' ? true : false,
				'recurring_type' => $expense['recurring_type'] ? $expense['recurring_type'] : 0,

			);
			return response()->setJSON($data_expense);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('expenses'));
		}
	}
	function get_expenses()
	{
		$expenses = array();
		if ($this->Privileges_Model->check_privilege('expenses', 'all')) {
			$expenses = $this->Expenses_Model->get_all_expenses_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('expenses', 'own')) {
			$expenses = $this->Expenses_Model->get_all_expenses_by_privileges(session()->usr_id);
		}
		$data_expenses = array();


		$graph['expenses_num'] = 0;
		$graph['expensesAmount'] = 0;

		$graph['billed_expenses_num'] = 0;
		$graph['billed_expenses'] = 0;

		$graph['not_billed_expenses_num'] = 0;
		$graph['not_billed_expenses'] = 0;

		$graph['overdue_expenses_num'] = 0;
		$graph['overdue_expenses'] = 0;


		$graph['billed'] = 0;
		$graph['not_billed'] = 0;
		$graph['overdue'] = 0;

		$settings = $this->Settings_Model->get_settings_ciuis();
		$appconfig = get_appconfig();
		foreach ($expenses as $expense) {

			switch ($settings['dateformat']) {
				case 'yy.mm.dd':
					$expensedate = _rdate($expense['date']);
					$duedate = _rdate($expense['duedate']);
					break;
				case 'dd.mm.yy':
					$expensedate = _udate($expense['date']);
					$duedate = _udate($expense['duedate']);
					break;
				case 'yy-mm-dd':
					$expensedate = _mdate($expense['date']);
					$duedate = _mdate($expense['duedate']);
					break;
				case 'dd-mm-yy':
					$expensedate = _cdate($expense['date']);
					$duedate = _cdate($expense['duedate']);
					break;
				case 'yy/mm/dd':
					$expensedate = _zdate($expense['date']);
					$duedate = _zdate($expense['duedate']);
					break;
				case 'dd/mm/yy':
					$expensedate = _kdate($expense['date']);
					$duedate = _kdate($expense['duedate']);
					break;
				default:
					$expensedate = _kdate($expense['date']);
					$duedate = _kdate($expense['duedate']);
			};

			$graph['expenses_num'] += 1;
			$graph['expensesAmount'] += $expense['amount'];

			if ($expense['invoice_id'] == NULL) {
				if ($expense['pago'] == "1") {
					$billstatus = lang2('billed');
					$color = 'success';

					$graph['billed_expenses_num'] += 1;
					$graph['billed_expenses'] += $expense['amount'];
				} else if (strtotime($expense['duedate']) < strtotime(date('Y-m-d')) && $expense['pago'] != "1") {
					$billstatus = lang2('overdue');
					$color = 'danger';

					$graph['overdue_expenses_num'] += 1;
					$graph['overdue_expenses'] += $expense['amount'];
				} else {
					$billstatus = lang2('notbilled');
					$color = 'warning';

					$graph['not_billed_expenses_num'] += 1;
					$graph['not_billed_expenses'] += $expense['amount'];
				}
			} else {
				$billstatus = lang2('billed');
				$color = 'success';
				$graph['billed_expenses_num'] += 1;
				$graph['billed_expenses'] += $expense['amount'];
			}

			if ($expense['customer_id'] != 0) {
				$billable = 'true';
			} else {
				$billable = 'false';
			}


			$data_expenses[] = array(
				'id' => $expense['id'],
				'title' => $expense['title'],
				'prefix' => $appconfig['expense_prefix'],
				'longid' => get_number('expenses', $expense['id'], 'expense', 'expense'),
				'amount' => (float)$expense['amount'],
				'staff' => $expense['staff'],
				'category' => $expense['category'],
				'billstatus' => $billstatus,
				'color' => $color,
				'billable' => $billable,
				'date' => $expensedate,
				'date2' => $expense['date'],
				'duedate' => $duedate,
				'created' => $expense['created'],
				'' . lang2('filterbycategory') . '' => $expense['category'],
				'' . lang2('filterbybillstatus') . '' => $billstatus,
			);
		};

		$graph['not_billed'] = ($graph['expenses_num'] > 0 ? number_format(($graph['not_billed_expenses_num'] * 100) / $graph['expenses_num']) : 0);
		$graph['billed'] = ($graph['expenses_num'] > 0 ? number_format(($graph['billed_expenses_num'] * 100) / $graph['expenses_num']) : 0);
		$graph['overdue'] = ($graph['expenses_num'] > 0 ? number_format(($graph['overdue_expenses_num'] * 100) / $graph['expenses_num']) : 0);

		return response()->setJSON(['expenses' => $data_expenses, 'graph' => $graph]);
	}
}
