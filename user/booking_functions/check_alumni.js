function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function checkAlumniDetails() {
    const alumniId = document.getElementById("alumni-id-field").value;
    const lastName = document.getElementById("alumni-last-name").value;
    const firstName = document.getElementById("alumni-first-name").value;
    const checkDetailsBtn = document.getElementById("check-details-btn");

    checkDetailsBtn.innerHTML = '<div class="spinner"></div> Checking...';
    checkDetailsBtn.disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "user/check_alumni_user.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.exists) {
                showMessage("Alumni details found.", true);

                setCookie("alumniId", alumniId, 1);
                setCookie("lastName", lastName, 1);
                setCookie("firstName", firstName, 1);

                setTimeout(function() {
                    document.getElementById("profile-form-section").style.display = "none";
                    document.getElementById("alumni-form-section").style.display = "block";
                }, 2000);
            } else {
                showMessage("No records found. Please check your Alumni ID card, last name, and first name.", false);
            }
            setTimeout(function() {
                checkDetailsBtn.innerHTML = 'Check Details';
                checkDetailsBtn.disabled = false;
            }, 2000);
        }
    };
    xhr.send("alumni_id=" + alumniId + "&last_name=" + lastName + "&first_name=" + firstName);
}