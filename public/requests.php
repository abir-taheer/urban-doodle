<?php
spl_autoload_register(function ($class_name) {
    include "../private/".$class_name . '.php';
});

if( ! isset($_POST['token']) || ! Session::hasSession()){
    exit;
}

$user = Session::getUser();
//the getFormTokenData function already verifies the token, so we don't need to worry about validation in our scripts
$form = $user->getFormTokenData($_POST['token']);

if($form['request'] === "vote"){
    exit;
}

//we don't need to check if the file exists since the data associated with the tokens is controlled and any 500 errors will notify of errors
if( count($form) > 1 ){
    //retrieve the file associated with the form function
    include "../private/requests/".$form['request'].".php";
}

echo json_encode($response);