<?php
    //autoload necessary classes
    spl_autoload_register(function ($class_name) {
        include "../private/".$class_name . '.php';
    });

    //split the pathName sent to us, using the / as a delimiter, into an array
    $path = explode("/", $_GET['page']);

   //in the case that a page requires sign in, respond with the following html and stop script execution
    function signInRequired(){
        if( ! Session::hasSession() ){
            echo "
                <!-- Script tag doesn't need nonce because apis.google.com is a trusted whitelisted source -->
                <script src='https://apis.google.com/js/platform.js'></script>
                <div class='mdl-grid'>
                    <div class='unready' data-type='std-card-cont'>
                        <div class='unready' data-type='std-expand'></div>
                        <h3 class='sumana text-center card-heading'>Sign In Required</h3>
                        <div class='sub-container'>
                            <p class='text-center'>You need to be signed in to access this page!</p>
                            <div class='center-flex'>
                                <div class='g-signin2 notranslate' data-onsuccess='onSignIn' data-longtitle='true'></div>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
            ";
            exit;
        }
    }

    //convert the file request into lowercase and get rid of trailing whitespace
    $page = (strtolower(trim($path[1])) === "") ? "index" : strtolower(trim($path[1]));

    //Get a list of all of the available pages
    $available_pages = scandir("../private/pages");

    if(in_array($page.".php", $available_pages)){
        //the page that the user requested does exist, include it in the response
        include("../private/pages/".$page.".php");
        //We included the page that the user request, stop executing this script
        Web::sendDependencies();
        exit;
    }

    //this statement is only reached in the case that the file requested by the user does not exist

    $auto_correct_exceptions = array("..", ".", "index.php", "vote");
    //since the requested file does not exist check our array of files to see if there is a file with a similar name that does exist
    foreach($available_pages as $p ){
        //if the current page being compared is the index, skip it since it does not have an actual name
        if( in_array($p, $auto_correct_exceptions) ) {
            continue;
        }

        //to increase the chances of finding a similar match based on the user's input, and because of how the similar_text works:
        //set the longer string as $string1 while setting the shorter string as $string2
        $string1 = ( strlen($p) > strlen($page) ) ? $p : $page;
        $string2 = ( strlen($p) < strlen($page) ) ? $p : $page;

        //if the similarity of the two strings is above a threshold, add them to an array of possible matches in $similar_pages
        if( similar_text($string1, $string2) > 3 ){
            $similar_pages[] = str_replace(".php", "", $p);
        }
   }
?>
<?php //The part below is only sent in case the page could not be found ?>
<div class="mdl-grid">
    <div class="unready" data-type="std-card-cont">
        <div class="unready" data-type="std-expand"></div>
        <h3 class="sumana text-center card-heading">Error:</h3>
        <div class="sub-container">
            <p class="text-center">The page you are looking for could not be found.</p>
            <div class="center-flex">
                <img src="/static/img/sad-cat.png" class="cat-404">
            </div>
            <?php // In the case that we found pages with a similar name in our page directory, ask the user if they meant to go to those pages?>
            <?php if( count($similar_pages) > 0 ): ?>
                <p class="text-center">Did you mean to go to one of the following pages?</p>
                <?php foreach( $similar_pages as $s ): ?>
                    <p class="text-center"><a href="/<?php echo $s; ?>"><?php echo ucwords($s); ?></a></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
