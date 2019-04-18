<?php
class BasicInfo {
    public $track, $id, $type, $content;
    public function __construct($track)
    {
        $data = Database::secureQuery("SELECT * FROM  `basic_info` WHERE `track` = :t", array(":t"=>$track), 'fetch');
        $this->track = $data["track"];
        $this->id = $data["id"];
        $this->type = $data["type"];
        $this->content = $data["data"];

    }

    public function getEncodedContent(){
        switch($this->type){
            case "website":
                $content = "<a href=\"//".addslashes($this->content)."\" target=\"_blank\"'>".$this->content."</a>";
                break;
            default:
                $content = htmlspecialchars($this->content);
        }
        return $content;
    }
}