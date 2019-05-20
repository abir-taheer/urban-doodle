<?php
require_once "../config.php";
spl_autoload_register(function ($class_name) {
    require_once "../classes/".$class_name . '.php';
});

if( ! isset($_POST['token']) || ! Session::hasSession()){
    exit;
}

$user = Session::getUser();
//the getFormTokenData function already verifies the token, so we don't need to worry about validation in our scripts
$form = $user->getFormTokenData($_POST['token']);

if($form['request'] === "confirm"){
    exit;
}

$response = ["status"=>"error", "message"=>[]];
//we don't need to check if the file exists since the data associated with the tokens is controlled and any 500 errors will be the result of invalid code
if( count($form) > 1 ){
    //retrieve the file associated with the form function
    require_once "../requests/".$form['request'].".php";
} else {
    $response["status"] = "error";
    $response["message"][] = "Invalid form token, try reloading the page. Please contact developer if this continues.";
}

echo json_encode($response);