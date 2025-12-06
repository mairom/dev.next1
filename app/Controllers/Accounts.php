<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Accounts extends BaseController
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

		$data['title'] = lang2('accounts');

		$data['accounts'] = $this->Accounts_Model->get_all_accounts();

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		return view('accounts/index', $data);
	}



	function account($id)
	{

		if ($this->Privileges_Model->check_privilege('accounts', 'all')) {

			$data['title'] = lang2('account');

			$data['account'] = $this->Accounts_Model->get_accounts($id);

			return view('accounts/account', $data);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('accounts'));
		}
	}



	function create()
	{

		if ($this->Privileges_Model->check_privilege('accounts', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = request()->getPost('name');

				$type = request()->getPost('type');

				$bankname = request()->getPost('bankname');

				$branchbank = request()->getPost('branchbank');

				$account = request()->getPost('account');

				$iban = request()->getPost('iban');

				$hasError = false;

				if ($name == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('account') . ' ' . lang2('name');
				} else if ($account == '' && $type == 1) {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('account') . ' ' . lang2('number');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$params = array(

						'name' => request()->getPost('name'),
						'type' => request()->getPost('type'),
						'bankname' => request()->getPost('bankname'),
						'branchbank' => request()->getPost('branchbank'),
						'account' => request()->getPost('account'),
						'iban' => request()->getPost('iban'),
						'status_id' => 0,
						"id_company" => session()->id_company

					);

					$id = $this->Accounts_Model->create($params);

					$data = array(

						'id' => $id,

						'name' => request()->getPost('name'),

						'amount' => 0,

						'icon' => 'mdi mdi-balance',

						'status' => lang2('accuntactive'),

					);

					$data['success'] = true;

					$data['message'] = lang2('accountadded');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function update($id)
	{

		if ($this->Privileges_Model->check_privilege('accounts', 'all')) {

			if ($this->Privileges_Model->check_privilege('accounts', 'edit')) {

				$data['accounts'] = $this->Accounts_Model->get_accounts($id);

				if (isset($data['accounts']['id'])) {

					if (isset($_POST) && count($_POST) > 0) {

						$params = array(

							'name' => request()->getPost('name'),
							'bankname' => request()->getPost('bankname'),
							'branchbank' => request()->getPost('branchbank'),
							'account' => request()->getPost('account'),
							'iban' => request()->getPost('iban'),
							'status_id' => request()->getPost('status'),
							"id_company" => session()->id_company
						);

						$this->Accounts_Model->update2($id, $params);

						$data['success'] = true;

						$data['message'] = lang2('accountupdated');

						return response()->setJSON($data);
					} else {

						return view('accounts/', $data);
					}
				} else {

					show_error(lang2('expense_not_exist'));
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function make_transfer($id)
	{

		if ($this->Privileges_Model->check_privilege('accounts', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$from_account_id = request()->getPost('from_account_id');

				$to_account_id = request()->getPost('to_account_id');

				$amount = request()->getPost('amount');

				$account = $this->Accounts_Model->account_details($id);

				$hasError = false;



				if ($to_account_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('account');
				} else if ($account['account_total'] == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('amount');
				} else if ($from_account_id == $to_account_id) {

					$hasError = true;

					$data['message'] = lang2('sameaccounterror') . ' ' . lang2('account');
				} else if ($account['account_total'] < $amount) {

					$hasError = true;

					$data['message'] = lang2('insufficient_balance');
				} else if ($amount <= 0) {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('amount');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}



				if (!$hasError) {

					$this->db->table('payments')->insert( array(

						'transactiontype' => 0,

						'is_transfer' => 1,

						'expense_id' => 0,

						'staff_id' => session()->usr_id,

						'amount' => request()->getPost('amount'),

						'account_id' => request()->getPost('to_account_id'),

						'customer_id' => 0,

						'not' => lang2('money_transfer_between_accounts'),

						'date' => date('Y-m-d h:i:s'),

					));

					$this->db->table('payments')->insert( array(

						'transactiontype' => 1,

						'is_transfer' => 1,

						'expense_id' => 0,

						'staff_id' => session()->usr_id,

						'amount' => request()->getPost('amount'),

						'account_id' => request()->getPost('from_account_id'),

						'customer_id' => 0,

						'not' => lang2('money_transfer_between_accounts'),

						'date' => date('Y-m-d h:i:s'),

					));

					$data['success'] = true;

					$data['message'] = lang2('success');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function remove($id)
	{

		if ($this->Privileges_Model->check_privilege('accounts', 'all')) {

			if ($this->Privileges_Model->check_privilege('accounts', 'delete')) {

				$accounts = $this->Accounts_Model->get_accounts($id);

				if (isset($accounts['id'])) {

					$result = $this->Accounts_Model->delete_account($id);

					$account = lang2('account');

					if ($result) {

						$data['message'] = sprintf(lang2('success_delete'), $account . '');

						$data['success'] = true;

						return response()->setJSON($data);
					} else {

						$data['message'] = sprintf(lang2('cant_delete'), $account . '');

						$data['success'] = false;

						return response()->setJSON($data);
					}
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function get_account($id)
	{

		if ($this->Privileges_Model->check_privilege('accounts', 'all')) {

			$account = $this->Accounts_Model->get_accounts($id);

			$payments = $this->db->table('payments')->where('account_id', $id)->orderBy('id', 'desc')->get()->getResultArray();

			// Sum of incoming payments
			$builder = $this->db->table('payments');
			$builder->selectSum('amount');
			$builder->where('account_id', $id);
			$builder->where('transactiontype', 0);
			$account_incomings_sum = $builder->get()->getRow()->amount;

			// Sum of outgoing payments
			$builder = $this->db->table('payments');
			$builder->selectSum('amount');
			$builder->where('account_id', $id);
			$builder->where('transactiontype', 1);
			$account_outgoings_sum = $builder->get()->getRow()->amount;


			$account_sum = ($account_incomings_sum - $account_outgoings_sum);

			if (!empty($account_sum)) {

				$account_total = $account_incomings_sum - $account_outgoings_sum;
			} else {

				$account_total = 0;
			}

			switch ($account['status_id']) {

				case '1':

					$is_status = false;

					break;

				case '0':

					$is_status = true;

					break;
			}



			$payments_data = array();

			foreach ($payments as $payment) {

				if ($payment['customer_id'] != 0) {

					$customer = $this->Customers_Model->get_customers($payment['customer_id']);

					switch ($customer['type']) {

						case '0':

							$name = $customer['company'];

							$type = lang2('corporatecustomers');

							break;

						case '1':

							$name = $customer['namesurname'];

							$type = lang2('individual');

							break;
					};
				} else {

					$name = 'false';
				}

				$staff = $this->Staff_Model->get_staff($payment['staff_id']);



				if ($payment['customer_id'] != 0) {

					$for_customer = true;
				} else {

					$for_customer = false;
				}

				$payments_data[] = array(

					'id' => $payment['id'],

					'transactiontype' => $payment['transactiontype'],

					'is_transfer' => $payment['is_transfer'],

					'invoice_id' => $payment['invoice_id'],

					'expense_id' => $payment['expense_id'],

					'customer_id' => $payment['customer_id'],

					'customer' => $name,

					'amount' => $payment['amount'],

					'account_id' => $payment['account_id'],

					'date' => date(DATE_ISO8601, strtotime($payment['date'])),

					'attachment' => $payment['attachment'],

					'staff_id' => $payment['staff_id'],

					'not' => $payment['not'],

					'staff' => $staff['staffname'],

					'for_customer' => $for_customer,

				);
			};



			$data_account = array(

				'id' => $account['id'],

				'name' => $account['name'],

				'type' => $account['type'],

				'bankname' => $account['bankname'],

				'branchbank' => $account['branchbank'],

				'account' => $account['account'],

				'iban' => $account['iban'],

				'account_total' => $account_total,

				'status' => $is_status,

				'payments' => $payments_data,

			);

			return response()->setJSON($data_account);
		}
	}



	function get_accounts()
	{

		if ($this->Privileges_Model->check_privilege('accounts', 'all')) {

			$accounts = $this->Accounts_Model->get_all_accounts();

			$data_account = array();

			foreach ($accounts as $account) {

				switch ($account['type']) {

					case '0':

						$icon = 'mdi mdi-balance-wallet';

						break;

					case '1':

						$icon = 'mdi mdi-balance';

						break;
				};

				switch ($account['status_id']) {

					case '0':

						$status = lang2('accuntactive');

						break;

					case '0':

						$status = lang2('accuntnotactive');

						break;
				};

				$data_account[] = array(

					'id' => $account['id'],

					'name' => $account['name'],

					'amount' => $data = $amountby = $this->Report_Model->get_account_amount($account['id']),

					'icon' => $icon,

					'status' => $status,

				);
			};

			return response()->setJSON($data_account);
		}
	}
}
