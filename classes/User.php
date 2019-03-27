<?php
Class User {
    /*** TODO ADD THESE FEATURES
     * MAKE A DATABASE TABLE FOR THE UNRECOGNIZED EMAILS
     * CHECKING FOR USER ROLES
     * CHECKING FOR ANY CONTACT THREADS THAT THIS USER STARTED
     * GETTING THE AVAILABLE ELECTIONS FOR A USER
     * CHECKING TO SEE IF A USER HAS VOTED FOR AN ELECTION ALREADY
     */

    public $email, $status, $u_id, $grade, $unrecognized_request;
    private $elections;

    /**
     * User constructor.
     * @param string $email The email address of the user. Used to check for unrecognized emails in such cases
     * @param string $u_id The user ID of the user, if this is not salted, the session will be destroyed
     */
    public function __construct($email, $u_id){
        $search_u_id = self::searchUser($u_id);

        $this->u_id = $u_id;
        $this->email = $email;

        if( count($search_u_id) > 1 ){
            //before we end everything, do another check to make sure that their voting hash isn't ONLY a hash of their email address
            if( $u_id === hash('sha256', $email) ){
                //their u_id hasn't been readied yet, kill the session and have them sign in again
                Session::deleteSession();
                echo "<p class='txt-ctr mdc-layout-grid__cell--span-12'>Please reload the page and sign in again</p>";
                exit;
            }

            //a status of 1 means that they are verified and ready to vote!
            $this->status = 1;
            $this->grade = $search_u_id['grade'];

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
                    echo "<p class='txt-ctr mdc-layout-grid__cell--span-12'>Please reload the page and sign in again</p>";
                    exit;
                }
            } else {
                //a status of -1 means that they are completely new and we have no record of them, they can't vote yet
                $this->status = -1;
            }
        }

    }

    /**
     * Return the salted hash of the user's email and also update their hash to such if their hash is still only a hash of their email address
     * @param string $email The email address to check
     * @param string $salt The sub value from Google
     * @return string
     */
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

    /**
     * Query the users table for $u_id and returns associative array
     * @param string $u_id The user ID to check
     * @return array
     */
    public static function searchUser($u_id){
        return Database::secureQuery(
            "SELECT * FROM `users` WHERE `user_id` = :u_id ",
            array(
                ":u_id"=>$u_id,
            ),
            'fetch'
        );
    }

    /**
     * Add an entry to the database of users. Returns true on success, false on error
     * @param string $email The email address of the user
     * @param string $grade The grade of the user to add
     * @return bool
     */
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

    /**
     * Queries the Unrecognized Emails table for the given email
     * @param string $email The email address to check
     * @return array
     */
    public static function searchUnrecognized($email){
        return Database::secureQuery(
            "SELECT * FROM `unrecognized_emails` WHERE `email` =:email ",
            array(":email"=>$email),
            'fetch'
        );
    }

    /**
     * Get an array of Election objects that the user is allowed to vote for
     * @return Election[]
     * @throws Exception
     */
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

    /**
     * Returns a list of the emails of admins who have the permissions specified in $permission
     * @param string $permission The permissions code that the admins must have
     * @return array
     */
    public static function getAdminEmails($permission){
        $e = Database::secureQuery(
            "SELECT `email` FROM `roles` WHERE (`association` = 'admin') AND (`privileges` = '*' OR `privileges` LIKE :p)",
            array(":p"=>"%".$permission."%"),
            null);
        $response = array();
        foreach( $e as $i ){
            $response[] = $i['email'];
        }
        return $response;

    }

    /**
     * Makes a form token to be sent with forms that tells the site which script files to hand the form data off to
     * @param string $request The name of the file associated with performing the request
     * @param string $extra Extra information to be used by the file processing the request
     * @param DateTime $expiration Date object of the expiration time for the token
     * @return string
     * @throws Exception
     */
    public function makeFormToken($request, $extra, $expiration){
        $expiration = $expiration->format("Y-m-d H:i:s");
        $token = bin2hex(random_bytes(36));
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
            if( Web::UTCDate($data['expires']) > Web::getUTCTime() ){
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
        $now = Web::getUTCTime()->format("Y-m-d H:i:s");
        return Database::secureQuery(
            "SELECT * FROM `form_tokens` WHERE `token` = :t AND `user_id` = :u AND `expires` > :n",
            array(":t"=>$token, ":u"=>$this->u_id, ":n"=>$now),
            'fetch');
    }

    /**
     * Deletes the form token with the `token` equal to $token
     * @param $token
     */
    public static function deleteFormToken($token){
        Database::secureQuery(
            "DELETE FROM `form_tokens` WHERE `token` = :t",
            array(":t"=>$token),
            null);
    }

    public function createConfirmToken($db_code, $votes){
        $data = Database::secureQuery("SELECT * FROM `vote_confirmations` WHERE `user_id` = :u AND `db_code` = :d AND `expires` > CURRENT_TIMESTAMP",
            array(":u"=>$this->u_id,":d"=>$db_code),
            "fetch");
        $expires = Web::UTCDate("+1 hour")->format(DATE_ATOM);
        if( count($data) > 1 ){
            $track = $data["track"];
            Database::secureQuery(
                "UPDATE `vote_confirmations` SET `content` = :c, `expires` = :e WHERE `track` = :t",
                array(":c"=>$votes, ":e"=>$expires, ":t"=>$data["track"]), null);
        } else {
            $track = bin2hex(random_bytes(36));
            Database::secureQuery("INSERT INTO `vote_confirmations` (`track`, `user_id`, `content`, `db_code`, `expires`) VALUES (:t, :u, :c, :d, :e)", array(":t"=>$track, ":u"=>$this->u_id, ":c"=>$votes, ":d"=>$db_code, ":e"=>$expires), null);
        }
        return $track;
    }

    public static function getConfirmationData($confirmation_id){
        return Database::secureQuery("SELECT * FROM `vote_confirmations` WHERE `track` = :t", array(":t"=>$confirmation_id), 'fetch');
    }

    public static function deleteConfirmToken($track){
        Database::secureQuery("DELETE FROM `vote_confirmations` WHERE `track` = :t", array(":t"=>$track), null);
    }
    public static function adminPermissions(){
        return array("u_e"=>"Unrecognized Email Approval");
    }
}