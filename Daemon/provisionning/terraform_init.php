<?php 


chdir('/home/maxime/Vm/'.$argv[1].'/');
exec('terraform 0.13upgrade -yes');
$test = shell_exec('terraform init');
//next time regex
$test = str_replace('"','*',str_replace("'","/", $test));
exec('echo "'.$test.'" >> log.txt');
$host = "";
$db_name = "";
$username = "";
$password = "";
$conn = null;
try{ $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name , $username , $password); }
catch (PDOException $exception) { echo "Connection error: " . $exception->getMessage(); }
$stmt = $conn->prepare("UPDATE virtuals_machines_states SET state = :state WHERE  id_virtual_machine = :id_virtual_machine");
$updated_state = 4000; 
$stmt->bindParam(':state', $updated_state);
$stmt->bindParam(':id_virtual_machine', $argv[1]);
$stmt->execute();

