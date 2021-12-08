<?php

/**
 * Controller Class Users
 *
 * @author      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Auth
 * @subpackage  Users
 */

namespace Acme\Core\System\Modules\Auth\Users\Controllers;

use \CodeIgniter\HTTP;
use Acme\Core\System\Modules\Auth\Users\Models\RolesModel;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class Roles extends \Acme\Core\System\Modules\Auth\Users\Controllers\RolesBaseController
{

    /**
     * @var $roles_model
     */
    public $roles_model;
    
    /**
     * Constructor.
     *
     * @param \CodeIgniter\HTTP\RequestInterface $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        // Initialise the parent controller
        parent::initController(\Config\Services::request(), \Config\Services::response(), \Config\Services::logger());

        $this->init($request);

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function __construct( $request, $response, $logger )
    {

        $this->roles_model = new RolesModel( $request );

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function index()
    {

        if($this->auth->loggedin()){

            //Parse the $user_data to get the user group landing page
            $this->launch();

        }else{

            //Initialise the array to hold the variables
            $this->ACMEDisplayForm();

        }

    }


    //*****************
    // ROLES
    //*****************

    /**
     * Create a new role by just passing the role name
     *
     * @param $name
     */
    public function role_create_new($name)
    {
        $name = urldecode($name);
        $data = array(
            "role_name" => $name,
            "role_slug" => acme_to_slug($name)
        );

        //Create a new role
        $result = $this->roles_model->roleCreateNew($data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Update the specified role
     * Pass the key value pair to query the existing role
     * Pass the new name for that role
     *
     * @param $key
     * @param $value
     * @param $name
     */
    public function role_update($key, $value, $name)
    {
        $name = urldecode($name);

        $data = array(
            "role_name" => $name,
            "role_slug" => acme_to_slug($name)
        );

        //Create a new role
        $result = $this->roles_model->roleUpdate(array($key => $value), $data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Delete the specified role
     *
     * @param $key
     * @param $value
     */
    public function role_delete($key, $value)
    {

        //Create a new role
        $result = $this->roles_model->roleDelete(array($key => $value));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific role by passing the id
     *
     * @param int $id
     */
    public function roles_get($id = 0)
    {
        // Get a role by id
        $result = $this->role_get_by_id($id);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific role by passing the slug
     *
     * @param $slug
     */
    public function role_get_by_slug($slug)
    {
        // Get a role by slug
        $result = $this->roles_model->roleGet(array("role_slug" => $slug));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific role by passing the name
     *
     * @param $name
     */
    public function role_get_by_name($name)
    {
        // Get a role by name
        $result = $this->roles_model->roleGet(array("role_name" => urldecode($name)));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific role by passing the id
     *
     * @param $id
     */
    public function role_get_by_id($id)
    {
        // Get a role by id
        $result = $this->roles_model->roleGet(array("role_id" => $id));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get all roles in the system
     */
    public function roles_get_all()
    {
        // Get all the roles in the system
        $result = $this->roles_model->roleGet(0);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get roles for the specified Work Group
     *
     * @param $key
     * @param $value
     */
    public function work_group_get_roles($key, $value)
    {

        //Create a new role
        $result = $this->roles_model->workGroupGetRoles(
            array(
                $key => $value
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * @param $key
     * @param $values
     * @param $includeUsers
     */
    public function work_group_get_by_roles(
        $key,
        $values,
        $includeUsers=FALSE
    )
    {

        //Create a new role
        $result = $this->roles_model->workGroupGetByRoles(
            array($key=>$values),
            $includeUsers
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group roles
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $role_key
     * @param $role_value
     */
    public function work_group_add_roles(
        $work_group_key,
        $work_group_value,
        $role_key, $role_value)
    {

        $role = array();

        $work_group = array();

        $role[$role_key] = work_group(SINGLE_PIPE, url_decode($role_value));

        $work_group[$work_group_key] = $work_group_value;

        //Create a new role
        $result = $this->roles_model->workGroupSetRoles($work_group, $role, FALSE);

        //Render the json object as output
        var_dump($result);


    }

    /**
     * Update Work Group roles
     *
     * @param $work_group_role_id
     * @param $role_id
     * @param $work_group_id
     * @param $new_role_id
     * @param $new_work_group_id
     */
    public function work_group_update_role(
        $work_group_role_id,
        $role_id,
        $work_group_id,
        $new_role_id,
        $new_work_group_id
    )
    {

        //Create a new role
        $result = $this->roles_model->workGroupUpdateRole(
            $work_group_role_id,
            $role_id,
            $work_group_id,
            $new_role_id,
            $new_work_group_id
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group roles
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $role_key
     * @param $role_value
     * @param bool $override
     */
    public function work_group_set_roles(
        $work_group_key,
        $work_group_value,
        $role_key,
        $role_value,
        $override=TRUE
    )
    {

        // Prepare a role array to hold the where clause data
        $role = array();

        // Prepare a work group array to hold the where clause data
        $work_group = array();

        // Create a role key for use in the query
        $role[$role_key] = explode(SINGLE_DASH, $role_value);

        // Create a work group key for use in the query
        $work_group[$work_group_key] = $work_group_value;

        //Create a new role
        $result = $this->roles_model->workGroupSetRoles($work_group, $role, $override);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Remove the specified role from a Work Group
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $role_key
     * @param $role_value
     */
    public function work_group_delete_role(
        $work_group_key,
        $work_group_value,
        $role_key,
        $role_value
    )
    {

        //Create a new role
        $result = $this->roles_model->workGroupDeleteRole(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $role_key => explode(SINGLE_DASH, $role_value)
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Toggle a Work Group role status 'active/inactive'
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $role_key
     * @param $role_value
     * @param $status
     */
    public function work_group_role_toggle_status(
        $work_group_key,
        $work_group_value,
        $role_key,
        $role_value,
        $status=NULL
    )
    {

        //Create a new role
        $result = $this->roles_model->workGroupRoleToggleStatus(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $role_key => explode(SINGLE_DASH, $role_value)
            ),
            $status
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Check to see if a Work Group has a specific Role
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $role_key
     * @param $role_value
     */
    public function work_group_has_role(
        $work_group_key,
        $work_group_value,
        $role_key,
        $role_value
    )
    {

        $role = array();

        $work_group = array();

        $role[$role_key] = $role_value;

        $work_group[$work_group_key] = $work_group_value;

        //Create a new role
        $result = $this->roles_model->workGroupHasRole($work_group, $role);

        //Render the json object as output
        var_dump($result);

    }

//*****************
// VIEWS
//*****************

    /**
     * Create a new view by just passing the view name
     *
     * @param $name
     */
    public function view_create_new($name)
    {
        $name = urldecode($name);
        $data = array(
            "view_name" => $name,
            "view_slug" => acme_to_slug($name)
        );

        //Create a new view
        $result = $this->roles_model->viewCreateNew($data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Update the specified view
     * Pass the key value pair to query the existing view
     * Pass the new name for that view
     *
     * @param $key
     * @param $value
     * @param $name
     */
    public function view_update($key, $value, $name)
    {
        $name = urldecode($name);

        $data = array(
            "view_name" => $name,
            "view_slug" => acme_to_slug($name)
        );

        //Create a new view
        $result = $this->roles_model->viewUpdate(array($key => $value), $data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Delete the specified view
     *
     * @param $key
     * @param $value
     */
    public function view_delete($key, $value)
    {

        //Create a new view
        $result = $this->roles_model->viewDelete(array($key => $value));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific view by passing the id
     *
     * @param int $id
     */
    public function views_get($id = 0)
    {
        // Get a view by id
        $result = $this->view_get_by_id($id);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific view by passing the slug
     *
     * @param $slug
     */
    public function view_get_by_slug($slug)
    {
        // Get a view by slug
        $result = $this->roles_model->viewGet(array("view_slug" => $slug));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific view by passing the name
     *
     * @param $name
     */
    public function view_get_by_name($name)
    {
        // Get a view by name
        $result = $this->roles_model->viewGet(array("view_name" => urldecode($name)));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific view by passing the id
     *
     * @param $id
     */
    public function view_get_by_id($id)
    {
        // Get a view by id
        $result = $this->roles_model->viewGet(array("view_id" => $id));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get all views in the system
     */
    public function views_get_all()
    {
        // Get all the views in the system
        $result = $this->roles_model->viewGet(0);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get views for the specified Work Group
     *
     * @param $key
     * @param $value
     */
    public function work_group_get_views($key, $value)
    {

        //Create a new view
        $result = $this->roles_model->workGroupGetViews(
            array(
                $key => $value
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * @param $key
     * @param $values
     * @param $includeUsers
     */
    public function work_group_get_by_views(
        $key,
        $values,
        $includeUsers=FALSE
    )
    {

        //Create a new view
        $result = $this->roles_model->workGroupGetByViews(
            array($key=>$values),
            $includeUsers
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group views
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $view_key
     * @param $view_value
     */
    public function work_group_add_views(
        $work_group_key,
        $work_group_value,
        $view_key, $view_value)
    {

        $view = array();

        $work_group = array();

        $view[$view_key] = work_group(SINGLE_PIPE, url_decode($view_value));

        $work_group[$work_group_key] = $work_group_value;

        //Create a new view
        $result = $this->roles_model->workGroupSetViews($work_group, $view, FALSE);

        //Render the json object as output
        var_dump($result);


    }

    /**
     * Update Work Group views
     *
     * @param $work_group_view_id
     * @param $view_id
     * @param $work_group_id
     * @param $new_view_id
     * @param $new_work_group_id
     */
    public function work_group_update_view(
        $work_group_view_id,
        $view_id,
        $work_group_id,
        $new_view_id,
        $new_work_group_id
    )
    {

        //Create a new view
        $result = $this->roles_model->workGroupUpdateView(
            $work_group_view_id,
            $view_id,
            $work_group_id,
            $new_view_id,
            $new_work_group_id
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group views
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $view_key
     * @param $view_value
     * @param bool $override
     */
    public function work_group_set_views(
        $work_group_key,
        $work_group_value,
        $view_key,
        $view_value,
        $override=TRUE
    )
    {

        // Prepare a view array to hold the where clause data
        $view = array();

        // Prepare a work group array to hold the where clause data
        $work_group = array();

        // Create a view key for use in the query
        $view[$view_key] = explode(SINGLE_DASH, $view_value);

        // Create a work group key for use in the query
        $work_group[$work_group_key] = $work_group_value;

        //Create a new view
        $result = $this->roles_model->workGroupSetViews($work_group, $view, $override);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Remove the specified view from a Work Group
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $view_key
     * @param $view_value
     */
    public function work_group_delete_view(
        $work_group_key,
        $work_group_value,
        $view_key,
        $view_value
    )
    {

        //Create a new view
        $result = $this->roles_model->workGroupDeleteView(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $view_key => explode(SINGLE_DASH, $view_value)
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Toggle a Work Group view status 'active/inactive'
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $view_key
     * @param $view_value
     * @param $status
     */
    public function work_group_view_toggle_status(
        $work_group_key,
        $work_group_value,
        $view_key,
        $view_value,
        $status=NULL
    )
    {

        //Create a new view
        $result = $this->roles_model->workGroupViewToggleStatus(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $view_key => explode(SINGLE_DASH, $view_value)
            ),
            $status
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Check to see if a Work Group has a specific View
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $view_key
     * @param $view_value
     */
    public function work_group_has_view(
        $work_group_key,
        $work_group_value,
        $view_key,
        $view_value
    )
    {

        $view = array();

        $work_group = array();

        $view[$view_key] = $view_value;

        $work_group[$work_group_key] = $work_group_value;

        //Create a new view
        $result = $this->roles_model->workGroupHasView($work_group, $view);

        //Render the json object as output
        var_dump($result);

    }

//*****************
// PERMISSIONS
//*****************

    /**
     * Create a new permission by just passing the permission name
     *
     * @param $name
     */
    public function permission_create_new($name)
    {
        $name = urldecode($name);
        $data = array(
            "permission_name" => $name,
            "permission_slug" => acme_to_slug($name)
        );

        //Create a new permission
        $result = $this->roles_model->permissionCreateNew($data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Update the specified permission
     * Pass the key value pair to query the existing permission
     * Pass the new name for that permission
     *
     * @param $key
     * @param $value
     * @param $name
     */
    public function permission_update($key, $value, $name)
    {
        $name = urldecode($name);

        $data = array(
            "permission_name" => $name,
            "permission_slug" => acme_to_slug($name)
        );

        //Create a new permission
        $result = $this->roles_model->permissionUpdate(array($key => $value), $data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Delete the specified permission
     *
     * @param $key
     * @param $value
     */
    public function permission_delete($key, $value)
    {

        //Create a new permission
        $result = $this->roles_model->permissionDelete(array($key => $value));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific permission by passing the id
     *
     * @param int $id
     */
    public function permissions_get($id = 0)
    {
        // Get a permission by id
        $result = $this->permission_get_by_id($id);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific permission by passing the slug
     *
     * @param $slug
     */
    public function permission_get_by_slug($slug)
    {
        // Get a permission by slug
        $result = $this->roles_model->permissionGet(array("permission_slug" => $slug));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific permission by passing the name
     *
     * @param $name
     */
    public function permission_get_by_name($name)
    {
        // Get a permission by name
        $result = $this->roles_model->permissionGet(array("permission_name" => urldecode($name)));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific permission by passing the id
     *
     * @param $id
     */
    public function permission_get_by_id($id)
    {
        // Get a permission by id
        $result = $this->roles_model->permissionGet(array("permission_id" => $id));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get all permissions in the system
     */
    public function permissions_get_all()
    {
        // Get all the permissions in the system
        $result = $this->roles_model->permissionGet(0);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get permissions for the specified Work Group
     *
     * @param $key
     * @param $value
     */
    public function work_group_get_permissions($key, $value)
    {

        //Create a new permission
        $result = $this->roles_model->workGroupGetPermissions(
            array(
                $key => $value
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * @param $key
     * @param $values
     * @param $includeUsers
     */
    public function work_group_get_by_permissions(
        $key,
        $values,
        $includeUsers=FALSE
    )
    {

        //Create a new permission
        $result = $this->roles_model->workGroupGetByPermissions(
            array($key=>$values),
            $includeUsers
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group permissions
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $permission_key
     * @param $permission_value
     */
    public function work_group_add_permissions(
        $work_group_key,
        $work_group_value,
        $permission_key, $permission_value)
    {

        $permission = array();

        $work_group = array();

        $permission[$permission_key] = work_group(SINGLE_PIPE, url_decode($permission_value));

        $work_group[$work_group_key] = $work_group_value;

        //Create a new permission
        $result = $this->roles_model->workGroupSetPermissions($work_group, $permission, FALSE);

        //Render the json object as output
        var_dump($result);


    }

    /**
     * Update Work Group permissions
     *
     * @param $work_group_permission_id
     * @param $permission_id
     * @param $work_group_id
     * @param $new_permission_id
     * @param $new_work_group_id
     */
    public function work_group_update_permission(
        $work_group_permission_id,
        $permission_id,
        $work_group_id,
        $new_permission_id,
        $new_work_group_id
    )
    {

        //Create a new permission
        $result = $this->roles_model->workGroupUpdatePermission(
            $work_group_permission_id,
            $permission_id,
            $work_group_id,
            $new_permission_id,
            $new_work_group_id
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group permissions
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $permission_key
     * @param $permission_value
     * @param bool $override
     */
    public function work_group_set_permissions(
        $work_group_key,
        $work_group_value,
        $permission_key,
        $permission_value,
        $override=TRUE
    )
    {

        // Prepare a permission array to hold the where clause data
        $permission = array();

        // Prepare a work group array to hold the where clause data
        $work_group = array();

        // Create a permission key for use in the query
        $permission[$permission_key] = explode(SINGLE_DASH, $permission_value);

        // Create a work group key for use in the query
        $work_group[$work_group_key] = $work_group_value;

        //Create a new permission
        $result = $this->roles_model->workGroupSetPermissions($work_group, $permission, $override);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Remove the specified permission from a Work Group
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $permission_key
     * @param $permission_value
     */
    public function work_group_delete_permission(
        $work_group_key,
        $work_group_value,
        $permission_key,
        $permission_value
    )
    {

        //Create a new permission
        $result = $this->roles_model->workGroupDeletePermission(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $permission_key => explode(SINGLE_DASH, $permission_value)
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Toggle a Work Group permission status 'active/inactive'
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $permission_key
     * @param $permission_value
     * @param $status
     */
    public function work_group_permission_toggle_status(
        $work_group_key,
        $work_group_value,
        $permission_key,
        $permission_value,
        $status=NULL
    )
    {

        //Create a new permission
        $result = $this->roles_model->workGroupPermissionToggleStatus(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $permission_key => explode(SINGLE_DASH, $permission_value)
            ),
            $status
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Check to see if a Work Group has a specific Permission
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $permission_key
     * @param $permission_value
     */
    public function work_group_has_permission(
        $work_group_key,
        $work_group_value,
        $permission_key,
        $permission_value
    )
    {

        $permission = array();

        $work_group = array();

        $permission[$permission_key] = $permission_value;

        $work_group[$work_group_key] = $work_group_value;

        //Create a new permission
        $result = $this->roles_model->workGroupHasPermission($work_group, $permission);

        //Render the json object as output
        var_dump($result);

    }

//*****************
// MODULES
//*****************

    /**
     * Create a new module by just passing the module name
     *
     * @param $name
     */
    public function module_create_new($name)
    {
        $name = urldecode($name);
        $data = array(
            "module_name" => $name,
            "module_slug" => acme_to_slug($name)
        );

        //Create a new module
        $result = $this->roles_model->moduleCreateNew($data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Update the specified module
     * Pass the key value pair to query the existing module
     * Pass the new name for that module
     *
     * @param $key
     * @param $value
     * @param $name
     */
    public function module_update($key, $value, $name)
    {
        $name = urldecode($name);

        $data = array(
            "module_name" => $name,
            "module_slug" => acme_to_slug($name)
        );

        //Create a new module
        $result = $this->roles_model->moduleUpdate(array($key => $value), $data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Delete the specified module
     *
     * @param $key
     * @param $value
     */
    public function module_delete($key, $value)
    {

        //Create a new module
        $result = $this->roles_model->moduleDelete(array($key => $value));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific module by passing the id
     *
     * @param int $id
     */
    public function modules_get($id = 0)
    {
        // Get a module by id
        $result = $this->module_get_by_id($id);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific module by passing the slug
     *
     * @param $slug
     */
    public function module_get_by_slug($slug)
    {
        // Get a module by slug
        $result = $this->roles_model->moduleGet(array("module_slug" => $slug));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific module by passing the name
     *
     * @param $name
     */
    public function module_get_by_name($name)
    {
        // Get a module by name
        $result = $this->roles_model->moduleGet(array("module_name" => urldecode($name)));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific module by passing the id
     *
     * @param $id
     */
    public function module_get_by_id($id)
    {
        // Get a module by id
        $result = $this->roles_model->moduleGet(array("module_id" => $id));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get all modules in the system
     */
    public function modules_get_all()
    {
        // Get all the modules in the system
        $result = $this->roles_model->moduleGet(0);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get modules for the specified Work Group
     *
     * @param $key
     * @param $value
     */
    public function work_group_get_modules($key, $value)
    {

        //Create a new module
        $result = $this->roles_model->workGroupGetModules(
            array(
                $key => $value
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * @param $key
     * @param $values
     * @param $includeUsers
     */
    public function work_group_get_by_modules(
        $key,
        $values,
        $includeUsers=FALSE
    )
    {

        //Create a new module
        $result = $this->roles_model->workGroupGetByModules(
            array($key=>$values),
            $includeUsers
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group modules
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $module_key
     * @param $module_value
     */
    public function work_group_add_modules(
        $work_group_key,
        $work_group_value,
        $module_key, $module_value)
    {

        $module = array();

        $work_group = array();

        $module[$module_key] = work_group(SINGLE_PIPE, url_decode($module_value));

        $work_group[$work_group_key] = $work_group_value;

        //Create a new module
        $result = $this->roles_model->workGroupSetModules($work_group, $module, FALSE);

        //Render the json object as output
        var_dump($result);


    }

    /**
     * Update Work Group modules
     *
     * @param $work_group_module_id
     * @param $module_id
     * @param $work_group_id
     * @param $new_module_id
     * @param $new_work_group_id
     */
    public function work_group_update_module(
        $work_group_module_id,
        $module_id,
        $work_group_id,
        $new_module_id,
        $new_work_group_id
    )
    {

        //Create a new module
        $result = $this->roles_model->workGroupUpdateModule(
            $work_group_module_id,
            $module_id,
            $work_group_id,
            $new_module_id,
            $new_work_group_id
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group modules
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $module_key
     * @param $module_value
     * @param bool $override
     */
    public function work_group_set_modules(
        $work_group_key,
        $work_group_value,
        $module_key,
        $module_value,
        $override=TRUE
    )
    {

        // Prepare a module array to hold the where clause data
        $module = array();

        // Prepare a work group array to hold the where clause data
        $work_group = array();

        // Create a module key for use in the query
        $module[$module_key] = explode(SINGLE_DASH, $module_value);

        // Create a work group key for use in the query
        $work_group[$work_group_key] = $work_group_value;

        //Create a new module
        $result = $this->roles_model->workGroupSetModules($work_group, $module, $override);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Remove the specified module from a Work Group
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $module_key
     * @param $module_value
     */
    public function work_group_delete_module(
        $work_group_key,
        $work_group_value,
        $module_key,
        $module_value
    )
    {

        //Create a new module
        $result = $this->roles_model->workGroupDeleteModule(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $module_key => explode(SINGLE_DASH, $module_value)
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Toggle a Work Group module status 'active/inactive'
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $module_key
     * @param $module_value
     * @param $status
     */
    public function work_group_module_toggle_status(
        $work_group_key,
        $work_group_value,
        $module_key,
        $module_value,
        $status=NULL
    )
    {

        //Create a new module
        $result = $this->roles_model->workGroupModuleToggleStatus(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $module_key => explode(SINGLE_DASH, $module_value)
            ),
            $status
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Check to see if a Work Group has a specific Module
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $module_key
     * @param $module_value
     */
    public function work_group_has_module(
        $work_group_key,
        $work_group_value,
        $module_key,
        $module_value
    )
    {

        $module = array();

        $work_group = array();

        $module[$module_key] = $module_value;

        $work_group[$work_group_key] = $work_group_value;

        //Create a new module
        $result = $this->roles_model->workGroupHasModule($work_group, $module);

        //Render the json object as output
        var_dump($result);

    }

//*****************
// COMPONENTS
//*****************

    /**
     * Create a new component by just passing the component name
     *
     * @param $name
     */
    public function component_create_new($name)
    {
        $name = urldecode($name);
        $data = array(
            "component_name" => $name,
            "component_slug" => acme_to_slug($name)
        );

        //Create a new component
        $result = $this->roles_model->componentCreateNew($data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Update the specified component
     * Pass the key value pair to query the existing component
     * Pass the new name for that component
     *
     * @param $key
     * @param $value
     * @param $name
     */
    public function component_update($key, $value, $name)
    {
        $name = urldecode($name);

        $data = array(
            "component_name" => $name,
            "component_slug" => acme_to_slug($name)
        );

        //Create a new component
        $result = $this->roles_model->componentUpdate(array($key => $value), $data);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Delete the specified component
     *
     * @param $key
     * @param $value
     */
    public function component_delete($key, $value)
    {

        //Create a new component
        $result = $this->roles_model->componentDelete(array($key => $value));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific component by passing the id
     *
     * @param int $id
     */
    public function components_get($id = 0)
    {
        // Get a component by id
        $result = $this->component_get_by_id($id);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific component by passing the slug
     *
     * @param $slug
     */
    public function component_get_by_slug($slug)
    {
        // Get a component by slug
        $result = $this->roles_model->componentGet(array("component_slug" => $slug));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific component by passing the name
     *
     * @param $name
     */
    public function component_get_by_name($name)
    {
        // Get a component by name
        $result = $this->roles_model->componentGet(array("component_name" => urldecode($name)));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a specific component by passing the id
     *
     * @param $id
     */
    public function component_get_by_id($id)
    {
        // Get a component by id
        $result = $this->roles_model->componentGet(array("component_id" => $id));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get all components in the system
     */
    public function components_get_all()
    {
        // Get all the components in the system
        $result = $this->roles_model->componentGet(0);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get components for the specified Work Group
     *
     * @param $key
     * @param $value
     */
    public function work_group_get_components($key, $value)
    {

        //Create a new component
        $result = $this->roles_model->workGroupGetComponents(
            array(
                $key => $value
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * @param $key
     * @param $values
     * @param $includeUsers
     */
    public function work_group_get_by_components(
        $key,
        $values,
        $includeUsers=FALSE
    )
    {

        //Create a new component
        $result = $this->roles_model->workGroupGetByComponents(
            array($key=>$values),
            $includeUsers
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group components
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $component_key
     * @param $component_value
     */
    public function work_group_add_components(
        $work_group_key,
        $work_group_value,
        $component_key, $component_value)
    {

        $component = array();

        $work_group = array();

        $component[$component_key] = work_group(SINGLE_PIPE, url_decode($component_value));

        $work_group[$work_group_key] = $work_group_value;

        //Create a new component
        $result = $this->roles_model->workGroupSetComponents($work_group, $component, FALSE);

        //Render the json object as output
        var_dump($result);


    }

    /**
     * Update Work Group components
     *
     * @param $work_group_component_id
     * @param $component_id
     * @param $work_group_id
     * @param $new_component_id
     * @param $new_work_group_id
     */
    public function work_group_update_component(
        $work_group_component_id,
        $component_id,
        $work_group_id,
        $new_component_id,
        $new_work_group_id
    )
    {

        //Create a new component
        $result = $this->roles_model->workGroupUpdateComponent(
            $work_group_component_id,
            $component_id,
            $work_group_id,
            $new_component_id,
            $new_work_group_id
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Create Work Group components
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $component_key
     * @param $component_value
     * @param bool $override
     */
    public function work_group_set_components(
        $work_group_key,
        $work_group_value,
        $component_key,
        $component_value,
        $override=TRUE
    )
    {

        // Prepare a component array to hold the where clause data
        $component = array();

        // Prepare a work group array to hold the where clause data
        $work_group = array();

        // Create a component key for use in the query
        $component[$component_key] = explode(SINGLE_DASH, $component_value);

        // Create a work group key for use in the query
        $work_group[$work_group_key] = $work_group_value;

        //Create a new component
        $result = $this->roles_model->workGroupSetComponents($work_group, $component, $override);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Remove the specified component from a Work Group
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $component_key
     * @param $component_value
     */
    public function work_group_delete_component(
        $work_group_key,
        $work_group_value,
        $component_key,
        $component_value
    )
    {

        //Create a new component
        $result = $this->roles_model->workGroupDeleteComponent(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $component_key => explode(SINGLE_DASH, $component_value)
            )
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Toggle a Work Group component status 'active/inactive'
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $component_key
     * @param $component_value
     * @param $status
     */
    public function work_group_component_toggle_status(
        $work_group_key,
        $work_group_value,
        $component_key,
        $component_value,
        $status=NULL
    )
    {

        //Create a new component
        $result = $this->roles_model->workGroupComponentToggleStatus(
            array(
                $work_group_key => $work_group_value
            ),
            array(
                $component_key => explode(SINGLE_DASH, $component_value)
            ),
            $status
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Check to see if a Work Group has a specific Component
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $component_key
     * @param $component_value
     */
    public function work_group_has_component(
        $work_group_key,
        $work_group_value,
        $component_key,
        $component_value
    )
    {

        $component = array();

        $work_group = array();

        $component[$component_key] = $component_value;

        $work_group[$work_group_key] = $work_group_value;

        //Create a new component
        $result = $this->roles_model->workGroupHasComponent($work_group, $component);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * @param $work_group_key
     * @param $work_group_value
     */
    public function work_group_get_meta(
        $work_group_key,
        $work_group_value
    )
    {

        //Create a new role
        $result = $this->roles_model->workGroupGetMetadata(array($work_group_key=>$work_group_value));

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Set a Work Group meta data by assigning a value to a specified key
     * If a meta data element already exists, it it replaced
     * If a meta data element does not exists, a new row is created
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $meta_key
     * @param $meta_value
     */
    public function work_group_set_meta(
        $work_group_key,
        $work_group_value,
        $meta_key,
        $meta_value
    )
    {

        //Create a new role
        $result = $this->roles_model->workGroupSetMetadata(
            array(
                $work_group_key=>$work_group_value
            ),
            $meta_key,
            $meta_value
        );

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Delete a Work Group meta data using the specified key
     *
     * @param $work_group_key
     * @param $work_group_value
     * @param $meta_key
     */
    public function work_group_delete_meta(
        $work_group_key,
        $work_group_value,
        $meta_key
    )
    {

        //Create a new role
        $result = $this->roles_model->workGroupDeleteMetadata(array($work_group_key=>$work_group_value), $meta_key);

        //Render the json object as output
        var_dump($result);

    }

    /**
     *  Get all Work Groups
     */
    public function work_groups_get_all()
    {
        //Create a new role
        $result = $this->roles_model->workGroupsGetAll();

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get a Work Group using key value pairs
     *
     * @param $key
     * @param $value
     */
    public function work_group_get(
        $key,
        $value
    )
    {

        $work_group_vars = array();

        $work_group_vars[$key] = $value;

        //Create a new role
        $result = $this->roles_model->workGroupGet($work_group_vars);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get the Work group by slug
     *
     * @param $slug
     */
    public function work_group_slug($slug)
    {
        //Create a new role
        $result = $this->roles_model->workGroupsGetBySlug($slug);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get the Work group by name
     *
     * @param $name
     */
    public function work_group_name($name)
    {
        //Create a new role
        $result = $this->roles_model->workGroupsGetByName($name);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get the Work Group
     *
     * @param $id
     */
    public function work_group($id=0)
    {

        $type = array("work_group_id" => $id);

        //Create a new role
        $result = $this->roles_model->workGroupGet($type);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get the Work Group meta data
     *
     * @param $key
     * @param $value
     */
    public function work_group_meta($key, $value)
    {

        $type = array($key => $value);

        //Create a new role
        $result = $this->roles_model->workGroupGetMetadata($type);

        //Render the json object as output
        var_dump($result);

    }

    /**
     * Get the user summary and authentication data
     * @param $id
     * @param $includePassword
     */
    public function user_data($id, $includePassword=FALSE)
    {

        //Create a new role
        $result = $this->roles_model->getUserData($id, $includePassword);

        var_dump($result);

    }

    /**
     * Get the user full details
     *
     * @param $id
     */
    public function user_details($id)
    {

        //Create a new role
        $result = $this->roles_model->getUserDetails(
            $id,
            array(
                "metadata",
                "vcards",
                "firebase",
                //"authentication_log",
                //"activity_log",
                "work_group_ids",
                "work_groups" => array(
                    "metadata",
                    "roles",
                    "permissions",
                    "views",
                    "modules",
                    "components"
                )
            )
        );

        //Render the json object as output
        return ($result);

    }
    
} 