<?php
class Election {
    public $db_code, $name, $color, $color_intensity, $type, $status, $start_time, $end_time, $grade, $visibility, $extra;
    private $candidates;
    public function __construct($db_code){
        $data = Database::secureQuery("SELECT * FROM `elections` WHERE `db_code` = :db_code ", array(":db_code"=>$db_code), "fetch");
        if( count($data) > 1 ){
            $this->db_code = $db_code;
            $this->name = $data["name"];
            $this->color = $data["color"];
            $this->color_intensity = $data["color_intensity"];
            $this->type = $data["type"];
            $this->status = $data["status"];
            $this->start_time = Web::UTCDate($data["start_time"]);
            $this->end_time = Web::UTCDate($data["end_time"]);
            $this->grade = $data["grade"];
            $this->visibility = $data["visibility"];
            $this->extra = $data["extra"];
        } else {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception("DB Code does not reference a valid election");
        }
    }

    /**
     * Get an array containing instances of the candidate class for each of the candidates in this election
     * @return array
     */
    public function getCandidates(){
        if(!isset($this->candidates)) {
            $data = Database::secureQuery("SELECT `id` FROM `candidates` WHERE `db_code` = :db_code", array(":db_code" => $this->db_code), null);
            foreach ($data as $i) {
                $this->candidates[] = new Candidate($i["id"]);
            }
        }
        return $this->candidates;
    }

    // Returns if election hasn't started yet (-1), has started and is in session (0), or has ended (1)
    public function electionState(){
        if( Web::getUTCTime() < $this->start_time ){
            return -1;
        }
        return Web::getUTCTime() < $this->end_time ? 0 : 1;
    }

    public static function getAllElections() {
        $response = [];
        $data = Database::secureQuery("SELECT `db_code` FROM `elections`", [], null);
        foreach($data as $election){
            $response[] = new Election($election["db_code"]);
        }
        return $response;
    }

}