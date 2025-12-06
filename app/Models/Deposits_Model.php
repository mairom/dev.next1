<?php

namespace App\Models;

use CodeIgniter\Model;

include APPPATH . '/third_party/vendor/autoload.php';

use Dompdf\Dompdf;

class Deposits_Model extends Model
{

	/* Get Deposits by ID */

	function get_deposits($id, $token = '')
	{
		$builder = $this->db->table('deposits')
			->select('*,customers.company as customer,customers.type as type,customers.namesurname as individual,customers.email as customeremail,customers.phone as customer_phone,customers.address as customeraddress,accounts.name as account,depositcat.name as category,staff.staffname as staff,staff.email as staffemail,deposits.description as desc, deposits.staff_id as depositstaff,deposits.id as id,deposits.status as deposit_status,deposits.created as depositcreate,recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate')
			->join('customers', 'deposits.customer_id = customers.id', 'left')
			->join('accounts', 'deposits.account_id = accounts.id', 'left')
			->join('depositcat', 'deposits.category_id = depositcat.id', 'left')
			->join('staff', 'deposits.staff_id = staff.id', 'left')
			->join('recurring', "deposits.id = recurring.relation AND recurring.relation_type = 'deposit'", 'left')
			->where('staff.id_company', session()->id_company)
			->orderBy('deposits.id', 'desc');

		if ($token == '') {
			return $builder->where('deposits.id', $id)->get()->getRowArray();
		} else {
			return $builder->where('deposits.token', $token)->get()->getRowArray();
		}
	}




	// All Deposits Count 

	function get_all_deposits_count()
	{

		$this->db->from('deposits');

		return $this->db->count_all_results();
	}



	/* Get All Deposits */

	function get_all_deposits($staff_id = '')
	{
		$builder = $this->db->table('deposits')
			->select('*,customers.company as customer,customers.type as type,customers.namesurname as individual,depositcat.name as category,staff.staffname as staff,deposits.description as desc, deposits.id as id')
			->join('customers', 'deposits.customer_id = customers.id', 'left')
			->join('depositcat', 'deposits.category_id = depositcat.id', 'left')
			->join('staff', 'deposits.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->orderBy('deposits.id', 'desc');

		if ($staff_id) {
			$builder->where('(deposits_created_by=' . $staff_id . ' OR deposits.staff_id=' . $staff_id . ')');
		}

		return $builder->get()->getResultArray();
	}

	function get_all_deposits_by_relation($relation_type, $relation_id)
	{
		return $this->db->table('deposits')
			->select('*,customers.company as customer,customers.type as type,customers.namesurname as individual,depositcat.name as category,staff.staffname as staff,deposits.description as desc, deposits.id as id')
			->join('customers', 'deposits.customer_id = customers.id', 'left')
			->join('depositcat', 'deposits.category_id = depositcat.id', 'left')
			->join('staff', 'deposits.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where(['relation' => $relation_id, 'relation_type' => $relation_type])
			->orderBy('deposits.id', 'desc')
			->get()
			->getResultArray();
	}

	function create($params)
	{
		$this->db->table('deposits')->insert($params);

		$deposit = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['deposit_series'] ? $appconfig['deposit_series'] : $deposit;
		$deposit_number = $appconfig['deposit_prefix'] . $number;

		$this->db->table('deposits')->where('id', $deposit)->update(['deposit_number' => $deposit_number]);

		$loggedinuserid = session()->usr_id;

		if ($params['status'] == '2') {
			$this->db->table('payments')->insert([
				'transactiontype' => 0,
				'deposit_id' => $deposit,
				'staff_id' => $loggedinuserid,
				'amount' => request()->getPost('total'),
				'account_id' => request()->getPost('account'),
				'customer_id' => request()->getPost('customer'),
				'is_transfer' => 0,
				'not' => lang2('deposit') . ' ' . lang2('for') . ' ' . '<a href="' . base_url('deposits/deposit/' . $deposit . '') . '">' . get_number('deposits', $deposit, 'deposit', 'deposit') . '</a>',
				'date' => _pdate(request()->getPost('date')),
			]);
		}

		$items = request()->getPost('items');
		foreach ($items as $item) {
			$this->db->table('items')->insert([
				'relation_type' => 'deposit',
				'relation' => $deposit,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $item['price'],
				'tax' => $item['tax'],
				'discount' => $item['discount'],
				'total' => $item['quantity'] * $item['price'] + (($item['tax']) / 100 * $item['quantity'] * $item['price']) - (($item['discount']) / 100 * $item['quantity'] * $item['price']),
			]);
		}

		// LOG
		$staffname = session()->staffname;
		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang('added') . ' <a href="' . base_url() . 'deposits/deposit/' . $deposit . '">' . get_number('deposits', $deposit, 'deposit', 'deposit') . '</a>.'),
			'staff_id' => $loggedinuserid,
			'customer_id' => request()->getPost('customer')
		]);

		return $deposit;
	}



	// Function to update deposits

	function update_deposit($id, $params)
	{

		$appconfig = get_appconfig();

		$deposit_data = $this->get_deposits($id, '');

		if ($deposit_data['deposit_number'] == '') {

			$number = $appconfig['deposit_series'] ? $appconfig['deposit_series'] : $id;

			$deposit_number = $appconfig['deposit_prefix'] . $number;

			$builder = $this->db->table('deposits');
			$response = $builder->where('id', $id)->update(['deposit_number' => $deposit_number]);


			if (($appconfig['deposit_series'] != '')) {

				$deposit_number = $appconfig['deposit_series'];

				$deposit_number = $deposit_number + 1;

				$this->Settings_Model->increment_series('deposit_series', $deposit_number);
			}
		}

		$this->db->table('deposits')->where('id', $id);
		$response = $this->db->table('deposits')->update($params);

		$items = request()->getPost('items');

		$i = 0;

		foreach ($items as $item) {

			if (isset($item['id'])) {

				$params = array(

					'relation_type' => 'deposit',

					'relation' => $id,

					'product_id' => $item['product_id'],

					'code' => $item['code'],

					'name' => $item['name'],

					'description' => $item['description'],

					'quantity' => $item['quantity'],

					'unit' => $item['unit'],

					'price' => $item['price'],

					'tax' => $item['tax'],

					'discount' => $item['discount'],

					'total' => $item['quantity'] * $item['price'] + (($item['tax']) / 100 * $item['quantity'] * $item['price']) - (($item['discount']) / 100 * $item['quantity'] * $item['price']),

				);

				$builder = $this->db->table('items');
				$response = $builder->where('id', $item['id'])->update($params);
			}

			if (empty($item['id'])) {

				$this->db->table('items')->insert(array(

					'relation_type' => 'deposit',

					'relation' => $id,

					'product_id' => $item['product_id'],

					'code' => $item['code'],

					'name' => $item['name'],

					'description' => $item['description'],

					'quantity' => $item['quantity'],

					'unit' => $item['unit'],

					'price' => $item['price'],

					'tax' => $item['tax'],

					'discount' => $item['discount'],

					'total' => $item['quantity'] * $item['price'] + (($item['tax']) / 100 * $item['quantity'] * $item['price']) - (($item['discount']) / 100 * $item['quantity'] * $item['price']),

				));
			}

			$i++;
		};

		$loggedinuserid = session()->usr_id;

		$staffname = session()->staffname;

		$this->db->table('logs')->insert(array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang('updated') . ' <a href="' . base_url() . 'deposits/deposit/' . $id . '">' . get_number('deposits', $id, 'deposit', 'deposit') . '</a>.'),

			'staff_id' => $loggedinuserid,

			'customer_id' => request()->getPost('customer')

		));
	}



	// Function to delete deposits

	function delete_deposits($id, $number)
	{

		$response = $this->db->table('deposits')->delete(array('id' => $id));

		$response = $this->db->table('payments')->delete(array('deposit_id' => $id));

		$response = $this->db->table('pending_process')->delete(array('process_relation' => $id, 'process_relation_type' => 'deposit'));

		// LOG

		$staffname = session()->staffname;

		$loggedinuserid = session()->usr_id;

		$this->db->table('logs')->insert(array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang('deleted') . ' ' . $number),

			'staff_id' => $loggedinuserid

		));
	}



	function get_depositcategory($id)
	{
		return $this->db->table('depositcat')
			->where('id', $id)
			->orderBy('id', 'desc')
			->get()
			->getRowArray();
	}
	
	/* Get All deposit Categories */
	
	function get_all_depositcat()
	{
		return $this->db->table('depositcat')
			->orderBy('id', 'desc')
			->get()
			->getResultArray();
	}
	



	/* Add deposit Category */

	function add_category($params)
	{

		$this->db->table('depositcat')->insert($params);

		return $this->db->insertID();
	}



	/* Update deposit Category */

	function update_category($id, $params)
	{

		return $this->db->table('depositcat')->where('id', $id)->update($params);
	}



	/* Delete deposit Category */

	function delete_category($id)
	{

		return $this->db->table('depositcat')->delete(array('id' => $id));
	}



	/* Check deposit Category */

	function check_category($id)
	{
		return $this->db->table('deposits')
			->where('category_id', $id)
			->countAllResults();
	}
	



	/* Check Total  deposit Amount */

	function depositsTotalAmount()
	{

		$total_value = $this->db->table('deposits')
			->selectSum('amount')
			->get()
			->getRow()
			->amount;


		if (!empty($total_value)) {
			$total = $total_value;
		} else {
			$total = 0;
		}

		return $total;
	}



	/* Check Number of Row For deposit  */

	function total_deposit_num($status)
	{
		return $this->db->table('deposits')
			->join('staff', 'deposits.staff_id = staff.id', 'left')
			->where('deposits.status', $status)
			->where('staff.id_company', session()->get('id_company'))
			->countAllResults();
	}

	/* Check Total Internal deposit */
	function total_deposits($status)
	{
		$total_value = $this->db->table('deposits')
			->selectSum('amount')
			->join('staff', 'deposits.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('deposits.status', $status)
			->get()
			->getRow()
			->amount;

		return $total_value ?? 0;
	}




	/* Add Recurring */

	function recurring_add($params)
	{
		$this->db->table('recurring')->insert($params);
		return $this->db->insertID();
	}



	/* Get All Recurring */

	function get_all_recurring()
	{

		return $this->db->table('recurring')
			->select('*')
			->where(['status' => '0', 'relation_type' => 'deposit'])
			->orderBy('id', 'asc')
			->get()
			->getResultArray();
	}



	/* Recurring Deposits Via Cron Job */

	function recurring_deposits($params, $items)
	{

		$this->db->table('deposits')->insert($params);

		$deposit = $this->db->insertID();

		$appconfig = get_appconfig();

		$number = $appconfig['deposit_series'] ? $appconfig['deposit_series'] : $deposit;

		$deposit_number = $appconfig['deposit_prefix'] . $number;

		$builder = $this->db->table('deposits');
		$response = $builder->where('id', $deposit)->update(['deposit_number' => $deposit_number]);


		if ($appconfig['deposit_series']) {

			$deposit_number = $appconfig['deposit_series'];

			$deposit_number = $deposit_number + 1;

			$this->Settings_Model->increment_series('deposit_series', $deposit_number);
		}

		$loggedinuserid = 0;

		if ($params['status'] == '2') {

			$this->db->table('payments')->insert(array(

				'transactiontype' => 0,

				'is_transfer' => 0,

				'deposit_id' => $deposit,

				'staff_id' => $loggedinuserid,

				'amount' => $params['amount'],

				'account_id' => $params['account_id'],

				'customer_id' => $params['customer_id'] ? $params['customer_id'] : 0,

				'not' => lang2('incomings') . ' ' . lang2('for') . ' <a href="' . base_url('deposits/deposit/' . $deposit . '') . '">' . get_number('deposits', $deposit, 'deposit', 'deposit') . '</a>',

				'date' => _pdate(date('Y-m-d')),

			));
		}

		$i = 0;

		foreach ($items as $item) {

			$this->db->table('items')->insert(array(

				'relation_type' => 'deposit',

				'relation' => $deposit,

				'product_id' => $item['product_id'],

				'code' => $item['code'],

				'name' => $item['name'],

				'description' => $item['description'],

				'quantity' => $item['quantity'],

				'unit' => $item['unit'],

				'price' => $item['price'],

				'tax' => $item['tax'],

				'discount' => $item['discount'],

				'total' => $item['quantity'] * $item['price'] + (($item['tax']) / 100 * $item['quantity'] * $item['price']) - (($item['discount']) / 100 * $item['quantity'] * $item['price']),

			));

			$i++;
		};

		//LOG

		$staffname = lang('recurring_deposit');

		$this->db->table('logs')->insert(array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('<a href="<' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang('added') . ' <a href="' . base_url() . 'deposits/deposit/' . $deposit . '">' . get_number('deposits', $deposit, 'deposit', 'deposit') . ''  . '</a>.'),

			'staff_id' => $loggedinuserid,

			'customer_id' => request()->getPost('customer')

		));

		return $deposit;
	}



	/* Update Recurring Deposits Date */

	function update_recurring_date($id)
	{

		$builder = $this->db->table('deposits');
		$builder->join('staff', 'deposits.staff_id = staff.id', 'left');
		$builder->where('deposits.id', $id);
		$builder->where('staff.id_company', session()->get('id_company'));

		return $builder->update(['last_recurring' => date('Y-m-d')]);
	}



	/* Update Recurring Deposits */

	public function recurring_update($id, $params)
	{
		$builder = $this->db->table('recurring');
		$sharax = $builder->where('relation', $id)
						  ->where('relation_type', 'deposit')
						  ->update($params);
		return $sharax;
	}
	
	public function update_pdf_status($id, $value)
	{
		$builder = $this->db->table('deposits');
		$builder->join('staff', 'deposits.staff_id = staff.id', 'left');
		$builder->where('deposits.id', $id);
		$builder->where('staff.id_company', session()->get('id_company'));
		$response = $builder->update(['pdf_status' => $value]);
	}
	


	function generate_pdf($id)
	{

		ini_set('max_execution_time', 0);

		ini_set('memory_limit', '2048M');

		if (!is_dir('uploads/files/deposits/' . $id)) {

			mkdir('./uploads/files/deposits/' . $id, 0777, true);
		}

		$data['title'] = '' . get_number('deposits', $id, 'deposit', 'deposit') . '';

		$data['deposit'] = $this->get_deposits($id, '');

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

		$data['customercountry'] = get_country($data['deposit']['country_id']);

		$data['customerstate'] = get_state_name($data['deposit']['state'], $data['deposit']['state_id']);

		$data['country'] = get_country($data['settings']['country_id']);


		$data['items'] = $this->db->table('items')
			->select('*')
			->where(['relation_type' => 'deposit', 'relation' => $id])
			->get()->getResultArray();

		$html =  view('deposits/pdf', $data, TRUE);

		$file_name = '' . get_number('deposits', $id, 'deposit', 'deposit') . '.pdf';

		$this->dompdf = new DOMPDF();

		$this->dompdf->loadHtml($html);

		$this->dompdf->set_option('isRemoteEnabled', TRUE);

		$this->dompdf->set_option('isHtml5ParserEnabled', TRUE);

		$this->dompdf->setPaper('A4', 'portrait');

		$this->dompdf->render();

		$output = $this->dompdf->output();

		file_put_contents('uploads/files/deposits/' . $id . '/' . $file_name . '', $output);

		$this->update_pdf_status($id, '1');

		$html = null;

		$this->output->delete_cache();

		$this->dompdf->loadHtml(null);

		$this->dompdf = null;

		unset($this->dompdf);

		return true;
	}



	function get_deposit_by_privileges($id, $staff_id = '')
	{

		$builder = $this->db->table('deposits')
			->select('*,customers.company as customer,customers.type as type,customers.namesurname as individual,customers.email as customeremail,customers.phone as customer_phone,customers.address as customeraddress,accounts.name as account,depositcat.name as category,staff.staffname as staff,staff.email as staffemail,deposits.description as desc, deposits.staff_id as depositstaff,deposits.id as id,deposits.status as deposit_status,deposits.created as depositcreate,recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate')
			->join('customers', 'deposits.customer_id = customers.id', 'left')
			->join('accounts', 'deposits.account_id = accounts.id', 'left')
			->join('depositcat', 'deposits.category_id = depositcat.id', 'left')
			->join('staff', 'deposits.staff_id = staff.id', 'left')
			->join('recurring', "deposits.id = recurring.relation AND recurring.relation_type = 'deposit'", 'left')
			->where('staff.id_company', session()->id_company)
			->orderBy('deposits.id', 'desc');

		if ($staff_id) {
			$builder->where('(deposits_created_by=' . $staff_id . ' OR deposits.staff_id=' . $staff_id . ')');
		}

		return $builder->where('deposits.id', $id)->get()->getRowArray();
	}



	function total_deposit_num_by_status($status, $staff_id)
	{
		return $this->db->table('deposits')
			->join('staff', 'deposits.staff_id = staff.id', 'left')
			->where('status', $status)
			->groupStart()
			->where('deposits_created_by', $staff_id)
			->orWhere('deposits.staff_id', $staff_id)
			->groupEnd()
			->where('staff.id_company', session()->id_company)
			->countAllResults();
	}

	/* Check Total Internal Deposit  */
	function total_deposits_by_status($staff_id, $status = '')
	{
		$builder = $this->db->table('deposits');
		$builder->selectSum('amount');

		if ($status !== '') {
			$builder->where('status', $status);
		}

		$builder->join('staff', 'deposits.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->groupStart()
			->where('deposits_created_by', $staff_id)
			->orWhere('deposits.staff_id', $staff_id)
			->groupEnd();

		$total_value = $builder->get()->getRow()->amount;

		return !empty($total_value) ? $total_value : 0;
	}
}
