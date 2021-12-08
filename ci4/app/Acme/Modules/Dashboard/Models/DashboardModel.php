<?php

/**
 * Model Class Dashboard
 *
 * @adminor      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Acme
 * @subpackage  Dashboard
 */

namespace Acme\Modules\Dashboard\Models;

/*
 * Make sure there is no direct access to the script
 */
if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class DashboardModel extends \Acme\Modules\Dashboard\Models\DashboardBaseModel
{
    /**
     * DashboardModel constructor.
     */
    public function __construct()
    {

        /**
         *  Call the parent Contructor
         */
        parent::__construct();

    }

}