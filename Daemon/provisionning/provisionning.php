<?php 

//database
$host = "localhost";
$db_name = "";
$username = "";
$password = "";


//time to execute every X second
$seconds = 20;
$micro = $seconds * 1000000;


$conn = null;
try{ $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name , $username , $password); }
catch (PDOException $exception) { echo "Connection error: " . $exception->getMessage(); } 

//code looping 
while(true) {
        $req = $conn->query("SELECT id_virtual_machine,state FROM virtuals_machines_states")->fetchAll();
        foreach($req as $row){
                switch ($row['state']) {
                        case 1000: 
                                exec("php create_folder.php " . $row['id_virtual_machine']);
                                break;
                        case 1001:
                                $stmt = $conn->prepare("UPDATE virtuals_machines_states SET state = :state WHERE  id_virtual_machine = :id_virtual_machine");
                                $updated_state = $row['state'] + 1; 
                                $stmt->bindParam(':state', $updated_state);
                                $stmt->bindParam(':id_virtual_machine', $row['id_virtual_machine']);
                                $stmt->execute();
                                break;
                        case 2000:
                                exec("php create_maintf_file.php " . $row['id_virtual_machine']);
                                break;
                        case 3000:
                                exec("php terraform_init.php " . $row['id_virtual_machine']);
                                break;
                        case 4000:
                                exec("php terraform_plan.php " . $row['id_virtual_machine']);
                                break;
                        case 5000:
                                exec("php terraform_apply.php " . $row['id_virtual_machine']);
                                break;
                }
        }

        //time before a new loop
        usleep($micro);
}
