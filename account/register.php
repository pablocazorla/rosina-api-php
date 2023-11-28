<?php
// required headers
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,client_app_id");


// files needed to connect to database
include_once '../config/database.php';
include_once '../config/config.php';
include_once '../objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate product object
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// set product property values
$user->username = isset($data->username) ? $data->username : "";
$user->firstname = isset($data->firstname) ? $data->firstname : "";
$user->lastname = isset($data->lastname) ? $data->lastname : "";
$user->email = isset($data->email) ? $data->email : "";
$user->phone = isset($data->phone) ? $data->phone : "";
$user->password = isset($data->password) ? $data->password : "";
$user->role = isset($data->role) ? $data->role : "USER";
//
$client_app_id = isset($data->client_app_id) ? $data->client_app_id : "";

// create the user
if (
  $client_app_id === $client_id_app &&
  !empty($user->username) &&
  !empty($user->firstname) &&
  !empty($user->lastname) &&
  !empty($user->email) &&
  !empty($user->phone) &&
  !empty($user->password) &&
  $user->create()
) {

  // set response code
  http_response_code(200);

  // display message: user was created
  echo json_encode(
    array(
      "message" => "User was created.",
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
}

// message if unable to create user
else {

  // set response code
  http_response_code(400);

  // display message: unable to create user
  echo json_encode(array("message" => "Unable to create user."));
}
