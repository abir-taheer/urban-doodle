$(".end-election").on("click", ev => {
    let token = ev.currentTarget.getAttribute("data-token");
    $.post("/requests.php", {"token":token}, resp => {
        $(ev.currentTarget).fadeOut();
        addSnackbarQueue("This election has successfully been ended. The results are now publicly available on the results page.");
    });
})