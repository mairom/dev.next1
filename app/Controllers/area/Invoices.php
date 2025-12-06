<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Invoices extends AREA_Controller {
	

	function index() {
		$data[ 'title' ] = lang( 'areatitleinvoices' );
		$data[ 'settings' ] = $this->Settings_Model->get_settings_ciuis();
		return view( 'area/invoices/index', $data );

	}

	function invoice( $token ) { 
		$invoice = $this->Invoices_Model->get_invoices_by_token( $token );
		$data[ 'invoice' ] = $invoice;
		$data['payment'] = $this->Settings_Model->payment_mode($data['invoice']['default_payment_method']);
		//$this->Settings_Model->get_payment_gateway_data();
		
		$data['items'] = $this->db->table('items')
		->select('*')
		->where(['relation_type' => 'invoice', 'relation' => $id])
		->get()->getResultArray();

		$data[ 'settings' ] = $this->Settings_Model->get_settings_ciuis();
		$data[ 'title' ] = get_number('invoices', $invoice['id'], 'invoice', 'inv') .' '. lang2('detail');
		return view( 'area/invoices/invoice_detail', $data );
	}

}