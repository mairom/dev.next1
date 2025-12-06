<?php
namespace App\Controllers;
class Forms extends BaseController {

	public function __construct(){
        

        $this->model('Leads_Model');
        $this->model('Emails_Model');
        $this->model('Settings_Model');
        define( 'LANG', $this->Settings_Model->get_crm_lang() );
        $this->lang->load( LANG.'_default', LANG);
        $this->lang->load( LANG, LANG );
    }

	function index(){
		header('Location: /');
	}

	function wlf($token) {
		if ($token) {
			$form = $this->Leads_Model->getFormData_by_token($token);
			if ($form) {
				if ($form['status'] == '1') {
					$data['form'] = $form;
					$data['otherData'] = array(
						'name' => lang2('name'),
						'confirm_text' => lang2('confirm_text_field'),
						'matching_text_field' => lang2('matching_text_field'),
						'please_correct_all_errors' => lang2('please_correct_all_errors'),
						'form_invalid_email' => lang2('invalid_email'),
						'invalid_regex' => lang2('invalid_regex'),
						'maxLength' => lang2('maxLength'),
						'minLength' => lang2('minLength'),
						'required' => lang2('required'),
						'error_message' => lang2('error_message'),
						'invalid_date' => lang2('invalid_date'),
						'max' => lang2('max'),
						'min' => lang2('min'),
						'next' => lang2('next'),
						'pattern' => lang2('pattern'),
						'previous' => lang2('previous'),
						'translations' => lang2('translations'),
					);
					return view( 'forms/web-form', $data);
				}
			}
		}
	}

	function save_lead() {
		$token = request()->getPost('token');
		if ($token) {
			$form = $this->Leads_Model->getFormData_by_token($token);
			if ($form) {
				if ($form['duplicate'] == '0') {
					if (request()->getPost('data[lEmail]')) {
						$email = request()->getPost('data[lEmail]');
						$is_duplicate = $this->Leads_Model->check_duplicate_lead($email);
						if ($is_duplicate) {
							$return['success'] = false;
							$return['message'] = lang2('duplicate_lead');
							return response()->setJSON($return);
						} else {
							echo $this->add_lead();
						}
					} else {
						echo $this->add_lead();
					}
				} else {
					echo $this->add_lead();
				}
			} else {
				$return['success'] = false;
				$return['message'] = lang2('invalid_token');
				return response()->setJSON($return);
			}
		} else {
			$return['success'] = false;
			$return['message'] = lang2('errormessage');
			return response()->setJSON($return);
		}
	}

	function add_lead() {
		$token = request()->getPost('token');
		$form = $this->Leads_Model->getFormData_by_token($token);
		$params = array();
		if (request()->getPost('data[lEmail]') == '') {
			$return['success'] = false;
			$return['message'] = lang2('invalid_email');
			return json_encode($return);
		}
		if (request()->getPost('data[lTitle]')) {
			$params['title'] = request()->getPost('data[lTitle]');
		}
		if (request()->getPost('data[lDate]')) {
			$params['dateassigned'] = date('Y-m-d' ,strtotime(request()->getPost('data[lDate]')));
		}
		if (request()->getPost('data[lDescription]')) {
			$params['description'] = request()->getPost('data[lDescription]');
		}
		if (request()->getPost('data[lAddress]')) {
			$params['address'] = request()->getPost('data[lAddress]');
		}
		if (request()->getPost('data[lCity]')) {
			$params['city'] = request()->getPost('data[lCity]');
		}
		if (request()->getPost('data[lState]')) {
			$params['state'] = request()->getPost('data[lState]');
		}
		if (request()->getPost('data[lCountry]')) {
			$params['country'] = request()->getPost('data[lCountry]');
		}
		if (request()->getPost('data[lWebsite]')) {
			$params['website'] = request()->getPost('data[lWebsite]');
		}
		if (request()->getPost('data[lEmail]')) {
			$params['email'] = request()->getPost('data[lEmail]');
		}
		if (request()->getPost('data[lCompany]')) {
			$params['company'] = request()->getPost('data[lCompany]');
		}
		if (request()->getPost('data[lName]')) {
			$params['name'] = request()->getPost('data[lName]');
		}
		if (request()->getPost('data[lPhone]')) {
			$params['phone'] = request()->getPost('data[lPhone]');
		}
		if (request()->getPost('data[lZip]')) {
			$params['zipCode'] = request()->getPost('data[lZip]');
		}
		$params['weblead'] = $form['id'];
		$params['type'] = 0;
		$params['assigned_id'] = $form['assigned_id'];
		$params['status'] = $form['lead_status'];
		$params['source'] = $form['lead_source'];
		$params['created'] = date('Y-m-d');
		$params['date_contacted'] = date('Y-m-d H:i:s');
		$this->db->table('leads')->insert( $params);
		$id = $this->db->insertID();
		if ($id) {
			$items = request()->getPost('custom');
			if ($items) {
				foreach ($items as $key => $value) {
					$paramm = array(
						'field_id' => $key,
						'data' => $value,
						'relation_type' => 'lead',
						'relation' => $id
					);
					$this->db->table('custom_fields_data')->insert( $paramm);
				}
			}
			if ($form['notification'] == '1') {
				$template = $this->Emails_Model->get_template('lead', 'lead_assigned');
				if ($template['status'] == 1) {
					$settings = $this->Settings_Model->get_settings_ciuis();
					$lead = $this->Leads_Model->get_lead( $id );
					$lead_url = '' . base_url( 'leads/lead/' . $id . '' ) . '';
					$message_vars = array(
						'{lead_name}' => request()->getPost('data[lName]'),
						'{lead_email}' => request()->getPost('data[lEmail]'), 
						'{lead_url}' => $lead_url,
						'{lead_assigned_staff}' => $lead['leadassigned'],
						'{company_name}' => $settings['company'],
						'{company_email}' => $settings['email'],
						'{name}' => $settings['company'],
						'{email_signature}' => $settings['email'],
					);
					$subject = strtr($template['subject'], $message_vars);
					$message = strtr($template['message'], $message_vars);

					$param = array(
						'from_name' => $template['from_name'],
						'email' => $lead['staffemail'],
						'subject' => $subject,
						'message' => $message,
						'created' => date( "Y.m.d H:i:s" )
					);
					if ($lead['staffemail']) {
						$this->db->table('email_queue')->insert($param );
					}
				}

				$template = $this->Emails_Model->get_template('lead', 'lead_submitted');
				if ($template['status'] == 1) {
					$lead = $this->Leads_Model->get_lead( $id );
					if (!request()->getPost('data[name]')) {
						$params['name'] = '';
					}
					$message_vars = array(
						'{lead_name}' => $params['name'],
						'{lead_email}' => $params['email'],
						'{lead_assigned_staff}' => $lead['leadassigned'],
						'{email_signature}' => $template['from_name'],
					);
					$subject = strtr($template['subject'], $message_vars);
					$message = strtr($template['message'], $message_vars);

					$param = array(
						'from_name' => $template['from_name'],
						'email' => $params['email'],
						'subject' => $subject,
						'message' => $message,
						'created' => date( "Y.m.d H:i:s" )
					);
					if ($params['email']) {
						$this->db->table('email_queue')->insert( $param );
					}
				}
			}
			$return['success'] = true;
			$return['check'] = request()->getPost('custom');
			$return['message'] = $form['success_message'];
			return json_encode($return);
		} else {
			$return['success'] = false;
			$return['message'] = lang2('errormessage');
			return json_encode($return);
		}
	}
}