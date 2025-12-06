<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Controle extends BaseController
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
        $data['title'] =  lang2('Controle financeiro');


        return view('controle/index', $data);
    }

    function atualizaConta()
    {
        $data = [];

        $builder = $this->db->table('companies_faturas');

        // Filtros de ano
        if (request()->getPost('ano') != null && request()->getPost('ano') != "-1") {
            $ano = request()->getPost('ano');
            $builder->where("YEAR(companies_faturas.data) =", $ano);
        }

        // Filtros de mês
        if (request()->getPost('mes') != null && request()->getPost('mes') != "-1") {
            $mes = request()->getPost('mes');
            $builder->where("MONTH(companies_faturas.data) =", $mes);
        }

        // Filtro de id_company
        if (request()->getPost('id_company') && request()->getPost('id_company') != "-1") {
            $builder->where('companies_faturas.id_company', request()->getPost('id_company'));
        }

        // Join e consulta
        $builder->join('companies', 'companies.id_company = companies_faturas.id_company', 'left');
        $data['faturas'] = $builder->get()->getResultArray();


        return response()->setJSON($data);
    }
}
