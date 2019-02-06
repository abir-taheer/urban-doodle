<?php
    spl_autoload_register(function ($class_name) {
        include "../private/".$class_name . '.php';
    });

   $path = explode("/", $_GET['page']);
   function signInRequired(){
       if( ! Session::hasSession() ){
           echo "
           <script src=\"https://apis.google.com/js/platform.js\"></script>
        <div class=\"mdl-grid\">
            <div class=\"mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--12-col\">
                <div class=\"mdl-card__title mdl-card--expand mdl-color--teal-300\"></div>
                <h3 class=\"sumana text-center card-heading\">Sign In Required</h3>
                <div class=\"sub-container\">
                    <p class=\"text-center\">You need to be signed in to access this page!</p>
                    <div class=\"center-flex\">
                        <div class=\"g-signin2 notranslate\" data-onsuccess=\"onSignIn\" data-theme=\"light\" data-longtitle=\"true\"></div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
           ";
           exit;
       }
   }
?>

<?php switch(strtolower(trim($path[1]))):
case "": ?>
    <script src="https://apis.google.com/js/platform.js"></script>
    <div class="mdl-grid">
        <div class="mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--12-col">
            <div class="mdl-card__title mdl-card--expand mdl-color--teal-300"></div>
            <h3 class="sumana text-center card-heading">Welcome</h3>
            <div class="sub-container">
                <!-- TODO small message here -->
                <?php if( ! Session::hasSession() ): ?>

                    <div class="center-flex">
                        <div class="g-signin2 notranslate" data-onsuccess="onSignIn" data-theme="light" data-longtitle="true"></div>
                    </div>
                    <br>

                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php break;?>
<?php case "dashboard": signInRequired(); ?>
    <h4>hello</h4>
    <?php break; ?>
<?php case "contact": signInRequired();?>

    <?php break; ?>
<?php default: ?>
        <div class="mdl-grid">
            <div class="mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--12-col">
                <div class="mdl-card__title mdl-card--expand mdl-color--teal-300"></div>
                <h3 class="sumana text-center card-heading">Error:</h3>
                <div class="sub-container">
                    <p class="text-center">The page you are looking for could not be found.</p>
                </div>
            </div>
        </div>
    <?php break;?>
    <?php endswitch;?>
