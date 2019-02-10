<?php

$c = scandir("../css");
print_r($c);
foreach( $c as $d ){
	if( $d == "." or $d == ".." ){
		continue;
	}
	echo $d. "  ----- ".base64_encode(hash("sha256", file_get_contents($d)))."<br>"; 
}
