<?php
signInRequired();
$user = Session::getUser();
$db_code = $_POST["election"];
try {
    $e = new Election($db_code);
    $c = $e->getCandidates();
} catch (Exception $e) {
    replyError("Error:", "There is no election with that ID.");
    exit;
}

//now we import the class that handles the current type of election
require_once "../classes/election_handlers/".$e->type.".php";
$handler  = new $e->type($e);
if (! $handler instanceof ElectionHandler) {
    throw new Exception("Class for handling an election must implement the ElectionHandler interface.");
}

// First call the verify functions to make sure that the data received from the client is valid
if( ! $handler->verifyVote($_POST["votes"]) ){
    // Let the user know that there was an error with their form
    replyError("Error:", "There were issues with the data received with your form.");
    exit;
}

$votes = $handler->encodeVotes($_POST["votes"]);
$confirmation_token = $user->createConfirmToken($db_code, $votes);

$handler->showConfirmation($confirmation_token);


