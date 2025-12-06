<?php

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use DateTime;
use DateInterval;

class CronJob extends BaseController
{


	public function __construct()
	{

		parent::loadModels();

		//define('LANG', $this->Settings_Model->get_crm_lang());
		//define('currency', $this->Settings_Model->get_currency());

		//$this->lang->load(LANG . '_default', LANG);
		//$this->lang->load(LANG, LANG);
	}



	function index()
	{

		header('Location: /');
	}

	function getEmailsImap()
	{
		$mbox = imap_open("{imap.example.org:143}INBOX", "username", "password") or die("can't connect: " . imap_last_error());
		$MC = imap_check($mbox);

		$result = imap_fetch_overview($mbox, "1:{$MC->Nmsgs}", 0);
		foreach ($result as $email) {
			if ($email->seen) {
				echo "Lida";
			}

			//	echo "#{$overview->msgno} ({$overview->date}) - From: {$overview->from}{$overview->subject}\n";
		}
		imap_close($mbox);
	}

	function monitoraEmails()
	{
		// 🔹 Busca todas as contas configuradas
		$emailsConfig = $this->db->table('settings_email')
			->where('imapHost is not null')
			->where('imapHost != ""')

			->where('imapUsername is not null')
			->where('imapUsername != ""')

			->where('imapPassoword is not null')
			->where('imapPassoword != ""')
			->get()->getResultArray();
		$resultado = [];
		$horaAtual = time();
		$umaHoraAtras = $horaAtual - 3600; // últimos 60 minutos

		foreach ($emailsConfig as $config) {
			// pula contas sem IMAP configurado
			if (empty($config['imapHost']) || empty($config['imapUsername']) || empty($config['imapPassoword'])) {
				continue;
			}

			// 🔹 Monta o hostname IMAP dinamicamente
			$port = $config['imapPort'] ?: '993';
			$hostname = sprintf('{%s:%s/imap/ssl}INBOX', $config['imapHost'], $port);

			// 🔹 Conecta à caixa
			$inbox = @imap_open($hostname, $config['imapUsername'], $config['imapPassoword']);

			if (!$inbox) {
				$resultado[] = [
					'email' => $config['imapUsername'],
					'status' => 'erro',
					'mensagem' => imap_last_error()
				];
				continue;
			}

			// 🔹 Busca mensagens não lidas de hoje
			$hoje = date('d-M-Y'); // formato IMAP: 23-Oct-2025
			$uids = imap_search($inbox, 'UNSEEN SINCE "' . $hoje . '"', SE_UID);

			$mensagens = [];

			if ($uids) {
				foreach ($uids as $uid) {
					$overview = imap_fetch_overview($inbox, $uid, FT_UID);
					$msg = $overview[0] ?? null;
					if (!$msg) continue;

					// verifica se é da última hora
					$dataEmail = isset($msg->udate) ? (int)$msg->udate : 0;
					if ($dataEmail < $umaHoraAtras) {
						continue; // ignora se for mais antigo que 1 hora
					}

					$assunto = isset($msg->subject) ? imap_utf8($msg->subject) : '(sem assunto)';
					$remetente = isset($msg->from) ? imap_utf8($msg->from) : '(desconhecido)';
					$body = $this->getBody($inbox, $uid);

					$mensagens[] = [
						'assunto'   => $assunto,
						'remetente' => $this->parseEmail($remetente),
						'mensagem'  => $body,
						'data'      => date('Y-m-d H:i:s', $dataEmail)
					];

					imap_setflag_full($inbox, $uid, "\\Seen", ST_UID);

					$lead = $this->db->table('leads')
						->join('leads_contatos', 'leads_contatos.id_lead = leads.id', 'left')
						->where('leads.email', $this->parseEmail($remetente))
						->orWhere('leads_contatos.email', $this->parseEmail($remetente))
						->get()
						->getRowArray();

					if ($lead) {
						$matrizAtv = [
							'data' => date('Y-m-d'),
							'anotacoes' => 'email recebido: <br>' . $assunto . '<br>' . $body,
							'reuniao_call' => "0",
							'retorno' => date('Y-m-d'),
							'dt_entrada' => date('Y-m-d H:i:s'),
							'is_reuniao' => '0',
							'email_enviado' => $this->parseEmail($remetente),
							'id_lead' => $lead['id']
						];

						$response = $this->db->table('leads_atv')->insert($matrizAtv);
					}
				}
			}

			imap_close($inbox);

			$resultado[] = [
				'email' => $config['imapUsername'],
				'status' => 'ok',
				'mensagens' => $mensagens
			];
		}


		return response()->setJSON($resultado);
	}

	/**
	 * Extrai corpo da mensagem (texto simples ou HTML)
	 */
	private function getBody($inbox, $uid)
	{
		$structure = imap_fetchstructure($inbox, $uid, FT_UID);

		if (!isset($structure->parts)) {
			return imap_body($inbox, $uid, FT_UID);
		}

		foreach ($structure->parts as $i => $part) {
			if ($part->subtype == 'PLAIN') {
				return imap_fetchbody($inbox, $uid, $i + 1, FT_UID);
			} elseif ($part->subtype == 'HTML') {
				return imap_fetchbody($inbox, $uid, $i + 1, FT_UID);
			}
		}

		return '(sem corpo)';
	}

	/**
	 * Extrai apenas o e-mail do campo "From"
	 */
	private function parseEmail($from)
	{
		if (preg_match('/<(.+)>/', $from, $matches)) {
			return $matches[1];
		}
		return $from;
	}


	function renova_creditos()
	{

		$empresas = $this->db->table('companies')
			->where('renovar', '1')
			->where('status', '1')
			->get()
			->getResultArray();

		foreach ($empresas as $index => $empresa) {
			$credito_atual = 0;
			$renovar_valor = !empty($empresa['renovar_valor']) ? $empresa['renovar_valor'] : 0;

			if ($empresa['credito_acumulativo'] == "1") {
				$credito_atual = $empresa['creditos'];
			}

			$this->db->table('companies')
				->where('id_company', $empresa['id_company'])
				->update(['creditos' => $credito_atual + $renovar_valor]);
		}

		print_r($empresas);
		//	exit;

	}



	public function envia_email_fluxo_automatico()
	{
		$agora = new DateTime();
		$inicio = new DateTime('08:00');
		$fim = new DateTime('19:00');
		if ($agora < $inicio || $agora > $fim) {
			echo "fora do horario";
			return;
		}
		//return;
		$res = [];
		$this->email = \Config\Services::email();
		$db = $this->db;
		$assinatura = [];
		$resultado = [];
		$limitLeads = 400;

		// Inclui TODAS as etapas automáticas (inclusive subetapas)
		$fluxos_etapas = $db->table('fluxos_etapas')
			->select('fluxos_etapas.*, fluxos.*, email_templates.*, fluxos.id_company, fluxos.automatico as fluxos_automatico')
			->join('fluxos', 'fluxos.id_fluxo = fluxos_etapas.id_fluxo', 'inner')
			->join('email_templates', 'email_templates.id = fluxos_etapas.email', 'left')
			->where('fluxos_etapas.automatico', '1')
			//->where('fluxos.automatico', '1')
			->groupStart()
			->where('fluxos_etapas.canal', '1')
			->orWhere('fluxos_etapas.canal', '2')
			->groupEnd()
			//->where('fluxos_etapas.email IS NOT NULL')
			->where('(CAST(`ultima_atualizacao` AS DATE) < CAST(now() AS DATE) or ultima_atualizacao is null)')
			->orderBy('`fluxos_etapas.id_fluxo`, `fluxos_etapas.numero` asc')
			->limit(1)
			->get()

			->getResultArray();

		//print_r($fluxos_etapas->getCompiledSelect());
		//	exit;

		foreach ($fluxos_etapas as $etapa) {
			$anexo = [];
			//	$db->table('fluxos_etapas')->where('id_etapa', $etapa['id_etapa'])->update(['ultima_atualizacao' => date('Y-m-d')]);

			if (($etapa['canal'] == "1" && ($etapa['email'] == "" || $etapa['email'] == "0"))) {

				$db->table('fluxos_etapas')->where('id_etapa', $etapa['id_etapa'])->update(['ultima_atualizacao' => date('Y-m-d')]);
				$res = ['status' => "sem email relacionado"];
				continue;
			}
			$assinatura = $db->table('email_assinaturas')
				->where('staff_id', $etapa['responsavel'])
				->get()
				->getRowArray();


			if ($etapa['canal'] == "2") {
				$anexo = $db->table('fluxos_anexos')
					->where('id_fluxo', $etapa['id_fluxo'])
					->get()
					->getRowArray();
			}


			$leadsDb = $db->table('leads l')
				->select('l.id AS id_lead, l.id_ultimaEtapaAutomatico, l.ultimoFlowAutomatico, l.email, l.company, l.name, l.ultimoVerificacaoFlowAutomatico')
				->join('leadsstatus ls', 'ls.id = l.status', 'inner')
				->where('(l.ultimoVerificacaoFlowAutomatico IS NULL OR DATE(l.ultimoVerificacaoFlowAutomatico) < CURDATE())');

			if ($etapa['fluxos_automatico'] == "0") {
				$leadsDb->where('l.sales_flow', $etapa['id_fluxo']);
			}

			if (str_contains($etapa['funil'], ',')) {
				$leadsDb->whereIn('ls.id_list', explode(',', $etapa['funil']));
			} else {
				$leadsDb->where('ls.id_list', $etapa['funil']);
			}


			$leads = $leadsDb->limit($limitLeads)
				->get()
				->getResultArray();

			//	print_r($leads->getCompiledSelect());
			//		exit;


			// ✅ Se não faltar ninguém → atualiza a data da etapa
			if (count($leads) == 0) {
				$db->table('fluxos_etapas')
					->where('id_etapa', $etapa['id_etapa'])
					->update([
						'ultima_atualizacao' => date('Y-m-d')
					]);
			}

			foreach ($leads as $lead) {
				$feito_anterior = true;
				$status = null;
				$db->table('leads')->where('id', $lead['id_lead'])->update(['ultimoVerificacaoFlowAutomatico' => date('Y-m-d')]);

				// Verificar se o Sales Flow específico está ativo para este lead
				$flowAtivo = $db->table('lead_sales_flow_automatico')
					->where('id_lead', $lead['id_lead'])
					->where('id_flow', $etapa['id_fluxo'])
					->where('ativo', '1')
					->get()
					->getRowArray();

				// Se for flow não global (automatico = 0), verificar se está ativo
				if ($etapa['fluxos_automatico'] == "0" && !$flowAtivo) {
					// Flow não está ativo para este lead - pular
					continue;
				}

				// Se for flow global (automatico = 1), não precisa estar na tabela lead_sales_flow_automatico
				// Mas se estiver na tabela e estiver desativado, pular também
				if ($etapa['fluxos_automatico'] == "1") {
					$flowDesativado = $db->table('lead_sales_flow_automatico')
						->where('id_lead', $lead['id_lead'])
						->where('id_flow', $etapa['id_fluxo'])
						->where('ativo', '0')
						->get()
						->getRowArray();
					
					if ($flowDesativado) {
						// Flow foi explicitamente desativado - pular
						continue;
					}
				}

				// Atualizar última verificação do flow
				if ($flowAtivo) {
					$db->table('lead_sales_flow_automatico')
						->where('id_lead', $lead['id_lead'])
						->where('id_flow', $etapa['id_fluxo'])
						->update(['ultima_verificacao' => date('Y-m-d H:i:s')]);
				}

				if ($etapa['numero'] > 1) {
					// Busca a etapa anterior (somente principais)
					$etapa_anterior = $db->table('fluxos_etapas')
						->where('id_fluxo', $etapa['id_fluxo'])
						->where('numero <', $etapa['numero'])
						->orderBy('numero', 'desc')
						->limit(1)
						->get()
						->getRowArray();

					//print_r($etapa_anterior->getCompiledSelect());
					//exit;

					if ($etapa_anterior) {
						$feito_anterior = $this->db->table('leads_atv')
							->where('id_etapa_flow', $etapa_anterior['id_etapa'])
							->where('id_lead',  $lead['id_lead'])
							->countAllResults() > 0;
					}
				}

				// Verifica se é a próxima etapa
				$deveEnviar = false;


				if ($lead['id_ultimaEtapaAutomatico'] == null && $etapa['numero'] == 1) {
					$deveEnviar = true;
				} else if ($lead['id_ultimaEtapaAutomatico'] == null && $etapa['numero'] > 1) {

					$status = "etapa maior que 1, porem não foi lançado automatico";

					$res[] = [
						'status' => "etapa maior que 1, porem não foi lançado automatico",
						'id_etapa' => $etapa['id_etapa'],
						'id_lead' => $lead['id_lead'],
					];

					$primeiraEtapaAutomatica = $db->table('fluxos_etapas')
						->where('id_fluxo', $etapa['id_fluxo'])
						->where('automatico', '1')
						->orderBy('numero', 'asc')
						->limit(1)
						->get()
						->getRowArray();


					if ($primeiraEtapaAutomatica['id_etapa'] == $etapa['id_etapa']) {
						$deveEnviar = true;
					} else {

						$res[] = [
							'status' => "etapa mdiferentes",
							'id_etapa' => $etapa['id_etapa'],
							'id_lead' => $lead['id_lead'],
						];

						$deveEnviar = false;
					}
					//continue;
				} else {
					$proximaEtapa = $db->table('fluxos_etapas')
						->where('id_fluxo', $etapa['id_fluxo'])
						->where('numero >', function ($builder) use ($lead) {
							$builder->select('numero')
								->from('fluxos_etapas')
								->where('id_etapa', $lead['id_ultimaEtapaAutomatico']);
						})
						->orderBy('numero', 'asc')
						->limit(1)
						->get()
						->getRowArray();


					if ($proximaEtapa) {
						if ($proximaEtapa['id_etapa'] == $etapa['id_etapa']) {
							$deveEnviar = true; // é a próxima etapa mesmo
						} else {

							$status = "próxima etapa do fluxo é diferente da etapa atual";
							$res[] = [
								'status' => "existe próxima etapa diferente",
								'id_etapa' => $etapa['id_etapa'],
								'id_lead' => $lead['id_lead'],
							];


							$deveEnviar = false; // existe próxima etapa diferente
						}
					} else {
						$status = "não existe próxima (chegou na última), não envia";
						$res[] = [
							'status' => "não existe próxima (chegou na última), não envia",
							'id_etapa' => $etapa['id_etapa'],
							'id_lead' => $lead['id_lead'],
						];
						$deveEnviar = false; // não existe próxima (chegou na última), não envia
					}
				}

				// Verifica data limite
				if (
					$feito_anterior &&
					$deveEnviar &&
					(
						$lead['ultimoFlowAutomatico'] == null ||
						strtotime(date('Y-m-d')) >= strtotime($lead['ultimoFlowAutomatico'] . '+' . $etapa['dias'] . ' days')
					)
				) {

					$SqlContatos = $this->db->table('leads_contatos')
						->select('id_lead, email AS emailCttPrincipal, nm_contato AS nm_contato_principal, telefone AS telefoneCttPrincipal')
						->where('id_lead', $lead['id_lead']);

					if ($etapa['enviar_para'] == "1") {
						$SqlContatos->where('ctt_principal', '1')->limit(1);
					}
					$contatos =	$SqlContatos
						->orderBy('id_lead_contato', 'desc')
						->groupBy('id_lead')->get()
						->getResultArray();


					if ($etapa['enviar_para'] == "3") {
						$leads_data = $this->db->table('leads_data')
							->select('value')
							->where('id_lead',  $lead['id_lead'])
							->get()
							->getRow();

						if ($leads_data) {
							$valueData = json_decode($leads_data->value, true);
							if (!empty($valueData['Phones'])) {
								foreach ($valueData['Phones'] as $i => $fone) {
									if (isset($fone['FormattedNumber']) && !empty($fone['FormattedNumber'])) {
										$contatos[] = $fone['FormattedNumber'];
									}
								}
							}
						}
					}

					foreach ($contatos as $contato) {

						if (($etapa['canal'] == "1" && empty($contato['emailCttPrincipal'])) ||
							($etapa['canal'] == "2" && empty($contato['telefoneCttPrincipal']))
						) {
							$status = $etapa['canal'] == "2" ? "sem email" : "sem whastApp";
							$res[] = [
								'status' => $etapa['canal'] == "2" ? "sem email" : "sem whastApp",
								'id_etapa' => $etapa['id_etapa'],
								'id_lead' => $lead['id_lead'],
							];
							continue;
						}

						$var = [
							"{email}", "{empresa}", "{nome}",
							'<p><span><br data-mce-bogus="1"></span></p>',
							'<p><br data-mce-bogus="1"></p>', '<br>'
						];
						$nome = $contato['nm_contato_principal'] ?: ($lead['company'] ?: $lead['name']);
						$valor = [
							$contato['emailCttPrincipal'] ?: $lead['email'],
							$lead['company'] ?: explode(' ', $lead['name'])[0],
							explode(' ', $nome)[0],
							"", "", ""
						];

						$Body = str_replace(
							$var,
							$valor,
							$etapa['message']
						)
							. "<br> " .
							(isset($assinatura['message']) ? $assinatura['message'] : '');
						$Subject = str_replace($var, $valor, $etapa['subject']);

						if ($etapa['canal'] == "1") {

							$this->Emails_Model->send_email(
								$lead['email'] ?: $contato['emailCttPrincipal'],
								'',
								$Subject,
								$Body,
								'',
								$etapa['anexo'],
								$etapa['id_company'],
								$etapa['responsavel'],

								$etapa['cc'] ?? null,
								$etapa['cco'] ?? null,
							);

							$resultado = ['status' => 'ok'];
						} else if ($etapa['canal'] == "2") {

							$Body = str_replace(
								$var,
								$valor,
								$etapa['campoCopiar']
							);

							$settings_ia = $db->table('settings_ia')
								->where('id_company', $etapa['id_company'])
								->where('staff_id', $etapa['responsavel'])
								->get()
								->getRowArray();

							if (!$settings_ia) {
								$status = "sem settings_ia";
								$res = ['status' => "sem settings_ia"];
								continue;
							}

							$number_send = $contato['telefoneCttPrincipal'];
							if (empty($contato['telefoneCttPrincipal'])) {
								$number_send = $lead['phone'];
							}

							if ($anexo) {
								$filePath = FCPATH . 'uploads/fluxos/' . $anexo['arquivo'];
								$this->envia_mensagem_whatsapp(
									$settings_ia['number'],
									$number_send,
									$Body,            // sem texto, apenas mídia
									$filePath        // passa o caminho do arquivo
								);
							} else {
								$resultado = $this->envia_mensagem_whatsapp($settings_ia['number'], $number_send, $Body);
							}



							if (is_string($resultado)) {
								$resultado = json_decode($resultado, true); // transforma em array
							}
						}
					}



					// Registra atividade
					$db->table('leads_atv')->insert([
						'id_etapa_flow' => $etapa['id_etapa'],
						'id_lead' => $lead['id_lead'],
						'dt_entrada' => date('Y-m-d H:i:s'),
						'anotacoes' => isset($resultado['status']) ? ($resultado['status'] == "ok" ? 'Envio automático do sales flow <br>' . $Body : $resultado['status']) : ($status != null ? $status  : 'Erro'),
						'data' => date('Y-m-d'),
						'horario' => date('H:i:s'),
						'retorno' => date('Y-m-d', strtotime('+1 days')),
						'atividade' => $etapa['canal'] == "1" ? '4' : '11',
					]);

					$res[] = $lead;

					// Atualiza lead
					$db->table('leads')
						->where('id', $lead['id_lead'])
						->update([
							'id_ultimaEtapaAutomatico' => $etapa['id_etapa'],
							'ultimoFlowAutomatico' => date('Y-m-d'),
							'ultimoRetorno' => date('Y-m-d', strtotime('+1 days'))
						]);

					// Atualizar etapa atual no Sales Flow Automático
					if ($flowAtivo) {
						// Buscar nome da atividade
						$nomeEtapa = 'Etapa ' . $etapa['numero'];
						if (isset($etapa['atividade']) && is_array($etapa['atividade']) && isset($etapa['atividade']['nm_atividade_select'])) {
							$nomeEtapa = $etapa['atividade']['nm_atividade_select'];
						} elseif (!empty($etapa['name'])) {
							$nomeEtapa = $etapa['name'];
						}

						$db->table('lead_sales_flow_automatico')
							->where('id_lead', $lead['id_lead'])
							->where('id_flow', $etapa['id_fluxo'])
							->update([
								'etapa_atual' => $nomeEtapa,
								'ultima_verificacao' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
							]);
					}
				} else {

					if ($status != null) {
						$db->table('leads_atv')->insert([
							'id_etapa_flow' => 0,
							'id_lead' => $lead['id_lead'],
							'dt_entrada' => date('Y-m-d H:i:s'),
							'anotacoes' => "tentativa de envio automatico mal sucedida: " . $status,
							'data' => date('Y-m-d'),
							'horario' => date('H:i:s'),
							'retorno' => null,
							'atividade' => $etapa['canal'] == "1" ? '4' : '11',
						]);
					}

					$db->table('leads')
						->where('id', $lead['id_lead'])
						->update([
							'id_ultimaEtapaAutomatico' => $etapa['id_etapa'],
							'ultimoFlowAutomatico' => date('Y-m-d'),
							
						]);
				}
			}
		}

		return response()->setJSON($res);
	}



	function envia_mensagem_whatsapp($number, $number_send, $text, $filePath = null)
	{
		if (substr($number_send, 0, 2) !== '55') {
			$number_send = "55" . $number_send;
		}

		$url = 'https://apiwhats.ageup.pro/send-message';
		$data = [
			"number" => $number,
			"number_send" => $number_send,
			"text" => $text
		];

		// Se tiver arquivo
		if ($filePath && file_exists($filePath)) {
			$mimeType = mime_content_type($filePath);
			$fileName = basename($filePath);
			$base64   = base64_encode(file_get_contents($filePath));

			$data['media'] = [
				"mimetype" => $mimeType,
				"filename" => $fileName,
				"data"     => $base64
			];
		}

		//log_message('debug', 'Enviando WhatsApp: ' . json_encode($data));

		$postdata = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // previne travar indefinidamente

		$result = curl_exec($ch);

		if ($result === false) {
			$error = curl_error($ch);
			$errno = curl_errno($ch);
			//log_message('error', "Erro cURL ($errno): $error");
			echo "Erro cURL ($errno): $error"; // para debug
		} else {
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			//log_message('debug', "Resposta HTTP ($httpCode): $result");
			//echo "Resposta HTTP ($httpCode): $result"; // para debug
		}

		curl_close($ch);

		return $result;
	}





	function envia_avisos()
	{
		$results = [];
		$this->email = \Config\Services::email();
		$avisos = $this->db->table('avisos')
			->where([
				'is_ativo' => '1',
				'tipo' => '1'
			])
			->get()
			->getResultArray();


		//print_r($avisos);
		//	exit;

		foreach ($avisos as $index => $aviso) {
			$assinatura = $this->db->table('email_assinaturas')
				->where('id_company', '1')
				->get()
				->getRowArray();

			$funcionarios = explode(",", $aviso['funcionarios']);
			foreach ($funcionarios as $funcionario) {
				$ultimo_log = $this->db->table('logs')
					->where('staff_id', $funcionario)
					->where('CAST(date AS DATE)', date('Y-m-d'))
					->orderBy('date', 'DESC')
					->get()
					->getRowArray();

				$staff = $this->db->table('staff')
					->where('id', $funcionario)
					->get()
					->getRowArray();

				$horas = $this->Staff_Model->get_horasTrabalho($funcionario);

				$avisos[$index]['horas'][] = $horas;
				$avisos[$index]['ultimo_log'][] = $ultimo_log;
				$avisos[$index]['horas'][] = $horas;

				if (
					$ultimo_log
					&& isset($ultimo_log['date'])
					&& strtotime(date('Y-m-d H:i:s')) > strtotime($ultimo_log['date'] . '+' . $aviso['tempo'] . ' minutes')
					&& (strtotime($staff['ultimo_aviso'] . '+' . $aviso['tempo'] . ' minutes') < strtotime(date('Y-m-d H:i:s')) || $staff['ultimo_aviso'] == "")
					&& (
						(strtotime(date('H:i:s')) > strtotime($horas['start01']) && strtotime(date('H:i:s')) < strtotime($horas['end01'])) ||
						(strtotime(date('H:i:s')) > strtotime($horas['start02']) && strtotime(date('H:i:s')) < strtotime($horas['end02']))
					)

				) {
					$data_inicio = new DateTime($ultimo_log['date']);
					$data_fim = new DateTime(date('Y-m-d H:i:s'));
					$dateInterval = $data_inicio->diff($data_fim);

					$minutes = $dateInterval->days * 24 * 60;
					$minutes += $dateInterval->h * 60;
					$minutes += $dateInterval->i;

					$tempo = intval($minutes / $aviso['tempo']) * $aviso['tempo'] . ' minutos';

					if ($tempo > 0) {
						if ($aviso['opcaoAlerta'] == "2" || $aviso['opcaoAlerta'] == "3") {
							foreach (explode(",", $aviso['emails']) as $email) {
								$results['emails'][] = $email;
								$body = "O funcionário $staff[staffname] está a $tempo sem atividade.";
								$body .= "<br> " . (isset($assinatura['message']) ? $assinatura['message'] : '');

								$Subject = "Aviso de inatividade!";

								$anexo = null;
								if (file_exists($aviso['anexo'])) {
									$anexo = $aviso['anexo'];
								}

								$sendEmail = $this->Emails_Model->send_email2($email, $Subject, $body, $anexo);

								$results['envios'][] = $sendEmail;
							}
						}
						if ($aviso['opcaoAlerta'] == "1" || $aviso['opcaoAlerta'] == "3") {
							$this->db->table('notifications')->insert([
								'staff_id' => $aviso['staff_id'],
								'staff_id_acao' => $funcionario,
								'date' => date('Y-m-d H:i:s'),
								'detail' => $aviso['menssagem'],
								'markread' => 0
							]);
						}
						$this->db->table('staff')
							->where('id', $funcionario)
							->update(['ultimo_aviso' => date('Y-m-d H:i:s')]);
					}
				}
			}
		}

		$results['avisos_achados'] = $avisos;

		return response()->setJSON($results);
	}

	function envia_reports()
	{
		$results = [];

		$data = $this->Companies_Model->get_companiesReports();

		foreach ($data as $company) {
			$data_inicial = $company['ultimoEnvio'] != '' ? $company['ultimoEnvio'] : $company['data'];
			$diferenca = strtotime(date('Y-m-d')) - strtotime($data_inicial);
			$dias = floor($diferenca / (60 * 60 * 24));

			if ($dias < $company['frequencia'] || in_array(date('w', strtotime(date('Y-m-d'))), [0, 6])) {
				continue;
			}
			$template = $this->db->table('email_templates')
				->where('id', $company['modelo_de_email'])
				->get()
				->getRowArray();

			$assinatura = $this->db->table('email_assinaturas')
				->where('id_company', $company['id_company'])
				->get()
				->getRowArray();

			$token = md5(date('YmdHis') . 'T8e') . rand(0, 800000) . 'FeT';
			$this->db->table('companies_reports_envio')->insert([
				'id_company' => $company['id_company'],
				'token' => $token,
				'data' => date('Y-m-d'),
				'permissoes' => $company['permissoes']
			]);


			foreach (explode(',', $company['emails']) as $email) {
				$var = [
					"{email}",
					"{empresa}",
					"{nome}",
					'<p><span><br data-mce-bogus="1"></span></p>',
					'<p><br data-mce-bogus="1"></p>',
					'<br>',

				];
				$valor = [
					'',
					$company['nm_company'],
					'',
					"",
					"",
					"",
				];
				$Body = str_replace($var, $valor, $template['message']);
				$Body .= "<br> <a href = '" . base_url('companies/ver_grafico') . "?token=$token' >Abrir o Dashboard</a>";
				$Body .= "<br> " . (isset($assinatura['message']) ? $assinatura['message'] : '');
				$Subject = str_replace($var, $valor, $template['subject']);

				$sendEmail = $this->Emails_Model->send_email2($email, $Subject, $Body);
				$results[] = $sendEmail;
			}
		}

		return response()->setJSON($results);
	}

	function email()
	{
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/Exception.php';
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/PHPMailer.php';
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/SMTP.php';

		$tasks = $this->Emails_Model->getTaskAut(true);
		$result = [];
		$total = [];

		if (count($tasks) == 0) {
			echo "Erro, Nenhuma tarefa! <br>";
		}

		foreach ($tasks as $task) {
			$total[$task['name_taskAut']] = 0;
			if (count($task['leads']) == 0) {
				echo "Erro, Nenhum lead! <br>";
			}

			$templates = [];
			$templates = [];
			foreach ($this->db->table('email_templates')->where('id_company', $task['id_company'])->get()->getResultArray() as $row) {
				$templates[$row['id']] = $row;
			}

			$assinatura = $this->db->table('email_assinaturas')
				->where('id_company', $task['id_company'])
				->where('staff_id', $task['respon_email'])
				->get()
				->getRowArray();

			foreach ($task['leads'] as $lead) {

				//print_r($task);
				//print_r($lead);

				if (
					!isset($lead['smtphost']) || !isset($lead['smtpusername']) || !isset($lead['smtppassoword']) || !isset($lead['sendermail']) ||
					!isset($lead['smtpport'])
					|| $lead['smtphost'] == "" || $lead['smtpusername'] == "" || $lead['smtppassoword'] == "" || $lead['sendermail'] == "" ||
					$lead['smtpport'] == ""
				) {
					echo "Erro ao enviar email " . $task['name_taskAut'] . '<br>';
					continue;
				}

				$etp = $lead['etapa_task'];
				$periodo = $task['periodo' . ($etp + 1) . '_taskAut'];

				if ($periodo != '') {
					$periodo = $periodo - 1;

					if ($periodo > 1) {
						$dataTask = new DateTime(date('Y-m-d'));
						$dataTask->sub(new DateInterval('P' . $periodo . 'D'));
						$data = $dataTask->format('Y-m-d');
					} else {
						$data = date('Y-m-d');
					}

					//print_r($data);
					//exit;

					if (
						(strtotime($lead['ultima_verificacao']) == strtotime(date('Y-m-d'))) ||
						(strtotime($lead['ultima_verificacao']) >= strtotime($data))
					) {
						continue;
					}
				} else {
					echo "Periodo não definido " . $task['name_taskAut'] . '<br>';

					continue;
				}

				if (!isset($templates[$task['template' . ($etp + 1)]])) {
					echo "modelo de email nao encontrado " . $task['name_taskAut'] . '<br>';
					continue;
				}

				$template = $templates[$task['template' . ($etp + 1)]];

				try {

					$mail = new PHPMailer(true);
					$mail->SMTPDebug = false;
					//$mail->isSMTP();
					$mail->Host = $lead['smtphost']; //'smtp.hostinger.com';
					$mail->SMTPAuth = true;
					$mail->Username = $lead['smtpusername']; //'teste@bmcomp.xyz';
					$mail->Password = $lead['smtppassoword']; //'senhaTemp02';
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
					$mail->Port = $lead['smtpport'];

					$mail->setFrom($lead['sendermail']/*'teste@bmcomp.xyz'*/);
					$email = "";
					if ($lead['enviar_para'] == "1" || $lead['enviar_para'] == null) {
						if ($lead['emailCttPrincipal'] != '') {
							$email = $lead['emailCttPrincipal'];
							$mail->addAddress($lead['emailCttPrincipal'], $lead['nm_contato_principal']);
						} elseif ($lead['email'] != '') {
							$email = $lead['email'];
							$mail->addAddress($lead['email'], $lead['nm_lead']);
						} else {
							echo "Erro, Nenhum email! lead: $lead[id_lead]  <br>";
							continue;
						}
					} else {

						$contatos = $this->db->table('leads_contatos')
							->select('id_lead, email AS emailCttPrincipal, nm_contato AS nm_contato_principal, telefone AS telefoneCttPrincipal')
							->where('id_lead', $lead['id_lead'])
							->orderBy('id_lead_contato', 'desc')
							->groupBy('id_lead')->get()
							->getResultArray();

						foreach ($contatos as $contato) {
							$mail->addAddress($contato['email'], $contato['nm_contato']);
						}
					}

					if ($task['copia_para_taskAut'] != "") {
						$mail->addCC($task['copia_para_taskAut']);
					}
					if ($task['copia_para_oculto_taskAut'] != "") {
						$mail->addBCC($task['copia_para_oculto_taskAut']);
					}
					if ($template['anexo'] != "") {
						if (file_exists(dirname(__FILE__) . '/../../uploads/anexos/' . $template['anexo'])) {
							$mail->addAttachment(dirname(__FILE__) . '/../../uploads/anexos/' . $template['anexo']);
						}
					}

					$mail->isHTML(true);
					$nome = "";
					$nm_company = "";

					if ($lead['tp_pessoa'] == '2') {
						$nm_company = $lead['company'];
					} else {
						$nm_company = $lead['name'];
					}

					if ($lead['nm_contato_principal'] != '') {
						$nome = $lead['nm_contato_principal'];
					} else if ($lead['nm_contato'] != '') {
						$nome = $lead['nm_contato'];
					} else {
						$nome = $lead['nm_lead'];
					}


					$var = [
						"{email}",
						"{empresa}",
						"{nome}",
						'<p><span><br data-mce-bogus="1"></span></p>',
						'<p><br data-mce-bogus="1"></p>',
						'<br>',
					];
					$valor = [
						$email,
						$nm_company,
						explode(' ', $nome)[0],
						"",
						"",
						"",
					];

					//	print_r($template);
					//	print_r($assinatura);

					$Body = str_replace(
						$var,
						$valor,
						(isset($template['message']) ? $template['message'] : '') . " <br> " .
							(isset($assinatura['message']) ? $assinatura['message'] : '')
					);

					//print_r($Body);
					//exit;

					$Subject = str_replace($var, $valor, $template['subject']);


					$mail->Subject = $Subject;
					$mail->Body = $Body;
					//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
					$mail->CharSet = 'UTF-8';
					if ($mail->send()) {
						$total[$task['name_taskAut']]++;
						$etp = $lead['etapa_task'];

						if (isset($task['periodo' . ($etp + 2) . '_taskAut']) && !empty($task['periodo' . ($etp + 2) . '_taskAut'])) {
							$periodo = $task['periodo' . ($etp + 2) . '_taskAut'];
							$dataTask = new DateTime(date(date('Y-m-d')));
							$dataTask->add(new DateInterval('P' . $periodo . 'D'));
							$data = $dataTask->format('Y-m-d');
						} else {
							$data = date('Y-m-d');
						}

						$result['result'][] = 'Enviado id:' . $lead['id_lead'];
						$leads_atv = array(
							'id_lead' => $lead['id_lead'],
							'atividade' => '1',
							'id_criador' => $lead['assigned_id'],
							'dt_entrada' => date('Y-m-d'),
							'data' => date('Y-m-d'),
							'horario' => date('H:i'),
							'duracao' => '00:00:00',
							'retorno' => $data,
							'anotacoes' => 'Email enviado automaticamente pelo sistema! <br> ' . $Body,
						);

						$this->db->table('leads_atv')->insert($leads_atv);

						if ($task['tipo'] == "L") {
							$this->db->query("UPDATE `email_task_leads`
							SET etapa = (email_task_leads.etapa + 1), ultima_verificacao = cast(now() as date)
							WHERE `email_task_leads`.`id_task` = '" . $task['id_task'] . "' and id_lead = '" . $lead['id_lead'] . "';");
						} else {
							$this->db->query("UPDATE `email_task_customers`
							SET etapa = (email_task_customers.etapa + 1), ultima_verificacao = cast(now() as date)
							WHERE `email_task_customers`.`id_task` = '" . $task['id_task'] . "' and id_customer = '" . $lead['id_lead'] . "';");
						}
					} else {
						$result['result'][] = 'Não enviado';
					}
				} catch (Exception $e) {
					echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo} <br>";
				}
			}
		}

		print_r($result);
		print_r($total);
	}



	function run()
	{

		foreach ($this->Invoices_Model->get_all_recurring() as $key => $value) {

			if ($value['relation_type'] == 'invoice') {

				if (($value['end_date'] != 'Invalid date') && (strtotime(date("Y-m-d", strtotime($value['end_date']))) <= strtotime(date('Y-m-d')))) {

					continue;
				}



				$id = $value['relation'];

				$invv = $this->db->table('invoices')
					->where('id', $value['relation'])
					->get()
					->getResultArray();


				$invv = end($invv);

				if ($invv['last_recurring'] != NULL && date('Y-m-d', strtotime($invv['last_recurring'])) == date('Y-m-d')) {

					continue;
				}

				// Years

				if ($value['type'] == '3' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'Years')) != date('Y-m-d')) {

					continue;
				}

				// Month

				if ($value['type'] == '2' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'month')) != date('Y-m-d')) {

					continue;
				}

				// Week

				if ($value['type'] == '1' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'week')) != date('Y-m-d')) {

					continue;
				}

				// Day

				if ($value['type'] == '0' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'day')) != date('Y-m-d')) {

					continue;
				}

				$invoice = $this->Invoices_Model->get_invoices($id);

				$created = date('Y-m-d');

				$invoices = array(

					'token' => md5(uniqid()),

					'no' => $invoice['no'],

					'serie' => $invoice['serie'],

					'customer_id' => $invoice['customer_id'],

					'staff_id' => $invoice['staff_id'],

					'status_id' => 3,

					'created' => $created,

					'last_recurring' => $created,

					'duedate' => date('Y-m-d', strtotime($created . '+30 days')), // +30 day

					'duenote' => $invoice['duenote'],

					'sub_total' => $invoice['sub_total'],

					'total_discount' => $invoice['total_discount'],

					'total_tax' => $invoice['total_tax'],

					'total' => $invoice['total'],

					'recurring' => $value['id'],

				);

				$items = $this->db->table('items')
					->select('*')
					->where('relation_type', 'invoice')
					->where('relation', $id)
					->get()
					->getResultArray();

				$invoices_id = $this->Invoices_Model->recurring_invoice($invoices, $items);

				if ($invoices_id) {

					$this->Invoices_Model->update_recurring_date($id);

					$this->Settings_Model->create_process('pdf', $invoices_id, 'invoice', 'invoice_recurring');
				}
			}
		}

		$this->expense_recurrings();
	}



	function expense_recurrings()
	{

		foreach ($this->Expenses_Model->get_all_recurring() as $key => $value) {

			if ($value['relation_type'] == 'expense') {

				if (($value['end_date'] != 'Invalid date') && (strtotime(date("Y-m-d", strtotime($value['end_date']))) <= strtotime(date('Y-m-d')))) {

					continue;
				}

				$id = $value['relation'];


				$invv = $this->db->table('expenses')
					->where('id', $value['relation'])
					->get()
					->getResultArray();

				$invv = end($invv);

				if ($invv['last_recurring'] != NULL && date('Y-m-d', strtotime($invv['last_recurring'])) == date('Y-m-d')) {

					continue;
				}

				// Years

				if ($value['type'] == '3' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'Years')) != date('Y-m-d')) {

					continue;
				}

				// Month

				if ($value['type'] == '2' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'month')) != date('Y-m-d')) {

					continue;
				}

				// Week

				if ($value['type'] == '1' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'week')) != date('Y-m-d')) {

					continue;
				}

				// Day

				if ($value['type'] == '0' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'day')) != date('Y-m-d')) {

					continue;
				}

				$expense = $this->Expenses_Model->get_expenses($id);

				$expense_data = array(

					'category_id' => $expense['category_id'],

					'staff_id' => $expense['staff_id'],

					'customer_id' => $expense['customer_id'],

					'account_id' => $expense['account_id'],

					'title' => $expense['title'],

					'number' => $expense['number'],

					'date' => $expense['date'],

					'created' => date('Y-m-d H:i:s'),

					'amount' => $expense['amount'],

					'total_tax' => $expense['total_tax'],

					'total_discount' => $expense['total_discount'],

					'sub_total' => $expense['sub_total'],

					'internal' => $expense['internal'],

					'last_recurring' => date('Y-m-d'),

					'expense_created_by' => session()->usr_id

				);

				$items = $this->db->table('items')
					->select('*')
					->where('relation_type', 'expense')
					->where('relation', $id)
					->get()
					->getResultArray();

				$expense_id = $this->Expenses_Model->recurring_expense($expense_data, $items);



				if ($expense_id) {

					$this->Expenses_Model->update_recurring_date($id);

					$this->Settings_Model->create_process('pdf', $expense_id, 'expense', 'expense_recurring');
				}
			}
		}

		$this->purchase_recurrings();
	}



	function emails()
	{

		$emails = $this->Emails_Model->get_emails();

		if ($emails) {

			$path = NULL;

			foreach ($emails as $key => $email) {

				$path = $email['attachments'];



				$data_email = @unserialize($email['email']);

				if ($data_email !== false) {

					$recipient = unserialize($email['email']);
				} else {

					$recipient = $email['email'];
				}

				$data = $this->Emails_Model->send_email($recipient, $email['from_name'], $email['subject'], $email['message'], $path);

				$this->email->clear(TRUE);

				if ($data['success'] == true) {

					$this->Emails_Model->email_sent($email['id']);

					$path = NULL;

					unset($path);
				}
			}
		}

		$this->check_events();

		$this->check_pending_process();
	}



	function check_events()
	{

		foreach ($this->Events_Model->get_event_triggers() as $key => $value) {

			if ($value['relation_type'] == 'event') {

				$type = 'minute';

				switch ($value['duration_type']) {

					case '0':

						$type = 'minutes';

						break;

					case '1':

						$type = 'hour';

						break;

					case '2':

						$type = 'day';

						break;

					case '3':

						$type = 'week';

						break;
				}

				$time = strtotime((date('Y-m-d H:i:s', strtotime($value['start'] . ' +' . $value['duration_period'] . $type))));

				$present = strtotime(date_by_timezone(date('Y-m-d H:i:s')));

				// echo strtotime((date('Y-m-d H:i:s',strtotime($value['start'].' +'.$value['duration_period'].$type)))).' calculated'.'<br>';

				// echo strtotime($value['start']).' start'.'<br>'.'<br>';



				// echo $time.' calculated'.'<br>';

				// echo $present.' now'.'<br>'.'<br>';

				// echo $value['id'].'<br>';

				// echo $type.'<br>';

				// echo $value['duration_period'].'<br>';

				// echo $value['start'].'<br>';

				// echo (date('Y-m-d H:i:s',strtotime($value['start'].' +'.$value['duration_period'].$type))).' calculated'.'<br>';

				// echo date_by_timezone(date('Y-m-d H:i:s')).' now'.'<br>';

				// echo strtotime((date('Y-m-d H:i:s',strtotime($value['start'].' +'.$value['duration_period'].$type)))).'<br>';

				// echo strtotime($value['start']).'<br>'.'<br>'.'<br>';



				if ($present >= $time) {

					$event = $this->Events_Model->get_event($value['relation']);

					if ($value['type'] == 'reminder') {

						return response()->setJSON($event);

						$reminder = array(
							'relation_type' => 'event',
							'relation' => $event['id'],
							'staff_id' => $event['staff_id'],
							'description' => $event['title'],
							'date' => $event['start'],
							'isnotified' => '0',
							'addedfrom' => $event['added_by'],
							'public' => ($event['public'] == '1') ? '1' : '0',
						);

						$this->db->table('reminders')->insert($reminder);

						$this->Events_Model->update_event_trigger($value['id']);
					}

					if ($value['type'] == 'email') {

						$template = $this->Emails_Model->get_template('staff', 'event_reminder');

						if ($template['status'] == 1) {

							$event = $this->Events_Model->get_event($value['relation']);

							$settings = $this->Settings_Model->get_settings_ciuis();

							$message_vars = array(

								'{empresa}' => $settings['company'],

								'{email}' => $settings['email'],

								'{staff}' => $event['staffname'],

								'{staff_email}' => $event['email'],

								'{event_title}' => $event['title'],

								'{event_type}' => $event['event_type'],

								'{event_details}' => $event['detail'],

								'{event_start}' => $event['start'],

								'{event_end}' => $event['end'],

							);

							$subject = strtr($template['subject'], $message_vars);

							$message = strtr($template['message'], $message_vars);

							if ($event['is_all'] == '1') {

								$staffs = $this->Events_Model->get_all_staffs();

								foreach ($staffs as $staff) {

									$recipients[] = $staff['email'];
								}
							} else {

								$recipients[] = $event['email'];
							}

							if (count($recipients) > 0) {

								$param = array(
									'from_name' => $template['from_name'],
									'email' => serialize($recipients),
									'subject' => $subject,
									'staff_id' => session()->get('usr_id'),
									'message' => $message,
									'created' => date_by_timezone(date('Y-m-d H:i:s')),
								);

								$this->db->table('email_queue')->insert($param);

								$this->Events_Model->update_event_trigger($value['id']);
							}
						}
					}
				}
			}
		}
	}



	function check_pending_process()
	{

		$settings = $this->Settings_Model->get_settings_ciuis();

		$processes = $this->Settings_Model->get_pending_process();

		if ($processes) {

			foreach ($processes as $key => $value) {

				$path = NULL;

				unset($path);

				if ($value['process_type']) {

					if ($value['process_type'] == 'pdf') {



						//Invoice

						if ($value['process_relation_type'] == 'invoice') {

							$invoice = $this->Invoices_Model->get_invoice_detail($value['process_relation']);

							$path = '';

							if ($invoice['pdf_status'] == 0) {

								$this->Invoices_Model->generate_pdf($invoice['id']);
							}

							$file = get_number('invoices', $invoice['id'], 'invoice', 'inv');

							$path = base_url('uploads/files/invoices/' . $invoice['id'] . '/' . $file . '.pdf');

							if ($value['process_template_name'] == 'invoice_message') {

								$template = $this->Emails_Model->get_template('invoice', 'invoice_message');

								if ($template['status'] == 1) {

									$inv_number = get_number('invoices', $invoice['id'], 'invoice', 'inv');

									$name = $invoice['customercompany'] ? $invoice['customercompany'] : $invoice['individualindividual'];

									$link = base_url('share/invoice/' . $invoice['token'] . '');

									if ($invoice['status_id'] == 1) {

										$invoicestatus = lang2('draft');
									}

									if ($invoice['status_id'] == 3) {

										$invoicestatus = lang2('unpaid');
									}

									if ($invoice['status_id'] == 4) {

										$invoicestatus = lang2('cancelled');
									}

									if ($invoice['status_id'] == 2) {

										$invoicestatus = lang2('partial');
									}

									$message_vars = array(

										'{invoice_number}' => $inv_number,

										'{invoice_link}' => $link,

										'{invoice_status}' => $invoicestatus,

										'{name}' => $settings['company'],

										'{email_signature}' => $settings['email'],

										'{nome}' => $name,

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$param = array(
										'from_name' => $template['from_name'],
										'email' => $invoice['email'],
										'subject' => $subject,
										'message' => $message,
										'staff_id' => session()->get('usr_id'),
										'created' => date("Y.m.d H:i:s"),
									);

									if ($template['attachment'] == 1) {

										$param['attachments'] = $path ? $path : NULL;
									}

									if ($invoice['email']) {

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}

									$path = null;

									unset($path);
								}
							}

							if ($value['process_template_name'] == 'invoice_recurring') {

								$template = $this->Emails_Model->get_template('invoice', 'invoice_recurring');

								if ($template['status'] == 1) {

									$inv_number = get_number('invoices', $invoice['id'], 'invoice', 'inv');

									$name = $invoice['customercompany'] ? $invoice['customercompany'] : $invoice['individualindividual'];

									$link = base_url('share/invoice/' . $invoice['token'] . '');

									$message_vars = array(

										'{invoice_number}' => $inv_number,

										'{invoice_link}' => $link,

										'{invoice_status}' => lang2('unpaid'),

										'{email_signature}' => $settings['email'],

										'{nome}' => $name,

										'{name}' => $settings['company'],

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$param = array(
										'from_name' => $template['from_name'],
										'email' => $invoice['email'],
										'staff_id' => session()->get('usr_id'),
										'subject' => $subject,
										'message' => $message,
										'created' => date_by_timezone(date('Y-m-d H:i:s')),
									);

									if ($template['attachment'] == 1) {

										$param['attachments'] = $path ? $path : NULL;
									}

									if ($invoice['email']) {

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}

									$path = null;

									unset($path);
								}
							}
						}



						//Expense

						if ($value['process_relation_type'] == 'expense') {

							$expense = $this->Expenses_Model->get_expenses($value['process_relation']);

							$path = '';

							if ($expense['pdf_status'] == 0) {

								$this->Expenses_Model->generate_pdf($expense['id']);
							}

							$file = get_number('expenses', $expense['id'], 'expense', 'expense');

							$path = base_url('uploads/files/expenses/' . $expense['id'] . '/' . $file . '.pdf');

							if ($value['process_template_name'] == 'expense_created') {

								$template = $this->Emails_Model->get_template('expense', 'expense_created');

								if ($template['status'] == '1') {

									if ($expense['namesurname']) {

										$customer = $expense['namesurname'];
									} else {

										$customer = $expense['customer'];
									}

									$message_vars = array(

										'{nome}' => $customer,

										'{expense_number}' => get_number('expenses', $expense['id'], 'expense', 'expense'),

										'{expense_title}' => $expense['title'],

										'{expense_category}' => $expense['category'],

										'{expense_date}' => $expense['date'],

										'{expense_description}' => $expense['description'],

										'{expense_amount}' => $expense['amount'],

										'{name}' => $settings['company'],

										'{email_signature}' => $settings['email'],

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$email = $expense['customeremail'] ? $expense['customeremail'] : $expense['staffemail'];

									$consultants = $this->Expenses_Model->get_consultants();

									$recipients = array();

									foreach ($consultants as $consultant) {

										$recipients[] = $consultant['email'];
									}

									$recipients[] = $email;

									if ($email) {

										$param = array(
											'from_name' => $template['from_name'],
											'email' => serialize($recipients),
											'subject' => $subject,
											'message' => $message,
											'created' => date_by_timezone(date('Y-m-d H:i:s')),
											'staff_id' => session()->get('usr_id'),
										);

										if ($template['attachment'] == 1) {

											$param['attachments'] = $path ? $path : NULL;
										}

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}
								}
							}

							if ($value['process_template_name'] == 'expense_recurring') {

								$template = $this->Emails_Model->get_template('expense', 'expense_recurring');

								if ($template['status'] == 1) {

									$customer = '';

									if ($expense['namesurname'] || $expense['customer']) {

										if ($expense['namesurname']) {

											$customer = $expense['namesurname'];
										} else {

											$customer = $expense['customer'];
										}
									}

									$message_vars = array(

										'{nome}' => $customer,

										'{staff}' => $expense['staff'],

										'{expense_number}' => get_number('expenses', $expense['id'], 'expense', 'expense'),

										'{expense_title}' => $expense['title'],

										'{expense_category}' => $expense['category'],

										'{expense_date}' => $expense['date'],

										'{expense_description}' => $expense['description'],

										'{expense_amount}' => $expense['amount'],

										'{name}' => $settings['company'],

										'{email_signature}' => $settings['email'],

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$email = $expense['staffemail'];

									if ($email) {

										$param = array(
											'from_name' => $template['from_name'],
											'email' => $email,
											'subject' => $subject,
											'message' => $message,
											'created' => date_by_timezone(date('Y-m-d H:i:s')),
											'staff_id' => session()->get('usr_id'),
										);

										if ($template['attachment'] == 1) {

											$param['attachments'] = $path ? $path : NULL;
										}

										$this->db->table('email_queue')->insert($param);
									}
								}

								//$template_c = $this->Emails_Model->get_template('expense', 'expense_consultant');

								if ($template['status'] == 1) {

									$customer = '';

									if ($expense['namesurname'] || $expense['customer']) {

										if ($expense['namesurname']) {

											$customer = $expense['namesurname'];
										} else {

											$customer = $expense['customer'];
										}
									}

									$message_vars = array(

										'{nome}' => $customer,

										'{staff}' => $expense['staff'],

										'{expense_number}' => get_number('expenses', $expense['id'], 'expense', 'expense'),

										'{expense_title}' => $expense['title'],

										'{expense_category}' => $expense['category'],

										'{expense_date}' => $expense['date'],

										'{expense_description}' => $expense['description'],

										'{expense_amount}' => $expense['amount'],

										'{name}' => $settings['company'],

										'{email_signature}' => $settings['email'],

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$consultants = $this->Expenses_Model->get_consultants();

									$recipients = array();

									foreach ($consultants as $consultant) {

										$recipients[] = $consultant['email'];
									}

									if (count($recipients) > 0) {

										$param = array(
											'from_name' => $template['from_name'],
											'email' => serialize($recipients),
											'subject' => $subject,
											'message' => $message,
											'created' => date_by_timezone(date('Y-m-d H:i:s')),
											'staff_id' => session()->get('usr_id'),
										);

										if ($template['attachment'] == 1) {

											$param['attachments'] = $path ? $path : NULL;
										}

										$this->db->table('email_queue')->insert($param);
									}
								}
							}

							$this->Settings_Model->remove_pending_process($value['process_id']);

							$path = NULL;

							unset($path);
						}



						//Purchase

						if ($value['process_relation_type'] == 'purchase') {

							$purchase = $this->Purchases_Model->get_purchases_detail($value['process_relation']);

							$path = '';

							if ($purchase['pdf_status'] == 0) {

								$this->Purchases_Model->generate_pdf($purchase['id']);
							}

							$file = get_number('purchases', $purchase['id'], 'purchase', 'purchase');

							$path = base_url('uploads/files/purchases/' . $purchase['id'] . '/' . $file . '.pdf');

							if ($value['process_template_name'] == 'purchase_message') {

								$template = $this->Emails_Model->get_template('purchase', 'purchase_message');

								if ($template['status'] == 1) {

									if ($purchase['status_id'] == '2') {

										$payments = $this->Expenses_Model->get_expense_by_purchase($purchase['id']);

										$payments_details = $this->Payments_Model->get_payment_details($payments);
									}

									$purchase_number = get_number('purchases', $purchase['id'], 'purchase', 'purchase');

									if ($purchase['status_id'] == 1) {

										$purchasestatus = lang2('draft');
									}

									if ($purchase['status_id'] == 3) {

										$purchasestatus = lang2('unpaid');
									}

									if ($purchase['status_id'] == 4) {

										$purchasestatus = lang2('cancelled');
									}

									if ($purchase['status_id'] == 2) {

										$purchasestatus = lang2('partial');
									}

									$name = $purchase['vendorcompany'];

									$link = base_url('share/purchases/' . $purchase['token'] . '');

									if ($purchase['status_id'] == '2') {

										$message_vars = array(

											'{purchase_number}' => $purchase_number,

											'{vendor_name}' => $name,

											'{issuance_date}' => $purchase['created'],

											'{due_date}' => $purchase['duedate'],

											'{payment_date}' => $payments_details['date'],

											'{payment_amount}' => $payments_details['amount'],

											'{payment_account}' => $payments_details['name'],

											'{payment_description}' => $payments_details['not'],

											'{payment_made_by}' => $payments_details['staffname'],

											'{purchase_status}' => $purchasestatus,

											'{total_amount}' => $purchase['total'],

											'{empresa}' => $settings['company'],

											'{email}' => $settings['email'],

											'{due_note}' => $purchase['duenote'],

											'{purchase_link}' => $link,

										);
									} else {

										$message_vars = array(

											'{purchase_number}' => $purchase_number,

											'{vendor_name}' => $name,

											'{issuance_date}' => $purchase['created'],

											'{due_date}' => $purchase['duedate'],

											'{purchase_status}' => $purchasestatus,

											'{total_amount}' => $purchase['total'],

											'{empresa}' => $settings['company'],

											'{email}' => $settings['email'],

											'{due_note}' => $purchase['duenote'],

											'{purchase_link}' => $link,

										);
									}

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);



									$param = array(
										'from_name' => $template['from_name'],
										'email' => $purchase['email'],
										'subject' => $subject,
										'message' => $message,
										'created' => date_by_timezone(date('Y-m-d H:i:s')),
										'staff_id' => session()->get('usr_id'),
									);

									if ($template['attachment'] == 1) {

										$param['attachments'] = $path ? $path : NULL;
									}

									if ($purchase['email']) {

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}

									$path = NULL;

									unset($path);
								}
							}

							if ($value['process_template_name'] == 'purchase_recurring') {

								$template = $this->Emails_Model->get_template('purchase', 'purchase_recurring');

								if ($template['status'] == 1) {

									$purchase_number = get_number('purchases', $purchase['id'], 'purchase', 'purchase');

									$name = $purchase['vendorcompany'];

									$link = base_url('share/purchase/' . $purchase['token'] . '');

									$message_vars = array(

										'{purchase_number}' => $purchase_number,

										'{vendor_name}' => $name,

										'{issuance_date}' => $purchase['created'],

										'{due_date}' => $purchase['duedate'],

										'{purchase_status}' => lang2('unpaid'),

										'{total_amount}' => $purchase['total'],

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

										'{due_note}' => $purchase['duenote'],

										'{purchase_link}' => $link,

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$param = array(
										'from_name' => $template['from_name'],
										'email' => $purchase['email'],
										'subject' => $subject,
										'message' => $message,
										'created' => date_by_timezone(date('Y-m-d H:i:s')),
										'staff_id' => session()->get('usr_id'),
									);

									if ($template['attachment'] == 1) {

										$param['attachments'] = $path ? $path : NULL;
									}

									if ($purchase['email']) {

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}

									$path = NULL;

									unset($path);
								}
							}
						}



						//Proposal

						if ($value['process_relation_type'] == 'proposal') {

							$pro = $this->Proposals_Model->get_pro_rel_type($value['process_relation']);

							$rel_type = $pro['relation_type'];

							$proposal = $this->Proposals_Model->get_proposals($value['process_relation'], $rel_type);

							if ($proposal['pdf_status'] == 0) {

								$this->Proposals_Model->generate_pdf($proposal['id']);
							}

							$file = get_number('proposals', $proposal['id'], 'proposal', 'proposal');

							$path = base_url('uploads/files/proposals/' . $proposal['id'] . '/' . $file . '.pdf');

							if ($value['process_template_name'] == 'send_proposal') {

								$template = $this->Emails_Model->get_template('proposal', 'send_proposal');

								if ($template['status'] == 1) {

									if ($rel_type == 'customer') {

										$customer = $proposal['customercompany'] ? $proposal['customercompany'] : $proposal['namesurname'];
									} else {

										$customer = $proposal['leadname'];
									}

									$link = base_url('share/proposal/' . $proposal['token'] . '');

									$message_vars = array(

										'{proposal_to}' => $customer,

										'{nome}' => $customer,

										'{proposal_number}' => get_number('proposals', $proposal['id'], 'proposal', 'proposal'),

										'{proposal_link}' => $link,

										'{subject}' => request()->getPost('subject'),

										'{details}' => request()->getPost('content'),

										'{name}' => $settings['company'],

										'{email_signature}' => $settings['email'],

										'{open_till}' => _pdate($proposal['opentill']),

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$param = array(
										'from_name' => $template['from_name'],
										'email' => $proposal['toemail'],
										'subject' => $subject,
										'message' => $message,
										'created' => date_by_timezone(date('Y-m-d H:i:s')),
										'staff_id' => session()->get('usr_id'),
									);

									if ($template['attachment'] == '1') {

										$param['attachments'] = $path ? $path : NULL;
									}

									if ($proposal['toemail']) {

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}
								}
							}
						}



						//Order

						if ($value['process_relation_type'] == 'order') {

							$order = $this->Orders_Model->get_orders($value['process_relation'], $rel_type);

							$data['orders'] = $this->Orders_Model->get_orders($value['process_relation'], $rel_type);

							$path = '';

							if ($order['pdf_status'] == 1) {

								$this->Orders_Model->generate_pdf($order['id']);
							}

							$file = get_number('orders', $order['id'], 'order', 'order');

							$path = base_url('uploads/files/orders/' . $order['id'] . '/' . $file . '.pdf');

							if ($value['process_template_name'] == 'order_message') {

								$template = $this->Emails_Model->get_template('order', 'order_message');

								if ($template['status'] == 0) {

									$pro = $this->Orders_Model->get_pro_rel_type($value['process_relation']);

									$rel_type = $pro['relation_type'];

									if ($rel_type == 'customer') {

										switch ($order['type']) {

											case '0':

												$orderto = $order['customercompany'];

												break;

											case '1':

												$orderto = $order['namesurname'];

												break;
										}

										$ordertoemail = $order['toemail'];
									}

									if ($rel_type == 'lead') {

										$orderto = $order['leadname'];

										$ordertoemail = $order['toemail'];
									}

									$order_number = get_number('orders', $order['id'], 'order', 'order');

									$message_vars = array(

										'{nome}' => $orderto,

										'{order_to}' => $orderto,

										'{name}' => $settings['company'],

										'{email_signature}' => $settings['email'],

										'{order_number}' => $order_number,

										'{app_name}' => $settings['company'],

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$param = array(
										'from_name' => $template['from_name'],
										'email' => $ordertoemail,
										'subject' => $subject,
										'message' => $message,
										'created' => date_by_timezone(date('Y-m-d H:i:s')),
										'staff_id' => session()->get('usr_id'),
									);

									if ($template['attachment'] == 1) {

										$param['attachments'] = $path ? $path : NULL;
									}

									if ($ordertoemail) {

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}

									$path = null;

									unset($path);
								}
							}
						}



						//Deposit

						if ($value['process_relation_type'] == 'deposit') {

							$deposit = $this->Deposits_Model->get_deposits($value['process_relation'], '');

							$path = '';

							if ($deposit['pdf_status'] == 0) {

								$this->Deposits_Model->generate_pdf($deposit['id']);
							}

							$file = get_number('deposits', $deposit['id'], 'deposit', 'deposit');

							$path = base_url('uploads/files/deposits/' . $deposit['id'] . '/' . $file . '.pdf');

							if ($value['process_template_name'] == 'deposit_message') {

								$template = $this->Emails_Model->get_template('deposit', 'deposit_message');

								if ($template['status'] == '1') {

									if ($deposit['individual']) {

										$customer = $deposit['individual'];
									} else {

										$customer = $deposit['customer'];
									}

									if ($deposit['deposit_status'] == '1') {

										$depositstatus = lang2('paid');
									} else if ($deposit['deposit_status'] == '2') {

										$depositstatus = lang2('internal');
									} else if ($deposit['deposit_status'] == '0') {

										$depositstatus = lang2('unpaid');
									}

									$link = base_url('share/deposit/' . $deposit['token'] . '');

									$message_vars = array(

										'{deposit_number}' => get_number('deposits', $deposit['id'], 'deposit', 'deposit'),

										'{customer_name}' => $customer,

										'{deposit_date}' => $deposit['date'],

										'{deposit_amount}' => $deposit['amount'],

										'{deposit_status}' => $depositstatus,

										'{deposit_link}' => $link,

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$param = array(
										'from_name' => $template['from_name'],
										'email' => $deposit['customeremail'],
										'subject' => $subject,
										'message' => $message,
										'created' => date_by_timezone(date('Y-m-d H:i:s')),
										'staff_id' => session()->get('usr_id'),
									);

									if ($template['attachment'] == 1) {

										$param['attachments'] = $path ? $path : NULL;
									}

									if ($deposit['customeremail']) {

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}

									$path = null;

									unset($path);
								}
							}

							if ($value['process_template_name'] == 'recurring_deposit') {

								$template = $this->Emails_Model->get_template('deposit', 'recurring_deposit');

								if ($template['status'] == '1') {

									if ($deposit['individual']) {

										$customer = $deposit['individual'];
									} else {

										$customer = $deposit['customer'];
									}

									if ($deposit['deposit_status'] == '1') {

										$depositstatus = lang2('paid');
									} else if ($deposit['deposit_status'] == '2') {

										$depositstatus = lang2('internal');
									} else if ($deposit['deposit_status'] == '0') {

										$depositstatus = lang2('unpaid');
									}

									$link = base_url('share/deposit/' . $deposit['token'] . '');

									$message_vars = array(

										'{deposit_number}' => get_number('deposits', $deposit['id'], 'deposit', 'deposit'),

										'{customer_name}' => $customer,

										'{deposit_date}' => $deposit['date'],

										'{deposit_amount}' => $deposit['amount'],

										'{deposit_status}' => $depositstatus,

										'{deposit_link}' => $link,

										'{empresa}' => $settings['company'],

										'{email}' => $settings['email'],

									);

									$subject = strtr($template['subject'], $message_vars);

									$message = strtr($template['message'], $message_vars);

									$param = array(
										'from_name' => $template['from_name'],
										'email' => $deposit['customeremail'],
										'subject' => $subject,
										'message' => $message,
										'created' => date_by_timezone(date('Y-m-d H:i:s')),
										'staff_id' => session()->get('usr_id'),
									);

									if ($template['attachment'] == 1) {

										$param['attachments'] = $path ? $path : NULL;
									}

									if ($deposit['customeremail']) {

										$this->db->table('email_queue')->insert($param);

										$this->Settings_Model->remove_pending_process($value['process_id']);
									}

									$path = null;

									unset($path);
								}
							}
						}
					}
				}
			}
		}
	}



	function purchase_recurrings()
	{

		foreach ($this->Purchases_Model->get_all_recurring() as $key => $value) {

			if ($value['relation_type'] == 'purchase') {

				if (($value['end_date'] != 'Invalid date') && (strtotime(date("Y-m-d", strtotime($value['end_date']))) <= strtotime(date('Y-m-d')))) {

					continue;
				}

				$id = $value['relation'];

				// $purchase = $this->Purchases_Model->get_purchases($id);

				$po = $this->db->table('purchases')
					->where('id', $value['relation'])
					->get()
					->getResultArray();


				$po = end($po);

				if ($po['last_recurring'] != NULL && date('Y-m-d', strtotime($po['last_recurring'])) == date('Y-m-d')) {

					continue;
				}

				// Years

				if ($value['type'] == '3' && date('Y-m-d', strtotime($po['last_recurring'] . ' +' . $value['period'] . 'Years')) != date('Y-m-d')) {

					continue;
				}

				// Month

				if ($value['type'] == '2' && date('Y-m-d', strtotime($po['last_recurring'] . ' +' . $value['period'] . 'month')) != date('Y-m-d')) {

					continue;
				}

				// Week

				if ($value['type'] == '1' && date('Y-m-d', strtotime($po['last_recurring'] . ' +' . $value['period'] . 'week')) != date('Y-m-d')) {

					continue;
				}

				// Day

				if ($value['type'] == '0' && date('Y-m-d', strtotime($po['last_recurring'] . ' +' . $value['period'] . 'day')) != date('Y-m-d')) {

					continue;
				}

				$purchase = $this->Purchases_Model->get_purchases($id);

				$created = date('Y-m-d');

				$purchases = array(

					'token' => md5(uniqid()),

					'no' => $purchase['no'],

					'serie' => $purchase['serie'],

					'vendor_id' => $purchase['vendor_id'],

					'staff_id' => $purchase['staff_id'],

					'status_id' => 3,

					'created' => $created,

					'last_recurring' => $created,

					'duedate' => date('Y-m-d', strtotime($created . '+30 days')), // +30 day

					'duenote' => $purchase['duenote'],

					'sub_total' => $purchase['sub_total'],

					'total_discount' => $purchase['total_discount'],

					'total_tax' => $purchase['total_tax'],

					'total' => $purchase['total'],

					'recurring' => $value['id'],

				);

				$items = $this->db->table('items')
					->select('*')
					->where('relation_type', 'purchase')
					->where('relation', $id)
					->get()
					->getResultArray();

				$purchases_id = $this->Purchases_Model->recurring_purchases($purchases, $items);

				if ($purchases_id) {

					$this->Purchases_Model->update_recurring_date($id);

					$this->Settings_Model->create_process('pdf', $purchases_id, 'purchase', 'purchase_recurring');
				}
			}
		}

		$this->deposit_recurrings();
	}



	function deposit_recurrings()
	{

		foreach ($this->Deposits_Model->get_all_recurring() as $key => $value) {

			if ($value['relation_type'] == 'deposit') {

				if (($value['end_date'] != 'Invalid date') && (strtotime(date("Y-m-d", strtotime($value['end_date']))) <= strtotime(date('Y-m-d')))) {

					continue;
				}

				$id = $value['relation'];

				$invv = $this->db->table('deposits')
					->where('id', $value['relation'])
					->get()
					->getResultArray();

				$invv = end($invv);

				if ($invv['last_recurring'] != NULL && date('Y-m-d', strtotime($invv['last_recurring'])) == date('Y-m-d')) {

					continue;
				}

				// Years

				if ($value['type'] == '3' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'Years')) != date('Y-m-d')) {

					continue;
				}

				// Month

				if ($value['type'] == '2' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'month')) != date('Y-m-d')) {

					continue;
				}

				// Week

				if ($value['type'] == '1' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'week')) != date('Y-m-d')) {

					continue;
				}

				// Day

				if ($value['type'] == '0' && date('Y-m-d', strtotime($invv['last_recurring'] . ' +' . $value['period'] . 'day')) != date('Y-m-d')) {

					continue;
				}

				$deposit = $this->Deposits_Model->get_deposits($id);

				$deposit_data = array(

					'token' => md5(uniqid()),

					'relation_type' => 'deposit',

					'category_id' => $deposit['category_id'],

					'staff_id' => $deposit['depositstaff'],

					'customer_id' => $deposit['customer_id'],

					'account_id' => $deposit['account_id'],

					'title' => $deposit['title'],

					'description' => $deposit['desc'],

					'date' => $deposit['date'],

					'created' => date('Y-m-d'),

					'amount' => $deposit['amount'],

					'total_tax' => $deposit['total_tax'],

					'sub_total' => $deposit['sub_total'],

					'status' => $deposit['deposit_status'],

					'recurring' => $value['id'],

					'last_recurring' => date('Y-m-d')

				);

				$items = $this->db->table('items')
					->select('*')
					->where('relation_type', 'deposit')
					->where('relation', $id)
					->get()
					->getResultArray();

				$deposit_id = $this->Deposits_Model->recurring_deposits($deposit_data, $items);



				if ($deposit_id) {

					$this->Deposits_Model->update_recurring_date($id);

					$this->Settings_Model->create_process('pdf', $deposit_id, 'deposit', 'recurring_deposit');
				}
			}
		}
	}
}
