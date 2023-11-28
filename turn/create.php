<?php
// required headers
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,client_app_id");


// files needed to connect to database
include_once '../config/database.php';
include_once '../config/tokenManager.php';
include_once '../objects/turn.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate product object
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

  // set product property values
  $turn->client_id = isset($data->client_id) ? $data->client_id : "";
  $turn->client_name = isset($data->client_name) ? $data->client_name : "";
  $turn->description = isset($data->description) ? $data->description : "";
  $turn->formula = isset($data->formula) ? $data->formula : "";
  $turn->cost = isset($data->cost) ? $data->cost : "";
  $turn->item_ids = isset($data->item_ids) ? $data->item_ids : "";
  $turn->day = isset($data->day) ? $data->day : "";
  $turn->startTime = isset($data->startTime) ? $data->startTime : "";
  $turn->duration = isset($data->duration) ? $data->duration : "";
  $turn->location = isset($data->location) ? $data->location : "";
  $turn->status = isset($data->status) ? $data->status : "";

  // create the client
  if (
    !empty($turn->client_id) &&
    !empty($turn->day) &&
    !empty($turn->startTime) &&
    !empty($turn->duration) &&
    $turn->create()
  ) {

    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(
      array(
        "message" => "Turn was created.",
        "data" => array(
          "id" => $turn->id,
          "client_id" => $turn->client_id,
          "client_name" => $turn->client_name,
          "formula" => $turn->formula,
          "cost" => $turn->cost,
          "item_ids" => $turn->item_ids,
          "description" => $turn->description,
          "day" => $turn->day,
          "startTime" => $turn->startTime,
          "duration" => $turn->duration,
          "location" => $turn->location,
          "status" => $turn->status
        )
      )
    );
  } else {
    // message if unable to create user
    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create turn."));
  }
}
