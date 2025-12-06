<?php

namespace App\Models;

use CodeIgniter\Model;

include APPPATH . '/third_party/vendor/autoload.php';

use Dompdf\Dompdf;

class Invoices_Model extends Model
{
	function get_invoices($id)
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individual,customers.address as customeraddress,customers.type as type,customers.email as email,customers.phone as customerphone,invoicestatus.name as statusname,invoices.status_id as status_id,invoices.created as created, invoices.id as id,invoices.billing_street as bill_street,invoices.billing_country as bill_country, invoices.billing_city as bill_city, invoices.billing_state as bill_state,invoices.billing_state_id as bill_state_id, invoices.billing_zip as bill_zip,invoices.shipping_street as shipp_street,invoices.shipping_country as shipp_country, invoices.shipping_city as shipp_city, invoices.shipping_state as shipp_state,invoices.shipping_state_id as shipp_state_id, invoices.shipping_zip as shipp_zip,recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate, invoices.invoiceId, invoices.default_payment_method as default_payment_method');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->where('invoices.id', $id)->get()->getRowArray();
	}

	function get_file($id)
	{
		$builder = $this->db->table('files');
		$builder->orderBy('id', 'desc');
		return $builder->where('id', $id)->get()->getRowArray();
	}

	function get_invoices_by_token($token)
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individualindividual,customers.address as customeraddress,customers.email as email,customers.phone as customerphone,invoicestatus.name as statusname,invoices.status_id as status_id, invoices.created as created, invoices.id as id ,invoices.billing_street as bill_street,invoices.billing_country as bill_country, invoices.billing_city as bill_city, invoices.billing_state as bill_state,invoices.billing_state_id as bill_state_id, invoices.billing_zip as bill_zip,invoices.shipping_street as shipp_street,invoices.shipping_country as shipp_country, invoices.shipping_city as shipp_city, invoices.shipping_state as shipp_state,invoices.shipping_state_id as shipp_state_id, invoices.shipping_zip as shipp_zip, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate, invoices.default_payment_method as default_payment_method');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->where('invoices.token', $token)->get()->getRowArray();
	}

	function get_all_invoices()
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individual,customers.address as customeraddress,customers.email as email, customers.type as type, invoicestatus.name as statusname,invoices.status_id as status_id, invoices.created as created, invoices.id as id ,recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('invoices.id', 'desc');
		return $builder->get()->getResultArray();
	}

	function get_all_invoices_by_customer($id)
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individual,customers.address as customeraddress,customers.email as email,invoicestatus.name as statusname,invoices.status_id as status_id, invoices.created as created, invoices.id as id ,recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('invoices.id', 'desc');
		return $builder->where('customer_id', $id)->get()->getResultArray();
	}

	function alteraDataRecurring($id, $i)
	{
		$builder = $this->db->table('recurring');
		$params = ['period' => $i];
		$builder->where('id', $id)->where('relation_type', 'invoice');
		$builder->update($params);
	}


	function dueinvoices()
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individual,customers.address as customeraddress,customers.email as email,customers.type as type,invoicestatus.name as statusname, invoices.created as created, invoices.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('invoices.id', 'desc');
		return $builder->where('DATE(duedate)', date('Y-m-d'))->get()->getResultArray();
	}

	function dueinvoices_by_staff()
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individual,customers.address as customeraddress,customers.email as email,customers.type as type,invoicestatus.name as statusname, invoices.created as created, invoices.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('invoices.id', 'desc');
		return $builder->where('DATE(duedate)', date('Y-m-d'))->where('invoices.staff_id', session()->usr_id)->get()->getResultArray();
	}

	function get_invoices_payment($id)
	{
		$builder = $this->db->table('payments');
		$builder->select('*');
		$builder->join('accounts', 'accounts.id = payments.account_id');
		$builder->where('payments.invoice_id', $id);
		$builder->where('staff.id_company', session()->id_company);
		return $builder->get()->getResultArray();
	}

	function overdueinvoices()
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individual,customers.address as customeraddress,customers.email as email,customers.type as type,invoicestatus.name as statusname, invoices.duedate, invoices.created as created, invoices.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('CURDATE() > invoices.duedate AND invoices.duedate != "0000-00-00" AND invoices.status_id != "4" AND invoices.status_id != "2"');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('invoices.id', 'desc');
		return $builder->get()->getResultArray();
	}

	function overdueinvoices_by_staff()
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individual,customers.address as customeraddress,customers.email as email,customers.type as type,invoicestatus.name as statusname, invoices.duedate, invoices.created as created, invoices.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('CURDATE() > invoices.duedate AND invoices.duedate != "0000-00-00" AND invoices.status_id != "4" AND invoices.status_id != "2"');
		$builder->orderBy('invoices.id', 'desc');
		return $builder->where('invoices.staff_id', session()->usr_id)->get()->getResultArray();
	}

	function get_invoice_detail($id)
	{
		$builder = $this->db->table('invoices');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,customers.company as customercompany,customers.namesurname as individualindividual,customers.address as customeraddress,customers.email as email,customers.phone as customerphone,invoicestatus.name as statusname,invoices.status_id as status_id, invoices.created as created, invoices.id as id ,invoices.billing_street as bill_street,invoices.billing_country as bill_country, invoices.billing_city as bill_city, invoices.billing_state as bill_state,invoices.billing_state_id as bill_state_id, invoices.billing_zip as bill_zip,invoices.shipping_street as shipp_street,invoices.shipping_country as shipp_country, invoices.shipping_city as shipp_city, invoices.shipping_state as shipp_state,invoices.shipping_state_id as shipp_state_id, invoices.shipping_zip as shipp_zip,recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate, invoices.default_payment_method as default_payment_method');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->where('invoices.id', $id)->get()->getRowArray();
	}


	function get_items_invoices($id)
	{
		$builder = $this->db->table('items');
		$builder->selectSum('total');
		$builder->where('relation_type', 'invoice');
		$builder->where('relation', $id);
		return $builder->get()->getRow();
	}

	function get_paid_invoices($id)
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->where('invoice_id', $id);
		return $builder->get()->getRow();
	}

	function recurring_add($params)
	{
		$builder = $this->db->table('recurring');
		$builder->insert($params);
		return $this->db->insertID();
	}

	function recurring_update($id, $params)
	{
		$builder = $this->db->table('recurring');
		$builder->where('relation', $id);
		$builder->where('relation_type', 'invoice');
		return $builder->update($params);
	}

	function get_all_recurring()
	{
		$builder = $this->db->table('recurring');
		$builder->select('*');
		$builder->where('status', '0');
		$builder->where('relation_type', 'invoice');
		$builder->orderBy('id', 'asc');
		return $builder->get()->getResultArray();
	}

	function recurring_invoice($invoices, $items)
	{
		$builder = $this->db->table('invoices');
		$builder->insert($invoices);
		$invoice = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['invoice_series'] ?? $invoice;
		$invoice_number = $appconfig['inv_prefix'] . $number;

		$builder->where('id', $invoice);
		$builder->update(['invoice_number' => $invoice_number]);

		if ($appconfig['invoice_series']) {
			$invoice_number = $appconfig['invoice_series'] + 1;
			$this->Settings_Model->increment_series('invoice_series', $invoice_number);
		}

		foreach ($items as $item) {
			$this->db->table('items')->insert([
				'relation_type' => 'invoice',
				'relation' => $invoice,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $this->moneyEua($item['price']),
				'discount' => $item['discount'],
				'total' => $item['total'],
			]);
		}

		// Log
		$staffname = lang2('recurring_invoice');
		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="#"> ' . $staffname . '</a> Adicionou a fatura <a href="' . base_url() . '/invoices/invoice/' . $invoice . '">' . get_number('invoices', $invoice, 'invoice', 'inv') . '</a>.',
			'staff_id' => 0,
			'customer_id' => $invoices['customer_id'],
		]);

		// Notification
		$staffavatar = 'default-avatar.jpg';
		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('isaddedanewinvoice'),
			'customer_id' => $invoices['customer_id'],
			'perres' => $staffavatar,
			'target' => base_url('area/invoice/' . $invoice),
		]);

		// Sales
		$this->db->table('sales')->insert([
			'invoice_id' => $invoice,
			'status_id' => 3,
			'staff_id' => 0,
			'customer_id' => $invoices['customer_id'],
			'total' => $invoices['total'],
			'date' => date('Y-m-d H:i:s'),
		]);

		return $invoice;
	}

	function update_recurring_date($id)
	{
		$builder = $this->db->table('invoices');
		return $builder->where('id', $id)
			->update(['last_recurring' => date('Y-m-d')]);
	}

	function invoice_add($params)
	{
		$builder = $this->db->table('invoices');
		$builder->insert($params);

		$invoice = $this->db->insertID();
		$appconfig = get_appconfig();
		$number = $appconfig['invoice_series'] ?? $invoice;
		$invoice_number = $appconfig['inv_prefix'] . $number;

		$builder->where('id', $invoice)
			->update(['invoice_number' => $invoice_number]);

		if (request()->getPost('status') === 'true') {
			$loggedinuserid = session()->usr_id;

			$this->db->table('deposits')->insert([
				'token' => md5(uniqid()),
				'relation_type' => 'invoice',
				'category_id' => $this->get_category_id(),
				'staff_id' => $loggedinuserid,
				'customer_id' => request()->getPost('customer'),
				'invoice_id' => $invoice,
				'account_id' => request()->getPost('account'),
				'title' => lang2('invoice'),
				'date' => date('Y-m-d'),
				'created' => date('Y-m-d H:i:s'),
				'amount' => request()->getPost('total'),
				'total_tax' => '0',
				'sub_total' => request()->getPost('total'),
				'status' => '2',
				'description' => sprintf(lang2('paymentfor'), $invoice),
				'last_recurring' => date('Y-m-d'),
			]);

			$this->db->table('payments')->insert([
				'transactiontype' => 0,
				'is_transfer' => 0,
				'invoice_id' => $invoice,
				'staff_id' => $loggedinuserid,
				'amount' => request()->getPost('total'),
				'customer_id' => request()->getPost('customer'),
				'account_id' => request()->getPost('account'),
				'not' => sprintf(lang2('paymentfor'), $invoice),
				'date' => date('Y-m-d H:i:s'),
			]);
		}

		$items = request()->getPost('items');
		foreach ($items as $item) {
			$this->db->table('items')->insert([
				'relation_type' => 'invoice',
				'relation' => $invoice,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'] ?? 'Produto',
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $this->moneyEua($item['price']),
				'discount' => $item['discount'],
				'total' => ($item['quantity'] * $this->moneyEua($item['price'])) - ($item['discount'] / 100 * $item['quantity'] * $this->moneyEua($item['price'])),
			]);
		}

		// LOG
		$staffname = session()->staffname;
		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' . $staffname . '</a> Adicionou a fatura <a href="' . base_url() . 'invoices/invoice/' . $invoice . '">' . get_number('invoices', $invoice, 'invoice', 'inv') . '</a>.',
			'staff_id' => session()->usr_id,
			'customer_id' => request()->getPost('customer'),
		]);

		// NOTIFICATION
		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('isaddedanewinvoice'),
			'customer_id' => request()->getPost('customer'),
			'perres' => session()->staffavatar,
			'target' => base_url('area/invoices/invoice/' . $params['token']),
		]);

		// SALES
		$status_value = request()->getPost('status');
		$status = ($status_value === 'true') ? 2 : 3;

		$this->db->table('sales')->insert([
			'invoice_id' => $invoice,
			'status_id' => $status,
			'staff_id' => session()->usr_id,
			'customer_id' => request()->getPost('customer'),
			'total' => request()->getPost('total'),
			'date' => date('Y-m-d H:i:s'),
		]);

		return $invoice;
	}

	function moneyEua($vl)
	{
		return number_format(str_replace(",", ".", str_replace(".", "", $vl)), 2, '.', '');
	}

	// UPDATE INVOCE
	function update_invoices($id, $params)
	{
		$appconfig = get_appconfig();

		$invoice_data = $this->get_invoices($id);

		if ($invoice_data['invoice_number'] == '') {
			$number = $appconfig['invoice_series'] ? $appconfig['invoice_series'] : $id;
			$invoice_number = $appconfig['inv_prefix'] . $number;

			$this->db->table('invoices')->where('id', $id)->update(['invoice_number' => $invoice_number]);

			if (($appconfig['invoice_series'] != '')) {
				$invoice_number = $appconfig['invoice_series'] + 1;
				$this->Settings_Model->increment_series('invoice_series', $invoice_number);
			}
		}

		$this->db->table('invoices')->where('id', $id)->update($params);

		$items = request()->getPost('items');

		foreach ($items as $item) {
			if (isset($item['id'])) {
				$params = [
					'relation_type' => 'invoice',
					'relation' => $id,
					'product_id' => $item['product_id'],
					'code' => $item['code'],
					'name' => $item['name'],
					'description' => $item['description'],
					'quantity' => $item['quantity'],
					'unit' => $item['unit'],
					'price' => $this->moneyEua($item['price']),
					'discount' => $item['discount'],
					'total' => $item['quantity'] * $this->moneyEua($item['price']) + ((0) / 100 * $item['quantity'] * $this->moneyEua($item['price'])) - (($item['discount']) / 100 * $item['quantity'] * $this->moneyEua($item['price'])),
				];

				$this->db->table('items')->where('id', $item['id'])->update($params);
			}

			if (empty($item['id'])) {
				$this->db->table('items')->insert([
					'relation_type' => 'invoice',
					'relation' => $id,
					'product_id' => $item['product_id'],
					'code' => $item['code'],
					'name' => $item['name'],
					'description' => $item['description'],
					'quantity' => $item['quantity'],
					'unit' => $item['unit'],
					'price' => $this->moneyEua($item['price']),
					'discount' => $item['discount'],
					'total' => $item['quantity'] * $this->moneyEua($item['price']) + ((0) / 100 * $item['quantity'] * $this->moneyEua($item['price'])) - (($item['discount']) / 100 * $item['quantity'] * $this->moneyEua($item['price'])),
				]);
			}
		}

		$invoices = $this->get_invoices($id);

		$this->db->table('sales')->where('invoice_id', $id)->update([
			'status_id' => $invoices['status_id'],
			'staff_id' => session()->get('usr_id'),
			'customer_id' => request()->getPost('customer'),
			'total' => request()->getPost('total'),
		]);

		// LOG
		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');
		$appconfig = get_appconfig();

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> Atualizou a fatura <a href="' . base_url() . 'invoices/invoice/' . $id . '">' . get_number('invoices', $id, 'invoice', 'inv') . '</a>.',
			'staff_id' => $loggedinuserid,
			'customer_id' => request()->getPost('customer'),
		]);

		// NOTIFICATION
		$staffname = session()->get('staffname');
		$staffavatar = session()->get('staffavatar');

		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('uptdatedinvoice'),
			'customer_id' => request()->getPost('customer'),
			'perres' => $staffavatar,
			'target' => base_url('area/invoice/' . $id),
		]);
	}

	function copy_invoice($params, $items, $total)
	{
		$this->db->table('invoices')->insert($params);
		$invoice = $this->db->insertID();

		foreach ($items as $item) {
			$this->db->table('items')->insert([
				'relation_type' => 'invoice',
				'relation' => $invoice,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $this->moneyEua($item['price']),
				'tax_code' => $item['tax_code'],
				'discount' => $item['discount'],
				'total' => $item['quantity'] * $this->moneyEua($item['price']) + ((0) / 100 * $item['quantity'] * $this->moneyEua($item['price'])) - (($item['discount']) / 100 * $item['quantity'] * $this->moneyEua($item['price'])),
			]);
		}

		// LOG
		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');
		$appconfig = get_appconfig();

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> Adicionou a fatura <a href="' . base_url() . 'invoices/invoice/' . $invoice . '">' . get_number('invoices', $invoice, 'invoice', 'inv') . '</a>.',
			'staff_id' => $loggedinuserid,
			'customer_id' => $params['customer_id'],
		]);

		// NOTIFICATION
		$staffname = session()->get('staffname');
		$staffavatar = session()->get('staffavatar');

		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('isaddedanewinvoice'),
			'customer_id' => $params['customer_id'],
			'perres' => $staffavatar,
			'target' => base_url('area/invoice/' . $invoice),
		]);

		//--------------------------------------------------------------------------------------
		$status_value = $params['status_id'];
		$status = ($status_value == 'true') ? 2 : 3;

		$this->db->table('sales')->insert([
			'invoice_id' => $invoice,
			'status_id' => $status,
			'staff_id' => $loggedinuserid,
			'customer_id' => $params['customer_id'],
			'total' => $params['total'],
			'date' => $params['created'],
		]);

		//----------------------------------------------------------------------------------------

		return $invoice;
	}




	//INVOICE DELETE

	function delete_invoices($id, $number)
	{
		$this->db->table('invoices')->delete(['id' => $id]);
		$this->db->table('items')->delete(['relation_type' => 'invoice', 'relation' => $id]);
		$this->db->table('payments')->delete(['invoice_id' => $id]);
		$this->db->table('sales')->delete(['invoice_id' => $id]);
		$this->db->table('recurring')->delete(['relation_type' => 'invoice', 'relation' => $id]);
		$this->db->table('expenses')->where('invoice_id', $id)->update(['invoice_id' => null]);
		$this->db->table('deposits')->delete(['relation_type' => 'invoice', 'invoice_id' => $id]);
		$this->db->table('pending_process')->delete(['process_relation' => $id, 'process_relation_type' => 'invoice']);

		// LOG
		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> Excluiu a fatura ' . $number,
			'staff_id' => $loggedinuserid,
		]);
	}

	function get_invoice_year()
	{
		return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM invoices ORDER BY year DESC')->get()->getResultArray();
	}

	function update_pdf_status($id, $value)
	{
		$this->db->table('invoices')->where('id', $id)->update(['pdf_status' => $value]);
	}

	function generate_pdf($id)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '2048M');

		if (!is_dir('uploads/files/invoices/' . $id)) {
			mkdir('./uploads/files/invoices/' . $id, 0777, true);
		}

		$data['invoice'] = $this->get_invoice_detail($id);
		$data['billing_country'] = get_country($data['invoice']['bill_country']);
		$data['billing_state'] = get_state_name($data['invoice']['bill_state'], $data['invoice']['bill_state_id']);
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);
		$data['country'] = get_country($data['settings']['country_id']);
		$default_payment_method = $data['invoice']['default_payment_method'];

		if ($default_payment_method == 'bank') {
			$modes = $this->Settings_Model->get_payment_gateway_data();
			$method = $modes['bank'];
		} else {
			$method = lang($data['invoice']['default_payment_method']);
		}

		$data['default_payment'] = $method;
		$data['payments'] = $this->get_invoices_payment($id);
		$data['items'] = $this->db->table('items')->select('*')->where(['relation_type' => 'invoice', 'relation' => $id])->get()->getResultArray();

		$file_name = get_number('invoices', $id, 'invoice', 'inv') . '.pdf';
		$html = view('invoices/pdf', $data, TRUE);

		$this->dompdf = new \Dompdf\Dompdf();
		$this->dompdf->loadHtml($html);
		$this->dompdf->set_option('isRemoteEnabled', TRUE);
		$this->dompdf->set_option('isHtml5ParserEnabled', TRUE);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();

		$output = $this->dompdf->output();
		file_put_contents('uploads/files/invoices/' . $id . '/' . $file_name, $output);

		$this->update_pdf_status($id, '1');

		$this->dompdf->clear();
		$this->dompdf = null;
		return true;
	}

	function get_category_id()
	{
		$builder = $this->db->table('depositcat');
		$builder->select('id');
		$builder->where('name', lang2('invoice'));
		$query = $builder->get()->getRow();

		if ($query) {
			return $query->id;
		} else {
			$params = [
				'name' => lang2('invoice'),
				'description' => lang2('invoice'),
			];
			$builder->insert($params);
			return $this->db->insertID();
		}
	}


	function get_all_invoices_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('invoices');

		$builder->select(
			'*, 
			staff.staffname as staffmembername, 
			staff.staffavatar as staffmemberresim, 
			customers.company as customercompany, 
			customers.namesurname as individual, 
			customers.address as customeraddress, 
			customers.email as email, 
			customers.type as type, 
			invoicestatus.name as statusname, 
			invoices.status_id as status_id, 
			invoices.created as created, 
			invoices.id as id, 
			recurring.id as recurring_id, 
			recurring.status as recurring_status, 
			recurring.relation_type as recurring_relation_type, 
			recurring.period as recurring_period, 
			recurring.type as recurring_type, 
			recurring.end_date as recurring_endDate'
		);

		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');

		$builder->where('staff.id_company', session()->get('id_company'));

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$builder->where('invoices.duedate >=', request()->getPost('dt_de'));
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$builder->where('invoices.duedate <=', request()->getPost('dt_ate'));
		}
		if (request()->getPost('flt_cliente') != null && request()->getPost('flt_cliente') != "-1") {
			$builder->where('invoices.customer_id', request()->getPost('flt_cliente'));
		}

		$builder->orderBy('invoices.duedate', 'desc');

		if ($staff_id) {
			return $builder->where('invoices.staff_id', $staff_id)->get()->getResultArray();
		} else {
			return $builder->get()->getResultArray();
		}
	}




	public function get_invoice_detail_by_privilegs($id, $staff_id = '')
{
    $builder = $this->db->table('invoices');
    $builder->select('
        invoices.*, 
        staff.staffname as staffmembername, 
        staff.staffavatar as staffmemberresim, 
        customers.company as customercompany, 
        customers.namesurname as individual, 
        customers.address as customeraddress, 
        customers.email as email, 
        customers.phone as customerphone, 
        invoicestatus.name as statusname, 
        invoices.status_id as status_id, 
        invoices.created as created, 
        invoices.id as id, 
        invoices.billing_street as bill_street, 
        invoices.billing_country as bill_country, 
        invoices.billing_city as bill_city, 
        invoices.billing_state as bill_state, 
        invoices.billing_state_id as bill_state_id, 
        invoices.billing_zip as bill_zip, 
        invoices.shipping_street as shipp_street, 
        invoices.shipping_country as shipp_country, 
        invoices.shipping_city as shipp_city, 
        invoices.shipping_state as shipp_state, 
        invoices.shipping_state_id as shipp_state_id, 
        invoices.shipping_zip as shipp_zip, 
        recurring.id as recurring_id, 
        recurring.status as recurring_status, 
        recurring.relation_type as recurring_relation_type, 
        recurring.period as recurring_period, 
        recurring.type as recurring_type, 
        recurring.end_date as recurring_endDate, 
        invoices.default_payment_method as default_payment_method'
    );
    $builder->join('customers', 'invoices.customer_id = customers.id', 'left');
    $builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
    $builder->join('recurring', 'invoices.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
    $builder->join('staff', 'staff.id = invoices.staff_id', 'left');
    $builder->where('staff.id_company', session()->get('id_company'));

    if ($staff_id) {
        $builder->where(['invoices.id' => $id, 'invoices.staff_id' => session()->get('usr_id')]);
    } else {
        $builder->where(['invoices.id' => $id]);
    }

    return $builder->get()->getRowArray();
}


	function get_files($id)
	{
		return $this->db->table('files')->where(['relation' => $id, 'relation_type' => 'invoice'])->orderBy('id', 'desc')->get()->getResultArray();
	}
}
