document.addEventListener('DOMContentLoaded', () => {
    const sendCodeBtn = document.getElementById('sendCodeBtn');
    const verifyCodeBtn = document.getElementById('verifyCodeBtn');
    const resetPasswordBtn = document.getElementById('resetPasswordBtn');
    const forgotResponseMessage = document.getElementById('forgotResponseMessage');
    const codeSection = document.getElementById('codeSection');
    const emailSection = document.getElementById('emailSection');
    const resetSection = document.getElementById('resetSection');
    const backToLoginLink = document.getElementById('backToLoginLink');
    const loginSection = document.getElementById('loginContainer'); 

    function showMessage(message, isSuccess) {
        forgotResponseMessage.textContent = message;
        forgotResponseMessage.style.display = "block";
        forgotResponseMessage.style.color = isSuccess ? "green" : "red";
    }

    function hideResponseMessage() {
        forgotResponseMessage.style.display = "none";
    }

    sendCodeBtn.addEventListener("click", () => {
        const email = document.getElementById("forgotEmail").value;

        if (!email) {
            showMessage("Please enter your email address.", false);
            return;
        }

        hideResponseMessage();
        const requestData = new URLSearchParams({ email: email });

        fetch('forgot_verify.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: requestData
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                showMessage("Verification code sent successfully!", true);
                codeSection.style.display = "block"; 
                emailSection.style.display = "none"; 
                sessionStorage.setItem('verification_code', result.verification_code); 
            } else {
                showMessage(result.message, false);
                codeSection.style.display = "none"; 
            }
        })
        .catch(() => {
            showMessage("Error sending verification code.", false);
            codeSection.style.display = "none"; 
        });
    });

    verifyCodeBtn.addEventListener("click", () => {
        const enteredCode = document.getElementById("verificationCode").value;
        const storedCode = sessionStorage.getItem('verification_code'); 

        if (enteredCode === storedCode) {
            showMessage("Verification successful!", true);

            setTimeout(() => {
                resetSection.style.display = "block"; 
                codeSection.style.display = "none"; 
            }, 3000); 
        } else {
            showMessage("Invalid verification code. Please try again.", false);
        }
    });

resetPasswordBtn.addEventListener("click", () => {
    const newPassword = document.getElementById("newPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (newPassword !== confirmPassword) {
        showMessage("Passwords do not match.", false);
        return;
    }

    const email = document.getElementById("forgotEmail").value; 
    const requestData = new URLSearchParams({ email: email, newPassword: newPassword });

    fetch('forgot_verify.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: requestData
    })
    .then(response => response.json())
    .then(result => {
        showMessage(result.message, result.status === 'success');
        if (result.status === 'success') {
            setTimeout(() => {
                hideForgotPasswordModal();
                window.location.reload();
            }, 3000); 
        }
    })
    .catch(() => {
        showMessage("Error resetting password.", false);
    });
});

    backToLoginLink.addEventListener("click", (e) => {
        e.preventDefault(); 
        document.getElementById('forgotPasswordModal').style.display = "none"; 
        loginSection.style.display = "none"; 
    });
});
