<?php
$background_colors = ["00b894","6c5ce7", "00cec9", "ff7675", "74b9ff", "fd79a8"];

$errors = [];

if( ! isset($_POST["name"]) || trim($_POST["name"]) === ""){
    $errors[] = "Candidate name cannot be empty";
}

if( !isset($_POST["initials"]) || trim($_POST["initials"]) === "" ){
    $errors[] = "Initials cannot be empty";
}
if( strlen($_POST["name"]) > 256 ){
    $errors[] = "Name must be less than 256 characters";
}


try {
    $election = new Election($form["extra"]);
} catch( Exception $e){
    $errors[] = "That election is no longer open";
}

if( count($errors) === 0 ){
    $candidate_id = bin2hex(random_bytes(4));
    $length_initials = strlen($_POST["initials"]);
    $length_initials = ($length_initials > 5 ) ? 5 : $length_initials;
    Database::secureQuery("INSERT INTO `candidates` (`id`, `db_code`, `name`) VALUES(:i, :d, :n)", [":i"=>$candidate_id, ":d"=>$election->db_code, ":n"=>$_POST["name"]], null);

    $color = $background_colors[random_int(0, count($background_colors) - 1)];
    $pic = file_get_contents("https://ui-avatars.com/api/?size=512&font-size=0.35&color=ffffff&name=".rawurlencode($_POST["initials"])."&background=".rawurlencode($color))."&length=".rawurlencode($length_initials);
    file_put_contents(app_root."/public/static/elections/".addslashes($election->db_code)."/candidates/".addslashes($candidate_id).".jpg", $pic);

    if( isset($_POST["editor"]) ){
        foreach($_POST["editor"] as $editor){
            $email = $editor["email"];
            if( !isset($email) || trim($email) === "" || ! filter_var($email, FILTER_VALIDATE_EMAIL) ){
                continue;
            }
            $track = bin2hex(random_bytes(8));
            $role = "Main Editor";
            $privileges = "*";
            Database::secureQuery("INSERT INTO `roles` (`track`, `email`, `association`, `role`, `privileges`) VALUES (:t, :e, :a, :r, :p)", [":t"=>$track, ":e"=>$email, ":a"=>$candidate_id, ":r"=>$role, ":p"=>$privileges], null);
        }
        $response["status"] = "success";
    }
} else {
    $response["message"] = $errors;
}