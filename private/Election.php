<?php
class Election {
    public $db_code;
    public function __construct($db_code){
        $data = Database::secureQuery("SELECT * FROM `elections` WHERE `db_code` = :db_code ", array(":db_code"=>$db_code), "fetch");
        if( count($data) > 0 ){
            $this->db_code = $db_code;
        }
    }

}