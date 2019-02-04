<?php
class Config{
    public static $store = [];
    public static $config_location = "../config.json";

    //If the config isn't already stored in a variable, do that so we don't have to keep asking the filesystem
    public static function setVariables(){
        if( self::$store === [] ){
            $curr_dir = explode("/",__DIR__);
            while( count($curr_dir) > 0 && ! file_exists("/".implode("/", $curr_dir)."/config.json") ){
                unset($curr_dir[count($curr_dir) - 1]);
            }
            if( ! file_exists("/".implode("/", $curr_dir)."/config.json") ){
                echo "Exception: COULD NOT LOCATE 'config.json' FILE.\n";
                exit;
            }
            self::$store = json_decode(file_get_contents("/".implode("/", $curr_dir)."/config.json"), true);
        }
    }
    public static function getConfig(){
        self::setVariables();
        return self::$store;
    }
}