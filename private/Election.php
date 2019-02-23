<?php
class Election {
    public $db_code, $name, $color, $color_intensity, $type, $status, $start_time, $end_time, $grade, $votes_allowed, $visibility;
    private $candidates;
    public function __construct($db_code){
        $data = Database::secureQuery("SELECT * FROM `elections` WHERE `db_code` = :db_code ", array(":db_code"=>$db_code), "fetch");
        if( count($data) > 0 ){
            $this->db_code = $db_code;
            $this->name = $data['name'];
            $this->color = $data['color'];
            $this->color_intensity = $data['color_intensity'];
            $this->type = $data['type'];
            $this->status = $data['status'];
            $this->start_time = strtotime($data['start_time']);
            $this->end_time = strtotime($data['end_time']);
            $this->grade = $data['grade'];
            $this->votes_allowed = $data['votes_allowed'];
            $this->visibility = $data['votes_allowed'];
        }
    }
    public function getCandidates(){
        if(!isset($this->candidates)) {
            $data = Database::secureQuery("SELECT `id` FROM `candidates` WHERE `db_code` = :db_code", array(":db_code" => $this->db_code), null);
            foreach ($data as $i) {
                $this->candidates[] = new Candidate($i);
            }
        }
        return $this->candidates;
    }

}