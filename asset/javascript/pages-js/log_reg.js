 const loginButtons = document.querySelectorAll(".openLogin");
 loginButtons.forEach(button => {
     button.addEventListener("click", function() {
         document.getElementById("loginOverlay").style.display = 'flex';
     });
 });

 document.getElementById("closeLogin").addEventListener("click", function() {
     document.getElementById("loginOverlay").style.display = 'none';
 });

 const registerButtons = document.querySelectorAll(".openRegister");
 registerButtons.forEach(button => {
     button.addEventListener("click", function() {
         document.getElementById("registerOverlay").style.display = 'flex';
     });
 });

 document.getElementById("closeRegister").addEventListener("click", function() {
     document.getElementById("registerOverlay").style.display = 'none'; 
 });        