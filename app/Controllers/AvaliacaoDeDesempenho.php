<?php
namespace App\Controllers;
if (!defined('BASEPATH')) exit('No direct script access allowed');

class AvaliacaoDeDesempenho extends BaseController
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

        return view('avalDeDesempenho/index', []);
    }
}
