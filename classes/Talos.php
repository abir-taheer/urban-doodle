<?php
class Talos {
    private $csrf_token, $session;
    public $status;
    public function __construct($username, $password) {
        // Make a curl request to Talos' sign in page
        $csrf_request = curl_init();
        curl_setopt_array($csrf_request, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_URL => "https://talos.stuy.edu/auth/login/"
        ]);
        $csrf_response = curl_exec($csrf_request);

        // Get the csrf cookie from the http response header
        preg_match_all("/^Set-Cookie:\s*([^;]*)/mi", $csrf_response, $login_cookies);
        $this->csrf_token = substr($login_cookies[1][0], 10);

        // Get the csrfmiddleaware form token
        preg_match("/name='csrfmiddlewaretoken' value='(.*)'/", $csrf_response, $csrf_form);
        $csrfmiddleaware = $csrf_form[1];

        // Sign into Talos using the CSRF Cookie and The CSRF form token
        $sign_in_request = curl_init();
        curl_setopt_array($sign_in_request, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_URL => "https://talos.stuy.edu/auth/login/",
            CURLOPT_COOKIE => "csrftoken=".$this->csrf_token,
            CURLOPT_REFERER => "https://talos.stuy.edu/auth/login/",
            CURLOPT_USERAGENT => org_name." Voting Agent",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                "username" => $username,
                "password" => $password,
                "csrfmiddlewaretoken" => $csrfmiddleaware,
                "next" => ""
            )
        ]);
        $sign_in_response = curl_exec($sign_in_request);
        // Get new session cookies this time
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $sign_in_response, $signed_in_cookies);
        if( ! isset($signed_in_cookies[1][1]) || substr($signed_in_cookies[1][1], 0, 9) !== "sessionid" ){
            $this->status = false;
        } else {
            $this->session = substr($signed_in_cookies[1][1], 10);
            $this->status = true;
        }
    }

    public function searchApi($query){
        // TODO IMPLEMENT THIS
        $first_request = curl_init();
        curl_setopt_array($first_request, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://talos.stuy.edu/api/students/?format=json&search=".urlencode($query),
            CURLOPT_COOKIE => "csrftoken=".$this->csrf_token.";sessionid=".$this->session,
            CURLOPT_USERAGENT => org_name." Voting Agent",
        ]);
        $first_response = curl_exec($first_request);

        $first_data = json_decode($first_response, true);

        $limit = $first_data["count"] + 1;

        $second_request = curl_init();
        curl_setopt_array($second_request, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://talos.stuy.edu/api/students/?format=json&search=".urlencode($query)."&limit=".$limit,
            CURLOPT_COOKIE => "csrftoken=".$this->csrf_token.";sessionid=".$this->session,
            CURLOPT_USERAGENT => org_name." Voting Agent",
        ]);

        $search_response = json_decode(curl_exec($second_request), true);

        return $search_response;
    }
}
