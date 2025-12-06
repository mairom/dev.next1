<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Tickets extends AREA_Controller
{


	function index()
	{
		$data['title'] = lang('areatitletickets');
		$data['ttc'] = $this->Area_Model->ttc();
		$data['otc'] = $this->Area_Model->otc();
		$data['ipc'] = $this->Area_Model->ipc();
		$data['atc'] = $this->Area_Model->atc();
		$data['ctc'] = $this->Area_Model->ctc();
		$data['ysy'] = ($data['ttc'] > 0 ? number_format(($data['otc'] * 100) / $data['ttc']) : 0);
		$data['bsy'] = ($data['ttc'] > 0 ? number_format(($data['ipc'] * 100) / $data['ttc']) : 0);
		$data['twy'] = ($data['ttc'] > 0 ? number_format(($data['atc'] * 100) / $data['ttc']) : 0);
		$data['iey'] = ($data['ttc'] > 0 ? number_format(($data['ctc'] * 100) / $data['ttc']) : 0);
		$builder = $this->db->table('tickets');
		$builder->select('tickets.*, customers.type as type, customers.company as company, customers.namesurname as namesurname, departments.name as department, staff.staffname as staffmembername, contacts.name as contactname, contacts.surname as contactsurname, tickets.staff_id as stid, tickets.id as id');
		$builder->join('contacts', 'tickets.contact_id = contacts.id', 'left');
		$builder->join('customers', 'contacts.customer_id = customers.id', 'left');
		$builder->join('departments', 'tickets.department_id = departments.id', 'left');
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->where('tickets.contact_id', $_SESSION['contact_id']);
		$data['tickets'] = $builder->get()->getResultArray();
		$data['departments'] = $this->db->table('departments')
			->get()
			->getResultArray();

		//Detaylar 
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		return view('area/inc/header', $data);
		return view('area/tickets/index', $data);
		return view('area/inc/footer', $data);
	}

	public function create_ticket()
	{
		if (request()->getMethod() === 'post') {
			// Configuração do upload
			$file = request()->getFile('attachment');
			$uploadPath = WRITEPATH . 'uploads/attachments/';

			if (!$file->isValid() || $file->getSize() == 0) {
				return redirect()->back()->with('error', 'Arquivo inválido ou não selecionado.');
			}

			if (!is_dir($uploadPath)) {
				mkdir($uploadPath, 0777, true);
			}

			$newName = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
			$file->move($uploadPath, $newName);

			// Dados do ticket
			$params = [
				'contact_id'     => session()->get('contact_id'),
				'customer_id'    => session()->get('customer'),
				'email'          => session()->get('email'),
				'department_id'  => request()->getPost('department'),
				'priority'       => request()->getPost('priority'),
				'status_id'      => 1,
				'subject'        => request()->getPost('subject'),
				'message'        => request()->getPost('message'),
				'attachment'     => $newName,
				'date'           => date("Y-m-d H:i:s"),
			];

			session()->setFlashdata('ntf1', 'Ticket adicionado');
			$tickets_id = $this->Area_Model->add_tickets($params);

			// Envio de e-mails
			$this->sendEmailNotifications($tickets_id);

			return redirect()->to('area/tickets');
		}
	}

	private function sendEmailNotifications($tickets_id)
	{
		// Envio de e-mail para administradores
		$template = $this->Emails_Model->get_template('ticket', 'new_customer_ticket');
		if ($template['status'] == 1) {
			$ticket = $this->Tickets_Model->get_tickets($tickets_id);
			$admins = $this->Staff_Model->get_all_admins();

			$priority = match (request()->getPost('priority')) {
				'1' => lang('low'),
				'2' => lang('medium'),
				'3' => lang('high'),
				default => '',
			};

			$message_vars = [
				'{customer_id}'         => session()->get('customer'),
				'{customer}'            => session()->get('name'),
				'{name}'                => session()->get('name'),
				'{email_signature}'     => session()->get('email'),
				'{ticket_subject}'      => request()->getPost('subject'),
				'{ticket_message}'      => request()->getPost('message'),
				'{ticket_department}'   => $ticket['department'],
				'{ticket_priority}'     => $priority,
			];

			$subject = strtr($template['subject'], $message_vars);
			$message = strtr($template['message'], $message_vars);

			$param = [
				'from_name'  => $template['from_name'],
				'email'      => $admins['email'],
				'subject'    => $subject,
				'message'    => $message,
				'created'    => date("Y-m-d H:i:s"),
			];

			if ($param['email']) {
				$this->db->table('email_queue')->insert($param);
			}
		}

		// Envio de e-mail automático de resposta
		$template = $this->Emails_Model->get_template('ticket', 'ticket_autoresponse');
		if ($template['status'] == 1) {
			$ticket = $this->Tickets_Model->get_tickets($tickets_id);

			$priority = match (request()->getPost('priority')) {
				'1' => lang('low'),
				'2' => lang('medium'),
				'3' => lang('high'),
				default => '',
			};

			$message_vars = [
				'{customer_id}'         => session()->get('customer'),
				'{customer}'            => session()->get('name'),
				'{name}'                => session()->get('name'),
				'{email_signature}'     => session()->get('email'),
				'{ticket_subject}'      => request()->getPost('subject'),
				'{ticket_message}'      => request()->getPost('message'),
				'{ticket_department}'   => $ticket['department'],
				'{ticket_priority}'     => $priority,
			];

			$subject = strtr($template['subject'], $message_vars);
			$message = strtr($template['message'], $message_vars);

			$param = [
				'from_name'  => $template['from_name'],
				'email'      => session()->get('email'),
				'subject'    => $subject,
				'message'    => $message,
				'created'    => date("Y-m-d H:i:s"),
			];

			if ($param['email']) {
				$this->db->table('email_queue')->insert($param);
			}
		}
	}


	function ticket($id)
	{
		$permission = $this->Tickets_Model->check_tickets_permission($id, $_SESSION['contact_id']);
		if ($permission) {
			$data['title'] = lang('areatitletickets');
			$data['ticketstatustitle'] = lang2('alltickets');
			$data['ttc'] = $this->Area_Model->ttc();
			$data['otc'] = $this->Area_Model->otc();
			$data['ipc'] = $this->Area_Model->ipc();
			$data['atc'] = $this->Area_Model->atc();
			$data['ctc'] = $this->Area_Model->ctc();
			$data['ysy'] = ($data['ttc'] > 0 ? number_format(($data['otc'] * 100) / $data['ttc']) : 0);
			$data['bsy'] = ($data['ttc'] > 0 ? number_format(($data['ipc'] * 100) / $data['ttc']) : 0);
			$data['twy'] = ($data['ttc'] > 0 ? number_format(($data['atc'] * 100) / $data['ttc']) : 0);
			$data['iey'] = ($data['ttc'] > 0 ? number_format(($data['ctc'] * 100) / $data['ttc']) : 0);
			$data['ticket'] = $this->Tickets_Model->get_tickets($id);

			$builder = $this->db->table('tickets');
			$builder->select('*, customers.type as type, customers.company as company, customers.namesurname as namesurname, departments.name as department, staff.staffname as staffmembername, contacts.name as contactname, contacts.surname as contactsurname, tickets.staff_id as stid, tickets.id as id');
			$builder->join('contacts', 'tickets.contact_id = contacts.id', 'left');
			$builder->join('customers', 'contacts.customer_id = customers.id', 'left');
			$builder->join('departments', 'tickets.department_id = departments.id', 'left');
			$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
			$builder->where('contact_id', request()->getSession()->get('contact_id'));
			$data['dtickets'] = $builder->get()->getResultArray();

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();
			return view('area/inc/header', $data);
			return view('area/tickets/ticket', $data);
			return view('area/inc/footer', $data);
		} else {
			return redirect()->to('area/tickets');
		}
	}

	public function reply($id)
	{
		if (request()->getMethod() === 'post') {
			$hasError = false;
			$data['message'] = '';
	
			if (request()->getPost('message') === '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('message');
			}
	
			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}
	
			$ticket = $this->Tickets_Model->get_tickets($id);
			$filename = NULL;
	
			// Verificação e upload do arquivo
			$file = request()->getFile('file');
			if ($file && $file->isValid()) {
				$uploadPath = WRITEPATH . 'uploads/attachments/';
				if (!is_dir($uploadPath)) {
					mkdir($uploadPath, 0777, true);
				}
	
				$newName = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
				$file->move($uploadPath, $newName);
				$filename = $newName;
			}
	
			// Dados do reply
			$params = [
				'ticket_id'   => $id,
				'staff_id'    => $ticket['staff_id'],
				'contact_id'  => session()->get('contact_id'),
				'date'        => date("Y-m-d H:i:s"),
				'name'        => session()->get('name'),
				'message'     => request()->getPost('message'),
				'attachment'  => $filename,
			];
	
			// Adiciona notificação
			$contact = session()->get('name');
			$contactavatar = 'n-img.png';
			$this->db->table('notifications')->insert([
				'date'        => date('Y-m-d H:i:s'),
				'detail'      => $contact . ' ' . lang('replied') . ' ' . lang('ticket') . '-' . $id,
				'perres'      => $contactavatar,
				'staff_id'    => $ticket['staff_id'],
				'target'      => base_url('tickets/ticket/' . $id),
			]);
	
			// Atualiza o status do ticket
			$this->db->table('tickets')->where('id', $id)->update([
				'status_id'  => 1,
				'lastreply'  => date("Y-m-d H:i:s"),
			]);
	
			// Envio de e-mail
			$this->sendReplyEmail($ticket, $params);
	
			// Adiciona a resposta ao banco de dados
			$this->Tickets_Model->add_reply_contact($params);
	
			$data['success'] = true;
			$data['message'] = lang2('ticket') . ' ' . lang2('updatemessage');
			return response()->setJSON($data);
		}
	}
	
	private function sendReplyEmail($ticket, $params)
	{
		$template = $this->Emails_Model->get_template('ticket', 'ticket_reply_to_staff');
		if ($template['status'] == 1) {
			$customer = $ticket['type'] == 0 ? $ticket['company'] : $ticket['namesurname'];
	
			$priority = match ($ticket['priority']) {
				'1' => lang('low'),
				'2' => lang('medium'),
				'3' => lang('high'),
				default => '',
			};
	
			$email = $ticket['staffemail'] ?: $this->Staff_Model->get_all_admins()['email'];
	
			$message_vars = [
				'{customer}'            => $customer,
				'{name}'                => session()->get('name'),
				'{email_signature}'     => session()->get('email'),
				'{ticket_subject}'      => $ticket['subject'],
				'{ticket_message}'      => request()->getPost('message'),
				'{ticket_department}'   => $ticket['department'],
			];
	
			$subject = strtr($template['subject'], $message_vars);
			$message = strtr($template['message'], $message_vars);
	
			$param = [
				'from_name'  => $template['from_name'],
				'email'      => $email,
				'subject'    => $subject,
				'message'    => $message,
				'created'    => date("Y-m-d H:i:s"),
			];
	
			if ($email) {
				$this->db->table('email_queue')->insert($param);
			}
		}
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
			return redirect()->to('tickets/index');
		}
	}
}
