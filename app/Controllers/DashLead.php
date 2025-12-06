<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class DashLead extends BaseController
{
	function __construct()
	{
		parent::loadModels();
	}


	function index()
	{
		$data['title'] = "Dashboard Lead";
		return view('dashLead/index', $data);
	}

	function getReports_qualificacoes()
	{
		$data = $this->Report_Model->getReportsQualificacoes();
		return response()->setJSON($data);
	}



	function getReports_closeTime()
	{
		$data = $this->Report_Model->getReports_closeTime();
		return response()->setJSON(["total" => $data]);
	}


	function getReports_leadsPorFunil()
	{
		$data = $this->Report_Model->getReports_leadsPorFunil();
		return response()->setJSON($data);
	}
}
