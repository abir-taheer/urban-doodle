<?php
class Web {
    public static $dependencies = array("script"=>array(), "css"=>array());
    public static function getNonce(){
        //this is something that we can use PHP's built in sessions for, since they will only need to last for the duration of the session
        session_name("Nonce_Ref");
        session_start();
        return (isset($_SESSION['nonce'])) ? $_SESSION['nonce'] : self::setNonce();
    }
    public static function setNonce(){
        $n = base64_encode(bin2hex(random_bytes(16)));
        $_SESSION['nonce'] = $n;
        return $n;
    }
    public static function addScript($src){
        self::$dependencies['script'][] = $src;
    }
    public static function addCSS($src){
        self::$dependencies['css'][] = $src;
    }
    public static function sendDependencies(){
        if( count(self::$dependencies['script']) + count(self::$dependencies['css']) > 0 ){
            header("X-Nonce: ".self::getNonce());
            header("X-Fetch-New-Sources: true");
            header("X-New-Sources: ".json_encode(self::$dependencies));
        } else {
            header("X-Fetch-New-Sources: false");
        }
    }
}