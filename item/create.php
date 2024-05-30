<?php
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
include_once '../objects/item.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate product object
$item = new Item($db);

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
  $item->name = isset($data->name) ? $data->name : "";
  $item->description = isset($data->description) ? $data->description : "";
  $item->categories = isset($data->categories) ? $data->categories : "";
  $item->type = isset($data->type) ? $data->type : "";
  $item->cost = isset($data->cost) ? $data->cost : "";

  // create the item
  if (
    !empty($item->name) &&
    !empty($item->type) &&
    $item->create()
  ) {

    // set response code
    http_response_code(200);

    // display message: user was created
    echo json_encode(
      array(
        "message" => "Item was created.",
        "data" => array(
          "id" => $item->id,
          "name" => $item->name,
          "description" => $item->description,
          "categories" => $item->categories,
          "type" => $item->type,
          "cost" => $item->cost
        )
      )
    );
  } else {
    // message if unable to create user
    // set response code
    http_response_code(400);

    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create item."));
  }
}
