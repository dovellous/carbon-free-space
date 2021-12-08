<?php

/**
 * Library Class Dashboard
 *
 * @adminor      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Acme
 * @subpackage  Dashboard
 */

namespace Acme\Modules\Dashboard\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

use Acme\System\Libraries\AcmeLibrary;

class DashboardLibrary extends AcmeLibrary
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
     * @var string $moduleName The name of this module
     */
    public $moduleName;

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
            "base" => "dashboard.library",
            "checksum" => "checksum.dashboard.library",
            "module" => "dashboard"
        );

        // Set the Codeigniter4 instance from the variable
        $this->CI4 = $CI4;

        // Module Name
        $this->moduleName = "Dashboard";

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

        // Set to true and render the layout as a Dashboard Dashboard
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
        $dataObject["locale"] = $CI4->dashboardConfig->locale;

        // Get the record from the database
        $result = $CI4->dashboardModel->acmeDashboardDBRead( $dataObject );

        // Acme > Dashboard header namespace
        $ns_header  = 'Acme\Modules\Dashboard\Views\Headers\DashboardHeader';

        // Acme > Dashboard content namespace
        $ns_content = 'Acme\Modules\Dashboard\Views\Layouts\DashboardLayout';

        // Acme > Dashboard footer namespace
        $ns_footer  = 'Acme\Modules\Dashboard\Views\Footers\DashboardFooter';

        // How long should we cache the view
        $cache = $CI4->dashboardConfig->config["view.cache_seconds"];
        
        // Render the View
        $this->renderHTMLView(
            $CI4->moduleName,
            $result,
            $dataObject,
            $CI4->dashboardConfig
                ->config,
            $CI4->dashboardConfig
                ->systemConfig[
            "layouts.themes.backend.default"
            ],
            $CI4->dashboardConfig->locale,
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

            // Set to true and render the layout as a Dashboard Dashboard
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
        $result = $CI4->dashboardModel->acmeDashboardDBRead( $dataObject );

        // Language
        $viewData["locale"] = $CI4->dashboardConfig->locale;

        // Acme > Dashboard header namespace
        $ns_header  = 'Acme\Modules\Dashboard\Views\Headers\HttpHeader';

        // Acme > Dashboard content namespace
        $ns_content = 'Acme\Modules\Dashboard\Views\Layouts\HttpLayout';

        // Acme > Dashboard footer namespace
        $ns_footer  = 'Acme\Modules\Dashboard\Views\Footers\HttpFooter';

        // How long should we cache the view
        $cache = $CI4->dashboardConfig->config["view.cache_seconds"];

        // Render the View
        $this->renderHTMLView(
            $CI4->moduleName,
            $result,
            $dataObject,
            $CI4->dashboardConfig
                ->config,
            $CI4->dashboardConfig
                ->systemConfig[
            "layouts.themes.frontend.default"
            ],
            $CI4->dashboardConfig->locale,
            $layout,
            $ns_header,
            $ns_content,
            $ns_footer,
            $cache
        );

    }

}