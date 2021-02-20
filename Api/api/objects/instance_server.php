<?php 
//'instance_server' object
class InstanceServer{
    
    //database connection
    private $conn;
    private $table_name = "instance_server";

    //objects rules
    public $id;
    public $type;
    public $image;
    public $tags;
    

    /**
     * constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * return InstanceServer rule with id set
     */
    public function findWithId() {
        if (isset($this->id)) {
            $query = <<<QUERY
            SELECT id_instance_server, type, image, tags
            FROM $this->table_name
            WHERE  id_instance_server = :id
            QUERY;

            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));

            $stmt->bindParam(':id', $this->id);

            if ($result = $stmt->execute()) {
                $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $value = !empty($value) ? $value[0] : null;
                return [ 'success' => "true", 'data' => $value ];
            } else {
                $error = $stmt->errorInfo();
                return [ 'success' => "false", 'error' => $error ];
            }
        } else {
            return [ 'success' => "false", 'error' => "pas d'id" ];
        }
    }

    /**
     * delete instance server with current id
     */
    public function delete() {
        $query = <<<QUERY
        DELETE FROM $this->table_name
        WHERE id_instance_server = :id
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
     * insert a new instance server
     */
    public function insert() {
        //change the order with tag analysis to not leed to an error
        $query = <<<QUERY
        INSERT INTO $this->table_name SET
            type = :type,
            image = :image,
            tags = :tags
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->image = htmlspecialchars(strip_tags($this->image));

        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':image', $this->image);

        //for the json 
        if (isset($this->tags) && $this->isJson($this->tags)) {
            $stmt->bindParam(':tags', $this->tags);
        } else {
            return [ 'success' =>  "false", 'error' => "Tags not in json string" ];
        }
        
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

        $current = $this->findWithId()['data'];
        if (empty($current)) {
            return [ 'success' => "false", 'error' => "can not update null" ];
        }

        $query = <<<QUERY
        UPDATE $this->table_name SET
            type = :type,
            image = :image,
            tags = :tags
        WHERE id_instance_server = :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->type = isset($this->type) ? htmlspecialchars(strip_tags($this->type)) : $current[ 'type' ];
        $this->image = isset($this->image) ? htmlspecialchars(strip_tags($this->image)) : $current[ 'image' ];
        $this->tags = isset($this->tags) ? $this->tags: $current[ 'tags' ];
        $this->id = isset($this->id) ? htmlspecialchars(strip_tags($this->id)) : $current[ 'id_instance_server' ];

        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':tags', $this->tags);
        $stmt->bindParam(':id', $this->id);
        
        
        if ($result = $stmt->execute()) {
            var_dump($result);
            return [ 'success' => "true", 'updated' => $this->id ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }


    /**
     * save intance server means create or update
     */
    public function save() {
        //check if user exist with id or with mail
        if (!empty($this->id)) {
            $exist = $this->findWithId();
            $exist = !empty($exist) && !empty($exist[ 'data' ][ 'id_instance_server' ]) ? $exist[ 'data' ][ 'id_instance_server' ] : null;
        } 
        if (isset($exist) && !is_null($exist)) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

//test
// include_once '../config/database.php';

// //get database connection
// $database = new Database();
// $db = $database->getConnection();

// $is = new InstanceServer($db);

// $is->id = 1;

// var_dump($is->findWithId());


// $is = new InstanceServer($db);
// $is->id = 1;
// $is->type = "DEV1-S";
// $is->image = "ubuntu-focal";
// $is->tags = '{"tags": ["FocalFossa","MyUbuntuInstance"]}';

// $result = $is->save();
// var_dump($result);

// $is->id = $result['added'];
// var_dump($is->delete());