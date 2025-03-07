document.addEventListener('DOMContentLoaded', () => {
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');
    const loginContainer = document.getElementById('loginContainer');
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');

    forgotPasswordLink.addEventListener('click', (event) => {
        event.preventDefault();
        loginContainer.style.display = 'none';
        forgotPasswordModal.style.display = 'block';
    });

    window.addEventListener('click', (event) => {
        if (event.target === forgotPasswordModal) {
            forgotPasswordModal.style.display = 'none';
            loginContainer.style.display = 'block';
        }
    });
});
