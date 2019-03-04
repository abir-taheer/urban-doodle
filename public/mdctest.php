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
    <style>
        body {
            display: flex;
            margin: 0;
        }

        .mdc-drawer-app-content {
            flex: auto;
            position: relative;
        }
        .mdc-drawer {
            position: fixed;
        }
        .main-content {
            overflow: auto;
            height: 100%;
        }
        .mdc-top-app-bar {
            z-index: 7;
            background-color: white;
            color: black;
        }
        .mdc-top-app-bar .material-icons {
            color: black;
        }
        .drawer-logo {
            padding-top: 10px;
            height: 100px;
            width: 100px;
        }
        .card-expand-default {
            width: 100%;
            height: 16px;
            background-color: var(--mdc-theme-primary);
            border-radius: 4px 4px 0 0;
        }
        .txt-ctr {
            text-align: center;
        }
        .flx-ctr {
            display: flex;
            justify-content: center;
        }
        .clickable {
            cursor: pointer;
        }
    </style>

    <script async nonce="<?php echo Web::getNonce(); ?>" src="static/js/jquery-3.3.1.min.js"></script>
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
        <div class="mdc-list drawer-pages-list" data-menu-items="<?php echo rawurlencode(json_encode(Web::$menu_pages)); ?>">
        </div>
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
            <div id="variable-region">
                <div class="mdc-card mdc-card--outlined mdc-layout-grid__cell--span-12">
                    <!-- ... content ... -->
                    <div class="card-expand-default"></div>
                    <h2 class="txt-ctr">Welcome</h2>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Initialize the material elements -->
<script nonce="<?php echo Web::getNonce(); ?>">
    function changePage(path){
        let vr = document.getElementById("variable-region");
        let pld = document.querySelector(".page-loader");
        $(pld).removeClass("mdc-linear-progress--closed");
        $(vr).fadeOut(1);
        $(vr).find("link").prop("disabled", true);
        $(vr).empty();
        if(path !== null){
            history.pushState({}, null, path);
        }

        let menuItems = document.querySelector(".drawer-pages-list").childNodes;
        let pg = window.location.pathname.split("/")[1];
        for(let x = 0 ; x < menuItems.length ; x++ ){
            let i = menuItems[x];
            ( $(i).data("page") === "/" + pg ) ? $(i).addClass("mdc-list-item--activated") : $(i).removeClass("mdc-list-item--activated");
        }
        $.get("/load.php?page=" + window.location.pathname, function(a, b, c){
            $(vr).html(a);
            if( c.getResponseHeader("X-Fetch-New-Sources") === "true" ){
                addSources(c);
            }
        }).fail(function(){
            $(vr).fadeIn();
            $(pld).addClass("mdc-linear-progress--closed");
        }).then(function(){
            $(vr).ready(function(){
                $(pld).addClass("mdc-linear-progress--closed");
                $(vr).fadeIn();
            });
        });
    }

    function addSources(c){
        let vr = document.getElementById("variable-region");
        let x,y;
        let new_src = JSON.parse(c.getResponseHeader("X-New-Sources"));
        let nce = c.getResponseHeader("X-Nonce");
        for(x in new_src.script){
            let node = document.createElement("script");
            node.setAttribute("nonce", nce);
            node.setAttribute("src", new_src.script[x]);
            vr.appendChild(node);
        }
        for(y in new_src.css){
            let node = document.createElement("link");
            node.setAttribute("nonce", nce);
            node.setAttribute("rel", "stylesheet");
            node.setAttribute("href", new_src.css[x]);
            vr.appendChild(node);
        }
    }

    $(document).ready(function () {
        // first setup the necessary elements
        let pageList = document.querySelector(".drawer-pages-list");
        let pages = JSON.parse(decodeURIComponent(pageList.getAttribute("data-menu-items")));
        for(let x = 0; x < pages.length ; x++){
            let a = document.createElement("a");
            a.classList.add("mdc-list-item");
            a.classList.add("change-page");
            a.classList.add("clickable");
            a.setAttribute("data-page", pages[x]['page']);
            let i = document.createElement("a");
            i.classList.add("material-icons");
            i.classList.add("mdc-list-item__graphic");
            i.innerHTML = pages[x]['icon'];
            a.appendChild(i);
            let span = document.createElement("span");
            span.classList.add("mdc-list-item__text");
            span.innerHTML = pages[x]['text'];
            a.appendChild(span);
            pageList.appendChild(a);
        }
        // Now load the first page
        changePage();
    });
    // Automatically instantiate the mdc elements on the page
    mdc.autoInit();

    // Create an instance of the drawer and store it
    let drawer = new mdc.drawer.MDCDrawer(document.querySelector('.mdc-drawer'));
    // Store the topAppBar in a variable
    let topAppBar = mdc.topAppBar.MDCTopAppBar.attachTo(document.querySelector('.mdc-top-app-bar'));

    // Automatically open the drawer if the user is on a desktop device
    if( window.innerWidth > 800 ){
        drawer.open = true;
    }

    // When user clicks on the hamburger menu in the appBar, trigger the drawer
    topAppBar.listen('MDCTopAppBar:nav', () => {
        drawer.open = !drawer.open;
    });

    window.onpopstate = function (){changePage()};
    $(document.body).on("click", ".change-page", function (ev) {
        changePage($(ev.currentTarget).data("page"));
        if(window.innerWidth <= 800){
            drawer.open = false;
        }
    });
    $('#variable-region').bind("DOMSubtreeModified",function(){
        window.mdc.autoInit(document, () => {});
    });
</script>
</body>
</html>