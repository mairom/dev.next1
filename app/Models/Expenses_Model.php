<?php

namespace App\Models;

use CodeIgniter\Model;

include APPPATH . '/third_party/vendor/autoload.php';

use Dompdf\Dompdf;

class Expenses_Model extends Model
{

	/* Get Expense by ID */


	function get_expenses($id)
	{
		return $this->db->table('expenses')
			->select('*, customers.company as customer, customers.type as type, customers.namesurname as individual, customers.email as customeremail, accounts.name as account, expensecat.name as category, staff.staffname as staff, expenses.description as desc, expenses.id as id, expenses.staff_id as staff_id, staff.email as staffemail')
			->join('customers', 'expenses.customer_id = customers.id', 'left')
			->join('accounts', 'expenses.account_id = accounts.id', 'left')
			->join('expensecat', 'expenses.category_id = expensecat.id', 'left')
			->join('staff', 'expenses.expense_created_by = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('expenses.id', $id)
			->orderBy('expenses.id', 'desc')
			->get()
			->getRowArray();
	}

	function all_expenses($id, $staff_id = '')
	{
		$builder = $this->db->table('expenses')
			->select('*, expenses.created, customers.company as customer, customers.type as type, customers.namesurname as individual,
				customers.email as customeremail, customers.phone as customer_phone, accounts.name as account, expensecat.name as category,
				expenses.description as desc, expenses.id as id, recurring.id as recurring_id, 
				recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, 
				recurring.type as recurring_type, recurring.end_date as recurring_endDate, customers.billing_street, customers.billing_city, 
				customers.billing_state, customers.billing_zip, customers.country_id, customers.taxoffice as customer_tax, 
				customers.taxnumber as customer_taxnum, 
				criador.staffname as staff, criador.id as staff_id,
				funcionario.staffname as funcionario_name, funcionario.id as funcionario_id')
			->join('customers', 'expenses.customer_id = customers.id', 'left')
			->join('accounts', 'expenses.account_id = accounts.id', 'left')
			->join('expensecat', 'expenses.category_id = expensecat.id', 'left')
			->join('staff as criador', 'expenses.expense_created_by = criador.id', 'left')
			->join('staff as funcionario', 'expenses.staff_id = funcionario.id', 'left')
			->join('recurring', 'expenses.id = recurring.relation AND recurring.relation_type = "expense"', 'left')
			->join('formas_pagamento', 'formas_pagamento.id_forma = expenses.id_forma_pgt', 'left')
			->where('criador.id_company', session()->id_company)
			->orderBy('expenses.id', 'desc');

		if ($staff_id) {
			$builder->where('(expense_created_by=' . $staff_id . ' OR expenses.staff_id=' . $staff_id . ')');
		}

		return $builder->where('expenses.id', $id)
			->get()
			->getRowArray();
	}




	function generate_pdf($id)
	{

		ini_set('max_execution_time', 0);

		ini_set('memory_limit', '2048M');

		if (!is_dir('uploads/files/expenses/' . $id)) {

			mkdir('./uploads/files/expenses/' . $id, 0777, true);
		}

		$data['expense'] = $this->all_expenses($id);

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

		$data['billing_country'] = get_country($data['expense']['country_id']);

		$data['billing_state'] = get_state_name($data['expense']['billing_state'], $data['expense']['billing_state_id']);

		$data['country'] = get_country($data['settings']['country_id']);

		$data['items'] = $this->db->table('items')
			->select('*')
			->where(['relation_type' => 'expense', 'relation' => $id])
			->get()->getResultArray();


		$files = $this->get_files($id);

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

		$file_name = '' . get_number('expenses', $id, 'expense', 'expense') . '' .  '.pdf';

		$html = view('expenses/pdf', $data, TRUE);

		$this->dompdf = new DOMPDF();

		$this->dompdf->loadHtml($html);

		$this->dompdf->set_option('isRemoteEnabled', TRUE);

		$this->dompdf->set_option('isHtml5ParserEnabled', TRUE);

		$this->dompdf->setPaper('A4', 'portrait');

		$this->dompdf->render();

		$output = $this->dompdf->output();

		$result = file_put_contents('uploads/files/expenses/' . $id . '/' . $file_name . '', $output);

		$this->update_pdf_status($id, '1');

		$html = null;

		$this->output->delete_cache();

		$this->dompdf->loadHtml(null);

		$this->dompdf = null;

		unset($this->dompdf);

		return true;
	}



	function get_consultants()
	{
		return $this->db->table('staff')
			->select('*')
			->where('other', '1')
			->where('inactive', NULL)
			->orderBy('id', 'asc')
			->get()
			->getResultArray();
	}

	function get_all_recurring()
	{
		return $this->db->table('recurring')
			->select('*')
			->where('status', '0')
			->where('relation_type', 'expense')
			->orderBy('id', 'asc')
			->get()
			->getResultArray();
	}

	function update_recurring_date($id)
	{
		return $this->db->table('expenses')
			->where('id', $id)
			->update(['last_recurring' => date('Y-m-d')]);
	}



	function recurring_expense($params, $items)
	{
		$builder = $this->db->table('expenses');
		$builder->insert($params);

		$expense = $this->db->insertID();
		$appconfig = get_appconfig();
		$number = $appconfig['expense_series'] ? $appconfig['expense_series'] : $expense;
		$expense_number = $appconfig['expense_prefix'] . $number;

		$builder->where('id', $expense)
			->update(['expense_number' => $expense_number]);

		if ($appconfig['expense_series']) {
			$expense_number = $appconfig['expense_series'] + 1;
			$this->Settings_Model->increment_series('expense_series', $expense_number);
		}

		$loggedinuserid = 0;

		$builder = $this->db->table('payments');
		$builder->insert([
			'transactiontype' => 1,
			'is_transfer' => 0,
			'expense_id' => $expense,
			'staff_id' => $loggedinuserid,
			'amount' => $params['amount'],
			'account_id' => $params['account_id'],
			'customer_id' => $params['customer_id'] ? $params['customer_id'] : 0,
			'not' => 'Outgoings for <a href="' . base_url('expenses/receipt/' . $expense) . '">EXP-' . $expense . '</a>',
			'date' => _pdate(date('Y-m-d')),
		]);

		$builder = $this->db->table('items');
		foreach ($items as $item) {
			$price = str_replace(',', '.', str_replace('.', '', $item['price']));
			$total = $item['quantity'] * $price + (0 / 100 * $item['quantity'] * $price) - ($item['discount'] / 100 * $item['quantity'] * $price);
			$builder->insert([
				'relation_type' => 'expense',
				'relation' => $expense,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $price,
				'discount' => $item['discount'],
				'total' => $total,
			]);
		}

		// LOG
		$staffname = lang2('expense_recurring');
		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('addedanewexpense') . ' <a href="expenses/receipt/' . $expense . '">' . get_number('expenses', $expense, 'expense', 'expense') . '</a>.',
			'staff_id' => $loggedinuserid,
			'customer_id' => request()->getPost('customer'),
		]);

		return $expense;
	}

	function get_all_expenses_count()
	{
		return $this->db->table('expenses')
			->countAllResults();
	}

	function get_items_invoices($id)
	{
		return $this->db->table('items')
			->selectSum('total')
			->where('relation_type', 'expense')
			->where('relation', $id)
			->get()
			->getRowArray();
	}

	function get_files($id)
	{
		return $this->db->table('files')
			->where('relation', $id)
			->where('relation_type', 'expense')
			->orderBy('id', 'desc')
			->get()
			->getResultArray();
	}

	function get_file($id)
	{
		return $this->db->table('files')
			->where('id', $id)
			->orderBy('id', 'desc')
			->get()
			->getRowArray();
	}

	function get_all_expenses($staff_id = '')
	{
		$builder = $this->db->table('expenses');
		$builder->select('*,customers.company as customer,customers.type as type,customers.namesurname as individual,expensecat.name as category,staff.staffname as staff,expenses.description as desc, expenses.id as id');
		$builder->join('customers', 'expenses.customer_id = customers.id', 'left');
		$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('expenses.id', 'desc');

		if ($staff_id) {
			$builder->where('expenses.staff_id', $staff_id);
		}

		return $builder->get()->getResultArray();
	}

	function get_all_expenses_by_relation($relation_type, $relation_id)
	{
		return $this->db->table('expenses')
			->select('*,customers.company as customer,customers.type as type,customers.namesurname as individual,expensecat.name as category,staff.staffname as staff,expenses.description as desc, expenses.id as id')
			->join('customers', 'expenses.customer_id = customers.id', 'left')
			->join('expensecat', 'expenses.category_id = expensecat.id', 'left')
			->join('staff', 'expenses.expense_created_by = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('expenses.relation', $relation_id)
			->where('expenses.relation_type', $relation_type)
			->orderBy('expenses.id', 'desc')
			->get()
			->getResultArray();
	}




	// Function to add new expenses



	function create($params)
	{

		$this->db->table('expenses')->insert( $params);

		$expense = $this->db->insertID();

		$appconfig = get_appconfig();

		$number = $appconfig['expense_series'] ? $appconfig['expense_series'] : $expense;

		$expense_number = $appconfig['expense_prefix'] . $number;
		$builder = $this->db->table('expenses');
		$response = $builder->where('id', $expense)->update(['expense_number' => $expense_number]);
		
		$loggedinuserid = session()->usr_id;

		$this->db->table('payments')->insert( array(

			'transactiontype' => 1,

			'is_transfer' => 0,

			'expense_id' => $expense,

			'staff_id' => $loggedinuserid,

			'amount' => request()->getPost('total'),

			'account_id' => request()->getPost('account'),

			'customer_id' => request()->getPost('customer'),

			'not' => 'Outgoings for <a href="' . base_url('expenses/receipt/' . $expense . '') . '">EXP-' . $expense . '</a>',

			'date' => _pdate(request()->getPost('date')),

		));

		$items = request()->getPost('items');

		$i = 0;

		foreach ($items as $item) {
			$this->db->table('items')->insert( array(
				'relation_type' => 'expense',
				'relation' => $expense,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				//'name' => $item[ 'name' ],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => str_replace(',', '.', str_replace('.', '', $item['price'])),
				//'tax' => $item[ 'tax' ],
				'discount' => $item['discount'],
				'total' => $item['quantity'] * str_replace(',', '.', str_replace('.', '', $item['price'])) + ((0) / 100 * $item['quantity'] * str_replace(',', '.', str_replace('.', '', $item['price']))) - (($item['discount']) / 100 * $item['quantity'] * str_replace(',', '.', str_replace('.', '', $item['price']))),
			));

			$i++;
		};

		//LOG

		$staffname = session()->staffname;

		$loggedinuserid = session()->usr_id;

		$appconfig = get_appconfig();

		$this->db->table('logs')->insert( array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('addedanewexpense') . ' <a href="expenses/receipt/' . $expense . '">' . get_number('expenses', $expense, 'expense', 'expense') .  '</a>.'),

			'staff_id' => $loggedinuserid,

			'customer_id' => request()->getPost('customer')

		));

		return $expense;
	}



	function create_expense($params)
	{

		$this->db->table('expenses')->insert( $params);

		$expense = $this->db->insertID();

		$appconfig = get_appconfig();

		$number = $appconfig['expense_series'] ? $appconfig['expense_series'] : $expense;

		$expense_number = $appconfig['expense_prefix'] . $number;
		$builder = $this->db->table('expenses');
		$response = $builder->where('id', $expense)->update(['expense_number' => $expense_number]);
		

		if ($appconfig['expense_series']) {

			$expense_number = $appconfig['expense_series'];

			$expense_number = $expense_number + 1;

			$this->Settings_Model->increment_series('expense_series', $expense_number);
		}

		$loggedinuserid = session()->usr_id;



		$purchaseId = request()->getPost('purchase');
		$vendorId = request()->getPost('vendor');
		$total = request()->getPost('total');
		$balance = request()->getPost('balance');
		$staffId = session()->get('usr_id');
		
		$builder = $this->db->table('purchases');
		
		if ($balance == 0) {
			$builder->where('id', $purchaseId)
					->update(['status_id' => 2]);
		
			$builder = $this->db->table('vendor_sales');
			$response = $builder->where('purchase_id', $purchaseId)
								->update([
									'status_id' => 2,
									'staff_id' => $staffId,
									'vendor_id' => $vendorId,
									'total' => $total
								]);
		} else {
			$builder->where('id', $purchaseId)
					->update(['status_id' => 3]);
		
			$builder = $this->db->table('vendor_sales');
			$response = $builder->where('purchase_id', $purchaseId)
								->update([
									'status_id' => 3,
									'staff_id' => $staffId,
									'vendor_id' => $vendorId,
									'total' => $total
								]);
		}
		

		$this->db->table('payments')->insert( array(

			'transactiontype' => 1,

			'is_transfer' => 0,

			'expense_id' => $expense,

			'purchase_id' => $params['purchase_id'],

			'vendor_id' => request()->getPost('vendor'),

			'amount' => request()->getPost('total'),

			'account_id' => request()->getPost('account'),

			'staff_id' => $loggedinuserid,

			'not' => 'Outgoings for <a href="' . base_url('expenses/receipt/' . $expense . '') . '">EXP-' . $expense . '</a>',

			'date' => _pdate(request()->getPost('date')),

		));

		$payment_id = $this->db->insertID();

		$i = 0;

		$appconfig = get_appconfig();

		$purchase_number = $appconfig['purchase_prefix'] . '' . str_pad(request()->getPost('purchase'), 6, '0', STR_PAD_LEFT);

		$this->db->table('items')->insert( array(

			'relation_type' => 'expense',

			'relation' => $expense,

			'code' => $purchase_number,

			'description' => lang2('purchase'),

			'name' => $purchase_number,

			'quantity' => '1',

			'price' => request()->getPost('total'),

			'total' => request()->getPost('total'),

		));



		//LOG

		$staffname = session()->staffname;

		$appconfig = get_appconfig();

		$this->db->table('logs')->insert( array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('addedanewexpense') . ' <a href="expenses/receipt/' . $expense . '">' . get_number('expenses', $expense, 'expense', 'expense') .  '</a>.'),

			'staff_id' => $loggedinuserid,

			'vendor_id' => request()->getPost('vendor')

		));



		return $payment_id;
	}



	public function recurring_update($id, $params)
	{
		$builder = $this->db->table('recurring');
		$sharax = $builder->where('relation', $id)
						  ->where('relation_type', 'expense')
						  ->update($params);
	
		return $sharax;
	}
	
	public function update_pdf_status($id, $value)
	{
		$builder = $this->db->table('expenses');
		$response = $builder->where('id', $id)
							->update(['pdf_status' => $value]);
	}
	



	// Function to update expenses



	public function update_expenses($id, $params)
{
    $appconfig = get_appconfig();
    $expense_data = $this->get_expenses($id);

    if (empty($expense_data['expense_number'])) {
        $number = $appconfig['expense_series'] ? $appconfig['expense_series'] : $id;
        $expense_number = $appconfig['expense_prefix'] . $number;

        $this->db->table('expenses')
                 ->where('id', $id)
                 ->update(['expense_number' => $expense_number]);

        if (!empty($appconfig['expense_series'])) {
            $expense_number = $appconfig['expense_series'] + 1;
            $this->Settings_Model->increment_series('expense_series', $expense_number);
        }
    }

    $this->db->table('expenses')
             ->where('id', $id)
             ->update($params);

    $items = request()->getPost('items');

    foreach ($items as $item) {
        $price = str_replace(',', '.', str_replace('.', '', $item['price']));
        $total = $item['quantity'] * $price + (0 / 100 * $item['quantity'] * $price) - ($item['discount'] / 100 * $item['quantity'] * $price);

        $itemParams = [
            'relation_type' => 'expense',
            'relation' => $id,
            'product_id' => $item['product_id'],
            'code' => $item['code'],
            'name' => $item['name'],
            'description' => $item['description'],
            'quantity' => $item['quantity'],
            'unit' => $item['unit'],
            'price' => $price,
            'discount' => $item['discount'],
            'total' => $total,
        ];

        if (isset($item['id'])) {
            $this->db->table('items')
                     ->where('id', $item['id'])
                     ->update($itemParams);
        } else {
            $this->db->table('items')->insert($itemParams);
        }
    }

    $paymentParams = [
        'transactiontype' => 1,
        'amount' => request()->getPost('amount'),
        'account_id' => request()->getPost('account'),
        'customer_id' => request()->getPost('customer'),
        'not' => 'Payment for <a href="' . base_url('expenses/edit/' . $id) . '">EXP-' . $id . '</a>',
        'date' => _pdate(request()->getPost('date')),
    ];

    $this->db->table('payments')
             ->where('expense_id', $id)
             ->update($paymentParams);

    $loggedinuserid = session()->get('usr_id');
    $staffname = session()->get('staffname');

    $logParams = [
        'date' => date('Y-m-d H:i:s'),
        'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('updated') . ' <a href="expenses/receipt/' . $id . '">' . get_number('expenses', $id, 'expense', 'expense') . '</a>.',
        'staff_id' => $loggedinuserid,
        'customer_id' => request()->getPost('customer'),
    ];

    $this->db->table('logs')->insert($logParams);
}




	// Function to delete expenses



	function delete_expenses($id, $number)
	{

		$response = $this->db->table('expenses')->delete( array('id' => $id));

		$response = $this->db->table('payments')->delete( array('expense_id' => $id));

		$response = $this->db->table('sales')->delete( array('invoice_id' => $id));

		$response = $this->db->table('files')->delete( array('relation' => $id, 'relation_type' => 'expense'));

		$response = $this->db->table('pending_process')->delete( array('process_relation' => $id, 'process_relation_type' => 'expense'));

		// LOG

		$staffname = session()->staffname;

		$loggedinuserid = session()->usr_id;

		$appconfig = get_appconfig();

		$this->db->table('logs')->insert( array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('deleted') . ' ' . $number),

			'staff_id' => $loggedinuserid

		));
	}


	public function get_expensecategory($id)
	{
		$builder = $this->db->table('expensecat');
		$builder->orderBy('id', 'desc');
		return $builder->where('id', $id)->get()->getRowArray();
	}

	/* Get All Expense Categories */
	public function get_all_expensecat()
	{
		$builder = $this->db->table('expensecat');
		$builder->where('id_company', session()->get('id_company'));
		$builder->orderBy('id', 'desc');
		return $builder->get()->getResultArray();
	}

	/* Add Expense Category */
	public function add_category($params)
	{
		$params['id_company'] = session()->get('id_company');
		$builder = $this->db->table('expensecat');
		$builder->insert($params);
		return $this->db->insertID();
	}

	/* Update Expense Category */
	public function update_category($id, $params)
	{
		$builder = $this->db->table('expensecat');
		$builder->where('id', $id);
		return $builder->update($params);
	}

	/* Delete Expense Category */
	public function check_category($id)
	{
		$builder = $this->db->table('expenses');
		$builder->where('category_id', $id);
		return $builder->countAllResults();
	}




	function delete_category($id)
	{

		return $this->db->table('expensecat')->delete( array('id' => $id));
	}



	function expensesTotalAmount()
	{
		$builder = $this->db->table('expenses');
		$builder->selectSum('amount');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		$total_value = $builder->get()->getRow()->amount;

		return $total_value ? $total_value : 0;
	}

	function billed_expenses()
	{
		$builder = $this->db->table('expenses');
		$builder->selectSum('amount');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->groupStart()
			->where('expenses.pago', 1)
			->orWhere('invoice_id IS NOT NULL')
			->groupEnd();

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		$total_value = $builder->get()->getRow()->amount;

		return $total_value ? $total_value : 0;
	}

	function not_billed_expenses()
	{
		$builder = $this->db->table('expenses');
		$builder->selectSum('amount');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->groupStart()
			->where('(pago IS NULL OR pago = 0)')
			->where('invoice_id IS NULL')
			->groupEnd();

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		$total_value = $builder->get()->getRow()->amount;

		return $total_value ? $total_value : 0;
	}

	function overdue_expenses()
	{
		$builder = $this->db->table('expenses');
		$builder->selectSum('amount');
		$builder->where('CURDATE() > expenses.duedate');
		$builder->where('expenses.duedate !=', '0000-00-00');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->groupStart()
			->where('(pago IS NULL OR pago = 0)')
			->where('invoice_id IS NULL')
			->groupEnd();

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		$total_value = $builder->get()->getRow()->amount;

		return $total_value ? $total_value : 0;
	}




	function expenses_num()
	{
		$builder = $this->db->table('expenses');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		return $builder->countAllResults();
	}

	function billed_expenses_num()
	{
		$builder = $this->db->table('expenses');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->groupStart()
			->where('expenses.pago', 1)
			->orWhere('invoice_id IS NOT NULL')
			->groupEnd();

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		return $builder->countAllResults();
	}


	function not_billed_expenses_num()
	{
		$builder = $this->db->table('expenses');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('(pago IS NULL OR pago = \'0\')');
		$builder->where('invoice_id IS NULL');

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		return $builder->countAllResults();
	}

	function overdue_expenses_num()
	{
		$builder = $this->db->table('expenses');
		$builder->select('COUNT(*) AS total');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('CURDATE() > expenses.duedate');
		$builder->where('expenses.duedate !=', '0000-00-00');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('(pago IS NULL OR pago = \'0\')');
		$builder->where('invoice_id IS NULL');

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		return $builder->get()->getRow()->total;
	}

	function get_expense_by_purchase($id)
	{
		$builder = $this->db->table('expenses');
		$builder->where('purchase_id', $id);
		$builder->limit(1);
		$query = $builder->get();

		return $query->getNumRows() > 0 ? $query->getRow()->id : null;
	}

	function get_all_expenses_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('expenses');
		$builder->select('*,customers.company AS customer,customers.type AS type,customers.namesurname AS individual,expensecat.name AS category,staff.staffname AS staff,expenses.description AS desc, expenses.id AS id, expenses.created');
		$builder->join('customers', 'expenses.customer_id = customers.id', 'left');
		$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('expenses.id', 'desc');

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		if ($staff_id) {
			$builder->groupStart()
				->where('expense_created_by', $staff_id)
				->orWhere('expenses.staff_id', $staff_id)
				->groupEnd();
		}

		return $builder->get()->getResultArray();
	}

	function get_expenses_by_privileges($id, $staff_id = '')
	{
		$builder = $this->db->table('expenses');
		$builder->select('*,customers.company AS customer,customers.type AS type,customers.namesurname AS individual,customers.email AS customeremail,accounts.name AS account,expensecat.name AS category,staff.staffname AS staff,expenses.description AS desc, expenses.id AS id, expenses.staff_id AS staff_id, staff.email AS staffemail');
		$builder->join('customers', 'expenses.customer_id = customers.id', 'left');
		$builder->join('accounts', 'expenses.account_id = accounts.id', 'left');
		$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->join('formas_pagamento', 'formas_pagamento.id_forma = expenses.id_forma_pgt', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('expenses.id', 'desc');

		if ($staff_id) {
			$builder->groupStart()
				->where('expense_created_by', $staff_id)
				->orWhere('expenses.staff_id', $staff_id)
				->groupEnd();
		}

		return $builder->where('expenses.id', $id)->get()->getRowArray();
	}

	function expenses_total_amount_by_status($type = '')
	{
		$builder = $this->db->table('expenses');
		$builder->selectSum('amount');

		if ($type == 'billed') {
			$builder->where('expenses.pago', '1');
			$builder->orWhere('invoice_id IS NOT NULL');
		} elseif ($type == 'notbilled') {
			$builder->where('(pago IS NULL OR pago = \'0\')');
			$builder->where('invoice_id IS NULL');
		} elseif ($type == 'overdue') {
			$builder->where('CURDATE() > expenses.duedate');
			$builder->where('expenses.duedate !=', '0000-00-00');
			$builder->where('(pago IS NULL OR pago = \'0\')');
			$builder->where('invoice_id IS NULL');
		}

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		$builder->groupStart()
			->where('expense_created_by', session()->usr_id)
			->orWhere('staff_id', session()->usr_id)
			->groupEnd();

		$total_value = $builder->get()->getRow()->amount;

		return !empty($total_value) ? $total_value : 0;
	}




	function expenses_num_by_type($type = '')
	{
		$builder = $this->db->table('expenses');

		if ($type == 'billed') {
			$builder->groupStart()
				->where('expenses.pago', '1')
				->orWhere('invoice_id IS NOT NULL')
				->groupEnd();
		} elseif ($type == 'notbilled') {
			$builder->groupStart()
				->where('pago IS NULL')
				->orWhere('pago', '0')
				->orWhere('invoice_id IS NULL')
				->groupEnd();
		} elseif ($type == 'overdue') {
			$builder->where('CURDATE() > expenses.duedate');
			$builder->where('expenses.duedate !=', '0000-00-00');
			$builder->where('expenses.invoice_id IS NULL');
		}

		$builder->groupStart()
			->where('expense_created_by', session()->usr_id)
			->orWhere('staff_id', session()->usr_id)
			->groupEnd();

		if (request()->getPost('data_de')) {
			$builder->where('expenses.duedate >=', request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where('expenses.duedate <=', request()->getPost('data_ate'));
		}

		return $builder->countAllResults();
	}
}
