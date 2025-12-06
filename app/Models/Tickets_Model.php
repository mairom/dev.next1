<?php

namespace App\Models;

use CodeIgniter\Model;

class Tickets_Model extends Model
{

	/* Get Tickets */

	function get_tickets($id)
	{
		return $this->db->table('tickets')
			->select('*, customers.type as type, customers.email as customeremail, customers.company as company, customers.namesurname as namesurname, departments.name as department, staff.staffname as staffmembername, staff.email as staffemail, contacts.name as contactname, contacts.surname as contactsurname, tickets.staff_id as stid, tickets.status_id as status_id, tickets.id as id')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('customers', 'contacts.customer_id = customers.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('tickets.id', $id)
			->get()
			->getRowArray();
	}

	/* Get All Tickets */
	function get_all_tickets()
	{
		return $this->db->table('tickets')
			->select('*, departments.name as department, staff.staffname as staffmembername, staff.staffavatar as staffavatar, contacts.name as contactname, contacts.surname as contactsurname, tickets.id as id, contacts.email as contactemail')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->orderBy('date', 'desc')
			->orderBy('priority', 'desc')
			->get()
			->getResultArray();
	}

	function get_all_tickets_by_customer($id)
	{
		return $this->db->table('tickets')
			->select('*, departments.name as department, staff.staffname as staffmembername, staff.staffavatar as staffavatar, contacts.name as contactname, contacts.surname as contactsurname, tickets.id as id')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('tickets.contact_id', $id)
			->orderBy('date', 'desc')
			->orderBy('priority', 'desc')
			->get()
			->getResultArray();
	}

	function get_all_open_tickets()
	{
		return $this->db->table('tickets')
			->select('*, departments.name as department, staff.staffname as staffmembername, staff.staffavatar as staffavatar, contacts.name as contactname, contacts.surname as contactsurname, tickets.id as id')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('tickets.status_id', 1)
			->get()
			->getResultArray();
	}

	function get_all_open_tickets_by_staff()
	{
		return $this->db->table('tickets')
			->select('*, departments.name as department, staff.staffname as staffmembername, staff.staffavatar as staffavatar, contacts.name as contactname, contacts.surname as contactsurname, tickets.id as id')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('tickets.status_id', 1)
			->where('tickets.staff_id', session()->get('usr_id'))
			->get()
			->getResultArray();
	}

	function get_all_inprogress_tickets()
	{
		return $this->db->table('tickets')
			->select('*, departments.name as department, staff.staffname as staffmembername, staff.staffavatar as staffavatar, contacts.name as contactname, contacts.surname as contactsurname, tickets.id as id')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('tickets.status_id', 2)
			->get()
			->getResultArray();
	}

	function get_all_answered_tickets()
	{
		return $this->db->table('tickets')
			->select('*, departments.name as department, staff.staffname as staffmembername, staff.staffavatar as staffavatar, contacts.name as contactname, contacts.surname as contactsurname, tickets.id as id')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('tickets.status_id', 3)
			->get()
			->getResultArray();
	}

	function get_all_closed_tickets()
	{
		return $this->db->table('tickets')
			->select('*, departments.name as department, staff.staffname as staffmembername, staff.staffavatar as staffavatar, contacts.name as contactname, contacts.surname as contactsurname, tickets.id as id')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'))
			->where('tickets.status_id', 4)
			->get()
			->getResultArray();
	}

	function add_tickets($params)
	{
		$this->db->table('tickets')->insert($params);
		$ticket = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['ticket_series'] ? $appconfig['ticket_series'] : $ticket;
		$ticket_number = $appconfig['ticket_prefix'] . $number;

		$this->db->table('tickets')->where('id', $ticket)->update(['ticket_number' => $ticket_number]);

		$url = base_url('tickets/ticket/' . $ticket);

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url('staff/staffmember/' . session()->get('usr_id')) . '"> ' . session()->get('staffname') . '</a> ' . lang2('added') . ' <a href="' . base_url('tickets/ticket/' . $ticket) . '">' . get_number('tickets', $ticket, 'ticket', 'ticket') . '</a>',
			'staff_id' => session()->get('usr_id')
		]);

		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => session()->get('staffname') . ' ' . lang2('created_a') . ' ' . lang2('ticket') . ' ' . get_number('tickets', $ticket, 'ticket', 'ticket'),
			'contact_id' => $params['contact_id'],
			'perres' => session()->get('staffavatar'),
			'target' => base_url('area/tickets/ticket/' . $ticket)
		]);

		return $ticket;
	}

	function add_reply_contact($params)
	{
		$this->db->table('ticketreplies')->insert($params);
		return $this->db->insertID();
	}




	function update_tickets($id, $params)
	{
		$appconfig = get_appconfig();
		$ticket_data = $this->get_tickets($id);

		if ($ticket_data['ticket_number'] == '') {
			$number = $appconfig['ticket_series'] ? $appconfig['ticket_series'] : $id;
			$ticket_number = $appconfig['ticket_prefix'] . $number;

			$this->db->table('tickets')->where('id', $id)->update(['ticket_number' => $ticket_number]);

			if ($appconfig['ticket_series'] != '') {
				$ticket_number = $appconfig['ticket_series'] + 1;
				$this->Settings_Model->increment_series('ticket_series', $ticket_number);
			}
		}

		$this->db->table('tickets')->where('id', $id)->update($params);
	}

	function markas()
	{
		$this->db->table('tickets')
			->where('id', request()->getPost('ticket_id'))
			->update(['status_id' => request()->getPost('status_id')]);
	}

	function delete_tickets($id, $number)
	{
		$this->db->table('tickets')->delete(['id' => $id]);

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . session()->get('usr_id') . '"> ' . session()->get('staffname') . '</a> ' . lang2('deleted') . ' ' . $number,
			'staff_id' => session()->get('usr_id')
		]);
	}

	function check_tickets_permission($id, $contact_id)
	{
		return $this->db->table('tickets')
			->where('id', $id)
			->where('contact_id', $contact_id)
			->countAllResults() > 0;
	}

	function weekly_ticket_stats()
	{
		$start_date = date('Y-m-d', strtotime('monday this week', strtotime('last sunday')));
		$end_date = date('Y-m-d', strtotime('sunday this week', strtotime('last sunday')));

		$tickets = $this->db->table('tickets')
			->where("CAST(date as DATE) >=", $start_date)
			->where("CAST(date as DATE) <=", $end_date)
			->get()
			->getResultArray();

		$chart = [
			'labels' => get_weekdays(),
			'datasets' => [
				[
					'label' => 'Weekly Ticket Report',
					'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
					'borderColor' => '#c53da9',
					'borderWidth' => 1,
					'tension' => false,
					'data' => array_fill(0, 7, 0)
				]
			]
		];

		foreach ($tickets as $ticket) {
			$ticket_day = date('l', strtotime($ticket['date']));
			$i = 0;

			foreach (get_weekdays_original() as $day) {
				if ($ticket_day == $day) {
					$chart['datasets'][0]['data'][$i]++;
				}
				$i++;
			}
		}

		return $chart;
	}

	function get_all_tickets_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('tickets')
			->select('*, departments.name as department, staff.staffname as staffmembername, staff.staffavatar as staffavatar, contacts.name as contactname, contacts.surname as contactsurname, tickets.id as id, contacts.email as contactemail')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->orderBy('date', 'desc')
			->orderBy('priority', 'desc')
			->where('staff.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->where('tickets.staff_id', $staff_id);
		}

		return $builder->get()->getResultArray();
	}

	function get_ticket_by_privileges($id, $staff_id = '')
	{
		$builder = $this->db->table('tickets')
			->select('*, customers.type as type, customers.email as customeremail, customers.company as company, customers.namesurname as namesurname, departments.name as department, staff.staffname as staffmembername, staff.email as staffemail, contacts.name as contactname, contacts.surname as contactsurname, tickets.staff_id as stid, tickets.status_id as status_id, tickets.id as id')
			->join('contacts', 'tickets.contact_id = contacts.id', 'left')
			->join('customers', 'contacts.customer_id = customers.id', 'left')
			->join('departments', 'tickets.department_id = departments.id', 'left')
			->join('staff', 'tickets.staff_id = staff.id', 'left')
			->where('staff.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->where('tickets.staff_id', $staff_id);
		}

		return $builder->where('tickets.id', $id)->get()->getRowArray();
	}
}
