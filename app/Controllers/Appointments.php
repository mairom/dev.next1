<?php
namespace App\Controllers;

defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Appointments extends BaseController {

	function index() {
		$data[ 'title' ] = 'Appointments';
		return view( 'appointments/index', $data );
	}
	function confirm_appointment($id)
	{
		if (isset($id)) {
			$this->db->table('appointments')
				->where('id', $id)
				->update(['status' => 1]);
		}
	}
	
	function decline_appointment($id)
	{
		if (isset($id)) {
			$this->db->table('appointments')
				->where('id', $id)
				->update(['status' => 2]);
		}
	}
	
	function mark_as_done_appointment($id)
	{
		if (isset($id)) {
			$this->db->table('appointments')
				->where('id', $id)
				->update(['status' => 3]);
		}
	}
	
	function remove_appointment($id)
	{
		if (isset($id)) {
			$this->db->table('appointments')
				->where('id', $id)
				->delete();
		}
	}
	
}