$(".select-material-type").on("change", ev => {
    if( ev.currentTarget.value === "poster" ){
       $(".poster-upload").removeClass("fear");
       $(".other-content").addClass("fear");
   } else {
       $(".poster-upload").addClass("fear");
       $(".other-content").removeClass("fear");
   }
});
$(".upload-material-poster").on("click", () => {
    $(".poster-upload-input")[0].click();
});
$(".change-candidate-pfp").on("click", () => {
    $("#pfp-upload input[type='file']").click();
});

$(".poster-upload-input").on("change", ev => {
    // TODO MAYBE SOME ISSUE HERE / CHECK FOR FILE TYPES
    let element = ev.currentTarget;
    let file = element.files[0];
    let errors = [];
    if( file.type !== "application/pdf" ){
        errors.push("The file uploaded is not a pdf");
    }

    if( file.size > 2000000 ){
        errors.push("That file is too large. The file size limit is 2MB");
    }

    if( errors.length === 0 ){
        $(".material-filename").html(file.name).parent().removeClass("fear");

    } else {
        element.value = "";
        immediateSnackbarList(errors);
    }
});

$(".submit-materials").on("click", () => {
    if( $(".select-material-type").val() === "poster" ){
        if( $(".poster-upload-input").val() === ""){
            immediateSnackbarList(["Poster file has not been uploaded"]);
            return;
        }

    }
    $.ajax({
        type: 'POST',
        url:"/requests.php",
        data: new FormData($(".materials-form")[0]),
        processData: false,
        contentType: false,
        success: r => {
            let resp = JSON.parse(r);
            if(resp.status === "success"){
                loadSubPage($(".materials-form").data("reload-page"));
            }
            immediateSnackbarList(resp.message);
        }
    });
});