<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class RelProdutividade extends BaseController
{
	function __construct()
	{
		parent::loadModels();
		$path = request()->getUri()->getSegment(1);
		if (!$this->Privileges_Model->has_privilege($path)) {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to('panel/');
		}
	}

	function index()
	{
		$data = [];
		$data['send_email'] = request()->getGet('send_email');
		$data['title'] = lang2('Relatório de produtividade');
		return view('RelProdutividade/index', $data);
	}

	function getReports_all()
	{
		$data = $this->Report_Model->getReports_produtividade();
		return response()->setJSON($data);
	}

	function getReports_TempoLogadoPorFuncionario()
	{
		$data = $this->Report_Model->getReports_TempoLogadoPorFuncionario();
		return response()->setJSON($data);
	}

	function leadsPorEmpresas()
	{
		$data = $this->Report_Model->leadsPorEmpresas();
		return response()->setJSON($data);
	}

	function getLeadsGerados()
	{
		$builder = $this->db->table('leads');
		$builder->select('leads.*');
		$builder->join('staff', 'leads.staff_id = staff.id', 'inner');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('assigned_id', request()->getPost('funcionario'));

		if (request()->getPost('dt_de')) {
			$builder->where('leads.created >=', request()->getPost('dt_de'));
		}
		if (request()->getPost('dt_ate')) {
			$builder->where('leads.created <=', request()->getPost('dt_ate'));
		}

		$data = $builder->get()->getResultArray();
		return response()->setJSON($data);
	}

	function getClientesSemLead()
	{
		$builder = $this->db->table('customers');
		$builder->select('customers.*');
		$builder->join('staff', 'customers.staff_id = staff.id', 'inner');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('assigned_id', request()->getPost('funcionario'));
		$builder->where('id_lead IS NULL');

		if (request()->getPost('dt_de')) {
			$builder->where('customers.created >=', request()->getPost('dt_de'));
		}
		if (request()->getPost('dt_ate')) {
			$builder->where('customers.created <=', request()->getPost('dt_ate'));
		}

		$data = $builder->get()->getResultArray();
		return response()->setJSON($data);
	}
}
