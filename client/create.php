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
include_once '../objects/client.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate product object
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

  // set product property values
  $client->name = isset($data->name) ? $data->name : "";
  $client->dni = isset($data->dni) ? $data->dni : 0;
  $client->birthday = isset($data->birthday) ? $data->birthday : "0000-00-00";
  $client->phone_contact = isset($data->phone_contact) ? $data->phone_contact : "";
  $client->phone = isset($data->phone) ? $data->phone : "";

  // create the client
  if (
    !empty($client->name) &&
    $client->create()
  ) {

    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(
      array(
        "message" => "Client was created.",
        "data" => array(
          "id" => $client->id,
          "name" => $client->name,
          "dni" => $client->dni,
          "birthday" => $client->birthday,
          "phone" => $client->phone,
          "phone_contact" => $client->phone_contact
        )
      )
    );
  } else {
    // message if unable to create user
    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create client."));
  }
}
