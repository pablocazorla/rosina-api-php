<?php
// required headers
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}
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
  $charge->page = isset($_GET['page']) ? $_GET['page'] : 0;
  $charge->pagination = isset($_GET['pagination']) ? $_GET['pagination'] : 10;
  if (isset($_GET['orderBy'])) {
    $charge->orderBy = $_GET['orderBy'];
  }
  if (isset($_GET['orderTo'])) {
    $charge->orderTo = $_GET['orderTo'];
  }

  if (isset($_GET['search'])) {
    $charge->search = $_GET['search'];
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
