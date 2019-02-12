<?php
spl_autoload_register(function ($class_name) {
    include "../private/".$class_name . '.php';
});
header("Content-Security-Policy: script-src *.googleapis.com apis.google.com 'nonce-".Web::getNonce()."';");

$config = Config::getConfig();
$id = Session::getIdInfo();
?>
<!-- Check this out on GitHub! https://github.com/abir-taheer/urban-doodle -->
<!doctype html>
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

    <link nonce="<?php echo Web::getNonce(); ?>" rel="stylesheet" href="/static/css/fonts.css">
    <link nonce="<?php echo Web::getNonce(); ?>" rel="stylesheet" href="/static/css/material.cyan-light_blue.min.css">
    <link nonce="<?php echo Web::getNonce(); ?>" rel="stylesheet" href="/static/css/global.css">

    <script nonce="<?php echo Web::getNonce(); ?>" src="/static/js/jquery-3.3.1.min.js"></script>
    <script nonce="<?php echo Web::getNonce(); ?>" async src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <?php if( Config::getConfig()['google_analytics']['use'] ): ?>

    <!-- Google Analytics -->
    <script nonce="<?php echo Web::getNonce(); ?>" async src="https://www.googletagmanager.com/gtag/js?id=<?php echo Config::getConfig()['google_analytics']['tag_id']; ?>"></script>
    <script nonce="<?php echo Web::getNonce(); ?>" async src="/static/js/gtag.js"></script>
    <?php endif; ?>

</head>
<body>
<div class="material-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="material-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title sumana"><?php echo htmlspecialchars($config['org_name']); ?></span>
        </div>
    </header>
    <div class="material-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="material-drawer-header">
            <img src="<?php echo (Session::hasSession()) ? htmlspecialchars($id['picture']) : Config::getConfig()['app_icon'];?>" class="material-avatar">
            <div class="material-avatar-dropdown">
                <span><?php echo (Session::hasSession()) ? htmlspecialchars($id['email']) : "Not Signed In";  ?></span>
                <div class="mdl-layout-spacer"></div>

            </div>
        </header>
        <nav class="material-navigation mdl-navigation mdl-color--blue-grey-800">
            <a class="unready" data-type="menu-item" data-page="/">
                <i class="unready" data-type="menu-txt">home</i>Home
            </a>
            <a class="unready" data-type="menu-item" data-page="/dashboard">
                <i class="unready mdl-badge mdl-badge--overlap" data-type="menu-txt" id="elections_icon">how_to_vote</i>My Elections
            </a>
            <a class="unready" data-type="menu-item" data-page="/results">
                <i class="unready" data-type="menu-txt">ballot</i>Results
            </a>
            <a class="unready" data-type="menu-item" data-page="/candidates">
                <i class="unready" data-type="menu-txt">people</i>Candidates
            </a>
            <a class="unready" data-type="menu-item" data-page="/contact">
                <i class="unready" data-type="menu-txt">contact_support</i>Contact Us
            </a><?php if(Session::hasSession()): ?>

                <a class="unready ignore-page sign-out" data-type="menu-item">
                    <i class="unready" data-type="menu-txt">power_settings_new</i>Sign Out
                </a>
            <?php endif; ?>

            <a class="mdl-navigation__link">
                <i class="unready" data-type="menu-txt">g_translate</i>
                <div id="google_translate_element" class=""></div>
            </a>
            <div class="mdl-layout-spacer"></div>
            <a class="unready" data-type="menu-item" data-page="faqs">
                <i class="unready" data-type="menu-txt">help_outline</i>
                <span>Faqs</span>
            </a>
        </nav>
    </div>
    <main class="mdl-layout__content mdl-color--grey-100">
        <div class="mdl-progress mdl-js-progress mdl-progress__indeterminate page-loader"></div>
        <div id="variable-region"></div>
        <div id="snackbar" class="mdl-js-snackbar mdl-snackbar">
            <div class="mdl-snackbar__text"></div>
            <button class="mdl-snackbar__action" id="snackbar-button" type="button"></button>
        </div>
        <div id="network-error" class="fear">
            <div class="mdl-grid">
                <div class="unready" data-type="std-card-cont">
                    <div class="unready" data-type="std-expand"></div>
                    <h3 class="sumana text-center card-heading">Network Error:</h3>
                    <div class="sub-container">
                        <p class="text-center">The requested page could not be served. Please check your internet connection.</p>
                        <div class="center-flex">
                            <img src="/static/img/sad-cat.png" class="cat-404">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script nonce="<?php echo Web::getNonce(); ?>" src="/static/js/material.min.js"></script>
<script nonce="<?php echo Web::getNonce(); ?>" src="/static/js/global.js"></script>
</body>
</html>
