$(".select-material-type").on("change", ev => {
    if( ev.currentTarget.value === "poster" ){
       $(".poster-upload").removeClass("fear");
       $(".other-content").addClass("fear");
   } else {
       $(".poster-upload").addClass("fear");
       $(".other-content").removeClass("fear");
   }
});