<?php

namespace App\Models;

use CodeIgniter\Model;


class Logs_Model extends Model
{

	function get_logs($date)
	{
		return $this->db->table('logs')
			->where('date', $date)
			->get()
			->getRowArray();
	}

	function getlog_json()
	{
		return $this->db->table('logs')
			->select('detail, date')
			->limit(50)
			->get()
			->getResult();
	}

	function get_all_logs()
	{
		return $this->db->table('logs')
			->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, logs.date as date')
			->join('staff', 'logs.staff_id = staff.id', 'left')
			->orderBy('date', 'desc')
			->limit(50)
			->get()
			->getResultArray();
	}

	function panel_last_logs($loadMore = '')
	{
		$builder = $this->db->table('logs')
			->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, logs.date as date')
			->join('staff', 'logs.staff_id = staff.id', 'left')
			->orderBy('date', 'desc')
			->where('staff.id_company', session()->get('id_company'));

		if ($loadMore == '') {
			$builder->limit(10);
		}

		return $builder->get()->getResultArray();
	}

	function panel_last_logs_by_staff($loadMore = '')
	{
		$builder = $this->db->table('logs')
			->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, logs.date as date')
			->join('staff', 'logs.staff_id = staff.id', 'left')
			->orderBy('date', 'desc')
			->where('staff_id', session()->get('usr_id'));

		if ($loadMore == '') {
			$builder->limit(10);
		}

		return $builder->get()->getResultArray();
	}

	function panel_last_login_log_by_staff()
	{
		return $this->db->table('logs')
			->select('*')
			->where('staff_id', session()->get('usr_id'))
			->where('key_log', 'inactive')
			->orWhere('key_log', 'login')
			->orderBy('id', 'desc')
			->get()
			->getRow();
	}

	function get_logs_by_date($data, $id)
	{
		$dateFormatted = implode("-", array_reverse(explode(".", $data)));

		$logs = $this->db->table('logs')
			->select('*')
			->where('staff_id', $id)
			->where("CAST(date AS DATE) =", $dateFormatted)
			->orderBy('id', 'desc')
			->get()
			->getResultArray();

		foreach ($logs as &$row) {
			$row['date'] = date("d/m/Y H:i:s", strtotime($row['date']));
		}

		return $logs;
	}

	function get_logs_by_date2($id)
	{
		$startDate = request()->getPost('dt_de');
		$endDate = request()->getPost('dt_ate');

		$logs = $this->db->table('logs')
			->select('*')
			->where('staff_id', $id)
			->where("CAST(date AS DATE) >=", $startDate)
			->where("CAST(date AS DATE) <=", $endDate)
			->orderBy('id', 'desc')
			->get()
			->getResultArray();

		foreach ($logs as &$row) {
			$row['date'] = date("d/m/Y H:i:s", strtotime($row['date']));
		}

		return $logs;
	}

	function get_logs_by_customer($id)
	{
		return $this->db->table('logs')
			->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, logs.date as date')
			->join('staff', 'logs.staff_id = staff.id', 'left')
			->orderBy('date', 'desc')
			->limit(50)
			->where('id', $id)
			->get()
			->getResultArray();
	}

	function get_logs_by_customerId($id, $loadMore = '')
	{
		$builder = $this->db->table('logs')
			->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, logs.date as date')
			->join('staff', 'logs.staff_id = staff.id', 'left')
			->orderBy('date', 'desc')
			->where('customer_id', $id);

		if ($loadMore == '') {
			$builder->limit(10);
		}

		return $builder->get()->getResultArray();
	}

	function project_logs($id)
	{
		return $this->db->table('logs')
			->orderBy('date', 'desc')
			->where('project_id', $id)
			->get()
			->getResultArray();
	}

	function staffmember_log()
	{
		return $this->db->table('logs')
			->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, logs.date as date')
			->join('staff', 'logs.staff_id = staff.id', 'left')
			->limit(5)
			->orderBy('date', 'desc')
			->where('staff_id', session()->get('usr_id'))
			->get()
			->getResultArray();
	}

	function delete_logs($date)
	{
		$response = $this->db->table('logs')->delete(['date' => $date]);

		if ($response) {
			return "Logs Deleted";
		} else {
			return "Log error";
		}
	}
}
