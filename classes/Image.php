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

    public static function getMainColor($full_image){
        // Shrinks the image to a 2x2 canvas to obtain the main color of the image

        $temp_image = imagecreatetruecolor(2, 2);
        imagealphablending($temp_image, false);
        imagesavealpha($temp_image, true);
        imagecopyresampled($temp_image, $full_image, 0, 0, 0, 0, 2, 2, imagesx($full_image), imagesy($full_image));
        $rgb = imagecolorat($temp_image, 1, 1);
        $colors = imagecolorsforindex($full_image, $rgb);
        imagedestroy($temp_image);

        return $colors;
    }

}