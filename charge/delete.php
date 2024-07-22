<?php
// required headers
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Accesstoken, Clientappid");
header("Access-Control-Allow-Methods: DELETE");
header("Allow: DELETE");
header("Content-Type: application/json; charset=UTF-8");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}
// files needed to connect to database
include_once '../config/database.php';
include_once '../config/tokenManager.php';
include_once '../objects/charge.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$charge = new Charge($db);


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
  $charge->id = isset($_GET['id']) ? $_GET['id'] : "";
  $charge_exists = $charge->existById();
  if ($charge_exists && $charge->delete()) {
    http_response_code(200);

    // response in json format
    echo json_encode(array(
      "message" => "Charge was deleted.",
      "id" => $charge->id
    ));
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Charge does not exist."));
  }
}
