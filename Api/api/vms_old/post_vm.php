<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once('../config/database.php');
include_once('../objects/vm.php');


//get the post
$data = json_decode(file_get_contents("php://input"));

//to do SANATIZEEEEEEEEEEEEEE
//SANNNNNNNNNNNNNNNNNNNAAAAAAAAAAAAAAAAAAAAAAAAAAATTTTTTTTTTTTTTTTTTTTTTTTIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIZZZZZZZZZZZZZZZZZZZZZZZZZZZZEEEEEEEEEEEEEEEEEEEEEEEEEEEE
$volume_size = $data->volume_size;
$volume_type = $data->volume_type;
$security_group_inbound_policy = $data->inbound_policy;
$security_group_outbound_policy = $data->outbound_policy;
$inbound_rules = $data->inbound_rules;
$outbound_rules = $data->outbound_rules;
$image_type = $data->image_type;
$image_image = $data->image_image;
$image_tags = $data->image_tags;

$database = new Database();
$db = $database->getConnection();

$vm = new VM($db,$volume_size,$volume_type,
$security_group_inbound_policy,$security_group_outbound_policy,
$inbound_rules,$outbound_rules,$image_type,$image_image,$image_tags);

if($vm->createVm()){$response['msg'] = "VM add to the bdd";http_response_code(200);} 
else {$response['msg'] = "Error";http_response_code(400);}
echo json_encode($response);
exit(0);