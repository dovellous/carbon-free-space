<?php namespace App\Libraries;

use \OAuth2\Request;

class Device{
  var $server;

  function __construct(){

      $this->request = new Request();

      $this->init();

  }

  public function init(){

  }

    public function generateClientId($uuid){

        return md5(sha1($uuid));

    }

    public function generateClientSecret($uuid){

        return password_hash(sha1(md5($uuid)) . sha1(md5($uuid)), PASSWORD_DEFAULT);

    }

    public function generateDeviceKey(){

        $deviceString = $this->requestGetVar('id').':'.$this->requestGetVar('manufacturer').':'.$this->requestGetVar('model').':'.$this->requestGetVar('operatingSystem').':'.$this->requestGetVar('uuid');

        $deviceKey = sha1($deviceString);

        return $deviceKey;

    }

    public function generateDeviceServerToken(){

        $deviceString = $this->requestGetVar('id').':'.$this->requestGetVar('manufacturer').':'.$this->requestGetVar('model').':'.$this->requestGetVar('operatingSystem').':'.$this->requestGetVar('uuid');

        $deviceKey = sha1($deviceString);

        return $deviceKey;

    }

    public function generateDevicePrivateKey(){

        $deviceString = $this->requestGetVar('id').':'.$this->requestGetVar('manufacturer').':'.$this->requestGetVar('model').':'.$this->requestGetVar('operatingSystem').':'.$this->requestGetVar('uuid');

        $deviceKey = sha1($deviceString);

        return $deviceKey;

    }

    public function requestGetVar($key){

        return isset($_REQUEST[$key])?$_REQUEST[$key]:'NULL';

    }

}
