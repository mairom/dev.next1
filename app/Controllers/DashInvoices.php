<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class DashInvoices extends BaseController
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
		$data['title'] = lang2('x_menu_dash_invoices');
		return view('dashInvoices/index', $data);
	}
	function getReports_faturamentoTotais()
	{
		$data = $this->Report_Model->getReports_faturamentoTotais();
		return response()->setJSON($data);
	}

	function getReports_graphFaturamento()
	{
		$data = $this->Report_Model->getReportsInvoices('faturamento');
		return response()->setJSON($data);
	}

	function getReports_graphFaturamentoehDespesa()
	{
		$data = $this->Report_Model->getReportsInvoices('faturamentoEDespesa');
		return response()->setJSON($data);
	}

	function getReports_graphFaturamentoOrigem()
	{
		$data = $this->Report_Model->get_graphFaturamentoOrigem();
		return response()->setJSON($data);
	}


	function getReports_graphDespesasECompras()
	{
		$data = $this->Report_Model->getReportsInvoices('despesasECompras');
		return response()->setJSON($data);
	}
	function getReports_graphreceitasEDespesas()
	{
		$data = $this->Report_Model->getReportsInvoices('receitasXDespesas');
		return response()->setJSON($data);
	}
	function getReports_graphAnaliseDeDespesas()
	{
		$data = $this->Report_Model->getReportsInvoices('AnaliseDeDespesas');
		return response()->setJSON($data);
	}
	function getReports_graphLucroLiquido()
	{
		$data = $this->Report_Model->getReportsInvoices('LucroLiquido');
		return response()->setJSON($data);
	}
}
