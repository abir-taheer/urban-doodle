<?php
spl_autoload_register(function ($class_name) {
    include "../private/".$class_name . '.php';
});

header('Content-Security: nonce="hellsdicj"');
$config = Config::getConfig();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo $config['metadata']['description']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="<?php echo $config['google-signin-client_id']; ?>">
    <title><?php echo $config['metadata']['title']; ?></title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="/resources/images/boe_logo.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Stuyvesant Board of Election Voting Site">
    <meta property="og:image" content="https://vote.stuysu.org/logo.png" />

    <link rel="stylesheet" href="/static/css/fonts.css">
    <link rel="stylesheet" href="/static/css/material.cyan-light_blue.min.css">
    <link rel="stylesheet" href="/static/css/global.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-119929576-1"></script>
    <script src="/static/js/material.min.js"></script>
    <script async src="/static/js/gtag.js"></script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="<?php echo $config['metadata']['title']; ?>" />
    <meta property="og:description"   content="<?php echo $config['metadata']['description']; ?>" />
    <meta property="og:image"         content="https://vote.stuysu.org/logo.png" />
</head>
<body>
<div class="material-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="material-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title sumana"><?php echo $config['org_name']; ?></span>
        </div>
    </header>
    <div class="material-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="material-drawer-header">
            <img src="//vote.stuysu.org/resources/images/boe_logo_invert.png" class="material-avatar">
            <div class="material-avatar-dropdown">
                <span>Not Signed In</span>
                <div class="mdl-layout-spacer"></div>

            </div>
        </header>
        <nav class="material-navigation mdl-navigation mdl-color--blue-grey-800">
            <a class="mdl-navigation__link change-page clickable" data-page="/">
                <i class="mdl-color-text--blue-grey-400 material-icons " role="presentation">home</i>Home
            </a>
            <a class="mdl-navigation__link clickable change-page clickable" data-page="/dashboard">
                <i class="mdl-color-text--blue-grey-400 material-icons mdl-badge mdl-badge--overlap " id="elections_icon" role="presentation">how_to_vote</i>My Elections
            </a>
            <a class="mdl-navigation__link change-page clickable" data-page="/results">
                <i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">ballot</i>Results
            </a>
            <a class="mdl-navigation__link change-page clickable" data-page="/candidates">
                <i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">people</i>Candidates
            </a>
            <a class="mdl-navigation__link change-page clickable" data-page="/contact">
                <i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">contact_support</i>Contact Us
            </a>
            <a class="mdl-navigation__link">
                <i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">g_translate</i>
                <div id="google_translate_element" class=""></div>
            </a>
            <div class="mdl-layout-spacer"></div>
            <a class="mdl-navigation__link change-page clickable" data-page="faqs">
                <i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">help_outline</i>
                <span class="visuallyhidden">Help</span>
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
    </main>
</div>
<script src="/static/js/global.js"></script>
</body>
</html>
