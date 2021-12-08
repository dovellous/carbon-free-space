<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class AutoLogin
{

    // database table name
    var $db = null;
    // database table name
    var $table = 'auth_autologin';
    var $expire = 5184000;

    /**
     * Get the settings from config
     */
    public function __construct($usersModel)
    {

        $this->usersModel = $usersModel;
        
    }

    /**
     * Get the private key for a specific user and series
     */
    public function get($user_id, $series) {

        $params = array(
            "table" => $this->table,
            "returnType" => "rowArray",
            "where" => array(
                "user_id" => $user_id,
                "series" => $series
            )
        );

        $row = $this->usersModel->legacyGetData($params);

        return $row ? $row['keyy'] : FALSE;

    }

    /**
     * Extend a user's current series with a new key
     */
    public function update($user_id, $series, $private) {

        $params = array(
            "table" => $this->table,
            "where" => array(
                "user_id" => $user_id,
                "series" => $series
            )
        );

        $params["data"] = array('keyy' => $private, 'created' => time());

        return $this->usersModel->legacyUpdateData($params);

    }

    /**
     * Start a new serie for a user
     */
    public function insert($user_id, $series, $private) {

        $params = array(
            "table" => $this->table
        );

        $params["data"] = array('user_id' => $user_id, 'series' => $series, 'keyy' => $private, 'created' => time());

        return $this->usersModel->legacyInsertData($params);

    }

    /**
     * Dlete a user's series
     */
    public function delete($user_id, $series) {

        $params = array(
            "table" => $this->table,
            "where" => array(
                "user_id" => $user_id,
                "series" => $series
            )
        );

        return $this->usersModel->legacyDeleteData($params);

    }

    /**
     * Remove all expired keys
     */
    public function purge() {

        $params = array(
            "table" => $this->table,
            "where" => array(
                "created <", time() - $this->expire
            )
        );

        return $this->usersModel->legacyDeleteData($params);

    }

}