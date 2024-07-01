<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Accesstoken, Clientappid");
header("Access-Control-Allow-Methods: GET");
header("Allow: GET");
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
  $user->id = isset($_GET['id']) ? $_GET['id'] : "";
  if ($user->setById()) {
    http_response_code(200);

    // response in json format
    echo json_encode(
      array(
        "message" => "User founded.",
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
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "User does not exist."));
  }
}
