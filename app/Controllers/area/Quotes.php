<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Quotes extends AREA_Controller
{


	function index()
	{
		$data['title'] = lang('quotes');
		$builder = $this->db->table('proposals');
		$builder->select('proposals.*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, customers.company as customer, customers.email as toemail, customers.namesurname as individual, customers.address as toaddress, proposals.id as id');
		$builder->join('customers', 'proposals.relation = customers.id', 'left');
		$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		$builder->where('proposals.relation', $_SESSION['customer']);
		$builder->where('proposals.relation_type', 'customer');
		$data['proposals'] = $builder->get()->getResultArray();

		//Detaylar
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		return view('area/quotes/index', $data);
	}

	function create()
	{
		$data['title'] = lang('request') . ' ' . lang('quote');
		if (isset($_POST) && count($_POST) > 0) {
			$details = request()->getPost('details');
			$subject = request()->getPost('subject');
			$hasError = false;
			$data['message'] = '';
			if ($subject == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('subject');
			} else if ($details == '') {
				$hasError = true;
				$data['message'] = lang2('invalidmessage') . ' ' . lang2('details');
			}

			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}
			if (!$hasError) {
				$relation_type = 'customer';
				$cutomer_staff = $this->Settings_Model->get_cutomer_staff($_SESSION['customer']);
				$params = array(
					'token' => md5(uniqid()),
					'subject' => request()->getPost('subject'),
					'customer_quote' => request()->getPost('details'),
					'date' => date('Y-m-d H:i:s'),
					'created' => date('Y-m-d H:i:s'),
					'opentill' => date('Y-m-d H:i:s'),
					'relation_type' => 'customer',
					'relation' => $_SESSION['customer'],
					'assigned' => $cutomer_staff['staff_id'],
					'addedfrom' => $cutomer_staff['staff_id'],
					'datesend' => date('Y-m-d H:i:s'),
					'comment' => 0,
					'status_id' => 0,
					'is_requested' => 1,
					//'invoice_id' => request()->getPost( 'invoice' ),
					//'dateconverted' => request()->getPost( 'dateconverted' ),
					'sub_total' => 0,
					'total_discount' => 0,
					'total_tax' => 0,
					'total' => 0,
				);
				$proposals_id = $this->Proposals_Model->proposal_add_by_customer($params);

				$template = $this->Emails_Model->get_template('quote', 'request_quote');
				if ($template['status'] == 1) {
					$proposal = $this->Proposals_Model->get_proposals($proposals_id, 'customer');
					$customer = $proposal['customercompany'] ? $proposal['customercompany'] : $proposal['namesurname'];
					$settings = $this->Settings_Model->get_settings_ciuis();
					$link = base_url('share/quote/' . $proposal['token'] . '');
					$message_vars = array(
						'{customer_name}' => $customer,
						'{quote_link}' => $link,
						'{subject}' => request()->getPost('subject'),
						'{details}' => request()->getPost('details'),
						'{company_name}' => $settings['company'],
						'{company_email}' => $settings['email'],
						'{staff}' => $cutomer_staff['staffname']
					);
					$subject = strtr($template['subject'], $message_vars);
					$message = strtr($template['message'], $message_vars);
					$param = array(
						'from_name' => $template['from_name'],
						'email' => $cutomer_staff['staff_email'],
						'subject' => $subject,
						'message' => $message,
						'created' => date("Y.m.d H:i:s")
					);
					if ($cutomer_staff['staff_email']) {
						$this->db->table('email_queue')->insert($param);
					}
				}
				$data['success'] = true;
				$data['message'] = lang2('proposal') . ' ' . lang2('createmessage');
				$data['id'] = $proposals_id;
				return response()->setJSON($data);
			}
		} else {
			return view('area/inc/header', $data);
			return view('area/quotes/create', $data);
			return view('area/inc/footer', $data);
		}
	}

	function request($token)
	{
		$proposal = $this->Proposals_Model->get_proposal_by_token($token);
		$id = $proposal['id'];
		$data['title'] = 'PRO-' . $id . ' Detail';
		$this->model('Proposals_Model');
		$this->model('Settings_Model');
		$pro = $this->Proposals_Model->get_pro_rel_type($id);
		$rel_type = $pro['relation_type'];
		$data['proposals'] = $this->Proposals_Model->get_proposals($id, $rel_type);

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
		return view('area/quotes/request', $data);
	}

	function customercomment()
	{
		if (isset($_POST) && count($_POST) > 0) {
			$params = array(
				'content' => request()->getPost('content'),
				'relation' => request()->getPost('relation'),
				'relation_type' => 'proposal',
				'staff_id' => session()->get('usr_id'),
				'created' => date('Y-m-d H:i:s'),
			);
			$action = $this->db->table('comments')->insert($params);
			$proposals = $this->Proposals_Model->get_pro_rel_type(request()->getPost('relation'));
			$this->db->table('notifications')->insert(array(
				'date' => date('Y-m-d H:i:s'),
				'detail' => $message = sprintf(lang('newcommentforproposal'), get_number('proposals', $proposals['id'], 'proposal', 'proposal')),
				'staff_id' => $proposals['assigned'],
				'perres' => 'customer_avatar_comment.png',
				'target' => '' . base_url('proposals/proposal/' . $proposals['id'] . '') . ''
			));
			session()->setFlashdata('ntf1', '' . lang('commentadded') . '');
			return redirect()->to('area/quotes/request/' . $proposals['token'] . '');
		} else {
			return redirect()->to('area/quotes/index');
		}
	}
}
