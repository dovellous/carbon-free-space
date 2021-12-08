<?php

/**
 * Config Class Dashboard
 *
 * @adminor      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Acme
 * @subpackage  Dashboard
 */

namespace Acme\Modules\Dashboard\Config;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

use Acme\System\Config\AcmeConfig;

class DashboardConfig extends AcmeConfig
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
     * The array to hold the Dashboard config values
     *
     * @var array $config
     */
    public $config = array();

    /**
     * The subscriptions for use with
     *
     * @var array $eventSubscriptions
     */
    public $eventSubscriptions = array();

    /**
     * Initialise the constructor by setting up environmental variables
     * Get the values from the .env file
     *
     * @var string $tablePrimaryColumn
     */
    public function __construct( $CI4Instance=null )
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
            "base" => "dashboard.config",
            "checksum" => "checksum.dashboard.config",
            "module" => "dashboard"
        );

        // Setup the system config values
        $this->setupConfig();

        // Get the system configurations and place them into the dashboard module
        $this->config["system"] = $this->systemConfig;

        // Create a regex to filter the relevant config settings for Dashboard
        $configRegex = "/^acme.config.dashboard/";

        // Get the .env variables
        $env = getenv();

        // Merge the variables from the _ENV and .env together
        $envars = array_merge($_ENV, $env);

        //Get the current Acme Dashboard event subscriptions
        $eventSubscriptionsObject = array(
            "tablePrefix" => "system_",
            "tableName" => "event_subscriptions",
            "tablePrimaryKey" => "event_id",
            "returnType" => "result_array",
            "where" => array("event_namespace"=>"acme.events.dashboard")
        );

        // Get the event metadata
        $dashboardEventSubscriptions = $CI4Instance->acmeDashboardDBRead($eventSubscriptionsObject);

        // Normalise the event metadata
        $this->eventSubscriptions = $CI4Instance->dashboardLibrary->normaliseEventSubscriptions($dashboardEventSubscriptions);


        //Get the current Acme Dashboard environment metadata
        $metaDataObject = array(
            "tablePrefix" => "system_",
            "tableName" => "environment_configurations",
            "tablePrimaryKey" => "environment_id",
            "returnType" => "result_array",
            "where" => array("env_namespace"=>"acme.config.dashboard")
        );

        // Get the environment metadata
        $dashboardMetadata = $CI4Instance->acmeDashboardDBRead($metaDataObject);

        // Normalise the environment metadata
        $normalisedDashboardData = $CI4Instance->dashboardLibrary->normaliseConfigMetaData($dashboardMetadata);

        // Loop each environment meta key, value pair
        foreach($envars as $k=>$v){

            // Pre initialise the value
            $value = null;

            // Streamline the key
            $key = str_replace("acme.config.dashboard.", "", $k);

            // Filter the configs to only those which petain to the dashboard
            preg_match($configRegex, $k, $matches, PREG_OFFSET_CAPTURE);

            //Check if we have a configuration in the database
            if(array_key_exists($key, $normalisedDashboardData)){

                // If the configuration exists in the database,
                // override the default .env value
                $value = $normalisedDashboardData[$key];

            }else {

                // The configuration does not exist in the database,
                // Load the default from the .env file

                if (!empty($matches)) {

                    $value = $v;

                }

            }

            if( $value != null ) {

                // Assign a meta data value to a meta key
                $this->config[$key] = $value;

            }

        }

        //Get all the constants variables in the system
        $constantsDataObject = array(
            "table" => "system_constants",
            "returnType" => "resultArray",
            "where" => array(
                "constant_is_active"=>1,
            ),
            "where_in" => array(
                "constant_namespace"=>array(
                    "acme.system.constants",
                    "acme.modules.dashboard.constants"
                )
            ),
            "sort" => array(
                "column" => "acme.system.constants",
                "direction" => "asc"
            ),     
            "groupBy" => "acme.system.constants"
        );

        // Get the metadata
        $constantsMetadata = $CI4Instance->legacyGetData($constantsDataObject);

        // Iterate each constant value to validate it
        foreach ($constantsMetadata as $constant){

            // Apply if the constant is not yet defined
            if(!defined( strtoupper($constant["constant_key"]))){

                // get the constant value from the database row
                $constantValue = acme_to_datatype($constant["constant_value"], $constant["constant_datatype"]);

                if($constantValue != NULL) {

                    // Define the constant into the global variable scope
                    define(strtoupper($constant["constant_key"]), $constantValue);

                }

            }

        }

        //Get all the view hooks in the system
        $hooksDataObject = array(
            "table" => "system_hooks",
            "returnType" => "resultArray",
            "where" => array(
                "hook_is_active"=>1
            ),
            "where_in" => array(
                "hook_namespace"=> array(
                    "acme.layouts.themes.default.hooks",
                    "acme.modules.dashboard.views.hooks"
                )
            )
        );

        // Get the metadata
        $hooksMetadata = $CI4Instance->legacyGetData($hooksDataObject);

        // Check the config to see if the array container for hooks has already been defined
        if(!array_key_exists("customHooks", $this->config)){

            $this->config["customHooks"] = array();

        }

        // Iterate each constant value to validate it
        foreach ($hooksMetadata as $hook){

            // Apply if the constant is not yet defined
            $this->config["customHooks"][$hook["hook_position"]] = $hook["hook_content"];

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

        if(array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }else{
            return NULL;
        }

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
        return $this->config[ $key ] = $value;
    }

}