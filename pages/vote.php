<?php
signInRequired();

$user = Session::getUser();
$db_code = $path[2];
try {
    $e = new Election($db_code);
    $c = $e->getCandidates();
} catch (Exception $e) {
    replyError("Error:", "There is no ongoing election with the ID ".htmlspecialchars($db_code).".");
    exit;
}

//now we import the class that handles the current type of election
require_once "../classes/election_handlers/".$e->type.".php";
$handler  = new $e->type($e);
if (! $handler instanceof ElectionHandler) {
    throw new Exception("Class for handling an election must implement the ElectionHandler interface.");
}

// Handle some user-based error cases below

// In the case that the election has not yet started
if( $e->electionState() === -1 ){
    replyError("Not Yet Started", "This election has not yet started. Election will start in: <a class=\"js-timer\" data-timer-type=\"countdown\" data-count-down-date=\"".base64_encode($e->start_time->format(DATE_ATOM))."\"></a><br><a class=\"small-txt\">(Times have been adjusted to match the server time.)</a>", "/static/img/vote.gif", "vote gif", "error-img");
    exit;
}

// In the case that the election has already concluded
if( $e->electionState() === 1 ){
    replyError("Election Finished", "Voting for this election has completed. Results will be available soon...", "/static/img/data-process.png", "election finished", "error-img");
    exit;
}

// In the case that the user is not allowed to vote for this election
if( !$user->canVote($db_code) ){
    replyError("Insufficient Permissions", "You currently do not have sufficient permissions to vote for this election");
    exit;
}

// The case that the user has already voted
if( $user->hasVoted($db_code) ){
    replyError($e->name, "<a class=\"green-txt\">You have voted!</a>", "/static/img/thank_you.gif", "thank you", "error-img");
    exit;
}

$handler->makeSelectionForm();