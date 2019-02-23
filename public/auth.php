<?php
spl_autoload_register(function ($class_name) {
    include "../private/".$class_name . '.php';
});

$e = new GoogleAuth($_POST['token']);
if( $e->status ){
    //their sign in attempt has been verified. Make sessions for them
    $u_id = User::readyUserId($e->email, $e->sub);
    Session::createVotingSession($u_id, strtotime("+ 1 day"));
    Session::createIdSession($e->first_name, $e->last_name, $e->email, $e->pic, strtotime("+ 1 day"));
    $response['status'] = "success";
    $response['message'] = "You have been successfully signed in!";
} else {
    $response['status'] = "error";
    $response['message'] = $e->error;
}
echo json_encode($response);