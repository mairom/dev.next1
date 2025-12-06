<?php

namespace App\Models;

use CodeIgniter\Model;
use PHPMailer;

class Emails_Model extends Model
{
	function get_email_templates()
	{
		return $this->db->table('email_templates')
			->where(['display' => 1, 'id_company' => session()->id_company])
			->get()
			->getResultArray();
	}

	function get_sent_emails()
	{
		return $this->db->table('email_queue')
			->select('*')
			//->join('staff', 'email_queue.email = staff.email', 'inner')
			//->where('status', 1)
			//->where('display', 1)
			->where('staff_id', session()->get('usr_id'))
			->orderBy('id', 'desc')
			->get()
			->getResultArray();
	}

	function getAdicionados($id_lead)
	{
		return $this->db->table('email_task_leads')
			->where('id_lead', $id_lead)
			->get()
			->getResultArray();
	}

	function getAdicionadosCustomer($id_customer)
	{
		return $this->db->table('email_task_customers')
			->where('id_customer', $id_customer)
			->get()
			->getResultArray();
	}

	function getTaskAutByUser($id)
	{
		return $this->db->table('email_task')
			->select('*, 
				(SELECT etapa FROM email_task_leads WHERE id_task = email_task.id_task and id_lead = ' . $id . ' order by id desc limit 1) as etapaAtual,
				(SELECT ultima_verificacao FROM email_task_leads WHERE id_task = email_task.id_task and id_lead = ' . $id . ' order by id desc limit 1) as ultima_verificacao')
			->where('email_task.id_company', session()->id_company)
			->where('email_task.tipo', "L")
			->orderBy('id_task', 'desc')
			->get()
			->getResultArray();


		//	echo $this->db->getLastQuery();
	}

	function getTaskAutByUserCustomer($id)
	{
		return
			$this->db->table('email_task')
			->select('*, 
				(SELECT etapa FROM email_task_customers WHERE id_task = email_task.id_task and id_customer = ' . $id . ' order by id desc limit 1) as etapaAtual,
				(SELECT ultima_verificacao FROM email_task_customers WHERE id_task = email_task.id_task and id_customer = ' . $id . ' order by id desc limit 1) as ultima_verificacao')
			->where('email_task.id_company', session()->id_company)
			->where('email_task.tipo', "C")
			->orderBy('id_task', 'desc')
			->get()

			//echo $this->db->getLastQuery();
			->getResultArray();
	}

	function getTaskAut($paraCron = false)
	{
		$builder = $this->db->table('email_task')
			->join('companies', 'companies.id_company = email_task.id_company', 'inner');

		if (!$paraCron) {
			$builder->where('email_task.id_company', session()->id_company);
		}

		if (request()->getPost('id_company')) {
			$builder->where('email_task.id_company', request()->getPost('id_company'));
		}

		$builder->orderBy('id_task', 'desc');

		if ($paraCron) {
			$builder->where('CAST(email_task.ultima_atualizacao AS DATE) <', date('Y-m-d'))
				->limit(12);
		}

		$result = $builder->get()->getResultArray();

	//	echo $this->db->getLastQuery();
	//	exit;

		if ($paraCron) {
			foreach ($result as $index => $row) {
				$this->db->table('email_task')
					->where('id_task', $row['id_task'])
					->update(['ultima_atualizacao' => date('Y-m-d H:i:s')]);


				if ($row['tipo'] == "L") {

					$leads = $this->db->table('email_task_leads')
						->select('*, IF(leads.name = "", leads.company, leads.name) as nm_lead, leads.id as id_lead,
						(SELECT email FROM leads_contatos WHERE id_lead = leads.id AND ctt_principal = "1" LIMIT 1) as emailCttPrincipal,
						(SELECT nm_contato FROM leads_contatos WHERE id_lead = leads.id AND ctt_principal = "1" LIMIT 1) as nm_contato_principal,
						(SELECT nm_contato FROM leads_contatos WHERE id_lead = leads.id LIMIT 1) as nm_contato,
						email_task_leads.etapa as etapa_task
						')
						->join('leads', 'leads.id = email_task_leads.id_lead', 'inner')
						->join('email_task', 'email_task.id_task  = email_task_leads.id_task', 'inner')
						->join('settings_email', 'settings_email.staff_id = email_task.respon_email', 'inner')
						->where('email_task_leads.id_task', $row['id_task'])
						->get()
						->getResultArray();

					//echo $this->db->getLastQuery();
					//exit;

				} else {
					$leads = $this->db->table('email_task_customers')
						->select('*, IF(customers.nome_fantasia != "", customers.nome_fantasia, customers.company) as nm_lead, customers.id as id_lead,
						(SELECT email FROM contacts WHERE customer_id = customers.id LIMIT 1) as emailCttPrincipal,
						(SELECT name FROM contacts WHERE customer_id = customers.id LIMIT 1) as nm_contato_principal,
					
						email_task_customers.etapa as etapa_task
						')
						->join('customers', 'customers.id = email_task_customers.id_customer', 'inner')
						->join('email_task', 'email_task.id_task  = email_task_customers.id_task', 'inner')
						->join('settings_email', 'settings_email.staff_id = email_task.respon_email', 'inner')
						->where('email_task_customers.id_task', $row['id_task'])
						->get()
						->getResultArray();
					//echo $leads->getCompiledSelect();
					//exit;
				}


				$result[$index]['leads'] = $leads;
				$result[$index]['dt_criado'] = date(get_dateFormat(), strtotime($row['dt_criado']));
			}
		} else {
			foreach ($result as $index => $row) {
				for ($i = 1; $i <= 15; $i++) {
					if ($row['template' . $i] != '') {
						$result[$index]['totalPeriodos'] = $i;
					}
				}
			}
		}

		return $result;
	}

	function get_email_template($id)
	{
		return $this->db->table('email_templates')
			->where('id', $id)
			->get()
			->getRowArray();
	}

	function get_email_template_ass()
	{
		return $this->db->table('email_assinaturas')
			->where('id_company', session()->id_company)
			->where('staff_id', session()->get('usr_id'))
			->get()
			->getRowArray();
	}

	function template_fields($name)
	{
		$result = $this->db->table('email_template_fields')
			->where('template_name', $name)
			->get()
			->getResultArray();

		if (empty($result)) {
			$result = $this->db->table('email_template_fields')
				->where('template_name', '0')
				->get()
				->getResultArray();
		}

		return $result;
	}

	function update_template($id, $params)
	{
		$this->db->table('email_templates')
			->where('id', $id)
			->update($params);

		return true;
	}

	function get_emails()
	{
		return $this->db->table('email_queue')
			->select('*')
			->where('status', 1)
			->orderBy('id', 'desc')
			->limit(10)
			->get()
			->getResultArray();
	}

	function email_sent($id)
	{
		$this->db->table('email_queue')
			->where('id', $id)
			->update(['status' => 0]);

		return true;
	}

	function get_template($relation, $name)
	{
		$template = $this->db->table('email_templates')
			->where(['relation' => $relation, 'name' => $name])
			->get()
			->getRowArray();

		if (!empty($template)) {
			return $template;
		} else {
			return [
				'status' => 0,
				'template' => $template
			];
		}
	}

	public function send_email($to, $from_name, $subject, $message = '', $attachment_path = '', $anexo = null, $id_company = null, $id_staf = null, $cc = null, $cco = null)
	{
		$settingsModel = new \App\Models\Settings_Model();
		$settings = $settingsModel->get_settings_ciuis($id_company, $id_staf);

		$assinatura = $this->get_email_template_ass('');

		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/Exception.php';
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/PHPMailer.php';
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/SMTP.php';
		$mail = new PHPMailer\PHPMailer\PHPMailer(true);
		try {
			//$mail->isSMTP();
			$mail->Host       = $settings['smtphost']; //'smtp.hostinger.com';
			$mail->SMTPAuth   = true;
			$mail->Username   = $settings['smtpusername']; //'teste@bmcomp.xyz';
			$mail->Password   = $settings['smtppassoword']; //'senhaTemp02';
			$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port       = $settings['smtpport'];
			$mail->CharSet = 'UTF-8';
			$mail->isSendmail();

			if ($cc != null) {
				$mail->addCC($cc);
			}

			if ($cco != null) {
				$mail->addBCC($cco);
			}

			if (isset($_POST['cc']) && !empty($_POST['cc'])) {
				$mail->addCC($_POST['cc']);
			}

			if (isset($_POST['cco']) && !empty($_POST['cco'])) {
				$mail->addBCC($_POST['cco']);
			}

			$mail->setFrom($settings['sendermail']);
			$mail->addAddress($to);
			//$mail->SMTPDebug = false;
			$mail->isHTML(true);

			$mail->Subject = $subject;
			$mail->Body    = $message . ' <br> ' . (isset($assinatura['message']) ? $assinatura['message'] : '');

			if ($anexo != null && file_exists($anexo)) {
				$mail->addAttachment(dirname(__FILE__) . '/../../uploads/anexos/' . $anexo);
			}



			return $mail->send();
		} catch (Exception $e) {
			return false;
		}
	}

	public function send_email2($to, $subject, $message, $anexo = null)
	{
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/Exception.php';
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/PHPMailer.php';
		include_once dirname(__FILE__) . '/../../vendor/PHPMailer/src/SMTP.php';
		$mail = new PHPMailer\PHPMailer\PHPMailer(true);
		try {
			// $mail->isSMTP();
			$mail->Host       = 'smtp.titan.email';
			$mail->SMTPAuth   = true;
			$mail->Username   = 'sandro.filipe@lead1.com.br';
			$mail->Password   = 'Discovery@19';
			$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port       = 587;
			$mail->CharSet = 'UTF-8';
			$mail->isSendmail();
			$mail->setFrom('cto@bluedoortech.com.br');
			$mail->addAddress($to);
			//$mail->SMTPDebug = true;
			$mail->isHTML(true);

			$mail->Subject = $subject;
			$mail->Body    = $message;

			if ($anexo != null && file_exists($anexo)) {
				$mail->addAttachment(dirname(__FILE__) . '/../../uploads/anexos/' . $anexo);
			}

			return $mail->send();
		} catch (Exception $e) {
			return false;
		}
	}
}
