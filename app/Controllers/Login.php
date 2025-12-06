<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
include_once(APPPATH . 'third_party/script/app_configuration.php');
include_once(APPPATH . 'third_party/script/app_functions.php');

class Login extends BaseController
{
	public $inactive;
	public $roles;
	public $db;

	function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->model('Settings_Model');
		$this->model('Companies_Model');


		$settings = $this->Settings_Model->get_settings('ciuis');
		$timezone = $settings['default_timezone'];

		date_default_timezone_set($timezone);
		$this->model('Staff_Model');
		$this->model('Emails_Model');

		$timezone = $settings['default_timezone'];

		date_default_timezone_set($timezone);
	}



	function index()
	{
		$settings = $this->Settings_Model->get_settings('ciuis');
		if ($settings['two_factor_authentication'] == 1) {
			if (session()->get('LoginOK') && session()->get('2FAVerify')) {
				return redirect()->to(base_url('panel'));
			} else {
				return $this->show_login(false);
			}
		} else {
			if (session()->get('LoginOK')) {
				return redirect()->to(base_url('panel'));
			} else {
				return $this->show_login(false);
			}
		}
	}


	function register($show_error = false)
	{
		$data['error'] = $show_error;
		$languages = $this->Settings_Model->get_languages();
		$lang = array();
		foreach ($languages as $language) {
			$lang[] = array(
				'name' => lang($language['name']),
				'foldername' => $language['foldername'],
				'id' => $language['id'],
				'langcode' => $language['langcode']
			);
		}
		$data['languages'] = $lang;
		
		return view('login/register', $data);
	}

	function register_form()
	{
		$plano_gratis = $this->db->table('plans')
			->where('tp_plano', 'gratis')
			->get()
			->getRowArray();


		$params = [
			'created' => date('Y-m-d H:i:s'),
			'name' => request()->getPost('tp_pessoa') == '1' ? request()->getPost('name') : null,
			'nm_company' => request()->getPost('tp_pessoa') == '2' ? request()->getPost('razao_social') : null,
			'country_id' => '32',
			'email' => request()->getPost('email'),
			'phone' => request()->getPost('phone'),
			'plano' => $plano_gratis['id_plan'],
			'vencimento' => date('Y-m-d', strtotime("+30 days", strtotime(date('Y-m-d')))),
			'tp_pessoa' => request()->getPost('tp_pessoa'),
			'cnpj' => !empty(request()->getPost('cnpj')) ? request()->getPost('cnpj') : null,
			'cpf' => !empty(request()->getPost('cpf')) ? request()->getPost('cpf') : null,
		];


		$this->db->table('companies')->insert($params);
		$company = $this->db->insertID();
		$role_id = $this->Companies_Model->insertSettings($company);
		$this->Companies_Model->insertLeadsAtvSelect($company);
		$this->Companies_Model->insertCategorias($company);
		$this->Companies_Model->insertFunilPadrao($company);

		$paramsStaff = array(
			'language' => 'portuguese_br',
			'role_id' => $role_id,
			'staffname' =>  request()->getPost('tp_pessoa') == '1' ? request()->getPost('name') : request()->getPost('razao_social'),
			'createdat' => date('Y-m-d'),
			'phone' => request()->getPost('phone'),
			'email' => request()->getPost('email'),
			'timezone' => 'America/Sao_Paulo',
			'admin' =>  1,
			'inactive' => NULL,
			'id_company' => $company
		);
		if (request()->getPost('password') != null) {
			$paramsStaff['password'] =  md5(request()->getPost('password'));
		}
		$staff_id = $this->Staff_Model->add_staff($paramsStaff);

		$this->auth();
	}




	function auth()
	{
		$settings = $this->Settings_Model->get_settings('ciuis');
		$this->model('Login_Model');
		$email = request()->getPost('email');
		$password = request()->getPost('password');
		$userInfo = $this->Staff_Model->getUserInfoByEmail($email);

		if ($userInfo) {
		} else {
			session()->setFlashdata('ntf4', lang2('customercanffindmail'));
			return redirect()->to(base_url('login'));
		}

		if ($email && $password && $this->Login_Model->validate_user($email, $password)) {
			if ($settings['two_factor_authentication'] == 1) {
				return redirect()->to(base_url('login/verify_login'));
			} else {
				//session()->setFlashdata('login_notification', lang2('welcomemessagetwo'));
				if (session()->get('admin')) {

					//	session()->setFlashdata('admin_notification', lang2('adminwelcome'));
				}

				if (request()->getPost('language') != null) {
					$this->Staff_Model->update_language(session()->usr_id, request()->getPost('language'));
					session()->set(array('language' => request()->getPost('language')));
					session()->set(['language' => request()->getPost('language')]);
				}

				$staffname = session()->staffname;
				$loggedinuserid = session()->usr_id;
				$token = md5(date('Y-m-d H:i:s'));
				$this->Staff_Model->update_staff($loggedinuserid, ['token' => $token]);
				session()->set(['token' => $token]);

				$builder = $this->db->table('logs');
				$builder->insert([
					'date' => date('Y-m-d H:i:s'),
					'key_log' => 'login',
					'detail' => '<a href="' . base_url('staff/staffmember/' . $loggedinuserid) . '"> ' . $staffname . '</a> ' . lang2('loggedinthesystem'),
					'staff_id' => $loggedinuserid
				]);

				return redirect()->to(base_url('panel'));
			}
		} else {

			session()->setFlashdata('ntf4', 'Senha incorreta!');
			return redirect()->to(base_url('login'));
		}
	}



	function verify_login()
	{

		$this->model('Login_Model');
		$data['secret'] = $this->google->createSecret();
		$website = "http://localhost:8888/googleautenticador/";
		$data['url_qr_code'] = $this->google->getQRCodeGoogleUrl(session()->get['email'], $data['secret'], $website);

		if (isset($_POST) && count($_POST) > 0) {
			$secret = request()->getPost('secret_code');
			$code_verificador = request()->getPost('code');
			$resultado = $this->google->verifyCode($secret, $code_verificador, 0);

			if ($resultado) {
				$this->Login_Model->two_factor_authentication();
				session()->setFlashdata('login_notification', '' . lang2('welcomemessagetwo') . '');
				if (session()->get('admin')) {
					session()->setFlashdata('admin_notification', '' . lang2('adminwelcome') . '');
				}

				$staffname = session()->staffname;
				$loggedinuserid = session()->usr_id;

				$this->db->table('logs')->insert(array(
					'date' => date('Y-m-d H:i:s'),
					'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('loggedinthesystem') . ''),
					'staff_id' => $loggedinuserid
				));

				return redirect()->to(base_url('panel'));
			} else {
				$session = session();
				$session->destroy();
				return redirect()->to(base_url('login'));
			}
		} else {

			return view('login/verify', $data);
		}
	}



	function show_login($show_error = false)
	{

		$data['error'] = $show_error;

		$languages = $this->Settings_Model->get_languages();
		$lang = [];
		foreach ($languages as $language) {
			$lang[] = array(
				'name' => lang($language['name']),
				'foldername' => $language['foldername'],
				'id' => $language['id'],
				'langcode' => $language['langcode']
			);
		}

		$data['languages'] = $lang;

		return view('login/login', $data);
	}



	public function logout()
	{
		$session = session();
		$session->destroy();
		return redirect()->to(base_url('login'));
	}



	function showphpinfo()
	{

		echo phpinfo();
	}



	public function forgot()
	{

		//$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		//print_r(request()->getPost());
		//exit;

		if (request()->getPost('email') == null) {
			return view('login/forgot');
		} else {
		
			$email = request()->getPost('email');
			//$clean = $this->security->xss_clean($email);
			$userInfo = $this->Staff_Model->getUserInfoByEmail($email);

			//print_r($userInfo);
			//exit;
			

			if (!$userInfo) {
				session()->setFlashdata('ntf4', lang2('customercanffindmail'));
				return redirect()->to(base_url('login'));
			}

			$token = $this->Staff_Model->insertToken($userInfo->id);
		
			$qstring = $this->base64url_encode($token);
			$url = base_url() . 'login/reset_password/token/' . $qstring;
			$template = $this->Emails_Model->get_template('staff', 'forgot_password');

			$message_vars = array(
				'{password_url}' => $url,
				'{staffname}' => $userInfo->staffname,
				'{email_signature}' => $template['from_name'],
			);

			$subject = strtr($template['subject'], $message_vars);
			$message = strtr($template['message'], $message_vars);
			$param = array(
				'from_name' => $template['from_name'],
				'email' => $email,
				'subject' => $subject,
				'message' => $message,
				'created' => date("Y.m.d H:i:s"),
				'status' => 0
			);

			if ($email) {
				$this->db->table('email_queue')->insert($param);
			}

			$this->Emails_Model->send_email2($email,  $subject, $message);
			session()->setFlashdata('ntf1', '<b>' . lang2('customerpasswordsend') . '</b>');
			return redirect()->to(base_url('login'));
		}
	}



	public function reset_password()
	{

		$token = $this->base64url_decode(request()->getUri()->getSegment(4));

		//$cleanToken = $this->security->xss_clean($token);

		$user_info = $this->Staff_Model->isTokenValid($token); //either false or array();

		if (!$user_info) {

			session()->setFlashdata('ntf1', lang2('tokenexpired'));

			return redirect()->to(base_url('login'));
		}

		$data = array(

			'firstName' => $user_info->staffname,

			'email' => $user_info->email,

			//'user_id'=>$user_info->id,

			'token' => $this->base64url_encode($token)

		);



		//$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');

		//$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');



		if (!request()->getPost('password')) {

			return view('login/reset_password', $data);
		} else {



			$post = request()->getPost();

			//	$cleanPost = $this->security->xss_clean($post);

			$hashed = md5($post['password']);

			$cleanPost['password'] = $hashed;

			$cleanPost['user_id'] = $user_info->id;

			unset($cleanPost['passconf']);

			if (!$this->Staff_Model->updatePassword($cleanPost)) {

				session()->setFlashdata('ntf1', lang2('problemupdatepassword'));
			} else {

				session()->setFlashdata('ntf1', lang2('passwordupdated'));
			}



			$template = $this->Emails_Model->get_template('staff', 'password_reset');

			$message_vars = array(

				'{staff_email}' => $user_info->email,

				'{staffname}' => $user_info->staffname,

				'{email_signature}' => $template['from_name'],

			);

			$subject = strtr($template['subject'], $message_vars);

			$message = strtr($template['message'], $message_vars);



			$param = array(

				'from_name' => $template['from_name'],

				'email' => $user_info->email,

				'subject' => $subject,

				'message' => $message,

				'created' => date("Y.m.d H:i:s"),

				'status' => 0

			);

			if ($user_info->email) {

				$this->db->table('email_queue')->insert($param);
			}



			$this->Emails_Model->send_email2($user_info->email, $subject, $message);

			return redirect()->to(base_url('login'));
		}
	}



	function license()
	{

		$data['title'] = 'Verify Licence';

		return view('login/licence_verify_input', $data);
	}



	function verify_licence()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$hasError = false;

			$message = '';

			if (request()->getPost('license') == '' || !request()->getPost('license')) {

				$hasError = true;

				$message = 'Please enter your Envato Purchase Code';
			} else if (strlen(request()->getPost('license')) < 5) {

				$hasError = true;

				$message = 'Please enter correct Envato Purchase Code';
			}

			if ($hasError) {

				session()->setFlashdata('error', $message);

				session()->setFlashdata('ntf4', $message);

				return redirect()->to(base_url('login/license'));
			}

			if (!$hasError) {

				$appUrl = 'http' . (empty($_SERVER['HTTPS']) ? '' : 's') . '://' . aplGetRootUrl(aplGetCurrentUrl(), 1, 1, 0, 1);

				aplVerifyEnvatoPurchase(request()->getPost('license'));

				$license = aplInstallLicense($appUrl, '', request()->getPost('license'));

				if ($license['notification_text'] == 'License OK') {

					session()->setFlashdata('ntf1', 'Your Envato License has been updated successfully!');

					return redirect()->to(base_url('login'));
				} else {

					session()->setFlashdata('ntf4', $license['notification_text']);

					session()->setFlashdata('error', $license['notification_text']);

					return redirect()->to(base_url('login/license'));
				}
			}
		}

		//if (isset($_POST['license']) && !empty($_POST['license'])) {

		//$appUrl = 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.aplGetRootUrl(aplGetCurrentUrl(), 1, 1, 0, 1);

		//echo $appUrl;

		//print_r(aplCheckData());

		//echo $appUrl;

		//echo aplGetRawDomain($appUrl);

		//print_r(json_encode(aplVerifyEnvatoPurchase('05256ecf-e10c-45cc-8afc-d0846397c093')));

		//print_r(aplCheckUserInput($appUrl, '', '131ca1c8-df08-4436-b771-3a34deb5dbfa'));

		//print_r(aplInstallLicense($appUrl, '', '05256ecf-e10c-45cc-8afc-d0846397c093'));

		//print_r(aplGetLicenseData());

		//print_r(aplVerifyLicense());

		//print_r(aplGetLicenseData());

		//echo print_r(aplVerifyEnvatoPurchase('05256ecf-e10c-45cc-8afc-d0846397c093'));

	}







	public function base64url_encode($data)
	{

		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}



	public function base64url_decode($data)
	{

		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
}
