<?php 

namespace Acme\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class AuthLibrary
{

    private $user;

    private $aes;

    private $passwordHash;

    public function __construct( $user, $request )
    {

        // Check if session was started already of begin it
        if(!$user->session) {
            // Session Class
            $this->session = \Config\Services::session();
        }else{
            $this->session = $user->session;
        }

        //
        $this->user = $user;

        //
        $this->aes = $user->aes;

        //
        $this->passwordHash = $user->passwordHash;

        //
        $this->acmeRequest = $request;
        
        if(!defined("AUTH_COOKIE_NAME")){
            
            define("AUTH_COOKIE_NAME", "autologin");
            
        }

        if(!defined("AUTH_COOKIE_ENCRYPT")){

            define("AUTH_COOKIE_ENCRYPT", FALSE);

        }

        if(!defined("AUTH_COOKIE_EXPIRY")){

            define("AUTH_COOKIE_EXPIRY", 60*60*24*3);

        }

        if(!defined("HASH_ALGORITHM")){

            define("HASH_ALGORITHM", "sha256");

        }

    }

    public function isRequestAuthorized()
    {
        
        // Requires login  // else continue
        //
        // If so, is User logged in // else show login form :: $this->redirectToLoginForm();
        // If so, is User authorised to use the module // :: 
        // If so, is the User authorized to use the component
        // If so, is the User authorized to use the component method
        // If not, show login form
        //
        // 

        return array(
            "isRequestAuthorised" => true,
            "httpCode" => "http" . 404,
            "httpMessage" => "Message Here"
        );

    }

    /**
     * @param bool $encrypt
     * @return array
     */

    public function signIn($encrypt=FALSE)
    {

        $username = $this->acmeRequest->getPost("username");

        if( !$username ){

            $username = $this->acmeRequest->getPost("email");

        }

        if( !$username ){

            $result['status'] = 'error';

            $result['message'] = 'Invalid username';

        }

        $password = $this->acmeRequest->getPost("password");

        $remember = $this->acmeRequest->getPost("remember");

        $result = array();

        // form submitted
        if ($username && $password) {

            //
            $remember = $remember == "on" ? TRUE : FALSE;

            //
            $userdata = array(
                "returnType"=>"row_array",
                "where" => array(
                    "au_username" => $username
                )
            );

            // get user from database
            $userResultObject = $this->user->usersModel->ACMEUsersDBRead( $userdata );

            $user = $userResultObject["resultObject"];

            if ($user) {

                // compare passwords
                if ($this->checkPassword($password, $user['au_password'])) {

                    unset($user["au_password"]);

                    //Get all user data
                    $user = $this
                        ->user
                        ->roles
                        ->user_details(
                            $user['user_id']
                        );

                    // mark user as logged in
                    $this->login(
                        $user['user_id'],
                        $user,
                        $remember
                    );

                    $result['status'] = 'success';

                    if( $encrypt ){

                        $payload = json_encode( $user );

                        $result['payload'] = $this->aes->encrypt($payload);

                    }else{

                        $result['payload'] = $user;

                    }

                    $result['message'] = 'Login successful';

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

        $result['loggedIn'] = $this->loggedIn();

        return $result;

    }

    /**
     * Mark a user as logged in and create autologin cookie if wanted
     *
     * @param string $id
     * @param string $user
     * @param boolean $remember
     * @return boolean
     */
    public function login($id, $user, $remember = TRUE) {

        if(!$this->loggedin()) {

            $this->setUserSessionData($id, $user);

            if ($remember) {

                $this->createAutoLogin($id);

            }

        }

        //TODO: Login history log

    }

    /**
     * Logout the current user, destroys the current session and autologin key
     */
    public function signOut() {
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

    }

    /**
     * Logout the current user, destroys the current session and autologin key
     */
    public function logout() {

        // mark user as logged out
        $this->destroyUserSessionData();

        // remove cookie and active key
        $this->deleteAutoLogin();

        // lets make sure the session is nullified
        $this->session->destroy();

    }

    /**
     * Check if the current user is logged in or not
     *
     * @return boolean
     */
    public function loggedin() {

        return $this->session->get('authUserLoggedIN');

    }

    /**
     * Check if the current user is logged in or not
     *
     * @return boolean
     */
    public function validateUserSession() {

        $userSessionData = $this->getUserSessionData();

        $checksumValue = $this->getUserSessionData()['authUserChecksum'];

        $generatedChecksumValue = md5(json_encode($userSessionData));

        return $checksumValue == $generatedChecksumValue;

    }

    /**
     * Returns the user id of the current user when logged in
     *
     * @return int
     */
    public function userID() {
        
        return $this->loggedin() ? $this->session->get('authUserID') : FALSE;
        
    }

    /**
     * Generate a new key pair and create the autologin cookie
     *
     * @param int $id
     * @param string $series
     */
    private function createAutoLogin($id, $series = FALSE) {

        // generate keys
        list($public, $private) = $this->generateKeys();

        // create new series or expand current series
        if (!$series) {
            list($series) = $this->generateKeys();
            $this->user->autoLogin->insert($id, $series, $private);
        } else {
            $this->user->autoLogin->update($id, $series, $private);
        }

        // write public key to cookie
        $cookie = array(
            'id' => $id,
            'series' => $series,
            'keyy' => $public
        );

        $this->writeCookie($cookie);
    }

    /**
     * Disable the current autologin key and remove the cookie
     */
    private function deleteAutoLogin() {

        $cookie = $this->readCookie();

        if ($cookie) {
            // remove current series

            $this->user->autoLogin->delete(
                $cookie['id'],
                $cookie['series']
            );

            // delete cookie
            setcookie(AUTH_COOKIE_NAME, 0, (time()-(3600*24*365)));
        }

    }

    /**
     * Detects the autologin cookie and check public/private key pair
     *
     * @return boolean
     */
    public function checkAutoLogin() {

        return $this->autoLogin();

    }

    /**
     * Detects the autologin cookie and check public/private key pair
     *
     * @return boolean
     */
    private function autoLogin() {

        $cookie = $this->readCookie();

        if ($cookie) {

            // remove expired keys
            $this->user->autoLogin->purge();

            // get private key
            $private = $this->user->autoLogin->get(
                $cookie['id'],
                $cookie['series']
            );

            if ($this->validateKeys($cookie['keyy'], $private)) {

                // user has a valid key, extend current series with new key
                $this->createAutoLogin(
                    $cookie['id'],
                    $cookie['series']
                );

                //
                $userdata = array(
                    "returnType"=>"row_array",
                    "where" => array(
                        "user_id" => $cookie['id']
                    )
                );

                // get user from database
                $userResultObject = $this->user->usersModel->ACMEUsersDBRead( $userdata );

                $user = $userResultObject["resultObject"];

                $this->setUserSessionData($cookie['id'], $user);

                return TRUE;

            } else {

                // the key was not valid, strange stuff going on
                // remove the active session to prevent theft!
                $this->deleteAutoLogin();

            }
        }

        return FALSE;

    }

    /**
     * Write data to autologin cookie
     *
     * @param array $data
     */
    private function writeCookie($data = array()) {

        $data = serialize($data);

        // encrypt cookie
        if (AUTH_COOKIE_ENCRYPT) {

            //$data = $this->encryption->encrypt($data);

        }

        return setcookie(AUTH_COOKIE_NAME, $data, time() + (AUTH_COOKIE_EXPIRY));

    }

    /**
     * Read data from autologin cookie
     *
     * @return boolean
     */
    private function readCookie() {

        if(!isset($_COOKIE[AUTH_COOKIE_NAME])) {

            return FALSE;

        }

        $cookie = $_COOKIE[AUTH_COOKIE_NAME];

        if (!$cookie) {

            return FALSE;

        }

        // decrypt cookie
        if (AUTH_COOKIE_ENCRYPT) {
            
            //$data = $this->encryption->decrypt($cookie);

            //TODO - Load encrypt lib

            $data = $cookie;
            
        }else{

            $data = $cookie;

        }

        $data = @unserialize($data);

        if (isset($data['id']) && isset($data['series']) && isset($data['keyy'])) {

            return $data;

        }

        return FALSE;
        
    }

    /**
     * Generate public/private key pair
     *
     * @return array
     */
    private function generateKeys() {

        $public = hash(HASH_ALGORITHM, uniqid(rand()));
        $private = hash_hmac(HASH_ALGORITHM, $public, $this->user->usersConfig->systemConfig['encryption.key']);
        return array($public, $private);
    }

    /**
     * Validate public/private key pair
     *
     * @param string $public
     * @param string $private
     * @return boolean
     */
    private function validateKeys($public, $private) {
        $check = hash_hmac(HASH_ALGORITHM, $public, $this->user->usersConfig->systemConfig['encryption.key']);
        return $check == $private;
    }

    /**
     * Compare user input password to stored hash
     *
     * @param string $password
     * @param string $stored_hash
     */
    public function checkPassword($password, $stored_hash) {

        //
        if(strlen($stored_hash) == 32){
            //
            return (
                //
                strcmp(
                    //
                    md5(
                        $password
                    ),
                    $stored_hash
                ) === 0);

        }else{

            // check password
            return $this->user->passwordHash->CheckPassword(
                //
                $password,
                //
                $stored_hash
            );

        }
    }

    /**
     * Password hashing function
     *
     * @param string $password
     */
    public function hashPassword($password) {

        // hash password
        return $this->user->passwordhash->HashPassword($password);

    }

    /**
     *
     */
    public function destroyUserSessionData() {

        return $this->session->set( array() );

    }

    /**
     *
     */
    public function setUserSessionData($id, $user) {

        $userArray = array(
            'id' => $id,
            'uuid' => $id,
            'userID' => $id,
            'username' => $user["au_username"],
            'authUserID' => $id,
            'authUserUUID' => $user["au_uuid"],
            'authUserName' => $user["au_username"],
            'authUserData' => $user,
            'authUserLoggedIN' => TRUE,
            'authUserLoggedTime' => time(),
        );

        $hashedUserArray = md5(json_encode($userArray));

        $userArray["authUserChecksum"] = $hashedUserArray;

        // mark user as logged in
        $this->session->set(
            $userArray
        );

        return $userArray;

    }

    /**
     *
     */
    public function getUserSessionData() {

        return $this->user->getUserSessionData();

    }

    /**
     *
     */
    public function getUserSessionMetaData() {

        $sessionData = $this->getUserSessionData();

        if(array_key_exists("metadata", $sessionData)){

            return $sessionData["metadata"];

        }

        return array();

    }

    /**
     *
     */
    public function getUserLandingPage() {

        $userSessionMetaData = $this->getUserSessionMetaData();

        if(isset($userSessionMetaData["defaultLandingPage"])) {

            return $userSessionMetaData["defaultLandingPage"];

        }else{

            return 'dashboard';

        }

    }

    /**
     * Validate public/private key pair
     *
     * @param string $public
     * @param string $private
     * @return boolean
     */
    public function getUserLoginPage() {

        return 'login';

    }

}