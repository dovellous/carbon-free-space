<?php

/**
 * Model Class Users
 *
 * @author      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Auth
 * @subpackage  Users
 */

namespace Acme\Core\System\Modules\Auth\Users\Models;

/*
 * Make sure there is no direct access to the script
 */
if (!defined('ACME_NAMESPACE')) ACME_exception(null, 'The application namespace is undefined. Please check your installation');

/*
 * Import the base model
 */

use CodeIgniter\Events\Events;
use Acme\Core\Bases\AcmeModel;
use Acme\Core\System\Modules\Auth\Users\Config\UsersConfig;
use Acme\Core\System\Modules\Auth\Users\Libraries\UsersLibrary;

// Note: Do not edit this file

class RolesBaseModel extends AcmeModel
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
     * Initialize the configurations for the users
     *
     * @var class $usersConfig
     */
    public $usersConfig;

    /**
     * Initialize the configurations for the system
     *
     * @var class $systemConfig
     */
    public $systemConfig;

    /**
     * Initialize the libraries for the users
     *
     * @var class $usersLibrary
     */
    public $usersLibrary;

    /**
     * The namespace in Acme notation for Users events and configurations
     *
     * @var string $acmeNamespace
     */
    public $acmeNamespace;

    /**
     * The namespace in Acme notation for Users configurations
     *
     * @var string $acmeConfigNamespace
     */
    public $eservConfigNamespace;

    /**
     * The namespace in Acme notation for Users libraries
     *
     * @var string $acmeLibraryNamespace
     */
    public $acmeLibraryNamespace;

    /**
     * Specifies the database table that this model primarily works with.
     * This only applies to the built-in CRUD methods.
     * We are not restricted to using only this table in our own queries.
     *
     * @var string $databaseTable
     */
    public $databaseTable;

    /**
     * This is the name of the column that uniquely identifies the records in this table.
     * This does not necessarily have to match the primary key that is specified in the database,
     * but is used with methods like find() to know what column to match the specified value to.
     *
     * @note All Models must have a primaryKey specified to allow all of the features to work as expected.
     *
     * @var string $databaseTablePrimaryColumn
     */
    public $databaseTablePrimaryColumn;

    /**
     * UsersBaseModel constructor.
     */
    public function __construct()
    {

        parent::__construct();

        // Initialise the params array for get and set methods
        $this->params = array();
        
        // Set health check vars
        $this->healthCheckName = array(
            "namespace" => __NAMESPACE__,
            "class" => __CLASS__,
            "method" => __METHOD__,
            "trait" => __TRAIT__,
            "file" =>__FILE__,
            "base" => "auth.users.model",
            "checksum" => "checksum.auth.users.model",
            "module" => "auth",
            "component" => "users"
        );

        // The current model namespace
        $this->acmeNamespace = "Acme.Auth.Users";

        // Module configs namespace
        $this->acmeConfigNamespace = "Acme\\Modules\\Auth\\Users\\Config\\UsersConfig";

        // Module library namespace
        $this->acmeLibraryNamespace = "Acme\\Modules\\Auth\\Users\\Libraries\\UsersLibrary";

        // Import users Helper Functions
        helper('Acme\Core\System\Modules\Auth\Users\Helpers\users');

        // Load the users Libraries
        $this->usersLibrary = new UsersLibrary( $this );

        // Load the users Configurations
        $this->usersConfig = new UsersConfig( $this );

        // Set system config
        $this->systemConfig = $this->getSystemConfig();

        // Include the system configs in the users configs
        if($this->usersConfig->systemConfig == null){

            // This should never happen, but lets see if the module
            // can access the system config, if it doesnt, assign
            $this->usersConfig->systemConfig = $this->systemConfig;

            // Only do so if the sys config is defined otherwise
            // we risk overriding them with null values
            if($this->usersConfig->systemConfig != null) {

                // Check if the users config is already an array
                if(is_array($this->usersConfig->config)) {

                    // include the system configs in the module config
                    if (array_key_exists("system.config", $this->usersConfig->config)) {

                        $this->usersConfig->config["system.config"] = $this->usersConfig->systemConfig;

                    }

                }

            }

        }


        // Register event listeners for Users

        // Get a list of event listeners
        $eventMethodsToSubscribeTo = $this->usersConfig->eventSubscriptions;

        // For each event register a handler
        foreach($eventMethodsToSubscribeTo as $event) {

            Events::on(
                $event["eventName"],
                function( ...$args ) use ($event){

                    // Get the input and output data
                    $meta = $args[0]["params"];

                    // Add more variables specific to this component

                    // Add the library namespace
                    /**
                     * @depricated $acmeLibraryNamespace this may no longer be necessary, consider removing it
                     */
                    $meta["eventNamespaceLibrary"] = $this->acmeLibraryNamespace;
                    
                    // Add a class namespace for Users
                    $meta["eventNamespace"] = __NAMESPACE__;
                    
                    // Add the Users Class name
                    $meta["eventNamespace"] = __CLASS__;
                    
                    // add the module name
                    $meta["eventModule"] = $this->usersLibrary->moduleName;
                    
                    // Add the component name
                    $meta["eventComponent"] = $this->usersLibrary->componentName;
                    
                    // Add an object containing the event specific configs
                    $meta["eventObject"] = $event;

                    // Sort the array keys
                    ksort($meta);

                    // Call the vent handler
                    $this->usersLibrary->onEventHandler( $meta );

                }
            );

        }

        // Set the table prefix for the database
        $this->databaseTable = $this->usersConfig->config['table.name'];

        $this->table = $this->databaseTable;

        // Set the table prefix for the database
        $this->databaseTablePrimaryColumn = $this->usersConfig->config['table.primary.column'];

        $this->primaryKey = $this->databaseTablePrimaryColumn;

        $this->returnType = 'array';

        // Set the table prefix for the database
        $this->databaseTablePrefix = $this->usersConfig->config['table.prefix'];

        $this->tablePrefix = $this->databaseTablePrefix;

        $this->returnType = 'array';

        // Set the table prefix for the database
        $this->db->setPrefix( $this->tablePrefix );

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

    //--------------------------------------------------------------------
    //       Function :: TABLE NAME ::
    //--------------------------------------------------------------------

    /**
     * Get the table name for the operation
     *
     * @param string $dataObject
     * @return string
     */
    private function getTableName($dataObject)
    {

        // Check if the table name has been specified
        if( array_key_exists("tableName", $dataObject)){

            // Set the table name specified in the user data object
            $databaseTable = $dataObject["tableName"];

        } else {

            // Or else use the default table name for the Users Class
            $databaseTable = $this->databaseTable;

        }

        // Return the relevant data table
        return $databaseTable;

    }

    
    //--------------------------------------------------------------------
    //       Function :: CREATE :: 
    //--------------------------------------------------------------------

    /**
     * Insert a new row into the database by passing an object with parameters
     *
     * @param string $dataObject
     * @return array
     */
    private function insertDataRow($dataObject)
    {

        // Get the table name for this database operation
        $databaseTable = $this->getTableName($dataObject);

        //insert new `Users` row into the database table `$this->databaseTable`
        $result = $this->insertData( $databaseTable, $dataObject );

        // Include the tablename in the event dispatcher
        $params = array(
            "namespace" => "acme.events.auth.users",
            "tableName" => $databaseTable,
            "method" => "PUT",
            "result" => $result
        );

        // Trigger the read event
        $this->usersLibrary->dispatchEvent($params, $dataObject, $result);

        // Return the result
        return $result;

    }

    // -- END CREATE

    //--------------------------------------------------------------------
    //       Function :: READ :: 
    //--------------------------------------------------------------------

    /**
     * Get the data from the database by passing an object with parameters in the `Users` table
     *
     * @param string $dataObject
     * @return array
     */
    private function readDataRow($dataObject)
    {

        // Get the table name for this database operation
        $databaseTable = $this->getTableName($dataObject);

        //insert new `Users` row into the database table `$this->databaseTable`
        $result = $this->readData( $databaseTable, $dataObject );

        // Include the tablename in the event dispatcher
        $params = array(
            "namespace" => "acme.events.auth.users",
            "tableName" => $databaseTable,
            "method" => "GET",
            "result" => $result
        );

        // Trigger the read event
        $this->usersLibrary->dispatchEvent($params, $dataObject, $result);

        // Return the result
        return $result;

    }

    // -- END READ

    //--------------------------------------------------------------------
    //       Function :: UPDATE :: 
    //--------------------------------------------------------------------

    /**
     * Update an existing row in the `Users` table
     *
     * @param string $id
     * @param string $dataObject
     * @return array
     */
    private function updateDataRow( $id, $dataObject)
    {

        // Get the table name for this database operation
        $databaseTable = $this->getTableName($dataObject);

        //insert new `Users` row into the database table `$this->databaseTable`
        $result = $this->updateData( $databaseTable, $id, $dataObject );

        // Include the tablename in the event dispatcher
        $params = array(
            "namespace" => "acme.events.auth.users",
            "tableName" => $databaseTable,
            "method" => "SET",
            
            "id" => $id
        );

        // Trigger the read event
        $this->usersLibrary->dispatchEvent($params, $dataObject, $result);

        // Return the result
        return $result;

    }

    // -- END UPDATE

    //--------------------------------------------------------------------
    //       Function :: DELETE :: 
    //--------------------------------------------------------------------

    /**
     * Delete a specific row in the `Users` table
     *
     * @param string $id
     * @param array $dataObject
     * @return array
     */
    private function deleteDataRow($id, $dataObject)
    {

        // Get the table name for this database operation
        $databaseTable = $this->getTableName($dataObject);

        //insert new `Users` row into the database table `$this->databaseTable`
        $result = $this->deleteData( $databaseTable, $id );

        // Include the tablename in the event dispatcher
        $params = array(
            "namespace" => "acme.events.auth.users",
            "tableName" => $databaseTable,
            "method" => "DEL",
            
            "id" => $id
        );

        // Trigger the read event
        $this->usersLibrary->dispatchEvent($params, $dataObject, $result);

        // Return the result
        return $result;

    }

    // -- END DELETE

    /**
     * Find all rows
     *
     * @param string  $uuid
     * @return array
     */
    public function ACMEUsersGetIdFromUUID( $uuid )
    {

        $dataObject = array(
            "where" => array(
                "uuid" => $uuid
            ),
            "returnType" => "row_array"
        );

        //Read data from the database
        $result = $this->ACMEUsersDBRead( $dataObject);

        return $result["resultObject"][$this->databaseTablePrimaryColumn];

    }

    /**
     * Find all rows
     *
     * @param array $dataObject
     * @return array
     */
    public function ACMEUsersDBRead( $dataObject)
    {

        // Write to system journal
        $this->usersLibrary->writeDBJournal(
            "DB_GET",
            "Auth",
            "Users",
            "UsersDBRead",
            array(
                "dataObject"=>$dataObject
            )
        );

        return $this->readDataRow( $dataObject );

    }

    /**
     * Insert a new row
     *
     * @param array $dataObject
     * @return array
     */
    public function ACMEUsersDBInsert( $dataObject)
    {

        // Write to system journal
        $this->usersLibrary->writeDBJournal(
            "DB_PUT",
            "Auth",
            "Users",
            "UsersDBInsert",
            array(
                "dataObject"=>$dataObject
            )
        );

        return $this->insertDataRow( $dataObject );

    }

    /**
     * Update an existing row
     *
     * @param int $id
     * @param array $dataObject
     * @return array
     */
    public function ACMEUsersDBUpdate( $id, $dataObject)
    {

        // Write to system journal
        $this->usersLibrary->writeDBJournal(
            "DB_SET",
            "Auth",
            "Users",
            "UsersDBUpdate",
            array(
                "id"=>$id,
                "dataObject"=>$dataObject
            )
        );

        return $this->updateDataRow( $id, $dataObject );

    }

    /**
     * Delete an existing row
     *
     * @param int $id
     * @param array $dataObject
     * @return array
     */
    public function ACMEUsersDBDelete( $id, $dataObject )
    {
        
        // Write to system journal
        $this->usersLibrary->writeDBJournal(
            "DB_DEL", 
            "Auth",
            "Users",
            "UsersDBDelete",
            array(
                "id"=>$id, 
                "dataObject"=>$dataObject
            )
        );
        
        // Call parent function to execute
        return $this->deleteDataRow( $id, $dataObject );

    }

    /**
     * Delete an existing row
     *
     * @return array
     */
    public function ACMEValidateRequest()
    {

        $request = $this->usersLibrary->validateRequest(  'post' );

        return $request;

    }

}