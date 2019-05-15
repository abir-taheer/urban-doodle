<?php
class Database {
    public static function secureQuery($query, $parameters = [], $data = null) {
        //Use PDO to make a secure, all purpose query function that returns a associative array
        $conn = new PDO("mysql:host=". db_host .";dbname=". db_name , db_username, db_password);
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
        try {
            $c = new PDO("mysql:host=".db_host.";dbname=".db_name, db_username, db_password);
            $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        }
        catch(PDOException $e) {
            return false;
        }
    }
}