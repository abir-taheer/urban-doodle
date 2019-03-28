<?php
signInRequired();
$user = Session::getUser();
$db_code = $_POST["election"];
try {
    $e = new Election($db_code);
    $c = $e->getCandidates();
} catch (Exception $e) {
    echo
        "<div class=\"mdc-card mdc-card--outlined mdc-layout-grid__cell--span-12\">
        <div class=\"card-expand-default\"></div>
        <h3 class=\"txt-ctr\">Error:</h3>
        <div class=\"sub-container\"></div>
        <p class=\"txt-ctr\">There is no ongoing election with the ID ".$db_code.".</p>
        <div class=\"flx-ctr\">
            <img src=\"/static/img/sad-cat.png\" class=\"cat-404\" alt=\"sad-cat\">
        </div>
    </div>";
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
    echo "<div class=\"mdc-card mdc-card--outlined mdc-layout-grid__cell--span-12\">
        <div class=\"card-expand-default\"></div>
        <h3 class=\"txt-ctr\">Error:</h3>
        <div class=\"sub-container\"></div>
        <p class=\"txt-ctr\">There were issues with the data received with your form</p>
        <div class=\"flx-ctr\">
            <img src=\"/static/img/sad-cat.png\" class=\"cat-404\" alt=\"sad-cat\">
        </div>
    </div>";
    exit;
}

$votes = $handler->encodeVotes($_POST["votes"]);
$confirmation_token = $user->createConfirmToken($db_code, $votes);

$handler->showConfirmation($confirmation_token);


