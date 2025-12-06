<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . '/third_party/modules/script_update_config.php';
include_once APPPATH . '/third_party/modules/script_update_functions.php';
include_once(APPPATH . 'third_party/modules/app_configuration.php');
include_once(APPPATH . 'third_party/modules/app_functions.php');

class Modules extends BaseController
{

	function index()
	{
		// $data[ 'title' ] = 'Modules';
		// return view( 'modules/index', $data );
	}

	function activate_module($type)
	{
		$is_demo = $this->Settings_Model->is_demo();
		if (!$is_demo) {
			if (isset($_POST) && count($_POST) > 0) {
				// license => 0DX8-680T-L4ZX-QQMS
				$appUrl = 'http' . (empty($_SERVER['HTTPS']) ? '' : 's') . '://' . aplGetRootUrl(aplGetCurrentUrl(), 1, 1, 0, 1);
				$license = aplInstallLicense($appUrl, '', request()->getPost('license_key'));
				if ($license['notification_text'] == 'License OK') {
					ini_set('max_execution_time', 0);
					ini_set('memory_limit', '2048M');
					if ($type == 'hr') {
						$module_version = 0.1;
					}
					$download_notifications_array = ausDownloadFile('version_upgrade_file', $module_version);
					if ($download_notifications_array['notification_case'] == "notification_operation_ok") {
						$download_notifications_array1 = ausDownloadFile('version_upgrade_query', $module_version);
						if ($download_notifications_array1['notification_case'] == "notification_operation_ok") {
							if ($this->install_module_activate()) {
								$this->db->update('modules', array('name' => $type . '_module', 'status' => 1, 'updatedat' => date('Y-m-d H:i:s'), 'license' => 'YES'));
								$data['success'] = true;
								$data['message'] = lang2('module_enabled');
							} else {
								$data['success'] = false;
								$data['message'] = lang2('errormessage');
							}
						} else {
							$data['success'] = false;
							$data['message'] = lang2('errormessage');
						}
					} else {
						$data['success'] = false;
						$data['message'] = lang2('errormessage');
					}
				} else {
					$data['success'] = false;
					$data['message'] = $license['notification_text'];
				}
				return response()->setJSON($data);
			}
		} else {
			$data['success'] = false;
			$data['message'] = 'You can not modify module in demo version';
			return response()->setJSON($data);
		}
	}

	function enable_module($type)
	{
		if (empty($apl_core_notifications = aplCheckSettings())) {
			if (!empty(aplGetLicenseData()) && is_array(aplGetLicenseData())) {
				$verifyRemoteCheck = aplVerifyLicense();
				if ($verifyRemoteCheck['notification_case'] != 'notification_license_ok') {
					$data['success'] = false;
					$data['message'] = lang2('module_license_error');
				} else {
					$this->db->update('modules', array('name' => $type . '_module', 'status' => 1, 'updatedat' => date('Y-m-d H:i:s'), 'license' => 'YES'));
					$this->install_module_enable();
					$data['success'] = true;
					$data['message'] = lang2('module_enabled');
				}
			} else {
				$this->db->update('modules', array('name' => $type . '_module', 'status' => 0, 'updatedat' => date('Y-m-d H:i:s'), 'license' => null));
				$data['success'] = false;
				$data['message'] = lang2('module_not_installed');
			}
		} else {
			$this->db->update('modules', array('name' => $type . '_module', 'status' => 0, 'updatedat' => date('Y-m-d H:i:s'), 'license' => null));
			$data['success'] = false;
			$data['message'] = lang2('module_not_installed');
		}
		return response()->setJSON($data);
	}

	function install_module_activate()
	{
		$this->load->helper('file');
		$this->load->helper('unzip');
		$db_file_name = './QAZWSXEDCRFV_DB.zip';
		$app_files = './QAZWSXEDCRFV_FILE.zip';
		$data['error'] = '';
		$data['message'] = '';
		$data['warning'] = lang2('warning');
		if (!unzip($db_file_name, "./", true, true)) {
			$data['success'] = false;
			$data['message'] = lang2('module_install_error');
		} else {
			$db_file = './Database/database.sql';
			$file_content = file_get_contents($db_file);
			$sqls = explode(';', $file_content);
			array_pop($sqls);
			foreach ($sqls as $sql) {
				$sql = trim($sql);
				if ($sql) {
					$this->db->db_debug = FALSE;
					$this->db->query($sql . ';');
					$error = $this->db->error();
					if ($error) {
						if (ini_get('allow_url_fopen')) {
							$error_file = APPPATH . 'Views/app-errors/errors-log.php';
							$fp = fopen($error_file, 'a');
							fwrite($fp, "<br>\nError[Type='Module Activating'][" . date("Y-m-d H:i:s") . "]: " . $error['message']);
							fclose($fp);
							$data['error'] = $error;
						}
						continue;
					}
				}
			}
			$data['success'] = true;
			$data['message'] = lang2('module_enabled');
			if (!unzip($app_files, "./", true, true)) {
				$data['success'] = false;
				$data['message'] = lang2('module_install_error');
			}
			if (is_file($app_files)) {
				unlink($app_files);
			}
			if (is_file($db_file_name)) {
				copy($db_file, "SQLupdate/hr_module.sql");
				unlink($db_file);
				unlink($db_file_name);
			}
			if ($data['success'] == false) {
				return false;
			} else {
				return true;
			}
		}
	}

	function install_module_enable()
	{
		$db_file = './SQLupdate/hr_module.php';
		$file_content = file_get_contents($db_file);
		$sqls = explode(';', $file_content);
		array_pop($sqls);
		foreach ($sqls as $sql) {
			$sql = trim($sql);
			if ($sql) {
				$this->db->db_debug = FALSE;
				$this->db->query($sql . ';');
				$error = $this->db->error();
				if ($error) {
					if (ini_get('allow_url_fopen')) {
						$error_file = APPPATH . 'Views/app-errors/errors-log.php';
						$fp = fopen($error_file, 'a');
						fwrite($fp, "<br>\nError[Type='Module Enabling'][" . date("Y-m-d H:i:s") . "]: " . $error['message']);
						fclose($fp);
						$data['error'] = $error;
					}
					continue;
				}
			}
		}
		return true;
	}

	function disable_module($type)
	{
		$this->db->update('modules', array('name' => $type . '_module', 'status' => 0, 'updatedat' => date('Y-m-d H:i:s'), 'license' => 'YES'));
		$this->db->table('menu')->delete(array('name' => 'x_menu_hrm'));
		$permission_id = $this->get_permission_id('x_menu_hrm');
		$this->db->table('privileges')->delete(array('permission_id' => $permission_id));
		$this->db->table('permissions')->delete(array('permission' => 'x_menu_hrm'));
		$data['success'] = true;
		$data['message'] = lang2('module_disabled');
		return response()->setJSON($data);
	}

	function get_permission_id($permission)
	{
		$data = $this->db->table('permissions')
			->where('permission', $permission)
			->get()
			->getRowArray();

		return $data['id'];
	}
}
