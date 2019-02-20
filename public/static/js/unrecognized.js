$("select[name='grade']").on("change", function(ev){
    let el = ev.currentTarget;
    let fw = $(".faculty-warning");
    (el.value === 'f') ? fw.fadeIn() : fw.fadeOut();
});