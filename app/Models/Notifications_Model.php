<?php
namespace App\Models;
use CodeIgniter\Model;

class Notifications_Model extends Model
{

	public function get_notifications($id)
	{
		return $this->db->table('notifications')->where('id', $id)->get()->getRowArray();
	}
	
	public function get_all_notifications()
	{
		$builder = $this->db->table('notifications');
		$builder->select('*, staff.staffname as staffmembername, staff.staffavatar as staffimage, notifications.public as public, 
			notifications.staff_id as staff_id, notifications.contact_id as contact_id, notifications.customer_id as customer_id, 
			notifications.id as notifyid');
		$builder->join('staff', 'notifications.staff_id = staff.id', 'left');
		$builder->where('public = 1 OR staff_id = ' . session()->get('usr_id'));
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('notifyid', 'desc');
		$builder->limit(20);
	
		$query = $builder->get();
		return $query->getResultArray();
	}
	
	public function readnotification()
	{
		$builder = $this->db->table('notifications');
		$builder->join('staff', 'notifications.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$new = $builder->where(['markread' => 1, 'staff_id' => session()->get('usr_id')])->get()->getResult();
	
		if ($new) {
			return '-unread';
		}
	}
	
	public function newnotification()
	{
		$builder = $this->db->table('notifications');
		$builder->join('staff', 'notifications.staff_id = staff.id', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$new = $builder->where(['markread' => 0, 'staff_id' => session()->get('usr_id')])->get()->getResult();
	
		return !empty($new);
	}
	
	public function get_notification()
	{
		$builder = $this->db->table('notifications');
		$builder->select('*, notifications.id as id_notifications');
		$builder->join('staff', 'notifications.staff_id_acao = staff.id', 'left');
		$builder->join('avisos', 'avisos.id_aviso = notifications.id_aviso', 'left');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->orderBy('notifications.id', 'desc');
		$builder->limit(20);
		$new = $builder->where('notifications.staff_id', session()->get('usr_id'))->get()->getResult();
	
		return $new;
	}
	
}
