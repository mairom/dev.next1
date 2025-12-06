<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Staff extends BaseController
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
		$path = request()->getUri()->getSegment(1);
		if (!$this->Privileges_Model->has_privilege($path)) {
			session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
			return redirect()->to('panel/');
			die;
		} else {
			$data['title'] = lang2('staff');
			$data['staff'] = $this->Staff_Model->get_all_staff();
			$data['settings'] = $this->Settings_Model->get_settings_ciuis();
			$data['departments'] = $this->Settings_Model->get_departments();
			$path = request()->getUri()->getSegment(1);
			if (!$this->Privileges_Model->has_privilege($path)) {
				session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
				return redirect()->to('panel/');
				die;
			}
			return view('staff/index', $data);
		}
	}



	function createStaff()
	{
		if ($this->Privileges_Model->check_privilege('staff', 'create')) {
			$data['title'] = 'Add Staff';
			if (isset($_POST) && count($_POST) > 0) {
				$language = request()->getPost('language');
				$staffname = request()->getPost('name');
				$department_id = request()->getPost('department');
				$email = request()->getPost('email');
				$timezone = request()->getPost('timezone');
				$role = request()->getPost('role');
				$hasError = false;
				$data['message'] = '';

				if ($staffname == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
				} else if ($email == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('email');
				} else if ($email != '') {
					if (
						$this->Staff_Model->isDuplicate($email) == TRUE &&
						request()->getPost('id') == null &&
						empty(request()->getPost('id'))
					) {
						$hasError = true;
						$data['message'] = lang2('staffemailalreadyexists');
					}
				}

				if (!$hasError) {

					if ($department_id == '') {
						$hasError = true;
						$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('staffdepartment');
					} else if ($language == '') {
						$hasError = true;
						$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('language');
					} //else if ($timezone == '') {
						//$hasError = true;
						//$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('staff') . ' ' . lang2('timezone');
					//} 
					else if ($role == '') {
						$hasError = true;
						$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('role');
					}
				}

				if ($hasError) {
					$data['success'] = false;
					return response()->setJSON($data);
				}

				if (!$hasError) {
					$role_type = $this->Staff_Model->get_role_type($role);
					$is_Admin = '';
					$is_Staff = '';
					$is_Other = '';
					if ($role_type == 'admin') {
						$is_Admin = 1;
					} else if ($role_type == 'staff') {
						$is_Staff = 1;
					} else {
						$is_Other = 1;
						$is_Staff = 1;
					}

					$appconfig = get_appconfig();

					$params = array(
						'language' => request()->getPost('language'),
						'role_id' => $role,
						'staffname' => request()->getPost('name'),
						//'staffavatar' => 'n-img.jpg',
						'createdat' => date('Y-m-d'),
						'department_id' => request()->getPost('department'),
						'phone' => request()->getPost('phone'),
						'address' => request()->getPost('address'),
						'email' => request()->getPost('email'),

						'admin' => $is_Admin ? $is_Admin : null,
						'timezone' => $timezone,
						'other' => $is_Other ? $is_Other : null,
						'staffmember' => $is_Staff ? $is_Staff : null,
						'inactive' => NULL,
						'id_company' => request()->getPost('id_company') != null ?  request()->getPost('id_company') : session()->id_company
					);
					if (request()->getPost('password') != null) {
						$params['password'] =  md5(request()->getPost('password'));
					}
					if (request()->getPost('staffavatar') != null && !empty(request()->getPost('staffavatar'))) {
						$params['staffavatar']  = upload($_POST['staffavatar'], "images");
					}

					if (request()->getPost('id') != null && !empty(request()->getPost('id'))) {
						$response = $this->db->table('staff')->where('id', request()->getPost('id'))->update($params);

						$staff_id = false;
						if ($response) {
							$data['success'] = true;
							$data['message'] = lang2('staff') . ' ' . lang2('updated');
						} else {
							$data['success'] = false;
						}
					} else {
						if (request()->getPost('staffavatar') == null || empty(request()->getPost('staffavatar'))) {
							$params['staffavatar']  = 'n-img.jpg';
						}
						$staff_id = $this->Staff_Model->add_staff($params);
					}


					if ($staff_id) {
						if (request()->getPost('custom_fields')) {
							$custom_fields = array(
								'custom_fields' => request()->getPost('custom_fields')
							);
							$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'staff', $staff_id);
						}

						$template = $this->Emails_Model->get_template('staff', 'new_staff');

						if ($template['status'] == 1) {
							$message_vars = array(
								'{staff_email}' => request()->getPost('email'),
								'{staff}' => request()->getPost('name'),
								'{password}' => request()->getPost('password'),
								'{name}' => session()->get('staffname'),
								'{email_signature}' => session()->get('email'),
								'login_url' => '' . base_url('login') . ''
							);

							$subject = strtr($template['subject'], $message_vars);
							$message = strtr($template['message'], $message_vars);
							$param = array(
								'from_name' => $template['from_name'],
								'email' => request()->getPost('email'),
								'subject' => $subject,
								'message' => $message,
								'created' => date("Y.m.d H:i:s")
							);

							if (request()->getPost('email')) {
								$this->db->table('email_queue')->insert($param);
							}
						}
						$data['success'] = true;
						if ($appconfig['staff_series']) {
							$staff_number = $appconfig['staff_series'];
							$staff_number = $staff_number + 1;
							$this->Settings_Model->increment_series('staff_series', $staff_number);
						}
						$data['message'] = lang2('staff') . ' ' . lang2('addmessage');
					}

					return response()->setJSON($data);
				}
			} else {

				$data['settings'] = $this->Settings_Model->get_settings_ciuis();
				$data['languages'] = $this->Settings_Model->get_languages();
				$data['departments'] = $this->Settings_Model->get_departments();

				return view('staff/add', $data);
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}



	function update($id)
	{

		//	if ($this->Privileges_Model->check_privilege('staff', 'all')) {

		//	if ($this->Privileges_Model->check_privilege('staff', 'edit')) {

		if (isset($id)) {

			if (isset($_POST) && count($_POST) > 0) {

				$is_demo = $this->Settings_Model->is_demo();

				if (!$is_demo) {

					$language = request()->getPost('language');
					$staffname = request()->getPost('name');

					$email = request()->getPost('email');


					$primaryEmail = $this->Staff_Model->get_staff_email($id);

					$role = request()->getPost('role');

					$hasError = false;

					$data['message'] = '';

					if ($staffname == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
					} else if ($email == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('email');
					} else if ($email != '') {

						if ($email != $primaryEmail['email']) {

							if ($this->Staff_Model->isDuplicate($email) == TRUE) {

								$hasError = true;

								$data['message'] = lang2('staffemailalreadyexists');
							}
						}
					}

					if (!$hasError) {

						if ($language == '') {
							$hasError = true;
							$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('language');
						} 
						//else if (request()->getPost('timezone') == '' || !request()->getPost('timezone')) {
						//	$hasError = true;
						//	$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('timezone');
						//}
					}

					if ($hasError) {

						$data['success'] = false;

						return response()->setJSON($data);
					}

					if (!$hasError) {

						$role_type = $this->Staff_Model->get_role_type($role);

						$is_Admin = '';

						$is_Staff = '';

						$is_Other = '';

						if ($role_type == 'admin') {

							$is_Admin = 1;
						} else if ($role_type == 'staff') {

							$is_Staff = 1;
						} else {

							$is_Other = 1;

							$is_Staff = 1;
						}

						switch ($_POST['inactive']) {

							case 'true':

								$is_Active = null;

								break;

							case 'false':

								$is_Active = 0;

								break;
						}

						$params = array(

							'language' => request()->getPost('language'),

							'staffname' => request()->getPost('name'),

							'department_id' => request()->getPost('department'),

							'role_id' => $role,

							'phone' => request()->getPost('phone'),

							'address' => request()->getPost('address'),

							'email' => request()->getPost('email'),

							'timezone' => request()->getPost('timezone'),

							'inactive' => $is_Active,

							'admin' => $is_Admin ? $is_Admin : null,

							'other' => $is_Other ? $is_Other : null,

							'staffmember' => $is_Staff ? $is_Staff : null,

						);

						$this->Staff_Model->update_staff($id, $params);

						if (session()->usr_id == $id) {
							session()->set(array('language' => request()->getPost('language')));
							session()->set(array('staff_timezone' => request()->getPost('staff_timezone')));
						}

						if (request()->getPost('custom_fields')) {

							$custom_fields = array(

								'custom_fields' => request()->getPost('custom_fields')

							);

							$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'staff', $id);
						}

						$data['success'] = true;

						$data['message'] = lang2('staffupdated');

						return response()->setJSON($data);
					}
				} else {

					$datas['success'] = false;

					$datas['message'] = lang2('demo_error');

					return response()->setJSON($datas);
				}
			}
		}
		//	} else {

		//		$datas['success'] = false;
		//
		$datas['message'] = lang2('you_dont_have_permission');

		//		return response()->setJSON($datas);
		//	}
		//	} else {

		//		session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

		//	return redirect()->to(base_url('staff'));
		//	}
	}

	function exportdata()
	{
		$this->load->dbutil();
		$this->load->helper('file');
		$this->load->helper('download');

		$select = "
		staffname 'nome do funcionário',
		phone,
		address,
		email,
		birthday as 'nascimento',
		IF(admin = '1', 'Sim', 'Não'),
		last_login as 'ultimo login',
        createdat as 'criado em',
		";

		$builder = $this->db->table('staff');
		$builder->select($select);
		$query = $builder->get();
		$result = $query->getResultArray();


		$delimiter = ";";
		$nuline    = "\r\n";
		force_download('Empresas.csv', "\xEF\xBB\xBF" . $this->dbutil->csv_from_result($q, $delimiter, $nuline));
	}



	function staffmember($id)
	{

		if ($this->Privileges_Model->check_privilege('staff', 'all')) {

			$data['title'] = lang2('staffdetail');
			$staff = $this->Staff_Model->get_staff($id);

			if (isset($staff['id'])) {

				$data['id'] = $staff['id'];
				return view('staff/detail', $data);
			} else {

				return redirect()->to('staff/');
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('staff'));
		}
	}



	function profile()
	{

		$id = session()->get('usr_id');

		$data['title'] = lang2('staffdetail');

		$staff = $this->Staff_Model->get_staff($id);

		if (isset($staff['id'])) {

			if (!$this->isAdmin()) {
				if ($staff['id'] != session()->usr_id) {
					session()->setFlashdata('ntf3', '' . $id . lang2('you_dont_have_permission'));
					return redirect()->to('panel');
				} else {
					$data['id'] = $staff['id'];
					return view('staff/detail', $data);
					//return view('inc/footer', $data);
				}
			} else {
				$data['id'] = $staff['id'];
				return view('staff/detail', $data);
			}
		} else {
			return redirect()->to('staff/');
		}
	}



	function isAdmin()
	{

		$id = session()->usr_id;

		$builder = $this->db->table('staff');
		$builder->select('*');
		$builder->where(['admin' => 1, 'id' => $id]);
		$rows = $builder->countAllResults();


		if ($rows > 0) {

			return true;
		} else {

			return false;
		}
	}



	public function change_avatar($id)
	{
		if ($id == session()->get('usr_id')) {
			goto profile;
		} else {
			if ($this->Privileges_Model->check_privilege('staff', 'edit')) {
				profile:
				if ($id) {

					$file = request()->getFile('file');

					if ($file && $file->isValid()) {
						$newName = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName());
						$file->move('./uploads/images/', $newName);

						if (is_file('./uploads/images/' . $newName)) {
							$params = [
								'staffavatar' => $newName,
							];
							$this->Staff_Model->update_staff($id, $params);

							return response()->setJSON([
								'success' => true,
								'message' => lang2('profile') . ' ' . lang2('updatemessage')
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
			} else {
				return response()->setJSON([
					'success' => false,
					'message' => lang2('you_dont_have_permission')
				]);
			}
		}
	}



	function remove($id)
	{

		if ($this->Privileges_Model->check_privilege('staff', 'all')) {
			if ($this->Privileges_Model->check_privilege('staff', 'delete')) {
				$staff = $this->Staff_Model->get_staff($id);
				if (isset($staff['id'])) {
					$is_demo = $this->Settings_Model->is_demo();
					if (!$is_demo) {

						if ($staff['id'] == session()->usr_id) {

							$data['success'] = false;

							$data['message'] = lang2('cannotdeleteown');

							return response()->setJSON($data);
						} else {

							$result = $this->Staff_Model->delete_staff($id, get_number('staff', $id, 'staff', 'staff'));

							$staff = lang2('staff');

							if ($result) {

								$data['message'] = sprintf(lang2('success_delete'), $staff . '');

								$data['success'] = true;

								return response()->setJSON($data);
							} else {

								$data['message'] = sprintf(lang2('cant_delete'), $staff . '');

								$data['success'] = false;

								return response()->setJSON($data);
							}
						}
					} else {

						$data['success'] = false;

						$data['message'] = 'Some Staffs Can not be deleted in Demo Mode';

						return response()->setJSON($data);
					}
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function add_department()
	{

		if ($this->Privileges_Model->check_privilege('staff', 'create')) {

			if (isAdmin()) {

				if (isset($_POST) && count($_POST) > 0) {

					$params = array(

						'name' => request()->getPost('name'),

						"id_company" => session()->id_company
					);

					$department = $this->Settings_Model->add_department($params);

					$data['message'] = lang2('department') . ' ' . lang2('addmessage');

					$data['success'] = true;
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('only_admin') . ' ' . lang2('create') . ' ' . lang2('department');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function update_department($id)
	{

		if ($this->Privileges_Model->check_privilege('staff', 'edit')) {

			if (isAdmin()) {

				$departments = $this->Settings_Model->get_department($id);

				if (isset($departments['id'])) {

					if (isset($_POST) && count($_POST) > 0) {

						$params = array(

							'name' => request()->getPost('name'),

						);

						session()->setFlashdata('ntf1', '<span><b>' . lang2('departmentupdated') . '</b></span>');

						$this->Settings_Model->update_department($id, $params);

						$data['message'] = lang2('department') . ' ' . lang2('updatemessage');

						$data['success'] = true;

						return response()->setJSON($data);
					}
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('only_admin') . ' ' . lang2('update') . ' ' . lang2('department');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function update_workplan($id)
	{

		if ($id == session()->usr_id) {

			goto next;
		} else {

			if ($this->Privileges_Model->check_privilege('staff', 'edit')) {

				next:

				$workplan = $this->Staff_Model->get_work_plan($id);

				if (isset($workplan['id'])) {

					if (isset($_POST) && count($_POST) > 0) {
						$response = $this->db->table('staff_work_plan')->where('id', $workplan['id'])->update(['work_plan' => request()->getPost('work_plan')]);
					}

					$data['success'] = true;

					$data['message'] = lang2('staff') . ' ' . lang2('work_plan') . ' ' . lang2('updated');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');
			}
		}

		return response()->setJSON($data);
	}



	function remove_department($id)
	{

		if ($this->Privileges_Model->check_privilege('staff', 'delete')) {

			if (isAdmin()) {

				$departments = $this->Settings_Model->get_department($id);

				if (isset($departments['id'])) {

					if ($this->Settings_Model->check_department($id) === 0) {

						$this->Settings_Model->delete_department($id);

						$data['message'] = lang2('department') . ' ' . lang2('deletemessage');

						$data['success'] = true;

						return response()->setJSON($data);
					} else {

						$data['message'] = lang2('department') . ' ' . lang2('is_linked') . ' ' . lang2('with') . ' ' . lang2('staff') . ', ' . lang2('so') . ' ' . lang2('cannot_delete') . ' ' . lang2('department');

						$data['success'] = false;

						return response()->setJSON($data);
					}
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('only_admin') . ' ' . lang2('delete') . ' ' . lang2('department');

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function appointment_availability($id, $value)
	{

		if ($value === 'true') {

			$availability = 1;
		} else {

			$availability = 0;
		}

		if (isset($id)) {
			$response = $this->db->table('staff')->where('id', $id)->update(['appointment_availability' => $availability]);
		};
	}



	function update_google_calendar($id)
	{

		$staff = $this->Staff_Model->get_staff($id);

		if (isset($staff['id'])) {

			if (isset($_POST) && count($_POST) > 0) {

				$params = array(

					'google_calendar_id' => request()->getPost('google_calendar_id'),

					'google_calendar_api_key' => request()->getPost('google_calendar_api_key'),

					'google_calendar_enable' => request()->getPost('google_calendar_enable'),

				);

				$this->Staff_Model->update_staff($id, $params);

				$notification = array(

					'color' => 'color success',

					'message' => lang2('google_calendar_settings_updated')

				);

				return response()->setJSON($notification);
			} else {

				$notification = array(

					'color' => 'color danger',

					'message' => lang2('google_calendar_settings_not_updated')

				);

				return response()->setJSON($notification);
			}
		} else

			show_error('The staff you are trying to update google calendar settings does not exist.');
	}



	function changestaffpassword()
	{

		$id = session()->get('usr_id');

		$staff = $this->Staff_Model->get_staff($id);

		if (isset($staff['id'])) {

			if (isset($_POST) && count($_POST) > 0) {

				$password = request()->getPost('password');

				$new_password = request()->getPost('new_password');

				$c_new_password = request()->getPost('c_new_password');

				$hasError = false;

				if ($password == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('old') . ' ' . lang2('password');
				} else if ($new_password == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('new') . ' ' . lang2('password');
				} else if (strlen($new_password) < 6) {

					$hasError = true;

					$data['message'] = lang2('password_length_error');
				} else if ($c_new_password != $new_password) {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('confirm') . ' ' . lang2('new') . ' ' . lang2('password');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					if ($this->Staff_Model->validate_user_password($id, $password)) {

						$params = array(

							'password' => md5($new_password),

						);

						$this->Staff_Model->update_staff($id, $params);



						$template = $this->Emails_Model->get_template('staff', 'password_reset');

						$message_vars = array(

							'{staff_email}' => $staff['email'],

							'{staffname}' => $staff['staffname'],

							'{email_signature}' => $template['from_name'],

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);



						$param = array(

							'from_name' => $template['from_name'],

							'email' => $staff['email'],

							'subject' => $subject,

							'message' => $message,

							'created' => date("Y.m.d H:i:s"),

							'status' => 0

						);

						if ($staff['email']) {

							$this->db->table('email_queue')->insert($param);



							$this->Emails_Model->send_email($staff['email'], $template['from_name'], $subject, $message);
						}

						$data['success'] = true;

						$data['message'] = lang2('password') . ' ' . lang2('updatemessage');

						return response()->setJSON($data);
					} else {

						$data['success'] = false;

						$data['message'] = lang2('incorrect') . ' ' . lang2('old') . ' ' . lang2('password');

						return response()->setJSON($data);
					}
				}
			} else {

				$data['staff'] = $this->Staff_Model->get_staff($id);
			}
		} else

			show_error('The staff you are trying to update password does not exist.');
	}



	function changestaffpassword_admin($id)
	{

		if ($this->Privileges_Model->check_privilege('staff', 'edit')) {

			$staff = $this->Staff_Model->get_staff($id);

			if (isset($staff['id'])) {

				if (isset($_POST) && count($_POST) > 0) {

					$new_password = request()->getPost('new_password');

					$c_new_password = request()->getPost('c_new_password');

					$hasError = false;

					if ($new_password == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('new') . ' ' . lang2('password');
					} else if (strlen($new_password) < 6) {

						$hasError = true;

						$data['message'] = lang2('password_length_error');
					} else if ($c_new_password != $new_password) {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('confirm') . ' ' . lang2('new') . ' ' . lang2('password');
					}

					if ($hasError) {

						$data['success'] = false;

						return response()->setJSON($data);
					}

					if (!$hasError) {

						$params = array(

							'password' => md5($new_password),

						);

						$this->Staff_Model->update_staff($id, $params);



						$template = $this->Emails_Model->get_template('staff', 'password_reset');

						$message_vars = array(

							'{staff_email}' => $staff['email'],

							'{staffname}' => $staff['staffname'],

							'{email_signature}' => $template['from_name'],

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);



						$param = array(

							'from_name' => $template['from_name'],

							'email' => $staff['email'],

							'subject' => $subject,

							'message' => $message,

							'created' => date("Y.m.d H:i:s"),

							'status' => 0

						);

						if ($staff['email']) {

							$this->db->table('email_queue')->insert($param);



							$this->Emails_Model->send_email($staff['email'], $template['from_name'], $subject, $message);
						}

						$data['success'] = true;

						$data['message'] = lang2('password') . ' ' . lang2('updatemessage');

						return response()->setJSON($data);
					}
				} else {

					$data['staff'] = $this->Staff_Model->get_staff($id);
				}
			} else {

				show_error('The staff you are trying to update password does not exist.');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function restore_workplan($staff_id)
	{

		if ($staff_id == session()->usr_id) {

			$result = $this->Staff_Model->restore_workplan($staff_id);

			if ($result) {

				session()->setFlashdata('ntf1', lang2('staff') . ' ' . lang2('work_plan') . ' ' . lang2('updated'));
			}

			return redirect()->to(base_url('staff/profile/' . $staff_id));
		} else {

			if ($this->Privileges_Model->check_privilege('staff', 'all')) {

				if ($this->Privileges_Model->check_privilege('staff', 'edit')) {

					$result = $this->Staff_Model->restore_workplan($staff_id);

					if ($result) {

						session()->setFlashdata('ntf1', lang2('staff') . ' ' . lang2('work_plan') . ' ' . lang2('updated'));
					}
				} else {

					session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
				}
			} else {

				session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
			}

			return redirect()->to(base_url('staff/staffmember/' . $staff_id));
		}
	}



	function staff_detail($id)
	{

		//	if ($this->Privileges_Model->check_privilege('staff', 'all')) {
		$staff = $this->Staff_Model->get_staff($id);
		$permissions = $this->Privileges_Model->get_all_permissions();
		$privileges = $this->Privileges_Model->get_privileges();

		$builder = $this->db->table('staff_work_plan');
		$builder->select('*');
		$builder->where('staff_id', $id);
		$work_plans = $builder->get()->getRowArray();

		if (isset($work_plans['work_plan'])) {
			$daily_work_plan = json_decode($work_plans['work_plan']);
		} else {
			$daily_work_plan = [];
		}

		$arr = array();
		foreach ($privileges as $privilege) {
			if ($privilege['relation'] == $staff['id'] && $privilege['relation_type'] == 'staff') {
				array_push($arr, $privilege['permission_id']);
			}
		}

		$data_privileges = array();

		if ($staff['other']) {

			foreach ($permissions as $permission) {

				if (($permission['key'] == 'invoices') || ($permission['key'] == 'expenses')) {

					$data_privileges[] = array(

						'id' => $permission['id'],

						'name' => '' . lang($permission['permission']) . '',

						'value' => '' . (array_search($permission['id'], $arr) !== FALSE) ? true : false . ''

					);
				}
			}
		} else {

			foreach ($permissions as $permission) {

				if ($permission['key'] != 'quotes') {

					$data_privileges[] = array(

						'id' => $permission['id'],

						'name' => '' . lang($permission['permission']) . '',

						'value' => '' . (array_search($permission['id'], $arr) !== FALSE) ? true : false . ''

					);
				}
			}
		}

		switch ($staff['admin']) {

			case 1:

				$isAdmin = true;

				$type = 'admin';

				break;

			case null || 0:

				$isAdmin = false;

				break;
		}

		switch ($staff['staffmember']) {

			case 1:

				$isStaff = true;

				$type = 'staffmember';

				break;

			case null || 0:

				$isStaff = false;

				break;
		}

		switch ($staff['other']) {

			case 1:

				$isOther = true;

				$isStaff = false;

				$type = 'other';

				break;

			case null || 0:

				$isOther = false;

				break;
		}

		switch ($staff['inactive']) {

			case null:

				$isInactive = true;

				break;

			case '0':

				$isInactive = false;

				break;
		}

		switch ($staff['google_calendar_enable']) {

			case '0':

				$GoogleCalendarEnable = false;

				break;

			case '1':

				$GoogleCalendarEnable = true;

				break;
		}

		$properties = array(
			'department' => $staff['department'],
			'sales_total' => $this->Staff_Model->total_sales_by_staff($id),
			'total_customer' => $this->Staff_Model->total_customer_by_staff($id),
			'total_ticket' => $this->Staff_Model->total_ticket_by_staff($id),
			'chart_data' => $this->Report_Model->staff_sales_graph($id),
			'chart_data_time' => $this->Report_Model->staff_sales_graph2($id),
		);

		$user_data = array(

			'id' => $staff['id'],

			'assigned_role' => $staff['role_id'],

			'language' => $staff['language'],

			'name' => $staff['staffname'],

			'avatar' => $staff['staffavatar'],

			'department_id' => $staff['department_id'],

			'phone' => $staff['phone'],

			'email' => $staff['email'],

			'timezone' => $staff['timezone'],

			'address' => $staff['address'],

			'google_calendar_id' => $staff['google_calendar_id'],

			'google_calendar_api_key' => $staff['google_calendar_api_key'],

			'google_calendar_enable' => $GoogleCalendarEnable,

			'admin' => $isAdmin,

			'other' => $isOther,

			'type' => $type,

			'staffmember' => $isStaff,

			'last_login' => $staff['last_login'],

			'active' => $isInactive,

			'properties' => $properties,

			'privileges' => $data_privileges,

			'work_plan' => $daily_work_plan,

			'staff_number' => get_number('staff', $staff['id'], 'staff', 'staff'),

		);

		return response()->setJSON($user_data);
		//} else {

		//	session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

		//	return redirect()->to(base_url('staff'));
		//	}
	}

	function get_chart_data_time()
	{
		$id = request()->getPost('id');
		$dt_ate = request()->getPost('dt_ate');
		$dt_de = request()->getPost('dt_de');

		$data = $this->Report_Model->staff_sales_graph2($id, $dt_de, $dt_ate);
		return response()->setJSON($data);
	}


	function get_data_timeVenci()
	{
		$data = $this->Report_Model->staff_sales_graph3();
		return response()->setJSON($data);
	}



	function get_staff()
	{

		if ($this->Privileges_Model->check_privilege('staff', 'all')) {

			$staffs = $this->Staff_Model->get_all_staff(false, true);
			$data_staffs = array();

			foreach ($staffs as $staff) {
				$type = lang2('admin');
				$color = '#26c281';

				if ($staff['admin'] == '1') {
					$type = lang2('admin');
					$color = '#26c281';
				}

				if ($staff['staffmember'] == '1') {
					$type = lang2('staff');
					$color = '#686868';
				}

				if ($staff['other'] == '1') {
					$type = lang2('other');
					$color = '#e8ab00';
				}

				$data_staffs[] = array(

					'id' => $staff['id'],
					'name' => $staff['staffname'],
					'avatar' => $staff['staffavatar'],
					'department' => $staff['department'],
					'createdat' => $staff['createdat'] != null ? date('d/m/Y', strtotime($staff['createdat'])) : '',
					'phone' => $staff['phone'],
					'address' => $staff['address'],
					'inactive' => $staff['inactive'],
					'email' => $staff['email'],
					'last_login' => $staff['last_login'],
					'appointment_availability' => $staff['appointment_availability'],
					'staff_number' => get_number('staff', $staff['id'], 'staff', 'staff'),
					'type' => $type,
					'color' => $color
				);
			};

			return response()->setJSON($data_staffs);
		}
	}

	function get_staff_ajax()
	{

		$result = $this->Leads_Model->get_all_leads_ajax($_GET['search']);
		$arSelect = [];

		foreach ($result as $row) {
			$arSelect[] = (object) ["id" => $row['id_lead'], "text" => $row['nm_lead']];
		}

		$json =  array('results' => $arSelect, "total_count" => '4');

		return response()->setJSON($json);
	}
}
