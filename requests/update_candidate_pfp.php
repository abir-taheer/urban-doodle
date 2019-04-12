<?php
$errors = [];
$user = Session::getUser();
if( ! $user->isManagerFor($form["extra"]) ){
    $errors[] = "User is not a campaign manager";
}
if( $_FILES["new_pfp"]["size"] > 2000000){
    $errors[] = "That file is too large. The limit is 2MB";
}
if ( !isset($_FILES['new_pfp']['error']) || is_array($_FILES['new_pfp']['error']) ) {
    $errors[] = "There was an error reading that file. Please try again and verify that the file is encoded properly";
}

if( count($errors) === 0 ){
    ini_set('memory_limit','16M');
    ini_set('file_uploads','On');

    $candidate = new Candidate($form["extra"]);

    // Rebuild the image sent to us by the candidate
    // This allows us to compress and resize it properly if not already done so
    // This also allows us to be sure that the file users will receive is actually an image
    $pfp = new Image($_FILES["new_pfp"]["tmp_name"]);

    // Resize the image provided by the candidate. Preserve the aspect ratio
    $pfp->resizePfp(800);

    // Create a new image with the same dimensions as the pfp
    $new_img = imagecreatetruecolor($pfp->width, $pfp->height);

    // Create a white layer
    // This will be used in the case of transparent images
    $white = imagecolorallocate($new_img,  255, 255, 255);

    // Fill the new image with the white background
    imagefilledrectangle($new_img, 0, 0, $pfp->width, $pfp->height, $white);

    // Combine the white image with the one provided by the candidate
    imagecopy($new_img, $pfp->image, 0, 0, 0, 0, $pfp->width, $pfp->height);

    // Save the new image in the same place that the candidate's photo is stored
    imagejpeg($new_img, app_root."/public/static/elections/".$candidate->db_code."/candidates/".$candidate->id.".jpg", 85);
    $response["status"] = "success";
} else {
    $response["status"] = "error";
    $response["message"] = $errors;
}
