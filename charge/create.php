<?php
// required headers
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Accesstoken, Clientappid");
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");
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

// instantiate product object
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

  // set product property values
  $charge->client_id = isset($data->client_id) ? $data->client_id : "";
  $charge->client_name = isset($data->client_name) ? $data->client_name : "";
  $charge->description = isset($data->description) ? $data->description : "";
  $charge->turn_id = isset($data->turn_id) ? $data->turn_id : "";
  $charge->status = isset($data->status) ? $data->status : "";
  $charge->cost = isset($data->cost) ? $data->cost : "";
  $charge->date_created = isset($data->date_created) ? $data->date_created : "";

  // create the charge
  if (
    !empty($charge->cost) &&
    $charge->create()
  ) {

    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(
      array(
        "message" => "Charge was created.",
        "data" => array(
          "id" => $charge->id,
          "client_id" => $charge->client_id,
          "client_name" => $charge->client_name,
          "description" => $charge->description,
          "turn_id" => $charge->turn_id,
          "status" => $charge->status,
          "cost" => $charge->cost,
          "date_created" => $charge->date_created,
        )
      )
    );
  } else {
    // message if unable to create user
    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create charge."));
  }
}
