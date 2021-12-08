<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class FirebaseLibrary
{
    
    public $url = 'https://fcm.googleapis.com/fcm/send';
    
    public $access_key = 'AAAAg_dOpt4:APA91bHleaCnMaoRutdSTNfgqiT_dwxGg1HOCQs0QTD6-PXRg-Kv5pDRfec8kuFv_IX-qLkq0Ccmdu4fgrUwksPGEZsX0Nfc3aFUAMyTDzruCQwFWVSWQuvENNHPCZ7Z-4CoQ01DQzu_';

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("curl");
    }

    public function register_device($uuid, $fcmToken, $userID, $device){


        $device_uuid = $uuid;

        $device_fbid = $fcmToken;

        $device_user = $userID;

        $platform = $device['platform'];
        $manufacture = $device['manufacture'];
        $model = $device['model'];
        $serial = $device['serial'];
        $cordova = $device['cordova'];
        $version = $device['version'];
        $virtual = $device['virtual'];


        $data = array(
            "uuid"=>$device_uuid,
            "fbid"=>$device_fbid,
            "userid"=>$device_user,
            "platform"=>$platform,
            "cordova"=>$cordova,
            "manufacture"=>$manufacture,
            "model"=>$model,
            "serial"=>$serial,
            "version"=>$version,
            "isvirtual"=>$virtual,
            "regd"=>date('Y-m-d H:i:s')
        );

        $sql = "select * from tbl_devices where userid = '$device_user'";

        $query = $this->db->query($sql);

        $rows = $query->result_array();

        if($rows == null){

            $result = $this->do_insert("tbl_devices", $data);

        }else{

            $result = $this->do_update("tbl_devices", $data, array("userid"=>$device_user));

        }

        if( $result["status"] == "success" ){

            $result["message"] = "A record was added successfully.";

        }else{

            $result["message"] = "There was error inserting data.";

        }

        return $result;

    }

    public function notify($tokens, $title, $message, $subtitle='', $data=array(), $is_topic=false){

        define( 'API_ACCESS_KEY', self::access_key);

        if(!is_array($tokens)){

            $tokens = array($tokens);

        }

        if(empty($data)){

            $data = array(
                'time'=>time(),
                'data'=>array()
            );

        }

        $notification = array(
            'message'   => $message,
            'body'      => $message,
            'title'     => $title,
            'subtitle'  => $subtitle,
            'tickerText'    => $message,
            "sound" => 1,
            'vibrate'   => 1,
            'largeIcon' => 'img/logo.png',
            'smallIcon' => 'img/logo.png',
            'icon' => 'img/logo.png',
            "color" => "#FF0000"
        );

        if($is_topic){

            $fields = array(
                'to'             => $tokens,
                'priority'     => "high",
                'notification' => $notification,
                'data'         => $data
            );

        }else{

            $fields = array(
                'registration_ids' => $tokens,
                'priority'     => "high",
                'notification' => $notification,
                'data'         => $data
            );

        }

        $headers = array(
            'Authorization:key = '.self::access_key,
            'Content-Type: application/json'
        );

        $result = $this->curl->post( self::url, $headers, $fields );

        return $result;

    }

}
