<?php

namespace App\Models;

use CodeIgniter\Model;

class Payments_Model extends Model
{

	function addpayment($params)
	{
		$this->db->table('deposits')->insert($params);
		$deposit = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['deposit_series'] ? $appconfig['deposit_series'] : $deposit;
		$deposit_number = $appconfig['deposit_prefix'] . $number;

		$this->db->table('deposits')->where('id', $deposit)->update(['deposit_number' => $deposit_number]);

		if (request()->getPost('balance') == 0) {
			$this->db->table('invoices')->where('id', request()->getPost('invoice'))->update(['status_id' => 2]);
			$this->db->table('sales')->where('invoice_id', request()->getPost('invoice'))->update(['status_id' => 2]);
		} else {
			$this->db->table('invoices')->where('id', request()->getPost('invoice'))->update(['status_id' => 3]);
			$this->db->table('sales')->where('invoice_id', request()->getPost('invoice'))->update(['status_id' => 3]);
		}

		$loggedinuserid = session()->get('usr_id');

		$this->db->table('payments')->insert([
			'transactiontype' => '0',
			'is_transfer' => '0',
			'invoice_id' => request()->getPost('invoice'),
			'amount' => request()->getPost('amount'),
			'account_id' => request()->getPost('account'),
			'date' => request()->getPost('date'),
			'not' => request()->getPost('not'),
			'attachment' => request()->getPost('attachment'),
			'customer_id' => request()->getPost('customer'),
			'staff_id' => $loggedinuserid,
		]);

		$payment_id = $this->db->insertID();

		$this->db->table('items')->insert([
			'relation_type' => 'deposit',
			'relation' => $deposit,
			'code' => 'deposit',
			'description' => get_number('deposits', $deposit, 'deposit', 'deposit'),
			'name' => 'deposit',
			'quantity' => '1',
			'price' => request()->getPost('amount'),
			'total' => request()->getPost('amount'),
		]);

		// LOG
		$staffname = session()->get('staffname');
		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url('staff/staffmember/' . $loggedinuserid) . '"> ' . $staffname . '</a> ' . lang2('added') . ' <a href="' . base_url('deposits/deposit/' . $deposit) . '">' . get_number('deposits', $deposit, 'deposit', 'deposit') . '</a>.',
			'staff_id' => $loggedinuserid,
			'customer_id' => request()->getPost('customer')
		]);

		return $payment_id;
	}

	function todaypayments()
	{

		return $this->db->table('payments')
			->where('DATE(date)', date('Y-m-d'))
			->join('staff', 'payments.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->get()
			->getResultArray();
	}

	function todaypayments_by_staff()
	{
	

		return $this->db->table('payments')
			->where('DATE(date)', date('Y-m-d'))
			->join('staff', 'payments.staff_id = staff.id', 'left')
			->where('staff_id', session()->get('usr_id'))
			->where('staff.id_company', session()->get('id_company'))
			->get()
			->getResultArray();
	}

	// Return the payment details
	function get_payment_details($id)
	{
		return $this->db->table('payments vp')
			->join('accounts ac', 'ac.id = vp.account_id')
			->join('staff st', 'st.id = vp.staff_id')
			->where('vp.id', $id)
			->where('st.id_company', session()->get('id_company'))
			->get()
			->getRowArray();
	}
}
