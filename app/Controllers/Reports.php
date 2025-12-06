<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends BaseController
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

        $data['title'] = "Reports";
        return view('reports/index', $data);
    }

    function getLista()
    {
        $data = [];
        if (request()->getPost('tipo') == "reunioes") {
            $builder = $this->db->table('leads_reunioes');

            $builder->select(
                '*, leads.name as nm_lead,
                (SELECT staffname FROM staff WHERE id = leads.closer) as nm_closer,
                staffname as nm_sdr'
            );
            $builder->join('leads', 'leads.id = leads_reunioes.id_lead', 'inner');
            $builder->join('staff', 'leads.assigned_id = staff.id', 'left');
            $builder->where('staff.id_company', session()->id_company);
            $builder->orderBy('data', 'desc');

            $dt_de = request()->getPost('dt_de');
            if ($dt_de != null && $dt_de != "-1") {
                $builder->where("leads_reunioes.data >=", $dt_de);
            }

            $dt_ate = request()->getPost('dt_ate');
            if ($dt_ate != null && $dt_ate != "-1") {
                $builder->where("leads_reunioes.data <=", $dt_ate);
            }

            $tipo_reuniao = request()->getPost('tipo_reuniao');
            if ($tipo_reuniao != null && $tipo_reuniao != "-1") {
                $builder->where('realizada', $tipo_reuniao);
            }

            $closer = request()->getPost('closer');
            if ($closer != null && $closer != "-1") {
                $builder->where("leads.closer", $closer);
            }

            $sdr = request()->getPost('sdr');
            if ($sdr != null && $sdr != "-1") {
                $builder->where("leads.assigned_id", $sdr);
            }

            $data = $builder->get()->getResultArray();
        } else  if (request()->getPost('tipo') == "conversoes") {

            $builder = $this->db->table('customers');

            $builder->select(
                '*, customers.company as nm_lead,
                (SELECT staffname FROM staff WHERE id = customers.closer) as nm_closer,
                staffname as nm_sdr, customers.created as data, customers.id as id_customer'
            );
            $builder->join('staff', 'customers.assigned_id = staff.id', 'left');
            $builder->where('customers.id_company', session()->id_company);
            $builder->orderBy('created', 'desc');

            $dt_de = request()->getPost('dt_de');
            if ($dt_de != null && $dt_de != "-1") {
                $builder->where('customers.created >=', $dt_de);
            }

            $dt_ate = request()->getPost('dt_ate');
            if ($dt_ate != null && $dt_ate != "-1") {
                $builder->where('customers.created <=', $dt_ate);
            }

            $tipo_reuniao = request()->getPost('tipo_reuniao');
            if ($tipo_reuniao != null && $tipo_reuniao != "-1") {
                $builder->where('realizada', $tipo_reuniao);
            }

            $closer = request()->getPost('closer');
            if ($closer != null && $closer != "-1") {
                $builder->where('customers.closer', $closer);
            }

            $sdr = request()->getPost('sdr');
            if ($sdr != null && $sdr != "-1") {
                $builder->where('customers.assigned_id', $sdr);
            }

            $data = $builder->get()->getResultArray();
        }


        return response()->setJSON($data);
    }
}
