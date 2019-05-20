"use strict";
/* auth */
/* TODO REMOVE PREVIOUS SNACKBAR FUNCTION CALLS */
let useGoogleAnalytics = $("meta[name=use-google-analytics]").attr("content") === "true";
let isSignedIn = $("meta[name=signed-in]").attr("content") === "true";
let gtagTrackingID = (useGoogleAnalytics) ? $("meta[name=gtag-tracking-id]").attr("content") : "";

function initializeItems(selector, mdcClass){
    let stuff = document.querySelectorAll(selector);
    for( let x = 0 ; x < stuff.length ; x ++ ){
        mdcClass.attachTo(stuff[x]);
    }
}

function replaceAll(string, search, replacement) {
    return string.replace(new RegExp(search, 'g'), replacement);
}

function initializeMDC(alreadyCalled = false){
    // Handle stuff like buttons that should automatically have ripple added

    initializeItems(".mdc-button, .mdc-tab__ripple, .mdc-card__primary-action", mdc.ripple.MDCRipple);

    initializeItems(".mdc-text-field", mdc.textField.MDCTextField);

    initializeItems(".mdc-text-field-helper-text", mdc.textField.MDCTextFieldHelperText);

    initializeItems(".mdc-tab-bar", mdc.tabBar.MDCTabBar);

    initializeItems(".mdc-list", mdc.list.MDCList);

    initializeItems(".mdc-chip-set", mdc.chips.MDCChipSet);

    initializeItems(".mdc-select", mdc.select.MDCSelect);

    initializeItems(".mdc-text-field-character-counter", mdc.textField.MDCTextFieldCharacterCounter);

    alreadyCalled ? window.mdc.autoInit(document, () => {}) : mdc.autoInit();

    if( document.querySelectorAll(".markdown-content-unready").length > 0 ){
        readyMarkdown();
    }
}

function readyMarkdown() {
    let markdowns = document.querySelectorAll(".markdown-content-unready");
    for( let x = 0; x < markdowns.length ; x++ ){
        markdowns[x].innerHTML = customMarkDownParser(atob(markdowns[x].innerHTML));
        markdowns[x].classList.remove("markdown-content-unready");
    }
}

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
                immediateSnackbarList(decoded_resp.message);
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
        let success_event = new Event("content_loaded");
        setTimeout(() => {
            document.body.dispatchEvent(success_event);
        }, 1000);
        if( c.getResponseHeader("X-Page-Redirect") != null ){
            changePage(c.getResponseHeader("X-Page-Redirect"));
            return;
        }

        if( c.getResponseHeader("X-Fetch-New-Sources") === "true" ){
            addSources(c);
        }

        if( c.getResponseHeader("X-Load-Sub") != null ){
            loadSubPage();
        }
    }).fail(function(){
        $(vr).fadeIn();
        $(pld).addClass("mdc-linear-progress--closed");
    }).then(function(){
        $(vr).ready(function(){
            $(pld).addClass("mdc-linear-progress--closed");
            $(vr).fadeIn(1);
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
        node.setAttribute("type", "text/css");
        node.setAttribute("href", new_src.css[y]);
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
initializeMDC();


function openPopup(url, platform) {
    let newpopup = window.open(url,'Share ' + platform,'height=400,width=550');
    if (window.focus) {
        newpopup.focus()
    }
    return newpopup;
}

function loadSubPage(path = null){
    let pld = document.querySelector(".page-loader");
    $(pld).removeClass("mdc-linear-progress--closed");
    if(path !== null){
        history.pushState({}, null, path);
    }
    $.get("/subload.php?page=" + window.location.pathname, (a, b, c) => {
        if( c.getResponseHeader("X-Page-Redirect") !== null ){
            changePage(c.getResponseHeader("X-Page-Redirect"));
            return;
        }
        $("#sub-variable-region").html(a);
        if( c.getResponseHeader("X-Fetch-New-Sources") === "true" ){
            addSources(c);
        }
        $(pld).addClass("mdc-linear-progress--closed");
    });
}

// Create an instance of the drawer and store it
const drawer = new mdc.drawer.MDCDrawer(document.querySelector(".mdc-drawer"));
const shareBox = new mdc.dialog.MDCDialog(document.querySelector('.share-dialog'));
const confirmDialog = new mdc.dialog.MDCDialog(document.querySelector('.confirm-dialog'));


let snackbar = new mdc.snackbar.MDCSnackbar(document.querySelector(".mdc-snackbar"));

// Automatically open the drawer if the user is on a desktop device
if( window.innerWidth > 1250 ){
    drawer.open = true;
}

window.onpopstate = function (){changePage()};

$(document.body).on("click", ".change-page", ev => {
    if( ev.ctrlKey ){
        return window.open($(ev.currentTarget).data("page"));
    }
    changePage($(ev.currentTarget).data("page"));
    if(window.innerWidth <= 1250){
        drawer.open = false;
        $(".obs").addClass("fear");
    }
}).on("click", ".share-card", ev => {
    let url = $(ev.currentTarget).data("share-url");
    $(".share-url").find(".url-content").val(url);
    shareBox.open();
}).on("click", ".copy-social", ev => {
    document.querySelector(".url-content").select();
    document.execCommand("copy");
    $(ev.currentTarget).html("Copied!");
    setTimeout(()=> {$(ev.currentTarget).html("Copy");}, 500);
}).on("click", ".submit-form", ev => {
    let button = ev.currentTarget;
    button.setAttribute("disabled", true);
    let cont = button.parentElement.parentElement;
    let form = $(cont).find("form");
    $.post($(form).data("action"), $(form).serialize(), (a, b, c) => {
        // TODO Add more callback options
        let resp = JSON.parse(a);
        immediateSnackbarList(resp.message);
        if( resp.status === "success" ){
            switch($(form).data("callback")){
                case "reload":
                    changePage();
                    break;
                case "change-page":
                    changePage($(form).data("reload-page"));
                    break;
            }
        } else {
            button.removeAttribute("disabled");
        }
    }).fail(ev => {
        button.removeAttribute("disabled");
        console.log(ev);
        immediateSnackbarList(["There was an error submitting the form: " + ev.statusText, "Contact us if this continues"]);
    });

}).on("click", ".sign-out", () => {
    location.replace("/signout.php");
}).on("click", ".sub-page-change", ev => {
    // TODO IMPLEMENT THIS
    let i = ev.currentTarget;
    loadSubPage(i.getAttribute("data-page"));
}).on("click", ".menu-trigger", () => {
    // When user clicks on the hamburger menu in the appBar, trigger the drawer

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
}).on("click", ".social", ev => {
    let i = ev.currentTarget;
    let Ji = $(i);
    let url = $(".url-content").val();
    let social_media  = [["so-facebook", "https://www.facebook.com/sharer/sharer.php?u=", "Facebook"], ["so-twitter", "https://twitter.com/intent/tweet?url=", "Twitter"], ["so-linkedin", "https://www.linkedin.com/shareArticle?mini=true&url=", "LinkedIn"], ["so-pinterest", "https://www.pinterest.com/pin/create/button/?url=", "Pinterest"]];
    for( let x = 0 ; x < social_media.length ; x ++ ) {
        if( Ji.hasClass(social_media[x][0]) ){
            openPopup(social_media[x][1] + encodeURIComponent(url), social_media[x][2]);
            return true;
        }
    }
    if( Ji.hasClass("so-envelope") ){
        window.location.href = "mailto:?body=" + encodeURIComponent(url);
        return true;
    }
    if( Ji.hasClass("so-print") ){
        var popup = openPopup(url);
        let readyCheck = setInterval(function() {
            if(popup.document.readyState === 'complete' ) {
                clearInterval(readyCheck);
                setTimeout(() => {
                    popup.postMessage("printClose", url);
                }, 1200);
            }
        }, 100);
        return true;
    }
    // TODO ADD SERVER REPORTING OF ERROR
    console.log("Unexpected case: " + i);
    return false;
}).on("click", ".markdown-content a", ev => {
    if( ev.currentTarget.getAttribute("href") !== null ){
        return confirm("The link you clicked on is taking you to: "+ ev.currentTarget.getAttribute("href") + ". Are you sure you want to continue?");
    }
}).on("submit", ".no-submit", ev => {
    ev.preventDefault();
});

function dependencySetup(setup, trial_wait = 500, max_num_tries = 20){
    let num_tries = 0;
    let attempts = setInterval(() => {
        try {
            num_tries += 1;
            setup();
            clearInterval(attempts);
        } catch(e){
            if( num_tries >= max_num_tries ){
                immediateSnackbarList(["This page could not be sucessfully setup. Please reload the page. If this keeps happening, please contact us <a href='/contact'> here</a>"]);
                clearInterval(attempts);
            }
        }
    }, 500);
}

window.addEventListener("message", receiveMessage, false);

function receiveMessage(event) {
    if (event.origin === window.location.origin ){
        if( event.data === "printClose" ){
            let readyInterval = setInterval(() => {
               if(initial_load){
                   clearInterval(readyInterval);
                   print();
                   close();
               }
            }, 100);
        }
    }
}
let initial_load = false;
document.body.addEventListener("content_loaded", () => {
    initial_load = true;
});

$("#variable-region").on("DOMSubtreeModified", () => {
    initializeMDC(true);
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

confirmDialog.listen('MDCDialog:opened', () => {
    initializeMDC(true);
});

function immediateSnackbarList(array) {
    for( let x = 0 ; x < array.length ; x ++ ){
        addSnackbarQueue(array[x]);
    }
    playSnackbarQueue();
}

function showConfirmation(template = document.createElement("template"), onConfirm = ()=>{}, onReject = () => {}, title = "Confirm"){
    $(".confirm-dialog .mdc-dialog__title").html(title);
    let content = template.content.cloneNode(true);
    let dialog_content = document.getElementById("confirm-dialog-content");
    dialog_content.innerHTML = "";
    dialog_content.appendChild(content);
    confirmDialog.open();
    $(".confirm-dialog .mdc-dialog__button").on("click", e => {
        let confirmation = e.currentTarget.getAttribute("data-mdc-dialog-action") === "yes";
        confirmation ? onConfirm() : onReject();
        $(".confirm-dialog .mdc-dialog__button").off();
        $(".confirm-dialog .mdc-dialog__scrim").off();

    });
    $(".confirm-dialog .mdc-dialog__scrim").on("click", ()=> {
        onReject();
        $(".confirm-dialog .mdc-dialog__scrim").off();
        $(".confirm-dialog .mdc-dialog__button").off();
    });
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

function getNthOccurence(string, search, n){
    let matches = 0;
    for( let x = 0; x < string.length - search.length; x++ ){
        let subString = string.substring(x, x + search.length);
        if( subString === search ){
            matches += 1;
        }
        if( matches === n ){
            return x;
        }
    }
    return -1;
}

function escapeHtml(htmlString){
    let newString = replaceAll(htmlString, /&/, "&amp;");
    newString = replaceAll(newString,/</, "&lt;");
    return newString;
}

function htmlEntities(str) {
    return replaceAll(str, /(.)/g, (match, letter) => {
       return "&#" + letter.charCodeAt(0) + ";";
    });
}

function customMarkDownParser(markdown){
    // Escape any html tags
    let converter = new showdown.Converter();
    converter.setFlavor('github');
    converter.setOption("emoji", true);
    converter.setOption("encodeEmails", true);
    converter.setOption("openLinksInNewWindow", true);
    converter.setOption("tables", true);
    converter.setOption("tasklists", true);
    converter.setOption("headerLevelStart", 2);
    converter.setOption("simpleLineBreaks", true);

    markdown = escapeHtml(markdown);

    let addColor = str => {
        let color_presets = {
            "red": "d63031",
            "blue": "0984e3",
            "purple": "6c5ce7",
            "green": "00b894",
            "yellow": "fdcb6e",
            "pink": "fd79a8",
            "teal": "00cec9",
            "grey": "636e72"
        };
        let content_start = -1;
        let content_end = -1;
        let color_start = -1;
        let color_end = -1;
        let num_backticks = 0;
        let is_code = false;

        for(let x = 0 ; x < str.length ; x++){
            let letter = str[x];
            if( letter === "`" && ! is_code ){
                num_backticks++;
                continue;
            }
            if( num_backticks > 0 && ! is_code){
                if( num_backticks === 1){
                    num_backticks++;
                }
                if( str.substring(x).indexOf("`".repeat(num_backticks)) !== -1 || str.substring(x).indexOf("`".repeat(num_backticks - 1)) !== -1 ){
                    is_code = true;
                } else {
                    num_backticks = 0;
                }
            }
            if( is_code ){
                if( str.substring(x, x + (num_backticks - 1) ) === "`".repeat(num_backticks - 1) ){
                    x += (str[x + (num_backticks - 1) ] !== "`") ? num_backticks - 1 : num_backticks;
                    is_code = false;
                    num_backticks = 0;
                } else {
                    continue;
                }
            }

            if( letter === "\\" ){
                x = x + 2;
            }

            if( letter === "\n" ){
                content_start = -1;
                content_end = -1;
                color_start = -1;
                color_end = -1;
                x++;
            }

            if( content_start !== -1){
                if(color_start !== -1){
                    if(letter === ")"){
                        color_end = x;
                        let color = replaceAll(str.substring(color_start + 1, color_end) ,/\W/, "");
                        if( typeof color_presets[color] !== 'undefined' && color !== "rainbow"){
                            color = color_presets[color];
                        }

                        let content = str.substring(content_start + 1, content_end);
                        let tag = (color !== "rainbow") ? "<a style='color: #"+ color +"'>"  + content + "</a>": "<a class='rainbow'>" + content + "</a>";
                        str = str.substring(0, content_start) + tag + str.substring(color_end + 1);
                        content_start = -1;
                        content_end = -1;
                        color_start = -1;
                        color_end = -1;
                        x = 0;
                    }
                } else {
                    if( str.substring(x, x + 2) === "}(" ){
                        content_end = x;
                        color_start = x + 1;
                    }
                }
            } else {
                if(letter === "{"){
                    content_start = x;
                }
            }
        }
        return str;
    };

    markdown = addColor(markdown);

    // while(markdown.indexOf("\\") !== -1){
    //     let index = markdown.indexOf("\\");
    //     markdown = markdown.substring(0, index) + htmlEntities(markdown[index + 1]) + markdown.substring(index + 2);
    // }

    let converted = converter.makeHtml(markdown);

    let center_regex = new RegExp(/(?:\(C\)(.*?)\(\/C\))/g);
    let htmlString = replaceAll(converted, center_regex, (match, content) => {
        return "<div class='flx-ctr'>" + content + "</div>";
    });

    return htmlString;
}


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

function getDateEnding(date){
    if( 10 < date && date < 14 ){
        return "th";
    }
    if( date % 10 === 1 ){
        return "st";
    }
    if( date % 10 === 2 ){
        return "nd";
    }
    if( date % 10 === 3 ){
        return "rd";
    }
    return "th";
}

let countdowns = setInterval(() => {

    // Calculate how long it's been since the page loaded
    let timePassed = new Date().getTime() - pageLoadedTime.getTime();

    // Get current server time by adding time since page load to the serverTime
    let currentTime = new Date(serverTime.getTime() + timePassed);

    // Get all elements on page that require the time to be updated
    let counters = document.getElementsByClassName("js-timer");
    let getTimes = dateObject => {
        let twelve_hours = (dateObject.getHours() % 12).toString().padStart(2, '0');
        return {
            "d":dateObject.getDate().toString().padStart(2, '0'),
            "D":short_days[dateObject.getDay()],
            "j":dateObject.getDate(),
            "l":long_days[dateObject.getDay()],
            "S":getDateEnding(dateObject.getDate()),
            "w":dateObject.getDay() + 1,
            "F":long_months[dateObject.getMonth()],
            "m":(dateObject.getMonth() + 1).toString().padStart(2, "0"),
            "M":short_months[dateObject.getMonth()],
            "n":dateObject.getMonth() + 1,
            "Y":dateObject.getFullYear(),
            "y":dateObject.getFullYear() % 100,
            "a": ( dateObject.getHours() >= 12 ) ? "pm" : "am",
            "h": (twelve_hours === "00" ? "12" : twelve_hours),
            "H":dateObject.getHours().toString().padStart(2, "0"),
            "i":dateObject.getMinutes().toString().padStart(2, '0'),
            "s":dateObject.getSeconds().toString().padStart(2, '0')
        };
    };
    let formatTimeString = (timeFormat, datetime = currentTime) => {
        let times = getTimes(datetime);
        let dateString = "";
        let escape = false;
        for( let a = 0; a < timeFormat.length ; a++ ){
            if( ! escape && timeFormat[a] === "\\" ){
                escape = true;
                continue;
            }
            dateString += ( timeFormat[a] in times && ! escape ) ? times[timeFormat[a]] : timeFormat[a];
            escape = false;
        }
        return dateString;
    };
    let remove_timer = [];
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
                    remove_timer.push(i);
                }
                i.innerHTML = countDownTxt;
                break;
            case "current":
                $(i).html(formatTimeString($(i).data("time-format")));
                break;
            case "to-local-time":
                let local_time_display = (i.hasAttribute("data-show-local")) ? new Date().toLocaleTimeString('en-us',{timeZoneName:'short'}).split(' ')[2] : "";
                $(i).html(formatTimeString($(i).data("time-format"), new Date(atob($(i).data("time-date")))) + " " + local_time_display);
                remove_timer.push(i);
                break;
        }
    }
    for( let x = 0 ; x < remove_timer.length ; x++ ){
        $(remove_timer[x]).removeClass("js-timer");
    }
}, 1000);