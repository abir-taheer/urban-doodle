$('.vote-submit').on("click", ev => {
    let disableButton = () => {
        ev.currentTarget.setAttribute('disabled', true);
    };
    let enableButton = () => {
        ev.currentTarget.removeAttribute('disabled');
    };
    let errors = [];
    let hasAllNone = true;
    disableButton();
    let form = $('.vote-form');
    for(let x = 1; x <= parseInt(form.data('num')); x++){
        let radios = document.querySelectorAll("input[name='votes[" + x + "]']:checked");
        if( radios.length !== 1 ){
            errors.push("You must make choice for each selection! You are free to choose 'No Selection' after voting for at least one candidate.");
            break;
        }
        if( radios[0].value !== 'na' ){
            hasAllNone = false;
        }
    }
    if( hasAllNone ){
        errors.push("You must vote for at least one candidate to submit your form!");
    }

    if( errors.length === 0 ){
        // submit the form
        $.post("/load.php?page=/confirm", form.serialize(), ev => {
           $('#variable-region').html(ev);
            $(".cancel-confirm").off().on("click", () => {
                changePage();
            });
            $(".confirm-votes").off().on("click", () =>{
                $.post("/requests.php", $(".confirm-form").serialize(), r => {
                    let resp = JSON.parse(r);
                    if( resp.status === "success" ){
                        addSnackbarQueue("Your vote has been successfully recorded. Thank you for voting!");
                        changePage();
                    } else {
                        for( let x = 0 ; x < resp.message.length ; x++ ) {
                            addSnackbarQueue(resp.message[x]);
                        }
                        playSnackbarQueue();
                    }

                });
            });
        });
    } else {
        immediateSnackbarList(errors);
        enableButton();
    }
});