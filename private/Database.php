<?php
class Database {
    private static $username;
    private static $password;
    private static $host;
    private static $dbname;

    public static function setVariables(){
        if(self::$dbname == null ) {
            //Get the database credentials
            $config = Config::getConfig();
            self::$username = $config['database']['username'];
            self::$password = $config['database']['password'];
            self::$host = $config['database']['host'];
            self::$dbname = $config['database']['db_name'];
        }
    }

    public static function secureQuery($query, $parameters, $data) {
        self::setVariables();

        //Use PDO to make a secure, all purpose query function that returns a associative array
        $conn = new PDO("mysql:host=". self::$host .";dbname=". self::$dbname , self::$username, self::$password);
        $stmt = $conn->prepare($query);
        foreach($parameters as $key => &$value ) {
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
        if(isset($data)){
            //Check to see if we want to return any special type of data
            if( $data = "assoc" ){
                $return = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $return = $stmt->{$data}();
            }
        } else {
            $return = $stmt->fetchAll();
        }
        return $return;
    }
    public static function testConn(){
        self::setVariables();
        try {
            $c = new PDO("mysql:host=".self::$host.";dbname=".self::$dbname, self::$username, self::$password);
            $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        }
        catch(PDOException $e) {
            return false;
        }
    }
}