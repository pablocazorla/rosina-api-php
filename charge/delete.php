<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: *");

// files needed to connect to database
include_once '../config/database.php';
include_once '../config/tokenManager.php';
include_once '../objects/charge.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$charge = new Charge($db);

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
  $charge->id = isset($data->id) ? $data->id : "";
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
