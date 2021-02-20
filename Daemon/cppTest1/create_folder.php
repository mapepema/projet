<?php

include_once 'database.php';
$db = new database;
$conn = $db->getConnection();

if($conn){
    createFolder($argv[1]);
}


function createFolder($id,$conn)
{
    $dest = "/usr/local/vm/" . $id;
    if (!is_dir($dest)){
        if(mkdir($dest, 0777, true)){
            $stmt = $conn->prepare("UPDATE virtuals_machines_states SET state = :state WHERE  id_virtual_machine = :id_virtual_machine");
            $updated_state = 2000;
            $stmt->bindParam(':state', $updated_state);
            $stmt->bindParam(':id_virtual_machine', $id);
            $stmt->execute();
        }
        else{
            //failed to create directory 
        }
    }
    else{
        //echo this in log file;
        $stmt = $conn->prepare("UPDATE virtuals_machines_states SET state = :state WHERE  id_virtual_machine = :id_virtual_machine");
        $updated_state = 1100;
        $stmt->bindParam(':state', $updated_state);
        $stmt->bindParam(':id_virtual_machine', $id);
        $stmt->execute();
    }
}