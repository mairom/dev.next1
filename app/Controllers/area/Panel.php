<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Panel extends AREA_Controller {

	function index() {
		$data[ 'title' ] = lang( 'areatitleindex' );
		$data[ 'ycr' ] = $this->Report_Model->ycr();
		$data[ 'customerdebt' ] = $this->Area_Model->customerdebt();
		$data[ 'settings' ] = $this->Settings_Model->get_settings_ciuis();
		$data[ 'customer_annual_sales_chart' ] = $this->Area_Model->customer_annual_sales_chart();
		$data[ 'totalticket' ] = $this->db->from( 'tickets' )->where( 'customer_id = ' . $_SESSION[ 'customer' ] . '' )->get()->getNumRows();
		$data[ 'totalinvoices' ] = $this->db->from( 'invoices' )->where( 'customer_id = ' . $_SESSION[ 'customer' ] . '' )->get()->getNumRows();
		$data[ 'totalproposals' ] = $this->db->from( 'proposals' )->where( 'relation = ' . $_SESSION[ 'customer' ] . ' AND relation_type = "customer"' )->get()->getNumRows();
		$data[ 'totalcontact' ] = $this->db->from( 'contacts' )->where( 'customer_id = ' . $_SESSION[ 'customer' ] . '' )->get()->getNumRows();
		$data[ 'totalpayment' ] = $this->db->from( 'payments' )->where( 'customer_id = ' . $_SESSION[ 'customer' ] . '' )->get()->getNumRows();
		return view( 'area/inc/header', $data );
		return view( 'area/index/area', $data );
	}

}