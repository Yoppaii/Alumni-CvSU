   document.getElementById('switchToLogin').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById("registerOverlay").style.display = 'none'; 
    document.getElementById("loginOverlay").style.display = 'flex'; 
});

document.getElementById('switchToRegister').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById("loginOverlay").style.display = 'none'; 
    document.getElementById("registerOverlay").style.display = 'flex'; 
});