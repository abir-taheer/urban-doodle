<?php
class GoogleAuth {
    public $status, $sub, $email, $domain, $name, $first_name, $last_name, $pic, $error;
    public function __construct($token){
        $data = json_decode(file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=".$token), true);
        if( $data === null ){
            $this->error[] = "The provided authentication token is invalid";
        } else {

            if( $data['aud'] !== google_auth_client_id ){
                $this->error[] = "The authentication token provided does not belong to this app";
            }
            if( $data['email_verified'] !== "true" ){
                $this->error[] = "According to Google, your email address has not yet been verified. Please resolve this before trying again.";
            }
            if( ! self::checkOrganizationAllowed($data['hd']) ){
                $this->error[] = "You are not allowed to sign in with an email address belonging to that organization.";
            }
        }

        if( !isset($this->error) ){
            $this->status = true;
            $this->email = $data['email'];
            $this->domain = $data['hd'];
            $this->name = $data['name'];
            $this->pic = $data['picture'];
            $this->sub = $data['sub'];
            $this->first_name = $data['given_name'];
            $this->last_name = $data['family_name'];
        } else {
            $this->status = false;
        }
    }
    public static function checkOrganizationAllowed($org){
        //if the user left it as an asterisk, allow all domains to sign in
        $domains = auth_allowed_org;
        if( $domains === "*" ){
            return true;
        }

        $domains_arr = array_map('trim', explode(",", $domains));
        if( in_array($org, $domains_arr) ){
            return true;
        }
        return false;
    }
}