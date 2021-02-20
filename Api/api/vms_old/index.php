<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// file to include to connect database
include_once '../config/database.php';

//file to include to use animal 
include_once './user_controller.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

 
if ($uri[2] !== "user"){
    header("HTTPS/1.1 404 Not Found");
    exit();
}

$userId = null;
if (isset($uri[3])) {
    $userId = (int) $uri[3];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

//$controller = new AnimalController($db, $requestMethod, $animalId);
//$controller->processRequest();
