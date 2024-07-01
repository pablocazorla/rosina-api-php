<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Accesstoken, Clientappid");
header("Access-Control-Allow-Methods: PUT");
header("Allow: PUT");
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

  $turn->id = isset($data->id) ? $data->id : "";
  $turn_exists = $turn->existById();

  if ($turn_exists) {

    // set user property values
    if (isset($data->client_id)) {
      $turn->client_id = $data->client_id;
    }
    if (isset($data->client_name)) {
      $turn->client_name = $data->client_name;
    }
    if (isset($data->description)) {
      $turn->description = $data->description;
    }
    if (isset($data->createdBy)) {
      $turn->createdBy = $data->createdBy;
    }
    if (isset($data->cost)) {
      $turn->cost = $data->cost;
    }
    if (isset($data->item_ids)) {
      $turn->item_ids = $data->item_ids;
    }
    if (isset($data->day)) {
      $turn->day = $data->day;
    }
    if (isset($data->startTime)) {
      $visit->startTime = $data->startTime;
    }
    if (isset($data->duration)) {
      $turn->duration = $data->duration;
    }
    if (isset($data->location)) {
      $turn->location = $data->location;
    }
    if (isset($data->status)) {
      $turn->status = $data->status;
    }

    //$turn->id = $validation['data']->id;

    // update the user record
    if ($turn->update()) {
      //  $jwt = $tm->getTokenByUser($turn);
      // set response code
      http_response_code(200);

      // response in json format
      echo json_encode(
        array(
          "message" => "Turn was updated.",
          "data" => array(
            "id" => $turn->id,
            "client_id" => $turn->client_id,
            "client_name" => $turn->client_name,
            "description" => $turn->description,
            "createdBy" => $turn->createdBy,
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
      // message if 
      // set response code
      http_response_code(401);

      // show error message
      echo json_encode(array("message" => "Unabled to update turn."));
    }
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Turn does not exist."));
  }
}
