<?php

namespace App\Controllers;

defined('BASEPATH') or exit('No direct script access allowed');

class Products extends BaseController
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

		$data['title'] = lang2('products');

		return view('products/index', $data);
	}



	function get_content($type, $id)
	{

		if ($type == 'index') {
		}

		if ($type == 'product') {

			$data['title'] = lang2('product');

			$data['product'] = $this->Products_Model->get_product_by_id($id);

			$html = view('products/product', $data);
		}

		echo $html;
	}



	function create()
	{

		if ($this->Privileges_Model->check_privilege('products', 'create')) {

			if (isset($_POST) && count($_POST) > 0) {

				$code = request()->getPost('code');
				$name = request()->getPost('name');
				$categoryid = request()->getPost('categoryid');
				$description = request()->getPost('description') ? request()->getPost('description') : null;
				$vendor_id = request()->getPost('vendor_id');
				$purchaseprice = request()->getPost('purchaseprice');
				$sale_price = request()->getPost('saleprice');
				$stock = request()->getPost('stock');
				//	$vat = request()->getPost('tax');
				$hasError = false;

				$data['message'] = '';

				if ($name == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('productname');
				}
				//else if ($categoryid == '') {
				//	$hasError = true;
				//	$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('productcategory');
				//}
				//else if ($purchaseprice == '') {
				//	$hasError = true;
				//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('purchaseprice');
				//}
				else if ($sale_price == '') {
					$hasError = true;
					$data['message'] = lang2('invalidmessage') . ' ' . lang2('salesprice');
				}
				//else if ($vendor_id == '') {
				//	$hasError = true;
				//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('vendor');
				//}
				//else if ($description == '') {
				//	$hasError = true;
				//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
				//}

				if ($hasError) {

					$data['success'] = false;

					return response()->setJSON($data);
				}

				if (!$hasError) {

					$appconfig = get_appconfig();

					$params = array(
						'code' => $code,
						'categoryid' => $categoryid,
						'productname' => $name,
						'description' => $description,
						'purchase_price' => $purchaseprice,
						'sale_price' => $sale_price,
						'stock' => $stock,
						//'vat' => $vat,
						'product_created_by' => session()->usr_id,
						'vendor_id' => $vendor_id,
						"id_company" => session()->id_company,
						'created' => date('Y-m-d')
					);

					$products_id = $this->Products_Model->add_products($params);

					if (request()->getPost('custom_fields')) {

						$custom_fields = array(

							'custom_fields' => request()->getPost('custom_fields')

						);

						$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'product', $products_id);
					}

					$data['success'] = true;

					$data['message'] = lang2('product') . ' ' . lang2('createmessage');

					$data['id'] = $products_id;

					if ($appconfig['product_series']) {

						$product_number = $appconfig['product_series'];

						$product_number = $product_number + 1;

						$this->Settings_Model->increment_series('product_series', $product_number);
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



	function categories()
	{

		if ($this->Privileges_Model->check_privilege('products', 'all')) {

			$data = $this->Products_Model->get_categories();
		} else if ($this->Privileges_Model->check_privilege('products', 'own')) {

			$data = $this->Products_Model->get_categories(session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('products'));
		}

		return response()->setJSON($data);
	}



	function add_category()
	{

		if ($this->Privileges_Model->check_privilege('products', 'create')) {

			if (isset($_POST)) {

				$params = array(

					'name' => request()->getPost('name'),
					"id_company" => session()->id_company

				);

				$this->db->table('productcategories')->insert($params);

				$id = $this->db->insertID();

				if ($id) {

					$data['success'] = true;

					$data['message'] = lang2('productcategory') . ' ' . lang2('createmessage');
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function update_category($id)
	{

		if ($this->Privileges_Model->check_privilege('products', 'edit')) {

			$data['category'] = $this->Products_Model->get_category($id);

			if (isset($data['category']['id'])) {

				if (isset($_POST) && count($_POST) > 0) {

					$params = array(

						'name' => request()->getPost('name'),

					);

					$this->Products_Model->update_category($id, $params);

					$data['success'] = true;

					$data['message'] = lang2('productcategory') . ' ' . lang2('updatemessage');
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function remove_category($id)
	{

		if ($this->Privileges_Model->check_privilege('products', 'delete')) {

			$category = $this->Products_Model->get_category($id);

			if (isset($category['id'])) {

				if ($this->Products_Model->check_category($id) == 0) {

					$this->Products_Model->remove_category($id);

					$data['success'] = true;

					$data['message'] = lang2('productcategory') . ' ' . lang2('deletemessage');

					return response()->setJSON($data);
				} else {

					$data['success'] = false;

					$data['message'] = $data['message'] = lang2('category') . ' ' . lang2('is_linked') . ' ' . lang2('with') . ' ' . lang2('product') . ', ' . lang2('so') . ' ' . lang2('cannot_delete') . ' ' . lang2('category');
				}
			}
		} else {

			$data['success'] = false;

			$data['message'] = lang2('you_dont_have_permission');
		}

		return response()->setJSON($data);
	}



	function get_product_categories()
	{

		$categories = $this->Products_Model->get_product_categories();

		$data_categories = array();

		foreach ($categories as $category) {

			$data_categories[] = array(

				'name' => $category['name'],

				'id' => $category['id'],

			);
		};

		return response()->setJSON($data_categories);
	}



	function update($id)
	{

		if ($this->Privileges_Model->check_privilege('products', 'all')) {

			$product = $this->Products_Model->get_product_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('products', 'own')) {

			$product = $this->Products_Model->get_product_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('products'));
		}

		if ($product) {

			if ($this->Privileges_Model->check_privilege('products', 'edit')) {

				if (isset($id)) {

					if (isset($_POST) && count($_POST) > 0) {

						$code = request()->getPost('code');
						$name = request()->getPost('name');
						$categoryid = request()->getPost('categoryid');
						$description = request()->getPost('description') ? request()->getPost('description') : null;
						$purchaseprice = request()->getPost('purchaseprice');
						$sale_price = request()->getPost('saleprice');
						$stock = request()->getPost('stock');
						//$vat = request()->getPost('tax');
						$vendor_id = request()->getPost('vendor_id');

						$hasError = false;

						$data['message'] = '';

						if ($name == '') {

							$hasError = true;

							$data['message'] = lang2('invalidmessage') . ' ' . lang2('productname');
						}
						//else if ($categoryid == '') {
						//	$hasError = true;
						//		$data['message'] = lang2('selectinvalidmessage') . ' ' . lang2('productcategory');
						//}
						//else if ($purchaseprice == '') {
						//	$hasError = true;
						//  $data['message'] = lang2('invalidmessage') . ' ' . lang2('purchaseprice');
						//}
						else if ($sale_price == '') {

							$hasError = true;

							$data['message'] = lang2('invalidmessage') . ' ' . lang2('salesprice');
						}
						// else if ($vendor_id == '') {
						//	$hasError = true;
						//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('vendor');
						//}
						//else if ($description == '') {
						//	$hasError = true;
						//	$data['message'] = lang2('invalidmessage') . ' ' . lang2('description');
						//	}

						if ($hasError) {

							$data['success'] = false;

							return response()->setJSON($data);
						}

						if (!$hasError) {

							$params = array(
								'code' => $code,
								'categoryid' => $categoryid,
								'productname' => $name,
								'description' => $description,
								'purchase_price' => $purchaseprice,
								'sale_price' => $sale_price,
								'stock' => $stock,
								'vendor_id' => $vendor_id,
								//	'vat' => $vat,
							);

							$this->Products_Model->update_products($id, $params);

							// Custom Field Post

							if (request()->getPost('custom_fields')) {

								$custom_fields = array(

									'custom_fields' => request()->getPost('custom_fields')

								);

								$this->Fields_Model->custom_field_data_add_or_update_by_type($custom_fields, 'product', $id);
							}

							$data['success'] = true;

							$data['message'] = lang2('product') . ' ' . lang2('updatemessage');

							return response()->setJSON($data);
						}
					}
				}
			} else {

				$data['success'] = false;

				$data['message'] = lang2('you_dont_have_permission');

				return response()->setJSON($data);
			}
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('products'));
		}
	}



	function get_category_id($name)
	{

		$rows = $this->db->table('productcategories')
			->where('productcategories.id_company', session()->id_company)
			->get()
			->getResultArray();

		foreach ($rows as $row) {

			if ($row['name'] = $name) {

				$category_id = $row['id'];
			}
		}

		return $category_id ? $category_id : '';
	}



	function productsimport()
	{

		if ($this->Privileges_Model->check_privilege('products', 'create')) {
			$uploadPath = ROOTPATH . 'public/uploads/attachments/';
			$file = request()->getFile('file');
			$filename = preg_replace("/[^a-z0-9_\-.]/i", '', $file->getName());
			$allowedTypes = ['csv'];

			if (!in_array($file->getExtension(), $allowedTypes)) {
				session()->setFlashdata('ntf3', 'Error');
				return redirect()->to('leads/index');
			}

			$data['products'] = $this->Products_Model->get_products_for_import();

			$data['error'] = '';

			if ($file->move($uploadPath, $filename)) {
				session()->setFlashdata('ntf4', lang2('csvimporterror'));
				return redirect()->to('leads/index');
			} else {
				$file_path = './uploads/imports/' . $filename;


				if ($this->import->get_array($file_path)) {

					$appconfig = get_appconfig();

					$csv_array = $this->import->get_array($file_path);

					$num = 1;

					$csv_errors = array();

					foreach ($csv_array as $row) {
						$category_name = $row['nome_da_categoria'];
						$category_id = $this->get_category_id($category_name);
						if (($row['nome_do_produto'] == '') || ($row['valor_de_venda'] == '')) {
							$num++;
							$csv_errors[] = array(
								'line' => $num,
							);
							continue;
						} else {

							$insert_data = array(
								'code' => $row['codigo'],
								'productname' => $row['nome_do_produto'],
								'description' => $row['descricao'],
								'purchase_price' => $row['valor_de_compra'],
								'sale_price' => $row['valor_de_venda'],
								'stock' => $row['estoque'],
								'vat' => $row['taxa'],
								'status_id' => $row['status_id'],
								'categoryid' => $category_id,
								'id_company' => session()->id_company
							);

							$num++;
							$this->Products_Model->insert_products_csv($insert_data);

							if ($appconfig['product_series']) {
								$product_number = $appconfig['product_series'];
								$product_number = $product_number + 1;
								$this->Settings_Model->increment_series('product_series', $product_number);
							}
						}
					}

					$datas['success'] = true;
					$datas['message'] = lang2('file') . ' ' . lang2('csvimportsuccess') . ' ' . lang2('butErrors');
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

			$data['success'] = false;

			$datas['message'] = lang2('you_dont_have_permission');

			return response()->setJSON($datas);
		}
	}



	function exportdata()
	{

		$this->load->dbutil();

		$this->load->helper('file');

		$this->load->helper('download');

		if ($this->Privileges_Model->check_privilege('products', 'all')) {

			$builder = $this->db->table('products');
			$builder->select('products.productname, products.description, products.code, products.purchase_price, products.sale_price, products.stock, products.vat, products.status_id, productcategories.name as category, productcategories.id as category_id, productimage');
			$builder->join('productcategories', 'products.categoryid = productcategories.id', 'left');
			$builder->where('productcategories.id_company', session()->id_company);

			$q = $builder->get()->getResultArray();
		} else if ($this->Privileges_Model->check_privilege('products', 'own')) {

			$builder = $this->db->table('products');
			$builder->select('products.productname, products.description, products.code, products.purchase_price, products.sale_price, products.stock, products.vat, products.status_id, productcategories.name as category, productcategories.id as category_id, productimage');
			$builder->join('productcategories', 'products.categoryid = productcategories.id', 'left');
			$builder->where('product_created_by', session()->usr_id);
			$builder->where('productcategories.id_company', session()->id_company);

			$q = $builder->get()->getResultArray();
		}


		$delimiter = ",";

		$nuline    = "\r\n";

		force_download('Products.csv', $this->dbutil->csv_from_result($q, $delimiter, $nuline));
	}



	function product($id)
	{

		if ($this->Privileges_Model->check_privilege('products', 'all')) {

			$product = $this->Products_Model->get_product_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('products', 'own')) {

			$product = $this->Products_Model->get_product_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('products'));
		}

		if ($product) {

			$data['title'] = lang2('product');

			$data['product'] = $product;

			return view('products/product', $data);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('products'));
		}
	}



	function remove($id)
	{
		if ($this->Privileges_Model->check_privilege('products', 'all')) {
			$product = $this->Products_Model->get_product_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('products', 'own')) {
			$product = $this->Products_Model->get_product_by_privileges($id, session()->usr_id);
		} else {
			$data['success'] = false;
			$data['message'] = lang2('you_dont_have_permission');
			return response()->setJSON($data);
		}

		if ($product) {
			if ($this->Privileges_Model->check_privilege('products', 'delete')) {
				if (isset($product['id'])) {
					$this->Products_Model->delete_products($id, get_number('products', $id, 'product', 'product'));
					session()->setFlashdata('ntf4', lang2('product') . ' ' . lang2('deletemessage'));
				} else {
					show_error('The products you are trying to delete does not exist.');
				}
			} else {
				session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			}
		} else {
			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));
			return redirect()->to(base_url('products'));
		}
	}



	function get_product($id)
	{

		$product = array();

		if ($this->Privileges_Model->check_privilege('products', 'all')) {

			$product = $this->Products_Model->get_product_by_privileges($id);
		} else if ($this->Privileges_Model->check_privilege('products', 'own')) {

			$product = $this->Products_Model->get_product_by_privileges($id, session()->usr_id);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('products'));
		}

		if ($product) {

			if (!$product['categoryid'] || $product['categoryid'] == 0) {

				$product['categoryid'] = null;

				$product['name'] = null;

				$product['id'] = $id;
			} else {

				$product = $this->Products_Model->get_products($id);
			}

			//




			// Contar o número de vendas de produtos
			$total_product_sales = $this->db->table('items')
				->where('product_id', $product['id'])
				->countAllResults();

			// Calcular o total de ganhos líquidos
			$netearnings = $this->db->table('items')
				->selectSum('total')
				->where('product_id', $product['id'])
				->get()
				->getRow()
				->total;


			if (!empty($netearnings)) {

				$total = $netearnings;
			} else {

				$total = 0;
			}

			// Calcular a soma dos impostos
			$total_tax_products = $this->db->table('items')
				->selectSum('tax')
				->where('product_id', $product['id'])
				->get()
				->getRow()
				->tax;


			if (!empty($netearnings)) {

				$total_tax = $total_tax_products;
			} else {

				$total_tax = 0;
			}

			$data_product = array(

				'id' => $product['id'],

				'code' => $product['code'],

				'productname' => $product['productname'],

				'description' => $product['description'],

				'productimage' => $product['productimage'],

				'purchase_price' => $product['purchase_price'],

				'sale_price' => $product['sale_price'],

				'stock' => $product['stock'],

				'categoryid' => $product['categoryid'],

				'vat' => $product['vat'],

				'status_id' => $product['status_id'],

				'category_name' => $product['name'],
				'vendor_id' => $product['vendor_id'],
				'company' => $product['company'],
				'total_sales' => $total_product_sales,

				'net_earning' => $total - $total_tax,

				'product_number' => get_number('products', $product['id'], 'product', 'product'),

			);

			return response()->setJSON($data_product);
		} else {

			session()->setFlashdata('ntf3', lang2('you_dont_have_permission'));

			return redirect()->to(base_url('products'));
		}
	}



	function get_products()
	{

		$products = array();

		if ($this->Privileges_Model->check_privilege('products', 'all')) {

			$products = $this->Products_Model->get_all_products_by_privileges();
		} else if ($this->Privileges_Model->check_privilege('products', 'own')) {

			$products = $this->Products_Model->get_all_products_by_privileges(session()->usr_id);
		}

		$settings = $this->Settings_Model->get_settings_ciuis();

		$data_products = array();

		$appconfig = get_appconfig();

		foreach ($products as $product) {

			$data_products[] = array(

				'product_id' => $product['id'],
				'code' => $product['code'],
				'name' => $product['productname'],
				'company' => $product['company'],
				'description' => $product['description'],
				'price' => (float)$product['sale_price'],
				'tax' => $product['vat'],
				'purchase_price' => (float)$product['purchase_price'],
				'category_name' => $product['name'],
				'stock' => (float)$product['stock'],
				'product_number' => get_number('products', $product['id'], 'product', 'product'),

			);
		};

		return response()->setJSON($data_products);
	}
}
