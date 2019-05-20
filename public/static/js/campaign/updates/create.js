dependencySetup(() => {
    let editor = document.querySelector("#markdown-editor textarea");
    let txtArea = $(editor);
    let title = $(".update-title").first();
    let titleContent = "<h1>"+ escapeHtml(title.val()) +"</h1>";

    let htmlContent = customMarkDownParser(txtArea.val());
    $("#markdown-editor .markdown-preview").html( titleContent + htmlContent);

    let is_waiting = false;

    $(".markdown-editor-buttons .boldify").on("click", () => {
        let start = editor.selectionStart + 0;
        let val = editor.value;
        let before = val.substring(0, editor.selectionStart);
        let selected = val.substring(editor.selectionStart, editor.selectionEnd);
        let after = val.substring(editor.selectionEnd);
        editor.value = before + "**" + selected + "**" + after;
        editor.selectionStart = start;
        updatePreview();
    });

    $(".markdown-editor-buttons .titlify").on("click", () => {
        let val = editor.value;
        let before = val.substring(0, editor.selectionStart);
        let reversed = before.split("").reverse().join("");
        let current_line = editor.selectionStart - (reversed.indexOf("\n") + 1);

        let addition = "";
        if( val[current_line + 1] === "#" ){
            addition = (val[current_line + 6] === "#") ? "" : "#";
        } else {
            addition  = "# ";
        }

        editor.value = val.substring(0, current_line + 1) + addition + val.substring(current_line + 1);
        updatePreview();
        $(editor).focus();
    });

    $(".markdown-editor-buttons .italify").on("click", () => {
        let val = editor.value;
        let before = val.substring(0, editor.selectionStart);
        let selected = val.substring(editor.selectionStart, editor.selectionEnd);
        let after = val.substring(editor.selectionEnd);
        editor.value = before + "_" + selected + "_" + after;
        updatePreview();
    });

    $(".markdown-editor-buttons .imagify").on("click", () => {
        let val = editor.value;
        let before = val.substring(0, editor.selectionStart);
        let selected = val.substring(editor.selectionStart, editor.selectionEnd);
        selected =  selected === "" ? "Quick Description" : selected;
        let after = val.substring(editor.selectionEnd);
        editor.value = before + "![" + selected + "](Image_Src)" + after;
        updatePreview();
    });

    $(".markdown-editor-buttons .quotify").on("click", () => {
        let val = editor.value;
        let before = val.substring(0, editor.selectionStart);
        let reversed = before.split("").reverse().join("");
        let current_line = editor.selectionStart - (reversed.indexOf("\n") + 1);
        let after = val.substring(current_line + 1);

        let addition = val[current_line + 1] === ">" ? "" : "> ";

        let next_line = after.indexOf("\n");

        if( next_line === -1 ){
            after += "\n";
        } else {
            after = after.substring(0, next_line) + "\n" + after.substring(next_line);
        }

        editor.value = val.substring(0, current_line + 1) + addition + after;
        updatePreview();
        $(editor).focus();
    });

    $(".markdown-editor-buttons .strikify").on("click", () => {
        let val = editor.value;
        let before = val.substring(0, editor.selectionStart);
        let selected = val.substring(editor.selectionStart, editor.selectionEnd);
        let after = val.substring(editor.selectionEnd);
        editor.value = before + "~~" + selected + "~~" + after;
        updatePreview();
    });

    $(".markdown-editor-buttons .colorify").on("click", () => {
        let val = editor.value;
        let before = val.substring(0, editor.selectionStart);
        let selected = val.substring(editor.selectionStart, editor.selectionEnd);
        let after = val.substring(editor.selectionEnd);
        editor.value = before + "{" + selected + "}(rainbow)" + after;
        updatePreview();
    });

    $(".markdown-editor-buttons .clearify").on("click", () => {
        let val = editor.value;
        let before = val.substring(0, editor.selectionStart);
        let selected = val.substring(editor.selectionStart, editor.selectionEnd);
        let after = val.substring(editor.selectionEnd);
        editor.value = before + $(customMarkDownParser(selected)).text() + after;
        updatePreview();
    });
    $(".markdown-editor-buttons .splitify").on("click", () => {
        $("#markdown-editor .mdc-layout-grid__cell--span-12").removeClass("mdc-layout-grid__cell--span-12").addClass("mdc-layout-grid__cell--span-6");
        $("#markdown-editor .splitify").addClass("fear");
        $("#markdown-editor .fullify").removeClass("fear");
        updatePreview();
    });

    $(".markdown-editor-buttons .fullify").on("click", () => {
        $("#markdown-editor .mdc-layout-grid__cell--span-6").removeClass("mdc-layout-grid__cell--span-6").addClass("mdc-layout-grid__cell--span-12");
        $("#markdown-editor .splitify").removeClass("fear");
        $("#markdown-editor .fullify").addClass("fear");
        updatePreview();
    });

    let updatePreview = () => {
        if( is_waiting ){
            return;
        }
        is_waiting = true;
        setTimeout(() => {
            titleContent = "<h1>"+ escapeHtml(title.val()) +"</h1>";
            htmlContent = customMarkDownParser(txtArea.val());
            let preview = document.querySelector("#markdown-editor .markdown-preview");
            let scroll_pos = [preview.scrollLeft, preview.scrollTop];
            $(preview).html(titleContent + htmlContent);
            preview.scrollTo(scroll_pos[0], scroll_pos[1]);
            is_waiting = false;
        }, 500);
    };

    txtArea.on("keyup change input", updatePreview);
    title.on("keyup change input", updatePreview);

    $(".pre-confirm").on("click", ev => {
        ev.currentTarget.setAttribute("disabled", true);
        $(".page-loader").removeClass("mdc-linear-progress--closed");
        setTimeout(() => {
            $(".page-loader").addClass("mdc-linear-progress--closed");
            ev.currentTarget.removeAttribute("disabled");
            $(ev.currentTarget).addClass("fear");
            $(".full-confirm").removeClass("fear");
            $(".cancel-update-confirm").off().on("click", () => {
               $(".full-confirm").addClass("fear");
               $(ev.currentTarget).removeClass("fear");
            });
        }, 1500);
    })
});

