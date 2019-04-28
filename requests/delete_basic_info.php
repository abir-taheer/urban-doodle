<?php
$item = new BasicInfo($form["extra"]);
$errors = [];
if( ! $item->constructed ){
    $errors[] = "That item does not exist. Please try reloading the page";
}

if( count($errors) === 0 ){
    Database::secureQuery("DELETE FROM `basic_info` WHERE `track` = :track", [":track"=>$form["extra"]], null);
    $response["status"] = "success";
} else {
    $response["message"] = $errors;
}

