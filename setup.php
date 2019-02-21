<?php
spl_autoload_register(function ($class_name) {
    include "private/".$class_name . '.php';
});

try{
    //first check to see if the database connection works
    if( ! Database::testConn() ){
        throw new Exception("DATABASE CONNECTION FAILED");
    }

    //our configuration uses event schedulers to keep the database clean of old sessions and expired access codes
    if( Database::secureQuery("SELECT @@global.event_scheduler", [], "fetch")["@@global.event_scheduler"] !== "ON"){
        throw new Exception("MYSQL EVENT SCHEDULER IS NOT SET TO ON.");
    }

    //execute some ssl to set up the database
    $e = new Email();
    $e->to = array("ataheer10@stuy.edu");
    //$e->cc = User::getAdminEmails("u_e");
    $e->subject = "Test Email";
    $e->body = "This is to test that the email configuration information that was given is correct. Please disregard this email.";
    if(! $e->send()){
        throw new Exception("FAILED SENDING TEST EMAIL. CHECK SMTP CONFIGURATIONS.");
    }


    //test the domain
    if ( !file_exists("public/static/test") ) {
        if(! mkdir ("public/static/test", 0755)){
            throw new Exception("MAKING TEST DIRECTORY FAILED. MAKE SURE PHP HAS EDIT PERMISSIONS TO THE APP DIRECTORY.");
        }
    }
    $random_test = bin2hex(random_bytes(16));
    if(file_put_contents ('public/static/test/index.php', $random_test) === false){
        throw new Exception("ERROR CREATING DOMAIN TEST FILE. MAKE SURE PHP HAS EDIT PERMISSIONS TO APP DIRECTORY");
    }
    $method = ( Config::getConfig()['ssl'] ) ? "https" : "http";
    if(file_get_contents($method."://".Config::getConfig()['domain']."/static/test/index.php") === $random_test){
        //test was a success, delete the test folder now
        if(! unlink("public/static/test/index.php")){
            throw new Exception("COULD NOT DELETE TEST FILE. MAKE SURE PHP HAS EDIT PERMISSIONS TO THE APP DIRECTORY");
        }
        if( ! rmdir("public/static/test") ){
            throw new Exception("COULD NOT DELETE TEST DIRECTORY. MAKE SURE DIRECTORY '".__dir__."/public/static/test' DOES NOT CONTAIN ANY FILES");
        }

    } else {
        throw new Exception("DOMAIN OR VIRTUALHOST OR SSL IS NOT SET UP PROPERLY");
    }


    //make our necessary directories for storing information
    if( ! file_exists("app_files/setup_successful") ){
        if ( !file_exists("public/static/img/candidates") ) {
            mkdir ("public/static/img/candidates", 0755);
        }

        //this will be used to store our election csv files so that the server can send them off quickly
        if ( !file_exists("public/static/elections") ) {
            mkdir ("public/static/elections", 0755);
        }

        if ( !file_exists("app_files") ) {
            //this will be used to store things like election votes
            mkdir ("app_files", 0744);
        }
        file_put_contents ('app_files/setup_successful', 'awesome-sauce');
    }

} catch (exception $error){
    echo "There was an error setting up the program. The error is as follows: \n".$error;
}