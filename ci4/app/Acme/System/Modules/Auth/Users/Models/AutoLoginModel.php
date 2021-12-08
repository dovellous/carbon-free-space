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
if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class AutoLoginModel
{
    /**
     * UsersModel constructor.
     */
    public function __construct()
    {

        /**
         *  Call the parent Contructor
         */
        parent::__construct();

    }

}