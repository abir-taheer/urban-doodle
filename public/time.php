<?php
require_once "../config.php";
//autoload necessary classes
spl_autoload_register(function ($class_name) {
    require_once "../classes/".$class_name . '.php';
});

$date_utc = new DateTime("now", new DateTimeZone("UTC"));

echo $date_utc->format(DateTime::ATOM);

