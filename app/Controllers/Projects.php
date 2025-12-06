<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

use DateTime;
use DateTimeZone;

class Projects extends BaseController
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

		$data['title'] = lang2('projects');

		$data['projects'] = $this->Projects_Model->get_all_projects();

		return view('projects/index', $data);
	}



	function project($id)
	{

		$project = $this->Projects_Model->get_projects($id);

		if ($this->Privileges_Model->check_privilege('projects', 'all')) {
			$data['title'] = $project['name'];
			$data['projects'] = $project;
			return view('projects/project', $data);
		} else if ($this->Privileges_Model->check_privilege('projects', 'own')) {

			if (($project['staff_id'] == session()->usr_id) || ($this->Projects_Model->check_member($project['id'], session()->usr_id)) == 'true') {
				$data['title'] = $project['name'];
				$data['projects'] = $project;
				return view('projects/project', $data);
			} else {
				session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
				return redirect()->to(base_url('projects'));
			}
		}
	}



	function create()
	{

		if ($this->Privileges_Model->check_privilege('projects', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = request()->getPost('name');

				//$customer_id = request()->getPost('customer');

				$description = request()->getPost('description');

				//	$tax = request()->getPost('tax');

				//$value = request()->getPost( 'value' );

				$start_date = request()->getPost('start');

				$end_date = request()->getPost('deadline');

				$template = request()->getPost('template');

				if ($template == 'false' || $template == '0' || !$template) {

					$template = 0;
				} else if ($template == 'true' || $template == '1') {

					$template = 1;

					//	$customer_id = 0;

				}

				$hasError = false;

				$data['message'] = '';

				if ($name == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
				}
				//else if ($customer_id == '' && $template == false) {
				//	$hasError = true;
				//	$data['message'] = lang2('selectinvalidmessage'). ' ' .lang2('customer');
				//}
				else if ($start_date == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('startdate');
				} else if ($end_date == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('deadline');
				} else if (strtotime($end_date) < strtotime($start_date)) {

					$hasError = true;

					$data['message'] = lang2('startdate') . ' ' . lang2('date_error') . ' ' . lang2('deadline');
				}
				//else if ($value == '') {
				//	$hasError = true;
				//	$data['message'] = lang2('invalidmessage'). ' ' .lang2('projectcost');
				//	}
				//	else if ($tax == '') {
				//	$hasError = true;
				//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('tax');
				//	}
				else if ($description == '') {

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

						'name' => $name,

						'description' => request()->getPost('description'),

						//'customer_id' => $customer_id,

						//'projectvalue' => $value,

						//'tax' => $tax,

						'start_date' => $start_date,

						'deadline' => $end_date,

						'staff_id' => session()->get('usr_id'),

						'status_id' => 1,

						'template' => $template,

						'created' => date('Y-m-d H:i:s'),

					);



					$this->db->table('projects')->insert($params);

					$project_id = $this->db->insertID();

					$appconfig = get_appconfig();

					$number = $appconfig['project_series'] ? $appconfig['project_series'] : $project_id;

					$project_number = $appconfig['project_prefix'] . $number;

					$this->db->table('projects')
						->where('id', $project_id)
						->update(['project_number' => $project_number]);


					if ($appconfig['project_series']) {

						$project_number = $appconfig['project_series'];

						$project_number = $project_number + 1;

						$this->Settings_Model->increment_series('project_series', $project_number);
					}



					// Custom Field Post

					if (request()->getPost('custom_fields')) {

						$custom_fields = array(

							'custom_fields' => request()->getPost('custom_fields')

						);

						$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'project', $project_id);
					}

					$this->db->table('logs')->insert(array(

						'date' => date('Y-m-d H:i:s'),

						'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('added') . ' ' . lang2('project') . ' ' . ' <a href="projects/project/' . $project_id . '">' . get_number('projects', $project_id, 'project', 'project') . '</a>'),

						'staff_id' => session()->usr_id,

						'project_id' => $project_id,

						//'customer_id' => $customer_id

					));

					$template = $this->Emails_Model->get_template('project', 'project_notification');

					if ($template['status'] == 1) {

						$project = $this->Projects_Model->get_projects($project_id);

						$project_url = '' . base_url('area/projects/project/' . $project_id . '') . '';

						switch ($project['status']) {

							case '1':

								$status_project = lang2('notstarted');

								break;

							case '2':

								$status_project = lang2('started');

								break;

							case '3':

								$status_project = lang2('percentage');

								break;

							case '4':

								$status_project = lang2('cancelled');

								break;

							case '5':

								$status_project = lang2('complete');

								break;
						};



						//	if ( $project[ 'namesurname' ] ) {
						//		$customer = $project[ 'namesurname' ];
						//	} else {
						//		$customer = $project[ 'customercompany' ];
						//	}

						$message_vars = array(

							//	'{customer}' => $customer,

							'{project_name}' => $name,

							'{project_start_date}' => $_POST['start'],

							'{project_end_date}' => $_POST['deadline'],

							//'{project_value}' => $value,

							//'{project_tax}' => $tax,

							'{loggedin_staff}' => session()->get('staffname'),

							'{project_url}' => $project_url,

							'{project_status}' => $status_project,

							'{name}' => session()->get('staffname'),

							'{email_signature}' => session()->get('email'),

							'{project_description}' => $project['description']

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);



						$param = array(

							'from_name' => $template['from_name'],

							//	'email' => $project['customeremail'],

							'subject' => $subject,

							'message' => $message,

							'created' => date("Y.m.d H:i:s")

						);

						//if ($project['customeremail']) {

						//	$this->db->table('email_queue')->insert( $param );

						//}

					}

					$data['success'] = true;

					$data['message'] = lang2('project') . ' ' . lang2('createmessage');

					$data['id'] = $project_id;

					if ($appconfig['project_series']) {

						$project_number = $appconfig['project_series'];

						$project_number = $project_number + 1;

						$this->Settings_Model->increment_series('project_series', $project_number);
					}

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function update($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'all')) {

			$data['project'] = $this->Projects_Model->get_project_by_priviliges($id);
		} else if ($this->Privileges_Model->check_privilege('projects', 'own')) {

			$projects = $this->Projects_Model->get_projects($id);

			if (($projects['staff_id'] == session()->usr_id) || ($this->Projects_Model->check_member($projects['id'], session()->usr_id)) == 'true') {

				$data['project'] = $this->Projects_Model->get_projects($id);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}

		if ($data['project']) {

			if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

				if (isset($data['project']['id'])) {

					if (isset($_POST) && count($_POST) > 0) {

						$name = request()->getPost('name');

						//$customer_id = request()->getPost('customer');

						$description = request()->getPost('description');

						//	$tax = request()->getPost('tax');

						//$value = request()->getPost( 'value' );

						$start_date = request()->getPost('start');

						$end_date = request()->getPost('deadline');

						$template = request()->getPost('template');

						if ($template == 'false' || $template == '0' || !$template) {

							$template = 0;
						} else if ($template == 'true' || $template == '1') {

							$template = 1;

							//	$customer_id = 0;

						}

						$hasError = false;

						$data['message'] = '';

						if ($name == '') {

							$hasError = true;

							$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
						}
						//else if ($customer_id == '' && $template == 0) {
						//	$hasError = true;
						//	$data['message'] = lang2('selectinvalidmessage'). ' ' .lang2('choisecustomer');
						//}
						else if ($start_date == '') {

							$hasError = true;

							$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('startdate');
						} else if ($end_date == '') {

							$hasError = true;

							$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('deadline');
						} else if (strtotime($end_date) < strtotime($start_date)) {

							$hasError = true;

							$data['message'] = lang2('startdate') . ' ' . lang2('date_error') . ' ' . lang2('deadline');
						}
						// else if ($value == '') {
						//		$hasError = true;
						//	$data['message'] = lang2('invalidmessage'). ' ' .lang2('projectcost');
						//	}
						//else if ($tax == '') {
						//	$hasError = true;
						//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('tax');
						//	}]
						else if ($description == '') {

							$hasError = true;

							$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
						}

						if ($hasError) {

							$data['success'] = false;

							return response()->setJSON($data);
						}

						if (!$hasError) {

							$params = array(

								'name' => $name,

								'description' => request()->getPost('description'),

								//'customer_id' => $customer_id,

								//'projectvalue' => $value,

								//'tax' => $tax,

								'start_date' => $_POST['start'],

								'deadline' => $_POST['deadline'],

								'staff_id' => session()->get('usr_id'),

								'status_id' => 1,

								'created' => date('Y-m-d H:i:s'),

							);

							$this->Projects_Model->update2($id, $params);

							// Custom Field Post

							if (request()->getPost('custom_fields')) {

								$custom_fields = array(

									'custom_fields' => request()->getPost('custom_fields')

								);

								$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'project', $id);
							}

							$data['success'] = true;

							$data['message'] = lang2('project') . ' ' . lang2('updatemessage');

							return response()->setJSON($data);
						}
					} else {

						return view('projects/index', $data);
					}
				} else {

					show_error('The task you are trying to edit does not exist.');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('projects'));
		}
	}



	function createticket($id)
	{

		if ($this->Privileges_Model->check_privilege('tickets', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$contact_id = request()->getPost('contact');

				$customer_id = request()->getPost('customer');

				$department_id = request()->getPost('department');

				$subject = request()->getPost('subject');

				$message = request()->getPost('message');

				$priority = request()->getPost('priority');



				$hasError = false;

				$data['message'] = '';

				if ($subject == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('subject');
				} else if ($customer_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
				} else if ($contact_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('contact');
				} else if ($department_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('department');
				} else if ($priority == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('priority');
				} else if ($message == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('message');
				}



				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$appconfig = get_appconfig();

					$params = array(

						'contact_id' => request()->getPost('contact'),

						'customer_id' => request()->getPost('customer'),

						'department_id' => request()->getPost('department'),

						'priority' => request()->getPost('priority'),

						'status_id' => 1,

						'subject' => request()->getPost('subject'),

						'message' => request()->getPost('message'),

						'relation_id' => $id,

						'relation' => 'project',

						'date' => date(" Y.m.d H:i:s "),

						'ticket_number' => $appconfig['ticket_prefix'] . $appconfig['ticket_series'],

						'staff_id' => session()->usr_id

					);

					$tickets_id = $this->Tickets_Model->add_tickets($params);



					$template = $this->Emails_Model->get_template('ticket', 'new_ticket');

					if ($template['status'] == 1) {

						$ticket = $this->Tickets_Model->get_tickets($tickets_id);

						if ($ticket['type'] == 0) {

							$customer = $ticket['company'];
						} else {

							$customer = $ticket['namesurname'];
						}



						switch ($ticket['priority']) {

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

							'{customer}' => $customer,

							'{name}' => session()->get('staffname'),

							'{email_signature}' => session()->get('email'),

							'{ticket_subject}' => request()->getPost('subject'),

							'{ticket_message}' => request()->getPost('message'),

							'{ticket_priority}' => $priority,

							'{ticket_department}' => $ticket['department'],

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);

						$param = array(

							'from_name' => $template['from_name'],

							'email' => $ticket['customeremail'],

							'subject' => $subject,

							'message' => $message,

							'created' => date("Y.m.d H:i:s")

						);

						if ($ticket['customeremail']) {

							$this->db->table('email_queue')->insert($param);
						}
					}

					$data['success'] = true;

					$ticket_number = $appconfig['ticket_series'];

					$ticket_number = $ticket_number + 1;

					$this->Settings_Model->increment_series('ticket_series', $ticket_number);

					$data['message'] = lang2('ticket') . ' ' . lang2('createmessage');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function ticket_markas()
	{

		if ($this->Privileges_Model->check_privilege('tickets', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$response = $this->db->table('tickets')
					->where('id', $_POST['ticket_id'])
					->update(['status_id' => $_POST['status_id']]);

				$data['success'] = true;
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function tickets($id)
	{

		$tickets = $this->Projects_Model->get_all_tickets($id);

		return response()->setJSON($tickets);
	}



	function remove_ticket($id)
	{

		if ($this->Privileges_Model->check_privilege('tickets', 'delete')) {

			$tickets = $this->Projects_Model->get_tickets($id);

			if (isset($tickets['id'])) {

				if ($this->Projects_Model->delete_tickets($id)) {

					$data['success'] = true;

					$data['message'] = lang2('ticket') . ' ' . lang2('deletemessage');
				}
			} else

				show_error('Eror');
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function copyProject($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'create')) {

			$project = $this->Projects_Model->get_projects($id);

			$params = array(

				'customer_id' => request()->getPost('customer_id'),

				'name' => $project['name'] . '-Copy',

				'staff_id' => session()->get('usr_id'),

				'status_id' => 1,

				'created' => date('Y-m-d H:i:s'),

				'projectvalue' => $project['projectvalue'],

				'tax' => $project['tax'],

				'start_date' => request()->getPost('startdate'),

				'deadline' => request()->getPost('enddate'),

				'description' => $project['description'],

				'template' => 0

			);

			$this->db->table('projects')->insert($params);

			$projectId = $this->db->insertID();

			$loggedinuserid = session()->usr_id;

			$staffname = session()->staffname;

			$this->db->table('logs')->insert(array(

				'date' => date('Y-m-d H:i:s'),

				'detail' => ('' . $staffname . lang2('created_a_new_project')),

				'staff_id' => $loggedinuserid,

				'project_id' => $projectId,

			));



			// Items List to be copied:

			$isExpenses = request()->getPost('expenses');

			$isServices = request()->getPost('services');

			$isMilestones = request()->getPost('milestones');

			$isTasks = request()->getPost('tasks');

			$isPeoples = request()->getPost('peoples');

			$isFiles = request()->getPost('files');

			$isNotes = request()->getPost('notes');



			if ($isServices == 'true') {

				$services = $this->Projects_Model->get_project_services($id);

				$this->Projects_Model->copy_services($services, $projectId);
			}

			if ($isExpenses == 'true') {

				$expenses = $this->Expenses_Model->get_all_expenses_by_relation('project', $id);

				$this->Projects_Model->copy_expenses($expenses, $projectId);
			}

			if ($isMilestones == 'true') {

				$milestones = $this->Projects_Model->get_all_project_milestones($id);

				$this->Projects_Model->copy_milestones($milestones, $projectId);
			}

			if ($isTasks == 'true') {

				$tasks = $this->Tasks_Model->get_project_tasks($id);

				$this->Projects_Model->copy_tasks($tasks, $projectId);
			}

			if ($isPeoples == 'true') {

				$members = $this->Projects_Model->get_members($id);

				$this->Projects_Model->copy_members($members, $projectId);
			}

			if ($isFiles == 'true') {

				$files = $this->Projects_Model->get_project_files($id);

				$this->Projects_Model->copy_files($files, $projectId);
			}

			if ($isNotes == 'true') {

				$builder = $this->db->table('notes');
				$builder->select('*');
				$builder->where(['relation' => $id, 'relation_type' => 'project']);
				$notes = $builder->get()->getResultArray();

				$this->Projects_Model->copy_notes($notes, $projectId);
			}

			$data['success'] = true;

			$data['message'] = lang2('project') . ' ' . lang2('createmessage');

			$data['id'] = $projectId;

			return response()->setJSON($data);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function addservice()
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST)) {

				if (isset($_POST) && count($_POST) > 0) {

					$categoryid = request()->getPost('categoryid');

					$productid = request()->getPost('productid');

					$servicename = request()->getPost('servicename');

					$serviceprice = request()->getPost('serviceprice');

					$servicetax = request()->getPost('servicetax');

					$quantity = request()->getPost('quantity');

					$unit = request()->getPost('unit');

					$servicedescription = request()->getPost('servicedescription');

					$project_id = request()->getPost('projectid');

					$hasError = false;

					$data['message'] = '';

					if ($categoryid == '') {

						$hasError = true;

						$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('category');
					} else if ($productid == '') {

						$hasError = true;

						$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('product');
					} else if ($servicename == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('productname');
					} else if ($serviceprice == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('price');
					} else if ($servicetax == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('tax');
					} else if ($quantity == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('quantity');
					} else if ($unit == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('unit');
					} else if ($servicedescription == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
					}



					if ($hasError) {

						$data['success'] = false;

						return response()->setJSON($data);
					}



					if (!$hasError) {

						$params = array(

							'categoryid' => $categoryid,

							'productid' => $productid,

							'servicename' => $servicename,

							'serviceprice' => $serviceprice,

							'servicetax' => $servicetax,

							'quantity' => $quantity,

							'unit' => $unit,

							'servicedescription' => $servicedescription,

							'projectid' => request()->getPost('projectid'),

						);

						$this->db->table('projectservices')->insert($params);

						$this->db->table('logs')->insert(array(

							'date' => date('Y-m-d H:i:s'),

							'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('added') . ' ' . lang2('service') . ' ' . lang2('for') . ' ' . ' <a href="projects/project/' . $project_id . '">' . get_number('projects', $project_id, 'project', 'project') . '</a>'),

							'staff_id' => session()->usr_id,

							'project_id' => $project_id,

						));

						$project = $this->db->insertID();

						$data['success'] = true;

						$data['message'] = lang2('service') . ' ' . lang2('createmessage');

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



	function updateservice($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST) && $id) {

				if (isset($_POST) && count($_POST) > 0) {

					$categoryid = request()->getPost('categoryid');

					$productid = request()->getPost('productid');

					$servicename = request()->getPost('servicename');

					$serviceprice = request()->getPost('serviceprice');

					$servicetax = request()->getPost('servicetax');

					$servicedescription = request()->getPost('servicedescription');

					$quantity = request()->getPost('quantity');

					$unit = request()->getPost('unit');

					$project_id = request()->getPost('projectid');

					$hasError = false;

					$data['message'] = '';

					if ($categoryid == '') {

						$hasError = true;

						$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('category');
					} else if ($productid == '') {

						$hasError = true;

						$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('product');
					} else if ($servicename == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('productname');
					} else if ($serviceprice == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('price');
					} else if ($servicetax == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('tax');
					} else if ($quantity == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('quantity');
					} else if ($unit == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('unit');
					} else if ($servicedescription == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
					}



					if ($hasError) {

						$data['success'] = false;

						return response()->setJSON($data);
					}



					if (!$hasError) {

						$params = array(

							'categoryid' => $categoryid,

							'productid' => $productid,

							'servicename' => $servicename,

							'serviceprice' => $serviceprice,

							'servicetax' => $servicetax,

							'quantity' => $quantity,

							'unit' => $unit,

							'servicedescription' => $servicedescription,

							'projectid' => request()->getPost('projectid'),

						);

						$this->db->table('projectservices')
							->where('id', $id)
							->update($params);

						$this->db->table('logs')->insert(array(

							'date' => date('Y-m-d H:i:s'),

							'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('updated') . ' ' . lang2('service') . ' ' . lang2('for') . ' ' . ' <a href="projects/project/' . $project_id . '">' . get_number('projects', $project_id, 'project', 'project') . '</a>'),

							'staff_id' => session()->usr_id,

							'project_id' => $project_id,

						));

						$data['success'] = true;

						$data['message'] = lang2('service') . ' ' . lang2('updatemessage');

						return response()->setJSON($data);
					}
				}
			} else {

				if ($hasError) {

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



	function get_project_services($id)
	{

		$data = $this->Projects_Model->get_project_services($id);

		return response()->setJSON($data);
	}



	function get_products_by_category($id)
	{

		$data = $this->Projects_Model->get_products_by_category($id);

		return response()->setJSON($data);
	}



	function markas_complete()
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$params = array(

					'project_id' => $_POST['project_id'],

					'status_id' => '5',

				);

				$response = $this->Projects_Model->markas_complete();

				$template = $this->Emails_Model->get_template('project', 'project_status_changed');

				if ($template['status'] == 1) {

					$project = $this->Projects_Model->get_projects($_POST['project_id']);

					$project_url = '' . base_url('area/projects/project/' . $_POST['project_id'] . '') . '';

					switch ($project['status']) {

						case '1':

							$status_project = lang2('notstarted');

							break;

						case '2':

							$status_project = lang2('started');

							break;

						case '3':

							$status_project = lang2('percentage');

							break;

						case '4':

							$status_project = lang2('cancelled');

							break;

						case '5':

							$status_project = lang2('complete');

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

						'{loggedin_staff}' => session()->get('staffname'),

						'{project_url}' => $project_url,

						'{project_status}' => $status_project,

						'{name}' => session()->get('staffname'),

						'{email_signature}' => session()->get('email'),

						'{project_description}' => $project['description']

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

				$return['success'] = true;

				$return['message'] = lang2('project_complete');
			}
		} else {

			$return['success'] = false;

			$return['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($return);
	}



	function markas()
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$params = array(

					'project_id' => $_POST['project_id'],

					'status_id' => $_POST['status_id'],

				);

				$tickets = $this->Projects_Model->markas();



				$template = $this->Emails_Model->get_template('project', 'project_status_changed');

				if ($template['status'] == 1) {

					$project = $this->Projects_Model->get_projects($_POST['project_id']);

					$project_url = '' . base_url('area/projects/project/' . $_POST['project_id'] . '') . '';

					switch ($project['status']) {

						case '1':

							$status_project = lang2('notstarted');

							break;

						case '2':

							$status_project = lang2('started');

							break;

						case '3':

							$status_project = lang2('percentage');

							break;

						case '4':

							$status_project = lang2('cancelled');

							break;

						case '5':

							$status_project = lang2('complete');

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

						'{loggedin_staff}' => session()->get('staffname'),

						'{project_url}' => $project_url,

						'{project_status}' => $status_project,

						'{name}' => session()->get('staffname'),

						'{email_signature}' => session()->get('email'),

						'{project_description}' => $project['description']

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

				$data['success'] = true;
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function addmilestone($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = request()->getPost('name');

				$order = request()->getPost('order');

				$description = request()->getPost('description');

				$hasError = false;

				$data['message'] = '';

				if ($name == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
				} else if (request()->getPost('duedate') == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('duedate');
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

						'project_id' => $id,

						'name' => $name,

						'order' => $order,

						'duedate' => _phdate(request()->getPost('duedate')),

						'description' => $description,

						'created' => date('Y-m-d'),

						'color' => 'green',

					);

					$response = $this->Projects_Model->add_milestone($id, $params);

					$data['success'] = true;

					$data['message'] = lang2('milestone') . ' ' . lang2('createmessage');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function updatemilestone($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = request()->getPost('name');

				$description = request()->getPost('description');

				$hasError = false;

				$data['message'] = '';

				if ($name == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
				} else if (request()->getPost('duedate') == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('duedate');
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

						'order' => request()->getPost('order'),

						'name' => request()->getPost('name'),

						'description' => request()->getPost('description'),

						'duedate' => request()->getPost('duedate'),

					);

					$response = $this->Projects_Model->update_milestone($id, $params);

					$data['success'] = true;

					$data['message'] = lang2('milestone') . ' ' . lang2('createmessage');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function removemilestone()
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST['milestone'])) {

				$milestone = $_POST['milestone'];

				$response = $this->db->table('milestones')->delete(array('id' => $milestone));

				$data['success'] = true;

				$data['message'] = lang2('milestone') . lang2('deleted');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function addtask($id)
	{

		if ($this->Privileges_Model->check_privilege('tasks', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = request()->getPost('name');

				$description =  request()->getPost('description');

				$priority = request()->getPost('priority');

				$assigned = request()->getPost('assigned');



				$hasError = false;

				$data['message'] = '';

				if ($name == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('task') . ' ' . lang2('name');
				} else if ($assigned == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned');
				} else if ($priority == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('priority');
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

						'name' => $name,

						'description' => $description,

						'priority' => $priority,

						'assigned' => $assigned,

						'relation_type' => 'project',

						'relation' => $id,

						'milestone' => request()->getPost('milestone'),

						'public' => request()->getPost('public'),

						'billable' => request()->getPost('billable'),

						'visible' => request()->getPost('visible'),

						'hourly_rate' => request()->getPost('hourlyrate'),

						'startdate' => request()->getPost('startdate'),

						'duedate' => request()->getPost('duedate'),

						'addedfrom' => session()->get('usr_id'),

						'status_id' => 1,

						'created' => date('Y-m-d H:i:s'),

					);

					session()->setFlashdata('ntf1', '<b>' . lang2('task_added') . '</b>');

					$this->db->table('tasks')->insert($params);

					$task_id = $this->db->insertID();

					$appconfig = get_appconfig();

					$number = $appconfig['task_series'] ? $appconfig['task_series'] : $task_id;

					$task_number = $appconfig['task_prefix'] . $number;

					$this->db->table('tasks')
						->where('id', $task_id)
						->update(['task_number' => $task_number]);


					if ($appconfig['task_series']) {

						$task_number = $appconfig['task_series'];

						$task_number = $task_number + 1;

						$this->Settings_Model->increment_series('task_series', $task_number);
					}

					$loggedinuserid = session()->usr_id;

					$staffname = session()->staffname;

					$this->db->table('logs')->insert(array(

						'date' => date('Y-m-d H:i:s'),

						'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('added') . ' ' . lang2('task') . ' ' . lang2('for') . ' ' . ' <a href="projects/project/' . $id . '">' . get_number('projects', $id, 'project', 'project') . '</a>'),

						'staff_id' => $loggedinuserid,

						'project_id' => $id,

					));

					$template = $this->Emails_Model->get_template('task', 'new_task_assigned');

					if ($template['status'] == 1) {

						$tasks = $this->Tasks_Model->get_task_detail($task_id);

						$task_url = '' . base_url('tasks/task/' . $task_id . '') . '';

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

					$data['success'] = true;

					$data['message'] = lang2('task') . ' ' . lang2('createmessage');

					return response()->setJSON($data);
				}
			} else {

				if ($hasError) {

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



	function addmember()
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$staff = $_POST['staff'];

				$projectId = $_POST['project'];

				$members = $this->Projects_Model->get_members($projectId);

				$hasError = false;

				$data['message'] = '';

				if ($staff == '' || $staff == null) {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('staff');
				} else {

					foreach ($members as $member) {

						if (($member['project_id'] == $projectId) && ($member['staff_id'] == $staff)) {

							$hasError = true;

							$data['message'] = lang2('same') . ' ' . lang2('staff') . ' ' . lang2('duplicate_message');

							continue;
						}
					}
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$params = array(

						'staff_id' => $staff,

						'project_id' => $_POST['project'],

					);

					$this->db->table('projectmembers')->insert($params);

					$this->db->table('notifications')->insert(array(

						'date' => date('Y-m-d H:i:s'),

						'detail' => (lang2('assignednewproject')),

						'perres' => session()->staffavatar,

						'staff_id' => $_POST['staff'],

						'target' => '' . base_url('projects/project/' . $_POST['project'] . '') . ''

					));

					$this->db->table('logs')->insert(array(

						'date' => date('Y-m-d H:i:s'),

						'detail' => ('' . session()->staffname . lang2('added_a_member_project')),

						'staff_id' => session()->usr_id,

						'project_id' => $_POST['project'],

					));

					$member_detail = $this->Staff_Model->get_staff($_POST['staff']);



					$template = $this->Emails_Model->get_template('project', 'staff_added');

					if ($template['status'] == 1) {

						$project = $this->Projects_Model->get_projects($_POST['project']);

						$project_url = '' . base_url('projects/project/' . $_POST['project'] . '') . '';

						switch ($project['status']) {

							case '1':

								$status_project = lang2('notstarted');

								break;

							case '2':

								$status_project = lang2('started');

								break;

							case '3':

								$status_project = lang2('percentage');

								break;

							case '4':

								$status_project = lang2('cancelled');

								break;

							case '5':

								$status_project = lang2('complete');

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

							'{loggedin_staff}' => session()->get('staffname'),

							'{project_url}' => $project_url,

							'{staff}' => $member_detail['staffname'],

							'{project_status}' => $status_project,

							'{name}' => session()->get('staffname'),

							'{email_signature}' => session()->get('email'),

							'{project_description}' => $project['description']

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);



						$param = array(

							'from_name' => $template['from_name'],

							'email' => $member_detail['email'],

							'subject' => $subject,

							'message' => $message,

							'created' => date("Y.m.d H:i:s")

						);

						if ($member_detail['email']) {

							$this->db->table('email_queue')->insert($param);
						}
					}



					$data['success'] = true;

					$data['message'] = lang2('project') . ' ' . lang2('createmessage');

					$data['member'] = $member_detail;

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function unlinkmember($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($_POST['linkid'])) {

				$linkid = $_POST['linkid'];

				$response = $this->db->table('projectmembers')
					->where('id', $linkid)
					->delete();


				$data['success'] = true;

				$data['message'] = lang2('staff') . ' ' . lang2('deletemessage');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function delete_file($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($id)) {

				$fileData = $this->Expenses_Model->get_file($id);

				if ($fileData) {

					$response = $this->db->table('files')
						->where('id', $id)
						->delete();


					if ($fileData['is_old'] == '1') {

						if (is_file('./uploads/files/' . $fileData['file_name'])) {

							unlink('./uploads/files/' . $fileData['file_name']);
						}
					} else {

						if (is_file('./uploads/files/projects/' . $fileData['relation'] . '/' . $fileData['file_name'])) {

							unlink('./uploads/files/projects/' . $fileData['relation'] . '/' . $fileData['file_name']);
						}
					}

					if ($response) {

						$data['success'] = true;

						$data['message'] = lang2('file') . ' ' . lang2('deletemessage');
					} else {

						$data['success'] = false;

						$data['message'] = lang2('errormessage');
					}

					return response()->setJSON($data);
				}
			} else {

				return redirect()->to('projects');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	public function add_file($id)
	{
		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			if (isset($id)) {

				if (request()->getPost()) {

					// Verifica se o diretório existe, se não, cria-o
					$uploadPath = './uploads/files/projects/' . $id;
					if (!is_dir($uploadPath)) {
						mkdir($uploadPath, 0777, true);
					}

					// Captura o arquivo enviado
					$file = request()->getFile('file');

					if ($file && $file->isValid()) {

						$new_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$file->move($uploadPath, $new_name);

						if (is_file($uploadPath . '/' . $new_name)) {

							// Insere os detalhes do arquivo no banco de dados
							$params = [
								'relation_type' => 'project',
								'relation' => $id,
								'file_name' => $new_name,
								'created' => date("Y.m.d H:i:s"),
								'is_old' => '0',
							];
							$this->db->table('files')->insert($params);

							// Envio de email para o cliente
							$template = $this->Emails_Model->get_template('project', 'new_file_uploaded_to_customer');

							if ($template['status'] == 1) {
								$project = $this->Projects_Model->get_projects($id);
								$project_url = base_url('area/projects/project/' . $id);

								switch ($project['status']) {
									case '1':
										$status_project = lang2('notstarted');
										break;
									case '2':
										$status_project = lang2('started');
										break;
									case '3':
										$status_project = lang2('percentage');
										break;
									case '4':
										$status_project = lang2('cancelled');
										break;
									case '5':
										$status_project = lang2('complete');
										break;
								}

								$customer = $project['namesurname'] ? $project['namesurname'] : $project['customercompany'];

								$message_vars = [
									'{customer}' => $customer,
									'{project_name}' => $project['name'],
									'{project_start_date}' => $project['start_date'],
									'{project_end_date}' => $project['deadline'],
									'{project_value}' => $project['projectvalue'],
									'{project_tax}' => $project['tax'],
									'{loggedin_staff}' => session()->get('staffname'),
									'{project_url}' => $project_url,
									'{project_status}' => $status_project,
									'{name}' => session()->get('staffname'),
									'{email_signature}' => session()->get('email'),
									'{project_description}' => $project['description'],
								];

								$subject = strtr($template['subject'], $message_vars);
								$message = strtr($template['message'], $message_vars);

								$param = [
									'from_name' => $template['from_name'],
									'email' => $project['customeremail'],
									'subject' => $subject,
									'message' => $message,
									'created' => date("Y.m.d H:i:s"),
								];

								if ($project['customeremail']) {
									$this->db->table('email_queue')->insert($param);
								}
							}

							return response()->setJSON([
								'success' => true,
								'message' => lang2('file') . ' ' . lang2('uploadmessage')
							]);
						} else {
							return response()->setJSON([
								'success' => false,
								'message' => lang2('errormessage')
							]);
						}
					} else {
						return response()->setJSON([
							'success' => false,
							'message' => $file->getErrorString()
						]);
					}
				}
			}
		} else {
			return response()->setJSON([
				'success' => false,
				'message' => lang2('you_dont_have_permission')
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

					return redirect()->to('projects/project/' . $fileData['relation']);
				}
			} else {

				if (is_file('./uploads/files/projects/' . $fileData['relation'] . '/' . $fileData['file_name'])) {

					$this->load->helper('file');

					$this->load->helper('download');

					$data = file_get_contents('./uploads/files/projects/' . $fileData['relation'] . '/' . $fileData['file_name']);

					force_download($fileData['file_name'], $data);
				} else {

					session()->setFlashdata('ntf4', lang2('filenotexist'));

					return redirect()->to('projects/project/' . $fileData['relation']);
				}
			}
		}
	}



	function checkpinned()
	{

		if (isset($_POST['project'])) {

			$project = $_POST['project'];

			$response = $this->db->table('projects')
				->where('id', $project)
				->update(['pinned' => 1]);


			$data['success'] = true;

			$data['message'] = lang2('pinnedprojects');
		}

		return response()->setJSON($data);
	}



	function unpinned()
	{

		if (isset($_POST['pinnedproject'])) {

			$pinnedproject = $_POST['pinnedproject'];

			$response = $this->db->table('projects')
				->where('id', $pinnedproject)
				->update(['pinned' => 0]);


			$data['success'] = true;

			$data['message'] = lang2('unpinned') . ' ' . lang2('project');
		}

		return response()->setJSON($data);
	}



	function addexpense($id)
	{

		if ($this->Privileges_Model->check_privilege('expenses', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$category_id = request()->getPost('category');

				$customer_id = request()->getPost('customer');

				$account_id = request()->getPost('account');

				$title = request()->getPost('title');

				$date = request()->getPost('date');

				$amount = request()->getPost('amount');

				$description = request()->getPost('description');



				$hasError = false;

				if ($title == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('title');
				} else if ($amount == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('amount');
				} else if ($category_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('category');
				} else if ($account_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('account');
				}



				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}



				if (!$hasError) {

					$appconfig = get_appconfig();

					$params = array(

						'category_id' => $category_id,

						'staff_id' => session()->usr_id,

						'customer_id' => $customer_id,

						'relation_type' => 'project',

						'relation' => $id,

						'account_id' => $account_id,

						'title' => $title,

						'date' => $date,

						'created' => date('Y-m-d H:i:s'),

						'amount' => $amount,

						'description' => $description,

						'internal' => '1',

						'total_tax' => '0',

						'total_discount' => '0',

						'sub_total' => $amount,

					);

					$this->db->table('expenses')->insert($params);

					$expense_id = $this->db->insertID();

					$appconfig = get_appconfig();

					$number = $appconfig['expense_series'] ? $appconfig['expense_series'] : $expense_id;

					$expense_number = $appconfig['expense_prefix'] . $number;

					$this->db->table('expenses')
						->where('id', $expense_id)
						->update(['expense_number' => $expense_number]);


					if ($appconfig['expense_series']) {

						$expense_number = $appconfig['expense_series'];

						$expense_number = $expense_number + 1;

						$this->Settings_Model->increment_series('expense_series', $expense_number);
					}



					$item = array(

						'relation_type' => 'expense',

						'relation' => $expense_id,

						'product_id' => '',

						'code' => '',

						'name' => request()->getPost('name'),

						'description' => $description,

						'quantity' => '1',

						'unit' => '1',

						'price' => $amount,

						'tax' => '0',

						'discount' => '0',

						'total' => $amount,

					);

					$this->db->table('items')->insert($item);

					$loggedinuserid = session()->usr_id;

					$appconfig = get_appconfig();

					$this->db->table('logs')->insert(array(

						'date' => date('Y-m-d H:i:s'),

						'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('added') . ' ' . lang2('expense') . ' ' . lang2('for') . ' ' . ' <a href="projects/project/' . $id . '">' . get_number('projects', $id, 'project', 'project') . '</a>'),

						'staff_id' => $loggedinuserid,

						'project_id' => $id,

						'customer_id' => request()->getPost('customer')

					));

					$template = $this->Emails_Model->get_template('expense', 'expense_created');

					if ($template['status'] == 1) {

						$expense = $this->Expenses_Model->get_expenses($expense_id);

						if ($expense['individual']) {

							$customer = $expense['individual'];
						} else {

							$customer = $expense['customer'];
						}

						$message_vars = array(

							'{customer}' => $customer,

							'{expense_number}' => get_number('expenses', $expense['id'], 'expense', 'expense'),

							'{expense_title}' => $expense['title'],

							'{expense_category}' => $expense['category'],

							'{expense_date}' => $expense['date'],

							'{expense_description}' => $expense['description'],

							'{expense_amount}' => $expense['amount'],

							'{name}' => session()->get('staffname'),

							'{email_signature}' => session()->get('email'),

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);



						$param = array(

							'from_name' => $template['from_name'],

							'email' => $expense['customeremail'],

							'subject' => $subject,

							'message' => $message,

							'created' => date("Y.m.d H:i:s")

						);

						if ($expense['customeremail']) {

							$this->db->table('email_queue')->insert($param);
						}
					}

					$data['success'] = true;

					$data['message'] = lang2('expense') . ' ' . lang2('createmessage');

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function convert($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'create')) {

			$project = $this->Projects_Model->get_projects($id);

			if (isset($_POST) && count($_POST) > 0) {

				$services = $this->Projects_Model->get_project_services($id);

				$params = array(

					'token' => md5(uniqid()),

					'staff_id' => $project['staff_id'],

					'customer_id' => $project['customer_id'],

					'created' => date('Y-m-d H:i:s'),

					'status_id' => 3,

					'total_discount' => 0,

					'total_tax' => 0,

					'total' => request()->getPost('total'),

					'project_id' => $id,

					'sub_total' => request()->getPost('total'),

				);

				$this->db->table('invoices')->insert($params);

				$invoice = $this->db->insertID();

				$total = 0;

				$total_tax = 0;

				$sub_total = 0;

				foreach ($services as $service) {

					$this->db->table('items')->insert(array(

						'relation_type' => 'invoice',

						'relation' => $invoice,

						'name' => $service['servicename'],

						'description' => $service['servicedescription'],

						'quantity' => $service['quantity'],

						'unit' => $service['unit'],

						'price' => $service['serviceprice'],

						'tax' => $service['servicetax'],

						'discount' => 0,

						'total' => $service['quantity'] * $service['serviceprice'] + (($service['servicetax']) / 100 * $service['quantity'] * $service['serviceprice']),

					));

					$total += $service['quantity'] * $service['serviceprice'] + (($service['servicetax']) / 100 * $service['quantity'] * $service['serviceprice']);

					$total_tax += ($service['servicetax']) / 100 * $service['quantity'] * $service['serviceprice'];
				};

				$sub_total = $total - $total_tax;

				$response = $this->db->table('invoices')
					->where('id', $invoice)
					->update(['total' => $total, 'sub_total' => $sub_total, 'total_tax' => $total_tax]);

				$this->db->table($this->db->dbprefix . 'sales')->insert([
					'invoice_id' => $invoice,
					'status_id' => 3,
					'staff_id' => session()->usr_id,
					'customer_id' => $project['customer_id'],
					'total' => $total,
					'date' => date('Y-m-d H:i:s')
				]);


				$staffname = session()->staffname;

				$this->db->table('logs')->insert(array(

					'date' => date('Y-m-d H:i:s'),

					'detail' => ('' . $message = sprintf(lang2('projecttoinvoicelog'), $staffname, $project['id']) . ''),

					'staff_id' => session()->usr_id,

					'customer_id' => $project['customer_id'],

				));

				$response = $this->db->table('projects')->where('id', $id)->update(['invoice_id' => $invoice]);


				$data['id'] = $invoice;

				$data['success'] = true;

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function convertwithcost($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'create')) {

			$project = $this->Projects_Model->get_projects($id);

			if (isset($_POST) && count($_POST) > 0) {

				$services = $this->Projects_Model->get_project_services($id);

				$params = array(

					'token' => md5(uniqid()),

					'staff_id' => $project['staff_id'],

					'customer_id' => $project['customer_id'],

					'created' => date('Y-m-d H:i:s'),

					'status_id' => 3,

					'total_discount' => 0,

					'total_tax' => 0,

					'total' => request()->getPost('total'),

					'project_id' => $id,

					'sub_total' => request()->getPost('total'),

				);

				$this->db->table('invoices')->insert($params);

				$invoice = $this->db->insertID();



				$this->db->table('items')->insert(array(

					'relation_type' => 'invoice',

					'relation' => $invoice,

					'name' => request()->getPost('name'),

					'description' => request()->getPost('description'),

					'quantity' => 1,

					'unit' => 'Unit',

					'price' => request()->getPost('cost'),

					'tax' => request()->getPost('tax'),

					'discount' => 0,

					'total' => 1 * request()->getPost('cost') + ((request()->getPost('tax')) / 100 * 1 * request()->getPost('cost')),

				));



				$total = 0;

				$sub_total = 0;

				$total_tax = (request()->getPost('tax')) / 100 * 1 * request()->getPost('cost');

				$total = 1 * request()->getPost('cost') + ((request()->getPost('tax')) / 100 * 1 * request()->getPost('cost'));

				$sub_total = $total - $total_tax;

				$response = $this->db->table('invoices')->where('id', $invoice)->update([
					'total' => $total,
					'sub_total' => $sub_total,
					'total_tax' => $total_tax
				]);


				foreach ($services as $service) {

					$this->db->table('items')->insert(array(

						'relation_type' => 'invoice',

						'relation' => $invoice,

						'name' => $service['servicename'],

						'description' => $service['servicedescription'],

						'quantity' => $service['quantity'],

						'unit' => $service['unit'],

						'price' => 0,

						'tax' => 0,

						'discount' => 0,

						'total' => 0,

					));
				};



				$this->db->table('sales')->insert([
					'invoice_id' => $invoice,
					'status_id' => 3,
					'staff_id' => session()->get('usr_id'),
					'customer_id' => $project['customer_id'],
					'total' => $total,
					'date' => date('Y-m-d H:i:s')
				]);


				$staffname = session()->staffname;

				$this->db->table('logs')->insert(array(

					'date' => date('Y-m-d H:i:s'),

					'detail' => ('' . $message = sprintf(lang2('projecttoinvoicelog'), $staffname, $project['id']) . ''),

					'staff_id' => session()->usr_id,

					'customer_id' => $project['customer_id'],

				));

				$response = $this->db->table('projects')->where('id', $id)->update(['invoice_id' => $invoice]);

				$data['id'] = $invoice;

				$data['success'] = true;

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function removeService($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			$services = $this->Projects_Model->get_project_service($id);

			if (isset($services['id'])) {

				$number = get_number('projects', $services['projectid'], 'project', 'project');

				$this->Projects_Model->delete_service($id, $number);

				$data['success'] = true;

				$data['message'] = lang2('service') . ' ' . lang2('deletemessage');
			} else {

				$data['success'] = false;

				$data['message'] = lang2('servicedoesnotexist');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	/* Remove Project */

	function remove($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'all')) {

			$project = $this->Projects_Model->get_project_by_priviliges($id);
		} else if ($this->Privileges_Model->check_privilege('projects', 'own')) {

			$projects = $this->Projects_Model->get_projects($id);

			if (($projects['staff_id'] == session()->usr_id) || ($this->Projects_Model->check_member($projects['id'], session()->usr_id)) == 'true') {

				$project = $projects;
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}

		if ($project) {

			if ($this->Privileges_Model->check_privilege('projects', 'delete')) {

				if (isset($project['id'])) {

					$this->Projects_Model->delete_projects($id, get_number('projects', $id, 'project', 'project'));

					$data['success'] = true;
				} else {

					show_error('The projects you are trying to delete does not exist.');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');
			}

			return response()->setJSON($data);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function customer_proposals($id)
	{

		$project = $this->Projects_Model->get_projects($id);

		if (isset($project['id'])) {

			$proposals = $this->Proposals_Model->customer_proposals($project['customer_id']);

			return response()->setJSON($proposals);
		}
	}



	function get_proposals($id)
	{

		$project = $this->Projects_Model->get_projects($id);

		if (isset($project['id'])) {

			$proposals = $this->Proposals_Model->project_proposals($id);

			$data_proposals = array();

			foreach ($proposals as $proposal) {

				$pro = $this->Proposals_Model->get_proposals($proposal['id'], $proposal['relation_type']);

				if ($pro['relation_type'] == 'customer') {

					if ($pro['customercompany'] === NULL) {

						$customer = $pro['namesurname'];
					} else $customer = $pro['customercompany'];
				}

				if ($pro['relation_type'] == 'lead') {

					$customer = $pro['leadname'];
				}


				$date =  date(get_dateFormat(), strtotime($proposal['date']));
				$opentill =  date(get_dateFormat(), strtotime($proposal['opentill']));


				switch ($proposal['status_id']) {

					case '0':

						$status = lang2('quote') . ' ' . lang2('request');

						$class = 'proposal-status-open';

						break;

					case '1':

						$status = lang2('draft');

						$class = 'proposal-status-accepted';

						break;

					case '2':

						$status = lang2('sent');

						$class = 'proposal-status-sent';

						break;

					case '3':

						$status = lang2('open');

						$class = 'proposal-status-open';

						break;

					case '4':

						$status = lang2('revised');

						$class = 'proposal-status-revised';

						break;

					case '5':

						$status = lang2('declined');

						$class = 'proposal-status-declined';

						break;

					case '6':

						$status = lang2('accepted');

						$class = 'proposal-status-accepted';

						break;
				};

				$appconfig = get_appconfig();

				$data_proposals[] = array(

					'id' => $proposal['id'],

					'assigned' => $proposal['assigned'],

					'prefix' => $appconfig['proposal_prefix'],

					'longid' => get_number('proposals', $proposal['id'], 'proposal', 'proposal'),

					'subject' => $proposal['subject'],

					'customer' => $customer,

					'relation' => $proposal['relation'],

					'date' => $date,

					'opentill' => $opentill,

					'status' => $status,

					'status_id' => $proposal['status_id'],

					'staff' => $proposal['staffmembername'],

					'staffavatar' => $proposal['staffavatar'],

					'total' => $proposal['total'],

					'class' => $class,

					'relation_type' => $proposal['relation_type'],

					'' . lang2('relationtype') . '' => $proposal['relation_type'],

					'' . lang2('filterbystatus') . '' => $status,

					'' . lang2('filterbycustomer') . '' => $customer,

					'' . lang2('filterbyassigned') . '' => $proposal['staffmembername'],

				);
			};

			return response()->setJSON($data_proposals);
		}
	}



	function link_proposal($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'edit')) {

			$project = $this->Projects_Model->get_projects($id);

			if (isset($project['id'])) {

				$pro_id = request()->getPost('proposal');

				$check = $this->Proposals_Model->check_project_id($id, $pro_id);

				$hasError = false;

				if ($pro_id == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('proposal');
				} else if ($check == 'exist') {

					$hasError = true;

					$data['message'] = lang2('proposal') . ' ' . lang2('already_linked') . ' ' . lang2('project');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {
					$response = $this->db->table('proposals')->where('id', $pro_id)->update(['project_id' => $id]);
					$data['message'] = lang2('proposal') . ' ' . lang2('link_message');

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



	function proposal_create($id)
	{

		if ($this->Privileges_Model->check_privilege('proposals', 'create')) {

			$project = $this->Projects_Model->get_projects($id);

			if (isset($_POST) && count($_POST) > 0) {

				$proposal_type = request()->getPost('proposal_type');

				$customer = request()->getPost('customer');

				$subject = request()->getPost('subject');

				$assigned = request()->getPost('assigned');

				$proposal_type = request()->getPost('proposal_type');

				$date = request()->getPost('date');

				$opentill = request()->getPost('opentill');

				$total = request()->getPost('total');

				$lead = request()->getPost('lead');

				$status = request()->getPost('status');

				$total_items = request()->getPost('total_items');

				$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);



				$hasError = false;

				$data['message'] = '';

				if ($subject == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('subject');
				} else if ($date == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('issue') . ' ' . lang2('date');
				} else if ($opentill == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('end') . ' ' . lang2('date');
				} else if (strtotime($opentill) < strtotime($date)) {

					$hasError = true;

					$data['message'] = lang2('issue') . ' ' . lang2('date') . ' ' . lang2('date_error') . ' ' . lang2('end') . ' ' . lang2('date');
				} else if ($assigned == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned');
				} else if ($status == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('status');
				} else if ($total_items == '0') {

					$hasError = true;

					$data['message'] = lang2('invalid_items');
				} else if ($total == 0) {

					$hasError = true;

					$data['message'] = lang2('invalid_total');
				}



				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$appconfig = get_appconfig();

					$allow_comment = request()->getPost('comment');

					if ($allow_comment != true) {

						$comment_allow = 0;
					} else {

						$comment_allow = 1;
					};

					$params = array(

						'token' => md5(uniqid()),

						'subject' => request()->getPost('subject'),

						'content' => request()->getPost('content'),

						'date' => _pdate(request()->getPost('date')),

						'created' => date('Y-m-d H:i:s'),

						'opentill' => _pdate(request()->getPost('opentill')),

						'relation_type' => 'customer',

						'relation' => $project['customer_id'],

						'assigned' => request()->getPost('assigned'),

						'project_id' => $id,

						'addedfrom' => session()->usr_id,

						'datesend' => _pdate(request()->getPost('datesend')),

						'comment' => $comment_allow,

						'status_id' => request()->getPost('status'),

						'invoice_id' => request()->getPost('invoice'),

						'dateconverted' => request()->getPost('dateconverted'),

						'sub_total' => request()->getPost('sub_total'),

						'total_discount' => request()->getPost('total_discount'),

						'total_tax' => request()->getPost('total_tax'),

						'total' => request()->getPost('total'),

					);

					$proposals_id = $this->Projects_Model->proposal_add($params);

					$template = $this->Emails_Model->get_template('proposal', 'send_proposal');

					if ($template['status'] == 1) {

						$pro = $this->Proposals_Model->get_pro_rel_type($proposals_id);

						$rel_type = $pro['relation_type'];

						$proposal = $this->Proposals_Model->get_proposals($proposals_id, $rel_type);

						if ($rel_type == 'customer') {

							$customer = $proposal['customercompany'] ? $proposal['customercompany'] : $proposal['namesurname'];
						} else {

							$customer = $proposal['leadname'];
						}

						$link = base_url('share/proposal/' . $proposal['token'] . '');

						$message_vars = array(

							'{proposal_to}' => $customer,

							'{customer}' => $customer,

							'{proposal_number}' => $proposals_id,

							'{proposal_link}' => $link,

							'{subject}' => request()->getPost('subject'),

							'{details}' => request()->getPost('content'),

							'{name}' => session()->get('staffname'),

							'{email_signature}' => session()->get('email'),

							'{open_till}' => _pdate(request()->getPost('opentill'))

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);

						$param = array(

							'from_name' => $template['from_name'],

							'email' => $proposal['toemail'],

							'subject' => $subject,

							'message' => $message,

							'created' => date("Y.m.d H:i:s")

						);

						if ($proposal['toemail']) {

							$this->db->table('email_queue')->insert($param);
						}
					}

					$data['success'] = true;

					$data['message'] = lang2('proposal') . ' ' . lang2('createmessage');

					$data['proposal_id'] = $proposals_id;

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function download_pdf($id)
	{

		if (isset($id)) {

			$appconfig = get_appconfig();

			$file_name = '' . get_number('project', $id, 'project', 'project') . '.pdf';

			if (is_file('./uploads/files/expenses/' . $id . '/' . $file_name)) {

				$this->load->helper('file');

				$this->load->helper('download');

				$data = file_get_contents('./uploads/files/expenses/' . $id . '/' . $file_name);

				force_download($file_name, $data);
			} else {

				session()->setFlashdata('ntf4', lang2('filenotexist'));

				return redirect()->to('expenses/receipt/' . $id);
			}
		} else {

			return redirect()->to('expenses/receipt/' . $id);
		}
	}



	function create_pdf($id)
	{

		if ($this->Privileges_Model->check_privilege('projects', 'all')) {

			$project = $this->Projects_Model->get_project_by_priviliges($id);
		} else if ($this->Privileges_Model->check_privilege('projects', 'own')) {

			$projects = $this->Projects_Model->get_projects($id);

			if (($projects['staff_id'] == session()->usr_id) || ($this->Projects_Model->check_member($projects['id'], session()->usr_id)) == 'true') {

				$project = $this->Projects_Model->get_projects($id);
			}
		} else {

			$result['success'] = false;

			$result['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($result);
		}

		if ($project) {

			ini_set('max_execution_time', 0);

			ini_set('memory_limit', '2048M');

			if (!is_dir('uploads/files/projects/' . $id)) {

				mkdir('./uploads/files/projects/' . $id, 0777, true);
			}

			switch ($project['status']) {

				case '1':

					$status_project = lang2('notstarted');

					break;

				case '2':

					$status_project = lang2('started');

					break;

				case '3':

					$status_project = lang2('percentage');

					break;

				case '4':

					$status_project = lang2('cancelled');

					break;

				case '5':

					$status_project = lang2('completed');

					break;
			};

			$data['logs'] = $this->projecttimelogs($id);

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

			$data['country'] = get_country($data['settings']['country_id']);

			$data['billing_country'] = get_country($project['country_id']);

			$data['billing_state'] = get_state_name($project['billing_state'], $project['billing_state_id']);

			$data['project'] = $project;

			$data['status'] = $status_project;

			$data['color'] = request()->getPost('color');

			$data['services'] = '';

			$data['customer'] = false;

			$data['is_summary'] = false;

			$data['milestones'] = '';

			$data['tasks'] = '';

			$data['expenses'] = '';

			$data['proposals'] = '';

			$data['tickets'] = '';

			$data['members'] = '';

			$data['files'] = '';

			$data['notes'] = '';

			$data['time_logs'] = '';





			if (request()->getPost('customer') == 'true') {

				$data['customer'] = true;
			} else {

				$data['customer'] = false;
			}

			if (request()->getPost('services') == 'true') {

				$data['services'] = $this->Projects_Model->get_project_services($id);
			} else {

				$data['services'] = '';
			}

			if (request()->getPost('milestones') == 'true') {

				$data['milestones'] = $this->Projects_Model->get_all_project_milestones($id);
			} else {

				$data['milestones'] = '';
			}

			if (request()->getPost('tasks') == 'true') {

				$data['tasks'] = $this->Tasks_Model->get_project_tasks($id);
			} else {

				$data['tasks'] = '';
			}

			if (request()->getPost('expenses') == 'true') {

				$data['expenses'] = $this->Expenses_Model->get_all_expenses_by_relation('project', $id);
			} else {

				$data['expenses'] = '';
			}

			if (request()->getPost('proposals') == 'true') {

				$data['proposals'] = $this->Proposals_Model->project_proposals($id);
			} else {

				$data['proposals'] = '';
			}

			if (request()->getPost('tickets') == 'true') {

				$data['tickets'] = $this->Projects_Model->get_all_tickets($id);
			} else {

				$data['tickets'] = '';
			}

			if (request()->getPost('peoples') == 'true') {

				$data['members'] = $this->Projects_Model->get_members_index($id);
			} else {

				$data['members'] = '';
			}

			if (request()->getPost('files') == 'true') {

				$data['files'] = $this->Projects_Model->get_project_files($id);
			} else {

				$data['files'] = '';
			}

			if (request()->getPost('notes') == 'true') {

				$data['notes'] = $this->Projects_Model->get_project_notes($id);
			} else {

				$data['notes'] = '';
			}

			if (request()->getPost('time_logs') == 'true') {

				$data['time_logs'] = $data['logs'];
			} else {

				$data['time_logs'] = '';
			}

			if (request()->getPost('summary') == 'true') {

				$data['is_summary'] = true;

				$data['summary'] = $this->project_summary($id);
			} else {

				$data['is_summary'] = false;
			}

			$appconfig = get_appconfig();

			$file_name = '' . get_number('projects', $id, 'project', 'project') . '.pdf';

			$html = view('projects/pdf', $data, TRUE);

			$this->load->library('dom');

			$this->dompdf->loadHtml($html);

			$this->dompdf->set_option('isRemoteEnabled', TRUE);

			$this->dompdf->setPaper('A4', 'portrait');

			$this->dompdf->render();

			$output = $this->dompdf->output();

			file_put_contents('uploads/files/projects/' . $id . '/' . $file_name . '', $output);

			//$this->dompdf->stream( '' . $file_name . '', array( "Attachment" => 0 ) );

			if ($output) {

				$result = array(

					'success' => true,

					'file_name' => $file_name,

				);

				$this->Projects_Model->update_pdf_status($id, '1');

				return response()->setJSON($result);
			} else {

				return redirect()->to(base_url('projects/pdf_fault/'));
			}
		} else {

			$result['success'] = false;

			$result['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($result);
		}
	}



	function project_summary($id)
	{

		$summary = array(

			'expenses' => $this->db->table('expenses')
				->where('relation', $id)
				->where('relation_type', 'project')
				->countAllResults(),

			'tickets' => $this->db->table('tickets')
				->where('relation_id', $id)
				->where('relation', 'project')
				->countAllResults(),

			'proposals' => $this->db->table('proposals')
				->where('project_id', $id)
				->countAllResults(),

			'tasks' => $this->db->table('tasks')
				->where('relation_type', 'project')
				->where('relation', $id)
				->countAllResults(),

			'milestones' => $this->db->table('milestones')
				->where('project_id', $id)
				->countAllResults(),

			'members' => $this->db->table('projectmembers')
				->where('project_id', $id)
				->countAllResults(),

			'files' => $this->db->table('files')
				->where('relation_type', 'project')
				->where('relation', $id)
				->countAllResults(),

			'services' => $this->db->table('projectservices')
				->where('projectid', $id)
				->countAllResults()

		);

		return $summary;
	}



	function pdf_generates($file)
	{

		return true;
	}



	function pdf_generated($file)
	{

		$result = array(

			'status' => true,

			'file_name' => $file,

		);

		return response()->setJSON($result);
	}



	function projecttimelogs($id)
	{

		$timelogs = $this->Projects_Model->get_project_time_log($id);

		$data_timelogs = array();

		foreach ($timelogs as $timelog) {

			$task = $this->Tasks_Model->get_task($timelog['task_id']);

			$date1 = new DateTime($timelog['start']);

			$diffs = $date1->diff(new DateTime($timelog['end']));

			$h = $diffs->days * 24;

			$h += $diffs->h;

			$minutes = $diffs->i;

			$seconds = $diffs->s;

			if ($minutes < 10) {

				$minutes = $minutes;
			}

			if ($seconds < 10) {

				$seconds = $seconds;
			}

			if ($h < 10) {

				$h = $h;
			}

			$total = $h . ':' . $minutes . ':' . $seconds;

			$minutess = $h * 60 + $minutes;

			if ($task['hourly_rate'] > 0) {

				$amounts = ($h + ($minutes / 60)) * $task['hourly_rate'];
			} else {

				$amounts = 0;
			}

			if ($task['status_id'] != 5) {

				$data_timelogs[] = array(

					'id' => $timelog['id'],

					'start' => $timelog['start'],

					'end' => $timelog['end'],

					'note' => $timelog['note'],

					'staff' => $timelog['staffmember'],

					'status' => $timelog['status'],

					//'timed' => $timed_minute,

					'total_logged' => $total,

					'total_amount' => $amounts,

					'minutes' => $minutess,

					'rate' => $task['hourly_rate'],

					'amount' => $amounts,

				);
			}
		};

		return $data_timelogs;
	}



	function projects_stats()
	{

		if ($this->Privileges_Model->check_privilege('projects', 'all')) {

			$stats = $this->Projects_Model->get_projects_stats();
		} else if ($this->Privileges_Model->check_privilege('projects', 'own')) {

			$stats = $this->Projects_Model->get_projects_stats(session()->usr_id);
		}

		return response()->setJSON($stats);
	}



	function get_project($id)
	{

		$project = array();

		if ($this->Privileges_Model->check_privilege('projects', 'all')) {

			$project = $this->Projects_Model->get_projects($id);
		} else if ($this->Privileges_Model->check_privilege('projects', 'own')) {

			$projects = $this->Projects_Model->get_projects($id);

			if (($projects['staff_id'] == session()->usr_id) || ($this->Projects_Model->check_member($projects['id'], session()->usr_id)) == 'true') {

				$project = $this->Projects_Model->get_projects($id);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('projects'));
		}

		if ($project) {

			$settings = $this->Settings_Model->get_settings_ciuis();

			$milestones = $this->Projects_Model->get_all_project_milestones($id);

			$projectmembers = $this->Projects_Model->get_members($id);

			$project_logs = $this->Logs_Model->project_logs($id);

			$totaltasks = $this->Report_Model->totalprojecttasks($id);

			$opentasks = $this->Report_Model->openprojecttasks($id);

			$completetasks = $this->Report_Model->completeprojecttasks($id);

			$progress = ($totaltasks > 0 ? number_format(($completetasks * 100) / $totaltasks) : 0);

			$customer = ($project['customercompany']) ? $project['customercompany'] : $project['namesurname'];

			$enddate = $project['deadline'];

			$current_date = new DateTime(date('Y-m-d'), new DateTimeZone($settings['default_timezone']));

			$end_date = new DateTime("$enddate", new DateTimeZone($settings['default_timezone']));

			$interval = $current_date->diff($end_date);

			$project_left_date = $interval->format('%a day(s)');

			if (date("Y-m-d") > $project['deadline']) {

				$ldt = 'Time\'s up!';
			} else $ldt = $project_left_date;

			switch ($project['status']) {

				case '1':

					$status_project = lang2('notstarted');

					break;

				case '2':

					$status_project = lang2('started');

					break;

				case '3':

					$status_project = lang2('percentage');

					break;

				case '4':

					$status_project = lang2('cancelled');

					break;

				case '5':

					$status_project = lang2('completed');

					break;
			};

			if (in_array(current_user_id, array_column($projectmembers, 'staff_id')) || isAdmin()) {

				$authorization = "true";
			} else {

				$authorization = 'false';
			};

			if ($project['invoice_id'] > 0) {

				$billed = lang2('yes');
			} else {

				$billed = lang2('no');
			}

			$tasks = $this->Tasks_Model->get_project_tasks($id);

			$data_projecttasks = array();

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

						$taskdone = '';

						break;
				};

				switch ($task['relation_type']) {

					case 'project':

						$relationtype = 'Project';

						break;

					case 'ticket':

						$relationtype = 'Tıcket';

						break;

					case 'proposal':

						$relationtype = 'Proposal';

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


				$data_projecttasks[] = array(

					'id' => $task['id'],

					'name' => $task['name'],

					'description' => $task['description'],

					'relationtype' => $relationtype,

					'status' => $status,

					'status_id' => $task['status_id'],

					'duedate' => $duedate,

					'startdate' => $startdate,

					'done' => $taskdone,

					'task_number' => get_number('tasks', $task['id'], 'task', 'task'),

				);
			};

			$appconfig = get_appconfig();

			$data_projectdetail = array(

				'id' => $project['id'],

				'name' => $project['name'],

				'value' => $project['projectvalue'],

				'status_id' => $project['status'],

				'tax' => $project['tax'],

				'description' => $project['description'],

				'start' => $project['start_date'],

				'start_edit' => $project['start_date'],

				'deadline' => date(get_dateFormat(), strtotime($project['deadline'])),

				'deadline_edit' => $project['deadline'],

				'created' => $project['created'],

				'finished' => $project['finished'],

				'template' => $project['template'],

				'status' => $status_project,

				'progress' => $progress,

				'totaltasks' => $totaltasks,

				'opentasks' => $opentasks,

				'completetasks' => $completetasks,

				'customer' => $customer,

				'customer_id' => $project['customer_id'],

				'ldt' => $ldt,

				'authorization' => $authorization,

				'billed' => $billed,

				'milestones' => $milestones,

				'tasks' => $data_projecttasks,

				'members' => $projectmembers,

				'project_logs' => $project_logs,

				'pdf_report' => $project['pdf_report'],

				'file_name' =>  get_number('projects', $project['id'], 'project', 'project') . '.pdf',

				'project_number' => get_number('projects', $project['id'], 'project', 'project'),

			);

			return response()->setJSON($data_projectdetail);
		}
	}



	function projectmilestones($id)
	{

		$milestones = $this->Projects_Model->get_all_project_milestones($id);

		$data_milestones = array();

		foreach ($milestones as $milestone) {

			if (date("Y-m-d") > $milestone['duedate']) {

				$status = 'is-completed';
			} else if (date("Y-m-d") < $milestone['duedate']) {

				$status = 'is-future';
			} else {

				$status = 'is-completed';
			}

			$tasks = $this->Projects_Model->get_all_project_milestones_task($milestone['id']);

			$data_milestones[] = array(

				'id' => $milestone['id'],

				'name' => $milestone['name'],

				'duedate' => $milestone['duedate'],

				'description' => $milestone['description'],

				'order' => $milestone['order'],

				'due' => $milestone['duedate'],

				'status' => $status,

				'tasks' => $tasks,

			);
		};

		return response()->setJSON($data_milestones);
	}



	function projectfiles($id)
	{

		if (isset($id)) {

			$files = $this->Projects_Model->get_project_files($id);

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

					$path = base_url('uploads/files/projects/' . $id . '/' . $file['file_name']);
				}

				$data[] = array(

					'id' => $file['id'],

					'project_id' => $file['relation'],

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



	function get_projecttimelogs($id)
	{

		$timelogs = $this->Projects_Model->get_project_time_log($id);

		$data_timelogs = array();

		foreach ($timelogs as $timelog) {

			$task = $this->Tasks_Model->get_task($timelog['task_id']);

			$start = $timelog['start'];

			$end = $timelog['end'];

			$timed_minute = intval(abs(strtotime($start) - strtotime($end)) / 60);

			$amount = $timed_minute / 60 * $task['hourly_rate'];



			$date1 = new DateTime($timelog['start']);

			$diffs = $date1->diff(new DateTime($timelog['end']));

			$h = $diffs->days * 24;

			$h += $diffs->h;

			$minutes = $diffs->i;

			$seconds = $diffs->s;

			if ($minutes < 10) {

				$minutes = $minutes;
			}

			if ($seconds < 10) {

				$seconds = $seconds;
			}

			if ($h < 10) {

				$h = $h;
			}

			$total = $h . ':' . $minutes . ':' . $seconds;

			$minutess = $h * 60 + $minutes;

			if ($task['hourly_rate'] > 0) {

				$amounts = ($h + ($minutes / 60)) * $task['hourly_rate'];
			} else {

				$amounts = 0;
			}



			if ($task['status_id'] != 5) {

				$data_timelogs[] = array(

					'id' => $timelog['id'],

					'start' => $timelog['start'],

					'end' => $timelog['end'],

					'staff' => $timelog['staffmember'],

					'status' => $timelog['status'],

					'timed' => $timed_minute,

					'amount' => $amount,

					'total_logged' => $total,

					'total_amount' => $amounts,

					'minutes' => $minutess,

				);
			}
		};

		return response()->setJSON($data_timelogs);
	}



	function get_projects()
	{

		$projects = $this->Projects_Model->get_all_projects();

		$data_projects = array();

		if ($this->Privileges_Model->check_privilege('projects', 'all')) {

			foreach ($projects as $project) {

				$settings = $this->Settings_Model->get_settings_ciuis();

				$totaltasks = $this->Report_Model->totalprojecttasks($project['id']);

				$opentasks = $this->Report_Model->openprojecttasks($project['id']);

				$completetasks = $this->Report_Model->completeprojecttasks($project['id']);

				$progress = ($totaltasks > 0 ? number_format(($completetasks * 100) / $totaltasks) : 0);

				$project_id = $project['id'];

				switch ($project['status']) {

					case '1':

						$projectstatus = 'notstarted';

						$icon = 'notstarted.png';

						$status = lang2('notstarted');

						break;

					case '2':

						$projectstatus = 'started';

						$icon = 'started.png';

						$status = lang2('started');

						break;

					case '3':

						$projectstatus = 'percentage';

						$icon = 'percentage.png';

						$status = lang2('percentage');

						break;

					case '4':

						$projectstatus = 'cancelled';

						$icon = 'cancelled.png';

						$status = lang2('cancelled');

						break;

					case '5':

						$projectstatus = 'complete';

						$icon = 'complete.png';

						$status = lang2('complete');

						break;
				}

				if ($project['status'] == '5') {

					$projectstatus = 'complete';

					$icon = 'complete.png';

					$status = lang2('completed');

					$progress = 100;
				}

				if ($project['template'] == '1') {

					$projectstatus = 'template';
				}

				$startdate =  date(get_dateFormat(), strtotime($project['start_date']));



				$customer = ($project['customercompany']) ? $project['customercompany'] : $project['namesurname'];

				$enddate = $project['deadline'];

				$current_date = new DateTime(date('Y-m-d'), new DateTimeZone('Asia/Dhaka'));

				$end_date = new DateTime("$enddate", new DateTimeZone('Asia/Dhaka'));

				$interval = $current_date->diff($end_date);

				$leftdays = $interval->format('%a day(s)');

				$members = $this->Projects_Model->get_members_index($project_id);

				$milestones = $this->Projects_Model->get_all_project_milestones($project_id);

				$appconfig = get_appconfig();

				$data_projects[] = array(

					'id' => $project['id'],

					'project_id' => $project['id'],

					'name' => $project['name'],

					'pinned' => $project['pinned'],

					'value' => $project['projectvalue'],

					'tax' => $project['tax'],

					'template' => $project['template'],

					'status_id' => $project['status'],

					'progress' => $progress,
					'startdate' => $startdate,
					'leftdays' => $leftdays,

					'customer' => $customer,

					'customeremail' => $project['customeremail'],

					'status_icon' => $icon,

					'status' => $status,

					'status_class' => $projectstatus,

					'customer_id' => $project['customer_id'],

					'members' => $members,

					'milestones' => $milestones,

					lang2('filterbystatus') => lang($projectstatus),

					lang2('filterbycustomer') => $customer,

					'project_number' => get_number('projects', $project['id'], 'project', 'project'),

				);
			}
		} else if ($this->Privileges_Model->check_privilege('projects', 'own')) {

			foreach ($projects as $project) {

				if (($project['staff_id'] == session()->usr_id) || ($this->Projects_Model->check_member($project['id'], session()->usr_id)) == 'true') {

					$settings = $this->Settings_Model->get_settings_ciuis();

					$totaltasks = $this->Report_Model->totalprojecttasks($project['id']);

					$opentasks = $this->Report_Model->openprojecttasks($project['id']);

					$completetasks = $this->Report_Model->completeprojecttasks($project['id']);

					$progress = ($totaltasks > 0 ? number_format(($completetasks * 100) / $totaltasks) : 0);

					$project_id = $project['id'];

					switch ($project['status']) {

						case '1':

							$projectstatus = 'notstarted';

							$icon = 'notstarted.png';

							$status = lang2('notstarted');

							break;

						case '2':

							$projectstatus = 'started';

							$icon = 'started.png';

							$status = lang2('started');

							break;

						case '3':

							$projectstatus = 'percentage';

							$icon = 'percentage.png';

							$status = lang2('percentage');

							break;

						case '4':

							$projectstatus = 'cancelled';

							$icon = 'cancelled.png';

							$status = lang2('cancelled');

							break;

						case '5':

							$projectstatus = 'complete';

							$icon = 'complete.png';

							$status = lang2('complete');

							break;
					}

					if ($project['status'] == '5') {

						$projectstatus = 'complete';

						$icon = 'complete.png';

						$status = lang2('completed');

						$progress = 100;
					}

					if ($project['template'] == '1') {

						$projectstatus = 'template';
					}

					$startdate =  date(get_dateFormat(), strtotime($project['start_date']));


					$customer = ($project['customercompany']) ? $project['customercompany'] : $project['namesurname'];

					$enddate = $project['deadline'];

					$current_date = new DateTime(date('Y-m-d'), new DateTimeZone('Asia/Dhaka'));

					$end_date = new DateTime("$enddate", new DateTimeZone('Asia/Dhaka'));

					$interval = $current_date->diff($end_date);

					$leftdays = $interval->format('%a day(s)');

					$members = $this->Projects_Model->get_members_index($project_id);

					$milestones = $this->Projects_Model->get_all_project_milestones($project_id);

					$appconfig = get_appconfig();

					$data_projects[] = array(

						'id' => $project['id'],

						'project_id' => $project['id'],

						'name' => $project['name'],

						'pinned' => $project['pinned'],

						'value' => $project['projectvalue'],

						'tax' => $project['tax'],

						'template' => $project['template'],

						'status_id' => $project['status'],

						'progress' => $progress,

						'startdate' => $startdate,

						'leftdays' => $leftdays,

						'customer' => $customer,

						'customeremail' => $project['customeremail'],

						'status_icon' => $icon,

						'status' => $status,

						'status_class' => $projectstatus,

						'customer_id' => $project['customer_id'],

						'members' => $members,

						'milestones' => $milestones,

						lang2('filterbystatus') => lang($projectstatus),

						lang2('filterbycustomer') => $customer,

						'project_number' => get_number('projects', $project['id'], 'project', 'project'),

					);
				}
			}
		} else {

			$data_projects = array();
		}

		return response()->setJSON($data_projects);
	}
}
