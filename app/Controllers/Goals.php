<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Goals extends BaseController
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
        $data['title'] = lang2('x_menu_goal');
        return view('goals/index', $data);
    }

    public function salva_meta()
    {
        $data = [
            'nm_goal' => request()->getPost('nm_goal'),
            'id_equipe' => request()->getPost('id_equipe'),
            'tp_meta' => request()->getPost('tp_meta'),
            'duracao' => request()->getPost('duracao'),
            'inicio' => request()->getPost('inicio'),
            'fim' => request()->getPost('fim'),
            'observacao' => request()->getPost('observacao'),
            'periodo' => request()->getPost('periodo'),
        ];

        $builder = $this->db->table('goals');

        if (request()->getPost('id_goal')) {
            $builder->where('id_goal', request()->getPost('id_goal'));
            $response = $builder->update($data);
            $id_goal = request()->getPost('id_goal');
        } else {
            $data['id_company'] = session()->id_company;
            $data['is_ativo'] = '1';
            $response = $builder->insert($data);
            $id_goal = $this->db->insertID();
        }

        if ($response) {
            $builder = $this->db->table('goals_definicoes');
            $builder->delete(['id_goal' => $id_goal]);

            if (request()->getPost('definicoes')) {
                foreach (request()->getPost('definicoes') as $definicao) {
                    $builder->insert([
                        'id_goal' => $id_goal,
                        'valor' => $definicao['valor'],
                        'bonificacao' => $definicao['bonificacao'],
                        'tp_bonificacao' => $definicao['tp_bonificacao'],
                    ]);
                }
            }
        }

        return response()->setJSON(['success' => $response]);
    }

    public function salva_equipe()
    {
        $funcionarios = implode(',', request()->getPost('equipe'));

        $data = [
            'nm_equipe' => request()->getPost('nm_equipe'),
            'funcionarios' => $funcionarios,
        ];

        $builder = $this->db->table('goals_equipe');

        if (request()->getPost('id_equipe')) {
            $builder->where('id_equipe', request()->getPost('id_equipe'));
            $response = $builder->update($data);
        } else {
            $data['id_company'] = session()->id_company;
            $data['is_ativo'] = '1';
            $response = $builder->insert($data);
        }

        return response()->setJSON(['success' => $response]);
    }



    public function remove_equipe()
    {
        $builder = $this->db->table('goals_equipe');
        $builder->where('id_equipe', request()->getPost('id_equipe'));
        $response = $builder->update(['is_ativo' => '0']);
        return response()->setJSON(['success' => $response]);
    }

    public function remove_goal()
    {
        $builder = $this->db->table('goals');
        $builder->where('id_goal', request()->getPost('id_goal'));
        $response = $builder->update(['is_ativo' => '0']);
        return response()->setJSON(['success' => $response]);
    }

    public function get_equipes()
    {
        $builder = $this->db->table('goals_equipe');
        $builder->where('id_company', session()->id_company);
        $builder->where('is_ativo', '1');
        $data = $builder->get()->getResultArray();

        $r = [];
        foreach ($data as $row) {
            $funcionarios = '';

            foreach (explode(',', $row['funcionarios']) as $f) {
                $staff = $this->Staff_Model->get_staff($f);
                $funcionarios .= $funcionarios == '' ? $staff['staffname'] : ',' . $staff['staffname'];
                $row['funcionarios_array'][] = ['id' => $staff['id'], 'text' => $staff['staffname']];
            }
            $row['funcionarios_list'] = $funcionarios;
            $r[] = $row;
        }

        return response()->setJSON($r);
    }

    public function get_metas()
    {
        $builder = $this->db->table('goals');
        $builder->where('goals.id_company', session()->id_company);
        $builder->where('goals.is_ativo', '1');
        $builder->join('goals_equipe', 'goals_equipe.id_equipe = goals.id_equipe', 'left');

        $data = $builder->get()->getResultArray();
        $res = [];
        foreach ($data as $row) {
            $builder = $this->db->table('goals_definicoes');
            $builder->where('id_goal', $row['id_goal']);
            $row['definicoes'] = $builder->get()->getResultArray();
            $res[] = $row;
        }
        return response()->setJSON($res);
    }
}
