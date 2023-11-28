<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,client_app_id");

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
  if ($charge->setById()) {
    http_response_code(200);

    // response in json format
    echo json_encode(
      array(
        "message" => "Charge founded.",
        "data" => array(
          "id" => $charge->id,
          "client_id" => $charge->client_id,
          "client_name" => $charge->client_name,
          "turn_id" => $charge->turn_id,
          "status" => $charge->status,
          "cost" => $charge->cost,
          "date_created" => $charge->date_created
        )
      )
    );
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Charge does not exist."));
  }
}
