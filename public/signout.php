<?php
require_once "../config.php";
spl_autoload_register(function ($class_name) {
    require_once "../classes/".$class_name . '.php';
});
Session::deleteSession();
VotingStation::isVotingStation() ? header("Location: "."https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=".(web_ssl ? "https://" : "http://").web_domain."/"): header("Location: /");