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
                $content = "<a href=\"http://".htmlspecialchars($this->content)."\" target=\"_blank\"'>".$this->content."</a>";
                break;
            case "email":
            case "email address":
                $content = "<a href=\"mailto:".htmlspecialchars($this->content)."\">".$this->content."</a>";
                break;
            case "phone":
            case "phone number":
            case "phone #":
                $number = preg_replace('/[^+0-9]/', '', $this->content);
                $content = "<a href=\"tel:".$number."\">".$this->content."</a>";
                break;
            default:
                $content = htmlspecialchars($this->content);
        }
        return $content;
    }
}