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
include_once '../objects/turn.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$turn = new Turn($db);

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
  $turn->id = isset($_GET['id']) ? $_GET['id'] : "";
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
