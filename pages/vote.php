<?php
signInRequired();

$user = Session::getUser();
$db_code = $path[2];
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

$handler->makeSelectionForm();