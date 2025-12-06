<?php

namespace App\Controllers;



if (!defined('BASEPATH')) exit('No direct script access allowed');
include_once APPPATH . '/third_party/script/script_update_config.php';
include_once APPPATH . '/third_party/script/script_update_functions.php';

class Settings extends BaseController
{
	function __construct()
	{
		parent::loadModels();
	}

	function index()
	{
		/*
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/Exception.php';
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/PHPMailer.php';
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/SMTP.php';
		$mail = new \PHPMailer\PHPMailer\PHPMailer(true);


		try {

			$mail->SMTPDebug = true;
            $mail->isSendMail();
            $mail->Host       = '177.153.58.78'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'cliente@ageup.pro'; 
            $mail->Password   = 'G32@3dsf!ff';           
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 456;

            $mail->setFrom('cliente@ageup.pro', 'AgeUp');
            $mail->addAddress('mairon.10@hotmail.com', 'Destinatário');
            $mail->isHTML(true);

            $mail->Subject = 'Teste de E-mail com PHPMailer4';
            $mail->Body    = '<h1>Olá!</h1><p>Este é um e-mail de teste enviado com PHPMailer no Laravel.</p>';
            $mail->AltBody = 'Este é um e-mail de teste enviado com PHPMailer no Laravel.';

            $mail->send();

		} catch (Exception $e) {
			return false;
		}

		exit;
*/

		$data['title'] = lang2('settings');
		if (session()->get('admin')) {
			return view('settings/index', $data);
		} else {
			return redirect()->to('panel');
		}
	}

	public function list_documents()
	{
		$docs = $this->db->table('documents')
			->orderBy('created_at', 'DESC')
			->where('id_company', session()->get('id_company'))
			->get()
			->getResultArray();



		return response()->setJSON($docs);
	}

	public function upload_documents()
	{
		// Proteções: verifique CSRF via filtros (CI4 normalmente cuida disso)
		$validationRules = [
			'name' => 'required|max_length[255]',
			'file' => [
				'uploaded[file]',
				'max_size[file,10240]', // 10MB (ajuste conforme necessário)
				'ext_in[file,pdf,doc,docx,xls,xlsx,png,jpg,jpeg,txt]',
			],
		];

		$file = request()->getFile('file');

		if (!$file->isValid()) {
			return response()->setStatusCode(400)->setJSON(['error' => 'Arquivo inválido']);
		}

		// Cria pasta de uploads se não existir (WRITEPATH . 'uploads' por exemplo)
		$uploadPath = FCPATH . 'uploads/documents';
		if (!is_dir($uploadPath)) {
			mkdir($uploadPath, 0755, true);
		}

		// Gera nome único seguro
		$newName = $file->getRandomName(); // já preserva extensão

		// Move o arquivo
		try {
			$file->move($uploadPath, $newName);
		} catch (\Exception $e) {
			return response()->setStatusCode(500)->setJSON(['error' => 'Falha ao mover arquivo: ' . $e->getMessage()]);
		}

		// Grava no DB
		$data = [
			'name' => request()->getPost('name'),
			'filename' => $newName,
			'original_name' => $file->getClientName(),
			'mime' => $file->getClientMimeType(),
			'size' => $file->getSize(),
			'id_company' =>  session()->get('id_company'),
		];

		$id = $this->db->table('documents')->insert($data);
		if (!$id) {
			// tenta remover arquivo salvo se DB falhar
			@unlink($uploadPath . DIRECTORY_SEPARATOR . $newName);
			return response()->setStatusCode(500)->setJSON(['error' => 'Falha ao salvar registro no banco']);
		}

		$data['id'] = $id;
		return response()->setJSON(['success' => true, 'document' => $data]);
	}

	public function delete_documents($id = null)
	{
		$doc = $this->db->table('documents')->find($id);
		if (!$doc) {
			return response()->setStatusCode(404)->setJSON(['error' => 'Documento não encontrado']);
		}

		$uploadPath = WRITEPATH . 'uploads/documents' . DIRECTORY_SEPARATOR . $doc['filename'];
		if (is_file($uploadPath)) {
			@unlink($uploadPath);
		}
		$this->db->table('documents')->delete($id);
		return response()->setJSON(['success' => true]);
	}

	public function get_funis()
	{

		$result = $this->db->table('settings_funis')
			->join('leadsstatus', 'leadsstatus.id = settings_funis.id_status', 'left')
			->where('id_company', session()->get('id_company'))
			->get()
			->getResultArray();

		$funis = [];
		foreach ($result as $row) {
			$funis[$row['id_list']]['id'] = $row['id_status'];
			$funis[$row['id_list']]['name'] = $row['name'];
		}

		return response()->setJSON($funis);
	}


	public function save_funil()
	{
		$data = request()->getJSON(true);

		if (!isset($data['id_list']) || !isset($data['id_status'])) {
			return response()->setJSON([
				'status' => 'error',
				'message' => 'Dados inválidos'
			])->setStatusCode(400);
		}

		$idCompany = session()->get('id_company');

		// atualiza ou insere
		$existing = $this->db->table('settings_funis')->where('id_company', $idCompany)
			->where('id_list', $data['id_list'])
			->get()
			->getRowArray();

		if ($existing) {
			$this->db->table('settings_funis')->where('id_company', $idCompany)
				->where('id_list', $data['id_list'])
				->update(['id_status' => $data['id_status']]);
		} else {
			$this->db->table('settings_funis')->insert([
				'id_company' => $idCompany,
				'id_list'    => $data['id_list'],
				'id_status'  => $data['id_status'],
				'staff_id' => session()->usr_id,
				'created_at' => date('Y-m-d H:i:s')
			]);
		}

		return response()->setJSON([
			'status' => 'success',
			'message' => 'Status do funil salvo com sucesso'
		]);
	}

	function updateImage()
	{
		$file = request()->getFile('file');

		if (!$file->isValid()) {
			return $this->fail('Arquivo inválido', 400);
		}

		$newName = $file->getRandomName();
		$path = FCPATH . 'uploads/';
		if (!is_dir($path)) {
			mkdir($path, 0755, true);
		}

		$file->move($path, $newName);
		$url = base_url('uploads/' . $newName);


		return response()->setJSON(['location' => $url]);
	}

	function updateEmail($settingname)
	{

		$params = array(
			'smtphost' => request()->getPost('smtphost'),
			'smtpport' => request()->getPost('smtpport'),
			'emailcharset' => request()->getPost('emailcharset'),
			'smtpusername' => request()->getPost('smtpusername'),
			'sendermail' => request()->getPost('sendermail'),
			'sender_name' => request()->getPost('sender_name'),
			'email_encryption' => request()->getPost('email_encryption'),
			'email_type' => request()->getPost('email_type'),
		);

		if (request()->getPost('smtppassoword') != null && request()->getPost('smtppassoword') != '********' && !empty(request()->getPost('smtppassoword'))) {
			$params['smtppassoword'] = request()->getPost('smtppassoword');
		}

		if (
			$this->db->table('settings')
			->where('id_company', request()->getPost('id_company'))
			->get()
			->getRowArray() != null
		) {

			$this->Settings_Model->update_settings($settingname, $params, request()->getPost('id_company'));
		} else {
			$params['id_company'] =  request()->getPost('id_company');
			$this->db->table('settings')->insert($params);
		}

		$datas['success'] = true;
		$datas['message'] = lang2('settingsupdated');
		return response()->setJSON($datas);
	}

	function get_assinatura()
	{
		$datas['result'] = $this->db->table('companies')
			->select('plans.*, vencimento, plano')
			->join('plans', 'plans.id_plan = companies.plano', 'left')
			->where('id_company', session()->get('id_company'))
			->get()
			->getRowArray();

		$datas['result']['vencimento'] = $datas['result']['vencimento'] != '' ? date('d/m/Y', strtotime($datas['result']['vencimento'])) : 'Indefinido';

		$datas['planos'] = $this->db->table('plans')
			->select("plans.*, IF(plans.valor_anual IS NOT NULL and plans.valor_anual > 0 and plans.valor_anual != '', '365', '30') as tipo")
			->where(['is_ativo' => '1', 'tp_plano' => 'personal'])
			->get()
			->getResultArray();

		foreach ($datas['planos'] as $index => $row) {
			$permissionsPlan = $this->Privileges_Model->get_all_permissionsByPlan($row['id_plan']);
			foreach ($permissionsPlan as $indexP => $permissao) {
				$permissionsPlan[$indexP]['key'] = lang2($permissao['key']);
			}
			$datas['planos'][$index]['privilegios'] = $permissionsPlan;
		}

		$datas['success'] = true;
		return response()->setJSON($datas);
	}


	function save_settingsIa()
	{
		$builder = $this->db->table('settings_ia');

		$exists = $builder->where('id_company', session()->id_company)
			->where('staff_id', session()->usr_id)
			->get()->getRow();

		$data = [
			'connected' => request()->getPost('connected'),
			'number'    => request()->getPost('number'),
			'usar_ia'   => request()->getPost('usar_ia'),
			'link'      => request()->getPost('link'),
			'key'       => request()->getPost('key'),
			'status_id' => request()->getPost('status_id'),
			'funil_list' => request()->getPost('funil_list'),
		];

		if ($exists) {
			$builder->where('id_company', session()->id_company)
				->where('staff_id', session()->usr_id)
				->update($data);
		} else {
			$data['id_company'] = session()->id_company;
			$data['staff_id'] = session()->usr_id;
			$builder->insert($data);
		}

		return response()->setJSON(['message' => 'Sucesso ao salvar!']);
	}

	function get_settingsIa(bool $viewNumbers = true)
	{
		$numberAtual = $this->db->table('settings_ia')
			->where('id_company', session()->id_company)
			->where('staff_id', session()->usr_id)
			->get()
			->getRowArray();

		$data['number_atual'] = $numberAtual;

		if ($viewNumbers && $this->Privileges_Model->check_privilege('whatsapp', 'all')) {
			$data['numbers'] = $this->db->table('settings_ia')
				->where('id_company', session()->id_company)
				->get()
				->getResultArray();
		}

		if (!$viewNumbers) {
			$data = $numberAtual ?? [];
		}

		return response()->setJSON($data ?: []);
	}

	function get_settingsIa2()
	{
		return $this->get_settingsIa(false);
	}

	function get_planos()
	{
		$datas['planos'] = $this->db->table('plans')
			->select('plans.*')
			->where('is_ativo', '1')
			->get()
			->getResultArray();
		$datas['success'] = true;
		return response()->setJSON($datas);
	}

	function updateEmailSetting($print = true, $staff_id = null)
	{

		if ($staff_id == null) {
			$staff_id = request()->getPost('staff_id');
			//session()->usr_id
		}

		$params = array(
			'imapHost' => request()->getPost('imapHost'),
			'imapPort' => request()->getPost('imapPort'),
			'imapUsername' => request()->getPost('imapUsername'),
			'imap_sendermail' => request()->getPost('imap_sendermail'),
			'imap_email_type' => request()->getPost('imap_email_type'),

			'smtphost' => request()->getPost('smtphost'),
			'smtpport' => request()->getPost('smtpport'),
			'emailcharset' => request()->getPost('emailcharset'),
			'smtpusername' => request()->getPost('smtpusername'),
			'sendermail' => request()->getPost('sendermail'),
			'sender_name' => request()->getPost('sender_name'),
			'email_encryption' => request()->getPost('email_encryption'),
			'email_type' => request()->getPost('email_type'),
		);

		if (request()->getPost('imapPassoword') && request()->getPost('imapPassoword') != '********') {
			$params['imapPassoword'] = request()->getPost('imapPassoword');
		}
		if (request()->getPost('smtppassoword') && request()->getPost('smtppassoword') != '********' && !empty(request()->getPost('smtppassoword'))) {

			$params['smtppassoword'] = request()->getPost('smtppassoword');
		}

		if ($this->db->table('settings_email')->where('staff_id', $staff_id)->countAllResults() > 0) {
			$this->db->table('settings_email')->where('staff_id', $staff_id)->update($params);
		} else {
			$params['staff_id'] = $staff_id;
			$this->db->table('settings_email')->insert($params);
		}


		if ($print) {
			$datas['success'] = true;
			$datas['message'] = lang2('settingsupdated');
			return response()->setJSON($datas);
		}
	}



	function get_motivos()
	{
		$data['motivos_pausa'] = $this->db->table('settings_motivos_pausa')
			->where('id_company', session()->id_company)
			->get()
			->getResultArray();

		return response()->setJSON($data);
	}

	function update($settingname)
	{

		if ($this->Privileges_Model->check_privilege('settings', 'all')) {

			if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

				if (isset($settingname)) {

					if (isset($_POST) && count($_POST) > 0) {

						$is_demo = $this->Settings_Model->is_demo();

						if (!$is_demo) {

							$config['upload_path'] = './uploads/ciuis_settings/';

							$config['allowed_types'] = 'gif|jpg|png|jpeg';

							switch ($_POST['pushState']) {

								case 'true':

									$PushState = 0;

									break;

								case 'false':

									$PushState = 0;

									break;
							}

							switch ($_POST['voicenotification']) {

								case 'true':

									$VoiceNotification = 1;

									break;

								case 'false':

									$VoiceNotification = 0;

									break;
							}

							$is_mysql = request()->getPost('is_mysql');

							if ($is_mysql == '1' || $is_mysql == 'true') {

								$is_mysql = '1';
							} else {

								$is_mysql = '0';
							}

							$params = array(
								'crm_name' => request()->getPost('crm_name'),
								'company' => request()->getPost('company'),
								'email' => request()->getPost('email'),
								'address' => request()->getPost('address'),
								'city' => request()->getPost('city'),
								'town' => request()->getPost('town'),
								'state_id' => request()->getPost('state_id'),
								'country_id' => request()->getPost('country_id'),
								'zipcode' => request()->getPost('zipcode'),

								'resumo' => request()->getPost('resumo'),
								'key_gpt' => request()->getPost('key_gpt'),
								'assistent_gpt' => request()->getPost('assistent_gpt'),

								'phone' => request()->getPost('phone'),
								'fax' => request()->getPost('fax'),
								'vatnumber' => request()->getPost('vatnumber'),
								'taxoffice' => request()->getPost('taxoffice'),
								'currencyid' => request()->getPost('currencyid'),
								'termtitle' => request()->getPost('termtitle'),
								'termdescription' => request()->getPost('termdescription'),
								'dateformat' => request()->getPost('dateformat'),
								'languageid' => request()->getPost('languageid'),
								'default_timezone' => request()->getPost('default_timezone'),

								'ramo_atividade' => request()->getPost('ramo_atividade'),
								'descricao_empresa' => request()->getPost('descricao_empresa'),
								'descricao_operacao' => request()->getPost('descricao_operacao'),
								'descricao_equipe' => request()->getPost('descricao_equipe'),
								/*
								'smtphost' => request()->getPost('smtphost'),
								'smtpport' => request()->getPost('smtpport'),
								'emailcharset' => request()->getPost('emailcharset'),
								'smtpusername' => request()->getPost('smtpusername'),
								'sendermail' => request()->getPost('sendermail'),
								'sender_name' => request()->getPost('sender_name'),
								'email_encryption' => request()->getPost('email_encryption'),
								*/
								'accepted_files_formats' => request()->getPost('accepted_files_formats'),
								'allowed_ip_adresses' => request()->getPost('allowed_ip_adresses'),
								'pushState' => $PushState,
								'voicenotification' => $VoiceNotification,
								'thousand_separator' => request()->getPost('thousand_separator'),
								'decimal_separator' => request()->getPost('decimal_separator'),
								'currency_position' => request()->getPost('currency_position'),
								'currency_display' => request()->getPost('currency_display'),
								//'email_type' => request()->getPost('email_type'),
								'is_mysql' => $is_mysql,
							);

							//if (request()->getPost('smtppassoword') != '********') {
							//	$params['smtppassoword'] = request()->getPost('smtppassoword');
							//}

							if (
								$this->db->table('settings')
								->where('id_company', session()->id_company)
								->get()
								->getRowArray() != null
							) {

								$this->Settings_Model->update_settings($settingname, $params);
							} else {
								$params['id_company'] =  session()->id_company;
								$this->db->table('settings')->insert($params);
							}



							$this->db->table('settings_motivos_pausa')->delete(['id_company' => session()->id_company]);
							if (request()->getPost('motivos_pausa')) {


								foreach (request()->getPost('motivos_pausa') as $index => $row) {
									$paramsSetting2['name'] = $row['name'];
									$paramsSetting2['id_company'] = session()->id_company;
									$this->db->table('settings_motivos_pausa')->insert($paramsSetting2);
								}
							}

							$this->Settings_Model->update_appconfig();
							$datas['success'] = true;
							$datas['message'] = lang2('settingsupdated');

							$this->updateEmailSetting(false, session()->usr_id);
							return response()->setJSON($datas);
						} else {

							$datas['success'] = false;

							$datas['message'] = lang2('demo_error');

							return response()->setJSON($datas);
						}
					}
				}
			} else {

				$datas['success'] = false;

				$datas['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($datas);
			}
		} else {

			$datas['success'] = false;

			$datas['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($datas);
		}
	}



	function db_backup()
	{

		if ($this->Privileges_Model->check_privilege('settings', 'create')) {

			$version = $this->Settings_Model->get_version_detail();

			$this->load->helper('file');

			$this->load->dbutil();

			$date = date('Y-m-d_H-i-s');

			$prefs = array('format' => 'zip', 'ignore' => array('db_backup', 'versions', 'sessions'), 'filename' => 'DB-backup_' . $date);



			$backup = $this->dbutil->backup($prefs);

			if (!write_file('./uploads/backup/DB-backup_' . $date . '.zip', $backup)) {

				$data['success'] = false;

				$data['message'] = lang2('errormessage');
			} else {

				$persentVersion = $version['versions_name'];

				write_file('./uploads/backup/DB-backup_' . $date . '.txt', $persentVersion);

				$zip = new ZipArchive();

				$this->zip->read_file('./uploads/backup/DB-backup_' . $date . '.txt');

				$this->zip->archive('./uploads/backup/DB-backup_' . $date . '.zip');

				unlink('./uploads/backup/DB-backup_' . $date . '.txt');

				$data['success'] = true;

				$data['message'] = lang2('backup') . ' ' . lang2('createmessage');
			}

			$activity = array(

				'staff_id' => session()->get('usr_id'),

				'version' => $version['versions_name'],

				'created' => date('Y-m-d H:i:s'),

				'filename' => $prefs['filename']

			);

			$this->Settings_Model->db_backup($activity);

			$staffname = session()->staffname;

			$loggedinuserid = session()->usr_id;

			$this->db->table('logs')->insert(array(

				'date' => date_by_timezone(date('Y-m-d H:i:s')),

				'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('created') . ' ' . lang2('db') . ' ' . lang2('backup') . ''),

				'staff_id' => $loggedinuserid

			));

			return response()->setJSON($data);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function download_backup($file)
	{

		$is_demo = $this->Settings_Model->is_demo();

		if (!$is_demo) {

			if (is_file('./uploads/backup/' . $file)) {

				$this->load->helper('file');

				$this->load->helper('download');

				$data = file_get_contents('./uploads/backup/' . $file);

				force_download($file, $data);
			} else {

				session()->setFlashdata('ntf4', lang2('filenotexist'));

				return redirect()->to('settings/index');
			}
		} else {

			session()->setFlashdata('ntf4', "Database cannot be downloaded in Demo Version");

			return redirect()->to('settings/index');
		}
	}



	function get_backup()
	{

		$settings = $this->Settings_Model->get_backup();

		foreach ($settings as $backup) {

			$data_setting[] = array(

				'id' => $backup['id'],

				'filename' => $backup['filename'],

				'version' => $backup['version'],

				'created' => date(get_dateTimeFormat(), strtotime($backup['created']))

			);
		}

		return response()->setJSON($data_setting);
	}



	function remove_backup($id)
	{

		if ($this->Privileges_Model->check_privilege('settings', 'delete')) {

			if (isset($id)) {

				$backup = $this->Settings_Model->get_db_backup($id);

				$file = $backup['filename'] . '.zip';

				if ($backup['id'] == $id) {

					$response = $this->db->table('db_backup')->delete(array('id' => $id));

					if ($response) {

						if (file_exists('./uploads/backup/' . $file)) {

							unlink('./uploads/backup/' . $file);
						}

						$staffname = session()->staffname;

						$loggedinuserid = session()->usr_id;

						$this->db->table('logs')->insert(array(

							'date' => date_by_timezone(date('Y-m-d H:i:s')),

							'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('deleted') . ' ' . lang2('db') . ' ' . lang2('backup') . ''),

							'staff_id' => $loggedinuserid

						));

						$data['success'] = true;

						$data['message'] = lang2('backup') . ' ' . lang2('deletemessage');

						return response()->setJSON($data);
					} else {

						$data['success'] = false;

						$data['message'] = lang2('errormessage');

						return response()->setJSON($data);
					}
				} else {

					$data['success'] = false;

					$data['message'] = lang2('backup_remove_error');

					return response()->setJSON($data);
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('errormessage');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function restore_database()
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

			if (isset($_POST)) {

				$is_demo = $this->Settings_Model->is_demo();

				if (!$is_demo) {

					ini_set('max_execution_time', 0);

					ini_set('memory_limit', '2048M');

					$version = $this->Settings_Model->get_version_detail();
					$this->load->helper('file');
					$this->load->helper('unzip');
					$this->load->database();



					$config['upload_path'] = './uploads/temp/';
					$config['allowed_types'] = '*';
					$config['max_size'] = '9000';
					$config['overwrite'] = TRUE;



					$this->load->library('upload', $config);
					$this->upload->initialize($config);


					if (!$this->upload->do_upload('upload_file')) {
						$error = $this->upload->display_errors('', ' ');
						$type = 'error';
						$message = $error;
						session()->setFlashdata('ntf4', $message);
						return redirect()->to('settings/index');
					} else {

						$data = array('upload_data' => $this->upload->data());

						$backup = "./uploads/temp/" . $data['upload_data']['file_name'];
					}

					if (!unzip($backup, "./uploads/temp/", true, true)) {

						$type = 'error';

						$message = lang2('backup_restore_error');
					} else {

						$backup = str_replace('.zip', '', $backup);

						$userVersion = file_get_contents($backup . ".txt");

						if ($userVersion == $version['versions_name']) {

							$this->load->dbforge();

							$file_content = file_get_contents($backup . ".sql");

							$this->db->query('USE ' . $this->db->database . ';');

							foreach (explode(";\n", $file_content) as $sql) {

								$sql = trim($sql);

								if ($sql) {

									$this->db->query($sql);
								}
							}

							$staffname = session()->staffname;

							$loggedinuserid = session()->usr_id;

							$this->db->table('logs')->insert(array(

								'date' => date_by_timezone(date('Y-m-d H:i:s')),

								'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('restored') . ' ' . lang2('db') . ' ' . lang2('backup') . ''),

								'staff_id' => $loggedinuserid

							));

							session()->setFlashdata('ntf1', lang2('restoresuccess'));

							unlink($backup . ".sql");

							unlink($backup . ".txt");

							unlink($backup . ".zip");

							return redirect()->to('settings/index');
						} else {

							unlink($backup . ".sql");

							unlink($backup . ".txt");

							unlink($backup . ".zip");

							session()->setFlashdata('ntf4', lang2('version_missmatch_error'));

							return redirect()->to('settings/index');
						}
					}
				} else {

					session()->setFlashdata('ntf4', lang2('demo_error'));

					return redirect()->to('settings/index');
				}
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to('settings/index');
		}
	}



	function restore_backup($id)
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

			if ($id) {

				$is_demo = $this->Settings_Model->is_demo();

				if (!$is_demo) {

					ini_set('max_execution_time', 0);

					ini_set('memory_limit', '2048M');

					$backupDetails = $this->Settings_Model->get_db_backup($id);

					$version = $this->Settings_Model->get_version_detail();

					if ($version['versions_name'] == $backupDetails['version']) {

						$tables = $this->db->list_tables();

						foreach ($tables as $tab) {

							if ($tab == "db_backup" || $tab == "versions" || $tab == "sessions") {

								continue;
							}

							$this->load->dbforge();

							$this->dbforge->drop_table($tab, TRUE);
						}

						$backup =  "./uploads/backup/" . $backupDetails['filename'] . '.zip';

						if (is_file($backup)) {

							$this->load->helper('file');

							$this->load->helper('unzip');

							$this->load->database();

							if (!unzip($backup, "./uploads/backup/", true, true)) {

								$data['success'] = false;

								$data['message'] = lang2('backup_restore_error');

								return response()->setJSON($data);
							} else {

								$this->load->dbforge();

								$backup = str_replace('.zip', '', $backup);

								$file_content = file_get_contents($backup . ".sql");

								$this->db->query('USE ' . $this->db->database . ';');

								foreach (explode(";\n", $file_content) as $sql) {

									$sql = trim($sql);

									if ($sql) {

										$this->db->query($sql);
									}
								}

								$staffname = session()->staffname;

								$loggedinuserid = session()->usr_id;

								$this->db->table('logs')->insert(array(

									'date' => date_by_timezone(date('Y-m-d H:i:s')),

									'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('restored') . ' ' . lang2('db') . ' ' . lang2('backup') . ''),

									'staff_id' => $loggedinuserid

								));

								unlink($backup . ".sql");

								unlink($backup . ".txt");

								session()->setFlashdata('ntf1', lang2('restoresuccess'));

								$data['success'] = true;

								$data['message'] = lang2('restoresuccess');

								return response()->setJSON($data);
							}
						} else {

							$data['success'] = false;

							$data['message'] = lang2('backup_restore_error');

							return response()->setJSON($data);
						}
					} else {

						$data['success'] = false;

						$data['message'] = lang2('version_missmatch_error');

						return response()->setJSON($data);
					}
				} else {

					$data['success'] = false;

					$data['message'] = lang2('demo_error');

					return response()->setJSON($data);
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('errormessage');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function replace_files()
	{

		if (isset($_POST)) {

			$this->load->helper('file');

			$this->load->helper('unzip');

			$config['upload_path'] = './';

			$config['allowed_types'] = 'zip';

			$config['max_size'] = '9000';

			$config['overwrite'] = TRUE;



			$this->load->library('upload', $config);

			$this->upload->initialize($config);



			if (!$this->upload->do_upload('upload_file')) {

				$error = $this->upload->display_errors('', ' ');

				$type = 'error';

				$message = $error;

				session()->setFlashdata('ntf4', $message);

				return redirect()->to('settings/index');
			} else {

				$data = array('upload_data' => $this->upload->data());

				$backup = './' . $data['upload_data']['file_name'];

				if (!unzip($backup, './', true, true)) {

					$type = 'error';

					$message = lang2('backup_restore_error');
				} else {

					unlink($backup);

					session()->setFlashdata('ntf1', lang2('upload_success'));

					$this->output->delete_cache();

					return redirect()->to('settings');
				}
			}
		}
	}



	function version_details()
	{

		$settings = $this->Settings_Model->get_version_detail();

		return response()->setJSON($settings);
	}



	function version_detail()
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

			$version_notifications_array = ausGetAllVersions();

			$settings = $this->Settings_Model->get_version_detail();

			if ($version_notifications_array['notification_case'] == "notification_operation_ok") {

				$version = $version_notifications_array['notification_data']['product_versions'];

				$list_array = array();

				$list_array_log = array();

				$flag = false;

				foreach (array_reverse($version) as $key => $value) {

					if ($flag) {

						if ($settings['versions_name'] < $value['version_number'] && $value['version_status'] == 1) {

							$value['version_number'];

							$list_array[] = array('version_number' => $value['version_number']);
						}

						break;
					} else {



						if ($settings['versions_name'] < $value['version_number'] && $value['version_status'] == 1) {

							$value['version_number'];

							$list_array[] = array('version_number' => $value['version_number']);

							$flag = true;
						}



						if ($settings['versions_name'] == $value['version_number']) {

							$flag = true;
						}
					}
				}

				$msg = "";

				$updated = "available";

				if (empty($list_array)) {

					$list_array[0] = array('version_number' => $settings['versions_name']);

					$msg = 'Already updated';

					$updated = "";
				}
			} else {

				$msg = 'alreadyupdated';

				$updated = "";

				$list_array[0] = array('version_number' => $settings['versions_name']);

				$list_array_log[0] = '';
			}

			$download_version = ausGetVersion($list_array[0]['version_number']);

			$version_changelog = $download_version['notification_data'];

			if (!$version_changelog) {

				$version_changelog = NULL;
			} else {

				$version_changelog = $download_version['notification_data']['version_changelog'];
			}

			if ($updated == '') {
				$this->db->table('versions')->where('id', '1')->update([
					'is_update_available' => 0,
					'last_checked' => date('Y-m-d')
				]);
			}


			$version_details  =	array(

				'settings' => $settings,

				'version' => $list_array[0],

				'msg' => $msg,

				'updated' => $updated,

				'version_changelog' => $version_changelog

			);

			return response()->setJSON($version_details);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to('settings/index');
		}
	}



	function download_update()
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

			// download the update

			$data['title'] = lang2('settings');

			$data['success'] = false;

			$data['message'] = '';

			// Only admin can download the update

			if (session()->get('admin')) {

				ini_set('max_execution_time', 0);

				ini_set('memory_limit', '2048M');

				$download_notifications_array = ausDownloadFile('version_upgrade_file', $_POST['version_number']);

				if ($download_notifications_array['notification_case'] == "notification_operation_ok") {

					$download_notifications_array1 = ausDownloadFile('version_upgrade_query', $_POST['version_number']);

					if ($download_notifications_array1['notification_case'] == "notification_operation_ok") {

						$data['success'] = true;

						$data['message'] = lang2('update_downloaded');
					} else {

						$data['success'] = false;

						$data['message'] = lang2('programfiles');
					}
				} else {

					$data['success'] = false;

					$data['message'] = lang2('programfiles');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('programfiles');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function install_update()
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

			if (isset($_POST) && request()->getPost('version_number')) {

				$version = $this->Settings_Model->get_version_detail();

				$this->load->helper('file');

				$this->load->helper('unzip');



				$db_file_name = './Database_package.zip'; // database package file name

				$app_files = './Update_package.zip'; // app files package

				$data['error'] = '';

				$data['message'] = '';

				$data['warning'] = lang2('warning');

				if (!unzip($db_file_name, "./", true, true)) { // error if db unzip fails

					$data['success'] = false;

					$data['message'] = lang2('install_update_error');
				} else {

					$db_file = './SQLupdate/update.sql';

					$file_content = file_get_contents($db_file); // get db content from file

					$sqls = explode(';', $file_content);

					array_pop($sqls);

					foreach ($sqls as $sql) {

						$sql = trim($sql);

						if ($sql) {

							$this->db->db_debug = FALSE;

							$this->db->query($sql . ';');

							$error = $this->db->error();

							if ($error) {

								if (ini_get('allow_url_fopen')) {

									$error_file = APPPATH . 'Views/app-errors/errors-log.php';

									$fp = fopen($error_file, 'a');

									fwrite($fp, "<br>\nError[Type='OTA Update'][" . date("Y-m-d H:i:s") . "]: " . $error['message']);

									fclose($fp);

									$data['error'] = $error;
								}

								continue;
							}
						}
					}

					$data['success'] = true;

					$data['message'] = lang2('updated_installed');

					if (!unzip($app_files, "./", true, true)) {

						$data['success'] = false;

						$data['message'] = lang2('install_update_error');
					}

					if (is_file($app_files)) {

						unlink($app_files);
					}

					if (is_file($db_file_name)) {

						copy($db_file, "SQLupdate/update." . date("Y-m-d_H-i-s") . ".sql");

						unlink($db_file);

						unlink($db_file_name);
					}

					$version = $this->Settings_Model->get_version_detail();


					$this->db->table('versions')->where('id', '1')->update(array('versions_name' => $_POST['version_number'], 'last_version' => $version['versions_name'], 'last_updated' => date('Y-m-d'), 'is_update_available' => 0, 'last_checked' => date('Y-m-d')));

					session()->setFlashdata('ntf1', lang2('softwareversionupdate'));

					$staffname = session()->staffname;

					$loggedinuserid = session()->usr_id;

					$this->db->table('logs')->insert(array(

						'date' => date_by_timezone(date('Y-m-d H:i:s')),

						'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('updatednewversion') . ''),

						'staff_id' => $loggedinuserid

					));

					$this->output->delete_cache();
				}

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function run_sql_file()
	{

		if (isset($_POST)) {

			$is_demo = $this->Settings_Model->is_demo();

			//if (!$is_demo) {

			if (!is_dir('tmp')) {

				mkdir('./tmp', 0777, true);
			}

			$config['upload_path'] = './tmp';

			$config['allowed_types'] = '*';

			$config['max_size'] = '9000';

			$config['file'] = $_FILES["file"]['name'];

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('file')) {

				$data['success'] = false;

				$data['message'] = $this->upload->display_errors();
			} else {

				$image_data = $this->upload->data();

				if (is_file('./tmp/' . $image_data['file_name'])) {

					$db_file = './tmp/' . $image_data['file_name'];

					$file_content = file_get_contents($db_file); // get db content from file

					$sqls = explode(';', $file_content);

					array_pop($sqls);

					foreach ($sqls as $sql) {

						$sql = trim($sql);

						if ($sql) {

							$this->db->db_debug = FALSE;

							$this->db->query($sql . ';');

							$error = $this->db->error();

							if ($error) {

								if (ini_get('allow_url_fopen')) {

									$error_file = APPPATH . 'Views/app-errors/errors-log.php';

									$fp = fopen($error_file, 'a');

									fwrite($fp, "<br>\nError[Type='Manual SQL File Upload'][" . date("Y-m-d H:i:s") . "]: " . $error['message']);

									fclose($fp);

									$data['error'] = $error;
								}

								continue;
							}
						}
					}

					unlink($db_file);

					$staffname = session()->staffname;

					$loggedinuserid = session()->usr_id;

					$this->db->table('logs')->insert(array(

						'date' => date_by_timezone(date('Y-m-d H:i:s')),

						'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('executed') . ' ' . lang2('manual_sql_query') . lang2('file') . ''),

						'staff_id' => $loggedinuserid

					));

					$data['success'] = true;

					$data['message'] = lang2('query_success');
				} else {

					$data['success'] = false;

					$data['message'] = 'File didnot uploaded, please try again';
				}
			}

			return response()->setJSON($data);

			// } else {

			// 	$data['success'] = false;

			//       	$data['message'] = lang2('demo_error');

			//       	return response()->setJSON($data);

			// }

		}
	}



	function sendTestEmail()
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {
			$email = request()->getPost('email');
			if (!empty($email) || $email != '') {
				$settings = $this->Settings_Model->get_settings_ciuis();
				//$email = $this->security->xss_clean($emailInput);

				$body = '<p>This is test SMTP email.</p> <p>If you are receiving this email that means your smtp setting are correct</p>';
				$data = $this->Emails_Model->send_email($email, $email, 'SMTP setup testing', $body);
				$param = array(
					'from_name' => $settings['sender_name'],
					'email' => $email,
					'subject' => 'SMTP setup testing',
					'message' => $body,
					'created' => date("Y.m.d H:i:s"),
					'status' => 0
				);

				if ($data) {
					$return['success'] = true;
					$return['message'] = lang2('mail_successfully_sent');
					if ($email) {
						$this->db->table('email_queue')->insert($param);
					}
					return response()->setJSON($return);
				} else {
					$return['success'] = false;
					$return['message'] = lang2('wrong_email_settings_msg');
					return response()->setJSON($return);
				}
			} else {
				$return['success'] = false;
				$return['message'] = lang2('invalidmessage') . ' ' . lang2('email');
				return response()->setJSON($return);
			}
		} else {
			$return['success'] = false;
			$return['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($return);
		}
	}



	function save_config()
	{
		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {
			$is_demo = $this->Settings_Model->is_demo();

			if (!$is_demo) {
				$branding = load_config();

				// Upload e atualização do logo do aplicativo
				if ($file = request()->getFile('applogo')) {
					if ($file->isValid() && !$file->hasMoved()) {
						$new_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$upload_path = './uploads/ciuis_settings/';
						$file->move($upload_path, $new_name);

						if (is_file($upload_path . $branding['app_logo'])) {
							unlink($upload_path . $branding['app_logo']);
						}

						$this->db->table('settings')->update(['settingname' => 'ciuis', 'app_logo' => $new_name]);
						$this->db->table('branding')->where('name', 'app_logo')->update(['value' => $new_name]);

						$this->log_activity('updated', 'applogo');
					}
				}

				// Upload e atualização do logo de navegação
				if ($file = request()->getFile('navlogo')) {
					if ($file->isValid() && !$file->hasMoved()) {
						$new_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$upload_path = './uploads/ciuis_settings/';
						$file->move($upload_path, $new_name);

						if (is_file($upload_path . $branding['nav_logo'])) {
							unlink($upload_path . $branding['nav_logo']);
						}

						$this->db->table('settings')->update(['settingname' => 'ciuis', 'logo' => $new_name]);
						$this->db->table('branding')->where('name', 'nav_logo')->update(['value' => $new_name]);

						$this->log_activity('updated', 'navlogo');
					}
				}

				// Upload e atualização do favicon
				if ($file = request()->getFile('favicon')) {
					if ($file->isValid() && !$file->hasMoved()) {
						$new_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$upload_path = './assets/img/images/';
						$file->move($upload_path, $new_name);

						if (is_file($upload_path . $branding['favicon_icon'])) {
							unlink($upload_path . $branding['favicon_icon']);
						}

						$this->db->table('branding')->where('name', 'favicon_icon')->update(['value' => $new_name]);
					}
				}

				// Upload e atualização da imagem de fundo 1
				if ($file = request()->getFile('back_lead1_img')) {
					if ($file->isValid() && !$file->hasMoved()) {
						$new_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$upload_path = './assets/img/images/';
						$file->move($upload_path, $new_name);

						if (is_file($upload_path . $branding['back_lead1'])) {
							unlink($upload_path . $branding['back_lead1']);
						}

						$this->db->table('branding')->where('name', 'back_lead1')->update(['value' => $new_name]);
					}
				}

				// Upload e atualização da imagem de login do admin
				if ($file = request()->getFile('admin_login_image')) {
					if ($file->isValid() && !$file->hasMoved()) {
						$new_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$upload_path = './assets/img/images/';
						$file->move($upload_path, $new_name);

						if (is_file($upload_path . $branding['admin_login_image'])) {
							unlink($upload_path . $branding['admin_login_image']);
						}

						$this->db->table('branding')->where('name', 'admin_login_image')->update(['value' => $new_name]);
					}
				}

				// Upload e atualização da imagem de login do cliente
				if ($file = request()->getFile('client_login_image')) {
					if ($file->isValid() && !$file->hasMoved()) {
						$new_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$upload_path = './assets/img/images/';
						$file->move($upload_path, $new_name);

						if (is_file($upload_path . $branding['client_login_image'])) {
							unlink($upload_path . $branding['client_login_image']);
						}

						$this->db->table('branding')->where('name', 'client_login_image')->update(['value' => $new_name]);
					}
				}

				// Upload e atualização do preloader
				if ($file = request()->getFile('preloader')) {
					if ($file->isValid() && !$file->hasMoved()) {
						$new_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$upload_path = './assets/img/';
						$file->move($upload_path, $new_name);

						if (is_file($upload_path . $branding['preloader']) && $branding['preloader'] != 'preloader.gif') {
							unlink($upload_path . $branding['preloader']);
						}

						$this->db->table('branding')->where('name', 'preloader')->update(['value' => $new_name]);
					}
				}

				// Atualização das outras configurações
				$support = request()->getPost('enable_support_button_on_client') === '1' ? '1' : '0';

				$this->db->table('branding')->where('name', 'meta_keywords')->update(['value' => request()->getPost('meta_keywords')]);
				$this->db->table('branding')->where('name', 'meta_description')->update(['value' => request()->getPost('meta_description')]);
				$this->db->table('branding')->where('name', 'title')->update(['value' => request()->getPost('title')]);
				$this->db->table('branding')->where('name', 'admin_login_text')->update(['value' => request()->getPost('admin_login_text')]);
				$this->db->table('branding')->where('name', 'client_login_text')->update(['value' => request()->getPost('client_login_text')]);
				$this->db->table('branding')->where('name', 'enable_support_button_on_client')->update(['value' => $support]);
				$this->db->table('branding')->where('name', 'support_button_title')->update(['value' => request()->getPost('support_button_title')]);
				$this->db->table('branding')->where('name', 'support_button_link')->update(['value' => request()->getPost('support_button_link')]);
				$this->db->table('branding')->where('name', 'disable_preloader')->update(['value' => request()->getPost('disable_preloader')]);

				$data['success'] = true;
				$data['message'] = lang2('settings') . ' ' . lang2('updatemessage');

				return response()->setJSON($data);
			} else {
				$data['success'] = false;
				$data['message'] = lang2('demo_error');
				return response()->setJSON($data);
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}

	private function log_activity($action, $item)
	{
		$staffname = session()->staffname;
		$loggedinuserid = session()->usr_id;

		$this->db->table('logs')->insert([
			'date' => date_by_timezone(date('Y-m-d H:i:s')),
			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2($action) . ' ' . lang2($item)),
			'staff_id' => $loggedinuserid
		]);
	}




	function create_custom_field()
	{

		if ($this->Privileges_Model->check_privilege('settings', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$hasError = false;

				$data['message'] = '';

				if (request()->getPost('name') == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
				} else if (request()->getPost('type') == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('type');
				} else if (request()->getPost('relation') == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('relation');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$params = array(

						'name' => request()->getPost('name'),

						'type' => request()->getPost('type'),

						'order' => request()->getPost('order'),

						'data' => request()->getPost('data'),

						'relation' => request()->getPost('relation'),

						'icon' => request()->getPost('icon'),

						'permission' => request()->getPost('permission'),

						'updated_on' => date('Y-m-d H:i:s'),

					);

					$response = $this->Fields_Model->create_new_field($params);

					if ($response) {

						$data['success'] = true;

						$data['message'] = lang2('custom_field_created');

						return response()->setJSON($data);
					} else {

						$data['success'] = false;

						$data['message'] = lang2('custom_field_not_created');

						return response()->setJSON($data);
					}
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function update_custom_field($id)
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

			if (isset($id)) {

				if (isset($_POST) && count($_POST) > 0) {

					$hasError = false;

					$data['message'] = '';

					if (request()->getPost('name') == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
					} else if (request()->getPost('type') == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('type');
					} else if (request()->getPost('relation') == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('relation');
					}

					if ($hasError) {

						$data['success'] = false;

						return response()->setJSON($data);
					}

					if (!$hasError) {

						$params = array(

							'name' => request()->getPost('name'),

							'type' => request()->getPost('type'),

							'order' => request()->getPost('order'),

							'data' => request()->getPost('data'),

							'relation' => request()->getPost('relation'),

							'icon' => request()->getPost('icon'),

							'permission' => request()->getPost('permission'),

							'updated_on' => date('Y-m-d H:i:s'),

						);

						$response = $this->Fields_Model->update_custom_field($id, $params);

						if ($response) {

							$data['success'] = true;

							$data['message'] = lang2('custom_field_updated');

							return response()->setJSON($data);
						} else {

							$data['success'] = true;

							$data['message'] = lang2('custom_field_not_updated');

							return response()->setJSON($data);
						}
					}
				} else {

					echo 'Custom field is not updated';
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function update_custom_field_status($id, $value)
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

			if (isset($id)) {

				$this->db->table('custom_fields')
					->where('id', $id)
					->update(['active' => $value]);

				$date = date('Y-m-d H:i:s');

				$this->db->table('custom_fields')
					->where('id', $id)
					->update(['updated_on' => $date]);


				$data['success'] = true;
			} else {

				$data['success'] = false;

				$data['message'] = 'Custom field status is not updated';
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}
		return response()->setJSON($data);
	}



	function remove_custom_field($id)
	{

		if ($this->Privileges_Model->check_privilege('settings', 'delete')) {

			if (isset($id)) {

				$response = $this->db->table('custom_fields')->delete(array('id' => $id));

				if ($response) {

					$data['success'] = true;

					$data['message'] = lang2('custom_field_removed');
				} else {

					$data['success'] = false;

					$data['message'] = lang2('custom_field_not_removed');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('custom_field_not_removed');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function execute_mysql_query()
	{
		if (request()->getPost()) {
			$mysql_query = request()->getPost('mysql_query');

			$hasError = false;
			$data['message'] = '';

			if (empty($mysql_query)) {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('query');
			}

			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}

			if (!$hasError) {
				$this->db->dbDebug = false;

				$resp = $this->db->query($mysql_query);

				$staffname = session()->get('staffname');
				$loggedinuserid = session()->get('usr_id');

				$this->db->table('logs')->insert([
					'date' => date_by_timezone(date('Y-m-d H:i:s')),
					'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('executed') . ' ' . lang2('manual_sql_query'),
					'staff_id' => $loggedinuserid
				]);

				$data['success'] = true;
				$data['message'] = lang2('query_success');
				$data['result'] = '';
				$data['info_message'] = '';
				$data['query_response'] = $resp;

				if (is_array($resp) || $resp instanceof Traversable || $resp != 'true' || $resp != 'false') {
					$data['success'] = 'info';
					$data['result'] = $resp->getResultArray();
					$data['info_message'] = 'You can find your query result in Console';
				}

				return response()->setJSON($data);
			}
		}
	}




	function get_smtp_password()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$is_demo = $this->Settings_Model->is_demo();

			if (!$is_demo) {

				$password = request()->getPost('password');

				$hasError = false;

				$data['message'] = '';

				if ($password == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('password');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$login = $this->db->table('staff')
						->where('email', session()->get('email'))
						->where('password', md5($password))
						->get()
						->getResult();


					if (is_array($login) && count($login) == 1) {

						$settings = $this->Settings_Model->get_settings_ciuis_origin();

						$data['password'] = $settings['smtppassoword'];

						$data['success'] = true;

						return response()->setJSON($data);
					} else {

						$data['message'] = lang2('incorrect_password');

						$data['success'] = false;

						return response()->setJSON($data);
					}
				}
			} else {

				$data['message'] = 'You can not see smtp password of demo version';

				$data['success'] = false;

				return response()->setJSON($data);
			}
		}
	}



	function check_for_update()
	{

		$version = $this->Settings_Model->get_version_detail();

		$value = 2;

		if ((strtotime($version['last_checked'] . ' +' . $value . 'week') < strtotime(date('Y-m-d'))) && $version['is_update_available'] == 0) {

			$check = $this->version_detail_of_app();

			if ($check === true) {
				$this->db->table('versions')
					->where('id', '1')
					->update(['is_update_available' => 1, 'last_checked' => date('Y-m-d')]);
			} else {
				$this->db->table('versions')
					->where('id', '1')
					->update(['is_update_available' => 0, 'last_checked' => date('Y-m-d')]);
			}
		}
	}



	function version_detail_of_app()
	{

		$version_notifications_array = ausGetAllVersions();

		$settings = $this->Settings_Model->get_version_detail();

		if ($version_notifications_array['notification_case'] == "notification_operation_ok") {

			$version = $version_notifications_array['notification_data']['product_versions'];

			$list_array = array();

			$list_array_log = array();

			$flag = false;

			foreach (array_reverse($version) as $key => $value) {

				if ($flag) {

					if ($settings['versions_name'] < $value['version_number'] && $value['version_status'] == 1) {

						$value['version_number'];

						$list_array[] = array('version_number' => $value['version_number']);
					}

					break;
				} else {

					if ($settings['versions_name'] < $value['version_number'] && $value['version_status'] == 1) {

						$value['version_number'];

						$list_array[] = array('version_number' => $value['version_number']);

						$flag = true;
					}

					if ($settings['versions_name'] == $value['version_number']) {

						$flag = true;
					}
				}
			}

			$msg = "";

			$updated = "available";

			if (empty($list_array)) {

				$list_array[0] = array('version_number' => $settings['versions_name']);

				$msg = 'Already updated';

				$updated = "";
			}
		} else {

			$msg = 'alreadyupdated';

			$updated = "";

			$list_array[0] = array('version_number' => $settings['versions_name']);

			$list_array_log[0] = '';
		}

		if ($updated === "available") {

			return true;
		} else {

			return false;
		}
	}



	function uninstall()
	{

		$is_demo = $this->Settings_Model->is_demo();

		if (!$is_demo) {

			if (isset($_POST) && count($_POST) > 0) {

				if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

					$hasError = false;

					if (request()->getPost('confirm') != '1') {

						$hasError = true;

						$data['message'] = lang2('uninstall_note');
					}

					if ($hasError) {

						$data['success'] = false;

						return response()->setJSON($data);
					}

					if (!$hasError) {

						include_once(APPPATH . 'third_party/script/app_configuration.php');

						include_once(APPPATH . 'third_party/script/app_functions.php');

						$check_data = remote_check();

						if ($check_data['notification_case'] = 'remote_check_done') {

							$handle = @fopen(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION, "w+");

							@fclose($handle);

							$data['success'] = true;

							$data['message'] = lang2('removed_lics');

							unsetSession();
						} else {

							$handle = @fopen(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION, "w+");

							@fclose($handle);

							$data['success'] = false;

							$data['message'] = $check_data['notification_text'];
						}

						return response()->setJSON($data);
					}
				} else {

					$data['success'] = false;

					$data['message'] = lang2('you_dont_have_permission');
				}
			} else {

				echo 'Error';
			}
		} else {

			$data['success'] = false;

			$data['message'] = 'You can not uninstall license in demo version';

			return response()->setJSON($data);
		}
	}



	function get_modules()
	{

		$module = $this->db->table('modules')
			->get()
			->getRowArray();


		// $modules_data = array();

		// foreach ($modules as $module) {

		// 	$modules_data[] = array(

		// 		'name' => $module['name'],

		// 		'module_status' => ($module['status'] == '1')?true:false,

		// 		'module_updated' => $module['updatedat']

		// 	);

		// }

		$modules_data = array(

			'name' => $module['name'],

			'hr_status' => ($module['status'] == '1') ? true : false,

			'module_updated' => $module['updatedat'],

			'module_license' => $module['license']

		);

		return response()->setJSON($modules_data);
	}



	/**********Create New Role************/

	function create_role()
	{

		if ($this->Privileges_Model->check_privilege('settings', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$role = request()->getPost('role');

				$hasError = false;

				if ($role == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('role');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$params = array(
						'role_name' => $role,
						'role_type' => request()->getPost('type'),
						'role_createdat' => date('Y-m-d_H-i-s'),
						'role_updatedat' => date('Y-m-d_H-i-s'),
						'created_by' => session()->usr_id,
						'id_company' => session()->id_company,
					);

					$this->Settings_Model->create_role($params);
					$data['message'] = lang2('role') . ' ' . lang2('createmessage');
					$data['success'] = true;
					return response()->setJSON($data);
				}
			}
		} else {

			$data['message'] = lang2('you_dont_have_permission');

			$data['success'] = false;

			return response()->setJSON($data);
		}
	}



	/**********Update Role************/

	function update_role($id)
	{

		if (($id == '1' || $id == '2' || $id == '3') && ($this->Settings_Model->is_demo())) {

			$data['message'] = 'Role can not be changed in Demo mode.';

			$data['success'] = false;

			return response()->setJSON($data);
		} else {

			if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

				$roles = $this->Settings_Model->get_role($id);

				if ($roles) {

					if (isset($_POST) && count($_POST) > 0) {

						$role = request()->getPost('role');

						$hasError = false;

						if ($role == '') {

							$hasError = true;

							$data['message'] = lang2('invalidmessage') . ' ' . lang2('role');
						}

						if ($hasError) {

							$data['success'] = false;

							return response()->setJSON($data);
						}

						if (!$hasError) {

							$params = array(

								'role_name' => $role,

								'role_type' => request()->getPost('type'),

								'role_updatedat' => date('Y-m-d_H-i-s'),

							);

							$this->Settings_Model->update_role($params, $id);

							$data['message'] = lang2('role') . ' ' . lang2('updatemessage');

							$data['success'] = true;

							return response()->setJSON($data);
						}
					}
				} else {

					$data['message'] = lang2('role') . ' ' . lang2('does_not_exist');

					$data['success'] = false;

					return response()->setJSON($data);
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		}
	}



	/**********Delete Role************/

	function delete_role($id)
	{

		if (($id == '1' || $id == '2' || $id == '3') && ($this->Settings_Model->is_demo())) {

			$data['message'] = 'Role can not be deleted in Demo mode.';

			$data['success'] = false;

			return response()->setJSON($data);
		} else {

			if ($this->Privileges_Model->check_privilege('settings', 'delete')) {

				$role = $this->Settings_Model->get_role($id);

				if ($role) {

					if ($this->Settings_Model->check_role($id) == 0) {

						$this->Settings_Model->delete_role($id);

						$data['success'] = true;

						$data['message'] = lang2('role') . ' ' . lang2('deletemessage');

						return response()->setJSON($data);
					} else {

						$data['success'] = false;

						$data['message'] = $data['message'] = lang2('role') . ' ' . lang2('is_linked') . ' ' . lang2('with') . ' ' . lang2('staff') . ', ' . lang2('so') . ' ' . lang2('cannot_delete') . ' ' . lang2('role');

						return response()->setJSON($data);
					}
				} else {

					$data['success'] = false;

					$data['message'] = lang2('role') . ' ' . lang2('does_not_exist');

					return response()->setJSON($data);
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		}
	}

	function requestCard()
	{
		require __DIR__ .  '/../../vendor/autoload.php';
		MercadoPago\SDK::setClientId("923838863142573");
		MercadoPago\SDK::setClientSecret("HfEXJQh4QFj9TdcmQvQQFcAFe4aQVNHT");
		//MercadoPago\SDK::setAccessToken("TEST-923838863142573-090907-ddf85ce404f039bda81a9ab4a0f7ab02-30618332");
		$plano = $this->db->table('plans')
			->where('id_plan', request()->getPost('id_plan'))
			->get()
			->getRowArray();

		$plan_tipo = request()->getPost('plan_tipo');

		$payment = new MercadoPago\Payment();
		$payment->transaction_amount = (float) $plan_tipo == 30 ?  $plano['valor'] : $plano['valor_anual'];
		$payment->token = request()->getPost('token');
		$payment->description = 'Venda de plano';
		$payment->installments = 1;
		$payment->payment_method_id = request()->getPost('paymentMethodId');
		$payment->issuer_id = (int) request()->getPost('issuer_id');
		$payer = new MercadoPago\Payer();
		$payer->email = session()->email;
		$payer->identification = array(
			"type" => 'CPF',
			"number" => request()->getPost('cpf')
		);
		$payment->payer = $payer;
		$payment->save();

		$success = 400;
		if ($payment->status == "approved") {
			$success = 200;

			$this->db->table('companies')
				->where('id_company', session()->get('id_company'))
				->update([
					'plano' => request()->getPost('id_plan'),
					'vencimento' => date('Y-m-d', strtotime("+$plan_tipo days", strtotime(date('Y-m-d'))))
				]);
		}

		return response()->setJSON(["success" => $success]);
	}

	function addPlano()
	{
		$param = [];
		if (!empty(request()->getPost('nm_forma'))) {
			$param['nm_forma'] = request()->getPost('nm_forma');
		}
		if (!empty(request()->getPost('valor'))) {
			$param['valor'] = str_replace(',', '.', str_replace('.', '', request()->getPost('valor')));
		}
		if (!empty(request()->getPost('valor_anual'))) {
			$param['valor_anual'] = str_replace(',', '.', str_replace('.', '', request()->getPost('valor_anual')));
		}

		if (!empty(request()->getPost('min_user'))) {
			$param['min_user'] = request()->getPost('min_user');
		}
		if (!empty(request()->getPost('nm_plan'))) {
			$param['nm_plan'] = request()->getPost('nm_plan');
		}

		$param['is_ativo'] = request()->getPost('is_ativo');

		if (!request()->getPost('id_plan')) {
			$param['tp_plano'] = 'personal';
			$this->db->table('plans')->insert($param);
		} else {
			$this->db->table('plans')->where('id_plan', request()->getPost('id_plan'))->update($param);
		}

		return response()->setJSON(['success' => true]);
	}

	function dell_planos($id)
	{
		$param = [];
		$param['is_ativo'] = '0';
		$this->db->table('plans')->where('id_plan', $id)->update($param);
		return response()->setJSON(['success' => true]);
	}

	function AddPermission()
	{
		$this->db->table('permissions_plans')->delete(['id_plan' => request()->getPost('id_plan')]);
		if (request()->getPost('permissions') != null) {
			$permissoes = [];
			foreach (request()->getPost('permissions') as $index => $row) {
				if ($row == 'true' && !empty($row)) {
					$permissoes[] = $index;
				}
			}
			if (!in_array("20", $permissoes)) {
				$this->db->table('permissions_plans')->insert(["id_permission" => "20", 'id_plan' => request()->getPost('id_plan')]);
			}


			foreach (request()->getPost('permissions') as $index => $row) {
				if ($row == 'true' && !empty($row)) {
					$this->db->table('permissions_plans')->insert(["id_permission" => $index, 'id_plan' => request()->getPost('id_plan')]);
				}
			}
		}
		return response()->setJSON(["success" => true]);
	}



	function get_prioridades()
	{
		$prioridades = $this->db->table('settings_prioridades')
			->where('settings_prioridades.id_company', session()->get('id_company'))
			->join('leads_list', 'leads_list.id_list = settings_prioridades.funil', 'left')
			->get()
			->getResultArray();


		foreach ($prioridades as $index => $row) {
			$prioridades[$index]['funil'] = explode(",", $row['funil']);
			$prioridades[$index]['status'] = $this->db->table('settings_prioridades_status')
				->where('id_prioridade', $row['id'])
				->get()
				->getResultArray();
		}
		return response()->setJSON(['prioridades' => $prioridades]);
	}


	/**********Get Permissions************/

	function get_permission_planos($id_plan = '')
	{
		$permissions = $this->Privileges_Model->get_all_permissions();
		$permissionsPlan = $this->Privileges_Model->get_all_permissionsByPlan($id_plan);
		$permissionsPlanData = [];
		$permissionsData = [];

		foreach ($permissionsPlan as $row) {
			$permissionsPlanData[] = $row['id_permission'];
		}

		foreach ($permissions as $row) {
			$row['permitido'] = in_array($row['id'], $permissionsPlanData) ? "1" : "0";
			$row['permission_key'] = lang2($row['permission']);
			$permissionsData[] = $row;
		}
		return response()->setJSON($permissionsData);
	}

	function get_permission($type = '', $permission_all = '')
	{

		$permissions = $this->Privileges_Model->get_all_permissionsByCompany(session()->id_company);

		$permissions_data = array();

		foreach ($permissions as $permission) {

			if ($type == 'staff' && $permission['key'] == 'settings') {

				continue;
			}

			if ($type == 'other' && $permission['key'] != 'invoices' && $permission['key'] != 'expenses') {

				continue;
			}

			$permissions_datas = array(

				'id' => $permission['id'],

				'key' => $permission['key'],

				'permission_key' => lang2($permission['permission']),

				'permission_view_own' => false,

				'permission_view_all' => false,

				'permission_create' => false,

				'permission_edit' => false,

				'permission_delete' => false,

			);

			if ($permission_all == '1') {

				if ($type == 'staff' && $permission['key'] == 'settings') {

					$permissions_datas = array(

						'id' => $permission['id'],

						'key' => $permission['key'],

						'permission_key' => lang2($permission['permission']),

						'permission_view_own' => false,

						'permission_view_all' => false,

						'permission_create' => false,

						'permission_edit' => false,

						'permission_delete' => false,

					);
				} else if ($type == 'staff' && $permission['key'] == 'staff') {

					$permissions_datas = array(

						'id' => $permission['id'],

						'key' => $permission['key'],

						'permission_key' => lang2($permission['permission']),

						'permission_view_own' => false,

						'permission_view_all' => true,

						'permission_create' => false,

						'permission_edit' => false,

						'permission_delete' => false,

					);
				} else if ($type == 'other' && ($permission['key'] == 'invoices' || $permission['key'] == 'expenses')) {

					$permissions_datas = array(

						'id' => $permission['id'],

						'key' => $permission['key'],

						'permission_key' => lang2($permission['permission']),

						'permission_view_own' => false,

						'permission_view_all' => true,

						'permission_create' => false,

						'permission_edit' => false,

						'permission_delete' => false,

					);
				} else {

					$permissions_datas = array(

						'id' => $permission['id'],

						'key' => $permission['key'],

						'permission_key' => lang2($permission['permission']),

						'permission_view_own' => true,

						'permission_view_all' => true,

						'permission_create' => true,

						'permission_edit' => true,

						'permission_delete' => true,

					);
				}
			}

			$permissions_data[] = $permissions_datas;
		}

		return response()->setJSON($permissions_data);
	}



	/**********Get all Role************/

	function get_roles($id_company = null)
	{
		$roles = $this->Settings_Model->get_all_roles($id_company);
		$data_role = array();
		foreach ($roles as $role) {

			if ($role['role_type'] == 'admin') {
				$type = lang2('admin');
			} else if ($role['role_type'] == 'staff') {
				$type = lang2('staff');
			} else {
				$type = lang2('other');
			}

			$data_role[] = array(
				'role_id' => $role['role_id'],
				'role_name' => $role['role_name'],
				'role_type' => $type,
				'user_type' => $role['role_type'],
				'updated_at' => date(get_dateTimeFormat(), strtotime($role['role_updatedat'])),
			);
		}

		return response()->setJSON($data_role);
	}

	function get_roles_export()
	{
		$q = $this->db->table('companies_permission_export')
			->where('id_company', session()->id_company)
			->get()
			->getResultArray();
		return response()->setJSON($q);
	}

	function get_roles_export_by_role($id_role)
	{
		$q = $this->db->table('companies_permission_export')
			->where('id_company', session()->id_company)
			->get()
			->getResultArray();

		foreach ($q as $index => $row) {
			$q[$index]['permitido'] = $this->db->table('role_permissions_export')
				->where('export_page', $row['export_page'])
				->where('id_role', $id_role)
				->get()
				->getRowArray() ? true : false;
			$q[$index]['permission_key'] = lang2($row['export_page']);
			$q[$index]['id'] = $row['export_page'];
		}

		return response()->setJSON($q);
	}





	function savePermissonRole()
	{
		if (request()->getPost('permissions_export') != null) {
			$this->db->table('role_permissions_export')->delete(['id_role' => request()->getPost('role_id')]);

			foreach (request()->getPost('permissions_export') as $index => $row) {
				if ($row == 'true' && !empty($row)) {
					$this->db->table('role_permissions_export')->insert(["export_page" => $index, 'id_role' => request()->getPost('role_id')]);
				}
			}
		}
		return response()->setJSON(["success" => true]);
	}


	function removePrioridade()
	{
		$this->db->table('settings_prioridades')->delete(['id' => request()->getPost('id_prioridade')]);
		$this->db->table('settings_prioridades_status')->delete(['id_prioridade' => request()->getPost('id_prioridade')]);
		return response()->setJSON(["success" => true, "message" => "Salvo com sucesso!"]);
	}

	function savePrioridade()
	{
		$id_prioridade = "";

		if (request()->getPost('id_prioridade')) {
			$id_prioridade = request()->getPost('id_prioridade');
			$this->db->table('settings_prioridades_status')->delete(['id_prioridade' => $id_prioridade]);

			$this->db->table('settings_prioridades')
				->where('id', $id_prioridade)
				->update([
					"id_company" => session()->get('id_company'),
					'funil' => implode(",", request()->getPost('funil')),
					'source_id' => request()->getPost('source_id'),
					'regras' => request()->getPost('regras'),
				]);
		} else {

			$this->db->table('settings_prioridades')->insert([
				"id_company" => session()->get('id_company'),
				'funil' => implode(",", request()->getPost('funil')),
				'source_id' => request()->getPost('source_id'),
				'regras' => request()->getPost('regras'),
			]);

			$id_prioridade = $this->db->insertID();
		}


		if (request()->getPost('fases_funil')) {
			foreach (request()->getPost('fases_funil') as $index => $row) {
				$paramsSetting['id_status'] = implode(",", $row['id_status']);
				$paramsSetting['ordem'] = $index;
				$paramsSetting['id_prioridade'] = $id_prioridade;
				$this->db->table('settings_prioridades_status')->insert($paramsSetting);
			}
		}

		return response()->setJSON(["success" => true, "message" => "Salvo com sucesso!"]);
	}

	public function get_atividades()
	{
		$builder = $this->db->table('leads_atv_select');
		$builder->select('*');
		$builder->where('id_company', session()->id_company);
		$builder->orderBy('nm_atividade_select', 'asc');
		$result = $builder->get()->getResultArray();

		return response()->setJSON($result);
	}

	public function get_atividades_customer()
	{
		$builder = $this->db->table('customers_atv_select');
		$builder->select('*');
		$builder->where('id_company', session()->id_company);
		$builder->orderBy('nm_atividade_select', 'asc');
		$result = $builder->get()->getResultArray();

		return response()->setJSON($result);
	}


	public function dell_atividades($id)
	{
		$builder = $this->db->table('leads_atv_select');
		$response = $builder->delete(['id_atv' => $id]);
		return response()->setJSON(['success' => $response]);
	}

	public function dell_atividades_custom($id)
	{
		$builder = $this->db->table('customers_atv_select');
		$response = $builder->delete(['id_atv' => $id]);
		return response()->setJSON(['success' => $response]);
	}




	/**********Get Role By Roleid************/

	function get_role($id)
	{

		$role = $this->Settings_Model->get_role($id);
		$permissions_data = array();
		$role_data = array();
		if ($role) {

			$permissions = $this->Privileges_Model->get_all_permissionsByCompany(session()->id_company);
			foreach ($permissions as $permission) {
				if ($role['role_type'] == 'staff' && $permission['key'] == 'settings') {
					continue;
				}

				if ($role['role_type'] == 'other' && $permission['key'] != 'invoices' && $permission['key'] != 'expenses') {
					continue;
				}

				$roles = $this->Settings_Model->get_role_permission($permission['id'], $id);

				if ($roles) {

					$permissions_data[] = array(

						'id' => $permission['id'],
						'role_permission_id' => $roles['role_permission_id'],
						'key' => $permission['key'],
						'permission_key' => lang2($permission['permission']),
						'permission_view_own' => $roles['permission_view_own'] == '1' ? true : false,
						'permission_view_all' => $roles['permission_view_all'] == '1' ? true : false,
						'permission_create' => $roles['permission_create'] == '1' ? true : false,
						'permission_edit' => $roles['permission_edit'] == '1' ? true : false,
						'permission_delete' => $roles['permission_delete'] == '1' ? true : false,

					);

					$role_data = array(

						'permissions_data' => $permissions_data,
						'role_name' => $role['role_name'],
						'role_type' => $role['role_type'],

					);
				} else {

					$permissions_data[] = array(
						'id' => $permission['id'],
						'key' => $permission['key'],
						'permission_key' => lang2($permission['permission']),
						'permission_view_own' => false,
						'permission_view_all' => false,
						'permission_create' => false,
						'permission_edit' => false,
						'permission_delete' => false,
					);

					$role_data = array(
						'permissions_data' => $permissions_data,
						'role_name' => $role['role_name'],
						'role_type' => $role['role_type'],
					);
				}
			}
		}

		return response()->setJSON($role_data);
	}



	function get_payment_methods()
	{

		$methods = $this->db->table('payment_methods')
			->get()
			->getResultArray();


		$methods_data = array();

		foreach ($methods as $method) {

			$methods_data[] = array(

				'id' => $method['id'],

				'input_label1' => lang2($method['input_label1']) ? lang2($method['input_label1']) : $method['input_label1'],

				'input_label2' => lang2($method['input_label2']) ? lang2($method['input_label2']) : $method['input_label2'],

				'input_label3' => $method['input_label3'] ? lang2($method['input_label3']) : null,

				'input_value1' => $method['input_value1'],

				'input_value2' => $method['input_value2'],

				'input_value3' => $method['input_value3'] ? $method['input_value3'] : null,

				'active' => $method['active'] == '1' ? true : false,

				'sandbox_account' => $method['sandbox_account'] == '1' ? true : false,

				'payment_record_account' => $method['payment_record_account'],

				'relation' => $method['relation'],

				'gateway_relation' => lang2($method['relation']) ? lang2($method['relation']) : $method['relation'],

				'name' => $method['name'],

				'image' => $method['image'],

				'gateway_note' => $method['gateway_note'],

				'updated_at' => date(get_dateTimeFormat(), strtotime($method['updated_at'])),

			);
		}

		return response()->setJSON($methods_data);
	}



	function update_payment_gateway($payment)
	{

		if ($this->Privileges_Model->check_privilege('settings', 'edit')) {

			if (isset($payment) && isAdmin()) {

				if (isset($_POST) && count($_POST) > 0) {

					$payment_mode = $this->Settings_Model->payment_mode($payment);

					$type = $payment;

					$input_value1 = request()->getPost('input_value1');

					$input_value2 = request()->getPost('input_value2');

					$input_value3 = request()->getPost('input_value3');

					$active = request()->getPost('active');

					$sandbox_account = request()->getPost('sandbox_account');

					$payment_record_account = request()->getPost('payment_record_account');

					$hasError = false;

					if ($active == '1') {

						if ($input_value1 == '' && !empty($payment_mode['input_label1'])) {

							$hasError = true;

							$return['message'] = lang2('required_message') . ' ' . lang2($payment_mode['input_label1']);
						} else if ($input_value2 == '' && !empty($payment_mode['input_label2'])) {

							$hasError = true;

							$return['message'] = lang2('required_message') . ' ' . lang2($payment_mode['input_label2']);
						} else if ($input_value3 == '' && !empty($payment_mode['input_label3'])) {

							$hasError = true;

							$return['message'] = lang2('required_message') . ' ' . lang2($payment_mode['input_label3']);
						} else if ($payment_record_account == '') {

							$hasError = true;

							$return['message'] = lang2('required_message') . ' ' . lang2('payment_account') . ' ' . lang2('for') . ' ' . lang2($type);
						}
					}

					if ($hasError) {

						$return['success'] = false;

						return response()->setJSON($return);
					}

					if (!$hasError) {

						$params = array(

							'input_value1' => request()->getPost('input_value1'),

							'input_value2' => request()->getPost('input_value2'),

							'input_value3' => request()->getPost('input_value3'),

							'active' => request()->getPost('active'),

							'sandbox_account' => request()->getPost('sandbox_account'),

							'payment_record_account' => request()->getPost('payment_record_account'),

							'updated_at' => date("Y.m.d H:i:s"),

						);

						$this->db->table('payment_methods')
							->where('relation', $payment)
							->update($params);


						$return['success'] = true;

						$return['message'] = lang2('payment_gateway') . ' ' . lang2('updatemessage');

						return response()->setJSON($return);
					}
				} else {

					$return['message'] = lang2('errormessage');

					$return['success'] = false;

					return response()->setJSON($return);
				}
			} else {

				$return['message'] = lang2('errormessage');

				$return['success'] = false;

				return response()->setJSON($return);
			}
		} else {

			$return['success'] = false;

			$return['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($return);
		}
	}

	public function addAtividadeListCustom()
	{
		if ($this->Privileges_Model->check_privilege('staff', 'edit')) {
			$uploadPath = './assets/img/atividades/';
			$file = request()->getFile('file');

			if ($file && $file->isValid()) {
				$newName = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
				$file->move($uploadPath, $newName);

				$matriz = [
					"nm_atividade_select" => request()->getGet('nm_atividade_select'),
					'atv_ft' => "assets/img/atividades/" . $newName,
					'id_company' => session()->get('id_company'),
				];

				if (request()->getGet('id_atv')) {
					$this->db->table('customers_atv_select')
						->where('id_atv', request()->getGet('id_atv'))
						->update($matriz);
				} else {
					$this->db->table('customers_atv_select')->insert($matriz);
				}

				return response()->setJSON(['success' => true, 'message' => 'Atividade ' . lang2('updatemessage')]);
			} else {
				$matriz = [
					"nm_atividade_select" => request()->getGet('nm_atividade_select'),
					'id_company' => session()->get('id_company'),
					'atv_ft' => request()->getGet('icon_atvSelect') ? "assets/img/atividades/icons/" . request()->getGet('icon_atvSelect') : null,
				];

				if (request()->getGet('id_atv')) {
					$this->db->table('customers_atv_select')
						->where('id_atv', request()->getGet('id_atv'))
						->update($matriz);
				} else {
					$this->db->table('customers_atv_select')->insert($matriz);
				}

				return response()->setJSON(['success' => true, 'message' => 'Atividade ' . lang2('updatemessage')]);
			}
		} else {
			return response()->setJSON(['success' => false, 'message' => lang2('you_dont_have_permission')]);
		}
	}


	public function addAtividadeList()
	{
		// Verifica se o usuário tem permissão para editar
		if ($this->Privileges_Model->check_privilege('staff', 'edit')) {

			// Verifica se há dados POST ou GET
			if (request()->getPost() || request()->getGet()) {
				// Configurações de upload
				$uploadPath = ROOTPATH . 'public/assets/img/atividades/';

				$allowedTypes = 'gif|jpg|png|jpeg|svg';
				$maxSize = 9000; // em KB

				// Obtém o arquivo do request
				$file = request()->getFile('file');

				if ($file && $file->isValid()) {
					$fileType = $file->getExtension();
					$fileSize = $file->getSizeByUnit('kb'); // em KB

					// Verifica se o tipo de arquivo e o tamanho estão dentro das permissões
					if (in_array($fileType, explode('|', $allowedTypes)) && $fileSize <= $maxSize) {
						$newName = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$filePath = $uploadPath . $newName;

						// Move o arquivo para o diretório de uploads
						if ($file->move($uploadPath, $newName)) {
							if (is_file($filePath)) {
								$matriz = [
									"nm_atividade_select" => request()->getGet('nm_atividade_select'),
									'atv_ft' => "assets/img/atividades/" . $newName,
									'id_company' => session()->get('id_company')
								];

								if (request()->getGet('id_atv') && !empty(request()->getGet('id_atv'))) {
									$response = $this->db->table('leads_atv_select')
										->where('id_atv', request()->getGet('id_atv'))
										->update($matriz);
								} else {
									$response = $this->db->table('leads_atv_select')->insert($matriz);
								}

								$data = [
									'success' => true,
									'message' => 'Atividade ' . lang2('updatemessage'),
								];
								return response()->setJSON($data);
							} else {
								$data = [
									'success' => false,
									'message' => lang2('errormessage'),
								];
								return response()->setJSON($data);
							}
						} else {
							$data = [
								'success' => false,
								'message' => 'Erro ao mover o arquivo para o diretório de uploads.',
							];
							return response()->setJSON($data);
						}
					} else {
						$data = [
							'success' => false,
							'message' => 'Arquivo inválido ou excede o tamanho permitido.',
						];
						return response()->setJSON($data);
					}
				} else {
					if (request()->getGet('nm_atividade_select')) {
						$matriz = [
							"nm_atividade_select" => request()->getGet('nm_atividade_select'),
							'id_company' => session()->get('id_company'),
							'atv_ft' => request()->getGet('icon_atvSelect') ? "assets/img/atividades/icons/" . request()->getGet('icon_atvSelect') : null,
						];

						if (request()->getGet('id_atv') && !empty(request()->getGet('id_atv'))) {
							$response = $this->db->table('leads_atv_select')
								->where('id_atv', request()->getGet('id_atv'))
								->update($matriz);
						} else {
							$response = $this->db->table('leads_atv_select')->insert($matriz);
						}

						$data = [
							'success' => true,
							'message' => 'Atividade ' . lang2('updatemessage'),
						];
						return response()->setJSON($data);
					} else {
						$data = [
							'success' => false,
							'message' => 'Erro ao adicionar: ',
						];
						return response()->setJSON($data);
					}
				}
			}
		} else {
			$data = [
				'success' => false,
				'message' => lang2('you_dont_have_permission'),
			];
			return response()->setJSON($data);
		}
	}


	function get_avisos()
	{
		$datas['result'] = $this->db->table('avisos')
			->where('is_ativo', '1')
			->where('id_company', session()->id_company)
			->get()
			->getResultArray();

		$datas['success'] = true;
		return response()->setJSON($datas);
	}


	function updt_aviso()
	{
		$param = [];
		if (!empty(request()->getPost('nm_aviso'))) {
			$param['nm_aviso'] = request()->getPost('nm_aviso');
		}
		if (!empty(request()->getPost('tempo'))) {
			$param['tempo'] = request()->getPost('tempo');
		}
		if (!empty(request()->getPost('funcionarios'))) {
			$param['funcionarios'] = implode(",", request()->getPost('funcionarios'));
		}

		if (!empty(request()->getPost('emails'))) {
			$param['emails'] = implode(",", request()->getPost('emails'));
		}

		if (!empty(request()->getPost('tipo'))) {
			$param['tipo'] = request()->getPost('tipo');
		}
		if (!empty(request()->getPost('opcaoAlerta'))) {
			$param['opcaoAlerta'] = request()->getPost('opcaoAlerta');
		}
		if (!empty(request()->getPost('frequencia'))) {
			$param['frequencia'] = request()->getPost('frequencia');
		}
		if (!empty(request()->getPost('diaSemana'))) {
			$param['diaSemana'] = request()->getPost('diaSemana');
		}
		if (!empty(request()->getPost('diaMes'))) {
			$param['diaMes'] = request()->getPost('diaMes');
		}
		if (!empty(request()->getPost('menssagem'))) {
			$param['menssagem'] = request()->getPost('menssagem');
		}

		if (request()->getPost('anexo') != null && !empty(request()->getPost('anexo'))) {
			$_POST['nm_file'] = request()->getPost('nm_file_anexo');
			$param['anexo']  = upload($_POST['anexo'], "anexos");
		}

		if (request()->getPost('alerta') != null && !empty(request()->getPost('alerta'))) {
			$_POST['nm_file'] = request()->getPost('nm_file_alerta');
			$param['alerta']  = upload($_POST['alerta'], "anexos");
		}

		$param['is_ativo'] = request()->getPost('is_ativo');



		if (!request()->getPost('id_aviso')) {
			$param['id_company'] = session()->id_company;
			$param['staff_id'] = session()->usr_id;
			$param['created'] = date('Y-m-d H-i-s');
			$this->db->table('avisos')->insert($param);
		} else {
			$this->db->table('avisos')->where('id_aviso', request()->getPost('id_aviso'))->update($param);
		}

		return response()->setJSON(['success' => true]);
	}
}
