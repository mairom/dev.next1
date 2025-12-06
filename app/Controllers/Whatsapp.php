<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp extends BaseController
{
    private $user;

    function __construct()
    {
        parent::loadModels();
    }

    function index()
    {
        $this->user = get_user();
        $data = [];
        $data['title'] = "Chat WhatsApp";

        return view('whatsapp/index', $data);
    }


    function envia_mensagem_whatsapp_selects()
    {
        $selectedLeads = request()->getPost('selectedLeads');
        $mensagem = request()->getPost('mensagem');
        $media = request()->getPost('media'); // base64
        $resultado = ['success' => false, 'message' => ''];

        $settings_ia = $this->db->table('settings_ia')
            ->where('id_company', session()->id_company)
            ->where('staff_id', session()->usr_id)
            ->get()
            ->getRowArray();

        if (!$settings_ia || $settings_ia['connected'] == "0") {
            return response()->setJSON(['success' => false, 'message' => 'WhatsApp não configurado']);
        }

        foreach ($selectedLeads as $id_lead) {
            // consulta lead


            $matrizAtv = [
                'data' => date('Y-m-d'),
                'id_lead' => $id_lead,
                'anotacoes' => 'Envio de mensagem em massa: <br>' . $mensagem,
                'reuniao_call' => "0",
                'atividade' => '11',
                'retorno' => date('Y-m-d'),
                'dt_entrada' => date('Y-m-d H:i:s'),
                'id_criador' => session()->usr_id,
                'is_reuniao' => '0',
            ];

            $response = $this->db->table('leads_atv')->insert($matrizAtv);


            $numeros = [];
            $lead = $this->db->table('leads l')
                ->select('l.id AS id_lead, l.name,  l.email, l.phone, l.company')
                ->where('id', $id_lead)
                ->get()
                ->getRowArray();
            $nm_contato_principal = !empty($lead['company']) ?? $lead['name'];
            $emailCttPrincipal =  $lead['email'];

            if (!empty($lead['phone'])) {
                $number = $lead['phone'];
                if (substr($number, 0, 2) !== '55') $number = "55" . $number;
                $numeros[] = $number;
            }

            $contatos =  $this->db->table('leads_contatos')
                ->where('id_lead', $id_lead)
                ->get()
                ->getResultArray();


            foreach ($contatos as $contato) {
                if ($contato['ctt_principal'] == "1") {
                    $nm_contato_principal = $contato['nm_contato'];
                    $emailCttPrincipal =  $contato['email'];
                }

                if (!empty($contato['telefone'])) {
                    $number = $contato['telefone'];
                    if (substr($number, 0, 2) !== '55') $number = "55" . $number;
                    $numeros[] = $number;
                }
            }

            $leads_data =  $this->db->table('leads_data')
                ->where('id_lead', $id_lead)
                ->get()
                ->getRowArray();

            if ($leads_data) {
                $data = json_decode($leads_data['value'], true);
                if (isset($data['Phones'])) {
                    foreach ($data['Phones'] as $phone) {
                        if (!empty($phone['Number']) && ($phone['IsWhatsapp'] == "true" || $phone['IsWhatsapp'])) {
                            $number = $phone['AreaCode'] . $phone['Number'];
                            if (substr($number, 0, 2) !== '55') $number = "55" . $number;
                            $numeros[] = $number;
                        }
                    }
                }
            }

            $numeros = array_unique($numeros);

            foreach ($numeros as $number) {
                $var = [
                    "{email}", "{empresa}", "{nome}", "{numero}"
                ];
                $valor = [
                    $lead['email'] ?: $emailCttPrincipal,
                    $lead['company'] ?: $lead['name'],
                    explode(' ', $nm_contato_principal)[0],
                    $number,
                ];

                $mensagem_formatado = str_replace($var, $valor, $mensagem);


                $data = [
                    "number" => $settings_ia['number'],
                    "number_send" => $number
                ];
                if (!empty($mensagem_formatado)) {
                    $data['text'] = $mensagem_formatado;
                }

                if ($media && isset($media['data'])) {
                    $data['media'] = [
                        'data' => $media['data'],
                        'filename' => $media['filename'],
                        'mimetype' => $media['mimetype']
                    ];
                }

                // envia via API Node.js
                $ch = curl_init('https://apiwhats.ageup.pro/send-message');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $result = curl_exec($ch);

              //  print_r( $result);
                curl_close($ch);
            }
        }

        return response()->setJSON(['success' => true, 'message' => 'Enviado com sucesso']);
    }
}
