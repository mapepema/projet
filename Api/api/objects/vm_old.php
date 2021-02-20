<?php 

class VM{

    private $conn;
    


    public $tablename;
    public $volume_size;
    public $volume_type;
    public $security_group_inbound_policy;
    public $security_group_outbound_policy;
    public $inbound_rules;
    public $outbound_rules;
    public $image_type;
    public $image_image;
    public $image_tags;


    public function __construct($db){
        $this->conn = $db;
    }

    public function findVm(){
        
    }

    public function createVm(){
        /**
         * Create instance volume 
         */
        $stmt = $this->conn->prepare("INSERT INTO instance_volume (size_in_gb,type) VALUES (:size_in_gb, :type)");
        $stmt->bindParam(':size_in_gb', $this->volume_size);
        $stmt->bindParam(':type', $this->volume_type);
        if(!$stmt->execute())return false;
        $id_instance_volume = $this->conn->lastInsertId();


        /**
         * Create instance security group
         */
        $stmt = $this->conn->prepare("INSERT INTO instance_security_group
         (inbound_default_policy, outbound_default_policy) VALUES (:inbound_default_policy, 
         :outbound_default_policy)");
        $stmt->bindParam(':inbound_default_policy', $this->security_group_inbound_policy);
        $stmt->bindParam(':outbound_default_policy', $this->security_group_outbound_policy);
        if(!$stmt->execute())return false;
        $id_instance_security_group = $this->conn->lastInsertId();


        /**
         * Create inbound rules for security group 
         */
        $id_inbound_rules = "";
        foreach ($this->inbound_rules as $key => $inbound_rule){
            //add sanatize inbound_rule
            $stmt = $this->conn->prepare("INSERT INTO inbound_rules
            (inbound_rule_action, port, ip) VALUES (:inbound_rule_action, 
            :port, :ip)");
            $stmt->bindParam(':inbound_rule_action', $inbound_rule->action);
            $stmt->bindParam(':port', $inbound_rule->port);
            if(!empty($inbound_rule->ip))$inbound_rule->ip = null;
            $stmt->bindParam(':ip',$inbound_rule->ip );
            if(!$stmt->execute())return false;
            $id_inbound_rules .= "".$this->conn->lastInsertId();
	    if($key !== array_key_last($this->inbound_rules)){
		$id_inbound_rules .= "-";
	    }
        }


        /**
         * Create outbound rules for security group
         */
        $id_outbound_rules = "";
        foreach ($this->outbound_rules as $key => $outbound_rule){
            //add sanatize outbound_rule
            $stmt = $this->conn->prepare("INSERT INTO outbound_rules
            (outbound_rule_action, port, ip) VALUES (:outbound_rule_action, 
            :port, :ip)");
            $stmt->bindParam(':outbound_rule_action', $outbound_rule->action);
            $stmt->bindParam(':port', $outbound_rule->port);
            if(!empty($outbound_rule->ip))$outbound_rule->ip = null;
            $stmt->bindParam(':ip', $outbound_rule->ip);
            if(!$stmt->execute())return false;
            $id_outbound_rules .= "".$this->conn->lastInsertId();
	    if($key !== array_key_last($this->outbound_rules)){ 
                $id_outbound_rules .= "-";
            }
        }


        /**
         * Create instance_server
         */
        $stmt = $this->conn->prepare("INSERT INTO instance_server (type, image, tags) VALUES (:type, :image, :tags)");
        $stmt->bindParam(':type', $this->image_type);
        $stmt->bindParam(':image', $this->image_image);
        $stmt->bindParam(':tags', $this->image_tags);
        if(!$stmt->execute())return false;
        $id_instance_server = $this->conn->lastInsertId();


        /**
         * Create VMMMMM
         */
        $stmt = $this->conn->prepare("INSERT INTO virtuals_machines (instance_volume_id, instance_security_group_id, inbound_rule_ids, outbound_rule_ids, instance_server_id)
         VALUES (:instance_volume_id, :instance_security_group_id, :inbound_rule_ids, :outbound_rule_ids, :instance_server_id)");
        $stmt->bindParam(':instance_volume_id', $id_instance_volume);
        $stmt->bindParam(':instance_security_group_id', $id_instance_security_group);
        $stmt->bindParam(':inbound_rule_ids', $id_inbound_rules);
        $stmt->bindParam(':outbound_rule_ids',$id_outbound_rules );
        $stmt->bindParam(':instance_server_id', $id_instance_server);
        if(!$stmt->execute())return false;
        $id_vm = $this->conn->lastInsertId();

        /**
         * Pass vm state to Init : 1000
         */
        $stmt = $this->conn->prepare("INSERT INTO virtuals_machines_states (id_virtual_machine , state, message) VALUES (:id_virtual_machine,'1000', 'Ajout BDD termine')");
        $stmt->bindParam(':id_virtual_machine',$id_vm);
        if(!$stmt->execute())return false;

        return true;
    }   
}