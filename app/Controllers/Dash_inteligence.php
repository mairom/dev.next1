<?php
namespace App\Controllers;
defined('BASEPATH') or exit('No direct script access allowed');

class Dash_inteligence extends BaseController
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
        $data['title'] = "Dashboard Business Inteligence";
        return view('dash_inteligence/index', $data);
    }

    function getPerfReuniaoMensal()
    {
        $data = $this->Report_Model->getPerfReuniaoMensal();
        return response()->setJSON($data);
    }

    function getReports_reuniaoAgendadaxRealizada()
    {
        $data = $this->Report_Model->getReports_reuniaoAgendadaxRealizada();
        return response()->setJSON($data);
    }

    function getReports_LeasxReunioesPorOrigem()
    {
        $data = $this->Report_Model->getReports_LeasxReunioesPorOrigem();
        return response()->setJSON($data);
    }

    function getReports_reunioes()
    {
        $data = $this->Report_Model->getReports_reunioes();
        return response()->setJSON($data);
    }

    function getReports_reunioesRealizadas()
    {
        $data = $this->Report_Model->getReports_reunioesRealizadas();
        return response()->setJSON($data);
    }

    

    function getReports_ClientesxReunioesPorOrigem()
    {
        $data = $this->Report_Model->getReports_ClientesxReunioesPorOrigem();
        return response()->setJSON($data);
    }

    function getReports_reunioesPorFuncionarios()
    {
        $data = $this->Report_Model->getReports_reunioesPorFuncionarios();
        return response()->setJSON($data);
    }

    function getReports_graphReunioesRealizadasFunil()
    {
        $data = $this->Report_Model->getReports_graphReunioesRealizadasFunil();
        return response()->setJSON($data);
    }

    function getReports_reunioesAgendadas()
    {
        $data = $this->Report_Model->getReports_reunioesAgendadas();
        return response()->setJSON($data);
    }

    function getReports_QuantidadeLeads()
    {
        $data = $this->Report_Model->getReports_QuantidadeLeads();
        return response()->setJSON($data);
    }

    function getReports_ConversoesPorFuncionario()
    {
        $data = $this->Report_Model->getReports_ConversoesPorFuncionario();
        return response()->setJSON($data);
    }

    function getReports_Conversoes()
    {
        $data = $this->Report_Model->getReports_Conversoes();
        return response()->setJSON($data);
    }

    function getReports_reunioesxConversoes()
    {
        $data = $this->Report_Model->getReports_reunioesxConversoes();
        return response()->setJSON($data);
    }

    function getReports_ConversoesPorCloser()
    {
        $data = $this->Report_Model->getReports_ConversoesPorCloser();
        return response()->setJSON($data);
    }

    function getReports_graphLeadsAcionados()
    {
        $data = $this->Report_Model->getReports_graphLeadsAcionados();
        return response()->setJSON($data);
    }


    function getTotaisPainel()
    {
        $data = [];
        $data['totalLeadsGerados'] = $this->Report_Model->getReports('totalLeadsGerados');
        $data['totalReunioes'] = $this->Report_Model->getReports('totalReunioes');
        $data['totalClientes'] = $this->Report_Model->getReports('totalClientes');

        return response()->setJSON($data);
    }
}
