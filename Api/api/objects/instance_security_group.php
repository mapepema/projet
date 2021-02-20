<?php
//'instance_security_group' object
//cette classe manque surement d'un column ip dans la database pour plus de flexibilité
class SecurityGroup {

    // database connection
    private $conn;
    private $table_name = "instance_security_group";

    // objects rules
    public $id;
    public $inbound_default_policy;
    public $outbound_default_policy;

    /**
     * constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * return inbound rule with id set
     */
    public function findWithId() {
        if (isset($this->id)) {
            $query = <<<QUERY
            SELECT id_instance_security_group, inbound_default_policy, outbound_default_policy
            FROM $this->table_name
            WHERE  id_instance_security_group = :id
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
     * return securitygroup rule with action and port set 
     * to not duplicate an existing rule
     */
    public function findPolicy() {
        if (isset($this->inbound_default_policy) && isset($this->outbound_default_policy)) {
            $query = <<<QUERY
            SELECT id_instance_security_group, inbound_default_policy, outbound_default_policy
            FROM $this->table_name
            WHERE inbound_default_policy = :inbound_default_policy AND outbound_default_policy = :outbound_default_policy
            QUERY;

            $stmt = $this->conn->prepare($query);

            $this->inbound_default_policy = htmlspecialchars(strip_tags($this->inbound_default_policy));
            $this->outbound_default_policy = htmlspecialchars(strip_tags($this->outbound_default_policy));

            $stmt->bindParam(':inbound_default_policy', $this->inbound_default_policy);
            $stmt->bindParam(':outbound_default_policy', $this->outbound_default_policy);

            if ($result = $stmt->execute()) {
                $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $value = !empty($value) ? $value[0] : null;
                return [ 'success' => "true", 'data' => $value ];
            } else {
                $error = $stmt->errorInfo();
                return [ 'success' => "false", 'error' => $error ];
            }
        } else {
            return [ 'success' => "false", 'error' => "inbound_default_policy or outbound_default_policy missing" ];
        }
    }

    /**
     * delete securitygroup with current id
     */
    public function delete() {
        $query = <<<QUERY
        DELETE FROM $this->table_name
        WHERE id_instance_security_group = :id
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
            inbound_default_policy = :inbound_default_policy,
            outbound_default_policy = :outbound_default_policy
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->inbound_default_policy = htmlspecialchars(strip_tags($this->inbound_default_policy));
        $this->outbound_default_policy = htmlspecialchars(strip_tags($this->outbound_default_policy));

        $stmt->bindParam(':inbound_default_policy', $this->inbound_default_policy);
        $stmt->bindParam(':outbound_default_policy', $this->outbound_default_policy);
        
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
            inbound_default_policy = :inbound_default_policy,
            outbound_default_policy = :outbound_default_policy
        WHERE id_instance_security_group = :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->inbound_default_policy = isset($this->inbound_default_policy) ? htmlspecialchars(strip_tags($this->inbound_default_policy)) : $current[ 'inbound_default_policy' ];
        $this->outbound_default_policy = isset($this->outbound_default_policy) ? htmlspecialchars(strip_tags($this->outbound_default_policy)) : $current[ 'outbound_default_policy' ];
        $this->id = isset($this->id) ? htmlspecialchars(strip_tags($this->id)) : $current[ 'id_instance_security_group' ];

        $stmt->bindParam(':inbound_default_policy', $this->inbound_default_policy);
        $stmt->bindParam(':outbound_default_policy', $this->outbound_default_policy);
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
            $exist = !empty($exist) && !empty($exist[ 'data' ][ 'id_instance_security_group' ]) ? $exist[ 'data' ][ 'id_instance_security_group' ] : null;
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

// $sg = new SecurityGroup($db);

// $sg->inbound_default_policy = "drop";
// $sg->outbound_default_policy = "accept";

// var_dump($sg->findPolicy());
// //$result = $sg->save();
// //var_dump($result);

// $sg->id = 1;

// var_dump($sg->findWithId());

// var_dump($sg->save());

