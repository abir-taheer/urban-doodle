<?php
spl_autoload_register(function ($class_name) {
    include "../private/".$class_name . '.php';
});
header("Content-Security-Policy: script-src *.googleapis.com apis.google.com 'nonce-".Web::getNonce()."';");

$config = Config::getConfig();
$id = Session::getIdInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="<?php echo $config['metadata']['description']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="<?php echo $config['google-signin-client_id']; ?>">

    <!-- Social Media Information -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $config['metadata']['title']; ?>" />
    <meta property="og:description" content="<?php echo addslashes($config['metadata']['description']); ?>" />
    <meta property="og:image" content="<?php echo Config::getConfig()['app_icon']; ?>" />

    <title><?php echo htmlspecialchars($config['metadata']['title']); ?></title>
    <link rel="icon" sizes="192x192" href="<?php echo Config::getConfig()['metadata']['favicon']; ?>">

    <link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">
    <link rel="stylesheet" href="static/css/fonts.css">
    <link rel="stylesheet" href="static/css/global.css">

    <script nonce="<?php echo Web::getNonce(); ?>" src="static/js/jquery-3.3.1.min.js"></script>
    <script nonce="<?php echo Web::getNonce(); ?>" src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
    <?php if( Config::getConfig()['google_analytics']['use'] ): ?>
        <!-- Google Analytics -->
        <script nonce="<?php echo Web::getNonce(); ?>" async src="https://www.googletagmanager.com/gtag/js"></script>
        <script nonce="<?php echo Web::getNonce(); ?>">
            window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '<?php echo Config::getConfig()['google_analytics']['tag_id']; ?>');
        </script>
    <?php endif; ?>
</head>
<body>
<header class="mdc-top-app-bar mdc-top-app-bar--fixed mdc-elevation--z1" id="app-bar" data-mdc-auto-init="MDCTopAppBar">
    <div class="mdc-top-app-bar__row">
        <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
            <a class="material-icons mdc-top-app-bar__navigation-icon">menu</a>
            <span class="mdc-top-app-bar__title sumana"><?php echo htmlspecialchars($config['org_name']); ?></span>
        </section>
    </div>
</header>
<aside class="mdc-drawer mdc-drawer--dismissible mdc-top-app-bar--fixed-adjust">
    <div class="mdc-drawer__header">
        <img alt="logo" src="<?php echo htmlspecialchars(Config::getConfig()['app_icon']); ?>" class="drawer-logo">
        <h3 class="mdc-drawer__title"><?php echo (Session::hasSession()) ? htmlspecialchars($id['first_name']." ".$id['last_name']) : "Not Signed In";  ?></h3>
        <h6 class="mdc-drawer__subtitle"><?php echo (Session::hasSession()) ? htmlspecialchars($id['email']) : ""; ?></h6>
    </div>
    <div class="mdc-drawer__content">
        <?php if(Session::hasSession()): ?>
        <a class="mdc-list-item sign-out clickable">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">power_settings_new</i>
            <span class="mdc-list-item__text">Sign Out</span>
        </a>
        <?php endif; ?>

        <div class="mdc-list drawer-pages-list" data-menu-items="<?php echo rawurlencode(json_encode(Web::$menu_pages)); ?>"></div>
    </div>
</aside>

<div class="mdc-drawer-app-content mdc-top-app-bar--fixed-adjust">
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

    <!-- Actual page content. Everything must be contained using the grid! -->
    <main class="main-content" id="main-content">
        <div class="mdc-layout-grid">
            <div id="variable-region"></div>
        </div>
    </main>
</div>

<!-- Initialize the material elements -->
<script src="static/js/global.js" nonce="<?php echo Web::getNonce(); ?>"></script>
</body>
</html>