<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

use DateTime;

class Lead1 extends BaseController
{
    public $creditos_ph3 = 0;
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
        $data['title'] = lang2('x_menu_lead_p_1');
        $data['back_lead1'] = "back-lead1.png";

        $r = $this->db->table('branding')->where('name', 'back_lead1')->get()->getRow();

        if ($r->value != "") {
            $data['back_lead1'] = "images/" . $r->value;
        }

        return view('lead1/index', $data);
    }

    function remove($ids)
    {

        if (session()->super_admin == "1") {
            if ($ids == "-1") {

                $this->db->table('lead1')->where('created IS NOT NULL')->delete();
            } else {
                foreach (explode("-", $ids) as $id) {
                    $this->db->table('lead1')->delete(['id_lead1' => $id]);
                }
            }

            return response()->setJSON(["success" => true]);
        }
    }

    function pesquisa()
    {
        $data = [];
        $data['title'] = lang2('x_menu_lead_p_1');

        $data['ramo'] = request()->getGet('ramo');
        $data['nome_empresa'] = request()->getGet('nome_empresa');
        $data['estado'] = request()->getGet('estado');
        $data['cidade'] = request()->getGet('cidade');
        $data['porte'] = request()->getGet('porte');
        $data['matrizEFilial'] = request()->getGet('matrizEFilial');

        return view('lead1/pesquisa', $data);
    }

    function view($id_lead1)
    {
        $data = [];
        $data['title'] = lang2('x_menu_lead_p_1');
        $data['id_lead1'] = $id_lead1;
        return view('lead1/view', $data);
    }



    function get_company()
    {
        $r = $this->db->table('lead1')
            ->select('*') // Se necessário, ajuste os campos que deseja selecionar
            ->where('id_lead1', request()->getPost('id_lead1'))
            ->join('lead1_backgroud', 'lead1_backgroud.nm_ramo = lead1.ramo_de_atividade', 'left')
            ->get()
            ->getRow();

        if ($r->logo_url == "") {
            $r->logo_url = base_url('assets/img/company.png');
        }
        if ($r->url != null) {
            $r->url_background = base_url($r->url);
        } else {
            $r->url_background = base_url('assets/img/staffmember_bg.png');
        }

        $data_inicio = new DateTime($r->abertura != null ? $r->abertura : $r->data_situacao);
        $data_fim = new DateTime(date('Y-m-d'));

        $anos = $data_inicio->diff($data_fim)->y;
        $r->anos_abertura = $anos > 1 ? $anos . " anos " : $anos . " ano";

        $r->descricao = mb_strimwidth($r->descricao, 0, 400, "...");

        return response()->setJSON($r);
    }


    public function get_totalLeadsGerados()
    {
        $builder = $this->db->table('lead1')
            ->select('count(*) as total');
        $data['total'] = $builder->get()->getRow()->total;

        $data['settings'] = $this->db->table('lead1_settings')->get()->getRow();

        return response()->setJSON($data);
    }

    public function get_lead1_background()
    {
        $data = $this->db->table('lead1_background')->get()->getResultArray();
        return response()->setJSON($data);
    }

    public function get_ramos()
    {
        $data = [];
        $query = request()->getGet('q');
        if (strlen($query) > 0) {
            $builder = $this->db->table('lead1_ramos')
                ->select('ramo_de_atividade as text, ramo_de_atividade as id')
                ->distinct()
                ->like('ramo_de_atividade', $query, 'both');

            // Executa a consulta e obtém os resultados como array
            $data = $builder->get()->getResultArray();
        }

        return response()->setJSON($data);
    }

    public function insert_ramos()
    {
        $builder = $this->db->table('lead1')
            ->select('ramo_de_atividade as text')
            ->groupBy('ramo_de_atividade');
        $data = $builder->get()->getResultArray();


        foreach ($data as $row) {
            $this->db->table('lead1_ramos')->insert([
                'ramo_de_atividade' => $row['text'],
                'created' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function get_nomes()
    {
        $query = request()->getGet('q');

        // Constrói a consulta na tabela 'lead1'
        $builder = $this->db->table('lead1')
            ->select('IF(nm_fantasia != "", nm_fantasia, razao_social) as text, id_lead1')
            ->groupStart()
            ->like('nm_fantasia', $query, 'before')
            ->orLike('razao_social', $query, 'before')
            ->groupEnd()
            ->distinct()
            ->limit(10);

        // Executa a consulta e obtém os resultados
        $data = $builder->get()->getResultArray();

        // Retorna os dados como JSON
        return response()->setJSON($data);
    }

    public function get_estados()
    {
        // Constrói a consulta na tabela 'lead1'
        $builder = $this->db->table('lead1')
            ->select('uf')
            ->groupBy('uf')
            ->limit(30);

        // Executa a consulta e obtém os resultados
        $data = $builder->get()->getResultArray();

        // Retorna os dados como JSON
        return response()->setJSON($data);
    }


    public function get_cidades()
    {
        $query = request()->getGet('q');
        $uf = request()->getGet('uf');

        $builder = $this->db->table('lead1_cidades');
        $builder->select('name as text, uf')
            ->like('name', '%' . $query . "%");

        if ($uf && $uf != "-1") {
            $builder->where('uf', $uf);
        }

        $builder->distinct()->limit(10);

        $data = $builder->get()->getResultArray();

        return response()->setJSON($data);
    }


    public function get_portes()
    {
        $query = request()->getGet('q');

        $builder = $this->db->table('lead1')
            ->select('porte as text')
            ->like('porte', $query, 'before')
            ->distinct()
            ->limit(10);

        $data = $builder->get()->getResultArray();

        return response()->setJSON($data);
    }



    public function requestCard()
    {
        require __DIR__ . '/../../vendor/autoload.php';
        $builder = $this->db->table('lead1_pacote')
            ->where('id_pacote', request()->getPost('id_pacote'));
        $pacote = $builder->get()->getRow();

        $builder = $this->db->table('companies')
            ->where('id_company', session()->get('id_company'));
        $creditos = $builder->get()->getRow()->creditos;

        $newSaldo = $creditos;

        SDK::setClientId("923838863142573");
        SDK::setClientSecret("HfEXJQh4QFj9TdcmQvQQFcAFe4aQVNHT");

        $payment = new Payment();
        $payment->transaction_amount = (float) $pacote->valor;
        $payment->token = request()->getPost('token');
        $payment->description = 'Venda de pacote Lead one';
        $payment->installments = 1;
        $payment->payment_method_id = request()->getPost('paymentMethodId');
        $payment->issuer_id = (int) request()->getPost('issuer_id');

        $payer = new Payer();
        $payer->email = 'pagamentos@lead1crm.com';
        $payer->identification = [
            "type" => 'CPF',
            "number" => request()->getPost('cpf')
        ];
        $payment->payer = $payer;
        $payment->save();

        $success = 400;
        if ($payment->status == "approved") {
            $success = 200;
            $newSaldo += $pacote->quantidade;

            $this->db->table('companies')->where('id_company', session()->get('id_company'));
            $this->db->update(['creditos' => $newSaldo]);
        }

        return response()->setJSON(["success" => $success, "saldo" => $newSaldo]);
    }

    public function ExportCsvLeads()
    {
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Leads ' . date('d-m-y') . '.csv"');
        header("Pragma: no-cache");
        header("Expires: 0");
        $outss = fopen("php://output", "w");
        fprintf($outss, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $header = [
            'cnpj',
            'razao_social',
            'nome_fantasia',
            'idade_empresa',
            'porte',
            'mei',
            'natureza',
            'status (situacao fiscal)',
            'regime_tributario',
            'capital_social',
            'faturamento_presumido',
            'numero_de_funcionarios',
            'total_matriz_e_filiais',
            'total_de_socios',
            'score_de_credito',
            'score_de_marketing',
            'instagram',
            'facebook',
            'linkedin'
        ];

        array_push($header, "cnae_primario_code", "cnae_primario_description");

        for ($i = 0; $i <= 8; $i++) {
            array_push($header, "cnae_secundario_code" . ($i + 1), "cnae_secundario_description" . ($i + 1));
        }

        for ($i = 0; $i <= 8; $i++) {
            array_push($header, "email_" . ($i + 1), "email_score_" . ($i + 1));
        }

        for ($i = 0; $i <= 8; $i++) {
            array_push($header, "phone_" . ($i + 1), "tipo (is mobile)_" . ($i + 1), "phone_score_" . ($i + 1), "observacao_" . ($i + 1));
        }

        for ($i = 0; $i <= 8; $i++) {
            array_push($header, "nome_socio_" . ($i + 1), "documento_" . ($i + 1));
        }

        for ($i = 0; $i <= 5; $i++) {
            array_push($header, "address_" . ($i + 1), "bairro_" . ($i + 1), "city_" . ($i + 1), "state_" . ($i + 1), "cep_" . ($i + 1));
        }

        for ($i = 0; $i <= 8; $i++) {
            array_push(
                $header,
                "nm_contato_" . ($i + 1),
                "cargo_" . ($i + 1),
                "email_ctt_" . ($i + 1),
                "telefone_" . ($i + 1),
                "ctt_principal_" . ($i + 1),
                "observacao_ctt_" . ($i + 1),
                "linkedin_" . ($i + 1),
                "dt_aniversario_" . ($i + 1)
            );
        }


        fputcsv($outss, $header, ";");

        $builder = $this->db->table('companies')->where('id_company', session()->get('id_company'));
        $creditos = $builder->get()->getRow()->creditos;
        $builder = $this->db->table('lead1_settings')->where('id_setting', '1');
        $lead1_settings = $builder->get()->getRow();
        $leads = request()->getGet('leads1');

        foreach ($leads as $lead) {
            if ($lead && $creditos > 1) {
                $lead1 = $this->db->table('lead1')->where('id_lead1', $lead)->get()->getResultArray()[0];

                $exists = $this->db->table('leads')
                    ->join('staff', 'staff.id = leads.staff_id', 'left')
                    ->where('id_company', session()->get('id_company'))
                    ->where('cnpj', $lead1['cnpj'])
                    ->countAllResults();

                if ($exists > 0) {
                    continue;
                }

                $linha = array_fill_keys($header, '');
                $linha['cnpj'] = "'" . $lead1['cnpj'] . "'" ?? '';
                $linha['razao_social'] = $lead1['razao_social'] ? $lead1['razao_social'] : $lead1['nm_fantasia'];
                $linha['nome_fantasia'] = $lead1['nm_fantasia'] ?? '';
                $linha['porte'] = $lead1['porte'] ? $lead1['porte'] : '';
                $linha['instagram'] = $lead1['url_insta'] ?? '';
                $linha['facebook'] = $lead1['url_face'] ?? '';
                $linha['linkedin'] = $lead1['url_linkedin'] ?? '';
                $linha['email_1'] = $lead1['email1'] ?? '';
                $linha['phone_1'] = $lead1['telefone1'] ? $lead1['ddd1'] . $lead1['telefone1'] : '';

                $this->db->table('companies_faturas')->insert([
                    'id_company' => session()->get('id_company'),
                    'staff_id' => session()->get('usr_id'),
                    'data' => date('Y-m-d H:i:s'),
                    'valor' => $lead1_settings->custoLead,
                    'referencia' => 'importação de lead ' . $lead1['nm_fantasia']
                ]);

                $creditos--;
                fputcsv($outss, $linha, ";");
                // print_r( $linha);
            }
        }



        $this->db->table('companies')
            ->where('id_company', session()->get('id_company'))
            ->update(['creditos' => $creditos]);

        fclose($outss);
        exit;
    }


    public function ImportarLeads()
    {
        $importados = [];

        $builder = $this->db->table('companies')->where('id_company', session()->get('id_company'));
        $creditos = $builder->get()->getRow()->creditos;

        $builder = $this->db->table('lead1_settings')->where('id_setting', '1');
        $lead1_settings = $builder->get()->getRow();

        $leads = request()->getPost('leads1');

        foreach ($leads as $lead) {
            if ($lead && $creditos > 1) {
                $importados[] = $lead;
                $lead1 = $this->db->table('lead1')->where('id_lead1', $lead)->get()->getRow();

                $exists = $this->db->table('leads')
                    ->join('staff', 'staff.id = leads.staff_id', 'left')
                    ->where('id_company', session()->get('id_company'))
                    ->where('cnpj', $lead1->cnpj)
                    ->countAllResults();

                if ($exists > 0) {
                    continue;
                }

                $this->db->table('leads')->insert([
                    'date_contacted' => date('Y-m-d H:i:s'),
                    'created' => date('Y-m-d H:i:s'),
                    'type' => '0',
                    'porte' => $lead1->porte,
                    'tp_pessoa' => '2',
                    'name' => $lead1->razao_social,
                    'company' => $lead1->nm_fantasia,
                    'cnpj' => $lead1->cnpj,
                    'description' => $lead1->descricao,
                    'web_site' => $lead1->website,
                    'country_id' => '32',
                    'email' => $lead1->email1,
                    'phone' => $lead1->ddd1 . $lead1->telefone1,
                    'instagram' => $lead1->url_insta,
                    'facebook' => $lead1->url_face,
                    'city' => $lead1->municipio,
                    'state' => $lead1->uf,
                    'linkedin' => $lead1->url_linkedin,
                    'assigned_id' => request()->getPost('importassigned'),
                    'funil_list' => '0',
                    'status' => request()->getPost('importstatus'),
                    'source' => '104',
                    'setor_atividade' => $lead1->ramo_de_atividade,
                    'dateassigned' => date('Y-m-d H:i:s'),
                    'staff_id' => session()->get('usr_id'),
                    'lost' => '0',
                    'junk' => '0',
                    'public' => '0',
                    'lead_status_id' => '1'
                ]);
                $id_lead = $this->db->insertID();

                $this->db->table('companies_faturas')->insert([
                    'id_company' => session()->get('id_company'),
                    'staff_id' => session()->get('usr_id'),
                    'data' => date('Y-m-d H:i:s'),
                    'valor' => $lead1_settings->custoLead,
                    'referencia' => 'importação de lead ' . $lead1->nm_fantasia
                ]);

                $creditos--;

                if (request()->getPost('enriquecer') == "1") {
                    $this->creditos_ph3 = $this->db->table('companies')
                        ->select('creditos_enriquecimento')
                        ->where('id_company', session()->get('id_company'))
                        ->get()
                        ->getRow()
                        ->creditos_enriquecimento;

                    $this->buscaDadosPh3a($id_lead, $lead1->cnpj);

                }
            }
        }

        $this->db->table('companies')
            ->where('id_company', session()->get('id_company'))
            ->update(['creditos' => $creditos]);

        return response()->setJSON(["success" => 200, "importados" => $importados, "credito" => $creditos]);
    }


    function autenticaPh3a()
    {
        $url = 'https://api.ph3a.com.br/DataBusca/api/Account/Login';
        $data = ["UserName" => "adm@contratei.net", "Password" => "Contratei@23"];

        $postdata = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);
        $r = json_decode($result);

        return $r->data->Token;
    }


    public function buscaDadosPh3a($id_lead, $cnpj)
    {
        $result = "";

        if ($this->creditos_ph3 >= 0) {
            $lead_data = $this->db->table('leads_data')
                ->select('*')
                ->where('cnpj', $cnpj)
                ->get()
                ->getRow();

            if ($lead_data) {
                $r = $lead_data->value;
                $data['success'] = true;
            } else {

                // Obtém o token
                $q = $this->db->table('tokens')
                    ->select('*')
                    ->where('type', 'ph3a')
                    ->get()
                    ->getRow();

                // Verifica se o token está expirado
                if (date('Y-m-d H:i:s', strtotime($q->created_at . '+20 minutes')) <= date('Y-m-d H:i:s')) {
                    $token = $this->autenticaPh3a();
                    $this->db->table('tokens')
                        ->where('type', 'ph3a')
                        ->update(['token' => $token, 'created_at' => date('Y-m-d H:i:s')]);
                } else {
                    $token = $q->token;
                }

                // Faz a requisição para a API
                $url = 'https://api.ph3a.com.br/DataBusca/data';
                $data = [
                    "Document" => $cnpj
                ];
                $postdata = json_encode($data);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Token: ' . $token]);
                $result = curl_exec($ch);
                curl_close($ch);

                // Atualiza o status de sucesso
                $data['success'] = ($result) ? true : false;

              //  print_r(json_decode($result));
              //  exit;

                $r = json_encode(json_decode($result)->Data);
            }

            // Atualiza os créditos
            $this->db->table('companies')
                ->where('id_company', session()->get('id_company'))
                ->update(['creditos_enriquecimento' => ($this->creditos_ph3 - 1)]);

            // Atualiza a última busca
            $this->db->table('leads')
                ->where('id', $id_lead)
                ->update(['ultimaBusca' => date('Y-m-d H:i:s')]);

            // Insere os dados da busca
            $this->db->table('leads_data')->insert([
                'id_lead' => $id_lead,
                'cnpj' => $cnpj,
                'value' => $r,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $resArray = json_decode($r);

            if (isset($resArray->Relateds)) {
              //  print_r($resArray->Relateds);
               // exit;
                foreach ($resArray->Relateds as $socio) {
                    $this->db->table('leads_contatos')->insert([
                        'id_lead' => $id_lead,
                        'nm_contato' => $socio->Name,
                        'cargo' => 'socio',
                        'dt_criado' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->creditos_ph3 = $this->creditos_ph3 - 1;
        } else {
            $data['success'] = false;
        }

        // Adiciona os dados da resposta
        return json_decode($result, true);
    }



    function get_companies()
    {


        $builder2 = $this->db->table('companies')->where('id_company', session()->get('id_company'));
        $creditos = $builder2->get()->getRow()->creditos;
        $limit = ($creditos > 5) ? (($creditos < 100) ? $creditos : 10000) : 5;

        $builder = $this->db->table('lead1')->limit($limit);
        if (request()->getPost('ramo') && request()->getPost('ramo') != "-1" && !empty(request()->getPost('ramo'))) {
            if (is_array(request()->getPost('ramo'))) {
                $builder->whereIn('ramo_de_atividade', request()->getPost('ramo'));
            } else {
                $builder->where('ramo_de_atividade', request()->getPost('ramo'));
            }
        }

        if (request()->getPost('estado') && request()->getPost('estado') != "-1" && !empty(request()->getPost('estado'))) {
            $builder->where('uf', request()->getPost('estado'));
        }

        if (request()->getPost('cidade') && request()->getPost('cidade') != "-1" && !empty(request()->getPost('cidade'))) {
            $builder->where('municipio', request()->getPost('cidade'));
        }

        if (request()->getPost('porte') && request()->getPost('porte') != "-1" && !empty(request()->getPost('porte'))) {
            $builder->where('porte', request()->getPost('porte'));
        }

        if (request()->getPost('nome_empresa') && request()->getPost('nome_empresa') != "-1" && !empty(request()->getPost('nome_empresa'))) {


            if (is_array(request()->getPost('nome_empresa'))) {
                $builder->group_start();
                foreach (request()->getPost('nome_empresa') as $nome) {
                    $builder->or_where("nm_fantasia LIKE '%" . $nome . "%' or razao_social LIKE '%" . $nome . "%'");
                }
                $builder->group_end();
            } else {
                $builder->where("(nm_fantasia LIKE '%" . request()->getPost('nome_empresa') . "%' or razao_social LIKE '%" . request()->getPost('nome_empresa') . "%')");
            }
        }

        if (request()->getPost('matrizEFilial') && request()->getPost('matrizEFilial') != "-1" && !empty(request()->getPost('matrizEFilial'))) {
            if (request()->getPost('matrizEFilial') == "M") {
                $builder->where('matriz_ou_filial', "MATRIZ");
            } else if (request()->getPost('matrizEFilial') == "F") {
                $builder->where('matriz_ou_filial', "FILIAL");
            }
        }

        // echo $builder->getCompiledSelect();
        //exit;
        $data = $builder->get()->getResultArray();


        $builder3 = $this->db->table('lead1');
        if (request()->getPost('ramo') && request()->getPost('ramo') != "-1" && !empty(request()->getPost('ramo'))) {
            if (is_array(request()->getPost('ramo'))) {
                $builder3->whereIn('ramo_de_atividade', request()->getPost('ramo'));
            } else {
                $builder3->where('ramo_de_atividade', request()->getPost('ramo'));
            }
        }

        if (request()->getPost('estado') && request()->getPost('estado') != "-1" && !empty(request()->getPost('estado'))) {
            $builder3->where('uf', request()->getPost('estado'));
        }

        if (request()->getPost('cidade') && request()->getPost('cidade') != "-1" && !empty(request()->getPost('cidade'))) {
            $builder3->where('municipio', request()->getPost('cidade'));
        }

        if (request()->getPost('porte') && request()->getPost('porte') != "-1" && !empty(request()->getPost('porte'))) {
            $builder3->where('porte', request()->getPost('porte'));
        }

        if (request()->getPost('nome_empresa') && request()->getPost('nome_empresa') != "-1" && !empty(request()->getPost('nome_empresa'))) {
            if (is_array(request()->getPost('nome_empresa'))) {
                $builder3->group_start();
                foreach (request()->getPost('nome_empresa') as $nome) {
                    $builder3->or_where("nm_fantasia LIKE '%" . $nome . "%' or razao_social LIKE '%" . $nome . "%'");
                }
                $builder3->group_end();
            } else {
                $builder3->where("(nm_fantasia LIKE '%" . request()->getPost('nome_empresa') . "%' or razao_social LIKE '%" . request()->getPost('nome_empresa') . "%')");
            }
        }

        if (request()->getPost('matrizEFilial') && request()->getPost('matrizEFilial') != "-1" && !empty(request()->getPost('matrizEFilial'))) {
            if (request()->getPost('matrizEFilial') == "M") {
                $builder3->where('matriz_ou_filial', "MATRIZ");
            } else if (request()->getPost('matrizEFilial') == "F") {
                $builder3->where('matriz_ou_filial', "FILIAL");
            }
        }
        $builder3->select('count(*) as total');
        $total = $builder3->get()->getRow()->total;


        foreach ($data as $index => $row) {
            $data[$index]['abertura'] = _kdate($row['abertura']);

            if ($row['logo_url'] == "") {
                $data[$index]['logo_url'] = base_url('assets/img/company.png');
            }
            $data_inicio = new DateTime($row['abertura']);
            $data_fim = new DateTime(date('Y-m-d'));

            $anos = $data_inicio->diff($data_fim)->y;
            $data[$index]['anos_abertura'] = $anos > 1 ? $anos . " anos " : $anos . " ano";
        }

        return response()->setJSON(['r' => $data, 'total' => $total, 'creditos' => $creditos]);
    }


    function get_companiesAdmin()
    {
        $data = [];
        $pageAtual = 0;

        if (session()->get('super_admin') == "1") {
            if (request()->getPost('ramo') && request()->getPost('ramo') != "-1" && !empty(request()->getPost('ramo'))) {
                if (is_array(request()->getPost('ramo'))) {
                    $this->db->table('lead1')->whereIn('ramo_de_atividade', request()->getPost('ramo'));
                } else {
                    $this->db->table('lead1')->where('ramo_de_atividade', request()->getPost('ramo'));
                }
            }

            if (request()->getPost('estado') && request()->getPost('estado') != "-1") {
                $this->db->table('lead1')->where('uf', request()->getPost('estado'));
            }

            if (request()->getPost('cidade') && request()->getPost('cidade') != "-1") {
                $this->db->table('lead1')->where('municipio', request()->getPost('cidade'));
            }

            if (request()->getPost('porte') && request()->getPost('porte') != "-1") {
                $this->db->table('lead1')->where('porte', request()->getPost('porte'));
            }

            if (request()->getPost('faixa_capital') && request()->getPost('faixa_capital') != "-1") {
                $faixa_capital = explode("|", request()->getPost('faixa_capital'));
                $this->db->table('lead1')->where("(capital_social >= $faixa_capital[0] and capital_social <= $faixa_capital[1])");
            }

            if (request()->getPost('nome_empresa') && request()->getPost('nome_empresa') != "-1") {
                if (is_array(request()->getPost('nome_empresa'))) {
                    $this->db->table('lead1')->groupStart();
                    foreach (request()->getPost('nome_empresa') as $nome) {
                        $this->db->orWhere("nm_fantasia LIKE '%" . $nome . "%' or razao_social LIKE '%" . $nome . "%'");
                    }
                    $this->db->groupEnd();
                } else {
                    $this->db->table('lead1')->where("(nm_fantasia LIKE '%" . request()->getPost('nome_empresa') . "%' or razao_social LIKE '%" . request()->getPost('nome_empresa') . "%')");
                }
            }

            if (request()->getPost('pageAtual')) {
                $pageAtual = request()->getPost('pageAtual') - 1;
            }

            $pageAtual = request()->getPost('pageAtual') ?? 0;
            $perPage = 10;
            $offset = $pageAtual * $perPage;

            $data = $this->db->table('lead1')->limit($perPage, $offset)->get()->getResultArray();

            foreach ($data as $index => $row) {
                $data[$index]['abertura'] = _kdate($row['abertura']);
                $data_inicio = new DateTime($row['abertura']);
                $data_fim = new DateTime(date('Y-m-d'));
                $anos = $data_inicio->diff($data_fim)->y;
                $data[$index]['anos_abertura'] = $anos > 1 ? $anos . " anos " : $anos . " ano";

                if ($row['logo_url'] == "") {
                    $data[$index]['logo_url'] = base_url('assets/img/company.png');
                }
            }
        }

        return response()->setJSON($data);
    }

    function get_pacotes()
    {
        // Consulta na tabela 'lead1_pacote' para obter os dados onde 'is_ativo' é 1
        $builder = $this->db->table('lead1_pacote')->where('is_ativo', '1');
        $data = $builder->get()->getResultArray();

        // Consulta na tabela 'companies' para obter o valor de 'creditos' da empresa atual
        $builder = $this->db->table('companies')->where('id_company', session()->get('id_company'));
        $creditos = $builder->get()->getRow()->creditos;


        return response()->setJSON(['data' => $data, 'creditos' => $creditos]);
    }

    function atualizaConta()
    {
        $builder = $this->db->table('companies_faturas')
            ->where('id_company', session()->get('id_company'));
        $data = $builder->get()->getResultArray();


        return response()->setJSON($data);
    }




    function save_company()
    {
        $data = [];
        if (session()->super_admin == "1") {
            $update_data = [
                'cnpj' => request()->getPost('cnpj'),
                'matriz_ou_filial' => request()->getPost('matriz_ou_filial'),
                'razao_social' => request()->getPost('razao_social'),
                'nm_fantasia' => request()->getPost('nm_fantasia'),
                'situacao' => request()->getPost('situacao'),
                'data_situacao' => request()->getPost('data_situacao'),
                'natureza_juridica' => request()->getPost('natureza_juridica'),
                'abertura' => request()->getPost('abertura'),
                'cnae' => request()->getPost('cnae'),
                'ramo_de_atividade' => request()->getPost('ramo_de_atividade'),
                'cnaes_secundarios' => request()->getPost('cnaes_secundarios'),
                'tipo_logradouro' => request()->getPost('tipo_logradouro'),
                'logradouro' => request()->getPost('logradouro'),
                'numero' => request()->getPost('numero'),
                'complemento' => request()->getPost('complemento'),
                'bairro' => request()->getPost('bairro'),
                'cep' => request()->getPost('cep'),
                'uf' => request()->getPost('uf'),
                'municipio' => request()->getPost('municipio'),
                'ddd1' => request()->getPost('ddd1'),
                'telefone1' => request()->getPost('telefone1'),
                'ddd2' => request()->getPost('ddd2'),
                'telefone2' => request()->getPost('telefone2'),
                'ddd3' => request()->getPost('ddd3'),
                'telefone3' => request()->getPost('telefone3'),
                'ddd4' => request()->getPost('ddd4'),
                'telefone4' => request()->getPost('telefone4'),
                'ddd5' => request()->getPost('ddd5'),
                'telefone5' => request()->getPost('telefone5'),
                'insta' => request()->getPost('insta'),
                'email1' => request()->getPost('email1'),
                'email2' => request()->getPost('email2'),
                'email3' => request()->getPost('email3'),
                'email4' => request()->getPost('email4'),
                'email5' => request()->getPost('email5'),
                'capital_social' => request()->getPost('capital_social'),
                'porte' => request()->getPost('porte'),
                'simples_nacional' => request()->getPost('simples_nacional'),
                'mei' => request()->getPost('mei'),
                'faturamento_presumido' => request()->getPost('faturamento_presumido'),
                'socio1_nome' => request()->getPost('socio1_nome'),
                'socio1_cpf' => request()->getPost('socio1_cpf'),
                'socio2_nome' => request()->getPost('socio2_nome'),
                'socio2_cpf' => request()->getPost('socio2_cpf'),
                'logo_url' => request()->getPost('logo_url'),
                'website' => request()->getPost('website'),
                'tagline' => request()->getPost('tagline'),
                'numero_funcionarios' => request()->getPost('numero_funcionarios'),
                'descricao' => request()->getPost('descricao'),
                'url_linkedin' => request()->getPost('url_linkedin'),
                'url_insta' => request()->getPost('url_insta'),
                'url_face' => request()->getPost('url_face'),
            ];

            if (request()->getPost('id_lead1')) {
                $this->db->table('lead1')->where('id_lead1', request()->getPost('id_lead1'))->update($update_data);
                $data['success'] = 200;
            } else {
                $data['success'] = 400;
            }
        }

        return response()->setJSON($data);
    }

    function SalvarConfigs()
    {
        $data = [];
        $data['success'] = 400;
        if (session()->super_admin == "1") {
            $update_data = [
                "custoLead" => request()->getPost('custoLead'),
                "custoEnriquecimento" => request()->getPost('custoEnriquecimento'),
            ];

            $this->db->table('lead1_settings')->where('id_setting', '1')->update($update_data);

            $data['success'] = 200;
        }

        return response()->setJSON($data);
    }

    function salva_pacote()
    {
        $data = [];
        $data['success'] = 400;
        if (session()->super_admin == "1") {
            $update_data = [
                "nm_pacote" => request()->getPost('nm_pacote'),
                "detalhes" => request()->getPost('detalhes'),
                "quantidade" => request()->getPost('quantidade'),
                "valor" => request()->getPost('valor'),
            ];

            if (request()->getPost('id_pacote')) {
                // Atualizar registro existente
                $this->db->table('lead1_pacote')->where('id_pacote', request()->getPost('id_pacote'))->update($update_data);
            } else {
                // Inserir novo registro
                $update_data['is_ativo'] = '1';
                $this->db->table('lead1_pacote')->insert($update_data);
            }

            $data['success'] = 200;
        }

        return response()->setJSON($data);
    }


    function delete_company()
    {
        $r = $this->db->table('lead1')->delete(['cnpj' => request()->getPost('id_lead1')]);
        $data['success'] = 400;
        if ($r) {
            $data['success'] = 200;
        }

        return response()->setJSON($data);
    }


    function delete_pacote()
    {
        $this->db->table('lead1_pacote')->where('id_pacote', request()->getPost('id_pacote'))->update(['is_ativo' => '0']);

        $data['success'] = 400;
        if ($r) {
            $data['success'] = 200;
        }

        return response()->setJSON($data);
    }


    function upload_ramo()
    {
        $data = ['error' => ''];
        $uploadPath = ROOTPATH . 'public/uploads/files/lead1/';
        $allowedTypes = 'gif|jpg|png|jpeg|webp';
        $maxSize = 1000; // em KB

        // Verifica se o diretório de upload existe, se não, cria
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Verifica se o arquivo foi enviado
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['file'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileSize = $file['size'] / 1024; // tamanho do arquivo em KB

            // Verifica se o tipo de arquivo é permitido e o tamanho do arquivo
            if (preg_match('/\b(' . str_replace('|', '|', $allowedTypes) . ')\b/', $fileExtension) && $fileSize <= $maxSize) {
                $newName = preg_replace("/[^a-z0-9_\-\.]/i", '', basename($file['name']));
                $filePath = $uploadPath . $newName;

                // Move o arquivo para o local desejado
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $urlPath = '/uploads/files/lead1/' . $newName;

                    // Atualiza o banco de dados
                    $this->db->table('lead1_backgroud')->delete(['nm_ramo' => request()->getGet('nm_ramo')]);
                    $this->db->table('lead1_backgroud')->insert([
                        'nm_ramo' => request()->getGet('nm_ramo'),
                        'url' => $urlPath,
                        'arquivo' => $newName
                    ]);

                    $data['success'] = true;
                } else {
                    $data['error'] = lang2('file_upload_error');
                }
            } else {
                $data['error'] = lang2('invalid_file_type_or_size');
            }
        } else {
            $data['error'] = lang2('no_file_uploaded');
        }

        if ($data['error']) {
            session()->setFlashdata('ntf1', lang2('csvimporterror'));
        }

        return response()->setJSON($data);
    }


    function import_companies_local()
    {
        set_time_limit(0);
        $file_path = './uploads/imports/00import_companies.csv';

        $handle = fopen($file_path, 'r');
        $cont = 0;
        $line = [];
        while ($row_old = fgetcsv($handle, 1000, ";")) {
            $row = [];
            $cont++;
            if ($cont == 1) {
                $line = $row_old;
                continue;
            }

            foreach ($line as $index => $chave) {
                if (isset($row_old[$index])) {
                    $row[$chave] = $row_old[$index];
                } else {
                    $row[$chave] = "";
                }
            }

            $insert_data = [
                'created' => date('Y-m-d H:i:s'),
                'cnpj' => isset($row['CNPJ']) ? $row['CNPJ'] : null,
                'matriz_ou_filial' => isset($row['Matriz_ou_Filial']) ? $row['Matriz_ou_Filial'] : null,
                'razao_social' => isset($row['Razao_Social']) ? $row['Razao_Social'] : null,
                'nm_fantasia' => isset($row['Nome_Fantasia']) ? $row['Nome_Fantasia'] : null,
                'situacao' => isset($row['Situacao']) ? $row['Situacao'] : null,
                'data_situacao' => isset($row['Data_Situacao']) ? implode("-", array_reverse(explode("/", $row['Data_Situacao']))) : null,
                'natureza_juridica' => isset($row['Situacao']) ? $row['Natureza_Juridica'] : null,
                'abertura' => isset($row['Data_Situacao']) ? implode("-", array_reverse(explode("/", $row['Abertura']))) : null,
                'cnae' => isset($row['CNAE']) ? $row['CNAE'] : null,
                'ramo_de_atividade' => isset($row['Ramo_de_Atividade']) ? $row['Ramo_de_Atividade'] : null,
                'cnaes_secundarios' => isset($row['CNAES_Secundarios']) ? $row['CNAES_Secundarios'] : null,
                'tipo_logradouro' => isset($row['Tipo_Logradouro']) ? $row['Tipo_Logradouro'] : null,
                'logradouro' => isset($row['Logradouro']) ? $row['Logradouro'] : null,
                'numero' => isset($row['Numero']) ? $row['Numero'] : null,
                'complemento' => isset($row['Complemento']) ? $row['Complemento'] : null,
                'bairro' => isset($row['Bairro']) ? $row['Bairro'] : null,
                'cep' => isset($row['CEP']) ? $row['CEP'] : null,
                'uf' => isset($row['UF']) ? $row['UF'] : null,
                'municipio' => isset($row['Municipio']) ? $row['Municipio'] : null,
                'ddd1' => isset($row['DDD1']) ? $row['DDD1'] : null,
                'telefone1' => isset($row['Telefone1']) ? $row['Telefone1'] : null,
                'ddd2' => isset($row['DDD2']) ? $row['DDD2'] : null,
                'telefone2' => isset($row['Telefone2']) ? $row['Telefone2'] : null,
                'ddd3' => isset($row['DDD3']) ? $row['DDD3'] : null,
                'telefone3' => isset($row['Telefone3']) ? $row['Telefone3'] : null,
                'ddd4' => isset($row['DDD4']) ? $row['DDD4'] : null,
                'telefone4' => isset($row['Telefone4']) ? $row['Telefone4'] : null,
                'ddd5' => isset($row['DDD5']) ? $row['DDD5'] : null,
                'telefone5' => isset($row['Telefone5']) ? $row['Telefone5'] : null,
                'insta' => isset($row['Insta']) ? $row['Insta'] : null,
                'email1' => isset($row['E-mail1']) ? $row['E-mail1'] : null,
                'email2' => isset($row['E-mail2']) ? $row['E-mail2'] : null,
                'email3' => isset($row['E-mail3']) ? $row['E-mail3'] : null,
                'email4' => isset($row['E-mail4']) ? $row['E-mail4'] : null,
                'email5' => isset($row['E-mail5']) ? $row['E-mail5'] : null,
                'capital_social' => isset($row['Capital_Social']) ? $row['Capital_Social'] : null,
                'porte' => isset($row['Porte']) ? $row['Porte'] : null,
                'simples_nacional' => isset($row['Simples_Nacional']) ? $row['Simples_Nacional'] : null,
                'mei' => isset($row['MEI']) ? $row['MEI'] : null,
                'faturamento_presumido' => isset($row['Faturamento_Presumido']) ? $row['Faturamento_Presumido'] : null,
                'socio1_nome' => isset($row['Socio1_Nome']) ? $row['Socio1_Nome'] : null,
                'socio1_cpf' => isset($row['Socio1_CPF']) ? $row['Socio1_CPF'] : null,
                'socio2_nome' => isset($row['Socio2_Nome']) ? $row['Socio2_Nome'] : null,
                'socio2_cpf' => isset($row['Socio2_CPF']) ? $row['Socio2_CPF'] : null,
                'logo_url' => isset($row['logo_url']) ? $row['logo_url'] : null,
                'website' => isset($row['website']) ? $row['website'] : null,
                'tagline' => isset($row['tagline']) ? $row['tagline'] : null,
                'numero_funcionarios' => isset($row['Numero_Funcionarios_range_end']) ? $row['Numero_Funcionarios_range_end'] : null,
                'descricao' => isset($row['Descricao']) ? $row['Descricao'] : null,
                'url_linkedin' => isset($row['Perfil_url-Linkedin']) ? $row['Perfil_url-Linkedin'] : null,
                'url_insta' => isset($row['Perfil_url_Instagram']) ? $row['Perfil_url_Instagram'] : null,
                'url_face' => isset($row['Perfil_url_Facebook']) ? $row['Perfil_url_Facebook'] : null,
            ];

            $this->db->table('lead1')->delete(['cnpj' => $row['CNPJ']]);
            $this->db->table('lead1')->insert($insert_data);
        }
    }

    function import_companies()
    {


        //  if ($this->Privileges_Model->check_privilege('customers', 'create')) {
        $arr = json_decode($_POST['data'], true);
        $sem_cnpj = 0;

        if ($arr) {
            foreach ($arr as $row) {
                if (!isset($row['CNPJ']) || empty($row['CNPJ'])) {
                    $sem_cnpj++;
                    continue;
                }

                $capitalSocial = isset($row['Capital_Social']) ? $row['Capital_Social'] : (isset($row['Capital Social']) ? $row['Capital Social'] : null);
                if (strpos($capitalSocial, ',') !== false) {

                    $capitalSocial = str_replace("R$ ", "", $capitalSocial);
                    $capitalSocial = number_format(str_replace(",", ".", str_replace(".", "", floatval($capitalSocial))), 2, '.', '');
                }
                $insert_data = [
                    'created' => date('Y-m-d H:i:s'),
                    'cnpj' => isset($row['CNPJ']) ? $row['CNPJ'] : null,
                    'matriz_ou_filial' => isset($row['Matriz_ou_Filial']) ? $row['Matriz_ou_Filial'] : (isset($row['Matriz ou Filial']) ? $row['Matriz ou Filial'] : null),
                    'razao_social' => isset($row['Razao_Social']) ? $row['Razao_Social'] : (isset($row['Razao Social']) ? $row['Razao Social'] : null),
                    'nm_fantasia' => isset($row['Nome_Fantasia']) ? $row['Nome_Fantasia'] : (isset($row['Nome Fantasia']) ? $row['Nome Fantasia'] : null),
                    'situacao' => isset($row['Situacao']) ? $row['Situacao'] : null,
                    'data_situacao' => isset($row['Data_Situacao']) ? implode("-", array_reverse(explode("/", $row['Data_Situacao']))) : (isset($row['Data Situacao']) ? implode("-", array_reverse(explode("/", $row['Data Situacao']))) : null),
                    'natureza_juridica' => isset($row['Natureza_Juridica']) ? $row['Natureza_Juridica'] : (isset($row['Natureza Juridica']) ? $row['Natureza Juridica'] : null),
                    'abertura' => isset($row['Abertura']) ? implode("-", array_reverse(explode("/", $row['Abertura']))) : null,
                    'cnae' => isset($row['CNAE']) ? $row['CNAE'] : null,
                    'ramo_de_atividade' => isset($row['Ramo_de_Atividade']) ? $row['Ramo_de_Atividade'] : (isset($row['Ramo de Atividade']) ? $row['Ramo de Atividade'] : null),
                    'cnaes_secundarios' => isset($row['CNAES_Secundarios']) ? $row['CNAES_Secundarios'] : (isset($row['CNAEs Secundarios']) ? $row['CNAEs Secundarios'] : null),
                    'tipo_logradouro' => isset($row['Tipo_Logradouro']) ? $row['Tipo_Logradouro'] : (isset($row['Tipo Logradouro']) ? $row['Tipo Logradouro'] : null),
                    'logradouro' => isset($row['Logradouro']) ? $row['Logradouro'] : null,
                    'numero' => isset($row['Numero']) ? $row['Numero'] : null,
                    'complemento' => isset($row['Complemento']) ? $row['Complemento'] : null,
                    'bairro' => isset($row['Bairro']) ? $row['Bairro'] : null,
                    'cep' => isset($row['CEP']) ? $row['CEP'] : null,
                    'uf' => isset($row['UF']) ? $row['UF'] : null,
                    'municipio' => isset($row['Municipio']) ? $row['Municipio'] : null,
                    'ddd1' => isset($row['DDD1']) ? $row['DDD1'] : null,
                    'telefone1' => isset($row['Telefone1']) ? $row['Telefone1'] : null,
                    'ddd2' => isset($row['DDD2']) ? $row['DDD2'] : null,
                    'telefone2' => isset($row['Telefone2']) ? $row['Telefone2'] : null,
                    'ddd3' => isset($row['DDD3']) ? $row['DDD3'] : null,
                    'telefone3' => isset($row['Telefone3']) ? $row['Telefone3'] : null,
                    'ddd4' => isset($row['DDD4']) ? $row['DDD4'] : null,
                    'telefone4' => isset($row['Telefone4']) ? $row['Telefone4'] : null,
                    'ddd5' => isset($row['DDD5']) ? $row['DDD5'] : null,
                    'telefone5' => isset($row['Telefone5']) ? $row['Telefone5'] : null,
                    'insta' => isset($row['Insta']) ? $row['Insta'] : null,
                    'email1' => isset($row['E-mail1']) ? $row['E-mail1'] : (isset($row['E-mail']) ? $row['E-mail'] : null),
                    'email2' => isset($row['E-mail2']) ? $row['E-mail2'] : null,
                    'email3' => isset($row['E-mail3']) ? $row['E-mail3'] : null,
                    'email4' => isset($row['E-mail4']) ? $row['E-mail4'] : null,
                    'email5' => isset($row['E-mail5']) ? $row['E-mail5'] : null,
                    'capital_social' => $capitalSocial,
                    'porte' => isset($row['Porte']) ? $row['Porte'] : null,
                    'simples_nacional' => isset($row['Simples_Nacional']) ? $row['Simples_Nacional'] : (isset($row['Simples Nacional']) ? $row['Simples Nacional'] : null),
                    'mei' => isset($row['MEI']) ? $row['MEI'] : null,
                    'faturamento_presumido' => isset($row['Faturamento_Presumido']) ? $row['Faturamento_Presumido'] : null,
                    'socio1_nome' => isset($row['Socio1_Nome']) ? $row['Socio1_Nome'] : (isset($row['Socio']) ? $row['Socio'] : null),
                    'socio1_cpf' => isset($row['Socio1_CPF']) ? $row['Socio1_CPF'] : null,
                    'socio2_nome' => isset($row['Socio2_Nome']) ? $row['Socio2_Nome'] : null,
                    'socio2_cpf' => isset($row['Socio2_CPF']) ? $row['Socio2_CPF'] : null,
                    'logo_url' => isset($row['logo_url']) ? $row['logo_url'] : null,
                    'website' => isset($row['website']) ? $row['website'] : null,
                    'tagline' => isset($row['tagline']) ? $row['tagline'] : null,
                    'numero_funcionarios' => isset($row['Numero_Funcionarios_range_end']) ? $row['Numero_Funcionarios_range_end'] : null,
                    'descricao' => isset($row['Descricao']) ? $row['Descricao'] : null,
                    'url_linkedin' => isset($row['Perfil_url-Linkedin']) ? $row['Perfil_url-Linkedin'] : null,
                    'url_insta' => isset($row['Perfil_url_Instagram']) ? $row['Perfil_url_Instagram'] : null,
                    'url_face' => isset($row['Perfil_url_Facebook']) ? $row['Perfil_url_Facebook'] : null,
                ];

                //$this->db->table('lead1')->delete( ['cnpj' => $row['CNPJ']]);
                $this->db->table('lead1')->insert($insert_data);
            }

            $datas['sem_cnpj'] = $sem_cnpj;
            $datas['success'] = true;
            $datas['message'] = lang2('file') . ' ' . lang2('csvimportsuccess');
        } else {
            $datas['sem_cnpj'] = $sem_cnpj;
            $datas['success'] = true;
            $datas['message'] = lang2('erro ao gerar array');
            $datas['array'] = $arr;
        }


        return response()->setJSON($datas);
        // } else {
        //     $datas['success'] = false;
        //     $datas['message'] = lang2('you_dont_have_permission');
        //     return response()->setJSON($datas);
        // }
    }
}
