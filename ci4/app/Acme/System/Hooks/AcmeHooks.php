<?php

namespace AcmeSystem\Config;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

use CodeIgniter\Config\BaseConfig;

class AcmeHooks extends BaseConfig
{

    /**
     * The table name for the database
     *
     * @var string $tableName
     */
    public $config = array();

    /**
     * Initialise the constructor by setting up environmental variables
     * Get the values from the .env file
     *
     * @var string $tablePrimaryColumn
     */
    public function __construct( $parentInstance=null )
    {

        $configRegex = "/^acme.config.system/";

        $env = getenv();

        $envars = array_merge($_ENV, $env);

        foreach($envars as $k=>$v){

            preg_match($configRegex, $k, $matches, PREG_OFFSET_CAPTURE);

            if(!empty($matches)){
                
                $k = str_replace("acme.config.system.","",$k);

                $this->config[$k]=$v;

            }

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
        return $this->config[ $key ];
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