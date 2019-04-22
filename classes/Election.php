<?php
class Election {
    public $db_code, $name, $type, $status, $start_time, $end_time, $grade, $visibility, $extra, $pic;
    private $candidates;
    public function __construct($db_code){
        $data = Database::secureQuery("SELECT * FROM `elections` WHERE `db_code` = :db_code ", array(":db_code"=>$db_code), "fetch");
        if( count($data) > 1 ){
            $this->db_code = $db_code;
            $this->name = $data["name"];
            $this->type = $data["type"];
            $this->status = $data["status"];
            $this->start_time = Web::UTCDate($data["start_time"]);
            $this->end_time = Web::UTCDate($data["end_time"]);
            $this->grade = $data["grade"];
            $this->visibility = $data["visibility"];
            $this->extra = $data["extra"];
            $this->pic = $data["pic"];
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

    public function getAllVotes(){
        return Database::secureQuery("SELECT * FROM `votes` WHERE `db_code` = :d" , array(":d"=>$this->db_code), null);
    }

    public function getDecodedVotes(){
        require_once app_root."/classes/election_handlers/".$this->type.".php";
        $handler = new $this->type($this);

        $decoded_votes = [];
        foreach( $this->getAllVotes() as $raw_vote ){
            $decoded_votes[] = ["content"=>$handler->decodeVotes($raw_vote["content"]), "grade"=>$raw_vote["grade"]];
        }

        return $decoded_votes;
    }

    public function getCandidateAssociation() {
        $response = [];
        foreach( $this->getCandidates() as $candidate ){
            $response[$candidate->id] = $candidate->name;
        }
        return $response;
    }

    public function numPossibleVoters(){
        preg_match_all("/\[(.*?)\]/",
            $this->grade,
            $grades,
            PREG_PATTERN_ORDER
        );
        $total_voters = 0;
        foreach( $grades[1] as $grade ){
            $voter_count = Database::secureQuery("SELECT COUNT(*) as `voters` FROM `users` WHERE `grade` = :g", array(":g"=>$grade), 'fetch');
            $total_voters += intval($voter_count["voters"]);
        }
        return $total_voters;
    }

    public function getVotesByGrade(){
        preg_match_all("/\[(.*?)\]/",
            $this->grade,
            $grades_allowed,
            PREG_PATTERN_ORDER
        );
        $votes_by_grade = [];
        foreach( $grades_allowed[1] as $grade ){
            $votes_by_grade[intval($grade)] = intval(Database::secureQuery("SELECT COUNT(*) AS `votes` FROM `votes` WHERE `db_code` = :d AND `grade` = :g", array(":d"=>$this->db_code, ":g"=>$grade), 'fetch')["votes"]);
        }
        return $votes_by_grade;
    }

    public function getEligibleVotersByGrade() {
        preg_match_all("/\[(.*?)\]/",
            $this->grade,
            $grades_allowed,
            PREG_PATTERN_ORDER
        );
        $voters_by_grade = [];
        foreach( $grades_allowed[1] as $grade ){
            $voters_by_grade[intval($grade)] = intval(Database::secureQuery("SELECT COUNT(*) AS `users` FROM `users` WHERE `grade` = :g", array(":g"=>$grade), 'fetch')["users"]);
        }
        return $voters_by_grade;
    }


}