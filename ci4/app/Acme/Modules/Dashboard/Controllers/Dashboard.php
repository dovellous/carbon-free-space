<?php

/**
 * Controller Class Dashboard
 *
 * @author      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Acme
 * @subpackage  Dashboard
 */

namespace Acme\Modules\Dashboard\Controllers;

use App\Libraries\Apiadaptor;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use Acme\System\Libraries\AuthLibrary;
use Acme\System\Libraries\AutoLogin;
use Acme\System\Libraries\GibberishaesLibrary;
use Acme\System\Libraries\PasswordHash;
use Acme\System\Libraries\ReCaptchaLibrary;
use Acme\Modules\Dashboard\Models\DashboardModel;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class Dashboard extends \Acme\Modules\Dashboard\Controllers\DashboardBaseController
{


    /**
     * @var $aes
     */
    public $api;

    /**
     * @var $roles
     */
    public $roles;
    
    /**
     * @var $captcha
     */
    public $captcha;
    
    /**
     * @var $aes
     */
    public $aes;
    
    /**
     * @var $passwordHash
     */
    public $passwordHash;
    
    /**
     * @var $autoLogin
     */
    public $autoLogin;
    
    /**
     * @var $auth
     */
    public $auth;

    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param LoggerInterface   $logger
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {



        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.: $this->session = \Config\Services::session();

        $this->api = new  Apiadaptor();

        $this->auth = new AuthLibrary(
            $this,
            $request
        );

        if(!$this->auth->loggedin()){

            //Parse the $user_data to get the user group landing page
            //Todo: current page
            //$this->showLogin();

        }

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function index(...$args)
    {

        //var_dump(get_defined_vars()); exit;

        $this->AcmeHome('DashboardAll');

    }

    /**
     * Add a user, password will be hashed
     *
     */
    private function AcmeHome()
    {

        $layout = "DashboardAll";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        $uuid = 0;

        $params = array(
            "views.header.layout" => "vertical",
            "views.header.style" => "default",
            "views.header.type" => "default.modern",
            "views.footer.layout" => "vertical",
            "views.footer.style" => "default",
            "views.footer.type" => "default.modern",
            "views.footer.scripts" => "Acme/Modules/Dashboard/Views/Hooks/PageScripts/DashboardAll",
        );

        // Render the view
        $this
            ->dashboardLibrary
            ->renderView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                $params
            );

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function AcmeOrders()
    {

        return $this->DisplayOrders('List');

    }

    /**
     * Add a user, password will be hashed
     * 
     */
    private function DisplayOrders($action)
    {

        $layout = "";

        $path = "";

        $url = "";

        switch( $action ){

            case "tiles" : {

                $layout = "Orders/List";

                $path = "";

                $url = "";

                break;

            }

            case "list" : {

                $layout = "Orders/List";

                $path = "";

                $url = "";

                break;

            }

            case "new" : {

                $layout = "Orders/List";

                $path = "";

                $url = "";

                break;

            }

            case "accepted" : {

                $layout = "Orders/List";

                $path = "";

                $url = "";

                break;

            }

            case "preparing" : {

                $layout = "Orders/List";

                $path = "";

                $url = "";

                break;

            }

            case "moving" : {

                $layout = "Orders/List";

                $path = "";

                $url = "";

                break;

            }

            case "completed" : {

                $layout = "Orders/List";

                $path = "";

                $url = "";

                break;

            }

            default : {

                $layout = "Orders/List";

                $path = "";

                $url = "";

                break;

            }

        }

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);
        
        $uuid = 0;

        $params = array(
            "views.header.layout" => "vertical",
            "views.header.style" => "default",
            "views.header.type" => "default.modern",
            "views.footer.layout" => "vertical",
            "views.footer.style" => "default",
            "views.footer.type" => "default.modern",
            "views.footer.scripts" => "Acme/Modules/Dashboard/Views/Hooks/PageScripts/OrdersList",
            "firebase.data" => NULL
            );
        
        // Render the view
        $this
            ->dashboardLibrary
            ->renderView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                $params
            );

    }


    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function AcmeOrder_preview($orderId)
    {

        return $this->DisplayOrderDetails($orderId);

    }

    /**
     * Add a user, password will be hashed
     *
     */
    private function DisplayOrderDetails($orderId)
    {

        $layout = "Orders/Details";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        $uuid = 0;

        $firebase_data = $this->api->get_orders($orderId);

        //var_dump("FIREBASE_DATA::", $firebase_data); die(); exit;

        $drivers = $this->api->get_drivers();

        //var_dump("DRIVERS::", $drivers); die(); exit;

        $params = array(
            "views.header.layout" => "vertical",
            "views.header.style" => "default",
            "views.header.type" => "default.modern",
            "views.footer.layout" => "vertical",
            "views.footer.style" => "default",
            "views.footer.type" => "default.modern",
            "views.footer.scripts" => "Acme/Modules/Dashboard/Views/Hooks/PageScripts/OrdersDetails",
            "firebase.data" => $firebase_data,
            "drivers" => $drivers
        );

        // Render the view
        $this
            ->dashboardLibrary
            ->renderView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                $params
            );

    }

    /**
     * Add a user, password will be hashed
     *
     */
    public function AcmeOrders_map($location=false)
    {

        $layout = "Orders/Map";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        $uuid = 0;

        $markers = $this->api->get_markers(true);

        $cmarkers = $this->api->get_markers(true);

        //var_dump("$markers::", $markers); die(); exit;

        $params = array(
            "views.header.layout" => "vertical",
            "views.header.style" => "default",
            "views.header.type" => "default.modern",
            "views.footer.layout" => "vertical",
            "views.footer.style" => "default",
            "views.footer.type" => "default.modern",
            "views.footer.scripts" => "Acme/Modules/Dashboard/Views/Hooks/PageScripts/OrdersMap",
            "cmarkers" => $cmarkers,
            "markers" => $markers
        );

        // Render the view
        $this
            ->dashboardLibrary
            ->renderView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                $params
            );

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function AcmeOrder_json()
    {

        $order = $this->api->get_orders();

        $orders_list = json_decode($order, 1);

        die(json_encode(array("data"=>$orders_list)));

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function AcmeOrders_update_status()
    {

        $uid = $_POST["uid"];

        $order_id = $_POST["order_id"];

        $state_key = $_POST["status"];

        $comment = $_POST["comments"];

        $push_to_kds = $_POST["push_to_kds"];

        $result = $this->api->update_order_status($uid, $order_id, $state_key, $comment);

        if($push_to_kds){

            //call_gaap

            //update with gaap data

        }

        die($result);

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function AcmeOrders_update_driver()
    {

        $uid = $_POST["uid"];

        $order_id = $_POST["order_id"];

        $reg = $_POST["vehicle"];

        $result = $this->api->update_order_driver($uid, $order_id, $reg);

        die($result);

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function AcmeOrders_add_payment()
    {

        $uid = $_POST["uid"];

        $order_id = $_POST["order_id"];

        $order_number = $_POST["order_number"];

        $date = $_POST["date"];

        $txn_id = $_POST["note"];

        $method = $_POST["method"];

        $currency = $_POST["currency"];

        $payment_gross = $_POST["amount"];

        $payment_status = $_POST["status"];

        $result = $this->api->add_payment($uid, $order_id, $order_number, $date, $txn_id, $method, $currency, $payment_gross, $payment_status, true);

        die($result);

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function AcmeOrder_details($order_id)
    {

        $order = $this->api->get_orders($order_id);

        $order_details = json_decode($order, 1);

        $data["order"] = $order;

        $data["order_details"] = $order_details;

        echo view('dashboard/admin-order-details', $data);

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function AcmeOrder_details_update_status($uid, $order_id, $status="NEW",$comment=false){

        $comments = "";

        if(isset($_POST["comments"])){

            $comments = str_replace("+", "%20", urlencode($_POST["comments"]));

        }

        $result = $this->api->update_order_status($uid,$order_id,$status,$comments);

        return $result;

    }

    /**
     * Add a user, password will be hashed
     *
     */
    private function OrderDetails($action)
    {

        $layout = "";

        $path = "";

        $url = "";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        $uuid = 0;

        $firebase_data = $this->api->get_orders();

        //var_dump("FIREBASE_DATA::", $firebase_data);

        $params = array(
            "views.header.layout" => "vertical",
            "views.header.style" => "default",
            "views.header.type" => "default.modern",
            "views.footer.layout" => "vertical",
            "views.footer.style" => "default",
            "views.footer.type" => "default.modern",
            "views.footer.scripts" => "Acme/Modules/Dashboard/Views/Hooks/PageScripts/OrdersList",
            "firebase.data" => $firebase_data
        );

        // Render the view
        $this
            ->dashboardLibrary
            ->renderView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                $params
            );

    }



} 