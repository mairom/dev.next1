<?php

namespace App\Models;

use CodeIgniter\Model;

include APPPATH . '/third_party/vendor/autoload.php';

use Dompdf\Dompdf;

class Purchases_Model extends Model
{


	function get_appconfig()
	{
		$configs = $this->db->table('appconfig')->get()->getResultArray();

		$data = [];

		foreach ($configs as $config) {
			$data[$config['name']] = $config['value'];
		}

		return response()->setJSON($data);
	}

	function get_purchases($id)
	{
		$builder = $this->db->table('purchases');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,vendors.company as vendorcompany,vendors.phone as vendor_phone,vendors.address as vendoraddress,vendors.email as email,purchases.status_id as status_id,purchases.created as created, purchases.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "purchase"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		return $builder->where('purchases.id', $id)->get()->getRowArray();
	}

	function get_purchases_by_token($token)
	{
		$builder = $this->db->table('purchases');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,vendors.company as vendorcompany,vendors.address as vendoraddress,vendors.email as email,vendors.phone as vendor_phone,purchases.status_id as status_id, purchases.created as created, purchases.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "purchase"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		return $builder->where('purchases.token', $token)->get()->getRowArray();
	}

	function get_all_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,vendors.company as vendorcompany,vendors.address as vendoraddress,vendors.phone as vendor_phone,vendors.email as email,purchases.status_id as status_id, purchases.created as created, purchases.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "purchase"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('purchases.id', 'desc');

		return $builder->get()->getResultArray();
	}

	function get_all_purchases_by_customer($id)
	{
		$builder = $this->db->table('purchases');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,vendors.company as vendorcompany,vendors.address as vendoraddress,vendors.phone as vendor_phone,vendors.email as email,invoices.status_id as status_id, invoices.created as created, invoices.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendors_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "purchase"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('purchases.id', 'desc');

		return $builder->where('purchases.vendors_id', $id)->get()->getResultArray();
	}

	function duepurchases()
	{
		$builder = $this->db->table('purchases');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,vendors.company as vendorcompany,vendors.address as vendoraddress,vendors.phone as vendor_phone,vendors.email as email,vendors.type as type, purchases.created as created, purchases.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('purchases.id', 'desc');

		return $builder->where('DATE(duedate)', date('Y-m-d'))->get()->getResultArray();
	}

	function overduepurchases()
	{
		$builder = $this->db->table('purchases');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,vendors.company as vendorcompany,vendors.address as vendoraddress,vendors.phone as vendor_phone,vendors.email as email,vendors.type as type, purchases.created as created, purchases.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "invoice"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('CURDATE() > purchases.duedate AND purchases.duedate != "0000-00-00" AND purchases.status_id != "4" AND purchases.status_id != "2"');
		$builder->orderBy('purchases.id', 'desc');

		return $builder->get()->getResultArray();
	}

	function get_purchases_detail($id)
	{
		$builder = $this->db->table('purchases');
		$builder->select('*,IFNULL(vendors.type, 0) as type,staff.staffname as staffmembername,staff.staffavatar as staffmemberresim,vendors.company as vendorcompany,vendors.address as vendoraddress,vendors.country_id as vendorcountry,vendors.state as vendorstate,vendors.city as vendorcity,vendors.town as vendortown,vendors.zipcode as vendorzip,vendors.phone as vendor_phone,vendors.email as email,purchases.status_id as status_id,purchases.created as created, purchases.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "purchase"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		return $builder->where('purchases.id', $id)->get()->getRowArray();
	}

	function get_items_purchases($id)
	{
		$builder = $this->db->table('items');
		$builder->selectSum('total');
		$builder->where('(relation_type = "purchase" AND relation = ' . $id . ')');

		return $builder->get()->getRow();
	}

	function get_paid_purchases($id)
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->where('(purchase_id = ' . $id . ') ');

		return $builder->get()->getRow();
	}

	function get_items_detail($id)
	{
		$builder = $this->db->table('items');
		$builder->select('*');
		$builder->where('(items.relation_type = "purchase" AND items.relation = ' . $id . ')');

		return $builder->get()->getResultArray();
	}



	// ADD RECURRING



	public function recurring_add($params)
	{
		$this->db->table('recurring')->insert($params);
		$sharax = $this->db->insertID();
		return $sharax;
	}

	// END ADD RECURRING

	// UPDATE RECURRING

	public function recurring_update($id, $params)
	{
		$this->db->table('recurring')
			->where('relation', $id)
			->where('relation_type', 'purchase')
			->update($params);
		return $this->db->affectedRows();
	}

	// END UPDATE RECURRING

	// GET ALL RECURRING

	public function get_all_recurring()
	{
		return $this->db->table('recurring')
			->select('*')
			->where('status', '0')
			->where('relation_type', 'purchase')
			->orderBy('id', 'asc')
			->get()
			->getResultArray();
	}

	// END GET ALL RECURRING

	// Copy Invoice

	public function recurring_purchases($purchases, $items)
	{
		$this->db->table('purchases')->insert($purchases);
		$purchase = $this->db->insertID();
		$appconfig = get_appconfig();

		$number = $appconfig['purchase_series'] ? $appconfig['purchase_series'] : $purchase;
		$purchase_number = $appconfig['purchase_prefix'] . $number;

		$this->db->table('purchases')
			->where('id', $purchase)
			->update(['purchase_number' => $purchase_number]);

		if ($appconfig['purchase_series']) {
			$purchase_number = $appconfig['purchase_series'] + 1;
			$this->Settings_Model->increment_series('purchase_series', $purchase_number);
		}

		foreach ($items as $item) {
			$this->db->table('items')->insert([
				'relation_type' => 'purchase',
				'relation' => $purchase,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => str_replace(',', '.', str_replace('.', '', $item['price'])),
				'tax' => $item['tax'],
				'discount' => $item['discount'],
				'total' => $item['total'],
			]);
		}

		// LOG
		$staffname = 'Ciuis CRM Recurring';
		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="#"> ' . $staffname . '</a> ' . lang2('added') . ' <a href="purchases/purchase/' . $purchase . '">' . get_number('purchases', $purchase, 'purchase', 'purchase') . '</a>.',
			'staff_id' => 0,
			'vendor_id' => $purchases['vendor_id']
		]);

		// NOTIFICATION
		$staffavatar = 'defualt-avatar.jpg';
		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('isaddedanewinvoice'),
			'vendor_id' => $purchases['vendor_id'],
			'perres' => $staffavatar,
			'target' => base_url('area/purchase/' . $purchase)
		]);

		//--------------------------------------------------------------------------------------

		$status = 3;
		$this->db->table('vendor_sales')->insert([
			'purchase_id' => $purchase,
			'status_id' => $status,
			'staff_id' => 0,
			'vendor_id' => $purchases['vendor_id'],
			'total' => $purchases['total'],
			'date' => date('Y-m-d H:i:s')
		]);

		//----------------------------------------------------------------------------------------

		return $purchase;
	}



	public function update_recurring_date($id)
	{
		return $this->db->table('purchases')
			->where('id', $id)
			->update(['last_recurring' => date('Y-m-d')]);
	}

	// END Copy Invoice

	// ADD INVOICE

	public function purchases_add($params)
	{
		$this->db->table('purchases')->insert($params);
		$purchase = $this->db->insertID();
		$appconfig = get_appconfig();

		$number = $appconfig['purchase_series'] ? $appconfig['purchase_series'] : $purchase;
		$purchase_number = $appconfig['purchase_prefix'] . $number;

		$this->db->table('purchases')
			->where('id', $purchase)
			->update(['purchase_number' => $purchase_number]);

		if (request()->getPost('status') == 'false') {
			$loggedinuserid = session()->get('usr_id');
			$this->db->table('payments')->insert([
				'purchase_id' => $purchase,
				'staff_id' => $loggedinuserid,
				'amount' => request()->getPost('total'),
				'vendor_id' => request()->getPost('vendor'),
				'account_id' => request()->getPost('account'),
				'not' => sprintf(lang2('paymentfor'), $purchase),
				'date' => request()->getPost('datepayment')
			]);
		}

		$items = request()->getPost('items');
		foreach ($items as $item) {
			$price = str_replace(',', '.', str_replace('.', '', $item['price']));
			$total = $item['quantity'] * $price + ($item['tax'] / 100 * $item['quantity'] * $price) - ($item['discount'] / 100 * $item['quantity'] * $price);

			$this->db->table('items')->insert([
				'relation_type' => 'purchase',
				'relation' => $purchase,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $price,
				'tax' => $item['tax'],
				'discount' => $item['discount'],
				'total' => $total,
			]);
		}

		// LOG
		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');
		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('added') . ' <a href="purchases/purchase/' . $purchase . '">' . get_number('purchases', $purchase, 'purchase', 'purchase') . '</a>.',
			'staff_id' => $loggedinuserid,
			'vendor_id' => request()->getPost('vendor')
		]);

		// NOTIFICATION
		$staffavatar = session()->get('staffavatar');
		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('isaddedanewpurchase'),
			'vendor_id' => request()->getPost('vendor'),
			'perres' => $staffavatar,
			'target' => base_url('area/purchases/' . $purchase)
		]);

		$status_value = request()->getPost('status');
		$status = ($status_value == 'true') ? 2 : 3;

		return $purchase;
	}

	// UPDATE PURCHASE

	public function update_purchases($id, $params)
	{
		$appconfig = get_appconfig();
		$purchase_data = $this->get_purchases($id);

		if ($purchase_data['purchase_number'] == '') {
			$number = $appconfig['purchase_series'] ? $appconfig['purchase_series'] : $id;
			$purchase_number = $appconfig['purchase_prefix'] . $number;
			$this->db->table('purchases')
				->where('id', $id)
				->update(['purchase_number' => $purchase_number]);

			if ($appconfig['purchase_series'] != '') {
				$purchase_number = $appconfig['purchase_series'] + 1;
				$this->Settings_Model->increment_series('purchase_series', $purchase_number);
			}
		}

		$this->db->table('purchases')
			->where('id', $id)
			->update($params);

		$items = request()->getPost('items');
		foreach ($items as $item) {
			$price = str_replace(',', '.', str_replace('.', '', $item['price']));
			$total = $item['quantity'] * $price + ($item['tax'] / 100 * $item['quantity'] * $price) - ($item['discount'] / 100 * $item['quantity'] * $price);

			if (isset($item['id'])) {
				$this->db->table('items')
					->where('id', $item['id'])
					->update([
						'relation_type' => 'purchase',
						'relation' => $id,
						'product_id' => $item['product_id'],
						'code' => $item['code'],
						'name' => $item['name'],
						'description' => $item['description'],
						'quantity' => $item['quantity'],
						'unit' => $item['unit'],
						'price' => $price,
						'tax' => $item['tax'],
						'discount' => $item['discount'],
						'total' => $total,
					]);
			} else {
				$this->db->table('items')->insert([
					'relation_type' => 'purchase',
					'relation' => $id,
					'product_id' => $item['product_id'],
					'code' => $item['code'],
					'name' => $item['name'],
					'description' => $item['description'],
					'quantity' => $item['quantity'],
					'unit' => $item['unit'],
					'price' => $price,
					'tax' => $item['tax'],
					'discount' => $item['discount'],
					'total' => $total,
				]);
			}
		}

		$purchases = $this->Purchases_Model->get_purchases($id);
		$this->db->table('vendor_sales')
			->where('purchase_id', $id)
			->update([
				'status_id' => $purchases['status_id'],
				'staff_id' => session()->get('usr_id'),
				'vendor_id' => request()->getPost('vendor'),
				'total' => request()->getPost('total'),
			]);

		// LOG
		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');
		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('updated') . ' <a href="purchases/purchase/' . $id . '">' . get_number('purchases', $id, 'purchase', 'purchase') . '</a>.',
			'staff_id' => $loggedinuserid,
			'vendor_id' => request()->getPost('vendor')
		]);

		// NOTIFICATION
		$staffavatar = session()->get('staffavatar');
		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('updated') . ' ' . lang2('purchase'),
			'vendor_id' => request()->getPost('vendor'),
			'perres' => $staffavatar,
			'target' => base_url('area/purchases/' . $id)
		]);
	}




	//INVOICE DELETE

	function delete_purchases($id, $number)
	{
		$appconfig = get_appconfig();

		$this->db->table('purchases')->delete(['id' => $id]);
		$this->db->table('items')->delete(['relation_type' => 'purchase', 'relation' => $id]);
		$this->db->table('payments')->delete(['purchase_id' => $id]);
		$this->db->table('expenses')->delete(['purchase_id' => $id]);
		$this->db->table('pending_process')->delete(['process_relation' => $id, 'process_relation_type' => 'purchase']);

		$file_name = get_number('purchases', $id, 'purchase', 'purchase') . '.pdf';
		$file = './assets/files/generated_pdf_files/purchases/' . $file_name;

		if (is_file($file)) {
			unlink($file);
		}

		// LOG
		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');
		$appconfig = get_appconfig();

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('deleted') . ' ' . $number,
			'staff_id' => $loggedinuserid
		]);

		return true;
	}

	function get_purchases_year()
	{
		return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM purchases ORDER BY year DESC')->get()->getResultArray();
	}

	function get_category_id()
	{
		$builder = $this->db->table('expensecat');
		$builder->select('id');
		$builder->where('name', lang2('purchase'));
		$query = $builder->get();

		if ($query->getNumRows() > 0) {
			$data = $query->getRow();
			return $data->id;
		} else {
			$params = [
				'name' => lang2('purchase'),
				'description' => lang2('purchase')
			];
			$builder->insert($params);
			return $this->db->insertID();
		}
	}



	function update_pdf_status($id, $value)
	{
		$this->db->table('purchases')->where('id', $id)->update(['pdf_status' => $value]);
	}

	function generate_pdf($id)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '2048M');

		if (!is_dir('uploads/files/purchases/' . $id)) {
			mkdir('./uploads/files/purchases/' . $id, 0777, true);
		}

		$data['title'] = get_number('purchases', $id, 'purchase', 'purchase');
		$data['purchase'] = $this->Purchases_Model->get_purchases_detail($id);
		$data['vendor_country'] = get_country($data['purchase']['vendorcountry']);
		$data['vendor_state'] = get_state_name(' ', $data['purchase']['vendorstate']);
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);
		$data['country'] = get_country($data['settings']['country_id']);
		$data['items'] = $this->db->table('items')
			->select('*')
			->where(['relation_type' => 'purchase', 'relation' => $id])
			->get()->getResultArray();

		$file_name = get_number('purchases', $id, 'purchase', 'purchase') . '.pdf';
		$html = view('purchases/pdf', $data, TRUE);

		$this->dompdf = new \Dompdf\Dompdf();
		$this->dompdf->loadHtml($html);
		$this->dompdf->setOptions(new \Dompdf\Options([
			'isRemoteEnabled' => TRUE,
			'isHtml5ParserEnabled' => TRUE
		]));
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		$output = $this->dompdf->output();
		file_put_contents('uploads/files/purchases/' . $id . '/' . $file_name, $output);

		$this->update_pdf_status($id, '1');

		$this->dompdf->clear();
		unset($this->dompdf);

		return true;
	}

	public function get_all_purchases_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('purchases');
		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffmemberresim, vendors.company as vendorcompany, vendors.address as vendoraddress, vendors.phone as vendor_phone, vendors.email as email, purchases.status_id as status_id, purchases.created as created, purchases.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "purchase"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('purchases.id', 'desc');

		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where("purchases.vendor_id", request()->getPost('fornecedor'));
		}

		if ($staff_id) {
			return $builder->where('purchases.staff_id', $staff_id)->get()->getResultArray();
		} else {
			return $builder->get()->getResultArray();
		}
	}

	public function get_purchase_by_privileges($id, $staff_id = '')
	{
		$builder = $this->db->table('purchases');
		$builder->select('*, IFNULL(vendors.type, 0) as type, staff.staffname as staffmembername, staff.staffavatar as staffmemberresim, vendors.company as vendorcompany, vendors.address as vendoraddress, vendors.country_id as vendorcountry, vendors.state as vendorstate, vendors.city as vendorcity, vendors.town as vendortown, vendors.zipcode as vendorzip, vendors.phone as vendor_phone, vendors.email as email, purchases.status_id as status_id, purchases.created as created, purchases.id as id, recurring.id as recurring_id, recurring.status as recurring_status, recurring.relation_type as recurring_relation_type, recurring.period as recurring_period, recurring.type as recurring_type, recurring.end_date as recurring_endDate');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('recurring', 'purchases.id = recurring.relation AND recurring.relation_type = "purchase"', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		if ($staff_id) {
			return $builder->where(['purchases.id' => $id, 'purchases.staff_id' => $staff_id])->get()->getRowArray();
		} else {
			return $builder->where(['purchases.id' => $id])->get()->getRowArray();
		}
	}
}
