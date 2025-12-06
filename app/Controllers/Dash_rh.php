<?php
namespace App\Controllers;
defined('BASEPATH') or exit('No direct script access allowed');

class Dash_rh extends BaseController
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
		return view('dash_rh/index', $data);
	}

	function getReports_all()
	{
		$data = $this->Report_Model->getReports_Rh();
		return response()->setJSON($data);
	}

	function getReports_TmpDiarioPorMes()
	{
		$data = $this->Report_Model->getReports_TmpDiarioPorMes();
		return response()->setJSON($data);
	}

	function getReports_LeadsPorFuncionarios()
	{
		$data = $this->Report_Model->getReports_LeadsPorFuncionarios();
		return response()->setJSON($data);
	}

	function getReports_ClientesPorFuncionario()
	{
		$data = $this->Report_Model->getReports_ClientesPorFuncionario();
		return response()->setJSON($data);
	}

	function getReports_AtvDiariaFunc()
	{
		$data = $this->Report_Model->getReports_AtvDiariaFunc();
		return response()->setJSON($data);
	}

	function getReports_mediaTempoLogadoPorFuncionario()
	{
		$data = $this->Report_Model->getReports_mediaTempoLogadoPorFuncionario();
		return response()->setJSON($data);
	}
	

	function getReports_MediaAtvSistem()
	{
		$data = $this->Report_Model->getReports_MediaAtvSistem();
		return response()->setJSON($data);
	}

	function getReports_top10Atvs()
	{
		$data = $this->Report_Model->getReports_top10Atvs();
		return response()->setJSON($data);
	}

}
