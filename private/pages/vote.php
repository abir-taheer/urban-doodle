<?php
signInRequired();

$user = Session::getUser();
$token = (isset($_POST['token'])) ? $_POST['token'] : $_COOKIE['Vote_Ref'];

if( $token == null ){
    //send out the invalid form token page
    echo "<div class='mdl-grid'><div class='unready' data-type='std-card-cont'><div class='unready' data-type='std-expand'></div><h3 class='sumana text-center card-heading'>Invalid Form Token</h3><div class='sub-container'><p class='text-center'>For security, all of the voting pages require that a one-time form token be sent with the request. <br>Please click the button below to return to the dashboard and try again</p><div class='center-flex'><div class='unready change-page' data-type='btn' data-page='/dashboard'>Return To Dashboard</div></div></div><br></div></div>";
    exit;
}

$form = $user->getFormTokenData($token);
//make a cookie for temporary storage such that if the user refreshes the page, the form doesn't disappear
setcookie("Vote_Ref", $form['token'], strtotime($form['expires']), "/", Config::getConfig()['domain'], Config::getConfig()['ssl'], true);
//start by getting the election that the form token references
$e = new Election($form['extra']);
$c = $e->getCandidates();

//now we import the class that handles the current type of election
require_once "../private/elections/".$e->type.".php";
$handler  = new $e->type($e->db_code);
$handler->makeSelectionForm();