<?php

namespace App\Models;

use CodeIgniter\Model;

class Sales_Model extends Model
{

	// Get sales by id
	public function get_sales($id)
	{
		return $this->db->table('sales')->where('id', $id)->get()->getRowArray();
	}

	// Get all sales
	public function get_all_sales()
	{
		return $this->db->table('sales')->get()->getResultArray();
	}

	// Function to add new sales
	public function add_sales($params)
	{
		$this->db->table('sales')->insert($params);
		return $this->db->insertID();
	}

	// Function to update sales
	public function update_sales($id, $params)
	{
		$this->db->table('sales')->where('id', $id)->update($params);
	}

	// Function to delete sales
	public function delete_sales($id)
	{
		$this->db->table('sales')->where('id', $id)->delete();
	}
}
