<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Accesstoken, Clientappid");
header("Access-Control-Allow-Methods: PUT");
header("Allow: PUT");
header("Content-Type: application/json; charset=UTF-8");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


// files needed to connect to database
include_once '../config/database.php';
include_once '../config/tokenManager.php';
include_once '../objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// generate token manager
$tm = new TokenManager();

$validation = $tm->validate();


if ($validation['error']) {
  http_response_code(401);

  // tell the user access denied  & show error message
  echo json_encode(array(
    "message" => "Access denied.",
    "error" => $validation['error']
  ));
} else {

  $user->id = isset($data->id) ? $data->id : "";
  $user_exists = $user->existById();

  if ($user_exists) {

    // set user property values
    if (isset($data->username)) {
      $user->username = $data->username;
    }
    if (isset($data->firstname)) {
      $user->firstname = $data->firstname;
    }
    if (isset($data->lastname)) {
      $user->lastname = $data->lastname;
    }
    if (isset($data->role)) {
      $user->role = $data->role;
    }
    if (isset($data->email)) {
      $user->email = $data->email;
    }
    if (isset($data->phone)) {
      $user->phone = $data->phone;
    }
 

    //$user->id = $validation['data']->id;

    // update the user record
    if ($user->update()) {
      //  $jwt = $tm->getTokenByUser($user);
      // set response code
      http_response_code(200);

      // response in json format
      echo json_encode(
        array(
          "message" => "User was updated.",
          "data" => array(
            "id" => $user->id,
            "username" => $user->username,
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "role" => $user->role,
            "email" => $user->email,
            "phone" => $user->phone
          )
        )
      );
    } else {
      // message if 
      // set response code
      http_response_code(401);

      // show error message
      echo json_encode(array("message" => "Unabled to update user."));
    }
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "User does not exist."));
  }
}
