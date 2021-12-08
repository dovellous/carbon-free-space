<?php

namespace Acme\System\Config;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

use CodeIgniter\Config\BaseConfig;

class AcmeConfig extends BaseConfig
{

    /**
     * The table name for the database
     *
     * @var string $tableName
     */
    public $systemConfig = array();

    /**
     * Initialise the constructor by setting up environmental variables
     * Get the values from the .env file
     *
     * @var string $tablePrimaryColumn
     */
    public function __construct( $parentInstance=null )
    {

        // Setup system config if not already done so
        if(empty($this->systemConfig)){
            
            $this->setupConfig();
            
        }
        
    }

    /**
     * Setup the system config vaalues
     */
    public function setupConfig()
    {

        // Get all the system environmental variables
        $this->systemConfig = acme_streamline_env("acme.config.system");

        //Get the public *views, *modules, *components
        $this->systemConfig["public"]= acme_streamline_env("acme.config.public", false, "array");

    }

    /**
     * Get the key value
     *
     * @param string $key
     * @return string
     */
    public function __get( $key )
    {

        if(array_key_exists($key, $this->systemConfig)) {
            return $this->systemConfig[$key];
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

        return $this->systemConfig[ $key ] = $value;

    }

}