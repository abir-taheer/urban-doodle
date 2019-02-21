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
            //a status of 1 means that they are verified and ready to vote!
            $this->status = 1;
        } else {
            $this->unrecognized_request = self::searchUnrecognized($this->email);
            if( count($this->unrecognized_request) > 1 ){
                //a status of 0 means that they submitted a request to have their data
                $this->status = 0;
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
            Database::secureQuery("INSERT INTO `users`(`user_id`, `grade`) VALUES (:u_id, :grade)",array(":u_id"=>$u_id, ":grade"=>$grade), 'null');
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
            $this->elections = Database::secureQuery(
                "SELECT * FROM `elections` WHERE `grade` LIKE :grade",
                array(":grade"=>"%".$this->grade."%"),
                null
            );
        }
        return $this->elections;
    }
    public static function getAdminEmails($permission){
        $e = Database::secureQuery("SELECT `email` FROM `roles` WHERE (`association` = 'admin') AND (`privileges` = '*' OR `privileges` LIKE :p)", array(":p"=>"%".$permission."%"), null);
        foreach( $e as $i ){
            $response[] = $i['email'];
        }
        return $response;

    }
    public static function adminPermissions(){
        return array("u_e"=>"Unrecognized Email Approval");
    }
}