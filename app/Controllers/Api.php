<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');

use DateTime;
use DateTimeZone;

class Api extends BaseController
{
	function __construct()
	{
		parent::loadModels();
	}

	function index()
	{
		echo 'Ciuis RestAPI Service';
	}

	function verifica_servidor()
	{

		$url = "https://apiwhats.ageup.pro/status";
		$email = "mairom99@gmail.com"; // coloque o email de destino
		$email2 = "sandro.filipe@lead1.com.br";
		$subject = "🚨 Alerta: Problema no servidor WhatsApp API";

		try {
			// Fazendo a requisição usando cURL nativo
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10); // timeout de 10s
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // caso tenha SSL self-signed
			$response = curl_exec($ch);
			$error    = curl_error($ch);
			curl_close($ch);

			if ($error) {
				// erro de conexão
				$body = "Não foi possível conectar ao servidor: $url \nErro: $error";
				$this->Emails_Model->send_email($email, $email, $subject, $body, '',  null, null,  null, $email2);
				return;
			}

			// Decodifica o retorno JSON
			$data = json_decode($response, true);

			if (!isset($data['status']) || $data['status'] !== "ok") {
				// retorno inesperado ou status != ok
				$body = "Servidor retornou resposta inesperada:\n\n" . $response;
				$this->Emails_Model->send_email($email, $email, $subject, $body, '',  null, null,  null, $email2);
			} else {
				echo "tudo ok";
			}
		} catch (\Exception $e) {
			// qualquer exceção
			$body = "Erro ao verificar servidor:\n" . $e->getMessage();
			$this->Emails_Model->send_email($email, $email, $subject, $body, '',  null, null,  null, $email2);
		}
	}

	function registra_atividade_lead()
	{
		$data = request()->getJSON(true); // true = array
		$numero_api = $data['numero_api'] ?? null;
		$numero     = $data['numero'] ?? null;
		$texto      = $data['texto'] ?? null;
		//$numero_api = "551152380830"; // request()->getPost('numero_api');
		//$numero =  "5519998088772"; //request()->getPost('numero');
		//$texto =  "Ola teste?"; //request()->getPost('texto');
		$id_lead = "0";

		$settings_ia = $this->db->table('settings_ia')
			->where('number', $numero_api)
			->get()
			->getRowArray();

		$lead = $this->db->table('leads')
			->select('leads.id as id_lead')
			->join('staff', 'staff.id = leads.staff_id', 'inner')
			->groupStart()
			->where('leads.phone', $numero)
			->orWhere('leads.phone', preg_replace('/^55/', '', $numero))
			->groupEnd()
			->where('staff.id_company', $settings_ia['id_company'])
			->get()
			->getRowArray();

		//	print_r($lead->getCompiledSelect());
		//exit;

		if ($lead) {
			$id_lead =  $lead['id_lead'];
		} else {
			$contatos =  $this->db->table('leads_contatos')
				->join('leads', 'leads.id = leads_contatos.id_lead', 'inner')
				->join('staff', 'staff.id = leads.staff_id', 'inner')
				->orderBy('id_lead_contato', 'desc')
				->groupStart()
				->where('telefone', $numero)
				->orWhere('telefone', preg_replace('/^55/', '', $numero))
				->groupEnd()
				->where('staff.id_company', $settings_ia['id_company'])
				->get()
				->getRowArray();

			//print_r($contatos->getCompiledSelect());
			//exit;

			if ($contatos) {
				$id_lead =  $contatos['id_lead'];
			}
		}



		//	print_r($lead->getCompiledSelect());
		//	exit;

		if ($id_lead == "0") {


			if ($settings_ia && $settings_ia['status_id'] != null && $settings_ia['status_id'] != '0') {
				$lead = $this->db->table('leads');
				$lead->insert([
					'date_contacted' => date('Y-m-d H:i:s'),
					'name' => $numero,
					'tp_pessoa' => '1',
					'description' => 'Adicionado automaticamente, chamado pelo whatsApp',
					'phone' =>  $numero,
					'is_whatsApp' => '1',
					'assigned_id' => $settings_ia['staff_id'],
					'created' => date('Y-m-d'),
					'status' => $settings_ia['status_id'],
					'source' => '7',
					'dateassigned' => date('Y-m-d'),
					'dt_nascimento' => date('Y-m-d'),
					'staff_id' => $settings_ia['staff_id'],
					'lead_status_id' => '1',
					'type' => '0'
				]);

				// Pega o último ID inserido
				$id_lead = $this->db->insertID();
			}
		}

		if ($id_lead != "0") {
			$this->db->table('leads_atv')->insert([
				'id_lead' => $id_lead,
				'dt_entrada' => date('Y-m-d H:i:s'),
				'anotacoes' => 'Mensagem whatsApp <br>' . $texto,
				'data' => date('Y-m-d'),
				'horario' => date('H:i:s'),
				'retorno' => date('Y-m-d', strtotime('+1 days')),
				'atividade' => '11',
			]);
		}
	}

	function settings()
	{

		$settings = $this->Settings_Model->get_settings_ciuis();
		$settings['smtppassoword'] = '********';
		$settings['timers'] = $this->Settings_Model->if_timer();
		$newnotification = $this->Notifications_Model->get_notification();
		$settings['newnotification'] = $newnotification ? true : false;
		$settings['notifications'] = $newnotification;

		$settings['settings_ia'] = $this->db->table('settings_ia')->where('staff_id', session()->usr_id)->get()->getRowArray();

		return response()->setJSON($settings);
	}



	function settings_detail($id = null)
	{

		$settings = $this->Settings_Model->get_settings_ciuis_origin($id);
		//$settings['smtppassoword'] = '********';
		$settings['timers'] = $this->Settings_Model->if_timer();
		$settings['newnotification'] = $this->Notifications_Model->newnotification();
		$settings['usr_id'] = session()->get('usr_id');
		$settings['id_company'] = session()->get('id_company');

		return response()->setJSON($settings);
	}

	public function create_lead($token)
	{
		$key = '1245632112456321'; // mesma chave
		$iv = '1234567812345678';  // IV fixo
		$id_list = openssl_decrypt(
			hex2bin($token),
			'AES-128-CBC',
			$key,
			OPENSSL_RAW_DATA,
			$iv
		);

		$settings_funis = $this->db->table('settings_funis')
			->where('id_list', $id_list)
			->get()
			->getRowArray();

		$data = request()->getJSON(true);

		if (!$data || !$settings_funis) {
			return response()->setJSON([
				'status'  => 'error',
				'message' => 'Nenhum dado recebido.'
			])->setStatusCode(400);
		}

		// 🔑 Verificar duplicidade pelo LinkedIn do contato
		$linkedinContato = $data['url_linkedin_contato'] ?? null;
		$id_lead = null;

		if ($linkedinContato) {
			$existente = $this->db->table('leads_contatos')
				->select('id_lead')
				->where('linkedin', $linkedinContato)
				->get()
				->getRowArray();

			if ($existente) {
				$id_lead = $existente['id_lead'];
			}
		}

		// Se não existe, cria o lead
		if (!$id_lead) {
			$name = 'Lead sem nome';
			if (isset($data['razao_social_lead']) && !empty($data['razao_social_lead'])) {
				$name = $data['razao_social_lead'];
			} else 	if (isset($data['nome_contato']) && !empty($data['nome_contato'])) {
				$name = $data['nome_contato'];
			}

			$leadData = [
				'name' =>  $name,
				'company' => $name,
				'setor_atividade' => $data['ramo_lead'] ?? null,
				'linkedin' => $data['url_linkedin_lead'] ?? null,
				'address' => $data['localizacao_lead'] ?? null,
				'web_site' => $data['site_lead'] ?? null,
				'website' => $data['dominio_lead'] ?? null,
				'campanha_linkedin' => $data['campanha_lead'] ?? null,

				'email' => $data['email'] ?? null,
				'phone' => $data['telefone1'] ?? null,
				'description' => $data['sobre'] ?? 'Adicionado automaticamente',
				'dt_nascimento' => $data['aniversario'] ?? null,

				'assigned_id' => $settings_funis['staff_id'],
				'status' => $settings_funis['id_status'],
				'staff_id' => $settings_funis['staff_id'],

				'created' => date('Y-m-d'),
				'dateassigned' => date('Y-m-d'),
				'date_contacted' => date('Y-m-d H:i:s'),

				'source' => '508',
				'tp_pessoa' => '1',
				'is_whatsApp' => '0',
				'lead_status_id' => '1',
				'type' => '0',
			];

			$builder = $this->db->table('leads');
			$builder->insert($leadData);
			$id_lead = $this->db->insertID();

			// Inserir contato principal
			if (!empty($data['nome_contato'])) {
				$leads_contatos = [
					'id_lead' => $id_lead,
					'ctt_principal' => '1',
					'dt_criado' => date('Y-m-d H:i:s'),

					'nm_contato' => $data['nome_contato']  ?? 'Contato sem nome',
					'cargo' => $data['cargo_contato'] ?? null,
					'dt_aniversario' =>  $data['aniversario_contato'] ?? null,
					'linkedin' => $linkedinContato,
					'email' => $data['email_contato'] ?? null,
					'email2' => $data['email2_contato'] ?? null,
					'email3' => $data['email3_contato'] ?? null,
					'email4' => $data['email4_contato'] ?? null,
					'messenger1' => $data['Messenger1_contato'] ?? null,
					'messenger2' => $data['Messenger2_contato'] ?? null,
					'telefone' => $data['Telefone1_contato'] ?? null,
					'telefone2' => $data['Telefone2_contato'] ?? null,
					'sobre' => $data['sobre_contato'] ?? null,
					'resumo_contato' => $data['resumo_contato'] ?? null,
					'inicio_empresa' => $data['inicio_empresa_contato'] ?? null,
					'data_conexao_linkedin_contato' => $data['data_conexao_linkedin_contato'] ?? null,
					'conexoes_em_comum_contato' => $data['conexoes_em_comum_contato'] ?? null,
					'conexoes_no_linkedin_contato' => $data['conexoes_no_linkedin_contato'] ?? null,
				];

				$this->db->table('leads_contatos')->insert($leads_contatos);
			}
		}

		// ✅ Inserir mensagens mesmo se já existir o lead
		if (!empty($data['last_sent_message_from'])) {
			$leads_atv = [
				'id_lead' => $id_lead,
				'dt_entrada' => date('Y-m-d H:i:s'),
				'data' => date('Y-m-d'),
				'retorno' => date('Y-m-d'),
				'horario' => date('H:i:s'),
				'atividade' => '13',
				'anotacoes' => ($data['last_sent_message_from'] ?? '') . ':' .
					($data['last_sent_message_text'] ?? '') . '<br>' .
					($data['last_sent_message_send_at'] ?? ''),
			];
			$this->db->table('leads_atv')->insert($leads_atv);
		}

		if (!empty($data['last_received_message_from'])) {
			$leads_atv2 = [
				'id_lead' => $id_lead,
				'dt_entrada' => date('Y-m-d H:i:s'),
				'data' => date('Y-m-d'),
				'retorno' => date('Y-m-d'),
				'horario' => date('H:i:s'),
				'atividade' => '13',
				'anotacoes' => ($data['last_received_message_from'] ?? '') . ':' .
					($data['last_received_message_text'] ?? '') . '<br>' .
					($data['last_received_message_send_at'] ?? ''),
			];
			$this->db->table('leads_atv')->insert($leads_atv2);
		}

		return response()->setJSON([
			'status'   => 'success',
			'message'  => $id_lead ? 'Lead processado com sucesso.' : 'Lead não encontrado.',
			'id_lead'  => $id_lead,
			'received' => $data
		]);
	}


	function languages()
	{

		$languages = $this->Settings_Model->get_languages();

		$lang = array();

		foreach ($languages as $language) {

			$lang[] = array(

				'name' => lang($language['name']),

				'foldername' => $language['foldername'],

				'id' => $language['id'],

				'langcode' => $language['langcode']

			);
		}

		return response()->setJSON($lang);
	}



	function currencies()
	{
		$jsonstring = file_get_contents('assets/json/currencies.json');
		return response()->setJSON(json_decode($jsonstring));
	}




	function timezones()
	{
		$jsonstring = file_get_contents('assets/json/timezones.json');
		return response()->setJSON(json_decode($jsonstring));
	}


	public function get_appconfig()
	{
		$builder = $this->db->table('appconfig');
		$configs = $builder->get()->getResultArray();

		$data = [];
		foreach ($configs as $config) {
			$data[$config['name']] = $config['value'];
		}

		return response()->setJSON($data);
	}



	function stats()
	{

		$otc = $this->Report_Model->otc();

		$yms = $this->Report_Model->yms();

		$bkt = $this->Report_Model->bkt();

		$ogt = $this->Report_Model->ogt();

		$pay = $this->Report_Model->pay();

		$exp = $this->Report_Model->exp();

		$bht = $this->Report_Model->bht();

		$ohc = $this->Report_Model->ohc();

		$oak = $this->Report_Model->oak();

		$akt = $this->Report_Model->akt();

		$mex = $this->Report_Model->mex();

		$pme = $this->Report_Model->pme();

		$ycr = $this->Report_Model->ycr();

		$oyc = $this->Report_Model->oyc();

		if ($otc > 1) {

			$newticketmsg = lang2('newtickets');
		} else $newticketmsg = lang2('newticket');

		if ($yms > 1) {

			$newcustomermsg = lang2('newcustomers');
		} else $newcustomermsg = lang2('newcustomer');

		if ($bkt > $ogt) {

			$todaysalescolor = 'default';
		} else {

			$todaysalescolor = 'danger';
		}

		$todayrate = $bkt - $ogt;

		if (empty($ogt)) {

			$todayrate = 'N/A';
		} else {

			if ($ogt != 0) {

				$todayrate = floor($todayrate / $ogt * 100);
			}
		}

		if ($bkt > $ogt) {

			$todayicon = 'icon ion-arrow-up-c';
		} else {

			$todayicon = 'icon ion-arrow-down-c';
		}

		$netcashflow = ($pay - $exp);

		if ($bht > $ohc) {

			$weekstat = 'default';
		} else {

			$weekstat = 'danger';
		}

		$weekrate = $bht - $ohc;

		if (empty($ohc)) {

			$weekrate = 'N/A';
		} else {

			if ($ohc != 0) {

				$weekrate = floor($weekrate / $ohc * 100);
			}
		}

		if ($bht > $ohc) {

			$weekratestatus = lang2('increase');
		} else {

			$weekratestatus = lang2('recession');
		}

		if ($akt > $oak) {

			$montearncolor = 'success';

			$monicon = 'icon ion-arrow-up-c';
		} else {

			$montearncolor = 'danger';

			$monicon = 'icon ion-arrow-down-c';
		}

		$oao = $akt - $oak;

		if (empty($oak)) {

			$monmessage = '' . lang2('notyet') . '';
		} else {

			if ($oak != 0) {

				$monmessage = floor($oao / $oak * 100);
			}
		}

		$time = date("H");

		$timezone = date("e");

		if ($time < "12") {

			$daymessage = lang2('goodmorning');

			$dayimage = 'morning.png';
		} else if ($time >= "12" && $time < "17") {

			$daymessage = lang2('goodafternoon');

			$dayimage = 'afternoon.png';
		} else if ($time >= "17" && $time < "19") {

			$daymessage = lang2('goodevening');

			$dayimage = 'evening.png';
		} else if ($time >= "19") {

			$daymessage = lang2('goodnight');

			$dayimage = 'night.png';
		}

		if ($mex > $pme) {

			$expensecolor = 'warning';
		} else {

			$expensecolor = 'danger';
		}

		if ($mex > $pme) {

			$expenseicon = 'icon ion-arrow-up-c';
		} else {

			$expenseicon = 'icon ion-arrow-down-c';
		}

		$expenses = $mex - $pme;

		if (empty($pme)) {

			$expensestatus = '' . lang2('notyet') . '';
		} else {

			if ($pme != 0) {

				$expensestatus = floor($expenses / $pme * 100);
			}
		}

		if ($ycr > $oyc) {

			$yearcolor = 'success';
		} else {

			$yearcolor = 'danger';
		}

		if ($ycr > $oyc) {

			$yearicon = 'icon ion-arrow-up-c';
		} else {

			$yearicon = 'icon ion-arrow-down-c';
		}

		$yearly = $ycr - $oyc;

		$yearmessage = '' . lang2('notyet') . '';

		if (empty($oyc)) {

			$yearmessage = '' . lang2('notyet') . '';
		} else {

			if ($oyc != 0) {

				$yearmessage = floor($yearly / $oyc * 100);
			} else {

				$yearmessage = '' . lang2('notyet') . '';
			}
		}

		$stats = array(

			'mex' => $mex = $this->Report_Model->mex(),

			'pme' => $pme = $this->Report_Model->pme(),

			'bkt' => $bkt = $this->Report_Model->bkt(),

			'bht' => $bht = $this->Report_Model->bht(),

			'ogt' => $ogt = $this->Report_Model->ogt(),

			'ohc' => $ohc = $this->Report_Model->ohc(),

			'otc' => $otc = $this->Report_Model->otc(),

			'ycr' => $ycr = $this->Report_Model->ycr(),

			'oyc' => $oyc = $this->Report_Model->oyc(),

			'oft' => $oft = $this->Report_Model->oft(),

			'tef' => $tef = $this->Report_Model->tef(),

			'vgf' => $vgf = $this->Report_Model->vgf(),

			'tbs' => $tbs = $this->Report_Model->tbs(),

			'akt' => $akt = $this->Report_Model->akt(),

			'oak' => $oak = $this->Report_Model->oak(),

			'tfa' => $tfa = $this->Report_Model->tfa(),

			'yms' => $yms = $this->Report_Model->yms(),

			'ttc' => $ttc = $this->Report_Model->ttc(),

			'ipc' => $ipc = $this->Report_Model->ipc(),

			'atc' => $atc = $this->Report_Model->atc(),

			'ctc' => $ctc = $this->Report_Model->ctc(),

			'put' => $put = $this->Report_Model->put(),

			'pay' => $pay = $this->Report_Model->pay(),

			'exp' => $exp = $this->Report_Model->exp(),

			'twt' => $twt = $this->Report_Model->twt(),

			'clc' => $clc = $this->Report_Model->clc(),

			'mlc' => $mlc = $this->Report_Model->mlc(),

			'mtt' => $mtt = $this->Report_Model->mtt(),

			'mct' => $mct = $this->Report_Model->mct(),

			'ues' => $ues = $this->Report_Model->ues(),

			'myc' => $myc = $this->Report_Model->myc(),

			'tpz' => $tpz = $this->Report_Model->tpz(),

			'nsp' => $nsp = $this->Report_Model->nsp(),

			'sep' => $sep = $this->Report_Model->sep(),

			'pep' => $pep = $this->Report_Model->pep(),

			'cap' => $cap = $this->Report_Model->cap(),

			'cop' => $cop = $this->Report_Model->cop(),

			'tht' => $tht = $this->Report_Model->tht(),

			'total_incomings' => $this->Report_Model->total_incomings(),

			'total_outgoings' => $this->Report_Model->total_outgoings(),

			'not_started_percent' => $tpz > 0 ? number_format(($nsp * 100) / $tpz) : 0,

			'started_percent' => $tpz > 0 ? number_format(($sep * 100) / $tpz) : 0,

			'percentage_percent' => $tpz > 0 ? number_format(($pep * 100) / $tpz) : 0,

			'cancelled_percent' => $tpz > 0 ? number_format(($cap * 100) / $tpz) : 0,

			'complete_percent' => $tpz > 0 ? number_format(($cop * 100) / $tpz) : 0,

			'totalpaym' => $this->Report_Model->totalpaym(),

			'incomings' => $this->Report_Model->incomings(),

			'outgoings' => $this->Report_Model->outgoings(),

			'ysy' => $ysy = ($ttc > 0 ? number_format(($otc * 100) / $ttc) : 0),

			'bsy' => $bsy = ($ttc > 0 ? number_format(($ipc * 100) / $ttc) : 0),

			'twy' => $twy = ($ttc > 0 ? number_format(($atc * 100) / $ttc) : 0),

			'iey' => $iey = ($ttc > 0 ? number_format(($ctc * 100) / $ttc) : 0),

			'ofy' => $ofy = ($tfa > 0 ? number_format(($tef * 100) / $tfa) : 0),

			'clp' => $clp = ($mlc > 0 ? number_format(($clc * 100) / $mlc) : 0),

			'mtp' => $mtp = ($mtt > 0 ? number_format(($mct * 100) / $mtt) : 0),

			'inp' => $inp = ($put > 0 ? number_format(($pay * 100) / $put) : 0),

			'ogp' => $ogp = ($put > 0 ? number_format(($exp * 100) / $put) : 0),

			'newticketmsg' => $newticketmsg,

			'newcustomermsg' => $newcustomermsg,

			'todaysalescolor' => $todaysalescolor,

			'todayrate' => $todayrate,

			'todayicon' => $todayicon,

			'netcashflow' => $netcashflow,

			'weekstat' => $weekstat,

			'weekrate' => $weekrate,

			'weekratestatus' => $weekratestatus,

			'daymessage' => $daymessage,

			'dayimage' => $dayimage,

			'montearncolor' => $montearncolor,

			'monicon' => $monicon,

			'monmessage' => $monmessage,

			'expensecolor' => $expensecolor,

			'expenseicon' => $expenseicon,

			'expensestatus' => $expensestatus,

			'yearcolor' => $yearcolor,

			'yearicon' => $yearicon,

			'yearmessage' => $yearmessage,

			'newnotification' => $this->Notifications_Model->newnotification(),

			'totaltasks' => $totaltasks = $this->Report_Model->totaltasks(),

			'opentasks' => $opentasks = $this->Report_Model->opentasks(),

			'inprogresstasks' => $inprogresstasks = $this->Report_Model->inprogresstasks(),

			'waitingtasks' => $waitingtasks = $this->Report_Model->waitingtasks(),

			'completetasks' => $completetasks = $this->Report_Model->completetasks(),

			'invoice_chart_by_status' => $invoice_chart_by_status = $this->Report_Model->invoice_chart_by_status(),

			'leads_to_win_by_leadsource' => $leads_to_win_by_leadsource = $this->Report_Model->leads_to_win_by_leadsource(),

			'leads_by_leadsource' => $leads_by_leadsource = $this->Report_Model->leads_by_leadsource(),

			'incomings_vs_outgoins' => $leads_by_leadsource = $this->Report_Model->incomings_vs_outgoins(),

			'expenses_by_categories' => $expenses_by_categories = $this->Report_Model->expenses_by_categories(),

			'top_selling_staff_chart' => $top_selling_staff_chart = $this->Report_Model->top_selling_staff_chart(),

			'weekly_sales' => $weekly_expense_chart = $this->Report_Model->weekly_sales(),

			'monthly_expenses' => $this->Report_Model->monthly_expenses(),

			'monthly_sales' => $this->Report_Model->monthly_sales(),

			//'weekly_expense_chart' => $this->Report_Model->weekly_expense_chart(),

			'months' => months(),

		);

		return response()->setJSON($stats);
	}



	function weekly_dashboard_chart()
	{

		$weekly_dash_chart = array(

			'weekly_expenses' => $this->Report_Model->weekly_dashboard_chart(),

		);

		return response()->setJSON($weekly_dash_chart);
	}



	function get_consultant_data()
	{

		if (session()->get('other')) {

			$months = array(

				mb_substr(lang2('january'), 0, 3, 'UTF-8'),

				mb_substr(lang2('february'), 0, 3, 'UTF-8'),

				mb_substr(lang2('march'), 0, 3, 'UTF-8'),

				mb_substr(lang2('april'), 0, 3, 'UTF-8'),

				mb_substr(lang2('may'), 0, 3, 'UTF-8'),

				mb_substr(lang2('june'), 0, 3, 'UTF-8'),

				mb_substr(lang2('july'), 0, 3, 'UTF-8'),

				mb_substr(lang2('august'), 0, 3, 'UTF-8'),

				mb_substr(lang2('september'), 0, 3, 'UTF-8'),

				mb_substr(lang2('october'), 0, 3, 'UTF-8'),

				mb_substr(lang2('november'), 0, 3, 'UTF-8'),

				mb_substr(lang2('december'), 0, 3, 'UTF-8')

			);

			$lang = array(

				'amount' => lang2('amount'),

				'expenses' => lang2('expenses'),

				'sales' => lang2('sales'),

				'sales_vs_expenses' => lang2('sales_vs_expenses')

			);

			$data['months_short'] = $months;

			$data['months'] = months();

			$data['lang'] = $lang;

			$data['totalInvoices'] = $this->Report_Model->totalData('invoices');

			$data['expenses'] = $this->Report_Model->totalData('expenses');

			$data['invoices_thisweek'] = $this->Report_Model->invoices_thisweek();

			$data['expenses_thisweek'] = $this->Report_Model->expenses_thisweek();

			$data['monthly_expenses'] = $this->Report_Model->monthly_expenses();

			$data['monthly_sales'] = $this->Report_Model->monthly_sales();

			return response()->setJSON($data);
		}
	}



	function user()
	{

		$id = session()->get('usr_id');

		$user = $this->Staff_Model->get_staff($id);

		$user_data = array(

			'id' => $user['id'],

			'role_id' => $user['role_id'],

			'language' => $user['language'],

			'name' => $user['staffname'],

			'avatar' => $user['staffavatar'],

			'department_id' => $user['department_id'],

			'phone' => $user['phone'],

			'email' => $user['email'],

			'root' => $user['root'],

			'admin' => $user['admin'],

			'staffmember' => $user['staffmember'],

			'last_login' => $user['last_login'],

			'inactive' => $user['inactive'],

			'appointment_availability' => $user['appointment_availability'],

		);

		return response()->setJSON($user_data);
	}



	function projects()
	{

		$projects = $this->Projects_Model->get_all_projects();

		$data_projects = array();

		foreach ($projects as $project) {

			if (($project['staff_id'] == session()->usr_id) || ($this->Projects_Model->check_member($project['id'], session()->usr_id)) == 'true' || $this->Settings_Model->isAdmin() == 'true') {

				$settings = $this->Settings_Model->get_settings_ciuis();

				$totaltasks = $this->Report_Model->totalprojecttasks($project['id']);

				$opentasks = $this->Report_Model->openprojecttasks($project['id']);

				$completetasks = $this->Report_Model->completeprojecttasks($project['id']);

				$progress = ($totaltasks > 0 ? number_format(($completetasks * 100) / $totaltasks) : 0);

				$project_id = $project['id'];

				switch ($project['status']) {

					case '1':

						$projectstatus = 'notstarted';

						$icon = 'notstarted.png';

						$status = lang2('notstarted');

						break;

					case '2':

						$projectstatus = 'started';

						$icon = 'started.png';

						$status = lang2('started');

						break;

					case '3':

						$projectstatus = 'percentage';

						$icon = 'percentage.png';

						$status = lang2('percentage');

						break;

					case '4':

						$projectstatus = 'cancelled';

						$icon = 'cancelled.png';

						$status = lang2('cancelled');

						break;

					case '5':

						$projectstatus = 'complete';

						$icon = 'complete.png';

						$status = lang2('complete');

						break;
				}

				if ($project['status'] == '5') {

					$projectstatus = 'complete';

					$icon = 'complete.png';

					$status = lang2('completed');

					$progress = 100;
				}

				if ($project['template'] == '1') {

					$projectstatus = 'template';
				}

				$startdate = date(get_dateFormat(), strtotime($project['start_date']));


				$customer = ($project['customercompany']) ? $project['customercompany'] : $project['namesurname'];

				$enddate = $project['deadline'];

				$current_date = new DateTime(date('Y-m-d'), new DateTimeZone('Asia/Dhaka'));

				$end_date = new DateTime("$enddate", new DateTimeZone('Asia/Dhaka'));

				$interval = $current_date->diff($end_date);

				$leftdays = $interval->format('%a day(s)');

				$members = $this->Projects_Model->get_members_index($project_id);

				$milestones = $this->Projects_Model->get_all_project_milestones($project_id);

				$appconfig = get_appconfig();

				$data_projects[] = array(

					'id' => $project['id'],

					'project_id' => $project['id'],

					'name' => $project['name'],

					'pinned' => $project['pinned'],

					'value' => $project['projectvalue'],

					'tax' => $project['tax'],

					'template' => $project['template'],

					'status_id' => $project['status'],

					'progress' => $progress,

					'startdate' => $startdate,

					'leftdays' => $leftdays,

					'customer' => $customer,

					'customeremail' => $project['customeremail'],

					'status_icon' => $icon,

					'status' => $status,

					'status_class' => $projectstatus,

					'customer_id' => $project['customer_id'],

					'members' => $members,

					'milestones' => $milestones,

					lang2('filterbystatus') => lang($projectstatus),

					lang2('filterbycustomer') => $customer,

					'project_number' => get_number('projects', $project['id'], 'project', 'project'),

				);
			}
		};

		return response()->setJSON($data_projects);
	}



	function notes()
	{

		$relation_type = request()->getUri()->getSegment(3);

		$relation_id = request()->getUri()->getSegment(4);

		$builder = $this->db->table('notes');
		$builder->select('*, staff.staffname as notestaff, notes.id as id');
		$builder->join('staff', 'notes.addedfrom = staff.id', 'left');
		$builder->where(['relation' => $relation_id, 'relation_type' => $relation_type]);
		$builder->orderBy('notes.id', 'desc');
		$notes = $builder->get()->getResultArray();

		$data_projectnotes = array();

		foreach ($notes as $note) {

			$data_projectnotes[] = array(

				'id' => $note['id'],

				'description' => $note['description'],

				'staffid' => $note['addedfrom'],

				'staff' => $note['notestaff'],

				'date' => _adate($note['created']),

			);
		};

		return response()->setJSON($data_projectnotes);
	}



	function discussions()
	{

		$relation_type = request()->getUri()->getSegment(3);

		$relation_id = request()->getUri()->getSegment(4);

		$builder = $this->db->table('discussions');
		$builder->select('*, contacts.name as discussion_contact_name, contacts.surname as discussion_contact_surname, staff.staffname as discussion_staff, discussions.id as id');
		$builder->join('staff', 'discussions.staff_id = staff.id', 'left');
		$builder->join('contacts', 'discussions.contact_id = contacts.id', 'left');
		$builder->where(['relation' => $relation_id, 'relation_type' => $relation_type]);
		$discussions = $builder->get()->getResultArray();

		$data_discussions = array();



		foreach ($discussions as $discussion) {

			$builder = $this->db->table('discussion_comments');
			$builder->where('discussion_id', $discussion['id']);
			$comments = $builder->get()->getResultArray();


			$data_discussions[] = array(

				'id' => $discussion['id'],

				'subject' => $discussion['subject'],

				'description' => $discussion['description'],

				'datecreated' => date(DATE_ISO8601, strtotime($discussion['datecreated'])),

				'staff_id' => $discussion['staff_id'],

				'staff' => $discussion['discussion_staff'],

				'contact_id' => $discussion['contact_id'],

				'contact' => '' . $discussion['discussion_contact_name'] . ' ' . $discussion['discussion_contact_surname'] . '',

				'comments' => $comments,

			);
		};

		return response()->setJSON($data_discussions);
	}



	function discussion_comments($id)
	{

		$builder = $this->db->table('discussion_comments');
		$builder->where('discussion_id', $id);
		$comments = $builder->get()->getResultArray();


		return response()->setJSON($comments);
	}



	function weekly_incomings()
	{

		$allsales[] = $this->Report_Model->weekly_incomings();

		for ($i = 0; $i < count($allsales); $i++) {

			foreach ($allsales[$i] as $salesc) {

				$salesday = date('l', strtotime($salesc['date']));

				$salestotal = $salesc['total'];

				$data_timelogs = array();

				foreach (weekdays_git() as $dayc) {

					if ($salesday == $dayc) {

						$total = $salestotal;
					} else $total = 0;

					$data_timelogs[] = array(

						'day' => $dayc,

						'amount' => $total,

						'type' => 'incoming',

					);
				}
			}
		}

		return response()->setJSON($data_timelogs);
	}



	function milestones()
	{

		$milestones = $this->Projects_Model->get_all_milestones();

		$data_milestones = array();

		foreach ($milestones as $milestone) {

			$data_milestones[] = array(

				'id' => $milestone['id'],

				'milestone_id' => $milestone['id'],

				'name' => $milestone['name'],

				'project_id' => $milestone['project_id'],

			);
		};

		return response()->setJSON($data_milestones);
	}

	function staff($global = false)
	{
		$staffs = $this->Staff_Model->get_all_staff($global);
		$data_staffs = array();

		foreach ($staffs as $staff) {
			//if ($staff['id'] != session()->usr_id) {
			$data_staffs[] = array(
				'id' => $staff['id'],
				'name' => $staff['staffname'],
				'id_company' => $staff['id_company_staff'],
				'email' => $staff['email'],
				'staff_number' => get_number('staff', $staff['id'], 'staff', 'staff'),
			);
			//}
		};

		return response()->setJSON($data_staffs);
	}



	function departments()
	{

		$departments = $this->Settings_Model->get_departments();

		$data_departments = array();

		foreach ($departments as $department) {

			$data_departments[] = array(

				'id' => $department['id'],

				'name' => $department['name'],

			);
		};

		return response()->setJSON($data_departments);
	}



	function expenses_by_relation()
	{

		$relation_type = request()->getUri()->getSegment(3);

		$relation_id = request()->getUri()->getSegment(4);

		$expenses = $this->Expenses_Model->get_all_expenses_by_relation($relation_type, $relation_id);

		$data_expenses = array();

		foreach ($expenses as $expense) {

			$expensedate = date(get_dateFormat(), strtotime($expense['date']));

			if ($expense['invoice_id'] == NULL) {

				$billstatus = lang2('notbilled') and $color = 'warning'

					and $billstatus_code = 'false';
			} else $billstatus = lang2('billed') and $color = 'success'

				and $billstatus_code = 'true';

			if ($expense['customer_id'] != 0) {

				$billable = 'true';
			} else {

				$billable = 'false';
			}

			if ($expense['internal'] == '1') {

				$billstatus = lang2('internal') and $color = 'success';
			}

			$appconfig = get_appconfig();

			$data_expenses[] = array(

				'id' => $expense['id'],

				'title' => $expense['title'],

				'prefix' => $appconfig['expense_prefix'],

				'longid' => get_number('expenses', $expense['id'], 'expense', 'expense'),

				'amount' => $expense['amount'],

				'staff' => $expense['staff'],

				'category' => $expense['category'],

				'billstatus' => $billstatus,

				'billstatus_code' => $billstatus_code,

				'color' => $color,

				'billable' => $billable,

				'date' => $expensedate,

			);
		};

		return response()->setJSON($data_expenses);
	}



	function expensescategories()
	{
		$expensescategories = $this->Expenses_Model->get_all_expensecat();
		$data_expensescategories = array();

		foreach ($expensescategories as $category) {
			$amtbc = 0;
			$catid = $category['id'];
			$amountby = $this->Report_Model->expenses_amount_by_category($catid);
			if ($amountby != NULL) {
				$amtbc = $amountby;
			}
			$percent = $this->Report_Model->expenses_percent_by_category($catid);

			$listInvoice = request()->getPost('listInvoice') != 1 || !request()->getPost('listInvoice') ? true : false;

			if ($amtbc > 0 || $listInvoice) {
				$data_expensescategories[] = array(
					'id' => $category['id'],
					'name' => $category['name'],
					'id_vendor' => $category['id_vendor'],
					'ctg_system' => $category['ctg_system'],
					'description' => $category['description'],
					'amountby' => $amtbc,
					'percent' => $percent,
				);
			}
		};

		$data_expensescategories = $this->array_sort_by_column($data_expensescategories, 'amountby');

		return response()->setJSON($data_expensescategories);
	}


	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
	{
		$sort_col = array();
		foreach ($arr as $key => $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
		return $arr;
	}



	function proposals()
	{

		$proposals = array();

		if ($this->Privileges_Model->check_privilege('proposals', 'all')) {

			$proposals = $this->Proposals_Model->get_all_proposals_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('proposals', 'own')) {

			$proposals = $this->Proposals_Model->get_all_proposals_by_privileges(session()->usr_id);
		}

		$data_proposals = array();

		foreach ($proposals as $proposal) {

			$pro = $this->Proposals_Model->get_proposals($proposal['id'], $proposal['relation_type']);

			if ($pro['relation_type'] == 'customer') {

				if (($pro['customercompany'] === NULL) || ($pro['customercompany'] == '')) {

					$customer = $pro['namesurname'];

					$customer_email = $pro['toemail'];
				} else {

					$customer = $pro['customercompany'];

					$customer_email = $pro['toemail'];
				}
			}

			if ($pro['relation_type'] == 'lead') {

				$customer = $pro['leadname'];

				$customer_email = $pro['toemail'];
			}


			$date = date(get_dateFormat(), strtotime($proposal['date']));
			$opentill = date(get_dateFormat(), strtotime($proposal['opentill']));

			switch ($proposal['status_id']) {

				case '0':

					$status = lang2('quote') . ' ' . lang2('request');

					$class = 'proposal-status-open';

					break;

				case '1':

					$status = lang2('draft');

					$class = 'proposal-status-accepted';

					break;

				case '2':

					$status = lang2('sent');

					$class = 'proposal-status-sent';

					break;

				case '3':

					$status = lang2('open');

					$class = 'proposal-status-open';

					break;

				case '4':

					$status = lang2('revised');

					$class = 'proposal-status-revised';

					break;

				case '5':

					$status = lang2('declined');

					$class = 'proposal-status-declined';

					break;

				case '6':

					$status = lang2('accepted');

					$class = 'proposal-status-accepted';

					break;
			};

			$appconfig = get_appconfig();

			$data_proposals[] = array(

				'id' => $proposal['id'],

				'assigned' => $proposal['assigned'],

				'prefix' => $appconfig['proposal_prefix'],

				'longid' => get_number('proposals', $proposal['id'], 'proposal', 'proposal'),

				'subject' => $proposal['subject'],

				'customer' => $customer,

				'relation' => $proposal['relation'],

				'date' => $date,

				'opentill' => $opentill,

				'status' => $status,

				'status_id' => $proposal['status_id'],

				'staff' => $proposal['staffmembername'],

				'staffavatar' => $proposal['staffavatar'],

				'total' => (float)$proposal['total'],

				'class' => $class,

				'relation_type' => $proposal['relation_type'],

				'customer_email' => $customer_email,

				'' . lang2('relationtype') . '' => $proposal['relation_type'],

				'' . lang2('filterbystatus') . '' => $status,

				'' . lang2('filterbycustomer') . '' => $customer,

				'' . lang2('filterbyassigned') . '' => $proposal['staffmembername'],

			);
		};

		return response()->setJSON($data_proposals);
	}



	function invoices()
	{
		$invoices = array();
		$duedate = '';
		$created = '';
		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {
			$invoices = $this->Invoices_Model->get_all_invoices_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {
			$invoices = $this->Invoices_Model->get_all_invoices_by_privileges(session()->usr_id);
		}

		$data_invoices = array();
		foreach ($invoices as $invoice) {

			$created =  date(get_dateFormat(), strtotime($invoice['created']));
			$duedate = date(get_dateFormat(), strtotime($invoice['duedate']));

			if ($invoice['duedate'] == 0000 - 00 - 00) {
				$realduedate = 'No Due Date';
			} else {
				$realduedate = $duedate;
			}

			$totalx = $invoice['total'];

			$builder = $this->db->table('payments')
				->selectSum('amount')
				->where('invoice_id', $invoice['id']);

			$paytotal = $builder->get()->getRow();

			$balance = $totalx - $paytotal->amount;

			if ($paytotal->amount < $invoice['total'] && $paytotal->amount > 0 && $invoice['status_id'] == 3) {
				$invoicestatus = lang2('partial');
				$color = 'warning';
			} else {
				if ($balance > 0) {
					$invoicestatus = lang2('unpaid');
					$color = 'danger';
				} else {
					$invoicestatus = lang2('paidinv');
					$color = 'success';
				}

				if ($paytotal->amount < $invoice['total'] && $paytotal->amount > 0) {
					$invoicestatus = lang2('partial');
					$color = 'warning';
				}

				if ($invoice['status_id'] == 3) {
					$invoicestatus = lang2('unpaid');
					$color = 'danger';
				}
			}


			if ($invoice['status_id'] == 1) {
				$invoicestatus = lang2('draft');
				$color = 'muted';
			}

			if ($invoice['status_id'] == 4) {
				$invoicestatus = lang2('cancelled');
				$color = 'danger';
			}

			if ($invoice['type'] == 1) {
				$customer = $invoice['individual'];
			} else {
				$customer = $invoice['customercompany'];
			}

			$appconfig = get_appconfig();

			$data_invoices[] = array(

				'id' => $invoice['id'],

				'prefix' => $appconfig['inv_prefix'],

				'longid' => get_number('invoices', $invoice['id'], 'invoice', 'inv'),

				'created' => $created,

				'duedate' => $realduedate,

				'customer' => $customer,

				'customer_id' => $invoice['customer_id'],

				'recurring_status' => $invoice['recurring_status'] == '0' ? true : false,

				'staff_id' => $invoice['staff_id'],

				'total' => (float)$invoice['total'],

				'status' => $invoicestatus,

				'color' => $color,

				'' . lang2('filterbystatus') . '' => $invoicestatus,

				'' . lang2('filterbycustomer') . '' => $customer,

			);
		};

		return response()->setJSON($data_invoices);
	}



	function dueinvoices()
	{

		$dueinvoices = array();

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {

			$dueinvoices = $this->Invoices_Model->dueinvoices();
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {

			$dueinvoices = $this->Invoices_Model->dueinvoices_by_staff();
		}

		if ($dueinvoices) {

			$data_dueinvoices = array();

			foreach ($dueinvoices as $invoice) {

				if ($invoice['type'] == 1) {

					$customer = $invoice['individual'];
				} else $customer = $invoice['customercompany'];

				$data_dueinvoices[] = array(

					'id' => $invoice['id'],

					'total' => $invoice['total'],

					'customer' => $customer,

				);
			};

			return response()->setJSON($data_dueinvoices);
		}
	}



	function overdueinvoices()
	{

		$overdueinvoices = array();

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {

			$overdueinvoices = $this->Invoices_Model->overdueinvoices();
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {

			$overdueinvoices = $this->Invoices_Model->overdueinvoices_by_staff();
		}

		if ($overdueinvoices) {

			$data_overdueinvoices = array();

			foreach ($overdueinvoices as $invoice) {

				if ($invoice['type'] == 1) {

					$customer = $invoice['individual'];
				} else $customer = $invoice['customercompany'];

				$today = time();

				$duedate = strtotime($invoice['duedate']); // or your date as well

				$created = strtotime($invoice['created']);

				$paymentday = $duedate - $created; // Calculate days left.

				$paymentx = $today - $created;

				$datepaymentnet = $paymentday - $paymentx;

				if ($datepaymentnet < 0) {

					$status = '' . floor($datepaymentnet / (60 * 60 * 24)) . ' days';
				};

				$data_overdueinvoices[] = array(

					'id' => $invoice['id'],

					'total' => $invoice['total'],

					'customer' => $customer,

					'status' => $status,

				);
			};

			return response()->setJSON($data_overdueinvoices);
		}
	}



	function reminders($print = true)
	{

		$reminders = $this->Trivia_Model->get_reminders();
		$data_reminders['public'] = $this->public_reminders();
		$data_reminders['private'] = array();

		foreach ($reminders as $reminder) {
			switch ($reminder['relation_type']) {
				case 'event':
					$remindertitle = lang2('eventreminder');
					break;
				case 'lead':
					$remindertitle = lang2('leadreminder');
					break;
				case 'customer':
					$remindertitle = lang2('customerreminder');
					break;
				case 'invoice':
					$remindertitle = lang2('invoicereminder');
					break;
				case 'expense':
					$remindertitle = lang2('expensereminder');
					break;
				case 'ticket':
					$remindertitle = lang2('ticketreminder');
					break;
				case 'proposal':
					$remindertitle = lang2('proposalreminder');
					break;
			};

			$data_reminders['private'][] = array(
				'id' => $reminder['id'],
				'title' => $remindertitle,
				'date' => date(DATE_ISO8601, strtotime($reminder['date'])),
				'description' => $reminder['description'],
				'creator' => $reminder['remindercreator'],
			);
		};
		if ($print) {
			return response()->setJSON($data_reminders);
		} else {
			return array_merge($data_reminders['public'], $data_reminders['private']);
		}
	}



	function public_reminders()
	{

		$reminders_public = $this->Trivia_Model->get_event_public_reminders();

		$data_reminders = array();

		foreach ($reminders_public as $reminder) {

			switch ($reminder['relation_type']) {

				case 'event':

					$remindertitle = lang2('eventreminder');

					break;

				case 'lead':

					$remindertitle = lang2('leadreminder');

					break;

				case 'customer':

					$remindertitle = lang2('customerreminder');

					break;

				case 'invoice':

					$remindertitle = lang2('invoicereminder');

					break;

				case 'expense':

					$remindertitle = lang2('expensereminder');

					break;

				case 'ticket':

					$remindertitle = lang2('ticketreminder');

					break;

				case 'proposal':

					$remindertitle = lang2('proposalreminder');

					break;
			};

			$data_reminders[] = array(

				'id' => $reminder['id'],

				'title' => $remindertitle,

				'date' => $reminder['date'],

				'description' => $reminder['description'],

				'creator' => $reminder['remindercreator'],

			);
		}

		return $data_reminders;
	}



	function reminders_by_type()
	{

		$relation_type = request()->getUri()->getSegment(3);

		$relation_id = request()->getUri()->getSegment(4);

		$builder = $this->db->table('reminders');
		$builder->select('*, staff.staffname as staff, staff.staffavatar as avatar, reminders.id as id');
		$builder->join('staff', 'reminders.staff_id = staff.id', 'left');
		$builder->where(['relation' => $relation_id, 'relation_type' => $relation_type]);
		$reminders = $builder->get()->getResultArray();

		$data_reminders = array();

		foreach ($reminders as $reminder) {

			$data_reminders[] = array(

				'id' => $reminder['id'],

				'date' => _adate($reminder['date']),

				'description' => $reminder['description'],

				'creator' => $reminder['staff'],

				'avatar' => base_url('uploads/images/' . $reminder['avatar'] . ''),

			);
		};

		return response()->setJSON($data_reminders);
	}



	function notifications()
	{

		$notifications = $this->Notifications_Model->get_all_notifications();
		$data_notifications = array();
		foreach ($notifications as $notification) {
			switch ($notification['markread']) {
				case 0:
					$read = true;
					break;
				case 1:
					$read = false;
					break;
			};

			$data_notifications[] = array(
				'id' => $notification['notifyid'],
				'target' => $notification['target'],
				'date' => tes_ciuis($notification['date']),
				'detail' => $notification['detail'],
				'avatar' => $notification['perres'],
				'read' => $read,
			);
		}

		return response()->setJSON([
			"notifications" => $data_notifications,
			"reminders" => $this->reminders(false),
			"leadsAtrasados" => $this->getLeadsAtrasados(false),
		]);
	}


	function getLeadsAtrasados()
	{

		$sql = $this->db->query("SELECT *, leads.id as id_lead,
		if(leads.name = '', leads.company, leads.name) as nm_lead
		FROM `leads`
		INNER JOIN staff ON leads.assigned_id = staff.id
		INNER JOIN leadsstatus ON leads.status = leadsstatus.id
		INNER JOIN leads_list ON leads_list.id_list = leadsstatus.id_list
		WHERE (SELECT retorno FROM `leads_atv` WHERE id_lead = leads.id order by leads_atv.retorno DESC limit 1) < cast(now() as date)
		AND staff.id_company = '" . session()->id_company . "'
		AND lost = '0'
		")->result();
		return $sql;
	}



	function mark_read_ntf()
	{
		$this->db->table('notifications')
			->where('public', '1')
			->orWhere('staff_id', session()->get('usr_id'))
			->update(['markread' => 1]);

		echo true;
	}

	function marcaLido($id)
	{
		$this->db->table('notifications')
			->where('staff_id', session()->get('usr_id'))
			->where('id', $id)
			->update(['markread' => 1]);

		echo true;
	}


	function chats()
	{
		$chats = [];
		$chats = $this->Chats_Model->get_all_chats_by_privileges();
		$data_chats = [];
		if ($chats) {

			foreach ($chats as $chat) {

				$builder = $this->db->table('chatreplies');
				$builder->select('*');
				$builder->where('(chat_staff1 = ' . session()->get('usr_id') . ' and chat_staff2 = ' . $chat['id'] . ') or (chat_staff2 = ' . session()->get('usr_id') . ' and chat_staff1 = ' . $chat['id'] . ')');
				$builder->orderBy('date', 'desc');
				$builder->limit(1);
				$query = $builder->get();
				$ultimaMsg = $query->getRowArray();


				$data_chats[] = array(
					'id' => $chat['id'],
					'msg_nova' =>  !empty($ultimaMsg) && $ultimaMsg['send_view'] == "" && $ultimaMsg['send_staff'] != session()->usr_id ?   1 : 0,
					'ultimaMsg' => $ultimaMsg ? $ultimaMsg['message'] : null,
					'lastreply' => $ultimaMsg ? (date(get_dateTimeFormat(), strtotime($ultimaMsg['date']))) : lang2('n_a'),
					'details' => $this->getDetails($chat['id']),
				);
			};
		}
		return response()->setJSON($data_chats);
	}

	function getDetails($id)
	{
		$builder = $this->db->table('staff');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('id', $id);
		$query = $builder->get();
		return $query->getRowArray();
	}


	function tickets()
	{

		$tickets = array();

		if ($this->Privileges_Model->check_privilege('tickets', 'all')) {

			$tickets = $this->Tickets_Model->get_all_tickets_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('tickets', 'own')) {

			$tickets = $this->Tickets_Model->get_all_tickets_by_privileges(session()->usr_id);
		}

		if ($tickets) {

			$data_tickets = array();

			foreach ($tickets as $ticket) {

				switch ($ticket['priority']) {

					case '1':

						$priority = lang2('low');

						break;

					case '2':

						$priority = lang2('medium');

						break;

					case '3':

						$priority = lang2('high');

						break;
				};

				$data_tickets[] = array(

					'id' => $ticket['id'],

					'subject' => $ticket['subject'],

					'message' => $ticket['message'],

					'staff_id' => $ticket['staff_id'],

					'contactname' => '' . $ticket['contactname'] . ' ' . $ticket['contactsurname'] . '',

					'priority' => $priority,

					'priority_id' => $ticket['priority'],

					'lastreply' => $ticket['lastreply'] ? (date(get_dateTimeFormat(), strtotime($ticket['lastreply']))) : lang2('n_a'),

					'status_id' => $ticket['status_id'],

					'customer_id' => $ticket['customer_id'],

					'contactemail' => $ticket['contactemail'],

					'ticket_number' => get_number('tickets', $ticket['id'], 'ticket', 'ticket'),

				);
			};

			return response()->setJSON($data_tickets);
		}
	}



	function newtickets()
	{
		$newtickets = array();
		if ($this->Privileges_Model->check_privilege('tickets', 'all')) {
			$newtickets = $this->Tickets_Model->get_all_open_tickets();
		} else if ($this->Privileges_Model->check_privilege('tickets', 'own')) {
			$newtickets = $this->Tickets_Model->get_all_open_tickets_by_staff();
		}

		if ($newtickets) {
			$data_newtickets = array();
			foreach ($newtickets as $ticket) {

				switch ($ticket['priority']) {

					case '1':

						$priority = lang2('low');

						break;

					case '2':

						$priority = lang2('medium');

						break;

					case '3':

						$priority = lang2('high');

						break;
				};

				$data_newtickets[] = array(

					'id' => $ticket['id'],

					'subject' => $ticket['subject'],

					'contactsurname' => $ticket['contactsurname'],

					'contactname' => $ticket['contactname'],

					'priority' => $priority,

				);
			};

			return response()->setJSON($data_newtickets);
		}
	}



	function transactions()
	{

		if (!isAdmin()) {

			$transactions = $this->Payments_Model->todaypayments_by_staff();
		} else {

			$transactions = $this->Payments_Model->todaypayments();
		}
		$data_transactions = array();
		if ($transactions) {



			foreach ($transactions as $transaction) {

				switch ($transaction['transactiontype']) {

					case '0':

						$type = 'paymenttoday';

						$icon = 'ion-log-in';

						$title = ($transaction['deposit_id'] ? lang2('deposit') : lang2('paymentistoday'));

						break;

					case '1':

						$type = 'expensetoday';

						$icon = 'ion-log-out';

						$title = lang2('expensetoday');

						break;
				};

				$data_transactions[] = array(

					'id' => $transaction['id'],

					'amount' => $transaction['amount'],

					'type' => $type,

					'title' => $title,

					'icon' => $icon,

				);
			};
		}

		return response()->setJSON($data_transactions);
	}



	function logs($loadMore = '')
	{

		if (!isAdmin()) {

			if ($loadMore) {

				$logs = $this->Logs_Model->panel_last_logs_by_staff($loadMore);
			} else {

				$logs = $this->Logs_Model->panel_last_logs_by_staff();
			}
		} else {

			if ($loadMore) {

				$logs = $this->Logs_Model->panel_last_logs($loadMore);
			} else {

				$logs = $this->Logs_Model->panel_last_logs();
			}
		}

		$data_logs = array();

		foreach ($logs as $log) {

			$data_logs[] = array(

				'logdate' => date(DATE_ISO8601, strtotime($log['date'])),

				'date' => tes_ciuis($log['date']),

				'detail' => $log['detail'],

				'customer_id' => $log['customer_id'],

				'project_id' => $log['project_id'],

				'staff_id' => $log['staff_id'],

			);
		};

		return response()->setJSON($data_logs);
	}



	// function contacts() {

	// 	$contacts = $this->Contacts_Model->get_all_contacts();

	// 	$permissions = $this->Privileges_Model->get_all_common_permissions();

	// 	$privileges = $this->Privileges_Model->get_privileges();

	// 	$data_contacts = array();

	// 	foreach ( $contacts as $contact ) {

	// 		$arr = array();

	// 		foreach ( $privileges as $privilege ) {

	// 			if ( $privilege[ 'relation' ] == $contact[ 'id' ] && $privilege[ 'relation_type' ] == 'contact' ) {

	// 				array_push( $arr, $privilege[ 'permission_id' ] );

	// 			}

	// 		}

	// 		$data_privileges = array();

	// 		foreach ( $permissions as $permission ) {

	// 			$data_privileges[] = array(

	// 				'id' => $permission[ 'id' ],

	// 				'name' => '' . lang( $permission[ 'permission' ] ) . '',

	// 				'value' => '' . ( array_search( $permission[ 'id' ], $arr ) !== FALSE ) ? true : false . ''

	// 			);

	// 		}

	// 		$data_contacts[] = array(

	// 			'id' => $contact[ 'id' ],

	// 			'customer_id' => $contact[ 'customer_id' ],

	// 			'name' => '' . $contact[ 'name' ] . '',

	// 			'surname' => '' . $contact[ 'surname' ] . '',

	// 			'email' => $contact[ 'email' ],

	// 			'phone' => $contact[ 'phone' ],

	// 			'username' => $contact[ 'username' ],

	// 			'address' => $contact[ 'address' ],

	// 			'extension' => $contact[ 'extension' ],

	// 			'mobile' => $contact[ 'mobile' ],

	// 			'password' => $contact[ 'password' ],

	// 			'language' => $contact[ 'language' ],

	// 			'skype' => $contact[ 'skype' ],

	// 			'linkedin' => $contact[ 'linkedin' ],

	// 			'position' => $contact[ 'position' ],

	// 			'primary' => $contact[ 'primary' ],

	// 			'admin' => $contact[ 'admin' ],

	// 			'inactive' => $contact[ 'inactive' ],

	// 			'privileges' => $data_privileges,

	// 		);

	// 	};

	// 	return response()->setJSON( $data_contacts );

	// }



	function contact($id)
	{

		$contacts = $this->Contacts_Model->get_customer_contacts($id);

		$permissions = $this->Privileges_Model->get_all_common_permissions();

		$privileges = $this->Privileges_Model->get_privileges();

		$data_contacts = array();

		foreach ($contacts as $contact) {

			$arr = array();

			foreach ($privileges as $privilege) {

				if ($privilege['relation'] == $contact['id'] && $privilege['relation_type'] == 'contact') {

					array_push($arr, $privilege['permission_id']);
				}
			}

			$data_privileges = array();

			foreach ($permissions as $permission) {

				$data_privileges[] = array(

					'id' => $permission['id'],

					'name' => '' . lang($permission['permission']) . '',

					'value' => '' . (array_search($permission['id'], $arr) !== FALSE) ? true : false . ''

				);
			}

			$data_contacts[] = array(

				'id' => $contact['id'],

				'customer_id' => $contact['customer_id'],

				'name' => '' . $contact['name'] . '',

				'surname' => '' . $contact['surname'] . '',

				'email' => $contact['email'],

				'phone' => $contact['phone'],

				'username' => $contact['username'],

				'address' => $contact['address'],

				'extension' => $contact['extension'],

				'mobile' => $contact['mobile'],

				'password' => $contact['password'],

				'language' => $contact['language'],

				'skype' => $contact['skype'],

				'linkedin' => $contact['linkedin'],
				'instagram' => $contact['instagram'],

				'position' => $contact['position'],

				'primary' => $contact['primary'],

				'admin' => $contact['admin'],

				'inactive' => $contact['inactive'],
				'facebook' => $contact['facebook'],
				'dt_nascimento' => $contact['dt_nascimento'],

				'privileges' => $data_privileges,

			);
		};

		return response()->setJSON($data_contacts);
	}



	function contact_privileges($id)
	{

		$permissions = $this->Privileges_Model->get_all_common_permissions();

		$privileges = $this->Privileges_Model->get_privileges();

		$arr = array();

		foreach ($privileges as $privilege) {

			if ($privilege['relation'] == $id && $privilege['relation_type'] == 'contact') {

				array_push($arr, $privilege['permission_id']);
			}
		}

		foreach ($permissions as $permission) {



			$data_privileges[] = array(

				'id' => $permission['id'],

				'name' => '' . lang($permission['permission']) . '',

				'value' => '' . (array_search($permission['id'], $arr) !== FALSE) ? 'true' : 'false' . '',

			);
		}

		return response()->setJSON($data_privileges);
	}



	function contact_permision()
	{

		$permissions = $this->Privileges_Model->get_all_common_permissions();

		$privileges = $this->Privileges_Model->get_privileges();

		$arr = array();

		foreach ($privileges as $privilege) {

			if ($privilege['relation_type'] == 'contact') {

				array_push($arr, $privilege['permission_id']);
			}
		}

		foreach ($permissions as $permission) {



			$data_privileges[] = array(

				'id' => $permission['id'],

				'name' => '' . lang($permission['permission']) . '',

				'value' => false,

			);
		}

		return response()->setJSON($data_privileges);
	}



	function customers()
	{
		$customers = array();
		$customer_sucess = request()->getPost('customer_sucess') ? request()->getPost('customer_sucess') : 0;

		if ($customer_sucess == "1") {
			if ($this->Privileges_Model->check_privilege('customer_sucess', 'all')) {
				$customers = $this->Customers_Model->get_all_customers('', true);
			} else if ($this->Privileges_Model->check_privilege('customer_sucess', 'own')) {
				$customers = $this->Customers_Model->get_all_customers(session()->usr_id, true);
			}
		} else {
			if ($this->Privileges_Model->check_privilege('customers', 'all')) {
				$customers = $this->Customers_Model->get_all_customers('');
			} else if ($this->Privileges_Model->check_privilege('customers', 'own')) {
				$customers = $this->Customers_Model->get_all_customers(session()->usr_id);
			}
		}


		$data_customers = array();
		foreach ($customers as $customer) {

			if ($customer['type'] == '0') {
				$name = $customer['company'];
				$type = lang2('corporatecustomers');
			} else {
				$name = $customer['namesurname'];
				$type = lang2('individual');
			}

			$total_unpaid_invoice_amount = 0;
			$total_paid_invoice_amount = 0;
			$total_paid_amount = 0;

			$contacts = [];
			//	$contacts = $this->Contacts_Model->get_customer_contacts($customer['id']);
			$country = get_country($customer['country_id']);
			$billing_country = get_country($customer['billing_country']);
			$shipping_country = get_country($customer['shipping_country']);
			$billing_state = get_state_name($customer['billing_state'], $customer['billing_state_id']);
			$shipping_state = get_state_name($customer['shipping_state'], $customer['shipping_state_id']);
			//	$appconfig = get_appconfig();

			$date1 = strtotime($customer['created']);
			$date2 = strtotime(date('Y-m-d'));
			$diff = abs($date2 - $date1);
			$years = floor($diff / (365 * 60 * 60 * 24));
			$months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

			if ($months > 1) {
				$months .= " meses";
			} else {
				$months .= " mes";
			}

			if ($customer['ultimoRetorno'] == "") {
				$prazo = "4";
			} elseif (strtotime(date('Y-m-d')) < strtotime($customer['ultimoRetorno'])) {
				$prazo = "1";
			} elseif (strtotime(date('Y-m-d')) > strtotime($customer['ultimoRetorno'])) {
				$prazo = "2";
			} elseif (strtotime(date('Y-m-d')) == strtotime($customer['ultimoRetorno'])) {
				$prazo = "3";
			} else {
				$prazo = "4";
			}

			$data_customers[] = array(
				'id' => $customer['id'],
				'customer_id' => $customer['id'],
				'name' => $name,
				'prazo' => $prazo,
				'ultimoRetorno' =>  $customer['ultimoRetorno'],
				'address' => $customer['address'],
				'etapa' => $customer['etapa'],
				'created' => date('d/m/Y', strtotime($customer['created'])),
				'lt' => $months,
				'email' => $customer['email'],
				'phone' => $customer['phone'],
				'name_custumer' => $customer['name_custumer'],
				'customer_sucess' => $customer['customer_sucess'],
				'view' => '1',
				'state_id' => $customer['state_id'],
				'billing_street' => $customer['billing_street'],
				'billing_city' => $customer['billing_city'],
				'billing_state' => $billing_state,
				'billing_state_id' => $customer['billing_state_id'],
				'billing_zip' => $customer['billing_zip'],
				'billing_country' => $billing_country,
				'billing_country_id' => $customer['billing_country'],
				'shipping_street' => $customer['shipping_street'],
				'shipping_city' => $customer['shipping_city'],
				'shipping_state' => $shipping_state,
				'shipping_state_id' => $customer['shipping_state_id'],
				'shipping_zip' => $customer['shipping_zip'],
				'shipping_country' => $shipping_country,
				'shipping_country_id' => $customer['shipping_country'],
				'customer_country' => $customer['country_id'],
				'balance' => (float)($total_unpaid_invoice_amount - $total_paid_amount + $total_paid_invoice_amount),
				'default_payment_method' => $customer['default_payment_method'],
				'group_name' => $customer['name'],
				'group_id' => $customer['groupid'],
				'customer_status_id' => $customer['customer_status_id'],
				'contacts' => $contacts,
				'' . lang2('filterbytype') . '' => $type,
				'' . lang2('filterbycountry') . '' => $country,
				'customer_number' => get_number('customers', $customer['id'], 'customer', 'customer'),
			);
		};

		return response()->setJSON($data_customers);
	}



	function countries()
	{
		$jsonstring = file_get_contents('assets/json/countries.json');
		return response()->setJSON(json_decode($jsonstring));
	}



	function get_states($countryId)
	{

		$states_data = get_states($countryId);

		return response()->setJSON($states_data);
	}



	function events()
	{

		$events = $this->Events_Model->get_all_events();

		$data_events = array();

		foreach ($events as $event) {

			if ($event['end'] < (date(" Y-m-d h:i:s"))) {

				$status = 'past';
			} else {

				$status = 'next';
			};

			$data_events[] = array(

				'day' => date('D', strtotime($event['start'])),

				'aday' => _dDay($event['start']),

				'start' => _adate($event['start']),

				'end' => _adate($event['end']),

				'start_iso_date' => date(get_dateTimeFormat(), strtotime($event['start'])),

				'start_date' => date(get_dateFormat(), strtotime($event['start'])),

				'end_date' => date(get_dateFormat(), strtotime($event['end'])),

				'detail' => $event['detail'],

				'title' => $event['title'],

				'staff' => $event['staff'],

				'status' => $status,

				'id' => $event['id'],

				'color' => $event['color'] ? $event['color'] : '#fff',

				'textColor' => $event['color'] ? $event['color'] : '#fff',

				'date' => $event['start'],

				'event_type' => $event['event_type'],

				'eventTextColor' => '#fff',

				'draggable' => false,

				'editable' => false,

				'allDay' => true,

				'relation' => 'event',

			);
		};

		return response()->setJSON($data_events);
	}



	function appointments()
	{

		$appointments = $this->Appointments_Model->get_all_appointments();

		$data_appointments = array();

		foreach ($appointments as $appointment) {

			if ($appointment['booking_date'] < date("Y-m-d")) {

				$status = 'past';
			} else {

				$status = 'next';
			};

			$data_appointments[] = array(

				'id' => $appointment['id'],

				'day' => date('D', strtotime($appointment['booking_date'])),

				'aday' => _dDay('' . $appointment['booking_date'] . ' ' . $appointment['start_time'] . ''),

				'start' => _adate('' . $appointment['booking_date'] . ' ' . $appointment['start_time'] . ''),

				'start_iso_date' => date(DATE_ISO8601, strtotime('' . $appointment['booking_date'] . ' ' . $appointment['start_time'] . '')),

				'start_date' => $appointment['booking_date'],

				'title' => '' . $message = sprintf(lang2('appointment_for'), $appointment['contact_name']) . '',

				'staff' => $appointment['staff'],

				'contact' => '' . $appointment['contact_name'] . ' ' . $appointment['contact_surname'] . '',

				'status_class' => $status,

				'status' => $appointment['status'],

				'date' => date(DATE_ISO8601, strtotime($appointment['booking_date'])),

			);
		};

		return response()->setJSON($data_appointments);
	}



	function all_appointments()
	{

		$appconfig = get_appconfig();

		$appointments = $this->Appointments_Model->get_all_confirmed_appointments();

		$data_appointments = array();

		foreach ($appointments as $appointment) {

			$data_appointments[] = array(

				'id' => $appointment['id'],

				'title' => '' . lang2('appointment') . ' : ' . $appointment['contact_name'],

				'text' => '' . $message = sprintf(lang2('appointment_for'), $appointment['contact_name']) . '',

				'start' => '' . $appointment['booking_date'] . ' ' . $appointment['start_time'] . '',

				'end' => '' . $appointment['booking_date'] . ' ' . $appointment['end_time'] . '',

				'status' => $appointment['status'],

				'start_date' =>  '' . date(get_dateFormat(), strtotime($appointment['booking_date']))  . ' ' . $appointment['start_time'] . '',

				'end_date' => '' . date(get_dateFormat(), strtotime($appointment['booking_date'])) . ' ' . $appointment['end_time'] . '',

				'relation' => 'appointment',

				'color' => $appconfig['appointment_color'],

				'staff' => $appointment['staff'],

			);
		};

		return response()->setJSON($data_appointments);
	}



	function calendar_projects()
	{

		$projects = array();

		if ($this->Privileges_Model->check_privilege('projects', 'all')) {

			$projects = $this->Projects_Model->get_all_projects_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('projects', 'own')) {

			$projects = $this->Projects_Model->get_all_projects_by_privileges(session()->usr_id);
		}

		$appconfig = get_appconfig();

		$data_projects = array();

		foreach ($projects as $project) {

			if (($project['staff_id'] == session()->usr_id) || ($this->Projects_Model->check_member($project['id'], session()->usr_id)) == 'true' || $this->Settings_Model->isAdmin() == 'true') {

				$data_projects[] = array(

					'id' => $project['id'],

					'title' => '' . get_number('projects', $project['id'], 'project', 'project') . ' : ' . $project['name'],

					'text' => $project['description'],

					'start' => $project['start_date'],

					'end' => $project['deadline'],

					'status' => $project['id'],

					'status_class' => $project['id'],

					'start_date' => date(get_dateFormat(), strtotime($project['start_date'])),

					'end_date' => date(get_dateFormat(), strtotime($project['deadline'])),

					'relation' => 'project',

					'color' => $appconfig['project_color'],

				);
			}
		};

		return response()->setJSON($data_projects);
	}



	function calendar_tasks()
	{

		$tasks = array();

		if ($this->Privileges_Model->check_privilege('tasks', 'all')) {

			$tasks = $this->Tasks_Model->get_all_tasks_calendar();
		} else if ($this->Privileges_Model->check_privilege('tasks', 'own')) {

			$tasks = $this->Tasks_Model->get_all_tasks_calendar(session()->usr_id);
		}

		if ($tasks) {

			$appconfig = get_appconfig();

			$data_tasks = array();

			foreach ($tasks as $task) {

				if ($task['priority'] == '1') {

					$task_priority = lang2('low');
				} elseif ($task['priority'] == '2') {

					$task_priority = lang2('medium');
				} elseif ($task['priority'] == '3') {

					$task_priority = lang2('high');
				}

				if ($task['status_id'] == '1') {

					$task_status = lang2('open');
				} elseif ($task['status_id'] == '2') {

					$task_status = lang2('inprogress');
				} elseif ($task['status_id'] == '3') {

					$task_status = lang2('waiting');
				} elseif ($task['status_id'] == '4') {

					$task_status = lang2('complete');
				} elseif ($task['status_id'] == '5') {

					$task_status = lang2('cancelled');
				}

				$data_tasks[] = array(

					'id' => $task['id'],

					'title' => '' . get_number('tasks', $task['id'], 'task', 'task') . ' : ' . $task['name'],

					'text' => $task['description'],

					'start' => $task['startdate'],

					'end' => $task['duedate'],

					'start_date' => date(get_dateFormat(), strtotime($task['startdate'])),

					'end_date' => date(get_dateFormat(), strtotime($task['duedate'])),

					'relation' => 'task',

					'color' => $appconfig['task_color'],

					'priority' => $task_priority,

					'status' => $task_status,

					'staff' => $task['staffname'],

				);
			};

			return response()->setJSON($data_tasks);
		}
	}


	function get_staff()
	{
		$result = [];

		if (isset($_GET['q'])) {
			$result = $this->Staff_Model->get_staffSearch("%" . $_GET['q'] . "%");
		}

		$json =  array('results' => $result);
		return response()->setJSON($json);
	}


	function get_google_events($id)
	{

		$staff = $this->Staff_Model->get_staff($id);

		$str = file_get_contents('https://www.googleapis.com/calendar/v3/calendars/' . $staff['google_calendar_id'] . '/events?key=' . $staff['google_calendar_api_key'] . '');

		$json = json_decode($str, true);

		return response()->setJSON($json['items']);
	}



	function meetings()
	{

		$builder = $this->db->table('meetings');
		$builder->select('*, staff.staffname as staff_name, customers.type as type, customers.company as customercompany, customers.namesurname as individual, meetings.id as id');
		$builder->join('customers', 'meetings.customer_id = customers.id', 'left');
		$builder->join('staff', 'meetings.staff_id = staff.id', 'left');
		$builder->where('meetings.staff_id', session()->get('usr_id'));
		$meetings = $builder->get()->getResultArray();

		$data_meetings = array();

		foreach ($meetings as $meet) {

			if ($meet['type'] == 1) {

				$customer = $meet['individual'];
			} else $customer = $meet['customercompany'];

			$data_meetings[] = array(

				'id' => $meet['id'],

				'title' => $meet['title'],

				'description' => $meet['description'],

				'date' => date(DATE_ISO8601, strtotime('' . $meet['date'] . ' ' . $meet['start'] . '')),

				'start' => $meet['start'],

				'end' => $meet['end'],

				'staff' => $meet['staff_name'],

				'customer' => $customer,

			);
		};

		return response()->setJSON($data_meetings);
	}





	function todos()
	{

		$todos = $this->Trivia_Model->get_todos();

		$data_todo = array();

		foreach ($todos as $todo) {

			$data_todo[] = array(

				'id' => $todo['id'],

				'date' => date(DATE_ISO8601, strtotime($todo['date'])),

				'description' => $todo['description'],

			);
		};

		return response()->setJSON($data_todo);
	}



	function donetodos()
	{

		$donetodos = $this->Trivia_Model->get_done_todos();

		$data_donetodos = array();

		foreach ($donetodos as $donetodo) {

			$data_donetodos[] = array(

				'id' => $donetodo['id'],

				'date' => date(DATE_ISO8601, strtotime($donetodo['date'])),

				'description' => $donetodo['description'],

			);
		};

		return response()->setJSON($data_donetodos);
	}



	function accounts()
	{

		$accounts = $this->Accounts_Model->get_all_accounts();

		$data_account = array();

		foreach ($accounts as $account) {

			$data_account[] = array(

				'id' => $account['id'],

				'name' => $account['name'],

			);
		};

		return response()->setJSON($data_account);
	}



	function leads()
	{

		$leads = $this->Leads_Model->get_all_leads();

		$data_leads = array();

		foreach ($leads as $lead) {

			$data_leads[] = array(

				'id' => $lead['id'],

				'name' => $lead['leadname'],

				'lead_number' => get_number('leads', $lead['id'], 'lead', 'lead'),

				'email' => $lead['leadmail'],

			);
		};

		return response()->setJSON($data_leads);
	}



	function leads_by_leadsource_leadpage()
	{

		return response()->setJSON($this->Report_Model->leads_by_leadsource_leadpage());
	}



	function products()
	{

		$products = $this->Products_Model->get_all_products();

		$data_products = array();

		$appconfig = get_appconfig();

		foreach ($products as $product) {

			$data_products[] = array(

				'product_id' => $product['id'],

				'code' => $product['code'],

				'name' => $product['productname'],

				'description' => $product['description'],

				'price' => (float)$product['sale_price'],

				'tax' => $product['vat'],

				'purchase_price' => (float)$product['purchase_price'],

				'category_name' => $product['name'],

				'stock' => (float)$product['stock'],

				'product_number' => get_number('products', $product['id'], 'product', 'product'),

			);
		};

		return response()->setJSON($data_products);
	}



	function vendors()
	{

		$vendors = array();

		$vendors = $this->Vendors_Model->get_all_vendors();

		$data_vendors = array();

		foreach ($vendors as $vendor) {

			$data_vendors[] = array(

				'id' => $vendor['id'],

				'name' => $vendor['company'],

				'email' => $vendor['email'],

				'vendor_number' => get_number('vendors', $vendor['id'], 'vendor', 'vendor'),

			);
		};

		return response()->setJSON($data_vendors);
	}



	function lang($lang)
	{

		$lang = session()->get('language');

		$language = 'pt';
		$path = APPPATH . 'Language/' . $language;
		$translations = [];
		$files = glob($path . '/*.php');

		foreach ($files as $file) {
			$translationData = include($file);
			$translations[basename($file, '.php')] = $translationData;
		}

		return response()->setJSON($translations['portuguese_br_default_lang']);
	}



	function custom_fields_by_type($type)
	{

		$custom_fields = $this->Fields_Model->custom_fields_by_type($type);

		$data_custom_fields = array();

		foreach ($custom_fields as $field) {

			$data_custom_fields[] = array(

				'id' => $field['id'],

				'name' => $field['name'],

				'type' => $field['type'],

				'order' => $field['order'],

				'data' => json_decode($field['data']),

				'relation' => $field['relation'],

				'permission' => $field['permission'] === 'true' ? true : false,

				'active' => $field['active'] === 'true' ? true : false,

				'date' => null,

			);
		};

		if ($custom_fields) {

			return response()->setJSON($data_custom_fields);
		} else {

			return response()->setJSON(false);
		}
	}



	function custom_fields()
	{

		$custom_fields = $this->Fields_Model->custom_fields();

		$data_custom_fields = array();

		foreach ($custom_fields as $field) {

			$data_custom_fields[] = array(

				'id' => ($field['id']),

				'name' => $field['name'],

				'type' => $field['type'],

				'order' => intval($field['order']),

				'data' => json_decode($field['data']),

				'relation' => $field['relation'],

				'icon' => $field['icon'],

				'permission' => $field['permission'] === 'true' ? true : false,

				'active' => $field['active'] === 'true' ? true : false,

				'updated_on' => ($field['updated_on'] == null) ? '' : date(get_dateTimeFormat(), strtotime($field['updated_on'])),

			);
		};

		return response()->setJSON($data_custom_fields);
	}



	function custom_field_data_by_id($id)
	{

		$field = $this->Fields_Model->custom_field_data_by_id($id);

		$data_custom_field = array(

			'id' => intval($field['id']),

			'name' => $field['name'],

			'type' => $field['type'],

			'order' => intval($field['order']),

			'data' => json_decode($field['data']),

			'relation' => $field['relation'],

			'icon' => $field['icon'],

			'permission' => $field['permission'] === 'true' ? true : false,

			'active' => $field['active'] === 'true' ? true : false,

			'date' => '',

		);

		return response()->setJSON($data_custom_field);
	}



	function custom_fields_data_by_type($type, $id)
	{

		$fields = $this->Fields_Model->custom_fields_by_type($type);

		$data_custom_fields = array();

		foreach ($fields as $field) {

			$data = $this->Fields_Model->custom_fields_data_by_type($type, $id, $field['id']);

			if ($data) {

				switch ($field['type']) {

					case 'input':

						$data_last = $data['data'];

						$selected_opt = 0;

						break;

					case 'date':

						$data_last = $data['data'];

						$selected_opt = 0;

						break;

					case 'number':

						$data_last = $data['data'];

						$selected_opt = 0;

						break;

					case 'textarea':

						$data_last = $data['data'];

						$selected_opt = 0;

						break;

					case 'select':

						$data_last = json_decode($field['data']);

						$selected_opt = json_decode($data['data']);

						break;
				}

				if ($field['icon'] != null) {

					$icon = $field['icon'];
				} else {

					$icon = 'mdi mdi-info-outline';
				}
			} else {

				$data_last = json_decode($field['data']);

				$selected_opt = null;
			}

			if ($field['icon'] != null) {

				$icon = $field['icon'];
			} else {

				$icon = 'mdi mdi-info-outline';
			}

			$data_custom_fields[] = array(

				'id' => $field['id'],

				'name' => $field['name'],

				'type' => $field['type'],

				'order' => $field['order'],

				'data' => $data_last,

				'selected_opt' => $selected_opt,

				'relation' => $field['relation'],

				'icon' => $icon,

				'permission' => $field['permission'] === 'true' ? true : false,

				'active' => $field['active'] === 'true' ? true : false,

			);
		};

		return response()->setJSON($data_custom_fields);
	}



	function search()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$input = request()->getPost('input');

			if (!$input || $input == '' || strlen($input) < 2) {

				echo false;
			} else {

				return response()->setJSON($this->Search_Model->search_data($input));
			}
		} else {

			redirect('panel/');
		}
	}



	function timer($id = NULL)
	{

		$status = request()->getPost('status');

		if ($status == 'start') {

			$id = $this->Tasks_Model->start_timer(session()->usr_id);

			if ($id) {

				$data['success'] = true;

				$data['type'] = 'Success';

				$data['message'] = lang2('timer_started');

				return response()->setJSON($data);
			} else {

				$data['success'] = false;

				$data['type'] = lang2('success');

				$data['message'] = lang2('errormessage');

				return response()->setJSON($data);
			}
		} else if ($status == 'stop') {

			$task = request()->getPost('task');

			$action = request()->getPost('action');

			if ($task == '' || !$task) {

				$data['success'] = false;

				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('task');

				return response()->setJSON($data);
			} else {

				if ($action == 'assign' || $action == 'stop') {

					if ($action == 'assign') {

						$params = array(

							//'relation' => 'task',

							'task_id' => $task,

							'note' => request()->getPost('note'),

						);

						$message = lang2('task_assigned');
					}

					if ($action == 'stop') {

						$date = new DateTime();

						$params = array(

							//'relation' => 'task',

							'task_id' => $task,

							'note' => request()->getPost('note'),

							'end' => $date->format('Y-m-d H:i:s')

						);

						$message = lang2('timer_stopped');
					}

					$timer = $this->Tasks_Model->get_timer();

					$result = $this->Tasks_Model->stop_timer($id, $params);

					if ($result) {

						$data['success'] = true;

						$data['type'] = lang2('success');

						$data['message'] = $message;

						$data['params'] = $params;

						return response()->setJSON($data);
					} else {

						$data['success'] = false;

						$data['type'] = lang2('error');

						$data['message'] = lang2('errormessage');

						return response()->setJSON($data);
					}
				} else {

					$data['success'] = false;

					$data['message'] = lang2('errormessage');

					return response()->setJSON($data);
				}
			}
		}
	}



	function delete_timer($id)
	{

		$timer = $this->Tasks_Model->get_timer();

		$result = $this->Tasks_Model->delete_timer($id);

		$data['success'] = true;

		$data['type'] = lang2('success');

		$data['message'] = lang2('timer') . ' ' . lang2('deletemessage');

		return response()->setJSON($data);
	}



	function get_timer()
	{

		$timer = $this->Tasks_Model->get_timer();

		$time_result = array();

		if ($timer) {

			foreach ($timer as $time) {

				$date1 = new Datetime($time['start']);

				$diffs = $date1->diff(new DateTime());

				$h = $diffs->days * 24;

				$h += $diffs->h;

				$minutes = $diffs->i;

				$seconds = $diffs->s;

				if ($minutes < 10) {

					$minutes = '0' . $minutes;
				}

				if ($seconds < 10) {

					$seconds = '0' . $seconds;
				}

				if ($h < 10) {

					$h = '0' . $h;
				}

				$total = $h . ':' . $minutes . ':' . $seconds;

				$secondss = ($h * 60 * 60) + ($minutes * 60) + ($seconds);

				$time_result[] = array(

					'id' => $time['id'],

					'started' =>  date(get_dateTimeFormat(), strtotime($time['start'])),

					'task_id' => $time['task_id'],

					'total' => $total,

					'seconds' => $secondss,

					'task' => $time['name'],

					'note' => $time['note'],

				);
			}

			$data = $time_result;

			return response()->setJSON($data);
		} else {

			$data = array();

			return response()->setJSON($data);
		}
	}



	function get_timer_data($id)
	{

		$data = $this->Tasks_Model->get_timer_data($id);

		return response()->setJSON($data);
	}



	function get_open_tasks()
	{

		$tasks = $this->Tasks_Model->get_all_tasks_for_timer();

		$data_tasks = array();

		foreach ($tasks as $task) {

			$data_tasks[] = array(

				'id' => $task['id'],

				'name' => $task['name'],

				'status_id' => $task['status_id'],

			);
		};

		return response()->setJSON($data_tasks);
	}



	function load_config()
	{

		$settings = $this->Settings_Model->get_rebranding_data();

		if ($settings['disable_preloader'] == '1') {

			$settings['disable_preloader'] = true;
		} else {

			$settings['disable_preloader'] = false;
		}

		if ($settings['enable_support_button_on_client'] == '1') {

			$settings['enable_support_button_on_client'] = true;
		} else {

			$settings['enable_support_button_on_client'] = false;
		}

		return response()->setJSON($settings);
	}



	function eventtypes()
	{

		$eventtypes = $this->Events_Model->get_eventtypes();

		return response()->setJSON($eventtypes);
	}



	function add_eventtype()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$name = request()->getPost('name');

			$color = request()->getPost('color');



			$hasError = false;

			$data['name'] = '';

			if ($name == '') {

				$hasError = true;

				$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
			} else if ($color == '') {

				$hasError = true;

				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('color');
			}

			if ($hasError) {

				$data['success'] = false;

				return response()->setJSON($data);
			}

			if (!$hasError) {

				$params = array(

					'color' => $color,

					'name' => $name,

					'public' => request()->getPost('public'),

				);

				$eventtype_id = $this->Events_Model->add_eventtype($params);

				if ($eventtype_id) {

					$data['success'] = true;

					$data['message'] = lang2('event') . ' ' . lang2('type') . ' ' . lang2('createmessage');

					$data['id'] = $eventtype_id;

					return response()->setJSON($data);
				} else {

					$data['success'] = false;

					$data['message'] = lang2('errormessage');

					return response()->setJSON($data);
				}
			}
		}
	}



	function remove_eventtype($id)
	{

		$eventtypes = $this->Events_Model->remove_eventtype($id);

		$data['success'] = true;

		$data['message'] = lang2('event') . ' ' . lang2('type') . ' ' . lang2('deletemessage');

		return response()->setJSON($data);
	}



	function deposits_by_relation()
	{

		$relation_type = request()->getUri()->getSegment(3);

		$relation_id = request()->getUri()->getSegment(4);

		$deposits = $this->Deposits_Model->get_all_deposits_by_relation($relation_type, $relation_id);

		$data_deposits = array();

		foreach ($deposits as $deposit) {

			$depositdate = date(get_dateFormat(), strtotime($deposit['date']));

			if ($deposit['invoice_id'] == NULL) {

				$billstatus = lang2('notbilled') and $color = 'warning' and $billstatus_code = 'false';
			} else {

				$billstatus = lang2('billed') and $color = 'success' and $billstatus_code = 'true';
			}

			if ($deposit['customer_id'] != 0) {

				$billable = 'true';
			} else {

				$billable = 'false';
			}

			$data_deposits[] = array(

				'id' => $deposit['id'],

				'title' => $deposit['title'],

				'prefix' => $appconfig['deposit_prefix'],

				'longid' => get_number('deposits', $deposit['id'], 'deposit', 'deposit'),

				'amount' => $deposit['amount'],

				'staff' => $deposit['staff'],

				'category' => $deposit['category'],

				'billstatus' => $billstatus,

				'billstatus_code' => $billstatus_code,

				'color' => $color,

				'billable' => $billable,

				'date' => $depositdate,

			);
		};

		return response()->setJSON($data_deposits);
	}



	function depositscategories()
	{

		$depositscategories = $this->Deposits_Model->get_all_depositcat();

		$data_depositscategories = array();

		foreach ($depositscategories as $category) {

			$catid = $category['id'];

			if ($this->Privileges_Model->check_privilege('deposits', 'all')) {

				$amountby = $this->Report_Model->deposits_amount_by_category($catid);

				$percent = $this->Report_Model->deposits_percent_by_category($catid);
			} else {

				$amountby = $this->Report_Model->deposits_amount_by_category($catid, session()->usr_id);

				$percent = $this->Report_Model->deposits_percent_by_category($catid, session()->usr_id);
			}

			if ($amountby != NULL) {

				$amtbc = $amountby;
			} else {

				$amtbc = 0;
			}



			$data_depositscategories[] = array(

				'id' => $category['id'],

				'name' => $category['name'],

				'description' => $category['description'],

				'amountby' => $amtbc,

				'percent' => $percent,

			);
		};

		return response()->setJSON($data_depositscategories);
	}



	function table_columns($relation)
	{

		$builder = $this->db->table('table_columns');
		$builder->where('relation', $relation);
		$columns = $builder->get()->getResultArray();

		$data = array();

		foreach ($columns as $column) {

			$data[$column['table_column']] = ($column['display'] == '1') ? true : false;
		}

		return response()->setJSON($data);
	}



	function update_columns($relation)
	{
		$column = request()->getPost('column');
		$value = request()->getPost('value');

		$this->db->table('table_columns')
			->where('relation', $relation)
			->where('table_column', $column)
			->update([
				'display' => $value,
				'updated_at' => date('Y-m-d H:i:s')
			]);

		echo true;
	}




	function search_customers($q)
	{
		$customers = $this->Customers_Model->search_customers($q);
		$data = array();
		foreach ($customers as $customer) {

			$billing_country = get_country($customer['billing_country']);
			$shipping_country = get_country($customer['shipping_country']);
			$billing_state = get_state_name($customer['billing_state'], $customer['billing_state_id']);
			$shipping_state = get_state_name($customer['shipping_state'], $customer['shipping_state_id']);

			$data[] = array(

				'name' => $customer['namesurname'] ? $customer['namesurname'] : $customer['company'],

				'email' => $customer['email'],

				'customer_number' => get_number('customers', $customer['id'], 'customer', 'customer'),

				'id' => $customer['id'],

				'billing_street' => $customer['billing_street'],

				'billing_city' => $customer['billing_city'],

				'billing_state' => $billing_state,

				'billing_state_id' => $customer['billing_state_id'],

				'billing_zip' => $customer['billing_zip'],

				'billing_country' => $billing_country,

				'billing_country_id' => $customer['billing_country'],

				'shipping_street' => $customer['shipping_street'],

				'shipping_city' => $customer['shipping_city'],

				'shipping_state' => $shipping_state,

				'shipping_state_id' => $customer['shipping_state_id'],

				'shipping_zip' => $customer['shipping_zip'],

				'shipping_country' => $shipping_country,

				'shipping_country_id' => $customer['shipping_country'],

			);
		}

		return response()->setJSON($data);
	}



	function accounts_total()
	{

		$total_incomings = $this->Report_Model->total_incomings();

		$total_outgoings = $this->Report_Model->total_outgoings();

		$account_total = $total_incomings - $total_outgoings;

		$total = array(

			'total_incomings' => $total_incomings,

			'total_outgoings' => $total_outgoings,

			'accounts_total' => $account_total,

		);

		return response()->setJSON($total);
	}



	function get_product_categories()
	{

		$categories = $this->Products_Model->get_product_categories();

		$data_categories = array();

		foreach ($categories as $category) {

			$data_categories[] = array(

				'name' => $category['name'],

				'id' => $category['id'],

			);
		};

		return response()->setJSON($data_categories);
	}


	public function get_frm_pagamentos()
	{
		$builder = $this->db->table('formas_pagamento');
		$builder->where("(id_company = '" . session()->get('id_company') . "' or id_company = '1')");
		$builder->where('is_ativo', '1');
		$builder->orderBy('nm_forma', 'asc');
		$data = $builder->get()->getResultArray();
		return response()->setJSON($data);
	}

	public function getExplicacaoBalao($id)
	{
		$builder = $this->db->table('explicacoes_graph');
		$builder->where('id_graph', $id);
		$data = $builder->get()->getRow();
		if ($data != null) {
			return response()->setJSON($data);
		} else {
			return response()->setJSON([]);
		}
	}

	public function setExplicacaoBalao()
	{
		$idGraphModal = request()->getPost('idGraphModal');
		$builder = $this->db->table('explicacoes_graph');
		$builder->where('id_graph', $idGraphModal);
		$data = $builder->get()->getRow();
		$param = [
			'explicacao' => request()->getPost('explicacao'),
			'titulo' => request()->getPost('titulo'),
			'exibir' => request()->getPost('exibir')
		];
		if ($data != null) {
			$builder->where('id_graph', $idGraphModal);
			$builder->update($param);
		} else {
			$param['id_graph'] = $idGraphModal;
			$builder->insert($param);
		}
		return response()->setJSON(['success' => true]);
	}

	public function getAllExplicacaoBalao()
	{
		$builder = $this->db->table('explicacoes_graph');
		$data = $builder->get()->getResultArray();
		$return = [];
		foreach ($data as $row) {
			$return[$row['id_graph']] = $row;
		}
		return response()->setJSON($return);
	}


	function updt_frm_pagamentos()
	{
		$param = [];
		if (!empty(request()->getPost('nm_forma'))) {
			$param['nm_forma'] = request()->getPost('nm_forma');
		}
		$param['is_ativo'] = request()->getPost('is_ativo');

		if (!request()->getPost('id_forma')) {
			$param['id_company'] = session()->get('id_company');
			$this->db->table('formas_pagamento')->insert($param);
		} else {
			$this->db->table('formas_pagamento')
				->where('id_forma', request()->getPost('id_forma'))
				->update($param);
		}

		return response()->setJSON(['success' => true]);
	}
}
