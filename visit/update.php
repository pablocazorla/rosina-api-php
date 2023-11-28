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
include_once '../objects/visit.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$visit = new Visit($db);

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

  $visit->id = isset($data->id) ? $data->id : "";
  $visit_exists = $visit->existById();

  if ($visit_exists) {

    // set user property values
    if (isset($data->client_id)) {
      $visit->client_id = $data->client_id;
    }
    if (isset($data->cost)) {
      $visit->cost = $data->cost;
    }
    if (isset($data->formula)) {
      $visit->formula = $data->formula;
    }
    if (isset($data->item_ids)) {
      $visit->item_ids = $data->item_ids;
    }
    if (isset($data->description)) {
      $visit->description = $data->description;
    }
    if (isset($data->day)) {
      $visit->day = $data->day;
    }
    if (isset($data->startTime)) {
      $visit->startTime = $data->startTime;
    }
    if (isset($data->duration)) {
      $visit->duration = $data->duration;
    }

    //$visit->id = $validation['data']->id;

    // update the user record
    if ($visit->update()) {
      //  $jwt = $tm->getTokenByUser($visit);
      // set response code
      http_response_code(200);

      // response in json format
      echo json_encode(
        array(
          "message" => "Visit was updated.",
          "data" => array(
            "id" => $visit->id,
            "client_id" => $visit->client_id,
            "cost" => $visit->cost,
            "formula" => $visit->formula,
            "item_ids" => $visit->item_ids,
            "description" => $visit->description,
            "day" => $visit->day,
            "startTime" => $visit->startTime,
            "duration" => $visit->duration
          )
        )
      );
    } else {
      // message if 
      // set response code
      http_response_code(401);

      // show error message
      echo json_encode(array("message" => "Unabled to update visit."));
    }
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Turn does not exist."));
  }
}
