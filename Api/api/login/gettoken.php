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


//set database 
$database = new Database();
$db = $database->getConnection();

if (is_null($db)) {
    http_response_code(200);
    exit(json_encode(
        array(
            'result' => 0,
            'message' => 'Login failed',
            'error' => 'Connection database failed'
        )
    ));
}

if(!isset($_GET['email'])) {
    http_response_code(200);
    exit(json_encode(
        array(
            'result' => 0,
            'message' => 'Login failed',
            'error' => 'Email not set'
        )
    ));
}

if(!isset($_GET['password'])) {
    http_response_code(200);
    exit(json_encode(
        array(
            'result' => 0,
            'message' => 'Login failed',
            'error' => 'Password not set'
        )
    ));
}


$user = new User($db);
$user->email = $_GET['email'];
$email_exist = $user->emailExist();



if(!empty($email_exist) && $email_exist['exist'] === "true" && password_verify($_GET[ 'password' ], $user->password)) {
    $user_infos = array(
        'id' => $user->id,
        'firstname' => $user->firstname,
        'surname' => $user->surname,
        'email' => $user->email,
        'permission' => $user->permission
    );

    $token = TokenUtils::CreateToken($user_infos[ 'id' ], $user_infos);

    if ($token[ 'result' ] === 1) {
        http_response_code(200);
        $token[ 'message' ] = 'Login success';
        exit( json_encode(
            $token
        ));
    }
    else {
        http_response_code(200);
        exit(json_encode(
            array(
                'result' => 0,
                'message' => 'Login failed',
                'error' => 'Error token generation'
            )
        ));
    }
    
} else {
    http_response_code(200);
    exit(json_encode(
        array(
            'result' => 0,
            'message' => 'Login failed',
            'error' => 'Wrong email or password'
        )
    ));
}