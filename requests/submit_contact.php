<?php
$track = bin2hex(random_bytes(32));
Database::secureQuery("INSERT INTO `contact` (`track`, `from`, `content`) VALUES (:t, :f, :c)",array(":t"=>$track, ":f"=>$user->email, ":c"=>$_POST["message"]), null);
$response["status"] = "success";