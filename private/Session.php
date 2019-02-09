<?php
class Session{
    private static $voter_info, $id_info;
    public static function hasSession(){
        $checkID = self::getIdInfo();
        $checkVoter = self::getVoterInfo();
        if(
            count($checkID) > 1 &&
            count($checkVoter) > 1 &&
            time() < strtotime($checkID['expires']) &&
            time() < strtotime($checkVoter['expires'])
        ){
            return true;
        }
        /** @return boolean */
        return false;
    }

    public static function getVoterInfo(){
        /** @return array */
        return Database::secureQuery(
            "SELECT * FROM `voter_tokens` WHERE `cookie_id` = :cookie",
            array(
                ":cookie"=>$_COOKIE[Config::getConfig()['voting_cookie_name']]
            ),
            "fetch"
        );
    }

    public static function getIdInfo(){
        /** @return array */

        //store in a variable to reduce number of queries made
        if( !isset(self::$id_info) ){
            self::$id_info = Database::secureQuery(
                "SELECT * FROM `id_tokens` WHERE `cookie_id` = :cookie ",
                array(
                    ":cookie"=>$_COOKIE[Config::getConfig()['id_cookie_name']]
                ),
                "assoc"
            );
        }
        return self::$id_info;
    }

    public static function getUserId(){
        /** @return string */
        return self::getVoterInfo()['user_id'];
    }

    public static function getEmail(){
        return self::getIdInfo()['email'];
    }

    //creates a record in the database with given data and creates a voting session cookie to reference the data
    public static function createVotingSession($email_hash, $expires){
        $track = bin2hex(random_bytes(64));
        $string_expires = date("Y-m-d H:i:s", $expires);
        Database::secureQuery(
            "INSERT INTO `voter_tokens`(`cookie_id`, `user_id`, `expires`) VALUES (:cookie, :hsh, :exp)",
            array(
                ":cookie"=>$track,
                ":hsh"=>$email_hash,
                ":exp"=>$string_expires
            ),
            null
        );
        setcookie(Config::getConfig()['voting_cookie_name'], $track, $expires, "/", Config::getConfig()['domain'], Config::getConfig()['ssl'], true);
    }

    //creates a record in the database with given data and creates a identity session cookie to reference the data
    public static function createIdSession($first, $last, $email, $picture, $expires){
        $track = bin2hex(random_bytes(64));
        $string_expires = date("Y-m-d H:i:s", $expires);
        Database::secureQuery(
            "INSERT INTO `id_tokens`(`cookie_id`, `first_name`, `last_name`, `email`, `picture`, `expires`) VALUES (:cookie, :frst, :lst, :email, :pic, :exp)",
            array(
                ":cookie"=>$track,
                ":frst"=>$first,
                ":lst"=>$last,
                ":email"=>$email,
                ":pic"=>$picture,
                "exp"=>$string_expires
            ),
            null
        );
        setcookie(Config::getConfig()['id_cookie_name'], $track, $expires, "/", Config::getConfig()['domain'], Config::getConfig()['ssl'], true);
    }
}