<?php

ini_set('memory_limit','16M');

$response["message"][] = print_r($_FILES, true);
$errors = [];

$file_path = $_FILES["new_file"]["tmp_name"];
if( $form["extra"] === "cover" ) {
    if ( !isset($_FILES['new_file']['error']) || is_array($_FILES['new_file']['error']) ) {
        $errors[] = "There was an error reading that file. Please try again and verify that the file is encoded properly";
    }
    if( $_FILES["new_file"]["size"] > 500000 ){
        $errors[] = "That file is too large. The limit is 0.5MB";
    }
    if( ! in_array(exif_imagetype($file_path), [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP] )){
        $errors[] = "That file is not an image. Please upload a file of the proper type";
    }

    if( count($errors) === 0 ){

        $new_name = bin2hex(random_bytes(4));
        if (exif_imagetype($file_path) !== IMAGETYPE_GIF) {
            $image = new Image($file_path);
            $image->resizePfp(600);
            imagejpeg($image->image, app_root . "/public/static/img/election_covers/" . $new_name . ".jpg", 85);
            $response["new_name"] = $new_name . ".jpg";
        } else {
            file_put_contents(app_root . "/public/static/img/election_covers/" . $new_name . ".gif", file_get_contents($file_path));
            $response["new_name"] = $new_name . ".gif";
        }

        $response["status"] = "success";
    } else {
        $response["status"] = "error";
        $response["message"] = $errors;
    }
}

