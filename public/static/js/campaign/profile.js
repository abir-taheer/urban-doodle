$(".change-candidate-pfp").on("click", () => {
    $("#pfp-upload input[type='file']").click();
});

$("#pfp-upload input[type='file']").on("change", ev => {
    // TODO MAYBE SOME ISSUE HERE / CHECK FOR FILE TYPES
    let element = ev.currentTarget;
    let file = element.files[0];
    let errors = [];

    if( ! file.type.startsWith("image/") ){
        errors.push("The file uploaded is not an image");
    }

    if( file.size > 2000000 ){
        errors.push("That file is too large. The file size limit is 2MB");
    }

    if( errors.length === 0 ){
        let reader = new FileReader();
        reader.onload = e => {
            showConfirmation(document.getElementById("pfp-confirm"), () => {
                $(".candidate-photo").attr("src", reader.result);
                // Send out the updated image to the server
                $.ajax({
                    type: 'POST',
                    url:"/requests.php",
                    data: new FormData($("#pfp-upload")[0]),
                    processData: false,
                    contentType: false,
                    success: r => {
                        console.log(r);
                    }
                });
            });
            $(".pfp-confirm-set-src").attr("src", reader.result);
        };
        reader.readAsDataURL(file);
    } else {
        for( let x = 0 ; x < errors.length ; x++ ){
            addSnackbarQueue(errors[x]);
        }
        playSnackbarQueue();
    }
});