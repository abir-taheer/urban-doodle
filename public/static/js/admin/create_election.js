if( typeof select_election_pic == 'undefined' ){
    var select_election_pic = new mdc.dialog.MDCDialog(document.querySelector('.election-pic-dialog'));
} else {
    select_election_pic = new mdc.dialog.MDCDialog(document.querySelector('.election-pic-dialog'));
}

$(".pick-election-img").off().on("click", ".selectable", ev => {
   $(".pick-election-img .selectable").removeClass("selected");
   $(ev.currentTarget).addClass("selected");
   if( document.querySelectorAll(".pick-election-img .selected").length === 1){
       document.querySelector(".can-select-img").removeAttribute("disabled");
   }
});

$(".upload-election-img").on("click", ev => {
    let upload = document.createElement("input");
    upload.setAttribute("type", "file");
    upload.setAttribute("name", "new_file");
    let token_element = document.createElement("input");
    token_element.setAttribute("name", "token");
    token_element.setAttribute("value", $(ev.currentTarget).data("form-token"));
    let form = document.createElement("form");
    form.appendChild(upload);
    form.appendChild(token_element);
    $(upload).on("change", ev =>{
        let file = upload.files[0];
        let status = true;
        if( file.size > 500000 ){
            addSnackbarQueue("That file is too large. Please make sure that the uploaded file is less than 0.5MB.");
            status = false;
        }
        if( ! file.type.startsWith("image/") ){
            addSnackbarQueue("That file is not an image!");
            status = false;
        }
        playSnackbarQueue();

        if(status){
            // Send it to the server and then make an item on the page that gets the image using the url from the server
            let reader = new FileReader();
            reader.onload = e => {
                $.ajax({
                    type: 'POST',
                    url:"/requests.php",
                    data: new FormData($(form)[0]),
                    processData: false,
                    contentType: false,
                    success: r => {
                        let data = JSON.parse(r);
                        if( data.status === "success" ){
                            $(".selectable").removeClass("selected");
                            $(".upload-election-img").after("<div class=\"mdc-layout-grid__cell selectable selected\"><img class=\"mdc-image-list__image\" src=\"/static/img/election_covers/"+ data.new_name +"\"></div>");
                        } else {
                            console.log(data);
                        }
                    }
                });
                // $(".pfp-confirm-set-src").attr("src", reader.result);
            };
            reader.readAsDataURL(file);
        }
    });
    $(upload).click();
});

$(".change-preview").on("click", ()=> {select_election_pic.open()});
$(".can-select-img").on("click", () => {
   let source = document.querySelector(".pick-election-img .selected").getAttribute("data-src");
   $("#election_pic").val(source);
   $(".image-preview img").attr("src", "/static/img/election_covers/" + source);
});