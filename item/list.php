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
include_once '../objects/item.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$item = new Item($db);

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
  $item->page = isset($_GET['page']) ? $_GET['page'] : 0;
  $item->pagination = isset($_GET['pagination']) ? $_GET['pagination'] : 10;
  if (isset($_GET['orderBy'])) {
    $item->orderBy = $_GET['orderBy'];
  }
  if (isset($_GET['orderTo'])) {
    $item->orderTo = $_GET['orderTo'];
  }

  if (isset($_GET['search'])) {
    $item->search = $_GET['search'];
  }

  if ($item->list()) {
    echo json_encode($item->collection);
  } else {
    echo json_encode(array(
      "message" => "ERROR",
      //"error" => $validation['error']
    ));
  }
}
