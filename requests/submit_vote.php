<?php
// If they got to this stage, that means their form and all other relevant information was already verified.
// Even in the case of a duplicate vote, mysql will throw an exception because of the unique index on the verification_hash column
// Just insert their choices into the database now

try {
    $vote_data = $user->getConfirmationData($form["extra"]);
    $e = new Election($vote_data["db_code"]);
    if( $e->electionState() === 0 && ! $user->hasVoted($vote_data["db_code"]) ){
        $verification = hash("sha256", $vote_data["db_code"].$user->u_id);
        Database::secureQuery("INSERT INTO `votes` (`db_code`, `content`, `verification_hash`) VALUES (:d, :c, :v)", array(":d"=>$vote_data["db_code"], ":c"=>$vote_data["content"], ":v"=>$verification), null );
        $response["status"] = "success";
    } else {
        $response["status"] = "error";
        $response["message"][] = "Error: You have already voted";
    }
} catch (Exception $e){
    $response["status"] = "error";
    $response["message"][] = "Unexpected Error Please Send This To Developer: ".$e;
}
