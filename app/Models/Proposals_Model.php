<?php

namespace App\Models;

use CodeIgniter\Model;

include APPPATH . '/third_party/vendor/autoload.php';

use Dompdf\Dompdf;

class Proposals_Model extends Model
{

	function get_all_proposals()
	{
		$builder = $this->db->table('proposals');
		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, proposals.id as id');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->orderBy('proposals.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));

		return $builder->get()->getResultArray();
	}

	function get_all_proposals_by_customer($id)
	{
		$builder = $this->db->table('proposals');
		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, proposals.id as id');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->orderBy('proposals.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('relation_type', 'customer');
		$builder->where('relation', $id);

		return $builder->get()->getResultArray();
	}

	function get_all_quotes_by_customer($id)
	{
		$builder = $this->db->table('proposals');
		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, proposals.id as id');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->orderBy('proposals.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('relation_type', 'customer');
		$builder->where('relation', $id);
		$builder->where('status_id', 0);
		$builder->where('is_requested', '1');

		return $builder->get()->getResultArray();
	}

	function customer_proposals($id)
	{
		$builder = $this->db->table('proposals');
		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, proposals.id as id');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->orderBy('proposals.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('relation_type', 'customer');
		$builder->where('relation', $id);

		return $builder->get()->getResultArray();
	}

	function project_proposals($id)
	{
		$builder = $this->db->table('proposals');
		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, proposals.id as id');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->orderBy('proposals.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('proposals.project_id', $id);

		return $builder->get()->getResultArray();
	}

	function get_proposal($id)
	{
		$builder = $this->db->table('proposals');
		return $builder->getWhere(['id' => $id])->getRowArray();
	}

	function check_project_id($project_id, $proposal_id)
	{
		$builder = $this->db->table('proposals');
		$data = $builder->where(['id' => $proposal_id, 'project_id' => $project_id])->countAllResults();

		if ($data > 1 || $data == 1) {
			return 'exist';
		} else {
			return 'not_exist';
		}
	}

	function get_pro_rel_type($id)
	{
		$builder = $this->db->table('proposals');
		return $builder->getWhere(['id' => $id])->getRowArray();
	}

	function get_proposal_by_token($token)
	{
		$builder = $this->db->table('proposals');
		return $builder->getWhere(['token' => $token])->getRowArray();
	}

	function get_proposals($id, $rel_type)
	{
		$builder = $this->db->table('proposals');
		if ($rel_type == 'customer') {
			$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, staff.email as staffemail, customers.type as type, customers.company as customercompany, customers.zipcode as zip, customers.email as toemail, customers.namesurname as namesurname, customers.address as toaddress, proposals.status_id as status_id, proposals.id as id');
			$builder->join('customers', 'proposals.relation = customers.id', 'left');
			$builder->join('staff', 'proposals.assigned = staff.id', 'left');
			$builder->where('staff.id_company', session()->get('id_company'));
			$builder->where('proposals.id', $id);

			return $builder->get()->getRowArray();
		} elseif ($rel_type == 'lead') {
			$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, staff.email as staffemail, leads.name as leadname, leads.address as toaddress, leads.email as toemail, proposals.id as id');
			$builder->join('leads', 'proposals.relation = leads.id', 'left');
			$builder->join('staff', 'proposals.assigned = staff.id', 'left');
			$builder->where('staff.id_company', session()->get('id_company'));
			$builder->where('proposals.id', $id);

			return $builder->get()->getRowArray();
		}

		return [];
	}

	function get_proposal_customer($id)
	{
		$builder = $this->db->table('proposals');
		$builder->select('customers.id as customer_id');
		$builder->join('customers', 'proposals.relation = customers.id', 'left');
		return $builder->getWhere(['proposals.id' => $id])->getRowArray();
	}

	function get_proposalitems($id)
	{
		$builder = $this->db->table('proposalitems');
		return $builder->getWhere(['id' => $id])->getRowArray();
	}

	// GET INVOICE DETAILS

	function get_proposal_productsi_art($id)
	{
		$builder = $this->db->table('proposalitems');
		$builder->selectSum('in[total]');
		$builder->where('proposal_id', $id);
		return $builder->get();
	}

	// CHANCE INVOICE STATUS

	function status_1($id)
	{
		$builder = $this->db->table('proposals');
		$builder->where('id', $id)->update(['status_id' => '1']);

		$builder = $this->db->table('sales');
		$builder->where('proposal_id', $id)->update(['status_id' => '1']);
	}

	function status_2($id)
	{
		$builder = $this->db->table('proposals');
		$builder->where('id', $id)->update(['status_id' => '2']);

		$builder = $this->db->table('sales');
		$builder->where('proposal_id', $id)->update(['status_id' => '2']);
	}

	function status_3($id)
	{
		$builder = $this->db->table('proposals');
		$builder->where('id', $id)->update(['status_id' => '3']);

		$builder = $this->db->table('sales');
		$builder->where('proposal_id', $id)->update(['status_id' => '3']);
	}


	function proposal_add_by_customer($params)
	{
		$this->db->table('proposals')->insert($params);
		$proposal = $this->db->insertID();

		$this->db->table('items')->insert([
			'relation_type' => 'proposal',
			'relation' => $proposal,
			'product_id' => '',
			'code' => '',
			'name' => '',
			'description' => '',
			'quantity' => '1',
			'unit' => 'Unit',
			'price' => 0,
			'tax' => 0,
			'discount' => 0,
			'total' => 0,
		]);

		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => ($_SESSION['name'] . ' ' . lang2('isrequested_quote')),
			'customer_id' => $_SESSION['customer'],
			'staff_id' => $params['addedfrom'],
			'target' => base_url('proposals/proposal/' . $proposal)
		]);

		return $proposal;
	}

	// ADD INVOICE

	function proposal_add($params)
	{
		$this->db->table('proposals')->insert($params);
		$proposal = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['proposal_series'] ? $appconfig['proposal_series'] : $proposal;
		$proposal_number = $appconfig['proposal_prefix'] . $number;

		$this->db->table('proposals')->where('id', $proposal)->update(['proposal_number' => $proposal_number]);

		// MULTIPLE INVOICE ITEMS POST
		$items = request()->getPost('items');

		foreach ($items as $item) {
			$this->db->table('items')->insert([
				'relation_type' => 'proposal',
				'relation' => $proposal,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => str_replace(',', '.', str_replace('.', '', $item['price'])),
				'tax' => $item['tax'],
				'discount' => $item['discount'],
				'total' => $item['quantity'] * str_replace(',', '.', str_replace('.', '', $item['price'])) + (($item['tax']) / 100 * $item['quantity'] * str_replace(',', '.', str_replace('.', '', $item['price']))) - (($item['discount']) / 100 * $item['quantity'] * str_replace(',', '.', str_replace('.', '', $item['price']))),
			]);
		}

		// LOG
		if (request()->getPost('proposal_type') != 'true') {
			$staffname = session()->staffname;
			$staffavatar = session()->staffavatar;

			$this->db->table('notifications')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => ($staffname . ' ' . lang2('isaddedanewproposal')),
				'customer_id' => request()->getPost('customer'),
				'perres' => $staffavatar,
				'target' => base_url('area/proposals/proposal/' . $params['token'])
			]);
		}

		$staffname = session()->staffname;
		$loggedinuserid = session()->usr_id;

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => ('<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('added') . ' <a href="proposals/proposal/' . $proposal . '">' . get_number('proposals', $proposal, 'proposal', 'proposal') . '</a>.'),
			'staff_id' => $loggedinuserid,
		]);

		return $proposal;
	}




	//PROPOSAL DELETE

	function delete_proposals($id, $number)
	{
		$this->db->table('proposals')->delete(['id' => $id]);
		$this->db->table('items')->delete(['relation_type' => 'proposal', 'relation' => $id]);
		$this->db->table('pending_process')->delete(['process_relation' => $id, 'process_relation_type' => 'proposal']);

		$staffname = session()->staffname;
		$loggedinuserid = session()->usr_id;

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('deleted') . ' ' . $number,
			'staff_id' => $loggedinuserid
		]);
	}

	function cancelled()
	{
		$this->db->table('proposals')->where('id', request()->getPost('proposal_id'))->update(['status_id' => request()->getPost('status_id')]);
	}

	function markas()
	{
		$this->db->table('proposals')->where('id', request()->getPost('proposal_id'))->update(['status_id' => request()->getPost('status_id')]);
	}

	function deleteproposalitem($id)
	{
		$this->db->table('proposalitems')->delete(['id' => $id]);
	}

	function get_proposal_year()
	{
		return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM proposals ORDER BY year DESC')->get()->getResultArray();
	}

	function update_pdf_status($id, $value)
	{
		$this->db->table('proposals')->where('id', $id)->update(['pdf_status' => $value]);
	}

	function generate_pdf($id)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '2048M');

		if (!is_dir('uploads/files/proposals/' . $id)) {
			mkdir('./uploads/files/proposals/' . $id, 0777, true);
		}

		$pro = $this->Proposals_Model->get_pro_rel_type($id);
		$rel_type = $pro['relation_type'];

		$data['proposals'] = $this->Proposals_Model->get_proposals($id, $rel_type);
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);
		$data['country'] = get_country($data['settings']['country_id']);
		$data['custcountry'] = get_country($data['proposals']['country_id']);
		$data['custstate'] = get_state_name($data['proposals']['state'], $data['proposals']['state_id']);

		$data['items'] = $this->db->table('items')->select('*')->getWhere(['relation_type' => 'proposal', 'relation' => $id])->getResultArray();

		$file_name = get_number('proposals', $id, 'proposal', 'proposal') . '.pdf';
		$html = view('proposals/pdf', $data, TRUE);

		$this->dompdf = new DOMPDF();
		$this->dompdf->loadHtml($html);
		$this->dompdf->set_option('isRemoteEnabled', TRUE);
		$this->dompdf->set_option('isHtml5ParserEnabled', TRUE);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();

		$output = $this->dompdf->output();
		file_put_contents('uploads/files/proposals/' . $id . '/' . $file_name, $output);

		$this->update_pdf_status($id, '1');

		$this->output->delete_cache();
		$this->dompdf->loadHtml(null);
		$this->dompdf = null;
		unset($this->dompdf);

		return true;
	}



	function get_all_proposals_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('proposals');
		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, proposals.id as id');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->orderBy('proposals.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->groupStart();
			$builder->where('proposals.assigned', $staff_id);
			$builder->orWhere('proposals.addedfrom', $staff_id);
			$builder->groupEnd();
		}
	
		return $builder->get()->getResultArray();
	}
	
	function get_proposals_by_privileges($id, $rel_type, $staff_id = '')
	{
		$builder = $this->db->table('proposals');
	
		if ($rel_type == 'customer') {
			$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, staff.email as staffemail, customers.type as type, customers.company as customercompany, customers.zipcode as zip, customers.email as toemail, customers.namesurname as namesurname, customers.address as toaddress, proposals.status_id as status_id, proposals.id as id');
			$builder->join('customers', 'proposals.relation = customers.id', 'left');
		} elseif ($rel_type == 'lead') {
			$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, staff.email as staffemail, leads.name as leadname, leads.address as toaddress, leads.email as toemail, proposals.id as id');
			$builder->join('leads', 'proposals.relation = leads.id', 'left');
		}
	
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->id_company);
	
		if ($staff_id) {
			$builder->where('proposals.id', $id);
			$builder->groupStart();
			$builder->where('proposals.assigned', $staff_id);
			$builder->orWhere('proposals.addedfrom', $staff_id);
			$builder->groupEnd();
			return $builder->get()->getRowArray();
		} else {
			return $builder->where('proposals.id', $id)->get()->getRowArray();
		}
	}
	
}
