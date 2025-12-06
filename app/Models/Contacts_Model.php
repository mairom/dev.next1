<?php

namespace App\Models;

use CodeIgniter\Model;

class Contacts_Model extends Model
{

	protected $table = 'contacts';

	function get_contacts($id)
	{

		return $this->db->table('contacts')
			->where('id', $id)
			->get()
			->getRowArray();
	}



	function get_all_contacts()
	{
		return $this->db->table('contacts')
			->get()
			->getResultArray();
	}



	function get_customer_contacts($id)
	{
		return $this->db->table('contacts')
			->where('customer_id', $id)
			->get()
			->getResultArray();
	}



	function create($params)
	{
		$this->db->table('contacts')->insert($params);
		$contact = $this->db->insertID();

		$customer = request()->getPost('customer');
		$staffname = session()->staffname;
		$contactname = request()->getPost('name');
		$contactsurname = request()->getPost('surname');
		$loggedinuserid = session()->usr_id;

		$this->db->table('logs')->insert(array(
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('' . $message = sprintf(lang2('addedcontact'), $staffname, $contactname, $contactsurname) . ''),
			'staff_id' => $loggedinuserid,
			'customer_id' => $customer,
		));

		return $contact;
	}



	public function update2($id, $params)
	{
		$builder = $this->db->table('contacts');
		$builder->where('id', $id);
		$builder->update($params);
	}

	public function delete2($id)
	{
		$builder = $this->db->table('contacts');
		$builder->where('id', $id);
		$builder->delete();
	}


	function isDuplicate($email)
	{

		$this->db->table('contacts')
			->where('email', $email)
			->join('customers', 'customers.id = contacts.customer_id', 'left')
			->where('id_company', session()->id_company)
			->limit(1)
			->get();

		$contacts = $this->db->affectedRows();

		$this->db->table('customers')
			->where('email', $email)
			->where('id_company', session()->id_company)
			->limit(1)
			->get();

		$customers = $this->db->affectedRows();


		if (($contacts > 0) || ($customers > 0)) {

			$result = true;
		} else {

			$result = false;
		}

		return $result;
	}



	public



	function insertToken($contact_id)
	{

		$token = substr(sha1(rand()), 0, 30);

		$date = date('Y-m-d');



		$string = array(

			'token' => $token,

			'contact_id' => $contact_id,

			'created' => $date

		);

		$query = $this->db->insert_string('tokens', $string);

		$this->db->query($query);

		return $token . $contact_id;
	}



	public



	function isTokenValid($token)
	{

		$tkn = substr($token, 0, 30);

		$uid = substr($token, 30);



		$q = $this->db->table('tokens')
			->where('token', $tkn)
			->where('contact_id', $uid)
			->limit(1)
			->get();



		if ($this->db->affected_rows() > 0) {

			$row = $q->row();



			$created = $row->created;

			$createdTS = strtotime($created);

			$today = date('Y-m-d');

			$todayTS = strtotime($today);



			if ($createdTS != $todayTS) {

				return false;
			}



			$user_info = $this->getUserInfo($row->contact_id);

			return $user_info;
		} else {

			return false;
		}
	}



	public



	function getUserInfo($id)
	{

		$q = $this->db->table('contacts')
			->where('id', $id)
			->limit(1)
			->get();

		if ($q->getNumRows() > 0) {
			$row = $q->getRow();
			return $row;
		} else {
			error_log('no user found getUserInfo(' . $id . ')');
			return false;
		}
	}



	public function updateUserInfo($post)
	{
		$data = [
			'password' => $post['password'],
			'last_login' => date('Y-m-d h:i:s A'),
			'inactive' => $this->inactive[1]
		];

		$builder = $this->db->table('contacts');
		$builder->where('id', $post['contact_id']);
		$builder->update($data);
		$success = $this->db->affectedRows();

		if (!$success) {
			error_log('Unable to updateUserInfo(' . $post['contact_id'] . ')');
			return false;
		}

		$user_info = $this->getUserInfo($post['contact_id']);
		return $user_info;
	}




	public function getUserInfoByEmail($email)
	{

		$q = $this->db->table('contacts')
			->where('email', $email)
			->limit(1)
			->get();

		if ($q->getNumRows() > 0) {
			$row = $q->getRow();
			return $row;
		} else {
			error_log('no user found getUserInfo(' . $email . ')');
			return false;
		}
	}

	public function updatePassword($post)
	{
		$builder = $this->db->table('contacts');
		$builder->where('id', $post['contact_id']);
		$builder->update(['password' => $post['password']]);
		$success = $this->db->affectedRows();

		if (!$success) {
			error_log('Unable to updatePassword(' . $post['contact_id'] . ')');
			return false;
		}

		return true;
	}
}
