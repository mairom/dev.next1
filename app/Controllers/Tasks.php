<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Tasks extends BaseController
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

		$data['title'] = lang2('tasks');

		$data['tasks'] = $this->Tasks_Model->get_all_tasks();

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		return view('tasks/index', $data);
	}



	function create()
	{

		if ($this->Privileges_Model->check_privilege('tasks', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = request()->getPost('name');

				$description =  request()->getPost('description');

				$priority = request()->getPost('priority');

				$assigned = request()->getPost('assigned');

				$startdate = request()->getPost('startdate');

				$duedate = request()->getPost('duedate');

				$status_id = request()->getPost('status_id');

				$relation = request()->getPost('relation');

				$hasError = false;

				$data['message'] = '';

				if ($relation == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('project');
				} else if ($name == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('task') . ' ' . lang2('name');
				} else if ($startdate == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('startdate');
				} else if ($duedate == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('duedate');
				} else if (strtotime($duedate) < strtotime($startdate)) {

					$hasError = true;

					$data['message'] = lang2('startdate') . ' ' . lang2('date_error') . ' ' . lang2('duedate');
				} else if ($assigned == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned');
				} else if ($priority == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('priority');
				} else if ($status_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('status');
				} else if ($description == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$appconfig = get_appconfig();

					$params = array(

						'name' => request()->getPost('name'),

						'description' => request()->getPost('description'),

						'priority' => request()->getPost('priority'),

						'assigned' => request()->getPost('assigned'),

						'relation_type' => request()->getPost('relation_type'),

						'relation' => request()->getPost('relation'),

						'milestone' => request()->getPost('milestone'),

						'public' => request()->getPost('public'),

						'billable' => request()->getPost('billable'),

						'visible' => request()->getPost('visible'),

						'hourly_rate' => request()->getPost('hourly_rate'),

						'startdate' => _pdate(request()->getPost('startdate')),

						'duedate' => _pdate(request()->getPost('duedate')),

						'addedfrom' => session()->get('usr_id'),

						'status_id' => request()->getPost('status_id'),

						'created' => date('Y-m-d H:i:s'),

					);

					$task = $this->Tasks_Model->add_task($params);

					if (request()->getPost('custom_fields')) {

						$custom_fields = array(

							'custom_fields' => request()->getPost('custom_fields')

						);

						$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'task', $task);
					}

					$this->db->table('notifications')->insert(array(

						'date' => date('Y-m-d H:i:s'),

						'detail' => (lang2('assignednewtask')),

						'perres' => session()->staffavatar,

						'staff_id' => $_POST['assigned'],

						'target' => '' . base_url('tasks/task/' . $task . '') . ''

					));

					$relation_type = request()->getPost('relation_type');

					if (isset($relation_type)) {

						if ($relation_type == 'project') {

							$this->db->table('logs')->insert(array(

								'date' => date('Y-m-d H:i:s'),

								'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('added') . ' <a href="tasks/task/' . $task . '">' . get_number('tasks', $task, 'task', 'task') . '</a>.'),

								'staff_id' => session()->usr_id,

								'project_id' => request()->getPost('relation'),

							));
						}
					}

					$template = $this->Emails_Model->get_template('task', 'new_task_assigned');

					if ($template['status'] == 1) {

						$tasks = $this->Tasks_Model->get_task_detail($task);

						$task_url = '' . base_url('tasks/task/' . $task . '') . '';

						$settings = $this->Settings_Model->get_settings_ciuis();

						switch ($tasks['status_id']) {

							case '1':

								$status = lang2('open');

								break;

							case '2':

								$status = lang2('inprogress');

								break;

							case '3':

								$status = lang2('waiting');

								break;

							case '4':

								$status = lang2('complete');

								break;

							case '5':

								$status = lang2('cancelled');

								break;
						};

						switch ($tasks['priority']) {

							case '1':

								$priority = lang2('low');

								break;

							case '2':

								$priority = lang2('medium');

								break;

							case '3':

								$priority = lang2('high');

								break;

							default:

								$priority = lang2('medium');

								break;
						};

						$message_vars = array(

							'{task_name}' => $tasks['name'],

							'{task_startdate}' => $tasks['startdate'],

							'{task_duedate}' => $tasks['duedate'],

							'{task_priority}' => $priority,

							'{task_url}' => $task_url,

							'{staffname}' => $tasks['assigner'],

							'{task_status}' => $status,

							'{company_name}' => $settings['company'],

							'{company_email}' => $settings['email'],

							'{name}' => session()->get('staffname'),

							'{email_signature}' => session()->get('email'),

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);

						$param = array(

							'from_name' => $template['from_name'],

							'email' => $tasks['staffemail'],

							'subject' => $subject,

							'message' => $message,

							'created' => date("Y.m.d H:i:s")

						);

						if ($tasks['staffemail']) {

							$this->db->table('email_queue')->insert($param);
						}
					}

					$data['message'] = lang2('task') . ' ' . lang2('createmessage');

					$data['success'] = true;

					$data['id'] = $task;

					if ($appconfig['task_series']) {

						$task_number = $appconfig['task_series'];

						$task_number = $task_number + 1;

						$this->Settings_Model->increment_series('task_series', $task_number);
					}

					return response()->setJSON($data);
				}
			}
		} else {

			$data['message'] = lang2('you_dont_have_permission');

			$data['success'] = false;

			return response()->setJSON($data);
		}
	}



	function update($id)
	{

		if ($this->Privileges_Model->check_privilege('tasks', 'all')) {

			$data['tasks']  = $this->Tasks_Model->get_task_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('tasks', 'own')) {

			$data['tasks']  = $this->Tasks_Model->get_task_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('tasks'));
		}

		if ($data['tasks']) {

			if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {

				if (isset($data['tasks']['id'])) {

					if (isset($_POST) && count($_POST) > 0) {

						$params = array(

							'name' => request()->getPost('name'),

							'description' => request()->getPost('description'),

							'priority' => request()->getPost('priority'),

							'status_id' => request()->getPost('status_id'),

							'assigned' => request()->getPost('assigned'),

							'public' => request()->getPost('public'),

							'billable' => request()->getPost('billable'),

							'visible' => request()->getPost('visible'),

							'hourly_rate' => request()->getPost('hourly_rate'),

							'startdate' => request()->getPost('startdate'),

							'duedate' =>  request()->getPost('duedate'),

						);

						$this->Tasks_Model->update_task($id, $params);

						// Custom Field Post

						if (request()->getPost('custom_fields')) {

							$custom_fields = array(

								'custom_fields' => request()->getPost('custom_fields')

							);

							$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'task', $id);
						}

						$data['success'] = true;

						$data['message'] = lang2('task') . ' ' . lang2('updatemessage');

						return response()->setJSON($data);
					} else {

						return view('tasks/index', $data);
					}
				} else {

					show_error('The task you are trying to edit does not exist.');
				}
			} else {

				$datas['success'] = false;

				$datas['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($datas);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('tasks'));
		}
	}



	function task($id)
	{

		if ($this->Privileges_Model->check_privilege('tasks', 'all')) {
			$data['task']  = $this->Tasks_Model->get_task_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('tasks', 'own')) {
			$data['task']  = $this->Tasks_Model->get_task_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('tasks'));
		}

		if ($data['task']) {
			$data['title'] = lang2('task');
			$task = $this->Tasks_Model->get_task($id);
			$rel_type = $task['relation_type'];

			return view('tasks/task', $data);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('tasks'));
		}
	}



	function addsubtask()
	{

		if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$params = array(

					'description' => $_POST['description'],

					'taskid' => $_POST['taskid'],

					'staff_id' => session()->get('usr_id'),

					'created' => date('Y-m-d H:i:s'),

				);

				$this->db->table('subtasks')->insert($params);

				$data['insert_id'] = $this->db->insertID();



				$template = $this->Emails_Model->get_template('task', 'task_comments');

				if ($template['status'] == 1) {

					$tasks = $this->Tasks_Model->get_task_detail($_POST['taskid']);

					$task_url = '' . base_url('tasks/task/' . $_POST['taskid'] . '') . '';

					switch ($tasks['status_id']) {

						case '1':

							$status = lang2('open');

							break;

						case '2':

							$status = lang2('inprogress');

							break;

						case '3':

							$status = lang2('waiting');

							break;

						case '4':

							$status = lang2('complete');

							break;

						case '5':

							$status = lang2('cancelled');

							break;
					};

					switch ($tasks['priority']) {

						case '1':

							$priority = lang2('low');

							break;

						case '2':

							$priority = lang2('medium');

							break;

						case '3':

							$priority = lang2('high');

							break;

						default:

							$priority = lang2('medium');

							break;
					};

					$message_vars = array(

						'{task_name}' => $tasks['name'],

						'{task_startdate}' => $tasks['startdate'],

						'{task_duedate}' => $tasks['duedate'],

						'{task_priority}' => $priority,

						'{task_url}' => $task_url, 'description',

						'{staffname}' => $tasks['assigner'],

						'{task_comment}' => $_POST['description'],

						'{task_status}' => $status,

						'{name}' => session()->get('staffname'),

						'{email_signature}' => session()->get('email'),

					);

					$subject = strtr($template['subject'], $message_vars);

					$message = strtr($template['message'], $message_vars);



					$param = array(

						'from_name' => $template['from_name'],

						'email' => $tasks['staffemail'],

						'subject' => $subject,

						'message' => $message,

						'created' => date("Y.m.d H:i:s")

					);

					if ($tasks['staffemail']) {

						$this->db->table('email_queue')->insert($param);
					}
				}

				$data['success'] = true;

				// return json_encode( $data );

			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function markascancelled()
	{

		if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {

			if (isset($_POST['task'])) {

				$task = $_POST['task'];

				$response = $this->db->table('tasks')->where('id', $task)->update(['status_id' => 5]);
				$response = $this->db->table('subtasks')->where('taskid', $task)->update(['complete' => 0]);

				$template = $this->Emails_Model->get_template('task', 'task_updated');

				if ($template['status'] == 1) {

					$tasks = $this->Tasks_Model->get_task_detail($task);

					$task_url = '' . base_url('tasks/task/' . $task . '') . '';

					switch ($tasks['status_id']) {

						case '1':

							$status = lang2('open');

							break;

						case '2':

							$status = lang2('inprogress');

							break;

						case '3':

							$status = lang2('waiting');

							break;

						case '4':

							$status = lang2('complete');

							break;

						case '5':

							$status = lang2('cancelled');

							break;
					};

					switch ($tasks['priority']) {

						case '1':

							$priority = lang2('low');

							break;

						case '2':

							$priority = lang2('medium');

							break;

						case '3':

							$priority = lang2('high');

							break;

						default:

							$priority = lang2('medium');

							break;
					};

					$message_vars = array(

						'{task_name}' => $tasks['name'],

						'{task_startdate}' => $tasks['startdate'],

						'{task_duedate}' => $tasks['duedate'],

						'{task_priority}' => $priority,

						'{task_url}' => $task_url, 'description',

						'{staffname}' => $tasks['assigner'],

						'{task_status}' => $status,

						'{logged_in_user}' => session()->get('staffname'),

						'{name}' => session()->get('staffname'),

						'{email_signature}' => session()->get('email'),

					);

					$subject = strtr($template['subject'], $message_vars);

					$message = strtr($template['message'], $message_vars);



					$param = array(

						'from_name' => $template['from_name'],

						'email' => $tasks['staffemail'],

						'subject' => $subject,

						'message' => $message,

						'created' => date("Y.m.d H:i:s")

					);

					if ($tasks['staffemail']) {

						$this->db->table('email_queue')->insert($param);
					}
				}

				$data['success'] = true;

				$data['message'] = lang2('task') . ' ' . lang2('markas') . ' ' . lang2('cancelled');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function markascompletetask()
	{

		if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {

			if (isset($_POST['task'])) {

				$task = $_POST['task'];

				$builder = $this->db->table('tasks');
				$response = $builder->where('id', $task)->update(['status_id' => 4, 'timer' => 0]);

				$builder = $this->db->table('subtasks');
				$response = $builder->where('taskid', $task)->update(['complete' => 1]);

				$end = date('Y-m-d H:i:s');
				$builder = $this->db->table('tasktimer');
				$response = $builder->where('task_id', $task)
					->where('status', 0)
					->update([
						'end' => $end,
						'note' => 'completed',
						'status' => 1
					]);

				$template = $this->Emails_Model->get_template('task', 'task_updated');

				if ($template['status'] == 1) {

					$tasks = $this->Tasks_Model->get_task_detail($task);

					$task_url = '' . base_url('tasks/task/' . $task . '') . '';

					switch ($tasks['status_id']) {

						case '1':

							$status = lang2('open');

							break;

						case '2':

							$status = lang2('inprogress');

							break;

						case '3':

							$status = lang2('waiting');

							break;

						case '4':

							$status = lang2('complete');

							break;

						case '5':

							$status = lang2('cancelled');

							break;
					};

					switch ($tasks['priority']) {

						case '1':

							$priority = lang2('low');

							break;

						case '2':

							$priority = lang2('medium');

							break;

						case '3':

							$priority = lang2('high');

							break;

						default:

							$priority = lang2('medium');

							break;
					};

					$message_vars = array(

						'{task_name}' => $tasks['name'],

						'{task_startdate}' => $tasks['startdate'],

						'{task_duedate}' => $tasks['duedate'],

						'{task_priority}' => $priority,

						'{task_url}' => $task_url, 'description',

						'{staffname}' => $tasks['assigner'],

						'{task_status}' => $status,

						'{logged_in_user}' => session()->get('staffname'),

						'{name}' => session()->get('staffname'),

						'{email_signature}' => session()->get('email'),

					);

					$subject = strtr($template['subject'], $message_vars);

					$message = strtr($template['message'], $message_vars);



					$param = array(

						'from_name' => $template['from_name'],

						'email' => $tasks['staffemail'],

						'subject' => $subject,

						'message' => $message,

						'created' => date("Y.m.d H:i:s")

					);

					if ($tasks['staffemail']) {

						$this->db->table('email_queue')->insert($param);
					}
				}

				$data['success'] = true;

				$data['message'] = lang2('task') . ' ' . lang2('markas') . ' ' . lang2('complete');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	public function completesubtasks()
	{
		if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {
			if (request()->getPost('subtask')) {
				$subtask = request()->getPost('subtask');

				$builder = $this->db->table('subtasks');
				$builder->where('id', $subtask)->update(['complete' => 1]);

				$data['success'] = true;
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}

	public function removesubtasks()
	{
		if ($this->Privileges_Model->check_privilege('tasks', 'delete')) {
			if (request()->getPost('subtask')) {
				$subtask = request()->getPost('subtask');

				$builder = $this->db->table('subtasks');
				$builder->where('id', $subtask)->delete();

				$data['success'] = true;
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}

	public function uncompletesubtasks()
	{
		if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {
			if (request()->getPost('task')) {
				$subtask = request()->getPost('task');

				$builder = $this->db->table('subtasks');
				$builder->where('id', $subtask)->update(['complete' => 0]);

				$data['success'] = true;
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}

	public function starttimer()
	{
		if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {
			if (request()->getPost()) {
				$params = [
					'task_id' => request()->getPost('task'),
					'status' => 0,
					'project_id' => request()->getPost('project'),
					'staff_id' => session()->get('usr_id'),
					'start' => date('Y-m-d H:i:s'),
					'end' => NULL
				];

				$builder = $this->db->table('tasktimer');
				$builder->insert($params);

				$builder = $this->db->table('tasks');
				$builder->where('id', request()->getPost('task'))->update(['timer' => 1]);

				$data['insert_id'] = $this->db->insertID();
				$data['success'] = true;
				$data['message'] = lang('timer_started');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}

	public function stoptimer()
	{
		if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {
			if (request()->getPost('task')) {
				$task = request()->getPost('task');
				$end = date('Y-m-d H:i:s');

				$builder = $this->db->table('tasktimer');
				$builder->where('task_id', $task)->where('status', 0)->update([
					'end' => $end,
					'note' => request()->getPost('note'),
					'status' => 1
				]);

				$builder = $this->db->table('tasks');
				$builder->where('id', request()->getPost('task'))->update(['timer' => 0]);

				$data['success'] = true;
				$data['message'] = lang('timer_stopped');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}

	public function deletefiles()
	{
		if ($this->Privileges_Model->check_privilege('tasks', 'delete')) {
			if (request()->getPost('fileid')) {
				$file = request()->getPost('fileid');

				$builder = $this->db->table('files');
				$builder->where('id', $file)->delete();

				$data['success'] = true;
				$data['message'] = lang('files') . ' ' . lang('deletemessage');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}




	public function add_file($id)
	{
		if ($this->Privileges_Model->check_privilege('tasks', 'edit')) {
			if ($id) {
				$uploadPath = './uploads/files/tasks/' . $id;

				if (!is_dir($uploadPath)) {
					mkdir($uploadPath, 0777, true);
				}

				$file = request()->getFile('file');

				if ($file && $file->isValid()) {
					$newName = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
					$file->move($uploadPath, $newName);

					if (is_file($uploadPath . '/' . $newName)) {
						$params = [
							'relation_type' => 'task',
							'relation' => $id,
							'file_name' => $newName,
							'created' => date("Y.m.d H:i:s"),
							'is_old' => '0',
						];

						$this->db->table('files')->insert($params);
					}

					$template = $this->Emails_Model->get_template('task', 'task_attachment');

					if ($template['status'] == 1) {
						$tasks = $this->Tasks_Model->get_task_detail($id);
						$task_url = base_url('tasks/task/' . $id);

						$status = match ($tasks['status_id']) {
							'1' => lang2('open'),
							'2' => lang2('inprogress'),
							'3' => lang2('waiting'),
							'4' => lang2('complete'),
							'5' => lang2('cancelled'),
							default => '',
						};

						$priority = match ($tasks['priority']) {
							'1' => lang2('low'),
							'2' => lang2('medium'),
							'3' => lang2('high'),
							default => lang2('medium'),
						};

						$message_vars = [
							'{task_name}' => $tasks['name'],
							'{task_startdate}' => $tasks['startdate'],
							'{task_duedate}' => $tasks['duedate'],
							'{task_priority}' => $priority,
							'{task_url}' => $task_url,
							'{staffname}' => $tasks['assigner'],
							'{task_status}' => $status,
							'{logged_in_user}' => session()->get('staffname'),
							'{name}' => session()->get('staffname'),
							'{email_signature}' => session()->get('email'),
						];

						$subject = strtr($template['subject'], $message_vars);
						$message = strtr($template['message'], $message_vars);

						$param = [
							'from_name' => $template['from_name'],
							'email' => $tasks['staffemail'],
							'subject' => $subject,
							'message' => $message,
							'created' => date("Y.m.d H:i:s"),
						];

						if ($tasks['staffemail']) {
							$this->db->table('email_queue')->insert($param);
						}
					}

					return response()->setJSON([
						'success' => true,
						'message' => lang2('file') . ' ' . lang2('uploadmessage'),
					]);
				} else {
					return response()->setJSON([
						'success' => false,
						'message' => $file->getErrorString(),
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




	function download_file($id)
	{

		if (isset($id)) {

			$fileData = $this->Expenses_Model->get_file($id);

			if ($fileData['is_old'] == '1') {

				if (is_file('./uploads/files/' . $fileData['file_name'])) {

					$this->load->helper('file');

					$this->load->helper('download');

					$data = file_get_contents('./uploads/files/' . $fileData['file_name']);

					force_download($fileData['file_name'], $data);
				} else {

					session()->setFlashdata('ntf4', lang2('filenotexist'));

					return redirect()->to('tasks/task/' . $fileData['relation']);
				}
			} else {

				if (is_file('./uploads/files/tasks/' . $fileData['relation'] . '/' . $fileData['file_name'])) {

					$this->load->helper('file');

					$this->load->helper('download');

					$data = file_get_contents('./uploads/files/tasks/' . $fileData['relation'] . '/' . $fileData['file_name']);

					force_download($fileData['file_name'], $data);
				} else {

					session()->setFlashdata('ntf4', lang2('filenotexist'));

					return redirect()->to('tasks/task/' . $fileData['relation']);
				}
			}
		}
	}



	public function delete_file($id)
	{
		if ($this->Privileges_Model->check_privilege('tasks', 'delete')) {
			if ($id) {
				$fileData = $this->Expenses_Model->get_file($id);

				if ($fileData) {
					$builder = $this->db->table('files');
					$response = $builder->where('id', $id)->delete();

					if ($fileData['is_old'] == '1') {
						if (is_file('./uploads/files/' . $fileData['file_name'])) {
							unlink('./uploads/files/' . $fileData['file_name']);
						}
					} else {
						if (is_file('./uploads/files/tasks/' . $fileData['relation'] . '/' . $fileData['file_name'])) {
							unlink('./uploads/files/tasks/' . $fileData['relation'] . '/' . $fileData['file_name']);
						}
					}

					if ($response) {
						$data['success'] = true;
						$data['message'] = lang('file') . ' ' . lang('deletemessage');
					} else {
						$data['success'] = false;
						$data['message'] = lang('errormessage');
					}

					return response()->setJSON($data);
				}
			} else {
				return redirect()->to('projects');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}




	public function add_files($id)
	{
		if ($id) {
			if (request()->getMethod() === 'post') {
				$uploadPath = './uploads/files/';

				$file = request()->getFile('file_name');

				if ($file && $file->isValid()) {
					$file->move($uploadPath);

					$params = [
						'relation_type' => 'task',
						'relation' => $id,
						'file_name' => $file->getName(),
						'created' => date("Y.m.d H:i:s"),
					];

					$this->db->table('files')->insert($params);

					$template = $this->Emails_Model->get_template('task', 'task_attachment');

					if ($template['status'] == 1) {
						$tasks = $this->Tasks_Model->get_task_detail($id);
						$settings = $this->Settings_Model->get_settings_ciuis();
						$task_url = base_url('tasks/task/' . $id);

						$status = match ($tasks['status_id']) {
							'1' => lang2('open'),
							'2' => lang2('inprogress'),
							'3' => lang2('waiting'),
							'4' => lang2('complete'),
							'5' => lang2('cancelled'),
							default => '',
						};

						$priority = match ($tasks['priority']) {
							'1' => lang2('low'),
							'2' => lang2('medium'),
							'3' => lang2('high'),
							default => lang2('medium'),
						};

						$message_vars = [
							'{task_name}' => $tasks['name'],
							'{task_startdate}' => $tasks['startdate'],
							'{task_duedate}' => $tasks['duedate'],
							'{task_priority}' => $priority,
							'{task_url}' => $task_url,
							'{staffname}' => $tasks['assigner'],
							'{task_status}' => $status,
							'{company_name}' => $settings['company'],
							'{company_email}' => $settings['email'],
							'{logged_in_user}' => session()->get('staffname'),
							'{name}' => session()->get('staffname'),
							'{email_signature}' => session()->get('email'),
						];

						$subject = strtr($template['subject'], $message_vars);
						$message = strtr($template['message'], $message_vars);

						$param = [
							'from_name' => $template['from_name'],
							'email' => $tasks['staffemail'],
							'subject' => $subject,
							'message' => $message,
							'created' => date("Y.m.d H:i:s"),
						];

						if ($tasks['staffemail']) {
							$this->db->table('email_queue')->insert($param);
						}
					}

					return redirect()->to('tasks/task/' . $id);
				}
			}
		}
	}




	function remove($id)
	{

		if ($this->Privileges_Model->check_privilege('tasks', 'all')) {

			$data['tasks']  = $this->Tasks_Model->get_task_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('tasks', 'own')) {

			$data['tasks']  = $this->Tasks_Model->get_task_by_privileges($id, session()->usr_id);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}

		if ($data['tasks']) {

			if ($this->Privileges_Model->check_privilege('tasks', 'delete')) {

				$number = get_number('tasks', $id, 'task', 'task');

				if (isset($id)) {

					$builder = $this->db->table('tasks');
					$response = $builder->where('id', $id)->delete();

					$builder = $this->db->table('subtasks');
					$response = $builder->where('taskid', $id)->delete();

					$builder = $this->db->table('tasktimer');
					$response = $builder->where('task_id', $id)->delete();

					$builder = $this->db->table('files');
					$response = $builder->where('relation_type', 'task')->where('relation', $id)->delete();

					$this->db->table('logs')->insert(array(

						'date' => date('Y-m-d H:i:s'),

						'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('deleted') . ' ' . lang2('task') . ' ' . $number . ''),

						'staff_id' => session()->usr_id

					));

					$data['success'] = true;

					$data['message'] = lang2('task') . ' ' . lang2('deletemessage');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');
			}

			return response()->setJSON($data);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('tasks'));
		}
	}



	function get_task($id)
	{

		$task = array();

		if ($this->Privileges_Model->check_privilege('tasks', 'all')) {

			$task  = $this->Tasks_Model->get_task_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('tasks', 'own')) {

			$task  = $this->Tasks_Model->get_task_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('tasks'));
		}

		if ($task) {

			$task = $this->Tasks_Model->get_task_detail($id);

			if ($task['milestone'] != NULL) {

				$milestone = $task['milestone'];
			} else {

				$milestone = lang2('nomilestone');
			}

			$settings = $this->Settings_Model->get_settings_ciuis();

			switch ($task['status_id']) {

				case '1':

					$status = lang2('open');

					break;

				case '2':

					$status = lang2('inprogress');

					break;

				case '3':

					$status = lang2('waiting');

					break;

				case '4':

					$status = lang2('complete');

					break;

				case '5':

					$status = lang2('cancelled');

					break;
			};

			switch ($task['priority']) {

				case '1':

					$priority = lang2('low');

					break;

				case '2':

					$priority = lang2('medium');

					break;

				case '3':

					$priority = lang2('high');

					break;

				default:

					$priority = lang2('medium');

					break;
			};

			switch ($task['public']) {

				case '1':

					$is_Public = true;

					break;

				case '0':

					$is_Public = false;

					break;
			}

			switch ($task['visible']) {

				case '1':

					$is_visible = true;

					break;

				case '0':

					$is_visible = false;

					break;
			}

			switch ($task['billable']) {

				case '1':

					$is_billable = true;

					break;

				case '0':

					$is_billable = false;

					break;
			}

			switch ($task['timer']) {

				case '1':

					$is_timer = true;

					break;

				case '0':

					$is_timer = false;

					break;
			}

			$taskdata = array(

				'id' => $task['id'],

				'name' => $task['name'],

				'description' => $task['description'],

				'staff' => $task['assigner'],

				'status' => $status,

				'priority' => $priority,

				'priority_id' => $task['priority'],

				'status_id' => $task['status_id'],

				'assigned' => $task['assigned'],

				'duedate' => date(get_dateFormat(), strtotime($task['duedate'])),

				'duedate_edit' => $task['duedate'],

				'startdate' => date(get_dateFormat(), strtotime($task['startdate'])),

				'startdate_edit' => $task['startdate'],

				'created' => date(get_dateFormat(), strtotime($task['created'])),

				'relation_type' => $task['relation_type'],

				'relation' => $task['relation'],

				'milestone' => $task['milestone'],

				'datefinished' => $task['datefinished'],

				'hourlyrate' => $task['hourly_rate'],

				'timer' => $is_timer,

				'public' => $is_Public,

				'visible' => $is_visible,

				'billable' => $is_billable,

				'task_number' => get_number('tasks', $task['id'], 'task', 'task'),



			);

			return response()->setJSON($taskdata);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('tasks'));
		}
	}



	function tasktimelogs($id)
	{

		$timelogs = $this->Tasks_Model->get_task_time_log($id);

		$data_timelogs = array();

		foreach ($timelogs as $timelog) {

			$task = $this->Tasks_Model->get_task($id);

			$start = $timelog['start'];

			$end = $timelog['end'];

			$timed_minute = intval(abs(strtotime($start) - strtotime($end)) / 60);

			$amount = $timed_minute / 60 * $task['hourly_rate'];

			if ($task['status_id'] != 5) {

				$data_timelogs[] = array(

					'id' => $timelog['id'],

					'start' => $timelog['start'],

					'end' => $timelog['end'],

					'staff' => $timelog['staffmember'],

					'status' => $timelog['status'],

					'timed' => $timed_minute,

					'amount' => $amount,

				);
			};
		};

		return response()->setJSON($data_timelogs);
	}



	function subtasks($id)
	{

		$subtasks = $this->Tasks_Model->get_subtasks($id);

		return response()->setJSON($subtasks);
	}



	function subtaskscomplete($id)
	{

		$subtaskscomplete = $this->Tasks_Model->get_subtaskscomplete($id);

		return response()->setJSON($subtaskscomplete);
	}



	function taskfiles($id)
	{

		if (isset($id)) {

			$files = $this->Tasks_Model->get_task_files($id);

			$data = array();

			foreach ($files as $file) {

				$ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);

				$type = 'file';

				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {

					$type = 'image';
				}

				if ($ext == 'pdf') {

					$type = 'pdf';
				}

				if ($ext == 'zip' || $ext == 'rar' || $ext == 'tar') {

					$type = 'archive';
				}

				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {

					$display = true;
				} else {

					$display = false;
				}

				if ($ext == 'pdf') {

					$pdf = true;
				} else {

					$pdf = false;
				}

				if ($file['is_old'] == '1') {

					$path = base_url('uploads/files/' . $file['file_name']);
				} else {

					$path = base_url('uploads/files/tasks/' . $id . '/' . $file['file_name']);
				}

				$data[] = array(

					'id' => $file['id'],

					'task_id' => $file['relation'],

					'file_name' => $file['file_name'],

					'created' => $file['created'],

					'display' => $display,

					'pdf' => $pdf,

					'type' => $type,

					'path' => $path,

				);
			}

			return response()->setJSON($data);
		}
	}



	function get_tasks()
	{

		$tasks = array();

		if ($this->Privileges_Model->check_privilege('tasks', 'all')) {

			$tasks = $this->Tasks_Model->get_all_tasks_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('tasks', 'own')) {

			$tasks = $this->Tasks_Model->get_all_tasks_by_privileges(session()->usr_id);
		}

		$data_tasks = array();

		foreach ($tasks as $task) {



			$settings = $this->Settings_Model->get_settings_ciuis();

			switch ($task['status_id']) {

				case '1':

					$status = lang2('open');

					$taskdone = '';

					break;

				case '2':

					$status = lang2('inprogress');

					$taskdone = '';

					break;

				case '3':

					$status = lang2('waiting');

					$taskdone = '';

					break;

				case '4':

					$status = lang2('complete');

					$taskdone = 'done';

					break;

				case '5':

					$status = lang2('cancelled');

					$taskdone = 'done';

					break;
			};

			switch ($task['relation_type']) {

				case 'project':

					$relationtype = lang2('project');

					break;

				case 'ticket':

					$relationtype = lang2('ticket');

					break;

				case 'proposal':

					$relationtype = lang2('proposal');

					break;
			};

			switch ($task['priority']) {

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


			$startdate =  date(get_dateFormat(), strtotime($task['startdate']));
			$duedate =  date(get_dateFormat(), strtotime($task['duedate']));
			$created =  date(get_dateFormat(), strtotime($task['created']));
			$datefinished =  date(get_dateFormat(), strtotime($task['datefinished']));


			$appconfig = get_appconfig();

			$data_tasks[] = array(

				'id' => $task['id'],

				'name' => $task['name'],

				'relationtype' => $relationtype,

				'status' => $status,

				'status_id' => $task['status_id'],

				'duedate' => $duedate,

				'startdate' => $startdate,

				'done' => $taskdone,

				'' . lang2('filterbystatus') . '' => $status,

				'' . lang2('filterbypriority') . '' => $priority,

				'task_number' => get_number('tasks', $task['id'], 'task', 'task'),

			);
		};

		return response()->setJSON($data_tasks);
	}
}
