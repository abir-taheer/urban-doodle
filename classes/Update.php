<?php

class Update {
    public $status, $title, $content, $denial_reason, $date, $created_by, $track, $type;
    public $constructed = false;

    public function  __construct($track) {
        $data = Database::secureQuery("SELECT * FROM `updates` WHERE `track` = :track", [":track"=>$track], 'fetch');
        if( count($data) > 1){
            $this->status = intval($data["status"]);
            $this->title = $data["title"];
            $this->content = $data["content"];
            $this->constructed = true;
            $this->date = Web::UTCDate($data["date"]);
            $this->created_by = $data["created_by"];
            $this->denial_reason = $data["denial_reason"];
            $this->track = $data["track"];
            $this->type = $data["type"];
        }
    }

    public static function getApprovedUpdates(){
        $updates = [];
        $data = Database::secureQuery("SELECT * FROM `updates` WHERE `status` = 1");
        foreach( $data as $update ){
            $updates[] = new Update($update["track"]);
        }
        return $updates;
    }
    public static function getApprovedUpdatesByGroup($group){
        $updates = [];
        $data = Database::secureQuery("SELECT * FROM `updates` WHERE `created_by` = :created_by AND `status` = 1", [":created_by"=>$group]);
        foreach( $data as $update ){
            $updates[] = new Update($update["track"]);
        }
        return $updates;
    }

    public static function getDeniedUpdatesByGroup($group){
        $updates = [];
        $data = Database::secureQuery("SELECT * FROM `updates` WHERE `created_by` = :created_by AND `status` = -1", [":created_by"=>$group]);
        foreach( $data as $update ){
            $updates[] = new Update($update["track"]);
        }
        return $updates;
    }
    public static function getUnreviewedUpdatesByGroup($group){
        $updates = [];
        $data = Database::secureQuery("SELECT * FROM `updates` WHERE `created_by` = :created_by AND `status` = 0", [":created_by"=>$group]);
        foreach( $data as $update ){
            $updates[] = new Update($update["track"]);
        }
        return $updates;
    }

}