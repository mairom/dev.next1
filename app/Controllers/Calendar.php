<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');
class Calendar extends BaseController
{
	function index()
	{
		$data['title'] = 'Calendar';
		$data['logs'] = $this->Logs_Model->get_all_logs();
		$data['tbs'] = $this->db->count_all('notifications', array('markread' => ('0')));
		$data['newnotification'] = $this->Notifications_Model->newnotification();
		$data['readnotification'] = $this->Notifications_Model->readnotification();
		$data['notifications'] = $this->Notifications_Model->get_all_notifications();
		$data['events'] = $this->Events_Model->get_all_events();
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		return view('calendar/index', $data);
	}

	function get_Events()
	{
		$data = $this->Events_Model->get_events_json();
		return response()->setJSON($data);
	}

	function addevent()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$vat = request()->getPost('title');
			$hasError = false;
			$data['message'] = '';
			if (request()->getPost('title') == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('event') . ' ' . lang2('title');
			} else if (request()->getPost('eventType') == '') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('event') . ' ' . lang2('type');
			} else if (request()->getPost('eventstart') == '') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('start') . ' ' . lang2('time');
			} else if (request()->getPost('eventend') == '') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('end') . ' ' . lang2('time');
			} else if (request()->getPost('detail') == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('event') . ' ' . lang2('detail');
			} else if (($_POST['notification'] == '1') && ($_POST['notification_duration'] == '0' || $_POST['notification_duration'] == '')) {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('period');
			}
			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}
			if (!$hasError) {
				$event_type = $this->Events_Model->get_eventtype(request()->getPost('eventType'));
				$params = array(
					'title' => $_POST['title'],
					'public' => $event_type['public'],
					'detail' => $_POST['detail'],
					'start' => date_by_timezone($_POST['eventstart']),
					'end' => date_by_timezone($_POST['eventend']),
					'color' => $event_type['color'],
					'is_all' => $_POST['email_to_all'],
					'event_type' => $_POST['eventType'],
					'reminder' => $_POST['notification'],
					'staff_id' => $_POST['staff_id'],
					'added_by' => session()->get('usr_id'),
					'staffname' => session()->get('staffname'),
					'created' => timestamp(),
				);
				$todos = $this->Events_Model->add_event($params);
				if ($_POST['notification'] == '1') {
					$param = array(
						'relation' => $todos,
						'relation_type' => 'event',
						'type' => $_POST['notification_type'],
						'duration_type' => $_POST['notification_time'],
						'duration_period' => $_POST['notification_duration'],
						'start' => $_POST['eventstart'],
						'end' => $_POST['eventend'],
					);
					$this->db->table('event_triggers')->insert( $param);
				}
				if ($todos) {
					$data['success'] = true;
					$data['message'] = lang2('event') . ' ' . lang2('createmessage');
					return response()->setJSON($data);
				} else {
					$data['success'] = false;
					$data['message'] = lang2('errormessage');
					return response()->setJSON($data);
				}
			}
		}
	}

	function new_appointment()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$params = array(
				'title' => $_POST['title'],
				'public' => $_POST['public'],
				'detail' => $_POST['detail'],
				'start' => $_POST['eventstart'],
				'end' => $_POST['eventend'],
				'staff_id' => session()->get('usr_id'),
				'staffname' => session()->get('staffname'),
			);
			$todos = $this->Events_Model->new_appointment($params);
		}
	}

	function confirm_appointment($id)
{
    if (isset($id)) {
        $this->db->table('appointments')
            ->where('id', $id)
            ->update(['status' => 1]);
    }
}

function decline_appointment($id)
{
    if (isset($id)) {
        $this->db->table('appointments')
            ->where('id', $id)
            ->update(['status' => 2]);
    }
}

function mark_as_done_appointment($id)
{
    if (isset($id)) {
        $this->db->table('appointments')
            ->where('id', $id)
            ->update(['status' => 3]);
    }
}

function remove_appointment($id)
{
    if (isset($id)) {
        $this->db->table('appointments')
            ->where('id', $id)
            ->delete();
    }
}

function remove($id)
{
    $events = $this->Events_Model->remove($id);
    if (isset($events['id'])) {
        $this->Events_Model->remove($id);
    }
    session()->setFlashdata('ntf1', lang('eventdeleted'));
    return redirect()->to('calendar/index');
}

function save_colors()
{
    if (request()->getPost()) {
        $staff = $this->db->table('staff')
            ->where('id', session()->get('usr_id'))
            ->get()
            ->getRowArray();

        if ($staff && $staff['admin']) {
            $params = [
                'appointment_color' => request()->getPost('appointment_color'),
                'project_color' => request()->getPost('project_color'),
                'task_color' => request()->getPost('task_color'),
            ];
            $this->Settings_Model->update_colors($params);
            $data['success'] = true;
            $data['message'] = lang2('color') . ' ' . lang2('updatemessage');
            return response()->setJSON($data);
        } else {
            $data['success'] = false;
            $data['message'] = lang2('errormessage');
            return response()->setJSON($data);
        }
    } else {
        $data['success'] = false;
        $data['message'] = lang2('errormessage');
        return response()->setJSON($data);
    }
}

}
