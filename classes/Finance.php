<?php
class Finance {
    public $track, $candidate_id, $amount, $title, $link, $extra, $time;
    public $constructed = false;

    public function __construct($track){
        $data = Database::secureQuery("SELECT * FROM `finances` WHERE `track` = :track", [":track"=>$track], 'fetch');
        if( count($data) > 1 ){
            $this->track = $data["track"];
            $this->candidate_id = $data["candidate_id"];
            $this->amount = $data["amount"] / 100;
            $this->title = $data["title"];
            $this->link = $data["link"];
            $this->extra = $data["extra"];
            $this->time = Web::UTCDate($data["time"]);
            $this->constructed = true;
        }
    }

    public static function getFinancesByCandidate($candidate_id){
        $data = Database::secureQuery("SELECT * FROM `finances` WHERE `candidate_id` = :candidate_id", [":candidate_id"=>$candidate_id]);
        $finances = [];
        foreach($data as $finance){
            $finances[] = new Finance($finance["track"]);
        }
        return $finances;
    }

    public static function getTotalSpending($candidate_id){
        return intval(Database::secureQuery(
            "SELECT SUM(`amount`) as `amount` FROM `finances` WHERE `candidate_id` = :candidate_id",
            [":candidate_id"=>$candidate_id],
            "fetch"
            )['amount']) / 100;
    }

}