<?php

/**
 * Model Class RolesModel
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
if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class RolesModel extends \Acme\Core\System\Modules\Auth\Users\Models\RolesBaseModel
{
    
    public $acmeRequest;
    
    /**
     * UsersModel constructor.
     */
    public function __construct( $request )
    {

        /**
         *  Call the parent Contructor
         */
        parent::__construct();
        
        $this->setUpConstants();

        $this->acmeRequest = $request;
        

    }

    /**
     * UserRoles_model constructor.
     */
    public function setUpConstants()
    {

    }

    /**
     * Create a new role
     *
     * @param array $data
     * @return array|NULL
     */
    public function roleCreateNew($data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_ROLES;

        if (empty($data)) {
            // Compose the data array from request input vars
            $data = array(

                "role_name" => $this->acmeRequest->getPost("name"),

                "role_slug" => acme_to_slug(
                    $this->acmeRequest->getPost("name")
                )

            );
        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new role
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Update the specified role
     *
     * @param $role
     * @param array $data
     * @return array
     */
    public function roleUpdate($role, $data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_ROLES;

        // Get the where clause
        $whereClause = $this->roleWhereClause($role);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            // Check if there is a where
            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            // Check if we are going to perform using Where
            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Only execute if we have the data
        if (!empty($data)) {

            // Get post vars
            $postVars = $_POST;

            // Check if the POST array has data
            if ($postVars != NULL) {

                $data = array();

                // Iterate all POST keys and create a data array object
                foreach ($postVars as $key => $value) {

                    $data[$key] = urldecode($value);

                }

            }

            // Add that data to the array
            $params["data"] = $data;

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The data to update is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyUpdateData($params);

        return $result;

    }

    /**
     * Compose a roles where clause
     *
     * @param $role
     * @return mixed
     */
    public function roleWhereClause($role)
    {

        // Array to hold the where clause data
        $where_clause_array = array();

        // Array to hold the where in data
        $whereIn_clause_array = array();

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($role)) {

            // See if we can query using the slug
            if (array_key_exists("role_id", $role)) {

                // Get the role_id
                $role_id = $role["role_id"];

                // If the role_id is an array, its a where-in clause
                if (is_array($role_id)) {

                    $whereIn_clause_array["role_id"] = $role_id;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = " role_id = '$role_id' ";

                }

            }

            // See if we can query using the slug
            if (array_key_exists("role_slug", $role)) {

                // Get the role_slug
                $role_slug = $role["role_slug"];

                // If the role_slug is an array, its a where-in clause
                if (is_array($role_slug)) {

                    $whereIn_clause_array["role_slug"] = $role_slug;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "role_slug = '$role_slug'";

                }

            }

            // See if we can query using the name
            if (array_key_exists("role_name", $role)) {

                // Get the role_name
                $role_name = $role["role_name"];

                // If the role_name is an array, its a where-in clause
                if (is_array($role_name)) {

                    $whereIn_clause_array["role_name"] = $role_name;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "role_name = '$role_name'";

                }

            }

            // Compose the where clause string
            $where_clause_string = implode(" OR ", $where_clause_array);

            // Check to see if a parameter other than id is passed
            if ($where_clause_string != "") {

                // Create the required params array
                $whereClause = $where_clause_string;

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        } else {

            // Assume its a numerical id that have been passed
            if (filter_var($role, FILTER_VALIDATE_INT)) {

                // Built a where clause based on the default primary key
                $whereClause = array(

                    "role_id" => $role

                );

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        }

        // Return the where clause array
        $whereClauseArray = array(

            "where" => $whereClause,

            "whereIn" => $whereIn_clause_array

        );

        return $whereClauseArray;

    }

    /**
     * Get all roles
     *
     * @return mixed
     */
    public function rolesGetAll()
    {
        // Get all data
        return $this->roleGet(0);

    }

    /**
     * Get the ids from the roles array
     *
     * @param $roles
     * @return mixed
     */
    public function roleGetIDs($roles)
    {
        // Initialise the ids array
        $ids = array();

        if (is_array($roles)) {

            // Get the id from the array
            foreach ($roles as $role) {

                $ids[] = $role["role_id"];

            }

        } else {

            //Otherwise just return the passed data
            $ids = $roles;

        }

        // Return the result
        return $ids;

    }

    /**
     * Get a specified role
     *
     * @param $role
     * @param string $returnType
     * @return mixed
     */
    public function roleGet($role, $returnType = QUERY_RETURN_MULTIPLE)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_ROLES;

        // Set the data type to return
        $params["returnType"] = $returnType;

        if ($role) {

            // Extract the query criteria so that we streamline it
            foreach ($role as $k => $v) {

                // Check if the value is an array
                if (is_array($v)) {

                    // Remove duplicates
                    $v = array_unique($v, SORT_REGULAR);

                    // Sort the array
                    sort($v, SORT_REGULAR);

                    // Re assign the key with the new value
                    $role[$k] = $v;

                }

            }

            // Get the where clause
            $whereClause = $this->roleWhereClause($role);

            // Create the where clause
            if (is_array($whereClause)) {

                // If its a literal where clause
                if (!empty($whereClause["where"])) {

                    $params["where"] = $whereClause["where"];

                }

                // Check if we have multiple where-in
                if (!empty($whereClause["whereIn"])) {

                    foreach ($whereClause["whereIn"] as $k => $v) {

                        $params["whereIn"][] = array($k => $v);

                    }

                }

            }

        }

        // Return the result an an array
        $result = $this->legacyGetData($params);

        return $result;

    }

    /**
     * Delete a role
     *
     * @param $role
     * @return array
     */
    public function roleDelete($role)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_ROLES;

        // Get the where clause
        $whereClause = $this->roleWhereClause($role);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyDeleteData($params);

        return $result;

    }

    /**
     * Get Work Group roles
     *
     * @param $work_group
     * @return array
     */

    public function workGroupGetRoles($work_group)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_WORK_GROUP_ROLES;

        //Check tp see if the argument is an array
        if (is_array($work_group)) {

            // Check if the argument is already a workgroup
            if (array_key_exists("work_group_id", $work_group)) {

                // Get the Work Group data from user criterion so that we have the actual work_group_id
                $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            } else {

                // Then lets assume that the data passed is actually the work group dataa
                $work_groupData = $work_group;

            }

        } else {

            // Then lets assume the value passed is a primary columns ID
            $work_groupData = array("work_group_id" => $work_group);

        }

        // Check to see if we have an id passed, otherwise get all
        if (isset($work_groupData["work_group_id"])) {

            // Prepare a where clause
            $params["where"] = array(

                "work_group_id" => $work_groupData["work_group_id"]

            );

            // Prepare a where clause
            $params["join"] = array(

                "join_table" => "auth_roles",

                "left_column" => "role_id",

                "right_column" => "role_id"

            );

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyGetData($params);


        // Return the result
        return $result;

    }

    /**
     * Get Work Groups that use the specified roles
     *
     * @param $role array
     * @param $includeUsers bool
     * @return array
     */
    public function workGroupGetByRoles($role, $includeUsers = FALSE)
    {

        // Array to hold the role data row
        $roleData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($role)) {

            $roleData = $this->roleGet($role);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "The argument 'role' must be an array, instead we have a string"

            );

        }

        if ($roleData != NULL) {

            // Prepare the role ids array
            $role_ids = array();

            // Iterate the role data array to get the exact role ids
            foreach ($roleData as $role) {

                // Extract thr role id and push it into the role ids array
                $role_ids[] = $role["role_id"];

            }

            // Prepare an array to hold the data to be used for the query
            $params = array();

            // Set the table name to retrieve the work group roles
            $params["table"] = TABLE_WORK_GROUP_ROLES;

            // Build the where clause using multiple role ids
            $params["whereIn"][] = array(

                // Since we are expecting the role ids to be an array
                // We are going to use where-in in the clause
                // Assign the role ids now
                "role_id" => $role_ids

            );

            // Get the work group roles from the database using the query parameters
            $result_work_group_roles = $this->legacyGetData($params);

            if (!empty($result_work_group_roles)) {

                // Prepare an array to hold the work group ids
                $work_group_role_ids = array();

                // For each result of the work group roles, parse the work group
                foreach ($result_work_group_roles as $work_group) {

                    // Extract the work group id and push it into the array previously defined
                    $work_group_role_ids[] = $work_group["work_group_id"];

                }

                //Check if the view is allowed for the work_group
                $params = array();

                // set the table name to get the work groups
                $params["table"] = TABLE_WORK_GROUPS;

                // Since we now have the work group ids
                $params["whereIn"][] = array(

                    // Assign work group ids for use using the where-in operator
                    "work_group_id" => $work_group_role_ids

                );

                // Get the work groups from the database
                $result_work_groups = $this->legacyGetData($params);

                // Prepare an array to hold the work group ids
                $work_group_ids = array();

                foreach ($result_work_groups as $work_group) {

                    // Push the work group id into the array
                    $work_group_ids[] = $work_group["work_group_id"];

                    // If we are to include the users
                    if ($includeUsers) {

                        //Check if the view is allowed for the work_group
                        $params = array();

                        // Set the table name to retrieve the work group users
                        $params["table"] = TABLE_WORK_GROUP_USERS;

                        // Build the where clause so that we get users for the correct work group
                        $params["where"] = array(

                            // Assign the correct work group id to associate with the correct users
                            "work_group_id" => $work_group["work_group_id"]

                        );

                        // Retrieve the work Goup users
                        $result_work_group_users = $this->legacyGetData($params);

                        //Check if the view is allowed for the work_group
                        $params = array();

                        // Set the table name to get the users from
                        $params["table"] = TABLE_USERS;

                        // Prepare an array to hold the user ids
                        $user_ids = array();

                        // If we manage to secure the user ids
                        // then extract the ids
                        if (!empty($result_work_group_users)) {

                            foreach ($result_work_group_users as $work_group_user) {

                                // Push the user id into the array
                                $user_ids[] = $work_group_user["user_id"];

                            }

                            // Get the user details using the given parameters
                            $result_user_details = $this->getUserData($user_ids);

                            // Create the user ids key and assign a list of user ids
                            $work_group["user_ids"] = $user_ids;

                            // Create a users key and assign the user result array
                            $work_group["users"] = $result_user_details;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["users"] = array();

                        }

                    }

                    // Streamline the  work group array by assigning the work group slug as
                    // the array key for use later on
                    $work_group_array[$work_group["work_group_slug"]] = $work_group;

                }

            } else {

                // Return error
                return array(

                    "status" => ERROR,

                    "message" => "Could not find work groups for the role specified"

                );

            }

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the role data using the provided parameters"

            );

        }

        // Return the result array
        return $work_group_array;

    }

    /**
     * Add a new role to a Work Group
     *
     * @param $role
     * @param int $work_group_id
     * @return array|NULL
     */

    public function workGroupAddRole($role, $work_group_id = 0)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_WORK_GROUP_ROLES;

        if (empty($role)) {
            // Compose the data array from the args
            $data = array(

                "work_group_id" => $this->acmeRequest->getPost("work_group_id"),

                "role_id" => $this->acmeRequest->getPost("role_id")

            );
        } else {
            // Compose the data array from request input vars
            $data = array(

                "work_group_id" => $work_group_id,

                "role_id" => $role["role_id"]

            );
        }

        // Check if we have valid ids for both 
        if (!$data["work_group_id"] || $data["role_id"]) {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID / Role ID"

            );

        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new role
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Create a new role for a specific Work Group
     *
     * @param $work_group
     * @param $role
     * @param $override
     * @return array
     */

    public function workGroupSetRoles($work_group, $role, $override = TRUE)
    {

        // Get Work Group data so that we can get the correct work_group_id
        $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        // Get the role data so that we can get the correct role_id
        $roleData = $this->roleGet($role);

        // get Work Group roles
        $work_groupRolesBefore = $this->workGroupGetRoles($work_groupData["work_group_id"]);

        // Will have to compose the data array for the insert operation
        $dataWorkGroupRoles = array();

        // The parameter to determine if the operation was successful
        $insertResult = false;

        // The parameter to identify the row affected
        $insertID = 0;

        // The parameter to identify the number row affected
        $affectedRows = 0;

        // Iterate the required role so that we get correct values for the data array
        foreach ($roleData as $_role) {

            $work_groupRoleObject = array();

            $work_groupRoleObject["role_id"] = $_role["role_id"];

            $work_groupRoleObject["work_group_id"] = $work_groupData["work_group_id"];

            $work_groupRoleObject["is_active"] = 1;

            $dataWorkGroupRoles[] = $work_groupRoleObject;

        }

        //Variable to hold the delete permission result
        $deleteWorkGroupRolesResult = NULL;

        //If we are overriding, it means we are deleting whats already exists
        if ($override) {

            $deleteWorkGroupRolesResult = $this->workGroupDeleteRole($work_groupData, $work_groupRolesBefore);

        }

        // Now update the data with the new values or rather insert data
        if (!empty($dataWorkGroupRoles)) {

            // Initialise the params array
            $params = array();

            // Set the table name to insert data
            $params["table"] = TABLE_WORK_GROUP_ROLES;

            //Set the data
            $params["data"] = $dataWorkGroupRoles;

            // Insert new Roles
            $insertResult = $this->legacyInsertData($params);

            //Get the number of affected rows
            $affectedRows = $this->db->affected_rows();

            // If the insert data was successful, then get the insert id
            if ($insertResult) {

                $insertID = $this->db->insert_id();

            }

        }

        // get Work Group roles
        $work_groupRolesAfter = $this->workGroupGetRoles($work_groupData["work_group_id"]);

        // Compose the return array
        $resultArray = array(
            "status" => $insertResult ? SUCCESS : ERROR,
            "last_insert_id" => $insertID,
            "affected_rows" => $affectedRows,
            "dataWorkGroupRoles" => $dataWorkGroupRoles,
            "work_group_roles_before" => $work_groupRolesBefore,
            "work_group_roles_after" => $work_groupRolesAfter,
            "deleteWorkGroupRolesResult" => $deleteWorkGroupRolesResult
        );

        // Return the final result
        return $resultArray;

    }

    /**
     * Update Work Group Role
     *
     * @param $work_group_role_id
     * @param $role_id
     * @param $work_group_id
     * @param $new_role_id
     * @param $new_work_group_id
     * @return array
     */
    public function workGroupUpdateRole($work_group_role_id, $role_id, $work_group_id, $new_role_id, $new_work_group_id)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_WORK_GROUP_ROLES;

        $params["where"] = array();

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_role_id) {

            // Also make sure we have the role id passed
            if (filter_var($work_group_role_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_role_id"] = (int)$work_group_role_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied User Role ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid User Role ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($role_id) {

            // Also make sure we have the role id passed
            if (filter_var($role_id, FILTER_VALIDATE_INT)) {

                $params["where"]["role_id"] = (int)$role_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Role ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Role ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the role id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // Compose the data array from request input vars
            $params["data"] = array(

                "role_id" => $new_role_id,

                "work_group_id" => $new_work_group_id

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * @param array $work_group
     * @param array $role
     * @return array
     */

    public function workGroupDeleteRole($work_group = array(), $role = array())
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Extract the work group ID
            $work_group_id = $work_group_data["work_group_id"];

        }

        $role_id = NULL;

        if (is_array($role)) {

            // Get the role so that we have the correct id
            $role_data = $this->roleGet($role);

        } else {

            $role_data = NULL;

        }

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_WORK_GROUP_ROLES;

        // Check to see if we have an id passed
        if ($work_group_id) {

            $params["where"] = array(

                "work_group_id" => $work_group_id

            );

            if (is_array($role_data)) {

                if (isset($role_data[0])) {

                    if (array_key_exists("role_id", $role_data[0])) {

                        $role_ids = array();

                        foreach ($role_data as $role_item) {

                            $role_ids[] = $role_item["role_id"];

                        }

                        $params["whereIn"][] = array(

                            "role_id" => $role_ids

                        );

                    }

                } else {

                    $role_ids = $this->roleGetIDs($role_data);

                    if (!empty($role_ids)) {

                        $params["whereIn"][] = array(

                            "role_id" => $role_ids

                        );

                    } else {

                        // Return an error
                        return array(

                            "status" => ERROR,

                            "message" => "Invalid Roles"

                        );

                    }

                }

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Invalid roles"

                );

            }

            // If not, then look for it in the array
        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyDeleteData($params);

        // Return the result
        return $result;

    }

    /**
     * @param array $work_group
     * @param array $role
     * @param null $status
     * @return array
     */
    public function workGroupRoleToggleStatus($work_group = array(), $role = array(), $status = NULL)
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Depending on the result returned it can be a row array or a result array
            // If it's a result array
            if (isset($work_group_data[0])) {

                $work_group_id = $work_group_data[0]["work_group_id"];

            } else {

                // Otherwise it's a row array
                $work_group_id = $work_group_data["work_group_id"];

            }

        }

        $role_id = NULL;

        if (is_array($role)) {

            // Get the role so that we have the correct id
            $role_data = $this->roleGet($role, QUERY_RETURN_SINGLE);

            // Depending on the result returned, it can be a result array or a row array
            // if it's a result array
            if (isset($role_data[0])) {

                $role_id = $role_data[0]["role_id"];

            } else {

                // Otherwise its a row array
                $role_id = $role_data["role_id"];

            }

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($role_id) {

            // Also make sure we have the role id passed
            if (filter_var($role_id, FILTER_VALIDATE_INT)) {

                $params["where"]["role_id"] = (int)$role_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Role ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Role ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the role id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // If the status is NULL,
            // Get the currrent status and do the opposite
            if ($status === NULL) {

                $params["table"] = TABLE_WORK_GROUP_ROLES;

                $params["returnType"] = QUERY_RETURN_SINGLE;

                $currentSettings = $this->legacyGetData($params);

                // Get the vaue of the current status
                if (is_array($currentSettings)) {

                    $currentStatus = (int)$currentSettings["is_active"];

                    if ($currentStatus) {

                        $currentStatus = 0;

                    } else {

                        $currentStatus = 1;

                    }

                } else {

                    // Return an error
                    return array(

                        "status" => ERROR,

                        "message" => "Could not determine the Work Group Role"

                    );

                }

            } else {

                $params["table"] = TABLE_WORK_GROUP_ROLES;

                $currentStatus = (int)$status;

            }

            // Compose the data array from request input vars
            $params["data"] = array(

                "is_active" => $currentStatus

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * Check to see if the Work Group has a specified role
     *
     * @param $work_group
     * @param $role
     * @return array
     */
    public function workGroupHasRole($work_group, $role)
    {

        // Array to hold the role data row
        $roleData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($role)) {

            $roleData = $this->roleGet($role, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the role data using the provided parameters"

            );

        }

        // Array to hold th work group data row
        $work_groupData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($work_group)) {

            $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find work group data using the provided parameters"

            );

        }

        // If we have all the data, assess if we can get the role from the work group
        if ($roleData != NULL && $work_groupData != NULL) {

            // make sure all the ids are present
            if (array_key_exists("role_id", $roleData) && array_key_exists("work_group_id", $work_groupData)) {

                // Initialise the params array
                $params = array();

                // Set the table name to insert data
                $params["table"] = TABLE_WORK_GROUP_ROLES;

                // Return a single row with the Work Group role data
                $params["returnType"] = QUERY_RETURN_SINGLE;

                // Create the where clause by inserting the id
                $params["where"] = array(

                    "role_id" => $roleData["role_id"],

                    'work_group_id' => $work_groupData["work_group_id"]

                );

                // No check if a user type record id exist that matches the role id
                $result = $this->legacyGetData($params);

                // If the result is defined, then the role exists
                // Hence the work group has that role
                if ($result != NULL && is_array($result)) {

                    // Return data
                    return array(
                        "status" => SUCCESS,
                        "data" => $result
                    );

                } else {

                    // Return error
                    return array(
                        "status" => ERROR,
                        "message" => "The Role / Work Group combination does not exist"
                    );

                }

            } else {

                // Return error
                return array(
                    "status" => ERROR,
                    "message" => "Missing Role ID / Work Group ID in the result set"
                );

            }

        } else {

            // Return error
            return array(
                "status" => ERROR,
                "message" => "Could not find data with the specified input parameters"
            );

        }

    }

    //***********************
    // PERMISSIONS
    //***********************

    /**
     * Create a new permission
     *
     * @param array $data
     * @return array|NULL
     */
    public function permissionCreateNew($data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_PERMISSIONS;

        if (empty($data)) {
            // Compose the data array from request input vars
            $data = array(

                "permission_name" => $this->acmeRequest->getPost("name"),

                "permission_slug" => acme_to_slug(
                    $this->acmeRequest->getPost("name")
                )

            );
        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new permission
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Update the specified permission
     *
     * @param $permission
     * @param array $data
     * @return array
     */
    public function permissionUpdate($permission, $data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_PERMISSIONS;

        // Get the where clause
        $whereClause = $this->permissionWhereClause($permission);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            // Check if there is a where
            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            // Check if we are going to perform using Where
            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Only execute if we have the data
        if (!empty($data)) {

            // Get post vars
            $postVars = $_POST;

            // Check if the POST array has data
            if ($postVars != NULL) {

                $data = array();

                // Iterate all POST keys and create a data array object
                foreach ($postVars as $key => $value) {

                    $data[$key] = urldecode($value);

                }

            }

            // Add that data to the array
            $params["data"] = $data;

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The data to update is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyUpdateData($params);

        return $result;

    }

    /**
     * Compose a permissions where clause
     *
     * @param $permission
     * @return mixed
     */
    public function permissionWhereClause($permission)
    {

        // Array to hold the where clause data
        $where_clause_array = array();

        // Array to hold the where in data
        $whereIn_clause_array = array();

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($permission)) {

            // See if we can query using the slug
            if (array_key_exists("permission_id", $permission)) {

                // Get the permission_id
                $permission_id = $permission["permission_id"];

                // If the permission_id is an array, its a where-in clause
                if (is_array($permission_id)) {

                    $whereIn_clause_array["permission_id"] = $permission_id;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = " permission_id = '$permission_id' ";

                }

            }

            // See if we can query using the slug
            if (array_key_exists("permission_slug", $permission)) {

                // Get the permission_slug
                $permission_slug = $permission["permission_slug"];

                // If the permission_slug is an array, its a where-in clause
                if (is_array($permission_slug)) {

                    $whereIn_clause_array["permission_slug"] = $permission_slug;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "permission_slug = '$permission_slug'";

                }

            }

            // See if we can query using the name
            if (array_key_exists("permission_name", $permission)) {

                // Get the permission_name
                $permission_name = $permission["permission_name"];

                // If the permission_name is an array, its a where-in clause
                if (is_array($permission_name)) {

                    $whereIn_clause_array["permission_name"] = $permission_name;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "permission_name = '$permission_name'";

                }

            }

            // Compose the where clause string
            $where_clause_string = implode(" OR ", $where_clause_array);

            // Check to see if a parameter other than id is passed
            if ($where_clause_string != "") {

                // Create the required params array
                $whereClause = $where_clause_string;

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        } else {

            // Assume its a numerical id that have been passed
            if (filter_var($permission, FILTER_VALIDATE_INT)) {

                // Built a where clause based on the default primary key
                $whereClause = array(

                    "permission_id" => $permission

                );

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        }

        // Return the where clause array
        $whereClauseArray = array(

            "where" => $whereClause,

            "whereIn" => $whereIn_clause_array

        );

        return $whereClauseArray;

    }

    /**
     * Get all permissions
     *
     * @return mixed
     */
    public function permissionsGetAll()
    {
        // Get all data
        return $this->permissionGet(0);

    }

    /**
     * Get the ids from the permissions array
     *
     * @param $permissions
     * @return mixed
     */
    public function permissionGetIDs($permissions)
    {
        // Initialise the ids array
        $ids = array();

        if (is_array($permissions)) {

            // Get the id from the array
            foreach ($permissions as $permission) {

                $ids[] = $permission["permission_id"];

            }

        } else {

            //Otherwise just return the passed data
            $ids = $permissions;

        }

        // Return the result
        return $ids;

    }

    /**
     * Get a specified permission
     *
     * @param $permission
     * @param string $returnType
     * @return mixed
     */
    public function permissionGet($permission, $returnType = QUERY_RETURN_MULTIPLE)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_PERMISSIONS;

        // Set the data type to return
        $params["returnType"] = $returnType;

        if ($permission) {

            // Extract the query criteria so that we streamline it
            foreach ($permission as $k => $v) {

                // Check if the value is an array
                if (is_array($v)) {

                    // Remove duplicates
                    $v = array_unique($v, SORT_REGULAR);

                    // Sort the array
                    sort($v, SORT_REGULAR);

                    // Re assign the key with the new value
                    $permission[$k] = $v;

                }

            }

            // Get the where clause
            $whereClause = $this->permissionWhereClause($permission);

            // Create the where clause
            if (is_array($whereClause)) {

                // If its a literal where clause
                if (!empty($whereClause["where"])) {

                    $params["where"] = $whereClause["where"];

                }

                // Check if we have multiple where-in
                if (!empty($whereClause["whereIn"])) {

                    foreach ($whereClause["whereIn"] as $k => $v) {

                        $params["whereIn"][] = array($k => $v);

                    }

                }

            }

        }

        // Return the result an an array
        $result = $this->legacyGetData($params);

        return $result;

    }

    /**
     * Delete a permission
     *
     * @param $permission
     * @return array
     */
    public function permissionDelete($permission)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_PERMISSIONS;

        // Get the where clause
        $whereClause = $this->permissionWhereClause($permission);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyDeleteData($params);

        return $result;

    }

    /**
     * Get Work Group permissions
     *
     * @param $work_group
     * @return array
     */

    public function workGroupGetPermissions($work_group)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

        //Check tp see if the argument is an array
        if (is_array($work_group)) {

            // Check if the argument is already a workgroup
            if (array_key_exists("work_group_id", $work_group)) {

                // Get the Work Group data from user criterion so that we have the actual work_group_id
                $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            } else {

                // Then lets assume that the data passed is actually the work group dataa
                $work_groupData = $work_group;

            }

        } else {

            // Then lets assume the value passed is a primary columns ID
            $work_groupData = array("work_group_id" => $work_group);

        }

        // Check to see if we have an id passed, otherwise get all
        if (isset($work_groupData["work_group_id"])) {

            // Prepare a where clause
            $params["where"] = array(

                "work_group_id" => $work_groupData["work_group_id"]

            );

            // Prepare a where clause
            $params["join"] = array(

                "join_table" => "auth_permissions",

                "left_column" => "permission_id",

                "right_column" => "permission_id"

            );

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyGetData($params);


        // Return the result
        return $result;

    }

    /**
     * Get Work Groups that use the specified permissions
     *
     * @param $permission array
     * @param $includeUsers bool
     * @return array
     */
    public function workGroupGetByPermissions($permission, $includeUsers = FALSE)
    {

        // Array to hold the permission data row
        $permissionData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($permission)) {

            $permissionData = $this->permissionGet($permission);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "The argument 'permission' must be an array, instead we have a string"

            );

        }

        if ($permissionData != NULL) {

            // Prepare the permission ids array
            $permission_ids = array();

            // Iterate the permission data array to get the exact permission ids
            foreach ($permissionData as $permission) {

                // Extract thr permission id and push it into the permission ids array
                $permission_ids[] = $permission["permission_id"];

            }

            // Prepare an array to hold the data to be used for the query
            $params = array();

            // Set the table name to retrieve the work group permissions
            $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

            // Build the where clause using multiple permission ids
            $params["whereIn"][] = array(

                // Since we are expecting the permission ids to be an array
                // We are going to use where-in in the clause
                // Assign the permission ids now
                "permission_id" => $permission_ids

            );

            // Get the work group permissions from the database using the query parameters
            $result_work_group_permissions = $this->legacyGetData($params);

            if (!empty($result_work_group_permissions)) {

                // Prepare an array to hold the work group ids
                $work_group_permission_ids = array();

                // For each result of the work group permissions, parse the work group
                foreach ($result_work_group_permissions as $work_group) {

                    // Extract the work group id and push it into the array previously defined
                    $work_group_permission_ids[] = $work_group["work_group_id"];

                }

                //Check if the view is allowed for the work_group
                $params = array();

                // set the table name to get the work groups
                $params["table"] = TABLE_WORK_GROUPS;

                // Since we now have the work group ids
                $params["whereIn"][] = array(

                    // Assign work group ids for use using the where-in operator
                    "work_group_id" => $work_group_permission_ids

                );

                // Get the work groups from the database
                $result_work_groups = $this->legacyGetData($params);

                // Prepare an array to hold the work group ids
                $work_group_ids = array();

                foreach ($result_work_groups as $work_group) {

                    // Push the work group id into the array
                    $work_group_ids[] = $work_group["work_group_id"];

                    // If we are to include the users
                    if ($includeUsers) {

                        //Check if the view is allowed for the work_group
                        $params = array();

                        // Set the table name to retrieve the work group users
                        $params["table"] = TABLE_WORK_GROUP_USERS;

                        // Build the where clause so that we get users for the correct work group
                        $params["where"] = array(

                            // Assign the correct work group id to associate with the correct users
                            "work_group_id" => $work_group["work_group_id"]

                        );

                        // Retrieve the work Goup users
                        $result_work_group_users = $this->legacyGetData($params);

                        //Check if the view is allowed for the work_group
                        $params = array();

                        // Set the table name to get the users from
                        $params["table"] = TABLE_USERS;

                        // Prepare an array to hold the user ids
                        $user_ids = array();

                        // If we manage to secure the user ids
                        // then extract the ids
                        if (!empty($result_work_group_users)) {

                            foreach ($result_work_group_users as $work_group_user) {

                                // Push the user id into the array
                                $user_ids[] = $work_group_user["user_id"];

                            }

                            // Get the user details using the given parameters
                            $result_user_details = $this->getUserData($user_ids);

                            // Create the user ids key and assign a list of user ids
                            $work_group["user_ids"] = $user_ids;

                            // Create a users key and assign the user result array
                            $work_group["users"] = $result_user_details;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["users"] = array();

                        }

                    }

                    // Streamline the  work group array by assigning the work group slug as
                    // the array key for use later on
                    $work_group_array[$work_group["work_group_slug"]] = $work_group;

                }

            } else {

                // Return error
                return array(

                    "status" => ERROR,

                    "message" => "Could not find work groups for the permission specified"

                );

            }

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the permission data using the provided parameters"

            );

        }

        // Return the result array
        return $work_group_array;

    }

    /**
     * Add a new permission to a Work Group
     *
     * @param $permission
     * @param int $work_group_id
     * @return array|NULL
     */

    public function workGroupAddPermission($permission, $work_group_id = 0)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

        if (empty($permission)) {
            // Compose the data array from the args
            $data = array(

                "work_group_id" => $this->acmeRequest->getPost("work_group_id"),

                "permission_id" => $this->acmeRequest->getPost("permission_id")

            );
        } else {
            // Compose the data array from request input vars
            $data = array(

                "work_group_id" => $work_group_id,

                "permission_id" => $permission["permission_id"]

            );
        }

        // Check if we have valid ids for both
        if (!$data["work_group_id"] || $data["permission_id"]) {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID / Permission ID"

            );

        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new permission
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Create a new permission for a specific Work Group
     *
     * @param $work_group
     * @param $permission
     * @param $override
     * @return array
     */

    public function workGroupSetPermissions($work_group, $permission, $override = TRUE)
    {

        // Get Work Group data so that we can get the correct work_group_id
        $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        // Get the permission data so that we can get the correct permission_id
        $permissionData = $this->permissionGet($permission);

        // get Work Group permissions
        $work_groupPermissionsBefore = $this->workGroupGetPermissions($work_groupData["work_group_id"]);

        // Will have to compose the data array for the insert operation
        $dataWorkGroupPermissions = array();

        // The parameter to determine if the operation was successful
        $insertResult = false;

        // The parameter to identify the row affected
        $insertID = 0;

        // The parameter to identify the number row affected
        $affectedRows = 0;

        // Iterate the required permission so that we get correct values for the data array
        foreach ($permissionData as $_permission) {

            $work_groupPermissionObject = array();

            $work_groupPermissionObject["permission_id"] = $_permission["permission_id"];

            $work_groupPermissionObject["work_group_id"] = $work_groupData["work_group_id"];

            $work_groupPermissionObject["is_active"] = 1;

            $dataWorkGroupPermissions[] = $work_groupPermissionObject;

        }

        //Variable to hold the delete permission result
        $deleteWorkGroupPermissionsResult = NULL;

        //If we are overriding, it means we are deleting whats already exists
        if ($override) {

            $deleteWorkGroupPermissionsResult = $this->workGroupDeletePermission($work_groupData, $work_groupPermissionsBefore);

        }

        // Now update the data with the new values or rather insert data
        if (!empty($dataWorkGroupPermissions)) {

            // Initialise the params array
            $params = array();

            // Set the table name to insert data
            $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

            //Set the data
            $params["data"] = $dataWorkGroupPermissions;

            // Insert new Permissions
            $insertResult = $this->legacyInsertData($params);

            //Get the number of affected rows
            $affectedRows = $this->db->affected_rows();

            // If the insert data was successful, then get the insert id
            if ($insertResult) {

                $insertID = $this->db->insert_id();

            }

        }

        // get Work Group permissions
        $work_groupPermissionsAfter = $this->workGroupGetPermissions($work_groupData["work_group_id"]);

        // Compose the return array
        $resultArray = array(
            "status" => $insertResult ? SUCCESS : ERROR,
            "last_insert_id" => $insertID,
            "affected_rows" => $affectedRows,
            "dataWorkGroupPermissions" => $dataWorkGroupPermissions,
            "work_group_permissions_before" => $work_groupPermissionsBefore,
            "work_group_permissions_after" => $work_groupPermissionsAfter,
            "deleteWorkGroupPermissionsResult" => $deleteWorkGroupPermissionsResult
        );

        // Return the final result
        return $resultArray;

    }

    /**
     * Update Work Group Permission
     *
     * @param $work_group_permission_id
     * @param $permission_id
     * @param $work_group_id
     * @param $new_permission_id
     * @param $new_work_group_id
     * @return array
     */
    public function workGroupUpdatePermission($work_group_permission_id, $permission_id, $work_group_id, $new_permission_id, $new_work_group_id)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

        $params["where"] = array();

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_permission_id) {

            // Also make sure we have the permission id passed
            if (filter_var($work_group_permission_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_permission_id"] = (int)$work_group_permission_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied User Permission ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid User Permission ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($permission_id) {

            // Also make sure we have the permission id passed
            if (filter_var($permission_id, FILTER_VALIDATE_INT)) {

                $params["where"]["permission_id"] = (int)$permission_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Permission ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Permission ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the permission id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // Compose the data array from request input vars
            $params["data"] = array(

                "permission_id" => $new_permission_id,

                "work_group_id" => $new_work_group_id

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * @param array $work_group
     * @param array $permission
     * @return array
     */

    public function workGroupDeletePermission($work_group = array(), $permission = array())
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Extract the work group ID
            $work_group_id = $work_group_data["work_group_id"];

        }

        $permission_id = NULL;

        if (is_array($permission)) {

            // Get the permission so that we have the correct id
            $permission_data = $this->permissionGet($permission);

        } else {

            $permission_data = NULL;

        }

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

        // Check to see if we have an id passed
        if ($work_group_id) {

            $params["where"] = array(

                "work_group_id" => $work_group_id

            );

            if (is_array($permission_data)) {

                if (isset($permission_data[0])) {

                    if (array_key_exists("permission_id", $permission_data[0])) {

                        $permission_ids = array();

                        foreach ($permission_data as $permission_item) {

                            $permission_ids[] = $permission_item["permission_id"];

                        }

                        $params["whereIn"][] = array(

                            "permission_id" => $permission_ids

                        );

                    }

                } else {

                    $permission_ids = $this->permissionGetIDs($permission_data);

                    if (!empty($permission_ids)) {

                        $params["whereIn"][] = array(

                            "permission_id" => $permission_ids

                        );

                    } else {

                        // Return an error
                        return array(

                            "status" => ERROR,

                            "message" => "Invalid Permissions"

                        );

                    }

                }

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Invalid permissions"

                );

            }

            // If not, then look for it in the array
        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyDeleteData($params);

        // Return the result
        return $result;

    }

    /**
     * @param array $work_group
     * @param array $permission
     * @param null $status
     * @return array
     */
    public function workGroupPermissionToggleStatus($work_group = array(), $permission = array(), $status = NULL)
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Depending on the result returned it can be a row array or a result array
            // If it's a result array
            if (isset($work_group_data[0])) {

                $work_group_id = $work_group_data[0]["work_group_id"];

            } else {

                // Otherwise it's a row array
                $work_group_id = $work_group_data["work_group_id"];

            }

        }

        $permission_id = NULL;

        if (is_array($permission)) {

            // Get the permission so that we have the correct id
            $permission_data = $this->permissionGet($permission, QUERY_RETURN_SINGLE);

            // Depending on the result returned, it can be a result array or a row array
            // if it's a result array
            if (isset($permission_data[0])) {

                $permission_id = $permission_data[0]["permission_id"];

            } else {

                // Otherwise its a row array
                $permission_id = $permission_data["permission_id"];

            }

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($permission_id) {

            // Also make sure we have the permission id passed
            if (filter_var($permission_id, FILTER_VALIDATE_INT)) {

                $params["where"]["permission_id"] = (int)$permission_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Permission ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Permission ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the permission id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // If the status is NULL,
            // Get the currrent status and do the opposite
            if ($status === NULL) {

                $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

                $params["returnType"] = QUERY_RETURN_SINGLE;

                $currentSettings = $this->legacyGetData($params);

                // Get the vaue of the current status
                if (is_array($currentSettings)) {

                    $currentStatus = (int)$currentSettings["is_active"];

                    if ($currentStatus) {

                        $currentStatus = 0;

                    } else {

                        $currentStatus = 1;

                    }

                } else {

                    // Return an error
                    return array(

                        "status" => ERROR,

                        "message" => "Could not determine the Work Group Permission"

                    );

                }

            } else {

                $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

                $currentStatus = (int)$status;

            }

            // Compose the data array from request input vars
            $params["data"] = array(

                "is_active" => $currentStatus

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * Check to see if the Work Group has a specified permission
     *
     * @param $work_group
     * @param $permission
     * @return array
     */
    public function workGroupHasPermission($work_group, $permission)
    {

        // Array to hold the permission data row
        $permissionData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($permission)) {

            $permissionData = $this->permissionGet($permission, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the permission data using the provided parameters"

            );

        }

        // Array to hold th work group data row
        $work_groupData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($work_group)) {

            $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find work group data using the provided parameters"

            );

        }

        // If we have all the data, assess if we can get the permission from the work group
        if ($permissionData != NULL && $work_groupData != NULL) {

            // make sure all the ids are present
            if (array_key_exists("permission_id", $permissionData) && array_key_exists("work_group_id", $work_groupData)) {

                // Initialise the params array
                $params = array();

                // Set the table name to insert data
                $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

                // Return a single row with the Work Group permission data
                $params["returnType"] = QUERY_RETURN_SINGLE;

                // Create the where clause by inserting the id
                $params["where"] = array(

                    "permission_id" => $permissionData["permission_id"],

                    'work_group_id' => $work_groupData["work_group_id"]

                );

                // No check if a user type record id exist that matches the permission id
                $result = $this->legacyGetData($params);

                // If the result is defined, then the permission exists
                // Hence the work group has that permission
                if ($result != NULL && is_array($result)) {

                    // Return data
                    return array(
                        "status" => SUCCESS,
                        "data" => $result
                    );

                } else {

                    // Return error
                    return array(
                        "status" => ERROR,
                        "message" => "The Permission / Work Group combination does not exist"
                    );

                }

            } else {

                // Return error
                return array(
                    "status" => ERROR,
                    "message" => "Missing Permission ID / Work Group ID in the result set"
                );

            }

        } else {

            // Return error
            return array(
                "status" => ERROR,
                "message" => "Could not find data with the specified input parameters"
            );

        }

    }

    //***********************
    // VIEW
    //***********************

    /**
     * Create a new view
     *
     * @param array $data
     * @return array|NULL
     */
    public function viewCreateNew($data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_VIEWS;

        if (empty($data)) {
            // Compose the data array from request input vars
            $data = array(

                "view_name" => $this->acmeRequest->getPost("name"),

                "view_slug" => acme_to_slug(
                    $this->acmeRequest->getPost("name")
                )

            );
        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new view
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Update the specified view
     *
     * @param $view
     * @param array $data
     * @return array
     */
    public function viewUpdate($view, $data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_VIEWS;

        // Get the where clause
        $whereClause = $this->viewWhereClause($view);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            // Check if there is a where
            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            // Check if we are going to perform using Where
            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Only execute if we have the data
        if (!empty($data)) {

            // Get post vars
            $postVars = $_POST;

            // Check if the POST array has data
            if ($postVars != NULL) {

                $data = array();

                // Iterate all POST keys and create a data array object
                foreach ($postVars as $key => $value) {

                    $data[$key] = urldecode($value);

                }

            }

            // Add that data to the array
            $params["data"] = $data;

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The data to update is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyUpdateData($params);

        return $result;

    }

    /**
     * Compose a views where clause
     *
     * @param $view
     * @return mixed
     */
    public function viewWhereClause($view)
    {

        // Array to hold the where clause data
        $where_clause_array = array();

        // Array to hold the where in data
        $whereIn_clause_array = array();

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($view)) {

            // See if we can query using the slug
            if (array_key_exists("view_id", $view)) {

                // Get the view_id
                $view_id = $view["view_id"];

                // If the view_id is an array, its a where-in clause
                if (is_array($view_id)) {

                    $whereIn_clause_array["view_id"] = $view_id;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = " view_id = '$view_id' ";

                }

            }

            // See if we can query using the slug
            if (array_key_exists("view_slug", $view)) {

                // Get the view_slug
                $view_slug = $view["view_slug"];

                // If the view_slug is an array, its a where-in clause
                if (is_array($view_slug)) {

                    $whereIn_clause_array["view_slug"] = $view_slug;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "view_slug = '$view_slug'";

                }

            }

            // See if we can query using the name
            if (array_key_exists("view_name", $view)) {

                // Get the view_name
                $view_name = $view["view_name"];

                // If the view_name is an array, its a where-in clause
                if (is_array($view_name)) {

                    $whereIn_clause_array["view_name"] = $view_name;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "view_name = '$view_name'";

                }

            }

            // Compose the where clause string
            $where_clause_string = implode(" OR ", $where_clause_array);

            // Check to see if a parameter other than id is passed
            if ($where_clause_string != "") {

                // Create the required params array
                $whereClause = $where_clause_string;

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        } else {

            // Assume its a numerical id that have been passed
            if (filter_var($view, FILTER_VALIDATE_INT)) {

                // Built a where clause based on the default primary key
                $whereClause = array(

                    "view_id" => $view

                );

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        }

        // Return the where clause array
        $whereClauseArray = array(

            "where" => $whereClause,

            "whereIn" => $whereIn_clause_array

        );

        return $whereClauseArray;

    }

    /**
     * Get all views
     *
     * @return mixed
     */
    public function viewsGetAll()
    {
        // Get all data
        return $this->viewGet(0);

    }

    /**
     * Get the ids from the views array
     *
     * @param $views
     * @return mixed
     */
    public function viewGetIDs($views)
    {
        // Initialise the ids array
        $ids = array();

        if (is_array($views)) {

            // Get the id from the array
            foreach ($views as $view) {

                $ids[] = $view["view_id"];

            }

        } else {

            //Otherwise just return the passed data
            $ids = $views;

        }

        // Return the result
        return $ids;

    }

    /**
     * Get a specified view
     *
     * @param $view
     * @param string $returnType
     * @return mixed
     */
    public function viewGet($view, $returnType = QUERY_RETURN_MULTIPLE)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_VIEWS;

        // Set the data type to return
        $params["returnType"] = $returnType;

        if ($view) {

            // Extract the query criteria so that we streamline it
            foreach ($view as $k => $v) {

                // Check if the value is an array
                if (is_array($v)) {

                    // Remove duplicates
                    $v = array_unique($v, SORT_REGULAR);

                    // Sort the array
                    sort($v, SORT_REGULAR);

                    // Re assign the key with the new value
                    $view[$k] = $v;

                }

            }

            // Get the where clause
            $whereClause = $this->viewWhereClause($view);

            // Create the where clause
            if (is_array($whereClause)) {

                // If its a literal where clause
                if (!empty($whereClause["where"])) {

                    $params["where"] = $whereClause["where"];

                }

                // Check if we have multiple where-in
                if (!empty($whereClause["whereIn"])) {

                    foreach ($whereClause["whereIn"] as $k => $v) {

                        $params["whereIn"][] = array($k => $v);

                    }

                }

            }

        }

        // Return the result an an array
        $result = $this->legacyGetData($params);

        return $result;

    }

    /**
     * Delete a view
     *
     * @param $view
     * @return array
     */
    public function viewDelete($view)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_VIEWS;

        // Get the where clause
        $whereClause = $this->viewWhereClause($view);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyDeleteData($params);

        return $result;

    }

    /**
     * Get Work Group views
     *
     * @param $work_group
     * @return array
     */

    public function workGroupGetViews($work_group)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_WORK_GROUP_VIEWS;

        //Check tp see if the argument is an array
        if (is_array($work_group)) {

            // Check if the argument is already a workgroup
            if (array_key_exists("work_group_id", $work_group)) {

                // Get the Work Group data from user criterion so that we have the actual work_group_id
                $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            } else {

                // Then lets assume that the data passed is actually the work group dataa
                $work_groupData = $work_group;

            }

        } else {

            // Then lets assume the value passed is a primary columns ID
            $work_groupData = array("work_group_id" => $work_group);

        }

        // Check to see if we have an id passed, otherwise get all
        if (isset($work_groupData["work_group_id"])) {

            // Prepare a where clause
            $params["where"] = array(

                "work_group_id" => $work_groupData["work_group_id"]

            );

            // Prepare a where clause
            $params["join"] = array(

                "join_table" => "auth_views",

                "left_column" => "view_id",

                "right_column" => "view_id"

            );

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyGetData($params);


        // Return the result
        return $result;

    }

    /**
     * Get Work Groups that use the specified views
     *
     * @param $view array
     * @param $includeUsers bool
     * @return array
     */
    public function workGroupGetByViews($view, $includeUsers = FALSE)
    {

        // Array to hold the view data row
        $viewData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($view)) {

            $viewData = $this->viewGet($view);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "The argument 'view' must be an array, instead we have a string"

            );

        }

        if ($viewData != NULL) {

            // Prepare the view ids array
            $view_ids = array();

            // Iterate the view data array to get the exact view ids
            foreach ($viewData as $view) {

                // Extract thr view id and push it into the view ids array
                $view_ids[] = $view["view_id"];

            }

            // Prepare an array to hold the data to be used for the query
            $params = array();

            // Set the table name to retrieve the work group views
            $params["table"] = TABLE_WORK_GROUP_VIEWS;

            // Build the where clause using multiple view ids
            $params["whereIn"][] = array(

                // Since we are expecting the view ids to be an array
                // We are going to use where-in in the clause
                // Assign the view ids now
                "view_id" => $view_ids

            );

            // Get the work group views from the database using the query parameters
            $result_work_group_views = $this->legacyGetData($params);

            if (!empty($result_work_group_views)) {

                // Prepare an array to hold the work group ids
                $work_group_view_ids = array();

                // For each result of the work group views, parse the work group
                foreach ($result_work_group_views as $work_group) {

                    // Extract the work group id and push it into the array previously defined
                    $work_group_view_ids[] = $work_group["work_group_id"];

                }

                //Check if the view is allowed for the work_group
                $params = array();

                // set the table name to get the work groups
                $params["table"] = TABLE_WORK_GROUPS;

                // Since we now have the work group ids
                $params["whereIn"][] = array(

                    // Assign work group ids for use using the where-in operator
                    "work_group_id" => $work_group_view_ids

                );

                // Get the work groups from the database
                $result_work_groups = $this->legacyGetData($params);

                // Prepare an array to hold the work group ids
                $work_group_ids = array();

                foreach ($result_work_groups as $work_group) {

                    // Push the work group id into the array
                    $work_group_ids[] = $work_group["work_group_id"];

                    // If we are to include the users
                    if ($includeUsers) {

                        //Check if the view is allowed for the work_group
                        $params = array();

                        // Set the table name to retrieve the work group users
                        $params["table"] = TABLE_WORK_GROUP_USERS;

                        // Build the where clause so that we get users for the correct work group
                        $params["where"] = array(

                            // Assign the correct work group id to associate with the correct users
                            "work_group_id" => $work_group["work_group_id"]

                        );

                        // Retrieve the work Goup users
                        $result_work_group_users = $this->legacyGetData($params);

                        //Check if the view is allowed for the work_group
                        $params = array();

                        // Set the table name to get the users from
                        $params["table"] = TABLE_USERS;

                        // Prepare an array to hold the user ids
                        $user_ids = array();

                        // If we manage to secure the user ids
                        // then extract the ids
                        if (!empty($result_work_group_users)) {

                            foreach ($result_work_group_users as $work_group_user) {

                                // Push the user id into the array
                                $user_ids[] = $work_group_user["user_id"];

                            }

                            // Get the user details using the given parameters
                            $result_user_details = $this->getUserData($user_ids);

                            // Create the user ids key and assign a list of user ids
                            $work_group["user_ids"] = $user_ids;

                            // Create a users key and assign the user result array
                            $work_group["users"] = $result_user_details;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["users"] = array();

                        }

                    }

                    // Streamline the  work group array by assigning the work group slug as
                    // the array key for use later on
                    $work_group_array[$work_group["work_group_slug"]] = $work_group;

                }

            } else {

                // Return error
                return array(

                    "status" => ERROR,

                    "message" => "Could not find work groups for the view specified"

                );

            }

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the view data using the provided parameters"

            );

        }

        // Return the result array
        return $work_group_array;

    }

    /**
     * Add a new view to a Work Group
     *
     * @param $view
     * @param int $work_group_id
     * @return array|NULL
     */

    public function workGroupAddView($view, $work_group_id = 0)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_WORK_GROUP_VIEWS;

        if (empty($view)) {
            // Compose the data array from the args
            $data = array(

                "work_group_id" => $this->acmeRequest->getPost("work_group_id"),

                "view_id" => $this->acmeRequest->getPost("view_id")

            );
        } else {
            // Compose the data array from request input vars
            $data = array(

                "work_group_id" => $work_group_id,

                "view_id" => $view["view_id"]

            );
        }

        // Check if we have valid ids for both
        if (!$data["work_group_id"] || $data["view_id"]) {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID / View ID"

            );

        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new view
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Create a new view for a specific Work Group
     *
     * @param $work_group
     * @param $view
     * @param $override
     * @return array
     */

    public function workGroupSetViews($work_group, $view, $override = TRUE)
    {

        // Get Work Group data so that we can get the correct work_group_id
        $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        // Get the view data so that we can get the correct view_id
        $viewData = $this->viewGet($view);

        // get Work Group views
        $work_groupViewsBefore = $this->workGroupGetViews($work_groupData["work_group_id"]);

        // Will have to compose the data array for the insert operation
        $dataWorkGroupViews = array();

        // The parameter to determine if the operation was successful
        $insertResult = false;

        // The parameter to identify the row affected
        $insertID = 0;

        // The parameter to identify the number row affected
        $affectedRows = 0;

        // Iterate the required view so that we get correct values for the data array
        foreach ($viewData as $_view) {

            $work_groupViewObject = array();

            $work_groupViewObject["view_id"] = $_view["view_id"];

            $work_groupViewObject["work_group_id"] = $work_groupData["work_group_id"];

            $work_groupViewObject["is_active"] = 1;

            $dataWorkGroupViews[] = $work_groupViewObject;

        }

        //Variable to hold the delete permission result
        $deleteWorkGroupViewsResult = NULL;

        //If we are overriding, it means we are deleting whats already exists
        if ($override) {

            $deleteWorkGroupViewsResult = $this->workGroupDeleteView($work_groupData, $work_groupViewsBefore);

        }

        // Now update the data with the new values or rather insert data
        if (!empty($dataWorkGroupViews)) {

            // Initialise the params array
            $params = array();

            // Set the table name to insert data
            $params["table"] = TABLE_WORK_GROUP_VIEWS;

            //Set the data
            $params["data"] = $dataWorkGroupViews;

            // Insert new Views
            $insertResult = $this->legacyInsertData($params);

            //Get the number of affected rows
            $affectedRows = $this->db->affected_rows();

            // If the insert data was successful, then get the insert id
            if ($insertResult) {

                $insertID = $this->db->insert_id();

            }

        }

        // get Work Group views
        $work_groupViewsAfter = $this->workGroupGetViews($work_groupData["work_group_id"]);

        // Compose the return array
        $resultArray = array(
            "status" => $insertResult ? SUCCESS : ERROR,
            "last_insert_id" => $insertID,
            "affected_rows" => $affectedRows,
            "dataWorkGroupViews" => $dataWorkGroupViews,
            "work_group_views_before" => $work_groupViewsBefore,
            "work_group_views_after" => $work_groupViewsAfter,
            "deleteWorkGroupViewsResult" => $deleteWorkGroupViewsResult
        );

        // Return the final result
        return $resultArray;

    }

    /**
     * Update Work Group View
     *
     * @param $work_group_view_id
     * @param $view_id
     * @param $work_group_id
     * @param $new_view_id
     * @param $new_work_group_id
     * @return array
     */
    public function workGroupUpdateView($work_group_view_id, $view_id, $work_group_id, $new_view_id, $new_work_group_id)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_WORK_GROUP_VIEWS;

        $params["where"] = array();

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_view_id) {

            // Also make sure we have the view id passed
            if (filter_var($work_group_view_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_view_id"] = (int)$work_group_view_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied User View ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid User View ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($view_id) {

            // Also make sure we have the view id passed
            if (filter_var($view_id, FILTER_VALIDATE_INT)) {

                $params["where"]["view_id"] = (int)$view_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied View ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid View ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the view id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // Compose the data array from request input vars
            $params["data"] = array(

                "view_id" => $new_view_id,

                "work_group_id" => $new_work_group_id

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * @param array $work_group
     * @param array $view
     * @return array
     */

    public function workGroupDeleteView($work_group = array(), $view = array())
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Extract the work group ID
            $work_group_id = $work_group_data["work_group_id"];

        }

        $view_id = NULL;

        if (is_array($view)) {

            // Get the view so that we have the correct id
            $view_data = $this->viewGet($view);

        } else {

            $view_data = NULL;

        }

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_WORK_GROUP_VIEWS;

        // Check to see if we have an id passed
        if ($work_group_id) {

            $params["where"] = array(

                "work_group_id" => $work_group_id

            );

            if (is_array($view_data)) {

                if (isset($view_data[0])) {

                    if (array_key_exists("view_id", $view_data[0])) {

                        $view_ids = array();

                        foreach ($view_data as $view_item) {

                            $view_ids[] = $view_item["view_id"];

                        }

                        $params["whereIn"][] = array(

                            "view_id" => $view_ids

                        );

                    }

                } else {

                    $view_ids = $this->viewGetIDs($view_data);

                    if (!empty($view_ids)) {

                        $params["whereIn"][] = array(

                            "view_id" => $view_ids

                        );

                    } else {

                        // Return an error
                        return array(

                            "status" => ERROR,

                            "message" => "Invalid Views"

                        );

                    }

                }

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Invalid views"

                );

            }

            // If not, then look for it in the array
        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyDeleteData($params);

        // Return the result
        return $result;

    }

    /**
     * @param array $work_group
     * @param array $view
     * @param null $status
     * @return array
     */
    public function workGroupViewToggleStatus($work_group = array(), $view = array(), $status = NULL)
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Depending on the result returned it can be a row array or a result array
            // If it's a result array
            if (isset($work_group_data[0])) {

                $work_group_id = $work_group_data[0]["work_group_id"];

            } else {

                // Otherwise it's a row array
                $work_group_id = $work_group_data["work_group_id"];

            }

        }

        $view_id = NULL;

        if (is_array($view)) {

            // Get the view so that we have the correct id
            $view_data = $this->viewGet($view, QUERY_RETURN_SINGLE);

            // Depending on the result returned, it can be a result array or a row array
            // if it's a result array
            if (isset($view_data[0])) {

                $view_id = $view_data[0]["view_id"];

            } else {

                // Otherwise its a row array
                $view_id = $view_data["view_id"];

            }

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($view_id) {

            // Also make sure we have the view id passed
            if (filter_var($view_id, FILTER_VALIDATE_INT)) {

                $params["where"]["view_id"] = (int)$view_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied View ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid View ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the view id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // If the status is NULL,
            // Get the currrent status and do the opposite
            if ($status === NULL) {

                $params["table"] = TABLE_WORK_GROUP_VIEWS;

                $params["returnType"] = QUERY_RETURN_SINGLE;

                $currentSettings = $this->legacyGetData($params);

                // Get the vaue of the current status
                if (is_array($currentSettings)) {

                    $currentStatus = (int)$currentSettings["is_active"];

                    if ($currentStatus) {

                        $currentStatus = 0;

                    } else {

                        $currentStatus = 1;

                    }

                } else {

                    // Return an error
                    return array(

                        "status" => ERROR,

                        "message" => "Could not determine the Work Group View"

                    );

                }

            } else {

                $params["table"] = TABLE_WORK_GROUP_VIEWS;

                $currentStatus = (int)$status;

            }

            // Compose the data array from request input vars
            $params["data"] = array(

                "is_active" => $currentStatus

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * Check to see if the Work Group has a specified view
     *
     * @param $work_group
     * @param $view
     * @return array
     */
    public function workGroupHasView($work_group, $view)
    {

        // Array to hold the view data row
        $viewData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($view)) {

            $viewData = $this->viewGet($view, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the view data using the provided parameters"

            );

        }

        // Array to hold th work group data row
        $work_groupData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($work_group)) {

            $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find work group data using the provided parameters"

            );

        }

        // If we have all the data, assess if we can get the view from the work group
        if ($viewData != NULL && $work_groupData != NULL) {

            // make sure all the ids are present
            if (array_key_exists("view_id", $viewData) && array_key_exists("work_group_id", $work_groupData)) {

                // Initialise the params array
                $params = array();

                // Set the table name to insert data
                $params["table"] = TABLE_WORK_GROUP_VIEWS;

                // Return a single row with the Work Group view data
                $params["returnType"] = QUERY_RETURN_SINGLE;

                // Create the where clause by inserting the id
                $params["where"] = array(

                    "view_id" => $viewData["view_id"],

                    'work_group_id' => $work_groupData["work_group_id"]

                );

                // No check if a user type record id exist that matches the view id
                $result = $this->legacyGetData($params);

                // If the result is defined, then the view exists
                // Hence the work group has that view
                if ($result != NULL && is_array($result)) {

                    // Return data
                    return array(
                        "status" => SUCCESS,
                        "data" => $result
                    );

                } else {

                    // Return error
                    return array(
                        "status" => ERROR,
                        "message" => "The View / Work Group combination does not exist"
                    );

                }

            } else {

                // Return error
                return array(
                    "status" => ERROR,
                    "message" => "Missing View ID / Work Group ID in the result set"
                );

            }

        } else {

            // Return error
            return array(
                "status" => ERROR,
                "message" => "Could not find data with the specified input parameters"
            );

        }

    }

    //***********************
    // MODULE
    //***********************

    /**
     * Create a new module
     *
     * @param array $data
     * @return array|NULL
     */
    public function moduleCreateNew($data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_MODULES;

        if (empty($data)) {
            // Compose the data array from request input vars
            $data = array(

                "module_name" => $this->acmeRequest->getPost("name"),

                "module_slug" => acme_to_slug(
                    $this->acmeRequest->getPost("name")
                )

            );
        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new module
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Update the specified module
     *
     * @param $module
     * @param array $data
     * @return array
     */
    public function moduleUpdate($module, $data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_MODULES;

        // Get the where clause
        $whereClause = $this->moduleWhereClause($module);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            // Check if there is a where
            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            // Check if we are going to perform using Where
            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Only execute if we have the data
        if (!empty($data)) {

            // Get post vars
            $postVars = $_POST;

            // Check if the POST array has data
            if ($postVars != NULL) {

                $data = array();

                // Iterate all POST keys and create a data array object
                foreach ($postVars as $key => $value) {

                    $data[$key] = urldecode($value);

                }

            }

            // Add that data to the array
            $params["data"] = $data;

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The data to update is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyUpdateData($params);

        return $result;

    }

    /**
     * Compose a modules where clause
     *
     * @param $module
     * @return mixed
     */
    public function moduleWhereClause($module)
    {

        // Array to hold the where clause data
        $where_clause_array = array();

        // Array to hold the where in data
        $whereIn_clause_array = array();

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($module)) {

            // See if we can query using the slug
            if (array_key_exists("module_id", $module)) {

                // Get the module_id
                $module_id = $module["module_id"];

                // If the module_id is an array, its a where-in clause
                if (is_array($module_id)) {

                    $whereIn_clause_array["module_id"] = $module_id;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = " module_id = '$module_id' ";

                }

            }

            // See if we can query using the slug
            if (array_key_exists("module_slug", $module)) {

                // Get the module_slug
                $module_slug = $module["module_slug"];

                // If the module_slug is an array, its a where-in clause
                if (is_array($module_slug)) {

                    $whereIn_clause_array["module_slug"] = $module_slug;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "module_slug = '$module_slug'";

                }

            }

            // See if we can query using the name
            if (array_key_exists("module_name", $module)) {

                // Get the module_name
                $module_name = $module["module_name"];

                // If the module_name is an array, its a where-in clause
                if (is_array($module_name)) {

                    $whereIn_clause_array["module_name"] = $module_name;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "module_name = '$module_name'";

                }

            }

            // Compose the where clause string
            $where_clause_string = implode(" OR ", $where_clause_array);

            // Check to see if a parameter other than id is passed
            if ($where_clause_string != "") {

                // Create the required params array
                $whereClause = $where_clause_string;

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        } else {

            // Assume its a numerical id that have been passed
            if (filter_var($module, FILTER_VALIDATE_INT)) {

                // Built a where clause based on the default primary key
                $whereClause = array(

                    "module_id" => $module

                );

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        }

        // Return the where clause array
        $whereClauseArray = array(

            "where" => $whereClause,

            "whereIn" => $whereIn_clause_array

        );

        return $whereClauseArray;

    }

    /**
     * Get all modules
     *
     * @return mixed
     */
    public function modulesGetAll()
    {
        // Get all data
        return $this->moduleGet(0);

    }

    /**
     * Get the ids from the modules array
     *
     * @param $modules
     * @return mixed
     */
    public function moduleGetIDs($modules)
    {
        // Initialise the ids array
        $ids = array();

        if (is_array($modules)) {

            // Get the id from the array
            foreach ($modules as $module) {

                $ids[] = $module["module_id"];

            }

        } else {

            //Otherwise just return the passed data
            $ids = $modules;

        }

        // Return the result
        return $ids;

    }

    /**
     * Get a specified module
     *
     * @param $module
     * @param string $returnType
     * @return mixed
     */
    public function moduleGet($module, $returnType = QUERY_RETURN_MULTIPLE)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_MODULES;

        // Set the data type to return
        $params["returnType"] = $returnType;

        if ($module) {

            // Extract the query criteria so that we streamline it
            foreach ($module as $k => $v) {

                // Check if the value is an array
                if (is_array($v)) {

                    // Remove duplicates
                    $v = array_unique($v, SORT_REGULAR);

                    // Sort the array
                    sort($v, SORT_REGULAR);

                    // Re assign the key with the new value
                    $module[$k] = $v;

                }

            }

            // Get the where clause
            $whereClause = $this->moduleWhereClause($module);

            // Create the where clause
            if (is_array($whereClause)) {

                // If its a literal where clause
                if (!empty($whereClause["where"])) {

                    $params["where"] = $whereClause["where"];

                }

                // Check if we have multiple where-in
                if (!empty($whereClause["whereIn"])) {

                    foreach ($whereClause["whereIn"] as $k => $v) {

                        $params["whereIn"][] = array($k => $v);

                    }

                }

            }

        }

        // Return the result an an array
        $result = $this->legacyGetData($params);

        return $result;

    }

    /**
     * Delete a module
     *
     * @param $module
     * @return array
     */
    public function moduleDelete($module)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_MODULES;

        // Get the where clause
        $whereClause = $this->moduleWhereClause($module);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyDeleteData($params);

        return $result;

    }

    /**
     * Get Work Group modules
     *
     * @param $work_group
     * @return array
     */

    public function workGroupGetModules($work_group)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_WORK_GROUP_MODULES;

        //Check tp see if the argument is an array
        if (is_array($work_group)) {

            // Check if the argument is already a workgroup
            if (array_key_exists("work_group_id", $work_group)) {

                // Get the Work Group data from user criterion so that we have the actual work_group_id
                $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            } else {

                // Then lets assume that the data passed is actually the work group dataa
                $work_groupData = $work_group;

            }

        } else {

            // Then lets assume the value passed is a primary columns ID
            $work_groupData = array("work_group_id" => $work_group);

        }

        // Check to see if we have an id passed, otherwise get all
        if (isset($work_groupData["work_group_id"])) {

            // Prepare a where clause
            $params["where"] = array(

                "work_group_id" => $work_groupData["work_group_id"]

            );

            // Prepare a where clause
            $params["join"] = array(

                "join_table" => "auth_modules",

                "left_column" => "module_id",

                "right_column" => "module_id"

            );

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyGetData($params);


        // Return the result
        return $result;

    }

    /**
     * Get Work Groups that use the specified modules
     *
     * @param $module array
     * @param $includeUsers bool
     * @return array
     */
    public function workGroupGetByModules($module, $includeUsers = FALSE)
    {

        // Array to hold the module data row
        $moduleData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($module)) {

            $moduleData = $this->moduleGet($module);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "The argument 'module' must be an array, instead we have a string"

            );

        }

        if ($moduleData != NULL) {

            // Prepare the module ids array
            $module_ids = array();

            // Iterate the module data array to get the exact module ids
            foreach ($moduleData as $module) {

                // Extract thr module id and push it into the module ids array
                $module_ids[] = $module["module_id"];

            }

            // Prepare an array to hold the data to be used for the query
            $params = array();

            // Set the table name to retrieve the work group modules
            $params["table"] = TABLE_WORK_GROUP_MODULES;

            // Build the where clause using multiple module ids
            $params["whereIn"][] = array(

                // Since we are expecting the module ids to be an array
                // We are going to use where-in in the clause
                // Assign the module ids now
                "module_id" => $module_ids

            );

            // Get the work group modules from the database using the query parameters
            $result_work_group_modules = $this->legacyGetData($params);

            if (!empty($result_work_group_modules)) {

                // Prepare an array to hold the work group ids
                $work_group_module_ids = array();

                // For each result of the work group modules, parse the work group
                foreach ($result_work_group_modules as $work_group) {

                    // Extract the work group id and push it into the array previously defined
                    $work_group_module_ids[] = $work_group["work_group_id"];

                }

                //Check if the module is allowed for the work_group
                $params = array();

                // set the table name to get the work groups
                $params["table"] = TABLE_WORK_GROUPS;

                // Since we now have the work group ids
                $params["whereIn"][] = array(

                    // Assign work group ids for use using the where-in operator
                    "work_group_id" => $work_group_module_ids

                );

                // Get the work groups from the database
                $result_work_groups = $this->legacyGetData($params);

                // Prepare an array to hold the work group ids
                $work_group_ids = array();

                foreach ($result_work_groups as $work_group) {

                    // Push the work group id into the array
                    $work_group_ids[] = $work_group["work_group_id"];

                    // If we are to include the users
                    if ($includeUsers) {

                        //Check if the module is allowed for the work_group
                        $params = array();

                        // Set the table name to retrieve the work group users
                        $params["table"] = TABLE_WORK_GROUP_USERS;

                        // Build the where clause so that we get users for the correct work group
                        $params["where"] = array(

                            // Assign the correct work group id to associate with the correct users
                            "work_group_id" => $work_group["work_group_id"]

                        );

                        // Retrieve the work Goup users
                        $result_work_group_users = $this->legacyGetData($params);

                        //Check if the module is allowed for the work_group
                        $params = array();

                        // Set the table name to get the users from
                        $params["table"] = TABLE_USERS;

                        // Prepare an array to hold the user ids
                        $user_ids = array();

                        // If we manage to secure the user ids
                        // then extract the ids
                        if (!empty($result_work_group_users)) {

                            foreach ($result_work_group_users as $work_group_user) {

                                // Push the user id into the array
                                $user_ids[] = $work_group_user["user_id"];

                            }

                            // Get the user details using the given parameters
                            $result_user_details = $this->getUserData($user_ids);

                            // Create the user ids key and assign a list of user ids
                            $work_group["user_ids"] = $user_ids;

                            // Create a users key and assign the user result array
                            $work_group["users"] = $result_user_details;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["users"] = array();

                        }

                    }

                    // Streamline the  work group array by assigning the work group slug as
                    // the array key for use later on
                    $work_group_array[$work_group["work_group_slug"]] = $work_group;

                }

            } else {

                // Return error
                return array(

                    "status" => ERROR,

                    "message" => "Could not find work groups for the module specified"

                );

            }

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the module data using the provided parameters"

            );

        }

        // Return the result array
        return $work_group_array;

    }

    /**
     * Add a new module to a Work Group
     *
     * @param $module
     * @param int $work_group_id
     * @return array|NULL
     */

    public function workGroupAddModule($module, $work_group_id = 0)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_WORK_GROUP_MODULES;

        if (empty($module)) {
            // Compose the data array from the args
            $data = array(

                "work_group_id" => $this->acmeRequest->getPost("work_group_id"),

                "module_id" => $this->acmeRequest->getPost("module_id")

            );
        } else {
            // Compose the data array from request input vars
            $data = array(

                "work_group_id" => $work_group_id,

                "module_id" => $module["module_id"]

            );
        }

        // Check if we have valid ids for both
        if (!$data["work_group_id"] || $data["module_id"]) {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID / Module ID"

            );

        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new module
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Create a new module for a specific Work Group
     *
     * @param $work_group
     * @param $module
     * @param $override
     * @return array
     */

    public function workGroupSetModules($work_group, $module, $override = TRUE)
    {

        // Get Work Group data so that we can get the correct work_group_id
        $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        // Get the module data so that we can get the correct module_id
        $moduleData = $this->moduleGet($module);

        // get Work Group modules
        $work_groupModulesBefore = $this->workGroupGetModules($work_groupData["work_group_id"]);

        // Will have to compose the data array for the insert operation
        $dataWorkGroupModules = array();

        // The parameter to determine if the operation was successful
        $insertResult = false;

        // The parameter to identify the row affected
        $insertID = 0;

        // The parameter to identify the number row affected
        $affectedRows = 0;

        // Iterate the required module so that we get correct values for the data array
        foreach ($moduleData as $_module) {

            $work_groupModuleObject = array();

            $work_groupModuleObject["module_id"] = $_module["module_id"];

            $work_groupModuleObject["work_group_id"] = $work_groupData["work_group_id"];

            $work_groupModuleObject["is_active"] = 1;

            $dataWorkGroupModules[] = $work_groupModuleObject;

        }

        //Variable to hold the delete permission result
        $deleteWorkGroupModulesResult = NULL;

        //If we are overriding, it means we are deleting whats already exists
        if ($override) {

            $deleteWorkGroupModulesResult = $this->workGroupDeleteModule($work_groupData, $work_groupModulesBefore);

        }

        // Now update the data with the new values or rather insert data
        if (!empty($dataWorkGroupModules)) {

            // Initialise the params array
            $params = array();

            // Set the table name to insert data
            $params["table"] = TABLE_WORK_GROUP_MODULES;

            //Set the data
            $params["data"] = $dataWorkGroupModules;

            // Insert new Modules
            $insertResult = $this->legacyInsertData($params);

            //Get the number of affected rows
            $affectedRows = $this->db->affected_rows();

            // If the insert data was successful, then get the insert id
            if ($insertResult) {

                $insertID = $this->db->insert_id();

            }

        }

        // get Work Group modules
        $work_groupModulesAfter = $this->workGroupGetModules($work_groupData["work_group_id"]);

        // Compose the return array
        $resultArray = array(
            "status" => $insertResult ? SUCCESS : ERROR,
            "last_insert_id" => $insertID,
            "affected_rows" => $affectedRows,
            "dataWorkGroupModules" => $dataWorkGroupModules,
            "work_group_modules_before" => $work_groupModulesBefore,
            "work_group_modules_after" => $work_groupModulesAfter,
            "deleteWorkGroupModulesResult" => $deleteWorkGroupModulesResult
        );

        // Return the final result
        return $resultArray;

    }

    /**
     * Update Work Group Module
     *
     * @param $work_group_module_id
     * @param $module_id
     * @param $work_group_id
     * @param $new_module_id
     * @param $new_work_group_id
     * @return array
     */
    public function workGroupUpdateModule($work_group_module_id, $module_id, $work_group_id, $new_module_id, $new_work_group_id)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_WORK_GROUP_MODULES;

        $params["where"] = array();

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_module_id) {

            // Also make sure we have the module id passed
            if (filter_var($work_group_module_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_module_id"] = (int)$work_group_module_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied User Module ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid User Module ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($module_id) {

            // Also make sure we have the module id passed
            if (filter_var($module_id, FILTER_VALIDATE_INT)) {

                $params["where"]["module_id"] = (int)$module_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Module ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Module ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the module id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // Compose the data array from request input vars
            $params["data"] = array(

                "module_id" => $new_module_id,

                "work_group_id" => $new_work_group_id

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * @param array $work_group
     * @param array $module
     * @return array
     */

    public function workGroupDeleteModule($work_group = array(), $module = array())
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Extract the work group ID
            $work_group_id = $work_group_data["work_group_id"];

        }

        $module_id = NULL;

        if (is_array($module)) {

            // Get the module so that we have the correct id
            $module_data = $this->moduleGet($module);

        } else {

            $module_data = NULL;

        }

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_WORK_GROUP_MODULES;

        // Check to see if we have an id passed
        if ($work_group_id) {

            $params["where"] = array(

                "work_group_id" => $work_group_id

            );

            if (is_array($module_data)) {

                if (isset($module_data[0])) {

                    if (array_key_exists("module_id", $module_data[0])) {

                        $module_ids = array();

                        foreach ($module_data as $module_item) {

                            $module_ids[] = $module_item["module_id"];

                        }

                        $params["whereIn"][] = array(

                            "module_id" => $module_ids

                        );

                    }

                } else {

                    $module_ids = $this->moduleGetIDs($module_data);

                    if (!empty($module_ids)) {

                        $params["whereIn"][] = array(

                            "module_id" => $module_ids

                        );

                    } else {

                        // Return an error
                        return array(

                            "status" => ERROR,

                            "message" => "Invalid Modules"

                        );

                    }

                }

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Invalid modules"

                );

            }

            // If not, then look for it in the array
        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyDeleteData($params);

        // Return the result
        return $result;

    }

    /**
     * @param array $work_group
     * @param array $module
     * @param null $status
     * @return array
     */
    public function workGroupModuleToggleStatus($work_group = array(), $module = array(), $status = NULL)
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Depending on the result returned it can be a row array or a result array
            // If it's a result array
            if (isset($work_group_data[0])) {

                $work_group_id = $work_group_data[0]["work_group_id"];

            } else {

                // Otherwise it's a row array
                $work_group_id = $work_group_data["work_group_id"];

            }

        }

        $module_id = NULL;

        if (is_array($module)) {

            // Get the module so that we have the correct id
            $module_data = $this->moduleGet($module, QUERY_RETURN_SINGLE);

            // Depending on the result returned, it can be a result array or a row array
            // if it's a result array
            if (isset($module_data[0])) {

                $module_id = $module_data[0]["module_id"];

            } else {

                // Otherwise its a row array
                $module_id = $module_data["module_id"];

            }

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($module_id) {

            // Also make sure we have the module id passed
            if (filter_var($module_id, FILTER_VALIDATE_INT)) {

                $params["where"]["module_id"] = (int)$module_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Module ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Module ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the module id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // If the status is NULL,
            // Get the currrent status and do the opposite
            if ($status === NULL) {

                $params["table"] = TABLE_WORK_GROUP_MODULES;

                $params["returnType"] = QUERY_RETURN_SINGLE;

                $currentSettings = $this->legacyGetData($params);

                // Get the vaue of the current status
                if (is_array($currentSettings)) {

                    $currentStatus = (int)$currentSettings["is_active"];

                    if ($currentStatus) {

                        $currentStatus = 0;

                    } else {

                        $currentStatus = 1;

                    }

                } else {

                    // Return an error
                    return array(

                        "status" => ERROR,

                        "message" => "Could not determine the Work Group Module"

                    );

                }

            } else {

                $params["table"] = TABLE_WORK_GROUP_MODULES;

                $currentStatus = (int)$status;

            }

            // Compose the data array from request input vars
            $params["data"] = array(

                "is_active" => $currentStatus

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * Check to see if the Work Group has a specified module
     *
     * @param $work_group
     * @param $module
     * @return array
     */
    public function workGroupHasModule($work_group, $module)
    {

        // Array to hold the module data row
        $moduleData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($module)) {

            $moduleData = $this->moduleGet($module, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the module data using the provided parameters"

            );

        }

        // Array to hold th work group data row
        $work_groupData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($work_group)) {

            $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find work group data using the provided parameters"

            );

        }

        // If we have all the data, assess if we can get the module from the work group
        if ($moduleData != NULL && $work_groupData != NULL) {

            // make sure all the ids are present
            if (array_key_exists("module_id", $moduleData) && array_key_exists("work_group_id", $work_groupData)) {

                // Initialise the params array
                $params = array();

                // Set the table name to insert data
                $params["table"] = TABLE_WORK_GROUP_MODULES;

                // Return a single row with the Work Group module data
                $params["returnType"] = QUERY_RETURN_SINGLE;

                // Create the where clause by inserting the id
                $params["where"] = array(

                    "module_id" => $moduleData["module_id"],

                    'work_group_id' => $work_groupData["work_group_id"]

                );

                // No check if a user type record id exist that matches the module id
                $result = $this->legacyGetData($params);

                // If the result is defined, then the module exists
                // Hence the work group has that module
                if ($result != NULL && is_array($result)) {

                    // Return data
                    return array(
                        "status" => SUCCESS,
                        "data" => $result
                    );

                } else {

                    // Return error
                    return array(
                        "status" => ERROR,
                        "message" => "The Module / Work Group combination does not exist"
                    );

                }

            } else {

                // Return error
                return array(
                    "status" => ERROR,
                    "message" => "Missing Module ID / Work Group ID in the result set"
                );

            }

        } else {

            // Return error
            return array(
                "status" => ERROR,
                "message" => "Could not find data with the specified input parameters"
            );

        }

    }


    //***********************
    // COMPONENT
    //***********************

    /**
     * Create a new component
     *
     * @param array $data
     * @return array|NULL
     */
    public function componentCreateNew($data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_COMPONENTS;

        if (empty($data)) {
            // Compose the data array from request input vars
            $data = array(

                "component_name" => $this->acmeRequest->getPost("name"),

                "component_slug" => acme_to_slug(
                    $this->acmeRequest->getPost("name")
                )

            );
        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new component
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Update the specified component
     *
     * @param $component
     * @param array $data
     * @return array
     */
    public function componentUpdate($component, $data = array())
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_COMPONENTS;

        // Get the where clause
        $whereClause = $this->componentWhereClause($component);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            // Check if there is a where
            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            // Check if we are going to perform using Where
            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Only execute if we have the data
        if (!empty($data)) {

            // Get post vars
            $postVars = $_POST;

            // Check if the POST array has data
            if ($postVars != NULL) {

                $data = array();

                // Iterate all POST keys and create a data array object
                foreach ($postVars as $key => $value) {

                    $data[$key] = urldecode($value);

                }

            }

            // Add that data to the array
            $params["data"] = $data;

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The data to update is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyUpdateData($params);

        return $result;

    }

    /**
     * Compose a components where clause
     *
     * @param $component
     * @return mixed
     */
    public function componentWhereClause($component)
    {

        // Array to hold the where clause data
        $where_clause_array = array();

        // Array to hold the where in data
        $whereIn_clause_array = array();

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($component)) {

            // See if we can query using the slug
            if (array_key_exists("component_id", $component)) {

                // Get the component_id
                $component_id = $component["component_id"];

                // If the component_id is an array, its a where-in clause
                if (is_array($component_id)) {

                    $whereIn_clause_array["component_id"] = $component_id;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = " component_id = '$component_id' ";

                }

            }

            // See if we can query using the slug
            if (array_key_exists("component_slug", $component)) {

                // Get the component_slug
                $component_slug = $component["component_slug"];

                // If the component_slug is an array, its a where-in clause
                if (is_array($component_slug)) {

                    $whereIn_clause_array["component_slug"] = $component_slug;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "component_slug = '$component_slug'";

                }

            }

            // See if we can query using the name
            if (array_key_exists("component_name", $component)) {

                // Get the component_name
                $component_name = $component["component_name"];

                // If the component_name is an array, its a where-in clause
                if (is_array($component_name)) {

                    $whereIn_clause_array["component_name"] = $component_name;

                } else {

                    // Otherwise its going to be a literal sql string
                    $where_clause_array[] = "component_name = '$component_name'";

                }

            }

            // Compose the where clause string
            $where_clause_string = implode(" OR ", $where_clause_array);

            // Check to see if a parameter other than id is passed
            if ($where_clause_string != "") {

                // Create the required params array
                $whereClause = $where_clause_string;

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        } else {

            // Assume its a numerical id that have been passed
            if (filter_var($component, FILTER_VALIDATE_INT)) {

                // Built a where clause based on the default primary key
                $whereClause = array(

                    "component_id" => $component

                );

            } else {

                // Create an empty where clause
                $whereClause = "";

            }

        }

        // Return the where clause array
        $whereClauseArray = array(

            "where" => $whereClause,

            "whereIn" => $whereIn_clause_array

        );

        return $whereClauseArray;

    }

    /**
     * Get all components
     *
     * @return mixed
     */
    public function componentsGetAll()
    {
        // Get all data
        return $this->componentGet(0);

    }

    /**
     * Get the ids from the components array
     *
     * @param $components
     * @return mixed
     */
    public function componentGetIDs($components)
    {
        // Initialise the ids array
        $ids = array();

        if (is_array($components)) {

            // Get the id from the array
            foreach ($components as $component) {

                $ids[] = $component["component_id"];

            }

        } else {

            //Otherwise just return the passed data
            $ids = $components;

        }

        // Return the result
        return $ids;

    }

    /**
     * Get a specified component
     *
     * @param $component
     * @param string $returnType
     * @return mixed
     */
    public function componentGet($component, $returnType = QUERY_RETURN_MULTIPLE)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_COMPONENTS;

        // Set the data type to return
        $params["returnType"] = $returnType;

        if ($component) {

            // Extract the query criteria so that we streamline it
            foreach ($component as $k => $v) {

                // Check if the value is an array
                if (is_array($v)) {

                    // Remove duplicates
                    $v = array_unique($v, SORT_REGULAR);

                    // Sort the array
                    sort($v, SORT_REGULAR);

                    // Re assign the key with the new value
                    $component[$k] = $v;

                }

            }

            // Get the where clause
            $whereClause = $this->componentWhereClause($component);

            // Create the where clause
            if (is_array($whereClause)) {

                // If its a literal where clause
                if (!empty($whereClause["where"])) {

                    $params["where"] = $whereClause["where"];

                }

                // Check if we have multiple where-in
                if (!empty($whereClause["whereIn"])) {

                    foreach ($whereClause["whereIn"] as $k => $v) {

                        $params["whereIn"][] = array($k => $v);

                    }

                }

            }

        }

        // Return the result an an array
        $result = $this->legacyGetData($params);

        return $result;

    }

    /**
     * Delete a component
     *
     * @param $component
     * @return array
     */
    public function componentDelete($component)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_COMPONENTS;

        // Get the where clause
        $whereClause = $this->componentWhereClause($component);

        // Create the where clause
        if (!empty($whereClause) || !$whereClause || $whereClause == "") {

            if (!empty($whereClause["where"])) {

                $params["where"] = $whereClause["where"];

            }

            if (!empty($whereClause["whereIn"])) {

                $params["whereIn"] = $whereClause["whereIn"];

            }

        } else {

            // The data is empty so there is nothing to update
            return array(

                "status" => ERROR,

                "message" => "The operation failed because the where clause is empty"

            );

        }

        // Return the result an an array
        $result = $this->legacyDeleteData($params);

        return $result;

    }

    /**
     * Get Work Group components
     *
     * @param $work_group
     * @return array
     */

    public function workGroupGetComponents($work_group)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to get the data
        $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

        //Check tp see if the argument is an array
        if (is_array($work_group)) {

            // Check if the argument is already a workgroup
            if (array_key_exists("work_group_id", $work_group)) {

                // Get the Work Group data from user criterion so that we have the actual work_group_id
                $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            } else {

                // Then lets assume that the data passed is actually the work group dataa
                $work_groupData = $work_group;

            }

        } else {

            // Then lets assume the value passed is a primary columns ID
            $work_groupData = array("work_group_id" => $work_group);

        }

        // Check to see if we have an id passed, otherwise get all
        if (isset($work_groupData["work_group_id"])) {

            // Prepare a where clause
            $params["where"] = array(

                "work_group_id" => $work_groupData["work_group_id"]

            );

            // Prepare a where clause
            $params["join"] = array(

                "join_table" => "auth_components",

                "left_column" => "component_id",

                "right_column" => "component_id"

            );

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyGetData($params);


        // Return the result
        return $result;

    }

    /**
     * Get Work Groups that use the specified components
     *
     * @param $component array
     * @param $includeUsers bool
     * @return array
     */
    public function workGroupGetByComponents($component, $includeUsers = FALSE)
    {

        // Array to hold the component data row
        $componentData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($component)) {

            $componentData = $this->componentGet($component);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "The argument 'component' must be an array, instead we have a string"

            );

        }

        if ($componentData != NULL) {

            // Prepare the component ids array
            $component_ids = array();

            // Iterate the component data array to get the exact component ids
            foreach ($componentData as $component) {

                // Extract thr component id and push it into the component ids array
                $component_ids[] = $component["component_id"];

            }

            // Prepare an array to hold the data to be used for the query
            $params = array();

            // Set the table name to retrieve the work group components
            $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

            // Build the where clause using multiple component ids
            $params["whereIn"][] = array(

                // Since we are expecting the component ids to be an array
                // We are going to use where-in in the clause
                // Assign the component ids now
                "component_id" => $component_ids

            );

            // Get the work group components from the database using the query parameters
            $result_work_group_components = $this->legacyGetData($params);

            if (!empty($result_work_group_components)) {

                // Prepare an array to hold the work group ids
                $work_group_component_ids = array();

                // For each result of the work group components, parse the work group
                foreach ($result_work_group_components as $work_group) {

                    // Extract the work group id and push it into the array previously defined
                    $work_group_component_ids[] = $work_group["work_group_id"];

                }

                //Check if the component is allowed for the work_group
                $params = array();

                // set the table name to get the work groups
                $params["table"] = TABLE_WORK_GROUPS;

                // Since we now have the work group ids
                $params["whereIn"][] = array(

                    // Assign work group ids for use using the where-in operator
                    "work_group_id" => $work_group_component_ids

                );

                // Get the work groups from the database
                $result_work_groups = $this->legacyGetData($params);

                // Prepare an array to hold the work group ids
                $work_group_ids = array();

                foreach ($result_work_groups as $work_group) {

                    // Push the work group id into the array
                    $work_group_ids[] = $work_group["work_group_id"];

                    // If we are to include the users
                    if ($includeUsers) {

                        //Check if the component is allowed for the work_group
                        $params = array();

                        // Set the table name to retrieve the work group users
                        $params["table"] = TABLE_WORK_GROUP_USERS;

                        // Build the where clause so that we get users for the correct work group
                        $params["where"] = array(

                            // Assign the correct work group id to associate with the correct users
                            "work_group_id" => $work_group["work_group_id"]

                        );

                        // Retrieve the work Goup users
                        $result_work_group_users = $this->legacyGetData($params);

                        //Check if the component is allowed for the work_group
                        $params = array();

                        // Set the table name to get the users from
                        $params["table"] = TABLE_USERS;

                        // Prepare an array to hold the user ids
                        $user_ids = array();

                        // If we manage to secure the user ids
                        // then extract the ids
                        if (!empty($result_work_group_users)) {

                            foreach ($result_work_group_users as $work_group_user) {

                                // Push the user id into the array
                                $user_ids[] = $work_group_user["user_id"];

                            }

                            // Get the user details using the given parameters
                            $result_user_details = $this->getUserData($user_ids);

                            // Create the user ids key and assign a list of user ids
                            $work_group["user_ids"] = $user_ids;

                            // Create a users key and assign the user result array
                            $work_group["users"] = $result_user_details;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["users"] = array();

                        }

                    }

                    // Streamline the  work group array by assigning the work group slug as
                    // the array key for use later on
                    $work_group_array[$work_group["work_group_slug"]] = $work_group;

                }

            } else {

                // Return error
                return array(

                    "status" => ERROR,

                    "message" => "Could not find work groups for the component specified"

                );

            }

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the component data using the provided parameters"

            );

        }

        // Return the result array
        return $work_group_array;

    }

    /**
     * Add a new component to a Work Group
     *
     * @param $component
     * @param int $work_group_id
     * @return array|NULL
     */

    public function workGroupAddComponent($component, $work_group_id = 0)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to insert data
        $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

        if (empty($component)) {
            // Compose the data array from the args
            $data = array(

                "work_group_id" => $this->acmeRequest->getPost("work_group_id"),

                "component_id" => $this->acmeRequest->getPost("component_id")

            );
        } else {
            // Compose the data array from request input vars
            $data = array(

                "work_group_id" => $work_group_id,

                "component_id" => $component["component_id"]

            );
        }

        // Check if we have valid ids for both
        if (!$data["work_group_id"] || $data["component_id"]) {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID / Component ID"

            );

        }

        // Add that data to the array
        $params["data"] = $data;

        // Create a new component
        $response = $this->legacyInsertData($params);

        // Return the response data array
        return $response;

    }

    /**
     * Create a new component for a specific Work Group
     *
     * @param $work_group
     * @param $component
     * @param $override
     * @return array
     */

    public function workGroupSetComponents($work_group, $component, $override = TRUE)
    {

        // Get Work Group data so that we can get the correct work_group_id
        $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        // Get the component data so that we can get the correct component_id
        $componentData = $this->componentGet($component);

        // get Work Group components
        $work_groupComponentsBefore = $this->workGroupGetComponents($work_groupData["work_group_id"]);

        // Will have to compose the data array for the insert operation
        $dataWorkGroupComponents = array();

        // The parameter to determine if the operation was successful
        $insertResult = false;

        // The parameter to identify the row affected
        $insertID = 0;

        // The parameter to identify the number row affected
        $affectedRows = 0;

        // Iterate the required component so that we get correct values for the data array
        foreach ($componentData as $_component) {

            $work_groupComponentObject = array();

            $work_groupComponentObject["component_id"] = $_component["component_id"];

            $work_groupComponentObject["work_group_id"] = $work_groupData["work_group_id"];

            $work_groupComponentObject["is_active"] = 1;

            $dataWorkGroupComponents[] = $work_groupComponentObject;

        }

        //Variable to hold the delete permission result
        $deleteWorkGroupComponentsResult = NULL;

        //If we are overriding, it means we are deleting whats already exists
        if ($override) {

            $deleteWorkGroupComponentsResult = $this->workGroupDeleteComponent($work_groupData, $work_groupComponentsBefore);

        }

        // Now update the data with the new values or rather insert data
        if (!empty($dataWorkGroupComponents)) {

            // Initialise the params array
            $params = array();

            // Set the table name to insert data
            $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

            //Set the data
            $params["data"] = $dataWorkGroupComponents;

            // Insert new Components
            $insertResult = $this->legacyInsertData($params);

            //Get the number of affected rows
            $affectedRows = $this->db->affected_rows();

            // If the insert data was successful, then get the insert id
            if ($insertResult) {

                $insertID = $this->db->insert_id();

            }

        }

        // get Work Group components
        $work_groupComponentsAfter = $this->workGroupGetComponents($work_groupData["work_group_id"]);

        // Compose the return array
        $resultArray = array(
            "status" => $insertResult ? SUCCESS : ERROR,
            "last_insert_id" => $insertID,
            "affected_rows" => $affectedRows,
            "dataWorkGroupComponents" => $dataWorkGroupComponents,
            "work_group_components_before" => $work_groupComponentsBefore,
            "work_group_components_after" => $work_groupComponentsAfter,
            "deleteWorkGroupComponentsResult" => $deleteWorkGroupComponentsResult
        );

        // Return the final result
        return $resultArray;

    }

    /**
     * Update Work Group Component
     *
     * @param $work_group_component_id
     * @param $component_id
     * @param $work_group_id
     * @param $new_component_id
     * @param $new_work_group_id
     * @return array
     */
    public function workGroupUpdateComponent($work_group_component_id, $component_id, $work_group_id, $new_component_id, $new_work_group_id)
    {

        // Initialise the params array
        $params = array();

        // Set the table name to update data
        $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

        $params["where"] = array();

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_component_id) {

            // Also make sure we have the component id passed
            if (filter_var($work_group_component_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_component_id"] = (int)$work_group_component_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied User Component ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid User Component ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($component_id) {

            // Also make sure we have the component id passed
            if (filter_var($component_id, FILTER_VALIDATE_INT)) {

                $params["where"]["component_id"] = (int)$component_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Component ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Component ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the component id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // Compose the data array from request input vars
            $params["data"] = array(

                "component_id" => $new_component_id,

                "work_group_id" => $new_work_group_id

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * @param array $work_group
     * @param array $component
     * @return array
     */

    public function workGroupDeleteComponent($work_group = array(), $component = array())
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Extract the work group ID
            $work_group_id = $work_group_data["work_group_id"];

        }

        $component_id = NULL;

        if (is_array($component)) {

            // Get the component so that we have the correct id
            $component_data = $this->componentGet($component);

        } else {

            $component_data = NULL;

        }

        // Initialise the params array
        $params = array();

        // Set the table name to delete data
        $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

        // Check to see if we have an id passed
        if ($work_group_id) {

            $params["where"] = array(

                "work_group_id" => $work_group_id

            );

            if (is_array($component_data)) {

                if (isset($component_data[0])) {

                    if (array_key_exists("component_id", $component_data[0])) {

                        $component_ids = array();

                        foreach ($component_data as $component_item) {

                            $component_ids[] = $component_item["component_id"];

                        }

                        $params["whereIn"][] = array(

                            "component_id" => $component_ids

                        );

                    }

                } else {

                    $component_ids = $this->componentGetIDs($component_data);

                    if (!empty($component_ids)) {

                        $params["whereIn"][] = array(

                            "component_id" => $component_ids

                        );

                    } else {

                        // Return an error
                        return array(

                            "status" => ERROR,

                            "message" => "Invalid Components"

                        );

                    }

                }

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Invalid components"

                );

            }

            // If not, then look for it in the array
        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        // Get the data
        $result = $this->legacyDeleteData($params);

        // Return the result
        return $result;

    }

    /**
     * @param array $work_group
     * @param array $component
     * @param null $status
     * @return array
     */
    public function workGroupComponentToggleStatus($work_group = array(), $component = array(), $status = NULL)
    {

        $work_group_id = NULL;

        if (is_array($work_group)) {

            // Get the work group so that we have the correct id
            $work_group_data = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

            // Depending on the result returned it can be a row array or a result array
            // If it's a result array
            if (isset($work_group_data[0])) {

                $work_group_id = $work_group_data[0]["work_group_id"];

            } else {

                // Otherwise it's a row array
                $work_group_id = $work_group_data["work_group_id"];

            }

        }

        $component_id = NULL;

        if (is_array($component)) {

            // Get the component so that we have the correct id
            $component_data = $this->componentGet($component, QUERY_RETURN_SINGLE);

            // Depending on the result returned, it can be a result array or a row array
            // if it's a result array
            if (isset($component_data[0])) {

                $component_id = $component_data[0]["component_id"];

            } else {

                // Otherwise its a row array
                $component_id = $component_data["component_id"];

            }

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($component_id) {

            // Also make sure we have the component id passed
            if (filter_var($component_id, FILTER_VALIDATE_INT)) {

                $params["where"]["component_id"] = (int)$component_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Component ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Component ID"

            );

        }

        // Check to see if we have an id passed, otherwise return an error
        if ($work_group_id) {

            // Also make sure we have the component id passed
            if (filter_var($work_group_id, FILTER_VALIDATE_INT)) {

                $params["where"]["work_group_id"] = (int)$work_group_id;

            } else {

                // Return an error
                return array(

                    "status" => ERROR,

                    "message" => "Supplied Work Group ID is not an integer"

                );

            }

        } else {

            // Return an error
            return array(

                "status" => ERROR,

                "message" => "Invalid Work Group ID"

            );

        }

        if (!empty($params["where"])) {

            // If the status is NULL,
            // Get the currrent status and do the opposite
            if ($status === NULL) {

                $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

                $params["returnType"] = QUERY_RETURN_SINGLE;

                $currentSettings = $this->legacyGetData($params);

                // Get the vaue of the current status
                if (is_array($currentSettings)) {

                    $currentStatus = (int)$currentSettings["is_active"];

                    if ($currentStatus) {

                        $currentStatus = 0;

                    } else {

                        $currentStatus = 1;

                    }

                } else {

                    // Return an error
                    return array(

                        "status" => ERROR,

                        "message" => "Could not determine the Work Group Component"

                    );

                }

            } else {

                $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

                $currentStatus = (int)$status;

            }

            // Compose the data array from request input vars
            $params["data"] = array(

                "is_active" => $currentStatus

            );

            // Return the result an an array
            $result = $this->legacyUpdateData($params);

        } else {

            // Return an error
            $result = array(

                "status" => ERROR,

                "message" => "Invalid where clause"

            );

        }

        return $result;

    }

    /**
     * Check to see if the Work Group has a specified component
     *
     * @param $work_group
     * @param $component
     * @return array
     */
    public function workGroupHasComponent($work_group, $component)
    {

        // Array to hold the component data row
        $componentData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($component)) {

            $componentData = $this->componentGet($component, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find the component data using the provided parameters"

            );

        }

        // Array to hold th work group data row
        $work_groupData = NULL;

        // All queries use ids in the where clause, if we do not have id, fetch it
        if (is_array($work_group)) {

            $work_groupData = $this->workGroupGet($work_group, QUERY_RETURN_SINGLE);

        } else {

            // Return error
            return array(

                "status" => ERROR,

                "message" => "Could not find work group data using the provided parameters"

            );

        }

        // If we have all the data, assess if we can get the component from the work group
        if ($componentData != NULL && $work_groupData != NULL) {

            // make sure all the ids are present
            if (array_key_exists("component_id", $componentData) && array_key_exists("work_group_id", $work_groupData)) {

                // Initialise the params array
                $params = array();

                // Set the table name to insert data
                $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

                // Return a single row with the Work Group component data
                $params["returnType"] = QUERY_RETURN_SINGLE;

                // Create the where clause by inserting the id
                $params["where"] = array(

                    "component_id" => $componentData["component_id"],

                    'work_group_id' => $work_groupData["work_group_id"]

                );

                // No check if a user type record id exist that matches the component id
                $result = $this->legacyGetData($params);

                // If the result is defined, then the component exists
                // Hence the work group has that component
                if ($result != NULL && is_array($result)) {

                    // Return data
                    return array(
                        "status" => SUCCESS,
                        "data" => $result
                    );

                } else {

                    // Return error
                    return array(
                        "status" => ERROR,
                        "message" => "The Component / Work Group combination does not exist"
                    );

                }

            } else {

                // Return error
                return array(
                    "status" => ERROR,
                    "message" => "Missing Component ID / Work Group ID in the result set"
                );

            }

        } else {

            // Return error
            return array(
                "status" => ERROR,
                "message" => "Could not find data with the specified input parameters"
            );

        }

    }


    /**
     * Get the meta data of a Work Group
     *
     * @param $work_group
     * @return array
     */
    public function workGroupGetMetadata($work_group)
    {
        //Check if the view is allowed for the work_group
        $work_groups = $this->workGroupGet($work_group);

        // Array to hold the meta data of the work group
        $work_groupMetadata = array();

        // Array to hold the meta data ids of the work group
        $work_groupMetadataIds = array();

        // Iterate the work group array so that we get the meta data for each work group
        foreach ($work_groups as $work_group) {

            // Populate the work group ids array with an work group id
            $work_groupMetadataIds[] = $work_group["work_group_id"];

            //Check if the view is allowed for the work_group
            $params = array();

            // Define the table
            $params["table"] = TABLE_WORK_GROUP_META;

            // Set the work group ID to use when extracting meta data items
            $params["where"] = array(

                "work_group_id" => $work_group["work_group_id"]

            );

            // Get meta data items
            $work_groupMetadataItem = $this->legacyGetData($params);

            // If we get the metadata, then create a new key on the
            // existing work group and assign that metadata
            $work_group["metadata"] = $work_groupMetadataItem;

            // Push the updated work group into the array
            $work_groupMetadata[] = $work_group;

        }

        // Return the work groups arrays with their metadata attached
        return $work_groupMetadata;

    }

    /**
     * Set a metadata for a specific Work Group
     *
     * @param $work_group
     * @param $key
     * @param $value
     * @return array
     */
    public function workGroupSetMetadata($work_group, $key, $value)
    {
        //Check if the view is allowed for the work_group
        $work_groups = $this->workGroupGet($work_group);

        // Array to hold the meta data of the work group
        $work_groupMetadata = array();

        // Array to hold the meta data ids of the work group
        $work_groupMetadataIds = array();

        // Iterate the work group array so that we get the meta data for each work group
        foreach ($work_groups as $work_group) {

            // Populate the work group ids array with an work group id
            $work_groupMetadataIds[] = $work_group["work_group_id"];

            //Check if the view is allowed for the work_group
            $params = array();

            // Define the table
            $params["table"] = TABLE_WORK_GROUP_META;

            // Set the work group ID to use when extracting meta data items
            $params["where"] = array(
                "work_group_id" => $work_group["work_group_id"],
                "meta_key" => $key
            );

            // Check if an item exists
            $itemExists = $this->legacyGetData($params);

            // If the returned data is empty
            // It means the antipated metadata was not available
            // Prepare to create a new row
            if (empty($itemExists)) {

                // Compose a dtata arraaa to hold the new row
                $params["data"] = array(
                    "work_group_id" => $work_group["work_group_id"],
                    "meta_key" => $key,
                    "meta_data" => urldecode($value),
                    "meta_created_on" => date("Y-m-d H:i:s"),
                    "meta_created_by" => $this->getUserID()
                );

                // Insert the new meta row into the database
                $work_groupMetadataItem = $this->legacyInsertData($params);

                // Get the insert data result
                $work_group["metadata"] = $work_groupMetadataItem;

                // Push he work group item to the metadata array holder
                $work_groupMetadata[] = $work_group;

            } else {


                // We got something, get ready to update existing data
                $work_groupIds = array();

                // If there was a previous key defined
                foreach ($itemExists as $work_groupItem) {

                    // Push a work group id that has been found into the work group ids array
                    $work_groupIds[] = $work_groupItem["work_group_id"];

                    // Assign the table name
                    $params["table"] = TABLE_WORK_GROUP_META;

                    // Define the where, explicitly using the primary columns fr better results
                    $params["where"] = array(
                        "work_group_meta_id" => $work_groupItem["work_group_meta_id"]
                    );

                    // Compose the dat array to update the data using the where clause
                    $params["data"] = array(
                        "work_group_id" => $work_group["work_group_id"],
                        "meta_key" => $key,
                        "meta_data" => urldecode($value),
                        "meta_updated_on" => date("Y-m-d H:i:s"),
                        "meta_updated_by" => $this->getUserID()
                    );

                    // Do update the data
                    $work_groupMetadata["updateResult"]["id-" . $work_group["work_group_id"]] = $this->legacyUpdateData($params);

                    // Get t see if there are any affected rows
                    $work_groupMetadata["affectedRows"]["id-" . $work_group["work_group_id"]] = $this->db->affected_rows();

                }

            }

        }

        // Push the ids the main array
        $work_groupMetadata["WorkGroupMetadataIds"] = $work_groupMetadataIds;

        // Return the result of the operation 
        return $work_groupMetadata;

    }

    /**
     * Delete a Work Group meta data of a specified meta key
     *
     * @param $work_group
     * @param $key
     * @return mixed
     */
    public function workGroupDeleteMetadata($work_group, $key)
    {
        //Check if the view is allowed for the work_group
        $work_groups = $this->workGroupGet($work_group);

        // Prepare an array to hold the work group IDs
        $work_groupMetadataIds = array();

        // Iterate the work group array and assess each work group
        // And see if we can delete it if it matches the where clause
        foreach ($work_groups as $work_group) {

            // Push the work group id into an array
            $work_groupMetadataIds[] = $work_group["work_group_id"];

            //Check if the view is allowed for the work_group
            $params = array();

            // Set the name of the table to delete the key from
            $params["table"] = TABLE_WORK_GROUP_META;

            // Build the where clause by associating a work group ID and a meta key
            $params["where"] = array(
                "work_group_id" => $work_group["work_group_id"],
                "meta_key" => $key
            );

            // Delete the row
            $work_groupMetadata["deleteResult"]["id-" . $work_group["work_group_id"]] = $this->legacyDeleteData($params);

            // Get the number of affected rows
            $work_groupMetadata["affectedRows"]["id-" . $work_group["work_group_id"]] = $this->db->affected_rows();

        }

        // Push the work group meta ids into an array
        $work_groupMetadata["WorkGroupMetadataIds"] = $work_groupMetadataIds;

        // Return the result of the operation
        return $work_groupMetadata;

    }

    /**
     * Get the specified Work Group
     *
     * @param $work_group
     * @param string $returnType
     * @return mixed
     */
    public function workGroupGet($work_group, $returnType = QUERY_RETURN_MULTIPLE)
    {
        //Check if the view is allowed for the work_group
        $params = array();

        // Set the ta ble name to get the data from
        $params["table"] = TABLE_WORK_GROUPS;

        // Set the return type for the data
        $params["returnType"] = $returnType;

        // Check if the work group passed is a data object
        if (is_array($work_group)) {

            // Prepare an array to hold the where clause
            $params["where"] = array();

            // Check if we are querying using the work group id
            if (array_key_exists("work_group_id", $work_group)) {
                // Use the work group ID in the where clause
                $params["where"]["work_group_id"] = $work_group["work_group_id"];

            }

            // Check if we are querying using the work group name
            if (array_key_exists("work_group_name", $work_group)) {
                // Use the work group name in the where clause
                $params["where"]["work_group_name"] = $work_group["work_group_name"];

            }

            // Check if we are querying using the work group slug
            if (array_key_exists("work_group_slug", $work_group)) {
                // Use the work group slug in the where clause
                $params["where"]["work_group_slug"] = $work_group["work_group_slug"];

            }

            // Check if we are querying using the account ID
            // In cases of a multi tenant setup (saas setup)
            if (array_key_exists("accountId", $work_group)) {
                // Use the account id in the where clause
                $params["where"]["accountId"] = $work_group["accountId"];

            }

        }

        // Retrieve the data and return the result
        return $this->legacyGetData($params);

    }

    /**
     * Get all Work groups in the system
     *
     * @return mixed
     */
    public function workGroupsGetAll()
    {
        // Prepare the array to use in fetching the data
        $params = array();

        // Set the table name
        $params["table"] = TABLE_WORK_GROUPS;

        // Just get all the data and return the result
        return $this->legacyGetData($params);

    }

    /**
     * Get the Work Group using its name, and perhaps the account id
     *
     * @param $work_group_name
     * @param int $accountId
     * @return mixed
     */
    public function workGroupsGetByName($work_group_name, $accountId = 0)
    {
        // Prepare the array to use in fetching the data
        $params = array();

        // Set the table name
        $params["table"] = TABLE_WORK_GROUPS;

        // Prepare the where clause so that
        // Data is retrieved by work group name
        $params["where"] = array(

            // Include the work group name in the where clause
            "work_group_name" => $work_group_name

        );

        // If the account id has been set, get data for that account
        if ($accountId) {

            // Include the account id so that only records
            // Of the particular account is retrieved
            $params["where"]["accountId"] = $accountId;

        }

        // Retrieve the data and return the result
        return $this->legacyGetData($params);

    }

    /**
     * Get the Work Group using its slug, and perhaps the account id
     *
     * @param $work_group_slug
     * @param int $accountId
     * @return mixed
     */
    public function workGroupsGetBySlug($work_group_slug, $accountId = 0)
    {
        // Prepare the array to use in fetching the data
        $params = array();

        // Set the table name
        $params["table"] = TABLE_WORK_GROUPS;

        // Prepare the where clause so that
        // Data is retrieved by work group slug
        $params["where"] = array(

            // Include the work group slug in the where clause
            "work_group_slug" => $work_group_slug

        );

        // If the account id has been set, get data for that account
        if ($accountId) {

            // Include the account id so that only records
            // Of the particular account is retrieved
            $params["where"]["accountId"] = $accountId;

        }

        // Retrieve the data and return the result
        return $this->legacyGetData($params);

    }

    /**
     * Get the Work Group using its id, and perhaps the account id
     *
     * @param $work_group_id
     * @param int $accountId
     * @return mixed
     */
    public function workGroupsGetByID($work_group_id, $accountId = 0)
    {
        // Prepare the array to use in fetching the data
        $params = array();

        // Set the table name
        $params["table"] = TABLE_WORK_GROUPS;

        // Prepare the where clause so that
        // Data is retrieved by work group id
        $params["where"] = array(

            // Include the work group id in the where clause
            "work_group_id" => $work_group_id

        );

        // If the account id has been set, get data for that account
        if ($accountId) {

            // Include the account id so that only records
            // Of the particular account is retrieved
            $params["where"]["accountId"] = $accountId;

        }

        // Retrieve the data and return the result
        return $this->legacyGetData($params);

    }

    /**
     * Get the Work Group using its id, and perhaps the account id
     *
     * @param int $accountId
     * @param int $departmentId
     * @return mixed
     */
    public function workGroupsGetByAccount($accountId = 0, $departmentId = 0)
    {
        // Prepare the array to use in fetching the data
        $params = array();

        // Set the table name
        $params["table"] = TABLE_WORK_GROUPS;

        // Prepare the where clause so that
        // Data is retrieved by account id
        $params["where"] = array(

            // Include the account id in the where clause
            "account_id" => $accountId

        );

        // If the department id has been set, get data for that department
        if ($departmentId) {

            // Include the account id so that only records
            // Of the particular account is retrieved
            $params["where"]["department_id"] = $departmentId;

        }

        // Retrieve the data and return the result
        return $this->legacyGetData($params);

    }

    /**
     * Get the work group by the department
     * If the account id is not set, we get it from the session
     *
     * @param $departmentId
     * @param int $accountId
     * @return mixed
     */
    public function workGroupsGetByDepartment($departmentId, $accountId = 0)
    {

        // Check if the id has been passed
        // If not, get it from the session
        if (!$accountId) {

            // Retrieve the account id from the session
            $accountId = $this->getAccountID();

        }

        // Fetch the data and return the results
        return $this->workGroupsGetByAccount($accountId, $departmentId);

    }

    /**
     * Get the user own specific work group
     * If the account id is not set, we get it from the session
     *
     * @param int $userId
     * @param int $accountId
     * @return mixed
     */
    public function workGroupGetSingleUser($userId, $accountId = 0)
    {
        // Prepare the array to use in fetching the data
        $params = array();

        // Set the table name
        $params["table"] = TABLE_WORK_GROUPS;

        // Check if the id has been passed
        // If not, get it from the session
        if (!$accountId) {

            // Retrieve the account id from the session
            $accountId = $this->getAccountID();

        }

        // Prepare the where clause so that
        // Data is retrieved by account id
        $params["where"] = array(

            // Include the account id in the where clause
            "account_id" => $accountId

        );

        // If the user id has not been set, get it from the session
        if ($userId) {

            $userId = $this->getUserID();

        }

        // Include the user id so that only records
        // Of the particular user is retrieved
        $params["where"]["single_user"] = $userId;

        // Retrieve the data and return the result
        return $this->legacyGetData($params);

    }

    /**
     * @return int
     */
    public function getUserAccount()
    {

        return 1;

    }

    /**
     * @return int
     */
    public function getCurrentUserAccount()
    {

        return 1;

    }

    /**
     * @return int
     */
    public function getUserDepartment()
    {

        return 1;

    }

    /**
     * @return int
     */
    public function getCurrentUserDepartment()
    {

        return 1;

    }

    /**
     * @return int
     */
    public function getAccountID()
    {

        return 1;

    }

    /**
     * @return int
     */
    public function getDepartmentID()
    {

        return 1;

    }

    /**
     * @return int
     */
    public function getUserID()
    {

        return 1;

    }

    /**
     * @return int
     */
    public function getCurrentUserID()
    {

        return 1;

    }

    /**
     * @param $getAllDetails
     * @return int|mixed
     */
    public function getCurrentUserData($getAllDetails)
    {

        $userId = $this->getUserID();

        if ($getAllDetails) {

            return $this->getUserDetails($userId);

        } else {

            return $this->getUserData($userId);

        }


    }

    /**
     * @return int|mixed
     */
    public function getCurrentUserSession()
    {

        return array();

    }

    /**
     * Get the user data by passing the username
     * 
     * @param $username
     * @param $includePassword
     * @return mixed
     */
    public function getUserDataByUsername($username, $includePassword = FALSE)
    {

        //Prepare the array to hold the querying data
        $params = array();

        // Set the table name to get the user data
        $params["table"] = TABLE_USERS;

        if (is_array($username)) {

            // Set the user id to determine the user we want
            $params["whereIn"][] = array(

                // Assign the user id in the where clause
                "au_username" => $username

            );

        } else {

            // Set the user id to determine the user we want
            $params["where"] = array(

                // Assign the user id in the where clause
                "au_username" => $username

            );

        }

        // Since its a single user, set the return type to a row array
        $params["returnType"] = QUERY_RETURN_SINGLE;

        // Now retrieve the user details
        $result_user_details = $this->legacyGetData($params);

        // For security reasons, the password is not included by default
        // Check if it's required
        if (!$includePassword) {

            // If it's not required, by default, just unset the key
            unset($result_user_details["au_password"]);

        }

        // Return the result
        return $result_user_details;

    }

    /**
     * Get the user data by passing the user id
     * 
     * @param $userId
     * @param $includePassword
     * @return mixed
     */
    public function getUserData($userId, $includePassword = FALSE)
    {

        //Prepare the array to hold the querying data
        $params = array();

        // Set the table name to get the user data
        $params["table"] = TABLE_USERS;

        if (is_array($userId)) {

            // Set the user id to determine the user we want
            $params["whereIn"][] = array(

                // Assign the user id in the where clause
                "user_id" => $userId

            );

        } else {

            // Set the user id to determine the user we want
            $params["where"] = array(

                // Assign the user id in the where clause
                "user_id" => $userId

            );

        }

        // Since its a single user, set the return type to a row array
        $params["returnType"] = QUERY_RETURN_SINGLE;

        // Now retrieve the user details
        $result_user_details = $this->legacyGetData($params);

        // For security reasons, the password is not included by default
        // Check if it's required
        if (!$includePassword) {

            // If it's not required, by default, just unset the key
            unset($result_user_details["au_password"]);

        }

        // Return the result
        return $result_user_details;

    }

    /**
     * Get the user full details
     * Details are extracted by passing an array containing
     * The details to retrieve
     *
     * @param $user
     * @param array $properties
     * @return mixed
     */
    public function getUserDetails($user, $properties = array())
    {

        $key = "user_id";

        $value = $user;

        if(filter_var($user, FILTER_VALIDATE_INT)){

            // Get the user details
            $result_user_details = $this->getUserData($user);

        } else {

            $key = "au_username";

            $value = $user;

            // Get the user details
            $result_user_details = $this->getUserDataByUsername($user);

        }
        
        // If we found a user now lets add other properties
        if ($result_user_details != NULL) {

            // If we require the user vcards
            if (in_array("vcards", $properties)) {

                // Prepare the array to hold the data that
                // we will user to query the vcards
                $params = array();

                // Set the table name
                $params["table"] = TABLE_USER_VCARDS;

                // Get all the columns
                $params["cols"] = "*";

                // Set the user id so that we get the correct vcards
                $params["where"] = array(
                    $key => $value
                );

                // Now retrieve the vcards
                $result_user_details["vcards"] = $this->legacyGetData($params);

            } else {

                // Otherwise ust return an empty array
                $result_user_details["vcards"] = array();

            }

            // If we require the user firebase
            if (in_array("firebase", $properties)) {

                // Prepare the array to hold the data that
                // we will user to query the firebase
                $params = array();

                // Set the table name
                $params["table"] = TABLE_USER_FIREBASE;

                // Get all the columns
                $params["cols"] = "*";

                // Return a row array since we are only interested in the single user
                $params["returnType"] = QUERY_RETURN_SINGLE;

                // Set the user id so that we get the correct firebase
                $params["where"] = array(
                    $key => $value
                );

                // Now retrieve the firebase
                $result_user_details["firebase"] = $this->legacyGetData($params);

            } else {

                // Otherwise ust return an empty array
                $result_user_details["firebase"] = array();

            }

            if (in_array("metadata", $properties)) {

                //Prepare the array to hold the querying data
                $params = array();

                // Set the table name to get the user meta data from
                $params["table"] = TABLE_USER_METADATA;

                // Just retrieve the 2 columns that are essential
                $params["cols"] = "option_name, option_value";

                // As always, just make sure we get the data, only for the user we want
                $params["where"] = array(
                    $key => $value
                );

                // Also make sure that we are only retrieving data that is activated
                $params["where"] = array(
                    "is_active" => 1
                );

                // Now retrieve the data from the database
                $result_user_metadata = $this->legacyGetData($params);

                // Optimise the user metadata for easy reading and assembly
                foreach ($result_user_metadata as $metadata) {

                    // It's a good idea to have a key set for easy retrieval
                    $result_user_details["metadata"][$metadata["option_name"]] = $metadata["option_value"];

                }

            } else {

                // Otherwise just return an empty array for the metadata
                $result_user_details["metadata"] = array();

            }

            // Check if we require the user activity log
            if (in_array("activity_log", $properties)) {

                // Prepare an array to hold the data for querying
                $params = array();

                // Set the table name to retrieve the user activities from
                $params["table"] = TABLE_USER_ACTIVITY_LOG;

                // Select all the columns
                $params["cols"] = "*";

                // As usual, make sure we set the user id to associate with
                $params["where"] = array(
                    $key => $value
                );

                // Now get the data from the database
                $result_user_details["activity_log"] = $this->legacyGetData($params);

            } else {

                // Otherwise just return an empty array
                $result_user_details["activity_log"] = array();

            }

            // Check if we require the user authentication log
            if (in_array("authentication_log", $properties)) {

                //Check if the view is allowed for the work_group
                $params = array();

                // Set the table name to retrieve the user auth history log
                $params["table"] = TABLE_USER_AUTHENTICATION_LOG;

                // Get all the database columns
                $params["cols"] = "*";

                // Set the where clause so that we associate the data with the right user
                $params["where"] = array(
                    $key => $value
                );

                // Now retrieve the data
                $result_user_details["auth_log"] = $this->legacyGetData($params);

            } else {

                // Otherwise just return an empty array
                $result_user_details["auth_log"] = array();

            }

            //Check if the view is allowed for the work_group
            $params = array();

            // Set the table name
            $params["table"] = TABLE_WORK_GROUP_USERS;

            // Initially we are interested in the work group id only
            $params["cols"] = "work_group_id";

            // Associate a user id and a work group id via the where clause
            $params["where"] = array(
                $key => $value
            );

            // Now get the data from the database
            $result_work_group_ids = $this->legacyGetData($params);

            // Prepare to collect a list of work group ids for that particular user
            $work_group_ids = array();

            foreach ($result_work_group_ids as $work_group) {

                // Push a work group id to an array for later use
                $work_group_ids[] = $work_group["work_group_id"];

            }

            var_dump($work_group_ids);

            // Since we have the work group ids, are they required
            // If so, let's retrieve those ids
            if (in_array("work_group_ids", $properties)) {

                // Push the work group id into an array
                $result_user_details["work_group_ids"] = $work_group_ids;

            } else {

                // Other wise just return an empty array
                $result_user_details["work_group_ids"] = array();

            }

            // Check to see if the user work groups and their properties are requires
            if (array_key_exists("work_groups", $properties)) {

                // Check if the view is allowed for the work_group
                $params = array();

                // Set the table name to retrieve the work groups from
                $params["table"] = TABLE_WORK_GROUPS;

                // We need an array of work groups to parse
                // Let's use our the previous work group ids
                // to get the actual work groups
                $params["whereIn"][] = array(

                    // To achieve this we build a where clause using where-in
                    "work_group_id" => $work_group_ids

                );

                var_dump($params);

                // Now get the data from the database
                $result_work_group_details = $this->legacyGetData($params);

                // Prepare an array to hold the full work group data
                $work_group_rows = array();

                // Iterate the data to parse each work  group
                foreach ($result_work_group_details as $work_group) {

                    // We now have the full details of the work group
                    // What shall we do with thi work group, several items can be retrieved
                    // - Metadata
                    // - Roles
                    // - Permissions
                    // - Views
                    // - Modules
                    // - Components

                    // is the work group metadata required in the request?
                    if (in_array("metadata", $properties["work_groups"])) {

                        // Prepare an array to hold the data to query
                        $params = array();

                        // Set the table name of the metadata to retrieve
                        $params["table"] = TABLE_WORK_GROUP_META;

                        // Compose a where clause where we specify the work group id
                        // So that wwe retrieve all the metadata associated with that id
                        $params["where"] = array(
                            // Assign the work group id in the where clause
                            "work_group_id" => $work_group["work_group_id"]
                        );

                        // Now get the metadata from the database
                        $result_work_group_metadata = $this->legacyGetData($params);

                        // Prepare an array to hold the metadata
                        $work_group_metadata_rows = array();

                        // Iterate the result to get the metadata
                        foreach ($result_work_group_metadata as $metadata) {

                            // Streamline the array by creating keys using the slug
                            // This will make it much easier to retrieve the data later
                            $work_group_metadata_rows[$metadata["meta_key"]] = $metadata["meta_data"];

                        }

                        // Now create a new key on the work group
                        // This is going to hold the metadata in question
                        $work_group["metadata"] = $work_group_metadata_rows;

                    } else {

                        // Otherwise just return an empty array
                        $work_group["metadata"] = array();

                    }

                    // If the work group roles are required in the request
                    if (in_array("roles", $properties["work_groups"])) {

                        // Prepare an array to hold the querying data
                        $params = array();

                        // Set the name of the table to retrieve the work group role id
                        $params["table"] = TABLE_WORK_GROUP_ROLES;

                        // We are only interested in the role id
                        $params["cols"] = "role_id";

                        // So we just get the role id for the specified work group id
                        $params["where"] = array(

                            // Assign the work group id
                            "work_group_id" => $work_group["work_group_id"]
                        );

                        // Get the role ids
                        $work_group_roles = $this->legacyGetData($params);

                        // Prepare an array to store just the role ids
                        $work_group_role_ids = array();

                        // Iterate the work group roles so that we parse each role
                        foreach ($work_group_roles as $role) {

                            // Extract the role id from the row array
                            $work_group_role_ids[] = $role["role_id"];

                        }

                        // if we have at least one work group role
                        if (count($work_group_role_ids) > 0) {

                            // Prepare an array to hold the data for the query
                            $params = array();

                            // Set the table to get the full details of the role
                            $params["table"] = TABLE_ROLES;

                            // Select the columns we are interested in
                            $params["cols"] = "role_id, role_slug, role_name";

                            // Only get the roles that are active
                            $params["where"] = array(

                                // lookup the last column where is_active = 1
                                "is_active" => 1
                            );

                            // Make sure we have at least one element (role id) in the array
                            $params["whereIn"][] = array(

                                // Since the array may contain more than one element,
                                // It's ideal to use where-in clause
                                "role_id" => $work_group_role_ids
                            );

                            // Now get the data using the provided parameters
                            $work_group_roles = $this->legacyGetData($params);

                            // Prepare an array to hold the work group roles
                            $work_group_role_array = array();

                            // Iterate the work group roles result array
                            // so that we parse each role
                            foreach ($work_group_roles as $role) {

                                // Streamline the roles by creating a key using the role slug
                                // so that it will be easier to use the data
                                $work_group_role_array[$role["role_slug"]] = $role;

                            }

                            // Then bundle all the roles into an array and
                            // create a new key, 'roles' and assign the roles array
                            $work_group["roles"] = $work_group_role_array;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["roles"] = array();

                        }

                    }

                    // If the work group views are required in the request
                    if (in_array("views", $properties["work_groups"])) {

                        // Prepare an array to hold the querying data
                        $params = array();

                        // Set the name of the table to retrieve the work group view id
                        $params["table"] = TABLE_WORK_GROUP_VIEWS;

                        // We are only interested in the view id
                        $params["cols"] = "view_id";

                        // So we just get the view id for the specified work group id
                        $params["where"] = array(

                            // Assign the work group id
                            "work_group_id" => $work_group["work_group_id"]
                        );

                        // Get the view ids
                        $work_group_views = $this->legacyGetData($params);

                        // Prepare an array to store just the view ids
                        $work_group_view_ids = array();

                        // Iterate the work group views so that we parse each view
                        foreach ($work_group_views as $view) {

                            // Extract the view id from the row array
                            $work_group_view_ids[] = $view["view_id"];

                        }

                        // if we have at least one work group view
                        if (count($work_group_view_ids) > 0) {

                            // Prepare an array to hold the data for the query
                            $params = array();

                            // Set the table to get the full details of the view
                            $params["table"] = TABLE_VIEWS;

                            // Select the columns we are interested in
                            $params["cols"] = "view_id, view_slug, view_name";

                            // Only get the views that are active
                            $params["where"] = array(

                                // lookup the last column where is_active = 1
                                "is_active" => 1
                            );

                            // Make sure we have at least one element (view id) in the array
                            $params["whereIn"][] = array(

                                // Since the array may contain more than one element,
                                // It's ideal to use where-in clause
                                "view_id" => $work_group_view_ids
                            );

                            // Now get the data using the provided parameters
                            $work_group_views = $this->legacyGetData($params);

                            // Prepare an array to hold the work group views
                            $work_group_view_array = array();

                            // Iterate the work group views result array
                            // so that we parse each view
                            foreach ($work_group_views as $view) {

                                // Streamline the views by creating a key using the view slug
                                // so that it will be easier to use the data
                                $work_group_view_array[$view["view_slug"]] = $view;

                            }

                            // Then bundle all the views into an array and
                            // create a new key, 'views' and assign the views array
                            $work_group["views"] = $work_group_view_array;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["views"] = array();

                        }

                    }

                    // If the work group permissions are required in the request
                    if (in_array("permissions", $properties["work_groups"])) {

                        // Prepare an array to hold the querying data
                        $params = array();

                        // Set the name of the table to retrieve the work group permission id
                        $params["table"] = TABLE_WORK_GROUP_PERMISSIONS;

                        // We are only interested in the permission id
                        $params["cols"] = "permission_id";

                        // So we just get the permission id for the specified work group id
                        $params["where"] = array(

                            // Assign the work group id
                            "work_group_id" => $work_group["work_group_id"]
                        );

                        // Get the permission ids
                        $work_group_permissions = $this->legacyGetData($params);

                        // Prepare an array to store just the permission ids
                        $work_group_permission_ids = array();

                        // Iterate the work group permissions so that we parse each permission
                        foreach ($work_group_permissions as $permission) {

                            // Extract the permission id from the row array
                            $work_group_permission_ids[] = $permission["permission_id"];

                        }

                        // if we have at least one work group permission
                        if (count($work_group_permission_ids) > 0) {

                            // Prepare an array to hold the data for the query
                            $params = array();

                            // Set the table to get the full details of the permission
                            $params["table"] = TABLE_PERMISSIONS;

                            // Select the columns we are interested in
                            $params["cols"] = "permission_id, permission_slug, permission_name";

                            // Only get the permissions that are active
                            $params["where"] = array(

                                // lookup the last column where is_active = 1
                                "is_active" => 1
                            );

                            // Make sure we have at least one element (permission id) in the array
                            $params["whereIn"][] = array(

                                // Since the array may contain more than one element,
                                // It's ideal to use where-in clause
                                "permission_id" => $work_group_permission_ids
                            );

                            // Now get the data using the provided parameters
                            $work_group_permissions = $this->legacyGetData($params);

                            // Prepare an array to hold the work group permissions
                            $work_group_permission_array = array();

                            // Iterate the work group permissions result array
                            // so that we parse each permission
                            foreach ($work_group_permissions as $permission) {

                                // Streamline the permissions by creating a key using the permission slug
                                // so that it will be easier to use the data
                                $work_group_permission_array[$permission["permission_slug"]] = $permission;

                            }

                            // Then bundle all the permissions into an array and
                            // create a new key, 'permissions' and assign the permissions array
                            $work_group["permissions"] = $work_group_permission_array;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["permissions"] = array();

                        }

                    }

                    // If the work group modules are required in the request
                    if (in_array("modules", $properties["work_groups"])) {

                        // Prepare an array to hold the querying data
                        $params = array();

                        // Set the name of the table to retrieve the work group module id
                        $params["table"] = TABLE_WORK_GROUP_MODULES;

                        // We are only interested in the module id
                        $params["cols"] = "module_id";

                        // So we just get the module id for the specified work group id
                        $params["where"] = array(

                            // Assign the work group id
                            "work_group_id" => $work_group["work_group_id"]
                        );

                        // Get the module ids
                        $work_group_modules = $this->legacyGetData($params);

                        // Prepare an array to store just the module ids
                        $work_group_module_ids = array();

                        // Iterate the work group modules so that we parse each module
                        foreach ($work_group_modules as $module) {

                            // Extract the module id from the row array
                            $work_group_module_ids[] = $module["module_id"];

                        }

                        // if we have at least one work group module
                        if (count($work_group_module_ids) > 0) {

                            // Prepare an array to hold the data for the query
                            $params = array();

                            // Set the table to get the full details of the module
                            $params["table"] = TABLE_MODULES;

                            // Select the columns we are interested in
                            $params["cols"] = "module_id, module_slug, module_name";

                            // Only get the modules that are active
                            $params["where"] = array(

                                // lookup the last column where is_active = 1
                                "is_active" => 1
                            );

                            // Make sure we have at least one element (module id) in the array
                            $params["whereIn"][] = array(

                                // Since the array may contain more than one element,
                                // It's ideal to use where-in clause
                                "module_id" => $work_group_module_ids
                            );

                            // Now get the data using the provided parameters
                            $work_group_modules = $this->legacyGetData($params);

                            // Prepare an array to hold the work group modules
                            $work_group_module_array = array();

                            // Iterate the work group modules result array
                            // so that we parse each module
                            foreach ($work_group_modules as $module) {

                                // Streamline the modules by creating a key using the module slug
                                // so that it will be easier to use the data
                                $work_group_module_array[$module["module_slug"]] = $module;

                            }

                            // Then bundle all the modules into an array and
                            // create a new key, 'modules' and assign the modules array
                            $work_group["modules"] = $work_group_module_array;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["modules"] = array();

                        }

                    }

                    // If the work group components are required in the request
                    if (in_array("components", $properties["work_groups"])) {

                        // Prepare an array to hold the querying data
                        $params = array();

                        // Set the name of the table to retrieve the work group component id
                        $params["table"] = TABLE_WORK_GROUP_COMPONENTS;

                        // We are only interested in the component id
                        $params["cols"] = "component_id";

                        // So we just get the component id for the specified work group id
                        $params["where"] = array(

                            // Assign the work group id
                            "work_group_id" => $work_group["work_group_id"]
                        );

                        // Get the component ids
                        $work_group_components = $this->legacyGetData($params);

                        // Prepare an array to store just the component ids
                        $work_group_component_ids = array();

                        // Iterate the work group components so that we parse each component
                        foreach ($work_group_components as $component) {

                            // Extract the component id from the row array
                            $work_group_component_ids[] = $component["component_id"];

                        }

                        // if we have at least one work group component
                        if (count($work_group_component_ids) > 0) {

                            // Prepare an array to hold the data for the query
                            $params = array();

                            // Set the table to get the full details of the component
                            $params["table"] = TABLE_COMPONENTS;

                            // Select the columns we are interested in
                            $params["cols"] = "component_id, component_slug, component_name";

                            // Only get the components that are active
                            $params["where"] = array(

                                // lookup the last column where is_active = 1
                                "is_active" => 1
                            );

                            // Make sure we have at least one element (component id) in the array
                            $params["whereIn"][] = array(

                                // Since the array may contain more than one element,
                                // It's ideal to use where-in clause
                                "component_id" => $work_group_component_ids
                            );

                            // Now get the data using the provided parameters
                            $work_group_components = $this->legacyGetData($params);

                            // Prepare an array to hold the work group components
                            $work_group_component_array = array();

                            // Iterate the work group components result array
                            // so that we parse each component
                            foreach ($work_group_components as $component) {

                                // Streamline the components by creating a key using the component slug
                                // so that it will be easier to use the data
                                $work_group_component_array[$component["component_slug"]] = $component;

                            }

                            // Then bundle all the components into an array and
                            // create a new key, 'components' and assign the components array
                            $work_group["components"] = $work_group_component_array;

                        } else {

                            // Otherwise just return an empty array
                            $work_group["components"] = array();

                        }

                    }

                    // Now that we have all the data we want for a specific work group
                    // Streamline the array by assigning the work group slug for later use
                    $work_group_rows[$work_group["work_group_slug"]] = $work_group;

                }

                $result_user_details["work_groups"] = $work_group_rows;

            } else {

                $result_user_details["work_groups"] = array();

            }

        }

        return $result_user_details;

    }

}