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
    if( ! actionButton ){
        $('#snackbar-button').addClass('fear');
    } else {
        $('#snackbar-button').removeClass('fear');
    }
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
        }
        let y = 0;
        while( y < new_classes.length ){
            $(curr).addClass(new_classes[y]);
            y++;
        }
        $(curr).removeClass("unready");
        x++;
    }
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
        if( ev.ctrlKey ){
            window.open(path, '_blank');
        } else {
            changePage(path);
        }
    });
});

function closeObfuscator(){
    if($('.mdl-layout__obfuscator').first().hasClass("is-visible")){
        $('div > .mdl-layout__obfuscator').trigger('click');
    }
}

$('#variable-region').bind("DOMSubtreeModified",function(){
    stdSetup();
});

function changePage(path, showError){
    $('.page-loader').fadeIn();
    // language=JQuery-CSS
    $('#variable-region').fadeOut(1);
    if(path !== null){
        history.pushState({}, null, path);
    }
    $.get("/load.php?page=" + window.location.pathname, function(response){
        $("#variable-region").html(response);
    }).fail(function(){
        $('#variable-region').html($('#network-error').html());
        $('#variable-region').fadeIn();
        $('.page-loader').fadeOut();
        showSnackbar("There was an error serving the page. Please check your internet connection!", 2000);
        closeObfuscator();
    }).then(function(){
        closeObfuscator();
        $("#variable-region").ready(function(){
            $('.page-loader').fadeOut();
            $("#variable-region").fadeIn();
        });
    });
}