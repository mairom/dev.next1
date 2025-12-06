<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

use Dompdf\Dompdf;

class Companies extends BaseController
{
    private $user;

    function __construct()
    {
        parent::loadModels();
    }

    function index()
    {
        $this->user = get_user();
        if ($this->user['super_admin'] != "1") {
            session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
            return redirect()->to('panel/');
            die;
        }

        $data = [];
        $data['title'] = "Empresas - Admin";

        return view('companies/index', $data);
    }


    function login($id_company)
    {
        if (session()->super_admin != "1") {
            session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
            return redirect()->to('panel/');
            die;
        }

        $id_admin =  session()->usr_id;
        $this->model('Login_Model');
        $login = $this->Login_Model->login_user_company($id_company);

        if ($login) {
            // Salve os detalhes do usuário atual em uma sessão para poder voltar a ele depois
            session()->set(['admin_user' => $id_admin]);
            return redirect()->to('panel/');
        } else {
            // Usuário não encontrado, redirecione para uma página de erro ou exiba uma mensagem de erro
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function login_admin()
    {
        if (session()->admin_user == null) {
            session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
            return redirect()->to('panel/');
            die;
        }

        $this->model('Login_Model');
        $login = $this->Login_Model->login_admin();

        if ($login) {
            // Salve os detalhes do usuário atual em uma sessão para poder voltar a ele depois
            session()->set(['admin_user' => null]);
            return redirect()->to('panel/');
        } else {
            // Usuário não encontrado, redirecione para uma página de erro ou exiba uma mensagem de erro
            redirect($_SERVER['HTTP_REFERER']);
        }
    }



    function get_companies()
    {
        $data = $this->Companies_Model->get_companies();
        return response()->setJSON($data);
    }

    function exportdata()
    {
        helper(['file', 'download']);
        $select = "
                nm_company as 'nome da empresa',
                cnpj,
                web_site,
                cpf,
                setor_atividade,
                dt_nascimento,
                porte,
                phone,
                email,
                instagram,
                facebook,
                address,
                city,
                zip,
                description,
                created as 'criado em'
            ";

        // Consultar os dados
        $builder = $this->db->table('companies');
        $builder->select($select);
        $query = $builder->get();
        $results = $query->getResultArray();

        $delimiter = ";";
        $nuline = "\r\n";

        $csvData = $this->arrayToCsv($results, $delimiter, $nuline);

        return response()->setHeader('Content-Type', 'application/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="Empresas.csv"')
            ->setBody($csvData);
    }

    private function arrayToCsv(array $data, $delimiter = ",", $newline = "\r\n")
    {
        if (empty($data)) {
            return '';
        }

        // Cria o ponteiro para a saída
        $f = fopen('php://temp', 'r+');

        // Escreve o cabeçalho CSV
        fputcsv($f, array_keys($data[0]), $delimiter);

        // Escreve as linhas CSV
        foreach ($data as $row) {
            fputcsv($f, $row, $delimiter);
        }

        // Rewind the stream
        rewind($f);

        // Lê o conteúdo
        $csv = stream_get_contents($f);

        // Fecha o ponteiro
        fclose($f);

        return $csv;
    }


    function ver_grafico()
    {
        $token = request()->getGet('token');
        $data = [
            'send_email' => request()->getGet('send_email'),
            'envio'      => $this->db->table('companies_reports_envio')->where('token', $token)->get()->getRowArray()
        ];


        if ($data['envio']) {
            $data['title'] = "Relatório " . $data['envio']['data'];
            return view('companies/graficos', $data);
        }
    }

    function create()
    {
        $params = [
            'created'                   => date('Y-m-d H:i:s'),
            'date_contacted'            => request()->getPost('date_contacted'),
            'name'                      => request()->getPost('name'),
            'nm_company'                => request()->getPost('nm_company'),
            'description'               => request()->getPost('description'),
            'country_id'                => request()->getPost('country_id'),
            'zip'                       => request()->getPost('zip'),
            'city'                      => request()->getPost('city'),
            'state_id'                  => request()->getPost('state_id'),
            'address'                   => request()->getPost('address'),
            'email'                     => request()->getPost('email'),
            'phone'                     => request()->getPost('phone'),
            'source_id'                 => request()->getPost('source_id'),
            'tp_pessoa'                 => request()->getPost('tp_pessoa'),
            'vencimento'                => request()->getPost('vencimento'),
            'creditos'                  => request()->getPost('creditos'),
            'creditos_enriquecimento'   => request()->getPost('creditos_enriquecimento'),
            'status'                    => request()->getPost('status'),
            'cnpj'                      => request()->getPost('cnpj') ?: null,
            'web_site'                  => request()->getPost('web_site') ?: null,
            'cpf'                       => request()->getPost('cpf') ?: null,
            'setor_atividade'           => request()->getPost('setor_atividade') ?: null,
            'dt_nascimento'             => request()->getPost('dt_nascimento') ?: null,
            'porte'                     => request()->getPost('porte') ?: null,
            'instagram'                 => request()->getPost('instagram') ?: null,
            'facebook'                  => request()->getPost('facebook') ?: null,
            'linkedin'                  => request()->getPost('linkedin') ?: null,

            'renovar'                   => request()->getPost('renovar') ?: null,
            'credito_acumulativo'       => request()->getPost('credito_acumulativo') ?: null,
            'renovar_valor'             => request()->getPost('renovar_valor') ?: null,
        ];

        if ($id_company = request()->getPost('id_company')) {
            $this->db->table('companies')
                ->where('id_company', $id_company)
                ->update($params);
        } else {
            $params['status'] = '1';
            $this->db->table('companies')->insert($params);
            $company =   $this->db->insertID();

            $this->Companies_Model->insertSettings($company);
            $this->Companies_Model->insertLeadsAtvSelect($company);
            $this->Companies_Model->insertCategorias($company);
            $this->Companies_Model->insertFunilPadrao($company);
            $this->Companies_Model->insertAssinatura($company);
            // $this->Companies_Model->insertAviso($company);
            // $this->insertEmailTemplates($company);
        }

        return response()->setJSON([
            'success' => true,
            'message' => 'Empresa criada ' . lang2('createmessage')
        ]);
    }

    function SaveReport()
    {
        $emails = '';
        $permissions = '';
        if (request()->getPost('emails')) {
            foreach (request()->getPost('emails') as $email) {
                $emails .= $emails != '' ? ',' . $email : $email;
            }
        }

        if (request()->getPost('permissions')) {
            foreach (request()->getPost('permissions') as $index =>  $permission) {
                if ($permission == 'true' && $index != 'permission_all') {
                    $permissions .= $permissions != '' ? ',' . $index : $index;
                }
            }
        }

        $id_company = request()->getPost('id_company');

        // Obtendo o relatório da empresa
        $companies_reports = $this->db->table('companies_reports')
            ->where('id_company', $id_company)
            ->get()
            ->getRow();

        // Preparando os parâmetros para inserção/atualização
        $params = [
            'status' => request()->getPost('status'),
            'frequencia' => request()->getPost('frequencia'),
            'data' => request()->getPost('data'),
            'modelo_de_email' => request()->getPost('modelo_de_email'),
            'emails' => $emails,
            'permissoes' => $permissions,
        ];

        if ($companies_reports) {
            // Atualizando o relatório existente
            $this->db->table('companies_reports')
                ->where('id_company', $id_company)
                ->update($params);
        } else {
            // Inserindo um novo relatório
            $params['id_company'] = $id_company;
            $this->db->table('companies_reports')->insert($params);
        }


        return response()->setJSON(['success' => 200, 'message' => 'Salvo com sucesso!']);
    }

    function remove($company)
    {
        $response = $this->db->table('companies')->where('id_company', $company)->delete();
        return response()->setJSON(["success" => $response]);
    }

    function get_permission($company = '')
    {
        $permissions = $this->Privileges_Model->get_all_permissions();
        $permissionsCompany = $this->Privileges_Model->get_all_permissionsByCompany($company);
        $permissionsCompanyData = [];
        $permissionsData = [];

        foreach ($permissionsCompany as $row) {
            $permissionsCompanyData[] = $row['id_permission'];
        }

        foreach ($permissions as $row) {
            $row['permitido'] = in_array($row['id'], $permissionsCompanyData) ? "1" : "0";
            $row['permission_key'] = lang2($row['permission']);
            $permissionsData[] = $row;
        }
        return response()->setJSON($permissionsData);
    }

    function get_permission_export($company = '')
    {
        $permissions = ['leads', 'customers', 'staff', 'customer_sucess', 'products'];
        $permissionsCompany = $this->Privileges_Model->get_all_permissionsExportByCompany($company);
        $permissionsCompanyData = [];
        $permissionsData = [];

        foreach ($permissionsCompany as $row) {
            $permissionsCompanyData[] = $row['export_page'];
        }

        foreach ($permissions as $row) {
            $row2 = [];
            $row2['permitido'] = in_array($row, $permissionsCompanyData) ? "1" : "0";
            $row2['permission_key'] = lang($row);
            $row2['id'] = $row;

            $permissionsData[] = $row2;
        }
        return response()->setJSON($permissionsData);
    }
    function AddPermission()
    {
      
        $this->db->table('companies_permission')->where(['id_company' => request()->getPost('id_company')])->delete();

        if (request()->getPost('permissions') != null) {
    
            $roleADM = $this->db->table('roles')
                ->select('role_id')
                ->where('id_company', request()->getPost('id_company'))
                ->orderBy('role_id', 'asc')
                ->limit(1)
                ->get()
                ->getRowArray();



            $permissoes = [];
            if ($roleADM) {
                $this->db->table('role_permissions')->where(['role_id' => $roleADM['role_id']])->delete();
                // $response = $this->db->table('role_permissions')->update(['role_id' => $roleADM]);

                foreach (request()->getPost('permissions') as $index => $row) {
                    if ($row == 'true' && !empty($row)) {
                        $permissoes[] = $index;
                        $params3 = array(
                            'id_company' => request()->getPost('id_company'),
                            'permission_id' => $index,
                            'permission_view_own' => "1",
                            'permission_view_all' => "1",
                            'permission_edit' => "1",
                            'permission_delete' => "1",
                            'permission_create' => '1',
                            'role_id' => $roleADM['role_id'],
                        );
                        $this->db->table('role_permissions')->insert($params3);
                    }
                }

                if (!in_array("20", $permissoes)) {
                    $params4 = array(
                        'id_company' => request()->getPost('id_company'),
                        'permission_id' => "20",
                        'permission_view_own' => "1",
                        'permission_view_all' => "1",
                        'permission_edit' => "1",
                        'permission_delete' => "1",
                        'permission_create' => '1',
                        'role_id' => $roleADM['role_id'],
                    );

                    $this->db->table('role_permissions')->insert($params4);
                    $this->db->table('companies_permission')->insert(["id_permission" => "20", 'id_company' => request()->getPost('id_company')]);
                }
            }

           

            foreach (request()->getPost('permissions') as $index => $row) {
                if ($row == 'true' && !empty($row)) {
                    $this->db->table('companies_permission')->insert(["id_permission" => $index, 'id_company' => request()->getPost('id_company')]);
                }
            }
        }

        if (request()->getPost('permissions_export') != null) {

            $this->db->table('companies_permission_export')->where(['id_company' => request()->getPost('id_company')])->delete();
            foreach (request()->getPost('permissions_export') as $index => $row) {
                if ($row == 'true' && !empty($row)) {
                    $this->db->table('companies_permission_export')->insert(["export_page" => $index, 'id_company' => request()->getPost('id_company')]);
                }
            }
        }
        return response()->setJSON(["success" => true]);
    }


    function company($id)
    {

        if (session()->super_admin == "1") {
            $company = $this->Companies_Model->get_company($id);
        } else {
            session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
            redirect(base_url('leads'));
        }

        if ($company) {
            $data = [];
            $data['company'] = $company;
            $data['title'] = "Empresa - " . ($company['tp_pessoa'] == "2" ? $company['nm_company'] : $company['name']);
            return view('companies/company', $data);
        } else {
            session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
            redirect(base_url('leads'));
        }
    }

    function get_company($id)
    {

        $company = $this->Companies_Model->get_company($id);
        $company['country'] = get_country($company['country_id']);
        $company['state']  = get_state_name("", $company['state_id']);

        return response()->setJSON($company);
    }

    function get_usuarios($id)
    {
        $company = $this->Companies_Model->get_usuarios($id);
        return response()->setJSON($company);
    }
}
