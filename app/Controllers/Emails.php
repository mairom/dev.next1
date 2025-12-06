<?php

namespace App\Controllers;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Emails extends BaseController
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

		$data['title'] = lang2('email_templates');

		return view('emails/index', $data);
	}



	function template($id)
	{
		$template = $this->Emails_Model->get_email_template($id);
		if ($template['id'] == $id) {
			$data['TEMPLATEID'] = $id;
			$data['title'] = (lang2($template['name'])) ? (lang2($template['name'])) : ($template['name']) . ' | ' . lang2('email_template');
			return view('emails/template', $data);
		}
	}

	function templateAss()
	{
		//$template = $this->Emails_Model->getTemplateAss($id);
		$data['title'] = "Assinatura de email";
		return view('emails/templateAss', $data);
	}

	function sendEmail()
	{

		$email = request()->getPost('email');
		$body = request()->getPost('message');
		$subject = request()->getPost('subject');
		$anexo = null;

		if (request()->getPost('anexo') != null && !empty(request()->getPost('anexo'))) {
			$anexo  = upload($_POST['anexo'], "anexos");
		}

		if (request()->getPost('attachment') != null && !empty(request()->getPost('attachment'))) {
			$anexo  = upload($_POST['attachment'], "anexos");
		}

		$var = [
			"{email}",
			"{empresa}",
			"{nome}",
			'<p><span><br data-mce-bogus="1"></span></p>',
			'<p><br data-mce-bogus="1"></p>', '<br>'
		];
		$valor = [
			$email,
			request()->getPost('company_name'),
			explode(" ", request()->getPost('customer'))[0],
			"", "", ""
		];

		$body = str_replace($var, $valor, $body);
		$subject = str_replace($var, $valor, $subject);

		//$body .= "<br> " . $assinatura['message'];

		$data = $this->Emails_Model->send_email($email, $email, $subject, $body, '', $anexo);

		if ($data) {
			$matriz = [
				'atividade' => '390',
				'data' => date('Y-m-d'),
				'horario' =>  date('H:i:s'),
				'duracao' => '00:00:00',
				'anotacoes' => "Envio de email manual <br> " . $body,
				'retorno' =>  date('Y-m-d'),
				'id_lead' => request()->getPost('id_lead'),
				'dt_entrada' => date('Y-m-d H:i:s'),
				'id_criador' => session()->usr_id,
			];
			$response = $this->db->table('leads_atv')->insert($matriz);

			if ($response) {
				$result['message'] = 'Enviado com sucesso!';
				$result['success'] = true;
			} else {
				$result['message'] = 'Erro ao salvar';
				$result['success'] = false;
			}
		} else {
			$result['message'] = 'Erro ao enviar';
			$result['success'] = false;
		}

		return response()->setJSON($result);
	}


	function send_email()
	{
		$data['TEMPLATEID'] = '0';
		$data['title'] = "Enviar email";
		return view('emails/send_email', $data);
	}

	function createEmail()
	{
		$data['TEMPLATEID'] = '0';
		$data['title'] = "Criar novo modelo de email";
		return view('emails/template', $data);
	}



	function create()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$name = request()->getPost('name');
			$from_name = request()->getPost('from_name');
			$subject = request()->getPost('subject');
			$message = request()->getPost('message');
			$relation = request()->getPost('relation');

			if ($name == '' || $from_name == '' || $subject == '' || $message == '' || $relation == '') {

				$data['message'] = lang2('invalidinput');
				$data['success'] = false;
				return response()->setJSON($data);
			} else {

				$params = array(

					'name' => $name,
					'from_name' => $from_name,
					'subject' => $subject,
					'message' => $message,
					'relation' => $relation,
					"id_company" => session()->id_company

				);

				$this->db->table('email_templates')->insert($params);
				$template_id = $this->db->insertID();

				if ($template_id) {
					$data['message'] = lang2('email_template') . ' ' . lang2('createmessage');
					$data['success'] = true;
					return response()->setJSON($data);
				}
			}
		}
	}



	function create_field()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$name = request()->getPost('name');

			$value = request()->getPost('value');

			$id = request()->getPost('id');

			if ($name == '' || $value == '' || $id == '') {

				$data['message'] = lang2('invalidinput');

				$data['success'] = false;

				return response()->setJSON($data);
			} else {

				$params = array(

					'template_id' => $id,

					'field_name' => $name,

					'field_value' => $value,
					"id_company" => session()->id_company

				);

				$this->db->table('email_template_fields')->insert($params);

				$template_id = $this->db->insertID();

				if ($template_id) {

					$data['message'] = lang2('email_template_field') . ' ' . lang2('createmessage');

					$data['success'] = true;

					return response()->setJSON($data);
				}
			}
		}
	}



	function get_email_templates()
	{

		$templates = $this->Emails_Model->get_email_templates();

		$data_templates = array();

		foreach ($templates as $template) {

			$data_templates[] = array(

				'id' => $template['id'],

				'from_name' => $template['from_name'],

				'name' => lang2($template['name']) ? lang2($template['name']) : $template['name'],

				'relation' => $template['relation'],

				'subject' => $template['subject'],

				'anexo' => $template['anexo'],

				'message' => $template['message'],

				'status' => $template['status']

			);
		}

		return response()->setJSON($data_templates);
	}



	function get_emails()
	{

		$emails = $this->Emails_Model->get_sent_emails();

		$data_emails = array();

		foreach ($emails as $email) {

			$data_email = @unserialize($email['email']);

			if ($data_email !== false) {

				$recipient = unserialize($email['email']);

				$recipient = implode(", ", $recipient);
			} else {

				$recipient = $email['email'];
			}

			$data_emails[] = array(

				'id' => $email['id'],

				'to' => $email['from_name'] ? $email['from_name'] : $email['email'],

				'email' => $recipient,

				'subject' => $email['subject'],

				'message' => $email['message'],

				'time' => date(get_dateTimeFormat(), strtotime($email['created'])),

				'attachment' => $email['attachments']

			);
		}

		return response()->setJSON($data_emails);
	}



	function template_fields($id)
	{
		$name = null;

		if ($id != '0') {
			$template = $this->Emails_Model->get_email_template($id);
			if ($template['name'] != null) {
				$name = $template['name'];
			} else {
				$name = '0';
			}
		} else {
			$name = '0';
		}

		if ($name != null) {
			$fields = $this->Emails_Model->template_fields($name);
			$data_templates = array();

			foreach ($fields as $field) {
				$data_templates[] = array(
					'name' => lang2($field['field_name']) ? lang2($field['field_name']) : $field['field_name'],
					'value' => $field['field_value']
				);
			}
			return response()->setJSON($data_templates);
		} else {
			echo "Erro";
		}
	}


	function get_email_template_ass($id)
	{
		$template = $this->Emails_Model->get_email_template_ass();
		if ($template) {

			$data_templates = array(
				'id' => $template['id'],
				'message' => ($template['message']),
			);
			return response()->setJSON($data_templates);
		} else {
			return response()->setJSON([
				'id' => 0,
				'message' => '',
			]);
		}
	}

	function get_email_template($id)
	{
		if ($id == 0) {
			return response()->setJSON([]);
		}
		$template = $this->Emails_Model->get_email_template($id);
		if ($template['id'] == $id) {
			$template = $this->Emails_Model->get_email_template($id);
			$status = $template['status'];
			if ($status == 1) {
				$status = true;
			} else {
				$status = false;
			}

			if (($template['name'] == 'expense_consultant') || ($template['name'] == 'expense_recurring') || ($template['name'] == 'expense_created') || ($template['name'] == 'new_file_uploaded_by_customer')) {
				$attachment = true;
			} else {
				$attachment = false;
			}

			$data_templates = array(
				'id' => $template['id'],
				'from_name' => $template['from_name'],
				'name' => lang2($template['name']) ? lang2($template['name']) : ($template['name']),
				'relation' => $template['relation'],
				'subject' => $template['subject'],
				'message' => ($template['message']),
				'attachment' => ($template['attachment'] == '1') ? true : false,
				'anexo' => $template['anexo'],
				'status' => $status,
				'isAttachment' => $attachment
			);

			return response()->setJSON($data_templates);
		}
	}

	function update_template_ass()
	{

		if ($this->Privileges_Model->check_privilege('emails', 'edit')) {
			$params = array(
				'message' => request()->getPost('message'),
			);



			if (
				$this->db->table('email_assinaturas')
				->where('id_company', session()->get('id_company'))
				->where('staff_id', session()->get('usr_id'))
				->get()
				->getRowArray() != null
			) {
				$response = $this->db->table('email_assinaturas')
					->where('id_company', session()->get('id_company'))
					->where('staff_id', session()->get('usr_id'))
					->update($params);
			} else {
				$params['id_company'] = session()->get('id_company');
				$params['staff_id'] = session()->get('usr_id');
				$response = $this->db->table('email_assinaturas')->insert($params);
			}


			if ($response) {
				$data['success'] = true;
				$data['message'] =  lang2('updatemessage');
				return response()->setJSON($data);
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


	function update_template($id)
	{
		if ($this->Privileges_Model->check_privilege('emails', 'edit')) {

			$params = array(
				'from_name' => request()->getPost('from_name'),
				'subject' => request()->getPost('subject'),
				'message' => request()->getPost('message'),
				'status' => request()->getPost('status'),
				'attachment' => request()->getPost('attachment'),
				"id_company" => session()->id_company
			);

			if (request()->getPost('relation')) {
				$params['relation'] = request()->getPost('relation');
			}

			if (request()->getPost('anexo') != null && !empty(request()->getPost('anexo'))) {
				$params['anexo']  = upload($_POST['anexo'], "anexos");
			}

			if ($id == '0') {
				$response = $this->db->table('email_templates')->insert($params);
			} else {
				$response = $this->Emails_Model->update_template($id, $params);
			}

			if ($response) {
				$data['success'] = true;
				$data['message'] = lang2('email_template') . ' ' . lang2('updatemessage');
				return response()->setJSON($data);
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


	function remove($id)
	{
		$this->db->table('email_templates')
			->where('id', $id)
			->delete();

		$data['message'] = lang2('Emails') . ' ' . lang2('deletemessage');
		$data['success'] = true;
		return response()->setJSON($data);
	}

	function removeAnexo($id)
	{
		$this->db->table('email_templates')
			->where('id', $id)
			->update(['anexo' => null]);

		$data['message'] = lang2('Anexo') . ' ' . lang2('deletemessage');
		$data['success'] = true;
		return response()->setJSON($data);
	}

	function move_to_trash($id)
	{
		if ($this->Privileges_Model->check_privilege('emails', 'edit')) {
			$this->db->table('email_queue')->delete(['id' => $id]);

			$data['message'] = lang2('email') . ' ' . lang2('deletemessage');
			$data['success'] = true;
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}
}
