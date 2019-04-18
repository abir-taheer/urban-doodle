<?php
class Web {
    private static $utc_time;
    public static $headers_sent = false;

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
//        [
//            "text"=>"My Feed",
//            "icon"=>"person",
//            "page"=>"/",
//            "session"=>"true"
//        ],
        [
            "text"=>"Elections",
            "icon"=>"how_to_vote",
            "page"=>"/elections",
            "session"=>"*"
        ],
//        [
//            "text"=>"Results",
//            "icon"=>"ballot",
//            "page"=>"/results",
//            "session"=>"*"
//        ],
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
        if( self::$headers_sent ){
            return;
        }
        self::$headers_sent = true;
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

    public static function getTitle($path)
    {
        switch ($path[1]) {
            case "candidates":
                // The Election is set, include that in the title
                if ( isset($path[2]) && $path[2] !== "") {
                    try{
                        $election = new Election($path[2]);
                        if( isset($path[3]) && $path[3] !== "" ){
                            $candidate = new Candidate($path[3]);
                            if ($candidate->constructed) {
                                return $candidate->name . " for " . $candidate->getElection()->name . " | " . web_title;
                            } else {
                                return "No Candidate Found | " . web_title;
                            }
                        } else {
                            return "View Candidates for ".$election->name." | ".web_title;
                        }
                    } catch (Exception $e){
                        return "Invalid Election | ".web_title;
                    }
                } else {
                    return "View Candidates | ".web_title;
                }
                break;
            case "elections":
                return "View Elections | ".web_title;
                break;
            case "vote":
                try {
                    $election = new Election($path[2]);
                    return $election->name." | ".web_title;
                } catch (Exception $e){
                    return "No Current Election Found | ".web_title;
                }
                break;
            case "contact":
                return "Contact Us | ".web_title;
                break;
            default:
                return web_title;
        }
    }

    public static function getDescription($path){
        switch ($path[1]) {
            case "candidates":
                // The Election is set, include that in the title
                if ( isset($path[2]) && $path[2] !== "") {
                    try{
                        $election = new Election($path[2]);
                        if( isset($path[3]) && $path[3] !== "" ){
                            $candidate = new Candidate($path[3]);
                            if ($candidate->constructed) {
                                return "View updates and ask questions to candidate: ".$candidate->name . " who is currently running for " . $candidate->getElection()->name;
                            } else {
                                return "The id that was passed using the url does not belong to any candidate";
                            }
                        } else {
                            return "View the profiles and updates of candidates running for ".$election->name;
                        }
                    } catch (Exception $e){
                        return "There is no current election with the id that was passed via the url";
                    }
                } else {
                    return "Choose an election to view the candidates for that election.";
                }
                break;
            case "elections":
                return "Choose an election to be able to vote for that election";
                break;
            case "vote":
                try {
                    $election = new Election($path[2]);
                    return "Vote for ".$election->name;
                } catch (Exception $e){
                    return "There currently is no election with the ID that was passed via the url.";
                }
                break;
            case "contact":
                return "Let us know how we're doing; give us any updates and suggestions; tell us anything you think we should know.";
                break;
            default:
                return web_description;
        }
    }
    public static function sendRedirect($path){
        header("X-Page-Redirect: ".$path);
    }
    public static function getSocialPic($path){
        switch($path[1]){
            case "vote":
                if( isset($path[2]) && $path[2] !== "" ){
                    try{
                        $election = new Election($path[2]);
                        return "https://abir.taheer.me/resources/images/vote.jpg";
                    } catch(Exception $e){
                        // Let this just go onto the default
                    }
                }
            case "candidates":
                if( isset($path[3]) && $path[3] !== ""){
                    $candidate = new Candidate($path[3]);
                    if($candidate->constructed){
                        return "/static/elections/".$candidate->db_code."/candidates/".$candidate->id.".png";
                    }
                }
            default:
                return app_icon;
        }
    }

}