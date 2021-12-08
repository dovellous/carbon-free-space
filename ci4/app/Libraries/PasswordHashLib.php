<?php namespace App\Libraries;

//use \OAuth2\Storage\Pdo;
use \App\Libraries\CustomOauthStorage;


class PasswordHashLib{

  function __construct(){
    $this->init();
  }

  public function hashPassword($password){
      $pepper = getConfigVariable("pepper");
      $pwd = sha1(md5($password));
      $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
      $pwd_hashed = password_hash($pwd_peppered, PASSWORD_ARGON2ID);
      return $pwd_hashed;
  }

    public function comparePasswords($password, $pwd_hashed){
        $pepper = getConfigVariable("pepper");
        $pwd = sha1(md5($password));
        $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
        return password_verify($pwd_peppered, $pwd_hashed) ? TRUE : FALSE;
    }

}
