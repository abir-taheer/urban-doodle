<?php
spl_autoload_register(function ($class_name) {
    include "../private/".$class_name . '.php';
});

if( ! isset($_POST['token']) || ! Session::hasSession()){
    exit;
}

$user = Session::getUser();
$form = $user->getFormTokenData($_POST['token']);

if( count($form) > 1 ){
    //retrieve the file associated with the form function
    include "../private/requests/".$form['request'].".php";
}

echo json_encode($response);