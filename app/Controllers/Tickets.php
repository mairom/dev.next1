<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Tickets extends BaseController
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



		$data['title'] = lang('tickets');

		$data['tickets'] = $this->Tickets_Model->get_all_tickets();

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		return view('tickets/index', $data);
	}



	public function create()
	{
		$data['title'] = lang('addticket');

		if ($this->Privileges_Model->check_privilege('tickets', 'create')) {

			$hasError = false;
			$data['message'] = '';
			if (empty(request()->getPost('subject')) || request()->getPost('subject') === 'undefined') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('subject');
			} elseif (empty(request()->getPost('customer')) || request()->getPost('customer') === 'undefined') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
			}
			// elseif (empty(request()->getPost('contact')) || request()->getPost('contact') === 'undefined') {
			//	$hasError = true;
			//	$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('contact');
			//}
			elseif (empty(request()->getPost('department')) || request()->getPost('department') === 'undefined') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('department');
			} elseif (empty(request()->getPost('priority')) || request()->getPost('priority') === 'undefined') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('priority');
			} elseif (empty(request()->getPost('message')) || request()->getPost('message') === 'undefined') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('message');
			}

			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}

			$appconfig = get_appconfig();
			$file = request()->getFile('file');

			if ($file && $file->isValid()) {
				$uploadPath = './uploads/attachments/';
				$config = [
					'upload_path' => $uploadPath,
					'allowed_types' => 'zip|rar|tar|gif|jpg|png|jpeg|pdf|doc|docx|xls|xlsx|txt|csv|ppt|opt',
					'max_size' => '9000',
					'file_name' => preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName()),
				];

				$data['success'] = false;
				return response()->setJSON($data);
			} else {
				$filename = null;
			}

			$params = [
				'contact_id' => request()->getPost('contact'),
				'customer_id' => request()->getPost('customer'),
				'department_id' => request()->getPost('department'),
				'priority' => request()->getPost('priority'),
				'status_id' => 1,
				'subject' => request()->getPost('subject'),
				'message' => request()->getPost('message'),
				'attachment' => $filename,
				'date' => date("Y.m.d H:i:s"),
				'staff_id' => session()->get('usr_id'),
			];

			session()->setFlashdata('ntf1', lang('ticketadded'));

			$tickets_id = $this->Tickets_Model->add_tickets($params);

			if ($tickets_id) {

				if (request()->getPost('custom_fields')) {
					$custom_fields = [
						'custom_fields' => request()->getPost('custom_fields')
					];
					$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'ticket', $tickets_id);
				}

				$template = $this->Emails_Model->get_template('ticket', 'new_ticket');

				if ($template['status'] == 1) {
					$ticket = $this->Tickets_Model->get_tickets($tickets_id);
					$customer = ($ticket['type'] == 0) ? $ticket['company'] : $ticket['namesurname'];

					$priority = match ($ticket['priority']) {
						'1' => lang('low'),
						'2' => lang('medium'),
						'3' => lang('high'),
						default => '',
					};

					$message_vars = [
						'{customer}' => $customer,
						'{name}' => session()->get('staffname'),
						'{email_signature}' => session()->get('email'),
						'{ticket_subject}' => request()->getPost('subject'),
						'{ticket_message}' => request()->getPost('message'),
						'{ticket_priority}' => $priority,
						'{ticket_department}' => $ticket['department'],
					];

					$subject = strtr($template['subject'], $message_vars);
					$message = strtr($template['message'], $message_vars);

					$param = [
						'from_name' => $template['from_name'],
						'email' => $ticket['customeremail'],
						'subject' => $subject,
						'message' => $message,
						'created' => date("Y.m.d H:i:s"),
					];

					if ($ticket['customeremail']) {
						$this->db->table('email_queue')->insert($param);
					}
				}

				$data['success'] = true;
				$data['message'] = lang2('ticket') . ' ' . lang2('createmessage');
				$data['id'] = $tickets_id;

				if ($appconfig['ticket_series']) {
					$ticket_number = $appconfig['ticket_series'] + 1;
					$this->Settings_Model->increment_series('ticket_series', $ticket_number);
				}

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




	function ticket($id)
	{

		if ($this->Privileges_Model->check_privilege('tickets', 'all')) {

			$data['ticket'] = $this->Tickets_Model->get_ticket_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('tickets', 'own')) {

			$data['ticket'] = $this->Tickets_Model->get_ticket_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));

			return redirect()->to(base_url('tickets'));
		}

		if ($data['ticket']) {

			$data['title'] = $data['ticket']['subject'];

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			$data['all_staff'] = $this->Staff_Model->get_all_staff();

			return view('tickets/ticket', $data);
		} else {

			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));

			return redirect()->to(base_url('tickets'));
		}
	}



	function assign_staff($id)
	{

		if ($this->Privileges_Model->check_privilege('tickets', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$params = array(

					'ticket_id' => $id,

					'staff_id' => request()->getPost('staff'),

				);

				$builder = $this->db->table('tickets');
				$response = $builder->where('id', $id)->update(['staff_id' => request()->getPost('staff')]);

				$this->db->table('notifications')->insert(array(

					'date' => date('Y-m-d H:i:s'),

					'detail' => ('' . session()->staffname . lang2('assigned') . ' ' . lang('ticket') . '-' . $id . ''),

					'staff_id' => request()->getPost('staff'),

					'perres' => session()->staffavatar,

					'target' => '' . base_url('tickets/ticket/' . $id . '') . ''

				));

				$user = $this->Staff_Model->get_staff(request()->getPost('staff'));



				$template = $this->Emails_Model->get_template('ticket', 'ticket_assigned');

				if ($template['status'] == 1) {

					$ticket = $this->Tickets_Model->get_tickets($id);

					if ($ticket['type'] == 0) {

						$customer = $ticket['company'];
					} else {

						$customer = $ticket['namesurname'];
					}



					switch ($ticket['priority']) {

						case '1':

							$priority = lang('low');

							break;

						case '2':

							$priority = lang('medium');

							break;

						case '3':

							$priority = lang('high');

							break;
					};



					$message_vars = array(

						'{assigned}' => $ticket['staffmembername'],

						'{customer}' => $customer,

						'{name}' => session()->get('staffname'),

						'{email_signature}' => session()->get('email'),

						'{ticket_subject}' => $ticket['subject'],

						'{ticket_message}' => $ticket['message'],

						'{ticket_priority}' => $priority,

						'{ticket_department}' => $ticket['department'],

					);

					$subject = strtr($template['subject'], $message_vars);

					$message = strtr($template['message'], $message_vars);



					$param = array(

						'from_name' => $template['from_name'],

						'email' => $ticket['staffemail'],

						'subject' => $subject,

						'message' => $message,

						'created' => date("Y.m.d H:i:s")

					);

					if ($ticket['staffemail']) {

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



	public function reply($id)
	{
		if ($this->Privileges_Model->check_privilege('tickets', 'edit')) {

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

			if (!$hasError) {
				$ticket = $this->Tickets_Model->get_tickets($id);

				$filename = null;
				if (request()->getFile('file') && request()->getFile('file')->isValid()) {
					$file = request()->getFile('file');
					$uploadPath = WRITEPATH . 'uploads/attachments/';
					$newName = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());

					if ($file->move($uploadPath, $newName)) {
						$filename = $file->getName();
					} else {
						$data['success'] = false;
						$data['message'] = $file->getErrorString();
						return response()->setJSON($data);
					}
				}

				$params = [
					'ticket_id'   => $id,
					'staff_id'    => session()->get('usr_id'),
					'contact_id'  => $ticket['contact_id'],
					'date'        => date("Y-m-d H:i:s"),
					'name'        => session()->get('staffname'),
					'message'     => request()->getPost('message'),
					'attachment'  => $filename,
				];

				$this->db->table('ticketreplies')->insert($params);

				$staffname = session()->get('staffname');
				$loggedinuserid = session()->get('usr_id');

				$this->db->table('logs')->insert([
					'date'     => date('Y-m-d H:i:s'),
					'detail'   => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang('replied') . ' <a href="tickets/ticket/' . $id . '"> ' . get_number('tickets', $id, 'ticket', 'ticket') . '</a>',
					'staff_id' => $loggedinuserid,
				]);

				$staffavatar = session()->get('staffavatar');

				$this->db->table('notifications')->insert([
					'date'        => date('Y-m-d H:i:s'),
					'detail'      => $staffname . ' ' . lang('replied') . ' ' . get_number('tickets', $id, 'ticket', 'ticket'),
					'contact_id'  => $ticket['contact_id'],
					'perres'      => $staffavatar,
					'target'      => base_url('area/tickets/ticket/' . $id),
				]);

				$this->db->table('tickets')->where('id', $id)->update([
					'status_id'  => 3,
					'lastreply'  => date("Y-m-d H:i:s"),
					'staff_id'   => $loggedinuserid,
				]);

				$template = $this->Emails_Model->get_template('ticket', 'ticket_reply_to_customer');

				if ($template['status'] == 1) {
					$customer = $ticket['type'] == 0 ? $ticket['company'] : $ticket['namesurname'];

					$priority = match ($ticket['priority']) {
						'1' => lang('low'),
						'2' => lang('medium'),
						'3' => lang('high'),
						default => lang('medium'),
					};

					$message_vars = [
						'{customer}'         => $customer,
						'{name}'             => session()->get('staffname'),
						'{email_signature}'  => session()->get('email'),
						'{ticket_subject}'   => $ticket['subject'],
						'{ticket_message}'   => $ticket['message'],
						'{ticket_priority}'  => $priority,
						'{ticket_department}' => $ticket['department'],
					];

					$subject = strtr($template['subject'], $message_vars);
					$message = strtr($template['message'], $message_vars);

					$param = [
						'from_name' => $template['from_name'],
						'email'     => $ticket['customeremail'],
						'subject'   => $subject,
						'message'   => $message,
						'created'   => date("Y-m-d H:i:s"),
					];

					if ($ticket['customeremail']) {
						$this->db->table('email_queue')->insert($param);
					}
				}

				$data['success'] = true;
				$data['message'] = lang2('ticket') . ' ' . lang2('updatemessage');
				return response()->setJSON($data);
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



	function markas()
	{

		if ($this->Privileges_Model->check_privilege('tickets', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = $_POST['name'];

				$params = array(

					'ticket_id' => $_POST['ticket_id'],

					'status_id' => $_POST['status_id'],

				);

				$data['success'] = true;

				$data['message'] = lang2('ticket') . ' ' . lang2('markas') . ' ' . $name;
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function remove($id)
	{

		if ($this->Privileges_Model->check_privilege('tickets', 'all')) {

			$ticket = $this->Tickets_Model->get_ticket_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('tickets', 'own')) {

			$ticket = $this->Tickets_Model->get_ticket_by_privileges($id, session()->usr_id);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}

		if ($ticket) {

			if ($this->Privileges_Model->check_privilege('tickets', 'delete')) {

				if (isset($ticket['id'])) {

					$this->Tickets_Model->delete_tickets($id, get_number('tickets', $id, 'ticket', 'ticket'));

					$data['success'] = true;

					$data['message'] = lang2('ticket') . ' ' . lang2('deletemessage');
				} else {

					show_error('Eror');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');
			}

			return response()->setJSON($data);
		} else {

			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));

			return redirect()->to(base_url('tickets'));
		}
	}



	function get_ticket($id)
	{


		if ($this->Privileges_Model->check_privilege('tickets', 'all')) {
			$ticket = $this->Tickets_Model->get_ticket_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('tickets', 'own')) {
			$ticket = $this->Tickets_Model->get_ticket_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			return redirect()->to(base_url('tickets'));
		}

		if ($ticket) {

			switch ($ticket['priority']) {
				case '1':
					$priority = lang('low');
					break;
				case '2':
					$priority = lang('medium');
					break;
				case '3':
					$priority = lang('high');
					break;
			};

			switch ($ticket['status_id']) {
				case '1':
					$status = lang('open');
					break;
				case '2':
					$status = lang('inprogress');
					break;
				case '3':
					$status = lang('answered');
					break;
				case '4':
					$status = lang('closed');
					break;
			};

			if ($ticket['type'] == 0) {
				$customer = $ticket['company'];
			} else $customer = $ticket['namesurname'];
			$replies = $this->db->table('ticketreplies')
				->where('ticket_id', $id)
				->get()
				->getResultArray();

			$data_ticketdetails = array(
				'id' => $ticket['id'],
				'subject' => $ticket['subject'],
				'message' => $ticket['message'],
				'relation' => $ticket['relation'],
				'relation_id' => $ticket['relation_id'],
				'staff_id' => $ticket['staff_id'],
				'contact_id' => $ticket['contact_id'],
				'contactname' => '' . $ticket['contactname'] . ' ' . $ticket['contactsurname'] . '',
				'priority' => $priority,
				'priority_id' => $ticket['priority'],
				'lastreply' => $ticket['lastreply'] ? (date(get_dateTimeFormat(), strtotime($ticket['lastreply']))) : lang2('n_a'),
				'status' => $status,
				'status_id' => $ticket['status_id'],
				'customer_id' => $ticket['customer_id'],
				'department' => $ticket['department'],
				'opened_date' => date(get_dateTimeFormat(), strtotime($ticket['date'])),
				'last_reply_date' => $ticket['lastreply'] ? (date(get_dateTimeFormat(), strtotime($ticket['lastreply']))) : lang2('n_a'),
				'attachment' => $ticket['attachment'],
				'customer' => $customer,
				'assigned_staff_name' => $ticket['staffmembername'],
				'replies' => $replies,
				'ticket_number' => get_number('tickets', $ticket['id'], 'ticket', 'ticket'),
			);
			return response()->setJSON($data_ticketdetails);
		} else {
			//session()->setFlashdata('ntf3', lang('you_dont_have_permission'));
			//return redirect()->to(base_url('tickets'));
			return response()->setJSON(['error' => 401, 'msg' => lang('you_dont_have_permission')]);
		}
	}
}
