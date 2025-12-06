<?php

namespace App\Controllers;

if (!defined('BASEPATH')) exit('No direct script access allowed');

use DateTime;
use DateTimeZone;

class Timesheets extends BaseController
{

	function __construct()
	{
		parent::loadModels();
		$path = request()->getUri()->getSegment(1);
		if (!$this->Privileges_Model->has_privilege($path)) {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to('panel/');
		}
	}

	function index()
	{
		$data['title'] = lang('timesheets');
		return view('timesheets/index', $data);
	}

	function logtime()
	{
		if ($this->Privileges_Model->check_privilege('timesheets', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$task = request()->getPost('task');
				$start_time = request()->getPost('start_time');
				$end_time = request()->getPost('end_time');
				$note = request()->getPost('note');

				$hasError = false;
				$data['message'] = '';
				if ($task == '') {
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('task');
					$hasError = true;
				} else if ($start_time == '') {
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('start_time');
					$hasError = true;
				} else if ($end_time == '') {
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('end_time');
					$hasError = true;
				} else if (strtotime($end_time) < strtotime($start_time)) {
					$data['message'] = lang2('start_time') . ' ' . lang2('date_error') . ' ' . lang2('end_time');
					$hasError = true;
				} else if ($note == '') {
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
					$hasError = true;
				}
				if ($hasError) {
					$data['success'] = false;
					return response()->setJSON($data);
				}
				if (!$hasError) {
					$params = array(
						'task_id' => $task,
						'start' => $start_time,
						'end' => $end_time,
						'note' => $note,
						'staff_id' => session()->get('usr_id'),
						'status' => 0,
						//'created' => date( 'Y-m-d H:i:s' ), 
					);
					$this->db->table('tasktimer')->insert( $params);
					$time_id = $this->db->insertID();
					if ($time_id) {
						$data['success'] = true;
						$data['message'] = lang2('timesheet') . ' ' . lang2('addmessage');
						return response()->setJSON($data);
					} else {
						$data['success'] = false;
						$data['message'] = lang2('errormessage');
						return response()->setJSON($data);
					}
				}
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}

	function update_logtime($id)
	{
		if ($this->Privileges_Model->check_privilege('timesheets', 'edit')) {
			if (isset($_POST) && count($_POST) > 0) {
				$task = request()->getPost('task');
				$start_time = request()->getPost('start_time');
				$end_time = request()->getPost('end_time');
				$note = request()->getPost('note');

				$hasError = false;
				$data['message'] = '';
				if ($task == '') {
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('task');
					$hasError = true;
				} else if ($start_time == '') {
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('start_time');
					$hasError = true;
				} else if ($end_time == '') {
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('end_time');
					$hasError = true;
				} else if (strtotime($end_time) < strtotime($start_time)) {
					$data['message'] = lang2('start_time') . ' ' . lang2('date_error') . ' ' . lang2('end_time');
					$hasError = true;
				} else if ($note == '') {
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
					$hasError = true;
				}
				if ($hasError) {
					$data['success'] = false;
					return response()->setJSON($data);
				}
				if (!$hasError) {
					$params = array(
						'task_id' => $task,
						'start' => $start_time,
						'end' => $end_time,
						'note' => $note,
					);
					$builder = $this->db->table('tasktimer');
					$response = $builder->where('id', $id)->update($params);
					
					$data['success'] = true;
					$data['message'] = lang2('timesheet') . ' ' . lang2('updatemessage');
					return response()->setJSON($data);
				}
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}

	function get_timesheet_data()
	{
		$result = array();
		if ($this->Privileges_Model->check_privilege('timesheets', 'all')) {
			$result = $this->Report_Model->get_timesheet_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('timesheets', 'own')) {
			$result = $this->Report_Model->get_timesheet_by_privileges(session()->usr_id);
		}
		$timesheet = array();
		$totalT = 0;
		$total_h = $total_m = $total_s = 0;
		foreach ($result as $field) {
			$end_time = $field['end'];
			$date = new DateTime();
			if ($end_time == NULL) {
				$endTime = NULL;
				$end_time = $date->format('Y-m-d H:i:s');
			} else {
				$endTime = $field['end'];
				$end_time = $field['end'];
			}
			$date1 = new DateTime($field['start']);
			$diffs = $date1->diff(new DateTime($end_time));
			$h = $diffs->days * 24;
			$h += $diffs->h;
			$minutes = $diffs->i;
			$seconds = $diffs->s;
			if ($minutes < 10) {
				$minutes = '0' . $minutes;
			}
			if ($seconds < 10) {
				$seconds = '0' . $seconds;
			}
			if ($h < 10) {
				$h = '0' . $h;
			}
			$total = $h . ':' . $minutes . ':' . $seconds;
			$total_h += $h;
			$total_m += $minutes;
			$total_s += $seconds;
			$timesheet[] = array(
				'id' => $field['id'],
				'name' => $field['name'],
				'start_time' => date(get_dateTimeFormat(), strtotime($field['start'])),
				'end_time' => $endTime ? (date(get_dateTimeFormat(), strtotime($endTime))) : null,
				'total_time' => $total,
				'task_id' => $field['task_id'],
				'note' => $field['note'],
				'relation_id' => $field['task_id'],
				'staff' => $field['staff'],
				'staff_id' => $field['staff_id'],
				'avatar' => $field['avatar'],
				'staff_email' => $field['email'],
				'task_number' => $field['task_id'] ? get_number('tasks', $field['task_id'], 'task', 'task') : '',
			);
		}
		if ($total_s > 59) {
			$total_m += (int)($total_s / 60);
			$total_s = $total_s % 60;
		}
		if ($total_m > 59) {
			$total_h += (int)($total_m / 60);
			$total_m = $total_m % 60;
		}
		$data = array(
			'total' => $total_h . 'h:' . $total_m . 'm:' . $total_s . 's',
			'timesheet' => $timesheet,
		);
		return response()->setJSON($data);
	}

	function delete_log($id)
	{
		if ($this->Privileges_Model->check_privilege('timesheets', 'delete')) {
			if (isset($id)) {
				$this->db->table('tasktimer')->delete( array('id' => $id));
				$data['success'] = true;
				$data['message'] = lang2('timesheet') . ' ' . lang2('deletemessage');
				return response()->setJSON($data);
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}
}
