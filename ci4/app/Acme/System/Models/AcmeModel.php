<?php
namespace Acme\System\Models;

/**
 * Class AcmeModel
 *
 * As many lines of extendend description as you want {@link element} links to an element
 * {@link http://www.example.com Example hyperlink inline link} links to a website
 * Below this goes the tags to further describe element you are documenting
 *
 * @param    type $varname description
 * @return    type    description
 * @access    public or private
 * @author    author name
 * @copyright    name date
 * @version    version
 * @see        name of another element that can be documented, produces a link to it in the documentation
 * @link        a url
 * @since    a version or a date
 * @deprecated    description
 * @deprec    alias for deprecated
 * @magic    phpdoc.de compatibility
 * @todo        phpdoc.de compatibility
 * @exception    Javadoc-compatible, use as needed
 * @throws    Javadoc-compatible, use as needed
 * @var        type    a data type for a class variable
 * @package    package name
 * @subpackage    sub package name, groupings inside of a project
 */

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

use Config\Database;

use CodeIgniter\Model;

use Acme\System\Config\AcmeConfig;

use CodeIgniter\Database\ConnectionInterface;

class AcmeModel extends Model
{

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array session
     */
    public $session = null;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    public $db = null;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $systemConfig = null;

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
     */
    public function __construct( ConnectionInterface &$db = null )
    {

        $this->db = \Config\Database::connect();

        // Ensure that the session is started and running
        if (session_status() == PHP_SESSION_NONE)
        {
            $this->session = \Config\Services::session();
        }

        //--------------------------------------------------------------------
        // Preload system models, libraries, configs here.
        //--------------------------------------------------------------------

        // Import Global Helper Functions
        helper('Acme\System\Helpers\acme');

        // Load the module  Configurations
        $this->systemConfig = new AcmeConfig();

    }

    /**
     * Log database error
     */
    protected function databaseLogError()
    {

        //--------------------------------------------------------------------
        // Preload system models, libraries, configs here.
        //--------------------------------------------------------------------

        die("Database Error!!!");

    }

    /**
     * Get the system configuration
     * 
     * @return array
     */
    public function getSystemConfig()
    {

        return $this->systemConfig->config;

    }

    /**
     * Get the system configuration
     *
     * @return array
     */
    public function getPrimaryKeyColumn( $tableName )
    {

        $fields = $this->db->getFieldNames($tableName);
        
        return $fields[0];

    }

    //--------------------------------------------------------------------
    //       Function :: CREATE :: 
    //--------------------------------------------------------------------

    /**
     * Get the data from the database by passing an object with parameters
     *
     * @param array $databaseTable The
     * @param array $dataObject
     * @return array
     */
    protected function insertData($databaseTable, $dataObject)
    {

        // Begin a transaction
        $this->db->transStart();

        // Check if the user specified an sql query
        if(isset($dataObject["sql"])){

            $sql = $dataObject["sql"];

        } else {

            //initialise the builder object
            $builder = $this->db->table($databaseTable);

            // Get the SQL query string
            $sql = $builder->set($dataObject)->getCompiledInsert();

        }

        // Execute the query
        $result = $this->db->query($sql);

        // If the commit was unsuccessful, handle the error and log it
        if ($this->db->transStatus() === FALSE) {

            $this->databaseLogError();


            // Roll back
            $this->db->transRollback();

        } else {

            // Commit changes
            $this->db->transCommit();

        }

        // Prepare the object array to hold the return data
        $resultObjectArray = array();

        // Get the transaction result
        $resultObjectArray["resultObject"] = $result;

        // Get the last sql query
        $resultObjectArray["lastQuery"] = $sql;

        // Get the last inserted id
        $resultObjectArray["lastInsertID"] = $this->db->insertID();

        // Return the result object array
        return $resultObjectArray;

    }


    //--------------------------------------------------------------------
    //       Function :: READ :: 
    //--------------------------------------------------------------------

    /**
     * Get the data from the database by passing an object with parameters
     *
     * @param array $databaseTable
     * @param array $queryObject
     * @return array
     */
    protected function readData($databaseTable, $queryObject)
    {

        //check if there are specific columns to retreive
        if (isset($queryObject["getSummaryOnly"])) {

            return $this->summaryData($queryObject);

        }

        //check if there are specific columns to retreive
        if (isset($queryObject["tablePrefix"])) {

            $this->db->setPrefix( $queryObject["tablePrefix"] );

        }

        // Begin a transaction
        $this->db->transStart();

        //initialise the builder object
        $builder = $this->db->table($databaseTable);

        // Check if the user specified an sql query
        if(isset($dataObject["sql"])){

            $sql = $dataObject["sql"];

            // Execute the query
            $query = $this->db->query($sql);

        } else {

            //check if there are specific columns to retrieve
            if (isset($queryObject["columns"])) {

                $builder->select(
                    implode(",", $queryObject["columns"])
                );

            } else {

                $builder->select();

            }

            //check if there is a where clause
            if (isset($queryObject["where"])) {

                $builder->where($queryObject["where"]);

            }

            //check if we are paginating the results or limit
            if (isset($queryObject["limit"])) {

                if (isset($queryObject["limit"]["rows"])) {

                    if (isset($queryObject["limit"]["offset"])) {

                        $builder->limit($queryObject["limit"]["rows"], $queryObject["limit"]["offset"]);

                    } else {

                        $builder->limit($queryObject["limit"]["rows"]);

                    }

                }

            }

            //check if we are supposed to sort the results by a specific column
            if (isset($queryObject["orderBy"])) {

                if (isset($queryObject["orderBy"]["column"])) {

                    if (isset($queryObject["orderBy"]["sort"])) {

                        $builder->orderBy($queryObject["orderBy"]["column"], $queryObject["orderBy"]["sort"]);

                    } else {

                        $builder->orderBy($queryObject["orderBy"]["column"], 'ASC');

                    }

                }

            }

            // Get the SQL query string
            $query = $builder->get();

        }

        // Prepare the array to hold the result
        $result = array();

        // Get the results by case switching the return type

        if (isset($queryObject["returnType"])) {

            switch ($queryObject["returnType"]) {

                // Return the row array
                case "row_array" : {

                    $result = $query->getRowArray();

                    break;

                }

                // Return the result array
                case "result_array" : {

                    $result = $query->getResultArray();

                    break;

                }

                // Return the result object
                default : {

                    $result = $query->getResult();

                    break;

                }

            }

        }

        // If the commit was unsuccessful, handle the error and log it
        if ($this->db->transStatus() === FALSE) {

            $this->databaseLogError();

            // Roll back
            $this->db->transRollback();

        } else {

            // Commit changes
            $this->db->transCommit();

        }

        // Prepare the object array to hold the return data
        $resultObjectArray = array();

        // Get the transaction result
        $resultObjectArray["resultObject"] = $result;

        // Get the last sql query
        /**
         * @depricated string $lastQuery Consider removing the line below
         */
        $resultObjectArray["lastQuery"] = $this->db->showLastQuery();

        // Get the field names
        $resultObjectArray["fieldNames"] = $query->getFieldNames();

        // Return the result object array
        return $resultObjectArray;

    }

    // -- END READ


    //--------------------------------------------------------------------
    //       Function :: UPDATE :: 
    //--------------------------------------------------------------------

    /**
     * Get the data from the database by passing an object with parameters
     *
     * @param array $databaseTable
     * @param array $id
     * @param array $dataObject
     * @return array
     */
    protected function updateData($databaseTable, $id, $dataObject)
    {

        // Begin a transaction
        $this->db->transStart();

        // Initialise the builder object
        $builder = $this->db->table($databaseTable);

        // Check if the user specified an sql query
        if(isset($dataObject["sql"])) {

            $sql = $dataObject["sql"];

        }else {

            // Get the database primary key
            $primaryColumn = $this->getPrimaryKeyColumn($databaseTable);

            // Define
            $builder->where($primaryColumn, $id);

            // Get the SQL query string
            $sql = $builder->set($dataObject["data"])->getCompiledUpdate();

        }

        // Execute the query
        $result = $this->db->query($sql);

        // If the commit was unsuccessful, handle the error and log it
        if ($this->db->transStatus() === FALSE) {

            $this->databaseLogError();


            // Roll back
            $this->db->transRollback();

        } else {

            // Commit changes
            $this->db->transCommit();

        }

        // Prepare the object array to hold the return data
        $resultObjectArray = array();

        // Get the transaction result
        $resultObjectArray["resultObject"] = $result;

        // Get the last sql query
        $resultObjectArray["lastQuery"] = $sql;

        // Get the last sql query
        $resultObjectArray["affectedRows"] = $this->db->affectedRows();

        // Get the last inserted id
        $resultObjectArray["updatedRowID"] = $id;

        // Return the result object array
        return $resultObjectArray;

    }

    // -- END UPDATE


    //--------------------------------------------------------------------
    //       Function :: DELETE :: 
    //--------------------------------------------------------------------

    /**
     * Get the data from the database by passing an object with parameters
     *
     * @param array $databaseTable
     * @param array $id
     * @return array
     */
    protected function deleteData($databaseTable, $id)
    {

        // Begin a transaction
        $this->db->transBegin();

        // Initialise the builder object
        $builder = $this->db->table($databaseTable);

        // Check if the user specified an sql query
        if(isset($dataObject["sql"])) {

            $sql = $dataObject["sql"];

        }else {

            // Get the database primary key
            $primaryColumn = $this->getPrimaryKeyColumn($databaseTable);

            // Define
            $builder->where($primaryColumn, $id);

            // Get the SQL query string
            $sql = $builder->getCompiledDelete();

        }
  
        // Execute the query
        $result = $this->db->query($sql);

        // If the commit was unsuccessful, handle the error and log it
        if ($this->db->transStatus() === FALSE) {

            $this->databaseLogError();


            // Roll back
            $this->db->transRollback();

        } else {

            // Commit changes
            $this->db->transCommit();

        }

        // Prepare the object array to hold the return data
        $resultObjectArray = array();

        // Get the transaction result
        $resultObjectArray["resultObject"] = $result;

        // Get the last sql query
        $resultObjectArray["lastQuery"] = $sql;

        // Get the last sql query
        $resultObjectArray["affectedRows"] = $this->db->affectedRows();

        // Get the last inserted id
        $resultObjectArray["updatedRowID"] = $id;

        // Return the result object array
        return $resultObjectArray;

    }

    // -- END DELETE


    /**
     * @param $params
     * @return mixed
     */
    public function legacyGetData($params)
    {

        if (array_key_exists("sql", $params)) {

            $query = $this->db->query($params["sql"]);

        } else {

            if (!array_key_exists("table", $params)) {

                // Return an error
                return array(
                    "status" => ERROR,
                    "message" => "Invalid table name"
                );

            }

            $builder = $this->db->table($params["table"]);

            if (array_key_exists("cols", $params)) {

                if (is_array($params["cols"])) {

                    implode(COMMA_SPACE, $params["cols"]);

                } else {

                    $cols = $params["cols"];

                }

                $builder->select($cols);

            } else {

                $builder->select("*");

            }

            if (array_key_exists("where", $params)) {

                $builder->where($params["where"]);

            }

            if (array_key_exists("whereIn", $params)) {

                foreach ($params["whereIn"] as $where_object) {

                    foreach ($where_object as $where_column => $where_data) {

                        $builder->whereIn($where_column, $where_data);

                    }

                }

            }

            if (array_key_exists("join", $params)) {


                $builder->join($params["join"]["join_table"], $params["join"]["join_table"] . "." . $params["join"]["left_column"] . " = " . $params["table"] . "." . $params["join"]["right_column"]);

            }

            if (array_key_exists("limit", $params)) {

                $builder->limit($params["limit"]["offset"], $params["limit"]["number"]);

            }

            if (array_key_exists("like", $params)) {

                $builder->like($params["like"]["col"], $params["like"]["value"]);

            }

            if (array_key_exists("order_by", $params)) {

                foreach ($params["order_by"] as $orderByObject) {

                    foreach ($orderByObject as $orderByKey => $orderByValue) {

                        $builder->orderBy($orderByKey, $orderByValue);

                    }

                }

            }

            $query = $builder->get();

        }

        if (array_key_exists("returnType", $params)) {

            if ($params["returnType"] == QUERY_RETURN_SINGLE) {

                $result = $query->getRowArray();

            } else {

                $result = $query->getResultArray();

            }

        } else {

            $result = $query->getResultArray();

        }

        return $result;

    }

    /**
     * Insert a new data
     *
     * @param $params
     * @return array
     */
    public function legacyInsertData($params)
    {

        if (array_key_exists("data", $params)) {

            $data = $params["data"];

        } else {

            // Return an error
            return array(
                "status" => ERROR,
                "message" => "Invalid data format"
            );

        }

        if (!array_key_exists("table", $params)) {

            return array(
                "status" => ERROR,
                "message" => "Invalid table name"
            );

        }

        $builder = $this->db->table($params["table"]);

        if (isset($data[0])) {
            $result = $builder->insertBatch($data);
        } else {
            $result = $builder->insert($data);
        }

        $returnData = array(
            "id" => $result->connID->insert_id,
            "affectedRows" => $result->connID->affected_rows,
            "result" => $result
        );

        return $returnData;

    }

    /**
     * @param $params
     * @return array
     */
    public function legacyUpdateData($params)
    {

        if (!array_key_exists("table", $params)) {

            // Return an error
            return array(
                "status" => ERROR,
                "message" => "Invalid table name"
            );

        }

        $builder = $this->db->table($params["table"]);

        if (array_key_exists("where", $params)) {

            $builder->where($params["where"]);

        } else {

            // Return an error
            return array(
                "status" => ERROR,
                "message" => "Invalid where clause"
            );

        }

        if (array_key_exists("data", $params)) {

            $data = $params["data"];

        } else {

            // Return an error
            return array(
                "status" => ERROR,
                "message" => "Invalid data"
            );

        }

        if (isset($data[0])) {
            $result = $builder->updateBatch($data);
        } else {
            $result = $builder->update($data);
        }
        
        $returnData = array(
            "affectedRows" => is_object($result) ? $result->connID->affected_rows : 0,
            "result" => $result
        );

        return $returnData;

    }

    /**
     * Delete a data record
     *
     * @param $params
     * @param int $id
     * @return array
     */
    public function legacyDeleteData($params, $id = 0)
    {

        if (!array_key_exists("table", $params)) {

            // Return an error
            return array(
                "status" => ERROR,
                "message" => "Invalid table name"
            );

        }

        $builder = $this->db->table($params["table"]);

        if (array_key_exists("where", $params)) {

            $builder->where($params["where"]);

        }

        if (array_key_exists("whereIn", $params)) {

            foreach ($params["whereIn"] as $where_object) {

                foreach ($where_object as $where_column => $where_data) {

                    $builder->whereIn($where_column, $where_data);

                }

            }

        }

        $result = $builder->delete();

        $returnData = array(
            "affectedRows" => $result->connID->affected_rows,
            "result" => $result
        );

        return $returnData;

    }


}
