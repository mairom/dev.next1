<?php

namespace App\Controllers;

require_once APPPATH . '/third_party/vendor/autoload.php';

use Dompdf\Dompdf;

defined('BASEPATH') or exit('No direct script access allowed');

class Report extends BaseController
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

		$data = $this->Settings_Model->get_settings_ciuis();

		$data['settings'] = $data;

		$data['title'] = $data['crm_name'] . ' ' . lang2('reports');

		$data['tbs'] = $this->db->count_all('notifications', array('markread' => ('0')));

		$data['bkt'] = $this->Report_Model->bkt(); // daily total sales

		$data['bht'] = $this->Report_Model->bht(); // weekly sales

		$data['ycr'] = $this->Report_Model->ycr(); // yearly sales

		$data['oyc'] = $this->Report_Model->oyc(); // yearly sales

		$data['oft'] = $this->Report_Model->oft(); // invoice total due

		$data['tef'] = $this->Report_Model->tef(); // total invoices

		$data['vgf'] = $this->Report_Model->vgf();

		$data['tbs'] = $this->Report_Model->tbs();

		$data['akt'] = $this->Report_Model->akt();

		$data['oak'] = $this->Report_Model->oak();

		$data['tfa'] = $this->Report_Model->tfa();

		$data['yms'] = $this->Report_Model->yms();

		$data['ofy'] = ($data['tfa'] > 0 ? number_format(($data['tef'] * 100) / $data['tfa']) : 0);

		$data['weekly_sales_chart_report'] = json_encode($this->Report_Model->weekly_sales_chart_report());

		$data['monthly_sales_graph'] = $this->Report_Model->monthly_sales_graph();

		$data['monthly_expense_graph'] = $this->Report_Model->monthly_expenses();

		$data['invoice_chart_by_status'] = json_encode($this->Report_Model->invoice_chart_by_status());

		$data['leads_by_leadsource'] = json_encode($this->Report_Model->leads_by_leadsource());

		$data['leads_to_win_by_leadsource'] = json_encode($this->Report_Model->leads_to_win_by_leadsource());

		$data['top_selling_staff_chart'] = json_encode($this->Report_Model->top_selling_staff_chart());

		$data['incomings_vs_outgoins'] = json_encode($this->Report_Model->incomings_vs_outgoins());

		$data['expenses_by_categories'] = json_encode($this->Report_Model->expenses_by_categories());

		$data['events'] = $this->Events_Model->get_all_events();

		return view('report/index', $data);
	}



	function get_reports_data()
	{

		$data['totalTickets'] = $this->Report_Model->totalData('tickets');
		$data['totalCustomers'] = $this->Report_Model->totalData('customers');
		$data['totalProjects'] = $this->Report_Model->totalData('projects');
		$data['totalProducts'] = $this->Report_Model->totalData('products');
		$data['totalTasks'] = $this->Report_Model->totalData('tasks');
		$data['totalLeads'] = $this->Report_Model->totalData('leads');
		$data['totalOrders'] = $this->Report_Model->totalData('orders');
		$data['totalQotations'] = 0;
		$data['totalInvoices'] = $this->Report_Model->totalData('invoices');
		$report = $this->Report_Model->weekly_incomings_vs_outgoings();
		$data['payments'] = $report['payments'];
		$data['expenses'] = $report['expenses'];
		$data['weekdays'] = array(lang2('monday'), lang2('tuesday'), lang2('wednesday'), lang2('thursday'), lang2('friday'), lang2('saturday'), lang2('sunday'),);
		return response()->setJSON($data);
	}



	function get_timesheet_data()
	{
		$result = $this->Report_Model->get_all_staff();

		foreach ($result as $field) {
			$total_dia = '';
			$total_mes = '';

			$dt_ate = date("Y-m-t");
			$dt_de = date("Y-m-01");
			$data = $this->Report_Model->staff_sales_graph2($field['id'], $dt_de, $dt_ate);

			foreach ($data['inline_graph'] as $index => $row) {
				if ($row['label'] == date('d/m/Y')) {
					$total_dia = $row['total'];
				}
				if ($row['total'] == '0') {
					continue;
				}

				if ($total_mes != '') {
					$dt  = new DateTime($total_mes);

					$parts = explode(':', $row['total']);
					$interval = new DateInterval('PT' . (int)$parts[0] . 'H' . $parts[1] . 'M' . $parts[2] . 'S');
					$dt->add($interval);

					$total_mes = $dt->format('H:i:s');
				} else {
					$total_mes = $row['total'];
				}
			}

			$timesheet[] = array(
				'id' => $field['id'],
				'staff' => $field['staffname'],
				'total_mes' => $total_mes != '' ? $total_mes : '00:00:00',
				'total_dia' => $total_dia != '0' ? $total_dia : '00:00:00',
				'staff_id' => $field['id'],
				'avatar' => $field['staffavatar'],
			);
		}


		$data = array(
			'timesheet' => $timesheet,
		);

		return response()->setJSON($data);
	}



	function expenses_payments_graph($year)
	{

		return response()->setJSON($this->Report_Model->expenses_payments_graph($year));
	}



	function customer_monthly_increase_chart($month)
	{

		return response()->setJSON($this->Report_Model->customer_monthly_increase_chart($month));
	}



	function lead_graph($month)
	{

		return response()->setJSON($this->Report_Model->lead_graph($month));
	}



	function test()
	{

		return response()->setJSON($this->Report_Model->a1());
	}



	function viewReports()
	{

		if (isset($_POST) && count($_POST) > 0) {

			$period = request()->getPost('period');
			$exporttype = request()->getPost('exporttype');
			$hasError = false;
			$data['message'] = '';

			if ($exporttype == '') {
				$hasError = true;
				$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('report') . ' ' . lang2('type');
			} else if ($period == '7') {

				$from = request()->getPost('from');
				$to = request()->getPost('to');

				if ($from == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('from') . ' ' . lang2('date');
				} else if ($to == '') {
					$hasError = true;
					$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('to') . ' ' . lang2('date');
				}
			}

			if ($hasError) {
				$data['success'] = false;
				return response()->setJSON($data);
			}

			if (!$hasError) {

				if ($exporttype == 'invoices') {
					$data['result'] = $this->Report_Model->InvoiceReport();
				} else if ($exporttype == 'expenses') {

					$data['result'] = $this->Report_Model->ExpenseReport();
				} else if ($exporttype == 'customers') {

					$data['result'] = $this->Report_Model->CustomerReport();
				} else if ($exporttype == 'proposals') {

					$data['result'] = $this->Report_Model->ProposalReport();
				} else if ($exporttype == 'deposits') {

					$data['result'] = $this->Report_Model->DepositsReport();
				} else if ($exporttype == 'orders') {

					$data['result'] = $this->Report_Model->OrdersReport();
				} else if ($exporttype == 'vendors') {

					$data['result'] = $this->Report_Model->VendorsReport();
				} else if ($exporttype == 'purchases') {

					$data['result'] = $this->Report_Model->PurchasesReport();
				} else if ($exporttype == 'contacts') {

					$data['result'] = $this->Report_Model->ContactsReport();
				} else if ($exporttype == 'tickets') {

					$data['result'] = $this->Report_Model->TicketsReport();
				} else if ($exporttype == 'tasks') {

					$data['result'] = $this->Report_Model->TasksReport();
				} else if ($exporttype == 'leads') {

					$data['result'] = $this->Report_Model->LeadsReport();
				} else if ($exporttype == 'products') {

					$data['result'] = $this->Report_Model->ProductReport();
				} else if ($exporttype == 'staff') {

					$data['result'] = $this->Report_Model->staffReport();
				} else if ($exporttype == 'projects') {

					$data['result'] = $this->Report_Model->ProjectsReport();
				}

				$data['exporttype'] = $exporttype;

				$data['success'] = true;

				return response()->setJSON($data);
			}
		}
	}



	function exportData()
	{

		$type = request()->getPost('type');

		$period = request()->getPost('period');

		$from = request()->getPost('from');

		$to = request()->getPost('to');

		if ($type == 'invoices') {

			$builder = $this->db->table('invoices');
			$builder->select('invoices.id as invid, invoice_number, serie, invoices.created as issuance_date, duedate, customers.id as custid, customer_number, customers.company as company, customers.namesurname as custname, staff.id as staffid, staff_number, staffname, invoicestatus.name as status, total_tax, total, invoices.default_payment_method as default_payment_method');
			$builder->join('customers', 'invoices.customer_id = customers.id', 'left');
			$builder->join('invoicestatus', 'invoices.status_id = invoicestatus.id', 'left');
			$builder->join('staff', 'invoices.staff_id = staff.id', 'left');
		} else if ($type == 'customers') {

			$builder = $this->db->table('customers');
			$builder->select('customers.id as custid, customer_number, type, created, company, namesurname, taxoffice, taxnumber, ssn, executive, address, zipcode, country_id as country, state, state_id, city, town, phone, fax, email, web, risk, customergroups.id as group, customergroups.name as groupname');
			$builder->join('customergroups', 'customers.groupid = customergroups.id', 'left');
		} else if ($type == 'expenses') {

			$builder = $this->db->table('expenses');
			$builder->select('expenses.id as expid, expense_number, invoice_id, expensecat.name as category, internal, customers.id as custid, customer_number, customers.company as company, namesurname, staff.id as staffid, staff_number, staffname, expenses.date as date, amount, accounts.name as payment_account');
			$builder->join('customers', 'expenses.customer_id = customers.id', 'left');
			$builder->join('accounts', 'expenses.account_id = accounts.id', 'left');
			$builder->join('expensecat', 'expenses.category_id = expensecat.id', 'left');
			$builder->join('staff', 'expenses.staff_id = staff.id', 'left');
		} else if ($type == 'proposals') {

			$builder = $this->db->table('proposals');
			$builder->select('proposals.id as proposalid, proposal_number, staffname as assignedstaff, subject, created, opentill, status_id, sub_total');
			$builder->join('staff', 'proposals.assigned = staff.id', 'left');
		} else if ($type == 'deposits') {

			$builder = $this->db->table('deposits');
			$builder->select('title, depositcat.name as category, customers.id as custid, customer_number, company, namesurname, staff.id as staffid, staff_number, staffname, deposits.id as depid, deposit_number, accounts.name as payment_account, deposits.date as depdate, deposits.sub_total as total, status');
			$builder->join('accounts', 'deposits.account_id = accounts.id', 'left');
			$builder->join('customers', 'deposits.customer_id = customers.id', 'left');
			$builder->join('depositcat', 'deposits.category_id = depositcat.id', 'left');
			$builder->join('staff', 'deposits.staff_id = staff.id', 'left');
		} else if ($type == 'orders') {

			$builder = $this->db->table('orders');
			$builder->select('orders.id as orderid, order_number, subject, relation_type, orders.status_id as status_id, customers.id as customerid, customer_number, customers.company, namesurname, leads.id as leadid, lead_number, leads.name as leadname, orders.status_id as status, orders.date as orderdate, opentill, orders.sub_total as total, staff.id as staffid, staff_number, staffname');
			$builder->join('customers', 'orders.relation = customers.id', 'left');
			$builder->join('staff', 'orders.assigned = staff.id', 'left');
			$builder->join('leads', 'orders.relation = leads.id', 'left');
		} else if ($type == 'vendors') {

			$builder = $this->db->table('vendors');
			$builder->select('vendors.id as vendorid, company, vendor_number, vendors_groups.name as groupname, email, country_id');
			$builder->join('vendors_groups', 'vendors.groupid = vendors_groups.id', 'left');
		} else if ($type == 'purchases') {

			$builder = $this->db->table('purchases');
			$builder->select('purchases.id as id, purchase_number, serie, vendors.id as vendorid, vendor_number, vendors.company as vendorname, purchases.created as issuance_date, duedate, datepayment, purchases.sub_total as total, purchases.status_id as status_id');
			$builder->join('vendors', 'purchases.vendor_id = vendors.id', 'left');
		} else if ($type == 'contacts') {

			$builder = $this->db->table('contacts');
			$builder->select('contacts.id as contactid, name, surname, contacts.email as email, contacts.phone as phone, mobile, company, namesurname, customers.id as customerid, customer_number, created');
			$builder->join('customers', 'contacts.customer_id = customers.id', 'left');
		} else if ($type == 'tasks') {

			$builder = $this->db->table('tasks');
			$builder->select('projects.id as projectid, project_number, projects.name as projectname, tasks.id as taskid, task_number, tasks.name as taskname, startdate, duedate, priority, tasks.status_id as status_id, staffname');
			$builder->join('staff', 'tasks.assigned = staff.id', 'left');
			$builder->join('projects', 'tasks.relation = projects.id', 'left');
		} else if ($type == 'leads') {

			$builder = $this->db->table('leads');
			$builder->select('*, leadsstatus.name as statusname, staff.staffname as leadassigned, leadssources.name as sourcename, leads.name as leadname, leads.phone as leadphone, leads.id as id, leads.address as address');
			$builder->join('leadsstatus', 'leads.status = leadsstatus.id', 'left');
			$builder->join('leadssources', 'leads.source = leadssources.id', 'left');
			$builder->join('staff', 'leads.assigned_id = staff.id', 'left');
		} else if ($type == 'tickets') {

			$builder = $this->db->table('tickets');
			$builder->select('*, customers.type as type, customers.email as customeremail, customers.company as company, customers.namesurname as namesurname, departments.name as department, staff.staffname as staffmembername, staff.email as staffemail, contacts.name as contactname, contacts.surname as contactsurname, tickets.staff_id as stid, tickets.status_id as status_id, tickets.id as id');
			$builder->join('contacts', 'tickets.contact_id = contacts.id', 'left');
			$builder->join('customers', 'contacts.customer_id = customers.id', 'left');
			$builder->join('departments', 'tickets.department_id = departments.id', 'left');
			$builder->join('staff', 'tickets.staff_id = staff.id', 'left');
		} else if ($type == 'products') {

			$builder = $this->db->table('products');
			$builder->select('productcategories.name, product_number, productcategories.id as categoryid, products.id as id, products.code, products.productname, products.description, products.purchase_price, products.sale_price, products.stock, products.vat, products.status_id, products.productimage');
			$builder->join('productcategories', 'products.categoryid = productcategories.id', 'left');
		} else if ($type == 'staff') {

			$builder = $this->db->table('staff');
			$builder->select('*, departments.name as department, staff.id as id');
			$builder->join('departments', 'staff.department_id = departments.id', 'left');
		} else if ($type == 'projects') {

			$builder = $this->db->table('projects');
			$builder->select('*, customers.company as customercompany, customers.namesurname as individual, customers.address as customeraddress, projects.status_id as status, projects.id as id, projects.staff_id as staff_id');
			$builder->join('customers', 'projects.customer_id = customers.id', 'left');
		}


		$builder->orderBy($type . '.id', 'desc');

		/*** Get All Report ***/

		if ($period == '0') {
			$q = $builder->get()->getResultArray();
		}

		/*** Get Current Year Report ***/

		else if ($period == '1') {

			$year = date('Y');
			if ($type == 'contacts') {
				$builder->where('YEAR(customers.created)', $year);
			} else if ($type == 'tickets') {
				$builder->where('YEAR(' . $type . '.date)', $year);
			} else if ($type == 'products' || $type == 'staff') {
				$builder->where('YEAR(' . $type . '.createdat)', $year);
			} else {
				$builder->where('YEAR(' . $type . '.created)', $year);
			}

			$q = $builder->get()->getResultArray();
		}

		/*** Get Current Month Report ***/

		else if ($period == '2') {

			$month = date('m');
			if ($type == 'contacts') {
				$builder->where('MONTH(customers.created)', $month);
			} else if ($type == 'tickets') {
				$builder->where('MONTH(' . $type . '.date)', $month);
			} else if ($type == 'products' || $type == 'staff') {
				$builder->where('MONTH(' . $type . '.createdat)', $month);
			} else {
				$builder->where('MONTH(' . $type . '.created)', $month);
			}

			$q = $builder->get()->getResultArray();
		}

		/*** Get Current Week Report ***/

		else if ($period == '3') {

			if ($type == 'contacts') {
				$builder->where('WEEK(customers.created) = WEEK(CURRENT_DATE)');
			} else if ($type == 'tickets') {
				$builder->where('WEEK(' . $type . '.date) = WEEK(CURRENT_DATE)');
			} else if ($type == 'products' || $type == 'staff') {
				$builder->where('WEEK(' . $type . '.createdat) = WEEK(CURRENT_DATE)');
			} else {
				$builder->where('WEEK(' . $type . '.created) = WEEK(CURRENT_DATE)');
			}

			$q = $builder->get()->getResultArray();
		}

		/*** Get Last Year Report ***/

		else if ($period == '4') {

			if ($type == 'contacts') {
				$builder->where('YEAR(customers.created) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
			} else if ($type == 'tickets') {
				$builder->where('YEAR(' . $type . '.date) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
			} else if ($type == 'products' || $type == 'staff') {
				$builder->where('YEAR(' . $type . '.createdat) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
			} else {
				$builder->where('YEAR(' . $type . '.created) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
			}

			$q = $builder->get()->getResultArray();
		}

		/*** Get Last Month Report ***/

		else if ($period == '5') {

			if ($type == 'contacts') {
				$builder->where('MONTH(customers.created) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
			} else if ($type == 'tickets') {
				$builder->where('MONTH(' . $type . '.date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
			} else if ($type == 'products' || $type == 'staff') {
				$builder->where('MONTH(' . $type . '.createdat) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
			} else {
				$builder->where('MONTH(' . $type . '.created) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
			}

			$q = $builder->get()->getResultArray();
		}

		/*** Get Last Week Report ***/

		else if ($period == '6') {

			if ($type == 'contacts') {
				$builder->where('WEEK(customers.created) = WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
			} else if ($type == 'tickets') {
				$builder->where('WEEK(' . $type . '.date) = WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
			} else if ($type == 'products' || $type == 'staff') {
				$builder->where('WEEK(' . $type . '.createdat) = WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
			} else {
				$builder->where('WEEK(' . $type . '.created) = WEEK(CURRENT_DATE - INTERVAL 1 WEEK)');
			}

			$q = $builder->get()->getResultArray();
		}


		/*** Get Custom Report ***/

		else if ($period == '7') {

			if ($type == 'contacts') {
				$builder->where('customers.created >=', $from);
				$builder->where('customers.created <=', $to);
			} else if ($type == 'tickets') {
				$builder->where($type . '.date >=', $from);
				$builder->where($type . '.date <=', $to);
			} else if ($type == 'products' || $type == 'staff') {
				$builder->where($type . '.createdat >=', $from);
				$builder->where($type . '.createdat <=', $to);
			} else {
				$builder->where($type . '.created >=', $from);
				$builder->where($type . '.created <=', $to);
			}

			$q = $builder->get()->getResultArray();
		}


		$filename = '';

		$export = array();

		if ($type == 'invoices') {

			foreach ($q as $data) {

				$customer = get_number('customers', $data['custid'], 'customer', 'customer');

				$export[] = array(

					lang2('invoice') => $data['invoice_number'] ? $data['invoice_number'] : $data['invid'],

					lang2('serie') => $data['serie'],

					lang2('issuance_date') => $data['issuance_date'],

					lang2('duedate') => $data['duedate'],

					lang2('customer') => $data['company'] ? $data['company'] . ' (' . $customer . ')' : $data['custname'] . '(' . $customer . ')',

					lang2('staff') => $data['staffname'] . ' (' . get_number('staff', $data['staffid'], 'staff', 'staff') . ')',

					lang2('status') => $data['status'],

					lang2('tax') => $data['total_tax'],

					lang2('total') => $data['total'],

					lang2('default_payment_method') => $data['default_payment_method']

				);
			}

			$filename = lang2('invoices');
		} else if ($type == 'customers') {

			$appconfig = get_appconfig();

			foreach ($q as $data) {

				$export[] = array(

					lang2('customer') => $data['customer_number'] ? $data['customer_number'] : $data['custid'],

					lang2('type') => $data['type'],

					lang2('created') => $data['created'],

					lang2('companyname') => $data['company'] ? $data['company'] : $data['namesurname'],

					$appconfig['tax_label'] . ' ' . lang2('taxoffice') => $data['taxoffice'],

					$appconfig['tax_label'] . ' ' . lang2('vatnumber') => $data['taxnumber'],

					lang2('ssn') => $data['ssn'],

					lang2('executiveupdate') => $data['executive'],

					lang2('address') => $data['address'],

					lang2('zipcode') => $data['zipcode'],

					lang2('country') => get_country($data['country']),

					lang2('state') => get_state_name($data['state'], $data['state_id']),

					lang2('city') => $data['city'],

					lang2('town') => $data['town'],

					lang2('phone') => $data['phone'],

					lang2('fax') => $data['fax'],

					lang2('email') => $data['email'],

					lang2('web') => $data['web'],

					lang2('riskstatus') => $data['risk'],

					lang2('group') => $data['group'] . ' ' . $data['groupname'],

				);
			}

			$filename = lang2('customers');
		} else if ($type == 'expenses') {

			foreach ($q as $data) {

				if ($data['invoice_id'] == NULL) {

					$billstatus = lang2('notbilled');
				} else {

					$billstatus = lang2('billed');
				}

				if ($data['internal'] == '1') {

					$customer = get_number('staff', $data['staffid'], 'staff', 'staff');

					$customername = $data['staffname'];

					$billstatus = lang2('internal');
				} else {

					$customer = get_number('customers', $data['custid'], 'customer', 'customer');

					$customername = $data['company'] ? $data['company'] : $data['namesurname'];
				}

				$export[] = array(

					lang2('expense') => $data['expense_number'] ? $data['expense_number'] : $data['expid'],

					lang2('category') => $data['category'],

					lang2('type') => ($data['internal'] == '1') ? lang2('internal') : '',

					lang2('customer') => $customername . ' (' . $customer . ')',

					lang2('status') => $billstatus,

					lang2('date') => $data['date'],

					lang2('amount') => $data['amount'],

					lang2('payment_account') => $data['payment_account'],

				);
			}

			$filename = lang2('expenses');
		} else if ($type == 'proposals') {

			foreach ($q as $data) {

				switch ($data['status_id']) {

					case '0':

						$status = lang2('quote') . ' ' . lang2('request');

						break;

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

				$export[] = array(

					lang2('proposal') => $data['proposal_number'] ? $data['proposal_number'] : $data['proposalid'],

					lang2('assignedstaff') => $data['assignedstaff'],

					lang2('subject') => $data['subject'],

					lang2('created') => $data['created'],

					lang2('opentill') => $data['opentill'],

					lang2('status') => $status,

					lang2('total') => $data['sub_total'],

				);
			}

			$filename = lang2('proposals');
		} else if ($type == 'deposits') {

			foreach ($q as $data) {

				if ($data['status'] == '1') {

					$billstatus = lang2('paid') and $color = 'success';
				} else if ($data['status'] == '0') {

					$billstatus = lang2('unpaid') and $color = 'danger';
				} else {

					$billstatus = lang2('internal') and $color = 'success';
				}

				if ($data['status'] == '2') {

					$customer = get_number('staff', $data['staffid'], 'staff', 'staff');

					$customername = $data['staffname'];
				} else {

					$customer = get_number('customers', $data['custid'], 'customer', 'customer');

					$customername = $data['company'] ? $data['company'] : $data['namesurname'];
				}

				$export[] = array(

					lang2('deposits') => get_number('deposits', $data['depid'], 'deposit', 'deposit'),

					lang2('title') => $data['title'],

					lang2('category') => $data['category'],

					lang2('customer') => $customername . ' (' . $customer . ')',

					lang2('status') => $billstatus,

					lang2('date') => $data['depdate'],

					lang2('amount') => $data['total'],

					lang2('create') => $data['staffname'],

					lang2('payment_account') => $data['payment_account'],

				);
			}

			$filename = lang2('deposits');
		} else if ($type == 'orders') {

			foreach ($q as $data) {

				switch ($data['status_id']) {

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

				if ($data['relation_type'] == 'customer') {

					$customer_number = get_number('customers', $data['customerid'], 'customer', 'customer');

					$customer = $data['company'] ? $data['company'] : $data['namesurname'];
				} else {

					$customer_number = get_number('leads', $data['leadid'], 'lead', 'lead');

					$customer = $data['leadname'];
				}

				$export[] = array(

					lang2('order') => get_number('orders', $data['orderid'], 'order', 'order'),

					lang2('subject') => $data['subject'],

					lang2('customer') . '/' . lang2('lead') => $customer . ' (' . $customer_number . ')',

					lang2('assigned') => $data['staffname'],

					lang2('status') => $status,

					lang2('issuance_date') => $data['orderdate'],

					lang2('opentill') => $data['opentill'],

					lang2('total') => $data['total']

				);
			}

			$filename = lang2('orders');
		} else if ($type == 'vendors') {

			foreach ($q as $data) {

				// Calcular o total de faturas não pagas
				$total_unpaid_invoice_amount = $this->db->table('purchases')
					->selectSum('total')
					->where('status_id', 3)
					->where('vendor_id', $data['vendorid'])
					->get()
					->getRow()
					->total;

				// Calcular o total de faturas pagas
				$total_paid_invoice_amount = $this->db->table('purchases')
					->selectSum('total')
					->where('status_id', 2)
					->where('vendor_id', $data['vendorid'])
					->get()
					->getRow()
					->total;

				// Calcular o total pago
				$total_paid_amount = $this->db->table('payments')
					->selectSum('amount')
					->where('transactiontype', 0)
					->where('vendor_id', $data['vendorid'])
					->get()
					->getRow()
					->amount;


				$export[] = array(

					lang2('vendor') => get_number('vendors', $data['vendorid'], 'vendor', 'vendor'),

					lang2('name') => $data['company'],

					lang2('group') . ' ' . lang2('name') => $data['groupname'],

					lang2('email') => $data['email'],

					lang2('country') => get_country($data['country_id']),

					lang2('amount') => $total_unpaid_invoice_amount - $total_paid_amount + $total_paid_invoice_amount,

				);
			}

			$filename = lang2('vendors');
		} else if ($type == 'purchases') {

			foreach ($q as $data) {

				$vendorname = $data['vendorname'];

				$totalx = $data['total'];

				// Calcular o total pago
				// Calcular o total pago
				$paytotal = $this->db->table('payments')
					->selectSum('amount')
					->where('purchase_id', $data['id'])
					->get()
					->getRow()
					->amount;

				// Calcular o saldo
				$balance = $totalx - $paytotal;

				// Determinar o status das compras
				if ($balance > 0) {
					$purchasesstatus = '';
				} else {
					$purchasesstatus = lang2('paidinv');
				}

				if ($paytotal < $data['total'] && $paytotal > 0) {
					if ($data['status_id'] == 3) {
						$purchasesstatus = lang2('partial');
					} else {
						$purchasesstatus = lang2('partial');
					}
				} else {
					if ($data['status_id'] == 3) {
						$purchasesstatus = lang2('unpaid');
					}
				}


				if ($data['status_id'] == 1) {

					$purchasesstatus = lang2('draft');
				}

				if ($data['status_id'] == 4) {

					$purchasesstatus = lang2('cancelled');
				}

				$export[] = array(

					lang2('purchase') => get_number("purchases", $data['id'], 'purchase', 'purchase'),

					lang2('serie') => $data['serie'],

					lang2('vendor') => $vendorname . ' (' . get_number('vendors', $data['vendorid'], 'vendor', 'vendor') . ')',

					lang2('issuance_date') => $data['issuance_date'],

					lang2('duedate') => $data['duedate'],

					lang2('amount') => $data['total'],

					lang2('status') => $purchasesstatus,

				);
			}

			$filename = lang2('purchases');
		} else if ($type == 'contacts') {

			foreach ($q as $data) {

				$customer = $data['company'] ? $data['company'] : $data['namesurname'];

				$export[] = array(

					lang2('name') => $data['name'] . ' ' . $data['surname'],

					lang2('email') => $data['email'],

					lang2('contactmobile') . '/' . lang2('phone') => $data['mobile'] ? $data['mobile'] : $data['phone'],

					lang2('customer') => $customer . ' (' . get_number('customers', $data['customerid'], 'customer', 'customer') . ')',

				);
			}

			$filename = lang2('contacts');
		} else if ($type == 'tasks') {

			foreach ($q as $data) {

				$project = $data['projectname'];

				$task = $data['taskname'];

				switch ($data['status_id']) {

					case '1':

						$status = lang2('open');

						break;

					case '2':

						$status = lang2('inprogress');

						break;

					case '3':

						$status = lang2('waiting');

						break;

					case '4':

						$status = lang2('complete');

						break;

					case '5':

						$status = lang2('cancelled');

						break;

					default:

						$status = lang2('open');

						break;
				};

				switch ($data['priority']) {

					case '1':

						$priority = lang2('low');

						break;

					case '2':

						$priority = lang2('medium');

						break;

					case '3':

						$priority = lang2('high');

						break;

					default:

						$priority = lang2('medium');

						break;
				};

				$export[] = array(

					lang2('task') => $task . ' (' . get_number('tasks', $data['taskid'], 'task', 'task'),

					lang2('project') => $project . ' (' . get_number('projects', $data['projectid'], 'project', 'project') . ')',

					lang2('startdate') => date(get_dateFormat(), strtotime($data['startdate'])),

					lang2('duedate') => date(get_dateFormat(), strtotime($data['duedate'])),

					lang2('priority') => $priority,

					lang2('status') => $status,

					lang2('assigned') => $data['staffname'],

				);
			}

			$filename = lang2('tasks');
		} else if ($type == 'leads') {

			foreach ($q as $data) {

				$lead = $data['leadname'];

				$export[] = array(

					lang2('lead') => $lead . ' (' . get_number('leads', $data['id'], 'lead', 'lead') . ')',

					lang2('companyname') => $data['company'],

					lang2('phone') => $data['leadphone'],

					lang2('address') => $data['address'],

					lang2('zipcode') => $data['zip'],

					lang2('country') => get_country($data['country_id']),

					lang2('state') => get_state_name($data['state'], $data['state_id']),

					lang2('city') => $data['city'],

					lang2('email') => $data['email'],

					lang2('web') => $data['website'],

					lang2('status') => $data['statusname'] ? $data['statusname'] : '',

					lang2('source') => $data['sourcename'] ? $data['sourcename'] : '',

					lang2('assigned') => $data['leadassigned'],

				);
			}

			$filename = lang2('leads');
		} else if ($type == 'tickets') {

			foreach ($q as $data) {

				if ($data['type'] == 0) {

					$customer = $data['company'];
				} else {

					$customer = $data['namesurname'];
				}

				switch ($data['priority']) {

					case '1':

						$priority = lang2('low');

						break;

					case '2':

						$priority = lang2('medium');

						break;

					case '3':

						$priority = lang2('high');

						break;

					default:

						$priority = lang2('medium');

						break;
				};

				switch ($data['status_id']) {

					case '1':

						$status = lang2('open');

						break;

					case '2':

						$status = lang2('inprogress');

						break;

					case '3':

						$status = lang2('answered');

						break;

					case '4':

						$status = lang2('closed');

						break;

					default:

						$status = lang2('open');

						break;
				};

				$export[] = array(

					lang2('ticket') => get_number('tickets', $data['id'], 'ticket', 'ticket'),

					lang2('subject') => $data['subject'],

					lang2('customer') => $customer . '(' . (get_number('tickets', $data['customer_id'], 'ticket', 'ticket')) . ')',

					lang2('contact') => '' . $data['contactname'] . ' ' . $data['contactsurname'] . '',

					lang2('department') => $data['department'],

					lang2('priority') => $priority,

					lang2('status') => $status,

					lang2('assigned') => $data['staffmembername'],

					lang2('lastreply') => $data['lastreply'] ? date(get_dateTimeFormat(), strtotime($data['lastreply'])) : '',

				);
			}

			$filename = lang2('tickets');
		} else if ($type == 'products') {

			$appconfig = get_appconfig();

			foreach ($q as $data) {

				$export[] = array(

					lang2('product') => get_number('products', $data['id'], 'product', 'product'),

					lang2('productcode') => $data['code'],

					lang2('name') => $data['productname'],

					lang2('description') => $data['description'],

					lang2('category') => $data['name'],

					lang2('purchaseprice') => $data['purchase_price'],

					lang2('salesprice') => $data['sale_price'],

					$appconfig['tax_label'] => $data['vat'],

					lang2('instock') => $data['stock'],

				);
			}

			$filename = lang2('products');
		} else if ($type == 'staff') {

			foreach ($q as $data) {

				if ($data['admin'] == '1') {

					$type = lang2('admin');
				} else if ($data['staffmember'] == '1' && $data['other'] == null) {

					$type = lang2('staff');
				} else {

					$type = lang2('other');
				}

				$export[] = array(

					lang2('staff') => get_number('staff', $data['id'], 'staff', 'staff'),

					lang2('name') => $data['staffname'],

					lang2('department') => $data['department'],

					lang2('phone') => $data['phone'],

					lang2('email') => $data['email'],

					lang2('type') => $type,

					lang2('address') => $data['address'],

				);
			}

			$filename = lang2('staff');
		} else if ($type == 'projects') {

			foreach ($q as $data) {

				switch ($data['status']) {

					case '1':

						$status = lang2('notstarted');

						break;

					case '2':

						$status = lang2('started');

						break;

					case '3':

						$status = lang2('percentage');

						break;

					case '4':

						$status = lang2('cancelled');

						break;

					case '5':

						$status = lang2('completed');

						break;

					default:

						$status = lang2('started');

						break;
				}

				if ($data['template'] == '1') {

					$customer = lang2('template');
				} else {

					$customer = ($data['customercompany']) ? $data['customercompany'] : $data['namesurname'];
				}

				$members = $this->Projects_Model->get_members_index($data['id']);

				$staff = array();

				foreach ($members as $member) {

					$staff[] = get_number('staff', $member['staff_id'], 'staff', 'staff');
				}

				$export[] = array(

					lang2('project') => get_number('projects', $data['id'], 'project', 'project'),

					lang2('name') => $data['name'],

					lang2('customer') => $customer,

					lang2('project_start_date') => date(get_dateFormat(), strtotime($data['start_date'])),

					lang2('project_end_date') => date(get_dateFormat(), strtotime($data['deadline'])),

					lang2('project_value') => $data['projectvalue'],

					lang2('status') => $status,

					lang2('project') . ' ' . lang2('members') => implode(',', $staff),

				);
			}

			$filename = lang2('projects');
		}

		header("Content-type: application/csv");

		header("Content-Disposition: attachment; filename=\"$filename" . ".csv\"");

		header("Pragma: no-cache");

		header("Expires: 0");

		$report = fopen('php://output', 'w');

		$field = array_keys($export[0]);

		$lang = array();

		foreach ($field as $lg) {

			$lang[] = $lg;
		}

		fputcsv($report,  $lang);

		foreach ($export as $row) {

			fputcsv($report, $row);
		}

		fclose($report);
	}



	function generatePdf()
	{

		$type = request()->getPost('type');
		$period = request()->getPost('period');
		$from = request()->getPost('from');
		$to = request()->getPost('to');

		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '2048M');

		if (!is_dir('uploads/files/reports')) {
			mkdir('./uploads/files/reports', 0777, true);
		}

		$data = array();

		if ($type == 'invoices') {
			$data['results'] = $this->Report_Model->get_all_invoices();
		} else if ($type == 'expenses') {

			$data['results'] = $this->Report_Model->get_all_expenses();
		} else if ($type == 'customers') {

			$data['results'] = $this->Report_Model->get_all_customers();
		} else if ($type == 'proposals') {

			$data['results'] = $this->Report_Model->get_all_proposals();
		} else if ($type == 'deposits') {

			$data['results'] = $this->Report_Model->get_all_deposits();
		} else if ($type == 'orders') {

			$data['results'] = $this->Report_Model->get_all_orders();
		} else if ($type == 'vendors') {

			$data['results'] = $this->Report_Model->get_all_vendors();
		} else if ($type == 'purchases') {

			$data['results'] = $this->Report_Model->get_all_purchases();
		} else if ($type == 'contacts') {

			$data['results'] = $this->Report_Model->get_all_contacts();
		} else if ($type == 'tasks') {

			$data['results'] = $this->Report_Model->get_all_tasks();
		} else if ($type == 'leads') {

			$data['results'] = $this->Report_Model->get_all_leads();
		} else if ($type == 'tickets') {

			$data['results'] = $this->Report_Model->get_all_tickets();
		} else if ($type == 'products') {

			$data['results'] = $this->Report_Model->get_all_products();
		} else if ($type == 'staff') {

			$data['results'] = $this->Report_Model->get_all_staff();
		} else if ($type == 'projects') {

			$data['results'] = $this->Report_Model->get_all_projects();
		}

		$filename = $type . '.pdf';

		$data['type'] = $type;

		$html = view('report/pdf', $data, TRUE);
		$this->load->library('dom');
		$this->dompdf->loadHtml($html);
		$this->dompdf->set_option('isRemoteEnabled', TRUE);
		$this->dompdf->setPaper('A4', 'landscape');
		$this->dompdf->render();

		$output = $this->dompdf->output();
		file_put_contents('uploads/files/reports/' . $filename . '', $output);

		if ($output) {
			$result = array(
				'success' => true,
				'file_name' => $filename,
			);
			return response()->setJSON($result);
		}
	}
}
