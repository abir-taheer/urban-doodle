<?php
ob_start();
require_once "../config.php";
//autoload necessary classes
spl_autoload_register(function ($class_name) {
    require_once "../classes/".$class_name . '.php';
});

// No 404 handling for this page since it shouldn't be directly accessible by the user

$path = explode("/", $_GET['page']);

$pages = [
    "candidates" => [
        "default"=>"profile.php",
        "file_loc" => 3
    ],
    "admin" => [
        "default" => "elections.php",
        "file_loc" => 2
    ],
    "campaign" => [
        "default" => "profile.php",
        "file_loc" => 3
    ]
];

if( isset($pages[$path[1]]) ){

    $main_page = $pages[$path[1]];

    if( $path[1] === "campaign" ){
        if( ! Session::hasSession() || ! Session::getUser()->isManager()){
            echo "<p class=\"mdc-layout-grid__cell--span-12\">You do not have permission to access this page</p>";
            ob_flush();
            exit;
        }

        $candidate = new Candidate($path[2]);
        $user = Session::getUser();
        $path[3] = $path[3] === "" || ! isset($path[3])  ? "profile" : $path[3];
        $privileges =  $user->getCampaignPrivileges($candidate->id);
        if( ! $candidate->constructed || ! $user->isManagerFor($candidate->id)){
            // ID WAS INVALID

            // Send them back to the campaign page with the hope that they get redirected to the proper
            Web::sendRedirect("/campaign");
            ob_flush();
            exit;
        }

        if(! $user->hasCampaignPrivileges($candidate->id, $path[3]) ){
            // They don't have permissions to access this page.
            // Redirect them to a page that they do have permissions to access
            echo "<p class=\"mdc-layout-grid__cell--span-12\">Click on one of the tabs above.</p>";
            ob_flush();
            exit;
        }

    }

    if( $path[1] === "admin" ){
        $user = Session::getUser();
        if( ! Session::hasSession() || ! $user->isAdmin()){
            echo "<p class=\"mdc-layout-grid__cell--span-12\">You do not have permission to access this page</p>";
            ob_flush();
            exit;
        }

        if( ! isset($path[2]) || $path[2] === ""){
            echo "<a class=\"mdc-layout-grid__cell--span-12\">Select an item from the menu above</a>";
            ob_flush();
            exit;
        }
    }

    if( file_exists("../subpages/". $path[1]."/".$path[$main_page["file_loc"]].".php") ){
        require_once "../subpages/". $path[1]."/".$path[$main_page["file_loc"]].".php";
        Web::sendDependencies();
        ob_flush();
    }
}