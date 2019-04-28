<?php
$candidate = new Candidate($form["extra"]);
$basic_info = $candidate->getBasicInfo();
$tracks = [];
$errors = [];

foreach( $basic_info as $info ){
    $tracks[] = $info->track;
}

if( count($_POST["item"]) !== count($tracks)){
    $errors[] = "Not all items have been accounted for. Try reloading the page";
}

foreach( $_POST["item"] as $track ){
    if( ! in_array($track, $tracks) ){
        $errors[] = "Non-existent item reported. Please try reloading the page.";
    }
}

if( count($errors) === 0 ){
    $counter = 0;
    foreach( $_POST["item"] as $track ){
        Database::secureQuery("UPDATE `basic_info` SET `order` = :odr WHERE `track` = :track", [":odr"=>$counter, ":track"=>$track], null);
        $counter++;
    }
    $response["status"] = "success";
} else {
    $response["message"] = $errors;
}