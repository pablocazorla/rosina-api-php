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
  $charge->page = isset($data->page) ? $data->page : 0;
  $charge->pagination = isset($data->pagination) ? $data->pagination : 10;
  if (isset($data->orderBy)) {
    $charge->orderBy = $data->orderBy;
  }
  if (isset($data->orderTo)) {
    $charge->orderTo = $data->orderTo;
  }

  if (isset($data->search)) {
    $charge->search = $data->search;
  }

  if ($charge->list()) {
    echo json_encode($charge->collection);
  } else {
    echo json_encode(array(
      "message" => "ERROR",
      //"error" => $validation['error']
    ));
  }
}
