<?php

/**
 * Library Class Users
 *
 * @author      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Auth
 * @subpackage  Users
 */

namespace Acme\Core\System\Modules\Auth\Users\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

use Acme\Core\System\Libraries\AcmeLibrary;

class UsersLibrary extends AcmeLibrary
{

    /**
     * The array to hold the Users config values
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
     * @var string $moduleName The name of the module this component belong to
     */
    public $moduleName;

    /**
     * @var string $componentName The name of this component
     */
    public $componentName;

    /**
     * The Codeigniter instance
     *
     * @var mixed $CI4
     */
    public $CI4 = null;

    /**
     * Initialise the constructor by setting up environmental variables
     * Get the values from the .env file
     * Pass the Codeigniter4 as a variable
     *
     * @var object $CI4
     */
    public function __construct( $CI4 )
    {

        // Initialise the params array for get and set methods
        $this->params = array();

        // Set health check vars
        $this->healthCheckName = array(
            "namespace" => __NAMESPACE__,
            "class" => __CLASS__,
            "method" => __METHOD__,
            "trait" => __TRAIT__,
            "file" =>__FILE__,
            "base" => "auth.users.library",
            "checksum" => "checksum.auth.users.library",
            "module" => "auth",
            "component" => "users"
        );

        // Set the Codeigniter4 instance from the variable
        $this->CI4 = $CI4;

        // Module Name
        $this->moduleName = "Auth";

        // Component Name
        $this->componentName = "Users";

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
     * Get the data from the database by passing an object with parameters
     *
     * @param $CI4
     * @param $method
     * @param $uuid
     * @param $layout
     * @param $dataArray
     * @param bool $isDashboard
     * @return array
     */
    public function renderView( $CI4, $method, $uuid, $layout, $dataArray, $params)
    {
        
        // Pre initialise the  ta object to hold the query criteria
        $dataObject = array();

        // Check if we have search criteria
        if(!empty($dataArray)){

            // Loop for each value pair
            foreach($dataArray as $k=>$v){

                // Extend the dataObject
                $dataObject[$k] = $v;

            }

        }

        // If the uuid is not set, then set it to all so that we retrieve all the results
        if( $uuid == null ){

            // Set the uuid to `all`
            $uuid = "all";

        }

        // Set to true and render the layout as a Users Dashboard
        $dataObject["params"] = $params;
            
        // Define the where clause
        if( $uuid != "all" ){

            // Set where clause by defining the uuid from the variable
            $dataObject["where"] = array("uuid" => $uuid);

        }

        // Specify the data type to expect from the database
        $dataObject["returnType"] = "result_array";

        // Method Name
        $dataObject["method"] = $method;

        // Allow localization of the view, i.e. make it translation ready
        $dataObject["language"] = $CI4->languageFile;

        // Language
        $dataObject["locale"] = $CI4->usersConfig->locale;

        // Get the record from the database
        $result = $CI4->usersModel->acmeUsersDBRead( $dataObject );

        // Auth > Users header namespace
        $ns_header  = 'Acme\Core\System\Modules\Auth\Users\Views\Headers\UsersHeader';

        // Auth > Users content namespace
        $ns_content = 'Acme\Core\System\Modules\Auth\Users\Views\Layouts\UsersLayout';

        // Auth > Users footer namespace
        $ns_footer  = 'Acme\Core\System\Modules\Auth\Users\Views\Footers\UsersFooter';

        // How long should we cache the view
        $cache = $CI4->usersConfig->config["view.cache_seconds"];
        
        // Render the View
        $this->renderHTMLView(
            $CI4->moduleName,
            $CI4->componentName,
            $result,
            $dataObject,
            $CI4->usersConfig
                ->config,
            $CI4->usersConfig
                ->systemConfig[
            "layouts.themes.backend.default"
            ],
            $CI4->usersConfig->locale,
            $layout,
            $ns_header,
            $ns_content,
            $ns_footer,
            $cache,
            $CI4->acmeRequest
        );

    }

    /**
     * Get the data from the database by passing an object with parameters
     *
     * @param $CI4
     * @param $method
     * @param $uuid
     * @param $layout
     * @param $dataArray
     * @param bool $isDashboard
     * @return array
     */
    public function renderHttpView( $CI4, $method, $uuid, $layout, $dataArray, $isDashboard=false)
    {

        // Pre initialise the  ta object to hold the query criteria
        $dataObject = array();

        // Check if we have search criteria
        if(!empty($dataArray)){

            // Loop for each value pair
            foreach($dataArray as $k=>$v){

                // Extend the dataObject
                $dataObject[$k] = $v;

            }

        }

        // If the uuid is not set, then set it to all so that we retrieve all the results
        if( $uuid == null ){

            // Set the uuid to `all`
            $uuid = "all";

        }

        // Check if the layout is a dashboard
        if( !$isDashboard ){

            // Proceed to render the normal view
            $dataObject["isDashboard"] = false;

        }else{

            // Set to true and render the layout as a Users Dashboard
            $dataObject["isDashboard"] = true;

        }

        // Define the where clause
        if( $uuid != "all" ){

            // Set where clause by defining the uuid from the variable
            $dataObject["where"] = array("uuid" => $uuid);

        }

        // Specify the data type to expect from the database
        $dataObject["returnType"] = "result_array";

        // Method Name
        $dataObject["method"] = $method;

        // Allow localization of the view, i.e. make it translation ready
        $dataObject["language"] = $CI4->languageFile;

        // Get the record from the database
        $result = $CI4->usersModel->acmeUsersDBRead( $dataObject );

        // Language
        $viewData["locale"] = $CI4->usersConfig->locale;

        // Auth > Users header namespace
        $ns_header  = 'Acme\Core\System\Modules\Auth\Users\Views\Headers\HttpHeader';

        // Auth > Users content namespace
        $ns_content = 'Acme\Core\System\Modules\Auth\Users\Views\Layouts\HttpLayout';

        // Auth > Users footer namespace
        $ns_footer  = 'Acme\Core\System\Modules\Auth\Users\Views\Footers\HttpFooter';

        // How long should we cache the view
        $cache = $CI4->usersConfig->config["view.cache_seconds"];

        // Render the View
        $this->renderHTMLView(
            $CI4->moduleName,
            $CI4->componentName,
            $result,
            $dataObject,
            $CI4->usersConfig
                ->config,
            $CI4->usersConfig
                ->systemConfig[
            "layouts.themes.frontend.default"
            ],
            $CI4->usersConfig->locale,
            $layout,
            $ns_header,
            $ns_content,
            $ns_footer,
            $cache
        );

    }

}