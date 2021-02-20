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
include_once '../objects/tokenUtils.php';
include_once '../objects/user.php';
include_once '../objects/vm.php';
include_once '../objects/instance_volume.php';
include_once '../objects/instance_security_group.php';
include_once '../objects/instance_server.php';
include_once '../objects/inbound_rule.php';
include_once '../objects/outbound_rule.php';

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

if (!isset($parts[1])){
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'Wrong request arguments, need an id' ]));
}

$id_vm = $parts[1];

if (!is_numeric($id_vm)) {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'Wrong request arguments, need an id' ]));
}

$id = $token->claims()->get('id');

if (!isset($id)) {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'id incorrect in token' ]));
}

$database = new Database();
$db = $database->getConnection();
$vm = new VirtualMachine($db);

$vm->id = $id_vm;

$response = $vm->findWithId();

if ($response['success'] === "true" && isset($response['data']['user_id'])) {
    if ($response['data']['user_id'] !== $id) {
        if ($token->claims()->get('permission') != 0) {
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'Insufficient rights' ]));
        }
    }
} else {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'Error vm search' ]));
}


if ($response['success'] === "true") {
    $arr = [];
    foreach ($response['data'] as &$virtualmachine) {
        $arr["id_virtual_machine"] = $virtualmachine['id_virtual_machine'];
        $arr["user_id"] = $virtualmachine['user_id'];

        //instance volume
        $iv = new InstanceVolume($db);
        $iv->id = $virtualmachine['instance_volume_id'];
        $res = $iv->findWithId();
        if ($res['success'] === "false") {
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'bad response instance volume' ]));
        }
        $arr["instance_volume"] = $res['data'];

        //instance security
        $is = new SecurityGroup($db);
        $is->id = $virtualmachine['instance_security_group_id'];
        $res = $is->findWithId();
        if ($res['success'] === "false") {
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'bad response instance volume' ]));
        }
        $arr["instance_security_group"] = $res['data'];

        //instance serveur
        $iserv = new InstanceServer($db);
        $iserv->id = $virtualmachine['instance_server_id'];
        $res = $iserv->findWithId();
        if ($res['success'] === "false") {
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'bad response instance volume' ]));
        }
        $arr["instance_server"] = $res['data'];

        //inboud_rules 
        $rules = [];
        $inbound_rules = explode("-", $virtualmachine['inbound_rule_ids']);
        foreach($inbound_rules as &$rule) {
            if (is_numeric($rule)) {
                $ir = new InboundRule($db);
                $ir->id = $rule;
                $res = $ir->findWithId();
                if ($res['success'] === "false") {
                    http_response_code(200);
                    exit(json_encode([ 'result' => 0, 'message' => 'bad response inboundrule' ]));
                }
                $rules[] = $res['data'];
            }
        }
        $arr["inbound_rules"] = $rules;

        //outboud_rules
        $rules = []; 
        $outbound_rules = explode("-", $virtualmachine['outbound_rule_ids']);
        foreach($outbound_rules as &$rule) {
            if (is_numeric($rule)) {
                $or = new OutboundRule($db);
                $or->id = $rule;
                $res = $or->findWithId();
                if ($res['success'] === "false") {
                    http_response_code(200);
                    exit(json_encode([ 'result' => 0, 'message' => 'bad response outboundrule' ]));
                }
                $rules[] = $res['data'];
            }
        }
        $arr["outbound_rules"] = $rules;
        
    }

    http_response_code(200);
    exit(json_encode([ 'result' => 1, 'message' => 'User vm', 'data' => $arr ]));

} else {

    http_response_code(200);
    exit(json_encode([ 'result' => 1, 'message' => 'Error finding user virtual machine']));
    
}

