<?php
class Nonce {
    public static function getNonce(){
        //this is something that we can use PHP's built in sessions for, since they will only need to last for the duration of the session
        session_name("nonce_ref");
        session_start();
        return (isset($_SESSION['nonce'])) ? $_SESSION['nonce'] : self::setNonce();
    }
    public static function setNonce(){
        $n = base64_encode(bin2hex(random_bytes(16)));
        $_SESSION['nonce'] = $n;
        return $n;
    }
}