if(typeof tabs == "undefined"){
    var tabs = document.querySelectorAll(".sub-page-tab");
} else {
    tabs = document.querySelectorAll(".sub-page-tab");
}

for(let x = 0; x < tabs.length ; x++ ){
    let i = tabs[x];
    if( window.location.pathname.includes(i.getAttribute("data-page")) ){
        $(".sub-page-tab").removeClass("mdc-tab--active").find(".mdc-tab-indicator").removeClass("mdc-tab-indicator--active");
        i.classList.add("mdc-tab--active");
        i.querySelector(".mdc-tab-indicator").classList.add("mdc-tab-indicator--active");
    }
}

$(".change-pic-overlay").on("click", () => {
   $("#pfp-upload").trigger("click");
});

$("#pfp-upload").on("change", ev => {
    // TODO MAYBE SOME ISSUE HERE / CHECK FOR FILE TYPES
    let element = ev.currentTarget;
    let file = element.files[0];
    if( file.size < 1000000 ){
        let reader = new FileReader();
        reader.onload = e => {
            showConfirmation(document.getElementById("pfp-confirm"), () => {
                $(".candidate-photo").attr("src", reader.result);
                console.log("success bois");
            });
            $(".pfp-confirm-set-src").attr("src", reader.result);
            console.log(reader.result);
        };
        reader.readAsDataURL(file);
    } else {
        addSnackbarQueue("That image is too large. The file size limit is 1MB.");
        playSnackbarQueue();
    }
});