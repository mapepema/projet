<?php 
//used to get mysql connection 
class Database {

    //database credential
    private $host = "127.0.0.1";
    private $db_name = "vm";
    private $username = "";
    private $password = "";
    public $conn; 

    public function getConnection() {
        $this->conn = null;
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }
        catch(PDOException $exception){
            echo "Connection error : " . $exception->getMessage();
        }
        return $this->conn;
    }


}