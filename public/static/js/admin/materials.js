$(".select-approval-type").on("change", ev => {
    let approve = ev.currentTarget.value === "approve";
    if( !approve ){
        $(".denial-form").removeClass("fear");
    } else {
        $(".denial-form").addClass("fear");

    }
});