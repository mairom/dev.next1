<?php

namespace App\Models;

use CodeIgniter\Model;



class Products_model extends Model
{


	public function get_products($id)
	{
		$builder = $this->db->table('products');
		$builder->select('*, productcategories.name, productcategories.id as categoryid, products.id as id, products.code, products.productname, products.description, products.purchase_price, products.sale_price, products.stock, products.vat, products.status_id, products.productimage');
		$builder->join('productcategories', 'products.categoryid = productcategories.id', 'inner');
		$builder->join('vendors', 'vendors.id = products.vendor_id', 'left');
		$builder->where(['products.id' => $id, 'products.id_company' => session()->id_company]);
		return $builder->get()->getRowArray();
	}

	public function get_product_by_id($id)
	{
		$builder = $this->db->table('products');
		$builder->select('*');
		$builder->where(['id' => $id]);
		return $builder->get()->getRowArray();
	}

	public function get_all_products()
	{
		$builder = $this->db->table('products');
		$builder->select('productcategories.name, productcategories.id as categoryid, products.id as id, products.code, products.productname, products.description, products.purchase_price, products.sale_price, products.stock, products.vat, products.status_id, products.productimage');
		$builder->join('productcategories', 'products.categoryid = productcategories.id', 'left');
		$builder->where('products.id_company', session()->id_company);
		$builder->orderBy('products.id', 'asc');
		return $builder->get()->getResultArray();
	}

	public function getallproductsjson()
	{
		$builder = $this->db->table('products');
		$builder->select('id id, code code, productname label, sale_price sale_price, vat vat');
		$builder->where('id_company', session()->id_company);
		return $builder->get()->getResult();
	}

	public function add_products($params)
	{
		$builder = $this->db->table('products');
		$builder->insert($params);
		$product = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['product_series'] ? $appconfig['product_series'] : $product;
		$product_number = $appconfig['product_prefix'] . $number;

		$builder->where('id', $product)->update(['product_number' => $product_number]);

		$logBuilder = $this->db->table('logs');
		$logBuilder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('addedanewproduct') . ' <a href="products/product/' . $product . '"> ' . lang2('product') . ' ' . get_number('products', $product, 'product', 'product') . '</a>',
			'staff_id' => session()->usr_id
		]);

		return $product;
	}

	public function insert_products_csv($data)
	{
		$builder = $this->db->table('products');
		$builder->insert($data);
		$product = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['product_series'] ? $appconfig['product_series'] : $product;
		$product_number = $appconfig['product_prefix'] . $number;

		$builder->where('id', $product)->update(['product_number' => $product_number]);
	}

	public function update_products($id, $params)
	{
		$appconfig = get_appconfig();
		$product_data = $this->get_products($id);

		if ($product_data['product_number'] == '') {
			$number = $appconfig['product_series'] ? $appconfig['product_series'] : $id;
			$product_number = $appconfig['product_prefix'] . $number;

			$builder = $this->db->table('products');
			$builder->where('id', $id)->update(['product_number' => $product_number]);

			if ($appconfig['product_series'] != '') {
				$product_number = $appconfig['product_series'] + 1;
				$this->Settings_Model->increment_series('product_series', $product_number);
			}
		}

		$builder = $this->db->table('products');
		$builder->where('id', $id);
		$response = $builder->update($params);

		$logBuilder = $this->db->table('logs');
		$logBuilder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('updated') . ' <a href="products/product/' . $id . '"> ' . lang2('product') . ' ' . get_number('products', $id, 'product', 'product') . '</a>',
			'staff_id' => session()->usr_id
		]);
	}

	public function get_products_for_import()
	{
		$builder = $this->db->table('products');
		$query = $builder->get();

		if ($query->getNumRows() > 0) {
			return $query->getResultArray();
		} else {
			return false;
		}
	}

	public function get_product_categories()
	{
		$builder = $this->db->table('productcategories');
		$builder->orderBy('id', 'desc');
		$builder->where('productcategories.id_company', session()->id_company);
		return $builder->get()->getResultArray();
	}

	public function get_categories($staff_id = '')
	{
		$builder = $this->db->table('products');
		$builder->select('productcategories.name as name, COUNT(productcategories.name) as y');
		$builder->join('productcategories', 'products.categoryid = productcategories.id', 'left');
		if ($staff_id) {
			$builder->where('product_created_by', $staff_id);
		}
		$builder->where('productcategories.id_company', session()->id_company);
		$builder->groupBy('productcategories.name');
		return $builder->get()->getResultArray();
	}

	public function get_category($id)
	{
		$builder = $this->db->table('productcategories');
		$builder->where('id', $id);
		return $builder->get()->getRowArray();
	}

	public function update_category($id, $params)
	{
		$builder = $this->db->table('productcategories');
		$builder->where('id', $id);
		return $builder->update($params);
	}

	public function check_category($id)
	{
		$builder = $this->db->table('products');
		$builder->where('categoryid', $id);
		$builder->where('products.id_company', session()->id_company);
		return $builder->countAllResults();
	}

	public function remove_category($id)
	{
		$builder = $this->db->table('productcategories');
		$response = $builder->delete(['id' => $id]);
		return $response;
	}

	public function delete_products($id, $number)
	{
		$builder = $this->db->table('products');
		$response = $builder->delete(['id' => $id]);

		$logBuilder = $this->db->table('logs');
		$logBuilder->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => '<a href="staff/staffmember/' . session()->usr_id . '"> ' . session()->staffname . '</a> ' . lang2('deleted') . ' ' . lang2('product') . ' ' . $number,
			'staff_id' => session()->usr_id
		]);

		$builder->resetQuery();
		$builder = $this->db->table('custom_fields_data');
		$builder->delete(['relation_type' => 'product', 'relation' => $id]);

		return $response;
	}

	public function get_all_products_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('products')
		->select('vendors.company,products.id_company,  productcategories.name, productcategories.id as categoryid, products.id as id, products.code, products.productname, products.description, products.purchase_price, products.sale_price, products.stock, products.vat, products.status_id, products.productimage')
		->join('productcategories', 'products.categoryid = productcategories.id', 'left')
		->join('vendors', 'vendors.id = products.vendor_id', 'left')
		->orderBy('products.id', 'desc')
		->where('products.id_company', session()->id_company);

		if ($staff_id) {
			$builder->where('product_created_by', $staff_id);
		}

		return $builder->get()->getResultArray();
	}

	public function get_product_by_privileges($id, $staff_id = '')
	{
		$builder = $this->db->table('products');
		$builder->select('products.*');
		$builder->where('products.id_company', session()->id_company);
		$builder->join('vendors', 'vendors.id = products.vendor_id', 'left');

		if ($staff_id) {
			$builder->where('products.id', $id);
			$builder->where('product_created_by', $staff_id);
		} else {
			$builder->where('products.id', $id);
		}

		return $builder->get()->getRowArray();
	}
}
