<?php
class Config{
    public static $store = [];
    public static $config_location = "config.json";

    //If the config isn't already stored in a variable, do that so we don't have to keep asking the filesystem
    public static function setVariables(){
        if( self::$store === [] ){
            //separate each part of the directories into an array
            $dir_count = count(explode(DIRECTORY_SEPARATOR, __dir__));
            $x = 0;

            //keep trying to go into a higher directory to look for the file and stop at highest directory
            while( ! file_exists(self::$config_location) and $x < $dir_count ){
                self::$config_location = "../".self::$config_location;
                $x++;
            }

            //if after the searching the file still could not be found, echo the error and exit script execution
            if( ! file_exists(self::$config_location) ){
                echo "Exception: COULD NOT LOCATE 'config.json' FILE.\n";
                exit;
            }
            self::$store = json_decode(file_get_contents(self::$config_location), true);
        }
    }
    public static function getConfig(){
        self::setVariables();
        return self::$store;
    }
}