<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class leadsToDo extends BaseController
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

	public function index()
	{
		$data = [];
		$data['title'] = lang2('Lead To Do');
		$data['back_lead1'] = "back-lead1.png";

		$builder = $this->db->table('branding');
		$builder->where('name', 'back_lead1');
		$r = $builder->get()->getRow();
		if ($r->value != "") {
			$data['back_lead1'] = "images/" . $r->value;
		}

		return view('leadsToDo/index', $data);
	}

	public function get_fluxo($id_lead, $id_fluxo)
	{
		$builder = $this->db->table('fluxos');
		$builder->where('fluxos.id_company', session()->get('id_company'));
		$builder->where('fluxos.id_fluxo', $id_fluxo);
		$fluxo = $builder->get()->getRowArray();

		$builder = $this->db->table('fluxos_etapas');
		$builder->where('id_fluxo', $fluxo['id_fluxo']);
		$builder->where('id_etapa_pai IS NULL', null, false);
		$etapas = $builder->get()->getResultArray();
		$etapasArr = $this->getFluxosEtapas($etapas, $id_lead);

		$fluxo['etapas'] = $etapasArr;

		return response()->setJSON(['fluxo' => $fluxo]);
	}

	public function getFluxosEtapas($etapas, $id_lead)
	{
		foreach ($etapas as $i => $etapa) {
			$etapas[$i]['dias'] = intval($etapa['dias']);
			$builder = $this->db->table('leads_atv_select');
			$builder->where('id_atv', $etapa['id_atividade']);
			$etapas[$i]['atividade'] = $builder->get()->getRowArray();
			$builder = $this->db->table('leads_atv');
			$builder->where('id_etapa_flow', $etapa['id_etapa']);
			$builder->where('id_lead', $id_lead);
			$etapas[$i]['feito'] = $builder->get()->getRowArray() ? 1 : 0;

			$builder = $this->db->table('fluxos_etapas');
			$builder->where('id_etapa_pai', $etapa['id_etapa']);
			$etapasFilho = $builder->get()->getResultArray();

			if (count($etapasFilho) > 0) {
				$etapasArr = $this->getFluxosEtapas($etapasFilho, $id_lead);
				$etapas[$i]['etapas'] = $etapasArr;
			}
		}
		return $etapas;
	}





	public function pausaLead()
	{
		$motivo = "";
		if (request()->getPost('pausa') == '1') {
			$acao = "pausou";
			$motivo = "<br>Motivo: <b>" . request()->getPost('motivo') . "<b>";
		} else {
			$acao = "retomou";
		}

		$r = $this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => session()->staffname . " $acao em Leads To Do. $motivo",
			'staff_id' => session()->usr_id,
		]);

		return response()->setJSON(['r' => $r]);
	}

	public function get_leads_next()
	{
		$prioridades = $this->db->table('settings_prioridades')
			->orderBy('id', 'asc')
			->where('id_company', session()->id_company)
			->get()->getResultArray();

		$lead = [];

		foreach ($prioridades as $prioridade) {
			$prioridades_status = $this->db->table('settings_prioridades_status')
				->orderBy('ordem', 'asc')
				->where('id_prioridade', $prioridade['id'])
				->get()->getResultArray();

			foreach ($prioridades_status as $status) {
				$builder = $this->db->table('leads');
				$builder->select('leads.*, IF(leads.name != "", leads.name, leads.company) as name_lead, leads.id as id_lead');
				$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'inner');
				$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
				$builder->join('staff', 'leads.assigned_id = staff.id', 'inner');

				$builder->where("(leads.lost IS NULL OR leads.lost = '0')");
				$builder->where('staff.id_company', session()->id_company);
				$builder->where('leads.assigned_id', session()->usr_id);
				$builder->where('leads.id != ' . request()->getPost('leadAtual'));

				$funils = explode(',', $prioridade['funil']);
				if (!in_array('-1', $funils)) {
					$builder->whereIn('leadsstatus.id_list', $funils);
				}


				$builder->where('leads.lead_status_id', '1');


				if ($status['id_status'] != '-1') {
					$builder->whereIn('leadsstatus.id', [$status['id_status']]);
				}

				$builder->where('leads.dateconverted IS NULL');

				if (request()->getPost('leadAtual')) {
					$builder->where('leads.id !=', request()->getPost('leadAtual'));
				}

				if (request()->getPost('ignoraLeads')) {
					$builder->whereNotIn('leads.id', request()->getPost('ignoraLeads'));
				}

				if ($prioridade['regras'] == "1") {
					$builder->orderBy('leads.ultimoRetorno', 'asc');
					$builder->where("(leads.ultimoRetorno < '" . date('Y-m-d') . "' OR `leads`.`ultimoRetorno` is null)");
				} else if ($prioridade['regras'] == "2") {
					$builder->orderBy('leads.temperatura', 'asc');
					$builder->orderBy('ISNULL(leads.temperatura)', 'asc');
				} else {
					$builder->orderBy('leads.qualificacao', 'desc');
					$builder->orderBy('ISNULL(leads.qualificacao)', 'desc');
				}

				$builder->limit(1);


				//echo $builder->getCompiledSelect();
				//continue;
				$lead = $builder->get()->getRowArray();


				if ($lead != null) {
					break;
				}
			}
		}


		return response()->setJSON(['lead_next' => $lead]);
	}

	function get_leads()
	{
		$prioridades = $this->db->table('settings_prioridades')
			->orderBy('id', 'asc')
			->where('id_company', session()->id_company)
			->get()->getResultArray();

		$result = [];
		$leads = [];
		$id_leads = [];
		$break = false;

		foreach ($prioridades as $prioridade) {
			$prioridades_status = $this->db->table('settings_prioridades_status')
				->orderBy('ordem', 'asc')
				->where('id_prioridade', $prioridade['id'])
				->get()->getResultArray();

			foreach ($prioridades_status as $status) {
				$builder = $this->db->table('leads');
				$builder->select('leads.*, IF(leads.name != "", leads.name, leads.company) as name_lead, leads.id as id_lead');
				$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'inner');
				$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
				$builder->join('staff', 'leads.assigned_id = staff.id', 'inner');

				$builder->where("(leads.lost IS NULL OR leads.lost = '0')");
				$builder->where('staff.id_company', session()->id_company);
				$builder->where('leads.assigned_id', session()->usr_id);

				$funils = explode(',', $prioridade['funil']);
				if (!in_array('-1', $funils)) {
					$builder->whereIn('leadsstatus.id_list', $funils);
				}


				$builder->where('leads.lead_status_id', '1');

				if (count($id_leads) > 0) {
					$builder->whereNotIn('leads.id', $id_leads);
				}

				if ($status['id_status'] != '-1') {
					$builder->whereIn('leadsstatus.id', [$status['id_status']]);
				}

				$builder->where('leads.dateconverted IS NULL');

				if (request()->getPost('leadAtual')) {
					$builder->where('leads.id !=', request()->getPost('leadAtual'));
				}

				if (request()->getPost('ignoraLeads')) {
					$builder->whereNotIn('leads.id', request()->getPost('ignoraLeads'));
				}

				if ($prioridade['regras'] == "1") {
					$builder->orderBy('leads.ultimoRetorno', 'asc');
					$builder->where("(leads.ultimoRetorno < '" . date('Y-m-d') . "' OR `leads`.`ultimoRetorno` is null)");
				} else if ($prioridade['regras'] == "2") {
					$builder->orderBy('leads.temperatura', 'asc');
					$builder->orderBy('ISNULL(leads.temperatura)', 'asc');
				} else {
					$builder->orderBy('leads.qualificacao', 'desc');
					$builder->orderBy('ISNULL(leads.qualificacao)', 'desc');
				}

				$builder->limit(100);

				//echo $builder->getCompiledSelect();
				//continue;
				$result = $builder->get()->getResultArray();




				foreach ($result as $lead) {
					$id_leads[] = $lead['id_lead'];
				}

				$id_leads = array_unique($id_leads);

				if ($result) {
					foreach ($result as $lead) {
						if (!in_array($lead['id_lead'], array_column($leads, 'id_lead'))) {
							$leads[] = $lead;
						}
					}

					if (count($leads) >= 100) {
						$break = true;
						break;
					}
				}
			}
			if ($break) {
				break;
			}
		}

		return response()->setJSON($leads);
	}


	function diasDatas($data_inicial)
	{
		$data_final = date('Y-m-d');
		$diferenca = strtotime($data_final) - strtotime($data_inicial);
		$dias = floor($diferenca / (60 * 60 * 24));
		return $dias;
	}
}
