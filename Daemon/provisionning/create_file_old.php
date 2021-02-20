<?php 
/**
 * Create file main.tf for terraform uses
*/

//database connection
$host = "localhost";
$db_name = "";
$username = "";
$password = "";
$conn = null;
try{ $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name , $username , $password); }
catch (PDOException $exception) { echo "Connection error: " . $exception->getMessage(); }

function getVmDetails($conn, $id_vm){
	$stmt = $conn->prepare("SELECT instance_volume_id, instance_security_group_id, inbound_rule_ids, outbound_rule_ids, instance_server_id FROM virtuals_machines WHERE id_virtual_machine = :id_virtual_machine");
	$stmt->bindParam(':id_virtual_machine', $id_vm);
	$stmt->execute();
	$vm = $stmt->fetch();
	exec('echo "'.implode($vm).'" >> log.txt');

	if(count($vm)>1){
		$vm_details = [];

		$stmt = $conn->prepare("SELECT size_in_gb, type FROM instance_volume WHERE id_instance_volume = :id_instance_volume");
		$stmt->bindParam(':id_instance_volume', $vm['instance_volume_id']);
		$stmt->execute();
		$result = $stmt->fetch();
		$vm_details['size_in_gb'] = $result['size_in_gb'];
		$vm_details['type_storage'] = $result['type'];

		$stmt = $conn->prepare("SELECT inbound_default_policy, outbound_default_policy FROM instance_security_group WHERE id_instance_security_group = :id_instance_security_group");
       		$stmt->bindParam(':id_instance_security_group', $vm['instance_security_group_id']); 
       		$stmt->execute(); 
        	$result = $stmt->fetch();
        	$vm_details['inbound_default_policy'] = $result['inbound_default_policy'];
        	$vm_details['outbound_default_policy'] = $result['outbound_default_policy'];

               $ids = explode('-',$vm['inbound_rule_ids']);
                if(count($ids) > 0){
                        $inbound_rules = [];
                        foreach($ids as $id){
                                $stmt = $conn->prepare("SELECT inbound_rule_action, port, ip FROM inbound_rules WHERE id_inbound_rule = :id_inbound_rule");
                                $stmt->bindParam(':id_inbound_rule',$id);
                                $stmt->execute(); 
                                $result=$stmt->fetch();
                                exec('echo "'.implode($result).'" >> log.txt');
                                $cont = [];
                                $cont['inbound_rule_action'] = $result['inbound_rule_action'];
                                $cont['port'] = $result['port'];
                                if(!is_null($result['ip'])){
                                        $cont['ip'] = $result['ip'];
                                }
                                exec('echo "'.implode($cont).'" >> log.txt');
                                $inbound_rules[] = $cont;
                        }
                        $vm_details['inbound_rules'] = $inbound_rules;
                }

                $ids = explode('-',$vm['outbound_rule_ids']);
                if(count($ids)>0){
                        $outbound_rules = [];
                        foreach($ids as $id){
                                $stmt = $conn->prepare("SELECT outbound_rule_action, port, ip FROM outbound_rules WHERE id_outbound_rule = :id_outbound_rule");
                                $stmt->bindParam(':id_outbound_rule',$id);
                                $stmt->execute(); 
                                $result = $stmt->fetch();
                                $cont = [];
                                $cont['outbound_rule_action'] = $result['outbound_rule_action'];
                                $cont['port'] = $result['port'];
                                if(!is_null($result['ip'])){
                                        $cont['ip'] = $result['ip'];
                                }
                                $inbound_rules[] = $cont;
                        }
                        $vm_details['outbound_rules'] = $inbound_rules;
                }


                exec('echo "'.implode(', ',$vm_details).'" >> log.txt');
        }
        else{
                //to do throw error 
        }
}

getVmDetails($conn, $argv[1]);
