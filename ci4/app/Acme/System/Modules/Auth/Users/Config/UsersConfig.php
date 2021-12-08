<?php

/**
 * Config Class Users
 *
 * @author      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Auth
 * @subpackage  Users
 */

namespace Acme\Core\System\Modules\Auth\Users\Config;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

use Acme\Core\System\Config\AcmeConfig;

class UsersConfig extends AcmeConfig
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
     * The array to hold the Users config values
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
            "base" => "auth.users.config",
            "checksum" => "checksum.auth.users.config",
            "module" => "auth",
            "component" => "users"
        );

        // Setup the system config values
        $this->setupConfig();

        // Get the system configurations and place them into the users component
        $this->config["system"] = $this->systemConfig;

        // Create a regex to filter the relevant config settings for Users
        $configRegex = "/^acme.config.auth.users/";

        // Get the .env variables
        $env = getenv();

        // Merge the variables from the _ENV and .env together
        $envars = array_merge($_ENV, $env);

        //Get the current Auth Users event subscriptions
        $eventSubscriptionsObject = array(
            "tablePrefix" => "system_",
            "tableName" => "event_subscriptions",
            "tablePrimaryKey" => "evnt_id",
            "returnType" => "result_array",
            "where" => array("evnt_namespace"=>"acme.events.auth.users")
        );

        // Get the event metadata
        $usersEventSubscriptions = $CI4Instance->acmeUsersDBRead($eventSubscriptionsObject);

        // Normalise the event metadata
        $this->eventSubscriptions = $CI4Instance->usersLibrary->normaliseEventSubscriptions($usersEventSubscriptions);


        //Get the current Auth Users environment metadata
        $metaDataObject = array(
            "tablePrefix" => "system_",
            "tableName" => "environment_configurations",
            "tablePrimaryKey" => "env_id",
            "returnType" => "result_array",
            "where" => array("env_namespace"=>"acme.config.auth.users")
        );

        // Get the environment metadata
        $usersMetadata = $CI4Instance->acmeUsersDBRead($metaDataObject);

        // Normalise the environment metadata
        $normalisedUsersData = $CI4Instance->usersLibrary->normaliseConfigMetaData($usersMetadata);

        // Loop each environment meta key, value pair
        foreach($envars as $k=>$v){

            // Pre initialise the value
            $value = null;

            // Streamline the key
            $key = str_replace("acme.config.auth.users.", "", $k);

            // Filter the configs to only those which petain to the users
            preg_match($configRegex, $k, $matches, PREG_OFFSET_CAPTURE);

            //Check if we have a configuration in the database
            if(array_key_exists($key, $normalisedUsersData)){

                // If the configuration exists in the database,
                // override the default .env value
                $value = $normalisedUsersData[$key];

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
                    "acme.core.system.constants",
                    "acme.core.system.modules.auth.users.constants"
                )
            ),
            "sort" => array(
                "column" => "acme.core.system.constants",
                "direction" => "asc"
            ),     
            "groupBy" => "acme.core.system.constants"
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
                    "acme.layouts.themes.nakai.hooks",
                    "acme.core.system.modules.auth.users.views.hooks"
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