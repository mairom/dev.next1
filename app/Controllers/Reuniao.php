<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Reuniao extends BaseController
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
        $data['title'] =  lang2('Reuniões');
        $data['id_user'] =  session()->usr_id;

        return view('reuniao/index', $data);
    }

    function remove_reuniao()
    {

        if ($this->Privileges_Model->check_privilege('reuniao', 'delete')) {
            $id_reuniao = request()->getPost('id_reuniao');


            $response = $this->db->table('leads_reunioes')->delete(['id_reuniao' => $id_reuniao]);
            if (request()->getPost('id_atividade')) {
                $id_atividade = request()->getPost('id_atividade');
                $response2 = $this->db->table('leads_atv')->delete(['id_atividade' => $id_atividade]);
            }


            $this->db->table('logs')->insert(array(
                'date' => date('Y-m-d H:i:s'),
                'detail' =>  '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
                    session()->staffname . '</a> removeu uma reuniao, ' . $id_reuniao,
                'staff_id' => session()->usr_id,
            ));

            $data['message'] = ' reunião ' . lang2('deletemessage');
            $data['result'] = true;
            return response()->setJSON($data);
        } else {
            $data['result'] = false;
            $data['message'] = lang2('you_dont_have_permission');
            return response()->setJSON($data);
        }
    }


    function get_reunioes()
    {
        $builder = $this->db->table('leads_reunioes');

        $builder->select(
            "leads.*, leads_reunioes.*, DATE_FORMAT(leads_reunioes.hora, '%H:%i') as hora_format, IF(leads.name = '', leads.company, leads.name) as nm_lead,
            customers.company as nm_customer, customers.phone as phone_c, customers.email as email_c"
        );
        $builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'left');
        $builder->join('customers', 'customers.id = leads_reunioes.id_customer', 'left');
        $builder->join('staff', 'staff.id = leads_reunioes.id_funcionario', 'inner');
        $builder->where('staff.id_company', session()->id_company);
        $builder->orderBy('leads_reunioes.hora', 'asc');

        $id_funcionario = request()->getPost('id_funcionario');
        if ($id_funcionario && $id_funcionario != '-1') {
            $builder->where('leads_reunioes.id_funcionario', $id_funcionario);
        }

        $month = request()->getPost('month');
        if ($month && $month != '-1') {
            $builder->where('MONTH(leads_reunioes.data)', ($month + 1));
        }

        $year = request()->getPost('year');
        if ($year && $year != '-1') {
            $builder->where('YEAR(leads_reunioes.data)', $year);
        }

        $leads_reunioes = $builder->get()->getResultArray();

        $data = [];
        foreach ($leads_reunioes as $row) {
            $data[$row['data']][] = $row;
        }

        return response()->setJSON($data);
    }

    function salva_reuniao2()
    {
        $id_funcionario = request()->getPost('id_funcionario');
        $data2 = request()->getPost('data');
        $hora = request()->getPost('hora');

        $this->db->table('leads_reunioes')->insert([
            'id_funcionario' => $id_funcionario,
            'data' => $data2,
            'hora' => $hora,
            'confirmado' => 1
        ]);


        $data = [];
        $data['message'] = lang2('lead') . ' ' . lang2('status') . ' ' . lang2('updatemessage');
        $data['success'] = true;

        return response()->setJSON($data);
    }


    function salva_reuniao()
    {
        $id_reuniao = request()->getPost('id_reuniao');
        $realizada = request()->getPost('realizada');
        $problema = request()->getPost('problema');
        $motivo = request()->getPost('motivo');

        // Atualiza a reunião
        $this->db->table('leads_reunioes')
            ->where('id_reuniao', $id_reuniao)
            ->update([
                'realizada' => $realizada,
                'problema' => $problema,
                'motivo' => $motivo,
                'confirmado' => 1
            ]);

        // Obtém os detalhes da reunião atualizada
        $reuniao = $this->db->table('leads_reunioes')
            ->where('id_reuniao', $id_reuniao)
            ->get()
            ->getRow();

        $closer = "";
        foreach (explode(',', $reuniao->id_funcionario) as $user) {
            $staff = $this->Staff_Model->get_name_staff($user);
            $closer .= ($closer != "" ? ', ' : '') . $staff['staffname'];
        }

        $matrizAtv = [
            'data' => $reuniao->data,
            'horario' => $reuniao->hora,
            'anotacoes' => 'Reunião agendada: ' . $reuniao->observacao . ' <br> ' .
                '<p><b>Data</b> ' . date('d/m/Y', strtotime($reuniao->data)) . ' ' . $reuniao->hora . ' </p>' .
                '<p><b>Closer:</b> ' . $closer . ' </p>' .
                '<p><b>Realizada?</b> ' . $realizada . ' </p>' .
                ($problema ? '<p><b>Problema:</b> ' . $problema . ' </p>' : '') .
                ($motivo ? '<p><b>Motivo:</b> ' . $motivo . ' </p>' : ''),
            'retorno' => $reuniao->data,
            'dt_entrada' => date('Y-m-d H:i:s'),
            'id_criador' => session()->get('usr_id'),
            'is_reuniao' => '1',
            'id_reuniao' => $reuniao->id_reuniao,
            'id_lead' => $reuniao->id_lead,
            'atividade' => $reuniao->tipo == "Presencial" ? "2" : "3"
        ];

        // Insere a nova atividade
        $this->db->table('leads_atv')->insert($matrizAtv);

        $data['message'] = lang2('lead') . ' ' . lang2('status') . ' ' . lang2('updatemessage');
        $data['success'] = true;

        return response()->setJSON($data);
    }
}
