<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Customer_sucess extends BaseController
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
        $data['title'] = lang2('customers');
        $data['settings'] = $this->Settings_Model->get_settings_ciuis();
        $data['payment'] = $this->Settings_Model->get_payment_gateway_data();
        return view('customer_sucess/index', $data);
    }


    public function get_funils()
    {
        $builder = $this->db->table('customers_funils');
        $builder->where('id_company', session()->get('id_company'));
        $builder->orderBy('id', 'desc');

        $results = $builder->get()->getResultArray();

        foreach ($results as $index => $d) {
            $builder = $this->db->table('customers_funils_etapas');
            $builder->where('id_company', session()->get('id_company'));
            $builder->where('id_funil', $d['id']);
            $builder->orderBy('ordem', 'asc');
            $e = $builder->get()->getResultArray();
        
            $results[$index]['etapas'] = $e;
        }

        return response()->setJSON($results);
    }

    public function add_funil()
    {
        if ($this->Privileges_Model->check_privilege('customers', 'create')) {
            if (request()->getPost()) {
                $params = [
                    'nm_funil' => request()->getPost('name'),
                    'id_company' => session()->get('id_company')
                ];

                $this->db->table('customers_funils')->insert($params);

                $this->db->table('logs')->insert([
                    'date' => date('Y-m-d H:i:s'),
                    'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
                        session()->get('staffname') . '</a> Criou um novo funil, ' . request()->getPost('name'),
                    'staff_id' => session()->get('usr_id'),
                ]);

                $data['message'] = lang2('status') . ' ' . lang2('addmessage');
                $data['success'] = true;
            } else {
                $data['success'] = false;
                $data['message'] = "false";
                return redirect()->to('leads/index');
            }
        } else {
            $data['success'] = false;
            $data['message'] = lang2('you_dont_have_permission');
        }

        return response()->setJSON($data);
    }

    public function update_funil($id)
    {
        if ($this->Privileges_Model->check_privilege('customers', 'edit')) {
            if (request()->getPost()) {
                $params = [
                    'nm_funil' => request()->getPost('name'),
                ];
                $this->db->table('customers_funils')->where('id', $id)->update($params);

                $this->db->table('logs')->insert([
                    'date' => date('Y-m-d H:i:s'),
                    'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
                        session()->get('staffname') . '</a> Atualizou o funil, ' . request()->getPost('name'),
                    'staff_id' => session()->get('usr_id'),
                ]);

                $data['message'] = 'Funil ' . lang2('updatemessage');
                $data['success'] = true;
                return response()->setJSON($data);
            }
        } else {
            $data['success'] = false;
            $data['message'] = lang2('you_dont_have_permission');
            return response()->setJSON($data);
        }
    }

    public function add_status()
    {
        if ($this->Privileges_Model->check_privilege('customers', 'create')) {
            if (request()->getPost()) {
                $params = [
                    'name' => request()->getPost('name'),
                    'id_funil' => request()->getPost('id_funil'),
                    'id_company' => session()->get('id_company')
                ];
                $this->db->table('customers_funils_etapas')->insert($params);

                $this->db->table('logs')->insert([
                    'date' => date('Y-m-d H:i:s'),
                    'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
                        session()->get('staffname') . '</a> Criou um novo status, ' . request()->getPost('name'),
                    'staff_id' => session()->get('usr_id'),
                ]);

                $data['message'] = lang2('status') . ' ' . lang2('addmessage');
                $data['success'] = true;
            } else {
                $data['success'] = false;
                $data['message'] = "false";
                return redirect()->to('leads/index');
            }
        } else {
            $data['success'] = false;
            $data['message'] = lang2('you_dont_have_permission');
        }

        return response()->setJSON($data);
    }

    public function remove_funil($id)
    {
        if ($this->Privileges_Model->check_privilege('customers', 'delete')) {
            $this->db->table('customers_funils')->delete(['id' => $id]);
            $this->db->table('customers_funils_etapas')->delete(['id_funil' => $id]);

            $this->db->table('logs')->insert([
                'date' => date('Y-m-d H:i:s'),
                'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
                    session()->get('staffname') . '</a> removeu o funil, ' . $id,
                'staff_id' => session()->get('usr_id'),
            ]);

            $data['message'] = lang2('lead') . ' Funil ' . lang2('deletemessage');
            $data['success'] = true;
            return response()->setJSON($data);
        } else {
            $data['success'] = false;
            $data['message'] = lang2('you_dont_have_permission');
            return response()->setJSON($data);
        }
    }


    public function update_status($id)
    {
        if ($this->Privileges_Model->check_privilege('customers', 'edit')) {

            $params = [
                'name' => request()->getPost('name'),
                'color' => request()->getPost('color'),
            ];
            $this->db->table('customers_funils_etapas')->where('id', $id)->update($params);

            $this->db->table('logs')->insert([
                'date' => date('Y-m-d H:i:s'),
                'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
                    session()->get('staffname') . '</a> Atualizou o status, ' . request()->getPost('name'),
                'staff_id' => session()->get('usr_id'),
            ]);

            $data['message'] = lang2('lead') . ' ' . lang2('status') . ' ' . lang2('updatemessage');
            $data['success'] = true;
            return response()->setJSON($data);
        } else {
            $data['success'] = false;
            $data['message'] = lang2('you_dont_have_permission');
            return response()->setJSON($data);
        }
    }

    public function remove_status($id)
    {
        if ($this->Privileges_Model->check_privilege('leads', 'delete')) {

            $this->db->table('customers_funils_etapas')->delete(['id' => $id]);

            $this->db->table('logs')->insert([
                'date' => date('Y-m-d H:i:s'),
                'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
                    session()->get('staffname') . '</a> removeu o status, ' . $id,
                'staff_id' => session()->get('usr_id'),
            ]);

            $data['message'] = lang2('lead') . ' ' . lang2('status') . ' ' . lang2('deletemessage');
            $data['success'] = true;
            return response()->setJSON($data);
        } else {
            $data['success'] = false;
            $data['message'] = lang2('you_dont_have_permission');
            return response()->setJSON($data);
        }
    }

    public function reorderList()
    {
        $i = 1;
        foreach (request()->getPost('order') as $row) {
            $this->db->table('customers_funils_etapas')->where('id', $row)->update(['ordem' => $i]);
            $i++;
        }
    }

    public function move_customer()
    {
        if ($this->Privileges_Model->check_privilege('customers', 'edit')) {

            $customer_id = request()->getPost('customer_id');
            $status_id = request()->getPost('status_id');
            $this->db->table('customers')->where('id', $customer_id)->update(['etapa' => $status_id]);

            $this->db->table('logs')->insert([
                'date' => date('Y-m-d H:i:s'),
                'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
                    session()->get('staffname') . '</a> moveu o cliente, ' . $customer_id,
                'staff_id' => session()->get('usr_id'),
            ]);

            return response()->setJSON(['success' => 200]);
        } else {
            session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
            return redirect()->to(base_url('customers_success'));
        }
    }
}
