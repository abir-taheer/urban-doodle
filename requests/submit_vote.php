<?php
// If they got to this stage, that means their form and all other relevant information was already verified.
// Even in the case of a duplicate vote, mysql will throw an exception because of the unique index on the verification_hash column
// Just insert their choices into the database now

try {
    $vote_data = $user->getConfirmationData($form["extra"]);
    $verification = hash("sha256", $vote_data["db_code"].$user->u_id);
    Database::secureQuery("INSERT INTO `votes` (`db_code`, `content`, `verification_hash`) VALUES (:d, :c, :v)", array(":d"=>$vote_data["db_code"], ":c"=>$vote_data["content"], ":v"=>$verification), null );
    User::deleteFormToken($form["token"]);
    User::deleteConfirmToken($form["content"]);
    $response["status"] = "success";
} catch (Exception $e){
    $response["status"] = "error";
    $response["message"][] = "Error: ".$e;
}
