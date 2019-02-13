$("select[name='grade']").change(function(ev){
    let el = ev.currentTarget;
    let w = $(".faculty-warning");
    (el.value === 'f') ? w.fadeIn() : w.fadeOut();
});