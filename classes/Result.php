<?php

class Result {
    public $db_code, $type, $name, $start_time, $end_time, $pic, $constructed, $ended;
    private $result_data, $election;
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
            $this->ended = true;
        } else {
            try {
                $data = new Election($db_code);
                $this->ended = false;
                $this->db_code = $data->db_code;
                $this->name = $data->name;
                $this->type = $data->type;
                $this->start_time = $data->start_time;
                $this->end_time = $data->end_time;
                $this->pic = $data->pic;
                $this->election = $data;
                $this->constructed = true;
            } catch(Exception $e) {
                $this->constructed = false;
            }
        }
    }
    public static function getAllResults(){
        $data = Database::secureQuery("SELECT `db_code` FROM `results` ORDER BY `end_time` DESC", [], null);
        $response = [];
        foreach( $data as $result ){
            $response[] = new Result($result["db_code"]);
        }
        return $response;
    }

    public function getResultData(){
        if( $this->ended ){
            if( !isset($this->result_data) ){
                $this->result_data = json_decode(file_get_contents(app_root."/public/static/elections/".$this->db_code."/results.json"), true);
            }
        } else {
            // calculate the results using the election_handler and then return that
            require_once(app_root.'/classes/election_handlers/'.$this->election->type.".php");
            $handler = new $this->election->type($this->election);
            return $handler->countVotes();
        }
        return $this->result_data;
    }
}