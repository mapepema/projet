<?php 
//'virtuals_machines_states' object
class VmState{
    
    //database connection
    private $conn;
    private $table_name = "virtuals_machines_states";

    //objects rules
    public $id;
    public $state;
    public $message;

    /**
     * constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * return state with id set
     */
    public function findWithId() {
        if (isset($this->id)) {
            $query = <<<QUERY
            SELECT id_virtual_machine, state, message
            FROM $this->table_name
            WHERE  id_virtual_machine = :id
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
    public function findState() {
        if (isset($this->state)) {
            $query = <<<QUERY
            SELECT id_virtual_machine, state, message
            FROM $this->table_name
            WHERE  state = :state
            QUERY;

            $stmt = $this->conn->prepare($query);

            $this->state = htmlspecialchars(strip_tags($this->state));

            $stmt->bindParam(':state', $this->state);

            if ($result = $stmt->execute()) {
                $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $value = !empty($value) ? $value : null;
                return [ 'success' => "true", 'data' => $value ];
            } else {
                $error = $stmt->errorInfo();
                return [ 'success' => "false", 'error' => $error ];
            }
        } else {
            return [ 'success' => "false", 'error' => "state not set" ];
        }
    }

    /**
     * delete vm state with current id
     */
    public function delete() {
        $query = <<<QUERY
        DELETE FROM $this->table_name
        WHERE id_virtual_machine = :id
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
     * insert a new vm state
     */
    public function insert() {

        $query = <<<QUERY
        INSERT INTO $this->table_name SET
            id_virtual_machine = :id,
            state = :state,
            message = :message
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->state = htmlspecialchars(strip_tags($this->state));
        $this->message = htmlspecialchars(strip_tags($this->message));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':state', $this->state);
        $stmt->bindParam(':message', $this->message);

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
            state = :state,
            message = :message
        WHERE id_virtual_machine = :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->state = isset($this->state) ? htmlspecialchars(strip_tags($this->state)) : $current[ 'state' ];
        $this->message = isset($this->message) ? htmlspecialchars(strip_tags($this->message)) : $current[ 'message' ];
        $this->id = isset($this->id) ? htmlspecialchars(strip_tags($this->id)) : $current[ 'id_virtual_machine' ];

        $stmt->bindParam(':state', $this->state);
        $stmt->bindParam(':message', $this->message);
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
            $exist = !empty($exist) && !empty($exist[ 'data' ][ 'id_virtual_machine' ]) ? $exist[ 'data' ][ 'id_virtual_machine' ] : null;
        } 
        if (isset($exist) && !is_null($exist)) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
}

//test
include_once '../config/database.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

// $vmstate = new vmState($db);

// $vmstate->id = 1;
// var_dump($vmstate->findWithId());

// $vmstate->state=1099;
// var_dump($vmstate->findState());

// $vmstate = new vmState($db);
// $vmstate->id = 1;
// $vmstate->state = 0;
// $vmstate->message = "test";
// $result = $vmstate->save();
// var_dump($result);


// $vmstate->state = 1099;
// $vmstate->message = "test1";
// $result = $vmstate->save();
// var_dump($result);

// $vmstate->state=1099;
//  var_dump($vmstate->findState());