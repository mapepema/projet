<?php 
exec('echo "maintf" >> log.txt');

//database
$host = "localhost";
$db_name = "";
$username = "";
$password = "";

$conn = null;
try{ $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name , $username , $password); }
catch (PDOException $exception) { echo "Connection error: " . $exception->getMessage(); }


//get the virtual machine 
// Génerer un tableau de inbound_rules_id avec l'id de la vm 
//need to change here 'WHERE virtual_machine_id = ....
$id_vm = $argv[1];
$vm = $conn->query("SELECT * FROM virtuals_machines WHERE id_virtual_machine = $id_vm" )->fetch();
$instance_volume_id = $vm[1];
$instance_security_group_id = $vm[2];
$inbound_rules_ids = explode("-", $vm[3]);
$outbound_rule_ids = explode("-",$vm[4]);
$instance_server_id = $vm[5];

// inbound_rule
$inbound_tab=array();
foreach($inbound_rules_ids as $value)
{
  if($value > 0)
  {
    $lig = $conn->query("SELECT * FROM inbound_rules WHERE id_inbound_rule = $value " )->fetch();
    array_push($inbound_tab, array($lig[0],$lig[1],$lig[2],$lig[3]));
  }
}

// outbound_rule

$outbound_tab=array();
foreach($outbound_rule_ids as $value)
{
  if($value > 0)
  {
    $lig = $conn->query("SELECT * FROM outbound_rules WHERE id_outbound_rule = $value " )->fetch();
    array_push($outbound_tab, array($lig[0],$lig[1],$lig[2],$lig[3]));
  }
}

$instance_volume = $conn->query("SELECT * FROM instance_volume WHERE id_instance_volume = $instance_volume_id" )->fetch();
  
$instance_server = $conn->query("SELECT * FROM instance_server WHERE id_instance_server = $instance_server_id" )->fetch(); 

$instance_security_group = $conn->query("SELECT * FROM instance_security_group WHERE id_instance_security_group = $instance_security_group_id" )->fetch();

//provider scaleway 
//to do switch to each provider 

//use of the api to get back all info of the virtual machine ?
$size_in_gb = $instance_volume[1]; 
$type_storage = $instance_volume[2];
$inbound_default_policy = $instance_security_group[1]; 
$outbound_default_policy = $instance_security_group[2];
$inbound_rules = ""; 
$outbound_rules = "";
$type_server = $instance_server[1];
$image = $instance_server[2]; 
$tags =$instance_server[3];

$message = '
provider \"scaleway\" {
  access_key      = \"\"
  secret_key      = \"\"
  organization_id = \"\"
  zone            = \"fr-par-1\"
  region          = \"fr-par\"  
}
 
resource \"scaleway_instance_ip\" \"public_ip\" {}


resource \"scaleway_instance_volume\" \"data\" {
  size_in_gb = '.$size_in_gb.'
  type = \"'.$type_storage.'\"
}

resource \"scaleway_instance_security_group\" \"www\" {
  inbound_default_policy  = \"'.$inbound_default_policy.'\"
  outbound_default_policy = \"'.$outbound_default_policy.'\"
';
 //echo " ".$message;

//foreach inbound rules 
$messageInbound = '';
  foreach($inbound_tab as $value)
  {
    $messageInbound.='
    inbound_rule { 
      action = \"'.$value[1].'\"
      port = \"'.$value[2].'\"';
      if(isset($value[3]))
    {
        $messageInbound.='
      ip = \"'.$value[3].'\"';
        
    }
      $messageInbound.=
    '
  }'
    ;}

//foreach outbound rules 
  $messageOutbound = '';
  foreach($outbound_tab as $value)
  {
    $messageOutbound.='
    outbound_rule { 
      action = \"'.$value[1].'\"
      port = \"'.$value[2].'\"';
      if(isset($value[3]))
    {
        $messageOutbound.='
      ip = \"'.$value[3].'\"';
        
    }

      $messageOutbound.=
    '
  }'
    ;}
$message.= $messageInbound;
$message.= $messageOutbound;

$message =$message . ' 
}

resource \"scaleway_instance_server\" \"my-ubuntu-instance\" {
  type  = \"'.$type_server.'\"
  image = \"'.$image.'\"

  tags = '.$tags.' 
  ip_id = scaleway_instance_ip.public_ip.id

  security_group_id = scaleway_instance_security_group.www.id
}';

//echo " ".$message

//exec(' echo "'.$message.'" >> test.txt');

exec(' echo "'.$message.'" > /home/maxime/Vm/'.$id_vm.'/main.tf');
$stmt = $conn->prepare("UPDATE virtuals_machines_states SET state = :state WHERE  id_virtual_machine = :id_virtual_machine");
$updated_state = 3000;
$stmt->bindParam(':state', $updated_state);
$stmt->bindParam(':id_virtual_machine', $id_vm);
$stmt->execute();

