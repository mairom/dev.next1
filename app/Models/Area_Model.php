<?php

namespace App\Models;

use CodeIgniter\Model;

defined('BASEPATH') or exit('No direct script access allowed');

class Area_Model extends Model
{


	function create_user($email, $password)
	{

		$data = array(

			'email' => $email,

			'password' => $this->hash_password($password),

			'created_at' => date('Y-m-j H:i:s'),

		);

		return $this->db->table('contacts')->insert( $data);
	}



	function resolve_user_login($email, $password)
	{
		$builder = $this->db->table('contacts');
		$builder->select('password');
		$builder->where('email', $email);
		$hash = $builder->get()->getRow()->password;

		return $this->verify_password_hash($password, $hash);
	}

	function get_contact_id_from_email($email)
	{
		$builder = $this->db->table('contacts');
		$builder->select('id');
		$builder->where('email', $email);
		return $builder->get()->getRow()->id;
	}

	function get_customer($email)
	{
		$builder = $this->db->table('contacts');
		$builder->select('customer_id');
		$builder->where('email', $email);
		return $builder->get()->getRow()->customer_id;
	}

	function get_user($contact_id)
	{
		$builder = $this->db->table('contacts');
		$builder->where('id', $contact_id);
		return $builder->get()->getRow();
	}



	function hash_password($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}



	function verify_password_hash($password, $hash)
	{
		return password_verify($password, $hash);
	}



	function ttc()
	{
		$contactId = session()->get('contact_id');

		$ttc = $this->db->table('tickets')
			->where('contact_id', $contactId)
			->countAllResults();

		return $ttc;
	}

	function otc()
	{
		$contactId = session()->get('contact_id');

		$otc = $this->db->table('tickets')
			->where('status_id', 1)
			->where('contact_id', $contactId)
			->countAllResults();

		return $otc;
	}

	function ipc()
	{
		$contactId = session()->get('contact_id');

		$ipc = $this->db->table('tickets')
			->where('status_id', 2)
			->where('contact_id', $contactId)
			->countAllResults();

		return $ipc;
	}

	function atc()
	{
		$contactId = session()->get('contact_id');

		$atc = $this->db->table('tickets')
			->where('status_id', 3)
			->where('contact_id', $contactId)
			->countAllResults();

		return $atc;
	}

	function ctc()
	{
		$contactId = session()->get('contact_id');

		$ctc = $this->db->table('tickets')
			->where('status_id', 4)
			->where('contact_id', $contactId)
			->countAllResults();

		return $ctc;
	}

	function add_tickets($params)
	{

		// Insere os dados na tabela 'tickets'
		$this->db->table('tickets')->insert($params);
		$ticket = $this->db->insertID();


		$contactname = $_SESSION['name'];

		$contactsurname = $_SESSION['surname'];

		$loggedinuserid = $_SESSION['contact_id'];

		$this->db->table('logs')->insert( array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('' . $contactname . ' ' . $contactsurname . ' ' . lang('added') . ' <a href="tickets/ticket/' . $ticket . '"> ' . lang('ticket') . '-' . $ticket . '</a>'),

			'customer_id' => $loggedinuserid

		));

		$customer = $_SESSION['customer'];

		$staffavatar = 'n-img.png';

		$this->db->table('notifications')->insert( array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('' . $contactname . ' ' . $contactsurname . ' ' . lang('addednewticket') . ''),

			'public' => 1,

			'target' => '' . base_url('tickets/ticket/' . $ticket . '') . '',

			'perres' => $staffavatar

		));

		return $this->db->insertID();
	}



	function newnotification()
	{

		$new = $this->db->table('notifications')->where('customerread', '0')->where('contact_id', $_SESSION['contact_id'])->get()->getResult();

		if ($new) {

			return true;
		} else {

			return false;
		}
	}



	function get_all_notifications()
	{
		return $this->db->table('notifications')
			->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, notifications.id as notifyid')
			->join('staff', 'notifications.staff_id = staff.id', 'left')
			->orderBy('notifyid', 'desc')
			->where('contact_id = ' . $_SESSION['contact_id'] . ' OR customer_id = ' . $_SESSION['customer'])
			->get()
			->getResultArray();
	}

	function customerdebt()
	{
		return $this->db->table('invoices')
			->selectSum('total')
			->where('status_id = 3 AND customer_id = ' . $_SESSION['customer'])
			->get()
			->getRow()
			->total;
	}

	function logs()
	{
		return $this->db->table('logs')
			->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, logs.date as date')
			->join('staff', 'logs.staff_id = staff.id', 'left')
			->orderBy('date', 'desc')
			->where('customer_id', $_SESSION['customer'])
			->get()
			->getResultArray();
	}

	function customer_annual_sales_chart()
	{
		$customer = $_SESSION['customer'];
		$totalsales = array();
		$i = 0;

		for ($mo = 1; $mo <= 12; $mo++) {
			$gains = $this->db->table('sales')
				->select('total')
				->where('MONTH(sales.date)', $mo)
				->where('customer_id = ' . $customer)
				->get()
				->getResultArray();

			if (!isset($totalsales[$mo])) {
				$totalsales[$i] = array();
			}

			if (count($gains) > 0) {
				foreach ($gains as $gainx) {
					$totalsales[$i][] = $gainx['total'];
				}
			} else {
				$totalsales[$i][] = 0;
			}

			$totalsales[$i] = array_sum($totalsales[$i]);
			$i++;
		}

		return json_encode($totalsales);
	}
}
