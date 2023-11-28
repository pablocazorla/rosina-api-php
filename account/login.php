<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Origin, X-Api-Key, X-Requested-With, Content-Type, Accept, Authorization, Client_app_id");
// header("HTTP/1.1 200 OK");

// files needed to connect to database
include_once '../config/database.php';
include_once '../config/tokenManager.php';
include_once '../objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$user = new User($db);
// get posted data
$data = json_decode(file_get_contents("php://input"));

// set user property values
$user->username = isset($data->username) ? $data->username : "";
$user_exists = $user->setByUsername();

$password = isset($data->password) ? $data->password : "";

// generate token manager
$tm = new TokenManager();

// generate jwt will be here
// check if username exists and if password is correct
if ($user_exists && password_verify($password, $user->password)) {

    // set response code
    http_response_code(200);

    // generate jwt
    $jwt = $tm->getTokenByUser($user);

    echo json_encode(
        array(
            "message" => "Successful login.",
            "user" => array(
                "firstname" => $user->firstname,
                "lastname" => $user->lastname,
                "username" => $user->username
            ),
            "access_token" => $jwt
        )
    );
} else {
    // login failed
    // set response code
    http_response_code(401);

    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}
