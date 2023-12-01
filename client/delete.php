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
include_once '../objects/client.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$client = new Client($db);

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
  $client->id = isset($data->id) ? $data->id : "";
  $client_exists = $client->existById();
  if ($client_exists && $client->delete()) {
    http_response_code(200);

    // response in json format
    echo json_encode(array(
      "message" => "Client was deleted.",
      "id" => $client->id
    ));
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Client does not exist."));
  }
}
