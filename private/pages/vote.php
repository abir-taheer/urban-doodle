<?php
signInRequired();

$user = Session::getUser();
$db_code = $path[2];
$e = new Election($db_code);
$c = $e->getCandidates();

//now we import the class that handles the current type of election
require_once "../private/handlers/".$e->type.".php";
$handler  = new $e->type($e->db_code);