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
include_once '../objects/turn.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$turn = new Turn($db);

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
  $turn->id = isset($data->id) ? $data->id : "";
  if ($turn->setById()) {
    http_response_code(200);

    // response in json format
    echo json_encode(
      array(
        "message" => "Turn founded.",
        "data" => array(
          "id" => $turn->id,
          "client_id" => $turn->client_id,
          "client_name" => $turn->client_name,
          "description" => $turn->description,
          "formula" => $turn->formula,
          "cost" => $turn->cost,
          "item_ids" => $turn->item_ids,
          "day" => $turn->day,
          "startTime" => $turn->startTime,
          "duration" => $turn->duration,
          "location" => $turn->location,
          "status" => $turn->status
        )
      )
    );
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Turn does not exist."));
  }
}
