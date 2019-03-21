if (typeof nonVoteChips === 'undefined') {
    var nonVoteChips = document.querySelector(".non-vote-container");
} else {
    nonVoteChips = document.querySelector(".non-vote-container");
}
if (typeof candidateSelect === 'undefined') {
    var candidateSelect = document.querySelector(".candidate-select");
} else {
    candidateSelect = document.querySelector(".candidate-select");
}

function setupSlip(list) {
    /* Move instantly if the user holds on an element with instant class */
    list.addEventListener("slip:beforewait", e => {
        if (e.target.classList.contains("drag-icon")) e.preventDefault();
    }, false);
    list.addEventListener("slip:afterswipe", e => {
        removeCandidate(e.target);
    }, false);
    list.addEventListener("slip:reorder", e => {
        e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
        return false;
    }, false);
    return new Slip(list);
}
$(nonVoteChips).off();
$(nonVoteChips).on("click", ".candidate-chip", ev => {
    let el = ev.currentTarget;
    let input = $("input[value=\""+ el.getAttribute("data-candidate-id") +"\"]");
    input.parent().removeClass("fear");
    input.attr("name", "vote[]");
    el.remove();
});

$(candidateSelect).off();
$(candidateSelect).on("click", ".candidate-remove", ev =>{
    removeCandidate(ev.currentTarget.parentElement.parentElement.parentElement);
});
function removeCandidate(el) {
    el.classList.add("fear");
    el.querySelector("input").setAttribute("name", "removed[]");
    let chip = document.createElement("div");
    chip.classList.add("mdc-chip", "candidate-chip");
    chip.setAttribute("data-candidate-id", el.querySelector("input").value);
    let i = document.createElement("i");
    i.classList.add("material-icons", "mdc-chip__icon", "mdc-chip__icon--leading");
    i.innerHTML = "add";
    chip.appendChild(i);
    let ctxt = document.createElement("div");
    ctxt.classList.add("mdc-chip__text");
    ctxt.innerHTML = el.querySelector(".candidate-name").innerHTML;
    chip.appendChild(ctxt);
    nonVoteChips.appendChild(chip);
}
$(".non-vote-container").on("DOMSubtreeModified", e => {
    let i = $(".not-vote-txt");
    (e.currentTarget.innerHTML === "") ? i.addClass("fear") : i.removeClass("fear");
});
$("#variable-region").ready(()=>{
    try {
        setupSlip(candidateSelect);
    } catch (e) {
        console.log("Error with setting up runoff elections: \n" + e + "\nTrying again in one second");
        setTimeout(()=>{
            $(candidateSelect).replaceWith($(candidateSelect).clone());
            setupSlip(candidateSelect);
        }, 1000);
    }
});