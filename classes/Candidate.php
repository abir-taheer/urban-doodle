<?php
Class Candidate{
    public $id, $db_code, $name, $paragraph, $website, $type, $status, $constructed;
    public function __construct($id){
        $data = Database::secureQuery("SELECT * FROM `candidates` WHERE `id` = :id", array(":id"=>$id), 'fetch');
        if( count($data) > 1){
            $this->id = $id;
            $this->db_code = $data['db_code'];
            $this->name = $data['name'];
            $this->paragraph = $data['paragraph'];
            $this->website = $data['website'];
            $this->type = $data['type'];
            $this->status = $data['status'];
            $this->constructed = true;
        } else {
            $this->constructed = false;
        }
    }
}