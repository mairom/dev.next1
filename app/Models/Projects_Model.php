<?php

namespace App\Models;

use CodeIgniter\Model;

class Projects_Model extends Model
{

	/*

	 * Get projects by id

	 */

	public function get_projects($id)
	{
		$builder = $this->db->table('projects');
		$builder->select('*,customers.id as customer_id, customers.company as customercompany,customers.namesurname as individual,customers.address as customeraddress,customers.email as customeremail,projects.status_id as status, projects.id as id, customers.billing_street, customers.billing_city, customers.billing_state,customers.billing_state_id, customers.billing_zip, customers.phone as customer_phone,customers.country_id, customers.taxoffice, customers.taxnumber, projects.staff_id as staff_id');
		$builder->join('customers', 'projects.customer_id = customers.id', 'left');

		return $builder->getWhere(['projects.id' => $id])->getRowArray();
	}

	public function get_members($id)
	{
		$builder = $this->db->table('projectmembers');
		$builder->select('*,staff.staffname as member,staff.staffavatar as memberavatar,staff.email as memberemail,projectmembers.id as id');
		$builder->join('staff', 'projectmembers.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);

		return $builder->getWhere(['projectmembers.project_id' => $id])->getResultArray();
	}

	public function get_project_admin($id)
	{
		$builder = $this->db->table('projects');
		$builder->select('staff.email as adminemail');
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);

		return $builder->getWhere(['projects.id' => $id])->getRowArray();
	}

	public function get_members_index($id)
	{
		$builder = $this->db->table('projectmembers');
		$builder->select('*,staff.staffname as member,staff.staffavatar as memberavatar,staff.email as memberemail,projectmembers.id as id');
		$builder->join('staff', 'projectmembers.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);

		return $builder->getWhere(['projectmembers.project_id' => $id])->getResultArray();
	}

	public function get_project_notes($id)
	{
		$builder = $this->db->table('notes');
		$builder->select('*,staff.staffname as notestaff,notes.id as id');
		$builder->join('staff', 'notes.addedfrom = staff.id', 'left');
		$builder->orderBy('notes.id', 'desc');

		return $builder->getWhere(['relation' => $id, 'relation_type' => 'project'])->getResultArray();
	}

	public function get_project_services($id)
	{
		$builder = $this->db->table('projectservices');
		$builder->select('*,productcategories.name as categoryname, projectservices.id as serviceid');
		$builder->join('productcategories', 'projectservices.categoryid = productcategories.id', 'left');
		$builder->orderBy('projectservices.id', 'desc');

		return $builder->getWhere(['projectservices.projectid' => $id])->getResultArray();
	}

	public function get_project_service($id)
	{
		$builder = $this->db->table('projectservices');
		$builder->select('*');

		return $builder->getWhere(['id' => $id])->getRowArray();
	}

	public function delete_service($id, $number)
	{
		$builder = $this->db->table('logs');
		$builder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('deleted') . ' ' . lang2('service') . ' ' . lang2('for') . ' ' . ' <a href="projects/project/' . $number . '"></a>'),
			'staff_id' => session()->usr_id,
		]);

		$this->db->table('projectservices')->delete(['id' => $id]);

		return true;
	}

	public function get_customer_by_contact($contact_id)
	{
		$builder = $this->db->table('contacts');
		$data = $builder->getWhere(['id' => $contact_id])->getRowArray();

		return $data['customer_id'];
	}

	public function check_project_permission($project, $contact_id)
	{
		$customer_id = $this->get_customer_by_contact($contact_id);

		if ($customer_id) {
			$builder = $this->db->table('projects');
			$data = $builder->getWhere(['id' => $project, 'customer_id' => $customer_id])->getNumRows();

			return $data > 0;
		} else {
			return false;
		}
	}

	public function get_all_tickets($id)
	{
		$builder = $this->db->table('tickets');
		$builder->select('*,departments.name as department,staff.staffname as staffmembername,staff.staffavatar as staffavatar,contacts.name as contactname,contacts.surname as contactsurname, tickets.id as id');
		$builder->join('contacts', 'tickets.contact_id = contacts.id', 'left');
		$builder->join('departments', 'tickets.department_id = departments.id', 'left');
		$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		$builder->orderBy('date', 'desc');
		$builder->orderBy('priority', 'desc');

		return $builder->getWhere(['relation_id' => $id, 'relation' => 'project'])->getResultArray();
	}

	public function get_ticket_replies($id)
	{
		$builder = $this->db->table('ticketreplies');
		$builder->select('*');

		return $builder->getWhere(['ticket_id' => $id])->getResultArray();
	}

	public function get_tickets($id)
	{
		$builder = $this->db->table('tickets');
		$builder->select('*');

		return $builder->getWhere(['id' => $id])->getRowArray();
	}

	public function delete_tickets($id)
	{
		$builder = $this->db->table('tickets');
		$builder->delete(['id' => $id]);

		$this->db->table('ticketreplies')->delete(['ticket_id' => $id]);

		$builder = $this->db->table('logs');
		$builder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('' . $message = sprintf(lang2('xdeletedxticket'), session()->staffname, $id) . ''),
			'staff_id' => session()->usr_id,
		]);

		return true;
	}



	public function get_products_by_category($id)
	{
		$builder = $this->db->table('products');
		$builder->select('*');

		return $builder->getWhere(['categoryid' => $id])->getResultArray();
	}

	public function copy_services($services, $project_id)
	{
		$builder = $this->db->table('projectservices');

		foreach ($services as $service) {
			$params = [
				'categoryid' => $service['categoryid'],
				'productid' => $service['productid'],
				'servicename' => $service['servicename'],
				'serviceprice' => $service['serviceprice'],
				'servicetax' => $service['servicetax'],
				'quantity' => $service['quantity'],
				'unit' => $service['unit'],
				'servicedescription' => $service['servicedescription'],
				'projectid' => $project_id,
			];

			$builder->insert($params);
		}

		return true;
	}

	public function copy_expenses($expenses, $project_id)
	{
		$builder = $this->db->table('expenses');

		foreach ($expenses as $expense) {
			$params = [
				'category_id' => $expense['category_id'],
				'staff_id' => session()->usr_id,
				'customer_id' => $expense['customer_id'],
				'relation_type' => 'project',
				'relation' => $project_id,
				'account_id' => $expense['account_id'],
				'title' => $expense['title'],
				'date' => $expense['date'],
				'created' => date('Y-m-d H:i:s'),
				'amount' => $expense['amount'],
				'description' => $expense['description'],
			];

			$builder->insert($params);
			$expenseId = $this->db->insertID();

			$appconfig = get_appconfig();
			$number = $appconfig['expense_series'] ? $appconfig['expense_series'] : $expenseId;
			$expense_number = $appconfig['expense_prefix'] . $number;

			$builder->where('id', $expenseId)->update(['expense_number' => $expense_number]);

			if ($appconfig['expense_series']) {
				$appconfig['expense_series']++;
				$this->Settings_Model->increment_series('expense_series', $appconfig['expense_series']);
			}

			$loggedinuserid = session()->usr_id;

			$this->db->table('payments')->insert([
				'transactiontype' => 1,
				'is_transfer' => 0,
				'expense_id' => $expenseId,
				'staff_id' => $loggedinuserid,
				'amount' => $expense['amount'],
				'account_id' => $expense['account_id'],
				'customer_id' => $expense['customer_id'],
				'not' => 'Outgoings for <a href="' . base_url('expenses/receipt/' . $expenseId) . '">EXP-' . $expenseId . '</a>',
				'date' => _pdate($expense['date']),
			]);

			$staffname = session()->staffname;

			$this->db->table('logs')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('addedanewexpense') . ' <a href="expenses/receipt/' . $expenseId . '">' . get_number('expenses', $expenseId, 'expense', 'expense') .  '</a>.'),
				'staff_id' => $loggedinuserid,
				'customer_id' => $expense['customer_id'],
			]);
		}

		return true;
	}

	public function copy_milestones($milestones, $project_id)
	{
		$builder = $this->db->table('milestones');

		foreach ($milestones as $milestone) {
			$params = [
				'project_id' => $project_id,
				'name' => $milestone['name'],
				'order' => $milestone['order'],
				'duedate' => _phdate($milestone['duedate']),
				'description' => $milestone['description'],
				'created' => date('Y-m-d'),
				'color' => 'green',
			];

			$builder->insert($params);
			$milestoneId = $this->db->insertID();

			$staffname = session()->staffname;
			$loggedinuserid = session()->usr_id;

			$this->db->table('logs')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => $staffname . ' ' . lang2('added') . ' ' . lang2('milestone'),
				'staff_id' => $loggedinuserid,
				'project_id' => $project_id,
			]);
		}

		return true;
	}



	public function copy_tasks($tasks, $project_id)
	{
		$builder = $this->db->table('tasks');

		foreach ($tasks as $task) {
			$params = [
				'name' => $task['name'],
				'description' => $task['description'],
				'priority' => $task['priority'],
				'assigned' => $task['assigned'],
				'relation_type' => 'project',
				'relation' => $project_id,
				'milestone' => $task['milestone'],
				'public' => $task['public'],
				'billable' => $task['billable'],
				'visible' => $task['visible'],
				'hourly_rate' => $task['hourly_rate'],
				'startdate' => $task['startdate'],
				'duedate' => $task['duedate'],
				'addedfrom' => session()->get('usr_id'),
				'status_id' => 1,
				'created' => date('Y-m-d H:i:s'),
			];

			$builder->insert($params);
			$task_id = $this->db->insertID();

			$appconfig = get_appconfig();
			$number = $appconfig['task_series'] ? $appconfig['task_series'] : $task_id;
			$task_number = $appconfig['task_prefix'] . $number;

			$builder->where('id', $task_id)->update(['task_number' => $task_number]);

			if ($appconfig['task_series']) {
				$appconfig['task_series']++;
				$this->Settings_Model->increment_series('task_series', $appconfig['task_series']);
			}

			$staffname = session()->staffname;
			$loggedinuserid = session()->usr_id;

			$this->db->table('logs')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => $staffname . ' ' . lang2('added') . ' ' . lang2('new') . ' ' . lang2('task'),
				'staff_id' => $loggedinuserid,
				'project_id' => $project_id,
			]);
		}

		return true;
	}

	public function copy_members($members, $project_id)
	{
		$builder = $this->db->table('projectmembers');

		foreach ($members as $member) {
			$params = [
				'staff_id' => $member['staff_id'],
				'project_id' => $project_id,
			];

			$builder->insert($params);

			$this->db->table('notifications')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => lang2('assignednewproject'),
				'perres' => session()->staffavatar,
				'staff_id' => $member['staff_id'],
				'target' => base_url('projects/project/' . $project_id),
			]);

			$this->db->table('logs')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => session()->staffname . ' ' . lang2('added_a_member_project'),
				'staff_id' => session()->usr_id,
				'project_id' => $project_id,
			]);
		}

		return true;
	}

	public function copy_files($files, $project_id)
	{
		$builder = $this->db->table('files');

		foreach ($files as $file) {
			$params = [
				'relation_type' => 'project',
				'relation' => $project_id,
				'file_name' => $file['file_name'],
				'created' => date("Y.m.d H:i:s"),
			];

			$builder->insert($params);
		}

		return true;
	}

	public function copy_notes($notes, $project_id)
	{
		$builder = $this->db->table('notes');

		foreach ($notes as $note) {
			$params = [
				'relation_type' => 'project',
				'relation' => $project_id,
				'description' => $note['description'],
				'addedfrom' => $note['addedfrom'],
				'created' => date("Y.m.d H:i:s"),
			];

			$builder->insert($params);
		}

		return true;
	}

	public function get_all_projects()
	{
		$builder = $this->db->table('projects');
		$builder->select('*, customers.company as customercompany, customers.namesurname as individual, customers.address as customeraddress, projects.status_id as status, projects.id as id, projects.staff_id as staff_id, customers.email as customeremail');
		$builder->join('customers', 'projects.customer_id = customers.id', 'left');
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('projects.id', 'desc');

		return $builder->get()->getResultArray();
	}

	public function check_member($project, $staff)
	{
		$builder = $this->db->table('projectmembers');
		$builder->where('project_id', $project);
		$builder->where('staff_id', $staff);

		$numRows = $builder->countAllResults();

		return $numRows > 0 ? 'true' : 'false';
	}

	public function get_all_projects_by_customer($id)
	{
		$builder = $this->db->table('projects');
		$builder->select('*, customers.company as customercompany, customers.namesurname as individual, customers.address as customeraddress, projects.status_id as status, projects.id as id, projects.staff_id as staff_id');
		$builder->join('customers', 'projects.customer_id = customers.id', 'left');
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
		$builder->where('projects.customer_id', $id);
		$builder->orderBy('projects.id', 'desc');

		return $builder->get()->getResultArray();
	}

	public function get_all_milestones()
	{
		$builder = $this->db->table('milestones');
		$builder->orderBy('id', 'asc');

		return $builder->get()->getResultArray();
	}

	public function get_all_project_milestones($id)
	{
		$builder = $this->db->table('milestones');
		$builder->where('project_id', $id);
		$builder->orderBy('order', 'asc');

		return $builder->get()->getResultArray();
	}

	public function get_all_project_milestones_task($id)
	{
		$builder = $this->db->table('tasks');
		$builder->where('milestone', $id);
		$builder->orderBy('id', 'desc');

		return $builder->get()->getResultArray();
	}




	public function get_project_time_log($id)
	{
		$builder = $this->db->table('tasktimer');
		$builder->select('*, staff.staffname as staffmember, tasktimer.id as id');
		$builder->join('staff', 'tasktimer.staff_id = staff.id', 'left');
		$builder->where('tasktimer.project_id', $id);

		return $builder->get()->getResultArray();
	}

	public function get_project_files($id)
	{
		$builder = $this->db->table('files');
		$builder->select('*');
		$builder->where('relation_type', 'project');
		$builder->where('relation', $id);
		$builder->orderBy('id', 'desc');

		return $builder->get()->getResultArray();
	}

	public function get_project_tasks($project_id)
	{
		$builder = $this->db->table('tasks');
		$builder->select('*');
		$builder->where('relation_type', 'project');
		$builder->where('relation', $project_id);

		return $builder->get()->getResultArray();
	}

	public function get_project_task_files($task_id)
	{
		$builder = $this->db->table('files');
		$builder->select('*');
		$builder->where('relation_type', 'task');
		$builder->where('relation', $task_id);

		return $builder->get()->getResultArray();
	}

	public function add_projects($params)
	{
		$builder = $this->db->table('projects');
		$builder->insert($params);
		$project = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['project_series'] ? $appconfig['project_series'] : $project;
		$project_number = $appconfig['project_prefix'] . $number;

		$builder->where('id', $project)->update(['project_number' => $project_number]);

		return $project;
	}



	/*

	 * function to update projects

	 */



	public function update2($id, $params)
	{
		$appconfig = get_appconfig();

		$project_data = $this->get_projects($id);

		if (empty($project_data['project_number'])) {
			$number = $appconfig['project_series'] ? $appconfig['project_series'] : $id;
			$project_number = $appconfig['project_prefix'] . $number;

			$builder = $this->db->table('projects');
			$builder->where('id', $id)->update(['project_number' => $project_number]);

			if (!empty($appconfig['project_series'])) {
				$project_number = $appconfig['project_series'] + 1;
				$this->Settings_Model->increment_series('project_series', $project_number);
			}
		}

		$builder = $this->db->table('projects');
		$builder->where('id', $id);
		$response = $builder->update($params);

		$loggedinuserid = session()->get('usr_id');
		$staffname = session()->get('staffname');

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('updated') . ' ' . lang2('project') . ' <a href="' . base_url() . 'projects/project/' . $id . '">' . get_number('projects', $id, 'project', 'project') . '</a>.',
			'staff_id' => $loggedinuserid,
		]);

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('updated') . ' ' . lang2('project'),
			'staff_id' => $loggedinuserid,
			'project_id' => $id,
		]);

		return $response;
	}

	public function markas()
	{
		$builder = $this->db->table('projects');
		$builder->where('id', request()->getPost('project_id'));
		return $builder->update(['status_id' => request()->getPost('status_id')]);
	}

	public function markas_complete()
	{
		$builder = $this->db->table('projects');
		$builder->where('id', request()->getPost('project_id'));
		$builder->update(['status_id' => request()->getPost('status_id')]);

		$this->db->table('tickets')
			->where(['relation' => 'project', 'relation_id' => request()->getPost('project_id')])
			->update(['status_id' => '4']);

		$this->db->table('tasks')
			->where(['relation' => request()->getPost('project_id'), 'relation_type' => 'project'])
			->update(['status_id' => '4']);

		return true;
	}

	public function add_milestone($id, $params)
	{
		$builder = $this->db->table('milestones');
		$builder->insert($params);
		$milestone = $this->db->insertID();

		$loggedinuserid = session()->get('usr_id');
		$staffname = session()->get('staffname');

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('added') . ' ' . lang2('milestone') . ' ' . lang2('for') . ' <a href="projects/project/' . $id . '">' . get_number('projects', $id, 'project', 'project') . '</a>',
			'staff_id' => $loggedinuserid,
			'project_id' => $id,
		]);

		return $milestone;
	}

	public function update_milestone($id, $params)
	{
		$builder = $this->db->table('milestones');
		$builder->where('id', $id);
		return $builder->update($params);
	}

	public function delete_projects($id, $number)
	{
		$builder = $this->db->table('projects');
		$builder->delete(['id' => $id]);

		$this->db->table('notes')->delete(['relation' => $id, 'relation_type' => 'project']);
		$this->db->table('logs')->delete(['project_id' => $id]);
		$this->db->table('projectmembers')->delete(['project_id' => $id]);
		$this->db->table('milestones')->delete(['project_id' => $id]);
		$this->db->table('projectservices')->delete(['projectid' => $id]);

		$tickets = $this->get_all_tickets($id);

		foreach ($tickets as $ticket) {
			$replies = $this->get_ticket_replies($ticket['id']);
			foreach ($replies as $reply) {
				if ($reply['attachment']) {
					$file_path = './uploads/attachments/' . $reply['attachment'];
					if (is_file($file_path)) {
						unlink($file_path);
					}
				}
			}
			$this->db->table('ticketreplies')->delete(['ticket_id' => $ticket['id']]);
		}
		$this->db->table('tickets')->delete(['relation_id' => $id, 'relation' => 'project']);

		$tasks = $this->get_project_tasks($id);
		foreach ($tasks as $task) {
			$task_files = $this->get_project_task_files($task['id']);
			foreach ($task_files as $task_file) {
				$file_path = './uploads/files/' . $task_file['file_name'];
				if (is_file($file_path)) {
					unlink($file_path);
				}
			}
			$this->db->table('files')->delete(['relation' => $task['id'], 'relation_type' => 'task']);
			$this->db->table('subtasks')->delete(['taskid' => $task['id']]);
		}
		$this->db->table('tasks')->delete(['relation' => $id, 'relation_type' => 'project']);

		$files = $this->get_project_files($id);
		foreach ($files as $file) {
			if ($file['is_old'] == '1') {
				$file_path = './uploads/files/' . $file['file_name'];
				if (is_file($file_path)) {
					unlink($file_path);
				}
			}
		}
		$this->db->table('files')->delete(['relation' => $id, 'relation_type' => 'project']);

		$folder = './uploads/files/projects/' . $id;
		if (is_dir($folder)) {
			delete_files($folder, true);
			rmdir($folder);
		}

		$loggedinuserid = session()->get('usr_id');
		$staffname = session()->get('staffname');

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('project_deleted') . ' ' . $number . '.',
			'staff_id' => $loggedinuserid,
		]);

		return true;
	}



	public function proposal_add($params)
	{
		$builder = $this->db->table('proposals');
		$builder->insert($params);
		$proposal = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['proposal_series'] ?: $proposal;
		$proposal_number = $appconfig['proposal_prefix'] . $number;

		$builder->where('id', $proposal)->update(['proposal_number' => $proposal_number]);

		if (!empty($appconfig['proposal_series'])) {
			$proposal_number = $appconfig['proposal_series'] + 1;
			$this->Settings_Model->increment_series('proposal_series', $proposal_number);
		}

		$items = request()->getPost('items');
		foreach ($items as $item) {
			$total = $item['quantity'] * $item['price']
				+ (($item['tax']) / 100 * $item['quantity'] * $item['price'])
				- (($item['discount']) / 100 * $item['quantity'] * $item['price']);

			$this->db->table('items')->insert([
				'relation_type' => 'proposal',
				'relation' => $proposal,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $item['price'],
				'tax' => $item['tax'],
				'discount' => $item['discount'],
				'total' => $total,
			]);
		}

		$staffname = session()->get('staffname');
		$staffavatar = session()->get('staffavatar');

		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $staffname . ' ' . lang2('isaddedanewproposal'),
			'customer_id' => $params['relation'],
			'perres' => $staffavatar,
			'target' => base_url('area/proposals/proposal/' . $params['token']),
		]);

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . session()->get('usr_id') . '"> ' . $staffname . '</a> ' . lang2('added') . ' <a href="proposals/proposal/' . $proposal . '">' . get_number('proposals', $proposal, 'proposal', 'proposal') . '</a>.',
			'staff_id' => session()->get('usr_id'),
		]);

		return $proposal;
	}

	public function update_pdf_status($id, $value)
	{
		$builder = $this->db->table('projects');
		$builder->where('id', $id);
		return $builder->update(['pdf_report' => $value]);
	}

	public function get_all_projects_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('projects');
		$builder->select('*, customers.company as customercompany, customers.namesurname as individual, customers.address as customeraddress, projects.status_id as status, projects.id as id, projects.staff_id as staff_id');
		$builder->join('customers', 'projects.customer_id = customers.id', 'left');
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('projects.id', 'desc');

		if (!empty($staff_id)) {
			$builder->where('projects.staff_id', $staff_id);
		}

		return $builder->get()->getResultArray();
	}


	public function get_projects_stats($staff_id = '')
	{
		if ($staff_id) {
			$tpz = $this->db->table('projects')->where('projects.staff_id', $staff_id)->countAllResults();
			$nsp = $this->db->table('projects')->where(['projects.staff_id' => $staff_id, 'projects.status_id' => 1])->countAllResults();
			$sep = $this->db->table('projects')->where(['projects.staff_id' => $staff_id, 'projects.status_id' => 2])->countAllResults();
			$pep = $this->db->table('projects')->where(['projects.staff_id' => $staff_id, 'projects.status_id' => 3])->countAllResults();
			$cap = $this->db->table('projects')->where(['projects.staff_id' => $staff_id, 'projects.status_id' => 4])->countAllResults();
			$cop = $this->db->table('projects')->where(['projects.staff_id' => $staff_id, 'projects.status_id' => 5])->countAllResults();
		} else {
			$tpz = $this->db->table('projects')->countAllResults();
			$nsp = $this->db->table('projects')->where('projects.status_id', 1)->countAllResults();
			$sep = $this->db->table('projects')->where('projects.status_id', 2)->countAllResults();
			$pep = $this->db->table('projects')->where('projects.status_id', 3)->countAllResults();
			$cap = $this->db->table('projects')->where('projects.status_id', 4)->countAllResults();
			$cop = $this->db->table('projects')->where('projects.status_id', 5)->countAllResults();
		}

		return [
			'not_started_percent' => $tpz > 0 ? number_format(($nsp * 100) / $tpz) : 0,
			'started_percent' => $tpz > 0 ? number_format(($sep * 100) / $tpz) : 0,
			'percentage_percent' => $tpz > 0 ? number_format(($pep * 100) / $tpz) : 0,
			'cancelled_percent' => $tpz > 0 ? number_format(($cap * 100) / $tpz) : 0,
			'complete_percent' => $tpz > 0 ? number_format(($cop * 100) / $tpz) : 0,
		];
	}

	public function get_project_by_priviliges($id, $staff_id = '')
	{
		$builder = $this->db->table('projects');
		$builder->select('*, customers.id as customer_id, customers.company as customercompany, customers.namesurname as individual, customers.address as customeraddress, customers.email as customeremail, projects.status_id as status, projects.id as id, customers.billing_street, customers.billing_city, customers.billing_state, customers.billing_state_id, customers.billing_zip, customers.phone as customer_phone, customers.country_id, customers.taxoffice, customers.taxnumber, projects.staff_id as staff_id');
		$builder->join('customers', 'projects.customer_id = customers.id', 'left');
		$builder->join('staff', 'projects.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		if (!empty($staff_id)) {
			$builder->where('projects.staff_id', $staff_id);
		}

		$builder->where('projects.id', $id);

		return $builder->get()->getRowArray();
	}
}
