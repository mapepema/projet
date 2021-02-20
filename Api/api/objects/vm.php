<?php
// 'virtuals_machines' object
class VirtualMachine {

    //database connection
    private $conn;
    private $table_name = "virtuals_machines";

    //objects rules
    public $id;
    public $user_id;
    public $instance_volume_id;
    public $instance_security_group_id;
    public $inbound_rule_ids;
    public $outbound_rule_ids;
    public $instance_server_id;

    /**
     * constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * return vm rule with id set
     */
    public function findWithId() {
        if (isset($this->id)) {
            $query = <<<QUERY
            SELECT id_virtual_machine, user_id, instance_volume_id, instance_security_group_id, inbound_rule_ids, outbound_rule_ids, instance_server_id
            FROM $this->table_name
            WHERE id_virtual_machine = :id
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
     * return vms of user id 
     */
    public function findUserVm() {
        if (isset($this->user_id)) {
            $query = <<<QUERY
            SELECT id_virtual_machine, user_id, instance_volume_id, instance_security_group_id, inbound_rule_ids, outbound_rule_ids, instance_server_id
            FROM $this->table_name
            WHERE user_id = :user_id
            QUERY;

            $stmt = $this->conn->prepare($query);

            $this->user_id = htmlspecialchars(strip_tags($this->user_id));

            $stmt->bindParam(':user_id', $this->user_id);

            if ($result = $stmt->execute()) {
                $value = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $value = !empty($value) ? $value : null;
                return [ 'success' => "true", 'data' => $value ];
            } else {
                $error = $stmt->errorInfo();
                return [ 'success' => "false", 'error' => $error ];
            }
        } else {
            return [ 'success' => "false", 'error' => "user id missing" ]; 
        }
    }


    /**
     * delete vm with current id
     */
    public function delete() {
        $query = <<<QUERY
        DELETE FROM $this->table_name
        WHERE id_virtual_machine= :id
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
         user_id = :user_id,
         instance_volume_id = :instance_volume_id,
         instance_security_group_id = :instance_security_group_id,
         inbound_rule_ids = :inbound_rule_ids,
         outbound_rule_ids = :outbound_rule_ids,
         instance_server_id = :instance_server_id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->instance_volume_id = htmlspecialchars(strip_tags($this->instance_volume_id));
        $this->instance_security_group_id = htmlspecialchars(strip_tags($this->instance_security_group_id));
        $this->inbound_rule_ids = htmlspecialchars(strip_tags($this->inbound_rule_ids));
        $this->outbound_rule_ids = htmlspecialchars(strip_tags($this->outbound_rule_ids));
        $this->instance_server_id = htmlspecialchars(strip_tags($this->instance_server_id));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':instance_volume_id', $this->instance_volume_id);
        $stmt->bindParam(':instance_security_group_id', $this->instance_security_group_id);
        $stmt->bindParam(':inbound_rule_ids', $this->inbound_rule_ids);
        $stmt->bindParam(':outbound_rule_ids', $this->outbound_rule_ids);
        $stmt->bindParam(':instance_server_id', $this->instance_server_id);
        
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
            user_id = :user_id,
            instance_volume_id = :instance_volume_id,
            instance_security_group_id = :instance_security_group_id,
            inbound_rule_ids = :inbound_rule_ids,
            outbound_rule_ids = :outbound_rule_ids,
            instance_server_id = :instance_server_id
        WHERE id_virtual_machine = :id
        QUERY;

        $stmt = $this->conn->prepare($query);

        $this->user_id = isset($this->user_id) ? htmlspecialchars(strip_tags($this->user_id)) : $current[ 'user_id' ];
        $this->instance_volume_id = isset($this->instance_volume_id) ? htmlspecialchars(strip_tags($this->instance_volume_id)) : $current[ 'instance_volume_id' ];
        $this->instance_security_group_id = isset($this->instance_security_group_id) ? htmlspecialchars(strip_tags($this->instance_security_group_id)) : $current[ 'instance_security_group_id' ];
        $this->inbound_rule_ids = isset($this->inbound_rule_ids) ? htmlspecialchars(strip_tags($this->inbound_rule_ids)) : $current[ 'inbound_rule_ids' ];
        $this->outbound_rule_ids = isset($this->outbound_rule_ids) ? htmlspecialchars(strip_tags($this->outbound_rule_ids)) : $current[ 'outbound_rule_ids' ];
        $this->instance_server_id = isset($this->instance_server_id) ? htmlspecialchars(strip_tags($this->instance_server_id)) : $current[ 'instance_server_id' ];
        $this->id = isset($this->id) ? htmlspecialchars(strip_tags($this->id)) : $current[ 'id_virtual_machine' ];

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':instance_volume_id', $this->instance_volume_id);
        $stmt->bindParam(':instance_security_group_id', $this->instance_security_group_id);
        $stmt->bindParam(':inbound_rule_ids', $this->inbound_rule_ids);
        $stmt->bindParam(':outbound_rule_ids', $this->outbound_rule_ids);
        $stmt->bindParam(':instance_server_id', $this->instance_server_id);
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
// file to include to connect database
// include_once '../config/database.php';

// //get database connection
// $database = new Database();
// $db = $database->getConnection();

// $vm = new VirtualMachine($db);

// $vm->id = 1;
// var_dump($vm->findWithId());
// $vm->inbound_rule_ids = "1-2-3";
// var_dump($vm->save());


// $vm = new VirtualMachine($db);

// $vm->user_id = 1;
// var_dump($vm->findUserVm());


// $vm = new VirtualMachine($db);

// $vm->user_id = 1;
// $vm->instance_volume_id = 1;
// $vm->instance_security_group_id = 1;
// $vm->inbound_rule_ids = "2-4-5";
// $vm->instance_server_id = 1;
// $result = $vm->save();

// $vm->id = $result['added'];
// var_dump($vm->delete());

