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
include_once '../objects/client.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$client = new Client($db);

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
    "error" => $validation['error']
  ));
} else {
  $client->id = isset($_GET['id']) ? $_GET['id'] : "";
  if ($client->setById()) {
    http_response_code(200);

    // response in json format
    echo json_encode(
      array(
        "message" => "Client founded.",
        "data" => array(
          "id" => $client->id,
          "name" => $client->name,
          "dni" => $client->dni,
          "birthday" => $client->birthday,
          "phone_contact" => $client->phone_contact,
          "phone" => $client->phone
        )
      )
    );
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Client does not exist."));
  }
}
