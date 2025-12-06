<?php

namespace App\Models;

use CodeIgniter\Model;

class Settings_Model extends Model
{


	public function get_settings($settingname)
	{
		$builder = $this->db->table('settings');
		$builder->select('settings.*, languages.name as language');
		$builder->join('languages', 'settings.languageid = languages.foldername', 'left');

		$builder->where('settings.settingname', $settingname);

		return $builder->get()->getRowArray();
	}



	public function get_settings_ciuis($id_company = null, $staff_id = null)
	{

		$session = session();

		if ($id_company == null && $session->get('id_company') != null) {
			$id_company = $session->get('id_company');
		} else if ($id_company == null && $session->get('id_company') == null) {
			$id_company = 1;
		}

		if ($staff_id == null && $session->get('usr_id') != null) {
			$staff_id = $session->get('usr_id');
		} else if ($staff_id == null && $session->get('usr_id') == null) {
			$staff_id = 35;
		}

		$builder = $this->db->table('settings');
		$builder->select('*, languages.name as language, settings.settingname as settingname');
		$builder->join('languages', 'settings.languageid = languages.foldername', 'left');
		$result = $builder->where('id_company', $id_company)->get()->getRowArray();


		$settings_email = $this->db->table('settings_email')->where('staff_id', $staff_id)->get()->getRowArray();



		if ($settings_email != null) {
			$result = array_merge($result, $settings_email);
		}


		return $result;
	}

	public function get_settings_ciuis_origin($idCompany = null, $staff_id = null)
	{
		$session = session();

		if ($idCompany == null) {
			$idCompany = $session->get('id_company');
		}

		if ($staff_id == null) {
			$staff_id = $session->get('usr_id');
		}

		$result = $this->db->table('settings')->where('id_company', $idCompany)->get()->getRowArray();
		$settings_email = $this->db->table('settings_email')->where('staff_id', $staff_id)->get()->getRowArray();

		$result['motivos_pausa'] = $this->db->table('settings_motivos_pausa')->where('id_company', $idCompany)->get()->getResultArray();

		if ($settings_email != null) {
			$result = array_merge($result, $settings_email);
		}
		return $result;
	}

	public function get_customer_staff($id)
	{
		$builder = $this->db->table('customers');
		$builder->select('staff.staffname, staff.id as staff_id, staff.email as staff_email');
		$builder->join('staff', 'customers.staff_id = staff.id', 'left');
		$builder->where('customers.id', $id);
		return $builder->get()->getRowArray();
	}

	public function get_payment_modes()
	{
		return $this->db->table('payment_modes')->get()->getResultArray();
	}

	public function update_settings($settingname, $params, $id_company = null)
	{
		$session = session();
		$staffname = $session->get('staffname');
		$loggedinuserid = $session->get('usr_id');

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('updatedsettings') . ''),
			'staff_id' => $loggedinuserid
		]);

		if ($id_company == null) {
			$this->db->table('settings')->where('id_company', $session->get('id_company'))->update($params);
		} else {
			$this->db->table('settings')->where('id_company', $id_company)->update($params);
		}
	}

	public function update_appconfig()
	{
		$input = request()->getPost();

		$prefixes = [
			'inv_prefix', 'project_prefix', 'order_prefix', 'expense_prefix',
			'proposal_prefix', 'tax_label', 'product_prefix', 'vendor_prefix',
			'customer_prefix', 'lead_prefix', 'ticket_prefix', 'staff_prefix',
			'purchase_prefix', 'task_prefix', 'invoice_series', 'project_series',
			'product_series', 'order_series', 'proposal_series', 'vendor_series',
			'customer_series', 'expense_series', 'lead_series', 'ticket_series',
			'staff_series', 'purchase_series', 'task_series', 'deposit_series',
			'deposit_prefix'
		];

		foreach ($prefixes as $prefix) {
			if ($input[$prefix]) {
				$this->db->table('appconfig')->where('name', $prefix)->update(['value' => $input[$prefix]]);
			}
		}
	}


	function is_demo()
	{
		$data = $this->db->table('settings')->get()->getRowArray();
		return ($data['is_demo'] == '1') ? true : false;
	}

	function get_version_detail()
	{
		return $this->db->table('versions')->orderBy('id', 'desc')->get()->getRowArray();
	}

	function db_backup($params)
	{
		$this->db->table('db_backup')->insert($params);
		return $this->db->insertID();
	}

	function get_backup()
	{
		return $this->db->table('db_backup')->orderBy('id', 'desc')->get()->getResultArray();
	}

	function get_db_backup($id)
	{
		return $this->db->table('db_backup')->where('id', $id)->get()->getRowArray();
	}

	function get_languages()
	{
		return $this->db->table('languages')->get()->getResultArray();
	}

	function get_department($id)
	{
		return $this->db->table('departments')->where('id', $id)->get()->getRowArray();
	}

	function get_departments()
	{
		return $this->db->table('departments')->get()->getResultArray();
	}

	function add_department($params)
	{
		$this->db->table('departments')->insert($params);
		return $this->db->insertID();
	}

	function update_department($id, $params)
	{
		$this->db->table('departments')->where('id', $id)->update($params);
	}

	function delete_department($id)
	{
		$this->db->table('departments')->where('id', $id)->delete();
	}

	function check_department($id)
	{
		return $this->db->table('staff')->where('department_id', $id)->countAllResults();
	}


	function get_menus()
	{

		return $this->db->table('menu')
			->where(['main_menu' => '0', 'is_ativo' => '1'])
			->orderBy('order_id', 'ASC')
			->get()
			->getResultArray();
	}

	function get_submenus($id)
	{
		return $this->db->table('menu')
			->orderBy('order_id', 'ASC')
			->where('main_menu', $id)
			->where('is_ativo', '1')
			->get()
			->getResultArray();
	}

	function get_crm_lang()
	{
		$query = $this->db->table('settings');
		$totalRecords = $query->countAllResults();
		$query->limit(1, 0);
		$res = $query->get();

		if ($totalRecords > 0) {
			$row = $res->getRow();
			return $row->languageid;
		}
	}

	function default_timezone()
	{
		$query = $this->db->table('settings')->get();
		$row = $query->getRow();

		return $row ? $row->default_timezone : 'portuguese_pt';
	}

	function two_factor_authentication()
	{
		$query = $this->db->table('settings')->get();
		$row = $query->getRow();

		return $row ? $row->two_factor_authentication : null;
	}




	//v_162 replacing database query by json file

	// function get_currency() {

	//     $this->db->limit(1, 0);

	//     $query = $this->db->get('settings');

	//     if ($query->getNumRows() > 0) {

	//         $row = $query->row();

	//         $currencyid =  $row->currencyid;

	// 	}

	// 	// $this->db->limit(1, 0);


	//     // if ($query->getNumRows() > 0) {

	//     //     $row = $query->row();

	//     //     return $row->code;

	//     // }

	// }



	//this function gets the currency id from the json file 


	function get_currency()
	{
		// Obtém a configuração da moeda da tabela 'settings'
		$query = $this->db->table('settings')
			->limit(1)
			->get();

		$currency_symbol = '';

		// Obtém o resultado como um array
		$result = $query->getResultArray();

		if (count($result) > 0) {
			$row = $result[0];
			$currencyid = $row['currencyid'];

			// Lê o arquivo JSON com as moedas
			$jsonstring = file_get_contents('assets/json/currencies.json');
			$obj = json_decode($jsonstring, true);

			// Encontra o símbolo da moeda correspondente
			foreach ($obj as $currency) {
				if ($currency['id'] == $currencyid) {
					$currency_symbol = $currency['code'];
					break; // Adiciona break para sair do loop após encontrar a moeda
				}
			}
		}

		return $currency_symbol;
	}

	public function load_config()
	{
		$builder = $this->db->table('settings')->limit(1, 0);
		$query = $builder->get();

		if ($builder->countAllResults() > 0) {
			return $query->getRow();
		} else {
			return false;
		}
	}



	function get_rebranding_data()
	{
		$configs = $this->db->table('branding')->get()->getResultArray();
		$data = array();

		foreach ($configs as $config) {
			$data[$config['name']] = $config['value'];
		}

		return $data;
	}

	function get_payment_gateway_data()
	{
		$payments = $this->get_payment_modes();
		$data = array();

		foreach ($payments as $payment) {
			$data[$payment['name']] = $payment['value'];

			if (
				in_array($payment['name'], [
					'authorize_aim_active',
					'paypal_active',
					'stripe_active',
					'payu_money_active',
					'ccavenue_active',
					'paypal_test_mode_enabled',
					'payu_money_test_mode_enabled',
					'ccavenue_test_mode',
					'razorpay_active',
					'razorpay_test_mode_enabled',
					'authorize_test_mode_enabled'
				])
			) {
				$data[$payment['name']] = $payment['value'] == '1' ? TRUE : FALSE;
			}

			if ($payment['name'] == 'primary_bank_account') {
				if ($payment['value']) {
					$bank = $this->db->table('accounts')->where('id', $payment['value'])->get()->getRowArray();
					if ($bank) {
						$data['bank'] = $bank['name'];
					}
				}
			}
		}

		return $data;
	}

	public function if_timer()
	{
		$builder = $this->db->table('tasktimer');
		$builder->where('end', NULL);
		$builder->where('staff_id', session()->get('usr_id'));
		$data = $builder->countAllResults();
		return $data > 0 ? 'true' : 'false';
	}

	function increment_series($column, $number)
	{
		$this->db->table('appconfig')
			->where('name', $column)
			->update(['value' => $number]);
	}

	function get_pending_process()
	{
		return $this->db->table('pending_process')
			->orderBy('process_id', 'asc')
			->limit(10)
			->get()
			->getResultArray();
	}




	function create_process($process, $process_relation, $process_relation_type, $process_template_name)
	{
		$process_param = array(
			'process_type' => $process,
			'process_relation' => $process_relation,
			'process_relation_type' => $process_relation_type,
			'process_created' => date("Y.m.d H:i:s"),
			'process_template_name' => $process_template_name,
			//'process_createdby' => session()->usr_id,
		);

		$this->db->table('pending_process')->insert($process_param);
	}

	function remove_pending_process($id)
	{
		$response = $this->db->table('pending_process')->where('process_id', $id)->delete();
	}

	function isAdmin()
	{
		$id = session()->usr_id;

		$rows = $this->db->table('staff')
			->where('admin', 1)
			->where('id', $id)
			->countAllResults();

		return $rows > 0 ? 'true' : 'false';
	}

	function update_colors($params)
	{
		$appointment = $params['appointment_color'];
		$project = $params['project_color'];
		$task = $params['task_color'];

		$this->db->table('appconfig')
			->where('name', 'appointment_color')
			->update(['value' => $appointment]);

		$this->db->table('appconfig')
			->where('name', 'project_color')
			->update(['value' => $project]);

		$this->db->table('appconfig')
			->where('name', 'task_color')
			->update(['value' => $task]);

		$this->db->table('logs')->insert(array(
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('updated') . ' ' . lang2('calendar') . ' ' . lang2('settings')),
			'staff_id' => session()->usr_id,
		));
	}

	/**********Create New Role************/

	function create_role($params)
	{
		$this->db->table('roles')->insert($params);
		$role_id = $this->db->insertID();

		//LOG
		$staffname = session()->staffname;
		$loggedinuserid = session()->usr_id;

		$this->db->table('logs')->insert(array(
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('added') . ' ' . lang2('role')),
			'staff_id' => $loggedinuserid,
		));

		$permissions = request()->getPost('permissions');

		foreach ($permissions as $permission) {
			$this->db->table('role_permissions')->insert(array(
				'permission_id' => $permission['id'],
				'permission_view_own' => $permission['permission_view_own'] == 'true' ? 1 : 0,
				'permission_view_all' => $permission['permission_view_all'] == 'true' ? 1 : 0,
				'permission_create' => $permission['permission_create'] == 'true' ? 1 : 0,
				'permission_edit' => $permission['permission_edit'] == 'true' ? 1 : 0,
				'permission_delete' => $permission['permission_delete'] == 'true' ? 1 : 0,
				'role_id' => $role_id,
				'id_company' => session()->id_company,
			));
		}
	}

	/**********Update Role************/

	function update_role($params, $role_id)
	{
		$this->db->table('roles')
			->where('role_id', $role_id)
			->update($params);

		//LOG
		$staffname = session()->staffname;
		$loggedinuserid = session()->usr_id;

		$this->db->table('logs')->insert(array(
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('updated') . ' ' . lang2('role')),
			'staff_id' => $loggedinuserid,
		));

		$permissions = request()->getPost('permissions');

		foreach ($permissions as $permission) {
			if (isset($permission['role_permission_id'])) {
				$param = array(
					'permission_view_own' => $permission['permission_view_own'] == 'true' ? 1 : 0,
					'permission_view_all' => $permission['permission_view_all'] == 'true' ? 1 : 0,
					'permission_create' => $permission['permission_create'] == 'true' ? 1 : 0,
					'permission_edit' => $permission['permission_edit'] == 'true' ? 1 : 0,
					'permission_delete' => $permission['permission_delete'] == 'true' ? 1 : 0,
				);

				$this->db->table('role_permissions')
					->where('role_permission_id', $permission['role_permission_id'])
					->update($param);
			} else if (empty($permission['role_permission_id'])) {
				$this->db->table('role_permissions')->insert(array(
					'permission_id' => $permission['id'],
					'permission_view_own' => $permission['permission_view_own'] == 'true' ? 1 : 0,
					'permission_view_all' => $permission['permission_view_all'] == 'true' ? 1 : 0,
					'permission_create' => $permission['permission_create'] == 'true' ? 1 : 0,
					'permission_edit' => $permission['permission_edit'] == 'true' ? 1 : 0,
					'permission_delete' => $permission['permission_delete'] == 'true' ? 1 : 0,
					'role_id' => $role_id,
					'id_company' => session()->id_company,
				));
			}
		}
	}

	/**********Get All Roles************/

	function get_all_roles($id_company = null)
	{
		$builder = $this->db->table('roles')
			->select('role_id, role_name, role_type, role_updatedat')
			->orderBy('role_id', 'asc');

		if ($id_company === null) {
			$builder->where('id_company', session()->id_company);
		} else {
			$builder->where('id_company', $id_company);
		}

		return $builder->get()->getResultArray();
	}




	function get_role($id)
	{
		return $this->db->table('roles')
			->where('role_id', $id)
			->get()
			->getRowArray();
	}

	/**********Get The Permission of Roles************/

	function get_role_permission($permission_id, $role_id)
	{
		return $this->db->table('role_permissions')
			->where('role_id', $role_id)
			->where('permission_id', $permission_id)
			->where('id_company', session()->id_company)
			->get()
			->getRowArray();
	}

	function check_role($id)
	{
		return $this->db->table('staff')
			->where('role_id', $id)
			->countAllResults();
	}

	/**********Delete Role************/

	function delete_role($id)
	{
		$this->db->table('roles')
			->where('role_id', $id)
			->delete();

		$this->db->table('role_permissions')
			->where('role_id', $id)
			->delete();

		// LOG

		$staffname = session()->staffname;
		$loggedinuserid = session()->usr_id;

		$this->db->table('logs')->insert(array(
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('deleted') . ' ' . lang2('role')),
			'staff_id' => $loggedinuserid,
		));

		return true;
	}

	function payment_mode($payment)
	{
		return $this->db->table('payment_methods')
			->where('relation', $payment)
			->get()
			->getRowArray();
	}
}
