<?php
namespace App\Models;
use CodeIgniter\Model;

class Fields_Model extends Model {
	function create_new_field($params)
	{
		$builder = $this->db->table('custom_fields');
		$builder->insert($params);
		return $this->db->insertID();
	}
	
	function update_custom_field($id, $params)
	{
		$builder = $this->db->table('custom_fields');
		$builder->where('id', $id);
		$builder->update($params);
	}
	
	function custom_fields()
	{
		$builder = $this->db->table('custom_fields');
		$builder->orderBy('order', 'asc');
		return $builder->get()->getResultArray();
	}
	
	function custom_field_data_by_id($id)
	{
		$builder = $this->db->table('custom_fields');
		return $builder->where('id', $id)->get()->getRowArray();
	}
	
	function custom_fields_by_type($type)
	{
		$builder = $this->db->table('custom_fields');
		$builder->select('*');
		$builder->where('relation', $type);
		$builder->orderBy('order', 'asc');
		return $builder->get()->getResultArray();
	}
	
	function custom_fields_data_by_type($type, $id, $field_id)
	{
		$builder = $this->db->table('custom_fields_data');
		return $builder->where([
			'relation_type' => $type,
			'relation' => $id,
			'field_id' => $field_id
		])->get()->getRowArray();
	}
	
	function custom_field_data_add_or_update_by_type($fields, $type, $id)
	{
		$builder = $this->db->table('custom_fields_data');
		$builder->where([
			'relation_type' => $type,
			'relation' => $id
		])->delete();
	
		if ($fields) {
			foreach ($fields['custom_fields'] as $field) {
				$builder->insert([
					'field_id' => $field['id'],
					'relation_type' => $type,
					'relation' => $id,
					'data' => $field['data']
				]);
			}
		}
	}
	

}