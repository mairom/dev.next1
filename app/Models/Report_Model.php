<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;
use DateInterval;

class Report_Model extends Model
{
	function totalprojecttasks($id)
	{
		$builder = $this->db->table('tasks');
		$builder->select('tasks.*');
		$builder->where('relation', $id);
		$builder->where('relation_type', 'project');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);


		$totalopentasks = $builder->countAllResults(); // Retorna o número de linhas
		return $totalopentasks;
	}
	function openprojecttasks($id)
	{
		$builder = $this->db->table('tasks');
		$builder->select('tasks.*');
		$builder->where('relation', $id);
		$builder->where('relation_type', 'project');
		$builder->where('status_id', '1');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);

		$totalopentasks = $builder->countAllResults(); // Retorna o número de linhas
		return $totalopentasks;
	}
	function completeprojecttasks($id)
	{
		$builder = $this->db->table('tasks');
		$builder->select('tasks.*');
		$builder->where('relation', $id);
		$builder->where('relation_type', 'project');
		$builder->where('status_id', 4);
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);

		$totalopentasks = $builder->countAllResults(); // Retorna o número de linhas
		return $totalopentasks;
	}
	public function newreminder()
	{
		$builder = $this->db->table('reminders');
		$builder->join('staff', 'reminders.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('date <= CURDATE()');
		$builder->where('staff_id', session()->get('usr_id'));
		$builder->where('isnotified != 1');
		// Usa countAllResults para contar o número de linhas
		$count = $builder->countAllResults();
		return $count;
	}
	function expenses_percent_by_category($id)
	{
		$builder = $this->db->table('expenses');
		$builder->select('COUNT(*) AS count');
		$builder->where('category_id', $id);
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$query = $builder->get();
		$excat = $query->getRow()->count;
		// Total Expenses
		$builder = $this->db->table('expenses');
		$builder->select('COUNT(*) AS count');
		$query = $builder->get();
		$totalexpenses = $query->getRow()->count;
		$percent = ($totalexpenses > 0 ? number_format(($excat * 100) / $totalexpenses) : 0);
		return $percent;
	}
	function expenses_amount_by_category($id)
	{
		$builder = $this->db->table('expenses');
		$builder->selectSum('amount');
		$builder->where('category_id', $id);
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('data_de')) {
			$builder->where("expenses.duedate >= '" . request()->getPost('data_de') . "'");
		}
		if (request()->getPost('data_ate')) {
			$builder->where("expenses.duedate <= '" . request()->getPost('data_ate') . "'");
		}
		$query = $builder->get();
		$total_value = $query->getRow()->amount;
		return !empty($total_value) ? $total_value : 0;
	}
	function get_account_amount($id)
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->join('staff', 'staff.id = payments.staff_id');
		$builder->where('account_id', $id);
		$builder->where('transactiontype', 0);
		$builder->where('staff.id_company', session()->id_company);
		$query = $builder->get();
		$account_incomings_sum = $query->getRow()->amount;
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->join('staff', 'staff.id = payments.staff_id');
		$builder->where('account_id', $id);
		$builder->where('transactiontype', 1);
		$builder->where('staff.id_company', session()->id_company);
		$query = $builder->get();
		$account_outgoings_sum = $query->getRow()->amount;
		$account_sum = $account_incomings_sum - $account_outgoings_sum;
		return !empty($account_sum) ? $account_sum : 0;
	}
	function pff_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->selectSum('total');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('status_id', 1);
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >= '" . request()->getPost('data_de') . "'");
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <= '" . request()->getPost('data_ate') . "'");
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where("purchases.vendor_id", request()->getPost('fornecedor'));
		}
		$query = $builder->get();
		$total_value = $query->getRow()->total;
		return !empty($total_value) ? $total_value : 0;
	}
	function ofv_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->selectSum('total');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('status_id', 2);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}
		$query = $builder->get();
		$total_value = $query->getRow()->total;
		return !empty($total_value) ? $total_value : 0;
	}
	function oft_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->selectSum('total');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('status_id', 3);
		$builder->where('CURDATE() <= duedate');
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}
		$query = $builder->get();
		$total_value = $query->getRow()->total;
		return !empty($total_value) ? $total_value : 0;
	}
	function vgf_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->selectSum('total');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('CURDATE() >= duedate');
		$builder->where('duedate !=', '0000-00-00');
		$builder->where('status_id !=', 4);
		$builder->where('status_id !=', 2);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}
		$query = $builder->get();
		$total_value = $query->getRow()->total;
		return !empty($total_value) ? $total_value : 0;
	}
	function tfa_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->where('status_id !=', 4);
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}

		return $builder->countAllResults();
	}
	function pfs_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->where('status_id', 1);
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}
		return $builder->countAllResults();
	}
	function otf_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->where('status_id', 2);
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}
		return $builder->countAllResults();
	}
	function tef_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->where('status_id', 3);
		$builder->where('CURDATE() <= duedate');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}
		return $builder->countAllResults();
	}
	function vdf_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->where('CURDATE() >= duedate');
		$builder->where('duedate !=', '0000-00-00');
		$builder->where('status_id !=', 4);
		$builder->where('status_id !=', 2);
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}
		return $builder->countAllResults();
	}
	function fam_purchases()
	{
		$builder = $this->db->table('purchases');
		$builder->selectSum('total');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where('purchases.vendor_id', request()->getPost('fornecedor'));
		}
		$query = $builder->get();
		$total_value = $query->getRow()->total;
		return !empty($total_value) ? $total_value : 0;
	}
	// ACCOUNTS
	function tht()
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('transactiontype', 0);
		$query = $builder->get();
		$total_value = $query->getRow()->amount;
		return !empty($total_value) ? $total_value : 0;
	}
	function total_incomings()
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('transactiontype', 0);
		$builder->where('is_transfer', 0);
		$query = $builder->get();
		$total_incomings = $query->getRow()->amount;
		return !empty($total_incomings) ? $total_incomings : 0;
	}
	function total_outgoings()
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('transactiontype', 1);
		$builder->where('is_transfer', 0);
		$query = $builder->get();
		$total_outgoings = $query->getRow()->amount;
		return !empty($total_outgoings) ? $total_outgoings : 0;
	}
	// CASH FLOW
	// ONLY THIS WEEK
	function put()
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('WEEK(date)', date('W'));
		$query = $builder->get();
		$total_value = $query->getRow()->amount;
		return !empty($total_value) ? $total_value : 0;
	}
	function pay()
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('WEEK(date)', date('W'));
		$builder->where('transactiontype', 0);
		$builder->where('is_transfer', 0);
		$query = $builder->get();
		$total_value = $query->getRow()->amount;
		return !empty($total_value) ? $total_value : 0;
	}
	function exp()
	{
		$builder = $this->db->table('payments');
		$builder->selectSum('amount');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('WEEK(date)', date('W'));
		$builder->where('transactiontype', 1);
		$builder->where('is_transfer', 0);
		$query = $builder->get();
		$total_value = $query->getRow()->amount;
		return !empty($total_value) ? $total_value : 0;
	}
	function totalpaym()
	{
		$builder = $this->db->table('payments');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('WEEK(date)', date('W'));
		return $builder->countAllResults();
	}
	function incomings()
	{
		$builder = $this->db->table('payments');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('WEEK(date)', date('W'));
		$builder->where('transactiontype', 0);
		return $builder->countAllResults();
	}
	// ONLY THIS WEEK
	function outgoings()
	{
		$builder = $this->db->table('payments');
		$builder->join('staff', 'payments.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('WEEK(date)', date('W'));
		$builder->where('transactiontype', 1);
		return $builder->countAllResults();
	}
	// PROJECTS FUNCTIONS
	function tpz()
	{
		$builder = $this->db->table('projects');
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function nsp()
	{
		$builder = $this->db->table('projects');
		$builder->where('status_id', 1);
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function sep()
	{
		$builder = $this->db->table('projects');
		$builder->where('status_id', 2);
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function pep()
	{
		$builder = $this->db->table('projects');
		$builder->where('status_id', 3);
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function cap()
	{
		$builder = $this->db->table('projects');
		$builder->where('status_id', 4);
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function cop()
	{
		$builder = $this->db->table('projects');
		$builder->where('status_id', 5);
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	// CUSTOMER FUNCTIONS
	function mst()
	{
		$builder = $this->db->table('customers');
		$builder->where('customers.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function tks()
	{
		$builder = $this->db->table('customers');
		$builder->where('type', 0);
		$builder->where('customers.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function tbm()
	{
		$builder = $this->db->table('customers');
		$builder->where('type', 1);
		$builder->where('customers.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function yms()
	{
		$builder = $this->db->table('customers');
		$builder->where('WEEK(created)', date('W'));
		$builder->where('customers.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function totaltasks()
	{
		$builder = $this->db->table('tasks');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function opentasks()
	{
		$builder = $this->db->table('tasks');
		$builder->where('status_id', 1);
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function inprogresstasks()
	{
		$builder = $this->db->table('tasks');
		$builder->where('status_id', 2);
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function waitingtasks()
	{
		$builder = $this->db->table('tasks');
		$builder->where('status_id', 3);
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function completetasks()
	{
		$builder = $this->db->table('tasks');
		$builder->where('status_id', 4);
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	// PROPOSAL FUNCTIONS
	function tpc()
	{
		$builder = $this->db->table('proposals');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	function dpc()
	{
		$builder = $this->db->table('proposals');
		$builder->where('status_id', 1);
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->countAllResults();
	}
	public function spc()
	{
		$builder = $this->builder('proposals');
		$builder->where('status_id', 2);
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function opc()
	{
		$builder = $this->builder('proposals');
		$builder->where('status_id', 3);
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function rpc()
	{
		$builder = $this->builder('proposals');
		$builder->where('status_id', 4);
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function pdc()
	{
		$builder = $this->builder('proposals');
		$builder->where('status_id', 5);
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function pac()
	{
		$builder = $this->builder('proposals');
		$builder->where('status_id', 6);
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function ttc()
	{
		$builder = $this->builder('tickets');
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function twt()
	{
		$builder = $this->builder('tickets');
		$builder->where('WEEK(date) = WEEK(CURDATE())');
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function otc()
	{
		$builder = $this->builder('tickets');
		$builder->where('status_id', 1);
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function ipc()
	{
		$builder = $this->builder('tickets');
		$builder->where('status_id', 2);
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function atc()
	{
		$builder = $this->builder('tickets');
		$builder->where('status_id', 3);
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function ctc()
	{
		$builder = $this->builder('tickets');
		$builder->where('status_id', 4);
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function clc()
	{
		$builder = $this->builder('leads');
		$builder->where('dateconverted IS NOT NULL');
		$builder->where('assigned_id', session()->get('usr_id'));
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function mlc()
	{
		$builder = $this->builder('leads');
		$builder->where('assigned_id', session()->get('usr_id'));
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function mct()
	{
		$builder = $this->builder('tickets');
		$builder->where('status_id', 4);
		$builder->where('staff_id', session()->get('usr_id'));
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function mtt()
	{
		$builder = $this->builder('tickets');
		$builder->where('staff_id', session()->get('usr_id'));
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function ues()
	{
		$monday_this_week = date('Y-m-d', strtotime('monday this week'));
		$sunday_this_week = date('Y-m-d', strtotime('sunday this week'));
		$builder = $this->builder('events');
		$builder->where('start BETWEEN ' . $this->db->escape($monday_this_week) . ' AND ' . $this->db->escape($sunday_this_week));
		$builder->where('(staff_id = ' . session()->get('usr_id') . ' OR public = 1)');
		$builder->join('staff', 'events.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function myc()
	{
		$builder = $this->builder('customers');
		$builder->where('staff_id', session()->get('usr_id'));
		$builder->where('customers.id_company', session()->get('id_company'));
		return $builder->countAllResults();
	}
	public function tbs()
	{
		$builder = $this->builder('notifications');
		$builder->join('staff', 'notifications.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->where(['markread' => 0, 'staff_id' => session()->get('usr_id')])->countAllResults();
	}
	public function bkt()
	{
		$builder = $this->builder('sales');
		$builder->selectSum('total');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('DATE(date)', 'CURDATE()', false);
		$builder->where('status_id !=', '1');
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}
	public function ogt()
	{
		$builder = $this->builder('sales');
		$builder->selectSum('total');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('DAY(date)', 'DAY(CURRENT_DATE - INTERVAL 1 DAY)', false);
		$builder->where('status_id !=', '1');
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}
	public function bht()
	{
		$builder = $this->builder('sales');
		$builder->selectSum('total');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('WEEK(date)', 'WEEK(CURRENT_DATE)', false);
		$builder->where('status_id !=', '1');
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}
	public function ohc()
	{
		$builder = $this->builder('sales');
		$builder->selectSum('total');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('WEEK(date)', 'WEEK(CURRENT_DATE - INTERVAL 1 WEEK)', false);
		$builder->where('status_id !=', '1');
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}
	public function mex()
	{
		$builder = $this->builder('expenses');
		$builder->selectSum('amount');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('MONTH(date)', 'MONTH(CURRENT_DATE)', false);
		$total_value = $builder->get()->getRow()->amount;
		return $total_value ?: 0;
	}
	public function pme()
	{
		$builder = $this->builder('expenses');
		$builder->selectSum('amount');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('MONTH(date)', 'MONTH(CURRENT_DATE - INTERVAL 1 MONTH)', false);
		$total_value = $builder->get()->getRow()->amount;
		return $total_value ?: 0;
	}
	public function akt()
	{
		$builder = $this->builder('sales');
		$builder->selectSum('total');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('MONTH(date)', 'MONTH(CURRENT_DATE)', false);
		$builder->where('status_id !=', '1');
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}
	public function oak()
	{
		$builder = $this->builder('sales');
		$builder->selectSum('total');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('MONTH(date)', 'MONTH(CURRENT_DATE - INTERVAL 1 MONTH)', false);
		$builder->where('status_id !=', '1');
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}
	public function ycr()
	{
		$builder = $this->builder('sales');
		$builder->selectSum('total');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('YEAR(date)', 'YEAR(CURRENT_DATE)', false);
		$builder->where('status_id !=', '1');
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}
	public function oyc()
	{
		$builder = $this->builder('sales');
		$builder->selectSum('total');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('YEAR(date)', 'YEAR(CURRENT_DATE - INTERVAL 1 YEAR)', false);
		$builder->where('status_id !=', '1');
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}

	public function getReports_AtvDiariaFunc()
	{
		$dt_de = request()->getPost('dt_de');
		$dt_ate = request()->getPost('dt_ate');

		// Verificar se as datas estão definidas e diferentes de "-1"
		if ($dt_de && $dt_de !== "-1") {
			$dt_de = request()->getPost('dt_de');
		} else {
			$dt_de = date('Y-m-d'); // Defina uma data padrão, se necessário
		}

		if ($dt_ate && $dt_ate !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
		} else {
			$dt_ate = date('Y-m-d'); // Defina uma data padrão, se necessário
		}

		// Gerar intervalo de datas
		$datas = [];
		for ($i2 = 0; $i2 <= $this->diasDatas($dt_de, $dt_ate); $i2++) {
			$dataAtual = date('Y-m-d', strtotime('-' . $i2 . ' days', strtotime($dt_ate)));
			$datas[] = $dataAtual;
		}

		$totalLogs = [];
		$datasets = [];
		$labels = [];

		foreach ($datas as $i => $data) {
			$builder = $this->db->table('logs');
			$builder->select('*, staff.id as id_staff');
			$builder->join('staff', 'logs.staff_id = staff.id', 'left');
			$builder->where('(key_log != "inactive" and key_log != "login" or key_log is null)');
			$builder->where('staff.id_company', session()->get('id_company'));
			$builder->where("CAST(date AS DATE) =", $data);
			$builder->orderBy('logs.id', 'asc');

			if (request()->getPost('funcionario') && request()->getPost('funcionario') !== "-1") {
				$funcionario = request()->getPost('funcionario');
				$builder->where("staff_id", $funcionario);
			}

			$logs = $builder->get()->getResultArray();

			$loginAnterior = "";
			$labels[$i] = date(get_dateFormat(), strtotime($data));

			if (count($logs) > 0) {
				foreach ($logs as $log) {
					if (!isset($totalLogs[$log['id_staff']])) {
						$totalLogs[$log['id_staff']] = [];
					}

					if (isset($totalLogs[$log['id_staff']][$i])) {
						$totalLogs[$log['id_staff']][$i] += 1;
					} else {
						$totalLogs[$log['id_staff']][$i] = 1;
					}
				}
			}
		}

		foreach ($totalLogs as $id => $valores) {
			$val = [];
			foreach ($labels as $i => $v) {
				if (isset($valores[$i])) {
					$val[$i] = $valores[$i];
				} else {
					$val[$i] = 0;
				}
			}

			// Buscar o nome do staff
			$staff = $this->db->table('staff')->where('id', $id)->get()->getRowArray();
			$datasets[] = [
				'backgroundColor' => [$this->get_cor_aleatoria()],
				'data' => $val,
				'label' => $staff['staffname'],
			];
		}

		$graphic = [
			'labels' => $labels,
			'datasets' => $datasets
		];

		return $graphic;
	}

	public function pff()
	{
		$builder = $this->db->table('invoices');
		$builder->selectSum('total');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('status_id', 1);

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("invoices.duedate >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("invoices.duedate <=", $dt_ate);
		}

		if (request()->getPost('flt_cliente') != null && request()->getPost('flt_cliente') != "-1") {
			$flt_cliente = request()->getPost('flt_cliente');
			$builder->where("invoices.customer_id", $flt_cliente);
		}

		$total_value = $builder->get()->getRow()->total;

		return !empty($total_value) ? $total_value : 0;
	}

	public function fam()
	{
		$builder = $this->db->table('invoices');
		$builder->selectSum('total');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("invoices.duedate >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("invoices.duedate <=", $dt_ate);
		}

		if (request()->getPost('flt_cliente') != null && request()->getPost('flt_cliente') != "-1") {
			$flt_cliente = request()->getPost('flt_cliente');
			$builder->where("invoices.customer_id", $flt_cliente);
		}

		$total_value = $builder->get()->getRow()->total;

		return !empty($total_value) ? $total_value : 0;
	}

	public function ofv()
	{
		$builder = $this->db->table('invoices');
		$builder->selectSum('total');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('status_id', 2);

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("invoices.duedate >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("invoices.duedate <=", $dt_ate);
		}

		if (request()->getPost('flt_cliente') != null && request()->getPost('flt_cliente') != "-1") {
			$flt_cliente = request()->getPost('flt_cliente');
			$builder->where("invoices.customer_id", $flt_cliente);
		}

		$total_value = $builder->get()->getRow()->total;

		return !empty($total_value) ? $total_value : 0;
	}

	public function oft()
	{
		$builder = $this->db->table('invoices');
		$builder->selectSum('total');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('status_id', 3);
		$builder->where('CURDATE() <= duedate');

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("invoices.duedate >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("invoices.duedate <=", $dt_ate);
		}

		if (request()->getPost('flt_cliente') != null && request()->getPost('flt_cliente') != "-1") {
			$flt_cliente = request()->getPost('flt_cliente');
			$builder->where("invoices.customer_id", $flt_cliente);
		}

		$total_value = $builder->get()->getRow()->total;

		return !empty($total_value) ? $total_value : 0;
	}

	public function vgf()
	{
		$builder = $this->builder('invoices');
		$builder->selectSum('total');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('CURDATE() >=', 'duedate');
		$builder->where("duedate NOT LIKE '0000-00-00'");
		$builder->where('status_id !=', '4');
		$builder->where('status_id !=', '2');


		$builder = $this->builder('invoices');
		$builder->selectSum('total');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('CURDATE() >= duedate'); // Corrigido: Remover as aspas em torno de 'duedate'
		$builder->where("duedate != '0000-00-00'"); // Corrigido: Usar != ao invés de NOT LIKE
		$builder->where('status_id !=', '4');
		$builder->where('status_id !=', '2');

		if ($dt_de = request()->getPost('dt_de')) {
			if ($dt_de != "-1") {
				$builder->where('invoices.duedate >=', $dt_de);
			}
		}
		if ($dt_ate = request()->getPost('dt_ate')) {
			if ($dt_ate != "-1") {
				$builder->where('invoices.duedate <=', $dt_ate);
			}
		}
		if ($flt_cliente = request()->getPost('flt_cliente')) {
			if ($flt_cliente != "-1") {
				$builder->where('invoices.customer_id', $flt_cliente);
			}
		}
		$total_value = $builder->get()->getRow()->total;
		return $total_value ?: 0;
	}
	public function tfa()
	{
		$builder = $this->builder('invoices');
		$builder->where('status_id !=', 4);
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		if ($dt_de = request()->getPost('dt_de')) {
			if ($dt_de != "-1") {
				$builder->where('invoices.duedate >=', $dt_de);
			}
		}
		if ($dt_ate = request()->getPost('dt_ate')) {
			if ($dt_ate != "-1") {
				$builder->where('invoices.duedate <=', $dt_ate);
			}
		}
		if ($flt_cliente = request()->getPost('flt_cliente')) {
			if ($flt_cliente != "-1") {
				$builder->where('invoices.customer_id', $flt_cliente);
			}
		}
		return $builder->countAllResults();
	}
	function pfs()
	{
		$pfs = $this->db->table('invoices')
			->select('COUNT(*) AS count')
			->join('staff', 'invoices.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('status_id', 1)
			->get()
			->getRow()
			->count;
		return $pfs ? $pfs : 0;
	}

	function otf()
	{
		$otf = $this->db->table('invoices')
			->select('COUNT(*) AS count')
			->join('staff', 'invoices.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('status_id', 2)
			->get()
			->getRow()
			->count;
		return $otf ? $otf : 0;
	}

	function tef()
	{
		$tef = $this->db->table('invoices')
			->select('COUNT(*) AS count')
			->join('staff', 'invoices.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('status_id', 3)
			->where('CURDATE() <= duedate')
			->get()
			->getRow()
			->count;
		return $tef ? $tef : 0;
	}

	function vdf()
	{
		$vdf = $this->db->table('invoices')
			->select('COUNT(*) AS count')
			->join('staff', 'invoices.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('CURDATE() >= duedate')
			->where('duedate !=', '0000-00-00')
			->where('status_id !=', 4)
			->where('status_id !=', 2)
			->get()
			->getRow()
			->count;
		return $vdf ? $vdf : 0;
	}

	function tcl($staff_id = '')
	{
		$builder = $this->db->table('leads');

		$builder->select('COUNT(*) AS count')
			->join('staff', 'leads.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('dateconverted IS NOT NULL ');
		if ($staff_id) {
			$builder->groupStart()
				->where('staff_id', $staff_id)
				->orWhere('assigned_id', $staff_id)
				->groupEnd();
		}
		$tcl = $builder->get()->getRow()->count;
		return $tcl ? $tcl : 0;
	}
	function tll($staff_id = '')
	{
		$builder = $this->db->table('leads')
			->select('COUNT(*) AS count')
			->join('staff', 'leads.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('lost', 1);
		if ($staff_id) {
			$builder->groupStart()
				->where('staff_id', $staff_id)
				->orWhere('assigned_id', $staff_id)
				->groupEnd();
		}
		$tll = $builder->get()->getRow()->count;
		return $tll ? $tll : 0;
	}
	function tjl($staff_id = '')
	{
		$builder = $this->db->table('leads')
			->select('COUNT(*) AS count')
			->join('staff', 'leads.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->id_company)
			->where('junk', 1);
		if ($staff_id) {
			$builder->groupStart()
				->where('staff_id', $staff_id)
				->orWhere('assigned_id', $staff_id)
				->groupEnd();
		}
		$tjl = $builder->get()->getRow()->count;
		return $tjl ? $tjl : 0;
	}
	function totalData($table)
	{
		$builder = $this->db->table($table)
			->select('COUNT(*) AS count');

		if (in_array($table, ["leads"])) {
			$builder->join('staff', 'leads.assigned_id = staff.id', 'left')
				->where('staff.id_company', session()->id_company);
		}

		if (in_array($table, ["customers", 'products'])) {
			$builder->where("$table.id_company", session()->id_company);
		}

		if (in_array($table, ["projects", "invoices", "tickets"])) {
			$builder->join('staff', "$table.staff_id = staff.id", 'left')
				->where('staff.id_company', session()->id_company);
		}

		$data = $builder->get()->getRow()->count;
		return $data ? $data : 0;
	}

	function monthly_sales_graph()
	{
		$totalsales = array_fill(1, 12, 0);
		for ($mon = 1; $mon <= 12; $mon++) {
			$builder = $this->db->table('sales')
				->select('SUM(total) AS total')
				->where('MONTH(date)', $mon);
			$result = $builder->get()->getRow()->total;
			$totalsales[$mon] = $result ? $result : 0;
		}
		return json_encode(array_values($totalsales));
	}

	function monthly_expenses()
	{
		$monthly_expenses = array_fill(1, 12, 0);
		for ($month = 1; $month <= 12; $month++) {
			$builder = $this->db->table('expenses')
				->select('SUM(amount) AS amount')
				->where('MONTH(duedate)', $month);
			$result = $builder->get()->getRow()->amount;
			$monthly_expenses[$month] = $result ? $result : 0;
		}
		return $monthly_expenses;
	}

	function monthly_sales()
	{
		$monthly_sales = array_fill(1, 12, 0); // Inicializa o array com 0 para cada mês
		for ($mon = 1; $mon <= 12; $mon++) {
			$builder = $this->db->table('sales');
			$builder->selectSum('total');
			$builder->where('MONTH(sales.date)', $mon);
			$builder->where('status_id !=', 1);
			$query = $builder->get();
			$result = $query->getRow();
			$monthly_sales[$mon] = $result->total ?  $result->total : 0; // Usa 0 se não houver resultado
		}
		return $monthly_sales;
	}

	function weekly_sales_chart()
	{
		$allsales = array();
		$builder = $this->db->table('sales');
		$builder->select('total, date');
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('DATE(sales.date) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('DATE(sales.date) <=', date('Y-m-d', strtotime('sunday this week')));
		$allsales = $builder->get()->getResultArray();
		$graphic = array(
			'labels' => weekdays(),
			'datasets' => array(
				array(
					'type' => 'bar',
					'backgroundColor' => '#C7CBD5',
					'hoverBackgroundColor' => '#ffe8a8',
					'hoverBorderColor' => '#f5f5f5',
					'data' => array_fill(0, 7, 0)
				),
				array(
					'type' => 'line',
					'backgroundColor' => '#C7CBD5',
					'hoverBackgroundColor' => '#ffe8a8',
					'hoverBorderColor' => '#f5f5f5',
					'data' => array_fill(0, 7, 0)
				),
			)
		);
		foreach ($allsales as $salesc) {
			$salesday = date('l', strtotime($salesc['date']));
			$dayIndex = array_search($salesday, weekdays_git());
			if ($dayIndex !== false) {
				$graphic['datasets'][0]['data'][$dayIndex] += $salesc['total'];
				$graphic['datasets'][1]['data'][$dayIndex] += $salesc['total'];
			}
		}
		return $graphic;
	}

	function weekly_expense_chart()
	{
		$allexpense = array();
		$builder = $this->db->table('expenses');
		$builder->select('amount, duedate');
		$builder->where('DATE(duedate) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('DATE(duedate) <=', date('Y-m-d', strtotime('sunday this week')));
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$allexpense = $builder->get()->getResultArray();
		$graphic = array(
			'labels' => weekdays(),
			'datasets' => array(
				array(
					'type' => 'bar',
					'backgroundColor' => '#C7CBD5',
					'hoverBackgroundColor' => '#ffe8a8',
					'hoverBorderColor' => '#f5f5f5',
					'data' => array_fill(0, 7, 0)
				),
				array(
					'type' => 'line',
					'backgroundColor' => '#C7CBD5',
					'hoverBackgroundColor' => '#ffe8a8',
					'hoverBorderColor' => '#f5f5f5',
					'data' => array_fill(0, 7, 0)
				),
			)
		);
		foreach ($allexpense as $expensec) {
			$expenseday = date('l', strtotime($expensec['duedate']));
			$dayIndex = array_search($expenseday, weekdays_git());
			if ($dayIndex !== false) {
				$graphic['datasets'][0]['data'][$dayIndex] += $expensec['amount'];
				$graphic['datasets'][1]['data'][$dayIndex] += $expensec['amount'];
			}
		}
		return $graphic;
	}

	function weekly_incomings()
	{
		$builder = $this->db->table('sales');
		$builder->select('total, date');
		$builder->where('DATE(date) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('DATE(date) <=', date('Y-m-d', strtotime('sunday this week')));
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		return $builder->get()->getResultArray();
	}

	function weekly_incomings_vs_outgoings()
	{
		$expenses = array_fill(0, 7, 0);
		$payments = array_fill(0, 7, 0);

		// Fetch expenses
		$builder = $this->db->table('expenses');
		$builder->select('amount, duedate');
		$builder->where('DATE(duedate) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('DATE(duedate) <=', date('Y-m-d', strtotime('sunday this week')));
		$allExpenses = $builder->get()->getResultArray();
		foreach ($allExpenses as $expensec) {
			$expenseday = date('l', strtotime($expensec['duedate']));
			$dayIndex = array_search($expenseday, weekdays_git());
			if ($dayIndex !== false) {
				$expenses[$dayIndex] += $expensec['amount'];
			}
		}

		// Fetch payments
		$builder = $this->db->table('payments');
		$builder->select('amount, date');
		$builder->join('invoices', 'invoices.id = payments.invoice_id');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('DATE(payments.date) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('DATE(payments.date) <=', date('Y-m-d', strtotime('sunday this week')));
		$allPayments = $builder->get()->getResultArray();
		foreach ($allPayments as $paymentc) {
			$paymentday = date('l', strtotime($paymentc['date']));
			$dayIndex = array_search($paymentday, weekdays_git());
			if ($dayIndex !== false) {
				$payments[$dayIndex] += $paymentc['amount'];
			}
		}

		return array(
			'expenses' => $expenses,
			'payments' => $payments
		);
	}

	function invoice_chart_by_status()
	{
		$statuslar = $this->db->table('invoicestatus')->get()->getResultArray();
		$colors = ciuis_colors();
		$graphic = array(
			'labels' => array(),
			'datasets' => array()
		);
		$_data = array();
		$_data['data'] = array();
		$_data['backgroundColor'] = array();
		$_data['hoverBackgroundColor'] = array();
		$i = 0;

		foreach ($statuslar as $status) {
			array_push($graphic['labels'], $status['name']);
			array_push($_data['backgroundColor'], $status['color']);
			array_push($_data['hoverBackgroundColor'], ciuis_set_color($status['color'], -20));
			array_push($_data['data'], $this->db->table('invoices')->where('status_id', $status['id'])->countAllResults());
			$i++;
		}

		$graphic['datasets'][] = $_data;
		$graphic['datasets'][0]['label'] = lang('invoice_status');

		return $graphic;
	}
	public function leads_to_win_by_leadsource()
	{
		$builder = $this->db->table('leadssources');
		$statuses = $builder->get()->getResultArray();
		$graphic = [
			'labels' => [],
			'datasets' => []
		];
		$_data = [
			'data' => [],
			'backgroundColor' => [],
			'hoverBackgroundColor' => []
		];
		foreach ($statuses as $status) {
			$builder = $this->db->table('leads');
			$builder->where('source', $status['id'])
				->where('dateconverted IS NOT NULL');
			$count = $builder->countAllResults();
			$graphic['labels'][] = $status['name'];
			$_data['backgroundColor'][] = '#777777';
			$_data['hoverBackgroundColor'][] = ciuis_set_color('#777777', -20);
			$_data['data'][] = $count;
		}
		$graphic['datasets'][] = $_data;
		$graphic['datasets'][0]['label'] = lang2('lead_source');
		return $graphic;
	}
	public function customer_annual_sales_chart($id, $year)
	{
		$pre_totalsales = [];
		for ($MO = 1; $MO <= 12; $MO++) {
			$builder = $this->db->table('invoices');
			$builder->select('invoices.sub_total as total')
				->join('staff', 'invoices.staff_id = staff.id', 'left')
				->where('YEAR(invoices.duedate)', $year)
				->where('MONTH(invoices.duedate)', $MO)
				->where('invoices.customer_id', $id)
				->where('staff.id_company', session()->id_company);
			$balances = $builder->get()->getResultArray();
			$pre_totalsales[$MO] = array_sum(array_column($balances, 'total'));
		}
		$all_months = months();
		$inline_graph = [];
		foreach (array_combine($all_months, $pre_totalsales) as $month => $total) {
			$inline_graph[] = [
				'month' => mb_substr($month, 0, 3, 'UTF-8'),
				'total' => $total,
			];
		}
		$graph = [
			'labels' => months(),
			'datasets' => [
				[
					'label' => $year,
					'data' => $pre_totalsales,
					'backgroundColor' => '#4c56d1',
					'borderColor' => 'transparent',
					'pointBackgroundColor' => '#FFFFFF',
					'lineTension' => '0.40',
				]
			],
			'inline_graph' => $inline_graph,
		];
		return $graph;
	}
	public function staff_sales_graph($id)
	{
		$totalsales = [];
		$pre_totalsales = [];
		for ($MO = 1; $MO <= 12; $MO++) {
			$builder = $this->db->table('sales');
			$builder->select('total')
				->where('MONTH(date)', $MO)
				->where('staff_id', $id)
				->join('staff', 'sales.staff_id = staff.id', 'left')
				->where('staff.id_company', session()->id_company);
			$balances = $builder->get()->getResultArray();
			$totalsales[$MO] = array_sum(array_column($balances, 'total'));
			$builder = $this->db->table('sales');
			$builder->select('sales.total')
				->where('YEAR(duedate)', 'YEAR(CURRENT_DATE - INTERVAL 1 YEAR)')
				->where('MONTH(duedate)', $MO)
				->where('sales.staff_id', $id)
				->join('staff', 'sales.staff_id = staff.id', 'left')
				->join('invoices', 'invoices.id = sales.invoice_id', 'left')
				->where('staff.id_company', session()->id_company);
			$balances = $builder->get()->getResultArray();
			$pre_totalsales[$MO] = array_sum(array_column($balances, 'total'));
		}
		$all_months = months();
		$inline_graph = [];
		foreach (array_combine($all_months, $totalsales) as $month => $total) {
			$inline_graph[] = [
				'month' => mb_substr($month, 0, 3, 'UTF-8'),
				'total' => $total,
			];
		}
		$graph = [
			'labels' => months(),
			'datasets' => [
				[
					'label' => lang2('this_year'),
					'data' => $totalsales,
					'backgroundColor' => '#f6c1638a',
					'borderColor' => 'transparent',
					'pointBackgroundColor' => '#FFFFFF',
					'lineTension' => '0.40',
				],
				[
					'label' => lang2('lastyear'),
					'data' => $pre_totalsales,
					'backgroundColor' => '#ddd',
					'borderColor' => 'transparent',
					'pointBackgroundColor' => '#FFFFFF',
					'lineTension' => '0.40',
				]
			],
			'inline_graph' => $inline_graph,
		];
		return $graph;
	}
	public function staff_sales_graph2($id, $dt_de = null, $dt_ate = null)
	{
		$totalLogs = [];
		$datas = [];
		if ($dt_ate == null && $dt_de == null) {
			for ($i2 = 0; $i2 <= 10; $i2++) {
				$datas[] = date('Y-m-d', strtotime('-' . $i2 . ' days', strtotime(date('Y-m-d'))));
			}
		} else {
			for ($i2 = 0; $i2 <= $this->diasDatas($dt_de, $dt_ate); $i2++) {
				$datas[] = date('Y-m-d', strtotime('-' . $i2 . ' days', strtotime($dt_ate)));
			}
		}
		foreach ($datas as $i => $data) {
			$builder = $this->db->table('logs');
			$builder->where('staff_id', $id)
				->where("CAST(date AS DATE) = '" . $data . "'");
			$logs = $builder->get()->getResultArray();
			$loginAnterior = "";
			$labels[$i] = date(get_dateFormat(), strtotime($data));
			if (count($logs) > 0) {
				foreach ($logs as $log) {
					if ($log['key_log'] == "login" || $loginAnterior == "") {
						$loginAnterior = $log['date'];
					} else if (
						($log['key_log'] == "inactive" || $log['key_log'] == "operating" || $log['key_log'] == "login" || $log['key_log'] == "") && $loginAnterior != ""
					) {
						$date  = new DateTime($log['date']);
						$date2 = new DateTime($loginAnterior);
						$v = $date->diff($date2);
						$min = str_pad($v->i, 2, '0', STR_PAD_LEFT);
						$hora = str_pad($v->h, 2, '0', STR_PAD_LEFT);
						$seg = str_pad($v->s, 2, '0', STR_PAD_LEFT);
						$totalSessao = $hora . ':' . $min . ':' . $seg;
						if (isset($totalLogs[$i])) {
							$dt = new DateTime($totalLogs[$i]);
							$parts = explode(':', $totalSessao);
							$interval = new DateInterval('PT' . (int)$parts[0] . 'H' . $parts[1] . 'M' . $parts[2] . 'S');
							$dt->add($interval);
							$totalI = $dt->format('H:i:s');
						}
						$totalLogs[$i] = isset($totalLogs[$i]) ? $totalI : $totalSessao;
						if ($log['key_log'] == "operating" || $log['key_log'] == "") {
							$loginAnterior = $log['date'];
						} else {
							$loginAnterior = "";
						}
					}
				}
				if (!isset($totalLogs[$i])) {
					$totalLogs[$i] = 0;
				}
			} else {
				$totalLogs[$i] = 0;
			}
		}
		$inline_graph = [];
		foreach ($totalLogs as $index => $total) {
			$inline_graph[] = [
				'label' => $labels[$index],
				'total' => $total,
			];
			$totalLogs[$index] = intval(str_replace(":", "", $total));
		}
		$graph = [
			'labels' => $labels,
			'datasets' => [
				[
					'label' => 'Horas trabalhadas',
					'data' => $totalLogs,
					'backgroundColor' => '#2196f3',
					'borderColor' => 'transparent',
					'pointBackgroundColor' => '#FFFFFF',
					'lineTension' => '0.40',
				]
			],
			'inline_graph' => $inline_graph,
		];
		return $graph;
	}
	public function diasDatas($data_inicial, $data_final)
	{
		$diferenca = strtotime($data_final) - strtotime($data_inicial);
		return floor($diferenca / (60 * 60 * 24));
	}
	public function leads_by_leadsource()
	{
		$builder = $this->db->table('leadssources');
		$statuses = $builder->get()->getResultArray();
		$graphic = [
			'labels' => [],
			'datasets' => []
		];
		$_data = [
			'data' => [],
			'backgroundColor' => [],
			'hoverBackgroundColor' => []
		];
		foreach ($statuses as $status) {
			$builder = $this->db->table('leads');
			$builder->where('source', $status['id']);
			$count = $builder->countAllResults();
			$graphic['labels'][] = $status['name'];
			$_data['backgroundColor'][] = '#777777';
			$_data['hoverBackgroundColor'][] = ciuis_set_color('#777777', -20);
			$_data['data'][] = $count;
		}
		$graphic['datasets'][] = $_data;
		$graphic['datasets'][0]['label'] = lang2('lead_source');
		return $graphic;
	}
	public function leads_by_leadsource_leadpage()
	{
		$builder = $this->db->table('leadssources');
		$statuses = $builder->get()->getResultArray();
		$graphic = [
			'labels' => [],
			'datasets' => []
		];
		$_data = [
			'data' => [],
			'backgroundColor' => [],
			'hoverBackgroundColor' => []
		];
		foreach ($statuses as $status) {
			$builder = $this->db->table('leads');
			$builder->where('source', $status['id']);
			$count = $builder->countAllResults();
			$graphic['labels'][] = $status['name'];
			$_data['backgroundColor'][] = '#26c281';
			$_data['hoverBackgroundColor'][] = ciuis_set_color('#39393b', -20);
			$_data['data'][] = $count;
		}
		$graphic['datasets'][] = $_data;
		$graphic['datasets'][0]['label'] = lang2('lead_source');
		return $graphic;
	}
	public function top_selling_staff_chart()
	{
		$this->model('Staff_Model');
		$staff = $this->Staff_Model->get_all_staff();
		$colors = 'rgb(235, 235, 235)';
		$graphic = [
			'labels' => [],
			'datasets' => []
		];
		$_data = [
			'data' => [],
			'backgroundColor' => [],
			'hoverBackgroundColor' => []
		];
		foreach ($staff as $staffmember) {
			$builder = $this->db->table('sales');
			$builder->where('staff_id', $staffmember['id']);
			$count = $builder->countAllResults();
			$graphic['labels'][] = $staffmember['staffname'];
			$_data['backgroundColor'][] = $colors;
			$_data['hoverBackgroundColor'][] = ciuis_set_color($colors, -90);
			$_data['data'][] = $count;
		}
		$graphic['datasets'][] = $_data;
		$graphic['datasets'][0]['label'] = lang2('staff');
		return $graphic;
	}
	public function weekly_sales_chart_report()
	{
		$builder = $this->db->table('sales');
		$builder->select('sales.total, sales.date');
		$builder->where('CAST(sales.date AS DATE) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('CAST(sales.date AS DATE) <=', date('Y-m-d', strtotime('sunday this week')));
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$allsales = $builder->get()->getResultArray();

		$graphic = [
			'labels' => weekdays(),
			'datasets' => [
				[
					'type' => 'line',
					'backgroundColor' => '#fff',
					'borderWidth' => '1',
					'hoverBackgroundColor' => '#ffe8a8',
					'hoverBorderColor' => '#f5f5f5',
					'data' => array_fill(0, 7, 0)
				]
			]
		];

		foreach ($allsales as $salesc) {
			$salesday = date('l', strtotime($salesc['date']));
			$x = 0;
			foreach (weekdays_git() as $dayc) {
				if ($salesday == $dayc) {
					$graphic['datasets'][0]['data'][$x] += $salesc['total'];
				}
				$x++;
			}
		}

		return $graphic;
	}

	public function weekly_dashboard_chart()
	{
		$builder = $this->db->table('expenses');
		$builder->select('expenses.amount, expenses.duedate AS date');
		$builder->where('CAST(expenses.duedate AS DATE) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('CAST(expenses.duedate AS DATE) <=', date('Y-m-d', strtotime('sunday this week')));
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$allexpenses = $builder->get()->getResultArray();

		$data = $this->gets_sales_data();

		$graphic = [
			'labels' => weekdays(),
			'datasets' => [
				[
					'type' => 'bar',
					'label' => lang2('expenses'),
					'backgroundColor' => '#f52f24',
					'borderWidth' => '1',
					'hoverBackgroundColor' => '#C7CBD5',
					'hoverBorderColor' => '#f5f5f5',
					'data' => array_fill(0, 7, 0)
				],
				[
					'type' => 'bar',
					'label' => lang2('sales'),
					'backgroundColor' => '#26c281',
					'borderWidth' => '1',
					'hoverBackgroundColor' => '#C7CBD5',
					'hoverBorderColor' => '#f5f5f5',
					'data' => $data
				],
				[
					'type' => 'line',
					'label' => 'Line',
					'backgroundColor' => '#ffffff',
					'hoverBackgroundColor' => '#ffffff',
					'hoverBorderColor' => '#ffffff',
					'data' => array_fill(0, 7, 0)
				]
			]
		];

		foreach ($allexpenses as $expensesc) {
			$expensesday = date('l', strtotime($expensesc['date']));
			$x = 0;
			foreach (weekdays_git() as $dayc) {
				if ($expensesday == $dayc) {
					$graphic['datasets'][0]['data'][$x] += $expensesc['amount'];
				}
				$x++;
			}
		}

		return $graphic;
	}

	public function gets_sales_data()
	{
		$data = array_fill(0, 7, 0);

		$builder = $this->db->table('sales');
		$builder->select('sales.total, sales.date');
		$builder->where('CAST(sales.date AS DATE) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('CAST(sales.date AS DATE) <=', date('Y-m-d', strtotime('sunday this week')));
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$allsales = $builder->get()->getResultArray();

		foreach ($allsales as $allsale) {
			$salesday = date('l', strtotime($allsale['date']));
			$x = 0;
			foreach (weekdays_git() as $dayc) {
				if ($salesday == $dayc) {
					$data[$x] += $allsale['total'];
				}
				$x++;
			}
		}

		return $data;
	}

	public function weekly_sales()
	{
		$builder = $this->db->table('sales');
		$builder->select('sales.total, sales.date');
		$builder->where('CAST(sales.date AS DATE) >=', date('Y-m-d', strtotime('monday this week')));
		$builder->where('CAST(sales.date AS DATE) <=', date('Y-m-d', strtotime('sunday this week')));
		$builder->join('staff', 'sales.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$allsales = $builder->get()->getResultArray();

		$graphic = ['data' => array_fill(0, 7, 0)];

		foreach ($allsales as $salesc) {
			$salesday = date('l', strtotime($salesc['date']));
			$x = 0;
			foreach (weekdays_git() as $dayc) {
				if ($salesday == $dayc) {
					$graphic['data'][$x] += $salesc['total'];
				}
				$x++;
			}
		}

		return $graphic;
	}

	public function customer_monthly_increase_chart($month)
	{
		$grp = $this->db->query('SELECT created FROM customers
        LEFT JOIN staff ON customers.staff_id = staff.id
        WHERE MONTH(created) = ' . $month . '
        AND staff.id_company = ' . session()->id_company . '
    ')->get()->getResultArray();
		$month_d = [];
		$data = [];
		for ($d = 1; $d <= 31; $d++) {
			$timec = mktime(12, 0, 0, $month, $d, date('Y'));
			if (date('m', $timec) == $month) {
				$month_d[] = _date(date('Y-m-d', $timec));
				$data[] = 0;
			}
		}
		$graphic = [
			'labels' => $month_d,
			'datasets' => [
				[
					'label' => lang2('customers'),
					'backgroundColor' => '#5ba768',
					'borderColor' => '#b9b9b9',
					'borderWidth' => 1,
					'tension' => false,
					'data' => $data
				]
			]
		];
		foreach ($grp as $customer) {
			$i = 0;
			foreach ($graphic['labels'] as $date) {
				if (_date($customer['created']) == $date) {
					$graphic['datasets'][0]['data'][$i]++;
				}
				$i++;
			}
		}
		return $graphic;
	}
	public function lead_graph($month)
	{
		$grp = $this->db->query('SELECT created FROM leads
        INNER JOIN staff ON leads.assigned_id = staff.id
        WHERE MONTH(created) = ' . $month . '
        AND staff.id_company = ' . session()->id_company . '
    ')->get()->getResultArray();
		$month_d = [];
		$data = [];
		for ($d = 1; $d <= 31; $d++) {
			$timec = mktime(12, 0, 0, $month, $d, date('Y'));
			if (date('m', $timec) == $month) {
				$month_d[] = _date(date('Y-m-d', $timec));
				$data[] = 0;
			}
		}
		$graphic = [
			'labels' => $month_d,
			'datasets' => [
				[
					'label' => lang2('leads'),
					'backgroundColor' => '#e26862',
					'borderColor' => '#b9b9b9',
					'borderWidth' => 1,
					'tension' => false,
					'data' => $data
				]
			]
		];
		foreach ($grp as $leads) {
			$i = 0;
			foreach ($graphic['labels'] as $date) {
				if (_date($leads['created']) == $date) {
					$graphic['datasets'][0]['data'][$i]++;
				}
				$i++;
			}
		}
		return $graphic;
	}
	public function incomings_vs_outgoings($currentyear = '')
	{
		$allmonths = [];
		$outgoings = [];
		$incomings = [];

		if (!is_numeric($currentyear)) {
			$currentyear = date('Y');
		}

		for ($m = 1; $m <= 12; $m++) {
			$allmonths[] = date('F', mktime(0, 0, 0, $m, 1));

			// Calculate expenses
			$builder = $this->db->table('expenses');
			$builder->select('amount');
			$builder->where('MONTH(date)', $m);
			$builder->where('YEAR(date)', $currentyear);
			$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
			$builder->where('staff.id_company', session()->id_company);
			$expenses = $builder->get()->getResultArray();
			$outgoings[$m - 1] = array_sum(array_column($expenses, 'amount'));

			// Calculate payments
			$builder = $this->db->table('payments');
			$builder->select('amount');
			$builder->join('invoices', 'invoices.id = payments.invoice_id');
			$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
			$builder->where('staff.id_company', session()->id_company);
			$builder->where('MONTH(payments.date)', $m);
			$builder->where('YEAR(payments.date)', $currentyear);
			$payments = $builder->get()->getResultArray();
			$incomings[$m - 1] = array_sum(array_column($payments, 'amount'));
		}

		$graph = [
			'labels' => $allmonths,
			'datasets' => [
				[
					'label' => lang2('payments'),
					'backgroundColor' => '#26c281',
					'borderColor' => '#26c281',
					'borderWidth' => 2,
					'tension' => false,
					'data' => $incomings
				],
				[
					'label' => lang2('expenses'),
					'backgroundColor' => '#f52f24',
					'borderColor' => '#f52f24',
					'borderWidth' => 2,
					'tension' => false,
					'data' => $outgoings
				]
			]
		];

		return $graph;
	}

	public function expenses_payments_graph($currentyear)
	{
		$allmonths = [];
		$outgoings = [];
		$incomings = [];

		if (!is_numeric($currentyear)) {
			$currentyear = date('Y');
		}

		for ($m = 1; $m <= 12; $m++) {
			$allmonths[] = date('F', mktime(0, 0, 0, $m, 1));

			// Expenses
			$builder = $this->db->table('expenses');
			$builder->select('amount');
			$builder->where('MONTH(date)', $m);
			$builder->where('YEAR(date)', $currentyear);
			$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
			$builder->where('staff.id_company', session()->id_company);
			$expenses = $builder->get()->getResultArray();
			$outgoings[$m - 1] = array_sum(array_column($expenses, 'amount'));

			// Payments
			$builder = $this->db->table('payments');
			$builder->select('amount');
			$builder->join('invoices', 'invoices.id = payments.invoice_id');
			$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
			$builder->where('staff.id_company', session()->id_company);
			$builder->where('MONTH(payments.date)', $m);
			$builder->where('YEAR(payments.date)', $currentyear);
			$payments = $builder->get()->getResultArray();
			$incomings[$m - 1] = array_sum(array_column($payments, 'amount'));
		}

		return [
			'labels' => $allmonths,
			'datasets' => [
				[
					'label' => lang2('payments'),
					'backgroundColor' => '#26c281',
					'borderColor' => '#26c281',
					'borderWidth' => 2,
					'tension' => false,
					'data' => $incomings
				],
				[
					'label' => lang2('expenses'),
					'backgroundColor' => '#f52f24',
					'borderColor' => '#f52f24',
					'borderWidth' => 2,
					'tension' => false,
					'data' => $outgoings
				]
			]
		];
	}

	public function expenses_by_categories()
	{
		$this->load->model('Expenses_Model');
		$expensecategories = $this->Expenses_Model->get_all_expensecat();
		$colors = 'rgba(255, 188, 0, 0.83)';
		$graphic = [
			'labels' => [],
			'datasets' => [
				[
					'label' => lang2('category'),
					'data' => [],
					'backgroundColor' => [],
					'hoverBackgroundColor' => []
				]
			]
		];

		foreach ($expensecategories as $expensecategory) {
			$builder = $this->db->table('expenses');
			$builder->where('category_id', $expensecategory['id']);
			$graphic['labels'][] = $expensecategory['name'];
			$graphic['datasets'][0]['backgroundColor'][] = $colors;
			$graphic['datasets'][0]['data'][] = $builder->countAllResults();
		}

		return $graphic;
	}

	public function get_timesheet()
	{
		$admin = $this->isAdmin();
		$builder = $this->db->table('tasktimer');
		$builder->select('tasktimer.id, tasktimer.start, tasktimer.end, tasktimer.task_id, tasks.name, tasktimer.note, staff.staffname as staff, staff.id as staff_id, staff.staffavatar as avatar, staff.email as staff_email');
		$builder->join('staff', 'tasktimer.staff_id = staff.id', 'left');
		$builder->join('tasks', 'tasktimer.task_id = tasks.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('tasktimer.id', 'desc');

		if (!$admin) {
			$builder->where('tasktimer.staff_id', session()->usr_id);
		}

		$data = $builder->get()->getResultArray();
		return !empty($data) ? $data : false;
	}

	public function weekly_timesheet()
	{
		$builder = $this->db->table('timer');
		$builder->select('timer.id, timer.start_time, timer.end_time, timer.relation_id, timer.relation, tasks.name, timer.note, staff.staffname as staff, staff.id as staff_id, staff.staffavatar as avatar');
		$builder->join('staff', 'timer.staff_id = staff.id', 'left');
		$builder->join('tasks', 'timer.relation_id = tasks.id', 'left');
		$builder->orderBy('timer.id', 'desc');
		$builder->where('staff.id_company', session()->id_company);

		$startOfWeek = date('Y-m-d', strtotime('monday this week'));
		$endOfWeek = date('Y-m-d', strtotime('sunday this week'));
		$builder->where('DATE(timer.start_time) >=', $startOfWeek);
		$builder->where('DATE(timer.start_time) <=', $endOfWeek);

		$allexpenses = $builder->get()->getResultArray();
		return $allexpenses;
	}

	public function isAdmin()
	{
		$id = session()->get('usr_id');
		$builder = $this->db->table('staff');
		$builder->select('*');
		$builder->where(['admin' => 1, 'id' => $id]);
		$builder->where('staff.id_company', session()->get('id_company'));

		$rows = $builder->countAllResults();
		return $rows > 0;
	}

	public function invoices_thisweek()
	{
		$builder = $this->db->table('invoices');
		$builder->where('YEARWEEK(created, 1) = YEARWEEK(CURDATE(), 1)');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$invoices_thisweek = $builder->countAllResults();
		return $invoices_thisweek;
	}

	public function expenses_thisweek()
	{
		$builder = $this->db->table('expenses');
		$builder->where('YEARWEEK(created, 1) = YEARWEEK(CURDATE(), 1)');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$expenses_thisweek = $builder->countAllResults();
		return $expenses_thisweek;
	}

	public function deposits_amount_by_category($id, $staff_id = '')
	{
		$builder = $this->db->table('deposits');
		$builder->selectSum('amount');
		$builder->join('staff', 'deposits.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('category_id', $id);

		if ($staff_id) {
			$builder->where('staff_id', $staff_id);
		}

		$total_value = $builder->get()->getRow()->amount;
		return $total_value ? $total_value : 0;
	}

	public function deposits_percent_by_category($id)
	{
		$builder = $this->db->table('deposits');
		$builder->where('category_id', $id);
		$builder->join('staff', 'deposits.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$excat = $builder->countAllResults();

		$builder->resetQuery(); // Reset the query to count the total deposits

		$builder->table('deposits');
		$builder->join('staff', 'deposits.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$totaldeposits = $builder->countAllResults();

		return $totaldeposits > 0 ? number_format(($excat * 100) / $totaldeposits) : 0;
	}

	public function get_all_invoices()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');

		$builder = $this->db->table('invoices');
		$builder->select('invoices.*, staff.staffname as staffmembername, customers.company as customercompany, customers.namesurname as individual, customers.address as customeraddress, invoices.status_id as status_id, invoices.created as created');
		$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
		$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('invoices.id', 'desc');

		switch ($period) {
			case '1':
				$year = date('Y');
				$builder->where('YEAR(invoices.created)', $year);
				break;
			case '2':
				$month = date('m');
				$builder->where('MONTH(invoices.created)', $month);
				break;
			case '3':
				$builder->where('WEEK(invoices.created) = WEEK(CURRENT_DATE)');
				break;
			case '4':
				$builder->where('YEAR(invoices.created)', date('Y') - 1);
				break;
			case '5':
				$builder->where('MONTH(invoices.created)', date('m', strtotime('-1 month')));
				break;
			case '6':
				$builder->where('WEEK(invoices.created)', date('W', strtotime('-1 week')));
				break;
			case '7':
				$builder->where('invoices.created >=', $from);
				$builder->where('invoices.created <=', $to);
				break;
		}

		$result = $builder->get()->getResultArray();

		foreach ($result as $index => $invoice) {
			$totalx = $invoice['total'];

			$payBuilder = $this->db->table('payments');
			$payBuilder->selectSum('amount');
			$payBuilder->where('invoice_id', $invoice['id']);
			$paytotal = $payBuilder->get()->getRow()->amount;

			$balance = $totalx - $paytotal;
			$invoicestatus = $color = '';

			if ($balance > 0) {
				$invoicestatus = lang2('paidinv');
				$color = 'success';
			} elseif ($paytotal < $invoice['total'] && $paytotal > 0 && $invoice['status_id'] == 3) {
				$invoicestatus = lang2('partial');
				$color = 'warning';
			} elseif ($paytotal < $invoice['total'] && $paytotal > 0) {
				$invoicestatus = lang2('partial');
				$color = 'warning';
			} elseif ($invoice['status_id'] == 3) {
				$invoicestatus = lang2('unpaid');
				$color = 'danger';
			} elseif ($invoice['status_id'] == 1) {
				$invoicestatus = lang2('draft');
				$color = 'muted';
			} elseif ($invoice['status_id'] == 4) {
				$invoicestatus = lang2('cancelled');
				$color = 'danger';
			}

			$result[$index]['statusname'] = $invoicestatus;
			$result[$index]['customer'] = $invoice['type'] == 1 ? $invoice['individual'] : $invoice['customercompany'];

			$itemsBuilder = $this->db->table('items');
			$itemsBuilder->where(['relation_type' => 'invoice', 'relation' => $invoice['id']]);
			$items = $itemsBuilder->get()->getResultArray();

			$result[$index]['items'] = $items;
		}

		return $result;
	}

	function get_all_customers()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('customers');
		$builder->select('*, customers.id as id, customergroups.id as groupid');
		$builder->join('customergroups', 'customers.groupid = customergroups.id', 'left');
		$builder->join('staff', 'customers.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('customers.id', 'desc');
		switch ($period) {
			case '0':
				// Get All Customers
				break;
			case '1':
				// Get Current Year Customers
				$year = date('Y');
				$builder->where('YEAR(customers.created)', $year);
				break;
			case '2':
				// Get Current Month Customers
				$month = date('m');
				$builder->where('MONTH(customers.created)', $month);
				break;
			case '3':
				// Get Current Week Customers
				$builder->where('WEEK(customers.created)', 'WEEK(CURRENT_DATE)');
				break;
			case '4':
				// Get Last Year Customers
				$builder->where('YEAR(customers.created)', 'YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
				break;
			case '5':
				// Get Last Month Customers
				$builder->where('MONTH(customers.created)', 'MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
				break;
			case '6':
				// Get Last Week Customers
				$builder->where('WEEK(customers.created)', 'WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
				break;
			case '7':
				// Get Custom Customers
				$builder->where('customers.created >=', $from);
				$builder->where('customers.created <=', $to);
				break;
		}
		return $builder->get()->getResultArray();
	}
	function get_all_expenses()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('expenses');
		$builder->select('*, customers.company as company, namesurname, customers.id as customerid, customers.type as type, expensecat.name as category, staff.staffname as staff, expenses.id as id, staff.id as staffid, accounts.name as payment_account');
		$builder->join('customers', 'expenses.customer_id = customers.id', 'left');
		$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
		$builder->join('accounts', 'expenses.account_id = accounts.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('expenses.id', 'desc');
		switch ($period) {
			case '0':
				// Get All Expenses
				break;
			case '1':
				// Get Current Year Expenses
				$year = date('Y');
				$builder->where('YEAR(expenses.created)', $year);
				break;
			case '2':
				// Get Current Month Expenses
				$month = date('m');
				$builder->where('MONTH(expenses.created)', $month);
				break;
			case '3':
				// Get Current Week Expenses
				$builder->where('WEEK(expenses.created)', 'WEEK(CURRENT_DATE)');
				break;
			case '4':
				// Get Last Year Expenses
				$builder->where('YEAR(expenses.created)', 'YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
				break;
			case '5':
				// Get Last Month Expenses
				$builder->where('MONTH(expenses.created)', 'MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
				break;
			case '6':
				// Get Last Week Expenses
				$builder->where('WEEK(expenses.created)', 'WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
				break;
			case '7':
				// Get Custom Expenses
				$builder->where('expenses.created >=', $from);
				$builder->where('expenses.created <=', $to);
				break;
		}
		return $builder->get()->getResultArray();
	}
	function get_all_proposals()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('proposals');
		$builder->select('*, staff.staffname as staffmembername, proposals.id as id, staff.id as staffid, staff_number');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('proposals.id', 'desc');
		switch ($period) {
			case '0':
				// Get All Proposals
				break;
			case '1':
				// Get Current Year Proposals
				$year = date('Y');
				$builder->where('YEAR(proposals.created)', $year);
				break;
			case '2':
				// Get Current Month Proposals
				$month = date('m');
				$builder->where('MONTH(proposals.created)', $month);
				break;
			case '3':
				// Get Current Week Proposals
				$builder->where('WEEK(proposals.created)', 'WEEK(CURRENT_DATE)');
				break;
			case '4':
				// Get Last Year Proposals
				$builder->where('YEAR(proposals.created)', 'YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
				break;
			case '5':
				// Get Last Month Proposals
				$builder->where('MONTH(proposals.created)', 'MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
				break;
			case '6':
				// Get Last Week Proposals
				$builder->where('WEEK(proposals.created)', 'WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
				break;
			case '7':
				// Get Custom Proposals
				$builder->where('proposals.created >=', $from);
				$builder->where('proposals.created <=', $to);
				break;
		}
		return $builder->get()->getResultArray();
	}
	function get_all_deposits()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('deposits');
		$builder->select('*, customers.company as company, namesurname, customers.id as customerid, depositcat.name as category, deposits.id as id, deposits.created as created, staffname, staff.id as staffid, accounts.name as payment_account');
		$builder->join('customers', 'deposits.customer_id = customers.id', 'left');
		$builder->join('depositcat', 'deposits.category_id = depositcat.id', 'left');
		$builder->join('staff', 'deposits.staff_id = staff.id', 'left');
		$builder->join('accounts', 'deposits.account_id = accounts.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('deposits.id', 'desc');
		if ($period == '0') {
			// Get All Deposits
			return $builder->get()->getResultArray();
		} elseif ($period == '1') {
			// Get Current Year Deposits
			$year = date('Y');
			$builder->where('YEAR(deposits.created)', $year);
		} elseif ($period == '2') {
			// Get Current Month Deposits
			$month = date('m');
			$builder->where('MONTH(deposits.created)', $month);
		} elseif ($period == '3') {
			// Get Current Week Deposits
			$builder->where('WEEK(deposits.created) = WEEK(CURRENT_DATE)');
		} elseif ($period == '4') {
			// Get Last Year Deposits
			$builder->where('YEAR(deposits.created) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
		} elseif ($period == '5') {
			// Get Last Month Deposits
			$builder->where('MONTH(deposits.created) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
		} elseif ($period == '6') {
			// Get Last Week Deposits
			$builder->where('WEEK(deposits.created) = WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
		} elseif ($period == '7') {
			// Get Custom Deposits
			$builder->where('deposits.created >=', $from);
			$builder->where('deposits.created <=', $to);
		}
		return $builder->get()->getResultArray();
	}

	function get_all_orders()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('orders');
		$builder->select('*, staff.staffname as staffmembername, staff.id as staffid, orders.id as id, customers.id as customerid, customer_number, customers.company, namesurname, leads.id as leadid, lead_number, leads.name as leadname');
		$builder->join('staff', 'orders.assigned = staff.id', 'left');
		$builder->join('customers', 'orders.relation = customers.id', 'left');
		$builder->join('leads', 'orders.relation = leads.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('orders.id', 'desc');
		if ($period == '0') {
			// Get All Orders
			return $builder->get()->getResultArray();
		} elseif ($period == '1') {
			// Get Current Year Orders
			$year = date('Y');
			$builder->where('YEAR(orders.created)', $year);
		} elseif ($period == '2') {
			// Get Current Month Orders
			$month = date('m');
			$builder->where('MONTH(orders.created)', $month);
		} elseif ($period == '3') {
			// Get Current Week Orders
			$builder->where('WEEK(orders.created) = WEEK(CURRENT_DATE)');
		} elseif ($period == '4') {
			// Get Last Year Orders
			$builder->where('YEAR(orders.created) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
		} elseif ($period == '5') {
			// Get Last Month Orders
			$builder->where('MONTH(orders.created) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
		} elseif ($period == '6') {
			// Get Last Week Orders
			$builder->where('WEEK(orders.created) = WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
		} elseif ($period == '7') {
			// Get Custom Orders
			$builder->where('orders.created >=', $from);
			$builder->where('orders.created <=', $to);
		}
		return $builder->get()->getResultArray();
	}

	function get_all_vendors()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('vendors');
		$builder->select('*, vendors.id as id');
		$builder->join('vendors_groups', 'vendors.groupid = vendors_groups.id', 'left');
		$builder->join('staff', 'vendors.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('vendors.id', 'desc');
		if ($period == '0') {
			// Get All Vendors
			return $builder->get()->getResultArray();
		} elseif ($period == '1') {
			// Get Current Year Vendors
			$year = date('Y');
			$builder->where('YEAR(vendors.created)', $year);
		} elseif ($period == '2') {
			// Get Current Month Vendors
			$month = date('m');
			$builder->where('MONTH(vendors.created)', $month);
		} elseif ($period == '3') {
			// Get Current Week Vendors
			$builder->where('WEEK(vendors.created) = WEEK(CURRENT_DATE)');
		} elseif ($period == '4') {
			// Get Last Year Vendors
			$builder->where('YEAR(vendors.created) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
		} elseif ($period == '5') {
			// Get Last Month Vendors
			$builder->where('MONTH(vendors.created) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
		} elseif ($period == '6') {
			// Get Last Week Vendors
			$builder->where('WEEK(vendors.created) = WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
		} elseif ($period == '7') {
			// Get Custom Vendors
			$builder->where('vendors.created >=', $from);
			$builder->where('vendors.created <=', $to);
		}
		return $builder->get()->getResultArray();
	}

	function get_all_purchases()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('purchases');
		$builder->select('*, staff.staffname as staffmembername, vendors.company as vendorcompany, vendors.id as vendorid, vendor_number, purchases.status_id as status_id, purchases.created as created, purchases.id as id');
		$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
		$builder->orderBy('purchases.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder = $this->apply_period_filter($builder, $period, $from, $to, 'created', 'purchases');
		return $builder->get()->getResultArray();
	}

	function get_all_contacts()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('contacts');
		$builder->select('contacts.id as contactid, name, surname, contacts.email as email, contacts.phone as phone, mobile, company, namesurname, customers.id as customerid, customer_number, created');
		$builder->join('customers', 'contacts.customer_id = customers.id', 'left');
		$builder->join('staff', 'contacts.customer_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder = $this->apply_period_filter($builder, $period, $from, $to, 'created', 'contacts');
		return $builder->get()->getResultArray();
	}

	function get_all_tickets()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('tickets');
		$builder->select('*, customers.type as type, customers.email as customeremail, customers.company as company, customers.namesurname as namesurname, departments.name as department, staff.staffname as staffmembername, staff.email as staffemail, contacts.name as contactname, contacts.surname as contactsurname, tickets.staff_id as stid, tickets.status_id as status_id, tickets.id as id');
		$builder->join('contacts', 'tickets.contact_id = contacts.id', 'left');
		$builder->join('customers', 'contacts.customer_id = customers.id', 'left');
		$builder->join('departments', 'tickets.department_id = departments.id', 'left');
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder = $this->apply_period_filter($builder, $period, $from, $to, 'date', 'tickets');
		return $builder->get()->getResultArray();
	}

	function apply_period_filter($builder, $period, $from, $to, $column = 'created', $table = 'purchases')
	{
		switch ($period) {
			case '1': // Current Year
				$builder->where("YEAR($table.$column)", date('Y'));
				break;
			case '2': // Current Month
				$builder->where("MONTH($table.$column)", date('m'));
				break;
			case '3': // Current Week
				$builder->where("WEEK($table.$column)", date('W'));
				break;
			case '4': // Last Year
				$builder->where("YEAR($table.$column)", date('Y') - 1);
				break;
			case '5': // Last Month
				$builder->where("MONTH($table.$column)", date('m', strtotime('-1 month')));
				break;
			case '6': // Last Week
				$builder->where("WEEK($table.$column)", date('W', strtotime('-1 week')));
				break;
			case '7': // Custom Date Range
				$builder->where("$table.$column >=", $from);
				$builder->where("$table.$column <=", $to);
				break;
			default: // All Records
				break;
		}

		return $builder;
	}

	function get_all_tasks()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('tasks');
		$builder->select('*, staffname, tasks.name as taskname, tasks.id as id, projects.id as projectid, project_number, projects.name as projectname');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->join('projects', 'tasks.relation = projects.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder = $this->apply_period_filter($builder, $period, $from, $to, 'created', 'tasks');
		return $builder->get()->getResultArray();
	}

	function get_all_leads()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('leads');
		$builder->select('*, leadsstatus.name as statusname, staff.staffname as leadassigned, leadssources.name as sourcename, leads.name as leadname, leads.phone as leadphone, leads.id as id, leads.email as email');
		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'left');
		$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->orderBy('leads.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder = $this->apply_period_filter($builder, $period, $from, $to, 'created', 'leads');
		return $builder->get()->getResultArray();
	}

	function get_all_products()
	{
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');
		$builder = $this->db->table('products');
		$builder->select('productcategories.name, productcategories.id as categoryid, products.id as id, products.code, products.productname, products.description, products.purchase_price, products.sale_price, products.stock, products.vat, products.status_id, products.productimage');
		$builder->join('productcategories', 'products.categoryid = productcategories.id', 'left');
		$builder->orderBy('products.id', 'desc');
		$builder->where('products.id_company', session()->get('id_company'));
		$builder = $this->apply_period_filter($builder, $period, $from, $to, 'createdat', 'products');
		return $builder->get()->getResultArray();
	}

	function get_all_staff()
	{
		$period = request()->getPost('period') ? request()->getPost('period') : null;
		$from = request()->getPost('from') ? request()->getPost('from') : null;
		$to = request()->getPost('to') ? request()->getPost('to') : null;
		$builder = $this->db->table('staff');
		$builder->select('*, departments.name as department, staff.id as id');
		$builder->join('departments', 'staff.department_id = departments.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		switch ($period) {
			case '0':
				return $builder->get()->getResultArray();
			case '1':
				$year = date('Y');
				$builder->where('YEAR(staff.createdAt)', $year);
				break;
			case '2':
				$month = date('m');
				$builder->where('MONTH(staff.createdAt)', $month);
				break;
			case '3':
				$builder->where('WEEK(staff.createdAt) = WEEK(CURRENT_DATE)');
				break;
			case '4':
				$builder->where('YEAR(staff.createdAt)', date('Y') - 1);
				break;
			case '5':
				$builder->where('MONTH(staff.createdAt)', date('m') - 1);
				break;
			case '6':
				$builder->where('WEEK(staff.createdAt)', WEEK(CURRENT_DATE) - 1);
				break;
			case '7':
				$builder->where('staff.createdAt >=', $from);
				$builder->where('staff.createdAt <=', $to);
				break;
			default:
				return $builder->get()->getResultArray();
		}
		return $builder->get()->getResultArray();
	}

	function get_all_projects()
	{
		$period = request()->getPost('period') ? request()->getPost('period') : null;
		$from = request()->getPost('from') ? request()->getPost('from') : null;
		$to = request()->getPost('to')  ? request()->getPost('to') : null;
		$builder = $this->db->table('projects');
		$builder->select('*, customers.company as customercompany, customers.namesurname as individual, customers.address as customeraddress, customers.id as customerid, projects.status_id as status, projects.id as id, projects.staff_id as staff_id');
		$builder->join('customers', 'projects.customer_id = customers.id', 'left');
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('projects.id', 'desc');
		switch ($period) {
			case '0':
				return $builder->get()->getResultArray();
			case '1':
				$year = date('Y');
				$builder->where('YEAR(projects.created)', $year);
				break;
			case '2':
				$month = date('m');
				$builder->where('MONTH(projects.created)', $month);
				break;
			case '3':
				$builder->where('WEEK(projects.created) = WEEK(CURRENT_DATE)');
				break;
			case '4':
				$builder->where('YEAR(projects.created)', date('Y') - 1);
				break;
			case '5':
				$builder->where('MONTH(projects.created)', date('m') - 1);
				break;
			case '6':
				$builder->where('WEEK(projects.created)', WEEK(CURRENT_DATE) - 1);
				break;
			case '7':
				$builder->where('projects.created >=', $from);
				$builder->where('projects.created <=', $to);
				break;
			default:
				return $builder->get()->getResultArray();
		}
		return $builder->get()->getResultArray();
	}


	function InvoiceReport()
	{
		$invoices = $this->get_all_invoices();
		$data_invoices = [];
		$invoiceTotal = 0;
		$builder = $this->db->table('payments');
		foreach ($invoices as $invoice) {
			$invoiceTotal += $invoice['total'];
			$totalx = $invoice['total'];
			$builder->selectSum('amount');
			$builder->where('invoice_id', $invoice['id']);
			$paytotal = $builder->get()->getRow()->amount;
			$balance = $totalx - $paytotal;
			if ($balance > 0) {
				$invoicestatus = '';
				$color = 'success';
			} else {
				$invoicestatus = lang2('paidinv');
				$color = 'success';
			}
			if ($paytotal < $invoice['total'] && $paytotal > 0 && $invoice['status_id'] == 3) {
				$invoicestatus = lang2('partial');
				$color = 'warning';
			} elseif ($paytotal < $invoice['total'] && $paytotal > 0) {
				$invoicestatus = lang2('partial');
				$color = 'warning';
			} elseif ($invoice['status_id'] == 3) {
				$invoicestatus = lang2('unpaid');
				$color = 'danger';
			} elseif ($invoice['status_id'] == 1) {
				$invoicestatus = lang2('draft');
				$color = 'muted';
			} elseif ($invoice['status_id'] == 4) {
				$invoicestatus = lang2('cancelled');
				$color = 'danger';
			}
			$customer = $invoice['type'] == 1 ? $invoice['individual'] : $invoice['customercompany'];
			$data_invoices[] = [
				'id' => $invoice['id'],
				'longid' => get_number('invoices', $invoice['id'], 'invoice', 'inv'),
				'created' => date(get_dateFormat(), strtotime($invoice['created'])),
				'duedate' => $invoice['duedate'] ? date(get_dateFormat(), strtotime($invoice['duedate'])) : '',
				'customer_id' => $invoice['customer_id'],
				'staff_id' => $invoice['staff_id'],
				'staffname' => $invoice['staffmembername'],
				'customer' => $customer,
				'total' => (float)$invoice['total'],
				'status' => $invoicestatus,
				'color' => $color,
			];
		}
		return [
			'data_invoice' => $data_invoices,
			'total' => $invoiceTotal,
		];
	}

	function CustomerReport()
	{
		$customers = $this->get_all_customers();
		$data_customers = [];
		$builder = $this->db->table('invoices');
		$builderPayments = $this->db->table('payments');

		foreach ($customers as $customer) {
			$name = $customer['type'] == '0' ? $customer['company'] : $customer['namesurname'];

			// Unpaid invoices
			$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
			$builder->where('staff.id_company', session()->get('id_company'));
			$builder->where('status_id', 3);
			$builder->where('customer_id', $customer['id']);
			$builder->selectSum('total');
			$total_unpaid_invoice_amount = $builder->get()->getRow()->total;

			// Paid invoices
			$builder->where('status_id', 2);
			$builder->where('customer_id', $customer['id']);
			$builder->selectSum('total');
			$total_paid_invoice_amount = $builder->get()->getRow()->total;

			// Payments
			$builderPayments->where('transactiontype', 0);
			$builderPayments->where('customer_id', $customer['id']);
			$builderPayments->selectSum('amount');
			$total_paid_amount = $builderPayments->get()->getRow()->amount;

			$data_customers[] = [
				'id' => $customer['id'],
				'name' => $name,
				'address' => $customer['address'],
				'email' => $customer['email'],
				'phone' => $customer['phone'],
				'customer_number' => get_number('customers', $customer['id'], 'customer', 'customer'),
				'group_name' => $customer['name'],
				'balance' => $total_unpaid_invoice_amount - $total_paid_amount + $total_paid_invoice_amount,
			];
		}

		return $data_customers;
	}


	function ExpenseReport()
	{
		$expenses = $this->get_all_expenses();
		$data_expenses = [];
		$expenseTotal = 0;

		foreach ($expenses as $expense) {
			$expenseTotal += $expense['amount'];

			if ($expense['invoice_id'] === null) {
				$billstatus = lang2('notbilled');
				$color = 'warning';
			} else {
				$billstatus = lang2('billed');
				$color = 'success';
			}

			if ($expense['internal'] == '1') {
				$customer = get_number('staff', $expense['staffid'], 'staff', 'staff');
				$customername = $expense['staff'];
				$billstatus = lang2('internal');
				$color = 'success';
			} else {
				$customer = get_number('customers', $expense['customerid'], 'customer', 'customer');
				$customername = $expense['company'] ? $expense['company'] : $expense['namesurname'];
			}

			$data_expenses[] = [
				'id' => $expense['id'],
				'longid' => get_number('expenses', $expense['id'], 'expense', 'expense'),
				'title' => $expense['title'],
				'category' => $expense['category'],
				'type' => $expense['internal'] == '1' ? lang2('internal') : '',
				'customer' => $customer,
				'customername' => $customername,
				'color' => $color,
				'billstatus' => $billstatus,
				'internal' => $expense['internal'],
				'staffid' => $expense['staffid'],
				'customerid' => $expense['customerid'],
				'date' => date(get_dateFormat(), strtotime($expense['date'])),
				'amount' => (float)$expense['amount'],
			];
		}

		return [
			'data_expense' => $data_expenses,
			'total' => $expenseTotal,
		];
	}

	function ProposalReport()
	{
		$proposals = $this->get_all_proposals();
		$data_proposals = [];
		$proposalTotal = 0;

		foreach ($proposals as $proposal) {
			$proposalTotal += $proposal['total'];

			switch ($proposal['status_id']) {
				case '0':
					$status = lang2('quote') . ' ' . lang2('request');
					$class = 'proposal-status-open';
					break;
				case '1':
					$status = lang2('draft');
					$class = 'proposal-status-accepted';
					break;
				case '2':
					$status = lang2('sent');
					$class = 'proposal-status-sent';
					break;
				case '3':
					$status = lang2('open');
					$class = 'proposal-status-open';
					break;
				case '4':
					$status = lang2('revised');
					$class = 'proposal-status-revised';
					break;
				case '5':
					$status = lang2('declined');
					$class = 'proposal-status-declined';
					break;
				case '6':
					$status = lang2('accepted');
					$class = 'proposal-status-accepted';
					break;
				default:
					$status = lang2('open');
					$class = 'proposal-status-open';
					break;
			}

			$data_proposals[] = [
				'id' => $proposal['id'],
				'longid' => get_number('proposals', $proposal['id'], 'proposal', 'proposal'),
				'subject' => $proposal['subject'],
				'date' => date(get_dateFormat(), strtotime($proposal['date'])),
				'opentill' => date(get_dateFormat(), strtotime($proposal['opentill'])),
				'status' => $status,
				'assigned' => $proposal['staffmembername'],
				'staffid' => $proposal['staffid'],
				'staff_number' => get_number('staff', $proposal['staffid'], 'staff', 'staff'),
				'total' => (float)$proposal['total'],
				'class' => $class,
			];
		}

		return [
			'data_proposal' => $data_proposals,
			'total' => $proposalTotal,
		];
	}


	public function DepositsReport()
	{
		$depositsModel = new \App\Models\DepositsModel();
		$deposits = $depositsModel->findAll();
		$data_deposits = [];

		foreach ($deposits as $deposit) {
			switch ($deposit['status']) {
				case '1':
					$billstatus = lang2('paid');
					$color = 'success';
					break;
				case '0':
					$billstatus = lang2('unpaid');
					$color = 'danger';
					break;
				default:
					$billstatus = lang2('internal');
					$color = 'success';
					break;
			}

			if ($deposit['status'] == '2') {
				$customer = get_number('staff', $deposit['staffid'], 'staff', 'staff');
				$customername = $deposit['staffname'];
			} else {
				$customer = get_number('customers', $deposit['customerid'], 'customer', 'customer');
				$customername = $deposit['company'] ? $deposit['company'] : $deposit['namesurname'];
			}

			$data_deposits[] = [
				'id' => $deposit['id'],
				'longid' => get_number('deposits', $deposit['id'], 'deposit', 'deposit'),
				'title' => $deposit['title'],
				'category' => $deposit['category'],
				'created' => date(get_dateFormat(), strtotime($deposit['created'])),
				'billstatus' => $billstatus,
				'color' => $color,
				'amount' => (float)$deposit['amount'],
				'date' => date(get_dateFormat(), strtotime($deposit['date'])),
				'customer' => $customer,
				'customername' => $customername,
				'status' => $deposit['status'],
				'staffid' => $deposit['staffid'],
				'customerid' => $deposit['customerid'],
			];
		}

		return $data_deposits;
	}

	public function OrdersReport()
	{
		$ordersModel = new \App\Models\OrdersModel();
		$orders = $ordersModel->findAll();
		$data_orders = [];

		foreach ($orders as $order) {
			if ($order['relation_type'] == 'customer') {
				$customer_number = get_number('customers', $order['customerid'], 'customer', 'customer');
				$customer = $order['company'] ? $order['company'] : $order['namesurname'];
			} else {
				$customer_number = get_number('leads', $order['leadid'], 'lead', 'lead');
				$customer = $order['leadname'];
			}

			switch ($order['status_id']) {
				case '1':
					$status = lang2('draft');
					$class = 'proposal-status-accepted';
					break;
				case '2':
					$status = lang2('sent');
					$class = 'proposal-status-sent';
					break;
				case '3':
					$status = lang2('open');
					$class = 'proposal-status-open';
					break;
				case '4':
					$status = lang2('revised');
					$class = 'proposal-status-revised';
					break;
				case '5':
					$status = lang2('declined');
					$class = 'proposal-status-declined';
					break;
				case '6':
					$status = lang2('accepted');
					$class = 'proposal-status-accepted';
					break;
				default:
					$status = lang2('open');
					$class = 'proposal-status-open';
					break;
			}

			$data_orders[] = [
				'id' => $order['id'],
				'longid' => get_number('orders', $order['id'], 'order', 'order'),
				'subject' => $order['subject'],
				'date' => date(get_dateFormat(), strtotime($order['date'])),
				'opentill' => date(get_dateFormat(), strtotime($order['opentill'])),
				'status' => $status,
				'total' => (float)$order['total'],
				'class' => $class,
				'customer' => $customer,
				'customer_number' => $customer_number,
				'assigned' => $order['staffmembername'],
				'staff' => get_number('staff', $order['staffid'], 'staff', 'staff'),
				'customerid' => $order['customerid'],
				'staffid' => $order['staffid']
			];
		}

		return $data_orders;
	}

	public function VendorsReport()
	{
		$vendorsModel = new \App\Models\VendorsModel();
		$vendors = $vendorsModel->findAll();
		$data_vendors = [];

		foreach ($vendors as $vendor) {
			$db = \Config\Database::connect();

			$total_unpaid_invoice_amount = $db->table('purchases')
				->selectSum('total')
				->where('status_id', '3')
				->where('vendor_id', $vendor['id'])
				->get()
				->getRow()->total;

			$total_paid_invoice_amount = $db->table('purchases')
				->selectSum('total')
				->where('status_id', '2')
				->where('vendor_id', $vendor['id'])
				->get()
				->getRow()->total;

			$total_paid_amount = $db->table('payments')
				->selectSum('amount')
				->where('transactiontype', '0')
				->where('vendor_id', $vendor['id'])
				->get()
				->getRow()->amount;

			$data_vendors[] = [
				'id' => $vendor['id'],
				'vendor_number' => get_number('vendors', $vendor['id'], 'vendor', 'vendor'),
				'name' => $vendor['company'],
				'email' => $vendor['email'],
				'address' => $vendor['address'],
				'group_name' => $vendor['name'],
				'phone' => $vendor['phone'],
				'balance' => $total_unpaid_invoice_amount - $total_paid_amount + $total_paid_invoice_amount,
			];
		}

		return $data_vendors;
	}

	public function PurchasesReport()
	{
		$purchasesModel = new \App\Models\PurchasesModel();
		$purchases = $purchasesModel->findAll();
		$data_purchases = [];
		$purchase_array = [];
		$purchaseTotal = 0;
		$db = \Config\Database::connect();

		foreach ($purchases as $purchase) {
			$purchaseTotal += $purchase['total'];
			$totalx = $purchase['total'];

			$paytotalQuery = $db->table('payments')
				->selectSum('amount')
				->where('purchase_id', $purchase['id'])
				->get();
			$paytotal = $paytotalQuery->getRow()->amount;
			$balance = $totalx - $paytotal;

			if ($balance > 0) {
				$purchasesstatus = '';
				$color = 'success';
			} else {
				$purchasesstatus = lang2('paidinv');
				$color = 'success';
			}

			if ($paytotal < $purchase['total'] && $paytotal > 0 && $purchase['status_id'] == 3) {
				$purchasesstatus = lang2('partial');
				$color = 'warning';
			} elseif ($paytotal < $purchase['total'] && $paytotal > 0) {
				$purchasesstatus = lang2('partial');
				$color = 'warning';
			} elseif ($purchase['status_id'] == 3) {
				$purchasesstatus = lang2('unpaid');
				$color = 'danger';
			} elseif ($purchase['status_id'] == 1) {
				$purchasesstatus = lang2('draft');
				$color = 'muted';
			} elseif ($purchase['status_id'] == 4) {
				$purchasesstatus = lang2('cancelled');
				$color = 'danger';
			}

			$data_purchases[] = [
				'id' => $purchase['id'],
				'serie' => $purchase['serie'],
				'longid' => get_number('purchases', $purchase['id'], 'purchase', 'purchase'),
				'created' => date(get_dateFormat(), strtotime($purchase['created'])),
				'duedate' => $purchase['duedate'] ? date(get_dateFormat(), strtotime($purchase['duedate'])) : '',
				'total' => (float)$purchase['total'],
				'status' => $purchasesstatus,
				'color' => $color,
				'vendor_id' => $purchase['vendor_id'],
				'vendor' => $purchase['vendorcompany'],
				'vendor_number' => get_number('vendors', $purchase['vendor_id'], 'vendor', 'vendor'),
				'staffname' => $purchase['staffmembername'],
			];

			$purchase_array = [
				'data_purchase' => $data_purchases,
				'total' => $purchaseTotal,
			];
		}

		return $purchase_array;
	}

	public function ContactsReport()
	{
		$contactsModel = new \App\Models\ContactsModel();
		$contacts = $contactsModel->findAll();
		$data_contacts = [];

		foreach ($contacts as $contact) {
			$data_contacts[] = [
				'id' => $contact['contactid'],
				'name' => $contact['name'] . ' ' . $contact['surname'],
				'customerid' => $contact['customerid'],
				'email' => $contact['email'],
				'customer' => $contact['company'] ? $contact['company'] : $contact['namesurname'],
				'customer_number' => get_number('customers', $contact['customerid'], 'customer', 'customer'),
				'mobile' => $contact['mobile'] ? $contact['mobile'] : $contact['phone'],
			];
		}

		return $data_contacts;
	}

	public function TicketsReport()
	{
		$ticketsModel = new \App\Models\TicketsModel();
		$tickets = $ticketsModel->findAll();
		$data_tickets = [];

		foreach ($tickets as $ticket) {
			switch ($ticket['priority']) {
				case '1':
					$priority = lang2('low');
					break;
				case '2':
					$priority = lang2('medium');
					break;
				case '3':
					$priority = lang2('high');
					break;
				default:
					$priority = lang2('medium');
					break;
			}

			switch ($ticket['status_id']) {
				case '1':
					$status = lang2('open');
					break;
				case '2':
					$status = lang2('inprogress');
					break;
				case '3':
					$status = lang2('answered');
					break;
				case '4':
					$status = lang2('closed');
					break;
				default:
					$status = lang2('open');
					break;
			}

			$customer = $ticket['type'] == 0 ? $ticket['company'] : $ticket['namesurname'];

			$data_tickets[] = [
				'id' => $ticket['id'],
				'subject' => $ticket['subject'],
				'customer' => $customer . '(' . get_number('customers', $ticket['customer_id'], 'customer', 'customer') . ')',
				'contactname' => $ticket['contactname'] . ' ' . $ticket['contactsurname'],
				'department' => $ticket['department'],
				'priority' => $priority,
				'status' => $status,
				'assigned_staff_name' => $ticket['staffmembername'],
				'last_reply_date' => $ticket['lastreply'] ? date(get_dateTimeFormat(), strtotime($ticket['lastreply'])) : '',
				'ticket_number' => get_number('tickets', $ticket['id'], 'ticket', 'ticket'),
			];
		}

		return $data_tickets;
	}

	public function TasksReport()
	{
		$tasksModel = new \App\Models\TasksModel();
		$tasks = $tasksModel->findAll();
		$data_tasks = [];

		foreach ($tasks as $task) {
			switch ($task['status_id']) {
				case '1':
					$status = lang2('open');
					break;
				case '2':
					$status = lang2('inprogress');
					break;
				case '3':
					$status = lang2('waiting');
					break;
				case '4':
					$status = lang2('complete');
					break;
				case '5':
					$status = lang2('cancelled');
					break;
				default:
					$status = lang2('open');
					break;
			}

			switch ($task['priority']) {
				case '1':
					$priority = lang2('low');
					break;
				case '2':
					$priority = lang2('medium');
					break;
				case '3':
					$priority = lang2('high');
					break;
				default:
					$priority = lang2('medium');
					break;
			}

			$data_tasks[] = [
				'id' => $task['id'],
				'task_number' => get_number('tasks', $task['id'], 'task', 'task'),
				'name' => $task['taskname'],
				'duedate' => date(get_dateFormat(), strtotime($task['duedate'])),
				'startdate' => date(get_dateFormat(), strtotime($task['startdate'])),
				'status' => $status,
				'priority' => $priority,
				'staffname' => $task['staffname'],
				'projectid' => $task['projectid'],
				'projectname' => $task['projectname'],
				'project' => get_number('projects', $task['projectid'], 'project', 'project'),
			];
		}

		return $data_tasks;
	}

	public function LeadsReport()
	{
		$leads = $this->get_all_leads();
		$data_leads = [];
		foreach ($leads as $lead) {
			$data_leads[] = [
				'id' => $lead['id'],
				'name' => $lead['leadname'],
				'company' => $lead['company'],
				'phone' => $lead['leadphone'],
				'color' => $lead['color'] ? $lead['color'] : '',
				'statusname' => $lead['statusname'] ? $lead['statusname'] : '',
				'sourcename' => $lead['sourcename'] ? $lead['sourcename'] : '',
				'assigned' => $lead['leadassigned'],
				'lead_number' => get_number('leads', $lead['id'], 'lead', 'lead'),
			];
		}
		return $data_leads;
	}

	public function ProductReport()
	{
		$products = $this->get_all_products();
		$data_products = [];
		foreach ($products as $product) {
			$data_products[] = [
				'product_id' => $product['id'],
				'code' => $product['code'],
				'name' => $product['productname'],
				'description' => $product['description'],
				'sales_price' => $product['sale_price'],
				'tax' => $product['vat'],
				'purchase_price' => $product['purchase_price'],
				'category_name' => $product['name'],
				'stock' => $product['stock'],
				'product_number' => get_number('products', $product['id'], 'product', 'product'),
			];
		}
		return $data_products;
	}

	public function staffReport()
	{
		$staffs = $this->get_all_staff();
		$data_staffs = [];
		foreach ($staffs as $staff) {
			if ($staff['admin'] == '1') {
				$type = lang2('admin');
			} elseif ($staff['staffmember'] == '1' && $staff['other'] === null) {
				$type = lang2('staff');
			} else {
				$type = lang2('other');
			}
			$data_staffs[] = [
				'id' => $staff['id'],
				'name' => $staff['staffname'],
				'department' => $staff['department'],
				'staff_number' => get_number('staff', $staff['id'], 'staff', 'staff'),
				'phone' => $staff['phone'],
				'address' => $staff['address'],
				'email' => $staff['email'],
				'type' => $type,
			];
		}
		return $data_staffs;
	}

	public function ProjectsReport()
	{
		$projects = $this->get_all_projects();
		$data_projects = [];
		foreach ($projects as $project) {
			switch ($project['status']) {
				case '1':
					$status = lang2('notstarted');
					break;
				case '2':
					$status = lang2('started');
					break;
				case '3':
					$status = lang2('percentage');
					break;
				case '4':
					$status = lang2('cancelled');
					break;
				case '5':
					$status = lang2('completed');
					break;
				default:
					$status = lang2('started');
					break;
			}
			$customer = $project['template'] == '1' ? lang2('template') : ($project['customercompany'] ? $project['customercompany'] : $project['namesurname']);
			$members = $this->ProjectsModel->get_members_index($project['id']);
			$data_projects[] = [
				'id' => $project['id'],
				'name' => $project['name'],
				'project_number' => get_number('projects', $project['id'], 'project', 'project'),
				'members' => $members,
				'customer' => $customer,
				'template' => $project['template'],
				'customerid' => $project['customerid'],
				'customer_number' => get_number('customers', $project['customerid'], 'customer', 'customer'),
				'startdate' => date(get_dateFormat(), strtotime($project['start_date'])),
				'enddate' => date(get_dateFormat(), strtotime($project['deadline'])),
				'value' => $project['projectvalue'],
				'status' => $status,
			];
		}
		return $data_projects;
	}

	public function get_timesheet_by_privileges($staff_id = '')
	{
		$admin = $this->isAdmin();
		$builder = $this->db->table('tasktimer');
		$builder->select('tasktimer.id, tasktimer.start, tasktimer.end, tasktimer.task_id, tasks.name, tasktimer.note, staff.staffname as staff, staff.id as staff_id, staff.staffavatar as avatar, email');
		$builder->join('staff', 'tasktimer.staff_id = staff.id', 'left');
		$builder->join('tasks', 'tasktimer.task_id = tasks.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('tasktimer.id', 'desc');
		if ($staff_id) {
			return $builder->where('tasktimer.staff_id', $staff_id)->get()->getResultArray();
		} else {
			return $builder->get()->getResultArray();
		}
	}

	public function total_amount_by_status($status = '3', $type = '')
	{
		$builder = $this->db->table($type);
		$builder->selectSum('total');
		if ($status == '3') {
			$builder->where('status_id = 3 AND CURDATE() <= duedate');
		} elseif ($status == 'due') {
			$builder->where('CURDATE() >= duedate AND duedate != "0000.00.00" AND status_id != "4" AND status_id != "2"');
		} elseif ($status == '1' || $status == '2') {
			$builder->where('status_id', $status);
		}
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where("purchases.vendor_id", request()->getPost('fornecedor'));
		}
		$builder->join('staff', "$type.staff_id = staff.id", 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('staff_id', session()->usr_id);
		$total_value = $builder->get()->getRow()->total;
		return !empty($total_value) ? $total_value : 0;
	}

	public function total_number_of_data_by_status($status, $type)
	{
		$builder = $this->db->table($type);
		if ($status == '4') {
			$builder->where('status_id !=', $status);
		} elseif ($status == '3') {
			$builder->where('status_id =', $status . ' AND CURDATE() <= duedate');
		} elseif ($status == 'due') {
			$builder->where('CURDATE() >= duedate AND duedate != "0000.00.00" AND status_id != "4" AND status_id != "2"');
		} else {
			$builder->where('status_id', $status);
		}
		$builder->join('staff', "$type.staff_id = staff.id", 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('staff_id', session()->usr_id);
		if (request()->getPost('data_de')) {
			$builder->where("purchases.duedate >=", request()->getPost('data_de'));
		}
		if (request()->getPost('data_ate')) {
			$builder->where("purchases.duedate <=", request()->getPost('data_ate'));
		}
		if (request()->getPost('fornecedor') && request()->getPost('fornecedor') != "-1") {
			$builder->where("purchases.vendor_id", request()->getPost('fornecedor'));
		}
		return $builder->countAllResults();
	}

	function getReports_LeasxReunioesPorOrigem()
	{
		$return = [];
		if (
			request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1"
			&& request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1"
		) {
			$builder = $this->db->table('leadssources');
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company = request()->getPost('id_company');
				$builder->where("leadssources.id_company", $id_company);
			} else {
				$builder->where('leadssources.id_company', session()->id_company);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leadssources.id", $flt_origem);
				} else {
					$builder->where("leadssources.id", $flt_origem);
				}
			}
			$r = $builder->get()->getResultArray();
			foreach ($r as $row) {
				$leads = $this->getLeadsGeradosByOrigem($row['id']);
				$reunioes = $this->getReunioesByOrigem($row['id']);
				$return[] = ['label' => $row['name'], 'leads' => $leads, 'reunioes' => $reunioes, 'total' => number_format((100 * $reunioes) / ($leads > 0 ? $leads : 1), 1, '.', '')];
			}
		}
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, '#2196f3', true, "Total %");
	}

	function getReports_reunioes()
	{
		$return = [];
		if (
			request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1"
			&& request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1"
		) {
			$builder = $this->db->table('leads_reunioes');
			$builder->select("count(*) as total, IF(realizada = 'Não Realizada', 'Não Realizada', leads_reunioes.realizada) as label");
			$builder->join('staff', 'staff.id = leads_reunioes.id_criador', 'inner');
			$builder->where('staff.id_company', session()->id_company);
			$builder->groupBy("label");
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de = request()->getPost('dt_de');
				$builder->where("leads_reunioes.data >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate = request()->getPost('dt_ate');
				$builder->where("leads_reunioes.data <=", $dt_ate);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'left');
				$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
				if (is_array($flt_origem)) {
					$builder->whereIn("leadssources.id", $flt_origem);
				} else {
					$builder->where("leadssources.id", $flt_origem);
				}
			}
			$return = $builder->get()->getResultArray();
		}
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, '#2196f3', true, "Total");
	}

	function getReports_ClientesxReunioesPorOrigem()
	{
		$return = [];
		if (
			request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1"
			&& request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1"
		) {
			$builder = $this->db->table('leadssources');
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company = request()->getPost('id_company');
				$builder->where("leadssources.id_company", $id_company);
			} else {
				$builder->where('leadssources.id_company', session()->id_company);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leadssources.id", $flt_origem);
				} else {
					$builder->where("leadssources.id", $flt_origem);
				}
			}
			$r = $builder->get()->getResultArray();
			foreach ($r as $row) {
				$clientes = $this->getClientesGeradosByOrigem($row['id']);
				$reunioes = $this->getReunioesByOrigem($row['id']);
				$return[] = ['label' => $row['name'], 'clientes' => $clientes, 'reunioes' => $reunioes, 'total' => number_format((100 * $clientes) / ($reunioes > 0 ? $reunioes : 1), 1, '.', '')];
			}
		}
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, '#2196f3', true, "Total %");
	}

	function getClientesGeradosByOrigem($id_origem)
	{
		$builder = $this->db->table('customers');
		$builder->select('count(*) as total');
		$builder->where("customers.source_id", $id_origem);
		if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
			$id_company = request()->getPost('id_company');
			$builder->where("staff.id_company", $id_company);
		}
		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("customers.created >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("customers.created <=", $dt_ate);
		}
		if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("customers.assigned_id", $flt_funcionario);
		}
		if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("customers.source_id", $flt_origem);
			} else {
				$builder->where("customers.source_id", $flt_origem);
			}
		}
		$r = $builder->get()->getRowArray()['total'];
		return $r;
	}

	function getLeadsGeradosByOrigem($id_origem)
	{
		$builder = $this->db->table('leads');
		$builder->select("count(*) as total");
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'left');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->where("leads.source", $id_origem);

		if (service('request')->getPost('id_company') != null && service('request')->getPost('id_company') != "-1") {
			$id_company = service('request')->getPost('id_company');
			$builder->where("staff.id_company", $id_company);
		}
		if (service('request')->getPost('dt_de') != null && service('request')->getPost('dt_de') != "-1") {
			$dt_de = service('request')->getPost('dt_de');
			$builder->where("leads.created >=", $dt_de);
		}
		if (service('request')->getPost('dt_ate') != null && service('request')->getPost('dt_ate') != "-1") {
			$dt_ate = service('request')->getPost('dt_ate');
			$builder->where("leads.created <=", $dt_ate);
		}
		if (service('request')->getPost('flt_funil') != null && service('request')->getPost('flt_funil') != "-1") {
			$flt_funil = service('request')->getPost('flt_funil');
			$builder->where("leadsstatus.id_list", $flt_funil);
		} else {
			if (session()->get('super_admin') != "1") {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
		}
		if (service('request')->getPost('flt_funcionario') != null && service('request')->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = service('request')->getPost('flt_funcionario');
			$builder->where("leads.assigned_id", $flt_funcionario);
		}
		if (service('request')->getPost('flt_origem') != null && service('request')->getPost('flt_origem') != "-1") {
			$flt_origem = service('request')->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source", $flt_origem);
			}
		}
		$r = $builder->get()->getRowArray()['total'];
		return $r;
	}

	function getReunioesByOrigem($id_origem)
	{
		$builder = $this->db->table('leads_reunioes');
		$builder->select('count(*) as total');
		$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
		$builder->join('staff', 'staff.id = leads_reunioes.id_criador', 'inner');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->where("leads.source", $id_origem);

		if (service('request')->getPost('dt_de') != null && service('request')->getPost('dt_de') != "-1") {
			$dt_de = service('request')->getPost('dt_de');
			$builder->where("leads_reunioes.data >=", $dt_de);
		}
		if (service('request')->getPost('dt_ate') != null && service('request')->getPost('dt_ate') != "-1") {
			$dt_ate = service('request')->getPost('dt_ate');
			$builder->where("leads_reunioes.data <=", $dt_ate);
		}
		if (service('request')->getPost('id_company') != null && service('request')->getPost('id_company') != "-1") {
			$id_company = service('request')->getPost('id_company');
			$builder->where("staff.id_company", $id_company);
		}
		if (service('request')->getPost('flt_funil') != null && service('request')->getPost('flt_funil') != "-1") {
			$flt_funil = service('request')->getPost('flt_funil');
			$builder->where("leadsstatus.id_list", $flt_funil);
		} else {
			if (session()->get('super_admin') != "1") {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
		}
		if (service('request')->getPost('flt_funcionario') != null && service('request')->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = service('request')->getPost('flt_funcionario');
			$builder->where("leads.assigned_id", $flt_funcionario);
		}
		if (service('request')->getPost('flt_origem') != null && service('request')->getPost('flt_origem') != "-1") {
			$flt_origem = service('request')->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source", $flt_origem);
			}
		}
		$r = $builder->get()->getRowArray()['total'];
		return $r;
	}

	function getReports_graphReunioesRealizadasFunil()
	{
		$builder = $this->db->table('leads_reunioes');
		$builder->select("count(*) as total, nm_list as label");
		$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->join('leads_list', 'leads_list.id_list =leadsstatus.id_list', 'inner');
		$builder->where('realizada', 'Realizada');
		$builder->where('leads_list.id_company', session()->get('id_company'));
		$builder->groupBy("leads_list.id_list");

		if (service('request')->getPost('dt_de') != null && service('request')->getPost('dt_de') != "-1") {
			$dt_de = service('request')->getPost('dt_de');
			$builder->where("leads_reunioes.data >=", $dt_de);
		}
		if (service('request')->getPost('dt_ate') != null && service('request')->getPost('dt_ate') != "-1") {
			$dt_ate = service('request')->getPost('dt_ate');
			$builder->where("leads_reunioes.data <=", $dt_ate);
		}
		if (service('request')->getPost('id_company') != null && service('request')->getPost('id_company') != "-1") {
			$id_company = service('request')->getPost('id_company');
			$builder->where("staff.id_company", $id_company);
		}
		if (service('request')->getPost('flt_funil') != null && service('request')->getPost('flt_funil') != "-1") {
			$flt_funil = service('request')->getPost('flt_funil');
			$builder->where("leadsstatus.id_list", $flt_funil);
		} else {
			if (session()->get('super_admin') != "1") {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
		}
		if (service('request')->getPost('flt_funcionario') != null && service('request')->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = service('request')->getPost('flt_funcionario');
			$builder->where("leads.assigned_id", $flt_funcionario);
		}
		if (service('request')->getPost('flt_origem') != null && service('request')->getPost('flt_origem') != "-1") {
			$flt_origem = service('request')->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source", $flt_origem);
			}
		}
		$return = $builder->get()->getResultArray();
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, '#2196f3', true);
	}

	function getReports_reunioesPorFuncionarios()
	{
		$builder = $this->db->table('leads_reunioes');
		$builder->select("count(*) as total, staffname as label");
		$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->where('realizada', 'Realizada');
		$builder->groupBy("leads.assigned_id");

		if (service('request')->getPost('dt_de') != null && service('request')->getPost('dt_de') != "-1") {
			$dt_de = service('request')->getPost('dt_de');
			$builder->where("leads_reunioes.data >= '$dt_de'");
		}
		if (service('request')->getPost('dt_ate') != null && service('request')->getPost('dt_ate') != "-1") {
			$dt_ate = service('request')->getPost('dt_ate');
			$builder->where("leads_reunioes.data <= '$dt_ate'");
		}
		if (service('request')->getPost('id_company') != null && service('request')->getPost('id_company') != "-1") {
			$id_company = service('request')->getPost('id_company');
			$builder->where("staff.id_company = '$id_company'");
		}
		if (service('request')->getPost('flt_funil') != null && service('request')->getPost('flt_funil') != "-1") {
			$flt_funil = service('request')->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '$flt_funil'");
		} else {
			if (session()->get('super_admin') != "1") {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
		}
		if (service('request')->getPost('flt_funcionario') != null && service('request')->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = service('request')->getPost('flt_funcionario');
			$builder->where("leads.assigned_id = '$flt_funcionario'");
		}
		if (service('request')->getPost('flt_origem') != null && service('request')->getPost('flt_origem') != "-1") {
			$flt_origem = service('request')->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source = '$flt_origem'");
			}
		}
		$return = $builder->get()->getResultArray();
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, '#2196f3', true);
	}

	function getReports_reunioesRealizadas()
	{
		$builder = $this->db->table('leads_reunioes');
		$builder->select("count(*) as total, if(staffname is not null, staffname, 'Cl. indefinido') as label");
		$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
		$builder->join('staff', 'leads.closer = staff.id', 'left');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->where('realizada', 'Realizada');
		$builder->groupBy("leads.closer");

		if (service('request')->getPost('id_company') && service('request')->getPost('id_company') != "-1") {
			$id_company = service('request')->getPost('id_company');
			$builder->where("staff.id_company = '$id_company'");
		}
		if (service('request')->getPost('dt_de') && service('request')->getPost('dt_de') != "-1") {
			$dt_de = service('request')->getPost('dt_de');
			$builder->where("leads_reunioes.data >= '$dt_de'");
		}
		if (service('request')->getPost('dt_ate') && service('request')->getPost('dt_ate') != "-1") {
			$dt_ate = service('request')->getPost('dt_ate');
			$builder->where("leads_reunioes.data <= '$dt_ate'");
		}
		if (service('request')->getPost('flt_funil') && service('request')->getPost('flt_funil') != "-1") {
			$flt_funil = service('request')->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '$flt_funil'");
		} else {
			if (session()->get('super_admin') != "1") {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
		}
		if (service('request')->getPost('flt_funcionario')  && service('request')->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = service('request')->getPost('flt_funcionario');
			$builder->where("leads.closer = '$flt_funcionario'");
		}
		if (service('request')->getPost('flt_origem') && service('request')->getPost('flt_origem') != "-1") {
			$flt_origem = service('request')->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source = '$flt_origem'");
			}
		}
		$return = $builder->get()->getResultArray();
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, '#2196f3', true);
	}

	function getReports_reunioesxConversoes()
	{
		$builder = $this->db->table('leads_reunioes');
		$builder->select("count(*) as total, if(staffname is not null, staffname, 'Cl. indefinido') as label, leads.closer");
		$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
		$builder->join('staff', 'leads.closer = staff.id', 'left');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->where('realizada', 'Realizada');
		$builder->groupBy("leads.closer");

		if (service('request')->getPost('id_company') != null && service('request')->getPost('id_company') != "-1") {
			$id_company = service('request')->getPost('id_company');
			$builder->where("staff.id_company = '$id_company'");
		}
		if (service('request')->getPost('dt_de') != null && service('request')->getPost('dt_de') != "-1") {
			$dt_de = service('request')->getPost('dt_de');
			$builder->where("leads_reunioes.data >= '$dt_de'");
		}
		if (service('request')->getPost('dt_ate') != null && service('request')->getPost('dt_ate') != "-1") {
			$dt_ate = service('request')->getPost('dt_ate');
			$builder->where("leads_reunioes.data <= '$dt_ate'");
		}
		if (service('request')->getPost('flt_funil') != null && service('request')->getPost('flt_funil') != "-1") {
			$flt_funil = service('request')->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '$flt_funil'");
		} else {
			if (session()->get('super_admin') != "1") {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
		}
		if (service('request')->getPost('flt_funcionario') != null && service('request')->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = service('request')->getPost('flt_funcionario');
			$builder->where("leads.closer = '$flt_funcionario'");
		}
		if (service('request')->getPost('flt_origem') != null && service('request')->getPost('flt_origem') != "-1") {
			$flt_origem = service('request')->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source = '$flt_origem'");
			}
		}
		$return = $builder->get()->getResultArray();
		foreach ($return as $index => $row) {
			$clientes = $this->getClientePorFuncionario($row['closer']);
			$return[$index]['total'] = number_format((100 * $clientes) / ($row['total'] > 0 ? $row['total'] : 1), 1, '.', '');
			$return[$index]['color'] = $this->get_cor_aleatoria();
		}
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, null, true, "Total %");
	}

	function getReports_ConversoesPorCloser()
	{
		$builder = $this->db->table('customers');
		$builder->select("count(*) as total, IF(staff.staffname IS NOT NULL, staff.staffname, 'Não definido') as label");
		$builder->join('staff', 'customers.closer = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->groupBy("customers.closer");
		$builder->orderBy('total', 'asc');

		if (service('request')->getPost('dt_de') != null && service('request')->getPost('dt_de') != "-1") {
			$dt_de = service('request')->getPost('dt_de');
			$builder->where("customers.created >= '$dt_de'");
		}
		if (service('request')->getPost('dt_ate') != null && service('request')->getPost('dt_ate') != "-1") {
			$dt_ate = service('request')->getPost('dt_ate');
			$builder->where("customers.created <= '$dt_ate'");
		}
		if (service('request')->getPost('flt_origem') != null && service('request')->getPost('flt_origem') != "-1") {
			$flt_origem = service('request')->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("customers.source_id", $flt_origem);
			} else {
				$builder->where("customers.source_id = '$flt_origem'");
			}
		}
		if (service('request')->getPost('flt_funcionario') != null && service('request')->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = service('request')->getPost('flt_funcionario');
			$builder->where("customers.closer = '$flt_funcionario'");
		}
		if (service('request')->getPost('flt_origem') != null && service('request')->getPost('flt_origem') != "-1") {
			$flt_origem = service('request')->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source_id", $flt_origem);
			} else {
				$builder->where("leads.source_id = '$flt_origem'");
			}
		}
		$return = $builder->get()->getResultArray();
		foreach ($return as $index => $row) {
			$return[$index]['color'] = $this->get_cor_aleatoria();
		}
		return $this->montaGraph($return, null, true, "Total");
	}

	function getClientePorFuncionario($id_funcionario)
	{
		$builder = $this->db->table('customers');
		$builder->select("count(*) as total, staff.staffname as label");
		$builder->join('staff', 'customers.closer = staff.id', 'left');
		$builder->groupBy("customers.closer");
		$builder->orderBy('total', 'asc');
		$builder->where("customers.closer", $id_funcionario);

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("customers.created >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("customers.created <=", $dt_ate);
		}
		if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("customers.source_id", $flt_origem);
			} else {
				$builder->where("customers.source_id", $flt_origem);
			}
		}
		$r = $builder->get()->getRowArray();

		return $r ? $r['total'] : 0;
	}

	function getReports_ClientesPorFuncionario()
	{
		$builder = $this->db->table('leads_atv');
		$builder->select("count(distinct leads_atv.id_customer) as total, staffname as label");
		$builder->join('customers', 'customers.id = leads_atv.id_customer', 'left');
		$builder->join('staff', 'customers.customer_sucess = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('leads_atv.id_customer IS NOT NULL');
		$builder->groupBy("customers.customer_sucess");

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("date(leads_atv.dt_entrada) >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("date(leads_atv.dt_entrada) <=", $dt_ate);
		}
		if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
			$flt_funcionario = request()->getPost('funcionario');
			$builder->where("customers.customer_sucess", $flt_funcionario);
		}

		$return = $builder->get()->getResultArray();
		foreach ($return as $index => $row) {
			$return[$index]['color'] = $this->get_cor_aleatoria();
		}
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});

		return $this->montaGraph($return, null, true);
	}

	function getReports_ConversoesPorFuncionario()
	{
		$builder = $this->db->table('customers');
		$builder->select("count(distinct customers.id) as total, staffname as label");
		$builder->join('staff', 'customers.assigned_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->groupBy("customers.assigned_id");

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("date(customers.created) >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("date(customers.created) <=", $dt_ate);
		}
		if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
			$flt_funcionario = request()->getPost('funcionario');
			$builder->where("customers.assigned_id", $flt_funcionario);
		}

		$return = $builder->get()->getResultArray();
		foreach ($return as $index => $row) {
			$return[$index]['color'] = $this->get_cor_aleatoria();
		}

		return $this->montaGraph($return, null, true);
	}

	function getPerfReuniaoMensal()
	{
		$return = [];
		if (
			request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1"
			&& request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1"
		) {
			$data1 = new DateTime(request()->getPost('dt_de'));
			$data2 = new DateTime(request()->getPost('dt_ate'));
			$intervalo = $data1->diff($data2)->m;
			for ($i = 0; $i <= $intervalo; $i++) {
				$data = date('m/Y', strtotime(date('Y-m', strtotime(request()->getPost('dt_ate'))) . " -$i month"));
				$builder = $this->db->table('leads_reunioes');
				$builder->select("count(DISTINCT concat(leads_reunioes.id_lead, leads_reunioes.data, leads_reunioes.hora)) as total, DATE_FORMAT(data,'%m/%Y') as label");
				$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
				$builder->join('staff', 'staff.id = leads_reunioes.id_criador', 'inner');
				$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
				$builder->where("DATE_FORMAT(data,'%m/%Y') = '$data'");
				$builder->groupBy("MONTH(data)");
				$builder->orderBy('data', 'desc');
				$builder->where('realizada', 'Realizada');
				if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
					$dt_de = request()->getPost('dt_de');
					$builder->where("leads_reunioes.data >= '$dt_de'");
				}
				if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
					$dt_ate = request()->getPost('dt_ate');
					$builder->where("leads_reunioes.data <= '$dt_ate'");
				}
				if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
					$id_company = request()->getPost('id_company');
					$builder->where("staff.id_company = '$id_company'");
				} else {
					$builder->where('staff.id_company', session()->id_company);
				}
				if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
					$flt_funil = request()->getPost('flt_funil');
					$builder->where("leadsstatus.id_list = '$flt_funil'");
				} else {
					if (session()->super_admin != "1") {
						$builder->where('staff.id_company', session()->id_company);
					}
				}
				if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
					$flt_funcionario = request()->getPost('flt_funcionario');
					$builder->where("leads.assigned_id = '$flt_funcionario'");
				}
				if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
					$flt_origem = request()->getPost('flt_origem');
					if (is_array($flt_origem)) {
						$builder->where("leads.source IN(" . implode(",", $flt_origem) . ")");
					} else {
						$builder->where("leads.source = '$flt_origem'");
					}
				}
				$r = $builder->get()->getRowArray();
				if (!$r) {
					$r = ['label' => $data, 'total' => 0];
				}
				$return[] = $r;
			}
		}
		return $this->montaGraph($return, '#2196f3', true);
	}

	function getQuantidadeDeReunioes()
	{
		$return = [];
		$builder = $this->db->table('leads_reunioes');
		$builder->select("count(DISTINCT concat(leads_reunioes.id_customer, leads_reunioes.data, leads_reunioes.hora)) as total, DATE_FORMAT(data,'%m/%Y') as label");
		$builder->join('customers', 'customers.id = leads_reunioes.id_customer', 'inner');
		$builder->join('staff', 'staff.id = leads_reunioes.id_criador', 'inner');
		$builder->groupBy("MONTH(data)");
		$builder->orderBy('data', 'desc');
		$builder->where('realizada', 'Realizada');
		if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
			$ano = request()->getPost('ano');
			$builder->where("YEAR(leads_reunioes.data) = '$ano'");
		}
		if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
			$id_company = request()->getPost('id_company');
			$builder->where("staff.id_company = '$id_company'");
		} else {
			$builder->where('staff.id_company', session()->id_company);
		}
		if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("leads.assigned_id = '$flt_funcionario'");
		}
		if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->where("leads.source IN(" . implode(",", $flt_origem) . ")");
			} else {
				$builder->where("leads.source = '$flt_origem'");
			}
		}
		$r = $builder->get()->getRowArray();
		if (!$r) {
			$r = ['label' => $data, 'total' => 0];
		}
		$return[] = $r;
		return $this->montaGraph($return, '#2196f3', true);
	}

	function getReports_reuniaoAgendadaxRealizada()
	{
		$return = [];
		$labels = [];
		$dataRealizado = [];
		$dataNaoRealizado = [];
		if (
			request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1"
			&& request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1"
		) {
			$data1 = new DateTime(request()->getPost('dt_de'));
			$data2 = new DateTime(request()->getPost('dt_ate'));
			$intervalo = $data1->diff($data2)->m;
			for ($i = 0; $i <= $intervalo; $i++) {
				$data = date('m/Y', strtotime(date('Y-m', strtotime(request()->getPost('dt_ate'))) . " -$i month"));
				$builder = $this->db->table('leads_reunioes');
				$builder->select("count(IF(realizada = 'Não Realizada', 1, NULL)) as Nrealizado, count(IF(realizada = 'Realizada', 1, NULL)) as realizado");
				$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
				$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
				$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
				$builder->where("DATE_FORMAT(data,'%m/%Y') = '$data'");
				$builder->groupBy("DATE_FORMAT(data,'%m/%Y')");
				$builder->orderBy('data', 'desc');
				if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
					$id_company = request()->getPost('id_company');
					$builder->where("staff.id_company = '$id_company'");
				} else {
					$builder->where('staff.id_company', session()->id_company);
				}
				if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
					$dt_de = request()->getPost('dt_de');
					$builder->where("leads_reunioes.data >= '$dt_de'");
				}
				if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
					$dt_ate = request()->getPost('dt_ate');
					$builder->where("leads_reunioes.data <= '$dt_ate'");
				}
				if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
					$flt_funil = request()->getPost('flt_funil');
					$builder->where("leadsstatus.id_list = '$flt_funil'");
				} else {
					if (session()->super_admin != "1") {
						$builder->where('staff.id_company', session()->id_company);
					}
				}
				if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
					$flt_funcionario = request()->getPost('flt_funcionario');
					$builder->where("leads.assigned_id = '$flt_funcionario'");
				}
				if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
					$flt_origem = request()->getPost('flt_origem');
					if (is_array($flt_origem)) {
						$builder->where("leads.source IN(" . implode(",", $flt_origem) . ")");
					} else {
						$builder->where("leads.source = '$flt_origem'");
					}
				}
				$r = $builder->get()->getRowArray();
				$labels[] = $data;
				if (!$r) {
					$dataRealizado[] = 0;
					$dataNaoRealizado[] = 0;
				} else {
					$dataRealizado[] = $r['realizado'];
					$dataNaoRealizado[] = $r['Nrealizado'];
				}
			}
		}
		$datasets[] = [
			'label' => 'Total Não realizado',
			'data' => $dataNaoRealizado,
			'backgroundColor' => $this->get_cor_aleatoria(),
			'borderColor' => 'transparent',
			'pointBackgroundColor' => '#FFFFFF',
			'lineTension' => '0.40',
		];
		$datasets[] = [
			'label' => 'Total realizado',
			'data' => $dataRealizado,
			'backgroundColor' => $this->get_cor_aleatoria(),
			'borderColor' => 'transparent',
			'pointBackgroundColor' => '#FFFFFF',
			'lineTension' => '0.40',
		];
		if (count($labels) == 0) {
			return ['datasets' => [], 'labels' => []];
		} else {
			return ['datasets' => $datasets, 'labels' => $labels];
		}
	}

	function getReports_QuantidadeLeads()
	{
		$builder = $this->db->table('leads');
		$builder->select("count(*) as total, nm_list as label");
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner');
		$builder->where('leads_list.id_company', session()->id_company);
		$builder->groupBy("leads_list.id_list");
		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads.created >= '$dt_de'");
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads.created <= '$dt_ate'");
		}
		if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
			$flt_funil = request()->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '$flt_funil'");
		}
		if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("leads.assigned_id = '$flt_funcionario'");
		}
		if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->where("leads.source IN(" . implode(",", $flt_origem) . ")");
			} else {
				$builder->where("leads.source = '$flt_origem'");
			}
		}
		$return = $builder->get()->getResultArray();
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, '#2196f3', true);
	}

	public function getReports_Conversoes()
	{
		$builder = $this->db->table('leads');
		$builder->select('leads_list.id_list, COUNT(*) as total');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner');
		$builder->where('leads_list.id_company', session()->get('id_company'));
		$builder->where('dateconverted IS NOT NULL');
		$builder->groupBy('leads_list.id_list');

		if (request()->getPost('dt_de')  && request()->getPost('dt_de') !== "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads.dateconverted >= '{$dt_de}'");
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads.dateconverted <= '{$dt_ate}'");
		}
		if (request()->getPost('flt_funil') && request()->getPost('flt_funil') !== "-1") {
			$flt_funil = request()->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '{$flt_funil}'");
		}
		if (request()->getPost('flt_funcionario') && request()->getPost('flt_funcionario') !== "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("leads.assigned_id = '{$flt_funcionario}'");
		}
		if (request()->getPost('flt_origem') && request()->getPost('flt_origem') !== "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source = '{$flt_origem}'");
			}
		}

		$subquery = $builder->getCompiledSelect();

		$builder = $this->db->table('leads_list');
		$builder->select('leads_list.nm_list as label, COALESCE(total_leads.total, 0) as total');
		$builder->join("($subquery) as total_leads", 'leads_list.id_list = total_leads.id_list', 'left');
		$builder->where('leads_list.id_company', session()->get('id_company'));

		$result = $builder->get()->getResultArray();
		usort($result, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});

		return $this->montaGraph($result, '#2196f3', true);
	}

	public function getReports_reunioesAgendadas()
	{
		$builder = $this->db->table('leads_reunioes');
		$builder->select("count(*) as total, staffname as label");
		$builder->join('staff', 'leads_reunioes.id_criador = staff.id', 'left');
		$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->groupBy('staff.id');

		if (request()->getPost('dt_de') && request()->getPost('dt_de') !== "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads_reunioes.data >= '{$dt_de}'");
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads_reunioes.data <= '{$dt_ate}'");
		}
		if (request()->getPost('id_company') && request()->getPost('id_company') !== "-1") {
			$id_company = request()->getPost('id_company');
			$builder->where("staff.id_company = '{$id_company}'");
		}
		if (request()->getPost('flt_funil') && request()->getPost('flt_funil') !== "-1") {
			$flt_funil = request()->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '{$flt_funil}'");
		} else {
			if (session()->get('super_admin') !== "1") {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
		}
		if (request()->getPost('flt_funcionario') && request()->getPost('flt_funcionario') !== "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("leads.assigned_id = '{$flt_funcionario}'");
		}
		if (request()->getPost('flt_origem') && request()->getPost('flt_origem') !== "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source = '{$flt_origem}'");
			}
		}

		$result = $builder->get()->getResultArray();
		usort($result, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});

		return $this->montaGraph($result, '#2196f3', true);
	}

	public function getReports_graphLeadsAcionados()
	{
		$builder = $this->db->table('leads_atv');
		$builder->select("count(distinct leads_atv.id_lead) as leads_acionados, staffname as label, leads_atv.id_lead as id_lead, leads.assigned_id");
		$builder->join('leads', 'leads.id = leads_atv.id_lead', 'inner');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->groupBy('leads.assigned_id');

		if (request()->getPost('dt_de') && request()->getPost('dt_de') !== "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads_atv.data >= '{$dt_de}'");
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads_atv.data <= '{$dt_ate}'");
		}
		if (request()->getPost('funcionario') && request()->getPost('funcionario') !== "-1") {
			$flt_funcionario = request()->getPost('funcionario');
			$builder->where("leads.assigned_id = '{$flt_funcionario}'");
		}

		$result = $builder->get()->getResultArray();
		foreach ($result as $index => $row) {
			$reunioes = $this->getReunioesPorFuncionario($row['assigned_id']);
			$result[$index]['total'] = number_format((100 * $reunioes) / ($row['leads_acionados'] > 0 ? $row['leads_acionados'] : 1), 1, '.', '');
			$result[$index]['color'] = $this->get_cor_aleatoria();
		}
		usort($result, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});

		return $this->montaGraph($result, null, true, "Total %");
	}

	public function getReports_produtividade()
	{
		$totalLogs = [];
		$totalGeral = 1;
		$dias = 1;

		$dataArr = [
			'horasTrabalhadas' => '00:00:00',
			'HorasOciosas' => '00:00:00',
			'LeadsAcionados' => 0,
			'ReunioesRealizadas' => 0
		];

		// Obter Leads Acionados
		$builder = $this->db->table('leads_atv');
		$builder->select("COUNT(DISTINCT leads_atv.id_lead) AS total");
		$builder->join('staff', 'leads_atv.id_criador = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('leads_atv.id_lead IS NOT NULL');

		if (request()->getPost('dt_de') && request()->getPost('dt_de') !== "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("DATE(leads_atv.dt_entrada) >=", $dt_de);
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("DATE(leads_atv.dt_entrada) <=", $dt_ate);
		}
		if (request()->getPost('funcionario') && request()->getPost('funcionario') !== "-1") {
			$flt_funcionario = request()->getPost('funcionario');
			$builder->where('leads_atv.id_criador', $flt_funcionario);
		}

		$dataArr['LeadsAcionados'] = $builder->get()->getRow()->total;

		// Obter Reuniões Realizadas
		$builder = $this->db->table('leads_reunioes');
		$builder->select("COUNT(*) AS total");
		$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
		$builder->join('staff', 'leads.closer = staff.id', 'left');
		$builder->where('realizada', 'Realizada');
		$builder->where('staff.id_company', session()->get('id_company'));

		if (request()->getPost('dt_de') && request()->getPost('dt_de') !== "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads_reunioes.data >=", $dt_de);
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads_reunioes.data <=", $dt_ate);
		}
		if (request()->getPost('funcionario') && request()->getPost('funcionario') !== "-1") {
			$funcionario = request()->getPost('funcionario');
			$builder->where('leads.closer', $funcionario);
		}

		$dataArr['ReunioesRealizadas'] = $builder->get()->getRow()->total;

		return $dataArr;
	}


	function getReunioesPorFuncionario($id_funcionario)
	{
		$builder = $this->db->table('leads_reunioes');
		$builder->select('count(*) as total');
		$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
		$builder->join('staff', 'staff.id = leads.assigned_id', 'inner');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->where('staff.id', $id_funcionario);
		$builder->where('staff.id_company', session()->id_company);
		if (request()->getPost('dt_de') && request()->getPost('dt_de') !== "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads_reunioes.data >= '{$dt_de}'");
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads_reunioes.data <= '{$dt_ate}'");
		}
		if (request()->getPost('flt_funil') && request()->getPost('flt_funil') !== "-1") {
			$flt_funil = request()->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '{$flt_funil}'");
		}
		if (request()->getPost('flt_funcionario') && request()->getPost('flt_funcionario') !== "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("leads.assigned_id = '{$flt_funcionario}'");
		}
		if (request()->getPost('flt_origem') && request()->getPost('flt_origem') !== "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source = '{$flt_origem}'");
			}
		}
		return intval($builder->get()->getRow()->total);
	}

	function getReportsQualificacoes()
	{
		$builder = $this->db->table('leads');
		$builder->select("count(*) as total, 
    CASE leads.qualificacao 
    WHEN 1 THEN 'Ruim'
    WHEN 2 THEN 'Médio'
    WHEN 3 THEN 'Bom'
    ELSE 'Muito bom' END as label,
    CASE leads.qualificacao 
    WHEN 1 THEN '#ff0000'
    WHEN 2 THEN '#ffb000'
    WHEN 3 THEN '#26c281'
    ELSE '#796eed' END as color");
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->where('leads.qualificacao IS NOT NULL');
		$builder->groupBy('leads.qualificacao');
		if (request()->getPost('id_company') && request()->getPost('id_company') !== "-1") {
			$id_company = request()->getPost('id_company');
			$builder->where('staff.id_company', $id_company);
		}
		if (request()->getPost('dt_de') && request()->getPost('dt_de') !== "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads.created >= '{$dt_de}'");
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads.created <= '{$dt_ate}'");
		}
		if (request()->getPost('flt_funil') && request()->getPost('flt_funil') !== "-1") {
			$flt_funil = request()->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '{$flt_funil}'");
		} else {
			if (session()->super_admin !== "1") {
				$builder->where('staff.id_company', session()->id_company);
			}
		}
		if (request()->getPost('flt_funcionario') && request()->getPost('flt_funcionario') !== "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("leads.assigned_id = '{$flt_funcionario}'");
		}
		if (request()->getPost('flt_origem') && request()->getPost('flt_origem') !== "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source = '{$flt_origem}'");
			}
		}
		$data = $builder->get()->getResultArray();
		return $this->montaGraph($data, '#2196f3', true);
	}

	function getReports_leadsPorFunil()
	{
		$builder = $this->db->table('leads');
		$builder->select("count(*) as total, nm_list as label");
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
		$builder->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner');
		$builder->groupBy('leadsstatus.id_list');
		if (request()->getPost('id_company') && request()->getPost('id_company') !== "-1") {
			$id_company = request()->getPost('id_company');
			$builder->where('staff.id_company', $id_company);
		}
		if (request()->getPost('dt_de') && request()->getPost('dt_de') !== "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads.created >= '{$dt_de}'");
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') !== "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads.created <= '{$dt_ate}'");
		}
		if (request()->getPost('flt_funil') && request()->getPost('flt_funil') !== "-1") {
			$flt_funil = request()->getPost('flt_funil');
			$builder->where("leadsstatus.id_list = '{$flt_funil}'");
		} else {
			if (session()->super_admin !== "1") {
				$builder->where('staff.id_company', session()->id_company);
			}
		}
		if (request()->getPost('flt_funcionario') && request()->getPost('flt_funcionario') !== "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("leads.assigned_id = '{$flt_funcionario}'");
		}
		if (request()->getPost('flt_origem') && request()->getPost('flt_origem') !== "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source = '{$flt_origem}'");
			}
		}
		$data = $builder->get()->getResultArray();
		usort($data, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($data, '#2196f3', true);
	}

	function getReports($report)
	{
		if ($report == "totalClientes") {
			$builder = $this->db->table('customers');
			$builder->select('count(*) as total');
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("customers.id_company", $id_company);
			} else {
				$builder->where('customers.id_company', session()->id_company);
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de =  request()->getPost('dt_de');
				$builder->where("customers.created >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate =  request()->getPost('dt_ate');
				$builder->where("customers.created <=", $dt_ate);
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("customers.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("customers.source_id", $flt_origem);
				} else {
					$builder->where("customers.source_id", $flt_origem);
				}
			}
			return intval($builder->get()->getRow()->total);
		}
		if ($report == "totalReunioes") {
			$leads = [];
			$datas = [];
			$total = 0;
			$builder = $this->db->table('leads_reunioes');
			$builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
			$builder->join('staff', 'staff.id = leads_reunioes.id_criador', 'inner');
			$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
			$builder->where('staff.id_company', session()->id_company);
			$builder->where('realizada', 'Realizada');
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de =  request()->getPost('dt_de');
				$builder->where("leads_reunioes.data >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate =  request()->getPost('dt_ate');
				$builder->where("leads_reunioes.data <=", $dt_ate);
			}
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("staff.id_company", $id_company);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil =  request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list", $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leads.source", $flt_origem);
				} else {
					$builder->where("leads.source", $flt_origem);
				}
			}
			foreach ($builder->get()->getResultArray() as $row) {
				if (!isset($datas[$row['data'] . '/' . $row['hora'] . '/' . $row['id_lead']])) {
					$total += 1;
					$datas[$row['data'] . '/' . $row['hora'] . '/' . $row['id_lead']] = 1;
				}
			}
			$builder = $this->db->table('leads_atv');
			$builder->join('leads', 'leads.id = leads_atv.id_lead', 'left');
			$builder->join('staff', 'staff.id = leads.assigned_id', 'inner');
			$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
			$builder->where("reuniao_call", "1");
			$builder->where("leads_atv.data <=", '2024-02-29');
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de =  request()->getPost('dt_de');
				$builder->where("leads_atv.data >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate =  request()->getPost('dt_ate');
				$builder->where("leads_atv.data <=", $dt_ate);
			}
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("staff.id_company", $id_company);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil =  request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list", $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leads.source", $flt_origem);
				} else {
					$builder->where("leads.source", $flt_origem);
				}
			}
			foreach ($builder->get()->getResultArray() as $row) {
				if (!isset($datas[$row['data'] . '/' . $row['horario'] . '/' . $row['id_lead']])) {
					$total += 1;
					$datas[$row['data'] . '/' . $row['horario'] . '/' . $row['id_lead']] = 1;
					$leads[] = ['id_lead' => $row['id_lead'], 'data' => $row['data'], 'hora' => $row['horario'], 'id_atividade' => $row['id_atividade']];
				}
			}
			return $total;
		}


		if ($report == "SetorDeAtividades") {
			$builder = $this->db->table('leads');
			$builder->select("count(*) as total, setor_atividade as label");
			$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
			$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
			$builder->where("setor_atividade is not null");
			$builder->where("setor_atividade != '0'");
			$builder->where("setor_atividade != ''");
			$builder->groupBy("setor_atividade");
			$builder->orderBy('setor_atividade', 'asc');
			$builder->limit(15);
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company = request()->getPost('id_company');
				$builder->where("staff.id_company = '$id_company'");
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de = request()->getPost('dt_de');
				$builder->where("leads.created >= '$dt_de'");
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate = request()->getPost('dt_ate');
				$builder->where("leads.created <= '$dt_ate'");
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil = request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list = '$flt_funil'");
			} else {
				if (session()->get('super_admin') != "1") {
					$builder->where('staff.id_company', session()->get('id_company'));
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario = request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id = '$flt_funcionario'");
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->where("leads.source in(" . implode(",", $flt_origem) . ")");
				} else {
					$builder->where("leads.source = '$flt_origem'");
				}
			}
			$data = $builder->get()->getResultArray();
			return $this->montaGraph($data, '#2196f3', true);
		}
		if ($report == "estados") {
			$builder = $this->db->table('leads');
			$builder->select("count(*) as total, state_id");
			$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
			$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
			$builder->where("state_id is not null");
			$builder->where("state_id != '0'");
			$builder->where("state_id != ''");
			$builder->groupBy("state_id");
			$builder->orderBy('state_id', 'asc');
			$builder->limit(15);
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company = request()->getPost('id_company');
				$builder->where("staff.id_company = '$id_company'");
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de = request()->getPost('dt_de');
				$builder->where("leads.created >= '$dt_de'");
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate = request()->getPost('dt_ate');
				$builder->where("leads.created <= '$dt_ate'");
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil = request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list = '$flt_funil'");
			} else {
				if (session()->get('super_admin') != "1") {
					$builder->where('staff.id_company', session()->get('id_company'));
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario = request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id = '$flt_funcionario'");
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->where("leads.source in(" . implode(",", $flt_origem) . ")");
				} else {
					$builder->where("leads.source = '$flt_origem'");
				}
			}
			$data = $builder->get()->getResultArray();
			foreach ($data as $index => $row) {
				$data[$index]['label'] = get_state_name('', $row['state_id']);
			}
			return $this->montaGraph($data, '#2196f3', true);
		}
		if ($report == "totalLeadsAtivos") {
			$builder = $this->db->table('leads');
			$builder->select('count(*) as total');
			$builder->where('lead_status_id', '1');
			$builder->where('(leads.lost = 0 or leads.lost is null)');
			$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
			$builder->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner');
			$builder->join('staff', 'leads.assigned_id = staff.id', 'inner');
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company = request()->getPost('id_company');
				$builder->where("staff.id_company = '$id_company'");
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de = request()->getPost('dt_de');
				$builder->where("leads.created >= '$dt_de'");
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate = request()->getPost('dt_ate');
				$builder->where("leads.created <= '$dt_ate'");
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil = request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list = '$flt_funil'");
			} else {
				if (session()->get('super_admin') != "1") {
					$builder->where('staff.id_company', session()->get('id_company'));
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario = request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id = '$flt_funcionario'");
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->where("leads.source in(" . implode(",", $flt_origem) . ")");
				} else {
					$builder->where("leads.source = '$flt_origem'");
				}
			}
			//$builder->get();
			//echo $this->db->getLastQuery();
			return intval($builder->get()->getRow()->total);
		}

		if ($report == "totalLeadsGerados") {
			$builder = $this->db->table('leads')
				->select('count(*) as total')
				->join('leadsstatus', 'leadsstatus.id = leads.status', 'left')
				->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'left')
				->join('leadssources', 'leads.source = leadssources.id', 'left')
				->join('staff', 'leads.assigned_id = staff.id', 'inner');

			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de =  request()->getPost('dt_de');
				$builder->where("leads.created >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate =  request()->getPost('dt_ate');
				$builder->where("leads.created <=", $dt_ate);
			}
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("staff.id_company", $id_company);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil =  request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list", $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leads.source", $flt_origem);
				} else {
					$builder->where("leads.source", $flt_origem);
				}
			}
			return intval($builder->get()->getRow()->total);
		}

		if ($report == "totalLeadsPipeline") {
			$builder = $this->db->table('leads')
				->select('count(*) as total')
				->join('leadsstatus', 'leads.status = leadsstatus.id', 'inner')
				->where("leadsstatus.name", 'pipeline')
				->where('lead_status_id', '1')
				->where('(leads.lost = 0 or leads.lost is null)')
				->join('staff', 'leads.assigned_id = staff.id', 'left')
				->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner');

			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("staff.id_company", $id_company);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil =  request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list", $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leads.source", $flt_origem);
				} else {
					$builder->where("leads.source", $flt_origem);
				}
			}
			return intval($builder->get()->getRow()->total);
		}

		if ($report == "totalLeadsEmDia") {
			$builder = $this->db->table('leads_atv')
				->select('count(*) as total')
				->join('leads', 'leads.id = leads_atv.id_lead', 'left')
				->join('staff', 'leads.assigned_id = staff.id', 'left')
				->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner')
				->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner')
				->where('leads_atv.retorno >=', date('Y-m-d'))
				->where('lead_status_id', '1')
				->where('(leads.lost = 0 or leads.lost is null)')
				->groupBy('leads_atv.id_lead');

			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("staff.id_company", $id_company);
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de =  request()->getPost('dt_de');
				$builder->where("leads.created >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate =  request()->getPost('dt_ate');
				$builder->where("leads.created <=", $dt_ate);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil =  request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list", $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leads.source", $flt_origem);
				} else {
					$builder->where("leads.source", $flt_origem);
				}
			}
			return $builder->countAllResults();
		}


		if ($report == "totalLeadsAtrasados") {
			$builder = $this->db->table('leads_atv')
				->select('MAX(retorno) as ultimoRetorno')

				->join('leads', 'leads.id = leads_atv.id_lead', 'inner')
				->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner')
				->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner')
				->join('staff', 'leads.assigned_id = staff.id', 'inner')
				->where('lead_status_id', '1')
				->where('dateconverted IS NULL ')
				->where('lost', '0')
				->groupBy('leads_atv.id_lead');

			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company = request()->getPost('id_company');
				$builder->where('staff.id_company', $id_company);
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de = request()->getPost('dt_de');
				$builder->where('leads.created >=', $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate = request()->getPost('dt_ate');
				$builder->where('leads.created <=', $dt_ate);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil = request()->getPost('flt_funil');
				$builder->where('leadsstatus.id_list', $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario = request()->getPost('flt_funcionario');
				$builder->where('leads.assigned_id', $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn('leads.source', $flt_origem);
				} else {
					$builder->where('leads.source', $flt_origem);
				}
			}

			$total = 0;
			foreach ($builder->get()->getResultArray() as $row) {
				if (strtotime($row['ultimoRetorno']) < strtotime(date('Y-m-d'))) {
					$total++;
				}
			}
			return intval($total);
		}

		if ($report == "totalAtividades") {
			$builder = $this->db->table('leads_atv')
				->select('count(*) as total')
				->join('leads', 'leads.id = leads_atv.id_lead', 'left')
				->join('staff', 'leads.assigned_id = staff.id', 'left')
				->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');

			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company = request()->getPost('id_company');
				$builder->where('staff.id_company', $id_company);
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de = request()->getPost('dt_de');
				$builder->where('leads.created >=', $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate = request()->getPost('dt_ate');
				$builder->where('leads.created <=', $dt_ate);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil = request()->getPost('flt_funil');
				$builder->where('leadsstatus.id_list', $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario = request()->getPost('flt_funcionario');
				$builder->where('leads.assigned_id', $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn('leads.source', $flt_origem);
				} else {
					$builder->where('leads.source', $flt_origem);
				}
			}

			return intval($builder->get()->getRow()->total);
		}

		if ($report == "nvOportunidade") {
			$builder = $this->db->table('leads')
				->select("count(*) as total, DATE_FORMAT(created, '%d/%m/%Y') as label")
				->join('staff', 'leads.assigned_id = staff.id', 'left')
				->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner')
				->groupBy('created')
				->orderBy('created', 'desc')
				->limit(15);

			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company = request()->getPost('id_company');
				$builder->where('staff.id_company', $id_company);
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de = request()->getPost('dt_de');
				$builder->where('leads.created >=', $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate = request()->getPost('dt_ate');
				$builder->where('leads.created <=', $dt_ate);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil = request()->getPost('flt_funil');
				$builder->where('leadsstatus.id_list', $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario = request()->getPost('flt_funcionario');
				$builder->where('leads.assigned_id', $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem = request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn('leads.source', $flt_origem);
				} else {
					$builder->where('leads.source', $flt_origem);
				}
			}

			return $this->montaGraph($builder->get()->getResultArray(), '#2196f3', true);
		}
		if ($report == "nvOportunidadeClientes") {
			$builder = $this->db->table('leads');
			$builder->select("count(*) as total, nm_company as label");
			$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'left');
			$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
			$builder->join('companies', 'companies.id_company = staff.id_company', 'inner');
			$builder->groupBy("companies.id_company");
			$builder->orderBy('total', 'asc');
			//$builder->limit(30);
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("staff.id_company", $id_company);
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de =  request()->getPost('dt_de');
				$builder->where("leads.created >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate =  request()->getPost('dt_ate');
				$builder->where("leads.created <=", $dt_ate);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil =  request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list", $flt_funil);
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leads.source", $flt_origem);
				} else {
					$builder->where("leads.source", $flt_origem);
				}
			}
			return $this->montaGraph($builder->get()->getResultArray(), '#2196f3', true);
		}
		if ($report == "nvOportunidadeMes") {
			$return = [];

			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$data1 = new DateTime(request()->getPost('dt_de'));
				$data2 = new DateTime(request()->getPost('dt_ate'));
			} else {
				$data1 = new DateTime(date('Y') . '-1');
				$data2 = new DateTime(date('Y-m'));
			}


			$intervalo = $data1->diff($data2)->m;
			for ($i = 0; $i <= $intervalo; $i++) {
				$data =  date('m/Y', strtotime($data2->format('Y-m-d') . " -$i month"));
				$builder = $this->db->table('leads');
				$builder->select("count(*) as total, DATE_FORMAT(created,'%m/%Y') as label");
				$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'left');
				$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
				$builder->where("DATE_FORMAT(created,'%m/%Y')", $data);
				$builder->groupBy("MONTH(created)");
				$builder->orderBy('created', 'desc');
				$builder->limit(8);
				if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
					$id_company =  request()->getPost('id_company');
					$builder->where("staff.id_company", $id_company);
				}
				if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
					$dt_de =  request()->getPost('dt_de');
					$builder->where("leads.created >=", $dt_de);
				}
				if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
					$dt_ate =  request()->getPost('dt_ate');
					$builder->where("leads.created <=", $dt_ate);
				}
				if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
					$flt_funil =  request()->getPost('flt_funil');
					$builder->where("leadsstatus.id_list", $flt_funil);
				} else {
					if (session()->super_admin != "1") {
						$builder->where('staff.id_company', session()->id_company);
					}
				}
				if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
					$flt_funcionario =  request()->getPost('flt_funcionario');
					$builder->where("leads.assigned_id", $flt_funcionario);
				}
				if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
					$flt_origem =  request()->getPost('flt_origem');
					if (is_array($flt_origem)) {
						$builder->whereIn("leads.source", $flt_origem);
					} else {
						$builder->where("leads.source", $flt_origem);
					}
				}
				$r = $builder->get()->getRowArray();
				if (!$r) {
					$r = ['label' => $data, 'total' => 0];
				}
				$return[] = $r;
			}

			/*
			$mes_de = explode('-', request()->getPost('dt_de'))[1];
			$mes_ate = explode('-', request()->getPost('dt_ate'))[1];
			$return_zero = [];
			for ($i = $mes_de; $i <= $mes_ate; $i++) {
				if ($r[($i - 1)]['mes_ref'] != $i) {
					$data =  date('m/Y', strtotime(request()->getPost('dt_de') . "$i + month"));
					$return_zero[($i - 1)] = ['total' => 0, 'label' => $data];
				}
			}
			*/
			return $this->montaGraph($return, '#2196f3', true);
		}

		if ($report == "graphOrigem") {
			$builder = $this->db->table('leads');
			$builder->select('count(*) as total, leadssources.name as label');
			$builder->join('leadssources', 'leadssources.id = leads.source', 'inner');
			$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
			$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
			$builder->groupBy("leads.source");
			$builder->orderBy('leadssources.name', 'asc');
			$builder->limit(8);
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("staff.id_company", $id_company);
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de =  request()->getPost('dt_de');
				$builder->where("leads.created >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate =  request()->getPost('dt_ate');
				$builder->where("leads.created <=", $dt_ate);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil =  request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list", $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leads.source", $flt_origem);
				} else {
					$builder->where("leads.source", $flt_origem);
				}
			}
			return $this->montaGraph($builder->get()->getResultArray(), "#8e44ad");
		}
		if ($report == "atv_graph") {
			$builder = $this->db->table('leads_atv');
			$builder->select('count(*) as total, nm_atividade_select as label');
			$builder->join('leads_atv_select', 'leads_atv_select.id_atv = leads_atv.atividade', 'inner');
			$builder->join('leads', 'leads.id = leads_atv.id_lead', 'left');
			$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
			$builder->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner');
			$builder->groupBy("atividade");
			$builder->orderBy('total', 'desc');
			$builder->limit(8);
			if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
				$id_company =  request()->getPost('id_company');
				$builder->where("staff.id_company", $id_company);
			}
			if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
				$dt_de =  request()->getPost('dt_de');
				$builder->where("leads_atv.data >=", $dt_de);
			}
			if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
				$dt_ate =  request()->getPost('dt_ate');
				$builder->where("leads_atv.data <=", $dt_ate);
			}
			if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
				$flt_funil =  request()->getPost('flt_funil');
				$builder->where("leadsstatus.id_list", $flt_funil);
			} else {
				if (session()->super_admin != "1") {
					$builder->where('staff.id_company', session()->id_company);
				}
			}
			if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
				$flt_funcionario =  request()->getPost('flt_funcionario');
				$builder->where("leads.assigned_id", $flt_funcionario);
			}
			if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
				$flt_origem =  request()->getPost('flt_origem');
				if (is_array($flt_origem)) {
					$builder->whereIn("leads.source", $flt_origem);
				} else {
					$builder->where("leads.source", $flt_origem);
				}
			}
			return $this->montaGraph($builder->get()->getResultArray(), "#f17d1a");
		}
	}
	public function getReports_closeTime()
	{
		$builder = $this->db->table('leads');
		$builder->select('AVG(DATEDIFF(leads.dateconverted, leads.dateassigned)) as total');
		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'inner');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->where("leads.dateconverted IS NOT NULL");

		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("leads.dateconverted >=", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("leads.dateconverted <=", $dt_ate);
		}
		if (request()->getPost('id_company') != null && request()->getPost('id_company') != "-1") {
			$id_company = request()->getPost('id_company');
			$builder->where("staff.id_company", $id_company);
		}
		if (request()->getPost('flt_funil') != null && request()->getPost('flt_funil') != "-1") {
			$flt_funil = request()->getPost('flt_funil');
			$builder->where("leadsstatus.id_list", $flt_funil);
		} else {
			if (session()->get('super_admin') != "1") {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
		}
		if (request()->getPost('flt_funcionario') != null && request()->getPost('flt_funcionario') != "-1") {
			$flt_funcionario = request()->getPost('flt_funcionario');
			$builder->where("leads.assigned_id", $flt_funcionario);
		}
		if (request()->getPost('flt_origem') != null && request()->getPost('flt_origem') != "-1") {
			$flt_origem = request()->getPost('flt_origem');
			if (is_array($flt_origem)) {
				$builder->whereIn("leads.source", $flt_origem);
			} else {
				$builder->where("leads.source", $flt_origem);
			}
		}

		return number_format($builder->get()->getRow()->total, 0, '.', '');
	}

	public function staff_sales_graph3()
	{
		$totalSalario = 0;
		$totalComissao = 0;
		$graph = [];
		$labels = [];
		$DataSalarios = [];
		$DataComissao = [];

		$id = request()->getPost('id');
		$dt_de = request()->getPost('dt_de');
		$dt_ate = request()->getPost('dt_ate');

		// Total Salário
		$builder = $this->db->table('expenses');
		$builder->select('SUM(amount) as total');
		$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
		$builder->where('expenses.staff_id', $id);
		$builder->where('expensecat.ctg_system', '1');
		if ($dt_de && $dt_de != "-1") {
			$builder->where("duedate >=", $dt_de);
		}
		if ($dt_ate && $dt_ate != "-1") {
			$builder->where("duedate <=", $dt_ate);
		}
		$totalSalario = $builder->get()->getRowArray()['total'];

		// Total Comissão
		$builder = $this->db->table('expenses');
		$builder->select('SUM(amount) as total');
		$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
		$builder->where('expensecat.ctg_system', '2');
		$builder->where('expenses.staff_id', $id);
		if ($dt_de && $dt_de != "-1") {
			$builder->where("duedate >=", $dt_de);
		}
		if ($dt_ate && $dt_ate != "-1") {
			$builder->where("duedate <=", $dt_ate);
		}
		$totalComissao = $builder->get()->getRowArray()['total'];

		// Salário por Mês
		$builder = $this->db->table('expenses');
		$builder->select("SUM(amount) as total, DATE_FORMAT(duedate, '%m/%Y') as duedate");
		$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
		$builder->where('expensecat.ctg_system', '1');
		$builder->where('expenses.staff_id', $id);
		$builder->groupBy("DATE_FORMAT(duedate, '%m/%Y')");
		if ($dt_de && $dt_de != "-1") {
			$builder->where("duedate >=", $dt_de);
		}
		if ($dt_ate && $dt_ate != "-1") {
			$builder->where("duedate <=", $dt_ate);
		}
		$salarios = $builder->get()->getResultArray();
		foreach ($salarios as $r) {
			$labels[] = $r['duedate'];
			$DataSalarios[] = $r['total'];
		}

		// Comissão por Mês
		$builder = $this->db->table('expenses');
		$builder->select("SUM(amount) as total, DATE_FORMAT(duedate, '%m/%Y') as duedate");
		$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
		$builder->where('expensecat.ctg_system IN (2, 3)');
		$builder->where('expenses.staff_id', $id);
		$builder->groupBy("DATE_FORMAT(duedate, '%m/%Y')");
		if ($dt_de && $dt_de != "-1") {
			$builder->where("duedate >=", $dt_de);
		}
		if ($dt_ate && $dt_ate != "-1") {
			$builder->where("duedate <=", $dt_ate);
		}
		$Comissoes = $builder->get()->getResultArray();
		foreach ($Comissoes as $r) {
			$DataComissao[] = $r['total'];
		}

		$graph = [
			'labels' => $labels,
			'datasets' => [
				[
					'backgroundColor' => '#C7CBD5',
					'data' => $DataSalarios,
					'label' => 'Salário'
				],
				[
					'backgroundColor' => '#ffe8a8',
					'data' => $DataComissao,
					'label' => 'Comissão e bônus'
				]
			]
		];

		return [
			'totalSalario' => $totalSalario,
			'totalComissao' => $totalComissao,
			'salarioEComissao' => $graph
		];
	}


	function getReports_top10Atvs()
	{
		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
		}
		for ($i2 = 0; $i2 <= $this->diasDatas($dt_de, $dt_ate); $i2++) {
			$dataAtual = date('Y-m-d', strtotime('-' . $i2 . ' days', strtotime(date($dt_ate))));
			$datas[] = $dataAtual;
		}
		$labels = [];
		$totalLogs = [];
		$datasets = [];
		foreach ($datas as $i => $data) {
			$builder = $this->db->table('logs');
			$builder->select('*, staff.id as id_staff');
			$builder->join('staff', 'logs.staff_id = staff.id', 'left');
			$builder->where('(key_log != "inactive" and key_log != "login" or key_log is null)');
			$builder->where('staff.id_company', session()->id_company);
			$builder->where("CAST(date AS DATE) = '" . $data . "'");
			$builder->orderBy('logs.id', 'asc');
			if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
				$funcionario = request()->getPost('funcionario');
				$builder->where("staff_id", $funcionario);
			}
			$logs = $builder->get()->getResultArray();
			$loginAnterior = "";
			if (count($logs) > 0) {
				foreach ($logs as $log) {
					if (!isset($totalLogs[$log['id_staff']])) {
						$totalLogs[$log['id_staff']] = [];
					}
					if (isset($totalLogs[$log['id_staff']][$i])) {
						$totalLogs[$log['id_staff']][$i] += 1;
					} else {
						$totalLogs[$log['id_staff']][$i] = 1;
					}
				}
			}
		}
		foreach ($totalLogs as $id => $valores) {
			$total = 0;
			$name = $this->db->table('staff')->where('id', $id)->get()->getRowArray()['staffname'];
			foreach ($valores as $v) {
				$total += $v;
			}
			$datasets[] = [
				'backgroundColor' => [$this->get_cor_aleatoria()],
				'data' => [$total],
				'label' => $name,
			];
			$labels[] = "Total";
		}
		$graphic = [
			'labels' => $labels,
			'datasets' => $datasets
		];
		return $graphic;
	}

	function getReports_TmpDiarioPorMes()
	{
		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
		}
		for ($i2 = 0; $i2 <= $this->diasDatas($dt_de, $dt_ate); $i2++) {
			$dataAtual = date('Y-m-d', strtotime('-' . $i2 . ' days', strtotime(date($dt_ate))));
			$datas[] = $dataAtual;
		}
		$totalLogs = [];
		foreach ($datas as $i => $data) {
			$builder = $this->db->table('logs');
			$builder->select('*, staff.id as id_staff');
			$builder->join('staff', 'logs.staff_id = staff.id', 'left');
			$builder->where('(key_log = "inactive" or key_log = "login" OR key_log = "operating")');
			$builder->where('staff.id_company', session()->id_company);
			$builder->where("CAST(date AS DATE) = '" . $data . "'");
			$builder->orderBy('logs.id', 'asc');
			if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
				$funcionario = request()->getPost('funcionario');
				$builder->where("staff_id", $funcionario);
			}
			$logs = $builder->get()->getResultArray();
			$loginAnterior = "";
			$labels[$i] = date(get_dateFormat(), strtotime($data));
			if (count($logs) > 0) {
				foreach ($logs as $log) {
					if (!isset($totalLogs[$log['id_staff']])) {
						$totalLogs[$log['id_staff']] = [];
					}
					if ($log['key_log'] == "login" && $loginAnterior == "") {
						$loginAnterior = $log['date'];
					} else if (
						($log['key_log'] == "inactive" || $log['key_log'] == "operating" || $log['key_log'] == "login") && $loginAnterior != ""
					) {
						$date = new DateTime($log['date']);
						$date2 = new DateTime($loginAnterior);
						$v = $date->diff($date2);
						$min = strlen($v->i) == 1 ? ("0" . $v->i) : $v->i;
						$hora = strlen($v->h) == 1 ? ("0" . $v->h) : $v->h;
						$seg = strlen($v->s) == 1 ? ("0" . $v->s) : $v->s;
						$totalSessao = $hora . ':' . $min . ':' . $seg;
						if (isset($totalLogs[$log['id_staff']][$i])) {
							$dt = new DateTime($totalLogs[$log['id_staff']][$i]);
							$parts = explode(':', $totalSessao);
							$interval = new DateInterval('PT' . (int)$parts[0] . 'H' . $parts[1] . 'M' . $parts[2] . 'S');
							$dt->add($interval);
							$totalI = $dt->format('H:i:s');
							$totalLogs[$log['id_staff']][$i] = $totalI;
						} else {
							$totalLogs[$log['id_staff']][$i] = $totalSessao;
						}
						if ($log['key_log'] == "operating") {
							$loginAnterior = $log['date'];
						} else {
							$loginAnterior = "";
						}
					}
				}
			}
		}
		foreach ($totalLogs as $id => $valores) {
			$val = [];
			foreach ($labels as $i => $v) {
				if (isset($valores[$i])) {
					$partes = explode(':', $valores[$i]);
					$val[$i] = $partes[0] > 0 ? $partes[0] : 0 . '.' . $partes[1];
				} else {
					$val[$i] = 0;
				}
			}
			$datasets[] = [
				'backgroundColor' => $this->get_cor_aleatoria(),
				'data' => $val,
				'label' => $this->db->table('staff')->where('id', $id)->get()->getRowArray()['staffname'],
			];
		}
		$graphic = [
			'labels' => $labels,
			'datasets' => $datasets
		];
		return $graphic;
	}

	function getReports_mediaTempoLogadoPorFuncionario()
	{
		$totalLogs = [];
		$loginAnterior = [];
		$dt_de = request()->getPost('dt_de');
		$dt_ate = request()->getPost('dt_ate');

		$builder = $this->db->table('logs');
		$builder->select('*, staff.id as id_staff');
		$builder->join('staff', 'logs.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('logs.id', 'asc');

		if ($funcionario = request()->getPost('funcionario')) {
			$builder->where("staff.id", $funcionario);
		}

		if ($dt_de) {
			$builder->where("CAST(logs.date AS DATE) >= '$dt_de'");
		}

		if ($dt_ate) {
			$builder->where("CAST(logs.date AS DATE) <= '$dt_ate'");
		}

		$logs = $builder->get()->getResultArray();

		foreach ($logs as $log) {
			$staffId = $log['staff_id'];
			if ($log['key_log'] == "login" || !isset($loginAnterior[$staffId]) || $loginAnterior[$staffId] == "") {
				$loginAnterior[$staffId] = $log['date'];
			} else if (
				($log['key_log'] == "inactive" || $log['key_log'] == "operating" || $log['key_log'] == "login" ||  $log['key_log'] == "") && (isset($loginAnterior[$staffId]) && $loginAnterior[$staffId] != "")
			) {
				$date = new DateTime($log['date']);
				$date2 = new DateTime($loginAnterior[$staffId]);
				$v = $date->diff($date2);
				$min = strlen($v->i) == 1 ? ("0" . $v->i) : $v->i;
				$hora = strlen($v->h) == 1 ? ("0" . $v->h) : $v->h;
				$seg = strlen($v->s) == 1 ? ("0" . $v->s) : $v->s;
				$totalSessao = $hora . ':' . $min . ':' . $seg;
				if (isset($totalLogs[$staffId])) {
					$dt = new DateTime($totalLogs[$staffId]);
					$parts = explode(':', $totalSessao);
					$interval = new DateInterval('PT' . (int)$parts[0] . 'H' . $parts[1] . 'M' . $parts[2] . 'S');
					$dt->add($interval);
					$totalI = $dt->format('H:i:s');
				}
				$totalLogs[$staffId] = isset($totalLogs[$staffId]) ? $totalI : $totalSessao;
				if ($log['key_log'] == "operating" || $log['key_log'] == "") {
					$loginAnterior[$staffId] = $log['date'];
				} else {
					$loginAnterior[$staffId] = "";
				}
			}
		}
		if (!isset($totalLogs[$staffId])) {
			$totalLogs[$staffId] = 0;
		}

		$data_inicio = new DateTime($dt_de);
		$data_fim = new DateTime($dt_ate);
		$dateInterval = $data_inicio->diff($data_fim);
		$totalDias = $dateInterval->days > 0 ? $dateInterval->days : 1;
		$data = [];
		foreach ($totalLogs as $index => $row) {
			$builder = $this->db->table('staff');
			$builder->select('staffname');
			$builder->where('staff.id', $index);
			$staffName = $builder->get()->getRow()->staffname;

			$data[] = [
				'total' => str_replace(":", "", date('H:i:s', (strtotime($row) / $totalDias))),
				'label' => $staffName,
				'color' => $this->get_cor_aleatoria()
			];
		}
		return $this->montaGraph($data, null, true);
	}

	public function leadsPorEmpresas()
	{
		$builder = $this->db->table('leads');
		$builder->select("count(leads.id) as total, companies.nm_company, companies.id_company");
		$builder->join('staff', 'staff.id = leads.staff_id', 'left');
		$builder->join('companies', 'companies.id_company = staff.id_company', 'left');
		$builder->orderBy('total', 'asc');
		$builder->groupBy('companies.id_company');
		return $builder->get()->getResultArray();
	}

	public function getReports_TempoLogadoPorFuncionario()
	{
		$totalLogs = '';
		$HorasOciosas = '';
		$loginAnterior = '';
		$InativoAnterior = '';
		$builder = $this->db->table('logs');
		$builder->select('*, staff.id as id_staff, logs.id as id_log');
		$builder->join('staff', 'logs.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('logs.id', 'asc');
		if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
			$funcionario = request()->getPost('funcionario');
			$builder->where('staff.id', $funcionario);
		}
		if (request()->getPost('dt_de') != null && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("DATE(logs.date) >= ", $dt_de);
		}
		if (request()->getPost('dt_ate') != null && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("DATE(logs.date) <= ", $dt_ate);
		}
		$logs = $builder->get()->getResultArray();
		if (count($logs) > 0) {
			foreach ($logs as $log) {
				if ($log['key_log'] == "login" || $loginAnterior == "") {
					$loginAnterior = $log['date'];
				} else if (
					($log['key_log'] == "operating" || $log['key_log'] == "login" || $log['key_log'] == "" || $log['key_log'] == "inactive") && $loginAnterior != ""
				) {
					if (date('Y-m-d', strtotime($log['date'])) != date('Y-m-d', strtotime($loginAnterior))) {
						$loginAnterior = $log['date'];
						continue;
					}
					$date = new DateTime($log['date']);
					$date2 = new DateTime($loginAnterior);
					$v = $date->diff($date2);
					$min = strlen($v->i) == 1 ? ("0" . $v->i) : $v->i;
					$hora = strlen($v->h) == 1 ? ("0" . $v->h) : $v->h;
					$seg = strlen($v->s) == 1 ? ("0" . $v->s) : $v->s;
					$totalSessao = $hora . ':' . $min . ':' . $seg;
					if ($log['key_log'] != "inactive") {
						if ($this->converterParaSegundos($totalSessao) > 600) {
							$totalSessaoInativo = $this->converterParaSegundos($totalSessao) - 600;
							if ($HorasOciosas != '') {
								$totalSessaoInativo = $this->formatarTempo($this->converterParaSegundos($HorasOciosas) + $totalSessaoInativo);
							} else {
								$totalSessaoInativo = $this->formatarTempo($totalSessaoInativo);
							}
							$HorasOciosas = $totalSessaoInativo;
						}
					}
					if ($totalLogs != '') {
						$totalI = $this->formatarTempo($this->converterParaSegundos($totalLogs) + $this->converterParaSegundos($totalSessao));
					}
					$totalLogs = $totalLogs != "" ? $totalI : $totalSessao;
					if ($log['key_log'] == "operating" || $log['key_log'] == "") {
						$loginAnterior = $log['date'];
					} else {
						$loginAnterior = "";
					}
				}
			}
		}
		if ($totalLogs == '') {
			$totalLogs = '00:00:00';
		}
		if ($HorasOciosas == '') {
			$HorasOciosas = '00:00:00';
		}
		return ['horasTrabalhadas' => $totalLogs, 'HorasOciosas' => $HorasOciosas, 'totalAcoes' => count($logs)];
	}

	public function converterParaSegundos($horas)
	{
		list($horas, $minutos, $segundos) = explode(':', $horas);
		return ($horas * 3600) + ($minutos * 60) + $segundos;
	}

	public function formatarTempo($segundos)
	{
		$horas = floor($segundos / 3600);
		$segundos %= 3600;
		$minutos = floor($segundos / 60);
		$segundos %= 60;
		return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
	}
	public function getReports_Rh()
	{
		$totalLogs = [];
		$totalGeral = 1;
		$dias = 1;
		$dataArr['totalTempGeral'] = '00:00:00';
		$dataArr['totalTempoAtivoSistem'] = '00:00:00';
		$dataArr['mediaDiariaTotal'] = 0;
		$dataArr['totalDeAtividade'] = 0;
		$dataArr['mediaDiaria'] = 0;
		$dataArr['totalLeads'] = 0;
		$dataArr['totalClientes'] = 0;

		$dt_de = request()->getPost('dt_de');
		$dt_ate = request()->getPost('dt_ate');

		if ($dt_de && $dt_ate && $dt_de != "-1" && $dt_ate != "-1") {
			$data_inicio = new DateTime($dt_de);
			$data_fim = new DateTime($dt_ate);
			$dateInterval = $data_inicio->diff($data_fim);
			$dias = $dateInterval->days > 0 ? $dateInterval->days : 1;
		}

		$datas = [];
		for ($i2 = 0; $i2 <= $this->diasDatas($dt_de, $dt_ate); $i2++) {
			$dataAtual = date('Y-m-d', strtotime('-' . $i2 . ' days', strtotime($dt_ate)));
			$datas[] = $dataAtual;
		}

		foreach ($datas as $i => $data) {
			$builder = $this->db->table('logs');
			$builder->select("count(*) as total");
			$builder->join('staff', 'logs.staff_id = staff.id', 'left');
			$builder->where('staff.id_company', session()->get('id_company'));
			$builder->where("DATE(logs.date) =", $data);
			$builder->orderBy('logs.id', 'asc');

			if (request()->getPost('funcionario') && request()->getPost('funcionario') != "-1") {
				$funcionario = request()->getPost('funcionario');
				$builder->where('staff.id', $funcionario);
			}

			$logs = $builder->get()->getRow()->total;
			$loginAnterior = "";
			$totalGeral += $logs;
			$labels[$i] = date(get_dateFormat(), strtotime($data));
		}

		$inline_graph = [];

		$partes = explode(':', $dataArr['totalTempGeral']);
		$segundos = $partes[0] * 3600 + $partes[1] * 60 + $partes[2];
		$dataArr['mediaDiariaTotal'] = gmdate("H:i:s", $segundos / $totalGeral);
		$dataArr['totalDeAtividade'] = $totalGeral;
		$dataArr['mediaDiaria'] = number_format($totalGeral / $dias, 0, '.', '');

		$builder = $this->db->table('leads_atv');
		$builder->select("count(distinct leads_atv.id_customer) as total");
		$builder->join('staff', 'leads_atv.id_criador = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('leads_atv.id_customer IS NOT NULL');

		if ($dt_de) {
			$builder->where("DATE(leads_atv.dt_entrada) >=", $dt_de);
		}
		if ($dt_ate) {
			$builder->where("DATE(leads_atv.dt_entrada) <=", $dt_ate);
		}
		if (request()->getPost('funcionario') && request()->getPost('funcionario') != "-1") {
			$flt_funcionario = request()->getPost('funcionario');
			$builder->where('leads_atv.id_criador', $flt_funcionario);
		}

		$dataArr['totalClientes'] = $builder->get()->getRow()->total;

		$builder = $this->db->table('leads_atv');
		$builder->select("count(distinct leads_atv.id_lead) as total");
		$builder->join('staff', 'leads_atv.id_criador = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('leads_atv.id_lead IS NOT NULL');

		if ($dt_de) {
			$builder->where("DATE(leads_atv.dt_entrada) >=", $dt_de);
		}
		if ($dt_ate) {
			$builder->where("DATE(leads_atv.dt_entrada) <=", $dt_ate);
		}
		if (request()->getPost('funcionario') && request()->getPost('funcionario') != "-1") {
			$flt_funcionario = request()->getPost('funcionario');
			$builder->where('leads_atv.id_criador', $flt_funcionario);
		}

		$dataArr['totalLeads'] = $builder->get()->getRow()->total;

		return $dataArr;
	}

	public function getReports_LeadsPorFuncionarios()
	{
		$builder = $this->db->table('leads_atv');
		$builder->select("count(distinct leads_atv.id_lead) as total, staffname as label, leads_atv.id_lead as id_lead");
		$builder->join('staff', 'leads_atv.id_criador = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('leads_atv.id_lead IS NOT NULL');
		$builder->groupBy('leads_atv.id_criador');

		if (request()->getPost('dt_de') && request()->getPost('dt_de') != "-1") {
			$dt_de = request()->getPost('dt_de');
			$builder->where("DATE(leads_atv.dt_entrada) >=", $dt_de);
		}
		if (request()->getPost('dt_ate') && request()->getPost('dt_ate') != "-1") {
			$dt_ate = request()->getPost('dt_ate');
			$builder->where("DATE(leads_atv.dt_entrada) <=", $dt_ate);
		}
		if (request()->getPost('funcionario') && request()->getPost('funcionario') != "-1") {
			$flt_funcionario = request()->getPost('funcionario');
			$builder->where('leads_atv.id_criador', $flt_funcionario);
		}

		$return = $builder->get()->getResultArray();
		foreach ($return as $index => $row) {
			$return[$index]['color'] = $this->get_cor_aleatoria();
		}
		usort($return, function ($a, $b) {
			return $a['total'] <=> $b['total'];
		});
		return $this->montaGraph($return, null, true);
	}

	public function get_cor_aleatoria()
	{
		$hex = array_merge(range(0, 9), range('A', 'F'));
		$cor = '#';
		while (strlen($cor) < 7) {
			$num = rand(0, 15);
			$cor .= $hex[$num];
		}
		return $cor;
	}

	public function getReports_faturamentoTotais()
	{
		// Faturamento Bruto
		$builder = $this->db->table('invoices');
		$builder->select('SUM(invoices.total) as total');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');

		if ($id_company = request()->getPost('id_company')) {
			$builder->where('staff.id_company', $id_company);
		} else {
			$builder->where('staff.id_company', session()->get('id_company'));
		}

		if ($ano = request()->getPost('ano')) {
			$builder->where("YEAR(invoices.duedate)", $ano);
		}

		if ($mes = request()->getPost('mes')) {
			$builder->where("MONTH(invoices.duedate)", $mes);
		}

		if ($produto = request()->getPost('produto')) {
			$builder->join('items', 'items.relation = invoices.id', 'left');
			$builder->where('items.product_id', $produto);
		}

		if ($vendedor = request()->getPost('vendedor')) {
			$builder->where('invoices.staff_id', $vendedor);
		}

		if ($situacao = request()->getPost('situacao')) {
			if ($situacao == "1") {
				$builder->where("((SELECT SUM(amount) FROM payments WHERE invoice_id = invoices.id) - invoices.total) <= 0");
			} else if ($situacao == "2") {
				$builder->where('invoices.status_id', 3);
			} else if ($situacao == "3") {
				$builder->where("invoices.duedate < CURRENT_DATE");
				$builder->where("((SELECT SUM(amount) FROM payments WHERE invoice_id = invoices.id) - invoices.total) > 0");
			}
		}

		$fatBruto = $builder->get()->getRow()->total;

		// Compras
		$builder = $this->db->table('purchases');
		$builder->select('SUM(purchases.total) as total');
		$builder->join('staff', 'purchases.staff_id = staff.id', 'left');

		if ($id_company = request()->getPost('id_company')) {
			$builder->where('staff.id_company', $id_company);
		} else {
			$builder->where('staff.id_company', session()->get('id_company'));
		}

		if ($ano = request()->getPost('ano')) {
			$builder->where("YEAR(purchases.duedate)", $ano);
		}

		if ($mes = request()->getPost('mes')) {
			$builder->where("MONTH(purchases.duedate)", $mes);
		}

		if ($produto = request()->getPost('produto')) {
			$builder->join('items', 'items.relation = purchases.id', 'left');
			$builder->where('items.product_id', $produto);
		}

		if ($vendedor = request()->getPost('vendedor')) {
			$builder->where('purchases.staff_id', $vendedor);
		}

		if ($situacao = request()->getPost('situacao')) {
			if ($situacao == "1") {
				$builder->where("((SELECT SUM(amount) FROM payments WHERE purchase_id = purchases.id) - purchases.total) <= 0");
			} else if ($situacao == "2") {
				$builder->where('purchases.status_id', 3);
			} else if ($situacao == "3") {
				$builder->where("purchases.duedate > CURRENT_DATE");
				$builder->where("((SELECT SUM(amount) FROM payments WHERE purchase_id = purchases.id) - purchases.total) > 0");
			}
		}

		$compras = $builder->get()->getRow()->total;

		// Despesas
		$builder = $this->db->table('expenses');
		$builder->select('SUM(expenses.amount) as total');
		$builder->where('expenses.purchase_id IS NULL');
		$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');

		if ($id_company = request()->getPost('id_company')) {
			$builder->where('staff.id_company', $id_company);
		} else {
			$builder->where('staff.id_company', session()->get('id_company'));
		}

		if ($ano = request()->getPost('ano')) {
			$builder->where("YEAR(expenses.duedate)", $ano);
		}

		if ($mes = request()->getPost('mes')) {
			$builder->where("MONTH(expenses.duedate)", $mes);
		}

		if ($produto = request()->getPost('produto')) {
			$builder->join('items', 'items.relation = expenses.id', 'left');
			$builder->where('items.product_id', $produto);
		}

		if ($vendedor = request()->getPost('vendedor')) {
			$builder->where('expenses.staff_id', $vendedor);
		}

		if ($situacao = request()->getPost('situacao')) {
			if ($situacao == "1") {
				$builder->where('expenses.invoice_id IS NOT NULL');
			} else if ($situacao == "2") {
				$builder->where('expenses.invoice_id IS NULL');
			} else if ($situacao == "3") {
				$builder->where('expenses.duedate > CURRENT_DATE');
				$builder->where('expenses.invoice_id IS NULL');
			}
		}

		$despesas = $builder->get()->getRow()->total;

		$totalDepsECompras = ($compras ?: 0) + ($despesas ?: 0);

		return [
			'fatBruto' => $fatBruto ?: 0,
			'totalDepsECompras' => $totalDepsECompras
		];
	}

	function getRecurring($data, $limit, $returnTotal = false)
	{
		$val = [];
		$total = 0;
		foreach ($data as $row) {
			if ($row['recurring_status'] == "") {
				$key = date('Ym', strtotime($row['duedate']));
				if (isset($val[$key])) {
					$v = $val[$key]['total'];
					$val[$key]['total'] = $v + $row['total'];
				} else {
					$val[$key] = [
						'total' => $row['total'],
						'label' => date('m/Y', strtotime($row['duedate']))
					];
				}
			} else if ($row['recurring_status'] == "0") {
				$builder = $this->db->table('recurring');
				$builder->join('invoices', 'invoices.id = recurring.relation', 'join');
				$builder->where('relation_type', 'invoice');
				$builder->where('relation', $row['invoice_id']);
				if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
					$ano = request()->getPost('ano');
					$builder->where("YEAR(invoices.duedate) = '$ano'");
				}
				if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
					$mes = request()->getPost('mes');
					$builder->where("MONTH(invoices.duedate) = '$mes'");
				} else {
					$mes = "-1";
				}
				if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
					$produto = request()->getPost('produto');
					$builder->join('items', 'items.relation = invoices.id', 'left');
					$builder->where("items.product_id = '$produto'");
				}
				if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
					$vendedor = request()->getPost('vendedor');
					$builder->where("invoices.staff_id = '$vendedor'");
				}
				if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
					if (request()->getPost('situacao') == "1") {
						$builder->where("((SELECT SUM(amount) AS total FROM payments WHERE invoice_id = invoices.id) - invoices.total) <= 0");
					} else if (request()->getPost('situacao') == "2") {
						$builder->where("invoices.status_id = 3");
					} else if (request()->getPost('situacao') == "3") {
						$builder->where("invoices.duedate < CAST(NOW() AS DATE)");
						$builder->where("((SELECT SUM(amount) AS total FROM payments WHERE invoice_id = invoices.id) - invoices.total) > 0");
					}
				}
				if ($limit != -1) {
					$builder->limit($limit);
				}
				$recurrings = $builder->get()->getResultArray();
				foreach ($recurrings as $recurring) {
					$key = date('Ym', strtotime($recurring['duedate']));
					if (isset($val[$key])) {
						$v = $val[$key]['total'];
						$val[$key]['total'] = $v + $recurring['total'];
						$total = $total + $recurring['total'];
					} else {
						if (count($val) < $limit) {
							if ($mes == "-1" || $mes == substr(date('Ym', strtotime($recurring['duedate'])), 0, 2)) {
								$val[$key] = [
									'total' => $recurring['total'],
									'label' => date('m/Y', strtotime($recurring['duedate']))
								];
								$total = $total + $recurring['total'];
							}
						}
					}
					for ($i = 1; $i <= $recurring['period']; $i++) {
						if ($recurring['type'] == "0") {
							$label = date('m/Y', strtotime("+" . $i . " days", strtotime($recurring['duedate'])));
							$index = date('Ym', strtotime("+" . $i . " days", strtotime($recurring['duedate'])));
						} else if ($recurring['type'] == "1") {
							$label = date('m/Y', strtotime("+" . $i . " week", strtotime($recurring['duedate'])));
							$index = date('Ym', strtotime("+" . $i . " week", strtotime($recurring['duedate'])));
						} else if ($recurring['type'] == "2") {
							$label = date('m/Y', strtotime("+" . $i . " month", strtotime($recurring['duedate'])));
							$index = date('Ym', strtotime("+" . $i . " month", strtotime($recurring['duedate'])));
						} else if ($recurring['type'] == "3") {
							$label = date('m/Y', strtotime("+" . $i . " year", strtotime($recurring['duedate'])));
							$index = date('Ym', strtotime("+" . $i . " year", strtotime($recurring['duedate'])));
						}
						if (isset($val[$label])) {
							$v = $val[$index]['total'];
							$val[$index]['total'] = $v + $recurring['total'];
							$total = $total + $recurring['total'];
						} else {
							if (count($val) < $limit) {
								if ($mes == "-1" || $mes == substr($index, 0, 2)) {
									$val[$index] = [
										'total' => $recurring['total'],
										'label' => $label
									];
									$total = $total + $recurring['total'];
								}
							}
						}
					}
				}
			}
		}
		if ($returnTotal) {
			return $total;
		} else {
			if (ksort($val)) {
				return $val;
			}
		}
	}

	function get_graphFaturamentoOrigem()
	{
		$builder = $this->db->table('invoices');
		$builder->distinct();
		$builder->select('SUM(invoices.total) AS total, leadssources.name AS label');
		$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		$builder->join('customers', 'customers.id = invoices.customer_id', 'left');
		$builder->join('leadssources', 'leadssources.id = customers.source_id', 'left');
		$builder->where('customers.source_id IS NOT NULL');
		$builder->where("customers.source_id != ''");
		if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
			$builder->where('staff.id_company', request()->getPost('id_company'));
		} else {
			$builder->where('staff.id_company', session()->id_company);
		}
		if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
			$ano = request()->getPost('ano');
			$builder->where("YEAR(invoices.duedate) = '$ano'");
		}
		if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
			$mes = request()->getPost('mes');
			$builder->where("MONTH(invoices.duedate) = '$mes'");
		}
		if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
			$produto = request()->getPost('produto');
			$builder->join('items', 'items.relation = invoices.id', 'left');
			$builder->where("items.product_id = '$produto'");
		}
		if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
			$vendedor = request()->getPost('vendedor');
			$builder->where("invoices.staff_id = '$vendedor'");
		}
		if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
			if (request()->getPost('situacao') == "1") {
				$builder->where("((SELECT SUM(amount) AS total FROM payments WHERE invoice_id = invoices.id) - invoices.total) <= 0");
			} else if (request()->getPost('situacao') == "2") {
				$builder->where("invoices.status_id = 3");
			} else if (request()->getPost('situacao') == "3") {
				$builder->where("invoices.duedate < CAST(NOW() AS DATE)");
				$builder->where("((SELECT SUM(amount) AS total FROM payments WHERE invoice_id = invoices.id) - invoices.total) > 0");
			}
		}
		$builder->groupBy('customers.source_id');
		$builder->orderBy('duedate', 'asc');
		$builder->limit(12);
		$data = $builder->get()->getResultArray();
		return $this->montaGraph($data, "#2196f3");
	}

	function getReportsInvoices($report)
	{
		if ($report == "faturamento") {
			$builder = $this->db->table('invoices');
			$builder->distinct();
			$builder->select('sum(invoices.total) as total, DATE_FORMAT(duedate, "%m/%Y") as label');
			$builder->join('staff', 'invoices.staff_id = staff.id', 'left');

			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->id_company);
			}

			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(invoices.duedate) = '$ano'");
			}

			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(invoices.duedate) = '$mes'");
			}

			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = invoices.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}

			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("invoices.staff_id = '$vendedor'");
			}

			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("((select sum(amount) as total from payments where invoice_id = invoices.id) - invoices.total) <= 0");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("invoices.status_id = 3");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("invoices.duedate < cast(now() as date)");
					$builder->where("((select sum(amount) as total from payments where invoice_id = invoices.id) - invoices.total) > 0");
				}
			}

			$builder->groupBy("DATE_FORMAT(duedate, '%m/%Y')");
			$builder->orderBy('duedate', 'asc');
			$builder->limit(12);
			$data = $builder->get()->getResultArray();
			return $this->montaGraph($data, "#2196f3");
		}

		if ($report == "faturamentoEDespesa") {
			$builder = $this->db->table('purchases');
			$builder->select('sum(purchases.total) as total, DATE_FORMAT(duedate, "%m/%Y") as label');
			$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
			} else {
				$ano = date('Y');
			}
			$builder->where("year(purchases.duedate) = '$ano'");
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(purchases.duedate) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = purchases.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("purchases.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("((select sum(amount) as total from payments where purchase_id = purchases.id) - purchases.total) <= 0");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("purchases.status_id = '3'");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("duedate > cast(now() as date)");
					$builder->where("((select sum(amount) as total from payments where purchase_id = purchases.id) - purchases.total) > 0");
				}
			}
			$builder->groupBy("DATE_FORMAT(duedate, '%m/%Y')");
			$builder->orderBy('duedate', 'desc');
			$compras = $builder->get()->getResultArray();

			$builder = $this->db->table('invoices');
			$builder->select('sum(invoices.total) as total, DATE_FORMAT(duedate, "%m/%Y") as label');
			$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(invoices.duedate) = '$ano'");
			} else {
				$ano = date('Y');
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(invoices.duedate) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = invoices.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("invoices.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("((select sum(amount) as total from payments where invoice_id = invoices.id) - invoices.total) <= 0");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("invoices.status_id = 3");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("invoices.duedate < cast(now() as date)");
					$builder->where("((select sum(amount) as total from payments where invoice_id = invoices.id)- invoices.total) > 0");
				}
			}

			$builder = $this->db->table('invoices');
			$builder->select('sum(invoices.total) as total, DATE_FORMAT(duedate, "%m/%Y") as label');
			$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(invoices.duedate) = '$ano'");
			} else {
				$ano = date('Y');
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(invoices.duedate) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = invoices.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("invoices.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("((select sum(amount) as total from payments where invoice_id = invoices.id) - invoices.total) <= 0");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("invoices.status_id = 3");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("invoices.duedate < cast(now() as date)");
					$builder->where("((select sum(amount) as total from payments where invoice_id = invoices.id) - invoices.total) > 0");
				}
			}
			$builder->groupBy("DATE_FORMAT(duedate, '%m/%Y')");
			$builder->orderBy('duedate', 'desc');
			$builder->limit(12);
			$fatura = $builder->get()->getResultArray();

			$builder = $this->db->table('expenses');
			$builder->distinct();
			$builder->select('sum(expenses.amount) as total, DATE_FORMAT(date, "%m/%Y") as label');
			$builder->where('expenses.purchase_id IS NULL');
			$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(expenses.date) = '$ano'");
			} else {
				$ano = date('Y');
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(expenses.date) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = expenses.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("expenses.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("expenses.invoice_id is not null");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("expenses.invoice_id is null");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("date > cast(now() as date)");
					$builder->where("expenses.invoice_id is null");
				}
			}
			$builder->groupBy("DATE_FORMAT(date, '%m/%Y')");
			$builder->orderBy('date', 'desc');
			$builder->limit(12);
			$despesa = $builder->get()->getResultArray();

			$dataFatura = [];
			$labelFatura = [];
			$dataDespesa = [];
			$labelDespesa = [];
			$months = [];
			foreach ($fatura as $row) {
				$dataFatura[intval(substr($row['label'], 0, 2)) - 1] = intval($row['total']);
				$labelFatura[] = $row['label'];
			}
			foreach ($despesa as $row) {
				$dataDespesa[intval(substr($row['label'], 0, 2)) - 1] = intval($row['total']);
				$labelDespesa[] = $row['label'];
			}
			foreach ($compras as $row) {
				if (isset($dataDespesa[intval(substr($row['label'], 0, 2)) - 1])) {
					$array = $dataDespesa[intval(substr($row['label'], 0, 2)) - 1];
					$dataDespesa[intval(substr($row['label'], 0, 2)) - 1] = intval($row['total']) + $array;
				} else {
					$dataDespesa[intval(substr($row['label'], 0, 2)) - 1] = intval($row['total']);
				}
			}
			for ($i = 1; $i <= 12; $i++) {
				$months[] = $i  . "/" . $ano;
			}
			$dataDespesa = array_replace([0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0], $dataDespesa);
			$dataFatura = array_replace([0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0], $dataFatura);
			$graph = [
				'labels' => $months,
				'datasets' => [
					[
						'label' => 'Faturas',
						'data' => $dataFatura,
						'backgroundColor' => '#187bcb',
						'borderColor' => 'transparent',
						'pointBackgroundColor' => '#FFFFFF',
						'lineTension' => '0.40',
					],
					[
						'label' => 'Despesas & Compras',
						'data' => $dataDespesa,
						'backgroundColor' => '#f17d1a',
						'borderColor' => 'transparent',
						'pointBackgroundColor' => '#FFFFFF',
						'lineTension' => '0.40',
					],
				],
			];
			return $graph;
		}
		if ($report == "despesasECompras") {
			// Consulta para Compras
			$builder = $this->db->table('purchases');
			$builder->distinct();
			$builder->select('sum(purchases.total) as total, DATE_FORMAT(duedate, "%m/%Y") as label');
			$builder->join('staff', 'purchases.staff_id = staff.id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(purchases.duedate) = '$ano'");
			} else {
				$ano = date('Y');
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(purchases.duedate) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = purchases.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("purchases.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("((select sum(amount) from payments where purchase_id = purchases.id) - purchases.total) <= 0");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("purchases.status_id = '3'");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("duedate > cast(now() as date)");
					$builder->where("((select sum(amount) from payments where purchase_id = purchases.id) - purchases.total) > 0");
				}
			}
			$builder->groupBy("DATE_FORMAT(duedate, '%m/%Y')");
			$builder->orderBy('duedate', 'desc');
			$builder->limit(12);
			$compras = $builder->get()->getResultArray();

			// Consulta para Despesas
			$builder = $this->db->table('expenses');
			$builder->distinct();
			$builder->select('sum(expenses.amount) as total, DATE_FORMAT(date, "%m/%Y") as label');
			$builder->where('expenses.purchase_id IS NULL');
			$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(expenses.date) = '$ano'");
			} else {
				$ano = date('Y');
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(expenses.date) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = expenses.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("expenses.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("expenses.invoice_id is not null");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("expenses.invoice_id is null");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("date > cast(now() as date)");
					$builder->where("expenses.invoice_id is null");
				}
			}
			$builder->groupBy("DATE_FORMAT(date, '%m/%Y')");
			$builder->orderBy('date', 'desc');
			$builder->limit(12);
			$despesa = $builder->get()->getResultArray();

			// Preparação dos Dados
			$dataCompra = [];
			$dataDespesa = [];
			$months = [];
			foreach ($compras as $row) {
				$dataCompra[intval(substr($row['label'], 0, 2)) - 1] = intval($row['total']);
			}
			foreach ($despesa as $row) {
				$dataDespesa[intval(substr($row['label'], 0, 2)) - 1] = intval($row['total']);
			}
			for ($i = 1; $i <= 12; $i++) {
				$months[] = $i  . "/" . $ano;
			}
			$dataCompra = array_replace(array_fill(0, 12, 0), $dataCompra);
			$dataDespesa = array_replace(array_fill(0, 12, 0), $dataDespesa);

			// Geração do Gráfico
			$graph = [
				'labels' => $months,
				'datasets' => [
					[
						'label' => 'Compras',
						'data' => $dataCompra,
						'backgroundColor' => '#f6c1638a',
						'borderColor' => 'transparent',
						'pointBackgroundColor' => '#FFFFFF',
						'lineTension' => '0.40',
					],
					[
						'label' => 'Despesas',
						'data' => $dataDespesa,
						'backgroundColor' => '#f17d1a',
						'borderColor' => 'transparent',
						'pointBackgroundColor' => '#FFFFFF',
						'lineTension' => '0.40',
					],
				],
			];
			return $graph;
		}

		if ($report == "receitasXDespesas") {
			// Consulta para Faturas (Receitas)
			$builder = $this->db->table('invoices');
			$builder->distinct();
			$builder->select('sum(invoices.total) as total, DATE_FORMAT(duedate, "%d/%m/%Y") as label');
			$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(duedate) = '$ano'");
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(duedate) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = invoices.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("invoices.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("((select sum(amount) from payments where invoice_id = invoices.id) - invoices.total) <= 0");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("invoices.status_id = 3");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("invoices.duedate < cast(now() as date)");
					$builder->where("((select sum(amount) from payments where invoice_id = invoices.id) - invoices.total) > 0");
				}
			}
			$builder->groupBy("DATE_FORMAT(duedate, '%d/%m/%Y')");
			$builder->orderBy('duedate', 'desc');
			$builder->limit(30);
			$faturas = $builder->get()->getResultArray();

			// Consulta para Despesas
			$builder = $this->db->table('expenses');
			$builder->distinct();
			$builder->select('sum(expenses.amount) as total, DATE_FORMAT(date, "%d/%m/%Y") as label');
			$builder->where('expenses.purchase_id IS NULL');
			$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(date) = '$ano'");
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(date) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = expenses.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("expenses.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("expenses.invoice_id is not null");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("expenses.invoice_id is null");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("date > cast(now() as date)");
					$builder->where("expenses.invoice_id is null");
				}
			}
			$builder->groupBy("DATE_FORMAT(date, '%d/%m/%Y')");
			$builder->orderBy('date', 'desc');
			$builder->limit(30);
			$despesas = $builder->get()->getResultArray();

			// Preparação dos Dados
			$dataFatura = [];
			$dataDespesa = [];
			$days = [];
			foreach ($faturas as $row) {
				$dataFatura[intval(substr($row['label'], 0, 2)) - 1] = intval($row['total']);
			}
			foreach ($despesas as $row) {
				$dataDespesa[intval(substr($row['label'], 0, 2)) - 1] = intval($row['total']);
			}
			for ($i = 1; $i <= date("t"); $i++) {
				$days[] = "Dia " . $i;
			}
			$dataFatura = array_replace(array_fill(0, date("t"), 0), $dataFatura);
			$dataDespesa = array_replace(array_fill(0, date("t"), 0), $dataDespesa);

			// Geração do Gráfico
			$graph = [
				'labels' => $days,
				'datasets' => [
					[
						'label' => 'Receitas',
						'data' => $dataFatura,
						'backgroundColor' => '#187bcb',
						'borderColor' => 'transparent',
						'pointBackgroundColor' => '#FFFFFF',
						'lineTension' => '0.40',
					],
					[
						'label' => 'Despesas',
						'data' => $dataDespesa,
						'backgroundColor' => '#f17d1a',
						'borderColor' => 'transparent',
						'pointBackgroundColor' => '#FFFFFF',
						'lineTension' => '0.40',
					],
				],
			];
			return $graph;
		}

		if ($report == "AnaliseDeDespesas") {
			// Consulta para Despesas
			$builder = $this->db->table('expenses');
			$builder->distinct();
			$builder->select('sum(expenses.amount) as total, expensecat.name as label');
			$builder->where('expenses.purchase_id IS NULL');
			$builder->join('staff', 'expenses.expense_created_by = staff.id', 'left');
			$builder->join('expensecat', 'expensecat.id = expenses.category_id', 'left');
			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('staff.id_company', request()->getPost('id_company'));
			} else {
				$builder->where('staff.id_company', session()->get('id_company'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("year(date) = '$ano'");
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
				$builder->where("MONTH(date) = '$mes'");
			}
			if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = expenses.id', 'left');
				$builder->where("items.product_id = '$produto'");
			}
			if (request()->getPost('vendedor') != null && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("expenses.staff_id = '$vendedor'");
			}
			if (request()->getPost('situacao') != null && request()->getPost('situacao') != "-1") {
				if (request()->getPost('situacao') == "1") {
					$builder->where("expenses.invoice_id is not null");
				} else if (request()->getPost('situacao') == "2") {
					$builder->where("expenses.invoice_id is null");
				} else if (request()->getPost('situacao') == "3") {
					$builder->where("date > cast(now() as date)");
					$builder->where("expenses.invoice_id is null");
				}
			}
			$builder->groupBy('expenses.category_id');
			$builder->orderBy('expensecat.name', 'asc');
			$builder->limit(30);
			$despesas = $builder->get()->getResultArray();

			// Geração do Gráfico
			return $this->montaGraph($despesas, "#3f51b5");
		}
	}
	function montaGraph($result,  $colors, $reverse = false, $label = "Total")
	{
		$graphic = array(
			'labels' => array(),
			'datasets' => array()
		);
		$_data = array();
		$_data['data'] = array();
		$_data['backgroundColor'] = array();
		$_data['hoverBackgroundColor'] = array();
		$labels = [];
		foreach ($result as $index => $row) {
			$labels[] = iconv('UTF-8', 'UTF-8//IGNORE', mb_convert_encoding(substr($row['label'], 0, 30), "UTF-8", "auto"));
			$_data['backgroundColor'][] = isset($row['color']) ? $row['color'] : $colors;
			$_data['data'][] = isset($row['totalG']) ? $row['totalG'] : $row['total'];
		}
		if ($reverse) {
			$_data['data'] =  array_reverse($_data['data']);
			$_data['backgroundColor'] =  array_reverse($_data['backgroundColor']);
			$graphic['labels'] = array_reverse($labels);
		} else {
			$_data['data'] =  $_data['data'];
			$graphic['labels'] = $labels;
		}
		$graphic['datasets'][] = $_data;
		$graphic['datasets'][0]['label'] = $label;
		return $graphic;
	}
	function getReportsDash($report)
	{
		if ($report == "metaVsResultado") {
			$dataRaiz = [];
			$datasets = [];
			$labels = [];
			$builder = $this->db->table('goals');
			$builder->where('id_company', session()->get('id_company'));
			if (request()->getPost('id_goal') != null && request()->getPost('id_goal') != "-1") {
				$builder->where('id_goal', request()->getPost('id_goal'));
			}
			if (request()->getPost('id_equipe') != null && request()->getPost('id_equipe') != "-1") {
				$builder->where('id_equipe', request()->getPost('id_equipe'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano =  request()->getPost('ano');
				$builder->where("YEAR(inicio) >= '$ano'");
				$builder->where("YEAR(fim) <= '$ano'");
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes =  request()->getPost('mes');
			} else {
				$mes = '-1';
			}
			$goals = $builder->get()->getResultArray();
			foreach ($goals as $meta) {
				$builder = $this->db->table('goals_definicoes');
				$builder->where('id_goal', $meta['id_goal']);
				$builder->orderBy('valor', 'asc');
				$definicao = $builder->get()->getRow();
				$data = [];
				$dataAtual = $meta['inicio'];
				$datas = [];
				$true = true;
				while ($true) {
					$dataFAtual = date('Y-m-d', strtotime('+' . (!empty($meta['periodo']) ?  $meta['periodo'] : 0) . ' days', strtotime($dataAtual)));
					if (strtotime($dataFAtual) > strtotime($meta['fim'])) {
						$dataFAtual = $meta['fim'];
					}
					if ($mes != '-1' && date('m', strtotime($dataAtual)) == $mes) {
						$datas[] = [
							'inicio' => $dataAtual,
							'fim' => $dataFAtual
						];
						$labels[] = date('d/m/Y', strtotime($dataAtual)) . ' ao ' . date('d/m/Y', strtotime($dataFAtual));
						$dataRaiz[] = 0;
					}
					if (strtotime($dataFAtual) >= strtotime($meta['fim'])) {
						$true = false;
					} else {
						$dataAtual =  $dataFAtual;
					}
				}
				if ($definicao) {
					foreach ($datas as $dataString) {
						$data[] = floatval($definicao->valor);
					}
				}
				$datasets[] = [
					'label' => "Meta - (" . $meta['nm_goal'] . ")",
					'data' => $data,
					'backgroundColor' => '#187bcb',
					'borderColor' => 'transparent',
					'pointBackgroundColor' => '#FFFFFF',
					'lineTension' => '0.40',
				];
				$builder = $this->db->table('goals_equipe');
				$builder->where('id_equipe', $meta['id_equipe']);
				$equipe = $builder->get()->getRow();
				$data = [];
				foreach ($datas as $dataString) {
					$total = 0;
					foreach (explode(",", $equipe->funcionarios) as $funcionario) {
						if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
							if ($funcionario != request()->getPost('funcionario')) {
								continue;
							}
						}
						if ($meta['tp_meta'] == '1') {
							$builder = $this->db->table('orders');
							$builder->select('count(total) as total, created');
							$builder->where('assigned', $funcionario);
							$builder->where("created >= '$dataString[inicio]'");
							$builder->where("created <= '$dataString[fim]'");
							$sqlExec = $builder->get();
						} else if ($meta['tp_meta'] == '2') {
							$builder = $this->db->table('customers');
							$builder->select('count(*) as total, created');
							$builder->where('staff_id', $funcionario);
							$builder->where("created >= '$dataString[inicio]'");
							$builder->where("created <= '$dataString[fim]'");
							$sqlExec = $builder->get();
						} else if ($meta['tp_meta'] == '3') {
							$builder = $this->db->table('products');
							$builder->select('count(*) as total, created');
							$builder->where('product_created_by', $funcionario);
							$builder->where("created >= '$dataString[inicio]'");
							$builder->where("created <= '$dataString[fim]'");
							if (request()->getPost('produto') != null && request()->getPost('produto') != "-1") {
								$builder->where('id', request()->getPost('produto'));
							}
							$sqlExec = $builder->get();
						} else if ($meta['tp_meta'] == '4') {
							$builder = $this->db->table('leads_atv');
							$builder->select('count(*) as total, data as created');
							$builder->where("assigned_id = '" . $funcionario . "'");
							$builder->join('leads', 'leads.id = leads_atv.id_lead', 'left');
							$builder->where("reuniao_call", "1");
							$builder->where("dt_entrada >= '$dataString[inicio]'");
							$builder->where("dt_entrada <= '$dataString[fim]'");
							$sqlExec = $builder->get();
						} else if ($meta['tp_meta'] == '5') {
							$builder = $this->db->table('proposals');
							$builder->select('count(*) as total, created');
							$builder->where('assigned', $funcionario);
							$builder->where("created >= '$dataString[inicio]'");
							$builder->where("created <= '$dataString[fim]'");
							$sqlExec = $builder->get();
						} else if ($meta['tp_meta'] == '6') {
							$builder = $this->db->table('invoices');
							$builder->select('sum(total) as total, duedate');
							$builder->where('staff_id', $funcionario);
							$builder->where("duedate >= '$dataString[inicio]'");
							$builder->where("duedate <= '$dataString[fim]'");
							$sqlExec = $builder->get();
						} else if ($meta['tp_meta'] == '7') {
						}
						$total += $sqlExec->getRow()->total;
					}
					$data[] = floatval($total);
				}
				$data = array_replace($dataRaiz, $data);
				$datasets[] = [
					'label' => "Resultado - (" . $meta['nm_goal'] . ")",
					'data' => $data,
					'backgroundColor' => '#187bcb',
					'borderColor' => 'transparent',
					'pointBackgroundColor' => '#FFFFFF',
					'lineTension' => '0.40',
				];
			}
			$graph = array(
				'labels' => $labels,
				'datasets' => $datasets
			);
			if (count($labels) == 0) {
				return ['datasets' => [], 'labels' => []];
			} else {
				return $graph;
			}
		} else if ($report == "PorcetAlcancados") {
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
			} else {
				$ano = date('Y');
			}
			$months = [];
			for ($i = 1; $i <= 12; $i++) {
				$months[] = $i . "/" . $ano;
			}
			$datasets = $this->getPorcetAlcancados($ano);
			$graph = [
				'labels' => $datasets['labels'],
				'datasets' => $datasets['datasets']
			];
			return $graph;
		} else if ($report == "comparatvAnoCorrentVsAnoPassado") {
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
			} else {
				$ano = date('Y');
			}
			$months = [];
			for ($i = 1; $i <= 12; $i++) {
				$months[] = 'Mes ' . $i;
			}
			$datasets = [];
			foreach ($this->getPorcetAlcancados($ano - 1) as $d) {
				$datasets[] = $d;
			}
			foreach ($this->getPorcetAlcancados($ano) as $d) {
				$datasets[] = $d;
			}
			$graph = [
				'labels' => $months,
				'datasets' => $datasets
			];
			return $graph;
		} else if ($report == "ControleBonifica") {
			$datasets = [];
			$builder = $this->db->table('goals');
			$builder->where('id_company', session()->id_company);
			if (request()->getPost('id_goal') != null && request()->getPost('id_goal') != "-1") {
				$builder->where('id_goal', request()->getPost('id_goal'));
			}
			if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
				//$builder->where('id_goal', request()->getPost('meta'));
			}
			if (request()->getPost('id_equipe') != null && request()->getPost('id_equipe') != "-1") {
				$builder->where('id_equipe', request()->getPost('id_equipe'));
			}
			if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("YEAR(inicio) >=", $ano);
				$builder->where("YEAR(fim) <=", $ano);
			} else {
				$ano = date('Y');
			}
			if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
				$mes = request()->getPost('mes');
			} else {
				$mes = '-1';
			}
			$goals = $builder->get()->getResultArray();
			foreach ($goals as $meta) {
				$definicoesBuilder = $this->db->table('goals_definicoes');
				$definicoesBuilder->where('id_goal', $meta['id_goal']);
				$definicoesBuilder->orderBy('valor', 'asc');
				$definicoes = $definicoesBuilder->get()->getResultArray();

				$equipeBuilder = $this->db->table('goals_equipe');
				$equipeBuilder->where('id_equipe', $meta['id_equipe']);
				$equipe = $equipeBuilder->get()->getRow();

				$resultados = [];
				foreach (explode(",", $equipe->funcionarios) as $funcionario) {
					if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
						if ($funcionario != request()->getPost('funcionario')) {
							continue;
						}
					}
					if ($meta['tp_meta'] == '1') {
						$sqlExec = $this->db->table('orders')
							->select('count(total) as total, created')
							->where('assigned', $funcionario)
							->where("created >=", $meta['inicio'])
							->where("created <=", $meta['fim'])
							->groupBy('created')
							->get();
					} else if ($meta['tp_meta'] == '2') {
						$sqlExec = $this->db->table('customers')
							->select('count(*) as total, created')
							->where('staff_id', $funcionario)
							->where("created >=", $meta['inicio'])
							->where("created <=", $meta['fim'])
							->groupBy('created')
							->get();
					} else if ($meta['tp_meta'] == '3') {
						$sqlExec = $this->db->table('products')
							->select('count(*) as total, created')
							->where('product_created_by', $funcionario)
							->where("created >=", $meta['inicio'])
							->where("created <=", $meta['fim'])
							->where('id', request()->getPost('produto'))
							->groupBy('created')
							->get();
					} else if ($meta['tp_meta'] == '4') {
						$sqlExec = $this->db->table('leads_atv')
							->select('count(*) as total, data as created')
							->where("id_criador", $funcionario)
							->orWhere("assigned_id", $funcionario)
							->join('leads', 'leads.id = leads_atv.id_lead', 'left')
							->where("reuniao_call", "1")
							->where("data >=", $meta['inicio'])
							->where("data <=", $meta['fim'])
							->groupBy('data')
							->get();
					} else if ($meta['tp_meta'] == '5') {
						$sqlExec = $this->db->table('proposals')
							->select('count(*) as total, created')
							->where('assigned', $funcionario)
							->where("created >=", $meta['inicio'])
							->where("created <=", $meta['fim'])
							->groupBy('created')
							->get();
					} else if ($meta['tp_meta'] == '6') {
						$sqlExec = $this->db->table('invoices')
							->select('sum(total) as total, duedate')
							->where('staff_id', $funcionario)
							->where("duedate >=", $meta['inicio'])
							->where("duedate <=", $meta['fim'])
							->groupBy('duedate')
							->get();
					}
					foreach ($sqlExec->getResultArray() as $row) {
						$dataV = 0;
						if (isset($row['created']) && isset($resultados[intval(explode('-', $row['created'])[1])])) {
							$dataV = $resultados[intval(explode('-', $row['created'])[1])];
						}
						if (isset($row['created'])) {
							$resultados[intval(explode('-', $row['created'])[1]) - 1] = $dataV + intval($row['total']);
						}
					}
				}
				$data = [];
				foreach ($definicoes as $definicao) {
					foreach ($resultados as $index => $resultado) {
						if ($resultado >= $definicao['valor']) {
							$dataV = 0;
							if (isset($data[$index])) {
								$dataV = $data[$index];
							}
							if ($definicao['tp_bonificacao'] == "R$") {
								$data[$index] = (isset($data[$index]) ? $data[$index] : 0) + $definicao['bonificacao'];
							} else {
								$data[$index] = (isset($data[$index]) ? $data[$index] : 0) + (($definicao['bonificacao'] * 100) / $resultado);
							}
						}
					}
				}
				$data = array_replace([0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0], $data);
				$datasets[] = [
					'label' => $meta['nm_goal'],
					'data' => $data,
					'backgroundColor' => '#187bcb',
					'borderColor' => 'transparent',
					'pointBackgroundColor' => '#FFFFFF',
					'lineTension' => '0.40',
				];
			}
			$months = [];
			for ($i = 1; $i <= 12; $i++) {
				$months[] = $i . "/" . $ano;
			}
			$graph = [
				'labels' => $months,
				'datasets' => $datasets
			];
			return $graph;
		}
	}
	function getPorcetAlcancados($ano)
	{
		$datasets = [];
		$labels = [];
		$dataRaiz = [];
		$data = [];

		$builder = $this->db->table('goals');
		$builder->where('id_company', session()->id_company);
		if (request()->getPost('id_goal') != null && request()->getPost('id_goal') != "-1") {
			$builder->where('id_goal', request()->getPost('id_goal'));
		}
		if (request()->getPost('id_equipe') != null && request()->getPost('id_equipe') != "-1") {
			$builder->where('id_equipe', request()->getPost('id_equipe'));
		}
		if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
			$ano = request()->getPost('ano');
			$builder->where("YEAR(inicio) >=", $ano);
			$builder->where("YEAR(fim) <=", $ano);
		}
		if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
			$mes = request()->getPost('mes');
		} else {
			$mes = '-1';
		}
		$goals = $builder->get()->getResultArray();

		foreach ($goals as $meta) {
			$meta['inicio'] = $ano . '-' . substr($meta['inicio'], -5);
			$meta['fim'] = $ano . '-' . substr($meta['fim'], -5);
			$dataAtual = $meta['inicio'];
			$datas = [];
			$true = true;
			while ($true) {
				$dataFAtual = date('Y-m-d', strtotime('+' . (!empty($meta['periodo']) ? $meta['periodo'] : 0) . ' days', strtotime($dataAtual)));
				if (strtotime($dataFAtual) > strtotime($meta['fim'])) {
					$dataFAtual = $meta['fim'];
				}
				if ($mes != '-1' && date('m', strtotime($dataAtual)) == $mes) {
					$datas[] = [
						'inicio' => $dataAtual,
						'fim' => $dataFAtual
					];
					$labels[] = date('d/m/Y', strtotime($dataAtual)) . ' ao ' . date('d/m/Y', strtotime($dataFAtual));
					$dataRaiz[] = 0;
				}
				if (strtotime($dataFAtual) >= strtotime($meta['fim'])) {
					$true = false;
				} else {
					$dataAtual = $dataFAtual;
				}
			}

			$definicoesBuilder = $this->db->table('goals_definicoes');
			$definicoesBuilder->where('id_goal', $meta['id_goal']);
			$definicoesBuilder->orderBy('valor', 'asc');
			$definicao = $definicoesBuilder->get()->getRow();

			$dataMeta = [];
			if ($definicao) {
				foreach ($datas as $dataString) {
					$dataMeta[] = floatval($definicao->valor);
				}
			}

			$equipeBuilder = $this->db->table('goals_equipe');
			$equipeBuilder->where('id_equipe', $meta['id_equipe']);
			$equipe = $equipeBuilder->get()->getRow();

			$dataResultado = [];
			foreach ($datas as $dataString) {
				$total = 0;
				foreach (explode(",", $equipe->funcionarios) as $funcionario) {
					if (request()->getPost('funcionario') != null && request()->getPost('funcionario') != "-1") {
						if ($funcionario != request()->getPost('funcionario')) {
							continue;
						}
					}
					if ($meta['tp_meta'] == '1') {
						$sqlExec = $this->db->table('orders')
							->select('count(total) as total')
							->where('assigned', $funcionario)
							->where("created >=", $dataString['inicio'])
							->where("created <=", $dataString['fim'])
							->get();
					} else if ($meta['tp_meta'] == '2') {
						$sqlExec = $this->db->table('customers')
							->select('count(*) as total')
							->where('staff_id', $funcionario)
							->where("created >=", $dataString['inicio'])
							->where("created <=", $dataString['fim'])
							->get();
					} else if ($meta['tp_meta'] == '3') {
						$sqlExec = $this->db->table('products')
							->select('count(*) as total')
							->where('product_created_by', $funcionario)
							->where("created >=", $dataString['inicio'])
							->where("created <=", $dataString['fim'])
							->get();
					} else if ($meta['tp_meta'] == '4') {
						$sqlExec = $this->db->table('leads_atv')
							->select('count(*) as total')
							->where("(id_criador = '" .  $funcionario . "' or assigned_id = '" . $funcionario . "')")
							->join('leads', 'leads.id = leads_atv.id_lead', 'left')
							->where("reuniao_call", "1")
							->where("data >=", $dataString['inicio'])
							->where("data <=", $dataString['fim'])
							->get();
					} else if ($meta['tp_meta'] == '5') {
						$sqlExec = $this->db->table('proposals')
							->select('count(*) as total')
							->where('assigned', $funcionario)
							->where("created >=", $dataString['inicio'])
							->where("created <=", $dataString['fim'])
							->get();
					} else if ($meta['tp_meta'] == '6') {
						$sqlExec = $this->db->table('invoices')
							->select('sum(total) as total')
							->where('staff_id', $funcionario)
							->where("duedate >=", $dataString['inicio'])
							->where("duedate <=", $dataString['fim'])
							->get();
					}
					$total += $sqlExec->getRow()->total;
				}
				$dataResultado[] = floatval($total);
				$dataPercentual = [];
				foreach ($dataMeta as $index => $metaArray) {
					$res = isset($dataResultado[$index]) ? $dataResultado[$index] : 0;
					$dataPercentual[$index] = floatval(number_format(($res * 100) / $metaArray, 2, '.', ''));
				}
			}
			$data = array_replace($dataRaiz, $dataPercentual);
			$datasets[] = [
				'label' => $meta['nm_goal'] . ' - ' . $ano,
				'data' => $data,
				'backgroundColor' => '#187bcb',
				'borderColor' => 'transparent',
				'pointBackgroundColor' => '#FFFFFF',
				'lineTension' => '0.40',
			];
		}

		if (count($labels) == 0) {
			return ['datasets' => [], 'labels' => []];
		} else {
			return ['datasets' => $datasets, 'labels' => $labels];
		}
	}

	function getReports_totaisCustomers()
	{
		$ano = "-1";
		if (request()->getPost('ano') != null) {
			$ano = request()->getPost('ano');
		} else {
			$ano = date('Y');
		}

		$builder = $this->db->table('customers');
		if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
			$builder->where('id_company', request()->getPost('id_company'));
		} else {
			$builder->where('id_company', session('id_company'));
		}
		$builder->where('dt_inactive IS NOT NULL');
		$builder->where('customer_status_id', '0');
		$totalCutomersInativos = $builder->countAllResults();

		$builder = $this->db->table('customers');
		if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
			$builder->where('id_company', request()->getPost('id_company'));
		} else {
			$builder->where('id_company', session('id_company'));
		}

		if ($ano == date('Y') || $ano == "-1") {
			if ($ano == date('Y')) {
				$builder->where('customer_status_id', '1');
			}
		} else {
			$builder->where('((year(dt_inactive) > ' . $ano . ' and year(created) <= ' . $ano . ') or dt_inactive IS NULL)');
		}
		$totalClientesAtivos = $builder->countAllResults();

		$builder = $this->db->table('customers');
		if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
			$builder->where('id_company', request()->getPost('id_company'));
		} else {
			$builder->where('id_company', session('id_company'));
		}
		$totalClientes = $builder->countAllResults();

		$totalLT = 0;
		$builder = $this->db->table('customers');
		$builder->select('*,
			DATEDIFF(dt_inactive, (SELECT created FROM invoices WHERE invoices.customer_id =  customers.id ORDER BY duedate asc LIMIT 1)) as diferenca,
			DATEDIFF(now(), (SELECT created FROM invoices WHERE invoices.customer_id =  customers.id ORDER BY duedate asc LIMIT 1)) as diferencaCreate,
			(SELECT sum(invoices.total) as total FROM invoices WHERE invoices.customer_id =  customers.id  ) as totalInvoices
		');
		if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
			$builder->where('id_company', request()->getPost('id_company'));
		} else {
			$builder->where('id_company', session('id_company'));
		}

		if (request()->getPost('ano')) {
			if (request()->getPost('ano') == date('Y')) {
				$builder->where('year(customers.created) <= ' . date('Y') . ' and (year(customers.dt_inactive) >= ' . date('Y') . ' or customers.dt_inactive IS NULL)');
			} else if (request()->getPost('ano') == "-1") {
				//$builder->where(' year(customers.created) <= ' . date('Y'));
			} else {
				$builder->where('year(customers.created) <= ' . request()->getPost('ano') . ' and (year(customers.dt_inactive) >= ' . request()->getPost('ano') . ' or customers.dt_inactive IS NULL)');
			}
		} else {
			$builder->where('year(customers.created) <= ' . date('Y') . ' and (year(customers.dt_inactive) >= ' . date('Y') . ' or customers.dt_inactive IS NULL)');
		}

		$query = $builder->get()->getResultArray();
		$totalLTNum = count($query);
		$totalFaturasClientesAtivos = 0;

		foreach ($query as $row) {
			$totalFaturasClientesAtivos += $row['totalInvoices'];

			if ($row['customer_status_id'] == '0' && $row['diferenca'] != null) {
				$totalLT = $totalLT + $row['diferenca'];
			} else if ($row['customer_status_id'] == '1') {
				$totalLT = $totalLT + $row['diferencaCreate'];
			} else {
				$totalLTNum--;
			}
		}

		$totalLTMes = number_format((($totalLT / $totalLTNum) / 30), 2, '.', '');
		$totalLTDias = '';
		if (isset(explode('.', $totalLTMes)[1])) {
			$b = (explode('.', $totalLTMes)[1] * 30) / 100;
			$totalLTMes = explode('.', $totalLTMes)[0];
			$totalLTDias = number_format($b, 0, '.', '');
		}

		return [
			'totalClientesAtivos' => $totalClientesAtivos,
			'totalLTMes' =>  $totalLTMes,
			'totalFaturasClientesAtivos' => $totalFaturasClientesAtivos,
			'totalLTDias' =>  $totalLTDias,
			'totalLTV' => number_format(($totalFaturasClientesAtivos / $totalClientes), 2, '.', ''),
			'totalClientesInativos' => $totalCutomersInativos,
			'totalClientes' => $totalClientes
		];
	}


	function getReportsCutomers($report)
	{
		$builder = $this->db->table('customers');
		$idCompany = request()->getPost('id_company') && !empty(request()->getPost('id_company')) && request()->getPost('id_company') != "-1" ? request()->getPost('id_company') : session()->id_company;
		$ano = request()->getPost('ano') ? request()->getPost('ano') : date('Y');

		if ($report == "FaturamentoTop10Custumers") {
			$builder = $this->db->table('invoices');
			$builder->distinct();
			$builder->select("SUM(invoices.total) as totalG, IF(namesurname IS NOT NULL and namesurname != '', namesurname, company) as label");
			$builder->join('customers', 'invoices.customer_id = customers.id', 'inner');
			$builder->join('staff', 'invoices.staff_id = staff.id', 'inner');

			if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
				$builder->where('customers.id_company', request()->getPost('id_company'));
			} else {
				$session = session();
				$builder->where('customers.id_company', $session->get('id_company'));
			}

			if (request()->getPost('ano') !== '' && request()->getPost('ano') != "-1") {
				$ano = request()->getPost('ano');
				$builder->where("YEAR(invoices.duedate)", $ano);
			}

			if (request()->getPost('produto') !== '' && request()->getPost('produto') != "-1") {
				$produto = request()->getPost('produto');
				$builder->join('items', 'items.relation = invoices.id', 'left');
				$builder->where("items.product_id", $produto);
			}

			if (request()->getPost('vendedor') !== '' && request()->getPost('vendedor') != "-1") {
				$vendedor = request()->getPost('vendedor');
				$builder->where("invoices.staff_id", $vendedor);
			}

			$builder->groupBy("customers.id");
			$builder->orderBy('totalG', 'desc');
			$builder->limit(12);

			$data = $builder->get()->getResultArray();

			//echo $this->db->getLastQuery();
			return $this->montaGraph($data, "#3f51b5");
		} else if ($report == "NovosClientes") {


			if (request()->getPost('dt_de') && request()->getPost('dt_de') != "-1") {
				$data1 = new DateTime(request()->getPost('dt_de'));
				$data2 = new DateTime(request()->getPost('dt_ate'));
			} else {
				$data1 = new DateTime('01-01-' . request()->getPost('ano'));
				$data2 = new DateTime('30-12-' . request()->getPost('ano'));
			}


			$intervalo = $data1->diff($data2)->m;
			$return = [];
			for ($i = 0; $i <= $intervalo; $i++) {
				$data = date('m/Y', strtotime(date('Y-m', strtotime($data2->format('Y-m-d'))) . " -$i month"));
				$builder->select('COUNT(*) AS total, DATE_FORMAT(created, "%m/%Y") AS label');
				$builder->where("DATE_FORMAT(created, '%m/%Y')", $data);
				$builder->groupBy('MONTH(created)');
				$builder->where('customers.id_company', $idCompany);

				$r = $builder->get()->getRowArray();


				
				if (!$r) {
					$r = ['label' => $data, 'total' => 0];
				}
				$return[] = $r;
			}
			return $this->montaGraph($return, "#4caf50", true);
		} else if ($report == "ClientesAtivos") {
			$data = [];
			$mesDoAno = ($ano == date('Y')) ? date('m') : 12;
			$builder->select('created, dt_inactive');
			$builder->where('customers.id_company', $idCompany);
			$dataSql = $builder->get()->getResultArray();

			
			for ($i = 1; $i <= $mesDoAno; $i++) {
				$total = 0;
				foreach ($dataSql as $row) {
					if ($row['created'] != "" && strtotime($row['created']) <= strtotime("$ano-$i-31")) {
						if ($row['dt_inactive'] == "" || strtotime($row['dt_inactive']) >= strtotime("$ano-$i-01")) {
							$total++;
						}
					}
				}
				$data[] = ['total' => $total, 'label' => "$i/$ano"];
			}
			return $this->montaGraph($data, "#00bcd4");
		} else if ($report == "estado") {
			$builder->select('COUNT(*) AS totalG, state_id');
			$builder->where('state_id IS NOT NULL');
			$builder->where("state_id !=", '0');
			$builder->where('customers.id_company', $idCompany);
			if ($ano != "-1") {
				$builder->where("YEAR(created)", $ano);
			}
			$builder->groupBy('state_id');
			$builder->orderBy('state_id', 'ASC');
			$data = $builder->get()->getResultArray();
			foreach ($data as $index => $row) {
				$data[$index]['label'] = get_state_name('', $row['state_id']);
			}
			return $this->montaGraph($data, "#4caf50");
		} else if ($report == "ramoDeAtividade") {
			$builder->select('COUNT(*) AS totalG, setor_atividade AS label');
			$builder->where('setor_atividade IS NOT NULL');
			$builder->where("setor_atividade !=", '0');
			$builder->where("setor_atividade !=", '');
			$builder->where('customers.id_company', $idCompany);
			if ($ano != "-1") {
				$builder->where("YEAR(created)", $ano);
			}
			$builder->groupBy('setor_atividade');
			$builder->orderBy('setor_atividade', 'ASC');
			$data = $builder->get()->getResultArray();

			usort($data, function ($a, $b) {
				return  $b['totalG'] <=> $a['totalG'];
			});


			return $this->montaGraph($data, "#4caf50");
		}
	}
}
