<?php
spl_autoload_register(function ($class_name) {
    include "../private/".$class_name . '.php';
});

$request = (isset($_POST['request'])) ? $_POST["request"] : $_GET['request'];
$request_location = "../private/requests/".$request.".php";

file_exists($request_location) ? include $request_location : exit;

echo json_encode($response);