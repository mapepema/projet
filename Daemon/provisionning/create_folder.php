<?php


/*
 * Creation of main.tf file container folder 
*/

//todo create database file to include 
//database connection
$host = "localhost";
$db_name = "";
$username = "";
$password = "";
$conn = null;
try{ $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name , $username , $password); }
catch (PDOException $exception) { echo "Connection error: " . $exception->getMessage(); }


CreateFolder($argv[1]);

 $stmt = $conn->prepare("UPDATE virtuals_machines_states SET state = :state WHERE  id_virtual_machine = :id_virtual_machine");
 $updated_state = 2000;
 $stmt->bindParam(':state', $updated_state);
 $stmt->bindParam(':id_virtual_machine', $argv[1]);
 $stmt->execute();


function CreateFolder($id)
{
	$dest = "/home/maxime/Vm/".$id;
	if (!is_dir($dest)) 
	{
		mkdir($dest, 0777, true);
	}
	else
	{
		//echo "Erreur, ce dossier existe deja";
		//need to echo this in logfile
	}
}



