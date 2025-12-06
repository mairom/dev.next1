<?php

namespace App\Models;

use CodeIgniter\Model;

class Login_Model extends Model
{

	var $details;

	function validate_user($email, $password)
	{

		$builder = $this->db->table('staff');
		$builder->where('email', $email);
		$builder->where('password', md5($password));
		$login = $builder->get()->getResult();


		if (!empty($login)) {
			$this->details = $login[0];
			session()->set('user_details', $login[0]);

			if (request()->getPost('remember')) {
				// Configurar o tempo de expiração da sessão
				session()->set('sess_expiration', 300 * 24 * 60 * 60); // 300 dias
				session()->set('sess_expire_on_close', FALSE);
			} else {
				session()->set('sess_expire_on_close', TRUE);
			}

			// Chame o método para configurar a sessão se necessário
			$this->set_session();
			return true;
		}

		return false;
	}



	function login_user_company($id_company)
	{
		$builder = $this->db->table('staff');
		$builder->where('id_company', $id_company);
		$builder->where('admin', '1');

		

		if ($id_company == "12") {
			$builder->where('id', '35');
		}

		$login = $builder->get()->getRow();

		if ($login) {
			$this->details = $login;
			$this->set_session2();
			return true;
		}

		return false;
	}

	function login_admin()
	{
		$builder = $this->db->table('staff');
		$builder->where('id', session()->admin_user);
		$login = $builder->get()->getRow();

		if ($login) {
			$this->details = $login;
			$this->set_session2();
			return true;
		}

		return false;
	}


	function set_session2()
	{
		session()->set(array(
			'usr_id' => $this->details->id,
			'staffname' => $this->details->staffname,
			'email' => $this->details->email,
			'root' => $this->details->root,
			'language' => $this->details->language,
			'admin' => $this->details->admin,
			'staffmember' => $this->details->staffmember,
			'staffavatar' => $this->details->staffavatar,
			'staff_timezone' => $this->details->timezone,
			'other' => $this->details->other,
			'id_company' => $this->details->id_company,
			'super_admin' => $this->details->super_admin,
			'LoginOK' => true,
			'token' => $this->details->token,
		));
	}



	function two_factor_authentication()
	{

		$this->set_two_factor_authentication_session();
	}



	function get_logged_in_staff_info($loggedinuserid = 0)
	{

		$staff_tablo = $this->db->dbprefix('staff');

		$sql = "SELECT $staff_tablo.id,$staff_tablo.root,$staff_tablo.language, $staff_tablo.admin,$staff_tablo.staffmember,$staff_tablo.email,$staff_tablo.staffname, $staff_tablo.staffavatar

		FROM $staff_tablo

		WHERE $staff_tablo.inactive=0 AND $staff_tablo.id=$loggedinuserid";

		return $this->db->query($sql)->row();
	}



	function set_session()
	{
		session()->set(array(
			'usr_id' => $this->details->id,
			'staffname' => $this->details->staffname,
			'email' => $this->details->email,
			'root' => $this->details->root,
			'language' => $this->details->language,
			'admin' => $this->details->admin,
			'staffmember' => $this->details->staffmember,
			'staffavatar' => $this->details->staffavatar,
			'staff_timezone' => $this->details->timezone,
			'other' => $this->details->other,
			'id_company' => $this->details->id_company,
			'super_admin' => $this->details->super_admin,
			'LoginOK' => true,
			'token' => $this->details->token,
		));

		if (empty($apl_core_notifications = aplCheckSettings())) {

			if (!empty(aplGetLicenseData()) && is_array(aplGetLicenseData())) {

				$verifyRemoteCheck = aplVerifyLicense();

				if ($verifyRemoteCheck['notification_case'] != 'notification_license_ok') {

					session()->set(array('remote_check' => true, 'LoginOK' => true));
				} else {

					session()->set(array('remote_check' => true, 'LoginOK' => true));
				}
			} else {

				session()->set(array('remote_check' => true, 'LoginOK' => true));
			}
		} else {

			session()->set(array('remote_check' => true, 'LoginOK' => true));
		}
	}



	function set_two_factor_authentication_session()
	{

		session()->set(array(

			'2FAVerify' => true

		));
	}



	function if_admin()
	{

		if (!session()->get('admin')) {

			return 'display:none';
		}
	}



	function usr_id()
	{

		$loggedinuserid = session()->usr_id;

		return $loggedinuserid ? $loggedinuserid : false;
	}



	function add_staff($params)
	{

		$this->db->table('staff')->insert($params);

		$staffmember = $this->db->insertID();

		$appconfig = get_appconfig();

		$number = $appconfig['staff_series'] ? $appconfig['staff_series'] : $staffmember;

		$staff_number = $appconfig['staff_prefix'] . $number;

		$this->db->table('staff')->where('id', $staffmember)->update(['staff_number' => $staff_number]);


		$workplan = array(

			0 =>

			array(

				'day' => lang2('monday'),

				'status' => true,

				'start' => '09:00',

				'end' => '18:00',

				'breaks' =>

				array(

					'start' => '14:30',

					'end' => '15:00',

				),

				'$$hashKey' => 'object:360',

			),

			1 =>

			array(

				'day' => lang2('tuesday'),

				'status' => true,

				'start' => '09:00',

				'end' => '18:00',

				'breaks' =>

				array(

					'start' => '14:30',

					'end' => '15:00',

				),

				'$$hashKey' => 'object:361',

			),

			2 =>

			array(

				'day' => lang2('wednesday'),

				'status' => true,

				'start' => '09:00',

				'end' => '18:00',

				'breaks' =>

				array(

					'start' => '14:30',

					'end' => '15:00',

				),

				'$$hashKey' => 'object:362',

			),

			3 =>

			array(

				'day' => lang2('thursday'),

				'status' => true,

				'start' => '09:00',

				'end' => '18:00',

				'breaks' =>

				array(

					'start' => '14:30',

					'end' => '15:00',

				),

				'$$hashKey' => 'object:363',

			),

			4 =>

			array(

				'day' => lang2('friday'),

				'status' => true,

				'start' => '09:00',

				'end' => '18:00',

				'breaks' =>

				array(

					'start' => '14:30',

					'end' => '15:00',

				),

				'$$hashKey' => 'object:364',

			),

			5 =>

			array(

				'day' => lang2('saturday'),

				'status' => false,

				'start' => '',

				'end' => '',

				'breaks' =>

				array(

					'start' => '',

					'end' => '',

				),

				'$$hashKey' => 'object:365',

			),

			6 =>

			array(

				'day' => lang2('sunday'),

				'status' => false,

				'start' => '',

				'end' => '',

				'breaks' =>

				array(

					'start' => '',

					'end' => '',

				),

				'$$hashKey' => 'object:366',

			),

		);

		$this->db->table('staff_work_plan')->insert(array(

			'staff_id' => $staffmember,

			'work_plan' => json_encode($workplan),

		));

		$stafadded = request()->getPost('name');

		$this->db->table('logs')->insert(array(

			'date' => date('Y-m-d H:i:s'),

			'detail' => ('' . $message = sprintf(lang2('xaddedstaff'), $stafadded, $stafadded) . ''),

			'staff_id' => $staffmember,

		));

		return $staffmember;
	}
}
