<?php

namespace App\Models;

use CodeIgniter\Model;

class Vendors_Model extends Model
{


	function get_vendors($id)
	{
		$builder = $this->db->table('vendors');
		$builder->select('*, vendors.id as id');
		$builder->where('vendors.id_company', session()->get('id_company'));
		$result = $builder->getWhere(['vendors.id' => $id])->getRowArray();

		return $result;
	}

	function get_all_vendors()
	{
		$builder = $this->db->table('vendors');
		$builder->select('*, vendors.id as id');
		$builder->join('vendors_groups', 'vendors.groupid = vendors_groups.id', 'left');
		$builder->where('vendors.id_company', session()->get('id_company'));
		$builder->orderBy('vendors.id', 'desc');
		$result = $builder->get()->getResultArray();

		return $result;
	}

	function add_vendors($params)
	{
		$builder = $this->db->table('vendors');
		$builder->insert($params);

		$vendor_id = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['vendor_series'] ? $appconfig['vendor_series'] : $vendor_id;
		$vendor_number = $appconfig['vendor_prefix'] . $number;

		$builder->where('id', $vendor_id);
		$builder->update(['vendor_number' => $vendor_number]);

		$logBuilder = $this->db->table('logs');
		$logBuilder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . session()->get('usr_id') . '"> ' . session()->get('staffname') . '</a> ' . lang('addedavendor') . ' <a href="vendors/vendor/' . $vendor_id . '">' . get_number('vendors', $vendor_id, 'vendor', 'vendor') . '</a>',
			'staff_id' => session()->get('usr_id'),
		]);

		return $vendor_id;
	}

	function update_vendors($id, $params)
	{
		$appconfig = get_appconfig();
		$vendor_data = $this->get_vendors($id);

		if ($vendor_data['vendor_number'] == '') {
			$number = $appconfig['vendor_series'] ? $appconfig['vendor_series'] : $id;
			$vendor_number = $appconfig['vendor_prefix'] . $number;

			$builder = $this->db->table('vendors');
			$builder->where('id', $id);
			$builder->update(['vendor_number' => $vendor_number]);

			if ($appconfig['vendor_series'] != '') {
				$appconfig['vendor_series']++;
				$this->Settings_Model->increment_series('vendor_series', $appconfig['vendor_series']);
			}
		}

		$builder = $this->db->table('vendors');
		$builder->where('id', $id);
		$response = $builder->update($params);

		$logBuilder = $this->db->table('logs');
		$logBuilder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . session()->get('usr_id') . '"> ' . session()->get('staffname') . '</a> ' . lang('updated') . ' <a href="vendors/vendor/' . $id . '">' . get_number('vendors', $id, 'vendor', 'vendor') . '</a>',
			'staff_id' => session()->get('usr_id'),
		]);

		return $response;
	}

	function delete_vendors($id, $number)
	{
		$builder = $this->db->table('purchases');
		$builder->where('vendor_id', $id);
		$purchaseCount = $builder->countAllResults();

		if ($purchaseCount > 0) {
			return false;
		} else {
			$vendorBuilder = $this->db->table('vendors');
			$response = $vendorBuilder->delete(['id' => $id]);

			$logBuilder = $this->db->table('logs');
			$logBuilder->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => '<a href="staff/staffmember/' . session()->get('usr_id') . '"> ' . session()->get('staffname') . '</a> ' . lang('deleted') . ' ' . $number,
				'staff_id' => session()->get('usr_id'),
			]);

			return $response;
		}
	}




	function get_vendor_groups()
	{
		$builder = $this->db->table('vendors_groups');
		$builder->orderBy('id', 'desc');
		return $builder->get()->getResultArray();
	}

	function get_groups($staff_id = '')
	{
		$builder = $this->db->table('vendors');
		$builder->select('vendors_groups.name as name, COUNT(vendors_groups.name) as y');
		$builder->join('vendors_groups', 'vendors.groupid = vendors_groups.id', 'left');
		$builder->where('vendors.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->where('staff_id', $staff_id);
		}

		$builder->groupBy('vendors_groups.name');
		return $builder->get()->getResultArray();
	}

	function get_group($id)
	{
		$builder = $this->db->table('vendors_groups');
		return $builder->getWhere(['id' => $id])->getRowArray();
	}

	function update_group($id, $params)
	{
		$builder = $this->db->table('vendors_groups');
		$builder->where('id', $id);
		return $builder->update($params);
	}

	function check_group($id)
	{
		$builder = $this->db->table('vendors');
		$builder->where('groupid', $id);
		return $builder->countAllResults();
	}

	function remove_group($id)
	{
		$builder = $this->db->table('vendors_groups');
		return $builder->delete(['id' => $id]);
	}

	function get_all_vendors_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('vendors');
		$builder->select('*, vendors.id as id');
		$builder->join('vendors_groups', 'vendors.groupid = vendors_groups.id', 'left');
		$builder->orderBy('vendors.id', 'desc');
		$builder->where('vendors.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->where('staff_id', $staff_id);
		}

		return $builder->get()->getResultArray();
	}

	function get_vendor_by_privileges($id, $staff_id = '')
	{
		$builder = $this->db->table('vendors');
		$builder->select('*, vendors.id as id');
		$builder->where('vendors.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->where('vendors.id', $id);
			$builder->where('staff_id', $staff_id);
		} else {
			$builder->where('vendors.id', $id);
		}

		return $builder->get()->getRowArray();
	}
}
