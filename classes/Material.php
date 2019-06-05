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
        $data = Database::secureQuery("SELECT * FROM `materials` WHERE `status` = -1");
        $materials = [];
        foreach($data as $material){
            $materials[] = new Material($material["track"]);
        }
        return $materials;
    }

    public static function getApprovedMaterials(){
        $data = Database::secureQuery("SELECT * FROM `materials` WHERE `status` = 1");
        $materials = [];
        foreach($data as $material){
            $materials[] = new Material($material["track"]);
        }
        return $materials;
    }

    public static function getPendingMaterials(){
        $data = Database::secureQuery("SELECT * FROM `materials` WHERE `status` = 0");
        $materials = [];
        foreach($data as $material){
            $materials[] = new Material($material["track"]);
        }
        return $materials;
    }

    public function getWatermark($src, $opacity = 0.3, $recolor = null,$font_path = app_root.'/public/static/fonts/open_sans.ttf'){
        list($watermark_width, $watermark_height) = getimagesize($src);
        $watermark = imagecreatefrompng($src);
        $image = imagecreatetruecolor($watermark_width, $watermark_height + 200);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        imagealphablending($watermark, false);
        imagesavealpha($watermark, true);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagecopyresampled($image, $watermark, 0, 0, 0, 0, $watermark_width, $watermark_height, $watermark_width, $watermark_height);

        $image_color = Image::getMainColor($watermark);
        $text_color = imagecolorallocate($image, $image_color["red"], $image_color["green"], $image_color["blue"]);

        $candidate = new Candidate($this->candidate_id);
        $election = $candidate->getElection();
        $text = ($this->status === 1) ? "APPROVED POSTER\nID: ".strtoupper($this->track)."\nUNTIL: ".$election->end_time->format("M jS, Y") : "NOT APPROVED";
        imagettftext($image, 28, 0, 20, 450, $text_color, $font_path, $text);

        if( $recolor !== null ){
            imagefilter($image, IMG_FILTER_COLORIZE, ($recolor["red"] ?? 0), ($recolor["green"] ?? 0), ($recolor["blue"] ?? 0) );
        }

        imagefilter($image, IMG_FILTER_COLORIZE, 0,0,0,127*(1 - $opacity));
        return $image;
    }
}