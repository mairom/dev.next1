<?php

namespace App\Controllers;

require_once APPPATH . '/third_party/vendor/autoload.php';

use Dompdf\Dompdf;

defined('BASEPATH') or exit('No direct script access allowed');

class Invoices extends BaseController
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

	function invoices()
	{
		$invoices = array();
		$graph = [];
		$duedate = '';
		$created = '';

		$graph['total'] = 0;
		$graph['valor_total'] = 0;

		$graph['pago_total'] = 0;
		$graph['pago_valor'] = 0;

		$graph['pendente_total'] = 0;
		$graph['pendente_valor'] = 0;

		$graph['vencido_total'] = 0;
		$graph['vencido_valor'] = 0;


		$graph['pendente_porcet'] = 0;
		$graph['pago_porcet'] = 0;
		$graph['vencido_porcet'] = 0;

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {
			$invoices = $this->Invoices_Model->get_all_invoices_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {
			$invoices = $this->Invoices_Model->get_all_invoices_by_privileges(session()->usr_id);
		}

		$data_invoices = array();
		foreach ($invoices as $invoice) {
			$definido = 0;
			$graph['total'] += 1;
			$graph['valor_total'] += $invoice['total'];

			$created =  date(get_dateFormat(), strtotime($invoice['created']));
			$duedate = date(get_dateFormat(), strtotime($invoice['duedate']));

			if ($invoice['duedate'] == 0000 - 00 - 00) {
				$realduedate = 'No Due Date';
			} else {
				$realduedate = $duedate;
			}

			$totalx = $invoice['total'];
			$invoicestatus = "";
			$color = "";
			$builder = $this->db->table('payments')
				->selectSum('amount')
				->where('invoice_id', $invoice['id']);

			$paytotal = $builder->get()->getRow();
			$balance = $totalx - $paytotal->amount;

			if ($balance == 0) {
				$invoicestatus = lang2('paidinv');
				$color = 'success';

				$graph['pago_total'] += 1;
				$graph['pago_valor'] += $invoice['total'];
			} else if ($paytotal < $invoice['total'] && $paytotal > 0 && $invoice['status_id'] == 3) {
				$invoicestatus = lang2('partial');
				$color = 'warning';
			} else if ($paytotal < $invoice['total'] && $paytotal > 0) {

				$invoicestatus = lang2('partial');
				$color = 'warning';
			} elseif (($invoice['status_id'] == 3 || $balance > 0) && strtotime($invoice['duedate']) > strtotime(date('Y-m-d'))) {
				$invoicestatus = lang2('unpaid');
				$color = 'warning';

				$graph['pendente_total'] += 1;
				$graph['pendente_valor'] += $invoice['total'];
			} else if ($invoicestatus != lang2('paidinv') && strtotime($invoice['duedate']) < strtotime(date('Y-m-d'))) {
				$invoicestatus = lang2('overdue');
				$color = 'danger';

				$graph['vencido_total'] += 1;
				$graph['vencido_valor'] += $invoice['total'];
			} else if ($invoice['status_id'] == 1) {
				$invoicestatus = lang2('draft');
				$color = 'muted';
			} else if ($invoice['status_id'] == 4) {
				$invoicestatus = lang2('cancelled');
				$color = 'danger';
			}

			if ($invoice['type'] == 1) {
				$customer = $invoice['individual'];
			} else {
				$customer = $invoice['customercompany'];
			}

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


		$graph['pendente_porcet'] = ($graph['total'] > 0 ? number_format(($graph['pendente_total'] * 100) / $graph['total']) : 0);
		$graph['pago_porcet'] = ($graph['total'] > 0 ? number_format(($graph['pago_total'] * 100) / $graph['total']) : 0);
		$graph['vencido_porcet'] = ($graph['total'] > 0 ? number_format(($graph['vencido_total'] * 100) / $graph['total']) : 0);

		return response()->setJSON(['invoices' => $data_invoices, 'graph' => $graph]);
	}



	function index()
	{

		$data['title'] = lang2('invoices');

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {

			$data['invoices'] = $this->Invoices_Model->get_all_invoices_by_privileges();
			$data['off'] = $this->Report_Model->pff();
			$data['ofv'] = $this->Report_Model->ofv();
			$data['oft'] = $this->Report_Model->oft();
			$data['vgf'] = $this->Report_Model->vgf();
			$data['tfa'] = $this->Report_Model->tfa();
			$data['pfs'] = $this->Report_Model->pfs();
			$data['otf'] = $this->Report_Model->otf();
			$data['tef'] = $this->Report_Model->tef();
			$data['vdf'] = $this->Report_Model->vdf();
			$data['fam'] = $this->Report_Model->fam();
		} else {

			$data['invoices'] = $this->Invoices_Model->get_all_invoices_by_privileges(session()->usr_id);
			$data['off'] = $this->Report_Model->total_amount_by_status('1', 'invoices');
			$data['ofv'] = $this->Report_Model->total_amount_by_status('2', 'invoices');
			$data['oft'] = $this->Report_Model->total_amount_by_status('3', 'invoices');
			$data['vgf'] = $this->Report_Model->total_amount_by_status('due', 'invoices');
			$data['tfa'] = $this->Report_Model->total_number_of_data_by_status('4', 'invoices');
			$data['pfs'] = $this->Report_Model->total_number_of_data_by_status('1', 'invoices');
			$data['otf'] = $this->Report_Model->total_number_of_data_by_status('2', 'invoices');
			$data['tef'] = $this->Report_Model->total_number_of_data_by_status('3', 'invoices');
			$data['vdf'] = $this->Report_Model->total_number_of_data_by_status('due', 'invoices');
			$data['fam'] = $this->Report_Model->total_amount_by_status('', 'invoices');
		}

		$data['ofy'] = ($data['tfa'] > 0 ? number_format(($data['tef'] * 100) / $data['tfa']) : 0);
		$data['ofx'] = ($data['tfa'] > 0 ? number_format(($data['otf'] * 100) / $data['tfa']) : 0);
		$data['vgy'] = ($data['tfa'] > 0 ? number_format(($data['vdf'] * 100) / $data['tfa']) : 0);
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		return view('invoices/index', $data);
	}



	function get_content($type, $id = null)
	{

		$html = "";

		if ($type == 'index') {
			$data['title'] = lang2('invoices');

			$data['off'] = $this->Report_Model->pff();
			$data['ofv'] = $this->Report_Model->ofv();
			$data['oft'] = $this->Report_Model->oft();
			$data['vgf'] = $this->Report_Model->vgf();
			$data['tfa'] = $this->Report_Model->tfa();
			$data['pfs'] = $this->Report_Model->pfs();
			$data['otf'] = $this->Report_Model->otf();
			$data['tef'] = $this->Report_Model->tef();
			$data['vdf'] = $this->Report_Model->vdf();
			$data['fam'] = $this->Report_Model->fam();
			$data['ofy'] = ($data['tfa'] > 0 ? number_format(($data['tef'] * 100) / $data['tfa']) : 0);
			$data['ofx'] = ($data['tfa'] > 0 ? number_format(($data['otf'] * 100) / $data['tfa']) : 0);
			$data['vgy'] = ($data['tfa'] > 0 ? number_format(($data['vdf'] * 100) / $data['tfa']) : 0);

			$data['invoices'] = $this->Invoices_Model->get_all_invoices();
			$data['settings'] = $this->Settings_Model->get_settings_ciuis();
			$html = view('invoices/invoices', $data, true);
		}

		if ($type == 'invoice' && isset($id)) {
			$appconfig = get_appconfig();
			$invoices = $this->Invoices_Model->get_invoice_detail($id);
			$data['title'] = '' . get_number('invoices', $invoices['id'], 'invoice', 'inv') . ' ' . lang2('detail') . '';
			$data['invoices'] = $this->Invoices_Model->get_invoice_detail($id);
			$html = view('invoices/invoice', $data);
		}

		//$html = $this->output->get_output($html);
		echo $html;
	}


	function get_graphs()
	{
		$data['off'] = $this->Report_Model->pff();
		$data['pago_valor'] = $this->Report_Model->ofv();
		$data['pago_total'] = $this->Report_Model->otf();

		$data['pendente_valor'] = $this->Report_Model->oft();
		$data['vencido_valor'] = $this->Report_Model->vgf();
		$data['total'] = $this->Report_Model->tfa();
		$data['pfs'] = $this->Report_Model->pfs();

		$data['pendente_total'] = $this->Report_Model->tef();
		$data['vencido_total'] = $this->Report_Model->vdf();
		$data['valor_total'] = $this->Report_Model->fam();

		$data['pendente_porcet'] = ($data['total'] > 0 ? number_format(($data['pendente_total'] * 100) / $data['total']) : 0); //ofy
		$data['pago_porcet'] = ($data['total'] > 0 ? number_format(($data['pago_total'] * 100) / $data['total']) : 0); //ofx
		$data['vencido_porcet'] = ($data['total'] > 0 ? number_format(($data['vencido_total'] * 100) / $data['total']) : 0); //vgy

		return response()->setJSON($data);
	}


	function create()
	{
		$data['title'] = lang2('newinvoice');
		if ($this->Privileges_Model->check_privilege('invoices', 'create')) {
			$data['title'] = lang2('newinvoice');
			if (isset($_POST) && count($_POST) > 0) {
				$customer = request()->getPost('customer');
				$created = request()->getPost('created');
				$duedate = request()->getPost('duedate');
				$datepayment = request()->getPost('datepayment');
				$totalItems = request()->getPost('totalItems');
				$recurring_period = request()->getPost('recurring_period');
				$recurring = request()->getPost('recurring');
				$status = request()->getPost('status');
				$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);
				$paid = ($status == 'true') ? '1' : '0';
				$hasError = false;

				if ($customer == '') {
					$hasError = true;
					$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
				} else if (($created == '')) {
					$hasError = true;
					$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('dateofissuance');
				} else if ($duedate == '') {
					$hasError = true;
					$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('duedate');
				} else if (($paid == '0') && (strtotime($duedate) < strtotime($created))) {
					$hasError = true;
					$datas['message'] = lang2('dateofissuance') . ' ' . lang2('date_error') . ' ' . lang2('duedate');
				} else if (((int)(request()->getPost('totalItems'))) == 0) {
					$hasError = true;
					$datas['message'] = lang2('invalid_items');
				} else if ($total == 0) {
					$hasError = true;
					$datas['message'] = lang2('invalid_total');
				}

				if ($hasError) {
					$datas['success'] = false;
					return response()->setJSON($datas);
				}

				if (!$hasError) {
					$appconfig = get_appconfig();
					$status_value = request()->getPost('status');
					if ($status_value == 'true') {
						$datepayment = request()->getPost('datepayment');
						$duenote = null;
						$status = 2;
					} else {
						$duenote = request()->getPost('duenote');
						$datepayment = null;
						$status = 3;
					}

					$params = array(
						'token' => md5(uniqid()),
						'no' => request()->getPost('no'),
						'serie' => request()->getPost('serie'),
						'customer_id' => request()->getPost('customer'),
						'staff_id' => session()->usr_id,
						'status_id' => $status,
						'created' => request()->getPost('created'),
						'last_recurring' => request()->getPost('created'),
						'duedate' => $duedate,
						'datepayment' => $datepayment,
						'duenote' => $duenote,
						'sub_total' => request()->getPost('sub_total'),
						'total_discount' => request()->getPost('total_discount'),
						//'total_tax' => request()->getPost( 'total_tax' ),
						'total' => request()->getPost('total'),
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
						'default_payment_method' => request()->getPost('default_payment_method'),
						'id_forma_pagment' => request()->getPost('id_forma_pagment'),
						'recurring' => request()->getPost('recurring') == "true" ? '1' : '0'
					);

					$invoices_id = $this->Invoices_Model->invoice_add($params);

					if (request()->getPost('custom_fields')) {
						$custom_fields = array(
							'custom_fields' => request()->getPost('custom_fields')
						);

						$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'invoice', $invoices_id);
					}
					$this->Settings_Model->create_process('pdf', $invoices_id, 'invoice', 'invoice_message');

					if (request()->getPost('recurring') == 'true'  || request()->getPost('recurring') == '1') {

						$true = true;
						$dias = [
							'0' => 1,
							'1' => 7,
							'2' => 30,
							'3' => 365
						];
						$dueDataAtual = $duedate;
						$createDataAtual = request()->getPost('created');

						while ($true) {
							if (request()->getPost('recurring_type') == 0 || request()->getPost('recurring_type') == 1 || request()->getPost('recurring_type') == 3) {
								$newDuedate = date('Y-m-d', strtotime("+" . $dias[request()->getPost('recurring_type')] . " days", strtotime(date($dueDataAtual))));
							} elseif (request()->getPost('recurring_type') == 2) {
								$newDuedate = date('Y-m-d', strtotime("+1 month", strtotime(date($dueDataAtual))));
							}

							if (strtotime($newDuedate) <= strtotime(request()->getPost('end_recurring'))) {
								$createDataAtual = date('Y-m-d', strtotime("+1 month", strtotime(date($createDataAtual))));

								$params['duedate'] = $newDuedate;
								$params['fatAtual'] = $invoices_id;
								$params['token'] = md5(uniqid());
								$params['created'] = $createDataAtual;

								$this->Invoices_Model->invoice_add($params);
								$dueDataAtual = $newDuedate;
							} else {
								$true = false;
							}
						}

						//$recurring_invoices_id = $this->Invoices_Model->recurring_add($SHXparams);
					}

					$datas['success'] = true;

					$datas['id'] = $invoices_id;

					if ($appconfig['invoice_series']) {

						$invoice_number = $appconfig['invoice_series'];

						$invoice_number = $invoice_number + 1;

						$this->Settings_Model->increment_series('invoice_series', $invoice_number);
					}

					$datas['message'] = lang2('invoice') . ' ' . lang2('createmessasge');

					return response()->setJSON($datas);
				}
			} else {

				return view('invoices/create', $data);
			}
		} else {

			session()->setFlashdata('ntf3', '' . $id . lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}
	}



	function update($id)
	{
		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {
			$invoices = $this->Invoices_Model->get_invoice_detail_by_privilegs($id);
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {
			$invoices = $this->Invoices_Model->get_invoice_detail_by_privilegs($id, session()->usr_id);
		} else {
			$datas['success'] = false;
			$datas['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($datas);
		}

		if ($invoices) {

			if ($this->Privileges_Model->check_privilege('invoices', 'edit')) {

				$data['payment'] = $this->Settings_Model->get_payment_gateway_data();

				if (!session()->get('other')) {

					$appconfig = get_appconfig();

					$data['title'] = '' . lang2('updateinvoicetitle') . ' ' . get_number('invoices', $id, 'invoice', 'inv');

					if (isset($invoices['id'])) {

						if (isset($_POST) && count($_POST) > 0) {

							$customer = request()->getPost('customer');

							$created = request()->getPost('created');

							$duedate = request()->getPost('duedate');

							$datepayment = request()->getPost('datepayment');

							//$account = request()->getPost('account');

							$totalItems = request()->getPost('totalItems');

							$recurring_period = request()->getPost('recurring_period');

							$recurring_status = request()->getPost('recurring_status');

							$status = request()->getPost('status');

							$total = filter_var(request()->getPost('total'), FILTER_SANITIZE_NUMBER_INT);

							$paid = ($status == 'true') ? '1' : '0';



							$hasError = false;



							if ($customer == '') {

								$hasError = true;

								$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('customer');
							} else if (($created == '')) {

								$hasError = true;

								$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('dateofissuance');
							} else if (($paid == '1') && (request()->getPost('account') == '')) {

								$hasError = true;

								$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('account');
							} else if (($paid == '1') && (request()->getPost('datepayment') == '')) {

								$hasError = true;

								$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('datepaid');
							} else if ($duedate == '') {

								$hasError = true;

								$datas['message'] = lang2('selectinvalidmessage') . ' ' . lang2('duedate');
							} else if (($paid == '0') && (strtotime($duedate) < strtotime($created))) {

								$hasError = true;

								$datas['message'] = lang2('dateofissuance') . ' ' . lang2('date_error') . ' ' . lang2('duedate');
							}
							//else if (($recurring_status == 'true') && (request()->getPost('recurring_period') == '')) {
							//	$hasError = true;
							//	$datas['message'] = lang2('invalidmessage') . ' ' . lang2('recurring_period');
							//} 
							else if (((int)(request()->getPost('totalItems'))) == 0) {

								$hasError = true;

								$datas['message'] = lang2('invalid_items');
							} else if ($total == 0) {

								$hasError = true;

								$datas['message'] = lang2('invalid_total');
							}

							if ($hasError) {

								$datas['success'] = false;

								return response()->setJSON($datas);
							}

							if (!$hasError) {


								$duenote = request()->getPost('duenote');
								$datepayment = null;
								$params = array(

									'no' => request()->getPost('no'),

									'serie' => request()->getPost('serie'),

									'customer_id' => request()->getPost('customer'),

									'created' => request()->getPost('created'),

									'last_recurring' => request()->getPost('created'),

									'duedate' => $duedate,

									'duenote' => $duenote,

									'sub_total' => request()->getPost('sub_total'),

									'total_discount' => request()->getPost('total_discount'),

									//'total_tax' => request()->getPost( 'total_tax' ),

									'total' => request()->getPost('total'),

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

									'default_payment_method' => request()->getPost('default_payment_method'),

									'id_forma_pagment' => request()->getPost('id_forma_pagment'),

									'recurring' =>  request()->getPost('recurring_status') == "true" ? '1' : '0',

								);

								$this->Invoices_Model->update_invoices($id, $params);



								// Custom Field Post

								if (request()->getPost('custom_fields')) {

									$custom_fields = array(

										'custom_fields' => request()->getPost('custom_fields')

									);

									$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'invoice', $id);
								}

								if (request()->getPost('excluirFuturas') == 1) {

									$this->db
										->where("duedate >= '" . date('Y-m-d') . "'")
										->delete('invoices', ['fatAtual' => $id]);


									$true = true;
									$dias = [
										'0' => 1,
										'1' => 7,
										'2' => 30,
										'3' => 365
									];
									$dueDataAtual = $duedate;
									while ($true) {
										if (request()->getPost('recurring_type') == 0 || request()->getPost('recurring_type') == 1 || request()->getPost('recurring_type') == 3) {
											$newDuedate = date('Y-m-d', strtotime("+" . $dias[request()->getPost('recurring_type')] . " days", strtotime(date($dueDataAtual))));
										} elseif (request()->getPost('recurring_type') == 2) {
											$newDuedate = date('Y-m-d', strtotime("+1 month", strtotime(date($dueDataAtual))));
										}

										if (strtotime($newDuedate) <= strtotime(request()->getPost('end_recurring'))) {
											$params['duedate'] = $newDuedate;
											$params['fatAtual'] = $id;
											$params['recurring'] = '0';

											$this->Invoices_Model->invoice_add($params);
											$dueDataAtual = $newDuedate;
										} else {
											$true = false;
										}
									}
								}






								$this->Invoices_Model->update_pdf_status($id, '0');

								$datas['success'] = true;

								$datas['id'] = $id;

								$datas['message'] = lang2('invoice') . ' ' . lang2('updatemessasge');

								return response()->setJSON($datas);
							}

							// END Recurring Invoice

						} else {

							$data['invoices'] = $invoices;

							return view('invoices/update', $data);
						}
					} else

						session()->setFlashdata('ntf3', '' . $id . lang2('error'));
				} else {

					session()->setFlashdata('ntf3', '' . $id . lang2('you_dont_have_permission'));

					return redirect()->to('invoices');
				}
			} else {

				session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

				return redirect()->to('invoices/invoice/' . $id);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to('invoices');
		}
	}



	function invoice($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id);
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}

		if ($invoice) {

			$data['title'] = '' . get_number('invoices', $id, 'invoice', 'inv');

			$data['invoices'] = $invoice;

			return view('invoices/invoice', $data);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}
	}



	function record_payment()
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'edit')) {

			$amount = request()->getPost('amount');

			$invoicetotal = request()->getPost('invoicetotal');

			if (isset($_POST) && count($_POST) > 0) {
				$amount = $amount;
				$not = request()->getPost('not');
				$account = request()->getPost('account');
				$invoice_id =  request()->getPost('invoice');
				$hasError = false;

				if ($amount == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('amount');
				}
				//	else if ($not == '') {
				//		$hasError = true;
				//		$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
				//}
				else if ($account == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('account') . ' ' . lang2('type');
				} else if ($amount > $invoicetotal) {

					$hasError = true;

					$data['message'] = lang2('paymentamounthigh') . ' ' . lang2('invoice');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$params = array(

						'token' => md5(uniqid()),

						'relation_type' => lang2('invoice'),

						'category_id' => $this->Invoices_Model->get_category_id(),

						'staff_id' => session()->usr_id,

						'customer_id' => request()->getPost('customer'),

						'invoice_id' =>  $invoice_id,

						'account_id' => request()->getPost('account'),

						'title' => lang2('invoice'),

						'date' => request()->getPost('date'),

						'created' => date('Y-m-d H:i:s'),

						'amount' => $amount,

						'total_tax' => '0',

						'sub_total' => $amount,

						'description' => request()->getPost('not'),

						'status' => '2',

						'last_recurring' => date('Y-m-d')

					);

					$payments = $this->Payments_Model->addpayment($params);

					$template = $this->Emails_Model->get_template('invoice', 'invoice_payment');

					if ($template['status'] == 1) {

						$invoice = $this->Invoices_Model->get_invoice_detail($invoice_id);

						$appconfig = get_appconfig();

						$inv_number = get_number('invoices', $invoice_id, 'invoice', 'inv');

						$name = $invoice['customercompany'] ? $invoice['customercompany'] : $invoice['individualindividual'];

						$link = base_url('share/invoice/' . $invoice['token'] . '');

						$message_vars = array(
							'{invoice_number}' => $inv_number,
							'{invoice_link}' => $link,
							'{payment_total}' => $amount,
							'{payment_date}' => request()->getPost('date'),
							'{email_signature}' => session()->get('email'),
							'{name}' => session()->get('staffname'),
							'{customer}' => $name
						);

						$subject = strtr($template['subject'], $message_vars);

						$message = strtr($template['message'], $message_vars);

						$param = array(
							'from_name' => $template['from_name'],
							'email' => $invoice['email'],
							'subject' => $subject,
							'message' => $message,
							'created' => date("Y.m.d H:i:s"),
						);

						if ($invoice['email']) {
							$this->db->table('email_queue')->insert($param);
						}
					}

					$data['success'] = true;

					$data['id'] = $invoice_id;

					$data['message'] = lang2('paymentaddedsuccessfully');

					if ($appconfig['deposit_series']) {
						$deposit_number = $appconfig['deposit_series'];
						$deposit_number = $deposit_number + 1;
						$this->Settings_Model->increment_series('deposit_series', $deposit_number);
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

	function download_file($id)
	{
		if (isset($id)) {
			$fileData = $this->Invoices_Model->get_file($id);
			if (is_file('./uploads/files/invoices/' . $fileData['relation'] . '/' . $fileData['file_name'])) {
				$this->load->helper('file');
				$this->load->helper('download');
				$data = file_get_contents('./uploads/files/invoices/' . $fileData['relation'] . '/' . $fileData['file_name']);
				force_download($fileData['file_name'], $data);
			} else {
				session()->setFlashdata('ntf4', lang2('filenotexist'));
				return redirect()->to('invoices/receipt/' . $fileData['relation']);
			}
		}
	}


	function download_pdf($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id);
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}

		if ($invoice) {

			if (isset($id)) {

				$file_name = '' . get_number('invoices', $id, 'invoice', 'inv') . '.pdf';

				if (is_file('./uploads/files/invoices/' . $id . '/' . $file_name)) {

					$this->load->helper('file');

					$this->load->helper('download');

					$data = file_get_contents('./uploads/files/invoices/' . $id . '/' . $file_name);

					force_download($file_name, $data);
				} else {

					session()->setFlashdata('ntf4', lang2('filenotexist'));

					return redirect()->to('invoices/invoice/' . $id);
				}
			} else {

				return redirect()->to('invoices/invoice/' . $id);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}
	}



	function create_pdf($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id);
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}

		if ($invoice) {

			ini_set('max_execution_time', 0);

			ini_set('memory_limit', '2048M');

			if (!is_dir('uploads/files/invoices/' . $id)) {

				mkdir('./uploads/files/invoices/' . $id, 0777, true);
			}

			$data['invoice'] = $invoice;

			$data['billing_country'] = get_country($data['invoice']['bill_country']);

			$data['billing_state'] = get_state_name($data['invoice']['bill_state'], $data['invoice']['bill_state_id']);

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

			$data['country'] = get_country($data['settings']['country_id']);

			$dafault_payment_method = $data['invoice']['default_payment_method'];

			if ($dafault_payment_method == 'bank') {

				$modes = $this->Settings_Model->get_payment_gateway_data();

				$method = $modes['bank'];
			} else {

				$method = lang($data['invoice']['default_payment_method']);
			}

			$data['default_payment'] = $method;

			$data['payments'] = $this->Invoices_Model->get_invoices_payment($id);

			$data['items'] = $this->db->table('items')
				->select('*')
				->where('relation_type', 'invoice')
				->where('relation', $id)
				->get()
				->getResultArray();
			return view('invoices/pdf', $data);

			$appconfig = get_appconfig();

			$file_name =  get_number('invoices', $id, 'invoice', 'inv') . '.pdf';

			$html = $this->output->get_output();

			require_once APPPATH . '/third_party/vendor/autoload.php';

			$this->dompdf = new DOMPDF();

			//$this->load->library( 'dom' );

			$this->dompdf->loadHtml($html);

			$this->dompdf->set_option('isRemoteEnabled', TRUE);

			$this->dompdf->setPaper('A4', 'portrait');

			$this->dompdf->render();

			$output = $this->dompdf->output();

			file_put_contents('uploads/files/invoices/' . $id . '/' . $file_name . '', $output);

			$this->Invoices_Model->update_pdf_status($id, '1');

			//$this->dompdf->stream( '' . $file_name . '', array( "Attachment" => 0 ) );

			if ($output) {

				return redirect()->to(base_url('invoices/pdf_generated/' . $file_name . ''));
			} else {

				return redirect()->to(base_url('invoices/pdf_fault/'));
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}
	}



	function print_($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id);
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}

		if ($invoice) {

			ini_set('max_execution_time', 0);

			ini_set('memory_limit', '2048M');

			if (!is_dir('uploads/files/invoices/' . $id)) {

				mkdir('./uploads/files/invoices/' . $id, 0777, true);
			}

			$data['payments'] = $this->Invoices_Model->get_invoices_payment($id);

			$data['invoice'] = $invoice;

			$data['billing_country'] = get_country($data['invoice']['bill_country']);

			$data['billing_state'] = get_state_name($data['invoice']['bill_state'], $data['invoice']['bill_state_id']);

			$data['settings'] = $this->Settings_Model->get_settings_ciuis();

			$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);

			$data['country'] = get_country($data['settings']['country_id']);

			$dafault_payment_method = $data['invoice']['default_payment_method'];

			if ($dafault_payment_method == 'bank') {

				$modes = $this->Settings_Model->get_payment_gateway_data();

				$method = $modes['bank'];
			} else {

				$method = lang($data['invoice']['default_payment_method']);
			}

			$data['default_payment'] = $method;

			$data['items'] = $this->db->table('items')
				->select('*')
				->where('relation_type', 'invoice')
				->where('relation', $id)
				->get()
				->getResultArray();
			return view('invoices/pdf', $data);

			$file_name = get_number('invoices', $id, 'invoice', 'inv') . '.pdf';

			$html = $this->output->get_output();

			$this->dompdf = new DOMPDF();

			$this->dompdf->loadHtml($html);

			$this->dompdf->set_option('isRemoteEnabled', TRUE);

			$this->dompdf->setPaper('A4', 'portrait');

			$this->dompdf->render();

			$output = $this->dompdf->output();

			file_put_contents('uploads/files/invoices/' . $id . '/' . $file_name . '', $output);

			if ($output) {

				return redirect()->to(base_url('uploads/files/invoices/' . $id . '/' . $file_name . ''));

				//$this->dompdf->stream( '' . $file_name . '', array( "Attachment" => 0 ) );

			} else {

				return redirect()->to(base_url('invoices/pdf_falut/'));
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
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

		$data['invoice'] = $this->Invoices_Model->get_invoice_detail($id);

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		$data['items'] = $this->db->table('items')
			->select('*')
			->where('relation_type', 'invoice')
			->where('relation', $id)
			->get()
			->getResultArray();
		return view('invoices/pdf', $data);
	}



	function send_invoice_email($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id);
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {

			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id, session()->usr_id);
		} else {

			$return['status'] = false;

			$return['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($return);
		}

		if ($invoice) {

			$template = $this->Emails_Model->get_template('invoice', 'invoice_message');

			$path = '';

			if ($template['attachment'] == '1') {

				if ($invoice['pdf_status'] == '0') {

					$this->Invoices_Model->generate_pdf($id);

					$file = get_number('invoices', $invoice['id'], 'invoice', 'inv');

					$path = base_url('uploads/files/invoices/' . $id . '/' . $file . '.pdf');
				} else {

					$file = get_number('invoices', $invoice['id'], 'invoice', 'inv');

					$path = base_url('uploads/files/invoices/' . $id . '/' . $file . '.pdf');
				}
			}



			$inv_number = '' . get_number("invoices", $invoice['id'], 'invoice', 'inv') . '';

			if ($invoice['status_id'] == 1) {

				$invoicestatus = lang2('draft');
			}

			if ($invoice['status_id'] == 3) {

				$invoicestatus = lang2('unpaid');
			}

			if ($invoice['status_id'] == 4) {

				$invoicestatus = lang2('cancelled');
			}

			if ($invoice['status_id'] == 2) {

				$invoicestatus = lang2('partial');
			}

			$name = $invoice['customercompany'] ? $invoice['customercompany'] : $invoice['individualindividual'];

			$link = base_url('share/invoice/' . $invoice['token'] . '');



			$message_vars = array(

				'{invoice_number}' => $inv_number,

				'{invoice_link}' => $link,

				'{invoice_status}' => $invoicestatus,

				'{email_signature}' => session()->get('email'),

				'{name}' => session()->get('staffname'),

				'{customer}' => $name

			);

			$subject = strtr($template['subject'], $message_vars);

			$message = strtr($template['message'], $message_vars);



			$param = array(

				'from_name' => $template['from_name'],

				'email' => $invoice['email'],

				'subject' => $subject,

				'message' => $message,

				'created' => date("Y.m.d H:i:s"),

				'status' => 0,

				'attachments' => $path ? $path : NULL,

			);



			$data = $this->Emails_Model->send_email($invoice['email'], $template['from_name'], $subject, $message, $path);

			if ($data['success'] == true) {

				$return['status'] = true;

				$return['message'] = $data['message'];

				if ($invoice['email']) {

					$this->db->table('email_queue')->insert($param);
				}

				return response()->setJSON($return);
			} else {

				$return['status'] = false;

				$return['message'] = lang2('errormessage');

				return response()->setJSON($return);
			}
		} else {

			$return['status'] = false;

			$return['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($return);
		}
	}



	function share($id)
	{

		$inv = $this->Invoices_Model->get_invoice_detail($id);

		// SEND EMAIL SETTINGS

		switch ($inv['type']) {

			case '0':

				$invcustomer = $inv['customercompany'];

				break;

			case '1':

				$invcustomer = $inv['namesurname'];

				break;
		}

		$subject = lang2('yourinvoicedetails');

		$to = $inv['email'];

		$data = array(

			'customer' => $invcustomer,

			'customermail' => $inv['email'],

			'invoicelink' => '' . base_url('share/invoice/' . $inv['token'] . '') . ''

		);

		$body = view('email/invoices/sendinvoice.php', $data, TRUE);

		$result = send_email($subject, $to, $data, $body);

		if ($result) {

			$response = $this->db->table('invoices')->where('id', $id)->update(['datesend' => date('Y-m-d H:i:s')]);

			session()->setFlashdata('ntf1', '<b>' . $inv['email'], lang2('sendmailcustomer') . '</b>');

			return redirect()->to('invoices/invoice/' . $id . '');
		} else {

			session()->setFlashdata('ntf4', '<b>' . lang2('sendmailcustomereror') . '</b>');

			return redirect()->to('invoices/invoice/' . $id . '');
		}
	}



	function mark_as_draft($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'edit')) {

			$response = $this->db->table('invoices')->where('id', $id)->update(['status_id' => 1]);

			$response = $this->db->table('sales')->where('invoice_id', $id)->update(['status_id' => 1]);


			$data['success'] = true;

			$data['message'] = lang2('markedasdraft');
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function mark_as_cancelled($id)
	{

		if ($this->Privileges_Model->check_privilege('invoices', 'edit')) {

			$response = $this->db->table('invoices')->where('id', $id)->update(['status_id' => 4]);

			$response = $this->db->table('sales')->delete(['invoice_id' => $id]);

			$response = $this->db->table('payments')->delete(['invoice_id' => $id]);


			$data['success'] = true;

			$data['message'] = lang2('markedascancelled');
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function remove($id)
	{
		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {
			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id);
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {
			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('invoices'));
		}

		if ($invoice) {

			if ($this->Privileges_Model->check_privilege('invoices', 'delete')) {

				if (isset($invoice['id'])) {
/*
					$this->load->helper('file');
					$folder = './uploads/files/invoices/' . $id;
					if (file_exists($folder)) {
						delete_files($folder, true);
						rmdir($folder);
					}
*/
					$this->Invoices_Model->delete_invoices($id, get_number('invoices', $id, 'invoice', 'inv'));

					$data['success'] = true;
					$data['message'] = lang2('invoicedeleted');
				} else {
					show_error('The invoices you are trying to delete does not exist.');
				}
			} else {
				$data['success'] = false;
				$data['message'] = lang2('you_dont_have_permission');
			}

			return response()->setJSON($data);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('invoices'));
		}
	}



	function remove_item($id)
	{

		$response = $this->db->table('items')->update(array('id' => $id));
	}



	function get_invoice($id)
	{
		$invoice = array();
		if ($this->Privileges_Model->check_privilege('invoices', 'all')) {
			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id);
		} else if ($this->Privileges_Model->check_privilege('invoices', 'own')) {
			$invoice = $this->Invoices_Model->get_invoice_detail_by_privilegs($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('invoices'));
		}

		if ($invoice) {

			$fatop = $this->Invoices_Model->get_items_invoices($id);

			$tadtu = $this->Invoices_Model->get_paid_invoices($id);

			$total = $invoice['total'];

			$today = time();

			$duedate = strtotime($invoice['duedate']); // or your date as well

			$created = strtotime($invoice['created']);

			$paymentday = $duedate - $created; // Bunun sonucu 14 gün olcak

			$paymentx = $today - $created;

			$datepaymentnet = $paymentday - $paymentx;

			if ($invoice['duedate'] == 0) {

				$duedate_text = 'No Due Date';
			} else {

				if ($datepaymentnet < 0) {

					$duedate_text = lang2('overdue');

					$duedate_text = '' . floor($datepaymentnet / (60 * 60 * 24)) . ' days';
				} else {

					$duedate_text = lang2('payableafter') . floor($datepaymentnet / (60 * 60 * 24)) . ' ' . lang2('day') . '';
				}
			}

			if ($invoice['datesend'] == 0) {

				$mail_status = lang2('notyetbeensent');
			} else $mail_status = _adate($invoice['datesend']);

			$kalan = $total - $tadtu->amount;

			$net_balance = $kalan;

			if ($tadtu->amount < $total && $tadtu->amount > 0) {

				$partial_is = true;
			} else $partial_is = false;

			$payments = $this->db->table('payments')
				->select('*, accounts.name as accountname, payments.id as id')
				->join('accounts', 'payments.account_id = accounts.id', 'left')
				->where('invoice_id', $id)
				->get()
				->getResultArray();

			$items = $this->db->table('items')
				->select('*')
				->where('relation_type', 'invoice')
				->where('relation', $id)
				->get()
				->getResultArray();
			//foreach($items as $index => $item){
			//	$items[$index]['price'] = number_format($item['price'], 2, ',', '.');
			//}

			if (isset($invoice['type']) &&  $invoice['type'] == 1 && isset($invoice['individual'])) {
				$customer = $invoice['individual'];
			} else {
				$customer = $invoice['customercompany'];
			}



			$properties = array(

				'invoice_id' => '' .  get_number('invoices', $invoice['id'], 'invoice', 'inv') . '',

				'customer' => $customer,

				'customer_address' => $invoice['customeraddress'],

				'customer_phone' => $invoice['customerphone'],

				'invoice_staff' => $invoice['staffmembername'],

				'invoice_number' => $invoice['invoice_number']

			);



			if ($invoice['recurring_endDate'] != 'Invalid date') {

				$recurring_endDate = date(DATE_ISO8601, strtotime($invoice['recurring_endDate']));
			} else {

				$recurring_endDate = '';
			}

			$billing_country = get_country($invoice['bill_country']);

			$shipping_country = get_country($invoice['shipp_country']);

			$billing_state = get_state_name($invoice['bill_state'], $invoice['bill_state_id']);

			$shipping_state = get_state_name($invoice['shipp_state'], $invoice['shipp_state_id']);

			$invoice_details = array(

				'id' => $invoice['id'],

				'sub_total' => $invoice['sub_total'],

				'total_discount' => $invoice['total_discount'],

				//'total_tax' => $invoice[ 'total_tax' ],

				'total' => $invoice['total'],

				'no' => $invoice['no'],

				'serie' => $invoice['serie'],

				'created' => date(get_dateFormat(), strtotime($invoice['created'])),

				'duedate' => date(get_dateFormat(), strtotime($invoice['duedate'])),

				'created_edit' => $invoice['created'],

				'duedate_edit' => $invoice['duedate'],

				'customer' => $invoice['customer_id'],

				'id_forma_pagment' => $invoice['id_forma_pagment'],

				'billing_street' => $invoice['bill_street'],

				'billing_city' => $invoice['bill_city'],

				'billing_state' => $billing_state,

				'billing_state_id' => $invoice['bill_state_id'],

				'billing_zip' => $invoice['bill_zip'],

				'billing_country' => $billing_country,

				'billing_country_id' => $invoice['bill_country'],

				'shipping_street' => $invoice['shipp_street'],

				'shipping_city' => $invoice['shipp_city'],

				'shipping_state' => $shipping_state,

				'shipping_state_id' => $invoice['shipp_state_id'],

				'shipping_zip' => $invoice['shipp_zip'],

				'shipping_country' => $shipping_country,

				'shipping_country_id' => $invoice['shipp_country'],

				'datepayment' => $invoice['datepayment'],

				'duenote' => $invoice['duenote'],

				'status_id' => $invoice['status_id'],

				'default_payment_method' => $invoice['default_payment_method'],

				'duedate_text' => $duedate_text,

				'mail_status' => $mail_status,

				'balance' => $net_balance,

				'partial_is' => $partial_is,

				'items' => $items,

				'payments' => $payments,

				// Recurring Invoice

				'recurring_endDate' => $recurring_endDate,

				'recurring_id' => $invoice['recurring_id'],

				'recurring_status' => $invoice['recurring_status'] == '0' ? true : false,

				'recurring_status' => $invoice['recurring_status'] == '0' ? true : false,

				'recurring_period' => (int)$invoice['recurring_period'],

				'recurring_type' => $invoice['recurring_type'] ? $invoice['recurring_type'] : 0,

				// END Recurring Invoice

				'payments' => $payments,

				'properties' => $properties,

				'invoice_number' => $invoice['invoice_number'],

				'pdf_status' => $invoice['pdf_status'],



			);

			return response()->setJSON($invoice_details);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('invoices'));
		}
	}

	function add_file($id)
	{
		if ($this->Privileges_Model->check_privilege('invoices', 'edit')) {
			if (isset($id)) {
				if (isset($_POST)) {
					$uploadPath = ROOTPATH . 'public/uploads/files/invoices/' . $id . '/';

					// Cria o diretório se não existir
					if (!is_dir($uploadPath)) {
						mkdir($uploadPath, 0777, true);
					}

					// Verifica se o arquivo foi enviado
					if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
						$file = $_FILES['file'];
						$allowedTypes = ['zip', 'rar', 'tar', 'gif', 'jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'ppt', 'opt'];
						$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

						// Verifica se a extensão do arquivo é permitida e o tamanho do arquivo
						if (in_array($fileExtension, $allowedTypes) && $file['size'] <= 9000000) {
							$newName = preg_replace("/[^a-z0-9_\-\.]/i", '', basename($file['name']));
							$filePath = $uploadPath . $newName;

							// Move o arquivo para o local desejado
							if (move_uploaded_file($file['tmp_name'], $filePath)) {
								$params = [
									'relation_type' => 'invoice',
									'relation' => $id,
									'file_name' => $newName,
									'created' => date("Y.m.d H:i:s"),
								];
								$this->Invoices_Model->update_pdf_status($id, '0');
								$this->db->table('files')->insert($params);

								$data['success'] = true;
								$data['message'] = lang2('file') . ' ' . lang2('uploadmessage');
							} else {
								$data['success'] = false;
								$data['message'] = lang2('file_upload_error');
							}
						} else {
							$data['success'] = false;
							$data['message'] = lang2('file_type_or_size_error');
						}
					} else {
						$data['success'] = false;
						$data['message'] = lang2('no_file_uploaded');
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


	function delete_file($id)
	{
		if ($this->Privileges_Model->check_privilege('invoices', 'delete')) {
			if (isset($id)) {
				$fileData = $this->Invoices_Model->get_file($id);
				$response = $this->db->table('files')->delete(['id' => $id]);

				if (is_file('./uploads/files/invoices/' . $fileData['relation'] . '/' . $fileData['file_name'])) {
					unlink('./uploads/files/invoices/' . $fileData['relation'] . '/' . $fileData['file_name']);
				}
				$this->Invoices_Model->update_pdf_status($fileData['relation'], '0');
				if ($response) {
					$data['success'] = true;
					$data['message'] = lang2('file') . ' ' . lang2('deletemessage');
				} else {
					$data['success'] = false;
					$data['message'] = lang2('errormessage');
				}
				return response()->setJSON($data);
			} else {
				return redirect()->to('invoices');
			}
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}
	}



	function files($id)
	{
		if (isset($id)) {
			$files = $this->Invoices_Model->get_files($id);
			$data = array();
			foreach ($files as $file) {
				$ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
				$type = 'file';
				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {
					$type = 'image';
				}
				if ($ext == 'pdf') {
					$type = 'pdf';
				}
				if ($ext == 'zip' || $ext == 'rar' || $ext == 'tar') {
					$type = 'archive';
				}
				if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'gif') {
					$display = true;
				} else {
					$display = false;
				}
				if ($ext == 'pdf') {
					$pdf = true;
				} else {
					$pdf = false;
				}
				$data[] = array(
					'id' => $file['id'],
					'invoice_id' => $file['relation'],
					'file_name' => $file['file_name'],
					'created' => $file['created'],
					'display' => $display,
					'pdf' => $pdf,
					'type' => $type,
					'path' => base_url('uploads/files/invoices/' . $id . '/' . $file['file_name']),
				);
			}
			return response()->setJSON($data);
		}
	}
}
