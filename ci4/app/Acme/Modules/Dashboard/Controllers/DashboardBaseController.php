<?php

/**
 * Base Controller Class Dashboard
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


use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class DashboardBaseController extends \Acme\System\Controllers\AcmeBaseController
{

    /**
     * The array to hold the Dashboard config values
     *
     * @var array $params
     */
    public $params;

    /**
     * Use this to check the health status of this module
     * @var string $healthCheckName
     */
    public $healthCheckName;

    /**
     * The module model class
     *
     * @var class $dashboardModel
     */
    public $dashboardModel;

    /**
     * Initialize the request for the dashboard
     *
     * @param class $dashboardRequest
     */
    public $dashboardRequest;

    /**
     * Initialize the response for the dashboard
     *
     * @param class $dashboardResponse
     */
    public $dashboardResponse;

    /**
     * Initialize the configurations for the dashboard
     *
     * @param class $dashboardConfig
     */
    public $dashboardConfig;

    /**
     * Initialize the libraries for the dashboard
     *
     * @var class $dashboardLibrary
     */
    public $dashboardLibrary;

    /**
     * Initialize the libraries for the dashboard
     *
     * @var class $dashboardLibrary
     */
    public $languageFile;

    /**
     * The name of this module
     *
     * @var string $moduleName
     */
    public $moduleName;

    /**
     * Instance of the main Request object.
     *
     * @var IncomingRequest|CLIRequest
     */

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

        //Load all helpers

        // Check if session was started already of begin it
        if(!$this->session) {
            // Session Class
            $this->session = \Config\Services::session();
        }

        // Initialise the params array for get and set methods
        $this->params = array();

        // Set health check vars
        $this->healthCheckName = array(
            "namespace" => __NAMESPACE__,
            "class" => __CLASS__,
            "method" => __METHOD__,
            "trait" => __TRAIT__,
            "file" =>__FILE__,
            "base" => "auth.dashboard.controller",
            "checksum" => "checksum.auth.dashboard.controller",
            "module" => "dashboard"
        );

        // Requests Class
        $this->dashboardRequest = $request;

        // Response Class
        $this->dashboardResponse = $response;

        // Model Class
        $this->dashboardModel = new \Acme\Modules\Dashboard\Models\DashboardModel();

        // Config Class
        $this->dashboardConfig = $this->dashboardModel->dashboardConfig;

        // Library Class
        $this->dashboardLibrary = $this->dashboardModel->dashboardLibrary;

        // Request Class
        $this->dashboardLibrary->acmeRequest = $this->acmeRequest;

        // Get the browser negotiated language
        $this->dashboardConfig->systemConfig["i18n.browser.negotiated"] = $this->language;
        // Get the user specified language
        $this->dashboardConfig->systemConfig["i18n.user.defined"] = $this->locale;
        // Get the system language
        $this->dashboardConfig->systemConfig["i18n.system.defined"] = $this->locale;
        // Get the system language

        $this->dashboardConfig->config["namespace.path"] = ROOTPATH . "Acme\Modules\Acme\Dashboard\\";

        // Add the system configs to the dashboard config array
        if(!isset($this->dashboardConfig->config["system.config"])) {

            // Only do so if we have some values
            if(array_key_exists("system.config", $this->dashboardConfig->config)){

                // include the system configs in the module config
                $this->dashboardConfig->config["system.config"] = $this->dashboardConfig->systemConfig;

            }

        }

        if(!acme_get_env("app.forceGlobalSecureRequests","bool")) {

            // Check if the module requires https
            if (isset($this->dashboardConfig->config["security.enforce.https"])) {

                // If the module requires https, redirect user to a secure location
                if ($this->dashboardConfig->config["security.enforce.https"] == "yes") {

                    // This controller is only accessible via HTTPS
                    if (!$this->request->isSecure()) {

                        // Redirect the user to this page via HTTPS, and set the StrictTransport-Security
                        // header so the browser will automatically convert all links to this page to HTTPS
                        // for the next year.

                        force_https();

                    }

                }

            }

        }

        // Module Name
        $this->moduleName = "Dashboard";

        // Setup the language file
        $this->languageFile = $this->moduleName . ".";

        // Get the current user roles
        $userSessionData = $this->session->get();

        // Check if the user is authorised to use this module
        $isOperationAllowed = $this->dashboardLibrary->isOperationAllowed($userSessionData, $request,  $this->dashboardConfig->config["system"], $this->moduleName, __METHOD__, __NAMESPACE__, __CLASS__);

        //If the operation is allowed proceed processing the page
        if($isOperationAllowed){

            // Assign the user data into config data for use in views as a global vars
            $this->dashboardConfig->config["user"] = $userSessionData;

        }else {

            // The session is invalid logout the user
            // return acme_redirect(acme_base_url("logout"));

            // exit;

        }

    }

    /**
     * Get the key value
     *
     * @param string $key
     * @return string
     */
    public function __get( $key )
    {

        // Get a library variable
        return $this->params[ $key ];
    }

    /**
     * Set the key value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function __set( $key, $value )
    {

        // Set a library variable
        return $this->params[ $key ] = $value;

    }

    /**
     * If the controller contains a method named _remap(), it will always get called regardless
     * of what the URI contains. It overrides the normal behavior in which the URI determines
     * which method is called, therby giving room to define a custom method routing rules.
     *
     * @param $method
     * @param array ...$params
     * @return mixed
     */
    public function _remap($method, ...$params)
    {

        if( $method === "index" || $method === null || !$method || empty($method)) {

            // Show the default module landing page
            return $this->index();

        }else{

            // Remap the controller to a corresponding method
            $method = 'Acme' . ucfirst($method);

            // Only remap if the controller method exists
            if (method_exists($this, $method)) {
                return $this->$method(...$params);
            }

            // Or else throw an error: 404 page not found exception
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound($method);

        }
    }

    /**
     * View all Dashboard items or specific details
     *
     * @param string $uuid
     */
    public function AcmeView( $uuid = "all")
    {

        // Check if the user is authorised to perform this action
        if(!$this->dashboardLibrary->isOperationAllowed($this->userRoles, __NAMESPACE__, __CLASS__, __METHOD__, $this->moduleName, 'READ')){

            // Redirect to forbidden pageyyyy
            acme_redirect(acme_base_url("page/http403/create-not-allowed"));

        }

        // View all Dashboard items
        $this->DoView($uuid, true);

    }

    /**
     * View all Dashboard items or specific details
     *
     * @param string $uuid
     * @param bool $renderDisplay Whether to render the tabular view or return the recordset
     */
    public function DoView( $uuid = "all", $renderDisplay=false)
    {

        // Pre initialise the  ta object to hold the query criteria
        $dataObject = array();

        if( $uuid == "all" || $uuid == "" || empty($uuid) || !$uuid ){

            // Layout file name
            $layout="DashboardGetAll";

            // Specify the data type to expect from the database
            $dataObject["returnType"] = "result_array";

        }else{

            // Layout file name
            $layout="DashboardViewDetails";

            // Specify the where clause
            $dataObject["where"] = array(
                "uuid" => $uuid
            );

            // Specify the data type to expect from the database
            $dataObject["returnType"] = "row_array";

        }

        // Tell the model that we need summary
        $dataObject["isDashboard"] = false;

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        if( $renderDisplay ) {

            // Render the view
            $this->dashboardLibrary->renderView($this, $method, $uuid, $layout, $dataObject, false);

        }else{

            // return a record set
            $results = $this->dashboardModel->AcmeDashboardDBRead( $dataObject);

            return $results;

        }
    }


    /**
     * Insert a new Dashboard item
     *
     */
    public function AcmeCreateNew()
    {

        // Check if the user is authorised to perform this action
        if(!$this->dashboardLibrary->isOperationAllowed($this->userRoles, __NAMESPACE__, __CLASS__, __METHOD__, $this->moduleName, 'INSERT')){

            // Redirect to forbidden page
            acme_redirect(acme_base_url("page/http403/create-not-allowed"));

        }

        // Insert data into the database
        $this->InsertForm();

    }

    /**
     * Insert a new Dashboard item
     *
     */
    public function InsertForm()
    {

        $uuid = null;

        // Layout file name
        $layout="DashboardCreateNew";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Tell the model that we need summary
        $dataArray["isDashboard"] = false;

        // Specify the data type to expect from the database
        $dataArray["returnType"] = "result_array";

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        // Render the view
        $this->dashboardLibrary->renderView( $this, $method, $uuid, $layout, $dataArray, false);

    }

    /**
     * Insert a new Dashboard item
     *
     * @return mixed
     */
    public function AcmeInsertItem()
    {

        $this->DoInsert(true);

    }

    /**
     * Insert a new Dashboard item
     *
     * @param bool $renderDisplay
     * @return mixed
     */
    public function DoInsert( $renderDisplay=false )
    {

        // Get the data array object based on the user input
        $dataObject = $this->dashboardLibrary->getCRUDArray( 'INSERT' );

        // Delete data from the database
        $result = $this->dashboardModel->AcmeDashboardDBInsert( $dataObject );

        // Show we proceed and show the records after the operation?
        if($renderDisplay) {

            // Render the list view, also return the message iin the flash data
            $this->AcmeView("all");

        }else{

            // Return the result as an array object for further processing
            return $result;

        }

    }

    /**
     * Display the Dashboard item update form
     *
     * @param string $uuid
     */
    public function AcmeUpdate( $uuid = "")
    {

        // Check if the user is authorised to perform this action
        if(!$this->dashboardLibrary->isOperationAllowed($this->userRoles, __NAMESPACE__, __CLASS__, __METHOD__, $this->moduleName, 'UPDATE')){

            // Redirect to forbidden page
            acme_redirect(acme_base_url("page/http403/update-not-allowed"));

        }

        // Get the ID from the Universal Unique ID
        $id = $this->dashboardModel->AcmeDashboardGetIdFromUUID( $uuid );

        // If the id is undefined, the record does not exist
        if( !$id ){

            // Render the list view, also return the message iin the flash data
            $this->AcmeView("all");

            return;

        }

        // Show the record update form
        $this->UpdateForm( $uuid );

    }

    /**
     * Display the Dashboard item update form
     *
     * @param string $uuid
     */
    public function UpdateForm( $uuid = "")
    {

        // Layout file name
        $layout="DashboardUpdateExisting";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Tell the model that we need summary
        $dataArray["isDashboard"] = false;

        // Specify the data type to expect from the database
        $dataArray["returnType"] = "result_array";

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        // Render the view
        $this->dashboardLibrary->renderView( $this, $method, $uuid, $layout, $dataArray, false);

    }

    /**
     * Update an existing Dashboard item
     *
     * @param string $uuid The record universal unique id o perform the operation
     * @return mixed
     */
    public function AcmeUpdateItem( $uuid )
    {

        $this->DoUpdate($uuid, true);

    }

    /**
     * Update an existing Dashboard item
     *
     * @param string $uuid The record universal unique id o perform the operation
     * @param bool $renderDisplay Should we return the operation result or just render the view
     * @return mixed
     */
    public function DoUpdate( $uuid, $renderDisplay=false )
    {

        $dataObject = array();

        // Specify the where clause
        $dataObject["where"] = array(
            "uuid" => $uuid
        );

        // Get the data array object based on the user input

        $dataObject["data"] = $this->dashboardLibrary->getCRUDArray( 'UPDATE' );

        $id = $this->dashboardModel->AcmeDashboardGetIdFromUUID( $uuid );

        // Update data in the database
        $result = $this->dashboardModel->AcmeDashboardDBUpdate( $id, $dataObject);

        // Show we proceed and show the records after the operation?
        if($renderDisplay) {

            // Render the list view, also return the message iin the flash data
            $this->AcmeView("all");

        }else{

            // Return the result as an array object for further processing
            return $result;

        }

    }

    /**
     * Insert a new Dashboard item
     *
     */
    public function AcmeDelete( $uuid = "")
    {

        // Check if the user is authorised to perform this action
        if(!$this->dashboardLibrary->isOperationAllowed($this->userRoles, __NAMESPACE__, __CLASS__, __METHOD__, $this->moduleName, 'DELETE')){

            // Redirect to forbidden page
            acme_redirect(acme_base_url("page/http403/delete-not-allowed"));

        }

        // Get the ID from the Universal Unique ID
        $id = $this->dashboardModel->AcmeDashboardGetIdFromUUID( $uuid );

        // If the id is undefined, the record does not exist
        if( !$id ){

            // Render the list view, also return the message iin the flash data
            $this->AcmeView("all");

            return;

        }

        // Delete data from the database
        return $this->DoDelete( $id, true );

    }

    /**
     * Delete an existing Dashboard item
     *
     * @param int $id The record id o perform the operation
     * @param bool $renderDisplay Should we return the operation result or just render the view
     * @return  mixed
     */
    public function DoDelete( $id, $renderDisplay=false )
    {

        // Delete data from the database
        $result = $this->dashboardModel->AcmeDashboardDBDelete( $id, array());

        // Show we proceed and show the records after the operation?
        if($renderDisplay) {

            // Render the list view, also return the message iin the flash data
            $this->AcmeView("all");

        }else{

            // Return the result as an array object for further processing
            return $result;

        }

    }

    /**
     * Get the user data stored in the session
     *
     */
    public function getUserSessionData(){

        return $this->session->get()["authUserData"];

    }

    /**
     * Get the data stored in the session
     *
     */
    public function getSessionData(){


        return $this->session->get();

    }

    public function AcmeDebug(...$args)
    {
        var_dump($args);
    }

}
