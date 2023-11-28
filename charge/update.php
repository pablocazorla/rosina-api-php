<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
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

  $charge->id = isset($data->id) ? $data->id : "";
  $item_exists = $charge->existById();

  if ($item_exists) {

    // set user property values
    if (isset($data->client_id)) {
      $charge->client_id = $data->client_id;
    }
    if (isset($data->client_name)) {
      $charge->client_name = $data->client_name;
    }
    if (isset($data->turn_id)) {
      $charge->turn_id = $data->turn_id;
    }
    if (isset($data->status)) {
      $charge->status = $data->status;
    }
    if (isset($data->cost)) {
      $charge->cost = $data->cost;
    }

    //$charge->id = $validation['data']->id;

    // update the user record
    if ($charge->update()) {
      //  $jwt = $tm->getTokenByUser($charge);
      // set response code
      http_response_code(200);

      // response in json format
      echo json_encode(
        array(
          "message" => "Charge was updated.",
          "data" => array(
            "id" => $charge->id,
            "client_id" => $charge->client_id,
            "client_name" => $charge->client_name,
            "turn_id" => $charge->turn_id,
            "cost" => $charge->cost,
            "status" => $charge->status,
            "date_created" => $charge->date_created
          )
        )
      );
    } else {
      // message if 
      // set response code
      http_response_code(401);

      // show error message
      echo json_encode(array("message" => "Unabled to update charge."));
    }
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Charge does not exist."));
  }
}
