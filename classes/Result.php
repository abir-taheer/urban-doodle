<?php

class Result {
    public $db_code, $type, $name, $start_time, $end_time, $pic, $constructed;
    public function __construct($db_code)
    {
        $data = Database::secureQuery("SELECT * FROM `results` WHERE `db_code` = :d", [":d"=>$db_code], 'fetch');
        if( count($data) > 1 ) {
            $this->db_code = $data["db_code"];
            $this->name = $data["name"];
            $this->type = $data["type"];
            $this->start_time = Web::UTCDate($data["start_time"]);
            $this->end_time = Web::UTCDate($data["end_time"]);
            $this->pic = $data["pic"];
            $this->constructed = true;
        } else {
            $this->constructed = false;
        }
    }
    public static function getAllResults(){
        $data = Database::secureQuery("SELECT `db_code` FROM `results`", [], null);
        $response = [];
        foreach( $data as $result ){
            $response[] = new Result($result["db_code"]);
        }
        return $response;
    }
}