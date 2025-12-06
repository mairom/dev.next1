<?php

namespace App\Models;

use CodeIgniter\Model;

class Tasks_Model extends Model
{

	function get_task($id)
	{
		$builder = $this->db->table('tasks');
		return $builder->getWhere(['id' => $id])->getRowArray();
	}

	function get_task_detail($id)
	{
		$builder = $this->db->table('tasks');
		$builder->select('tasks.*, staff.staffname as assigner, staff.email as staffemail');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->getWhere(['tasks.id' => $id])->getRowArray();
	}

	function get_task_time_log($id)
	{
		$builder = $this->db->table('tasktimer');
		$builder->select('tasktimer.*, staff.staffname as staffmember');
		$builder->join('staff', 'tasktimer.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->getWhere(['tasktimer.task_id' => $id])->getResultArray();
	}

	function get_project_tasks($id)
	{
		$builder = $this->db->table('tasks');
		$builder->select('*');
		$builder->where([
			'tasks.relation_type' => 'project',
			'tasks.relation' => $id
		]);
		$builder->orderBy('id', 'desc');
		return $builder->get()->getResultArray();
	}

	function get_all_tasks()
	{
		$builder = $this->db->table('tasks');
		$builder->select('*');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->get()->getResultArray();
	}

	function get_all_tasks_calendar($staff_id = '')
	{
		$builder = $this->db->table('tasks');
		$builder->select('tasks.*, tasks.id as id');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		if ($staff_id) {
			$builder->where('(tasks.assigned = ' . $this->db->escape($staff_id) . ' OR tasks.addedfrom = ' . $this->db->escape($staff_id) . ')');
		}
		$builder->where('staff.id_company', session()->get('id_company'));
		return $builder->get()->getResultArray();
	}

	function get_all_tasks_for_timer()
	{
		$builder = $this->db->table('tasks');
		$builder->select('*');
		$user_id = session()->get('usr_id');
		if (!$this->isAdmin()) {
			$builder->where('assigned', $user_id);
		}
		$builder->orderBy('id', 'desc');
		return $builder->get()->getResultArray();
	}




	function isAdmin()
	{
		$id = session()->get('usr_id');
		$builder = $this->db->table('staff');
		$builder->where(['admin' => 1, 'id' => $id]);
		$rowCount = $builder->countAllResults();
		return $rowCount > 0;
	}

	function get_subtasks($id)
	{
		$builder = $this->db->table('subtasks');
		$builder->where(['taskid' => $id, 'complete' => 0]);
		$builder->orderBy('id', 'desc');
		return $builder->get()->getResultArray();
	}

	function get_subtaskscomplete($id)
	{
		$builder = $this->db->table('subtasks');
		$builder->where(['taskid' => $id, 'complete' => 1]);
		return $builder->get()->getResultArray();
	}

	function get_task_files($id)
	{
		$builder = $this->db->table('files');
		$builder->where(['relation_type' => 'task', 'relation' => $id]);
		return $builder->get()->getResultArray();
	}

	function add_task($params)
	{
		$builder = $this->db->table('tasks');
		$builder->insert($params);
		$task = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['task_series'] ? $appconfig['task_series'] : $task;
		$task_number = $appconfig['task_prefix'] . $number;

		$builder->where('id', $task)->update(['task_number' => $task_number]);

		return $task;
	}

	function update_task($id, $params)
	{
		$appconfig = get_appconfig();
		$task_data = $this->get_task($id);

		if (empty($task_data['task_number'])) {
			$number = $appconfig['task_series'] ? $appconfig['task_series'] : $id;
			$task_number = $appconfig['task_prefix'] . $number;

			$builder = $this->db->table('tasks');
			$builder->where('id', $id)->update(['task_number' => $task_number]);

			if (!empty($appconfig['task_series'])) {
				$task_number = $appconfig['task_series'] + 1;
				$this->Settings_Model->increment_series('task_series', $task_number);
			}
		}

		$builder = $this->db->table('tasks');
		$builder->where('id', $id);
		$builder->update($params);

		$loggedinuserid = session()->get('usr_id');
		$staffname = session()->get('staffname');

		$logBuilder = $this->db->table('logs');
		$logBuilder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '">' . $staffname . '</a> ' . lang2('updated') . ' <a href="tasks/task/' . $id . '">' . get_number('tasks', $id, 'task', 'task') . '</a>.',
			'staff_id' => $loggedinuserid,
		]);
	}

	function start_timer($id)
	{
		$date = new DateTime();
		$builder = $this->db->table('tasktimer');
		$builder->insert([
			'task_id' => NULL,
			'staff_id' => $id,
			'start' => $date->format('Y-m-d H:i:s'),
			'note' => '',
		]);
		return $this->db->insertID();
	}


	function stop_timer($timer_id, $params)
	{
		$builder = $this->db->table('tasktimer');
		$builder->where('id', $timer_id);
		$response = $builder->update($params);

		$taskBuilder = $this->db->table('tasks');
		$taskBuilder->where('id', $params['task_id']);
		$taskResponse = $taskBuilder->update(['timer' => 0]);

		return $response && $taskResponse;
	}

	function delete_timer($timer_id)
	{
		$builder = $this->db->table('tasktimer');
		return $builder->delete(['id' => $timer_id]);
	}

	function get_timer()
	{
		$builder = $this->db->table('tasktimer');
		$builder->select('tasktimer.id, tasktimer.start, tasktimer.end, tasktimer.task_id, tasks.name, tasktimer.note');
		$builder->join('tasks', 'tasktimer.task_id = tasks.id', 'left');
		$builder->join('staff', 'tasktimer.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('tasktimer.end', NULL);
		$builder->where('tasktimer.staff_id', session()->get('usr_id'));
		$builder->orderBy('tasktimer.id', 'desc');

		$data = $builder->get()->getResultArray();

		return !empty($data) ? $data : false;
	}

	function get_timer_data($id)
	{
		$builder = $this->db->table('tasktimer');
		$builder->join('staff', 'tasktimer.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$response = $builder->getWhere(['tasktimer.id' => $id])->getRowArray();

		return $response;
	}

	function get_all_tasks_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('tasks');
		$builder->select('*');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('tasks.id', 'desc');

		if ($staff_id) {
			$builder->groupStart();
			$builder->where('tasks.assigned', $staff_id);
			$builder->orWhere('tasks.addedfrom', $staff_id);
			$builder->groupEnd();
		}

		return $builder->get()->getResultArray();
	}

	function get_task_by_privileges($id, $staff_id = '')
	{
		$builder = $this->db->table('tasks');
		$builder->select('*, staff.staffname as assigner, tasks.id as id, staff.email as staffemail');
		$builder->join('staff', 'tasks.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->where('tasks.id', $id);
			$builder->groupStart();
			$builder->where('tasks.assigned', $staff_id);
			$builder->orWhere('tasks.addedfrom', $staff_id);
			$builder->groupEnd();
		}

		return $builder->getWhere(['tasks.id' => $id])->getRowArray();
	}
}
