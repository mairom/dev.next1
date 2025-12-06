<?php

namespace App\Models;

use CodeIgniter\Model;

defined('BASEPATH') or exit('No direct script access allowed');

class Privileges_Model extends Model
{

	public function get_staff_permissions($id)
	{
		$builder = $this->db->table('privileges');
		$result = $builder->select('permission_id')
			->where(['relation_type' => 'staff', 'relation' => $id])
			->get();
		return array_column($result->getResultArray(), 'permission_id');
	}

	public function has_privilege($path)
	{



		$staff_id = session()->usr_id;
		$role = $this->db->table('staff')->where('id', $staff_id)->get()->getRowArray();
		$role_id = $role['role_id'];


		$permission = $this->db->table('permissions')->where('key', $path)->get()->getRowArray();
		$permission_id = isset($permission['id']) ? $permission['id'] : 0;
		$permissions = $this->db->table('role_permissions')->where(['permission_id' => $permission_id, 'role_id' => $role_id])->get()->getRowArray();



		$permissionsCompany = $this->get_all_permissionsByCompany(session()->id_company);


		$permissionsCompanyData = [];
		foreach ($permissionsCompany as $row) {
			$permissionsCompanyData[] = $row['id_permission'];
		}

		$company = $this->db->table('companies')->where('id_company', session()->id_company)->get()->getRowArray();
		$permissionsPlanData = [];
		if ($company['vencimento'] != "") {
			if (strtotime($company["vencimento"]) >= strtotime(date('Y-m-d'))) {
				$permissionsPlan = $this->get_all_permissionsByPlan($company['plano']);
				foreach ($permissionsPlan as $row) {
					$permissionsPlanData[] = $row['id_permission'];
				}
			} else {
				if (!request()->getGet('p')) {
					//return redirect()->to(base_url('settings?p=minha_assinatura'));
					header('Location: ' . base_url('settings?p=minha_assinatura'));
					exit;
				}
				return false;
			}
		}


		if (!empty($permissions)) {
			if ($permissions || session()->admin == "1") {
				if ($permissions['permission_view_own'] == '1' || $permissions['permission_view_all'] == '1' || session()->admin == "1") {
					if (in_array($permission_id, $permissionsCompanyData) || in_array($permission_id, $permissionsPlanData)) {
						return true;
					}
				}
			}
		}
		return false;
	}

	public function check_privilege($path, $type)
	{
		$staff_id = session()->usr_id;
		$role = $this->db->table('staff')->where('id', $staff_id)->get()->getRowArray();
		$role_id = $role['role_id'];
		$permission = $this->db->table('permissions')->where('key', $path)->get()->getRowArray();
		$permission_id = $permission['id'];
		$permission = $this->db->table('role_permissions')->where(['permission_id' => $permission_id, 'role_id' => $role_id])->get()->getRowArray();



		if ($permission) {
			if ($type == 'create' && $permission['permission_create'] == '1') {
				return true;
			} else if ($type == 'edit' && $permission['permission_edit'] == '1') {
				return true;
			} else if ($type == 'delete' && $permission['permission_delete'] == '1') {
				return true;
			} else if ($type == 'all' && $permission['permission_view_all'] == '1') {
				return true;
			} else if ($type == 'own' && $permission['permission_view_own'] == '1') {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function contact_has_privilege($path)
	{
		$relation = session()->contact_id;
		$builder = $this->db->table('privileges');
		$builder->select('*,permissions.key as permission_key')
			->join('permissions', 'privileges.permission_id = permissions.id', 'left')
			->where(['permissions.key' => $path, 'relation' => $relation, 'relation_type' => 'contact']);
		$rows = $builder->countAllResults();

		return ($rows > 0) ? true : false;
	}

	public function get_privileges()
	{
		return $this->db->table('privileges')->get()->getResultArray();
	}

	public function get_all_permissions()
	{
		return $this->db->table('permissions')->get()->getResultArray();
	}

	public function get_all_permissionsByPlan($id_plan)
	{
		return $this->db->table('permissions_plans')
			->join('permissions', 'permissions.id = permissions_plans.id_permission', 'left')
			->where('id_plan', $id_plan)
			->get()
			->getResultArray();
	}

	public function get_all_permissionsByCompany($company)
	{
		return $this->db->table('companies_permission')
			->join('permissions', 'permissions.id = companies_permission.id_permission', 'left')
			->where('id_company', $company)
			->get()
			->getResultArray();
	}

	public function get_all_permissionsExportByCompany($company)
	{
		return $this->db->table('companies_permission_export')
			->where('id_company', $company)
			->get()
			->getResultArray();
	}

	public function get_all_common_permissions()
	{
		return $this->db->table('permissions')
			->where('type', 'common')
			->get()
			->getResultArray();
	}

	public function add_privilege($id, $privileges)
	{
		$array = ['relation_type' => 'staff', 'relation' => $id];
		$this->db->table('privileges')->where($array)->delete();
		$data = [];
		foreach ($privileges as $key) {
			$data[] = [
				'relation' => (int)$id,
				'relation_type' => 'staff',
				'permission_id' => (int)$key
			];
		}
		return $this->db->table('privileges')->insertBatch($data) ? true : false;
	}

	public function add_contact_privilege($id, $privileges)
	{
		$array = ['relation_type' => 'contact', 'relation' => $id];
		$this->db->table('privileges')->where($array)->delete();
		$data = [];
		foreach ($privileges as $key) {
			$data[] = [
				'relation' => (int)$id,
				'relation_type' => 'contact',
				'permission_id' => (int)$key
			];
		}
		return $this->db->table('privileges')->insertBatch($data) ? true : false;
	}
}
