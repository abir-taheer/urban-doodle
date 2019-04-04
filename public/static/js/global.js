"use strict";
/* auth */
/* TODO REMOVE PREVIOUS SNACKBAR FUNCTION CALLS */
let useGoogleAnalytics = $("meta[name=use-google-analytics]").attr("content") === "true";
let isSignedIn = $("meta[name=signed-in]").attr("content") === "true";
let gtagTrackingID = (useGoogleAnalytics) ? $("meta[name=gtag-tracking-id]").attr("content") : "";
function onSignIn(googleUser) {
    let token = googleUser.getAuthResponse().id_token;
    //let profile = googleUser.getBasicProfile();

    $.post("/auth.php", {"token":token} ,
        function(response){
            let decoded_resp = JSON.parse(response);
            if( decoded_resp.status === "success" ){
                // Log the sign in on Google Analytics if it is used
                if(useGoogleAnalytics){
                    gtag("event", "login", {"method": "Google"});
                }

                addSnackbarQueue("You have been successfully signed in");
                // Refresh the page and they should see the updated page
                window.location.reload();
            } else {
                // There was an error, read out the error in a snackbar
                let x = 0;
                while( x < decoded_resp.message.length ){
                    addSnackbarQueue(decoded_resp.message[x]);
                    x++;
                }
                playSnackbarQueue();
            }
    });
}

// If the user ever manually triggered the drawer
let manDrawTrig = false;

function changePage(path = null){
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
        gtag("config", gtagTrackingID, {
            "page_path": window.location.pathname
        });
    }
    playSnackbarQueue();
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

$(".obs").on("click", ev => {
   drawer.open = false;
   $(ev.currentTarget).addClass("fear");
   $("#variable-region").fadeIn();
});

$(document).ready(() => {
    // first setup the necessary elements
    let pageList = document.querySelector(".drawer-pages-list");
    let pages = JSON.parse(atob(pageList.getAttribute("data-menu-items")));
    for(let x = 0; x < pages.length ; x++){
        if( pages[x].session !== "*" && pages[x].session !== $("meta[name=signed-in]").attr("content") ){
            continue;
        }
        let a = document.createElement("a");
        a.classList.add("mdc-list-item");
        a.classList.add("change-page");
        a.classList.add("clickable");
        a.setAttribute("data-mdc-auto-init", "MDCRipple");
        a.setAttribute("data-page", pages[x]["page"]);
        let i = document.createElement("a");
        i.classList.add("material-icons");
        i.classList.add("mdc-list-item__graphic");
        i.innerHTML = pages[x]["icon"];
        a.appendChild(i);
        let span = document.createElement("span");
        span.classList.add("mdc-list-item__text");
        span.innerHTML = pages[x]["text"];
        a.appendChild(span);
        pageList.appendChild(a);
    }
    // Now load the first page
    changePage();

    // Play the snackbars
    playSnackbarQueue();
});
// Automatically instantiate the mdc elements on the page
mdc.autoInit();

// Create an instance of the drawer and store it
let drawer = new mdc.drawer.MDCDrawer(document.querySelector(".mdc-drawer"));
let snackbar = new mdc.snackbar.MDCSnackbar(document.querySelector(".mdc-snackbar"));

// Automatically open the drawer if the user is on a desktop device
if( window.innerWidth > 1250 ){
    drawer.open = true;
}

// When user clicks on the hamburger menu in the appBar, trigger the drawer
$(document.body).on("click", ".menu-trigger", () => {
    let a = $("#variable-region");
    let b = $(".obs");
    if( window.innerWidth < 1250) {
        (!drawer.open) ? a.fadeOut(1) : a.fadeIn();
        (!drawer.open) ? b.removeClass("fear") : b.addClass("fear");
    } else {
        a.fadeIn();
    }
    drawer.open = !drawer.open;
    if( ! manDrawTrig && window.innerWidth > 1250 ){
        manDrawTrig = true;
    }
});

window.onpopstate = function (){changePage()};
$(document.body).on("click", ".change-page", ev => {
    changePage($(ev.currentTarget).data("page"));
    if(window.innerWidth <= 1250){
        drawer.open = false;
        $(".obs").addClass("fear");
    }
});
$("#variable-region").on("DOMSubtreeModified", () => {
    window.mdc.autoInit(document, () => {});
});
$(document.body).on("click", ".sign-out", () => {
    location.replace("/signout.php");
});

if(! isSignedIn){
    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/) && ! navigator.userAgent.match(/(FB|Messenger)/)) {
        document.pagehide = () => {
            gapi.auth2.getAuthInstance().disconnect()
        };
    } else {
        window.onbeforeunload = () => {
            gapi.auth2.getAuthInstance().disconnect()
        };
    }
}


$(window).resize(() => {
    if( window.innerWidth <= 1250 ){
        if( drawer.open ){
            drawer.open = false;
        }
    } else {
        if( ! manDrawTrig ){
            drawer.open = true;
        }
        $("#variable-region").fadeIn();
    }
});

$(document.body).on("click", ".submit-form", ev => {
    let button = ev.currentTarget;
    button.setAttribute("disabled", true);
    let cont = button.parentElement.parentElement;
    let form = $(cont).find("form");
    $.post($(form).data("action"), $(form).serialize(), (a, b, c) => {
        // TODO Add more callback options
        let resp = JSON.parse(a);
        for( let x = 0 ; x < resp.message.length ; x++ ){
            addSnackbarQueue(resp.message[x]);
        }
        if( resp.status === "success" ){
            switch($(form).data("callback")){
                case "reload":
                    changePage();
                    break;
            }
        } else {
            button.removeAttribute("disabled");
            playSnackbarQueue();
        }
    });

});

snackbar.listen("MDCSnackbar:closed", () => {
    playSnackbarQueue();
});

function playSnackbarQueue(){
    try {
        let snacks = JSON.parse(localStorage.getItem("snackbar"));
        if( snacks.length > 0 ){
            // Only show the first snackbar on the list, this function will automatically get called again once the snackbar closes
            snackbar.labelText = snacks[0];
            $(".mdc-snackbar__action").addClass("fear");
            snacks.shift();
            snackbar.open();
            localStorage.setItem("snackbar", JSON.stringify(snacks));
        }
    } catch(e){
        localStorage.setItem("snackbar", "[]");
    }
}

function addSnackbarQueue(txt){
    try {
        if( typeof JSON.parse(localStorage.getItem("snackbar")) != "object" ){
            throw new Error("Snackbar not setup");
        }
    } catch(e){
        localStorage.setItem("snackbar", "[]");
    }
    let snacks = JSON.parse(localStorage.getItem("snackbar"));
    snacks.push(txt);
    localStorage.setItem("snackbar", JSON.stringify(snacks));
}

// Record the time the page finished loading
let pageLoadedTime = new Date();

// Calculate how long it took the page to load
let pageLoadOffset = pageLoadedTime.getTime() - pageLoadStart.getTime();

// Create a variable containing the time received from the server
let serverTime = new Date(atob($("meta[name=server-utc-time]").attr("content")));

// Offset the server time with the page load time
serverTime.setTime(serverTime.getTime() + pageLoadOffset);

let short_months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

let long_months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

let short_days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
let long_days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

let countdowns = setInterval(() => {

    // Calculate how long it's been since the page loaded
    let timePassed = new Date().getTime() - pageLoadedTime.getTime();

    // Get current server time by adding time since page load to the serverTime
    let currentTime = new Date(serverTime.getTime() + timePassed);

    // Get all elements on page that require the time to be updated
    let counters = document.getElementsByClassName("js-timer");
    for( let x = 0; x < counters.length ; x++ ){
        let i = counters[x];
        let countType = $(i).data("timer-type");
        switch (countType) {
            case "countdown":
                let countDownDate = atob($(i).data("count-down-date"));
                let dist = new Date(countDownDate).getTime() - currentTime.getTime();
                let countDownTxt = "";
                let d = Math.floor(dist / (1000 * 60 * 60 * 24)); // Days till date
                let h = Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));  // Hours till date
                let m = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60)); // Minutes till date
                let s = Math.floor((dist % (1000 * 60)) / 1000); // Seconds till date
                countDownTxt = (d > 0) ? d + "d " : countDownTxt;
                countDownTxt = (dist > 3600000 ) ? countDownTxt + h + "h ": countDownTxt;
                countDownTxt = countDownTxt + m + "m ";
                countDownTxt =  ( dist < 86400000 )? countDownTxt + s + "s" : countDownTxt;
                if( $(i).hasClass("js-timer__warning") && dist < parseInt($(i).data("count-down-warning")) && ! $(i).hasClass("red-txt") ){
                    $(i).addClass("red-txt");
                }
                if( dist < 0 ){
                    countDownTxt = "0s";
                    $(i).removeClass("js-timer");
                }
                i.innerHTML = countDownTxt;
                break;
            case "current":
                let timeFormat = $(i).data("time-format");
                let times = {
                    "d":currentTime.getDate().toString().padStart(2, '0'),
                    "D":short_days[currentTime.getDay()],
                    "l":long_days[currentTime.getDay()],
                    "w":currentTime.getDay() + 1,
                    "F":long_months[currentTime.getMonth()],
                    "m":(currentTime.getMonth() + 1).toString().padStart(2, "0"),
                    "M":short_months[currentTime.getMonth()],
                    "n":currentTime.getMonth() + 1,
                    "Y":currentTime.getFullYear(),
                    "y":currentTime.getFullYear() % 100,
                    "a": ( currentTime.getHours() >= 12 ) ? "pm" : "am",
                    "h":(currentTime.getHours() % 12).toString().padStart(2, '0'),
                    "H":currentTime.getHours().toString().padStart(2, "0"),
                    "i":currentTime.getMinutes().toString().padStart(2, '0'),
                    "s":currentTime.getSeconds().toString().padStart(2, '0')
                };
                let dateString = "";
                for( let a = 0; a < timeFormat.length ; a++ ){
                    dateString += ( timeFormat[a] in times ) ? times[timeFormat[a]] : timeFormat[a];
                }
                console.log(dateString);
                $(i).html(dateString);
                break;
        }
    }
}, 1000);