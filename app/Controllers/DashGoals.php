<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class DashGoals extends BaseController
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
		$data['title'] = lang2('x_menu_dash_goals');
		return view('dashGoals/index', $data);
	}


	function getReports_metaVsResultado()
	{
		$data = $this->Report_Model->getReportsDash('metaVsResultado');
		return response()->setJSON($data);
	}
	function getReports_PorcetAlcancados()
	{
		$data = $this->Report_Model->getReportsDash('PorcetAlcancados');
		return response()->setJSON($data);
	}
	function getReports_comparatvAnoCorrentVsAnoPassado()
	{
		$data = $this->Report_Model->getReportsDash('comparatvAnoCorrentVsAnoPassado');
		return response()->setJSON($data);
	}
	function getReports_graphControleBonifica()
	{
		$data = $this->Report_Model->getReportsDash('ControleBonifica');
		return response()->setJSON($data);
	}
}
