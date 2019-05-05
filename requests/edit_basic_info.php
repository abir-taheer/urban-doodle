<?php
$errors = [];

$info = new BasicInfo($form["extra"]);

if( ! $info->constructed ) {
    $errors[] = "That basic information does not exist. Please try reloading the page";
}

if( !isset($_POST["type"]) || trim($_POST["type"]) === "" ){
    $errors[] = "Info type cannot be left empty";
}

if( strlen($_POST["type"]) > 64 ){
    $errors[] = "Info type must not be longer than 64 characters";
}

if( !isset($_POST["content"]) || trim($_POST["content"]) === "" ){
    $errors[] = "Content cannot be left empty";
}

if( strlen($_POST["content"]) > 255 ){
    $errors[] = "Content must not be longer than 255 characters!";
}

if( count($errors) === 0 ){
    Database::secureQuery("UPDATE `basic_info` SET `type` = :type, `data` = :content WHERE `track` = :track", [":type"=>$_POST["type"], ":content"=>$_POST["content"], ":track"=>$info->track], null);
    $response["status"] = "success";
} else {
    $response["message"] = "error";
}