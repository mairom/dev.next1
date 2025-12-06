<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Customers extends BaseController
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
		$data['title'] = lang2('customers');
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
		return view('customers/index', $data);
	}

	function addNewReuniao()
	{
		$matriz = [
			'data' => request()->getPost('data'),
			'hora' => request()->getPost('hora'),

			'id_customer' => request()->getPost('id_customer'),

			'id_funcionario' => implode(",", request()->getPost('id_funcionario')),
			'tipo' => request()->getPost('tipo'),
			'realizada' => request()->getPost('realizada'),
			'created_at' => date('Y-m-d H:i:s'),

			'problema' => request()->getPost('problema'),
			'motivo' => request()->getPost('motivo'),
			'observacao' => request()->getPost('observacao'),
			'id_criador' => session()->usr_id,
		];

		$detail = '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
			session()->staffname . '</a>  adicionou nova reunião no cliente <a href="' . base_url() .
			'customers/customer/' . $_POST['id_customer'] . '">' . $_POST['id_customer'] . '</a>.';

		$this->db->table('logs')->insert(array(
			'date' => date('Y-m-d H:i:s'),
			'detail' =>  $detail,
			'staff_id' => session()->usr_id,
		));

		if (request()->getPost('id_reuniao') && !empty(request()->getPost('id_reuniao'))) {
			$response = $this->db->table('leads_reunioes')
				->where('id_reuniao', request()->getPost('id_reuniao'))
				->update($matriz);
		} else {
			$response = $this->db->table('leads_reunioes')->insert($matriz);
			$id_reuniao = $this->db->insertID();
			$closer = "";

			foreach (request()->getPost('id_funcionario') as $user) {
				$closer .= ($closer != "" ? ', ' : '') .	$this->Staff_Model->get_name_staff($user)['staffname'];
			}

			$matrizAtv = [
				'data' => request()->getPost('data'),
				'horario' => request()->getPost('hora'),
				'anotacoes' => 'Reunião agendada: ' . request()->getPost('observacao') . ' <br> ' .
					'<p><b>Data</b> ' . date('d/m/Y', strtotime(request()->getPost('data')))  . ' ' . request()->getPost('hora') . ' </p>' .
					'<p><b>Closer:</b> ' . $closer . ' </p>',
				'reuniao_call' => "1",
				'retorno' => request()->getPost('data'),
				'dt_entrada' => date('Y-m-d H:i:s'),
				'id_criador' => session()->usr_id,
				'is_reuniao' => '1',
				'id_reuniao' => $id_reuniao
			];

			$matrizAtv['id_customer'] = request()->getPost('id_customer');
			$matrizAtv['atividade'] = request()->getPost('tipo') == "Presencial" ? "2" : "3";
			$response = $this->db->table('leads_atv')->insert($matrizAtv);
		}

		return response()->setJSON(["result" => $response]);
	}



	function create()
	{
		if ($this->Privileges_Model->check_privilege('customers', 'create')) {
			if (isset($_POST) && count($_POST) > 0) {
				$company = request()->getPost('company');
				$namesurname = request()->getPost('namesurname');
				$email = request()->getPost('email');
				$default_payment_method = request()->getPost('default_payment_method');
				$group = request()->getPost('groupid');

				if (request()->getPost('type') == 'true') {
					$type = 1;
					$company = '';
				} else {
					$type = 0;
					$namesurname = '';
				}

				$hasError = false;
				$data['message'] = '';

				if ($company == '' && $type == 0) {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('company');
				}
				//else if ($namesurname == '' && $type == 1) {
				//	$hasError = true;
				//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('customer') . ' ' . lang2('name');
				//}
				//else if ($email == '') {
				//	$hasError = true;
				//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('customer') . ' ' . lang2('email');
				//	}
				//else if ($this->Contacts_Model->isDuplicate(request()->getPost('email'))) {
				//	$hasError = true;
				//	$data['message'] = lang2('contact') . ' ' . lang2('email_exist');
				//}
				else if ($group == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('customer') . ' ' . lang2('group');
				}
				if ($hasError) {
					$data['success'] = false;
					return response()->setJSON($data);
				}

				if (!$hasError) {
					$appconfig = get_appconfig();
					$params = array(
						'created' => request()->getPost('created') ? request()->getPost('created') : date('Y-m-d H:i:s'),
						'dt_inactive' => request()->getPost('dt_inactive') && !empty(request()->getPost('dt_inactive')) ? request()->getPost('dt_inactive') : null,
						'type' => $type,
						'company' => $company,
						//'namesurname' => $namesurname,
						'ssn' => request()->getPost('ssn'),
						'executive' => request()->getPost('executive'),
						'address' => request()->getPost('address'),
						'phone' => request()->getPost('phone'),
						'etapa' => request()->getPost('etapa'),
						'email' => request()->getPost('email'),
						'fax' => request()->getPost('fax'),
						'web' => request()->getPost('web'),
						'taxoffice' => request()->getPost('taxoffice'),
						'taxnumber' => request()->getPost('taxnumber'),
						'country_id' => request()->getPost('country_id'),
						'state_id' => request()->getPost('state_id'),
						'closer' => request()->getPost('closer'),
						'customer_sucess' => request()->getPost('customer_sucess'),
						'city' => request()->getPost('city'),
						'town' => request()->getPost('town'),
						'zipcode' => request()->getPost('zipcode'),
						'billing_street' => request()->getPost('billing_street'),
						'billing_city' => request()->getPost('billing_city'),
						'billing_state_id' => request()->getPost('billing_state_id'),
						'billing_zip' => request()->getPost('billing_zip'),
						'billing_country' => request()->getPost('billing_country'),
						'shipping_street' => request()->getPost('shipping_street'),
						'shipping_city' => request()->getPost('shipping_city'),
						'shipping_state_id' => request()->getPost('shipping_state_id'),
						'shipping_zip' => request()->getPost('shipping_zip'),
						'shipping_country' => request()->getPost('shipping_country'),
						'staff_id' => session()->get('usr_id'),
						'default_payment_method' => request()->getPost('default_payment_method'),
						'groupid' => request()->getPost('groupid'),
						'cpf' => request()->getPost('cpf'),
						'cpf' => request()->getPost('cpf'),
						'source_id' => request()->getPost('source_id'),
						'assigned_id' => request()->getPost('assigned_id'),
						'is_whatsApp' => request()->getPost('is_whatsApp'),
						'instagram' => request()->getPost('instagram'),
						'porte' => request()->getPost('porte'),
						'facebook' => request()->getPost('facebook'),
						'linkedin' => request()->getPost('linkedin'),
						'dt_nascimento' => request()->getPost('dt_nascimento'),
						'setor_atividade' => request()->getPost('setor_atividade'),
						'tp_pessoa' => request()->getPost('tp_pessoa'),
						'description' => request()->getPost('description'),
						'cnpj' => request()->getPost('cnpj'),

						"id_company" => session()->id_company
					);
					$customers_id = $this->Customers_Model->add_customers($params);


					$template = $this->Emails_Model->get_template('customer', 'new_customer');

					if ($template['status'] == 1) {

						$admins = $this->Staff_Model->get_all_admins();
						$name = request()->getPost('company');
						$type = lang2('company');


						$message_vars = array(

							'{customer_type}' => $type,

							'{name}' => $name,

							'{customer_email}' => request()->getPost('email'),

						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);



						$param = array(
							'from_name' => $template['from_name'],
							'email' => $admins['email'],
							'subject' => $subject,
							'message' => $message,
							'staff_id' => session()->get('usr_id'),
						);

						if ($param['email']) {

							$this->db->table('email_queue')->insert($param);
						}
					}

					if (request()->getPost('custom_fields')) {

						$custom_fields = array(

							'custom_fields' => request()->getPost('custom_fields')

						);

						$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'customer', $customers_id);
					}

					$data['success'] = true;

					$data['id'] = $customers_id;

					$data['message'] = lang2('customer') . ' ' . lang2('createmessage');

					if ($appconfig['customer_series']) {

						$customer_number = $appconfig['customer_series'];

						$customer_number = $customer_number + 1;

						$this->Settings_Model->increment_series('customer_series', $customer_number);
					}

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function groups()
	{

		if ($this->Privileges_Model->check_privilege('customers', 'all')) {

			$data = $this->Customers_Model->get_groups();
		} else if ($this->Privileges_Model->check_privilege('customers', 'own')) {

			$data = $this->Customers_Model->get_groups(session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('customers'));
		}

		return response()->setJSON($data);
	}



	function add_group()
	{

		if ($this->Privileges_Model->check_privilege('customers', 'create')) {

			if (isset($_POST)) {

				$params = array(

					'name' => request()->getPost('name'),
					"id_company" => session()->id_company

				);

				$this->db->table('customergroups')->insert($params);

				$id = $this->db->insertID();

				if ($id) {

					$data['success'] = true;

					$data['message'] = lang2('customergroup') . ' ' . lang2('createmessage');
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function update_group($id)
	{

		if ($this->Privileges_Model->check_privilege('customers', 'edit')) {
			$data['group'] = $this->Customers_Model->get_group($id);
			if (isset($data['group']['id'])) {
				if (isset($_POST) && count($_POST) > 0) {
					$params = array(
						'name' => request()->getPost('name'),
					);

					$this->Customers_Model->update_group($id, $params);
					$data['success'] = true;
					$data['message'] = lang2('customergroup') . ' ' . lang2('updatemessage');
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function remove_group($id)
	{

		if ($this->Privileges_Model->check_privilege('customers', 'delete')) {

			$group = $this->Customers_Model->get_group($id);

			if (isset($group['id'])) {

				if ($this->Customers_Model->check_group($id) == 0) {

					$this->Customers_Model->remove_group($id);

					$data['success'] = true;

					$data['message'] = lang2('customergroup') . ' ' . lang2('deletemessage');

					return response()->setJSON($data);
				} else {

					$data['success'] = false;

					$data['message'] = $data['message'] = lang2('group') . ' ' . lang2('is_linked') . ' ' . lang2('with') . ' ' . lang2('customer') . ', ' . lang2('so') . ' ' . lang2('cannot_delete') . ' ' . lang2('group');
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function get_customer_groups()
	{

		$groups = $this->Customers_Model->get_customer_groups();

		$data_categories = array();

		foreach ($groups as $group) {

			$data_categories[] = array(

				'name' => $group['name'],

				'id' => $group['id'],

			);
		};

		return response()->setJSON($data_categories);
	}



	function customer($id)
	{
		$data['payment'] = $this->Settings_Model->get_payment_gateway_data();
		if ($this->Privileges_Model->check_privilege('customers', 'all')) {
			$customer = $this->Customers_Model->get_customers_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('customers', 'own')) {
			$customer = $this->Customers_Model->get_customers_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('customers'));
		}

		$data['title'] = lang2('customer') . ' ' . $customer['company'];

		if ($customer) {
			$data['ycr'] = $this->Report_Model->ycr();
			if (isset($customer['id'])) {
				if (isset($_POST) && count($_POST) > 0) {

					if ($this->Privileges_Model->check_privilege('customers', 'edit')) {

						$company = request()->getPost('company');
						$namesurname = request()->getPost('namesurname');
						$email = request()->getPost('email');
						$default_payment_method = request()->getPost('default_payment_method');
						if (request()->getPost('type') == 'true') {
							$type = 1;
							$company = '';
						} else {
							$type = 0;
							$namesurname = '';
						}

						$hasError = false;
						$data['message'] = '';

						if ($company == '' && $type == 0) {
							$hasError = true;
							$data['message'] = lang2('invalidmessage') . ' ' . lang2('company');
						}
						//else if ($email == '') {
						//	$hasError = true;
						//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('customer') . ' ' . lang2('email');
						//}

						if ($hasError) {
							$data['success'] = false;
							return response()->setJSON($data);
						}
						if (request()->getPost('apgFat') == "1") {
							$this->db->table('invoices')
								->where('duedate >=', date('Y-m-d'))
								->delete(['customer_id' => $id]);
						}

						if (!$hasError) {
							$params = array(
								'company' => $company,
								'type' => $type,
								'namesurname' => $namesurname,
								'groupid' => request()->getPost('group_id'),
								'ssn' => request()->getPost('ssn'),
								'executive' => request()->getPost('executive'),
								'address' => request()->getPost('address'),
								'phone' => request()->getPost('phone'),
								'email' => request()->getPost('email'),
								'fax' => request()->getPost('fax'),
								'web' => request()->getPost('web'),
								'taxoffice' => request()->getPost('taxoffice'),
								'taxnumber' => request()->getPost('taxnumber'),
								'country_id' => request()->getPost('country_id'),
								'state_id' => request()->getPost('state_id'),
								'closer' => request()->getPost('closer'),
								'customer_sucess' => request()->getPost('customer_sucess'),
								'city' => request()->getPost('city'),
								'town' => request()->getPost('town'),
								'zipcode' => request()->getPost('zipcode'),
								'billing_street' => request()->getPost('billing_street'),
								'billing_city' => request()->getPost('billing_city'),
								'billing_state_id' => request()->getPost('billing_state_id'),
								'billing_zip' => request()->getPost('billing_zip'),
								'billing_country' => request()->getPost('billing_country'),
								'shipping_street' => request()->getPost('shipping_street'),
								'shipping_city' => request()->getPost('shipping_city'),
								'shipping_state_id' => request()->getPost('shipping_state_id'),
								'shipping_zip' => request()->getPost('shipping_zip'),
								'shipping_country' => request()->getPost('shipping_country'),
								'staff_id' => session()->get('usr_id'),
								'risk' => request()->getPost('risk'),
								'customer_status_id' => request()->getPost('status_id'),
								'default_payment_method' => request()->getPost('default_payment_method'),
								'cpf' => request()->getPost('cpf'),
								'tp_pessoa' => request()->getPost('tp_pessoa'),
								'cnpj' => request()->getPost('cnpj'),
								'setor_atividade' => request()->getPost('setor_atividade'),
								'source_id' => request()->getPost('source_id'),
								'assigned_id' => request()->getPost('assigned_id'),
								'description' => request()->getPost('description'),
								'etapa' => request()->getPost('etapa'),
								'dt_inactive' => request()->getPost('dt_inactive') && !empty(request()->getPost('dt_inactive')) ? request()->getPost('dt_inactive') : null,
							);

							if (request()->getPost('created')) {
								$params['created'] =  request()->getPost('created');
							}

							if ($customer['id'] != request()->getPost('status_id')) {
								if (request()->getPost('status_id') == '0' && !request()->getPost('dt_inactive')) {
									$params['dt_inactive'] = date("Y-m-d H:i:s");
								}
							}

							$this->Customers_Model->update_customers($id, $params);
							if (request()->getPost('custom_fields')) {
								$custom_fields = array(
									'custom_fields' => request()->getPost('custom_fields')
								);
								$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'customer', $id);
							}

							if (request()->getPost('status_id') &&  request()->getPost('status_id') == "1") {
								$invoices = $this->Invoices_Model->get_all_invoices_by_customer($id);

								foreach ($invoices as $row) {
									if ($row['recurring'] == "1") {
										$recurrings = $this->db->table('recurring')
											->where('relation_type', 'invoice')
											->where('relation', $row['id'])
											->get()
											->getResultArray();

										foreach ($recurrings as $recurring) {
											for ($i = 1; $i <= $recurring['period']; $i++) {
												if ($recurring['type'] == "0") {
													$data1 = date('Y-m', strtotime("+" . $i . " days", strtotime($row['duedate'])));
												} else if ($recurring['type'] == "1") {
													$data1 = date('Y-m', strtotime("+" . $i . " week", strtotime($row['duedate'])));
												} else if ($recurring['type'] == "2") {
													$data1 = date('Y-m', strtotime("+" . $i . " month", strtotime($row['duedate'])));
												} else if ($recurring['type'] == "3") {
													$data1 = date('Y-m', strtotime("+" . $i . " year", strtotime($row['duedate'])));
												}

												if (strtotime($data1) > strtotime(date('Y-m'))) {
													$this->Invoices_Model->alteraDataRecurring($recurring['id'], $i);
													break;
												}
											}
										}
									}
								}
							}

							$data['success'] = true;
							$data['message'] = lang2('customer') . ' ' . lang2('updatemessage');

							return response()->setJSON($data);
						}
					} else {

						$data['success'] = false;

						$data['message'] = lang2('you_dont_have_permission');

						return response()->setJSON($data);
					}
				} else {

					$data['customers'] = $this->Customers_Model->get_customers($id);
					$data['id_user'] = session()->get('usr_id');

					return view('customers/customer', $data);
				}
			} else {

				show_error('Eror');
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('customers'));
		}
	}



	function get_group_id($name)
	{

		$rows = $this->db->table('customergroups')
			->get()
			->getResultArray();

		$group_id = 0;
		foreach ($rows as $row) {

			if ($row['name'] = $name) {

				$group_id = $row['id'];
			}
		}

		return $group_id ? $group_id : '';
	}

	public function customersimport()
	{
		// Verifica se o usuário tem permissão para criar clientes
		if ($this->Privileges_Model->check_privilege('customers', 'create')) {

			$data['customers'] = $this->Customers_Model->get_customers_for_import();
			$data['error'] = '';

			// Caminho para upload de arquivos
			$uploadPath = ROOTPATH . 'public/uploads/imports/';
			$file = request()->getFile('file');

			// Limpeza do nome do arquivo
			$filename = preg_replace("/[^a-z0-9_\-.]/i", '', $file->getName());

			// Verifica se o arquivo foi movido corretamente para o diretório de uploads
			if (!$file->move($uploadPath, $filename)) {
				$data['error'] = $file->getErrorString();
				session()->setFlashdata('ntf1', lang2('csvimporterror'));
			} else {
				$file_path = $uploadPath . $filename;

				// Abrir e processar o arquivo CSV sem plugin
				if (($handle = fopen($file_path, 'r')) !== false) {
					$appconfig = get_appconfig();
					$csv_array = [];
					$csv_errors = [];
					$num = 1;

					// Ler cabeçalhos do CSV
					$header = fgetcsv($handle, 1000, ';');



					// Processar linhas do CSV
					while (($row = fgetcsv($handle, 1000, ';')) !== false) {
						$csv_data = array_combine($header, $row);
						$customer_success = null;
						$sdr = null;
						$closer = null;

						// Verifica "Customer sucess"
						if (isset($csv_data['Customer sucess']) && !empty($csv_data['Customer sucess'])) {
							$staff = $this->Staff_Model->get_staffSearch($csv_data['Customer sucess']);
							if ($staff) {
								$customer_success = $staff[0]->id;
							} 
						}

						// Verifica "SDR"
						if (isset($csv_data['SDR']) && !empty($csv_data['SDR'])) {
							$staff = $this->Staff_Model->get_staffSearch($csv_data['SDR']);
							if ($staff) {
								$sdr = $staff[0]->id;
							} 
						}

						// Verifica "Closer"
						if (isset($csv_data['Closer']) && !empty($csv_data['Closer'])) {
							$staff = $this->Staff_Model->get_staffSearch($csv_data['Closer']);
							if ($staff) {
								$closer = $staff[0]->id;
							} 
						}

						// Verifica o grupo do cliente
						$group_id = 0;
						if (isset($csv_data['Cliente Grupo'])) {
							$group_name = $csv_data['Cliente Grupo'];
							$group_id = $this->get_group_id($group_name);
						}


						if (($csv_data['email'] == '') || ($csv_data['Tipo de pessoa'] == '')) {
							$num++;
							$csv_errors[] = ['line' => $num . ' Campo: email ou Tipo de pessoa'];
							continue;
						}

						// Formata e insere os dados do cliente
						$tp_pessoa = $csv_data['Tipo de pessoa'] ?? null;
						$insert_data = [
							'tp_pessoa' => $tp_pessoa == "J" ? "2" : "1",
							'created' => date('Y-m-d H:i:s'),
							'type' => $csv_data['Tipo de pessoa'],
							'namesurname' => $csv_data['name (PF)'] ?? null,
							'cpf' => $csv_data['cpf (PF)'] ?? null,
							'dt_nascimento' => isset($csv_data['dt_nascimento (PF)']) ? implode("-", array_reverse(explode("/", $csv_data['dt_nascimento (PF)']))) : null,
							'company' => $csv_data['Razao Social (PJ)'] ?? null,
							'nome_fantasia' => $csv_data['Nome Fantasia (PJ)'] ?? null,
							'cnpj' => $csv_data['cnpj (PJ)'] ?? null,
							'Inscricao_estadual' => $csv_data['Inscricao Estadual (PJ)'] ?? null,
							'setor_atividade' => $csv_data['setor_atividade (PJ)'] ?? null,
							'porte' => $csv_data['Porte'] ?? null,
							'web_site' => $csv_data['web_site'] ?? null,
							'data_ultima_compra' => $csv_data['Data ultima Compra'] ?? null,
							'valor_ultima_compra' => $csv_data['Valor ultima Compra'] ?? null,
							'total_de_compras' => $csv_data['Total de Compras'] ?? null,
							'SDR' => $sdr,
							'closer' => $closer,
							'customer_sucess' => $customer_success,
							'instagram' => $csv_data['instagram'] ?? null,
							'facebook' => $csv_data['facebook'] ?? null,
							'linkedin' => $csv_data['linkedin'] ?? null,
							'zipcode' => $csv_data['zip'] ?? null,
							'city' => $csv_data['city'] ?? null,
							'state' => $csv_data['state'] ?? null,
							'address' => $csv_data['address'] ?? null,
							'email' => $csv_data['email'] ?? null,
							'phone' => $csv_data['phone'] ?? null,
							'description' => $csv_data['description'] ?? null,
							'customer_status_id' => '1',
							'staff_id' => session()->get('usr_id'),
							'groupid' => $group_id,
							'id_company' => session()->get('id_company')
						];
						$num++;

						// Insere os dados no banco
						$id = $this->Customers_Model->insert_customers_csv($insert_data);

						// Processar contatos associados
						$this->insert_contacts($csv_data, $id, 1);
						$this->insert_contacts($csv_data, $id, 2);

						// Atualiza a série de clientes, se aplicável
						if ($appconfig['customer_series']) {
							$customer_number = $appconfig['customer_series'] + 1;
							$this->Settings_Model->increment_series('customer_series', $customer_number);
						}
					}
					fclose($handle); // Fecha o arquivo CSV

					// Resposta de sucesso
					$datas['success'] = true;
					$datas['message'] = lang2('file') . ' ' . lang2('csvimportsuccess') . ' ' . (count($csv_errors) > 0 ? lang2('butErrors') : '');
					if (count($csv_errors) > 0) {
						$datas['errors'] = $csv_errors;
					}

					return response()->setJSON($datas);
				} else {
					// Erro ao abrir o arquivo
					$datas['success'] = false;
					$datas['message'] = lang2('errormessage');
					return response()->setJSON($datas);
				}
			}
		} else {
			// Resposta de erro para falta de permissão
			$datas['success'] = false;
			$datas['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($datas);
		}
	}


	private function insert_contacts($csv_data, $id, $contact_number)
	{
		if (isset($csv_data['nm_contato_0' . $contact_number]) && !empty($csv_data['nm_contato_0' . $contact_number])) {
			$matriz = [
				'nm_contato' => $csv_data['nm_contato_0' . $contact_number],
				'dt_criado' => date('Y-m-d'),
				'cargo' => $csv_data['cargo_0' . $contact_number] ?? null,
				'email' => $csv_data['email_0' . $contact_number] ?? null,
				'id_lead' => $id,
				'telefone' => $csv_data['telefone_0' . $contact_number] ?? null,
				'linkedin' => $csv_data['linkedin_0' . $contact_number] ?? null,
				'observacao' => $csv_data['observacao_0' . $contact_number] ?? null,
				'dt_aniversario' => isset($csv_data['dt_aniversario_0' . $contact_number]) ? implode("-", array_reverse(explode("/", $csv_data['dt_aniversario_0' . $contact_number]))) : null,
				'id_criador' => session()->usr_id,
				'ctt_principal' =>   isset($csv_data['ctt_principal_0' . $contact_number]) ? $csv_data['ctt_principal_0' . $contact_number] : null,
			];
			$response = $this->db->table('leads_contatos')->insert($matriz);
		}
	}


	function customersimport2()
	{

		if ($this->Privileges_Model->check_privilege('customers', 'create')) {

			$data['customers'] = $this->Customers_Model->get_customers_for_import();
			$data['error'] = '';

			$uploadPath = ROOTPATH . 'public/uploads/imports/';
			$file = request()->getFile('file');
			$filename = preg_replace("/[^a-z0-9_\-.]/i", '', $file->getName());

			if (!$file->move($uploadPath, $filename)) {
				$data['error'] = $this->upload->display_errors();
				session()->setFlashdata('ntf1', lang2('csvimporterror'));
				//redirect( 'customers/index' );

			} else {
				$file_path = './uploads/imports/' . $filename;

				if ($this->import->get_array($file_path)) {
					$appconfig = get_appconfig();
					$csv_array = $this->import->get_array($file_path);
					$num = 1;
					$csv_errors = array();

					foreach ($csv_array as $row) {
						$customer_sucess = null;
						$sdr = null;
						$closer = null;

						if (isset($row['Customer sucess']) && !empty($row['Customer sucess'])) {
							$staff = $this->Staff_Model->get_staffSearch($row['Customer sucess']);
							if ($staff) {
								$customer_sucess =  $staff[0]->id;
							} else {
								$csv_errors[] = array(
									'line' => $num . ' Campo: Customer sucess',
								);
							}
						}

						if (isset($row['SDR']) && !empty($row['SDR'])) {
							$staff = $this->Staff_Model->get_staffSearch($row['SDR']);
							if ($staff) {
								$sdr =  $staff[0]->id;
							} else {
								$csv_errors[] = array(
									'line' => $num . ' Campo: SDR',
								);
							}
						}

						if (isset($row['Closer']) && !empty($row['Closer'])) {
							$staff = $this->Staff_Model->get_staffSearch($row['Closer']);
							if ($staff) {
								$closer =  $staff[0]->id;
							} else {
								$csv_errors[] = array(
									'line' => $num . ' Campo: Closer',
								);
							}
						}


						$group_id = 0;
						if (isset($row['Cliente Grupo'])) {
							$group_name = $row['Cliente Grupo'];
							$group_id = $this->get_group_id($group_name);
						}


						if (($row['email'] == '') || ($row['Tipo de pessoa'] == '')) {
							$num++;
							$csv_errors[] = array(
								'line' => $num,
							);
							continue;
						} else {

							$tp_pessoa = isset($row['Tipo de pessoa']) ? $row['Tipo de pessoa'] : (isset($row['﻿Tipo de pessoa']) ? $row['﻿Tipo de pessoa'] : null);

							$insert_data = array(
								'tp_pessoa' =>  $tp_pessoa  == "J" ? "2" : "1",
								'created' => date('Y-m-d H:i:s'),
								'type' => $row['Tipo de pessoa'],
								'namesurname' => isset($row['name (PF)']) ? $row['name (PF)'] : null,
								'cpf' => isset($row['cpf (PF)']) ? $row['cpf (PF)'] : null,
								'dt_nascimento' => isset($row['dt_nascimento (PF)']) ? $data = implode("-", array_reverse(explode("/", $row['dt_nascimento (PF)']))) : null,
								'company' => isset($row['Razao Social (PJ)']) ? $row['Razao Social (PJ)'] : null,
								'nome_fantasia' => isset($row['Nome Fantasia (PJ)']) ? $row['Nome Fantasia (PJ)'] : null,
								'cnpj' => isset($row['cnpj (PJ)']) ? $row['cnpj (PJ)'] : null,
								'Inscricao_estadual' => isset($row['Inscricao Estadual (PJ)']) ? $row['Inscricao Estadual (PJ)'] : null,
								'setor_atividade' => isset($row['setor_atividade (PJ)']) ? $row['setor_atividade (PJ)'] : null,
								'porte' => isset($row['Porte']) ? $row['Porte'] : null,

								'web_site' => isset($row['web_site']) ? $row['web_site'] : null,
								'data_ultima_compra' => isset($row['Data ultima Compra']) ? $row['Data ultima Compra'] : null,

								'valor_ultima_compra' => isset($row['Valor ultima Compra']) ? $row['Valor ultima Compra'] : null,
								'total_de_compras' => isset($row['Total de Compras']) ? $row['Total de Compras'] : null,
								'SDR' => $sdr,

								'closer' => $closer,
								'customer_sucess' => $customer_sucess,

								'instagram' => isset($row['instagram']) ? $row['instagram'] : null,
								'facebook' => isset($row['facebook']) ? $row['facebook'] : null,
								'linkedin' => isset($row['linkedin']) ? $row['linkedin'] : null,
								'zipcode' => isset($row['zip']) ? $row['zip'] : null,
								'city' => isset($row['city']) ? $row['city'] : null,
								'state' => isset($row['state']) ? $row['state'] : null,
								'address' => isset($row['address']) ? $row['address'] : null,
								//'Bairro' => $row['Bairro'],

								'email' => isset($row['email']) ? $row['email'] : null,
								'phone' => isset($row['phone']) ? $row['phone'] : null,
								'description' => isset($row['description']) ? $row['description'] : null,

								'customer_status_id' => '1',
								'staff_id' => session()->get('usr_id'),
								'groupid' => $group_id,
								"id_company" => session()->id_company
							);
							$num++;
							$id = $this->Customers_Model->insert_customers_csv($insert_data);


							if (isset($row['nm_contato_01']) && !empty($row['nm_contato_01'])) {
								$matriz = [
									'nm_contato' => $row['nm_contato_01'],
									'dt_criado' => date('Y-m-d'),
									'cargo' =>  isset($row['cargo_01']) ? $row['cargo_01'] : null,
									'email' =>  isset($row['email_01']) ? $row['email_01'] : null,
									'id_lead' => $id,
									'telefone' =>  isset($row['telefone_01']) ? $row['telefone_01'] : null,
									'linkedin' =>  isset($row['linkedin_01']) ? $row['linkedin_01'] : null,
									'observacao' =>  isset($row['observacao_01']) ? $row['observacao_01'] : null,
									'dt_aniversario' => isset($row['dt_aniversario_01']) ? $data = implode("-", array_reverse(explode("/", $row['dt_aniversario_01']))) : null,
									'id_criador' => session()->usr_id,
									'ctt_principal' =>   isset($row['ctt_principal_01']) ? $row['ctt_principal_01'] : null,
								];
								$response = $this->db->table('leads_contatos')->insert($matriz);
							}

							if (isset($row['nm_contato_02']) && !empty($row['nm_contato_02'])) {
								$matriz = [
									'nm_contato' => $row['nm_contato_02'],
									'dt_criado' => date('Y-m-d'),
									'cargo' =>  isset($row['cargo_02']) ? $row['cargo_02'] : null,
									'email' =>  isset($row['email_02']) ? $row['email_02'] : null,
									'id_lead' => $id,
									'telefone' =>  isset($row['telefone_02']) ? $row['telefone_02'] : null,
									'linkedin' =>  isset($row['linkedin_02']) ? $row['linkedin_02'] : null,
									'observacao' =>  isset($row['observação_02']) ? $row['observação_02'] : null,
									'dt_aniversario' => isset($row['dt_aniversario_02']) ? $data = implode("-", array_reverse(explode("/", $row['dt_aniversario_02']))) : null,
									'id_criador' => session()->usr_id,
									'ctt_principal' =>   isset($row['ctt_principal_02']) ? $row['ctt_principal_02'] : null,
								];
								$response = $this->db->table('leads_contatos')->insert($matriz);
							}

							if ($appconfig['customer_series']) {
								$customer_number = $appconfig['customer_series'];
								$customer_number = $customer_number + 1;
								$this->Settings_Model->increment_series('customer_series', $customer_number);
							}
						}
					}

					$datas['success'] = true;
					$datas['message'] = lang2('file') . ' ' . lang2('csvimportsuccess') . ' ' . (count($csv_errors) > 0 ? lang2('butErrors') : '');
					if (count($csv_errors) > 0) {
						$datas['errors'] = $csv_errors;
					}


					return response()->setJSON($datas);
				} else {
					$datas['success'] = false;
					$datas['message'] = lang2('errormessage');
					return response()->setJSON($datas);
				}
			}
		} else {

			$datas['success'] = false;

			$datas['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($datas);
		}
	}



	function exportdata()
	{
		$this->load->dbutil();
		$this->load->helper('file');
		$this->load->helper('download');

		if ($this->Privileges_Model->check_privilege('customers', 'all')) {
			$q = $this->db->table('customers')
				->select('type, created, company, namesurname, taxoffice, taxnumber, ssn, executive, address, zipcode, country_id, state, city, town, phone, fax, email, web, customer_status_id, risk, customergroups.name as group, customergroups.id as group_id')
				->join('customergroups', 'customers.groupid = customergroups.id', 'left')
				->where('customers.id_company', session()->get('id_company'))
				->orderBy('customers.id', 'desc')
				->get();
		} else if ($this->Privileges_Model->check_privilege('customers', 'own')) {
			$q = $this->db->table('customers')
				->select('type, created, company, namesurname, taxoffice, taxnumber, ssn, executive, address, zipcode, country_id, state, city, town, phone, fax, email, web, customer_status_id, risk, customergroups.name as group, customergroups.id as group_id')
				->join('customergroups', 'customers.groupid = customergroups.id', 'left')
				->where('staff_id', session()->get('usr_id'))
				->where('customers.id_company', session()->get('id_company'))
				->orderBy('customers.id', 'desc')
				->get();
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('customers'));
		}


		$delimiter = ";";
		$nuline    = "\r\n";
		force_download('Customers.csv', "\xEF\xBB\xBF" . $this->dbutil->csv_from_result($q, $delimiter, $nuline));
	}



	function addreminder()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$params = array(

				'description' => request()->getPost('description'),

				'relation' => request()->getPost('relation'),

				'relation_type' => 'customer',

				'staff_id' => request()->getPost('staff'),

				'addedfrom' => session()->get('usr_id'),

				'date' => request()->getPost('date'),

			);

			$notes = $this->Trivia_Model->add_reminder($params);

			session()->setFlashdata('ntf1', '' . lang2('reminderadded') . '');

			return redirect()->to('customers/customer/' . request()->getPost('relation') . '');
		} else {

			return redirect()->to('leads/index');
		}
	}



	function remove($id)
	{
		if ($this->Privileges_Model->check_privilege('customers', 'all')) {
			$customer = $this->Customers_Model->get_customers_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('customers', 'own')) {
			$customer = $this->Customers_Model->get_customers_by_privileges($id, session()->usr_id);
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}

		if ($customer) {
			if ($this->Privileges_Model->check_privilege('customers', 'delete')) {
				if (isset($customer['id'])) {
					$result = $this->Customers_Model->delete_customers($id, get_number('customers', $id, 'customer', 'customer'));
					$customer = lang2('customer');
					if ($result) {
						$data['message'] = sprintf(lang2('success_delete'), $customer . '');
						$data['success'] = true;
						return response()->setJSON($data);
					} else {
						$data['message'] = sprintf(lang2('cant_delete'), $customer . '');
						$data['success'] = false;
						return response()->setJSON($data);
					}
				} else {
					show_error('Customer not deleted');
				}
			} else {
				$data['success'] = false;
				$data['message'] = lang2('you_dont_have_permission');
				return response()->setJSON($data);
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('customers'));
		}
	}



	function customers_json()
	{
		$customers = $this->Customers_Model->get_all_customers();
		header('Content-Type: application/json');
		return response()->setJSON($customers);
	}



	function customers_arama_json()
	{

		$veriler = $this->Customers_Model->search_json_customer();

		return response()->setJSON($veriler);
	}



	function create_contact()
	{

		if ($this->Privileges_Model->check_privilege('customers', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {
				$hasError = false;
				$data['message'] = '';

				if (request()->getPost('name') == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
				} else if (request()->getPost('email') == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('email');
				}



				if ($hasError) {
					$data['success'] = false;
					return response()->setJSON($data);
				}

				if (!$hasError) {
					if ($this->Contacts_Model->isDuplicate(request()->getPost('email'))) {
						$data['success'] = false;
						$data['message'] = lang2('contact') . ' ' . lang2('email_exist');
						return response()->setJSON($data);
					} else {

						//	if ((request()->getPost('password') == '') && (request()->getPost('isPrimary') == 'true')) {
						//		$hasError = true;
						//		$data['message'] = lang2('invalidmessage') . ' ' . lang2('password');
						//	}

						if ($hasError) {
							$data['success'] = false;
							return response()->setJSON($data);
						} else {
							$primary = 0;

							switch (request()->getPost('isPrimary')) {
								case 'true':
									$primary = 1;
									break;

								case 'false':
									$primary = 0;
									break;
							}

							switch (request()->getPost('isAdmin')) {
								case true:
									$isAdmin = 1;
									break;

								case false:
									$isAdmin = 0;
									break;
							}

							$params = array(
								'name' => request()->getPost('name'),
								'surname' => request()->getPost('surname'),
								'phone' => request()->getPost('phone'),
								'extension' => request()->getPost('extension'),
								'mobile' => request()->getPost('mobile'),
								'email' => request()->getPost('email'),
								'address' => request()->getPost('address'),
								'skype' => request()->getPost('skype'),
								'linkedin' => request()->getPost('linkedin'),
								'facebook' => request()->getPost('facebook'),
								'dt_nascimento' => request()->getPost('dt_nascimento'),
								'customer_id' => request()->getPost('customer'),
								'position' => request()->getPost('position'),
								'instagram' => request()->getPost('instagram'),
								'primary' => $primary,
								'admin' => $isAdmin,
							);

							$contacts_id = $this->Contacts_Model->create($params);

							if ($contacts_id) {
								$template = $this->Emails_Model->get_template('customer', 'new_contact_added');

								if ($template['status'] == 1 && $primary == 1) {

									/*
									$message_vars = array(
										'{login_email}' => request()->getPost('email'),
										'{login_password}' => (request()->getPost('password')) ? (request()->getPost('password')) : ' ',
										'{app_url}' => '' . base_url('area/login') . '',
										'{email_signature}' => session()->get('email'),
										'{name}' => session()->get('staffname'),
										'{customer}' => request()->getPost('name')
									);
									$subject = strtr($template['subject'], $message_vars);
									$message = strtr($template['message'], $message_vars);

									$param = array(
										'from_name' => $template['from_name'],
										'email' => request()->getPost('email'),
										'subject' => $subject,
										'message' => $message,
										'created' => date("Y.m.d H:i:s"),
									);

									if (request()->getPost('email')) {
										$this->db->table('email_queue')->insert( $param);
									}
									*/
								}

								$data['success'] = true;
								$data['message'] = lang2('contact') . ' ' . lang2('createmessage');
								return response()->setJSON($data);
							} else {

								$data['success'] = false;

								$data['message'] = lang2('errormessage');

								return response()->setJSON($data);
							}
						}
					}
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}

	function get_list_atividades($id)
	{
		$data = $this->Leads_Model->get_atividades_by_user(null, $id);
		return response()->setJSON($data);
	}



	function contact()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$hasError = false;
			$data['message'] = '';
			if (request()->getPost('name') == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
			} else if (request()->getPost('email') == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('email');
			}

			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}

			if (!$hasError) {
				if ($this->Contacts_Model->isDuplicate(request()->getPost('email'))) {
					$data['success'] = false;
					$data['message'] = lang2('contact') . ' ' . lang2('email_exist');
					return response()->setJSON($data);
				} else {

					if ($hasError) {
						$data['success'] = false;
						return response()->setJSON($data);
					} else {
						$data['success'] = true;
						$data['message'] = lang2('contact') . ' ' . lang2('createmessage');
						return response()->setJSON($data);
					}
				}
			}
		}
	}



	function update_contact($id)
	{

		if ($this->Privileges_Model->check_privilege('customers', 'edit')) {

			$contacts = $this->Contacts_Model->get_contacts($id);

			if (isset($contacts['id'])) {

				if (isset($_POST) && count($_POST) > 0) {

					$hasError = false;

					$data['message'] = '';

					if (request()->getPost('name') == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('name');
					} else if (request()->getPost('email') == '') {

						$hasError = true;

						$data['message'] = lang2('invalidmessage') . ' ' . lang2('email');
					}

					if ($hasError) {

						$data['success'] = false;

						return response()->setJSON($data);
					}

					if (!$hasError) {

						if ($this->Contacts_Model->isDuplicate(request()->getPost('email')) &&  (request()->getPost('email') != $contacts['email'])) {

							$data['success'] = false;

							$data['message'] = lang2('contact') . ' ' . lang2('email_exist');

							return response()->setJSON($data);
						} else {

							$params = array(

								'name' => request()->getPost('name'),

								'surname' => request()->getPost('surname'),

								'phone' => request()->getPost('phone'),

								'extension' => request()->getPost('extension'),

								'mobile' => request()->getPost('mobile'),

								'email' => request()->getPost('email'),

								'address' => request()->getPost('address'),

								'skype' => request()->getPost('skype'),
								'linkedin' => request()->getPost('linkedin'),
								'instagram' => request()->getPost('instagram'),

								'position' => request()->getPost('position'),
								'facebook' => request()->getPost('facebook'),
								'dt_nascimento' => request()->getPost('dt_nascimento'),
							);

							$this->Contacts_Model->update2($id, $params);

							$data['success'] = true;

							$data['message'] = lang2('contact') . ' ' . lang2('updatemessage');

							return response()->setJSON($data);
						}
					}
				}
			} else {

				show_error('The contacts you are trying to edit does not exist.');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function update_contact_privilege($id, $value, $privilege_id)
	{

		if ($this->Privileges_Model->check_privilege('customers', 'edit')) {
			if ($value != 'false') {
				$params = array(
					'relation' => (int)$id,
					'relation_type' => 'contact',
					'permission_id' => (int)$privilege_id
				);

				$this->db->table('privileges')->insert($params);
			} else {
				$response = $this->db->table('privileges')->update(array('relation' => $id, 'relation_type' => 'contact', 'permission_id' => $privilege_id));
			}

			$data['success'] = true;
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function change_password_contact($id)
	{

		if ($this->Privileges_Model->check_privilege('customers', 'edit')) {

			$contact = $this->Contacts_Model->get_contacts($id);

			if (isset($contact['id'])) {

				if (isset($_POST) && count($_POST) > 0) {

					$params = array(

						'password' => password_hash(request()->getPost('password'), PASSWORD_BCRYPT),

					);

					$customer = $contact['customer_id'];

					$staffname = session()->staffname;

					$contactname = $contact['name'];

					$contactsurname = $contact['surname'];

					$loggedinuserid = session()->usr_id;

					$this->db->table('logs')->insert(array(

						'date' => date('Y-m-d H:i:s'),

						'detail' => ('' . $message = sprintf(lang2('changedpassword'), $staffname, $contactname, $contactsurname) . ''),

						'staff_id' => $loggedinuserid,

						'customer_id' => $customer,

					));

					$this->Contacts_Model->update2($id, $params);



					// send email to contact about password change

					$template = $this->Emails_Model->get_template('staff', 'customer_password_reset');

					$message_vars = array(

						'{email}' => $contact['email'],

						'{contact}' => $contact['name'] . ' ' . $contact['surname'],

						'{email_signature}' => $template['from_name'],

					);

					$subject = strtr($template['subject'], $message_vars);

					$message = strtr($template['message'], $message_vars);



					$param = array(
						'from_name' => $template['from_name'],
						'email' => $contact['email'],
						'subject' => $subject,
						'message' => $message,
						'created' => date("Y.m.d H:i:s"),
						'staff_id' => session()->get('usr_id'),
						'status' => 1
					);

					if ($contact['email']) {

						$this->db->table('email_queue')->insert($param);
					}

					$data['success'] = true;

					$data['message'] = ' ' . $contact['name'] . ' ' . lang2('passwordchanged') . '';

					return response()->setJSON($data);
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function remove_contact($id)
	{

		if ($this->Privileges_Model->check_privilege('customers', 'edit')) {

			$contacts = $this->Contacts_Model->get_contacts($id);

			if (isset($contacts['id'])) {

				$this->Contacts_Model->delete2($id);

				$data['success'] = true;

				$data['message'] = lang2('contactdeleted');

				return response()->setJSON($data);
			} else {

				show_error('The contacts you are trying to delete does not exist.');
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}

	function getListSelectCustomers()
	{
		$data = $this->Leads_Model->getListSelectCustomers();
		return response()->setJSON($data);
	}

	function get_customer($id, $year  = null)
	{
		if ($year == null) {
			$year = date('Y');
		}

		$customer = array();
		if ($this->Privileges_Model->check_privilege('customers', 'all')) {
			$customer = $this->Customers_Model->get_customers_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('customers', 'own')) {
			$customer = $this->Customers_Model->get_customers_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('customers'));
		}

		if ($customer) {
			$country = get_country($customer['country_id']);
			$state = get_state_name($customer['state'], $customer['state_id']);
			$contacts = $this->Contacts_Model->get_customer_contacts($id);
			$subsidiaries = $this->Customers_Model->get_subsidiaries($id);
			$netrevenue = $this->db->table('invoices')
				->selectSum('total')
				->where('(status_id = 2 AND customer_id = ' . $customer['id'] . ')')->get()->getRow();


			$builder = $this->db->table('invoices')
				->selectSum('total')
				->where('(status_id != 1 AND customer_id = ' . $customer['id'] . ')');

			$grossrevenue = $builder->get();

			$data_customerdetail = array(
				'id' => $customer['id'],
				'type' => $customer['type'],
				'isIndividual' => ($customer['type'] == '1') ? true : false,
				'created' => $customer['created'],
				'funil_origem' => $customer['nm_funil_origem'],
				'staff_id' => $customer['staff_id'],
				'company' => $customer['company'],
				'namesurname' => $customer['namesurname'],
				'taxoffice' => $customer['taxoffice'],
				'taxnumber' => $customer['taxnumber'],
				'ssn' => $customer['ssn'],
				'closer' => $customer['closer'],
				'customer_sucess' => $customer['customer_sucess'],
				'executive' => $customer['executive'],
				'ultimoRetorno' => $customer['ultimoRetorno'],
				'address' => $customer['address'],
				'etapa' => $customer['etapa'],
				'zipcode' => $customer['zipcode'],
				'country_id' => $customer['country_id'],
				'state' => $state,
				'state_id' => $customer['state_id'],
				'city' => $customer['city'],
				'town' => $customer['town'],
				'default_payment_method' => $customer['default_payment_method'],
				'billing_street' => $customer['billing_street'],
				'billing_city' => $customer['billing_city'],
				'billing_state' => $customer['billing_state'],
				'billing_state_id' => $customer['billing_state_id'],
				'billing_zip' => $customer['billing_zip'],
				'billing_country' => $customer['billing_country'],
				'country' => $country,
				'shipping_street' => $customer['shipping_street'],
				'shipping_city' => $customer['shipping_city'],
				'shipping_state' => $customer['shipping_state'],
				'shipping_state_id' => $customer['shipping_state_id'],
				'shipping_zip' => $customer['shipping_zip'],
				'shipping_country' => $customer['shipping_country'],
				'phone' => $customer['phone'],
				'fax' => $customer['fax'],
				'email' => $customer['email'],
				'web' => $customer['web'],
				'risk' => intval($customer['risk']),
				'netrevenue' => $netrevenue->total,
				'grossrevenue' => $grossrevenue->getRow()->total,
				'subsidiaries' => $subsidiaries,
				'contacts' => $contacts,
				'chart_data' => $this->Report_Model->customer_annual_sales_chart($id, $year),
				'group_name' => $customer['name'],
				'group_id' => $customer['groupid'],
				'customer_number' => get_number('customers', $customer['id'], 'customer', 'customer'),
				'customer_status_id' => ($customer['customer_status_id'] == 1 ? true : false),
				'dt_inactive' => $customer['dt_inactive'],

				'is_whatsApp' => $customer['is_whatsApp'],

				'cpf' => $customer['cpf'],
				'cnpj' => $customer['cnpj'],
				'description' => $customer['description'],

				'instagram' => $customer['instagram'],
				'porte' => $customer['porte'],
				'facebook' => $customer['facebook'],
				'linkedin' => $customer['linkedin'],
				'dt_nascimento' => $customer['dt_nascimento'],
				'setor_atividade' => $customer['setor_atividade'],
				'source_id' => $customer['source_id'],
				'assigned_id' => $customer['assigned_id'],
				'source' => $customer['sourcename'],
				'date_contacted' => date(get_dateFormat(), strtotime($customer['created'])),
				'tp_pessoa' => $customer['tp_pessoa'],
				'list_atividades' => $this->Leads_Model->get_atividades_by_user(null, $customer['id']),

			);

			return response()->setJSON($data_customerdetail);
		}
	}


	function addNewAtividade()
	{
		$matriz = [
			'data' => request()->getPost('data'),
			'horario' => request()->getPost('horario'),
			'duracao' => request()->getPost('duracao'),
			'anotacoes' => request()->getPost('anotacoes'),
			'reuniao_call' => request()->getPost('reuniao_call'),
			'retorno' => request()->getPost('retorno'),
			'dt_entrada' => date('Y-m-d H:i:s'),
			'id_criador' => session()->usr_id,
		];

		if (request()->getPost('id_lead')) {
			$matriz['id_lead'] = request()->getPost('id_lead');
			$matriz['atividade'] = request()->getPost('atividade');
			$detail = '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
				session()->staffname . '</a>  adicionou nova atividade no lead <a href="' . base_url() .
				'leads/lead/' . $_POST['id_lead'] . '">' . $_POST['id_lead'] . '</a>.';
		}

		if (request()->getPost('id_customer')) {
			$matriz['id_customer'] = request()->getPost('id_customer');
			$matriz['atividade_customer'] = request()->getPost('atividade');
			$detail = '<a href="' . base_url() . 'staff/staffmember/' . session()->usr_id . '"> ' .
				session()->staffname . '</a>  adicionou nova atividade no cliente <a href="' . base_url() .
				'customers/customer/' . $_POST['id_customer'] . '">' . $_POST['id_customer'] . '</a>.';
		}

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => $detail,
			'staff_id' => session()->get('usr_id'),
		]);

		if (request()->getPost('id_atividade') && !empty(request()->getPost('id_atividade'))) {
			unset($matriz['dt_entrada']);
			$response = $this->db->table('leads_atv')
				->where('id_atividade', request()->getPost('id_atividade'))
				->update($matriz);
		} else {
			if (request()->getPost('id_lead')) {
				$response = $this->db->table('email_task_leads')
					->delete(['id_lead' => request()->getPost('id_lead')]);
			}
			$response = $this->db->table('leads_atv')->insert($matriz);
		}


		return response()->setJSON(["result" => $response]);
	}

	function get_invoices($id)
	{
		$invoices = $this->Invoices_Model->get_all_invoices_by_customer($id);
		$data_invoices = array();

		foreach ($invoices as $invoice) {
			$created = _kdate($invoice['created']);
			$duedate = _kdate($invoice['duedate']);

			if ($invoice['duedate'] == 0000 - 00 - 00) {
				$realduedate = 'Indef'; //'No Due Date';
			} else $realduedate = $duedate;
			$totalx = $invoice['total'];
			$builder = $this->db->table('payments')
				->selectSum('amount')
				->where('invoice_id', $invoice['id']);

			$paytotal = $builder->get()->getRow();
			$balance = $totalx - $paytotal->amount;


			if ($balance > 0) {
				$invoicestatus = '';
			} else $invoicestatus = lang2('paidinv');
			$color = 'success';;

			if ($paytotal->amount < $invoice['total'] && $paytotal->amount > 0 && $invoice['status_id'] == 3) {
				$invoicestatus = lang2('partial');
				$color = 'warning';
			} else {
				if ($paytotal->amount < $invoice['total'] && $paytotal->amount > 0) {
					$invoicestatus = lang2('partial');
					$color = 'warning';
				}

				if ($invoice['status_id'] == 3) {
					$invoicestatus = lang2('unpaid');
					$color = 'danger';
				}
			}

			if ($invoice['status_id'] == 1) {
				$invoicestatus = lang2('draft');
				$color = 'muted';
			}

			if ($invoice['status_id'] == 4) {
				$invoicestatus = lang2('cancelled');
				$color = 'danger';
			}
			if ($invoice['type'] == 1) {
				$customer = $invoice['individual'];
			} else $customer = $invoice['customercompany'];
			$appconfig = get_appconfig();
			$data_invoices[] = array(
				'id' => $invoice['id'],
				'prefix' => $appconfig['inv_prefix'],
				'longid' => get_number('invoices', $invoice['id'], 'invoice', 'inv'),
				'created' => $created,
				'duedate' => $realduedate,
				'customer' => $customer,
				'customer_id' => $invoice['customer_id'],
				'recurring_status' => $invoice['recurring_status'] == '0' ? true : false,
				'staff_id' => $invoice['staff_id'],
				'total' => (float)$invoice['total'],
				'status' => $invoicestatus,
				'color' => $color,
				'' . lang2('filterbystatus') . '' => $invoicestatus,
				'' . lang2('filterbycustomer') . '' => $customer,
			);
		};
		return response()->setJSON($data_invoices);
	}
}
