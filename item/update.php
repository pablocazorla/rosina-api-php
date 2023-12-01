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
include_once '../objects/item.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
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

  $item->id = isset($data->id) ? $data->id : "";
  $item_exists = $item->existById();

  if ($item_exists) {

    // set user property values
    if (isset($data->name)) {
      $item->name = $data->name;
    }
    if (isset($data->description)) {
      $item->description = $data->description;
    }
    if (isset($data->type)) {
      $item->type = $data->type;
    }
    if (isset($data->cost)) {
      $item->cost = $data->cost;
    }

    //$item->id = $validation['data']->id;

    // update the user record
    if ($item->update()) {
      //  $jwt = $tm->getTokenByUser($item);
      // set response code
      http_response_code(200);

      // response in json format
      echo json_encode(
        array(
          "message" => "Item was updated.",
          "data" => array(
            "id" => $item->id,
            "name" => $item->name,
            "description" => $item->description,
            "cost" => $item->cost,
            "type" => $item->type,
            "created" => $item->created
          )
        )
      );
    } else {
      // message if 
      // set response code
      http_response_code(401);

      // show error message
      echo json_encode(array("message" => "Unabled to update item."));
    }
  } else {
    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array("message" => "Item does not exist."));
  }
}
