<?php

$errors = [];

if($_POST["type"] !== "deny" && $_POST["type"] !== "approve"){
    $errors[] = "Invalid approval type";
}

if( $_POST["type"] === "deny" ){
    if( trim($_POST["reason"]) === "" ){
        $errors[] = "Reason cannot be left empty for denial!";
    }
    if( strlen($_POST["reason"]) > 255 ){
        $errors[] = "Denial reason must be less than 255 characters";
    }
}

if( count($errors) === 0 ){
    $material = new Material($form["extra"]);
    $candidate = new Candidate($material->candidate_id);
    // Send an email
    $email = new Email();
    $email->to = $candidate->getManagerEmails();
    $email->subject = "Material has just been reviewed";
    $email->body = "One of the materials that your campaign submitted for approval (".htmlspecialchars($material->title).") has been reviewed. You can view it's status <a href='".((web_ssl) ? "https://" : "http://").web_domain."/campaign/".$candidate->id."/materials/".$material->track."'>here</a>.";
    $email->send();
    if( $_POST["type"] === "approve" ){
        Database::secureQuery("UPDATE `materials` SET `status` = 1 WHERE `track` = :track", [":track"=>$material->track]);
    } else {

        Database::secureQuery("UPDATE `materials` SET `status` = -1, `denial_reason` = :reason WHERE `track` = :track", [":track"=>$material->track, ":reason"=>$_POST["reason"]]);

    }
    $response["status"] = "success";
}