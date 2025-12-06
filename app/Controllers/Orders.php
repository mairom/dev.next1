<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends BaseController
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

		$data['title'] = lang2('orders');

		$data['orders'] = $this->Orders_Model->get_all_orders();

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		return view('orders/index', $data);
	}



	function create()
	{

		if ($this->Privileges_Model->check_privilege('orders', 'create')) {

			$data['title'] = lang2('new') . ' ' . lang2('order');

			$appconfig = get_appconfig();

			if (isset($_POST) && count($_POST) > 0) {

				$order_type = request()->getPost('order_type');

				$customer = request()->getPost('customer');

				$subject = request()->getPost('subject');

				$assigned = request()->getPost('assigned');

				$date = request()->getPost('date');

				$opentill = request()->getPost('opentill');

				$total = request()->getPost('total');

				$lead = request()->getPost('lead');

				$status = request()->getPost('status');

				$total_items = request()->getPost('total_items');

				$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);

				$hasError = false;

				$data['message'] = '';

				if ($subject == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('subject');
				} else if ($customer == '' && $order_type == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
				} else if ($lead == '' && $order_type == 'true') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('lead');
				} else if ($date == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('issue') . ' ' . lang2('date');
				} else if ($assigned == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('assigned');
				} else if ($status == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('status');
				} else if ($opentill == '') {

					$hasError = true;

					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('end') . ' ' . lang2('date');
				} else if (strtotime($opentill) < strtotime($date)) {

					$hasError = true;

					$data['message'] = lang2('issue') . ' ' . lang2('date') . ' ' . lang2('date_error') . ' ' . lang2('end') . ' ' . lang2('date');
				} else if ($total_items == '0') {

					$hasError = true;

					$data['message'] = lang2('invalid_items');
				} else if ($total == 0) {

					$hasError = true;

					$data['message'] = lang2('invalid_total');
				}



				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					if ($order_type != true) {

						$relation_type = 'customer';

						$relation = request()->getPost('customer');
					} else {

						$relation_type = 'lead';

						$relation = request()->getPost('lead');
					};

					$allow_comment = request()->getPost('comment');

					if ($allow_comment != true) {

						$comment_allow = 0;
					} else {

						$comment_allow = 1;
					};

					$params = array(
						'token' => md5(uniqid()),
						'subject' => request()->getPost('subject'),
						'content' => request()->getPost('content'),
						'date' =>  request()->getPost('date'),
						'created' => request()->getPost('created'),
						'opentill' => request()->getPost('opentill'),
						'relation_type' => $relation_type,
						'relation' => $relation,
						'assigned' => request()->getPost('assigned'),
						'addedfrom' => session()->usr_id,
						'datesend' => _pdate(request()->getPost('datesend')),
						'comment' => $comment_allow,
						'status_id' => request()->getPost('status'),
						'invoice_id' => request()->getPost('invoice'),
						'dateconverted' => request()->getPost('dateconverted'),
						'sub_total' => request()->getPost('sub_total'),
						'total_discount' => request()->getPost('total_discount'),
						'total_tax' => request()->getPost('total_tax'),
						'total' => request()->getPost('total'),


					);

					$orders_id = $this->Orders_Model->order_add($params);

					// Custom Field Post

					if (request()->getPost('custom_fields')) {

						$custom_fields = array(

							'custom_fields' => request()->getPost('custom_fields')

						);

						$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'order', $orders_id);
					}

					$this->Settings_Model->create_process('pdf', $orders_id, 'order', 'order_message');

					$data['success'] = true;

					$data['message'] = lang2('order') . ' ' . lang2('createmessage');

					$data['id'] = $orders_id;

					if ($appconfig['order_series']) {

						$order_number = $appconfig['order_series'];

						$order_number = $order_number + 1;

						$this->Settings_Model->increment_series('order_series', $order_number);
					}

					return response()->setJSON($data);
				}
			} else {

				return view('orders/create', $data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function update($id)
	{

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$data['order'] = $this->Orders_Model->get_order_by_priviliges($id, $rel_type);
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$data['order']  = $this->Orders_Model->get_order_by_priviliges($id, $rel_type, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}

		if ($data['order']) {

			if ($this->Privileges_Model->check_privilege('orders', 'edit')) {

				$data['title'] = lang2('update') . ' ' . lang2('order');

				if (isset($pro['id'])) {

					if (isset($_POST) && count($_POST) > 0) {

						switch (request()->getPost('order_type')) {

							case 'true':

								$relation_type = 'lead';

								$relation = request()->getPost('lead');

								break;

							case 'false':

								$relation_type = 'customer';

								$relation = request()->getPost('customer');

								break;
						};

						switch (request()->getPost('comment')) {

							case 'true':

								$comment_allow = 1;

								break;

							case 'false':

								$comment_allow = 0;

								break;
						};

						$params = array(

							'subject' => request()->getPost('subject'),

							'content' => request()->getPost('content'),

							'date' =>  request()->getPost('date'),

							'created' =>  request()->getPost('created'),

							'opentill' =>  request()->getPost('opentill'),

							'relation_type' => $relation_type,

							'relation' => $relation,

							'assigned' => request()->getPost('assigned'),

							'addedfrom' => session()->usr_id,

							'datesend' => _pdate(request()->getPost('datesend')),

							'comment' => $comment_allow,

							'status_id' => request()->getPost('status'),

							'invoice_id' => request()->getPost('invoice'),

							'dateconverted' => request()->getPost('dateconverted'),

							'sub_total' => request()->getPost('sub_total'),

							'total_discount' => request()->getPost('total_discount'),

							'total_tax' => request()->getPost('total_tax'),

							'total' => request()->getPost('total'),

						);

						$this->Orders_Model->update_orders($id, $params);

						// Custom Field Post

						if (request()->getPost('custom_fields')) {

							$custom_fields = array(

								'custom_fields' => request()->getPost('custom_fields')

							);

							$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'order', $id);
						}

						$this->Orders_Model->update_pdf_status($id, '0');

						echo $id;
					} else {

						return view('orders/update', $data);
					}
				} else {

					session()->setFlashdata('ntf3', '' . $id . lang2('orderediterror'));
				}
			} else {

				session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

				return redirect()->to(base_url('orders/order/' . $id));
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}
	}



	function order($id)
	{

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$data['orders'] = $this->Orders_Model->get_order_by_priviliges($id, $rel_type);
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$data['orders']  = $this->Orders_Model->get_order_by_priviliges($id, $rel_type, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}

		if ($data['orders']) {

			$data['title'] = lang2('order');

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			return view('orders/order', $data);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}
	}



	function download_pdf($id)
	{

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$order = $this->Orders_Model->get_order_by_priviliges($id, $rel_type);
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$order  = $this->Orders_Model->get_order_by_priviliges($id, $rel_type, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}

		if ($order) {

			if (isset($id)) {

				$file_name = '' . get_number('orders', $id, 'order', 'order') . '.pdf';

				if (is_file('./uploads/files/orders/' . $id . '/' . $file_name)) {

					$this->load->helper('file');

					$this->load->helper('download');

					$data = file_get_contents('./uploads/files/orders/' . $id . '/' . $file_name);

					force_download($file_name, $data);
				} else {

					session()->setFlashdata('ntf4', lang2('filenotexist'));

					return redirect()->to('orders/order/' . $id);
				}
			} else {

				return redirect()->to('orders/order/' . $id);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}
	}



	function create_pdf($id)
	{

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$order = $this->Orders_Model->get_order_by_priviliges($id, $rel_type);
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$order  = $this->Orders_Model->get_order_by_priviliges($id, $rel_type, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}

		if ($order) {

			ini_set('max_execution_time', 0);

			ini_set('memory_limit', '2048M');

			if (!is_dir('uploads/files/orders/' . $id)) {

				mkdir('./uploads/files/orders/' . $id, 0777, true);
			}

			$data['orders'] = $order;

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

			$data['country'] = get_country($data['settings']['country_id']);

			$data['custcountry'] = get_country($data['orders']['country_id']);

			$data['custstate'] = get_state_name($data['orders']['state'], $data['orders']['state_id']);

			$data['items'] = $this->db->table('items')
				->select('*')
				->where(['relation_type' => 'order', 'relation' => $id])
				->get()->getResultArray();

			return view('orders/pdf', $data);

			$file_name = '' . get_number('orders', $id, 'order', 'order') . '.pdf';

			$html = $this->output->get_output();

			$this->load->library('dom');

			$this->dompdf->loadHtml($html);

			$this->dompdf->set_option('isRemoteEnabled', TRUE);

			$this->dompdf->setPaper('A4', 'portrait');

			$this->dompdf->render();

			$output = $this->dompdf->output();

			unset($this->dompdf);

			file_put_contents('uploads/files/orders/' . $id . '/' . $file_name . '', $output);

			$this->Orders_Model->update_pdf_status($id, '1');

			//$this->dompdf->stream( '' . $file_name . '', array( "Attachment" => 0 ) );

			if ($output) {

				return redirect()->to(base_url('orders/pdf_generated/' . $file_name . ''));
			} else {

				return redirect()->to(base_url('orders/pdf_fault/'));
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}
	}



	function print_($id)
	{

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$order = $this->Orders_Model->get_order_by_priviliges($id, $rel_type);
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$order  = $this->Orders_Model->get_order_by_priviliges($id, $rel_type, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}

		if ($order) {

			$data['orders'] = $order;

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

			$data['country'] = get_country($data['settings']['country_id']);

			$data['custcountry'] = get_country($data['orders']['country_id']);

			$data['custstate'] = get_state_name($data['orders']['state'], $data['orders']['state_id']);

			$data['items'] = $this->db->table('items')
				->select('*')
				->where(['relation_type' => 'order', 'relation' => $id])
				->get()->getResultArray();

			return view('orders/pdf', $data);

			$file_name = '' . get_number('orders', $id, 'order', 'order') . '.pdf';

			$html = $this->output->get_output();

			$this->load->library('dom');

			$this->dompdf->loadHtml($html);

			$this->dompdf->set_option('isRemoteEnabled', TRUE);

			$this->dompdf->setPaper('A4', 'portrait');

			$this->dompdf->render();

			$output = $this->dompdf->output();

			file_put_contents('assets/files/generated_pdf_files/orders/' . $file_name . '', $output);

			return redirect()->to(base_url('assets/files/generated_pdf_files/orders/' . $file_name . ''));

			//$this->dompdf->stream( '' . $file_name . '', array( "Attachment" => 0 ) );

		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}
	}



	function pdf_generated($file)
	{

		$result = array(

			'status' => true,

			'file_name' => $file,

		);

		return response()->setJSON($result);
	}



	function pdf_fault()
	{

		$result = array(

			'status' => false,

		);

		return response()->setJSON($result);
	}



	function dp($id)
	{

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		$data['orders'] = $this->Orders_Model->get_orders($id, $rel_type);

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		$data['items'] = $this->db->table('items')
			->select('*')
			->where(['relation_type' => 'order', 'relation' => $id])
			->get()->getResultArray();

		return view('orders/pdf', $data);
	}



	function share($id)
	{

		$setconfig = $this->Settings_Model->get_settings_ciuis();

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($rel_type == 'customer') {

			$order = $this->Orders_Model->get_orders($id, $rel_type);

			$data['orders'] = $this->Orders_Model->get_orders($id, $rel_type);

			switch ($order['type']) {

				case '0':

					$orderto = $order['customercompany'];

					break;

				case '1':

					$orderto = $order['namesurname'];

					break;
			}

			$ordertoemail = $order['toemail'];
		}

		if ($rel_type == 'lead') {

			$order = $this->Orders_Model->get_orders($id, $rel_type);

			$data['orders'] = $this->Orders_Model->get_orders($id, $rel_type);

			$orderto = $order['leadname'];

			$ordertoemail = $order['toemail'];
		}

		$subject = lang2('neworder');

		$to = $ordertoemail;

		$data = array(

			'customer' => $orderto,

			'customermail' => $ordertoemail,

			'orderlink' => '' . base_url('share/order/' . $pro['token'] . '') . ''

		);

		$body = view('email/orders/send.php', $data, TRUE);

		$result = send_email($subject, $to, $data, $body);

		if ($result) {

			$response = $this->db->table('orders')
				->where('id', $id)
				->update(['datesend' => date('Y-m-d H:i:s')]);


			session()->setFlashdata('ntf1', '<b>' . lang2('sendmailcustomer') . '</b>');

			return redirect()->to('orders/order/' . $id . '');
		} else {

			session()->setFlashdata('ntf4', '<b>' . lang2('sendmailcustomereror') . '</b>');

			return redirect()->to('orders/order/' . $id . '');
		}
	}



	function send_order_email($id)
	{

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$order = $this->Orders_Model->get_order_by_priviliges($id, $rel_type);
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$order  = $this->Orders_Model->get_order_by_priviliges($id, $rel_type, session()->usr_id);
		} else {

			$return['status'] = false;

			$return['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($return);
		}

		if ($order) {

			if ($rel_type == 'customer') {

				$data['orders'] = $order;

				switch ($order['type']) {

					case '0':

						$orderto = $order['customercompany'];

						break;

					case '1':

						$orderto = $order['namesurname'];

						break;
				}

				$ordertoemail = $order['toemail'];
			}

			if ($rel_type == 'lead') {

				$data['orders'] = $order;

				$orderto = $order['leadname'];

				$ordertoemail = $order['toemail'];
			}



			$template = $this->Emails_Model->get_template('order', 'order_message');



			$path = '';

			if ($template['attachment'] == '1') {

				if ($order['pdf_status'] == '0') {

					$this->Orders_Model->generate_pdf($id);

					$file = get_number('orders', $order['id'], 'order', 'order');

					$path = base_url('uploads/files/orders/' . $id . '/' . $file . '.pdf');
				} else {

					$file = get_number('orders', $order['id'], 'order', 'order');

					$path = base_url('uploads/files/orders/' . $id . '/' . $file . '.pdf');
				}
			}



			$order_number = get_number('orders', $id, 'order', 'order');

			$settings = $this->Settings_Model->get_settings_ciuis();

			$message_vars = array(

				'{customer}' => $orderto,

				'{order_to}' => $orderto,

				'{email_signature}' => session()->get('email'),

				'{name}' => session()->get('staffname'),

				'{order_number}' => $order_number,

				'{app_name}' => $settings['company'],

				'{company_name}' => $settings['company']

			);

			$subject = strtr($template['subject'], $message_vars);

			$message = strtr($template['message'], $message_vars);

			$param = array(

				'from_name' => $template['from_name'],

				'email' => $ordertoemail,

				'subject' => $subject,

				'message' => $message,

				'created' => date("Y.m.d H:i:s"),

				'status' => 0,

				'attachments' => $path ? $path : NULL,

			);

			if ($ordertoemail) {

				

				$data = $this->Emails_Model->send_email($ordertoemail, $template['from_name'], $subject, $message, $path);

				if ($data['success'] == true) {

					$return['status'] = true;

					$return['message'] = $data['message'];

					$this->db->table('email_queue')->insert($param);

					return response()->setJSON($return);
				} else {

					$return['status'] = false;

					$return['message'] = lang2('errormessage');

					return response()->setJSON($return);
				}
			}
		} else {

			$return['status'] = false;

			$return['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($return);
		}
	}



	function expiration($id)
	{

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		$setconfig = $this->Settings_Model->get_settings_ciuis();

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($rel_type == 'customer') {

			$order = $this->Orders_Model->get_orders($id, $rel_type);

			$data['orders'] = $this->Orders_Model->get_orders($id, $rel_type);

			switch ($order['type']) {

				case '0':

					$orderto = $order['customercompany'];

					break;

				case '1':

					$orderto = $order['namesurname'];

					break;
			}

			$ordertoemail = $order['toemail'];
		}

		if ($rel_type == 'lead') {

			$order = $this->Orders_Model->get_orders($id, $rel_type);

			$data['orders'] = $this->Orders_Model->get_orders($id, $rel_type);

			$orderto = $order['leadname'];

			$ordertoemail = $order['toemail'];
		}

		$subject = lang2('orderexpiryreminder');

		$to = $ordertoemail;

		$data = array(

			'customer' => $orderto,

			'customermail' => $ordertoemail,

			'orderlink' => '' . base_url('share/order/' . $pro['token'] . '') . ''

		);

		$body = view('email/orders/expiration.php', $data, TRUE);

		$result = send_email($subject, $to, $data, $body);

		if ($result) {

			$response = $this->db->table('orders')
				->where('id', $id)
				->update(['datesend' => date('Y-m-d H:i:s')]);


			session()->setFlashdata('ntf1', '<b>' . lang2('sendmailcustomer') . '</b>');

			return redirect()->to('orders/order/' . $id . '');
		} else {

			session()->setFlashdata('ntf4', '<b>' . lang2('sendmailcustomereror') . '</b>');

			return redirect()->to('orders/order/' . $id . '');
		}
	}



	function convert_invoice($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'create')) {

			$data['title'] = lang2('convertordertoinvoice');

			$pro = $this->Orders_Model->get_pro_rel_type($id);

			$rel_type = $pro['relation_type'];

			$order = $this->Orders_Model->get_orders($id, $rel_type);


			$items =  $this->db->table('items')
				->select('*')
				->where(['relation_type' => 'order', 'relation' => $id])
				->get()->getResultArray();


			$date = strtotime("+7 day");

			if (isset($order['id'])) {

				$params = array(

					'token' => md5(uniqid()),

					'no' => null,

					'serie' => null,

					'customer_id' => $order['relation'],

					'staff_id' => session()->usr_id,

					'status_id' => 3,

					'created' => date('Y-m-d H:i:s'),

					'duedate' => date('Y-m-d H:i:s', $date),

					'datepayment' => 0,

					'duenote' => null,

					//'order_id' => $order[ 'id' ],

					'sub_total' => $order['sub_total'],

					'total_discount' => $order['total_discount'],

					'total_tax' => $order['total_tax'],

					'total' => $order['total'],

				);

				$this->db->table('invoices')->insert($params);

				$invoice = $this->db->insertID();

				$appconfig = get_appconfig();

				$number = $appconfig['invoice_series'] ? $appconfig['invoice_series'] : $invoice;

				$invoice_number = $appconfig['inv_prefix'] . $number;

				$this->db->table('invoices')
					->where('id', $invoice)
					->update(['invoice_number' => $invoice_number]);

				if ($appconfig['invoice_series']) {

					$invoice_number = $appconfig['invoice_series'];

					$invoice_number = $invoice_number + 1;

					$this->Settings_Model->increment_series('invoice_series', $invoice_number);
				}

				$i = 0;

				foreach ($items as $item) {

					$this->db->table('items')->insert(array(

						'relation_type' => 'invoice',

						'relation' => $invoice,

						'product_id' => $item['product_id'],

						'code' => $item['code'],

						'name' => $item['name'],

						'description' => $item['description'],

						'quantity' => $item['quantity'],

						'unit' => $item['unit'],

						'price' => $item['price'],

						'tax' => $item['tax'],

						'discount' => $item['discount'],

						'total' => $item['quantity'] * $item['price'] + (($item['tax']) / 100 * $item['quantity'] * $item['price']) - (($item['discount']) / 100 * $item['quantity'] * $item['price']),

					));

					$i++;
				};

				//LOG

				$staffname = session()->staffname;

				$loggedinuserid = session()->usr_id;

				$appconfig = get_appconfig();

				$this->db->table('logs')->insert(array(

					'date' => date('Y-m-d H:i:s'),

					'detail' => ('' . $message = sprintf(lang2('coverttoinvoice'), $staffname, get_number('orders', $order['id'], 'order', 'order')) . ''),

					'staff_id' => $loggedinuserid,

					'customer_id' => $order['relation']

				));

				//NOTIFICATION

				$staffname = session()->staffname;

				$staffavatar = session()->staffavatar;

				$this->db->table('notifications')->insert(array(

					'date' => date('Y-m-d H:i:s'),

					'detail' => ('' . $staffname . ' ' . lang2('isaddedanewinvoice') . ''),

					'customer_id' => $order['relation'],

					'perres' => $staffavatar,

					'target' => '' . base_url('area/invoice/' . $invoice . '') . ''

				));

				//--------------------------------------------------------------------------------------

				$this->db->table('sales')->insert([
					'invoice_id' => $invoice,
					'status_id' => 3,
					'staff_id' => $loggedinuserid,
					'customer_id' => $order['relation'],
					'total' => $order['total'],
					'date' => date('Y-m-d H:i:s')
				]);
				

				$response = $this->db->table('orders')
				->where('id', $id)
				->update([
					'invoice_id' => $invoice,
					'status_id' => 6,
					'dateconverted' => date('Y-m-d H:i:s')
				]);

				$data['id'] = $invoice;

				$data['success'] = true;

				return response()->setJSON($data);
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}
	}



	function markas()
	{

		if ($this->Privileges_Model->check_privilege('orders', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$name = $_POST['name'];

				$params = array(

					'order_id' => $_POST['order_id'],

					'status_id' => $_POST['status_id'],

				);

				$tickets = $this->Orders_Model->markas();

				$data['success'] = true;

				$data['message'] = lang2('order') . ' ' . lang2('markas') . ' ' . $name;
			}
		} else {



			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function cancelled()
	{

		if ($this->Privileges_Model->check_privilege('orders', 'edit')) {

			if (isset($_POST) && count($_POST) > 0) {

				$params = array(

					'order' => $_POST['order_id'],

					'status_id' => $_POST['status_id'],

				);

				$tickets = $this->Orders_Model->cancelled();

				$data['success'] = true;
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function remove($id)
	{

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$order = $this->Orders_Model->get_order_by_priviliges($id, $rel_type);
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$order  = $this->Orders_Model->get_order_by_priviliges($id, $rel_type, session()->usr_id);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}

		if ($order) {

			if ($this->Privileges_Model->check_privilege('orders', 'delete')) {

				if (isset($order['id'])) {

					$this->Orders_Model->delete_orders($id, get_number('orders', $id, 'order', 'order'));

					$this->load->helper('file');

					$folder = './uploads/files/orders/' . $id;

					if (is_dir($folder)) {

						delete_files($folder, true);

						rmdir($folder);
					}

					$data['success'] = true;

					$data['message'] = lang2('order') . ' ' . lang2('deleted');

					return response()->setJSON($data);
				} else {

					show_error('The orders you are trying to delete does not exist.');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}
	}



	function remove_item($id)
	{

		$response = $this->db->table('items')->delete(array('id' => $id));
	}



	function get_order($id)
	{

		$order = array();

		$pro = $this->Orders_Model->get_pro_rel_type($id);

		$rel_type = $pro['relation_type'];

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$order = $this->Orders_Model->get_order_by_priviliges($id, $rel_type);
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$order  = $this->Orders_Model->get_order_by_priviliges($id, $rel_type, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}

		if ($order) {

			$items =  $this->db->table('items')
				->select('*')
				->where(['relation_type' => 'order', 'relation' => $id])
				->get()->getResultArray();

				$comments = $this->db->table('comments')
				->where('relation', $id)
				->where('relation_type', 'order')
				->get()
				->getResultArray();
			
			$customername = '';

			if ($rel_type == 'customer') {

				$customer_id = $order['relation'];

				$customername = $order['namesurname'] ? $order['namesurname'] : $order['customercompany'];

				$lead_id = '';

				$order_type = false;
			} else {

				$lead_id = $order['relation'];

				$customer_id = '';

				$order_type = true;
			}

			if ($order['comment'] != 0) {

				$comment = true;
			} else {

				$comment = false;
			}

			switch ($order['status_id']) {

				case '1':

					$status = lang2('draft');

					break;

				case '2':

					$status = lang2('sent');

					break;

				case '3':

					$status = lang2('open');

					break;

				case '4':

					$status = lang2('revised');

					break;

				case '5':

					$status = lang2('declined');

					break;

				case '6':

					$status = lang2('accepted');

					break;

				default:

					$status = lang2('open');

					break;
			};

			$appconfig = get_appconfig();

			$order_details = array(

				'id' => $order['id'],

				'token' => $order['token'],

				'long_id' => get_number('orders', $order['id'], 'order', 'order'),

				'subject' => $order['subject'],

				'content' => $order['content'],

				'comment' => $comment,

				'sub_total' => $order['sub_total'],

				'total_discount' => $order['total_discount'],

				'total_tax' => $order['total_tax'],

				'total' => $order['total'],

				'customer' => $customer_id,

				'customername' => $customername,

				'lead' => $lead_id,

				'order_type' => $order_type,

				'created' => date(get_dateFormat(), strtotime($order['created'])),

				'created_edit' => $order['created'],

				'date' => date(get_dateFormat(), strtotime($order['date'])),

				'opentill' => date(get_dateFormat(), strtotime($order['opentill'])),

				'opentill_edit' => $order['opentill'],

				'status' => $order['status_id'],

				'assigned' => $order['assigned'],

				'content' => $order['content'],

				'invoice_id' => $order['invoice_id'],

				'status_name' => $status,

				'items' => $items,

				'comments' => $comments,

				'order_number' => $order['order_number'],

				'pdf_status' => $order['pdf_status'],

			);

			return response()->setJSON($order_details);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('orders'));
		}
	}



	function get_orders()
	{

		$orders = array();

		if ($this->Privileges_Model->check_privilege('orders', 'all')) {

			$orders = $this->Orders_Model->get_all_orders_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('orders', 'own')) {

			$orders = $this->Orders_Model->get_all_orders_by_privileges(session()->usr_id);
		}

		$data_orders = array();

		foreach ($orders as $order) {

			$pro = $this->Orders_Model->get_orders($order['id'], $order['relation_type']);

			if ($pro['relation_type'] == 'customer') {

				if (($pro['customercompany'] === NULL) || ($pro['customercompany'] == '')) {

					$customer = $pro['namesurname'];

					$customer_email = $pro['toemail'];
				} else {

					$customer = $pro['customercompany'];

					$customer_email = $pro['toemail'];
				}
			}

			if ($pro['relation_type'] == 'lead') {

				$customer = $pro['leadname'];

				$customer_email = $pro['toemail'];
			}

			$settings = $this->Settings_Model->get_settings_ciuis();

			switch ($settings['dateformat']) {

				case 'yy.mm.dd':

					$date = _rdate($order['date']);

					$opentill = _rdate($order['opentill']);

					break;

				case 'dd.mm.yy':

					$date = _udate($order['date']);

					$opentill = _udate($order['opentill']);

					break;

				case 'yy-mm-dd':

					$date = _mdate($order['date']);

					$opentill = _mdate($order['opentill']);

					break;

				case 'dd-mm-yy':

					$date = _cdate($order['date']);

					$opentill = _cdate($order['opentill']);

					break;

				case 'yy/mm/dd':

					$date = _zdate($order['date']);

					$opentill = _zdate($order['opentill']);

					break;

				case 'dd/mm/yy':

					$date = _kdate($order['date']);

					$opentill = _kdate($order['opentill']);

					break;
			};

			switch ($order['status_id']) {

				case '1':

					$status = lang2('draft');

					$class = 'order-status-accepted';

					break;

				case '2':

					$status = lang2('sent');

					$class = 'order-status-sent';

					break;

				case '3':

					$status = lang2('open');

					$class = 'order-status-open';

					break;

				case '4':

					$status = lang2('revised');

					$class = 'order-status-revised';

					break;

				case '5':

					$status = lang2('declined');

					$class = 'order-status-declined';

					break;

				case '6':

					$status = lang2('accepted');

					$class = 'order-status-accepted';

					break;

				default:

					$status = lang2('open');

					$class = 'order-status-open';

					break;
			};

			$data_orders[] = array(

				'id' => $order['id'],

				'assigned' => $order['assigned'],

				'prefix' => lang2('orderprefix'),

				'longid' => get_number('orders', $order['id'], 'order', 'order'),

				'subject' => $order['subject'],

				'customer' => $customer,

				'relation' => $order['relation'],

				'date' => $date,

				'opentill' => $opentill,

				'status' => $status,

				'status_id' => $order['status_id'],

				'staff' => $order['staffmembername'],

				'staffavatar' => $order['staffavatar'],

				'total' => (float)$order['total'],

				'class' => $class,

				'relation_type' => $order['relation_type'],

				'customer_email' => $customer_email,

				'' . lang2('relationtype') . '' => $order['relation_type'],

				'' . lang2('filterbystatus') . '' => $status,

				'' . lang2('filterbycustomer') . '' => $customer,

				'' . lang2('filterbyassigned') . '' => $order['staffmembername'],

			);
		};

		return response()->setJSON($data_orders);
	}
}
