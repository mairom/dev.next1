<?php

namespace App\Controllers;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class EmailsAut extends BaseController
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
		$data['title'] = lang2('x_menu_emails_aut');
		return view('email_aut/index', $data);
	}


	function create()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$name_taskAut = request()->getPost('name_taskAut');
			//	$hora_taskAut = request()->getPost('hora_taskAut');

			if (
				$name_taskAut == '' // || $hora_taskAut == ''
			) {
				$data['message'] = lang2('invalidinput');
				$data['success'] = false;
				return response()->setJSON($data);
			} else {
				$params = array(
					'name_taskAut' => $name_taskAut,
					//	'hora_taskAut' => $hora_taskAut,
					'copia_para_taskAut' => request()->getPost('copia_para_taskAut'),
					'respon_email' => request()->getPost('respon_email'),
					'copia_para_oculto_taskAut' => request()->getPost('copia_para_oculto_taskAut'),
					'tipo' => request()->getPost('tipo'),
					'enviar_para' => request()->getPost('enviar_para'),
					"id_company" => session()->id_company,
					'dt_criado' => date('Y-m-d'),
					//'ultima_verificacao' => date('Y-m-d'),
				);

				for ($i = 1; $i <= 15; $i++) {
					if (request()->getPost('template' . $i) != null) {
						$params['template' . $i] = request()->getPost('template' . $i);
						$params['periodo' . $i . '_taskAut'] = request()->getPost('periodo' . $i . '_taskAut');
					}
				}


				if (request()->getPost('id_task') == null) {
					$params['id_staff'] = session()->get('usr_id');

					$this->db->table('email_task')->insert($params);
					$template_id = $this->db->insertID();
				} else {
					$this->db->table('email_task')
						->where('id_task', request()->getPost('id_task'))
						->update($params);

					$template_id = request()->getPost('id_task');
				}


				if ($template_id) {


					$data['message'] =  'Tarefa automática ' . lang2('updatemessage');
					$data['success'] = true;
					return response()->setJSON($data);
				}
			}
		}
	}

	function getTaskAutByUser($id)
	{
		$emails = $this->Emails_Model->getTaskAutByUser($id);
		return response()->setJSON($emails);
	}

	function getTaskAutByUserCustomer($id)
	{
		$emails = $this->Emails_Model->getTaskAutByUserCustomer($id);
		return response()->setJSON($emails);
	}

	function getTaskAut()
	{
		$emails = $this->Emails_Model->getTaskAut();
		return response()->setJSON($emails);
	}

	function getAdicionados($id_lead)
	{
		$emails = $this->Emails_Model->getAdicionados($id_lead);
		return response()->setJSON($emails);
	}

	function getAdicionadosCustomer($id_lead)
	{
		$emails = $this->Emails_Model->getAdicionadosCustomer($id_lead);
		return response()->setJSON($emails);
	}

	function UpdateAdicionadoTask()
	{
		$response = $this->db->table('email_task_leads')
			->where('id_lead', request()->getPost('id_lead'))
			->delete();

		foreach (request()->getPost('adicionadoTask') as $index => $row) {
			if ($row == "true") {
				$params = array(
					'id_task' => $index,
					'id_lead' => request()->getPost('id_lead'),
					'ultima_verificacao' => date('Y-m-d'),
				);
				$this->db->table('email_task_leads')->insert($params);
			}
		}


		$data['message'] =  'Atualizado com sucesso!';
		$data['success'] = true;

		return response()->setJSON($data);
	}

	function UpdateAdicionadoTask2()
	{
		$response = $this->db->table('email_task_leads')
			->whereIn('id_lead', request()->getPost('selectedLeads'))
			->where('id_task', request()->getPost('id_task'))
			->delete();

		if (request()->getPost('action') == "true") {
			foreach (request()->getPost('selectedLeads') as $index => $id_lead) {
				$params = array(
					'id_task' => request()->getPost('id_task'),
					'id_lead' => $id_lead,
					'ultima_verificacao' => date('Y-m-d'),
				);
				$this->db->table('email_task_leads')->insert($params);
			}
		}



		$data['message'] =  'Atualizado com sucesso!';
		$data['success'] = true;

		return response()->setJSON($data);
	}


	function UpdateFunil()
	{
		$response = $this->db->table('leads')
			->whereIn('id', request()->getPost('selectedLeads'))
			->update([
				'funil_list' => request()->getPost('funil_list'),
				'status' => request()->getPost('status_id'),
			]);


		$data['message'] =  'Atualizado com sucesso!';
		$data['success'] = true;

		return response()->setJSON($data);
	}

	function UpdateFunc()
	{
		$response = $this->db->table('leads')
			->whereIn('id', request()->getPost('selectedLeads'))
			->update([
				'assigned_id' => request()->getPost('assigned_id'),
			]);


		$data['message'] =  'Atualizado com sucesso!';
		$data['success'] = true;

		return response()->setJSON($data);
	}

	function UpdateInativar()
	{
		$response = $this->db->table('leads')
			->whereIn('id', request()->getPost('selectedLeads'))
			->update([
				'lead_status_id' => 0,
				'lost' => 1,
				'motivo' => request()->getPost('motivo'),
			]);


		$data['message'] =  'Atualizado com sucesso!';
		$data['success'] = true;

		return response()->setJSON($data);
	}


	function SelectsExcluir()
	{
		$response = $this->db->table('leads')
			->whereIn('id', request()->getPost('selectedLeads'))
			->delete();


		$data['message'] =  'Atualizado com sucesso!';
		$data['success'] = true;

		return response()->setJSON($data);
	}






	function UpdateAdicionadoTaskCustomer()
	{
		//$response = $this->db->table('email_task_customers')->update(array('id_customer' => request()->getPost('id_customer')));
		$response = $this->db->table('email_task_customers')
			->where('id_customer', request()->getPost('id_customer'))
			->delete();

		foreach (request()->getPost('adicionadoTask') as $index => $row) {
			if ($row == "true") {
				$params = array(
					'id_task' => $index,
					'id_customer' => request()->getPost('id_customer'),
					'ultima_verificacao' => date('Y-m-d'),
				);
				$this->db->table('email_task_customers')->insert($params);
			}
		}


		$data['message'] =  'Atualizado com sucesso!';
		$data['success'] = true;

		return response()->setJSON($data);
	}

	function delete($id)
	{
		$response = $this->db->table('email_task')->delete(array('id_task' => $id));
		if ($response) {
			$data['message'] =  'Tarefa automática excluida';
			$data['success'] = true;
		} else {
			$data['message'] =  'Erro! tente novamente.';
			$data['success'] = false;
		}
		return response()->setJSON($data);
	}
}
