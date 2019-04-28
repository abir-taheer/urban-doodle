<?php
$errors = [];

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
    $track = bin2hex(random_bytes(16));
    $candidate = new Candidate($form["extra"]);
    $order = 0;
    foreach( $candidate->getBasicInfo() as $info ){
        if( $info->order >= $order ){
            $order = $info->order + 1;
        }
    }

    Database::secureQuery(
        "INSERT INTO `basic_info` (`track`, `candidate_id`, `type`, `data`, `order`) VALUES (:track, :cand_id, :type, :content, :odr);",
        [
            ":track"=>$track,
            ":cand_id"=>$candidate->id,
            ":type"=>$_POST["type"],
            ":content"=>$_POST["content"],
            ":odr"=>$order
        ],
        null);

    $response["status"] = "success";

} else {
    $response["message"] = $errors;
}
