(function (){
    let num_tries = 0;
    let is_waiting = false;
    let retry_setup = setInterval( () => {
        let converter = new showdown.Converter();
        $("#markdown-editor .markdown-preview").html(customMarkDownParser($("#markdown-editor textarea").val()));
        let setupMDE = () => {
            $("#markdown-editor textarea").on("keyup", ev => {
                if( is_waiting ){
                    return;
                }
                is_waiting = true;
                setTimeout(() => {
                    $("#markdown-editor .markdown-preview").html(customMarkDownParser(ev.currentTarget.value));
                    is_waiting = false;
                }, 300);
            });
        };
        try {
            num_tries++;
            setupMDE();
            clearInterval(retry_setup);
        } catch( e ){
            if( num_tries > 20 ){
                alert("One of the required components for this page could not be loaded. Please try again and reload this page.");
                clearInterval(retry_setup);
            }
        }
    }, 500 );
})();
