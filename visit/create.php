<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,client_app_id");


// files needed to connect to database
include_once '../config/database.php';
include_once '../config/tokenManager.php';
include_once '../objects/visit.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate product object
$visit = new Visit($db);

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
  $visit->client_id = isset($data->client_id) ? $data->client_id : "";
  $visit->cost = isset($data->cost) ? $data->cost : "";
  $visit->formula = isset($data->formula) ? $data->formula : "";
  $visit->item_ids = isset($data->item_ids) ? $data->item_ids : "";
  $visit->description = isset($data->description) ? $data->description : "";
  $visit->day = isset($data->day) ? $data->day : "";
  $visit->startTime = isset($data->startTime) ? $data->startTime : "";
  $visit->duration = isset($data->duration) ? $data->duration : "";

  // create the client
  if (
    !empty($visit->client_id) &&
    !empty($visit->day) &&
    !empty($visit->startTime) &&
    !empty($visit->duration) &&
    $visit->create()
  ) {

    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(
      array(
        "message" => "Visit was created.",
        "data" => array(
          "id" => $visit->id,
          "client_id" => $visit->client_id,
          "cost" => $visit->cost,
          "formula" => $visit->formula,
          "item_ids" => $visit->item_ids,
          "description" => $visit->description,
          "day" => $visit->day,
          "startTime" => $visit->startTime,
          "duration" => $visit->duration
        )
      )
    );
  } else {
    // message if unable to create user
    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create visit."));
  }
}
