<?php
	
$action = null;
	
if(isset($_GET['a']))
	$action = $_GET['a'];
	
$parts = explode('/', $action);
$method = $_SERVER['REQUEST_METHOD'];
	
switch($parts[0])
{
    case 'gettoken':
		include_once 'gettoken.php';
        break;
			
	case 'validatetoken':
		include_once 'validatetoken.php';
		break;
            
    default:
		header('Content-Type: application/json;charset=utf-8');
		exit(json_encode([ 'result' => 0, 'message' => 'From /api/login: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . ' is not a valid query' ]));
		break;
}