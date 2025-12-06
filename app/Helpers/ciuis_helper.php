<?php


define('BASEPATH', realpath(dirname(__FILE__)));
require_once APPPATH . '/third_party/vendor/autoload.php';

use Gerardojbaez\Money\Money;
use Gerardojbaez\Money\Currency;
use Config\Database;
use App\Models\Settings_Model;
use App\Models\Privileges_Model;
use App\Models\Login_Model;

defined('BASEPATH') or exit('No direct script access allowed');

$CI_INSTANCE = null;

function register_CI4(&$_ci)
{
	global $CI_INSTANCE;
	$CI_INSTANCE = $_ci;
}


function customer_meeting_check($staff_id, $customer_id, $date)
{
	$ci = \Config\Database::connect();

	$row = $ci->table('meetings')
		->where(['customer_id' => $customer_id, 'date' => $date, 'staff_id' => $staff_id])
		->get()
		->getRowArray();
	return $row['date'];
}

function lang2($line, array $args = [])
{
	$lang = \Config\Services::language()->getLine("App." . $line, $args);
	return $lang != "App." . $line ? $lang : $line;
}

function check_meeting($staff_id, $date)
{

	$ci = \Config\Database::connect();

	$row = $ci->table('meetings')
		->where(['date' => $date, 'staff_id' => $staff_id])
		->get()
		->getRowArray();


	if (isset($row['date'])) {

		return true;
	} else {

		return false;
	}
}



function weekdays()
{

	return array(

		'' . lang2('monday') . '',

		'' . lang2('tuesday') . '',

		'' . lang2('wednesday') . '',

		'' . lang2('thursday') . '',

		'' . lang2('friday') . '',

		'' . lang2('saturday') . '',

		'' . lang2('sunday') . '',

	);
}



function months()
{

	return array(

		'' . lang2('january') . '',

		'' . lang2('february') . '',

		'' . lang2('march') . '',

		'' . lang2('april') . '',

		'' . lang2('may') . '',

		'' . lang2('june') . '',

		'' . lang2('july') . '',

		'' . lang2('august') . '',

		'' . lang2('september') . '',

		'' . lang2('october') . '',

		'' . lang2('november') . '',

		'' . lang2('december') . '',

	);
}



function weekdays_git()
{

	return array(

		'Monday',

		'Tuesday',

		'Wednesday',

		'Thursday',

		'Friday',

		'Saturday',

		'Sunday'

	);
}



function ciuis_colors()
{

	$colors = array(

		'#28B8DA',

		'#03a9f4',

		'#c53da9',

		'#757575',

		'#8e24aa',

		'#d81b60',

		'#0288d1',

		'#7cb342',

		'#fb8c00',

		'#84C529',

		'#fb3b3b'

	);



	return $colors;
}



function email_config()
{
	$ci = \Config\Database::connect();
	$session = session();

	$class = $ci->query("SELECT * FROM settings WHERE id_company = ?", [$session->get('id_company')])->getResultArray();
	return $class;
}



function ciuis_Hash()
{
	return substr(str_shuffle(str_repeat(md5(uniqid()), 10)), 0, 6);
}



function ciuis_set_color($hex, $steps)
{

	$steps = max(-255, min(255, $steps));

	$hex = str_replace('#', '', $hex);

	if (strlen($hex) == 3) {

		$hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
	}

	$color_parts = str_split($hex, 2);

	$return = '#';

	foreach ($color_parts as $color) {

		$color = hexdec($color);

		$color = max(0, min(255, $color + $steps));

		$return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
	}

	return $return;
}



function _date($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%d.%m.%y", strtotime($date));
}



function _adate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return date("j F Y, g:i a", strtotime($date));
}



function _dDay($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%d", strtotime($date));
}



function _pdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%Y-%m-%d", strtotime($date));
}



function _phdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%Y-%m-%d H:i", strtotime($date));
}



// DATE TYPE

// 3000.12.01

function _rdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%Y.%m.%m", strtotime($date));
}

// 01.12.3000

function _udate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%d.%m.%Y", strtotime($date));
}

// 3000-12-01

function _mdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%y-%m-%d", strtotime($date));
}

// 01-12-3000

function _cdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%d-%m-%y", strtotime($date));
}



function _cxdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%d-%m-%Y", strtotime($date));
}

// 3000/12/01

function _zdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%y/%m/%d", strtotime($date));
}

// 01/12/3000

function _kdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%d/%m/%y", strtotime($date));
}



function _ktdate($date)
{

	if ($date == '' || is_null($date) || $date == '0000-00-00') {

		return '';
	}

	return strftime("%d/%m/%y", strtotime($date));
}



function convertToHoursMins($time, $format = '%02d:%02d')
{

	if ($time < 1) {

		return;
	}

	$hours = floor($time / 60);

	$minutes = ($time % 60);

	return sprintf($format, $hours, $minutes);
}



function tes_ciuis($datetime, $full = false)
{

	$today = time();

	$createdday = strtotime($datetime);

	$datediff = abs($today - $createdday);

	$difftext = "";

	$years = floor($datediff / (365 * 60 * 60 * 24));

	$months = floor(($datediff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

	$days = floor(($datediff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

	$hours = floor($datediff / 3600);

	$minutes = floor($datediff / 60);

	$seconds = floor($datediff);

	// Years

	if ($difftext == "") {

		if ($years > 1)

			$difftext = $years . lang2('yearsago');

		elseif ($years == 1)

			$difftext = $years . lang2('yearago');
	}

	// Mounth

	if ($difftext == "") {

		if ($months > 1)

			$difftext = $months . lang2('monthsago');

		elseif ($months == 1)

			$difftext = $months . lang2('monthago');
	}

	// Days

	if ($difftext == "") {

		if ($days > 1)

			$difftext = $days . lang2('daysago');

		elseif ($days == 1)

			$difftext = $days . lang2('dayago');
	}

	// Hours

	if ($difftext == "") {

		if ($hours > 1)

			$difftext = $hours . lang2('hoursago');

		elseif ($hours == 1)

			$difftext = $hours . lang2('hourago');
	}

	// Minutes

	if ($difftext == "") {

		if ($minutes > 1)

			$difftext = $minutes . lang2('minutesago');

		elseif ($minutes == 1)

			$difftext = $minutes . lang2('minuteago');
	}

	// Seconds

	if ($difftext == "") {

		if ($seconds > 1)

			$difftext = $seconds . lang2('secondsago');

		elseif ($seconds == 1)

			$difftext = $seconds . lang2('secondago');
	}

	return $difftext;
}



function isAdmin()
{

	$ci = \Config\Database::connect();
	$session = session();

	$id = $session->get('usr_id');

	$rows = $ci->table('staff')
		->where(['admin' => 1, 'id' => $id])
		->countAllResults();

	return $rows > 0;
}


function load_config()
{
	// Obter a instância do banco de dados
	$db = Database::connect();

	// Obter as configurações da tabela 'branding'
	$builder = $db->table('branding');
	$configs = $builder->get()->getResultArray();

	$data = [];

	foreach ($configs as $config) {
		$data[$config['name']] = $config['value'];
	}

	// Verificar e adicionar configurações adicionais se necessário
	if (empty($data['nav_logo']) || empty($data['app_logo'])) {
		$builder = $db->table('settings');
		$settings = $builder->where('settingname', 'ciuis')->get()->getRowArray();

		$data['app_logo'] = $settings['app_logo'] ?? '';
		$data['nav_logo'] = $settings['logo'] ?? '';
	}

	return $data;
}

function form_open($action = '', $attributes = [])
{
	// Define default attributes
	$default_attributes = [
		'method' => 'post',
		'id' => null,
		'class' => null,
		'enctype' => null
	];

	// Merge user attributes with default attributes
	$attributes = array_merge($default_attributes, $attributes);

	// Build attributes string
	$attr_string = '';
	foreach ($attributes as $key => $value) {
		if ($value !== null) {
			$attr_string .= sprintf(' %s="%s"', htmlspecialchars($key, ENT_QUOTES, 'UTF-8'), htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
		}
	}

	// Start building the form tag
	$form_open = '<form action="' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '"' . $attr_string . '>';

	// Add CSRF protection field
	$form_open .= csrf_field();

	return $form_open;
}

function form_close()
{
	return "</form>";
}

function get_appconfig()
{
	$ci = Config\Database::connect();
	$configs = $ci->table('appconfig')->get()->getResultArray();
	$data = array();

	foreach ($configs as $config) {
		$data[$config['name']] = $config['value'];
	}

	return $data;
}



function is_update_available()
{

	$ci = \Config\Database::connect();
	$version = $ci->table('versions')->where('id', '1')->get()->getRowArray();


	if ($version['is_update_available'] === '1') {

		return true;
	} else {

		return false;
	}
}



//return the country name based on the country id

function get_country($id)
{

	$country_name = '';

	$countries = file_get_contents('assets/json/countries.json');

	$obj = json_decode($countries, true);

	foreach ($obj as $country) {

		if ($country['id'] == $id) {

			$country_name = $country['shortname'];

			break;
		}
	}

	return $country_name;
}



//return the state name based on the state id

function get_state_name($name = '', $id = '')
{

	$state_name = '';

	if ($id == "" || $id == null) {

		$state_name = $name;
	} else {

		$states = file_get_contents('assets/json/states.json');

		$obj = json_decode($states, true);

		foreach ($obj as $state) {

			if ($state['id'] == $id) {

				$state_name = $state['state_name'];

				break;
			}
		}
	}

	return $state_name;
}



//return the all states regards the country id

function get_states($id)
{

	$states_data = array();

	$states = file_get_contents('assets/json/states.json');

	$obj = json_decode($states, true);

	foreach ($obj as $state) {

		if ($state['country_id'] == $id) {

			$states_data[] = $state;
		}
	}

	return $states_data;
}



function staff_timezone($date)
{

	$ci = \Config\Database::connect();
	$settings = $ci->table('settings')->where('settingname', 'ciuis')->get()->getRowArray();


	if (!empty($ci->session->userdata('staff_timezone'))) {

		$timezone = $ci->session->userdata('staff_timezone');
	} else {

		$timezone = $settings['default_timezone'];
	}

	$settingsdate = date_create($date, timezone_open($settings['default_timezone']));

	date_timezone_set($date, timezone_open($timezone));

	switch ($settings['dateformat']) {

		case 'yy.mm.dd':

			$return = date_format($date, 'Y.m.d H:i:s');

			break;

		case 'dd.mm.yy':

			$return = date_format($date, 'd.m.Y H:i:s');

			break;

		case 'yy-mm-dd':

			$return = date_format($date, 'Y-m-d H:i:s');

			break;

		case 'dd-mm-yy':

			$return = date_format($date, 'd-m-Y H:i:s');

			break;

		case 'yy/mm/dd':

			$return = date_format($date, 'Y/m/d H:i:s');

			break;

		case 'dd/mm/yy':

			$return = date_format($date, 'd/m/Y H:i:s');

			break;
	};

	return $return;
}



function date_by_timezone($date, $type = 'date')
{

	$ci = \Config\Database::connect();
	$settings = $ci->table('settings')->where('settingname', 'ciuis')->get()->getRowArray();


	$newdate = date_create($date, timezone_open($settings['default_timezone']));

	return date_format($newdate, 'Y-m-d H:i:s');
}



function timestamp()
{

	$ci = \Config\Database::connect();

	$date = date('Y-m-d H:i:s');

	$settings = $ci->table('settings')->where('settingname', 'ciuis')->get()->getRowArray();


	$newdate = date_create($date, timezone_open($settings['default_timezone']));

	return date_format($newdate, 'Y-m-d H:i:s');
}



function unsetSession()
{

	$ci->session->unset_userdata('usr_id');

	$ci->session->unset_userdata('email');

	$ci->session->unset_userdata('root');

	$ci->session->unset_userdata('language');

	$ci->session->unset_userdata('admin');

	$ci->session->unset_userdata('staffmember');

	$ci->session->unset_userdata('staffname');

	$ci->session->unset_userdata('staffavatar');

	$ci->session->unset_userdata('LoginOK');

	$ci->session->unset_userdata('other');

	$ci->session->unset_userdata('staff_timezone');

	$ci->session->unset_userdata('remote_check');

	//redirect(site_url('login/license'));

}


function get_number($table, $id, $name, $configname = NULL)
{
	$ci = \Config\Database::connect();
	$response = $ci->table($table)->where('id', $id)->get()->getRowArray();


	if (isset($response[$name . '_number'])) {
		return $response[$name . '_number'];
	} else if ($configname != null && isset($response['id'])) {
		$appconfig = get_appconfig();

		$number = $appconfig[$configname . '_prefix'] . str_pad($response['id'], 6, '0', STR_PAD_LEFT);
		return $number;
	}
	return 0;
}



function get_dateFormat()
{
	$ci = \Config\Database::connect();
	$dateFormat = $ci->table('settings')->get()->getRow()->dateformat;


	switch ($dateFormat) {
		case 'yy.mm.dd':
			$return = 'Y.m.d';
			break;
		case 'dd.mm.yy':
			$return = 'd.m.Y';
			break;
		case 'yy-mm-dd':
			$return = 'Y-m-d';
			break;
		case 'dd-mm-yy':
			$return = 'd-m-Y';
			break;
		case 'yy/mm/dd':
			$return = 'Y/m/d';
			break;
		case 'dd/mm/yy':
			$return = 'd/m/Y';
			break;
		default:
			$return = 'd/m/Y';
	};

	$return = 'd.m.Y';

	return $return;
}



function get_dateTimeFormat()
{

	$ci = \Config\Database::connect();
	$dateFormat = $ci->table('settings')->get()->getRow()->dateformat;

	switch ($dateFormat) {

		case 'yy.mm.dd':
			$return = 'Y.m.d H:i:s';
			break;
		case 'dd.mm.yy':
			$return = 'd.m.Y H:i:s';
			break;
		case 'yy-mm-dd':
			$return = 'Y-m-d H:i:s';
			break;
		case 'dd-mm-yy':
			$return = 'd-m-Y H:i:s';
			break;
		case 'yy/mm/dd':
			$return = 'Y/m/d H:i:s';
			break;
		case 'dd/mm/yy':
			$return = 'd/m/Y H:i:s';
			break;
		default:
			$return = 'd.m.Y H:i:s';
	};

	return $return;
}



function post_data($url, $params = array())
{

	$ch = curl_init(); //crul initialize

	curl_setopt($ch, CURLOPT_URL, $url); // set url

	curl_setopt($ch, CURLOPT_POST, 1);

	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$output = curl_exec($ch);

	curl_close($ch); //curl close

	return $output;
}



function get_menu()
{
	$settings_model = new Settings_Model();
	$privileges_Model = new Privileges_Model();

	$menus = $settings_model->get_menus();
	$data_menus = array();

	//print_r($menus);

	foreach ($menus as $menu) {
		$sub_menus = $settings_model->get_submenus($menu['id']);
		$data_submenus = array();
		foreach ($sub_menus as $sub_menu) {
			//if ($sub_menu['id'] == 15) {
			//	print_r($sub_menu);
			//	print_r($privileges_Model->has_privilege($sub_menu['url']));
			//}

			if ($privileges_Model->has_privilege($sub_menu['url']) || $sub_menu['url'] == "panel") {
				if ($sub_menu['url'] != NULL) {
					$suburl = '' . base_url($sub_menu['url']) . '';
				} else {
					$suburl = '#';
				}

				$data_submenus[] = array(
					'id' => $sub_menu['id'],
					'order_id' => $sub_menu['order_id'],
					'main_menu' => $sub_menu['main_menu'],
					'name' => lang($sub_menu['name']),
					'description' => lang($sub_menu['description']),
					'icon' => $sub_menu['icon'],
					'icon_svg' => $sub_menu['icon_svg'],
					'url' => $suburl,
				);
			}

			if ($sub_menu['name'] == 'x_menu_calendar') {
				$data_submenus[] = array(
					'id' => '23',
					'order_id' => '2',
					'main_menu' => '5',
					'name' => lang2('x_menu_calendar'),
					'description' => lang2('manage_calendar'),
					'icon' => 'ion-android-calendar',
					'url' => base_url('calendar'),
				);
			}
		};
		if ($menu['url'] != NULL) {
			$url = '' . base_url($menu['url']) . '';
		} else {
			$url = '#';
		}

		$data_menus[] = array(

			'id' => $menu['id'],
			'order_id' => $menu['order_id'],
			'main_menu' => $menu['main_menu'],
			'name' => lang2('' . $menu['name'] . ''),
			'description' => $menu['description'],
			'icon' => $menu['icon'],
			'url' => $url,
			'sub_menu' => $data_submenus,

		);
	};

	return ($data_menus);
}


function upload($data, $local)
{
	if (empty($data) || $data == "") {
		return null;
	}

	$path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $local . "/";
	if (!file_exists($path)) {
		mkdir($path, 0777, $recursive = true);
	}

	$image_array_1 = explode(";", $data);
	$extensao = explode("/", $image_array_1[0])[1];
	$image_array_2 = explode(",", $image_array_1[1]);
	$data = base64_decode($image_array_2[1]);
	if ($_POST['nm_file']) {
		$imageName = $_POST['nm_file'] /* . date('Yhmdhi') */ . '.' . $extensao;
	} else {
		$imageName = md5(time()) . '.' . $extensao;
	}


	file_put_contents($path . "/" . $imageName, $data);

	return  $imageName;
}



function get_leftmenu()
{
	$login_model = new Login_Model();
	$Settings_Model = new Settings_Model();

	$if_admin = $login_model->if_admin();
	define('if_admin', realpath(dirname(__FILE__)));
	define('currency', $Settings_Model->get_currency());

	if (!$if_admin) {

		$permission_menu = 0;
	} else $permission_menu = 1;

	$all_menu = array(

		'1' => array(
			'title' => lang2('x_menu_panel'),
			'show_staff' => 0,
			'url' => base_url('panel'),
			'icon' => 'ion-ios-analytics-outline',
			'path' => null,
			'show' => 'true'
		),

		'2' => array(
			'title' => lang2('x_menu_proposals'),
			'show_staff' => 0,
			'url' => base_url('proposals'),
			'icon' => 'ico-ciuis-proposals',
			'path' => 'proposals',
			'show' => 'false'
		),

		'3' => array(
			'title' => lang2('x_menu_orders'),
			'show_staff' => 0,
			'url' => base_url('orders'),
			'icon' => 'ion-ios-filing-outline',
			'path' => 'orders',
			'show' => 'false'
		),

		'4' => array(
			'title' => lang2('x_menu_invoices'),
			'show_staff' => 0,
			'url' => base_url('invoices'),
			'icon' => 'ico-ciuis-invoices',
			'path' => 'invoices',
			'show' => 'false'
		),

		'5' => array(
			'title' => lang2('x_menu_purchases'),
			'show_staff' => 0,
			'url' => base_url('purchases'),
			'icon' => 'ion-ios-cart-outline',
			'path' => 'purchases',
			'show' => 'false'
		),

		'6' => array(
			'title' => lang2('x_menu_projects'),
			'show_staff' => 0,
			'url' => base_url('projects'),
			'icon' => 'ico-ciuis-projects',
			'path' => 'projects',
			'show' => 'false'
		),

		'7' => array(
			'title' => lang2('x_menu_tasks'),
			'show_staff' => 0,
			'url' => base_url('tasks'),
			'icon' => 'ico-ciuis-tasks',
			'path' => 'tasks',
			'show' => 'false'
		),

		'8' => array(
			'title' => lang2('x_menu_tickets'),
			'show_staff' => 0,
			'url' => base_url('tickets'),
			'icon' => 'ico-ciuis-supports',
			'path' => 'tickets',
			'show' => 'false'
		),

		'9' => array(
			'title' => lang2('x_menu_expenses'),
			'show_staff' => 0,
			'url' => base_url('expenses'),
			'icon' => 'ico-ciuis-expenses',
			'path' => 'expenses',
			'show' => 'false'
		),

		'10' => array(
			'title' => lang2('x_menu_deposits'),
			'show_staff' => 0,
			'url' => base_url('deposits'),
			'icon' => 'ion-ios-paper-outline',
			'path' => 'deposits',
			'show' => 'false'

		),

		'11' => array(
			'title' => lang2('x_menu_timesheets'),
			'show_staff' => 0,
			'url' => base_url('timesheets'),
			'icon' => 'ion-ios-clock-outline',
			'path' => 'timesheets',
			'show' => 'false'
		),

	);



	$data_left_menu = array();
	$privileges_Model = new Privileges_Model();

	foreach ($all_menu as $menu) {

		if ($privileges_Model->has_privilege($menu['path']) || $menu['show'] != 'false') {

			$show = true;

			$data_left_menu[] = array(

				'title' => $menu['title'],

				'show_staff' => 0,

				'url' => $menu['url'],

				'icon' => $menu['icon'],

				'path' => $menu['path'],

				'show' => true

			);
		} else {

			continue;
		}
	}

	return ($data_left_menu);
}



function get_user()
{
	$ci = \Config\Database::connect();
	$id = session()->get('usr_id');

	$user = $ci->table('staff')
		->select('*, languages.name as stafflanguage, departments.name as department, staff.id as id')
		->join('departments', 'staff.department_id = departments.id', 'left')
		->join('languages', 'staff.language = languages.foldername', 'left')
		->where('(staff.inactive != 0 OR staff.inactive IS NULL)')
		->where('staff.id', $id)
		->get()
		->getRowArray();

	$user_data = [];


	if (!$user) {
		$session = session();
		$session->destroy();
		return redirect()->to('/login');
		//$ci->index();
	} else {
		$settings = get_settings();
		$user_data = array(
			'id' => $user['id'],
			'role_id' => $user['role_id'],
			'language' => $user['language'],
			'name' => $user['staffname'],
			'avatar' => $user['staffavatar'],
			'department_id' => $user['department_id'],
			'phone' => $user['phone'],
			'email' => $user['email'],
			'birthday' => $user['birthday'],
			'root' => $user['root'],
			'admin' => $user['admin'],
			'staffmember' => $user['staffmember'],
			'last_login' => $user['last_login'],
			'inactive' => $user['inactive'],
			'appointment_availability' => $user['appointment_availability'],
			'settings' => $settings,
			'super_admin' => $user['super_admin'],
		);
	}

	return $user_data;
}



function get_settings()
{

	$ci = \Config\Database::connect();

	return $ci->table('settings')->where('settingname', 'ciuis')->get()->getRowArray();
}



function get_active_payment_methods()
{

	$ci = \Config\Database::connect();

	return $ci->table('payment_methods')->where('active', '1')->get()->getResultArray();
}


function check_privilege_export($type)
{
	$ci = \Config\Database::connect();
	$session = session();

	$q = $ci->table('companies_permission_export')
		->where(['id_company' => $session->get('id_company'), 'export_page' => $type])
		->get()
		->getRowArray();

	$r = false;
	if ($q) {
		$role = $ci->table('staff')
			->where('id', $session->get('usr_id'))
			->get()
			->getRowArray();
		$q2 = $ci->table('role_permissions_export')
			->where(['export_page' => $type, 'id_role' => $role['role_id']])
			->get()
			->getRowArray();

		if ($q2) {
			$r = true;
		}
	}
	return $r;
}

function check_privilege($type, $privileges)
{
	$ci = \Config\Database::connect();
	$session = session();

	$staff_id = $session->get('usr_id');
	$role = $ci->table('staff')->where('id', $staff_id)->get()->getRowArray();
	$role_id = $role['role_id'];

	$permission = $ci->table('permissions')->where('key', $type)->get()->getRowArray();
	$permission_id = $permission['id'];

	$permission = $ci->table('role_permissions')
		->where(['permission_id' => $permission_id, 'role_id' => $role_id])
		->get()
		->getRowArray();




	if ($permission) {
		if ($privileges == 'create' && $permission['permission_create'] == '1') {
			return true;
		} else if ($privileges == 'edit' && $permission['permission_edit'] == '1') {
			return true;
		} else if ($privileges == 'delete' && $permission['permission_delete'] == '1') {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}



function amount_format($amount, $currency_needed = false)
{
	$settings_model = new Settings_Model();
	$ci = \Config\Database::connect();

	$settings = $ci->table('settings')->where('settingname', 'ciuis')->get()->getRowArray();

	$currency = $settings_model->get_currency($settings['currencyid']);
	$currency = new Currency($currency);

	if ($settings['thousand_separator'] != 'auto') {

		$currency->setThousandSeparator($settings['thousand_separator']);
	}

	if ($settings['decimal_separator'] != 'auto') {

		$currency->setDecimalSeparator($settings['decimal_separator']);
	}

	if ($currency_needed === true) {

		if ($settings['currency_position'] == 'auto') {

			$currency->setSymbolPlacement($settings['currency_position']);

			$final_amount = new Money($amount, $currency);
		} else if ($settings['currency_position'] == 'after') {

			$data = new Money($amount, $currency);

			$final_amount = $data->amount();

			if ($settings['currency_display'] == 'code') {

				$final_amount = $data->amount() . ' ' . $currency->getCode();
			} else {

				$final_amount = $data->amount() . $currency->getSymbol();
			}
		} else if ($settings['currency_position'] == 'before') {

			$data = new Money($amount, $currency);

			$final_amount = $data->amount();

			if ($settings['currency_display'] == 'code') {

				$final_amount = $currency->getCode() . ' ' . $data->amount();
			} else {

				$final_amount = $currency->getSymbol() . $data->amount();
			}
		} else {

			$data = new Money($amount, $currency);

			$final_amount = $data->amount();
		}
	} else {

		$data = new Money($amount, $currency);

		$final_amount = $data->amount();
	}

	return $final_amount;
}
