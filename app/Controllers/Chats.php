<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
class Chats extends BaseController
{
	function __construct()
	{
		parent::loadModels();
	}
	function index()
	{
		$data['title'] = lang2('chats');
		$data['chats'] = $this->Chats_Model->get_all_chats();
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		return view('chats/index', $data);
	}

	function create()
	{
		$data['title'] = lang2('addchat');

		if (!$this->Privileges_Model->check_privilege('chats', 'create')) {
			return response()->setJSON([
				'success' => false,
				'message' => lang2('you_dont_have_permission')
			]);
		}

		if ($_POST) {
			$user = request()->getPost('user');
			$priority = request()->getPost('priority');
			$message = request()->getPost('message');

			$hasError = false;
			$data['message'] = '';

			if (empty($user) || $user == 'undefined') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('user');
			} elseif (empty($priority) || $priority == 'undefined') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('priority');
			} elseif (empty($message) || $message == 'undefined') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('message');
			}

			if ($hasError) {
				return response()->setJSON([
					'success' => false,
					'message' => $data['message']
				]);
			}

			$appconfig = get_appconfig();

			$filename = null;
			if (isset($_FILES['file']) && $_FILES['file']['name'] != '') {
				$uploadPath = ROOTPATH . 'public/uploads/attachments/';
				$file = request()->getFile('file');
				$filename = preg_replace("/[^a-z0-9_\-.]/i", '', $file->getName());
				$file->move($uploadPath, $filename);
			}



			$params = [
				'send_user_id'   => $user,
				'department_id'  => request()->getPost('department'),
				'priority'       => $priority,
				'status_id'      => 1,
				'subject'        => request()->getPost('subject'),
				'message'        => $message,
				'attachment'     => $filename,
				'date'           => date("Y.m.d H:i:s"),
				'send_staff'     => session()->usr_id
			];

			session()->setFlashdata('ntf1', lang2('chatadded'));

			$chats_id = $this->Chats_Model->add_chats($params);

			if ($chats_id) {
				if (request()->getPost('custom_fields')) {
					$this->Fields_Model->custom_field_data_add_or_update_by_type([
						'custom_fields' => request()->getPost('custom_fields')
					], 'chat', $chats_id);
				}

				$template = $this->Emails_Model->get_template('chat', 'new_chat');
				if ($template['status'] == 1) {
					$chat = $this->Chats_Model->get_chats($chats_id);

					$priority = match ($chat['priority']) {
						'1' => lang2('low'),
						'2' => lang2('medium'),
						'3' => lang2('high'),
					};

					$message_vars = [
						'{name}'           => session()->get('staffname'),
						'{email_signature}' => session()->get('email'),
						'{chat_subject}'   => request()->getPost('subject'),
						'{chat_message}'   => request()->getPost('message'),
						'{chat_priority}'  => $priority,
						'{chat_department}' => $chat['department']
					];

					$subject = strtr($template['subject'], $message_vars);
					$message = strtr($template['message'], $message_vars);

					$param = [
						'from_name' => $template['from_name'],
						'subject'   => $subject,
						'message'   => $message,
						'created'   => date("Y.m.d H:i:s")
					];

					if ($chat['customeremail']) {
						$this->db->table('email_queue')->insert($param);
					}
				}

				if ($appconfig['ticket_series']) {
					$appconfig['ticket_series']++;
					$this->Settings_Model->increment_series('ticket_series', $appconfig['ticket_series']);
				}

				return response()->setJSON([
					'success' => true,
					'message' => lang2('chat') . ' ' . lang2('createmessage'),
					'id'      => $chats_id
				]);
			}

			return response()->setJSON([
				'success' => false,
				'message' => lang2('errormessage')
			]);
		}
	}

	function chats_getNews()
	{
		$id = request()->getPost('id');
		$result = 0;
		$replies = [];

		if ($id) {
			$replies = $this->Chats_Model->get_chat_by_privileges($id);
			$result = 1;
		}

		return response()->setJSON([
			'chatAtual'      => ['result' => $result, 'replies' => $replies],
			'chats'          => $this->chats(),
			'ultimaVerificacao' => request()->getPost('ultimaVerificacao')
		]);
	}

	function chats()
	{
		$chats = $this->Chats_Model->get_all_chats_by_privileges();
		$data_chats = [];

		foreach ($chats as $chat) {
			$ultimaMsg = $this->db->table('chatreplies')
				->select('*')
				->where('(chat_staff1 = ' . session()->get('usr_id') . ' and chat_staff2 = ' . $chat['id'] . ') or (chat_staff2 = ' . session()->get('usr_id') . ' and chat_staff1 = ' . $chat['id'] . ')')
				->orderBy('id', 'desc')
				->limit(1)
				->get()
				->getRowArray();

			$data_chats[] = [
				'id'         => $chat['id'],
				'msg_nova'   => !empty($ultimaMsg) && $ultimaMsg['send_view'] == "" && $ultimaMsg['send_staff'] != session()->usr_id ? 1 : 0,
				'ultimaMsg'  => $ultimaMsg ? $ultimaMsg['message'] : null,
				'lastreply'  => $ultimaMsg ? date(get_dateTimeFormat(), strtotime($ultimaMsg['date'])) : lang2('n_a'),
				'details'    => $this->getDetails($chat['id']),
			];
		}

		return $data_chats;
	}

	function get_chat($id)
	{
		$data = [
			'replies' => $this->Chats_Model->get_chat_by_privileges($id),
			'details' => $this->getDetails($id)
		];

		$this->db->table('chatreplies')
			->where('(chat_staff1 = ' . $this->db->escape(session()->get('usr_id')) . ' AND chat_staff2 = ' . $this->db->escape($id) . ') OR (chat_staff2 = ' . $this->db->escape(session()->get('usr_id')) . ' AND chat_staff1 = ' . $this->db->escape($id) . ')')
			->where('send_view IS NULL')
			->update(['send_view' => '1']);


		return response()->setJSON($data);
	}


	function getDetails($id)
	{
		return $this->db->table('staff')->where('id', $id)->where('id_company', session()->id_company)->get()->getRowArray();
	}

	function chat($id)
	{
		if ($this->Privileges_Model->check_privilege('chats', 'all')) {
		} else if ($this->Privileges_Model->check_privilege('chats', 'own')) {
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('chats'));
		}


		$data['title'] = "Chat";
		$data['id'] = $id;
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		$data['all_staff'] = $this->Staff_Model->get_all_staff();

		$this->db->table('chatreplies')
			->where('send_staff !=', session()->get('usr_id'))
			->update(['send_view' => '1']);


		return view('chats/chat', $data);
	}

	function assign_staff($id)
	{
		if ($this->Privileges_Model->check_privilege('chats', 'edit')) {
			if (isset($_POST) && count($_POST) > 0) {
				$params = array(
					'send_staff' => request()->getPost('staff'),
				);

				$response = $this->db->table('chats')
					->where('id', $id)
					->update(['send_staff' => request()->getPost('staff')]);


				$this->db->table('notifications')->insert([
					'date' => date('Y-m-d H:i:s'),
					'detail' => session()->staffname . lang2('assigned') . ' ' . lang2('chat') . '-' . $id,
					'send_staff' => request()->getPost('staff'),
					'perres' => session()->staffavatar,
					'target' => base_url('chats/chat/' . $id)
				]);

				$user = $this->Staff_Model->get_staff(request()->getPost('staff'));
				$template = $this->Emails_Model->get_template('chat', 'chat_assigned');
				if ($template['status'] == 1) {
					$chat = $this->Chats_Model->get_chats($id);

					switch ($chat['priority']) {
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
					$message_vars = array(
						'{assigned}' => $chat['staffmembername'],
						//'{customer}' => $customer,
						'{name}' => session()->get('staffname'),
						'{email_signature}' => session()->get('email'),
						'{chat_subject}' => $chat['subject'],
						'{chat_message}' => $chat['message'],
						'{chat_priority}' => $priority,
						'{chat_department}' => $chat['department'],
					);
					$subject = strtr($template['subject'], $message_vars);
					$message = strtr($template['message'], $message_vars);
					$param = array(
						'from_name' => $template['from_name'],
						'email' => $chat['staffemail'],
						'subject' => $subject,
						'message' => $message,
						'created' => date("Y.m.d H:i:s")
					);
					if ($chat['staffemail']) {
						$this->db->table('email_queue')->insert($param);
					}
				}
				$data['name'] = $user['staffname'];
				$data['success'] = true;
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}
		return response()->setJSON($data);
	}

	function reply_suporte()
	{
		$assistant_id = "asst_sGhPuv5Ine8IAhogqYvZ8Mf2";
		//$assistant_id = "asst_k5YmD32NRnBgvNA7qkUN5KLE";
		$api_key = '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';

		/*
		
		//$url = 'https://api.openai.com/v1/chat/completions';
		$url = 'https://api.openai.com/v1/assistants/' . $assistant_id . '/completions';

		$data = [
			'model' => 'gpt-4',
			'messages' => [
				[
					'role' => 'system',
					'content' => 'Você é um assistente que ajuda com o CRM Next1.'
				],
				[
					'role' => 'user',
					'content' => request()->getPost('message')
				]
			],
			'temperature' => 0.7
		];

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $api_key,
			'OpenAI-Beta: assistants=v1',
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'Erro na requisição: ' . curl_error($ch);
		} else {
			$response_data = json_decode($response, true);
			print_r($response_data);
			exit;
			$mensaem = str_replace("\n", "<br>", $response_data['choices'][0]['message']['content']);
			return response()->setJSON(['response' => $mensaem]);
		}

		*/

		$api_url = 'https://api.openai.com/v1';
		$create_thread_url = $api_url . '/threads';
		$thread_res = $this->CreateThread($api_key, $create_thread_url);
		if ($thread_res) {
			$thread_id = $thread_res;
			$message = request()->getPost('message');
			$add_message_to_thread_url = $api_url . "/threads/$thread_id/messages";
			$add_msg_res = $this->addMessageToThread($api_key, $message, $add_message_to_thread_url);
			if ($add_msg_res) {
				$data_ass = ["assistant_id" => "$assistant_id"];
				$run_url = $api_url . '/threads/' . $thread_id . '/runs';
				$run_id = $this->runMessageThread($api_key, $data_ass, $run_url);
				if ($run_id) {
					$run_steps_url = "$api_url/threads/$thread_id/runs/$run_id/steps";
					$this->getRunSteps($api_key, $run_steps_url);
					$runs_sts_url = "$api_url/threads/$thread_id/runs/$run_id";
					$sts_chk = $this->getRunStatus($api_key, $runs_sts_url);
					//print_r($sts_chk);
					$ass_res_rul = "$api_url/threads/$thread_id/messages";
					sleep(10);
					$res = $this->getAssResponseMessages($api_key, $ass_res_rul);
					if ($res) {
						$data_d = json_decode($res, true);
						//print_r($data_d);
						//exit;
						if (isset($data_d['data'][0]) && isset($data_d['data'][0]['content'][0])){
							$mensaem = str_replace(["\n", "."], ["<br>", ".<br>"], $data_d['data'][0]['content'][0]['text']['value']);
						}else{
							$mensaem = "Não consegui processar, mande sua duvida novamente!";
						}

						return response()->setJSON(['response' => $mensaem]);
					} else {
						return false;
					}
				}
			}
		}
	}


	function CreateThread($api_key, $url)
	{
		$response = $this->curlAPIPost($api_key, $url);
		if ($response) {
			$thread_data = json_decode($response, true);
			if (isset($thread_data['id'])) {
				$thread_id = $thread_data['id'];
				//echo "THREAD ID:$thread_id<r>";
				return $thread_id;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function addMessageToThread($api_key, $message, $create_thread_url)
	{
		$add_thread_data = ['role' => 'user', 'content' => $message];
		$response = $this->curlAPIPost($api_key, $create_thread_url, $add_thread_data);
		if ($response) {
			$msg_data = json_decode($response, true);
			if (isset($msg_data['id'])) {
				return $msg_data['id'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	//run message thread
	function runMessageThread($api_key, $message, $run_thread_url)
	{
		$response = $this->curlAPIPost($api_key, $run_thread_url, $message);
		if ($response) {
			//echo "runMessageThread:$response";
			$run_data = json_decode($response, true);
			if (isset($run_data['id'])) {
				return $run_data['id'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function getRunSteps($api_key, $url)
	{
		$response = $this->getCurlCall($api_key, $url);
		return $response;
	}

	function getRunStatus($api_key, $url)
	{
		$response = $this->getCurlCall($api_key, $url);
		//echo "getRunStatus:$response<br>";
		return $response;
	}

	function getAssResponseMessages($api_key, $url)
	{
		$response = $this->getCurlCall($api_key, $url);
		return $response;
	}


	function curlAPIPost($api_key, $url, $data = '')
	{
		$headers = [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $api_key,
			'OpenAI-Beta: assistants=v2',
		];
		$curl = curl_init($url);
		if ($data != '') {
			$json_data = json_encode($data);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
		}
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo ("cURL Error #:" . $err);
			return false;
		} else {
			//	print_r($response);

			return $response;
		}
	}

	function getCurlCall($api_key, $url)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'OpenAI-Beta: assistants=v2',
				'Authorization: Bearer ' . $api_key,
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo ("cURL Error #:" . $err);
			return false;
		} else {
			return $response;
		}
	}


	function reply($id)
	{
		//if ($this->Privileges_Model->check_privilege('chats', 'edit')) {
		if (isset($_POST) && count($_POST) > 0) {
			$hasError = false;
			$data['message'] = '';
			if (request()->getPost('message') == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('message');
			}
			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}
			if (!$hasError) {
				if (isset($_FILES['file']) && $_FILES['file']['name'] != '') {
					$uploadPath = ROOTPATH . 'public/uploads/attachments/';
					$file = request()->getFile('file');
					$filename = preg_replace("/[^a-z0-9_\-.]/i", '', $file->getName());
					$file->move($uploadPath, $filename);
				} else {
					$filename = NULL;
				}
				$params = array(
					'send_staff' => session()->get('usr_id'),
					'chat_staff2' => session()->get('usr_id'),
					'chat_staff1' => $id,
					'date' => date(" Y-m-d h:i:s"),
					'name' => session()->get('staffname'),
					'message' => request()->getPost('message'),
					'attachment' => $filename,
				);
				$this->db->table('chatreplies')->insert($params);
				$staffname = session()->staffname;
				$loggedinuserid = session()->usr_id;
				$this->db->table('logs')->insert(array(
					'date' => date('Y-m-d H:i:s'),
					'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('replied') . ' <a href="chats/chat/' . $id . '"> ' . $id . '</a>'),
					'staff_id' => $loggedinuserid
				));
				$staffname = session()->staffname;
				$loggedinuserid = session()->usr_id;
				$staffavatar = session()->staffavatar;
				$this->db->table('notifications')->insert(array(
					'date' => date('Y-m-d H:i:s'),
					'detail' => ('' . $staffname . ' ' . lang2('replied') . ' ' . session()->get('usr_id') . ''),
					//	'contact_id' => $chat['contact_id'],
					'perres' => $staffavatar,
					'target' => '' . base_url('area/chats/chat/' . session()->get('usr_id') . '') . ''
				));


				$data['success'] = true;
				$data['message'] = lang2('chat') . ' ' . lang2('updatemessage');
				return response()->setJSON($data);
			}
		}
		//	} else {
		//		$data['success'] = false;
		//		$data['message'] = lang2('you_dont_have_permission');
		//		return response()->setJSON($data);
		//	}
	}

	function attachments($file)
	{
		if (is_file('./uploads/attachments/' . $file)) {
			$this->load->helper('file');
			$this->load->helper('download');
			$data = file_get_contents('./uploads/attachments/' . $file);
			force_download($file, $data);
		} else {
			session()->setFlashdata('ntf4', lang2('filenotexist'));
			return redirect()->to('chats/index');
		}
	}

	function markas()
	{
		if ($this->Privileges_Model->check_privilege('chats', 'edit')) {
			if (isset($_POST) && count($_POST) > 0) {
				$name = $_POST['name'];
				$params = array(
					'status_id' => $_POST['status_id'],
				);
				$data['success'] = true;
				$data['message'] = lang2('chat') . ' ' . lang2('markas') . ' ' . $name;
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}
		return response()->setJSON($data);
	}
}
