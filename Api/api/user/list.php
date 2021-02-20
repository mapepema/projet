<?php 
if($method === 'OPTIONS')
{
    header("Access-Control-Allow-Origin: * ");
    header("Access-Control-Allow-Headers: access-control-allow-origin, content-type, authorization");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    
    exit;
}
else if($method === 'GET')
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
    exit(json_encode([ 'result' => 0, 'message' => 'Insufficient right' ]));
}
//set database 
$database = new Database();
$db = $database->getConnection();

$response = User::findAll($db);

if ($response[ 'success'] === "true") {
    http_response_code(200);
    exit(json_encode([ 'result' => 1, 'message' => 'All accounts', 'data' => $response[ 'data' ] ]));
} else {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'Error in db' ]));
}



