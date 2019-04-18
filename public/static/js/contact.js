$(".prevent_contact").on("submit", ev => {
   ev.preventDefault();
   $.post("/requests.php", $(ev.currentTarget).serialize(), r => {
      $(ev.currentTarget).html("<p class='txt-ctr'>Your message has been recieved!</p>");
   });
});