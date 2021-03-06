<?php
require_once "../config.php";
spl_autoload_register(function ($class_name) {
    require_once "../classes/".$class_name . '.php';
});

$e = new GoogleAuth($_POST['token']);
if( $e->status ){
    //their sign in attempt has been verified. Make sessions for them
    $u_id = User::readyUserId($e->email, $e->sub);
    $expiration = VotingStation::isVotingStation() ? Web::UTCDate("+5 min") : Web::UTCDate("+1 day");
    if( VotingStation::isVotingStation() ) {
        setcookie("Current_Session", $expiration->format(DATE_ATOM), $expiration->getTimestamp(), "/");
    }

    Session::createVotingSession($u_id, $expiration);
    Session::createIdSession($e->first_name, $e->last_name, $e->email, $e->pic, $expiration);
    $response['status'] = "success";
    $response['message'] = "You have been successfully signed in! ";
} else {
    $response['status'] = "error";
    $response['message'] = $e->error;
}
echo json_encode($response);