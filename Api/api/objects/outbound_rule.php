<?php 
//'outbound-rules' object
class OutboundRule{
    
    //database connection
    private $conn;
    private $table_name = "outbound_rules";

    //objects rules
    public $id;
    public $action;
    public $port;
    public $ip;

    /**
     * constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * return outbound rule with id set
     */
    public function findWithId() {
        if (isset($this->id)) {
            $query = <<<QUERY
            SELECT id_outbound_rule, outbound_rule_action, port, ip
            FROM $this->table_name
            WHERE id_outbound_rule = :id
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
     * return outbound rule with action and port set 
     * to not duplicate an existing rule
     */
    public function findActionPortIP() {
        $ip_set = $this->ip === null ? "" : "AND ip = :ip";

        if (isset($this->action) && isset($this->port)) {
            $query = <<<QUERY
            SELECT id_outbound_rule, outbound_rule_action, port, ip
            FROM $this->table_name
            WHERE outbound_rule_action = :action AND port = :port $ip_set
            QUERY;

            $stmt = $this->conn->prepare($query);

            $this->action = htmlspecialchars(strip_tags($this->action));
            $this->port = htmlspecialchars(strip_tags($this->port));
            
            $stmt->bindParam(':action', $this->action);
            $stmt->bindParam(':port', $this->port);

            if ($this->ip !== null) {
                $this->ip = htmlspecialchars(strip_tags($this->ip));
                $stmt->bindParam(':ip', $this->ip);
            }

            if ($result = $stmt->execute()) {
                $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $value = !empty($value) ? $value[0] : null;
                return [ 'success' => "true", 'data' => $value ];
            } else {
                $error = $stmt->errorInfo();
                return [ 'success' => "false", 'error' => $error ];
            }
        } else {
            return [ 'success' => "false", 'error' => "action or port missing" ];
        }
    }

    /**
     * delete user with current id
     */
    public function delete() {
        $query = <<<QUERY
        DELETE FROM $this->table_name
        WHERE id_outbound_rule= :id
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
            outbound_rule_action = :action,
            port = :port,
            ip = :ip
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->action = htmlspecialchars(strip_tags($this->action));
        $this->port = htmlspecialchars(strip_tags($this->port));
        $this->ip = htmlspecialchars(strip_tags($this->ip));

        $stmt->bindParam(':action', $this->action);
        $stmt->bindParam(':port', $this->port);
        $stmt->bindParam(':ip', $this->ip);
        
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
            outbound_rule_action = :action,
            port = :port,
            ip = :ip
        WHERE user_id = :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->action = isset($this->action) ? htmlspecialchars(strip_tags($this->action)) : $current[ 'outbound_rule_action' ];
        $this->port = isset($this->port) ? htmlspecialchars(strip_tags($this->port)) : $current[ 'port' ];
        $this->ip = isset($this->ip) ? htmlspecialchars(strip_tags($this->ip)) : $current[ 'ip' ];

        $stmt->bindParam(':action', $this->action);
        $stmt->bindParam(':port', $this->port);
        $stmt->bindParam(':ip', $this->ip);
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
     * save user means create or update
     */
    public function save() {
        //check if user exist with id or with mail
        if (!empty($this->id)) {
            $exist = $this->findWithId();
            $exist = !empty($exist) && !empty($exist[ 'data' ][ 'id_outbound_rule' ]) ? $exist[ 'data' ][ 'id_outbound_rule' ] : null;
        } 
        if (isset($exist) && !is_null($exist)) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
}


//test part
// file to include to connect database
// include_once '../config/database.php';

// //get database connection
// $database = new Database();
// $db = $database->getConnection();


// $ibr = new OutboundRule($db);
// $ibr->action = "accept";
// $ibr->port = 444;

// $result = $ibr->findActionPort();
// var_dump($result);

// $result = $ibr->save();
// var_dump($result);

// $ibr->id = $result['added'];

// $test = $ibr->delete();
// var_dump($test);

