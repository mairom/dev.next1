<?php
namespace App\Controllers;

class App_Errors extends BaseController {

	// Function for error-logging with timestamp
	function index() {
		return view('app-errors/errors-log');
	}
}