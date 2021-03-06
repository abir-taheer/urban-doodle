<?php
require_once "../config.php";
spl_autoload_register(function ($class_name) {
    require_once "../classes/".$class_name . ".php";
});
header("Content-Security-Policy: script-src *.googleapis.com apis.google.com 'self' 'nonce-".Web::getNonce()."';");
$request = explode("/", $_SERVER["SCRIPT_URL"]);


$id = Session::getIdInfo();
?>
<!-- https://github.com/abir-taheer/urban-doodle -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Record time that page starts loading -->
        <script nonce="<?php echo Web::getNonce(); ?>">
            let pageLoadStart = new Date();
        </script>

        <!-- Site SEO Info -->
        <meta charset="utf-8">
        <title><?php echo htmlspecialchars(web_title); ?></title>
        <meta name="description" content="<?php echo htmlspecialchars(web_description); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

        <!-- Google Sign-In Information -->
        <meta name="google-signin-scope" content="profile email">
        <meta name="google-signin-client_id" content="<?php echo google_auth_client_id; ?>">

        <!-- Time Information -->
        <meta name="server-utc-time" content="<?php echo base64_encode(Web::getUTCTime()->format(DateTime::ATOM)); ?>">
        <meta name="app-time-zone" content="<?php echo base64_encode(app_time_zone); ?>">

        <!-- Social Media Information -->
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?php echo htmlspecialchars(Web::getTitle($request)); ?>">
        <meta property="og:description" content="<?php echo htmlspecialchars(Web::getDescription($request)); ?>">
        <meta property="og:image" content="<?php echo Web::getSocialPic($request); ?>">

        <!-- Favicon -->
        <link rel="icon" sizes="192x192" href="<?php echo web_favicon; ?>">

        <!-- Page Setup Stylesheets -->
        <link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="/static/css/fonts.css">
        <link rel="stylesheet" href="/static/css/global.css">

        <!-- Necessary Page Setup Scripts -->
        <script nonce="<?php echo Web::getNonce(); ?>" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script nonce="<?php echo Web::getNonce(); ?>" src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
        <script nonce="<?php echo Web::getNonce(); ?>" src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.9.0/showdown.min.js" async></script>

        <!-- Information for use in scripts -->
        <meta name="signed-in" content="<?php echo (Session::hasSession()) ? "true" : "false" ; ?>">

        <!-- Google Analytics -->
<?php if( google_analytics_use ): ?>
        <meta name="use-google-analytics" content="true">
        <meta name="gtag-tracking-id" content="<?php echo google_analytics_tag_id; ?>">
        <script nonce="<?php echo Web::getNonce(); ?>" async src="https://www.googletagmanager.com/gtag/js?id=<?php echo google_analytics_tag_id; ?>"></script>
        <script nonce="<?php echo Web::getNonce(); ?>">
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag("js", new Date());
            gtag("config", "<?php echo google_analytics_tag_id; ?>");
        </script>
<?php else: ?>
        <meta name="use-google-analytics" content="false" >
<?php endif; ?>

    </head>
    <body>
        <!-- Top Nav Bar -->
        <header class="mdc-top-app-bar mdc-top-app-bar--fixed" id="app-bar" data-mdc-auto-init="MDCTopAppBar">
            <div class="mdc-top-app-bar__row">
                <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
                    <a class="material-icons mdc-top-app-bar__navigation-icon menu-trigger">menu</a>
                    <span class="mdc-top-app-bar__title sumana"><?php echo htmlspecialchars(org_name); ?><?php if(VotingStation::isVotingStation() && Session::hasSession()): ?> : Voting Station Session <a class="js-timer" data-timer-type="countdown" data-count-down-date="<?php echo base64_encode($_COOKIE["Current_Session"]); ?>"></a><?php endif; ?></span>
                </section>
            </div>
        </header>

        <!-- Menu Drawer -->
        <aside class="mdc-drawer mdc-drawer--dismissible mdc-top-app-bar--fixed-adjust">
            <div class="mdc-drawer__header">
                <img alt="logo" src="<?php echo htmlspecialchars(app_icon); ?>" class="drawer-logo">
                <h3 class="mdc-drawer__title"><?php echo (Session::hasSession()) ? htmlspecialchars($id["first_name"]." ".$id["last_name"]) : "Not Signed In";  ?></h3>
                <h6 class="mdc-drawer__subtitle"><?php echo (Session::hasSession()) ? htmlspecialchars($id["email"]) : ""; ?></h6>
            </div>
            <div class="mdc-drawer__content">
        <?php if(Session::hasSession()): ?>

                <a class="mdc-list-item" href="/signout.php" >
                    <i class="material-icons mdc-list-item__graphic" aria-hidden="true">power_settings_new</i>
                    <span class="mdc-list-item__text">Sign Out</span>
                </a>

            <?php if( Session::getUser()->isAdmin() ): ?>

                <a class="mdc-list-item clickable change-page" data-page="/admin">
                    <i class="material-icons mdc-list-item__graphic" aria-hidden="true">build</i>
                    <span class="mdc-list-item__text">Admin</span>
                </a>

            <?php endif; ?>
            <?php if( Session::getUser()->isManager() ): ?>

                <a class="mdc-list-item clickable change-page" data-page="/campaign">
                    <i class="material-icons mdc-list-item__graphic" aria-hidden="true">assignment_ind</i>
                    <span class="mdc-list-item__text">Manage Campaign</span>
                </a>

            <?php endif; ?>
        <?php endif; ?>

                <!-- Menu container with a base64-Encoded json of menu items, their resource paths, and icons -->
                <div class="mdc-list drawer-pages-list" data-menu-items="<?php echo base64_encode(json_encode(Web::$menu_pages)); ?>"></div>
            </div>
        </aside>

        <!-- Rest of viewable content -->
        <div class="mdc-drawer-app-content mdc-top-app-bar--fixed-adjust">
            <div class="obs mobile-only fear clickable"></div>
            <!-- Indeterminate Loader -->
            <div role="progressbar" class="mdc-linear-progress mdc-linear-progress--indeterminate page-loader">
                <div class="mdc-linear-progress__buffering-dots"></div>
                <div class="mdc-linear-progress__buffer"></div>
                <div class="mdc-linear-progress__bar mdc-linear-progress__primary-bar">
                    <span class="mdc-linear-progress__bar-inner"></span>
                </div>
                <div class="mdc-linear-progress__bar mdc-linear-progress__secondary-bar">
                    <span class="mdc-linear-progress__bar-inner"></span>
                </div>
            </div>

            <!-- Actual page content. Everything must be contained using an mdc grid! -->
            <main class="main-content" id="main-content">
                <div class="mdc-layout-grid">
                    <div id="variable-region" class="mdc-layout-grid__inner muli"></div>
                </div>
            </main>
        </div>

        <!-- Snackbar -->
        <div class="mdc-snackbar">
            <div class="mdc-snackbar__surface">
                <div class="mdc-snackbar__label" role="status" aria-live="polite"></div>
                <div class="mdc-snackbar__actions">
                    <button type="button" class="mdc-button mdc-snackbar__action"></button>
                    <button class="mdc-icon-button mdc-snackbar__dismiss material-icons" title="Dismiss">close</button>
                </div>
            </div>
        </div>

        <!-- Social Media Share Dialog -->
        <div class="mdc-dialog share-dialog" data-mdc-auto-init="MDCDialog">
            <div class="mdc-dialog__container">
                <div class="mdc-dialog__surface">
                    <h2 class="mdc-dialog__title">Share Content</h2>
                    <div class="mdc-dialog__content">
                        <div class="share-url">
                            <input type="text" class="url-content" readonly>
                            <a class="copy-social">Copy</a>
                        </div>
                    </div>
                    <footer class="mdc-dialog__actions flx-ctr">
                        <button class="mdc-icon-button social so-facebook"><i class="fa fa-facebook"></i></button>
                        <button class="mdc-icon-button social so-twitter"><i class="fa fa-twitter"></i></button>
                        <button class="mdc-icon-button social so-linkedin"><i class="fa fa-linkedin"></i></button>
                        <button class="mdc-icon-button social so-pinterest"><i class="fa fa-pinterest"></i></button>
                        <button class="mdc-icon-button social so-envelope"><i class="fa fa-envelope"></i></button>
                        <button class="mdc-icon-button social so-print"><i class="fa fa-print"></i></button>

                    </footer>
                </div>
            </div>
            <div class="mdc-dialog__scrim"></div>
        </div>

        <div class="mdc-dialog confirm-dialog">
            <div class="mdc-dialog__container">
                <div class="mdc-dialog__surface">
                    <h2 class="mdc-dialog__title">Confirm</h2>
                    <div class="mdc-dialog__content" id="confirm-dialog-content"></div>
                    <footer class="mdc-dialog__actions">
                        <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="no">
                            <span class="mdc-button__label">Cancel</span>
                        </button>
                        <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="yes">
                            <span class="mdc-button__label">Confirm</span>
                        </button>
                    </footer>
                </div>
            </div>
            <div class="mdc-dialog__scrim"></div>
        </div>

        <!-- Global functions to be used by all pages -->
        <script src="/static/js/global.js" nonce="<?php echo Web::getNonce(); ?>"></script>
    </body>
</html>