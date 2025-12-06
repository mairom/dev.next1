<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Vendors extends BaseController
{

	function __construct()
	{
		parent::loadModels();
		$path = request()->getUri()->getSegment(1);
		if (!$this->Privileges_Model->has_privilege($path)) {
			session()->setFlashdata('ntf3', '' . lang2('you_dont_have_permission'));
			return redirect()->to('panel/');
			die;
		}
	}



	function index()
	{

		$data['title'] = lang2('vendors');

		$data['vendors'] = $this->Vendors_Model->get_all_vendors();

		$data['settings'] = $this->Settings_Model->get_settings_ciuis();

		return view('vendors/index', $data);
	}



	function create()
	{

		if ($this->Privileges_Model->check_privilege('vendors', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$company = request()->getPost('name');

				$email = request()->getPost('email');

				$groupid = request()->getPost('groupid');

				$hasError = false;

				$data['message'] = '';

				if ($company == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('vendor') . ' ' . lang2('name');
				} else if ($groupid == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('vendor') . ' ' . lang2('group');
				} else if ($email == '') {

					$hasError = true;

					$data['message'] = lang2('invalidmessage') . ' ' . lang2('vendor') . ' ' . lang2('email');
				}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$appconfig = get_appconfig();

					$params = array(

						'created' => date('Y-m-d H:i:s'),

						'company' => request()->getPost('name'),

						'groupid' => request()->getPost('groupid'),

						'ssn' => request()->getPost('ssn'),

						//'executive' => request()->getPost( 'executive' ),

						'address' => request()->getPost('address'),

						'phone' => request()->getPost('phone'),

						'email' => request()->getPost('email'),

						'fax' => request()->getPost('fax'),

						'web' => request()->getPost('web'),

						//	'taxoffice' => request()->getPost( 'taxoffice' ),

						//	'taxnumber' => request()->getPost( 'taxnumber' ),

						'country_id' => request()->getPost('country_id'),

						'state' => request()->getPost('state'),

						'city' => request()->getPost('city'),

						'town' => request()->getPost('town'),

						'zipcode' => request()->getPost('zipcode'),

						'staff_id' => session()->get('usr_id'),
						'id_company' => session()->id_company,
						'vendor_status_id' => '1',

					);

					$vendors_id = $this->Vendors_Model->add_vendors($params);

					$data['success'] = true;

					$data['id'] = $vendors_id;

					if ($appconfig['vendor_series']) {

						$vendor_number = $appconfig['vendor_series'];

						$vendor_number = $vendor_number + 1;

						$this->Settings_Model->increment_series('vendor_series', $vendor_number);
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



	function vendor($id)
	{

		if ($this->Privileges_Model->check_privilege('vendors', 'all')) {
			$vendor = $this->Vendors_Model->get_vendor_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('vendors', 'own')) {
			$vendor = $this->Vendors_Model->get_vendor_by_privileges($id, session()->usr_id);
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('vendors'));
		}

		if ($vendor) {

			$data['title'] = lang2('vendor') . ' ' . get_number('vendors', $id, 'vendor', 'vendor');

			$data['ycr'] = $this->Report_Model->ycr();

			if (isset($vendor['id'])) {

				if (isset($_POST) && count($_POST) > 0) {

					if ($this->Privileges_Model->check_privilege('vendors', 'edit')) {

						$company = request()->getPost('name');

						$email = request()->getPost('email');

						$groupid = request()->getPost('groupid');

						$hasError = false;

						$data['message'] = '';

						if ($company == '') {

							$hasError = true;

							$data['message'] = lang2('invalidmessage') . ' ' . lang2('vendor') . ' ' . lang2('name');
						} else if ($groupid == '') {

							$hasError = true;

							$data['message'] = lang2('invalidmessage') . ' ' . lang2('vendor') . ' ' . lang2('group');
						} else if ($email == '') {

							$hasError = true;

							$data['message'] = lang2('invalidmessage') . ' ' . lang2('vendor') . ' ' . lang2('email');
						}

						if ($hasError) {

							$data['success'] = false;

							return response()->setJSON($data);
						}

						if (!$hasError) {

							$appconfig = get_appconfig();

							$params = array(
								'company' => request()->getPost('name'),
								'groupid' => request()->getPost('groupid'),
								'ssn' => request()->getPost('ssn'),
								//	'executive' => request()->getPost( 'executive' ),
								'address' => request()->getPost('address'),
								'phone' => request()->getPost('phone'),
								'email' => request()->getPost('email'),
								'fax' => request()->getPost('fax'),
								'web' => request()->getPost('web'),
								//	'taxoffice' => request()->getPost( 'taxoffice' ),
								//	'taxnumber' => request()->getPost( 'taxnumber' ),
								'country_id' => request()->getPost('country_id'),
								'state' => request()->getPost('state'),
								'city' => request()->getPost('city'),
								'town' => request()->getPost('town'),
								'zipcode' => request()->getPost('zipcode'),
								'staff_id' => session()->get('usr_id'),
								'risk' => request()->getPost('risk'),
								'vendor_status_id' => request()->getPost('status_id'),
								'vendor_number' => get_number('vendors', $id, 'vendor', 'vendor'),

							);
							$this->Vendors_Model->update_vendors($id, $params);
							$data['success'] = true;
							$data['message'] = lang2('vendorsupdated');
							return response()->setJSON($data);
						}
					} else {
						$data['success'] = false;
						$data['message'] = lang2('you_dont_have_permission');
						return response()->setJSON($data);
					}
				} else {
					$data['vendors'] = $this->Vendors_Model->get_vendors($id);
					return view('vendors/vendor', $data);
				}
			} else {
				return redirect()->to(base_url('vendors'));
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('vendors'));
		}
	}



	function groups()
	{

		if ($this->Privileges_Model->check_privilege('vendors', 'all')) {

			$data = $this->Vendors_Model->get_groups();
		} else if ($this->Privileges_Model->check_privilege('vendors', 'own')) {

			$data = $this->Vendors_Model->get_groups(session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('vendors'));
		}

		return response()->setJSON($data);
	}



	function add_group()
	{

		if ($this->Privileges_Model->check_privilege('vendors', 'create')) {

			if (isset($_POST)) {

				$params = array(

					'name' => request()->getPost('name')

				);

				$this->db->table('vendors_groups')->insert( $params);

				$id = $this->db->insertID();

				if ($id) {

					$data['success'] = true;

					$data['message'] = lang2('vendor') . ' ' . lang2('group') . ' ' . lang2('createmessage');
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

		if ($this->Privileges_Model->check_privilege('vendors', 'edit')) {

			$data['group'] = $this->Vendors_Model->get_group($id);

			if (isset($data['group']['id'])) {

				if (isset($_POST) && count($_POST) > 0) {

					$params = array(

						'name' => request()->getPost('name'),

					);

					$this->Vendors_Model->update_group($id, $params);

					$data['success'] = true;

					$data['message'] = lang2('vendor') . ' ' . lang2('group') . ' ' . lang2('updatemessage');
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

		if ($this->Privileges_Model->check_privilege('vendors', 'delete')) {

			$group = $this->Vendors_Model->get_group($id);

			if (isset($group['id'])) {

				if ($this->Vendors_Model->check_group($id) == 0) {

					$this->Vendors_Model->remove_group($id);

					$data['success'] = true;

					$data['message'] = lang2('vendor') . ' ' . lang2('group') . ' ' . lang2('deletemessage');
				} else {

					$data['success'] = false;

					$data['message'] = $data['message'] = lang2('group') . ' ' . lang2('is_linked') . ' ' . lang2('with') . ' ' . lang2('vendor') . ', ' . lang2('so') . ' ' . lang2('cannot_delete') . ' ' . lang2('group');
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function get_vendor_groups()
	{

		$groups = $this->Vendors_Model->get_vendor_groups();

		$data_categories = array();

		foreach ($groups as $group) {

			$data_categories[] = array(

				'name' => $group['name'],

				'id' => $group['id'],

			);
		};

		return response()->setJSON($data_categories);
	}



	function remove($id)
	{

		if ($this->Privileges_Model->check_privilege('vendors', 'all')) {

			$vendor = $this->Vendors_Model->get_vendor_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('vendors', 'own')) {

			$vendor = $this->Vendors_Model->get_vendor_by_privileges($id, session()->usr_id);
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($data);
		}

		if ($vendor) {

			if ($this->Privileges_Model->check_privilege('vendors', 'delete')) {

				if (isset($vendor['id'])) {

					$this->Vendors_Model->delete_vendors($id, get_number('vendors', $id, 'vendor', 'vendor'));

					$data['success'] = true;

					$data['message'] = lang2('vendor') . ' ' . lang2('deletemessage');

					return response()->setJSON($data);
				} else {

					show_error('vendor not deleted');
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('vendors'));
		}
	}



	function get_vendor($id)
	{

		$vendor = array();

		if ($this->Privileges_Model->check_privilege('vendors', 'all')) {

			$vendor = $this->Vendors_Model->get_vendor_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('vendors', 'own')) {

			$vendor = $this->Vendors_Model->get_vendor_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('vendors'));
		}

		if ($vendor) {

			$this->model('Vendors_Model');
			$country = get_country($vendor['country_id']);
			$state = get_state_name('', $vendor['state']);

			// Calcular o total de compras com status_id igual a 2
			$netrevenue = $this->db->table('purchases')
				->selectSum('total')
				->where('status_id', 2)
				->where('vendor_id', $vendor['id'])
				->get()
				->getRow()
				->total;

			// Calcular o total de compras com status_id diferente de 1 e 4
			$grossrevenue = $this->db->table('purchases')
				->selectSum('total')
				->where('status_id !=', 1)
				->where('status_id !=', 4)
				->where('vendor_id', $vendor['id'])
				->get()
				->getRow()
				->total;



				$purchases = $this->db->table('purchases')
				->where('vendor_id', $vendor['id'])
				->get()
				->getResultArray();
			

			$data_customerdetail = array(

				'id' => $vendor['id'],
				'created' => $vendor['created'],
				'staff_id' => $vendor['staff_id'],
				'name' => $vendor['company'],
				//'taxoffice' => $vendor[ 'taxoffice' ],
				//'taxnumber' => $vendor[ 'taxnumber' ],
				'ssn' => $vendor['ssn'],
				//'executive' => $vendor[ 'executive' ],
				'address' => $vendor['address'],
				'zipcode' => $vendor['zipcode'],
				'country_id' => $vendor['country_id'],
				'state' => $vendor['state'],
				'state_name' => $state,
				'purchases' => $purchases,
				'city' => $vendor['city'],
				'town' => $vendor['town'],
				'phone' => $vendor['phone'],
				'fax' => $vendor['fax'],
				'email' => $vendor['email'],
				'web' => $vendor['web'],
				'risk' => intval($vendor['risk']),
				'country' => $country,
				'group_id' => $vendor['groupid'],
				'netrevenue' => $netrevenue,
				'grossrevenue' => $grossrevenue,
				'vendor_number' => $vendor['vendor_number'],
				'vendor_status_id' => ($vendor['vendor_status_id'] == '1') ? true : false,
			);

			return response()->setJSON($data_customerdetail);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('vendors'));
		}
	}



	function get_vendors()
	{

		$vendors = array();

		if ($this->Privileges_Model->check_privilege('vendors', 'all')) {

			$vendors = $this->Vendors_Model->get_all_vendors_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('vendors', 'own')) {

			$vendors = $this->Vendors_Model->get_all_vendors_by_privileges(session()->usr_id);
		}

		$data_customers = array();

		foreach ($vendors as $vendor) {

			// Total de faturas não pagas
			$total_unpaid_invoice_amount = $this->db->table('purchases')
				->selectSum('total')
				->where('status_id', 3)
				->where('vendor_id', $vendor['id'])
				->get()
				->getRow()
				->total;

			// Total de faturas pagas
			$total_paid_invoice_amount = $this->db->table('purchases')
				->selectSum('total')
				->where('status_id', 2)
				->where('vendor_id', $vendor['id'])
				->get()
				->getRow()
				->total;

			// Total pago
			$total_paid_amount = $this->db->table('payments')
				->selectSum('amount')
				->where('transactiontype', 0)
				->where('vendor_id', $vendor['id'])
				->get()
				->getRow()
				->amount;


			$country = get_country($vendor['country_id']);

			$data_customers[] = array(

				'id' => $vendor['id'],

				'vendor' => $vendor['id'],

				'name' => $vendor['company'],

				'address' => $vendor['address'],

				'group_name' => $vendor['name'],

				'email' => $vendor['email'],

				'balance' => $total_unpaid_invoice_amount - $total_paid_amount + $total_paid_invoice_amount,

				'phone' => $vendor['phone'],

				'' . lang2('filterbycountry') . '' => $country,

				'vendor_number' => get_number('vendors', $vendor['id'], 'vendor', 'vendor'),

			);
		};

		return response()->setJSON($data_customers);
	}
}
