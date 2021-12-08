<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class GoogleLibrary
{
    
    public $url = 'https://fcm.googleapis.com/fcm/send';
    
    public $access_key = 'AAAAg_dOpt4:APA91bHleaCnMaoRutdSTNfgqiT_dwxGg1HOCQs0QTD6-PXRg-Kv5pDRfec8kuFv_IX-qLkq0Ccmdu4fgrUwksPGEZsX0Nfc3aFUAMyTDzruCQwFWVSWQuvENNHPCZ7Z-4CoQ01DQzu_';

    public $curl;
    
    public function __construct()
    {
        $this->curl = new CurlLibrary();
        $this->api = new APILibrary();
    }

    /**
     * Sets the key-size for encryption/decryption in number of bits
     * @param  $nNewSize int The new key size. The valid integer values are: 128, 192, 256 (default) */
    function parse($method, $arguments)
    {

        call_user_func(array($this, $method), $arguments);

    }

    /**
     * Check if the user is authorised to use the module.
     * Override controller Check by passing TRUE as parameter
     * @param boolean $override : Override and authorise the module on the fly
     */
    public function map($args) {

        $url="https://maps.googleapis.com/maps/api/staticmap?size=$args[4]x$args[5]&markers=icon:http://www.google.com/mapfiles/arrow.png|$args[2],$args[3]&visible=$args[2],$args[3]|$args[2],$args[3]&zoom=$args[1]&key=AIzaSyDFOG__PFRlwPY2RRYbC87re_qcWrdq0HY";

        $result = $this->curl->post( $url, array(), array() );

        $this->api->write_image($result);

    }

    /**
     * Check if the user is authorised to use the module.
     * Override controller Check by passing TRUE as parameter
     * @param boolean $override : Override and authorise the module on the fly
     */
    public function places($args) {

        $url="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$args[2],$args[3]&radius=$args[4]&type=$args[1]&key=AIzaSyDFOG__PFRlwPY2RRYbC87re_qcWrdq0HY";

        $result = $this->curl->post( $url, array(), array() );

        $this->api->write_json(json_decode($result,1));

    }

    /**
     * Check if the user is authorised to use the module.
     * Override controller Check by passing TRUE as parameter
     * @param boolean $override : Override and authorise the module on the fly
     */
    public function directions($args) {

        $url="https://maps.googleapis.com/maps/api/directions/json?origin=$args[1]&destination=$args[2]&key=AIzaSyDFOG__PFRlwPY2RRYbC87re_qcWrdq0HY";

        $result = $this->curl->post( $url, array(), array() );

        $this->api->write_json(json_decode($result,1));

    }

}
