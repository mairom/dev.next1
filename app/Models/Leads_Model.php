<?php
namespace App\Models;

use CodeIgniter\Model;

class Leads_Model extends Model
{
	function get_lead($id)
	{
		$builder = $this->db->table('leads');
		$builder->select('*,leadsstatus.name as statusname,staff.staffname as leadassigned,staff.staffavatar as assignedavatar,
		staff.email as staffemail,leadssources.name as sourcename,leads.name as leadname,leads.email as leadmail,leads.phone as leadphone,
		IF(leads.name != "", leads.name, leads.company) as name_lead,
		leads.address as address,leads.id as id');
		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'left');
		$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');

		return $builder->where('leads.id', $id)
			->where('staff.id_company', session()->id_company)
			->get()
			->getRowArray();
	}

	function get_source($id)
	{
		$builder = $this->db->table('leadssources');
		return $builder->where('id', $id)
			->get()
			->getRowArray();
	}

	function get_status($id)
	{
		$builder = $this->db->table('leadsstatus');
		return $builder->where('id', $id)
			->get()
			->getRowArray();
	}

	function check_sources($id)
	{
		$builder = $this->db->table('leads');
		return $builder->where('source', $id)
			->countAllResults();
	}

	function check_statuses($id)
	{
		$builder = $this->db->table('leads');
		return $builder->where('status', $id)
			->where('lost', '0')
			->where('leads.dateconverted IS NULL')
			->countAllResults();
	}

	function get_all_leads()
	{
		$builder = $this->db->table('leads');
		$builder->select('*,leadsstatus.name as statusname,staff.staffname as leadassigned,staff.staffavatar as assignedavatar,leadssources.name as sourcename,leads.name as leadname,leads.email as leadmail,leads.phone as leadphone,leads.id as id');
		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'left');
		$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->where('public = 1 OR assigned_id = ' . session()->get('usr_id'));
		$builder->where('staff.id_company', session()->id_company);
		$builder->orderBy('leads.id', 'desc');

		return $builder->get()
			->getResultArray();
	}

	function get_all_leads_ajax($a = null)
	{
		$builder = $this->db->table('leads');
		$builder->select('*, if(leads.name = "", leads.company, leads.name) as nm_lead, leads.id as id_lead');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'inner');
		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'inner');
		$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
		$builder->join('leads_list', 'leads_list.id_list = leadsstatus.id_list', 'inner');
		$builder->where('staff.id_company', session()->id_company);

		if ($a != null) {
			$builder->like('leads.name', $a)
				->orLike('leads.company', $a);
		}

		$builder->orderBy('leads.id', 'desc');
		return $builder->get()
			->getResultArray();
	}

	function get_all_leads_list($super = 0)
	{
		$builder = $this->db->table('leads_list');
		$builder->select('*');
		if ($super == 0) {
			$builder->where('id_company', session()->id_company);
		}
		$builder->orderBy('nm_list', 'asc');
		return $builder->get()
			->getResultArray();
	}

	function get_all_leads_for_admin()
	{
		$builder = $this->db->table('leads');
		$builder->select('*,leadsstatus.name as statusname,staff.staffname as leadassigned,staff.staffavatar as assignedavatar,leadssources.name as sourcename,leads.name as leadname,leads.email as leadmail,leads.phone as leadphone,leads.id as id');
		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'left');
		$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->orderBy('leads.id', 'desc');
		$builder->limit(100);
		return $builder->get()->getResultArray();
	}

	function get_all_web_leads()
	{
		$builder = $this->db->table('webleads');
		$builder->select('webleads.name,webleads.status as formstatus,webleads.created,webleads.id,leadsstatus.name as statusname,staff.staffname as leadassigned,staff.staffavatar as assignedavatar,leadssources.name as sourcename');
		$builder->join('leadsstatus', 'webleads.lead_status = leadsstatus.id', 'left');
		$builder->join('leadssources', 'webleads.lead_source = leadssources.id', 'left');
		$builder->join('staff', 'webleads.assigned_id = staff.id', 'left');
		$builder->where('assigned_id', session()->get('usr_id'));
		$builder->orderBy('webleads.id', 'desc');

		return $builder->get()
			->getResultArray();
	}

	function get_all_web_leads_for_admin()
	{
		$builder = $this->db->table('webleads');
		$builder->select('webleads.name,webleads.status as formstatus,webleads.created,webleads.id,leadsstatus.name as statusname,staff.staffname as leadassigned,staff.staffavatar as assignedavatar,leadssources.name as sourcename');
		$builder->join('leadsstatus', 'webleads.lead_status = leadsstatus.id', 'left');
		$builder->join('leadssources', 'webleads.lead_source = leadssources.id', 'left');
		$builder->join('staff', 'webleads.assigned_id = staff.id', 'left');
		$builder->orderBy('webleads.id', 'desc');

		return $builder->get()->getResultArray();
	}

	function get_weblead($id, $staff_id = '')
	{
		$builder = $this->db->table('webleads');
		if ($staff_id) {
			return $builder->where('assigned_id', session()->get('usr_id'))
				->where('webleads.id', $id)
				->get()
				->getRowArray();
		} else {
			return $builder->where('webleads.id', $id)
				->get()
				->getRowArray();
		}
	}

	function isAdmin()
	{
		$id = session()->get('usr_id');
		$builder = $this->db->table('staff');
		$rows = $builder->where('admin', 1)
			->where('id', $id)
			->countAllResults();

		return $rows > 0;
	}

	function getFormData_by_token($token)
	{
		$builder = $this->db->table('webleads');
		$total = $builder->where('token', $token)
			->countAllResults();

		if ($total > 0) {
			return $builder->where('token', $token)
				->get()
				->getRowArray();
		} else {
			return false;
		}
	}

	function check_duplicate_lead($email)
	{
		$builder = $this->db->table('leads');
		$total = $builder->where('email', $email)
			->countAllResults();

		return $total > 0;
	}

	function delete_web_form($id)
	{
		$loggedinuserid = session()->get('usr_id');
		$builder = $this->db->table('logs');
		$builder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . session()->get('staffname') . '</a> ' . lang2('deleted') . ' ' . lang2('webleads') . ' ' . lang2('form'),
			'staff_id' => $loggedinuserid
		]);

		$builder = $this->db->table('webleads');
		return $builder->delete(['id' => $id]);
	}


	function update_weblead_form($id, $params)
	{
		$builder = $this->db->table('webleads');
		$response = $builder->where('id', $id)->update($params);

		if ($response) {
			$loggedinuserid = session()->get('usr_id');
			$staffname = session()->get('staffname');
			$builder = $this->db->table('logs');
			$builder->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => '<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('updated') . ' ' . lang2('webleads') . ' <a href="' . base_url() . 'leads/form/' . $id . '">' . lang2('form') . '</a>.',
				'staff_id' => $loggedinuserid,
			]);

			return true;
		} else {
			return false;
		}
	}

	function get_leads_sources($global = false)
	{
		$builder = $this->db->table('leadssources');
		if (!$global) {
			$builder->where('leadssources.id_company', session()->get('id_company'));
		}
		return $builder->get()->getResultArray();
	}

	function get_leads_sources_by_list($id)
	{
		$builder = $this->db->table('leadsstatus');
		$where = [];

		if ($flt_funil = request()->getPost('flt_funil')) {
			if ($flt_funil != '-1') {
				$where[] = "leadsstatus.id_list = '" . $flt_funil . "'";
			}
		}
		if ($lead_status_id = request()->getPost('lead_status_id')) {
			if ($lead_status_id != '-1') {
				$where[] = "leads.lead_status_id = '" . $lead_status_id . "'";
			}
		}
		if ($flt_origem = request()->getPost('flt_origem')) {
			if ($flt_origem != '-1') {
				$where[] = "leads.source = '" . $flt_origem . "'";
			}
		}
		if ($flt_funcionario = request()->getPost('flt_funcionario')) {
			if ($flt_funcionario != '-1') {
				$where[] = "leads.assigned_id = '" . $flt_funcionario . "'";
			}
		}
		if ($flt_periodo = request()->getPost('flt_periodo')) {
			if ($flt_periodo != '-1') {
				$where[] = "DATEDIFF(CURDATE(), date_contacted) < " . $flt_periodo;
			}
		}
		if ($flt_vencidos = request()->getPost('flt_vencidos')) {
			if ($flt_vencidos == "1") {
				$where[] = "leads.ultimoRetorno > '" . date('Y-m-d') . "'";
			} elseif ($flt_vencidos == "2") {
				$where[] = "leads.ultimoRetorno < '" . date('Y-m-d') . "'";
			} elseif ($flt_vencidos == "3") {
				$where[] = "leads.ultimoRetorno = '" . date('Y-m-d') . "'";
			} elseif ($flt_vencidos == "4") {
				$where[] = "leads.ultimoRetorno IS NULL";
			}
		}

		$subQuery = "(SELECT DISTINCT count(*) FROM `leads`
			INNER JOIN staff ON staff.id = leads.assigned_id
			LEFT JOIN leads_oport_perdida ON leads_oport_perdida.id_opt_perd = leads.id
			WHERE status = leadsstatus.id
			AND staff.id_company = '" . session()->get('id_company') . "'
			AND (
				(lost is null or lost = '0') AND lead_status_id = '1'
			)
			OR (
				lost = '1' AND retorn_futuro = '1' AND CAST(now() as date) >= leads_oport_perdida.quando AND etapa = leadsstatus.id
			)
			" . implode(' AND ', $where) . ") as totalLeadsStatus";

		$builder->select("*," . $subQuery);
		$builder->where('id_list', $id);
		$builder->orderBy('ordem', 'asc');

		return $builder->get()->getResultArray();
	}

	function get_leads_sources_by_list2($id)
	{
		$builder = $this->db->table('leadsstatus');
		$builder->select('*');
		$builder->where('id_list', $id);
		$builder->orderBy('ordem', 'asc');
		return $builder->get()->getResultArray();
	}

	function get_leads_status()
	{
		$builder = $this->db->table('leadsstatus');
		$builder->orderBy('ordem', 'asc');
		return $builder->get()->getResultArray();
	}

	function create_weblead_form($params)
	{
		$builder = $this->db->table('webleads');
		$builder->insert($params);
		$id = $this->db->insertID();
		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');
		$builder = $this->db->table('logs');
		$builder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('addedwebleadform') . ' <a href="' . base_url() . 'leads/form/' . $id . '">' . lang2('form') . '</a>.',
			'staff_id' => $loggedinuserid,
		]);

		return $id;
	}

	function add_lead($params)
	{
		$builder = $this->db->table('leads');
		$builder->insert($params);
		$lead = $this->db->insertID();
		$appconfig = get_appconfig();
		$number = $appconfig['lead_series'] ?? $lead;
		$lead_number = $appconfig['lead_prefix'] . $number;
		$builder->where('id', $lead)->update(['lead_number' => $lead_number]);
		$builder = $this->db->table('logs');
		$builder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="' . base_url() . 'staff/staffmember/' . session()->get('usr_id') . '"> ' . session()->get('staffname') . '</a> ' . lang2('added') . ' <a href="' . base_url() . 'leads/lead/' . $lead . '">' . get_number('leads', $lead, 'lead', 'lead') . '</a>',
			'staff_id' => session()->get('usr_id')
		]);
		return $lead;
	}

	function update_lead($id, $params)
	{
		$appconfig = get_appconfig();
		$lead_data = $this->get_lead($id);

		if (empty($lead_data['lead_number'])) {
			$number = $appconfig['lead_series'] ?? $id;
			$lead_number = $appconfig['lead_prefix'] . $number;

			$builder = $this->db->table('leads');
			$builder->where('id', $id)->update(['lead_number' => $lead_number]);

			if (!empty($appconfig['lead_series'])) {
				$lead_number = $appconfig['lead_series'] + 1;
				$this->Settings_Model->increment_series('lead_series', $lead_number);
			}
		}

		$builder = $this->db->table('leads');
		$response = $builder->where('id', $id)->update($params);

		if ($response) {
			$loggedinuserid = session()->get('usr_id');
			$staffname = session()->get('staffname');

			$builder = $this->db->table('logs');
			$builder->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => '<a href="' . base_url() . 'staff/staffmember/' . $loggedinuserid . '"> ' . $staffname . '</a> ' . lang2('updated') . ' <a href="' . base_url() . 'leads/lead/' . $id . '">' . (!empty($lead_data['company']) ? $lead_data['company'] : $lead_data['name']) . '</a>.',
				'staff_id' => $loggedinuserid,
			]);

			return true;
		} else {
			return false;
		}
	}

	function delete_lead($id, $number)
	{
		$builder = $this->db->table('proposals');
		$proposal = $builder->where(['relation_type' => 'lead', 'relation' => $id])->countAllResults();

		$builder = $this->db->table('orders');
		$order = $builder->where(['relation_type' => 'lead', 'relation' => $id])->countAllResults();

		if ($proposal > 0 || $order > 0) {
			return false;
		} else {
			$builder = $this->db->table('leads');
			$response = $builder->delete(['id' => $id]);

			if ($response) {
				$loggedinuserid = session()->get('usr_id');
				$builder = $this->db->table('logs');
				$builder->insert([
					'date' => date('Y-m-d H:i:s'),
					'detail' => '<a href="staff/staffmember/' . $loggedinuserid . '"> ' . session()->get('staffname') . '</a> ' . lang2('deleted') . ' ' . lang2('lead') . ' ' . $number,
					'staff_id' => $loggedinuserid,
				]);

				return true;
			} else {
				return false;
			}
		}
	}

	function delete_source($id)
	{
		$builder = $this->db->table('leadssources');
		$response = $builder->delete(['id' => $id]);
		return $response;
	}

	function delete_status($id)
	{
		$builder = $this->db->table('leadsstatus');
		$response = $builder->delete(['id' => $id]);
		return $response;
	}

	function isDuplicate($email)
	{
		$builder = $this->db->table('leads');
		$builder->where('email', $email);
		$builder->limit(1);
		return $builder->countAllResults() > 0;
	}

	function add_status($params)
	{
		$builder = $this->db->table('leadsstatus');
		$builder->insert($params);
		return $this->db->insertID();
	}

	function add_funil($params)
	{
		$builder = $this->db->table('leads_list');
		$builder->insert($params);
		return $this->db->insertID();
	}

	function add_source($params)
	{
		$builder = $this->db->table('leadssources');
		$builder->insert($params);
		return $this->db->insertID();
	}

	function update_status($id, $params)
	{
		$builder = $this->db->table('leadsstatus');
		return $builder->where('id', $id)->update($params);
	}

	function update_funil($id_list, $params)
	{
		$builder = $this->db->table('leads_list');
		return $builder->where('id_list', $id_list)->update($params);
	}

	function update_source($id, $params)
	{
		$builder = $this->db->table('leadssources');
		return $builder->where('id', $id)->update($params);
	}

	function get_leads_for_import()
	{
		$builder = $this->db->table('leads');
		$result = $builder->get()->getResultArray();
		return  $result ? $result  : false;
	}


	function insert_csv($data)
	{
		$builder = $this->db->table('leads');
		$builder->insert($data);
		return $this->db->insertID();
	}

	function get_all_leads_by_privileges($staff_id = '', $lost = null, $convert = -1)
	{
		$select = '*, "" as ctt_principal, leadsstatus.color, leadsstatus.id_list, leadsstatus.name as statusname, staff.staffname as leadassigned, staff.staffavatar as assignedavatar,
		leadssources.name as sourcename, leads.name as leadname, leads.email as leadmail, leads.phone as leadphone, leads.id as id, leads.status as status_f,
		IF(leads.name != "", leads.name, leads.company) as name_lead, leads.lost';

		if (request()->getPost('lost') == '1' || request()->getPost('lost') == '-1') {
			$select .= ", leads_oport_perdida.*";
		}

		$builder = $this->db->table('leads');
		$builder->distinct();
		$builder->select($select);

		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'inner');
		$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'inner');

		if (request()->getPost('lost') == '1' || request()->getPost('lost') == '-1') {
			$builder->join('leads_oport_perdida', 'leads_oport_perdida.id_lead = leads.id', 'left');
			$builder->groupBy('leads.id');
		}

		if (request()->getPost('lost') != '0') {
			$builder->join('(SELECT * FROM leads_oport_perdida LIMIT 1) pdd', 'pdd.id_lead = leads.id', 'left');
		}

		$builder->where('staff.id_company', session()->get('id_company'));

		if (request()->getPost('limit')) {
			if (request()->getPost('limit') == '-1') {
				$builder->limit(1000, 20);
			}
		}

		if (request()->getPost('limite')) {
			$builder->limit(request()->getPost('limite'));
		}

		if (request()->getPost('com_fluxo') && request()->getPost('com_fluxo') == 1) {
			$builder->join('fluxos', 'fluxos.funil = leadsstatus.id_list', 'inner');
		}

		if (request()->getPost('flt_funil') && request()->getPost('flt_funil') != '-1') {
			$builder->where('leadsstatus.id_list', request()->getPost('flt_funil'));
		}

		if (request()->getPost('leadAtual') && request()->getPost('leadAtual') != '-1') {
			$builder->orWhere('leads.id', request()->getPost('leadAtual'));
		}

		if (request()->getPost('lost') != '-1') {
			$lost = request()->getPost('lost');
			if ($lost == "0") {
				$builder->where("(leads.lost IS NULL OR leads.lost = '0')");
			} else {
				$builder->where('leads.lost', $lost);
			}
		} else {
			if ($lost != null) {
				if ($lost == "0") {
					$builder->where("(leads.lost IS NULL OR leads.lost = '0')");
				} else {
					$builder->where('leads.lost', $lost);
				}
			}
		}

		if (request()->getPost('flt_origem') && request()->getPost('flt_origem') != '-1') {
			$builder->where('leads.source', request()->getPost('flt_origem'));
		}

		if (request()->getPost('temperatura') && request()->getPost('temperatura') != '-1') {
			$builder->where('leads.temperatura', request()->getPost('temperatura'));
		}

		if (request()->getPost('canais') && request()->getPost('canais') != '-1') {
			$canal = request()->getPost('canais');
			if ($canal == '1') { // Email
				$builder->groupStart();
				$builder->orGroupStart();
				$builder->where('leads.email IS NOT NULL');
				$builder->where('leads.email !=', '');
				$builder->groupEnd();
				$builder->orWhere('(SELECT COUNT(*) FROM leads_contatos WHERE id_lead = leads.id AND email IS NOT NULL AND email != "") > 0', NULL, FALSE);
				$builder->orWhere('(SELECT COUNT(*) FROM leads_data WHERE id_lead = leads.id AND JSON_LENGTH(Emails) > 0) > 0', NULL, FALSE);
				$builder->groupEnd();
			} elseif ($canal == '2') { // Telefone
				$builder->groupStart();
				$builder->orGroupStart();
				$builder->where('leads.phone IS NOT NULL');
				$builder->where('leads.phone !=', '');
				$builder->groupEnd();
				$builder->orWhere('(SELECT COUNT(*) FROM leads_contatos WHERE id_lead = leads.id AND telefone IS NOT NULL AND telefone != "") > 0', NULL, FALSE);
				$builder->orWhere('(SELECT COUNT(*) FROM leads_data WHERE id_lead = leads.id AND JSON_LENGTH(Phones) > 0) > 0', NULL, FALSE);
				$builder->groupEnd();
			} elseif ($canal == '3') { // Email + Telefone
				$builder->groupStart();
				// Verifica Email
				$builder->groupStart();
				$builder->orGroupStart();
				$builder->where('leads.email IS NOT NULL');
				$builder->where('leads.email !=', '');
				$builder->groupEnd();
				$builder->orWhere('(SELECT COUNT(*) FROM leads_contatos WHERE id_lead = leads.id AND email IS NOT NULL AND email != "") > 0', NULL, FALSE);
				$builder->orWhere('(SELECT COUNT(*) FROM leads_data WHERE id_lead = leads.id AND JSON_LENGTH(Emails) > 0) > 0', NULL, FALSE);
				$builder->groupEnd();
				// Verifica Telefone
				$builder->groupStart();
				$builder->orGroupStart();
				$builder->where('leads.phone IS NOT NULL');
				$builder->where('leads.phone !=', '');
				$builder->groupEnd();
				$builder->orWhere('(SELECT COUNT(*) FROM leads_contatos WHERE id_lead = leads.id AND telefone IS NOT NULL AND telefone != "") > 0', NULL, FALSE);
				$builder->orWhere('(SELECT COUNT(*) FROM leads_data WHERE id_lead = leads.id AND JSON_LENGTH(Phones) > 0) > 0', NULL, FALSE);
				$builder->groupEnd();
				$builder->groupEnd();
			} elseif ($canal == '4') { // LinkedIn
				$builder->where('leads.linkedin IS NOT NULL');
				$builder->where('leads.linkedin !=', '');
			}
		}

		if (request()->getPost('flt_funcionario') && request()->getPost('flt_funcionario') != '-1') {
			$builder->where('leads.assigned_id', request()->getPost('flt_funcionario'));
		}

		if (request()->getPost('flt_periodo') && request()->getPost('flt_periodo') != '-1') {
			$builder->where("DATEDIFF(CURDATE(), date_contacted) <", request()->getPost('flt_periodo'));
		}

		if (request()->getPost('status')) {
			$builder->where('leads.status', request()->getPost('status'));
		}

		if (request()->getPost('flt_vencidos')) {
			$flt_vencidos = request()->getPost('flt_vencidos');
			if ($flt_vencidos == "1") {
				$builder->where("leads.ultimoRetorno > '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "2") {
				$builder->where("leads.ultimoRetorno < '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "3") {
				$builder->where("leads.ultimoRetorno = '" . date('Y-m-d') . "'", NULL, false);
			} elseif ($flt_vencidos == "4") {
				$builder->where("leads.ultimoRetorno IS NULL", NULL, false);
			}
		}

		if ($convert != -1) {
			if ($convert) {
				$builder->where('leads.dateconverted IS NOT NULL');
			} else {
				$builder->where('leads.dateconverted IS NULL');
			}
		}

		$builder->orderBy('name_lead', 'asc');

		if ($staff_id) {
			$builder->where('(leads.staff_id=' . $staff_id . ' OR leads.assigned_id=' . $staff_id . ')');
		}

		$leads = $builder->get()->getResultArray();

		// Buscar dados de contatos em uma query separada
		$leadIds = array_column($leads, 'id');
		$contatosData = [];
		$enrichmentData = [];

		if (!empty($leadIds)) {
			// Query para buscar emails e telefones dos contatos
			$contatosBuilder = $this->db->table('leads_contatos');
			$contatosBuilder->select('id_lead, 
				GROUP_CONCAT(DISTINCT CASE WHEN email IS NOT NULL AND email != "" THEN email END SEPARATOR ",") as contatos_emails,
				GROUP_CONCAT(DISTINCT CASE WHEN telefone IS NOT NULL AND telefone != "" THEN telefone END SEPARATOR ",") as contatos_telefones');
			$contatosBuilder->whereIn('id_lead', $leadIds);
			$contatosBuilder->groupBy('id_lead');
			$contatosResult = $contatosBuilder->get()->getResultArray();

			foreach ($contatosResult as $contato) {
				$contatosData[$contato['id_lead']] = $contato;
			}

			// Query para buscar dados de enriquecimento
			$enrichmentBuilder = $this->db->table('leads_data');
			$enrichmentBuilder->select('id_lead, 
				IF(JSON_LENGTH(COALESCE(JSON_EXTRACT(value, "$.Emails"), "[]")) > 0, "1", "0") as has_enrichment_emails,
				IF(JSON_LENGTH(COALESCE(JSON_EXTRACT(value, "$.Phones"), "[]")) > 0, "1", "0") as has_enrichment_phones');
			$enrichmentBuilder->whereIn('id_lead', $leadIds);
			$enrichmentResult = $enrichmentBuilder->get()->getResultArray();

			foreach ($enrichmentResult as $enrichment) {
				$enrichmentData[$enrichment['id_lead']] = $enrichment;
			}
		}

		// Adicionar os dados aos leads
		foreach ($leads as &$lead) {
			$leadId = $lead['id'];
			
			// Adicionar dados de contatos
			$lead['contatos_emails'] = isset($contatosData[$leadId]) ? $contatosData[$leadId]['contatos_emails'] : null;
			$lead['contatos_telefones'] = isset($contatosData[$leadId]) ? $contatosData[$leadId]['contatos_telefones'] : null;
			
			// Adicionar dados de enriquecimento
			$lead['has_enrichment_emails'] = isset($enrichmentData[$leadId]) ? $enrichmentData[$leadId]['has_enrichment_emails'] : '0';
			$lead['has_enrichment_phones'] = isset($enrichmentData[$leadId]) ? $enrichmentData[$leadId]['has_enrichment_phones'] : '0';
		}

		return $leads;
	}

	function get_lead_by_privileges($id, $staff_id = '')
	{
		$builder = $this->db->table('leads');
		$builder->select('*, leadsstatus.name as statusname, staff.staffname as leadassigned,leads.status as status_f,
		staff.staffavatar as assignedavatar, staff.email as staffemail, leadssources.name as sourcename,
		leads.name as leadname, leads.email as leadmail, leads.phone as leadphone, leads.address as address,
		leads.id as id');
		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'left');
		$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
		$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		$builder->join('leads_oport_perdida', 'leads_oport_perdida.id_opt_perd = leads.id', 'left');

		if ($staff_id) {
			$builder->where('leads.id', $id);
			$builder->where('(leads.staff_id=' . $staff_id . ' OR leads.assigned_id=' . $staff_id . ')');
			return $builder->get()->getRowArray();
		} else {
			$builder->where('leads.id', $id);
			$builder->where('staff.id_company', session()->get('id_company'));
			return $builder->get()->getRowArray();
		}
	}

	function get_status_list($id)
	{
		$builder = $this->db->table('leadsstatus');
		$builder->where('id_list', function ($builder) use ($id) {
			$builder->select('id_list')
				->from('leadsstatus')
				->where('id', $id)
				->limit(1);
		});
		$builder->orderBy('ordem', 'asc');
		return $builder->get()->getResult();
	}

	function getListSelectLeads()
	{
		$builder = $this->db->table('leads_atv_select');
		$builder->select('*');
		$builder->where("id_company =", session()->get('id_company'));
		$builder->orWhere("id_company =", '1');
		$builder->orderBy('nm_atividade_select', 'asc');
		return $builder->get()->getResultArray();
	}

	function getListSelectCustomers()
	{
		$builder = $this->db->table('customers_atv_select');
		$builder->select('*');
		$builder->where("id_company =", session()->get('id_company'));
		$builder->orWhere("id_company =", '1');
		return $builder->get()->getResultArray();
	}

	function getContatosLead($id)
	{
		$builder = $this->db->table('leads_contatos');
		$builder->select('*');
		$builder->where('id_lead', $id);
		return $builder->get()->getResultArray();
	}

	function getContatosLeadAll($id)
	{
		$builder = $this->db->table('leads_contatos');
		$builder->select('*');
		$builder->join('leads', 'leads.id = leads_contatos.id_lead', 'left');
		$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'left');
		$builder->where('leadsstatus.id_list', $id);
		return $builder->get()->getResultArray();
	}

	function get_atividades_by_user($id_lead = null, $id_customer = null)
	{
		$builder = $this->db->table('leads_atv');
		$builder->select('*');
		$builder->join('staff', 'staff.id = leads_atv.id_criador', 'left');

		if ($id_lead !== null) {
			$builder->where('id_lead', $id_lead);
		} elseif ($id_customer) {
			$builder->where('id_customer', $id_customer);
		}
		$builder->orderBy('leads_atv.dt_entrada', 'desc');

		$result = $builder->get()->getResultArray();
		$return = [];

		foreach ($result as $row) {
			$data = [];

			if ($row['atividade'] !== null) {
				$activityBuilder = $this->db->table('leads_atv_select');
				$activityBuilder->select('*');
				$activityBuilder->where('id_atv', $row['atividade']);
				$activityResult = $activityBuilder->get()->getResultArray();
				if (!empty($activityResult)) {
					$data = $activityResult[0];
				}
			} elseif ($row['atividade_customer'] !== null) {
				$activityBuilder = $this->db->table('customers_atv_select');
				$activityBuilder->select('*');
				$activityBuilder->where('id_atv', $row['atividade_customer']);
				$activityResult = $activityBuilder->get()->getResultArray();
				if (!empty($activityResult)) {
					$data = $activityResult[0];
				}
			}

			if ($row['is_reuniao'] == "1") {
				$reuniaoBuilder = $this->db->table('leads_reunioes');
				$reuniaoBuilder->where('id_reuniao', $row['id_reuniao']);
				$data['reuniao'] = $reuniaoBuilder->get()->getRow();
			}

			$return[] = array_merge($row, $data);
		}

		return $return;
	}
}
