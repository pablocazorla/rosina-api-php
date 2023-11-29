<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: *");

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
/* $data = json_decode(file_get_contents("php://input")); */

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
  if (isset($_GET['search'])) {
    $turn->search = $_GET['search'];
  }
  if (isset($_GET['from_day'])) {
    $turn->from_day = $_GET['from_day'];
  }
  if (isset($_GET['to_day'])) {
    $turn->to_day = $_GET['to_day'];
  }

  if ($turn->list()) {
    echo json_encode($turn->collection);
  } else {
    echo json_encode(array(
      "message" => "ERROR",
      //"error" => $validation['error']
    ));
  }
}
