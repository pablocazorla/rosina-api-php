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
include_once '../objects/visit.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
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
  if (isset($data->search)) {
    $visit->search = $data->search;
  }
  if (isset($data->from_day)) {
    $visit->from_day = $data->from_day;
  }
  if (isset($data->to_day)) {
    $visit->to_day = $data->to_day;
  }
  if (isset($data->by_client_id)) {
    $visit->by_client_id = $data->by_client_id;
  }

  if ($visit->list()) {
    echo json_encode($visit->collection);
  } else {
    echo json_encode(array(
      "message" => "ERROR",
      //"error" => $validation['error']
    ));
  }
}
