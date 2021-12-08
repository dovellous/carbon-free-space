<?php

/**
 * Controller Class Users
 *
 * @author      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Auth
 * @subpackage  Users
 */

namespace Acme\Core\System\Modules\Auth\Users\Controllers;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class Http extends \Acme\Core\System\Modules\Auth\Users\Controllers\UsersBaseController
{


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


    /**
     * Constructor.
     *
     * @param \CodeIgniter\HTTP\RequestInterface $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        // Initialise the parent controller
        parent::initController(\Config\Services::request(), \Config\Services::response(), \Config\Services::logger());

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function index()
    {

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    public function ACMES404($page="xxxxx")
    {


        $uuid = "";

        $layout = "http404";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array(
            "page" => $page
        );

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        //var_dump(get_defined_vars()); exit;

        // Render the view
        $this
            ->usersLibrary
            ->renderHttpView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                true
            );

    }

} 