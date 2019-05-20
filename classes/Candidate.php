<?php
Class Candidate{
    public $id, $db_code, $name, $status, $constructed;
    public function __construct($id){
        $data = Database::secureQuery("SELECT * FROM `candidates` WHERE `id` = :id", array(":id"=>$id), 'fetch');
        if( count($data) > 1){
            $this->id = $id;
            $this->db_code = $data['db_code'];
            $this->name = $data['name'];
            $this->status = $data['status'];
            $this->constructed = true;
        } else {
            $this->constructed = false;
        }
    }

    public function getElection(){
        return new Election($this->db_code);
    }

    public function getBasicInfo(){
        $data = Database::secureQuery("SELECT * FROM `basic_info` WHERE `candidate_id` = :id ORDER BY `order`", array(":id"=>$this->id), null);
        $response = [];
        foreach( $data as $info ){
            $response[] = new BasicInfo($info["track"]);
        }
        return $response;
    }

    public function getApprovedMaterials(){
        $data = Database::secureQuery("SELECT * FROM `materials` WHERE `candidate_id` = :cand_id AND `status` = 1", [":cand_id"=>$this->id]);
        $materials = [];
        foreach($data as $material){
            $materials[] = new Material($material["track"]);
        }
        return $materials;
    }
    public function getDeniedMaterials(){
        $data = Database::secureQuery("SELECT * FROM `materials` WHERE `candidate_id` = :cand_id AND `status` = -1", [":cand_id"=>$this->id]);
        $materials = [];
        foreach($data as $material){
            $materials[] = new Material($material["track"]);
        }
        return $materials;
    }
    public function getPendingMaterials(){
        $data = Database::secureQuery("SELECT * FROM `materials` WHERE `candidate_id` = :cand_id AND `status` = 0", [":cand_id"=>$this->id]);
        $materials = [];
        foreach($data as $material){
            $materials[] = new Material($material["track"]);
        }
        return $materials;
    }

    public function getManagerEmails(){
        $emails = [];
        $data = Database::secureQuery("SELECT `email` FROM `roles` WHERE `association` = :cand_id", [":cand_id"=>$this->id]);
        foreach( $data as $email ){
            $emails[] = $email["email"];
        }
        return $emails;
    }
}