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


if (isset($parts[1])) {
    if ($token->claims()->get('permission') != 0) {
        http_response_code(200);
        exit(json_encode([ 'result' => 0, 'message' => 'Insufficient rights' ]));
    }

    $id = $parts[1];

    if (!is_numeric($id)) {
        http_response_code(200);
        exit(json_encode([ 'result' => 0, 'message' => 'Wrong request arguments, need an id' ]));
    }
    
} else {
    $id = $token->claims()->get('id');

    if (!isset($id)) {
        http_response_code(200);
        exit(json_encode([ 'result' => 0, 'message' => 'User id incorrect in token' ]));
    }
}

//set database 
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$user->id = $id;


if (isset($_POST[ 'firstname' ])) {
    $user->firstname = $_POST['firstname'];
}

if (isset($_POST[ 'surname' ])) {
    $user->surname = $_POST['surname'];
}

if (isset($_POST[ 'password' ])) {
    $user->password = $_POST['password'];
}

if (isset($_POST[ 'permission' ])) {
    $user->permission = $_POST['permission'];
}

$response = $user->save();

if ($response['success'] === "false") {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'Error in update' ]));
} else if ($response['success'] === "true") {
    http_response_code(200);
    exit(json_encode([ 'result' => 1, 'message' => 'Updated' ]));
}
