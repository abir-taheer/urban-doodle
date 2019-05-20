<?php
$response["message"] = [print_r($_POST, true)];

$errors = [];

if( trim($_POST["update-title"]) === ""){
    $errors[] = "Update title cannot be left empty!";
}
if( trim($_POST["content"]) === "" ){
    $errors[] = "Update content cannot be left empty!";
}

if( strlen($_POST["update-title"]) > 64){
    $errors[] = "Update title cannot be longer than 64 characters";
}
if( strlen($_POST["content"]) > 1024){
    $errors[] = "Update content cannot be longer than 1024 characters";
}
if( count($errors) === 0){
    Database::secureQuery(
        "INSERT INTO `updates` (`track`, `created_by`, `type`, `title`, `content`) VALUES (:track, :candidate_id, 'new', :title, :content)",
        [
            ":track" => bin2hex(random_bytes(16)),
            ":candidate_id"=>$form["extra"],
            ":title"=>$_POST["update-title"],
            ":content"=>$_POST["content"]
        ]
    );
    $response["message"] = ["Update was successfully submitted for approval!"];
    $response["status"] = "success";
} else {
    $response["message"] = $errors;
}