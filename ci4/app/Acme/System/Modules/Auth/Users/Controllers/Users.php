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

use Acme\Core\System\Libraries\AuthLibrary;
use Acme\Core\System\Libraries\AutoLogin;
use Acme\Core\System\Libraries\GibberishaesLibrary;
use Acme\Core\System\Libraries\PasswordHash;
use Acme\Core\System\Libraries\ReCaptchaLibrary;
use Acme\Core\System\Modules\Auth\Users\Models\UsersModel;
//use Acme\Core\System\Modules\Auth\Users\Models\RolesModel;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class Users extends \Acme\Core\System\Modules\Auth\Users\Controllers\UsersBaseController
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

        $this->captcha = new ReCaptchaLibrary(
            $this->usersConfig->systemConfig['google.captcha.secret'],
            $this->usersConfig->systemConfig['google.captcha.enabled']
        );

        $this->aes = new GibberishaesLibrary();

        $this->passwordHash = new PasswordHash(
            array(
                'iteration_count_log2' => 8, 
                'portable_hashes' => FALSE
            )
        );

        $this->autoLogin = new AutoLogin($this->usersModel);

        $this->roles = new Roles(
            $request, 
            $response, 
            $logger
        );

        $this->auth = new AuthLibrary(
            $this, 
            $request
        );

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function index()
    {

        if($this->auth->loggedin()){

            //Parse the $user_data to get the user group landing page
            $this->launch();

        }else{

			$autoLoginData = $this->auth->checkAutoLogin();

			//var_dump($autoLoginData); exit;

            if($autoLoginData){

                //
                $this->launch(); 
                
            }else{

                //Initialise the array to hold the variables
                $this->ACMEDisplayForm("UsersLoginForm");

            }

        }

    }

    /**
     * Add a user, password will be hashed
     * 
     */
    public function ACMEDisplayForm($layout="UsersLoginForm", $uuid=NULL)
    {

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        $params = array(
            "views.header.layout" => "vertical",
            "views.header.style" => "auth_modern",
            "views.header.type" => "auth_modern",
            "views.footer.layout" => "vertical",
            "views.footer.style" => "auth_modern",
            "views.footer.type" => "auth_modern",
            );

        // Render the view
        $this
            ->usersLibrary
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
    public function ACMEDisplayView($layout="UsersLoginForm", $uuid=NULL)
    {

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);
        
        $params = array(
            "themes.backend.layout" => "vertical",
            "themes.backend.style" => "default",
            "themes.backend.header" => "default",
            "themes.backend.footer" => "default",
            );

        // Render the view
        $this
            ->usersLibrary
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
    public function ACMEPasswordReset()
    {

        //
        $this->ACMEDisplayForm("UsersLoginForm");

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function ACMEAccountRecover()
    {

        //
        $this->ACMEDisplayForm("UsersRecoverPassword");

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function ACMELockScreen()
    {

        //
        $this->ACMEDisplayForm("UsersLockScreen");

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function ACMEEmailConfirmation()
    {

        //
        $this->ACMEDisplayForm("UsersEmailConfirmation");

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    public function ACMEAuthenticateUser($encrypt=FALSE)
    {

        $captchaResponse = NULL;

        //
        if(acme_get_env("acme.config.system.google.captcha.enabled", "bool")){

            //
            $captchaResponse = $this->captcha->verify();

            if ($captchaResponse != null && $captchaResponse->success) {

                $response = true;

            }else{

                $response = false;

            }

        }else{

            $response = true;

        }

        //
        if ($response) {

            $result = $this->auth->signIn($encrypt);

            //
            if($result["status"] == "success"){

                $this->session->setFlashdata(
                    'fmsg_auth',
                    array(
                        "class"=>"success",
                        "title"=>"Success",
                        "message"=>$result["message"]
                    )
                );

                $this->launch($result);

            }else{

                //
                $this->session->setFlashdata(
                    'fmsg_auth',
                    array(
                        "class"=>"danger",
                        "title"=>"Login Error!",
                        "message"=>$result["message"]
                    )
                );

                //
                $this->showLogin();

            }

        }
        else{

            //
            $errors = $captchaResponse->errorCodes;

            //
            $error_html="";

            //
            if(!empty($errors) && $errors != NULL){

                $error_html .= "<br>Reason: ";

                if(is_array($errors)){
                    $error_html .= implode("<br>", $errors);
                }else{
                    $error_html .= "$errors";
                }

            }

            //
            $error_html = "<p><code>$error_html</code></p>";

            //
            $this->session->setFlashdata(
                'fmsg_auth',
                array(
                    "class"=>"danger",
                    "title"=>"Capture Error!",
                    "message"=>$error_html
                )
            );

            //
            acme_redirect( acme_base_url("login") );

        }

    }

    /**
     * Register the user into the system
     * Redirect the user to a page depending on the $result
     * Successful registration open another page to complete registration
     * Error can be due to invalid captcha
     */
    public function ACMERegisterUser()
    {

        $response = $this->captcha->verify();

        if ($response != null && $response->success) {

            $result = $this->sign_up();

            //
            if($result["status"] == "success"){

                $this->session->setFlashdata('fmsg_auth', array("class"=>"success", "title"=>"Success", "message"=>$result["message"]));

                $this->complete_registration($result);

            }else{

                $this->session->setFlashdata('fmsg_auth', array("class"=>"danger", "title"=>"Registration Error!", "message"=>$result["message"]));

                acme_redirect( acme_base_url("register") );

            }

        }else{ //

            $errors = $response->errorCodes;

            $error_html="";

            //
            if(!empty($errors) && $errors != NULL){

                $error_html .= "<span class='reason'>Reason: </span>";

                if(is_array($errors)){
                    $error_html .= implode("<br>", $errors);
                }else{
                    $error_html .= "$errors";
                }

            }

            $error_html = "<p><code>$error_html</code></p>";

            //
            $this->session->setFlashdata('fmsg_auth', array("class"=>"danger", "title"=>"Capture Error!", "message"=>"Captcha verification has failed. Please try again" . $error_html));



            acme_redirect( acme_base_url("register") );

        }

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @access     public
     * @return     array     registration data result object
     */
    public function ACMESignIn($encrypt)
    {

        //
        $result = $this->ACMEAuthenticateUser( $encrypt);

        //
        return $result;

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @access     public
     * @return     array     registration data result object
     */
    public function ACMESignUp()
    {

        //
        $user = $this->auth->prepare_new_user();

        //
        $result = $this->auth->register_user( $user );

        //
        return $result;

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    public function ACMELogout()
    {

        //
        $this->auth->signOut();

        //Include flash message to show alert
        acme_redirect( acme_base_url("login") );

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    public function ACMEDashboard()
    {

        $uuid = "";

        $layout = "LAYOUT";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array();

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        // Render the view
        $this
            ->usersLibrary
            ->renderView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                true
            );

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    public function ACMELanding($landingPage)
    {

        $uuid = "";

        $layout = "Users" . ucwords($landingPage);

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array(
            "fullHeader"=>true
        );

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        // Render the view
        $this
            ->usersLibrary
            ->renderView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                true
            );

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    public function landing($landingPage)
    {

        $this->landing($landingPage);

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    public function ACME404($page)
    {

        $uuid = "";

        $layout = "http404";

        // Pre initialise the  ta object to hold the query criteria
        $dataArray = array(
            "page" => $page
        );

        // Attach the method name
        $method = array(__NAMESPACE__, __CLASS__, __METHOD__);

        // Render the view
        $this
            ->usersLibrary
            ->renderView(
                $this,
                $method,
                $uuid,
                $layout,
                $dataArray,
                true
            );

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    public function ACMELogin()
    {

        //
        $this->index();

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    private function login()
    {

        //
        $this->ACMELogin();

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    private function launch()
    {

        if($this->getUserSessionData() != NULL) {

            $url = $this->auth->getUserLandingPage();

            acme_redirect( acme_base_url("landing" . "/" . $url) );

        }else{

            $this->showLogin();

        }

    }

    /**
     * Insert data into the database and return the result as array
     *
     * Delete a user
     *
     * @return array registration data result object
     */
    private function showLogin()
    {

        $url = $this->auth->getUserLoginPage();

        acme_redirect( acme_base_url() . "/" . $url );

    }

} 