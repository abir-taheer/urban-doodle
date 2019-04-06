let tabs = document.querySelectorAll(".sub-page-tab");
for(let x = 0; x < tabs.length ; x++ ){
    let i = tabs[x];
    if( window.location.pathname.includes(i.getAttribute("data-page")) ){
        $(".sub-page-tab").removeClass("mdc-tab--active").find(".mdc-tab-indicator").removeClass("mdc-tab-indicator--active");
        i.classList.add("mdc-tab--active");
        i.querySelector(".mdc-tab-indicator").classList.add("mdc-tab-indicator--active");
    }
}