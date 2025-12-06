<?php
namespace App\Controllers;
defined('BASEPATH') or exit('No direct script access allowed');

class customersDash extends BaseController
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
		$data['title'] = lang2('x_menu_dash_customers');
		return view('dashCustumers/index', $data);
	}

	function getReports_totaisCustomers()
	{
		$data = $this->Report_Model->getReports_totaisCustomers();
		return response()->setJSON($data);
	}

	function getReports_graphFaturamentoTop10()
	{
		$data = $this->Report_Model->getReportsCutomers('FaturamentoTop10Custumers');
		return response()->setJSON($data);
	}
	function getReports_graphNovosClientes()
	{
		$data = $this->Report_Model->getReportsCutomers('NovosClientes');
		return response()->setJSON($data);
	}
	function getReports_graphClientesAtivos()
	{
		$data = $this->Report_Model->getReportsCutomers('ClientesAtivos');
		return response()->setJSON($data);
	}

	function getReports_estado()
	{
		$data = $this->Report_Model->getReportsCutomers('estado');
		return response()->setJSON($data);
	}
	function getReports_ramoDeAtividade()
	{
		$data = $this->Report_Model->getReportsCutomers('ramoDeAtividade');
		return response()->setJSON($data);
	}
	function getQuantidadeDeReunioes()
	{
		$data = $this->Report_Model->getQuantidadeDeReunioes();
		return response()->setJSON($data);
	}
}
