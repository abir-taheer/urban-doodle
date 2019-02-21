'use strict';
/* auth */
function onSignIn(googleUser) {
    let token = googleUser.getAuthResponse().id_token;
    //let profile = googleUser.getBasicProfile();

    $.post("/requests.php", {"request":"auth", "token":token} ,
        function(response){
            let decoded_resp = JSON.parse(response);
            if( decoded_resp.status === "success" ){
                //add the success message to the queue
                addSnackbarQueue(decoded_resp.message, 3000);
                //refresh the page and they should see the updated page
                window.location.reload();
            } else {
                //there was an error, read out the error in a snackbar
                let x = 0;
                while( x < decoded_resp.message.length ){
                    showSnackbar(decoded_resp.message[x] , 2000);
                    x++;
                }
            }
    });
}

function showSnackbar(message, timeout, actionButton, handler, buttonText){
    let snackbarContainer = document.querySelector('#snackbar');
    let sb = $('#snackbar-button');
    void( ! actionButton ) ? sb.addClass('fear'): sb.removeClass('fear');
    let data = {
        message: message,
        timeout: timeout,
        actionHandler: handler,
        actionText: buttonText
    };
    snackbarContainer.MaterialSnackbar.showSnackbar(data);
}

function addSnackbarQueue(message, timeout){
    try {
        if (typeof localStorage.getItem("snackbar") === undefined) {
            localStorage.setItem("snackbar-alerts", "[[]]");
        }
        let currentAlerts = JSON.parse(localStorage.getItem("snackbar-alerts"));
        let newAlert = [message, timeout];
        currentAlerts.push(newAlert);
        localStorage.setItem("snackbar-alerts", JSON.stringify(currentAlerts));
    } catch (er){
        localStorage.setItem("snackbar-alerts", "[]");
        addSnackbarQueue(message, timeout);
    }
}

function stdSetup(){
    //give all of the material icons a notranslate class that tells our translator not to change them
    let icons = document.querySelectorAll(".material-icons");
    let xy = 0;
    while( xy < icons.length){
        if( ! $(icons[xy]).hasClass("notranslate") ){
            $(icons[xy]).addClass("notranslate");
        }
        xy++;
    }
    //A custom function to add the special mdl classes so that our raw html code is cleaner
    //look for elements that have the "unready" class so that we know that they need to be prepared
    let unelements = document.querySelectorAll(".unready");
    let x = 0;
    while( x < unelements.length ){
        let curr = unelements[x];
        let setup_type = $(curr).data("type");
        let new_classes = [];
        switch(setup_type){
            case "std-card-cont":
                new_classes.push("mdl-color--white","mdl-shadow--2dp", "mdl-cell", "mdl-cell--12-col");
                break;
            case "std-expand":
                new_classes.push("mdl-card__title", "mdl-card--expand", "mdl-color--teal-300");
                break;
            case "menu-item":
                new_classes.push("mdl-navigation__link", "change-page", "clickable");
                break;
            case "menu-txt":
                new_classes.push("mdl-color-text--blue-grey-400", "material-icons");
                $(curr).attr("role", "presentation");
                break;
            case "f-txt-input":
                new_classes.push("mdl-textfield", "mdl-js-textfield", "mdl-textfield--floating-label");
                break;
            case "btn-rpl-act":
                new_classes.push("mdl-button", "mdl-js-button", "mdl-button--raised", "mdl-button--accent", "mdl-color-text--white");
                break;
            case "spinner":
                new_classes.push("mdl-spinner", "mdl-js-spinner","is-active");
        }
        let y = 0;
        while( y < new_classes.length ){
            $(curr).addClass(new_classes[y]);
            y++;
        }
        $(curr).removeClass("unready");
        x++;
    }
    componentHandler.upgradeAllRegistered();
}

function playSnackbarQueue(attempt){
    let data = localStorage.getItem("snackbar-alerts");
    if(typeof data !== undefined){
        let x;
        try {
            let alerts = JSON.parse(data);
            //there are alerts and we need to play them all out
            for(x in alerts){
                showSnackbar(alerts[x][0], alerts[x][1], false, false, "");
            }
            localStorage.setItem("snackbar-alerts", "[]");
        } catch(e) {
            // the dom elements probably aren't set up yet. Try again in 1 second
            attempt = ( attempt === undefined ) ? 0 : attempt;
            // if we've already tried unsuccesfully 3 times, give up and try to deliver the alerts via standard javascript alerts
            if( attempt < 3 ){
                setTimeout(function(){playSnackbarQueue( attempt + 1 );}, 1000);
            } else {
                let alerts = JSON.parse(data);
                for( x in alerts ){
                    alert(alerts[x][0]);
                }
                localStorage.setItem("snackbar-alerts", "[]");
            }
        }
    } else {
        localStorage.setItem("snackbar-alerts", "[]");
    }
}

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

/* end auth */

/* translate widget */
$('body').ready(function(){
    $('.material-icons').addClass('notranslate');
});
function googleTranslateElementInit() {
    new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
setTimeout(function(){makeEnglish()}, 1000);
function makeEnglish() {
    let e = $('.goog-te-menu-value > span').first();
    if( $(e).html() === "Select Language" ){
        $(e).html('English');
    }
}
$('.goog-te-banner > img').click(function() { setTimeout(function(){makeEnglish()}, 1000); });
/* end translate widget */

$(document).ready(function(){
    //add the necessary classes to the mdl items
    stdSetup();

    //if there are any stored snackbar messages, play them out
    playSnackbarQueue();

    //add all event listeners to below here to prevent mismatch based on mdl classes

    //make a jquery get request to a signout page on the click of a signout button
    $('.sign-out').click(function(){
        $.post('/requests.php', {request:"signout"}).done(function() {
            addSnackbarQueue("You have been sucessfully signed out!", 2000);
            window.location.reload();
        }).fail(function(er){
            closeObfuscator();
            showSnackbar("There was an error signing you out. You are currently not connected to the internet!", 3000);
        });
    });

    //load the requested page here:
    changePage(null);
    $('.change-page').click(function(ev){
        let el = ev.currentTarget;
        if( $(el).hasClass("ignore-page") ){return;}
        let path = $(el).data("page");
        void( ev.ctrlKey ) ? window.open(path, '_blank') : changePage(path);
    });
});

//if the mdl obfuscator is open, close it by triggering a click event
function closeObfuscator(){
    if($('.mdl-layout__obfuscator').first().hasClass("is-visible")){
        $('div > .mdl-layout__obfuscator').trigger('click');
    }
}

//every time that the variable region changes, call our function to setup everything that is unready
$('#variable-region').bind("DOMSubtreeModified",function(){
    stdSetup();
});

function changePage(path, showError){
    let vr = document.getElementById("variable-region");
    let vs = document.getElementById("variable-sources");
    let pld = document.querySelector(".page-loader");
    $(pld).fadeIn();
    $(vr).fadeOut(1);
    if(path !== null){
        history.pushState({}, null, path);
    }
    $.get("/load.php?page=" + window.location.pathname, function(a, b, c){
        $(vr).html(a);
        if( c.getResponseHeader("X-Fetch-New-Sources") === "true" ){
            addSources(c);
        }
    }).fail(function(){
        $(vr).html($('#network-error').html());
        $(vr).fadeIn();
        $(pld).fadeOut();
        showSnackbar("There was an error serving the page. Please check your internet connection!", 2000);
        closeObfuscator();
    }).then(function(){
        closeObfuscator();
        $(vr).ready(function(){
            $(pld).fadeOut();
            $(vr).fadeIn();
        });
    });
}

function addSources(c){
    let vr = document.getElementById("variable-region");
    let vs = document.getElementById("variable-sources");
    let x,y;
    let new_src = JSON.parse(c.getResponseHeader("X-New-Sources"));
    let nce = c.getResponseHeader("X-Nonce");
    for(x in new_src.script){
        let node = document.createElement("script");
        node.setAttribute("nonce", nce);
        node.setAttribute("src", new_src.script[x]);
        vs.appendChild(node);
    }
    for(y in new_src.css){
        let node = document.createElement("link");
        node.setAttribute("nonce", nce);
        node.setAttribute("rel", "stylesheet");
        node.setAttribute("href", new_src.css[x]);
        vs.appendChild(node);
    }
}
$("#variable-region").on("click", ".form-submit", function (ev) {
    //since the button is outside of the form, it won't trigger the form submit  event
    let b = ev.currentTarget;
    //disable the button and show the spinner
    $(b).attr("disabled", true);
    $(b.parentNode).find(".button-spinner").removeClass("fear");

    //next obtain the form element
    let form = $(b.parentNode.parentNode).find("form")[0];
    $(form).find(".error-text").addClass("fear");

    let check = validateForm(form.elements);
    if( check.status === "ok" ){
        //send out the form to its intended target
        $.post( $(form).data("action") , $(form).serialize(), function(a, b, c){
           switch($(form).data("callback")){
               case "reload":
                   let x;
                   a = JSON.parse(a);
                   if( a.status === "success" ){
                       for( x in a.message ){
                           addSnackbarQueue(a.message[x], 5000);
                       }
                       window.location.reload();
                   } else {
                       for(x in a.message){
                           showSnackbar(a.message[x], 4000, false,null, null);
                       }
                   }
                   break;
           }
        });
    }
    //don't send the form and show the error
    setTimeout(function(){
            $(b).attr("disabled", false);
            $(b.parentNode).find(".button-spinner").addClass("fear");
            $(form).find(".error-text").removeClass("fear");
        }, 1000);
});

function validateForm(form_elements){
    let response = {"status": "ok"};
    for(let x=0; x < form_elements.length ; x++ ){
        let e = form_elements[x];
        let val = e.value;
        if( $(e).hasClass("validate") ){
            let v = $(e).data("validation");
            if( v.includes("r") && val == "" ){;
                response.status = "error";
                $(e.parentNode).addClass("is-invalid");
            }
            if( v.includes("p") && val.match($(e).attr("pattern")) == null){
                response.status = "error";
                $(e.parentNode).addClass("is-invalid");
            }
        }
    }
    return response;
}