<?php namespace App\Controllers;

use \App\Libraries\Oauth;
use App\Models\ClientModel;
use \OAuth2\Request;
use CodeIgniter\API\ResponseTrait;
use App\Models\DeviceModel;

class Device extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Accept,Origin,Authorization,DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type");
        /*
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
        header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
        */
        $this->request = new Request();

    }

    public function register()
    {

        $respond = $this->register_device();

        $code = $respond["code"];

        $body = $respond["body"];

        $result = $this->respond(($body), $code);

        return $result;

    }

    public function register_device()
    {

        helper('form');

        $data = [];

        if ($this->request->getMethod() != 'post' && $this->request->getMethod() != 'get')
            return $this->fail('Only post request is allowed');

        $rules = [
            'uuid' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {

            return $this->fail($this->validator->getErrors());

        } else {

            $oauth = new Oauth();

            $deviceLib = new \App\Libraries\Device();

            $model = new DeviceModel();

            $clientModel = new ClientModel();

            $client_id = $deviceLib->generateClientId($this->requestGetVar('uuid'));

            $client_secret = $deviceLib->generateClientSecret($this->requestGetVar('uuid'));

            $redirect_uri = "";

            $grant_types = "password";
            $scope = "app";
            $user_id = 0;

            $clientData = [
                "client_id" => $client_id,
                "client_secret" => $client_secret,
                "redirect_uri" =>  $redirect_uri,
                "grant_types" => $grant_types,
                "scope" => $scope,
                "user_id" => $user_id
            ];

            $_client = $clientModel->where("client_id", $client_id)->get()->getRowArray();

            if($_client == NULL){

                $device_client_id = $clientModel->insert($clientData);

            } else {

                $device_client_id = $clientModel->update($client_id, $clientData);

            }

            if($device_client_id) {

                $data = [
                    'device_client_id' => $client_id,
                    'device_uuid' => $this->requestGetVar('uuid'),
                    'device_name' => $this->requestGetVar('deviceName'),
                    'device_is_virtual' => $this->requestGetVar('isVirtual'),
                    'device_manufacturer' => $this->requestGetVar('manufacturer'),
                    'device_model' => $this->requestGetVar('model'),
                    'device_os' => $this->requestGetVar('operatingSystem'),
                    'device_os_ver' => $this->requestGetVar('osVersion'),
                    'device_platform' => $this->requestGetVar('platform'),
                    'device_agent' => $_SERVER["HTTP_USER_AGENT"],
                    'device_webview_version' => $this->requestGetVar('webViewVersion'),
                    'device_app_name' => $this->requestGetVar('name'),
                    'device_app_build' => $this->requestGetVar('build'),
                    'device_app_id' => $this->requestGetVar('id'),
                    'device_app_version' => $this->requestGetVar('version'),
                    'device_firebase_token' => 0,
                    'device_status' => 'INACTIVE',
                ];

                $device_id = $model->insert($data);

                return array("code"=>200, "body"=>$clientData);

            }else{

                return array("code"=>404, "body"=>$clientData);

            }

        }

    }

    public function requestGetVar($key){

        return isset($_REQUEST[$key])?$_REQUEST[$key]:'NULL';

    }


}
