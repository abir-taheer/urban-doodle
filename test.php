<?php
$config = json_decode(file_get_contents("config.json"), true);
echo file_get_contents("http://".$config['domain']."/static/test/index.php");