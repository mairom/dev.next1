<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Proposals extends AREA_Controller
{


	function index()
	{
		$data['title'] = lang('areatitleproposals');
		$builder = $this->db->table('proposals');
		$builder->select('proposals.*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, customers.company as customer, customers.email as toemail, customers.namesurname as individual, customers.address as toaddress, proposals.id as id');
		$builder->join('customers', 'proposals.relation = customers.id', 'left');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('proposals.relation', $_SESSION['customer']);
		$builder->where('proposals.relation_type', 'customer');
		$data['proposals'] = $builder->get()->getResultArray();

		//Detaylar
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		return view('area/proposals/index', $data);
	}

	function proposal($token)
	{
		$proposal = $this->Proposals_Model->get_proposal_by_token($token);
		$id = $proposal['id'];
		$data['title'] = get_number('proposals', $id, 'proposal', 'proposal') . ' ' . ' Detail';
		$this->model('Proposals_Model');
		$this->model('Settings_Model');
		$pro = $this->Proposals_Model->get_pro_rel_type($id);
		$rel_type = $pro['relation_type'];
		$data['proposals'] = $this->Proposals_Model->get_proposals($id, $rel_type);
		$data['proposals']['proposal_number'] = get_number('proposals', $proposal['id'], 'proposal', 'proposal');

		$data['items'] = $this->db->table('items')
			->select('*')
			->where(['relation_type' => 'proposal', 'relation' => $id])
			->get()->getResultArray();

			$data['comments'] = $this->db->table('comments')
			->where('relation', $id)
			->where('relation_type', 'proposal')
			->get()
			->getResultArray();
		
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		return view('share/proposal', $data);
	}
}
