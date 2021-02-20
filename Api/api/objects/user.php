<?php
//'user' object
class User{

    //database connection
    private $conn;
    private $table_name = "users"; 
    
    //objects user 
    public $id; 
    public $firstname;
    public $surname;
    public $email;
    public $password;
    public $permission;

    /**
     * constructor
     */
    public function __construct($db){
        $this->conn = $db;
    }

    /**
     * return all users if success 
     */
    static public function findAll($conn) {
        $query = <<<QUERY
        SELECT user_id, firstname, surname, email, permission
        FROM users
        QUERY;

        $stmt = $conn->prepare($query);
        
        if ($result = $stmt->execute()) {
            $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [ 'success' => "true", 'data' => $value ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }

    /**
     * return user with id if success 
     */
    static public function find($conn, $id) {
        $query = <<<QUERY
        SELECT user_id, firstname, surname, email, permission
        FROM users
        WHERE user_id= :id
        QUERY;

        $stmt = $conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':id', $id);

        if ($result = $stmt->execute()) {
            $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [ 'success' => "true", 'data' => $value[0] ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }

    /**
     * return user with email if success 
     */
    static public function findEmail($conn, $email) {
        $query = <<<QUERY
        SELECT user_id, firstname, surname, email, permission
        FROM users
        WHERE email= :email
        QUERY;

        $stmt = $conn->prepare($query);

        $email = htmlspecialchars(strip_tags($email));

        $stmt->bindParam(':email', $email);
        
        if ($result = $stmt->execute()) {
            $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $value = !empty($value) && isset($value[0]) ?  $value[0] : null;
            return [ 'success' => "true", 'data' => $value ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }

    /**
     * return user with id if success 
     */
    public function findCurrent() {
        $query = <<<QUERY
        SELECT user_id, firstname, surname, email, permission
        FROM $this->table_name
        WHERE user_id= :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);
        
        if ($result = $stmt->execute()) {
            $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $value = !empty($value) && isset($value[0]) ?  $value[0] : null;
            return [ 'success' => "true", 'data' => $value ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }

    /**
     * check if email exist
     */
    public function emailExist() {
        $query = <<<QUERY
        SELECT user_id, firstname, surname, email, password, permission
        FROM $this->table_name
        WHERE email= :email
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        
        if (!empty($this->email)) {
            $stmt->bindParam(':email', $this->email);
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $value = $stmt->fetch(PDO::FETCH_ASSOC);
                    $this->id = $value[ 'user_id' ];
                    $this->firstname = $value[ 'firstname' ];
                    $this->surname = $value[ 'surname' ];
                    $this->email = $value[ 'email' ];
                    $this->permission = $value[ 'permission' ];
                    $this->password = $value[ 'password' ];
                    return [ 'success' => "true", 'exist' => "true"];
                } else {
                    return [ 'success' => "true", 'exist' => "false" ];
                }
            } else {
                $error = $stmt->errorInfo();
                return [ 'success' =>  "false", 'error' => $error ];
            }
        } else {
            return [ 'success' =>  "false", 'error' =>"email is not set" ];
        }
    }

    /**
     * static delete with id 
     */
    static public function deleteId($conn, $id) {
        $query = <<<QUERY
        DELETE FROM users
        WHERE user_id= :id
        QUERY;

        $stmt = $conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':id', $id);
        
        if ($result = $stmt->execute()) {
            return [ 'success' => "true", 'deleted' => $id ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }

    /**
     * delete user with current id
     */
    public function delete() {
        $query = <<<QUERY
        DELETE FROM $this->table_name
        WHERE user_id= :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);
        
        if ($result = $stmt->execute()) {
            return [ 'success' => "true", 'deleted' => $this->id ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }

    /**
     * insert a new user
     */
    public function insert() {
        $query = <<<QUERY
        INSERT INTO $this->table_name SET
            firstname = :firstname,
            surname = :surname,
            email = :email,
            password = :password,
            permission = :permission
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->surname = htmlspecialchars(strip_tags($this->surname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->permission = htmlspecialchars(strip_tags($this->permission));

        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':permission', $this->permission);

        $this->password=htmlspecialchars(strip_tags($this->password));
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
        
        if ($result = $stmt->execute()) {
            return [ 'success' => "true", 'added' => $this->conn->lastInsertId() ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }

    /**
     * update user
     */
    public function update() {

        $current = $this->findCurrent()['data'];
        if (empty($current)) {
            return [ 'success' => "false", 'error' => "can not update null" ];
        }
        //if password needs to be updated
        $password_set = !empty($this->password) ? ", password = :password" : "";

        $query = <<<QUERY
        UPDATE $this->table_name SET
            firstname = :firstname,
            surname = :surname,
            email = :email,
            permission = :permission
            $password_set
        WHERE user_id = :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->firstname = isset($this->firstname) ? htmlspecialchars(strip_tags($this->firstname)) : $current[ 'firstname' ];
        $this->surname = isset($this->surname) ? htmlspecialchars(strip_tags($this->surname)) : $current[ 'surname' ];
        $this->email = isset($this->email) ? htmlspecialchars(strip_tags($this->email)) : $current[ 'email' ];
        $this->permission = isset($this->permission) ? htmlspecialchars(strip_tags($this->permission)) : $current[ 'permission' ];

        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':permission', $this->permission);

        // hash the password before saving to database
        //to do : change the hash and add salage
        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }

        $stmt->bindParam(':id', $current[ 'user_id' ]);
        
        if ($result = $stmt->execute()) {
            var_dump($result);
            return [ 'success' => "true", 'updated' => $this->id ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }


    /**
     * save user means create or update
     */
    public function save() {
        //check if user exist with id or with mail
        if (!empty($this->id)) {
            $exist = User::find($this->conn, $this->id);
            $exist = !empty($exist) && !empty($exist[ 'data' ][ 'user_id' ]) ? $exist[ 'data' ][ 'user_id' ] : null;
        } 

        if (!empty($this->email)) {
            $exist = User::findEmail($this->conn, $this->email);
            $exist = !empty($exist) && !empty($exist[ 'data' ][ 'user_id' ]) ? $exist[ 'data' ][ 'user_id' ] : null;
        }

        if (isset($exist) && !is_null($exist)) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

}

//test part
// // file to include to connect database
//include_once '../config/database.php';

//get database connection
//  $database = new Database();
//  $db = $database->getConnection();

//  $user = new User($db);

//  $user->firstname = "surname1";
//  $user->surname = "familyname1";
//  $user->password = "test";
//  $user->email = "surname1@familyname1.com";
//  $user->permission = 1;

//  $test = $user->save();


// print_r($test);
// print_r(User::emailExist($db, "coll@cool"));
// print_r(User::deleteId($db, 9));



