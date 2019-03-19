let nonVoteChips = document.querySelector(".non-vote-container");
$("#variable-region").ready(()=>{
    function setupSlip(list) {
        /* Move instantly if the user holds on an element with instant class */
        list.addEventListener('slip:beforewait', e => {
            if (e.target.classList.contains('instant')) e.preventDefault();
        }, false);
        list.addEventListener('slip:afterswipe', e => {
            e.target.classList.add("fear");
            e.target.querySelector("input").setAttribute("name", "removed[]");
            let chip = document.createElement("div");
            chip.classList.add("mdc-chip", "candidate-chip");
            chip.setAttribute("data-candidate-id", e.target.querySelector("input").value);
            let i = document.createElement("i");
            i.classList.add("material-icons", "mdc-chip__icon", "mdc-chip__icon--leading");
            i.innerHTML = "add";
            chip.appendChild(i);
            let ctxt = document.createElement("div");
            ctxt.classList.add("mdc-chip__text");
            ctxt.innerHTML = e.target.querySelector(".candidate-name").innerHTML;
            chip.appendChild(ctxt);
            nonVoteChips.appendChild(chip);
        }, false);
        list.addEventListener('slip:reorder', e => {
            e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
            return false;
        }, false);
        return new Slip(list);
    }
    setupSlip(document.querySelector(".candidate-select"));
});
$(nonVoteChips).on("click", ".candidate-chip", ev => {
    let el = ev.currentTarget;
    let input = $("input[value=\""+ el.getAttribute("data-candidate-id") +"\"]");
    input.parent().removeClass("fear");
    input.attr("name", "vote[]");
    el.remove();
});