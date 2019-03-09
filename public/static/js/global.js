'use strict';
/* auth */
/* TODO REMOVE PREVIOUS SNACKBAR FUNCTION CALLS */
let useGoogleAnalytics = $("meta[name=use-google-analytics]").attr("content") === "true";
if(useGoogleAnalytics){
    var gtagTrackingID = $("meta[name=gtag-tracking-id]").attr("content");
}
function onSignIn(googleUser) {
    let token = googleUser.getAuthResponse().id_token;
    //let profile = googleUser.getBasicProfile();

    $.post("/auth.php", {"token":token} ,
        function(response){
            let decoded_resp = JSON.parse(response);
            if( decoded_resp.status === "success" ){
                //add the success message to the queue
                //addSnackbarQueue(decoded_resp.message, 3000);

                // Log the sign in on Google Analytics if it is used
                if(useGoogleAnalytics){
                    gtag('event', 'login', {'method': 'Google'});
                }

                // Refresh the page and they should see the updated page
                window.location.reload();
            } else {
                // There was an error, read out the error in a snackbar
                let x = 0;
                while( x < decoded_resp.message.length ){
                    //showSnackbar(decoded_resp.message[x] , 2000);
                    x++;
                }
            }
    });
}

// If the user ever manually triggered the drawer
let manDrawTrig = false;

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

    if(useGoogleAnalytics){
        gtag('config', gtagTrackingID, {
            'page_path': window.location.pathname
        });
    }
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
        a.setAttribute("data-mdc-auto-init", "MDCRipple");
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
    if( ! manDrawTrig && window.innerWidth > 800 ){
        manDrawTrig = true;
    }
});

window.onpopstate = function (){changePage()};
$(document.body).on("click", ".change-page", function (ev) {
    changePage($(ev.currentTarget).data("page"));
    if(window.innerWidth <= 800){
        drawer.open = false;
    }
});
$('#variable-region').on("DOMSubtreeModified",function(){
    window.mdc.autoInit(document, () => {});
});
$(document.body).on("click", ".sign-out", function(){
    $.post('/signout.php').done(function() {
        //addSnackbarQueue("You have been sucessfully signed out!", 2000);
        window.location.reload();
    }).fail(function(er){
        if( window.innerWidth <= 800 ){
            drawer.open = false;
        }
        //showSnackbar("There was an error signing you out. You are currently not connected to the internet!", 3000);
    });
});

if (navigator.userAgent.match(/(FB|Messenger)/)) {
    window.onbeforeunload = function(){
        gapi.auth2.getAuthInstance().disconnect()
    };
} else {
    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
        document.pagehide = function(){
            gapi.auth2.getAuthInstance().disconnect()
        };
    } else {
        window.onbeforeunload = function(){
            gapi.auth2.getAuthInstance().disconnect()
        };
    }
}

$(window).resize(function () {
    if( window.innerWidth <= 1250 ){
        if( drawer.open ){
            drawer.open = false;
        }
    } else {
        if( ! manDrawTrig ){
            drawer.open = true;
        }
    }
});

// Calculate how long it took the page to load
let pageLoadOffset = new Date().getTime() - pageLoadStart.getTime();
let serverTime = new Date(decodeURIComponent($("meta[name=server-utc-time]").attr("content")));
serverTime.setTime(serverTime.getTime() + pageLoadOffset);
let countdowns = setInterval(function () {
    // TODO
    let timePassed = new Date().getTime() - serverTime.getTime();
    let timeSeconds = serverTime.getTime() + timePassed;
    //currentTime.setTime();
    console.log(new Date(timeSeconds));


    // After the above is done, update all of the countdowns on the page
    let counters = document.getElementsByClassName("countdown-timer");
    for( let x = 0; x < counters.length ; x++ ){
        // TODO
    }
}, 1000);