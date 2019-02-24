<?php
signInRequired();

$user = Session::getUser();
$form = $user->getFormTokenData($_POST['token']);

//start by getting the election that the form token references
$e = new Election($form['extra']);
$c = $e->getCandidates();
//make a cookie for temporary storage such that if the user refreshes the page, the form doesn't disappear
setcookie("Vote_Ref", $form['token'], strtotime($form['expires']), "/", Config::getConfig()['domain'], Config::getConfig()['ssl'], true);

//now we import the class that handles the current type of election
require_once "../private/elections/".$e->type.".php";
$handler  = new $e->type($e->db_code);
$handler->makeSelectionForm();