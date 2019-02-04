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
                showSnackbar(decoded_resp.message, 3000, false, false, "");
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
        timeout: 2000,
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
        console.log(er);
        localStorage.setItem("snackbar-alerts", "[]");
        addSnackbarQueue(message, timeout);
    }
}

function playSnackbarQueue(){
    try {
        let data = localStorage.getItem("snackbar-alerts");
        if(typeof data !== undefined){
            let alerts = JSON.parse(data);
            //there are alerts and we need to play them all out
            let x;
            for(x in alerts){
                showSnackbar(alerts[x][0], alerts[x][1], false, false, "");
            }
            localStorage.setItem("snackbar-alerts", "[]");
        } else {
            localStorage.setItem("snackbar-alerts", "[]");
        }
    } catch(e) {
        setTimeout(function(){playSnackbarQueue();}, 1000);
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
    //if there are any stored snackbar messages, play them out
    playSnackbarQueue();

   //load the requested page here:
    changePage(null);
    $('.change-page').click(function(ev){
        let path = $(ev.currentTarget).data("page");
        changePage(path);
    });
});

function changePage(path){
    $('.page-loader').fadeIn();
    // language=JQuery-CSS
    $('#variable-region').fadeOut(1);
    if($('.mdl-layout__obfuscator').first().hasClass("is-visible")){
        $('div > .mdl-layout__obfuscator').trigger('click');
    }
    if(path !== null){
        history.pushState({}, null, path);
    }
    $.get("/load.php?page=" + window.location.pathname, function(response){
        $("#variable-region").html(response);
    }).then(function(){
       $("#variable-region").ready(function(){
           $('.page-loader').fadeOut();
           $("#variable-region").fadeIn();
       });
    });
}