<?php 
if($method === 'OPTIONS')
{
    header("Access-Control-Allow-Origin: * ");
    header("Access-Control-Allow-Headers: access-control-allow-origin, content-type, authorization");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    
    exit;
}
else if($method === 'POST')
{
    header("Access-Control-Allow-Origin: * ");
    header("Access-Control-Allow-Headers: access-control-allow-origin, content-type, authorization");
    header("Content-Type: application/json; charset=UTF-8");

}
else
{
    header("Content-Type: application/json; charset=UTF-8");
    exit(json_encode([ 'result' => 0, 'message' => 'Bad query method' ]));
}



//files to include
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/user.php';
include_once '../objects/tokenUtils.php';


if (!isset($_SERVER['HTTP_AUTHORIZATION']))
{
    http_response_code(400);
    exit(json_encode([ 'result' => 0, 'message' => 'No token bearer' ]));
}

$token = substr($_SERVER['HTTP_AUTHORIZATION'],8, strlen($_SERVER['HTTP_AUTHORIZATION'])-9);

$result = TokenUtils::ValidateToken($token);

if ($result['result'] === 0) {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'Not valid token' ]));
}

$token = TokenUtils::ParseToken($token);

if ($token->claims()->get('permission') != 0) {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'Insufficient rights' ]));
}

if (isset($_POST[ 'firstname' ])
    && isset($_POST[ 'surname' ])
    && isset($_POST[ 'email' ])
    && isset($_POST[ 'password' ])
    && isset($_POST[ 'permission' ])
    ) {
    //todo validate all 
    //set database 
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $user->firstname = $_POST[ 'firstname' ];
    $user->surname = $_POST[ 'surname' ];
    $user->email = $_POST[ 'email' ];
    $user->password = $_POST[ 'password' ];
    $user->permission = $_POST[ 'permission' ];
    $response = $user->save();
    if ($response[ 'success' ] === "false") {
        http_response_code(200);
        exit(json_encode([ 'result' => 0, 'message' => 'Email may already exist' ]));
    } elseif ($response[ 'success' ] === "true") {
        http_response_code(200);
        exit(json_encode([ 'result' => 0, 'message' => 'User added', 'data' => $response[ 'added' ] ]));
    }
} else {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'FormData not complete' ]));
}