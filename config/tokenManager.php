<?php
// show error reporting
error_reporting(E_ALL);

// set your default time-zone
date_default_timezone_set('Asia/Manila');

include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;



class TokenManager
{

  private $jwt;
  private $client_app_id;
  public $current_client_app_id = 'TiPKOpWDLaBEadGgf9uK';
  private $key = "mNTXY2rqh5dFlvucPhoWD28GuR1H";
  private $issuer = "UQuCChDuTtKZgNs49F5i";
  private $issued_at;
  private $expiration_time;
  private $aaa;

  // constructor
  public function __construct()
  {
    $headers = apache_request_headers();

    $this->aaa = 'headers';

     foreach ($headers as $header => $value) {
      $this->aaa .=  $header .': ' . $value . ', ';
  } 

   // $aaa = 'nada';

    if(isset($headers['Accesstoken'])){
    //  $aaa = $headers['Authorization'];
      $this->jwt = $headers['Accesstoken'];//substr(strstr($headers['Accesstoken']," "), 1);
    }else{
      $this->jwt = null;
    }


    


    $this->issued_at = time();
    $this->expiration_time = time() + (60 * 60 * 24 * 365); // 1 hour = 60 * 60 // A year
  }
  public function getTokenByUser($user)
  {
    $token = array(
      "iat" => $this->issued_at,
      "exp" => $this->expiration_time,
      "iss" => $this->issuer,
      "data" => array(
        "id" => $user->id,
        "firstname" => $user->firstname,
        "lastname" => $user->lastname,
        "username" => $user->username
      )
    );
    $this->jwt = JWT::encode($token, $this->key);
    return $this->jwt;
  }
  public function validate()
  {
    // if ($this->client_app_id !== $this->current_client_app_id) {
    // return array(
    //   "error" => "Invalid client_app_id"
    // );
    //  } else {

    if ($this->jwt) {
      try {
        // decode jwt
        $decoded = JWT::decode($this->jwt, $this->key, array('HS256'));
        return  array(
          "error" => false,
          "data" => $decoded->data
        );

        // $decoded->data;
      } catch (Exception $e) {
        return array(
          "error" => $e->getMessage()
        );
      }
    } else {
      return array(
        "error" => "No token present ffff",
        "aaa" => $this->aaa
      );
    }
    //}
  }
}
