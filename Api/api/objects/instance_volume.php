<?php 
//'instance_server' object
class InstanceVolume{
    
    //database connection
    private $conn;
    private $table_name = "instance_volume";

    //objects rules
    public $id;
    public $size_in_gb;
    public $type;

    /**
     * constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * return instance volume rule with id set
     */
    public function findWithId() {
        if (isset($this->id)) {
            $query = <<<QUERY
            SELECT id_instance_volume, size_in_gb, type
            FROM $this->table_name
            WHERE  id_instance_volume = :id
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
     * return instance volume rule with size and type set
     */
    public function findSizeType() {
        if (isset($this->size_in_gb) && isset($this->type)) {
            $query = <<<QUERY
            SELECT id_instance_volume, size_in_gb, type
            FROM $this->table_name
            WHERE  size_in_gb = :size_in_gb AND type = :type
            QUERY;

            $stmt = $this->conn->prepare($query);

            $this->size_in_gb = htmlspecialchars(strip_tags($this->size_in_gb));
            $this->type = htmlspecialchars(strip_tags($this->type));

            $stmt->bindParam(':size_in_gb', $this->size_in_gb);
            $stmt->bindParam(':type', $this->type);

            if ($result = $stmt->execute()) {
                $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $value = !empty($value) ? $value[0] : null;
                return [ 'success' => "true", 'data' => $value ];
            } else {
                $error = $stmt->errorInfo();
                return [ 'success' => "false", 'error' => $error ];
            }
        } else {
            return [ 'success' => "false", 'error' => "pas de size ou de type" ];
        }
    }

    /**
     * delete instance volume with current id
     */
    public function delete() {
        $query = <<<QUERY
        DELETE FROM $this->table_name
        WHERE id_instance_volume = :id
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
            size_in_gb = :size_in_gb,
            type = :type
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->size_in_gb = htmlspecialchars(strip_tags($this->size_in_gb));
        $this->type = htmlspecialchars(strip_tags($this->type));

        $stmt->bindParam(':size_in_gb', $this->size_in_gb);
        $stmt->bindParam(':type', $this->type);

        if ($result = $stmt->execute()) {
            return [ 'success' => "true", 'added' => $this->conn->lastInsertId() ];
        } else {
            $error = $stmt->errorInfo();
            return [ 'success' =>  "false", 'error' => $error ];
        }
    }

    /**
     * update instance volume
     */
    public function update() {

        $current = $this->findWithId()['data'];
        if (empty($current)) {
            return [ 'success' => "false", 'error' => "can not update null" ];
        }

        $query = <<<QUERY
        UPDATE $this->table_name SET
            size_in_gb = :size_in_gb,
            type = :type
        WHERE id_instance_volume = :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->size_in_gb = isset($this->size_in_gb) ? htmlspecialchars(strip_tags($this->size_in_gb)) : $current[ 'size_in_gb' ];
        $this->type = isset($this->type) ? htmlspecialchars(strip_tags($this->type)) : $current[ 'type' ];
        $this->id = isset($this->id) ? htmlspecialchars(strip_tags($this->id)) : $current[ 'id_instance_volume' ];

        $stmt->bindParam(':size_in_gb', $this->size_in_gb);
        $stmt->bindParam(':type', $this->type);
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
     * save intance volume means create or update
     */
    public function save() {
        //check if user exist with id 
        if (!empty($this->id)) {
            $exist = $this->findWithId();
            $exist = !empty($exist) && !empty($exist[ 'data' ][ 'id_instance_volume' ]) ? $exist[ 'data' ][ 'id_instance_volume' ] : null;
        } 
        if (isset($exist) && !is_null($exist)) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
}

//test
// include_once '../config/database.php';

// //get database connection
// $database = new Database();
// $db = $database->getConnection();

// $iv = new InstanceVolume($db);

// $iv->id = 1;

// var_dump($iv->findWithId());



// $iv = new InstanceVolume($db);

// $iv->size_in_gb = 20;
// $iv->type = "l_ssd";


// var_dump($iv->findSizeType());


// $iv = new InstanceVolume($db);

// $iv->size_in_gb = 20;
// $iv->type = "l_ssd";
// $result = $iv->save();

// $iv->id = $result['added'];
// var_dump($iv->delete());
