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

class AuthModel
{

    // default values
    private $cookie_name;
    private $cookie_encrypt;
    private $autologin_expire;
    private $hash_algorithm;

    private $ci;

    /**
     * Constructor, loads dependencies, initializes the library
     * and detects the autologin cookie
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('../../modules/messages/models/Notifications_model', 'notifications');

        $this->load->model('Autologin_model', 'autologin');

        $this->ci = &get_instance();

        // initialize from config
        if (!empty($config)) {
            $this->initialize($config);
        }

        log_message('debug', 'Authentication library initialized');

        // detect autologin
        if (!$this->session->userdata('auth_loggedin')) {

            $this->autologin();

        }

    }

    /**
     * Initialize with configuration array
     *
     * @param array $config
     */
    public function initialize($config = array()) {
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * Initialize with configuration array
     *
     * @param array $config
     */

    public function sign_in($encrypt_for_export=FALSE)
    {

        $username = $this->input->post("username");

        if( !$username ){

            $username = $this->input->post("email");

        }

        $password = $this->input->post("password");

        $remember = $this->input->post("remember");

        $result = array();

        // form submitted
        if ($username && $password) {

            $remember = $remember != NULL ? TRUE : FALSE;

            // get user from database

            $user = $this->user->get('username', $username);

            if ($user) {

                // compare passwords
                if ($this->user->check_password($password, $user['password'])) {

                    unset($user["password"]);

                    // mark user as logged in
                    $this->login($user['id'], $user, $remember);

                    $result['status'] = 'success';

                    if( $encrypt_for_export ){

                        $payload = json_encode( $user );

                        $result['payload'] = $this->aes->encrypt($payload);

                    }else{

                        $result['payload'] = $user;

                    }

                    $result['message'] = 'Login successful.';

                } else {

                    $result['status'] = 'error';

                    $result['message'] = 'Wrong password';

                }

            } else {

                $result['status'] = 'error';

                $result['message'] = 'User does not exist';

            }

        }else{

            $result['status'] = 'error';

            $result['message'] = 'Invalid parameters';

        }

        $result['logged_in'] = $this->auth->loggedIn();

        return $result;

    }

    /**
     * Mark a user as logged in and create autologin cookie if wanted
     *
     * @param string $id
     * @param boolean $remember
     * @return boolean
     */
    public function login($id, $user, $remember = TRUE) {

        if(!$this->loggedin()) {

            // mark user as logged in
            $this->session->set_userdata(
                array(
                    'auth_user' => $id,
                    'auth_loggedin' => TRUE,
                    'auth_data' => $user
                )
            );

            if ($remember) {

                $this->create_autologin($id);

            }

        }

        //TODO: Login history log

    }

    /**
     * Logout the current user, destroys the current session and autologin key
     */
    public function sign_out() {
        // remove cookie and active key
        $this->logout();
    }

    /**
     * Logout the current user, destroys the current session and autologin key
     */
    public function prepare_new_user() {
        // remove cookie and active key
        return $this->user->prepare_new_user();
    }

    /**
     * Logout the current user, destroys the current session and autologin key
     */
    public function register_user( $user ) {
        // remove cookie and active key
        return $this->user->insert( $user );
    }

    /**
     * Logout the current user, destroys the current session and autologin key
     */
    public function send_reset_instructions() {

        $uuid = md5(rand(0,100));

        $key = md5(rand(0,100));

        $variables = array();

        $variables["btn_class"] = "";
        $variables["btn_label"] = "ACTIVATE EMAIL";
        $variables["btn_link"] = BASE_URL . "auth/activate/" . $uuid . "/" . $key;

        $result = $this->notifications->get_email()->send(
            "doug.maposa@gmail.com",
            "Scale-readiness programme",
            "Accelerate2030 is a global multi-stakeholder scale-readiness programme",
            $variables
        );

        var_dump($result); exit;

    }

    /**
     * Logout the current user, destroys the current session and autologin key
     */
    public function logout() {

        // mark user as logged out
        $this->session->set_userdata(
            array(
                'auth_user' => FALSE,
                'auth_loggedin' => FALSE
            )
        );

        // remove cookie and active key
        $this->delete_autologin();

        // lets make sure the session is nullified
        $this->session->sess_destroy();

    }

    /**
     * Check if the current user is logged in or not
     *
     * @return boolean
     */
    public function loggedin() {
        return $this->session->userdata('auth_loggedin');
    }

    /**
     * Returns the user id of the current user when logged in
     *
     * @return int
     */
    public function userid() {
        return $this->loggedin() ? $this->session->userdata('auth_user') : FALSE;
    }

    /**
     * Generate a new key pair and create the autologin cookie
     *
     * @param int $id
     * @param string $series
     */
    private function create_autologin($id, $series = FALSE) {
        // generate keys
        list($public, $private) = $this->generate_keys();

        // create new series or expand current series
        if (!$series) {
            list($series) = $this->generate_keys();
            $this->autologin->insert($id, $series, $private);
        } else {
            $this->autologin->update($id, $series, $private);
        }

        // write public key to cookie
        $cookie = array('id' => $id, 'series' => $series, 'keyy' => $public);
        $this->write_cookie($cookie);
    }

    /**
     * Disable the current autologin key and remove the cookie
     */
    private function delete_autologin() {

        if ($cookie = $this->read_cookie()) {
            // remove current series

            $this->autologin->delete($cookie['id'], $cookie['series']);

            // delete cookie
            $this->input->set_cookie(array('name' => $this->cookie_name, 'value' => '', 'expire' => ''));
        }

    }

    /**
     * Detects the autologin cookie and check public/private key pair
     *
     * @return boolean
     */
    private function autologin() {

        if ($cookie = $this->read_cookie()) {
            // remove expired keys
            $this->load->model('login');
            $this->autologin->purge();

            // get private key
            $private = $this->autologin->get($cookie['id'], $cookie['series']);

            if ($this->validate_keys($cookie['keyy'], $private)) {
                // mark user as logged in
                $this->session->set_userdata(array('auth_user' => $cookie['id'], 'auth_loggedin' => TRUE));

                // user has a valid key, extend current series with new key
                $this->create_autologin($cookie['id'], $cookie['series']);
                return TRUE;
            } else {
                // the key was not valid, strange stuff going on
                // remove the active session to prevent theft!
                $this->delete_autologin();
            }
        }

        return FALSE;

    }

    /**
     * Write data to autologin cookie
     *
     * @param array $data
     */
    private function write_cookie($data = array()) {
        $data = serialize($data);

        // encrypt cookie
        if ($this->cookie_encrypt) {
            $this->load->library('encrypt');
            $data = $this->encryption->encrypt($data);
        }

        return $this->input->set_cookie(array('name' => $this->cookie_name, 'value' => $data, 'expire' => $this->autologin_expire));
    }

    /**
     * Read data from autologin cookie
     *
     * @return boolean
     */
    private function read_cookie() {
        $cookie = $this->input->cookie($this->cookie_name, TRUE);

        if (!$cookie) {
            return FALSE;
        }

        // decrypt cookie
        if ($this->cookie_encrypt) {
            $this->load->library('encrypt');
            $data = $this->encryption->decrypt($cookie);
        }

        $data = @unserialize($data);

        if (isset($data['id']) && isset($data['series']) && isset($data['key'])) {
            return $data;
        }

        return FALSE;
    }

    /**
     * Generate public/private key pair
     *
     * @return array
     */
    private function generate_keys() {
        $public = hash($this->hash_algorithm, uniqid(rand()));
        $private = hash_hmac($this->hash_algorithm, $public, $this->config->item('encryption_key'));

        return array($public, $private);
    }

    /**
     * Validate public/private key pair
     *
     * @param string $public
     * @param string $private
     * @return boolean
     */
    private function validate_keys($public, $private) {
        $check = hash_hmac($this->hash_algorithm, $public, $this->config->item('encryption_key'));
        return $check == $private;
    }

    /**
     * Validate public/ddprivate key pair
     *
     * @param string $public
     * @param string $private
     * @return boolean
     */
    public function get_user_landing_page() {

        return 'dashboard';

    }

}