<?php

namespace App\Models;

use CodeIgniter\Model;

include APPPATH . '/third_party/vendor/autoload.php';

use Dompdf\Dompdf;

class Orders_Model extends Model
{

	// Função para obter todas as ordens
	function get_all_orders()
	{
		$builder = $this->db->table('orders');

		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, orders.id as id');
		$builder->join('staff', 'orders.assigned = staff.id', 'left');
		$builder->orderBy('orders.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));

		return $builder->get()->getResultArray();
	}

	// Função para obter todas as ordens por cliente
	function get_all_orders_by_customer($id)
	{
		$builder = $this->db->table('orders');

		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, orders.id as id');
		$builder->join('staff', 'orders.assigned = staff.id', 'left');
		$builder->orderBy('orders.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));

		return $builder->where('relation_type', 'customer')->where('relation', $id)->get()->getResultArray();
	}

	// Função para obter uma ordem específica
	function get_order($id)
	{
		return $this->db->table('orders')->where('id', $id)->get()->getRowArray();
	}

	// Função para obter a ordem com base no token
	function get_order_by_token($token)
	{
		return $this->db->table('orders')->where('token', $token)->get()->getRowArray();
	}

	// Função para obter ordens por tipo de relação
	function get_orders($id, $rel_type)
	{
		$builder = $this->db->table('orders');

		if ($rel_type == 'customer') {
			$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, customers.type as type, customers.company as customercompany, customers.email as toemail, customers.namesurname as namesurname, customers.address as toaddress, customers.zipcode as zip, orders.status_id as status_id, orders.id as id, orders.created as created');
			$builder->join('customers', 'orders.relation = customers.id', 'left');
		} elseif ($rel_type == 'lead') {
			$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffavatar, leads.name as leadname, leads.address as toaddress, leads.email as toemail, orders.status_id as status_id, orders.id as id');
			$builder->join('leads', 'orders.relation = leads.id', 'left');
		}

		$builder->join('staff', 'orders.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		return $builder->where('orders.id', $id)->get()->getRowArray();
	}

	// Função para obter itens da ordem
	function get_orderitems($id)
	{
		return $this->db->table('orderitems')->where('order_id', $id)->get()->getResultArray();
	}

	// Função para somar o total dos produtos da ordem
	function get_order_productsi_art($id)
	{
		return $this->db->table('orderitems')->selectSum('total')->where('order_id', $id)->get()->getRow()->total;
	}

	// Funções para mudar o status da ordem
	function status_1($id)
	{
		$this->db->table('orders')->where('id', $id)->update(['status_id' => '1']);
		$this->db->table('sales')->where('order_id', $id)->update(['status_id' => '1']);
	}

	function status_2($id)
	{
		$this->db->table('orders')->where('id', $id)->update(['status_id' => '2']);
		$this->db->table('sales')->where('order_id', $id)->update(['status_id' => '2']);
	}

	function status_3($id)
	{
		$this->db->table('orders')->where('id', $id)->update(['status_id' => '3']);
		$this->db->table('sales')->where('order_id', $id)->update(['status_id' => '3']);
	}

	// Função para adicionar uma nova ordem
	function order_add($params)
	{
		$this->db->table('orders')->insert($params);
		$order = $this->db->insertID();

		$appconfig = get_appconfig();
		$number = $appconfig['order_series'] ? $appconfig['order_series'] : $order;
		$order_number = $appconfig['order_prefix'] . $number;

		$this->db->table('orders')->where('id', $order)->update(['order_number' => $order_number]);

		// Inserir itens da ordem
		$items = request()->getPost('items');
		foreach ($items as $item) {
			$price = str_replace(',', '.', str_replace('.', '', $item['price']));
			$total = $item['quantity'] * $price + ($item['tax'] / 100 * $item['quantity'] * $price) - ($item['discount'] / 100 * $item['quantity'] * $price);

			$this->db->table('items')->insert([
				'relation_type' => 'order',
				'relation' => $order,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $price,
				'tax' => $item['tax'],
				'discount' => $item['discount'],
				'total' => $total,
			]);
		}

		// Log e Notificação
		if (request()->getPost('order_type') != 'true') {
			$staffname = session()->get('staffname');
			$staffavatar = session()->get('staffavatar');

			$this->db->table('notifications')->insert([
				'date' => date('Y-m-d H:i:s'),
				'detail' => "$staffname " . lang2('isaddedaneworder'),
				'customer_id' => request()->getPost('customer'),
				'perres' => $staffavatar,
				'target' => base_url('area/order/' . $order),
			]);
		}

		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => "<a href='staff/staffmember/$loggedinuserid'>$staffname</a> " . lang2('added') . " <a href='orders/order/$order'>" . lang2('order') . get_number('orders', $order, 'order', 'order') . "</a>.",
			'staff_id' => $loggedinuserid,
		]);

		return $order;
	}



	// Função para atualizar ordens
	function update_orders($id, $params)
	{
		$appconfig = get_appconfig();
		$order_data = $this->get_order($id);

		if (empty($order_data['order_number'])) {
			$number = $appconfig['order_series'] ? $appconfig['order_series'] : $id;
			$order_number = $appconfig['order_prefix'] . $number;

			$this->db->table('orders')->where('id', $id)->update(['order_number' => $order_number]);

			if (!empty($appconfig['order_series'])) {
				$order_number = $appconfig['order_series'] + 1;
				$this->Settings_Model->increment_series('order_series', $order_number);
			}
		}

		$this->db->table('orders')->where('id', $id)->update($params);

		$items = request()->getPost('items');
		foreach ($items as $item) {
			$price = str_replace(',', '.', str_replace('.', '', $item['price']));
			$total = $item['quantity'] * $price + ($item['tax'] / 100 * $item['quantity'] * $price) - ($item['discount'] / 100 * $item['quantity'] * $price);

			$item_data = [
				'relation_type' => 'order',
				'relation' => $id,
				'product_id' => $item['product_id'],
				'code' => $item['code'],
				'name' => $item['name'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit' => $item['unit'],
				'price' => $price,
				'tax' => $item['tax'],
				'discount' => $item['discount'],
				'total' => $total,
			];

			if (!empty($item['id'])) {
				$this->db->table('items')->where('id', $item['id'])->update($item_data);
			} else {
				$this->db->table('items')->insert($item_data);
			}
		}

		// Log
		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');
		$relation = request()->getPost('order_type') != 'true' ? request()->getPost('customer') : request()->getPost('lead');

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => "<a href='staff/staffmember/$loggedinuserid'> $staffname</a> " . lang2('updated') . " <a href='orders/order/$id'>" . get_number('orders', $id, 'order', 'order') . "</a>.",
			'staff_id' => $loggedinuserid,
			'customer_id' => $relation,
		]);

		// Notificação
		$staffavatar = session()->get('staffavatar');

		$this->db->table('notifications')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => "$staffname " . lang2('uptdatedorder'),
			'customer_id' => $relation,
			'perres' => $staffavatar,
			'target' => base_url("area/order/$id"),
		]);

		return $response ? "Proposal Updated." : "There was a problem during the update.";
	}

	// Função para excluir ordens
	function delete_orders($id, $number)
	{
		$this->db->table('orders')->delete(['id' => $id]);
		$this->db->table('items')->delete(['relation_type' => 'order', 'relation' => $id]);
		$this->db->table('pending_process')->delete(['process_relation' => $id, 'process_relation_type' => 'order']);

		$staffname = session()->get('staffname');
		$loggedinuserid = session()->get('usr_id');

		$this->db->table('logs')->insert([
			'date' => date('Y-m-d H:i:s'),
			'detail' => "<a href='staff/staffmember/$loggedinuserid'> $staffname</a> " . lang2('deleted') . " $number",
			'staff_id' => $loggedinuserid,
		]);
	}

	// Função para cancelar ordens
	function cancelled()
	{
		$order_id = request()->getPost('order_id');
		$status_id = request()->getPost('status_id');

		return $this->db->table('orders')->where('id', $order_id)->update(['status_id' => $status_id]);
	}



	public function markas()
	{
		$order_id = request()->getPost('order_id');
		$status_id = request()->getPost('status_id');

		return $this->db->table('orders')->where('id', $order_id)->update(['status_id' => $status_id]);
	}

	public function deleteorderitem($id)
	{
		return $this->db->table('orderitems')->delete(['id' => $id]);
	}

	public function get_order_year()
	{
		return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM orders ORDER BY year DESC')->get()->getResultArray();
	}

	public function update_pdf_status($id, $value)
	{
		return $this->db->table('orders')->where('id', $id)->update(['pdf_status' => $value]);
	}

	public function generate_pdf($id)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '2048M');

		$path = WRITEPATH . 'uploads/files/orders/' . $id;
		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}

		$pro = $this->Orders_Model->get_pro_rel_type($id);
		$rel_type = $pro['relation_type'];

		$data['orders'] = $this->Orders_Model->get_orders($id, $rel_type);
		$data['settings'] = $this->Settings_Model->get_settings_ciuis();
		$data['state'] = get_state_name($data['settings']['state'], $data['settings']['state_id']);
		$data['country'] = get_country($data['settings']['country_id']);
		$data['custcountry'] = get_country($data['orders']['country_id']);
		$data['custstate'] = get_state_name($data['orders']['state'], $data['orders']['state_id']);
		$data['items'] = $this->db->table('items')->where(['relation_type' => 'order', 'relation' => $id])->get()->getResultArray();

		$file_name = get_number('orders', $id, 'order', 'order') . '.pdf';
		$html = view('orders/pdf', $data, true);

		$dompdf = new \Dompdf\Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();

		$output = $dompdf->output();
		file_put_contents($path . '/' . $file_name, $output);

		$this->update_pdf_status($id, '1');

		return true;
	}

	public function get_all_orders_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('orders');
		$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffavatar,orders.id as id');
		$builder->join('staff', 'orders.assigned = staff.id', 'left');
		$builder->orderBy('orders.id', 'desc');
		$builder->where('staff.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->groupStart()
				->where('orders.assigned', $staff_id)
				->orWhere('orders.addedfrom', $staff_id)
				->groupEnd();
		}

		return $builder->get()->getResultArray();
	}

	public function get_order_by_priviliges($id, $rel_type, $staff_id = '')
	{
		$builder = $this->db->table('orders');

		if ($rel_type == 'customer') {
			$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffavatar,customers.type as type,customers.company as customercompany,customers.email as toemail,customers.namesurname as namesurname,customers.address as toaddress,customers.zipcode as zip,orders.status_id as status_id, orders.id as id, orders.created as created');
			$builder->join('customers', 'orders.relation = customers.id', 'left');
		} elseif ($rel_type == 'lead') {
			$builder->select('*,staff.staffname as staffmembername,staff.staffavatar as staffavatar,leads.name as leadname,leads.address as toaddress,leads.email as toemail,orders.status_id as status_id,orders.id as id');
			$builder->join('leads', 'orders.relation = leads.id', 'left');
		}

		$builder->join('staff', 'orders.assigned = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));

		if ($staff_id) {
			$builder->where('orders.id', $id);
			$builder->groupStart()
				->where('orders.assigned', $staff_id)
				->orWhere('orders.addedfrom', $staff_id)
				->groupEnd();
			return $builder->get()->getRowArray();
		} else {
			return $builder->where('orders.id', $id)->get()->getRowArray();
		}
	}
}
