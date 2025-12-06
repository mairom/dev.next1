<?php
namespace App\Controllers;
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Editor extends BaseController {

	function __construct() {
		
		if (!session()->get('admin')) {
			session()->setFlashdata( 'ntf3', '' . lang( 'you_dont_have_permission' ) );
		return redirect()->to( 'panel' );
		}
	}

	function index() {
		$data['title'] = lang2('editor');
		return view('editor/editor', $data);
	}

	function restore_default($type = null) {
		switch ($type) {
			case '1':
				$data = file_get_contents(APPPATH. 'language/'.session()->get('language').'/'.session()->get('language').'_lang_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '2':
				$data = file_get_contents(APPPATH. 'Views/invoices/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '3':
				$data = file_get_contents(APPPATH. 'Views/expenses/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '4':
				$data = file_get_contents(APPPATH. 'Views/purchases/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '5':
				$data = file_get_contents(APPPATH. 'Views/proposals/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '6':
				$data = file_get_contents(APPPATH. 'Views/orders/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '7':
				$data = file_get_contents(APPPATH. 'Views/deposits/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '8':
				$data = file_get_contents(APPPATH. 'Views/projects/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '9':
				$data = print_r(file_get_contents(base_url('assets/json/countries_default.json')));
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '10':
				$data = print_r(file_get_contents(base_url('assets/json/states_default.json')));
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			default:
				$data = file_get_contents(APPPATH. 'language/'.session()->get('language').'/'.session()->get('language').'_lang_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
		}
		echo $data;
	}

	function get_data($type = null) {
		switch ($type) {
			case '1':
				$data = file_get_contents(APPPATH. 'language/'.session()->get('language').'/'.session()->get('language').'_lang.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '2':
				$data = file_get_contents(APPPATH. 'Views/invoices/pdf.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '3':
				$data = file_get_contents(APPPATH. 'Views/expenses/pdf.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '4':
				$data = file_get_contents(APPPATH. 'Views/purchases/pdf.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '5':
				$data = file_get_contents(APPPATH. 'Views/proposals/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '6':
				$data = file_get_contents(APPPATH. 'Views/orders/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '7':
				$data = file_get_contents(APPPATH. 'Views/deposits/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '8':
				$data = file_get_contents(APPPATH. 'Views/projects/pdf_default.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '9':
				$data = print_r(file_get_contents(base_url('assets/json/countries.json')));
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			case '10':
				$data = print_r(file_get_contents(base_url('assets/json/states.json')));
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
			default:
				$data = file_get_contents(APPPATH. 'language/'.session()->get('language').'/'.session()->get('language').'_lang.php');
				if ($data == false) {
					$data = lang2('unable_to_read');
				}
				break;
		}
		echo $data;
	}

	function save($file) {
		if ( isset( $_POST ) && count( $_POST ) > 0 ) {
			if ($file == '1' || $file == '2' || $file == '3' || $file == '4' || $file == '5' || $file == '6' || $file == '7' || $file == '8' || $file == '9' || $file == '10') {
				if ($file == '1') {
					$file_path = APPPATH. 'language/'.session()->get('language').'/'.session()->get('language').'_lang.php';
				}
				if ($file == '2') {
					$file_path = APPPATH. 'Views/invoices/pdf.php';
				}
				if ($file == '3') {
					$file_path = APPPATH. 'Views/expenses/pdf.php';
				}
				if ($file == '4') {
					$file_path = APPPATH. 'Views/purchases/pdf.php';
				}
				if ($file == '5') {
					$file_path = APPPATH. 'Views/proposals/pdf.php';
				}
				if ($file == '6') {
					$file_path = APPPATH. 'Views/orders/pdf.php';
				}
				if ($file == '7') {
					$file_path = APPPATH. 'Views/deposits/pdf.php';
				}
				if ($file == '8') {
					$file_path = APPPATH. 'Views/projects/pdf.php';
				}
				if ($file == '9') {
					$file_path = base_url('assets/json/countries.json');
				}
				if ($file == '10') {
					$file_path = base_url('assets/json/states.json');
				}
				if(!is_writable($file_path)) {
					$data['message'] = lang2('file_not_writable');
					$data['success'] = false;
				} else {
					$fp = fopen($file_path, 'w+');
					fwrite($fp, request()->getPost('data', FALSE));
					fclose($fp);
					$data['message'] = lang2('file_data').' '.lang2('updatemessage');
					$data['success'] = true;
				}
			} else {
				$data['success'] = false;
				$data['message'] = lang2('select_valid_file');
			}
			return response()->setJSON($data);
		}
	}
}


