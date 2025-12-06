<?php

namespace App\Models;

use CodeIgniter\Model;


class Customers_Model extends Model
{
	function get_customers($id)
	{
		$builder = $this->db->table('customers');
		$builder->select('*, customers.id as id');
		$builder->join('customergroups', 'customers.groupid = customergroups.id', 'left');
		$builder->where(['customers.id' => $id, 'customers.id_company' => session()->get('id_company')]);
		return $builder->get()->getRowArray();
	}




	function get_all_customers($staff_id = '', $custumer_sucess = false)
	{

		$builder = $this->db->table('customers');
		$builder->select('*, customers.id as id, staff.staffname as name_custumer');
		$builder->join('customergroups', 'customers.groupid = customergroups.id', 'left');
		$builder->join('staff', 'customers.customer_sucess = staff.id', 'left');
		$builder->orderBy('customers.id', 'desc');
		$builder->where('customers.id_company', session()->get('id_company'));

		if (request()->getGet('customer_status_id')) {
			$builder->where('customer_status_id', request()->getGet('customer_status_id'));
		}

		if ($custumer_sucess) {
			if ($staff_id != '') {
				$builder->where('customers.customer_sucess', session()->get('usr_id'));
			}
		}

		if ($staff_id) {
			$builder->where('customers.customer_sucess', $staff_id);
		}

		return $builder->get()->getResultArray();
	}



	function get_subsidiaries($id)
	{
		$builder = $this->db->table('customers');
		$builder->select('*, customers.id as id');
		$builder->where('customers.subsidiary_parent_id', $id);
		$builder->where('customers.id_company', session()->get('id_company'));
		$builder->orderBy('customers.id', 'desc');
		return $builder->get()->getResultArray();
	}



	function add_customers($params)
	{
		$this->db->table('customers')->insert($params);
		$customer_id = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['customer_series'] ? $appconfig['customer_series'] : $customer_id;
		$customer_number = $appconfig['customer_prefix'] . $number;

		$this->db->table('customers')
			->where('id', $customer_id)
			->update(['customer_number' => $customer_number, 'id_company' => session()->get('id_company')]);

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . session()->get('usr_id') . '"> ' . session()->get('staffname') . '</a> ' . lang2('addedacustomer') . ' <a href="customers/customer/' . $customer_id . '">' . get_number('customers', $customer_id, 'customer', 'customer') . '</a>',
			'staff_id' => session()->get('usr_id')
		]);

		$isContact = request()->getPost('contact');

		if ($isContact == '1') {

			$password = password_hash(request()->getPost('password'), PASSWORD_BCRYPT);

			if (request()->getPost('company')) {

				$company = request()->getPost('company');
			} else {

				$company = request()->getPost('namesurname');
			}

			$param = array(

				'name' => $company,

				'surname' => '',

				'phone' => request()->getPost('phone'),

				'email' => request()->getPost('email'),

				'password' => $password,

				'address' => request()->getPost('address'),

				'customer_id' => $customer_id,

			);
			$Contacts_Model = new Contacts_Model;
			$contacts_id = $Contacts_Model->create($param);

			if ($contacts_id) {

				$update_contact_privilege = $this->update_contact_privilege($contacts_id, 'true', '1');

				$update_contact_privilege = $this->update_contact_privilege($contacts_id, 'true', '2');

				$update_contact_privilege = $this->update_contact_privilege($contacts_id, 'true', '7');

				$update_contact_privilege = $this->update_contact_privilege($contacts_id, 'true', '9');

				$update_contact_privilege = $this->update_contact_privilege($contacts_id, 'true', '16');


				$Emails_Model = new Emails_Model;
				$template = $Emails_Model->get_template('customer', 'new_contact_added');

				if ($template['status'] == 1) {

					$message_vars = array(

						'{login_email}' => request()->getPost('email'),

						'{login_password}' => (request()->getPost('password')) ? (request()->getPost('password')) : ' ',

						'{app_url}' => '' . base_url('area/login') . '',

						'{email_signature}' => session()->get('email'),

						'{name}' => session()->get('staffname'),

						'{customer}' => request()->getPost('name')

					);

					$subject = strtr($template['subject'], $message_vars);

					$message = strtr($template['message'], $message_vars);

					$param = array(

						'from_name' => $template['from_name'],

						'email' => request()->getPost('email'),

						'subject' => $subject,

						'message' => $message,

						'created' => date("Y.m.d H:i:s"),

					);

					if (request()->getPost('email')) {

						$this->db->table('email_queue')->insert($param);
					}
				}
			}
		}

		return $customer_id;
	}



	function update_contact_privilege($id, $value, $privilege_id)
	{
		$builder = $this->db->table('privileges');

		if ($value != 'false') {
			$params = [
				'relation' => (int)$id,
				'relation_type' => 'contact',
				'permission_id' => (int)$privilege_id
			];

			$builder->insert($params);
			return $this->db->insertID();
		} else {
			$builder->where([
				'relation' => $id,
				'relation_type' => 'contact',
				'permission_id' => $privilege_id
			]);
			$builder->delete();
		}
	}

	public function get_customers_for_import()
	{
		$builder = $this->db->table('customers'); // Constrói a consulta para a tabela 'customers'
		$query = $builder->get()->getResultArray(); // Executa a consulta e retorna o resultado
	
		if (count($query) > 0) { // Conta corretamente o número de linhas
			return $query; // Retorna o resultado em formato de array
		} else {
			return false; // Retorna false se não houver linhas
		}
	}


	function get_customer_groups()
	{
		$builder = $this->db->table('customergroups');
		$builder->orderBy('id', 'desc');
		$builder->where('id_company', session()->get('id_company'));
		return $builder->get()->getResultArray();
	}

	function get_groups($staff_id = '')
	{
		$builder = $this->db->table('customers');
		$builder->select('customergroups.id, customergroups.name as name, COUNT(customergroups.name) as y');
		$builder->join('customergroups', 'customers.groupid = customergroups.id', 'left');

		if ($staff_id) {
			$builder->where('staff_id', $staff_id);
		}

		$builder->groupBy('customergroups.name');
		$builder->where('customers.id_company', session()->get('id_company'));

		return $builder->get()->getResultArray();
	}

	function get_group($id)
	{
		$builder = $this->db->table('customergroups');
		$builder->where('id', $id);
		$builder->where('id_company', session()->get('id_company'));
		return $builder->get()->getRowArray();
	}

	function update_group($id, $params)
	{
		$builder = $this->db->table('customergroups');
		$builder->where('id', $id);
		return $builder->update($params);
	}

	function check_group($id)
	{
		$builder = $this->db->table('customers');
		$builder->where('groupid', $id);
		$builder->where('customers.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}

	function remove_group($id)
	{
		$builder = $this->db->table('customergroups');
		$builder->where('id', $id);
		return $builder->delete();
	}

	function insert_customers_csv($data)
	{
		$builder = $this->db->table('customers');
		$builder->insert($data);
		$customer = $this->db->insertID();
		$appconfig = get_appconfig();
		$number = $appconfig['customer_series'] ? $appconfig['customer_series'] : $customer;
		$customer_number = $appconfig['customer_prefix'] . $number;

		$builder->where('id', $customer);
		$builder->update(['customer_number' => $customer_number]);

		return $customer;
	}




	public function update_customers($id, $params)
	{
		$appconfig = get_appconfig();
		$customer_data = $this->get_customers($id);

		if ($customer_data['customer_number'] == '') {
			$number = $appconfig['customer_series'] ? $appconfig['customer_series'] : $id;
			$customer_number = $appconfig['customer_prefix'] . $number;
			$builder = $this->db->table('customers');
			$builder->where('id', $id)->update(['customer_number' => $customer_number]);

			if ($appconfig['customer_series'] != '') {
				$customer_number = $appconfig['customer_series'];
				$customer_number = $customer_number + 1;
				$this->model('Settings_Model');

				$Settings_Model = new Settings_Model;
				$Settings_Model->increment_series('invoice_series', $customer_number);
			}
		}

		$builder = $this->db->table('customers');
		$response = $builder->where('id', $id)->update($params);

		$loggedinuserid = session()->get('usr_id');
		$staffname = session()->get('staffname');

		$builder = $this->db->table('logs');
		$builder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '">' . $staffname . '</a> ' . lang('updated') . ' <a href="' . base_url() . 'customers/customer/' . $id . '">' . get_number('customers', $id, 'customer', 'customer') . '</a>.',
			'staff_id' => $loggedinuserid,
		]);
	}


	function delete_customers($id, $number)
	{
		$builder = $this->db->table('invoices');
		$invoice = $builder->where('customer_id', $id)->countAllResults();

		$builder = $this->db->table('proposals');
		$proposal = $builder->where(['relation_type' => 'customer', 'relation' => $id])->countAllResults();

		$builder = $this->db->table('expenses');
		$expense = $builder->where('customer_id', $id)->countAllResults();

		$builder = $this->db->table('projects');
		$project = $builder->where('customer_id', $id)->countAllResults();

		$builder = $this->db->table('tickets');
		$ticket = $builder->where('customer_id', $id)->countAllResults();

		$builder = $this->db->table('deposits');
		$deposit = $builder->where('customer_id', $id)->countAllResults();

		$builder = $this->db->table('orders');
		$order = $builder->where(['relation_type' => 'customer', 'relation' => $id])->countAllResults();

		if ($invoice > 0 || $proposal > 0 || $expense > 0 || $project > 0 || $ticket > 0 || $deposit > 0 || $order > 0) {
			return false;
		} else {
			$builder = $this->db->table('customers');
			$builder->where('id', $id)->delete();

			$builder = $this->db->table('contacts');
			$builder->where('customer_id', $id)->delete();

			$builder = $this->db->table('logs');
			$builder->where('customer_id', $id)->delete();

			$builder = $this->db->table('notifications');
			$builder->where('customer_id', $id)->delete();

			$builder = $this->db->table('reminders');
			$builder->where(['relation_type' => 'customer', 'relation' => $id])->delete();

			$builder = $this->db->table('notes');
			$builder->where(['relation_type' => 'customer', 'relation' => $id])->delete();

			$loggedinuserid = session()->get('usr_id');

			$builder = $this->db->table('logs');
			$builder->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . session()->get('staffname') . '</a> ' . lang2('deleted') . ' ' . $number,
				'staff_id' => $loggedinuserid,
			]);

			return true;
		}
	}



	function search_json_customer()
	{
		$builder = $this->db->table('customers');
		$builder->select('id as customer, type as customertype, company as company, namesurname as individual');
		$builder->where('customers.id_company', session()->get('id_company'));
		return $builder->get()->getResult();
	}

	function get_customers_by_privileges($id, $staff_id = '')
	{
		$builder = $this->db->table('customers');
		$builder->select('*, customers.id as id, leadssources.name as sourcename, leads_list.nm_list as nm_funil_origem');
		$builder->join('customergroups', 'customers.groupid = customergroups.id', 'left');
		$builder->join('leadssources', 'customers.source_id = leadssources.id', 'left');
		$builder->join('leads_list', 'leads_list.id_list = customers.funil_origem', 'left');
		$builder->where('customers.id_company', session()->get('id_company'));
		$builder->orderBy('created', 'desc');

		if ($staff_id) {
			$builder->where('customers.customer_sucess', $staff_id);
		}

		$builder->where('customers.id', $id);
		return $builder->get()->getRowArray();
	}

	function search_customers($q)
	{
		$builder = $this->db->table('customers');
		$builder->select('*');
		$builder->where('customers.id_company', session()->get('id_company'));

		if (!empty($q)) {
			$builder->groupStart(); 
			$builder->like('email', $q);
			$builder->orLike('company', $q);
			$builder->orLike('namesurname', $q);
			$builder->groupEnd(); 
		}

		$builder->orderBy('id', 'desc');
		return $builder->get()->getResultArray();
	}
}
