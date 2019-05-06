<?php
class BasicInfo {
    public $track, $id, $type, $content, $order, $constructed;
    public function __construct($track)
    {
        $data = Database::secureQuery("SELECT * FROM  `basic_info` WHERE `track` = :t", array(":t"=>$track), 'fetch');
        if( count($data) > 1 ){
            $this->track = $data["track"];
            $this->id = $data["candidate_id"];
            $this->type = $data["type"];
            $this->content = $data["data"];
            $this->order = $data["order"];
            $this->constructed = true;
        } else {
            $this->constructed = false;
        }
    }

    public function getEncodedContent(){
        switch(strtolower($this->type)){
            case "website":
            case "url":
                $content = "<a href=\"http://".htmlspecialchars($this->content)."\" target=\"_blank\"'>".htmlspecialchars($this->content)."</a>";
                break;
            case "email":
            case "email address":
                $content = "<a href=\"mailto:".htmlspecialchars($this->content)."\">".htmlspecialchars($this->content)."</a>";
                break;
            case "phone":
            case "phone number":
            case "phone #":
                $number = $this->content;
                preg_match_all("/[a-zA-Z]/", $this->content, $letter_matches);
                $letter_associations = [
                    "a-cA-C" => "2",
                    "d-fD-F" => "3",
                    "g-iG-I" => "4",
                    "j-lJ-L" => "5",
                    "m-oM-O" => "6",
                    "p-sP-S" => "7",
                    "t-vT-V" => "8",
                    "w-zW-Z" => "9"
                ];
                foreach( $letter_matches[0] as $letter_match ){
                    foreach( $letter_associations as $letter_range => $association ){
                        if(preg_match("/[".$letter_range."]/", $letter_match)) {
                            $number = str_replace($letter_match, $association, $number);
                            break;
                        }
                    }
                }
                $number = preg_replace('/[^+0-9]/', '', $number);
                $content = "<a href=\"tel:".$number."\">".htmlspecialchars($this->content)."</a>";
                break;
            default:
                $content = htmlspecialchars($this->content);
        }
        return $content;
    }
}