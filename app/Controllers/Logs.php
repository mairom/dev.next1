<?php
namespace App\Controllers;
defined('BASEPATH') or exit('No direct script access allowed');

class Logs extends BaseController
{

	function __construct()
	{
		parent::loadModels();
	}
	
	function index()
	{
		$data['title'] = 'Logs';
		$data['logs'] = $this->Logs_Model->get_all_logs();
		$data['tbs'] = $this->db->count_all('notifications', array('markread' => ('0')));
		$data['newnotification'] = $this->Notifications_Model->newnotification();
		$data['readnotification'] = $this->Notifications_Model->readnotification();
		$data['notifications'] = $this->Notifications_Model->get_all_notifications();
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		return view('logs', $data);
	}


	function remove($id)
	{
		session()->setFlashdata('logs_silindi', lang2('logsdeleted'));
		$logs = $this->Logs_Model->get_logs($id);
		if (isset($logs['id'])) {
			$this->Logs_Model->delete_logs($id);
		return redirect()->to('logs/index');
		} else
			show_error('Logs Silindi.');
	}


	function last_logs()
	{
		$data['logs'] = $this->Logs_Model->panel_last_logs();
		$data = array();
		return response()->setJSON($data);
	}


	function insert()
	{
		$key_log = request()->getPost('key_log');
		$prossiga = true;

		if (in_array($key_log, ['inactive', 'login'])) {
			$lastOperation = $this->Logs_Model->panel_last_login_log_by_staff()->key_log;
			if ($key_log == $lastOperation) {
				$prossiga = false;
			}
		}

		if ($prossiga) {
			$param = array(
				'detail' => request()->getPost('detail'),
				'key_log' => $key_log,
				'date' =>  date("Y.m.d H:i:s"),
				'staff_id' => session()->usr_id,
			);
			$this->db->table('logs')->insert( $param);
		}
	}


	function getlog_json()
	{
		$data = $this->Logs_Model->getlog_json();
		return response()->setJSON($data);
	}

	function get_logs_by_date()
	{
		$data = $this->Logs_Model->get_logs_by_date(request()->getPost('date'), request()->getPost('staff_id'));

		if (!$data) {
			$data = [];
		}
		return response()->setJSON($data);
	}
}
