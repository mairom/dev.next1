<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_flow extends BaseController
{
    public $letras = [
        '1' => "A", '2' => "B", '3' => "C", '4' => "D", '5' => "E", '6' => "F", '7' => "G", '8' => "H",
        '9' => "I", '10' => "J", '11' => "K", '12' => "L", '13' => "M", '14' => "N", '15' => "O", '16' => "P",
        '17' => "Q", '18' => "R", '19' => "S", '20' => "T", '21' => "U", '22' => "V", '23' => "W", '24' => "X", '25' => "Y", '26' => "Z"
    ];

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
        $data['title'] = lang2('Sales flow');

        return view('sales_flow/index', $data);
    }

    function fluxos()
    {
        $fluxos = $this->db->table('fluxos')->where('id_company', session()->id_company)->get()->getResultArray();
        foreach ($fluxos as $index => $fluxo) {
            $etapas = $this->db->table('fluxos_etapas')->select('*, fluxos_etapas.id_etapa as id_etapa_original')->where('id_fluxo', $fluxo['id_fluxo'])->orderBy('cast(numero as unsigned integer)', 'asc')->where('id_etapa_pai IS NULL')->get()->getResultArray();
            $etapasArr = $this->getFluxosEtapas($etapas);

            $fluxos[$index]['etapas'] = $etapasArr['etapas'];
            $fluxos[$index]['funil'] = explode(",", $fluxo['funil']);
            $fluxos[$index]['funcionario'] = explode(",", $fluxo['funcionario']);
            $fluxos[$index]['origem'] = explode(",", $fluxo['origem']);
        }
        return response()->setJSON($fluxos);
    }

    function get_fluxos_manual()
    {
        $fluxos = $this->db->table('fluxos')
            ->where('id_company', session()->id_company)
            ->where('automatico', '0')
            ->get()->getResultArray();
        return response()->setJSON($fluxos);
    }

    function getFluxosEtapas($etapas, $camadas = 0)
    {
        foreach ($etapas as $i => $etapa) {
            $etapas[$i]['dias'] = intval($etapa['dias']);
            $etapas[$i]['atividade'] = $this->db->table('leads_atv_select')->getWhere(['id_atv' => $etapa['id_atividade']])->getRowArray();

            $etapasFilho = $this->db->table('fluxos_etapas')->select('*, fluxos_etapas.id_etapa as id_etapa_original')->where('id_etapa_pai', $etapa['id_etapa'])->get()->getResultArray();

            if (count($etapasFilho) > 0) {
                $etapasArr = $this->getFluxosEtapas($etapasFilho, $camadas + 1);
                $etapas[$i]['etapas'] = $etapasArr['etapas'];
                $etapas[$i]['camadas'] = $etapasArr['camadas'];
            }
        }
        return ['etapas' => $etapas, 'camadas' => $camadas];
    }

    function remove_etapa($id)
    {
        $this->db->table('fluxos_etapas')->where('id_etapa', $id)->delete();
        return response()->setJSON(['success' => 200]);
    }

    function limpaSalesFlowRemovidos()
    {
        $leads = $this->db->table('leads')
            ->where('id_ultimaEtapaAutomatico is not null')
            ->get()->getResultArray();

        foreach ($leads as $lead) {

            $fluxos_etapas = $this->db->table('fluxos_etapas')
                ->select('id_etapa')
                ->where('id_etapa', $lead['id_ultimaEtapaAutomatico'])
                ->get()->getRowArray();
            if (!$fluxos_etapas) {
                $this->db->table('leads')->where('id', $lead['id'])->update([
                    'id_ultimaEtapaAutomatico' => null,
                    'ultimoFlowAutomatico' => null,
                    'ultimoVerificacaoFlowAutomatico' => null
                ]);
            }
        }



        return response()->setJSON(['success' => 200]);
    }

    function remove_flow($id)
    {
        $this->db->table('fluxos')->where('id_fluxo', $id)->delete();

        $fluxos = $this->db->table('fluxos_etapas')
            ->where('id_fluxo', $id)
            ->where('automatico', '1')
            ->get()->getResultArray();

        foreach ($fluxos as $fluxo) {
            $this->db->table('leads')->where('id_ultimaEtapaAutomatico', $fluxo['id_etapa'])->update([
                'id_ultimaEtapaAutomatico' => null
            ]);
        }

        $this->db->table('fluxos_etapas')->where('id_fluxo', $id)->delete();

        return response()->setJSON(['success' => 200]);
    }

    function SalvaFluxo()
    {
        $fluxo = request()->getPost('fluxo');

        if ($fluxo) {
            $fluxo = json_decode($fluxo, true); // transforma em array
        }

        if ($fluxo['etapas']) {
            // print_r($fluxo['etapas']);
            //  exit;
            $this->db->table('fluxos_etapas')->where('id_fluxo', $fluxo['id_fluxo'])->delete();
            $this->SalvaFluxoArr($fluxo['etapas'], $fluxo);
        }

        $file = request()->getFile('file');


        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/etapas', $newName);

            $this->db->table('fluxos_anexos')->insert([
                'id_fluxo' => $fluxo['id_fluxo'],
                'file_name' => $file->getClientName(),
                'file_path' => 'uploads/etapas/' . $newName,
                'uploaded_at' => date('Y-m-d H:i:s')
            ]);
        }

        $data['success'] = true;
        return response()->setJSON($data);
    }

    function SalvaFluxoArr($etapas, $fluxo, $id_etapa_pai = null, $indexPai = '')
    {
        foreach ($etapas as $index => $etapa) {
            $indexNv = $indexPai . ($indexPai != '' ? '-' : '') . (strlen($id_etapa_pai) > 2 ? $this->letras[$index + 1] : $index + 1);

            $this->db->table('fluxos_etapas')->insert([
                'id_etapa' => isset($etapa['id_etapa_original']) ? $etapa['id_etapa_original'] : null,
                'id_fluxo' => $fluxo['id_fluxo'],
                'id_etapa_pai' => $id_etapa_pai,
                'descricao' => $etapa['descricao'] ?? null,
                'campoCopiar' => $etapa['campoCopiar'] ?? null,
                'email' => isset($etapa['email']) ? $etapa['email'] : null,
                'usar_ia' => isset($etapa['usar_ia']) ? $etapa['usar_ia'] : null,
                'id_atividade' =>  $etapa['id_atividade'] ?? null,
                'dias' =>  $etapa['dias'] ?? null,

                'cc' =>   isset($etapa['cc']) ? $etapa['cc'] : null,
                'cco' =>   isset($etapa['cco']) ? $etapa['cco'] : null,

                'canal' =>  isset($etapa['canal']) ? $etapa['canal'] : null,
                'responsavel' =>   isset($etapa['responsavel']) ? $etapa['responsavel'] : null,
                'automatico' =>   isset($etapa['automatico']) ? $etapa['automatico'] : null,
                'enviar_para' =>   isset($etapa['enviar_para']) ? $etapa['enviar_para'] : null,

                'seguir_para' =>  isset($etapa['seguir_para']) ? $etapa['seguir_para'] : null,
                'numero' => $id_etapa_pai == '' ? ($index + 1) : $indexNv
            ]);

            if (isset($etapa['etapas'])) {
                $this->SalvaFluxoArr($etapa['etapas'], $fluxo, $this->db->insertID(), $indexNv);
            }
        }
    }

    function create()
    {
        if ($this->Privileges_Model->check_privilege('sales_flow', 'create')) {
            $data['message'] = '';
            $hasError = false;
            if (!$hasError) {


                $params = [
                    'created' => date('Y-m-d H:i:s'),
                    'name' => request()->getPost('name') ?: null,
                    'funil' => request()->getPost('funil') ? implode(",", request()->getPost('funil')) : null,
                    'automatico' => request()->getPost('automatico'),
                    'funcionario' => request()->getPost('funcionario') ? implode(",", request()->getPost('funcionario')) : null,
                    'origem' => request()->getPost('origem') ? implode(",", request()->getPost('origem')) : null,
                    'id_company' => session()->id_company
                ];

                if (request()->getPost('id_fluxo')) {
                    $id_fluxo = request()->getPost('id_fluxo');
                    $this->db->table('fluxos')->where('id_fluxo', $id_fluxo)->update($params);
                    $this->db->table('fluxos_etapas')->where('id_fluxo', $id_fluxo)->delete();
                } else {
                    $this->db->table('fluxos')->insert($params);
                    $id_fluxo = $this->db->insertID();
                }

                if (request()->getPost('etapas')) {
                    $this->SalvaFluxoArr(request()->getPost('etapas'), ['id_fluxo' => $id_fluxo]);
                }

                $data['success'] = true;
                $data['message'] = lang2('Sales flow') . ' ' . lang2('createmessage');
                return response()->setJSON($data);
            }
        } else {
            $data['success'] = false;
            $data['message'] = lang2('you_dont_have_permission');
            return response()->setJSON($data);
        }
    }
}
