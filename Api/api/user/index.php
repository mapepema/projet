<?php
	
$action = null;
	
if(isset($_GET['a']))
	$action = $_GET['a'];
	
$parts = explode('/', $action);

$method = $_SERVER['REQUEST_METHOD'];
	
switch($parts[0])
{
    case 'get':
        include_once 'get.php';
        break;

    case 'create':
        include_once 'create.php';
        break;

    case 'delete':
        include_once 'delete.php';
        break;

    case 'list':
        include_once 'list.php';
        break;
    
    case 'update':
        include_once 'update.php';
        break;
			            
    default:
		header('Content-Type: application/json;charset=utf-8');
		exit(json_encode([ 'result' => 0, 'message' => 'From /api/user: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . ' is not a valid query' ]));
		break;
}