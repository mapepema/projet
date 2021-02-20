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
include_once '../objects/tokenUtils.php';
include_once '../objects/user.php';
include_once '../objects/vm.php';
include_once '../objects/instance_volume.php';
include_once '../objects/instance_security_group.php';
include_once '../objects/instance_server.php';
include_once '../objects/inbound_rule.php';
include_once '../objects/outbound_rule.php';
include_once '../objects/vm_state.php';

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

$user_id = $token->claims()->get('id');

if (!isset($user_id)) {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'id incorrect in token' ]));
}

if (isset($_POST[ 'size_in_gb' ])
    && isset($_POST[ 'instance_volume_type' ])
    && isset($_POST[ 'inbound_default_policy' ])
    && isset($_POST[ 'outbound_default_policy' ])
    && isset($_POST[ 'all_inbound_rules' ])
    && isset($_POST[ 'all_outbound_rules' ])
    && isset($_POST[ 'instance_server_type' ])
    && isset($_POST[ 'instance_server_image' ])
    && isset($_POST[ 'instance_server_tags' ])
    ) {
        // set database 
        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();

        //-------------------INSTANCE VOLUME----------------------//

        $iv = new InstanceVolume($db);
        $iv->size_in_gb = $_POST[ 'size_in_gb' ];
        $iv->type = $_POST[ 'instance_volume_type' ];

        // search if exists 
        $res = $iv->findSizeType();

        // if something goes wrong rollback and exit
        if ($res[ 'success' ] === "false") {
            $db->rollBack();
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'Error find InstanceVolume' ]));
        }

        // if doesnt exist create else get the id
        if ($res[ 'data' ] === null) {
            $res = $iv->save();
            //if save error rollback and exit
            if ($res[ 'success' ] === "false") {
                $db->rollBack();
                http_response_code(200);
                exit(json_encode([ 'result' => 0, 'message' => 'Error find InstanceVolume' ]));
            } else {
                $id_instance_volume = $res[ 'added' ];
            }
        } else {
            $id_instance_volume = $res[ 'data' ][ 'id_instance_volume' ];
        }

        //-------------------INSTANCE SECURITY GROUP----------------------//

        $sg = new SecurityGroup($db);
        $sg->inbound_default_policy = $_POST[ 'inbound_default_policy' ];
        $sg->outbound_default_policy = $_POST[ 'outbound_default_policy' ];

        // search if exists 
        $res = $sg->findPolicy();

        // if something goes wrong rollback and exit
        if ($res[ 'success' ] === "false") {
            $db->rollBack();
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'Error find InstanceVolume' ]));
        }

        // if doesnt exist create else get the id
        if ($res[ 'data' ] === null) {
            $res = $sg->save();
            //if save error rollback and exit
            if ($res[ 'success' ] === "false") {
                $db->rollBack();
                http_response_code(200);
                exit(json_encode([ 'result' => 0, 'message' => 'Error find SecurityGroup' ]));
            } else {
                $id_instance_security_group = $res[ 'added' ];
            }
        } else {
            $id_instance_security_group = $res[ 'data' ][ 'id_instance_security_group' ];
        }

        //-------------------INBOUND RULES----------------------//
        $rules = json_decode($_POST[ 'all_inbound_rules' ]);
        foreach($rules as &$rule) {
            $ir = new InboundRule($db);
            $ir->action = $rule->action;
            $ir->port = $rule->port;
            $ir->ip = !isset($rule->ip) ? null : $rule->ip;

            $res = $ir->findActionPortIP();
            // if something goes wrong rollback and exit
            if ($res[ 'success' ] === "false") {
                $db->rollBack();
                http_response_code(200);
                exit(json_encode([ 'result' => 0, 'message' => 'Error find InboundRule' ]));
            }

            // if doesnt exist create else get the id
            if ($res[ 'data' ] === null) {
                $res = $ir->save();
                //if save error rollback and exit
                if ($res[ 'success' ] === "false") {
                    $db->rollBack();
                    http_response_code(200);
                    exit(json_encode([ 'result' => 0, 'message' => 'Error save InboundRule' ]));
                } else {
                    if (!isset($inbound_rule_ids) || empty($inbound_rule_ids)) {
                        $inbound_rule_ids = "" . $res[ 'added' ];
                    } else {
                        $inbound_rule_ids = $inbound_rule_ids . "-" . $res[ 'added' ];
                    }
                    //$inbound_rule_ids = $res[ 'added' ];
                }
            } else {
                if (!isset($inbound_rule_ids) || empty($inbound_rule_ids)) {
                    $inbound_rule_ids = "" . $res[ 'data' ][ 'id_inbound_rule' ];
                } else {
                    $inbound_rule_ids = $inbound_rule_ids . "-" . $res[ 'data' ][ 'id_inbound_rule' ];
                }
            } 
        }


        //-------------------OUTBOUND RULES----------------------//
        $rules = json_decode($_POST[ 'all_outbound_rules' ]);
        foreach($rules as &$rule) {
            $or = new OutboundRule($db);
            $or->action = $rule->action;
            $or->port = $rule->port;
            $or->ip = !isset($rule->ip) ? null : $rule->ip;

            $res = $or->findActionPortIP();
            // if something goes wrong rollback and exit
            if ($res[ 'success' ] === "false") {
                $db->rollBack();
                http_response_code(200);
                exit(json_encode([ 'result' => 0, 'message' => 'Error find OutboundRule' ]));
            }

            // if doesnt exist create else get the id
            if ($res[ 'data' ] === null) {
                $res = $or->save();
                //if save error rollback and exit
                if ($res[ 'success' ] === "false") {
                    $db->rollBack();
                    http_response_code(200);
                    exit(json_encode([ 'result' => 0, 'message' => 'Error save OutboundRule' ]));
                } else {
                    if (!isset($outbound_rule_ids) || empty($outbound_rule_ids)) {
                        $outbound_rule_ids = "" . $res[ 'added' ];
                    } else {
                        $outbound_rule_ids = $outbound_rule_ids . "-" . $res[ 'added' ];
                    }
                }
            } else {
                if (!isset($outbound_rule_ids) || empty($outbound_rule_ids)) {
                    $outbound_rule_ids = "" . $res[ 'data' ][ 'id_outbound_rule' ];
                } else {
                    $outbound_rule_ids = $outbound_rule_ids . "-" . $res[ 'data' ][ 'id_outbound_rule' ];
                }
            } 
        }

        //-------------------INSTANCE SERVER----------------------//

        $is = new InstanceServer($db);
        $is->type = $_POST[ 'instance_server_type' ];
        $is->image = $_POST[ 'instance_server_image' ];
        $is->tags = $_POST[ 'instance_server_tags' ];

        //instance server is unique then no need to check if exist
        $res = $is->save();
        //if save error rollback and exit
        if ($res[ 'success' ] === "false") {
            $db->rollBack();
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'Error Add InstanceServer' ]));
        } else {
            $id_instance_server = $res[ 'added' ];
        }

        //-------------------VM----------------------//

        $vm = new VirtualMachine($db);
        $vm->user_id = $user_id;
        $vm->instance_volume_id = $id_instance_volume;
        $vm->instance_security_group_id = $id_instance_security_group;
        $vm->inbound_rule_ids = $inbound_rule_ids;
        $vm->outbound_rule_ids = $outbound_rule_ids;
        $vm->instance_server_id = $id_instance_server;

        //vm is unique then no need to check if exist
        $res = $vm->save();
        //if save error rollback and exit
        if ($res[ 'success' ] === "false") {
            $db->rollBack();
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'Error Add VM' ]));
        } else {
            $id_vm = $res[ 'added' ];
        }

        //-------------------VM STATE----------------------//

        $vmstate = new VmState($db);
        $vmstate->id = $id_vm;
        $vmstate->state = 1000;
        $vmstate->message = "New machine";
        //vmstate is unique then no need to check if exist
        $res = $vmstate->save();
        //if save error rollback and exit
        if ($res[ 'success' ] === "false") {
            $db->rollBack();
            http_response_code(200);
            exit(json_encode([ 'result' => 0, 'message' => 'Error Add VMSTATE' ]));
        } else {
            $db->commit();
            http_response_code(200);
            exit(json_encode([ 'result' => 1, 'message' => 'VM created', 'added' => $id_vm ]));
        }
} else {
    http_response_code(200);
    exit(json_encode([ 'result' => 0, 'message' => 'FormData not complete' ]));
}

