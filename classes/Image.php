<?php

class Image
{
    public $status = true;
    public $path, $image, $height, $width;
    public function __construct($file_path) {
        $this->path = $file_path;
        $type = exif_imagetype($file_path);
        switch($type){
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($file_path);
                break;
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($file_path);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($file_path);
                break;
            case IMAGETYPE_BMP:
                $this->image = imagecreatefrombmp($file_path);
                break;
            default:
                $this->status = false;
        }
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
        imagecolorallocatealpha ( $this->image , 255 , 255 , 255 , 0 );

    }

    public function resizePfp($width = -1, $height = -1){
        $this->image = imagescale ( $this->image , $width, $height);
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }
}