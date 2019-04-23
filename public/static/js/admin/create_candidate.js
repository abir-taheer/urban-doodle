$(".add-editor").on("click", ev => {
    ev.preventDefault();
    let editors = document.querySelectorAll(".editor-container");
    let currentNum = editors.length;
    let newEditor = document.createElement("div");
    newEditor.classList.add("sub-container","editor-container");
    let txt = document.createElement("p");
    txt.innerHTML = "Editor " + (currentNum + 1);
    newEditor.appendChild(txt);
    let txt_field = document.createElement("div");
    txt_field.classList.add("mdc-text-field");

    let input = document.createElement("input");
    input.classList.add("mdc-text-field__input");
    input.setAttribute("name", "editor[" + currentNum + "][email]");
    txt_field.appendChild(input);
    let line_ripple = document.createElement("div");
    line_ripple.classList.add("mdc-line-ripple");
    txt_field.appendChild(line_ripple);

    let label = document.createElement("label");
    label.innerHTML = "Editor's Email";
    label.classList.add("mdc-floating-label");
    txt_field.appendChild(label);

    newEditor.appendChild(txt_field);

    let remove_icon = document.createElement("i");
    remove_icon.classList.add("material-icons", "remove-editor","clickable");
    remove_icon.innerHTML = "clear";

    newEditor.appendChild(remove_icon);
    newEditor.appendChild(document.createElement("br"));

    document.querySelector(".all-editors-container").appendChild(newEditor);

});

$(".all-editors-container").on("click", ".remove-editor", ev => {
    ev.currentTarget.parentElement.remove();
    let editors = document.querySelectorAll(".editor-container");
    for( let x = 0 ; x < editors.length ; x ++  ){
        let i = editors[x];
        $(i).find("p").html("Editor " + (x + 1));
        $(i).find("input").attr("name", "editor[" + x + "][email]");
    }
});