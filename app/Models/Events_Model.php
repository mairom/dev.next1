<?php

namespace App\Models;

use CodeIgniter\Model;

class Events_Model extends Model
{


	function get_all_events()
	{
		$builder = $this->db->table('events')
			->select('*, staff.staffname as staff, staff.staffavatar as staff_avatar, events.id as id, event_types.name as event_type')
			->join('staff', 'events.staff_id = staff.id', 'left')
			->join('event_types', 'events.event_type = event_types.id', 'left')
			->where('events.public = 1 OR events.staff_id = ' . session()->get('usr_id'))
			->where('staff.id_company', session()->get('id_company'));

		return $builder->get()->getResultArray();
	}

	function get_eventtypes()
	{
		return $this->db->table('event_types')->get()->getResultArray();
	}

	function get_eventtype($id)
	{
		return $this->db->table('event_types')->where('id', $id)->get()->getRowArray();
	}

	function remove_eventtype($id)
	{
		$this->db->table('events')->delete(['event_type' => $id]);
		return $this->db->table('event_types')->delete(['id' => $id]);
	}

	function add_eventtype($params)
	{
		$this->db->table('event_types')->insert($params);
		return $this->db->insertID();
	}

	function get_event_triggers()
	{
		return $this->db->table('event_triggers')->where('status', '0')->get()->getResultArray();
	}

	function get_event($id)
	{
		$builder = $this->db->table('events')
			->select('*, staff.staffname as staff, staff.staffavatar as staff_avatar, events.id as id, event_types.name as event_type, events.added_by as added_by')
			->join('staff', 'events.staff_id = staff.id', 'left')
			->join('event_types', 'events.event_type = event_types.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('events.id', $id);

		return $builder->get()->getRowArray();
	}

	function update_event_trigger($id)
	{
		$this->db->table('event_triggers')->where('id', $id)->update(['status' => 1]);
		return true;
	}

	function get_all_staffs()
	{
		$builder = $this->db->table('staff')
			->select('*, departments.name as department, staff.id as id')
			->join('departments', 'staff.department_id = departments.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('inactive', '0');

		return $builder->get()->getResultArray();
	}

	function add_event($params)
	{
		$this->db->table('events')->insert($params);
		return $this->db->insertID();
	}

	function remove($id)
	{
		return $this->db->table('events')->delete(['id' => $id]);
	}
}
