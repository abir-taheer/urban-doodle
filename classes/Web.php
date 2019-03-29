<?php
class Web {
    private static $utc_time;

    /**
     * An array of all of the items to be generated that will appear on the menu
     * @var array
     */
    public static $menu_pages = [
        [
            "text"=>"Home",
            "icon"=>"home",
            "page"=>"/",
            "session"=>"false"
        ],
        [
            "text"=>"My Feed",
            "icon"=>"person",
            "page"=>"/",
            "session"=>"true"
        ],
        [
            "text"=>"Elections",
            "icon"=>"how_to_vote",
            "page"=>"/elections",
            "session"=>"*"
        ],
        [
            "text"=>"Results",
            "icon"=>"ballot",
            "page"=>"/results",
            "session"=>"*"
        ],
        [
            "text"=>"Candidates",
            "icon"=>"people",
            "page"=>"/candidates",
            "session"=>"*"
        ],
        [
            "text"=>"Contact Us",
            "icon"=>"chat_bubble",
            "page"=>"/contact",
            "session"=>"*"
        ],
        [
            "text"=>"Help",
            "icon"=>"help",
            "page"=>"/help",
            "session"=>"*"
        ]
    ];
    public static $dependencies = array("script"=>array(), "css"=>array());

    /**
     * Returns a nonce to be used for the current user's browsing session
     * Stores the nonce using PHP Sessions
     * Generates new one if session does not exist and stores it in the session
     * @return string
     */
    public static function getNonce(){
        //this is something that we can use PHP's built in sessions for, since they will only need to last for the duration of the session
        session_name("Nonce_Ref");
        session_start();
        return (isset($_SESSION['nonce'])) ? $_SESSION['nonce'] : self::setNonce();
    }

    /**
     * Helper function to create a unique nonce and store it in the session
     * @return string
     */
    public static function setNonce(){
        try {
            $n = base64_encode(bin2hex(random_bytes(16)));
        } catch (Exception $e){
            $crypto_strong = true;
            $n = bin2hex(openssl_random_pseudo_bytes(16, $crypto_strong));
        }
        $_SESSION['nonce'] = $n;
        return $n;
    }

    /**
     * Adds a necessary script to be sent in the header so that it can be appended to the site
     * @param string $src The path to the script file
     */
    public static function addScript($src){
        self::$dependencies['script'][] = $src;
    }

    /**
     * Adds a necessary stylesheet to be sent in the header so that it can be appended to the site
     * @param string $src The path to the stylesheet file
     */
    public static function addCSS($src){
        self::$dependencies['css'][] = $src;
    }


    /**
     * Reports to client whether or not dependencies are needed to render the page properly
     * Sends relevant information using HTTP headers
     */
    public static function sendDependencies(){
        if( count(self::$dependencies['script']) + count(self::$dependencies['css']) > 0 ){
            header("X-Nonce: ".self::getNonce());
            header("X-Fetch-New-Sources: true");
            header("X-New-Sources: ".json_encode(self::$dependencies));
        } else {
            header("X-Fetch-New-Sources: false");
        }
    }

    /**
     * Returns a DateTime instance with the current time, and the timezone set to UTC
     * @return DateTime
     */
    public static function getUTCTime(){
        if( !isset(self::$utc_time) ){
            try {
                self::$utc_time = new DateTime("now", new DateTimeZone("UTC"));
            } catch (Exception $e) {
                echo $e->getMessage();
                exit;
            }
        }
        return self::$utc_time;
    }

    /**
     * Returns an instance of the DateTime class in accordance with a given time
     * @param string $time A string that is in accordance with the accepted values for the "time" parameter of the DateTime constructor
     * @return DateTime An instance of the DateTime class, with the timezone set to UTC
     */
    public static function UTCDate($time){
        try {
            return new DateTime($time, new DateTimeZone("UTC"));
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}