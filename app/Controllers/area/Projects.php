<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Projects extends AREA_Controller
{


	function index()
	{
		$data['title'] = lang('areatitleprojects');
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		return view('area/projects/index', $data);
	}

	function project($id)
	{
		$permission = $this->Projects_Model->check_project_permission($id, $_SESSION['contact_id']);
		if ($permission) {
			$project = $this->Projects_Model->get_projects($id);
			$data['title'] = $project['name'];
			$data['projects'] = $project;
			return view('area/projects/project', $data);
		} else {
			return redirect()->to('area/projects');
		}
	}

	function addnote()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$description = $_POST['description'];
			$hasError = false;
			$data['message'] = '';
			if ($description == '' || !$description) {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('note');
			}
			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}
			if (!$hasError) {
				$params = array(
					'relation_type' => $_POST['relation_type'],
					'relation' => $_POST['relation'],
					'description' => $_POST['description'],
					'customer_id' => $_SESSION['customer'],
					'created' => date('Y-m-d H:i:s'),
				);

				$this->db->table('notes')->insert($params);
				$data['insert_id'] = $this->db->insertID();

				$template = $this->Emails_Model->get_template('project', 'new_note_to_members_by_customer');
				if ($template['status'] == 1) {
					$project = $this->Projects_Model->get_projects($_POST['relation']);
					$project_url = '' . base_url('projects/project/' . $_POST['relation'] . '') . '';
					switch ($project['status']) {
						case '1':
							$status_project = lang('notstarted');
							break;
						case '2':
							$status_project = lang('started');
							break;
						case '3':
							$status_project = lang('percentage');
							break;
						case '4':
							$status_project = lang('cancelled');
							break;
						case '5':
							$status_project = lang('complete');
							break;
					};
					if ($project['namesurname']) {
						$customer = $project['namesurname'];
					} else {
						$customer = $project['customercompany'];
					}
					$message_vars = array(
						'{customer}' => $customer,
						'{project_name}' => $project['name'],
						'{project_start_date}' => $project['start_date'],
						'{project_end_date}' => $project['deadline'],
						'{project_value}' => $project['projectvalue'],
						'{project_tax}' => $project['tax'],
						'{note}' => $_POST['description'],
						'{loggedin_staff}' => $_SESSION['name'],
						'{project_url}' => $project_url,
						'{project_status}' => $status_project,
						'{name}' => $_SESSION['name'],
						'{email_signature}' => $_SESSION['email'],
					);
					$email = '';
					$project_admin = $this->Projects_Model->get_project_admin($_POST['relation']);
					if ($project_admin['adminemail']) {
						$email = $project_admin['adminemail'];
					}
					$members = $this->Projects_Model->get_members($_POST['relation']);
					$recipients = array();
					foreach ($members as $member) {
						$recipients[] = $member['memberemail'];
					}
					$recipients[] = $email;
					if (count($recipients) > 0) {
						$subject = strtr($template['subject'], $message_vars);
						$message = strtr($template['message'], $message_vars);
						$param = array(
							'from_name' => $template['from_name'],
							'email' => serialize($recipients),
							'subject' => $subject,
							'message' => $message,
							'created' => date("Y.m.d H:i:s")
						);
						$this->db->table('email_queue')->insert($param);
					}
				}
				$data['success'] = true;
				$data['message'] = lang2('note') . ' ' . lang2('addmessage');
				return response()->setJSON($data);
			}
		}
	}

	public function add_file($id)
	{
		if ($id) {
			if (request()->getMethod() === 'post') {
				// Cria o diretório se não existir
				$uploadPath = WRITEPATH . 'uploads/files/projects/' . $id;
				if (!is_dir($uploadPath)) {
					mkdir($uploadPath, 0777, true);
				}

				// Configurações do upload
				$file = request()->getFile('file_name');
				$config = [
					'uploadPath'   => $uploadPath,
					'allowedTypes' => 'zip|rar|tar|gif|jpg|png|jpeg|gif|pdf|doc|docx|xls|xlsx|txt|csv|ppt|opt',
					'maxSize'      => '9000',
					'fileName'     => preg_replace("/[^a-z0-9\_\-\.]/i", '', $file->getName()),
				];

				// Move o arquivo
				if ($file->isValid() && $file->move($config['uploadPath'], $config['fileName'])) {
					$filename = $file->getName();

					// Insere os detalhes do arquivo no banco de dados
					$params = [
						'relation_type' => 'project',
						'relation'      => $id,
						'file_name'     => $filename,
						'created'       => date("Y-m-d H:i:s"),
						'is_old'        => '0'
					];
					$this->db->table('files')->insert($params);

					// Envia o e-mail
					$template = $this->Emails_Model->get_template('project', 'new_file_uploaded_by_customer');
					if ($template['status'] == 1) {
						$project = $this->Projects_Model->get_projects($id);
						$project_url = base_url('projects/project/' . $id);
						$status_project = match ($project['status']) {
							'1' => lang('notstarted'),
							'2' => lang('started'),
							'3' => lang('percentage'),
							'4' => lang('cancelled'),
							'5' => lang('complete'),
							default => '',
						};

						$customer = $project['namesurname'] ?: $project['customercompany'];

						$attachment = $template['attachment'] == 1 ? base_url('uploads/files/projects/' . $id . '/' . $filename) : '';

						$message_vars = [
							'{customer}'          => $customer,
							'{project_name}'      => $project['name'],
							'{project_start_date}' => $project['start_date'],
							'{project_end_date}'  => $project['deadline'],
							'{project_value}'     => $project['projectvalue'],
							'{project_tax}'       => $project['tax'],
							'{note}'              => request()->getPost('description'),
							'{loggedin_staff}'    => session()->get('name'),
							'{project_url}'       => $project_url,
							'{project_status}'    => $status_project,
							'{name}'              => session()->get('name'),
							'{email_signature}'   => session()->get('email'),
						];

						$email = '';
						$project_admin = $this->Projects_Model->get_project_admin($id);
						if ($project_admin['adminemail']) {
							$email = $project_admin['adminemail'];
						}

						$members = $this->Projects_Model->get_members($id);
						$recipients = array_map(fn ($member) => $member['memberemail'], $members);
						if ($email) {
							$recipients[] = $email;
						}

						if (!empty($recipients)) {
							$subject = strtr($template['subject'], $message_vars);
							$message = strtr($template['message'], $message_vars);

							$param = [
								'from_name'  => $template['from_name'],
								'email'      => serialize($recipients),
								'subject'    => $subject,
								'message'    => $message,
								'created'    => date("Y-m-d H:i:s"),
								'attachments' => $attachment
							];
							$this->db->table('email_queue')->insert($param);
						}
					}

					return redirect()->to('area/projects/project/' . $id);
				} else {
					// Se o upload falhar, você pode lidar com o erro aqui
					return redirect()->back()->with('error', 'Erro ao fazer o upload do arquivo.');
				}
			}
		}
	}


	function download_file($id)
	{
		if (isset($id)) {
			$fileData = $this->Expenses_Model->get_file($id);
			if ($fileData['is_old'] == '1') {
				if (is_file('./uploads/files/' . $fileData['file_name'])) {
					$this->load->helper('file');
					$this->load->helper('download');
					$data = file_get_contents('./uploads/files/' . $fileData['file_name']);
					force_download($fileData['file_name'], $data);
				} else {
					session()->setFlashdata('ntf4', lang2('filenotexist'));
					return redirect()->to('projects/project/' . $fileData['relation']);
				}
			} else {
				if (is_file('./uploads/files/projects/' . $fileData['relation'] . '/' . $fileData['file_name'])) {
					$this->load->helper('file');
					$this->load->helper('download');
					$data = file_get_contents('./uploads/files/projects/' . $fileData['relation'] . '/' . $fileData['file_name']);
					force_download($fileData['file_name'], $data);
				} else {
					session()->setFlashdata('ntf4', lang2('filenotexist'));
					return redirect()->to('projects/project/' . $fileData['relation']);
				}
			}
		}
	}
}
