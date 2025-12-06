<?php

namespace App\Models;

use CodeIgniter\Model;

class Accounts_Model extends Model
{

	public function get_accounts($id)
	{
		$builder = $this->db->table('accounts');
		return $builder->where('id', $id)
			->where('id_company', session()->get('id_company'))
			->get()
			->getRowArray();
	}

	public function get_all_accounts()
	{
		$builder = $this->db->table('accounts');
		return $builder->where('id_company', session()->get('id_company'))
			->get()
			->getResultArray();
	}

	public function get_all_transactions()
	{
		$builder = $this->db->table('payments');
		return $builder->get()
			->getResultArray();
	}

	public function create($params)
	{
		$builder = $this->db->table('accounts');
		$builder->insert($params);
		$account = $this->db->insertID();

		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');

		$builder = $this->db->table('logs');
		$builder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang('addedanewaccount') . ' <a href="accounts/account/' . $account . '"> ' . lang('account') . '-' . $account . '</a>',
			'staff_id' => $loggedinuserid
		]);

		return $account;
	}




	public function update2($id, $params)
	{
		$builder = $this->db->table('accounts');
		$builder->where('id', $id);
		$response = $builder->update($params);

		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');

		$builder = $this->db->table('logs');
		$builder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang('updated') . ' <a href="accounts/account/' . $id . '"> ' . lang('account') . '-' . $id . '</a>',
			'staff_id' => $loggedinuserid
		]);

		return $response;
	}

	public function delete_account($id)
	{
		$builder = $this->db->table('payments');
		$payments = $builder->where('account_id', $id)->countAllResults();

		$builder = $this->db->table('expenses');
		$expenses = $builder->where('account_id', $id)->countAllResults();

		$builder = $this->db->table('orders');
		$order = $builder->where('relation_type', 'customer')
			->where('relation', $id)
			->countAllResults();

		if (($payments > 0) || ($expenses > 0)) {
			return false;
		} else {
			$builder = $this->db->table('accounts');
			$response = $builder->delete(['id' => $id]);

			$loggedinuserid = session()->get('usr_id');
			$staffname = session()->get('staffname');

			$builder = $this->db->table('logs');
			$builder->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang('deleted') . ' ' . lang('account') . '-' . $id,
				'staff_id' => $loggedinuserid
			]);

			return true;
		}
	}

	function account_details($id)
	{

		$account = $this->Accounts_Model->get_accounts($id);
		$account_incomings_sum = $this->db->table('payments')
			->selectSum('amount')
			->where('account_id', $id)
			->where('transactiontype', 0)
			->get()
			->getRow()
			->amount;

		$account_outgoings_sum = $this->db->table('payments')
			->selectSum('amount')
			->where('account_id', $id)
			->where('transactiontype', 1)
			->get()
			->getRow()
			->amount;


		$account_sum = ($account_incomings_sum - $account_outgoings_sum);

		if (!empty($account_sum)) {

			$account_total = $account_incomings_sum - $account_outgoings_sum;
		} else {

			$account_total = 0;
		}

		switch ($account['status_id']) {

			case '1':

				$is_status = false;

				break;

			case '0':

				$is_status = true;

				break;
		}

		$data_account = array(

			'id' => $account['id'],

			'name' => $account['name'],

			'type' => $account['type'],

			'bankname' => $account['bankname'],

			'branchbank' => $account['branchbank'],

			'account' => $account['account'],

			'iban' => $account['iban'],

			'account_total' => $account_total,

			'status' => $is_status,

		);

		return $data_account;
	}
}
