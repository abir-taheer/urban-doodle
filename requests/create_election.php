<?php
$name = $_POST["name"];
$errors = [];

if( trim($name) === "" ){
    $errors[] = "Election name cannot be empty";
}

if( file_exists(app_root."/classes/election_handlers/".$_POST["type"].".php") ){
    include app_root."/classes/election_handlers/".$_POST["type"].".php";
    if( ! in_array("ElectionHandler", class_implements($_POST["type"]) ) ){
        $errors[] = "That election type does not implement the ElectionHandler Interface. Please contact the developer!";
    }

} else {
    $errors[] = "That election type does not exist";
}

$time_created = true;

if( ! isset($_POST["start"]) || $_POST["start"] === "" ){
    $errors[] = "Election start time cannot be empty!";
    $time_created = false;
}

if( ! isset($_POST["end"]) || $_POST["end"] === "" ){
    $errors[] = "Election end time cannot be empty!";
    $time_created = false;
}

if( $time_created ) {
    try {
        $start_time = new DateTime($_POST["start"], new DateTimeZone(app_time_zone));
    } catch (Exception $e){
        $errors[] = "Election start datetime is not formatted correctly";
        $time_created = false;
    }
    try {
        $end_time = new DateTime($_POST["end"], new DateTimeZone(app_time_zone));
    } catch (Exception $e){
        $errors[] = "Election end datetime is not formatted correctly";
        $time_created = false;
    }
}

if( !isset($_POST["grade"]) ){
    $errors[] = "At least 1 grade must be able to vote for this election";
}

if( $time_created ){

    $start_time->setTimezone(new DateTimeZone("UTC"));
    $end_time->setTimezone(new DateTimeZone("UTC"));

    if( ! ($start_time < $end_time) ){
        $errors[] = "Election start time must be before the end time";
    }
    if( Web::getUTCTime() > $start_time ){
        $errors[] = "Election start time must be in the future";
    }
}

if( trim($_POST["pic"]) !== "" ){
    if( ! file_exists(app_root."/public/static/img/election_covers/".trim($_POST["pic"])) ){
        $errors[] = "That image does not exist";
    }
} else {
    $errors[] = "No election image was provided";
}

if( count($errors) === 0 ){
    // Add the election to the database
    $db_code = bin2hex(random_bytes(4));
    $grades = "";
    foreach( $_POST["grade"] as $grade=>$off ){
        $grades.= "[".$grade."]";
    }
    Database::secureQuery(
        "INSERT INTO `elections`(`db_code`, `name`, `type`, `start_time`, `end_time`, `grade`, `pic`) VALUES (:d, :n, :t, :s, :e, :g, :p)",
        array(
            ":d"=>$db_code,
            ":n"=>$_POST["name"],
            ":t"=>$_POST["type"],
            ":s"=>$start_time->format("Y-m-d H:i:s"),
            ":e"=>$end_time->format("Y-m-d H:i:s"),
            ":g"=>$grades,
            ":p"=>$_POST["pic"]),
        null
    );
    mkdir(app_root."/public/static/elections/".$db_code);
    mkdir(app_root."/public/static/elections/".$db_code."/candidates");
    $response["status"] = "success";
} else {
    $response["status"] = "error";
    $response["message"] = $errors;
}
