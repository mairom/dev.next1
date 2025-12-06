<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Trivia extends AREA_Controller
{

	function mark_read_notification($id)
	{
		if (isset($id)) {
			$builder = $this->db->table('notifications');
			$response = $builder->where('id', $id)->update(['customerread' => 1]);
		}
	}
}
