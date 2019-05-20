<?php
class Material {
    public $track, $candidate_id, $title, $content, $date, $status, $denial_reason, $type;
    public $constructed = false;
    public function __construct($track){
        $data = Database::secureQuery("SELECT * FROM `materials` WHERE `track` = :track", [":track"=>$track], 'fetch');
        if( count($data) > 1 ){
            $this->track = $data["track"];
            $this->candidate_id = $data["candidate_id"];
            $this->title = $data["title"];
            $this->content = $data["content"];
            $this->date = Web::UTCDate($data["date"]);
            $this->status = intval($data["status"]);
            $this->denial_reason = $data["denial_reason"];
            $this->type = $data["type"];
            $this->constructed = true;
        }
    }

    public static function getDeniedMaterials(){

    }

    public static function getApprovedMaterials(){

    }

    public static function getPendingMaterials(){

    }
}