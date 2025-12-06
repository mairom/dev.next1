<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
class Gateway extends BaseController
{

	function __construct()
	{

		$this->load->library("Omni");
		$this->lang->load('english_default', 'english');
		$this->lang->load('english', 'english');
		$this->model('Invoices_Model');
		$this->model('Customers_Model');
		$this->model('Settings_Model');
	}

	function success()
	{
		return view('gateway/success');
	}

	function cancel()
	{
		return view('gateway/cancel');
	}

	public function paypal_ipn($token)
	{
		$settings = $this->Settings_Model->get_settings_ciuis();

		if (isset($token)) {
			//$payment = $this->Settings_Model->get_payment_gateway_data();
			$payment = $this->Settings_Model->payment_mode('paypal');
			$invoice = $this->Invoices_Model->get_invoices_by_token($token);

			$this->db->table('invoices')->where('id', $invoice['id'])->update([
				'status_id' => 2,
				'duedate' => 0
			]);

			$this->db->table('sales')->where('invoice_id', $invoice['invoice_id'])->update([
				'status_id' => 2,
				'staff_id' => $invoice['staff_id'],
				'customer_id' => $invoice['customer_id'],
				'total' => $invoice['amount']
			]);

			$this->db->table('payments')->insert([
				'transactiontype' => 0,
				'invoice_id' => $invoice['id'],
				'staff_id' => $invoice['staff_id'],
				'amount' => $invoice['total'],
				'customer_id' => $invoice['customer_id'],
				'account_id' => $payment['payment_record_account'],
				'not' => lang2('paymentfor') . ' ' . lang2('invoice') . ' ' . $invoice['id'],
				'mode' => lang2('paypal'),
				'date' => date('Y-m-d H:i:s')
			]);

			return redirect()->to('gateway/success');
		}
	}

	function paypal($token)
	{
		//Set variables for paypal form
		$invoice = $this->Invoices_Model->get_invoices_by_token($token);
		$returnURL = base_url('gateway/paypal_ipn/' . $invoice['token'] . '');
		$cancelURL = base_url('gateway/cancel');
		$notifyURL = base_url('pay/ipn'); //ipn url
		$userID = 1; //current user id
		$invoiceno = get_number('invoices', $invoice['id'], 'invoice', 'inv') . '';
		$logo = base_url() . 'assets/img/logo.png';
		$this->omni->add_field('return', $returnURL);
		$this->omni->add_field('cancel_return', $cancelURL);
		$this->omni->add_field('notify_url', $notifyURL);
		$this->omni->add_field('item_name', $invoiceno);
		$this->omni->add_field('custom', $userID);
		$this->omni->add_field('item_number', get_number('invoices', $invoice['id'], 'invoice', 'inv'));
		$this->omni->add_field('amount', $invoice['total']);
		$this->omni->image($logo);
		$this->omni->paypal_auto_form();
	}

	function razorpay($token)
	{
		$appconfig = get_appconfig();
		$invoice = $this->Invoices_Model->get_invoices_by_token($token);
		if ($invoice) {
			//$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
			$data['payment'] = $this->Settings_Model->payment_mode('razorpay');
			if ($data['payment']['active'] == '1') {
				//$this->load->library( "Razorpay" );
				$settings = $this->Settings_Model->get_settings_ciuis();
				$data['crm_name'] = $settings['crm_name'];
				$data['logo'] = base_url('uploads/ciuis_settings/' . $settings['logo']);
				$data['customer'] = $this->Customers_Model->get_customers($invoice['customer_id']);
				$data['invoice_id'] = sprintf(lang('paymentfor'),  get_number('invoices', $invoice['id'], 'invoice', 'inv'));
				$data['currency'] = $this->Settings_Model->get_currency();
				$data['invoice'] = $invoice;
				$data['cust_name'] = ($data['customer']['type'] === 1) ? $data['customer']['namesurname'] : $data['customer']['company'];
				$data['invoice']['inv_id'] =  get_number('invoices', $invoice['id'], 'invoice', 'inv');
				return view('gateway/razorpay', $data);
			} else {
				return redirect()->to(base_url('panel'));
			}
		}
	}

	function pay_razorpay()
	{
		$data = request()->getPost();
		//$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
		$data['payment'] = $this->Settings_Model->payment_mode('razorpay');
		if ($data['payment']['active'] == '1') {
			$this->load->library("razorpay");
			$pay = $this->razorpay->razorpay_success($data);
			if ($pay === true) {
				$payment = $data['payment'];
				$invoice = $this->Invoices_Model->get_invoices($data['invoice_id']);
				$response = $this->db->table('invoices')
					->where('id', $data['invoice_id'])
					->update(['status_id' => 2, 'duedate' => 0]);

				$response = $this->db->table('sales')
					->where('invoice_id', $data['invoice_id'])
					->update([
						'status_id' => 2,
						'staff_id' => $invoice['staff_id'],
						'customer_id' => $invoice['customer_id'],
						'total' => $data['amount']
					]);

				$this->db->table('payments')
					->insert([
						'transactiontype' => 0,
						'invoice_id' => $invoice['id'],
						'staff_id' => $invoice['staff_id'],
						'amount' => $data['amount'],
						'customer_id' => $invoice['customer_id'],
						'account_id' => $payment['payment_record_account'],
						'not' => lang2('paymentfor') . ' ' . lang2('invoice') . ' ' . $invoice['id'],
						'mode' => lang2('razorpay'),
						'date' => date('Y-m-d H:i:s')
					]);

				return redirect()->to(base_url('gateway/success'));
			} else {
				return redirect()->to(base_url('gateway/cancel'));
			}
		} else {
			return redirect()->to(base_url('area/panel'));
		}
	}

	function stripe($token)
	{
		$appconfig = get_appconfig();
		$invoice = $this->Invoices_Model->get_invoices_by_token($token);
		if ($invoice) {
			//$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
			$data['payment'] = $this->Settings_Model->payment_mode('stripe');
			if ($data['payment']['active'] == '1') {
				$settings = $this->Settings_Model->get_settings_ciuis();
				$data['crm_name'] = $settings['crm_name'];
				$data['logo'] = base_url('uploads/ciuis_settings/' . $settings['logo']);
				$data['customer'] = $this->Customers_Model->get_customers($invoice['customer_id']);
				$data['invoice_id'] = sprintf(lang('paymentfor'), get_number('invoices', $invoice['id'], 'invoice', 'inv'));
				$data['currency'] = $this->Settings_Model->get_currency();
				$data['invoice'] = $invoice;
				$data['invoice']['inv_id'] =   get_number('invoices', $invoice['id'], 'invoice', 'inv');
				return view('gateway/stripe', $data);
			} else {
				return redirect()->to(base_url('panel'));
			}
		}
	}

	public function stripe_success()
	{
		$data = request()->getPost();
		// echo '<script>console.log('.json_encode($data).')</script>';
		$response = $this->omni->stripe_success($data);
		if ($response === true) {
			//$payment = $this->Settings_Model->get_payment_gateway_data();
			$payment = $this->Settings_Model->payment_mode('stripe');
			$invoice = $this->Invoices_Model->get_invoices($data['invoice_id']);
			$response = $this->db->table('invoices')
				->where('id', $data['invoice_id'])
				->update([
					'status_id' => 2,
					'duedate' => 0
				]);

			$response = $this->db->table('sales')
				->where('invoice_id', $data['invoice_id'])
				->update([
					'status_id' => 2,
					'staff_id' => $invoice['staff_id'],
					'customer_id' => $invoice['customer_id'],
					'total' => $data['amount']
				]);

			$this->db->table('payments')->insert(array(
				'transactiontype' => 0,
				'invoice_id' => $invoice['id'],
				'staff_id' => $invoice['staff_id'],
				'amount' => $data['amount'],
				'customer_id' => $invoice['customer_id'],
				'account_id' => $payment['payment_record_account'],
				'not' => '' . lang2('paymentfor') . ' ' . lang2('invoice') . ' ' . $invoice['id'] . '',
				'mode' => lang2('stripe'),
				'date' => date('Y-m-d H:i:s'),
			));
			return redirect()->to(base_url('gateway/success'));
		} else {
			return redirect()->to(base_url('gateway/cancel'));
		}
	}

	public function authorize($token)
	{
		$data['invoice'] = $this->Invoices_Model->get_invoices_by_token($token);
		$invoice = $this->Invoices_Model->get_invoices_by_token($token);
		$data['customer'] = $this->Customers_Model->get_customers($invoice['customer_id']);
		return view('gateway/authorize/authorize', $data);
	}

	public function pushPayment()
	{
		$settings = $this->Settings_Model->get_settings_ciuis();
		//$payment = $this->Settings_Model->get_payment_gateway_data();
		$payment = $this->Settings_Model->payment_mode('authorize');
		$dataCustomers = array(
			"fname" => request()->getPost('fname'),
			"lname" => request()->getPost('lname'),
			"address" => request()->getPost('address'),
			"city" => request()->getPost('city'),
			"state" => request()->getPost('state'),
			"country" => request()->getPost('country'),
			"zip" => request()->getPost('zip'),
			"phone" => request()->getPost('phone'),
			"email" => request()->getPost('email'),
			"cnumber" => request()->getPost('cnumber'),
			"cexpdate" => request()->getPost('cexpdate'),
			"ccode" => request()->getPost('ccode'),
			"cdesc" => request()->getPost('cdesc'),
			"amount" => request()->getPost('camount'),
			"inv_id" => request()->getPost('inv_id')
		);
		$result = $this->omni->chargerCreditCard($dataCustomers);
		if ($result) {
			$data['authorize_result'] = $result;
			$invoice = $this->Invoices_Model->get_invoices(request()->getPost('inv_id'));
			$response = $this->db->table('invoices')
				->where('id', request()->getPost('inv_id'))
				->update([
					'status_id' => 2,
					'duedate' => 0
				]);

			$response = $this->db->table('sales')
				->where('invoice_id', request()->getPost('inv_id'))
				->update([
					'status_id' => 2,
					'staff_id' => $invoice['staff_id'],
					'customer_id' => $invoice['customer_id'],
					'total' => request()->getPost('camount'),
				]);

			$this->db->table('payments')->insert([
				'transactiontype' => 0,
				'invoice_id' => $invoice['id'],
				'staff_id' => $invoice['staff_id'],
				'amount' => request()->getPost('camount'),
				'customer_id' => $invoice['customer_id'],
				'account_id' => $payment['payment_record_account'],
				'not' => sprintf(lang('paymentfor'), $invoice['id']),
				'mode' => lang2('authorize_aim'),
				'date' => date('Y-m-d H:i:s'),
			]);

			return redirect()->to('gateway/success');
		} else {
			return redirect()->to('gateway/cancel');
		}
	}

	function payumoney($token)
	{
		$invoice = $this->Invoices_Model->get_invoices_by_token($token);
		if ($invoice) {
			//$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
			$data['payment'] = $this->Settings_Model->payment_mode('payumoney');
			if ($data['payment']['active'] == '1') {
				$data['customer'] = $this->Customers_Model->get_customers($invoice['customer_id']);
				$data['txnid'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
				$data['hash'] = $this->get_invoice_hash([
					'key' => $data['payment']['input_value1'],
					'txnid' => $data['txnid'],
					'amount' => $invoice['total'],
					'phone' => $data['customer']['phone'],
					'productinfo' => 'Invoice Payment',
					'firstname' => $data['customer']['namesurname'] ? $data['customer']['namesurname'] : $data['customer']['company'],
					'email' => $data['customer']['email'],
					'udf1' => $invoice['id']
				], $data['payment']['input_value2']);
				$data['payu']['url'] = "https://secure.payu.in/_payment";
				$hash_string = $data['payment']['input_value2'];
				$data['hash'] = strtolower(hash('sha512', $hash_string));
				$data['invoice_id'] = sprintf(lang('paymentfor'), get_number('invoices', $invoice['id'], 'invoice', 'inv'));
				$data['currency'] = $this->Settings_Model->get_currency();
				$data['invoice'] = $invoice;
				return view('gateway/payumoney', $data);
			}
		}
	}

	function payumoney_success()
	{
		$data = request()->getPost();
		//$response = $this->omni->stripe_success($data);
		if ($data) {
			//$payment = $this->Settings_Model->get_payment_gateway_data();
			$payment = $this->Settings_Model->payment_mode('payumoney');
			$invoice = $this->Invoices_Model->get_invoices($data['invoice_id']);
			$response = $this->db->table('invoices')
				->where('id', $data['invoice_id'])
				->update([
					'status_id' => 2,
					'duedate' => 0
				]);

			$response = $this->db->table('sales')
				->where('invoice_id', $data['invoice_id'])
				->update([
					'status_id' => 2,
					'staff_id' => $invoice['staff_id'],
					'customer_id' => $invoice['customer_id'],
					'total' => $data['amount'],
				]);

			$this->db->table('payments')->insert([
				'transactiontype' => 0,
				'invoice_id' => $invoice['id'],
				'staff_id' => $invoice['staff_id'],
				'amount' => $data['amount'],
				'customer_id' => $invoice['customer_id'],
				'account_id' => $payment['payment_record_account'],
				'not' => sprintf('%s %s %d', lang2('paymentfor'), lang2('invoice'), $invoice['id']),
				'mode' => lang2('payumoney'),
				'date' => date('Y-m-d H:i:s'),
			]);

			return redirect()->to(base_url('gateway/success'));
		} else {
			return redirect()->to(base_url('gateway/cancel'));
		}
	}

	public function get_invoice_hash($posted, $salt)
	{
		$hash_sequence = 'key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10';
		$hashVarsSeq = explode('|', $hash_sequence);
		$hash_string = '';
		foreach ($hashVarsSeq as $hash_var) {
			$hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
			$hash_string .= '|';
		}
		$hash_string .= $salt;
		$hash = strtolower(hash('sha512', $hash_string));
		return $hash;
	}

	function ccavenue($token)
	{
		$invoice = $this->Invoices_Model->get_invoices_by_token($token);
		if ($invoice) {
			//$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
			$data['payment'] = $this->Settings_Model->payment_mode('ccavenue');
			if ($data['payment']['active'] == '1') {
				$data['customer'] = $this->Customers_Model->get_customers($invoice['customer_id']);
				$data['txnid'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
				$data['invoice_id'] = sprintf(lang('paymentfor'), get_number('invoices', $invoice['id'], 'invoice', 'inv'));
				$data['currency'] = $this->Settings_Model->get_currency();
				$data['invoice'] = $invoice;
				return view('gateway/ccavenue', $data);
			} else {
				return redirect()->to(base_url('panel'));
			}
		}
	}

	public function ccavenue_confirm()
	{
		//$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
		$data['payment'] = $this->Settings_Model->payment_mode('ccavenue');
		if ($data['payment']['active'] == '1') {
			$data['title'] = lang2('make_payment') . 'via' . lang2('ccavenue');
			return view('gateway/pay_ccavenue', $data);
		} else {
			return redirect()->to(base_url('panel'));
		}
	}

	public function ccavenue_success()
	{
		$order_info = $this->ccavenue_order_status($_POST);
		//$payment = $this->Settings_Model->get_payment_gateway_data();
		$payment = $this->Settings_Model->payment_mode('ccavenue');
		if ($order_info['order_status'] == 'Success') {
			$invoice = $this->Invoices_Model->get_invoices($order_info['order_id']);
			$response = $this->db->table('invoices')
				->where('id', $order_info['order_id'])
				->update([
					'status_id' => 2,
					'duedate' => 0
				]);

			$response = $this->db->table('sales')
				->where('invoice_id', $order_info['order_id'])
				->update([
					'status_id' => 2,
					'staff_id' => $invoice['staff_id'],
					'customer_id' => $invoice['customer_id'],
					'total' => $order_info['amount'],
				]);

			$this->db->table('payments')->insert([
				'transactiontype' => 0,
				'invoice_id' => $invoice['id'],
				'staff_id' => $invoice['staff_id'],
				'amount' => $order_info['amount'],
				'customer_id' => $invoice['customer_id'],
				'account_id' => $payment['payment_record_account'],
				'not' => sprintf(lang('paymentfor'), $invoice['id']),
				'mode' => lang2('ccavenue'),
				'date' => date('Y-m-d H:i:s'),
			]);

			if ($response) {
				session()->setFlashdata('ntf1', '' . $result['message']);
			} else {
				session()->setFlashdata('ntf1', '' . $result['message']);
			}
		} else {
			session()->setFlashdata('ntf4', '' . 'Thank You. Your transaction status is ' . $order_info['order_status']);
		}
		return redirect()->to('gateway/success');
	}

	public function ccavenue_failure()
	{
		$order_info = $this->ccavenue_order_status($_POST);
		if (!$order_info) {
			session()->setFlashdata('ntf3', '' . 'Invalid Transaction');
		} else {
			session()->setFlashdata('ntf3', '' . 'Thank You. Your transaction status is ' . $order_info['order_status']);
		}
		return redirect()->to('gateway/cancel');
	}

	public function ccavenue_order_status($data)
	{
		require_once APPPATH . '/libraries/Ccavenue.php';
		//$payment = $this->Settings_Model->get_payment_gateway_data();
		$payment = $this->Settings_Model->payment_mode('ccavenue');
		$workingKey = $payment['input_value2'];
		$encResponse = $data["encResp"];
		$rcvdString = decrypt_cc($encResponse, $workingKey);
		$result = "";
		$decryptValues = explode('&', $rcvdString);
		$dataSize = sizeof($decryptValues);
		for ($i = 0; $i < $dataSize; $i++) {
			$information = explode('=', $decryptValues[$i]);
			if ($i == 3) $order_status = $information[1];
		}
		return $order_status;
		for ($i = 0; $i < $dataSize; $i++) {
			$information = explode('=', $decryptValues[$i]);
			if ($i == 0) {
				$result['order_id'] = $information[1];
			};
			if ($i == 1) {
				$result['tracking_id'] = $information[1];
			};
			if ($i == 3) {
				$result['order_status'] = $information[1];
			};
			if ($i == 10) {
				$result['amount'] = $information[1];
			};
		}
		return $result;
	}
}
