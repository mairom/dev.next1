<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
class Trivia extends BaseController
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
		echo 'Trivia';
	}
	function addtodo()
	{
		if (isset($_POST) && count($_POST) > 0) {
			if ($_POST['tododetail'] != '') {
				$params = array(
					'description' => $_POST['tododetail'],
					'staff_id' => session()->get('usr_id'),
					'date' => date('Y-m-d H:i:s'),
				);
				$this->db->table('todo')->insert($params);
				$data['insert_id'] = $this->db->insertID();
				$data['success'] = true;
				$data['messageDate'] = lang2('just_now');
				return response()->setJSON($data);
			} else {
				$data['success'] = false;
				return response()->setJSON($data);
			}
		}
	}
	public function donetodo()
	{
		if (request()->getPost('todo')) {
			$todo = request()->getPost('todo');
			$builder = $this->db->table('todo');
			$response = $builder->where('id', $todo)->update(['done' => 1]);
		}
	}
	public function undonetodo()
	{
		if (request()->getPost('todo')) {
			$todo = request()->getPost('todo');
			$builder = $this->db->table('todo');
			$response = $builder->where('id', $todo)->update(['done' => 0]);
		}
	}
	function removetodo()
	{
		$this->Trivia_Model->removetodo();
	}

	function addnote()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$description = $_POST['description'];
			$hasError = false;
			$data['message'] = '';
			if ($description == '' || !$description) {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('note');
			}
			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}
			if (!$hasError) {
				$params = array(
					'relation_type' => $_POST['relation_type'],
					'relation' => $_POST['relation'],
					'description' => $_POST['description'],
					'addedfrom' => session()->get('usr_id'),
					'created' => date('Y-m-d H:i:s'),
				);
				$this->db->table('notes')->insert($params);
				$data['insert_id'] = $this->db->insertID();
				if ($_POST['relation_type'] == 'project') {
					$template = $this->Emails_Model->get_template('project', 'new_note_to_customers');
					if ($template['status'] == 1) {
						$project = $this->Projects_Model->get_projects($_POST['relation']);
						$project_url = '' . base_url('area/projects/project/' . $_POST['relation'] . '') . '';
						switch ($project['status']) {
							case '1':
								$status_project = lang('notstarted');
								break;
							case '2':
								$status_project = lang('started');
								break;
							case '3':
								$status_project = lang('percentage');
								break;
							case '4':
								$status_project = lang('cancelled');
								break;
							case '5':
								$status_project = lang('complete');
								break;
						};
						if ($project['namesurname']) {
							$customer = $project['namesurname'];
						} else {
							$customer = $project['customercompany'];
						}
						$message_vars = array(
							'{customer}' => $customer,
							'{project_name}' => $project['name'],
							'{project_start_date}' => $project['start_date'],
							'{project_end_date}' => $project['deadline'],
							'{project_value}' => $project['projectvalue'],
							'{project_tax}' => $project['tax'],
							'{note}' => $_POST['description'],
							'{loggedin_staff}' => session()->get('staffname'),
							'{project_url}' => $project_url,
							'{project_status}' => $status_project,
							'{name}' => session()->get('staffname'),
							'{email_signature}' => session()->get('email'),
						);
						$subject = strtr($template['subject'], $message_vars);
						$message = strtr($template['message'], $message_vars);
						$param = array(
							'from_name' => $template['from_name'],
							'email' => $project['customeremail'],
							'subject' => $subject,
							'message' => $message,
							'created' => date("Y.m.d H:i:s")
						);
						if ($project['customeremail']) {
							$this->db->table('email_queue')->insert($param);
						}
					}
				}
				$data['success'] = true;
				$data['message'] = lang2('note') . ' ' . lang2('addmessage');
				return response()->setJSON($data);
			}
		}
	}
	function set_onsite_visit()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$title = request()->getPost('title');
			$description = request()->getPost('description');
			$customer_id = request()->getPost('customer_id');
			$staff_id = request()->getPost('staff_id');
			$start = request()->getPost('start');
			$end = request()->getPost('end');
			$data['message'] = '';
			$hasError = false;
			if ($title == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('title');
			} else if ($customer_id == '') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
			} else if ($staff_id == '') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('staff');
			} else if ($start == '') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('start') . ' ' . lang2('date');
			} else if ($end == '') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('end') . ' ' . lang2('date');
			} else if (strtotime($end) < strtotime($start)) {
				$hasError = true;
				$data['message'] = lang2('startdate') . ' ' . lang2('date_error') . ' ' . lang2('end') . ' ' . lang2('date');
			} else if ($description == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
			}
			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}
			if (!$hasError) {
				$params = array(
					'title' => $_POST['title'],
					'description' => $_POST['description'],
					'customer_id' => $_POST['customer_id'],
					'staff_id' => $_POST['staff_id'],
					'date' => $_POST['date'],
					'start' => $_POST['start'],
					'end' => $_POST['end'],
				);
				$this->db->table('meetings')->insert($params);
				$data['success'] = true;
				$data['message'] = lang2('onsite_visit_added');
				return response()->setJSON($data);
			}
		}
	}
	function create_discussion()
	{
		if (isset($_POST) && count($_POST) > 0) {
			switch (request()->getPost('show_to_customer')) {
				case 'true':
					$show_to_customer_value = 1;
					break;
				case 'false':
					$show_to_customer_value = 0;
					break;
			}
			$params = array(
				'relation_type' => $_POST['relation_type'],
				'relation' => $_POST['relation'],
				'subject' => $_POST['subject'],
				'description' => $_POST['description'],
				'contact_id' => $_POST['contact_id'],
				'staff_id' => $_POST['staff_id'],
				'show_to_customer' => $show_to_customer_value,
				'datecreated' => date('Y-m-d H:i:s'),
			);
			$this->db->table('discussions')->insert($params);
			$data['insert_id'] = $this->db->insertID();
			return response()->setJSON($data);
		}
	}
	function add_discussion_comment()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$params = array(
				'discussion_id' => $_POST['discussion_id'],
				'content' => $_POST['content'],
				'staff_id' => session()->get('usr_id'),
				'contact_id' => $_POST['contact_id'],
				'full_name' => $_POST['full_name'],
				'created' => date('Y-m-d H:i:s'),
			);
			$this->db->table('discussion_comments')->insert($params);
			$data['insert_id'] = $this->db->insertID();
			return response()->setJSON($data);
		}
	}
	function addreminder()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$params = array(
				'relation_type' => $_POST['relation_type'],
				'relation' => $_POST['relation'],
				'description' => $_POST['description'],
				'staff_id' => $_POST['staff'],
				'addedfrom' => session()->get('usr_id'),
				'date' => $_POST['date'],
			);
			$this->db->table('reminders')->insert($params);
			$data['insert_id'] = $this->db->insertID();
			return response()->setJSON($data);
		}
	}
	public function updatenote($id)
	{
		if ($id) {
			$description = request()->getPost('description', FILTER_SANITIZE_STRING);
			if ($description != '') {
				$builder = $this->db->table('notes');
				$response = $builder->where('id', $id)->update(['description' => $description]);
				$message = lang('note') . ' ' . lang('updatemessage');
				echo $message;
			} else {
				echo lang('invalidmessage') . ' ' . lang('note');
			}
		}
	}
	function removenote()
	{
		$this->Trivia_Model->removenote();
	}
	function remove_reminder()
	{
		$this->Trivia_Model->removereminder();
		echo true;
	}
	function removereminder()
	{
		$this->Trivia_Model->removereminder();
	}
	public function markreadreminder()
	{
		if (request()->getPost('reminder_id')) {
			$reminderId = request()->getPost('reminder_id');
			$builder = $this->db->table('reminders');
			$response = $builder->where('id', $reminderId)->update(['isnotified' => 1]);
		}
	}
	public function mark_read_notification($id)
	{
		if ($id) {
			$builder = $this->db->table('notifications');
			$response = $builder->where('id', $id)->update(['markread' => 1]);
		}
	}
}
