<?php

namespace App\Models;

use CodeIgniter\Model;

class Trivia_Model extends Model
{

	// Get todos by staff id
	function get_todos()
	{
		return $this->db->table('todo')
			->where(['done' => 0, 'staff_id' => session()->get('usr_id')])
			->get()
			->getResultArray();
	}

	function get_done_todos()
	{
		return $this->db->table('todo')
			->where(['done' => 1, 'staff_id' => session()->get('usr_id')])
			->get()
			->getResultArray();
	}

	// Function to add new todo ajax
	function add_todo($params)
	{
		$this->db->table('todo')->insert($params);
		return $this->db->insertID();
	}

	function removetodo()
	{
		if (isset($_POST['todo'])) {
			$todoid = $_POST['todo'];
			$this->db->table('todo')->delete(['id' => $todoid]);
		}
	}

	function add_note($params)
	{
		$this->db->table('notes')->insert($params);
		return $this->db->insertID();
	}

	function get_note($id)
	{
		return $this->db->table('notes')->where('id', $id)->get()->getRowArray();
	}

	function get_reminder($id)
	{
		return $this->db->table('reminders')->where('id', $id)->get()->getRowArray();
	}

	function delete_note($id)
	{
		$this->db->table('notes')->delete(['id' => $id]);
	}

	function delete_reminder($id)
	{
		$this->db->table('reminders')->delete(['id' => $id]);
	}

	function removereminder()
	{
		if (isset($_POST['reminder'])) {
			$reminderid = $_POST['reminder'];
			$this->db->table('reminders')->delete(['id' => $reminderid]);
		}
	}

	function removenote()
	{
		if (isset($_POST['notes'])) {
			$noteid = $_POST['notes'];
			$this->db->table('notes')->delete(['id' => $noteid]);
		}
	}

	function add_reminder($params)
	{
		$this->db->table('reminders')->insert($params);
		return $this->db->insertID();
	}

	function get_reminders()
	{
		$builder = $this->db->table('reminders')
			->select('*, staff.staffname as remindercreator, staff.staffavatar as staffpicture, reminders.id as id')
			->join('staff', 'reminders.addedfrom = staff.id', 'left')
			->where('CURDATE() >= date')
			->where('date !=', '0000-00-00')
			->where('isnotified', '1')
			->where('public', '0')
			->where('staff_id', session()->get('usr_id'))
			->where('staff.id_company', session()->get('id_company'));

		return $builder->get()->getResultArray();
	}

	function get_event_public_reminders()
	{
		$builder = $this->db->table('reminders')
			->select('*, staff.staffname as remindercreator, staff.staffavatar as staffpicture, reminders.id as id')
			->join('staff', 'reminders.addedfrom = staff.id', 'left')
			->where('date !=', '0000-00-00')
			->where('isnotified', '1')
			->where('public', '1')
			->where('staff.id_company', session()->get('id_company'));

		return $builder->get()->getResultArray();
	}
}
