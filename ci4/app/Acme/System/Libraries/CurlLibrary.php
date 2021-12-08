<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class CurlLibrary
{

    /**
     * CurlLibrary constructor.
     */
    public function __construct() {

    }

    /**
     * @param $url
     * @param $headers
     * @param $fields
     * @return mixed|string
     */
    public function post( $url, $headers, $fields ) {


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        if ($result === FALSE) {

            $result = curl_error($ch);

        }

        curl_close($ch);

        return $result;

    }

}