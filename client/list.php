<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization");
header("Access-Control-Allow-Headers: X-Requested-With");

// files needed to connect to database
include_once '../config/database.php';
include_once '../config/tokenManager.php';
include_once '../objects/client.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$client = new Client($db);


$headers = apache_request_headers();
if (isset($headers['pablo'])) {
  echo $headers['pablo'];
}

// get posted data
//$data = json_decode(file_get_contents("php://input"));

// generate token manager
$tm = new TokenManager();

$validation = $tm->validate();

if ($validation['error']) {
  http_response_code(401);

  // tell the user access denied  & show error message
  echo json_encode(array(
    "message" => "Access denied.",
    "error" => $validation['error'],
    "aaa" => $validation['aaa']
  ));
} else {

 /*
  $orderBy = $_GET['orderBy'];
  $orderTo = $_GET['orderTo'];
  $search = $_GET['search'];
*/


  if (isset($_GET['page'])) {
    $client->page = $_GET['page'];
  }
  if (isset($_GET['pagination'])) {
    $client->pagination = $_GET['pagination'];
  }
  
  if (isset($_GET['orderBy'])) {
    $client->orderBy = $_GET['orderBy'];
  }
  if (isset($_GET['orderTo'])) {
    $client->orderTo = $_GET['orderTo'];
  }

  if (isset($_GET['search'])) {
    $client->search = $_GET['search'];
  }
  

  if ($client->list()) {
    echo json_encode($client->collection);
  } else {
    echo json_encode(array(
      "message" => "ERROR",
      //"error" => $validation['error']
    ));
  }
}
