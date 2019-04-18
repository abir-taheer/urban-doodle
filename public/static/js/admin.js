if( typeof menus === 'undefined'){
    var menus = document.querySelectorAll(".make-tab-menu");
} else {
    menus = document.querySelectorAll(".make-tab-menu");
}
for( let x = 0 ; x < menus.length ; x++ ){
    let menu = menus[x];
    let tabs = atob(menu.getAttribute("data-tabs"));
}

$(".mdc-tab").on("click", ev => {

});