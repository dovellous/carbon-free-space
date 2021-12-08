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

namespace Acme\System\Controllers;


use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class AcmeBaseController extends \App\Controllers\BaseController
{

    /**
     * Session data
     *
     * @var array $session
     */
    public $session;

    /**
     * CSRF Session data
     *
     * @var array $csrfData
     */
    public $csrfData;

    /**
     * The default language
     *
     * @var string $language
     */
    protected $language;

    /**
     * The default locale
     *
     * @var string $locale
     */
    public $locale;

    /**
     * The default language
     *
     * @var string $locale
     */
    public $acmeRequest;

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

        helper(
            array(
                'array',
                'cookie',
                'session',
                'date',
                'filesystem',
                'form', 'html',
                'inflector',
                'number',
                'security',
                'text',
                'xml'
            )
        );

        // Ensure that the session is started and running
        if (session_status() == PHP_SESSION_NONE)
        {
            $this->session = \Config\Services::session();
        }

        if(!$this->session || $this->session === NULL){

            $this->session = \Config\Services::session();

        }else {

            // Check to see if the session has been started
            if (!$this->session->get() || empty($this->session->get())) {

                // Start the session if not started
                $this->session->start();

            }

        }

        $sessionFlashDataCSRFArray = $this->session->getFlashdata('csrfData');



        if($sessionFlashDataCSRFArray == null){

            $previousCSRFToken = md5(time());

            $previousCSRFHash  = md5(time());

        } else {

            $previousCSRF = $sessionFlashDataCSRFArray['current'];

            $previousCSRFToken = $previousCSRF['token'];

            $previousCSRFHash = $previousCSRF['hash'];

        }

        $currentCSRFToken = csrf_token();

        $currentCSRFHash  = csrf_hash();

        $this->csrfData = array(
            "previous"=> array("token"=>$previousCSRFToken, "hash"=>$previousCSRFHash),
            "current" => array("token"=>$currentCSRFToken,  "hash"=>$currentCSRFHash)
        );

        $this->session->setFlashdata( 'csrfData', $this->csrfData );

        $this->session->set("csrfData", $this->csrfData);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        // $this->session = \Config\Services::session();

        // Start the request
        $this->acmeRequest = $request;

        // Setup the supported languages
        $supportedLangs = ['en', 'en-US', 'fr'];

        // Do some negotiation
        $this->language = $this->acmeRequest->negotiate('language', $supportedLangs);

        //Get the locale from the get vars
        $locale = $this->acmeRequest->getGet("locale");

        //check if we have a locale iin the get params
        if($locale){

            // Set the locale to a session variable
            $this->session->set("locale", $locale);

            // Set the locale to a cookie variable
            set_cookie("locale", $locale);

            // Assign it to a property
            $this->locale = $locale;

        }else{

            // If the language has not been satisfied in the get
            // Look for it in the session data
            if($this->session->get("locale")){

                // Set the property based on the value in the session data
                $this->locale = $this->session->get("locale");

            } else {

                // Set the property based on the value iin the cookie vars
                $this->locale = get_cookie("locale");

            }


        }

        if(!$this->locale) {

            // Set the locale
            $this->locale = $this->acmeRequest->getLocale();

        }

        // Write to system journal
        \Acme\System\Libraries\AcmeLibrary::writeAccessJournal(
            uri_string(),
            $this->acmeRequest->uri->getHost(),
            $this->acmeRequest->uri->getPath(),
            $this->acmeRequest->uri->getPort(),
            $this->acmeRequest->uri->getSegments(),
            $this->acmeRequest->uri->getFragment(),
            $this->acmeRequest->uri->getQuery(),
            $this->acmeRequest->getVar()
        );

    }

}
