<?php
$response["message"] = [print_r($_POST, true)];
$errors = [];
$cannot_be_empty = ["title"=>128, "amount"=>9, "use"=>512, "link"=>255];

foreach( $cannot_be_empty as $type => $max_len ){
    if( trim($_POST[$type]) === "" ){
        $errors[] = ucwords($type)." cannot be empty";
    }
    if( strlen($_POST[$type]) > $max_len){
        $errors[] = ucwords($type)." cannot be longer than ".$max_len." characters";
    }
}

if( ! filter_var($_POST["link"], FILTER_VALIDATE_URL) ){
    $errors[] = "Link to receipt is not a valid url";
}

if(! preg_match('/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/', $_POST["amount"]) ){
    $errors[] = "Amount is not formatted correctly";
}

if( count($errors) === 0){
    $response["status"] = "success";
    $track = bin2hex(random_bytes(8));
    Database::secureQuery(
        "INSERT INTO `finances` (`track`, `candidate_id`, `amount`, `title`, `link`, `extra`) VALUES(:track, :cand_id, :amount, :title, :link, :extra)",
        [
            ":track"=>$track,
            ":cand_id"=>$form["extra"],
            ":amount"=>str_replace(".", "", $_POST["amount"]),
            ":title" => $_POST["title"],
            ":link"=>$_POST["link"],
            ":extra"=>$_POST["use"]
        ]);
    $response["message"] = ["Finance has been sucessfully submitted"];
} else {
    $response["message"] = $errors;
}