<?php
Class Candidate{
    public $id, $db_code, $name, $paragraph, $website, $type, $status;
    public function __construct($id){
        $data = Database::secureQuery("SELECT * FROM `candidates` WHERE `id` = :id", array(":id"=>$id), 'fetch');
        if( count($data) > 0){
            $this->id = $id;
            $this->db_code = $data['db_code'];
            $this->name = $data['name'];
            $this->paragraph = $data['paragraph'];
            $this->website = $data['website'];
            $this->type = $data['type'];
            $this->status = $data['status'];
        }
    }
}