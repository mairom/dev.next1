<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

use App\Models\Login_Model;

class Leads extends BaseController
{
	function __construct()
	{
		parent::loadModels();
		$path = request()->getUri()->getSegment(1);
		if (!$this->Privileges_Model->has_privilege($path)) {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('panel'));
		}
	}

	function index()
	{
		$data['title'] = lang2('leads');
		$data['tbs'] = $this->db->table('notifications')->where('markread', '0')->countAll();

		if ($this->Privileges_Model->check_privilege('leads', 'all')) {
			$data['tlh'] = $this->db->table('leads')->countAll();
			$data['tcl'] = $this->Report_Model->tcl();
			$data['tll'] = $this->Report_Model->tll();
			$data['tjl'] = $this->Report_Model->tjl();
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {
			$leads = $this->Leads_Model->get_all_leads_by_privileges(session()->usr_id);
			$data['tlh'] = sizeof($leads);
			$data['tcl'] = $this->Report_Model->tcl(session()->usr_id);
			$data['tll'] = $this->Report_Model->tll(session()->usr_id);
			$data['tjl'] = $this->Report_Model->tjl(session()->usr_id);
		}


		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		$login_model = new Login_Model();


		if (!$login_model->if_admin()) {

			$data['leads'] = $this->Leads_Model->get_all_leads_for_admin();
		} else {
			$data['leads'] = $this->Leads_Model->get_all_leads();
		};


		return view('leads/index', $data);
	}




	function forms()
	{

		$data['title'] = lang2('leadsforms');

		return view('forms/index', $data);
	}



	function form($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'all')) {

			$check = $this->Leads_Model->get_weblead($id);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {

			$check = $this->Leads_Model->get_weblead($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('leads'));
		}

		if ($check) {

			if (isset($id)) {

				$data['formId'] = $check['id'];

				$data['token'] = $check['token'];

				$data['submit'] = $check['submit_text'];

				$data['title'] = lang2('leadsforms');

				return view('forms/form', $data);
			} else {

				return redirect()->to(base_url('leads'));
			}
		} else {

			session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));

			return redirect()->to(base_url('leads'));
		}
	}



	function get_weblead($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'all')) {

			$form = $this->Leads_Model->get_weblead($id);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {

			$form = $this->Leads_Model->get_weblead($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('leads'));
		}

		if ($form) {

			if ($form['duplicate'] == '1') {

				$duplicate = true;
			} else {

				$duplicate = false;
			}

			if ($form['notification'] == '1') {

				$notification = true;
			} else {

				$notification = false;
			}

			if ($form['status'] == '1') {

				$status = true;
			} else {

				$status = false;
			}

			$data = array(

				'name' => $form['name'],

				'assigned_id' => $form['assigned_id'],

				'status_id' => $form['lead_status'],

				'source_id' => $form['lead_source'],

				'submit_text' => $form['submit_text'],

				'success_message' => $form['success_message'],

				'data' => $form['form_data'],

				'token' => $form['token'],

				'duplicate' => $duplicate,

				'notification' => $notification,

				'status' => $status

			);

			return response()->setJSON($data);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('leads'));
		}
	}



	function add_weblead_form()
	{

		if ($this->Privileges_Model->check_privilege('leads', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {
				$name = request()->getPost('name');
				$assigned_id = request()->getPost('assigned_id');
				$status_id = request()->getPost('status_id');
				$source_id = request()->getPost('source_id');
				$submit_text = request()->getPost('submit_text');
				$success_message = request()->getPost('success_message');

				$hasError = false;
				if (request()->getPost('duplicate') == 'true') {
					$duplicate = '1';
				} else {
					$duplicate = '0';
				}

				if (request()->getPost('notification') == 'true') {
					$notification = '1';
				} else {
					$notification = '0';
				}

				if (request()->getPost('status') == 'true') {
					$status = '1';
				} else {
					$status = '0';
				}

				if ($name == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
				} else if ($assigned_id == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned');
				} else if ($status_id == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('status');
				} else if ($source_id == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('source');
				} else if ($success_message == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('message_after_success');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$params = array(

						'token' => md5(uniqid()),

						'name' => $name,

						'assigned_id' => $assigned_id,

						'lead_status' => $status_id,

						'lead_source' => $source_id,

						'form_data' => '[{"label":"Name","type":"textfield","key":"lName","input":true,"tableView":true},{"label":"E-Mail","type":"email","key":"lEmail","input":true,"tableView":true},{"label":"Phone","type":"number","title":"Phone","key":"lPhone","input":true,"tableView":true},{"label":"Description","isUploadEnabled":false,"type":"textarea","key":"lDescription","input":true,"tableView":true},{"label":"Submit","type":"button","key":"lSubmit","input":true,"tableView":true}]',

						'submit_text' => $submit_text,

						'success_message' => $success_message,

						'duplicate' => $duplicate,

						'notification' => $notification,

						'created' => date('Y-m-d H:i:s'),

						'status' => $status,

					);

					$weblead_id = $this->Leads_Model->create_weblead_form($params);

					if ($weblead_id) {

						$data['success'] = true;

						$data['message'] = lang2('weblead') . ' ' . lang2('createmessage');

						$data['id'] = $weblead_id;

						return response()->setJSON($data);
					} else {

						$data['success'] = false;

						$data['message'] = lang2('errormessage');

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



	function save_weblead_form($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = request()->getPost('name');

				$assigned_id = request()->getPost('assigned_id');

				$status_id = request()->getPost('status_id');

				$source_id = request()->getPost('source_id');

				$submit_text = request()->getPost('submit_text');

				$success_message = request()->getPost('success_message');



				$hasError = false;

				if (request()->getPost('duplicate') == 'true') {

					$duplicate = '1';
				} else {

					$duplicate = '0';
				}

				if (request()->getPost('notification') == 'true') {

					$notification = '1';
				} else {

					$notification = '0';
				}

				if (request()->getPost('status') == 'true') {

					$status = '1';
				} else {

					$status = '0';
				}



				if ($name == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
				} else if ($assigned_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned');
				} else if ($status_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('status');
				} else if ($source_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('source');
				} else if ($success_message == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('message_after_success');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$params = array(

						'name' => $name,

						'assigned_id' => $assigned_id,

						'lead_status' => $status_id,

						'lead_source' => $source_id,

						'submit_text' => $submit_text,

						'success_message' => $success_message,

						'duplicate' => $duplicate,

						'notification' => $notification,

						'status' => $status,

					);

					$weblead_id = $this->Leads_Model->update_weblead_form($id, $params);

					if ($weblead_id) {

						$data['success'] = true;

						$data['message'] = lang2('weblead') . ' ' . lang2('updatemessage');

						$data['id'] = $weblead_id;

						return response()->setJSON($data);
					} else {

						$data['success'] = false;

						$data['message'] = lang2('errormessage');

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



	function save_weblead_components($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$weblead_id = $this->db->table('webleads')->where('id', $id)->update(['form_data' => request()->getPost('components', FALSE)]);

				if ($weblead_id) {

					$data['success'] = true;

					$data['message'] = lang2('weblead') . ' ' . lang2('updatemessage');

					return response()->setJSON($data);
				} else {

					$data['success'] = false;

					$data['message'] = lang2('errormessage');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function change_weblead_status($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				if (request()->getPost('status') == 'true') {

					$status = '1';
				} else {

					$status = '0';
				}

				$weblead_id = $this->db->table('webleads')->where('id', $id)->update(['status' => $status]);


				if ($weblead_id) {

					$data['success'] = true;

					$data['message'] = lang2('statuschanged');

					return response()->setJSON($data);
				} else {

					$data['success'] = false;

					$data['message'] = lang2('errormessage');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function webleads()
	{

		if ($this->Privileges_Model->check_privilege('leads', 'all')) {

			$leads = $this->Leads_Model->get_all_web_leads_for_admin();
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {

			$leads = $this->Leads_Model->get_all_web_leads();
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('leads'));
		}

		$data_leads = array();

		foreach ($leads as $lead) {

			if ($lead['formstatus'] == '1') {

				$status = true;
			} else {

				$status = false;
			}

			$data_leads[] = array(

				'id' => $lead['id'],

				'total_submissions' => $this->db->table('leads')
					->where('weblead', $lead['id'])
					->countAllResults(),


				'name' => $lead['name'],

				'statusname' => $lead['statusname'],

				'sourcename' => $lead['sourcename'],

				'assigned' => $lead['leadassigned'],

				'avatar' => $lead['assignedavatar'],

				'createddate' => $lead['created'],

				'status' => $status

			);
		};

		return response()->setJSON($data_leads);
	}



	function delete_web_form($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'delete')) {

			$form = $this->Leads_Model->get_weblead($id);

			if (isset($form['id'])) {

				$this->Leads_Model->delete_web_form($id);

				$data['success'] = true;

				$data['message'] = lang2('weblead') . ' ' . lang2('deletemessage');

				return response()->setJSON($data);
			} else {

				$data['success'] = false;

				$data['message'] = lang2('web_lead_not_found');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}


	function reorderList()
	{
		$i = 1;
		foreach (request()->getPost('order') as $row) {
			$this->db->table('leadsstatus')
				->where('id', $row)
				->update(['ordem' => $i]);
			$i++;
		}
	}

	function create()
	{

		$lead = [];
		if ($this->Privileges_Model->check_privilege('leads', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$title = request()->getPost('title');
				$date_contacted = request()->getPost('date_contacted');
				$name = request()->getPost('name');
				$email = request()->getPost('email');
				$country_id = request()->getPost('country_id');
				$assigned = request()->getPost('assigned');
				$status = request()->getPost('status');
				$source = request()->getPost('source');
				$description = request()->getPost('description');
				$data['message'] = '';
				$hasError = false;

				if ($assigned == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned') . ' ' . lang2('staff');
				} else if ($status == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('status');
				} else if ($source == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('source');
				}

				/*
							if ($assigned == '') {
								$hasError = true;
								$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned') . ' ' . lang2('staff');
							} else if ($status == '') {
								$hasError = true;
								$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('status');
							} else if ($source == '') {
								$hasError = true;
								$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('source');
							} else if ($email == '') {
								$hasError = true;
								$data['message'] = lang2('invalidmessage') . ' ' . lang2('email');
							} else if ($country_id == '') {
								$hasError = true;
								$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('country');
							} else if ($description == '') {
								$hasError = true;
								$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
							} else if ($date_contacted == '') {
								$hasError = true;
								$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('date_contacted');
							}
							*/

				if ($hasError) {
					$data['success'] = false;
					return response()->setJSON($data);
				}

				if (!$hasError) {
					$appconfig = get_appconfig();
					$leadParam = array(
						'created' => date('Y-m-d H:i:s'),
						'date_contacted' => !request()->getPost('date_contacted') ? request()->getPost('date_contacted') : date('Y-m-d H:i:s'),
						'type' => request()->getPost('type'),
						'name' => request()->getPost('name'),
						'title' => request()->getPost('title'),
						'company' => request()->getPost('company'),
						'description' => request()->getPost('description'),
						'country_id' => request()->getPost('country_id'),
						'zip' => request()->getPost('zip'),
						'city' => request()->getPost('city'),
						'closer' => request()->getPost('closer'),
						'state_id' => request()->getPost('state_id'),
						'temperatura' => request()->getPost('temperatura'),
						'address' => request()->getPost('address'),
						'email' => request()->getPost('email'),
						'website' => request()->getPost('website'),
						'tags' => request()->getPost('tags'),
						'phone' => request()->getPost('phone'),
						'assigned_id' => request()->getPost('assigned'),
						'source' => request()->getPost('source'),
						'public' => request()->getPost('public'),
						'dateassigned' => date('Y-m-d H:i:s'),
						'staff_id' => session()->get('usr_id'),
						'status' => request()->getPost('status'),
						'lead_status_id' => '1',
						'tp_pessoa' => request()->getPost('tp_pessoa'),

						'cnpj' => !empty(request()->getPost('cnpj')) ? request()->getPost('cnpj') : null,
						'web_site' => !empty(request()->getPost('web_site')) ? request()->getPost('web_site') : null,
						'cpf' => !empty(request()->getPost('cpf')) ? request()->getPost('cpf') : null,
						'setor_atividade' => !empty(request()->getPost('setor_atividade')) ? request()->getPost('setor_atividade') : null,
						'dt_nascimento' => !empty(request()->getPost('dt_nascimento')) ? request()->getPost('dt_nascimento') : null,
						'porte' => !empty(request()->getPost('porte')) ? request()->getPost('porte') : null,
						'instagram' => !empty(request()->getPost('instagram')) ? request()->getPost('instagram') : null,
						'facebook' => !empty(request()->getPost('facebook')) ? request()->getPost('facebook') : null,
						'linkedin' => !empty(request()->getPost('linkedin')) ? request()->getPost('linkedin') : null,
						'is_whatsApp' => !empty(request()->getPost('is_whatsApp')) ? request()->getPost('is_whatsApp') : null,
					);

					$lead_id = $this->Leads_Model->add_lead($leadParam);



					$this->db->table('logs')->insert([
						'date' => date('Y-m-d H:i:s'),
						'detail' => '<a href="' . base_url('staff/staffmember/' . session()->get('usr_id')) . '"> ' .
							session()->get('staffname') . '</a> Criou um novo lead, <a href="' . base_url('leads/lead/' . $lead_id) . '">' . $lead_id . '</a>.',
						'staff_id' => session()->get('usr_id'),
					]);

					// Custom Field Post

					if (request()->getPost('custom_fields')) {
						$custom_fields = array(
							'custom_fields' => request()->getPost('custom_fields')
						);
						$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'lead', $lead_id);
					}

					// Custom Field Post

					$this->db->table('tags')->insert(array(
						'relation_type' => 'lead',
						'relation' => $lead_id,
						'data' => request()->getPost('tags')
					));



					$template = $this->Emails_Model->get_template('lead', 'lead_assigned');

					if ($template['status'] == 1) {

						$lead = $this->Leads_Model->get_lead($lead_id);
						$lead_url = '' . base_url('leads/lead/' . $lead_id . '') . '';
						$message_vars = array(
							'{lead_name}' => request()->getPost('name'),
							'{lead_email}' => request()->getPost('email'),
							'{lead_url}' => $lead_url,
							'{lead_assigned_staff}' => $lead['leadassigned'],
							'{name}' => session()->get('staffname'),
							'{email_signature}' => session()->get('email'),

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);



						$param = array(
							'from_name' => $template['from_name'],
							'email' => $lead['staffemail'],
							'subject' => $subject,
							'message' => $message,
							'created' => date("Y.m.d H:i:s"),
							'staff_id' => session()->get('usr_id'),
						);
						if ($lead['staffemail']) {
							$this->db->table('email_queue')->insert($param);
						}
					}

					$template = $this->Emails_Model->get_template('lead', 'lead_submitted');

					if ($template['status'] == 1) {

						$lead = $this->Leads_Model->get_lead($lead_id);

						$message_vars = array(
							'{lead_name}' => request()->getPost('name'),
							'{lead_email}' => request()->getPost('email'),
							'{lead_assigned_staff}' => $lead['leadassigned'],
							'{email_signature}' => $template['from_name'],
						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);

						$param = array(
							'from_name' => $template['from_name'],
							'email' => $lead['staffemail'],
							'subject' => $subject,
							'message' => $message,
							'created' => date("Y.m.d H:i:s"),
							'staff_id' => session()->get('usr_id'),
						);

						if ($lead['staffemail']) {

							$this->db->table('email_queue')->insert($param);
						}
					}

					$data['success'] = true;

					$data['id'] = $lead_id;

					if ($appconfig['lead_series']) {
						$lead_number = $appconfig['lead_series'];
						$lead_number = $lead_number + 1;
						$this->Settings_Model->increment_series('lead_series', $lead_number);
					}


					$aviso = $this->db->table('avisos')
						->where('id_company', session()->get('id_company'))
						->where('tipo', '3')
						->where('is_ativo', '1')
						->get()->getRowArray();


					if ($aviso) {
						//if (in_array(session()->usr_id, explode(",", $aviso->funcionarios))) {
						if ($aviso['opcaoAlerta'] == "2" || $aviso['opcaoAlerta'] == "3") {

							$assinatura = $this->db->table('email_assinaturas')
								->where('id_company', '1')
								->get()
								->getRowArray();


							foreach (explode(",", $aviso['emails']) as $email) {
								$body = $aviso['menssagem'];
								$body .= "<br> " . $assinatura['message'];

								$Subject = "Aviso de novo lead!";
								$sendEmail = $this->Emails_Model->send_email2($email, $Subject, $body, $aviso['anexo']);
							}
						}

						if ($aviso['opcaoAlerta'] == "1" || $aviso['opcaoAlerta'] == "3") {
							foreach (explode(",", $aviso['funcionarios']) as $funcionario) {
								//if ($funcionario != session()->usr_id) {
								$this->db->table('notifications')->insert([
									'staff_id' => $funcionario,
									'staff_id_acao' => session()->get('usr_id'),
									'id_aviso' => $aviso['id_aviso'],
									'date' => date('Y-m-d H:i:s'),
									'detail' => $aviso['menssagem'],
									'markread' => 0
								]);

								//	}
							}
						}
						//}
					}

					$lead['diasAtivo'] = $this->diasDatas($lead['created']);
					$data['lead'] = $lead;
					$data['message'] = lang2('lead') . ' ' . lang2('createmessage');
					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}


	function teste2()
	{
		$assinatura = $this->db->table('email_assinaturas')
			->where('id_company', session()->id_company)
			->get()
			->getRowArray();

		$body = "Teste";
		$body .= "<br> " . $assinatura['message'];

		$Subject = "Aviso de novo lead!";
		$sendEmail = $this->Emails_Model->send_email2("mairom99@gmail.com", $Subject, $body);
	}
	function updateFluxo()
	{

		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {

			$this->Leads_Model->update_lead(request()->getPost('id_lead'), ['id_fluxo' => request()->getPost('id_fluxo')]);

			$data['message'] = lang2('lead') . ' ' . lang2('updatemessage');
			$data['success'] = true;
			return response()->setJSON($data);
		}
	}
	function updateTemp($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'all')) {
			$data['lead'] = $this->Leads_Model->get_lead_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {
			$data['lead'] = $this->Leads_Model->get_lead_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}

		if ($data['lead']) {
			if ($this->Privileges_Model->check_privilege('leads', 'edit')) {
				if (isset($data['lead']['id'])) {
					if (isset($_POST) && count($_POST) > 0) {

						$params = array(
							'temperatura' => request()->getPost('temperatura'),
						);
						$this->Leads_Model->update_lead($id, $params);

						$data['message'] = lang2('lead') . ' ' . lang2('updatemessage');
						$data['success'] = true;
						return response()->setJSON($data);
					}
				}
			}
		}
	}

	function updateQual($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'all')) {
			$data['lead'] = $this->Leads_Model->get_lead_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {
			$data['lead'] = $this->Leads_Model->get_lead_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}

		if ($data['lead']) {
			if ($this->Privileges_Model->check_privilege('leads', 'edit')) {
				if (isset($data['lead']['id'])) {
					if (isset($_POST) && count($_POST) > 0) {

						$params = array(
							'qualificacao' => request()->getPost('qualificacao'),
						);
						$this->Leads_Model->update_lead($id, $params);

						$data['message'] = lang2('lead') . ' ' . lang2('updatemessage');
						$data['success'] = true;
						return response()->setJSON($data);
					}
				}
			}
		}
	}

	function update($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'all')) {
			$data['lead'] = $this->Leads_Model->get_lead_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {
			$data['lead'] = $this->Leads_Model->get_lead_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}

		if ($data['lead']) {
			if ($this->Privileges_Model->check_privilege('leads', 'edit')) {
				if (isset($data['lead']['id'])) {
					if (isset($_POST) && count($_POST) > 0) {

						$data['message'] = '';
						$hasError = false;

						/*	$title = request()->getPost('title');
										  $name = request()->getPost('name');
										  $email = request()->getPost('email');
										  $country_id = request()->getPost('country_id');
										  $assigned = request()->getPost('assigned_id');
										  $status = request()->getPost('status');
										  $source = request()->getPost('source');
										  $description = request()->getPost('description');


										  if ($name == '') {
											  $hasError = true;
											  $data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
										  } else if ($assigned == '') {
											  $hasError = true;
											  $data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned') . ' ' . lang2('staff');
										  } else if ($status == '') {
											  $hasError = true;
											  $data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('status');
										  } else if ($source == '') {
											  $hasError = true;
											  $data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('source');
										  } else if ($email == '') {
											  $hasError = true;
											  $data['message'] = lang2('invalidmessage') . ' ' . lang2('email');
										  } else if ($country_id == '') {
											  $hasError = true;
											  $data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('country');
										  } else if ($description == '') {
											  $hasError = true;
											  $data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
										  }

										  if ($hasError) {
											  $data['success'] = false;
											  return response()->setJSON($data);
										  }*/

						if (!$hasError) {
							$params = array(
								'date_contacted' => request()->getPost('date_contacted'),
								'type' => request()->getPost('type'),
								'name' => request()->getPost('name'),
								'title' => request()->getPost('title'),
								'company' => request()->getPost('company'),
								'description' => request()->getPost('description'),
								'country_id' => request()->getPost('country_id'),
								'zip' => request()->getPost('zip'),
								'city' => request()->getPost('city'),
								'closer' => request()->getPost('closer'),
								'state_id' => request()->getPost('state_id'),
								'address' => request()->getPost('address'),
								'email' => request()->getPost('email'),
								'website' => request()->getPost('website'),
								'phone' => request()->getPost('phone'),
								'assigned_id' => request()->getPost('assigned_id'),
								'source' => request()->getPost('source'),
								'public' => request()->getPost('public'),
								'staff_id' => session()->get('usr_id'),
								'status' => request()->getPost('status'),
								'temperatura' => request()->getPost('temperatura'),
								'tags' => request()->getPost('tags'),
								'tp_pessoa' => request()->getPost('tp_pessoa'),

								'cnpj' => !empty(request()->getPost('cnpj')) ? request()->getPost('cnpj') : null,
								'web_site' => !empty(request()->getPost('web_site')) ? request()->getPost('web_site') : null,
								'cpf' => !empty(request()->getPost('cpf')) ? request()->getPost('cpf') : null,
								'setor_atividade' => !empty(request()->getPost('setor_atividade')) ? request()->getPost('setor_atividade') : null,
								'dt_nascimento' => !empty(request()->getPost('dt_nascimento')) ? request()->getPost('dt_nascimento') : null,
								'porte' => !empty(request()->getPost('porte')) ? request()->getPost('porte') : null,
								'instagram' => !empty(request()->getPost('instagram')) ? request()->getPost('instagram') : null,
								'facebook' => !empty(request()->getPost('facebook')) ? request()->getPost('facebook') : null,
								'linkedin' => !empty(request()->getPost('linkedin')) ? request()->getPost('linkedin') : null,
								'is_whatsApp' => !empty(request()->getPost('is_whatsApp')) ? request()->getPost('is_whatsApp') : null,
							);

							if (request()->getPost('lost')) {
								$params['lost'] = request()->getPost('lost');
							}

							if (request()->getPost('junk')) {
								$params['junk'] = request()->getPost('junk');
							}

							$this->Leads_Model->update_lead($id, $params);

							$this->db->table('logs')->insert(array(
								'date' => date('Y-m-d H:i:s'),
								'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
									session()->staffname . '</a> Autualizou o lead <a href="' . base_url() .
									'leads/lead/' . $id . '">' . $id . '</a>.',
								'staff_id' => session()->usr_id,
							));

							// Custom Field Post
							if (request()->getPost('custom_fields')) {
								$custom_fields = array(
									'custom_fields' => request()->getPost('custom_fields')
								);
								$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'lead', $id);
							}
							$data['message'] = lang2('lead') . ' ' . lang2('updatemessage');
							$data['success'] = true;
							return response()->setJSON($data);
						}
					} else {
						return redirect()->to(base_url('leads'));
					}
				} else {
					show_error('The lead you are trying to update does not exist.');
				}
			} else {
				$data['message'] = lang2('you_dont_have_permission');
				$data['success'] = false;
				return response()->setJSON($data);
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}
	}



	function findLead($phone)
	{

		$subquery = $this->db->table('leads_contatos')
			->select('id_lead, email, nm_contato, telefone')
			->where('ctt_principal', '1')
			->orderBy('id_lead_contato', 'asc')
			->groupBy('id_lead');

		$lead = $this->db->table('leads l')
			->select('l.id AS id_lead')
			->where('staff.id_company', session()->id_company)
			->groupStart()
			->where('l.phone', $phone)
			->orWhere('l.phone', preg_replace('/^55/', '', $phone))
			->orWhere('telefone', $phone)
			->orWhere('telefone', preg_replace('/^55/', '', $phone))
			->groupEnd()
			->join('staff', 'l.assigned_id = staff.id', 'inner')
			->join("({$subquery->getCompiledSelect()}) lc", 'lc.id_lead = l.id', 'left')
			->get()
			->getRowArray();

		//echo $lead->getCompiledSelect();
		//exit;

		if ($lead) {
			return redirect()->to(base_url('leads/lead/' . $lead['id_lead']));
		} else {
			echo "lead não encontrado";
		}
	}

	function lead($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'all')) {
			$lead = $this->Leads_Model->get_lead_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {
			$lead = $this->Leads_Model->get_lead_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}

		if ($lead) {
			$data['title'] = $lead['leadname'];
			$data['lead'] = $lead;
			$data['id_user'] = session()->usr_id;
			return view('leads/lead', $data);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}
	}



	function convert($id)
	{
		if ($this->Privileges_Model->check_privilege('customers', 'create')) {
			$lead = $this->Leads_Model->get_lead($id);
			$settings = $this->Settings_Model->get_settings_ciuis();

			if ($lead['dateconverted'] != null) {
				$data['success'] = false;
				$data['message'] = lang2('leadalreadyconverted');

				return response()->setJSON($data);
			} else {
				$params = array(
					'staff_id' => $lead['staff_id'],
					'company' => $lead['company'],
					'type' => $lead['type'],
					'id_lead' => $lead['id'],
					'namesurname' => $lead['company'],
					'created' => date('Y-m-d H:i:s'),
					'address' => $lead['address'],
					'zipcode' => $lead['zip'],
					'country_id' => $lead['country_id'],
					'state' => $lead['state'],
					'assigned_id' => $lead['assigned_id'],
					'city' => $lead['city'],
					'phone' => $lead['leadphone'],
					'email' => $lead['leadmail'],
					'web' => $lead['website'],
					'source_id' => $lead['source'],
					'closer' => $lead['closer'],
					'funil_origem' => $lead['id_list'],
					'id_company' => session()->id_company
				);

				$this->db->table('customers')->insert($params);
				$customer = $this->db->insertID();

				$atividades = $this->Leads_Model->get_atividades_by_user($id);
				foreach ($atividades as $atividade) {
					$matriz = [
						'atividade' => $atividade['atividade'],
						'data' => $atividade['data'],
						'horario' => $atividade['horario'],
						'duracao' => $atividade['duracao'],
						'anotacoes' => $atividade['anotacoes'],
						'retorno' => $atividade['retorno'],
						'dt_entrada' => $atividade['dt_entrada'],
						'id_criador' => $atividade['id_criador'],
						'id_customer' => $customer
					];
					$response = $this->db->table('leads_atv')->insert($matriz);
				}


				$contatos = $this->Leads_Model->getContatosLead($id);
				foreach ($contatos as $contato) {
					$matriz2 = [
						'customer_id' => $customer,
						'name' => $contato['nm_contato'],
						'dt_nascimento' => $contato['dt_aniversario'],
						'phone' => $contato['telefone'],
						'email' => $contato['email'],
						'username' => $contato['email'],
						'language' => '',
						'linkedin' => $contato['linkedin'],
						'primary' => '0',
						'admin' => '0',
						'inactive' => '0',
					];
					$this->db->table('contacts')->insert($matriz2);
				}

				$this->db->table('logs')->insert([
					'date' => date('Y-m-d H:i:s'),
					'detail' => sprintf(lang2('coverttocustomer'), session()->staffname, get_number('leads', $lead['id'], 'lead', 'lead')),
					'staff_id' => session()->usr_id,
					'customer_id' => $customer,
				]);

				$this->db->table('leads')->where('id', $id)->update([
					'dateconverted' => date('Y-m-d H:i:s')
				]);

				$this->db->table('proposals')->where('relation', $id)->where('relation_type', 'lead')->update([
					'relation' => $customer,
					'relation_type' => 'customer'
				]);

				$data['id'] = $customer;
				$data['success'] = true;

				$aviso = $this->db->table('avisos')
					->where('id_company', session()->id_company)
					->where('tipo', '2')
					->where('is_ativo', '1')
					->get()
					->getRowArray();


				if ($aviso) {
					//if (in_array(session()->usr_id, explode(",", $aviso->funcionarios))) {
					if ($aviso['opcaoAlerta'] == "2" || $aviso['opcaoAlerta'] == "3") {

						$assinatura = $this->db->table('email_assinaturas')->where('id_company', '1')->get()->getRowArray();

						foreach (explode(",", $aviso['emails']) as $email) {
							$body = $aviso['menssagem'];
							$body .= "<br> " . $assinatura['message'];

							$Subject = "Aviso de novo lead convertido!";
							$sendEmail = $this->Emails_Model->send_email2($email, $Subject, $body, $aviso['anexo']);
						}
					}

					if ($aviso['opcaoAlerta'] == "1" || $aviso['opcaoAlerta'] == "3") {
						foreach (explode(",", $aviso['funcionarios']) as $funcionario) {
							//	if ($funcionario != session()->usr_id) {
							$this->db->table('notifications')->insert([
								'staff_id' => $funcionario,
								'staff_id_acao' => session()->get('usr_id'),
								'id_aviso' => $aviso['id_aviso'],
								'date' => date('Y-m-d H:i:s'),
								'detail' => $aviso['menssagem'],
								'markread' => 0
							]);

							//	}
						}
					}
					//}
				}

				return response()->setJSON($data);
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}



	function add_status()
	{
		if ($this->Privileges_Model->check_privilege('leads', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$params = array(
					'name' => request()->getPost('name'),
					'id_list' => request()->getPost('id_list'),
				);
				$status = $this->Leads_Model->add_status($params);
				$this->db->table('logs')->insert(array(
					'date' => date('Y-m-d H:i:s'),
					'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
						session()->staffname . '</a> Criou um novo status, ' . request()->getPost('name'),
					'staff_id' => session()->usr_id,
				));

				$data['message'] = lang2('status') . ' ' . lang2('addmessage');
				$data['success'] = true;
			} else {
				$data['success'] = false;
				$data['message'] = "false";
				return redirect()->to(base_url('leads'));
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}

	function add_funil()
	{
		if ($this->Privileges_Model->check_privilege('leads', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$params = array(
					'nm_list' => request()->getPost('name'),
					"id_company" => session()->id_company
				);
				$status = $this->Leads_Model->add_funil($params);

				$this->db->table('logs')->insert(array(
					'date' => date('Y-m-d H:i:s'),
					'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
						session()->staffname . '</a> Criou um novo funil, ' . request()->getPost('name'),
					'staff_id' => session()->usr_id,
				));

				$data['message'] = lang2('status') . ' ' . lang2('addmessage');
				$data['success'] = true;
			} else {
				$data['success'] = false;
				$data['message'] = "false";
				return redirect()->to(base_url('leads'));
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}

	function autenticaPh3a()
	{
		$url = 'https://api.ph3a.com.br/DataBusca/api/Account/Login';
		//$data = ["UserName" => "93976b5f-3678-293d-d9d6-b20964349d22"];
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
		//	print_r($r);
		//	exit;
		return $r->data->Token;
	}
	public function UpdateAtivo()
	{
		$leads_data = request()->getPost('leads_data');
		$id_lead = request()->getPost('id_lead');

		//print_r($leads_data);
		//exit;

		array_walk_recursive($leads_data, function (&$value) {
			if ($value === 'true') {
				$value = true;
			} elseif ($value === 'false') {
				$value = false;
			}
		});

		$this->db->table('leads_data')
			->where('id_lead', $id_lead)
			->update(['value' => json_encode($leads_data)]);

		return response()->setJSON(['success' => true]);
	}
	public function buscaDadosPh3a($documentVar = null, $id_leadVar = null)
	{
		$creditos = $this->db->table('companies')
			->select('creditos_enriquecimento')
			->where('id_company', session()->get('id_company'))
			->get()
			->getRow()
			->creditos_enriquecimento;

		$result = "";
		$document = $documentVar != null ? $documentVar : request()->getPost('document');
		$id_lead = $id_leadVar != null ? $id_leadVar : request()->getPost('id_lead');

		if ($creditos >= 0) {
			$lead_data = $this->db->table('leads_data')
				->select('*')
				->where('cnpj', $document)
				->get()
				->getRow();

			if ($lead_data) {
				$r = $lead_data->value;
				$data['success'] = true;
			} else {
				$q = $this->db->table('tokens')
					->select('*')
					->where('type', 'ph3a')
					->get()
					->getRow();
				if (date('Y-m-d H:i:s', strtotime($q->created_at . '+20 minutes')) <= date('Y-m-d H:i:s')) {
					$token = $this->autenticaPh3a();
					$this->db->table('tokens')
						->where('type', 'ph3a')
						->update(['token' => $token, 'created_at' => date('Y-m-d H:i:s')]);
				} else {
					$token = $q->token;
				}

				$url = 'https://api.ph3a.com.br/DataBusca/data';
				$data = [
					"Document" => $document
				];
				$postdata = json_encode($data);
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Token: ' . $token]);
				$result = curl_exec($ch);
				curl_close($ch);

				$data['success'] = ($result) ? true : false;
				$r = json_encode(json_decode($result)->Data);

				//echo $r;
				//exit;
			}

			$this->db->table('companies')
				->where('id_company', session()->get('id_company'))
				->update(['creditos_enriquecimento' => ($creditos - 1)]);

			$this->db->table('leads')
				->where('id', $id_lead)
				->update(['ultimaBusca' => date('Y-m-d H:i:s')]);

			$this->db->table('leads_data')->insert([
				'id_lead' => $id_lead,
				'cnpj' => $document,
				'value' => $r,
				'created_at' => date('Y-m-d H:i:s'),
			]);
		} else {
			$data['success'] = false;
		}

		// Adiciona os dados da resposta
		if ($documentVar == null) {
			$data['data'] = json_decode($result, true);
			return response()->setJSON($data);
		} else {
			return json_decode($result, true);
		}
	}




	function update_status($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {
			$data['statuses'] = $this->Leads_Model->get_status($id);

			if (isset($data['statuses']['id'])) {
				if (isset($_POST) && count($_POST) > 0) {
					$params = array(
						'name' => request()->getPost('name'),
						'color' => request()->getPost('color'),
					);
					$this->Leads_Model->update_status($id, $params);

					$this->db->table('logs')->insert(array(
						'date' => date('Y-m-d H:i:s'),
						'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
							session()->staffname . '</a> Atualizou o status, ' . request()->getPost('name'),
						'staff_id' => session()->usr_id,
					));

					$data['message'] = lang2('lead') . ' ' . lang2('status') . ' ' . lang2('updatemessage');
					$data['success'] = true;
					return response()->setJSON($data);
				}
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}

	function update_funil($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {
				$params = array(
					'nm_list' => request()->getPost('name'),
				);
				$this->Leads_Model->update_funil($id, $params);

				$this->db->table('logs')->insert(array(
					'date' => date('Y-m-d H:i:s'),
					'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
						session()->staffname . '</a> Atualizou o funil, ' . request()->getPost('name'),
					'staff_id' => session()->usr_id,
				));

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
	function get_fluxo($id_lead, $id_funil)
	{
		$fluxos = $this->db->table('fluxos')
			->where('id_company', session()->id_company)
			->where(" FIND_IN_SET(  $id_funil, funil) ")
			->orderBy('id_fluxo', 'asc')
			->get()
			->getResultArray();

		//	echo $fluxos->getCompiledSelect();
		//	exit;

		foreach ($fluxos as $index => $fluxo) {
			$etapas = $this->db->table('fluxos_etapas')
				->where('id_fluxo', $fluxo['id_fluxo'])
				->where('id_etapa_pai IS NULL')
				->get()
				->getResultArray();

			$etapasArr = $this->getFluxosEtapas($etapas, $id_lead);

			$fluxos[$index]['etapas'] = $etapasArr;
		}

		return response()->setJSON(['fluxos' => $fluxos]);
	}

	function get_sales_flow_automatico($id_lead)
	{
		// Buscar o lead para pegar o funil
		$lead = $this->db->table('leads')
			->where('id', $id_lead)
			->get()
			->getRowArray();

		if (!$lead) {
			return response()->setJSON([]);
		}

		// Buscar os flows disponíveis para este funil
		$flows = $this->db->table('fluxos')
			->where('id_company', session()->id_company)
			->where(" FIND_IN_SET(" . $lead['id_list'] . ", funil) ")
			->orderBy('id_fluxo', 'asc')
			->get()
			->getResultArray();

		$result = [];
		foreach ($flows as $flow) {
			// Verificar se o flow está ativo para este lead
			$flowLead = $this->db->table('lead_sales_flow_automatico')
				->where('id_lead', $id_lead)
				->where('id_flow', $flow['id_fluxo'])
				->get()
				->getRowArray();

			$result[] = [
				'id_flow' => $flow['id_fluxo'],
				'name_flow' => $flow['name'],
				'etapaAtual' => $flowLead['etapa_atual'] ?? 'Não iniciado',
				'ultima_verificacao' => $flowLead['ultima_verificacao'] ?? '-',
				'ativo' => $flowLead['ativo'] ?? '0'
			];
		}

		return response()->setJSON($result);
	}

	function UpdateAdicionadoFlow()
	{
		$id_lead = request()->getPost('id_lead');
		$id_flow = request()->getPost('id_flow');
		$adicionadoFlow = request()->getPost('adicionadoFlow');

		if (!$id_lead || !$id_flow) {
			return response()->setJSON([
				'success' => false,
				'message' => 'Dados inválidos'
			]);
		}

		$ativo = isset($adicionadoFlow[$id_flow]) && $adicionadoFlow[$id_flow] ? '1' : '0';

		// Verificar se já existe registro
		$existing = $this->db->table('lead_sales_flow_automatico')
			->where('id_lead', $id_lead)
			->where('id_flow', $id_flow)
			->get()
			->getRowArray();

		if ($existing) {
			// Atualizar
			$this->db->table('lead_sales_flow_automatico')
				->where('id_lead', $id_lead)
				->where('id_flow', $id_flow)
				->update([
					'ativo' => $ativo,
					'updated_at' => date('Y-m-d H:i:s')
				]);
		} else {
			// Inserir
			$this->db->table('lead_sales_flow_automatico')->insert([
				'id_lead' => $id_lead,
				'id_flow' => $id_flow,
				'ativo' => $ativo,
				'etapa_atual' => 'Não iniciado',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}

		$message = $ativo == '1' ? 'Sales Flow ativado com sucesso!' : 'Sales Flow desativado com sucesso!';

		return response()->setJSON([
			'success' => true,
			'message' => $message
		]);
	}

	function UpdateSalesFlowAtivo()
	{
		$id_lead = request()->getPost('id_lead');
		$id_flow = request()->getPost('id_flow');
		$ativo = request()->getPost('ativo');

		if (!$id_lead) {
			return response()->setJSON([
				'success' => false,
				'message' => 'Lead inválido'
			]);
		}

		if (!$id_flow) {
			return response()->setJSON([
				'success' => false,
				'message' => 'Flow não informado'
			]);
		}

		// Normalizar o valor de ativo
		$ativoNormalizado = ($ativo === '1' || $ativo === 1 || $ativo === true || $ativo === 'true') ? '1' : '0';

		// Verificar se já existe registro
		$existing = $this->db->table('lead_sales_flow_automatico')
			->where('id_lead', $id_lead)
			->where('id_flow', $id_flow)
			->get()
			->getRowArray();

		if ($existing) {
			// Atualizar
			$this->db->table('lead_sales_flow_automatico')
				->where('id_lead', $id_lead)
				->where('id_flow', $id_flow)
				->update([
					'ativo' => $ativoNormalizado,
					'updated_at' => date('Y-m-d H:i:s')
				]);
		} else {
			// Inserir
			$this->db->table('lead_sales_flow_automatico')->insert([
				'id_lead' => $id_lead,
				'id_flow' => $id_flow,
				'ativo' => $ativoNormalizado,
				'etapa_atual' => 'Não iniciado',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}

		$message = $ativoNormalizado == '1' 
			? 'Sales Flow Automático ativado! Os emails serão enviados automaticamente.' 
			: 'Sales Flow Automático desativado. Os emails não serão mais enviados automaticamente.';

		return response()->setJSON([
			'success' => true,
			'message' => $message,
			'ativo' => $ativoNormalizado
		]);
	}

	function check_flow_active($id_lead, $id_flow)
	{
		// Verificar se o flow específico está ativo para este lead
		$flowAtivo = $this->db->table('lead_sales_flow_automatico')
			->where('id_lead', $id_lead)
			->where('id_flow', $id_flow)
			->where('ativo', '1')
			->get()
			->getRowArray();

		return response()->setJSON([
			'ativo' => $flowAtivo ? true : false
		]);
	}

	function get_fluxo_funil($id_funil)
	{
		$fluxos = $this->db->table('fluxos')
			->where('id_company', session()->id_company)
			->where('funil', $id_funil)
			->orderBy('id_fluxo', 'asc')
			->get()
			->getResultArray();

		foreach ($fluxos as $index => $fluxo) {
			$etapas = $this->db->table('fluxos_etapas')
				->where('id_fluxo', $fluxo['id_fluxo'])
				->where('id_etapa_pai IS NULL')
				->get()
				->getResultArray();

			$etapasArr = $this->getFluxosEtapas($etapas);

			$fluxos[$index]['etapas'] = $etapasArr;
		}

		return response()->setJSON(['fluxos' => $fluxos]);
	}

	function getFluxosEtapas($etapas, $id_lead = null)
	{
		foreach ($etapas as $i => $etapa) {
			$etapas[$i]['dias'] = intval($etapa['dias']);
			$etapas[$i]['atividade'] = $this->db->table('leads_atv_select')
				->where('id_atv', $etapa['id_atividade'])
				->get()
				->getRowArray();

			if ($id_lead != null) {
				$etapas[$i]['feito'] = $this->db->table('leads_atv')
					->where('id_etapa_flow', $etapa['id_etapa'])
					->where('id_lead', $id_lead)
					->get()
					->getRowArray() ? 1 : 0;
			}

			$etapasFilho = $this->db->table('fluxos_etapas')
				->where('id_etapa_pai', $etapa['id_etapa'])
				->get()
				->getResultArray();

			if (count($etapasFilho) > 0) {
				$etapas[$i]['etapas'] = $this->getFluxosEtapas($etapasFilho, $id_lead);
			}
		}
		return $etapas;
	}


	function remove_funil($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'delete')) {
			$this->db->table('leads_list')->delete(['id_list' => $id]);
			$this->db->table('leadsstatus')->delete(['id_list' => $id]);

			$this->db->table('logs')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
					session()->staffname . '</a> removeu o funil, ' . $id,
				'staff_id' => session()->usr_id,
			]);

			return response()->setJSON([
				'message' => lang2('lead') . ' Funil ' . lang2('deletemessage'),
				'success' => true,
			]);
		} else {
			return response()->setJSON([
				'success' => false,
				'message' => lang2('you_dont_have_permission'),
			]);
		}
	}

	function remove_status($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'delete')) {
			$lead = $this->Leads_Model->get_status($id);
			if (isset($lead['id'])) {
				if ($this->Leads_Model->check_statuses($id) === 0) {
					$this->Leads_Model->delete_status($id);
					$this->db->table('logs')->insert([
						'date' => date('Y-m-d H:i:s'),
						'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
							session()->staffname . '</a> removeu o status, ' . $id,
						'staff_id' => session()->usr_id,
					]);

					return response()->setJSON([
						'message' => lang2('lead') . ' ' . lang2('status') . ' ' . lang2('deletemessage'),
						'success' => true,
					]);
				} else {
					return response()->setJSON([
						'message' => lang2('status') . ' ' . lang2('used_message') . ' ' . lang2('some') . ' ' . lang2('lead'),
						'success' => false,
					]);
				}
			}
		} else {
			return response()->setJSON([
				'success' => false,
				'message' => lang2('you_dont_have_permission'),
			]);
		}
	}

	function add_source()
	{
		if ($this->Privileges_Model->check_privilege('leads', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$params = [
					'name' => request()->getPost('name'),
					'id_company' => session()->id_company,
				];

				$this->Leads_Model->add_source($params);

				$this->db->table('logs')->insert([
					'date' => date('Y-m-d H:i:s'),
					'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
						session()->staffname . '</a> adicionou uma nova origem, ' . request()->getPost('name'),
					'staff_id' => session()->usr_id,
				]);

				return response()->setJSON([
					'message' => lang2('lead') . ' ' . lang2('source') . ' ' . lang2('addmessage'),
					'success' => true,
				]);
			} else {
				return redirect()->to(base_url('leads'));
			}
		} else {
			return response()->setJSON([
				'success' => false,
				'message' => lang2('you_dont_have_permission'),
			]);
		}
	}




	function update_source($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {
			$data['sources'] = $this->Leads_Model->get_source($id);

			if (isset($data['sources']['id'])) {
				if (request()->getPost()) {
					$params = [
						'name' => request()->getPost('name'),
					];

					$this->db->table('logs')->insert([
						'date' => date('Y-m-d H:i:s'),
						'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
							session()->staffname . '</a> atualizou a origem, ' . request()->getPost('name'),
						'staff_id' => session()->usr_id,
					]);

					$this->Leads_Model->update_source($id, $params);

					return response()->setJSON([
						'message' => lang2('lead') . ' ' . lang2('source') . ' ' . lang2('updatemessage'),
						'success' => true,
					]);
				}
			}
		} else {
			return response()->setJSON([
				'success' => false,
				'message' => lang2('you_dont_have_permission'),
			]);
		}
	}

	function remove_source($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'delete')) {
			$lead = $this->Leads_Model->get_source($id);

			if (isset($lead['id'])) {
				if ($this->Leads_Model->check_sources($id) === 0) {
					$this->Leads_Model->delete_source($id);

					$this->db->table('logs')->insert([
						'date' => date('Y-m-d H:i:s'),
						'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
							session()->staffname . '</a> removeu a origem, ' . $id,
						'staff_id' => session()->usr_id,
					]);

					return response()->setJSON([
						'message' => lang2('lead') . ' ' . lang2('source') . ' ' . lang2('deletemessage'),
						'success' => true,
					]);
				} else {
					return response()->setJSON([
						'message' => lang2('source') . ' ' . lang2('used_message') . ' ' . lang2('some') . ' ' . lang2('lead'),
						'success' => false,
					]);
				}
			}
		} else {
			return response()->setJSON([
				'success' => false,
				'message' => lang2('you_dont_have_permission'),
			]);
		}
	}

	public function move_lead()
	{
		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {
			$lead_id = request()->getPost('lead_id');
			$status_id = request()->getPost('status_id');

			$this->db->table('leads')->where('id', $lead_id)->update(['status' => $status_id]);
			$lead = $this->Leads_Model->get_lead($lead_id);

			$data_lead = [
				'id' => $lead['id'],
				'name' => $lead['leadname'],
				'company' => $lead['company'],
				'phone' => $lead['leadphone'],
				'color' => $lead['color'],
				'status' => $lead['status'],
				'statusname' => $lead['statusname'],
				'source' => $lead['source'],
				'sourcename' => $lead['sourcename'],
				'assigned' => $lead['leadassigned'],
				'avatar' => $lead['assignedavatar'],
				'staff' => $lead['staff_id'],
				'createddate' => $lead['created'],
				lang2('filterbystatus') => $lead['statusname'],
				lang2('filterbysource') => $lead['sourcename'],
			];

			$this->db->table('logs')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
					session()->get('staffname') . '</a> moveu o lead, ' . $lead['leadname'],
				'staff_id' => session()->get('usr_id'),
			]);

			return response()->setJSON($data_lead);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}
	}

	public function mark_as_lead($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {

			if (isset($id)) {

				if (request()->getPost() && count(request()->getPost()) > 0) {

					if (request()->getPost('perdeOportu') && !empty(request()->getPost('perdeOportu'))) {
						$perdeOportu = request()->getPost('perdeOportu');
						$matriz = [
							"id_lead" => $id,
							"motivo" => $perdeOportu['motivo'],
							"outro" => isset($perdeOportu['outro']) ? $perdeOportu['outro'] : null,
							"retorn_futuro" => isset($perdeOportu['retorn_futuro']) ? $perdeOportu['retorn_futuro'] : null,
							"quando" => isset($perdeOportu['quando']) ? $perdeOportu['quando'] : null,
							"dt_entrada" => date('Y-m-d H:i:s'),
							"etapa" => isset($perdeOportu['etapa']) ? $perdeOportu['etapa'] : null,
						];
						$this->db->table('leads_oport_perdida')->insert($matriz);

						$this->db->table('logs')->insert([
							'date' => date('Y-m-d H:i:s'),
							'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
								session()->get('staffname') . '</a> marcou o lead <a href="' . base_url() .
								'leads/lead/' . $id . '">' . $id . '</a>. como perdido',
							'staff_id' => session()->get('usr_id'),
						]);
					}

					$value = request()->getPost('value');
					if ($value == 1) {
						$this->db->table('leads')->where('id', $id)->update(['lost' => 1]);
					} elseif ($value == 2) {
						$this->db->table('leads')->where('id', $id)->update(['lost' => 0]);
					} elseif ($value == 3) {
						$this->db->table('leads')->where('id', $id)->update(['junk' => 1]);
					} elseif ($value == 4) {
						$this->db->table('leads')->where('id', $id)->update(['junk' => 0]);
					}

					$data['success'] = true;
					$data['message'] = lang2('updated');
				} else {
					$data['success'] = false;
					return redirect()->to(base_url('leads'));
				}
			} else {
				throw new \CodeIgniter\Exceptions\PageNotFoundException('The expensecategory you are trying to edit does not exist.');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}


	public function importCSV($file_path)
	{
		if (!file_exists($file_path)) {
			print_r('Arquivo não encontrado.');
			exit;
		}

		$csvArray = [];
		if (($handle = fopen($file_path, 'r')) !== false) {
			$headers = null;

			while (($row = fgetcsv($handle, 1000, ';')) !== false) {
				if ($headers === null) {

					$headers = array_map('trim', $row);
					continue;
				}

				$csvArray[] = array_combine($headers, $row);
			}
			fclose($handle);
		}

		if (empty($csvArray)) {
			print_r('O arquivo CSV está vazio ou inválido.');
			exit;
		}

		return $csvArray;
	}

	function import()
	{
		if ($this->Privileges_Model->check_privilege('leads', 'create')) {
			$uploadPath = ROOTPATH . 'public/uploads/attachments/';
			$file = request()->getFile('userfile');
			$filename = date('Ymdihs') . preg_replace("/[^a-z0-9_\-.]/i", '', $file->getName());
			$allowedTypes = ['csv'];

			if (!in_array($file->getExtension(), $allowedTypes)) {
				session()->setFlashdata('ntf3', 'Error');
				return redirect()->to(base_url('leads'));
			}

			$data['leads'] = $this->Leads_Model->get_leads_for_import();
			$data['error'] = '';
			$file->move($uploadPath, $filename);

			$csv_array = $this->importCSV($uploadPath . $filename);


			foreach ($csv_array as $row) {
				$tp_pessoa = isset($row['Tipo de pessoa']) ? $row['Tipo de pessoa'] : (isset($row['﻿Tipo de pessoa']) ? $row['﻿Tipo de pessoa'] : null);


				if (empty($row['company']) && empty($row['name'])) {
					continue;
				}

				$insert_data = array(
					'tp_pessoa' => $tp_pessoa == "J" ? "2" : "1",
					'created' => date('Y-m-d H:i:s'),
					'name' => isset($row['name']) ? $row['name'] : null,
					//	'title' => $row['title'],
					'company' => isset($row['company']) ? $row['company'] : null,
					'type' => '0',
					'description' => $row['description'],
					'zip' => $row['zip'],
					'city' => isset($row['city']) ? $row['city'] : null,
					'country_id' => '32',
					'state' => $row['state'],
					'address' => $row['address'],
					'email' => $row['email'],
					'lead_status_id' => '1',
					'website' => $row['website'],
					'phone' => $row['phone'],
					'assigned_id' => request()->getPost('importassigned'),
					'staff_id' => session()->get('usr_id'),
					'source' => request()->getPost('importsource'),
					'dateassigned' => date('Y-m-d H:i:s'),
					'status' => request()->getPost('importstatus'),

					'cnpj' => isset($row['cnpj']) ? $row['cnpj'] : null,
					'cpf' => isset($row['cpf']) ? $row['cpf'] : null,
					'setor_atividade' => isset($row['setor_atividade']) ? $row['setor_atividade'] : null,
					'web_site' => isset($row['web_site']) ? $row['web_site'] : null,
					'setor_atividade' => isset($row['setor_atividade']) ? $row['setor_atividade'] : null,
					'dt_nascimento' => isset($row['dt_nascimento']) ? $data = implode("-", array_reverse(explode("/", $row['dt_nascimento']))) : null,
					'porte' => isset($row['porte']) ? $row['porte'] : null,
					'phone' => isset($row['phone']) ? $row['phone'] : null,
					'instagram' => isset($row['instagram']) ? str_replace('"', "", $row['instagram']) : null,
					'facebook' => isset($row['facebook']) ? str_replace('"', "", $row['facebook']) : null,
					'linkedin' => isset($row['linkedin']) ? str_replace('"', "", $row['linkedin']) : null,
					'date_contacted' => date('Y-m-d H:i:s'),
				);

				$id = $this->Leads_Model->insert_csv($insert_data);

				if (isset($row['nm_contato_01']) && !empty($row['nm_contato_01'])) {
					$matriz = [
						'nm_contato' => $row['nm_contato_01'],
						'dt_criado' => date('Y-m-d'),
						'cargo' => isset($row['cargo_01']) ? $row['cargo_01'] : null,
						'email' => isset($row['email_01']) ? $row['email_01'] : null,
						'id_lead' => $id,
						'telefone' => isset($row['telefone_01']) ? $row['telefone_01'] : null,
						'linkedin' => isset($row['linkedin_01']) ? $row['linkedin_01'] : null,
						'observacao' => isset($row['observação_01']) ? $row['observação_01'] : null,
						'dt_aniversario' => isset($row['dt_aniversario_01']) ? $data = implode("-", array_reverse(explode("/", $row['dt_aniversario_01']))) : null,
						'id_criador' => session()->usr_id,
						'ctt_principal' => isset($row['ctt_principal_01']) ? $row['ctt_principal_01'] : null,
					];
					$response = $this->db->table('leads_contatos')->insert($matriz);
				}

				if (isset($row['nm_contato_02']) && !empty($row['nm_contato_02'])) {
					$matriz = [
						'nm_contato' => $row['nm_contato_02'],
						'dt_criado' => date('Y-m-d'),
						'cargo' => isset($row['cargo_02']) ? $row['cargo_02'] : null,
						'email' => isset($row['email_02']) ? $row['email_02'] : null,
						'id_lead' => $id,
						'telefone' => isset($row['telefone_02']) ? $row['telefone_02'] : null,
						'linkedin' => isset($row['linkedin_02']) ? $row['linkedin_02'] : null,
						'observacao' => isset($row['observação_02']) ? $row['observação_02'] : null,
						'dt_aniversario' => isset($row['dt_aniversario_02']) ? $data = implode("-", array_reverse(explode("/", $row['dt_aniversario_02']))) : null,
						'id_criador' => session()->usr_id,
						'ctt_principal' => isset($row['ctt_principal_02']) ? $row['ctt_principal_02'] : null,
					];
					$response = $this->db->table('leads_contatos')->insert($matriz);
				}
			}
			session()->setFlashdata('ntf1', lang2('csvimportsuccess'));
			return redirect()->to(base_url('leads'));
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}
	}

	public function exportdataFull()
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
				"email_" . ($i + 1),
				"telefone_" . ($i + 1),
				"ctt_principal_" . ($i + 1),
				"observacao_" . ($i + 1),
				"linkedin_" . ($i + 1),
				"dt_aniversario_" . ($i + 1)
			);
		}


		fputcsv($outss, $header, ";");
		$builder = $this->db->table('leads')
			->select("leads.*, 
			IF(leads.tp_pessoa = '2', 'J', 'F') as tipo_pessoa, 
			IF(leads.lost = '1', 'Inativo', 'Ativo') as status_lead,
			leads_list.nm_list as funil,
			DATE_FORMAT(leads.created,'%d/%m/%Y') as criado_em")

			->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner')
			->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner')
			->join('staff', 'leads.assigned_id = staff.id', 'inner')


			->join('leadssources', 'leads.source = leadssources.id', 'left')
			->where('staff.id_company', session()->get('id_company'));

		if (request()->getGet('lost') == '1') {
			$builder->join('leads_oport_perdida', 'leads_oport_perdida.id_lead = leads.id', 'left');
			$builder->groupBy('leads.id');
		}


		if (request()->getGet('flt_funil') && request()->getGet('flt_funil') != '-1') {
			$builder->where('leadsstatus.id_list', request()->getGet('flt_funil'));
		}


		$lost = request()->getGet('lost');
		if (request()->getGet('lost') != '-1') {

			if ($lost == "0") {
				$builder->where("(leads.lost IS NULL OR leads.lost = '0')");
			} else {
				$builder->where('leads.lost', $lost);
			}
		}

		if (request()->getGet('flt_origem') && request()->getGet('flt_origem') != '-1') {
			$builder->where('leads.source', request()->getGet('flt_origem'));
		}

		if (request()->getGet('temperatura') && request()->getGet('temperatura') != '-1') {
			$builder->where('leads.temperatura', request()->getGet('temperatura'));
		}

		if (request()->getGet('flt_funcionario') && request()->getGet('flt_funcionario') != '-1') {
			$builder->where('leads.assigned_id', request()->getGet('flt_funcionario'));
		}

		if (request()->getGet('flt_periodo') && request()->getGet('flt_periodo') != '-1') {
			$builder->where("DATEDIFF(CURDATE(), date_contacted) <", request()->getGet('flt_periodo'));
		}


		if (request()->getGet('flt_vencidos')) {
			$flt_vencidos = request()->getGet('flt_vencidos');
			if ($flt_vencidos == "1") {
				$builder->where("leads.ultimoRetorno > '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "2") {
				$builder->where("leads.ultimoRetorno < '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "3") {
				$builder->where("leads.ultimoRetorno = '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "4") {
				$builder->where("leads.ultimoRetorno IS NULL", NULL, false);
			}
		}

		$leads = $builder->get()->getResultArray();

		foreach ($leads as $row) {
			$valueData = [];
			$data = [];

			$linha = array_fill_keys($header, '');

			if (isset($row['cnpj']) && !empty($row['cnpj'])) {
				$data = $this->db->table('leads_data')
					->select('value')
					->where('id_lead', $row['id'])
					->orWhere('cnpj', $row['cnpj'])
					->get()
					->getRow();

				if (!$data) {
					$valueData = $this->buscaDadosPh3a($row['cnpj'], $row['id']);
				} else {
					$valueData = $data ? json_decode($data->value, true) : [];
				}
			}



			$linha['cnpj'] = "'" . $row['cnpj'] . "'" ?? '';
			$linha['razao_social'] = $row['company'] ? $row['company'] : $row['name'];
			$linha['nome_fantasia'] = $row['name'] ?? '';
			$linha['porte'] = $row['porte'] ? $row['porte'] : (isset($valueData['Company']) ? $valueData['Company']['BusinessSizeOriginal'] : '');
			$linha['instagram'] = $row['instagram'] ?? '';
			$linha['facebook'] = $row['facebook'] ?? '';
			$linha['linkedin'] = $row['linkedin'] ?? '';
			$linha['idade_empresa'] = $valueData['Age'] ?? '';
			$linha['mei'] = (isset($valueData['Company']) && isset($valueData['Company']['IsMEI']) ? ($valueData['Company']['IsMEI'] ? 'Sim' : 'Não') : '');
			$linha['natureza'] = (isset($valueData['Company']) ? ($valueData['Company']['LegalNature'] ?? '') : '');
			$linha['status (situacao fiscal)'] = $valueData['FiscalSituation']['Status'] ?? '';
			//$linha['Regime_Tributário'] = $valueData['FiscalSituation']['Status'] ?? '';
			//$linha['Capital_social'] = $valueData['FiscalSituation']['Status'] ?? '';
			$linha['faturamento_presumido'] = $valueData['Revenue']['Presumed'] ?? '';
			$linha['numero_de_funcionarios'] = (isset($valueData['Company']) ? ($valueData['Company']['TotalEmployees'] ?? '') : '');
			$linha['total_matriz_e_filiais'] = (isset($valueData['Company']) ? ($valueData['Company']['TotalCompanies'] ?? '') : '');
			$linha['total_de_socios'] = (isset($valueData['Company']) ? ($valueData['Company']['TotalPartners'] ?? '') : '');
			$linha['score_de_credito'] = $valueData['CreditScore']['D00'] ?? '';
			$linha['score_de_marketing'] = $valueData['MarketingScore']['D00'] ?? '';


			$contatos = $this->db->table('leads_contatos')
				->where('id_lead', $row['id'])
				->orderBy('ctt_principal', 'desc')
				->get()
				->getResultArray();

			foreach ($contatos as $i => $contato) {
				if ($i >= 8)
					break;
				$linha["nm_contato_" . ($i + 1)] = $contato['nm_contato'] ?? '';
				$linha["cargo_" . ($i + 1)] = $contato['cargo'] ?? '';
				$linha["email_" . ($i + 1)] = $contato['email'] ?? '';
				$linha["telefone_" . ($i + 1)] = $contato['telefone'] ?? '';
				$linha["ctt_principal_" . ($i + 1)] = $contato['ctt_principal'] ?? '';
				$linha["observacao_" . ($i + 1)] = $contato['observacao'] ?? '';
				$linha["linkedin_" . ($i + 1)] = $contato['linkedin'] ?? '';
				$linha["dt_aniversario_" . ($i + 1)] = $contato['dt_aniversario'] ?? '';
			}



			if (!empty($valueData['Activities'])) {
				$iCnae = 0;
				foreach ($valueData['Activities'] as $cnae) {
					if ($iCnae >= 8)
						break;

					if (isset($cnae['IsPrimary']) && $cnae['IsPrimary']) {
						$linha["cnae_primario_description"] = $cnae['Description'] ?? '';
						$linha["cnae_primario_code"] = $cnae['Code'] ?? '';
					} else {
						$linha["cnae_secundario_description" . ($iCnae + 1)] = $cnae['Description'] ?? '';
						$linha["cnae_secundario_code" . ($iCnae + 1)] = $cnae['Code'] ?? '';
						$iCnae++;
					}
				}
			}

			if (!empty($valueData['PartnerShips'])) {
				foreach ($valueData['PartnerShips'][0]['Partners'] as $i => $socio) {
					if ($i >= 8)
						break;
					$linha["nome_socio_" . ($i + 1)] = $socio['Name'] ?? '';
					$linha["documento_" . ($i + 1)] = $socio['Document'] ?? '';
				}
			}



			if (!empty($valueData['Emails'])) {
				foreach ($valueData['Emails'] as $i => $email) {
					if ($i >= 8)
						break;
					$linha["email_" . ($i + 1)] = $email['Email'] ?? '';
					$linha["email_score_" . ($i + 1)] = $email['Score'] ?? '';
				}
			}

			if (!empty($valueData['Phones'])) {
				foreach ($valueData['Phones'] as $i => $fone) {
					if ($i >= 8)
						break;

					$linha["phone_" . ($i + 1)] = $fone['FormattedNumber'] ?? '';
					$linha["tipo (is mobile)_" . ($i + 1)] = isset($fone['IsMobile']) ? ($fone['IsMobile'] ? 'Móvel' : 'Fixo') : '';
					$linha["phone_score_" . ($i + 1)] = $fone['Score'] ?? '';
					$linha["observacao_" . ($i + 1)] = '';
				}
			}

			if (!empty($valueData['Addresses'])) {
				foreach ($valueData['Addresses'] as $i => $end) {
					if ($i >= 4)
						break;
					$linha["address_" . ($i + 1)] = ($end['Street'] ?? '') . ', ' . ($end['Number'] ?? '');
					$linha["bairro_" . ($i + 1)] = $end['District'] ?? '';
					$linha["city_" . ($i + 1)] = $end['City'] ?? '';
					$linha["state_" . ($i + 1)] = $end['State'] ?? '';
					$linha["cep_" . ($i + 1)] = $end['ZipCode'] ?? '';
				}
			}


			fputcsv($outss, $linha, ";");
		}
		fclose($outss);
		exit;
	}

	function exportdata()
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
		$builder = $this->db->table('leads')
			->select("leads.*, leads.id as id_leadP, 
			IF(leads.tp_pessoa = '2', 'J', 'F') as tipo_pessoa, 
			IF(leads.lost = '1', 'Inativo', 'Ativo') as status_lead,
			leads_list.nm_list as funil,
			DATE_FORMAT(leads.created,'%d/%m/%Y') as criado_em")

			->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner')
			->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner')
			->join('staff', 'leads.assigned_id = staff.id', 'inner')


			->join('leadssources', 'leads.source = leadssources.id', 'left')
			->where('staff.id_company', session()->get('id_company'));

		if (request()->getGet('lost') == '1') {
			$builder->join('leads_oport_perdida', 'leads_oport_perdida.id_lead = leads.id', 'left');
			$builder->groupBy('leads.id');
		}


		if (request()->getGet('flt_funil') && request()->getGet('flt_funil') != '-1') {
			$builder->where('leadsstatus.id_list', request()->getGet('flt_funil'));
		}


		$lost = request()->getGet('lost');
		if (request()->getGet('lost') != '-1') {

			if ($lost == "0") {
				$builder->where("(leads.lost IS NULL OR leads.lost = '0')");
			} else {
				$builder->where('leads.lost', $lost);
			}
		}

		if (request()->getGet('flt_origem') && request()->getGet('flt_origem') != '-1') {
			$builder->where('leads.source', request()->getGet('flt_origem'));
		}

		if (request()->getGet('temperatura') && request()->getGet('temperatura') != '-1') {
			$builder->where('leads.temperatura', request()->getGet('temperatura'));
		}

		if (request()->getGet('flt_funcionario') && request()->getGet('flt_funcionario') != '-1') {
			$builder->where('leads.assigned_id', request()->getGet('flt_funcionario'));
		}

		if (request()->getGet('flt_periodo') && request()->getGet('flt_periodo') != '-1') {
			$builder->where("DATEDIFF(CURDATE(), date_contacted) <", request()->getGet('flt_periodo'));
		}


		if (request()->getGet('flt_vencidos')) {
			$flt_vencidos = request()->getGet('flt_vencidos');
			if ($flt_vencidos == "1") {
				$builder->where("leads.ultimoRetorno > '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "2") {
				$builder->where("leads.ultimoRetorno < '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "3") {
				$builder->where("leads.ultimoRetorno = '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "4") {
				$builder->where("leads.ultimoRetorno IS NULL", NULL, false);
			}
		}

		//echo $builder->getCompiledSelect();
		//exit;

		$leads = $builder->get()->getResultArray();

		foreach ($leads as $row) {

			$linha = array_fill_keys($header, '');
			$linha['cnpj'] = "'" . $row['cnpj'] . "'" ?? '';
			$linha['razao_social'] = $row['company'] ? $row['company'] : $row['name'];
			$linha['nome_fantasia'] = $row['name'] ?? '';
			$linha['porte'] = $row['porte'] ? $row['porte'] : '';
			$linha['instagram'] = $row['instagram'] ?? '';
			$linha['facebook'] = $row['facebook'] ?? '';
			$linha['linkedin'] = $row['linkedin'] ?? '';

			$linha['email_1'] = $row['email'] ?? '';

			$contatos = $this->db->table('leads_contatos')
				->where('id_lead', $row['id_leadP'])
				->orderBy('ctt_principal', 'desc')
				->get()
				->getResultArray();


			foreach ($contatos as $i => $contato) {
				if ($i >= 8)
					break;
				$linha["nm_contato_" . ($i + 1)] = $contato['nm_contato'] ?? '';
				$linha["cargo_" . ($i + 1)] = $contato['cargo'] ?? '';
				$linha["email_ctt_" . ($i + 1)] = $contato['email'] ?? '';
				$linha["telefone_" . ($i + 1)] = $contato['telefone'] ?? '';
				$linha["ctt_principal_" . ($i + 1)] = $contato['ctt_principal'] ? ($contato['ctt_principal'] == '1' ? 'Sim' : 'Não') : '';
				$linha["observacao_ctt_" . ($i + 1)] = $contato['observacao'] ?? '';
				$linha["linkedin_" . ($i + 1)] = $contato['linkedin'] ?? '';
				$linha["dt_aniversario_" . ($i + 1)] = $contato['dt_aniversario'] ?? '';
			}

			fputcsv($outss, $linha, ";");
		}
		fclose($outss);
		exit;
	}



	function remove_converted($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'delete')) {

			$response = $this->db->table('leads')->delete(array('status' => $id));
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('leads'));
		}
	}



	public function make_converted_status($id)
	{
		if ($this->Privileges_Model->check_privilege('leads', 'edit')) {
			$this->db->table('settings')->where('settingname', 'ciuis')->update(['converted_lead_status_id' => $id]);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}
	}




	function remove($id)
	{

		if ($this->Privileges_Model->check_privilege('leads', 'all')) {

			$lead = $this->Leads_Model->get_lead_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {

			$lead = $this->Leads_Model->get_lead_by_privileges($id, session()->usr_id);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}

		if ($lead) {

			if ($this->Privileges_Model->check_privilege('leads', 'delete')) {

				// check if the lead exists before trying to delete it

				$lead_number = get_number('leads', $id, 'lead', 'lead');

				if (isset($lead['id'])) {

					$this->Leads_Model->delete_lead($id, $lead_number);

					$this->db->table('logs')->insert(array(
						'date' => date('Y-m-d H:i:s'),
						'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
							session()->staffname . '</a>  removeu o lead <a href="' . base_url() .
							'leads/lead/' . $lead['id'] . '">' . $lead['id'] . '</a>.',
						'staff_id' => session()->usr_id,
					));

					$data['success'] = true;

					$data['message'] = lang2('lead') . ' ' . lang2('deletemessage');

					return response()->setJSON($data);
				} else {

					show_error('The lead you are trying to delete does not exist.');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('leads'));
		}
	}



	function get_lead($id)
	{
		$lead = array();
		if ($this->Privileges_Model->check_privilege('leads', 'all')) {
			$lead = $this->Leads_Model->get_lead_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {
			$lead = $this->Leads_Model->get_lead_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('leads'));
		}

		if ($lead) {
			$leads_data = $this->db->table('leads_data')
				->where('id_lead', $id)
				->get()
				->getRow();


			if ($leads_data != null) {
				$leads_data = json_decode($leads_data->value);
			}

			$country = get_country($lead['country_id']);
			$state = get_state_name($lead['state'], $lead['state_id']);
			switch ($lead['public']) {
				case '0':
					$is_public = false;
					break;
				case '1':
					$is_public = true;
					break;
			}

			switch ($lead['type']) {
				case '0':
					$is_individual = false;
					break;
				case '1':
					$is_individual = true;
					break;
			}


			$data_lead = array(
				'id' => $lead['id'],
				'type' => $lead['type'],
				'cpf' => $lead['cpf'],
				'dt_nascimento' => $lead['dt_nascimento'],
				'instagram' => $lead['instagram'],
				'facebook' => $lead['facebook'],
				'linkedin' => $lead['linkedin'],
				'name' => $lead['leadname'],
				'title' => $lead['title'],
				'company' => $lead['company'],
				'description' => $lead['description'],
				'country_id' => $lead['country_id'],
				'country' => $country,
				'zip' => $lead['zip'],
				'city' => $lead['city'],
				'id_fluxo' => $lead['id_fluxo'],
				'state' => $state,
				'state_id' => $lead['state_id'],
				'email' => $lead['leadmail'],
				'closer' => $lead['closer'],
				'address' => $lead['address'],
				'tags' => $lead['tags'],
				'website' => $lead['website'],
				'ultimoRetorno' => $lead['ultimoRetorno'],
				'phone' => $lead['leadphone'],
				'is_whatsApp' => $lead['is_whatsApp'],
				'assigned' => $lead['leadassigned'],
				'assigned_id' => $lead['assigned_id'],
				'created' => $lead['created'],
				'status_id' => $lead['status'],
				'status' => $lead['statusname'],
				'source_id' => $lead['source'],
				'source' => $lead['sourcename'],
				'lastcontact' => $lead['lastcontact'],
				'dateassigned' => $lead['dateassigned'],
				'tp_pessoa' => $lead['tp_pessoa'],
				'staff_id' => $lead['staff_id'],
				'dateconverted' => $lead['dateconverted'],
				'date_contacted' => $lead['date_contacted'],
				'lost' => $lead['lost'],
				'temperatura' => $lead['temperatura'],
				'qualificacao' => $lead['qualificacao'],
				'id_list' => $lead['id_list'],
				'junk' => $lead['junk'],
				'public' => $is_public,
				'type' => $is_individual,
				'lead_number' => get_number('leads', $lead['id'], 'lead', 'lead'),
				'lead_status_id' => ($lead['lead_status_id'] == '1') ? true : false,
				'status_list' => $this->Leads_Model->get_status_list($lead['status']),
				'list_atividades' => $this->Leads_Model->get_atividades_by_user($lead['id']),

				'cnpj' => $lead['cnpj'],
				'web_site' => $lead['web_site'],
				'setor_atividade' => $lead['setor_atividade'],
				'porte' => $lead['porte'],
				'ultimaBusca' => $lead['ultimaBusca'],

				'leads_data' => $leads_data,

				//'hora_taskAut' => $lead['hora_taskAut'],
				//'periodo_taskAut' => $lead['periodo_taskAut'],
				//	'copia_para_oculto_taskAut' => $lead['copia_para_oculto_taskAut'],
				//	'copia_para_taskAut' => $lead['copia_para_taskAut'],
				//	'name_taskAut' => $lead['name_taskAut'],
				//	'template' => $lead['template'],

			);

			if ($lead['temperatura'] == "1") {
				$data_lead['temperaturaTitle'] = "Quente";
				$data_lead['tempColor'] = "#f00";
				$data_lead['backTempColor'] = "#f00";
				$data_lead['colorBtn'] = "#fff";
			} else if ($lead['temperatura'] == "2") {
				$data_lead['temperaturaTitle'] = "Morno";
				$data_lead['tempColor'] = "#ffbc00";
				$data_lead['backTempColor'] = "#ffbc00";
				$data_lead['colorBtn'] = "#fff";
			} else if ($lead['temperatura'] == "3") {
				$data_lead['temperaturaTitle'] = "Frio";
				$data_lead['tempColor'] = "#0008ff";
				$data_lead['backTempColor'] = "#0008ff";
				$data_lead['colorBtn'] = "#fff";
			} else {
				$data_lead['temperaturaTitle'] = "Indefinido";
				$data_lead['tempColor'] = "#777";
				$data_lead['backTempColor'] = "#f5f5f5";
				$data_lead['colorBtn'] = "#777";
			}


			return response()->setJSON($data_lead);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('leads'));
		}
	}

	function get_credito()
	{
		$row = $this->db->table('companies')
			->where('id_company', session()->id_company)
			->get()
			->getRow();

		$data['creditos'] = $row->creditos;
		$data['creditos_enriquecimento'] = $row->creditos_enriquecimento;
		return response()->setJSON($data);
	}

	function get_list_atividades($id)
	{
		$data = $this->Leads_Model->get_atividades_by_user($id);
		return response()->setJSON($data);
	}

	function leadstatuses()
	{
		$leadstatuses = $this->Leads_Model->get_leads_status();
		return response()->setJSON($leadstatuses);
	}

	function getListSelectLeads()
	{
		$data = $this->Leads_Model->getListSelectLeads();
		return response()->setJSON($data);
	}

	function getContatosLead($id)
	{
		$data = $this->Leads_Model->getContatosLead($id);
		return response()->setJSON($data);
	}

	function getContatosLeadAll($id)
	{
		$data = $this->Leads_Model->getContatosLeadAll($id);
		return response()->setJSON($data);
	}

	function leadsources($super = false)
	{
		$leadsources = $this->Leads_Model->get_leads_sources($super);
		return response()->setJSON($leadsources);
	}

	function leadslist($super = "0")
	{
		$data = [];
		$result = [];
		if ($super != "0" && session()->super_admin == "1") {
			$result = $this->Leads_Model->get_all_leads_list(1);
		} else {
			$result = $this->Leads_Model->get_all_leads_list(0);
		}

		foreach ($result as $index => $row) {
			$row['leadstatuses'] = $this->Leads_Model->get_leads_sources_by_list2($row['id_list']);
			$data[$row['id_list']] = $row;
		}
		return response()->setJSON($data);
	}

	public function updateTeste()
	{
		// Obter todos os leads onde 'ultimoRetorno' é NULL
		$leads = $this->db->table('leads')
			->select('leads.id as id_lead')
			->where('ultimoRetorno IS NULL')
			->join('leadsstatus', 'leadsstatus.id = leads.status', 'inner')
			->where("(lost = '0' or lost is null)")
			->where('leadsstatus.id_list', '9')
			->get()
			->getResultArray();

		//echo $leads->getCompiledSelect();
		//exit;

	//	print_r($leads);
	//	exit;

		foreach ($leads as $lead) {
			// Obter a última atividade de retorno para o lead
			$lastActivity = $this->db->table('leads_atv')
				->where('id_lead', $lead['id_lead'])
				->orderBy('retorno', 'desc')
				->get()
				->getRowArray();

			// Atualizar o lead com o último retorno encontrado
			$this->db->table('leads')
				->where('id', $lead['id_lead'])
				->update(['ultimoRetorno' => $lastActivity['retorno'] ?? null]);
		}
	}

	public function updateStatus()
	{
		$status = request()->getPost('status');
		$id = request()->getPost('id');

		$response = $this->db->table('leads')
			->where('id', $id)
			->update(['status' => $status]);

		return response()->setJSON(['result' => $response]);
	}

	public function addNewAtividade()
	{
		$matriz = [
			'data' => request()->getPost('data'),
			'email' => request()->getPost('email'),
			'horario' => request()->getPost('horario'),
			'duracao' => request()->getPost('duracao'),
			'anotacoes' => request()->getPost('anotacoes'),
			'reuniao_call' => request()->getPost('reuniao_call'),
			'retorno' => request()->getPost('retorno'),
			'dt_entrada' => date('Y-m-d H:i:s'),
			'id_etapa_flow' => request()->getPost('id_etapa_flow'),
			'id_criador' => session()->get('usr_id'),
		];

		$detail = '';

		if (request()->getPost('id_lead')) {
			$matriz['id_lead'] = request()->getPost('id_lead');
			$matriz['atividade'] = request()->getPost('atividade');

			$lead = $this->db->table('leads')
				->select('*')
				->where('id', request()->getPost('id_lead'))
				->get()
				->getRowArray();

			$detail = '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
				session()->get('staffname') . '</a> adicionou nova atividade no lead <a href="' . base_url() .
				'leads/lead/' . request()->getPost('id_lead') . '">' . ($lead['name'] != "" ? $lead['name'] : $lead['company']) . '</a>.';
		}

		if (request()->getPost('id_customer')) {
			$matriz['id_customer'] = request()->getPost('id_customer');
			$matriz['atividade_customer'] = request()->getPost('atividade');
			$cliente = $this->db->table('customers')
				->select('*')
				->where('id', request()->getPost('id_customer'))
				->get()
				->getRowArray();

			$detail = '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' .
				session()->get('staffname') . '</a> adicionou nova atividade no cliente <a href="' . base_url() .
				'customers/customer/' . request()->getPost('id_customer') . '">' . $cliente['company'] . '</a>.';
		}

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $detail,
			'staff_id' => session()->get('usr_id'),
		]);

		if (request()->getPost('id_atividade') && !empty(request()->getPost('id_atividade'))) {
			unset($matriz['dt_entrada']);
			$response = $this->db->table('leads_atv')
				->where('id_atividade', request()->getPost('id_atividade'))
				->update($matriz);
		} else {
			if (request()->getPost('id_lead')) {
				$this->db->table('email_task_leads')
					->where('id_lead', request()->getPost('id_lead'))
					->delete();
			}
			$response = $this->db->table('leads_atv')->insert($matriz);
		}

		return response()->setJSON(["result" => $response]);
	}


	function addNewReuniao()
	{
		$matriz = [
			'data' => request()->getPost('data'),
			'hora' => request()->getPost('hora'),

			'id_lead' => request()->getPost('id_lead'),

			'id_funcionario' => implode(",", request()->getPost('id_funcionario')),
			'tipo' => request()->getPost('tipo'),
			'realizada' => request()->getPost('realizada'),
			'created_at' => date('Y-m-d H:i:s'),

			'problema' => request()->getPost('problema'),
			'motivo' => request()->getPost('motivo'),
			'observacao' => request()->getPost('observacao'),
			'id_criador' => session()->usr_id,
		];

		$detail = '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
			session()->staffname . '</a>  adicionou nova reunião no lead <a href="' . base_url() .
			'leads/lead/' . $_POST['id_lead'] . '">' . $_POST['id_lead'] . '</a>.';

		$this->db->table('logs')->insert(array(
			'date' => date('Y-m-d H:i:s'),
			'detail' => $detail,
			'staff_id' => session()->usr_id,
		));

		if (request()->getPost('id_reuniao') && !empty(request()->getPost('id_reuniao'))) {
			// Atualizar se 'id_reuniao' estiver presente e não vazio
			$response = $this->db->table('leads_reunioes')
				->where('id_reuniao', request()->getPost('id_reuniao'))
				->update($matriz);
		} else {
			// Inserir se 'id_reuniao' não estiver presente ou estiver vazio
			$response = $this->db->table('leads_reunioes')->insert($matriz);
			$id_reuniao = $this->db->insertID();

			$closer = "";

			foreach (request()->getPost('id_funcionario') as $user) {
				$closer .= ($closer != "" ? ', ' : '') . $this->Staff_Model->get_name_staff($user)['staffname'];
			}

			$matrizAtv = [
				'data' => request()->getPost('data'),
				'horario' => request()->getPost('hora'),
				'anotacoes' => 'Reunião agendada: ' . request()->getPost('observacao') . ' <br> ' .
					'<p><b>Data</b> ' . date('d/m/Y', strtotime(request()->getPost('data'))) . ' ' . request()->getPost('hora') . ' </p>' .
					'<p><b>Closer:</b> ' . $closer . ' </p>',
				'reuniao_call' => "1",
				'retorno' => request()->getPost('data'),
				'dt_entrada' => date('Y-m-d H:i:s'),
				'id_criador' => session()->usr_id,
				'is_reuniao' => '1',
				'id_reuniao' => $id_reuniao
			];

			$matrizAtv['id_lead'] = request()->getPost('id_lead');
			$matrizAtv['atividade'] = request()->getPost('tipo') == "Presencial" ? "2" : "3";
			$response = $this->db->table('leads_atv')->insert($matrizAtv);
		}

		return response()->setJSON(["result" => $response]);
	}



	public function AlterarSalesFlow()
	{
		$leads = request()->getPost('selectedLeads');

		$response = $this->db->table('leads')
			->whereIn('id', $leads)
			->update([
				'sales_flow' => request()->getPost('sales_flow'),
			]);

		$data['message'] =  'Atualizado com sucesso!';
		$data['success'] = true;
		return response()->setJSON($data);
	}

	public function gerarResposta()
	{
		// 🧾 Pega o texto enviado (por exemplo, o conteúdo do e-mail)
		$mensagem = request()->getPost('mensagem');

		if (empty($mensagem)) {
			return response()->setJSON([
				'status' => 'error',
				'message' => 'Nenhuma mensagem enviada.'
			]);
		}

		// 🔑 Busca configurações da empresa
		$settings = $this->db->table('settings')
			->where('id_company', session()->id_company)
			->get()
			->getRowArray();

		if (!$settings || empty($settings['key_gpt'])) {
			return response()->setJSON([
				'status' => 'error',
				'message' => 'Chave da API não configurada em configurações.'
			]);
		}

		// 🔄 Verifica se há um assistente configurado
		if (empty($settings['assistent_gpt'])) {

			// 🌐 Endpoint da API da OpenAI
			$url = 'https://api.openai.com/v1/chat/completions';

			// 🧠 Contexto da empresa
			$contexto_empresa = '';
			if (!empty($settings['descricao_empresa'])) {
				$contexto_empresa .= "Sobre a empresa: " . $settings['descricao_empresa'] . "\n\n";
			}
			if (!empty($settings['resumo'])) {
				$contexto_empresa .= "Instruções adicionais: " . $settings['resumo'] . "\n\n";
			}

			// 🧠 Prompt base
			$prompt = $contexto_empresa . "Responda de forma educada e profissional a este e-mail:\n\n" . $mensagem;

			// 🧩 Cabeçalhos HTTP
			$headers = [
				'Content-Type: application/json',
				'Authorization: ' . 'Bearer ' . $settings['key_gpt'],
			];

			// 📦 Corpo da requisição
			$data = [
				'model' => 'gpt-4o-mini', // pode trocar por gpt-4o
				'messages' => [
					['role' => 'system', 'content' => 'Você é um assistente útil e profissional.'],
					['role' => 'user', 'content' => $prompt],
				],
				'max_tokens' => 400,
			];
		} else {
			// ✅ Se há assistente configurado, usa o endpoint dos Assistants
			$url = 'https://api.openai.com/v1/assistants/' . $settings['assistent_gpt'] . '/responses';

			$headers = [
				'Content-Type: application/json',
				'Authorization: ' . 'Bearer ' . $settings['key_gpt'],
				'OpenAI-Beta: assistants=v2',
			];

			$data = [
				'model' => 'gpt-4o-mini',
				'input' => [
					['role' => 'user', 'content' => $mensagem]
				]
			];
		}

		// 🔄 Executa a requisição cURL
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => $headers,
		]);

		$response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if ($error) {
			throw new \CodeIgniter\HTTP\Exceptions\HTTPException('Erro ao conectar à API do GPT: ' . $error);
		}

		$result = json_decode($response, true);

		// 📖 Extrai a resposta (para chat normal ou assistente)
		if (!empty($result['choices'][0]['message']['content'])) {
			$resposta = $result['choices'][0]['message']['content'];
		} elseif (!empty($result['output'][0]['content'][0]['text'])) {
			$resposta = $result['output'][0]['content'][0]['text'];
		} else {
			$resposta = 'Sem resposta do GPT.';
		}

		return response()->setJSON([
			'status' => 'ok',
			'resposta' => trim($resposta),
		]);
	}



	function addNewContato()
	{

		$matriz = [
			'nm_contato' => $_POST['nm_contato'],
			'dt_criado' => date('Y-m-d'),
			'cargo' => $_POST['cargo'],
			'email' => $_POST['email'],
			'id_lead' => $_POST['id_lead'],
			'telefone' => $_POST['telefone'],
			'linkedin' => $_POST['linkedin'],
			'observacao' => $_POST['observacao'],
			'dt_aniversario' => $_POST['dt_aniversario'],
			'id_criador' => session()->usr_id,
			'ctt_principal' => $_POST['ctt_principal'],
			'is_whatsApp' => $_POST['is_whatsApp'],

		];

		$this->db->table('logs')->insert(array(
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
				session()->staffname . '</a>  adicionou novo contato no lead <a href="' . base_url() .
				'leads/lead/' . $_POST['id_lead'] . '">' . $_POST['id_lead'] . '</a>.',
			'staff_id' => session()->usr_id,
		));

		if (isset($_POST['id_contato']) && !empty($_POST['id_contato'])) {
			unset($matriz['dt_criado']);
			$response = $this->db->table('leads_contatos')
				->where('id_lead_contato', $_POST['id_contato'])
				->update($matriz);
		} else {
			$response = $this->db->table('leads_contatos')->insert($matriz);
		}

		return response()->setJSON(["result" => $response]);
	}

	function get_leads_by_funil()
	{
		$leads = array();
		if ($this->Privileges_Model->check_privilege('leads', 'all')) {
			$leads = $this->Leads_Model->get_all_leads_by_privileges('', null, false);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {
			$leads = $this->Leads_Model->get_all_leads_by_privileges(session()->usr_id, null, false);
		}

		$data_leads = array();

		foreach ($leads as $index => $lead) {
			if ($lead['ultimoRetorno'] == "") {
				$prazo = "4";
			} elseif (strtotime(date('Y-m-d')) < strtotime($lead['ultimoRetorno'])) {
				$prazo = "1";
			} elseif (strtotime(date('Y-m-d')) > strtotime($lead['ultimoRetorno'])) {
				$prazo = "2";
			} elseif (strtotime(date('Y-m-d')) == strtotime($lead['ultimoRetorno'])) {
				$prazo = "3";
			} else {
				$prazo = "4";
			}

			if ($lead['lost'] == "1") {
				if ($lead['quando'] != "" && $lead['retorn_futuro'] == "1" && strtotime(date('Y-m-d')) >= strtotime($lead['quando'])) {
					$lead['status'] = $lead['etapa'];
					$lead['view'] = "1";
				} else {
					$lead['view'] = "0";
					//continue;
				}
			} else {
				$lead['view'] = "1";
			}

			if ($lead['temperatura'] == "1") {
				$lead['temperatura'] = "Quente";
				$lead['tempColor'] = "#f00";
			} else if ($lead['temperatura'] == "2") {
				$lead['temperatura'] = "Morno";
				$lead['tempColor'] = "#ffbc00";
			} else if ($lead['temperatura'] == "3") {
				$lead['temperatura'] = "Frio";
				$lead['tempColor'] = "#0008ff";
			} else {
				$lead['temperatura'] = "Indefinido";
				$lead['tempColor'] = "#777";
			}


			$data_leads[] = array(
				'id' => $lead['id'],
				'name_lead' => $lead['name_lead'],
				'name' => $lead['leadname'],
				'lost' => $lead['lost'],
				'id_list' => $lead['id_list'],
				'ultimoFlowAutomatico' => $lead['ultimoFlowAutomatico'],
				'assigned_id' => $lead['assigned_id'],
				'company' => $lead['company'],
				'phone' => $lead['leadphone'],
				'color' => $lead['color'] ? $lead['color'] : '',
				'status' => $lead['status_f'] ? $lead['status_f'] : '',
				//'status_f' => $lead['status_f'] ? $lead['status_f'] : '',
				'statusname' => $lead['statusname'] ? $lead['statusname'] : '',
				'source' => $lead['source'] ? $lead['source'] : '',
				'sourcename' => $lead['sourcename'] ? $lead['sourcename'] : '',
				'assigned' => $lead['leadassigned'],
				'avatar' => $lead['assignedavatar'],
				'staff' => $lead['staff_id'],
				'date_contacted' => date(get_dateFormat(), strtotime($lead['date_contacted'])),
				'tags' => $lead['tags'],
				'createddate' => $lead['created'],
				'createddateF' => date(get_dateFormat(), strtotime($lead['created'])),
				'diasAtivo' => $this->diasDatas($lead['created']),
				'' . lang2('filterbystatus') . '' => $lead['statusname'] ? $lead['statusname'] : '',
				'' . lang2('filterbysource') . '' => $lead['sourcename'] ? $lead['sourcename'] : '',
				'lead_number' => get_number('leads', $lead['id'], 'lead', 'lead'),
				'email' => $lead['leadmail'],
				'prazo' => $prazo,
				'lead_status_id' => $lead['lead_status_id'],
				'tp_pessoa' => $lead['tp_pessoa'],
				'ctt_principal' => $lead['ctt_principal'],
				'temperatura' => $lead['temperatura'],
				'tempColor' => $lead['tempColor'],
				'ContatosLead' => [], //$this->Leads_Model->getContatosLead($lead['id']),
				'view' => $lead['view']
			);
		};

		return response()->setJSON($data_leads);
	}

	function diasDatas($data_inicial)
	{
		$data_final = date('Y-m-d');
		$diferenca = strtotime($data_final) - strtotime($data_inicial);
		$dias = floor($diferenca / (60 * 60 * 24));
		return $dias;
	}

	function get_leads()
	{
		$leads = array();
		if ($this->Privileges_Model->check_privilege('leads', 'all')) {
			$leads = $this->Leads_Model->get_all_leads_by_privileges('', null, false);
		} else if ($this->Privileges_Model->check_privilege('leads', 'own')) {
			$leads = $this->Leads_Model->get_all_leads_by_privileges(session()->usr_id, null, false);
		}

		$data_leads = array();

		foreach ($leads as $index => $lead) {
			if ($lead['ultimoRetorno'] == "") {
				$prazo = "4";
			} elseif (strtotime(date('Y-m-d')) < strtotime($lead['ultimoRetorno'])) {
				$prazo = "1";
			} elseif (strtotime(date('Y-m-d')) > strtotime($lead['ultimoRetorno'])) {
				$prazo = "2";
			} elseif (strtotime(date('Y-m-d')) == strtotime($lead['ultimoRetorno'])) {
				$prazo = "3";
			} else {
				$prazo = "4";
			}

			if ($lead['lost'] == "1") {
				if ($lead['quando'] != "" && $lead['retorn_futuro'] == "1" && strtotime(date('Y-m-d')) >= strtotime($lead['quando'])) {
					$lead['status'] = $lead['etapa'];
				} else {
					//continue;
				}
			}
			$tags = $this->db->table('tags')
				->select('*')
				->where('relation_type', 'lead')
				->where('relation', $lead['id'])
				->get()
				->getRowArray();

			$data_leads[] = array(
				'id' => $lead['id'],
				'name' => $lead['leadname'],
				'lost' => $lead['lost'],
				'assigned_id' => $lead['assigned_id'],
				'company' => $lead['company'],
				'phone' => $lead['leadphone'],
				'color' => $lead['color'] ? $lead['color'] : '',
				'status' => $lead['status'] ? $lead['status'] : '',
				'statusname' => $lead['statusname'] ? $lead['statusname'] : '',
				'source' => $lead['source'] ? $lead['source'] : '',
				'sourcename' => $lead['sourcename'] ? $lead['sourcename'] : '',
				'assigned' => $lead['leadassigned'],
				'avatar' => $lead['assignedavatar'],
				'staff' => $lead['staff_id'],
				'date_contacted' => date(get_dateFormat(), strtotime($lead['date_contacted'])),
				'tags' => $tags,
				'createddate' => $lead['created'],
				'' . lang2('filterbystatus') . '' => $lead['statusname'] ? $lead['statusname'] : '',
				'' . lang2('filterbysource') . '' => $lead['sourcename'] ? $lead['sourcename'] : '',
				'lead_number' => get_number('leads', $lead['id'], 'lead', 'lead'),
				'email' => $lead['leadmail'],
				'prazo' => $prazo,
				'lead_status_id' => $lead['lead_status_id'],
				'tp_pessoa' => $lead['tp_pessoa'],
				'ctt_principal' => $lead['ctt_principal'],
				'name_lead' => $lead['name_lead'],
				'ContatosLead' => [] //$this->Leads_Model->getContatosLead($lead['id'])
			);
		};

		return response()->setJSON($data_leads);
	}
}
