<?php
spl_autoload_register(function ($class_name) {
    include "../private/".$class_name . '.php';
});
Session::deleteSession();