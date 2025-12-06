<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */

    public $db;

    function loadModels()
    {
        $this->model('Settings_Model');
        $this->model('Notifications_Model');
        $this->model('Staff_Model');
        $this->model('Chats_Model');
        $this->model('Deposits_Model');
        $this->model('Products_Model');
        $this->model('Companies_Model');
        $this->model('Report_Model');
        $this->model('Orders_Model');
        $this->model('Login_Model');
        $this->model('Leads_Model');
        $this->model('Privileges_Model');
        $this->model('Invoices_Model');
        $this->model('Payments_Model');
        $this->model('Tickets_Model');
        $this->model('Events_Model');
        $this->model('Trivia_Model');
        $this->model('Logs_Model');
        $this->model('Proposals_Model');

        $this->model('Fields_Model');
        $this->model('Customers_Model');
        $this->model('Emails_Model');
        $this->model('Expenses_Model');
        $this->model('Accounts_Model');
        $this->model('Vendors_Model');
        $this->model('Purchases_Model');
        $this->model('Projects_Model');
        $this->model('Tasks_Model');
        $this->model('Contacts_Model');

        $this->db = \Config\Database::connect();
    }
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        //print_r($request);
        //   exit;
       

        ini_set('MEMORY_LIMIT', '10024M');

        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: session() = \Config\Services::session();
        $view_folder = '';
        if (!isset($view_folder[0]) && is_dir(APPPATH . 'Views' . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . 'Views';
        } elseif (is_dir($view_folder)) {
            if (($_temp = realpath($view_folder)) !== FALSE) {
                $view_folder = $_temp;
            } else {
                $view_folder = strtr(
                    rtrim($view_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
            }
        } elseif (is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . strtr(
                trim($view_folder, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
            );
        }


        helper('ciuis_helper');
        define('LANG', 'portuguese_br');
        define('current_user_id', session()->get('usr_id'));
        define('VIEWPATH', $view_folder . DIRECTORY_SEPARATOR);
    }


    protected function model($model, $alias = null)
    {
        $modelClass = 'App\Models\\' . $model;

        if (class_exists($modelClass)) {
            $modelInstance = new $modelClass();


            $this->$model = $modelInstance;


            return $modelInstance;
        }

        throw new \Exception("Model $model não encontrado.");
    }
}
