<?php
namespace App\Controllers;
defined('BASEPATH') or exit('No direct script access allowed');

class Panel extends BaseController
{
	function __construct()
	{
		parent::loadModels();
	}
	
	function index()
	{
		$data['user_data'] = get_user();
		$rebrand = load_config();
		$this->model('Privileges_Model');
		$data['title'] = 'Painel';
		$data['acessoMeta'] = $this->Privileges_Model->has_privilege('dashGoals');
		$data['acessoCliente'] = $this->Privileges_Model->has_privilege('customersDash');
		$data['acessoFinanceiro'] = $this->Privileges_Model->has_privilege('dashInvoices');

	

		if (date('H') >= 5 && date('H') < 12) {
			$data['saudacao'] = "Bom dia,";
		}else if (date('H') >= 12 && date('H') < 18) {
			$data['saudacao'] = "Bom tarde,";
		} else if (date('H') >= 18 && date('H') < 24) {
			$data['saudacao'] = "Boa noite,";
		} else if (date('H') >= 0 && date('H') < 5) {
			$data['saudacao'] = "Boa madrugada,";
		}


		if (session()->get('other')) {
			return view('panel/consultant-panel', $data);
		} else {
			return view('panel/index2', $data);
		}
	}

	function getReports_nvOportunidade()
	{
		$data = $this->Report_Model->getReports('nvOportunidade');
		return response()->setJSON($data);
	}

	function getReports_nvOportunidade_mes()
	{
		$data = $this->Report_Model->getReports('nvOportunidadeMes');
		return response()->setJSON($data);
	}

	function getReports_graphOrigem()
	{

		$data = $this->Report_Model->getReports('graphOrigem');
		return response()->setJSON($data);
	}

	function getReports_atv_graph()
	{

		$data = $this->Report_Model->getReports('atv_graph');


		return response()->setJSON($data);
	}

	function getReports_totalAtividades()
	{

		$data = $this->Report_Model->getReports('totalAtividades');
		return response()->setJSON($data);
	}
	function getReports_totalLeadsAtivos()
	{
		$data = $this->Report_Model->getReports('totalLeadsAtivos');
		return response()->setJSON($data);
	}

	function getReports_totalLeadsGerados()
	{
		$data = $this->Report_Model->getReports('totalLeadsGerados');
		return response()->setJSON($data);
	}


	function getReports_totalLeadsAtrasados()
	{

		$data = $this->Report_Model->getReports('totalLeadsAtrasados');
		return response()->setJSON($data);
	}
	function getReports_totalLeadsEmDia()
	{

		$data = $this->Report_Model->getReports('totalLeadsEmDia');
		return response()->setJSON($data);
	}
	function getReports_totalLeadsPipeline()
	{
		$data = $this->Report_Model->getReports('totalLeadsPipeline');
		return response()->setJSON($data);
	}

	function getReports_nvOportunidadePorCliente()
	{
		$data = $this->Report_Model->getReports('nvOportunidadeClientes');
		return response()->setJSON($data);
	}
	function getReports_graphEstados()
	{
		$data = $this->Report_Model->getReports('estados');
		return response()->setJSON($data);
	}
	function getReports_graphSetorDeAtividades()
	{
		$data = $this->Report_Model->getReports('SetorDeAtividades');
		return response()->setJSON($data);
	}
}
