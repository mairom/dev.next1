<?php
namespace App\Models;
use CodeIgniter\Model;

class Chats_Model extends Model
{


	/* Get All Chats */
	public function get_all_chats()
	{
		$builder = $this->db->table('staff');
		$builder->select('*');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('staff.id !=', session()->get('usr_id'));
		$builder->where('staff.inactive !=', 0);
		return $builder->get()->getResultArray();
	}
	
	public function add_reply_contact($params)
	{
		$builder = $this->db->table('chatreplies');
		$builder->insert($params);
		return $this->db->insertID();
	}
	
	public function get_chat_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('chatreplies');
		$builder->select('*');
		$builder->where('((chat_staff1 = ' . session()->get('usr_id') . ' and chat_staff2 = ' . $staff_id . ') or (chat_staff2 = ' . session()->get('usr_id') . ' and chat_staff1 = ' . $staff_id . '))');
	
		if ($ultimaVerificacao = request()->getPost('ultimaVerificacao')) {
			$builder->where("date >=", $ultimaVerificacao);
		}
	
		$builder->orderBy('date', 'asc');
		$data = $builder->get()->getResultArray();
	
		foreach ($data as $i => $dt) {
			$data[$i]['date'] = date(" d/m/Y H:i ", strtotime($dt['date']));
		}
	
		return $data;
	}
	
	public function get_all_chats_by_privileges($staff_id = '')
	{
		$builder = $this->db->table('staff');
		$builder->select('*, (SELECT date FROM `chatreplies` WHERE (chat_staff1 = ' . session()->get('usr_id') . ' and chat_staff2 = staff.id) or (chat_staff2 = ' . session()->get('usr_id') . ' and chat_staff1 = staff.id) order by id desc limit 1) as lastDate');
		$builder->where('staff.id_company', session()->get('id_company'));
		$builder->where('staff.id !=', session()->get('usr_id'));
		$builder->where('staff.inactive IS NULL');
		$builder->orderBy('lastDate', 'desc');
	
		if ($staff_id) {
			return $builder->where('staff.id', $staff_id)->get()->getResultArray();
		} else {
			return $builder->get()->getResultArray();
		}
	}
	
}
