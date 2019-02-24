<?php
Class User {
    /*** TODO ADD THESE FEATURES
     * MAKE A DATABASE TABLE FOR THE UNRECOGNIZED EMAILS
     * CHECKING FOR USER ROLES
     * CHECKING FOR ANY CONTACT THREADS THAT THIS USER STARTED
     * GETTING THE AVAILABLE ELECTIONS FOR A USER
     * CHECKING TO SEE IF A USER HAS VOTED FOR AN ELECTION ALREADY
     */

    public $email, $status, $u_id, $unrecognized_request;
    private $elections;

    public function __construct($email, $u_id){
        $search_u_id = count(self::searchUser($u_id));

        $this->u_id = $u_id;
        $this->email = $email;

        if( ($search_u_id) > 1 ){
            //before we end everything, do another check to make sure that their voting hash isn't ONLY a hash of their email address
            if( $u_id == hash('sha256', $email) ){
                //their u_id hasn't been readied yet, kill the session and have them sign in again
                Session::deleteSession();
                echo "<p class='text-center'>Please reload the page and sign in again</p>";
                exit;
            }

            //a status of 1 means that they are verified and ready to vote!
            $this->status = 1;

        } else {
            $this->unrecognized_request = self::searchUnrecognized($this->email);
            if( count($this->unrecognized_request) > 1 ){
                //a status of 0 means that they submitted a request to have their data
                $this->status = 0;
                if( intval($this->unrecognized_request['approval']) > -1 ){
                    //check to see if they were added into the database
                    if( count(self::searchUser(hash("sha256", $this->unrecognized_request['email']))) < 2 ){
                        self::createUser($this->unrecognized_request['email'], $this->unrecognized_request['grade']);
                    }

                    //their request was approved, sign them out
                    Session::deleteSession();
                    echo "<p class='text-center'>Please reload the page and sign in again</p>";
                    exit;
                }
            } else {
                //a status of -1 means that they are completely new and we have no record of them, they can't vote yet
                $this->status = -1;
            }
        }

    }

    public static function readyUserId($email, $salt){
        $hash_e = hash("sha256", $email);
        $u_id = hash("sha256", $email.$salt);
        $search_e = count(self::searchUser($hash_e));

        if( $search_e > 0 ){
            //they still have a standard hashed email as their user_id, add the salt in
            Database::secureQuery(
                "UPDATE `users` SET `user_id` = :u_id WHERE `user_id` = :hash_e",
                array(
                    ":u_id"=>$u_id,
                    "hash_e"=>$hash_e
                ),
                null
            );
        }

        return $u_id;
    }

    //function to quickly return user's info using user_id
    public static function searchUser($u_id){
        return Database::secureQuery(
            "SELECT * FROM `users` WHERE `user_id` = :u_id ",
            array(
                ":u_id"=>$u_id,
            ),
            'fetch'
        );
    }
    public static function createUser($email, $grade){
        //insert the new user into the database
        try{
            $u_id = hash("sha256", $email);
            Database::secureQuery(
                "INSERT INTO `users`(`user_id`, `grade`) VALUES (:u_id, :grade)",
                array(":u_id"=>$u_id, ":grade"=>$grade),
                null);
            return true;
        } catch(Exception $e){
            return false;
        }

    }
    public static function searchUnrecognized($email){
        return Database::secureQuery(
            "SELECT * FROM `unrecognized_emails` WHERE `email` =:email ",
            array(":email"=>$email),
            'fetch'
        );
    }
    public function getElections(){
        if( ! isset($this->elections) ){
            $data = Database::secureQuery(
                "SELECT * FROM `elections` WHERE `grade` LIKE :grade",
                array(":grade"=>"%".$this->grade."%"),
                null
            );
            foreach( $data as $i ){
                $this->elections[] = new Election($i['db_code']);
            }
        }
        return $this->elections;
    }
    public static function getAdminEmails($permission){
        $e = Database::secureQuery(
            "SELECT `email` FROM `roles` WHERE (`association` = 'admin') AND (`privileges` = '*' OR `privileges` LIKE :p)",
            array(":p"=>"%".$permission."%"),
            null);
        foreach( $e as $i ){
            $response[] = $i['email'];
        }
        return $response;

    }
    public function makeFormToken($request, $extra, $expiration){
        $expiration = date("Y-m-d H:i:s", $expiration);
        $token = bin2hex(random_bytes(64));
        $data = Database::secureQuery(
            "SELECT * FROM `form_tokens` WHERE `user_id` = :u AND `request` = :r AND `extra` = :e",
            array(
                ":u"=>$this->u_id,
                ":r"=>$request,
                ":e"=>$extra,
                ),
            'fetch');
        if( count($data) > 1 ){
            //they already have a token previously generated, check if it has been used or expired yet
            if( strtotime($data['expires']) > time() ){
                return $data['token'];
            }
            Database::secureQuery(
                "DELETE FROM `form_tokens` WHERE `token` = :t",
                array(":t"=>$data['token']),
                null);
        }
        Database::secureQuery(
            "INSERT INTO `form_tokens`(`token`, `user_id`, `request`, `extra`, `expires`) VALUES (:t, :u, :r, :ext, :exp)",
            array(
                ":t"=>$token,
                ":u"=>$this->u_id,
                ":r"=>$request,
                ":ext"=>$extra,
                ":exp"=>$expiration
            ), null
        );
        return $token;
    }

    public function getFormTokenData($token){
        $now = date("Y-m-d H:i:s");
        return Database::secureQuery(
            "SELECT * FROM `form_tokens` WHERE `token` = :t AND `user_id` = :u AND `expires` > :n",
            array(":t"=>$token, ":u"=>$this->u_id, ":n"=>$now),
            'fetch');
    }

    public static function useFormToken($token){
        Database::secureQuery(
            "DELETE FROM `form_tokens` WHERE `token` = :t",
            array(":t"=>$token),
            null);
    }
    public static function adminPermissions(){
        return array("u_e"=>"Unrecognized Email Approval");
    }
}