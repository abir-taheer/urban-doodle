<?php
class Help {
    public $track, $title, $content, $date;
    public $constructed = false;
    public function __construct($track){
        $data = Database::secureQuery("SELECT * FROM `help` WHERE `track` = :track", [":track"=>$track], 'fetch');
        if( count($data) > 1){
            $this->track = $data["track"];
            $this->title = $data["title"];
            $this->content = $data["content"];
            $this->date = $data["date"];
            $this->constructed = true;
        }
    }

    public static function getAll($limit = -1){
        $query = "SELECT * FROM `help`";
        if( $limit !== -1){
            $query.= " LIMIT ".$limit;
        }
        $helps = [];
        $data = Database::secureQuery($query);
        foreach($data as $help){
            $helps[] = new Help($help["track"]);
        }
        return $helps;
    }

    public static function getByGroup($group){
        $helps = [];
        $data = Database::secureQuery(
            "SELECT * FROM `help` INNER JOIN `help_groups` ON help_groups.track = help.track WHERE help_groups.group_name = :group_name",
            [":group_name"=>$group],
            null
            );
        foreach($data as $help){
            $helps[] = new Help($help["track"]);
        }
        return $helps;
    }
    public function inGroup($group){
        return intval(Database::secureQuery(
            "SELECT COUNT(*) as `exists` FROM `help_groups` WHERE `group_name` = :group_name AND `track` = :track",
            [
                ":group_name"=>$group,
                ":track"=>$this->track
            ],
            'fetch'
            )["exists"]) > 0;
    }
}