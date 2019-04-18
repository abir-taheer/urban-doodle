<?php
class VotingStation
{
    public static function isVotingStation() {
        return $_COOKIE["Voting_Station"] === "true";
    }

    public static function makeVotingStation() {
        setcookie("Voting_Station", "true", Web::UTCDate("+1 day"), "/", web_domain, web_ssl, false);
    }
}