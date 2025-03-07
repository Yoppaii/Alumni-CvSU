<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CvSU</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
    :root {
        --login-primary: #006400;
        --login-secondary: #008000;
        --login-accent: #90EE90;
        --login-text-light: #ffffff;
        --login-text-dark: #333333;
        --login-gray-light: #f5f5f5;
        --login-border-color: rgba(0, 0, 0, 0.1);
        --login-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
        --login-shadow-md: 0 2px 5px rgba(0, 0, 0, 0.1);
        --login-transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top: 4px solid var(--login-text-light);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .loading-content {
        text-align: center;
    }

    .loading-text {
        margin-top: 15px;
        color: var(--login-text-light);
        font-size: 14px;
        font-weight: 500;
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-overlay-show {
        animation: fadeIn 0.3s ease-in-out forwards;
    }

    .loading-overlay-hide {
        animation: fadeOut 0.3s ease-in-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }

    body {
        background: url('asset/images/bahay.jpg') no-repeat center center;
        background-size: cover;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        position: relative;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        filter: blur(15px);
        z-index: -1;
    }

    .register-container {
        width: 100%;
        max-width: 400px;
        background: var(--login-text-light);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--login-shadow-md);
    }

    .register-form-container {
        padding: 40px;
        background: var(--login-text-light);
    }

    .register-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .register-header h1 {
        font-size: 1.75rem;
        color: var(--login-text-dark);
        margin-bottom: 8px;
    }

    .register-header p {
        color: #666;
        font-size: 0.9rem;
    }

    .input-group {
        margin-bottom: 15px;
        position: relative;
    }

    .input-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--login-text-dark);
        font-size: 0.85rem;
    }

    .input-group input {
        width: 100%;
        padding: 10px 10px 10px 35px;
        border: 1px solid var(--login-border-color);
        border-radius: 6px;
        font-size: 0.9rem;
        transition: var(--login-transition);
    }

    .input-group i {
        position: absolute;
        left: 12px;
        top: 33px;
        color: #666;
        transition: var(--login-transition);
    }

    .input-group input:focus {
        outline: none;
        border-color: var(--login-primary);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .input-group input:focus + i {
        color: var(--login-primary);
    }

    .register-button {
        width: 100%;
        padding: 10px;
        background: var(--login-primary);
        color: var(--login-text-light);
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--login-transition);
        margin-top: 15px;
    }

    .register-button:hover {
        background: var(--login-secondary);
    }

    .login-link {
        text-align: center;
        margin-top: 15px;
        color: #666;
        font-size: 0.85rem;
    }

    .login-link a {
        color: var(--login-primary);
        text-decoration: none;
        font-weight: 500;
        transition: var(--login-transition);
    }

    .login-link a:hover {
        color: var(--login-secondary);
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: var(--login-text-light);
        border-radius: 6px;
        box-shadow: var(--login-shadow-sm);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        animation: slideIn 0.3s ease-out;
        z-index: 1000;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @media (max-width: 480px) {
        .register-container {
            max-width: 100%;
        }

        .register-form-container {
            padding: 20px;
        }

        .notification {
            left: 20px;
            right: 20px;
            text-align: center;
            justify-content: center;
        }
    }
            .legal-links {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--login-border-color);
            color: #666;
            font-size: 0.75rem;
            line-height: 1.4;
        }

        .legal-links a {
            color: var(--login-primary);
            text-decoration: none;
            transition: var(--login-transition);
        }

        .legal-links a:hover {
            color: var(--login-secondary);
            text-decoration: underline;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 480px) {
            .login-container {
                max-width: 100%;
            }

            .login-form-container {
                padding: 20px;
            }

            .notification {
                left: 20px;
                right: 20px;
                text-align: center;
                justify-content: center;
            }

            .legal-links {
                font-size: 0.7rem;
                padding: 0 10px;
            }
        }
</style>
</head>
<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Loading...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="register-container">
        <div class="register-form-container">
            <div class="register-header">
                <h1>Create Account</h1>
                <p>Join CvSU today</p>
            </div>

            <form id="registerForm" action="Sending-Code.php" method="POST">
                <div class="input-group">
                    <label for="register-username">Username</label>
                    <input type="text" id="register-username" name="username" required>
                    <i class="fas fa-user"></i>
                </div>

                <div class="input-group">
                    <label for="register-email">Email address</label>
                    <input type="email" id="register-email" name="email" required>
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="input-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password" required>
                    <i class="fas fa-lock"></i>
                </div>

                <div class="input-group">
                    <label for="register-confirmPassword">Confirm Password</label>
                    <input type="password" id="register-confirmPassword" name="confirmPassword" required>
                    <i class="fas fa-lock"></i>
                </div>

                <button type="submit" class="register-button">Create Account</button>

                <div class="login-link">
                    Already have an account? <a href="?Cavite-State-University=login">Sign in</a>
                </div>

                <div class="legal-links">
                    By signing in or creating an account, you agree to our
                    <a href="?Cavite-State-University=terms-and-conditions">Terms & Conditions</a> and
                    <a href="?Cavite-State-University=privacy-policy">Privacy Policy</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showNotification(message, type = 'error') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = message;
            notification.style.borderLeft = `4px solid ${type === 'success' ? '#388e3c' : '#ff4444'}`;

            container.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            }, 5000);
        }

        function showLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'none';
        }

        function validateForm() {
            let isValid = true;
            const username = document.getElementById('register-username');
            const email = document.getElementById('register-email');
            const password = document.getElementById('register-password');
            const confirmPassword = document.getElementById('register-confirmPassword');

            if (username.value.length < 3) {
                showNotification('Username must be at least 3 characters long');
                isValid = false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                showNotification('Please enter a valid email address');
                isValid = false;
            }

            if (password.value.length < 8) {
                showNotification('Password must be at least 8 characters long');
                isValid = false;
            }

            if (password.value !== confirmPassword.value) {
                showNotification('Passwords do not match');
                isValid = false;
            }

            return isValid;
        }

        document.querySelector('.login-link a').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading();
            setTimeout(() => {
                window.location.href = this.href;
            }, 500);
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                showLoading();
                
                const formData = new FormData(this);
                
                fetch('/Alumni-CvSU/admin/Sending-Code.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    
                    if (data.status === 'error') {
                        showNotification(data.message);
                    } else if (data.status === 'success') {
                        showNotification('Successfully send an OTP...', 'success');
                        setTimeout(() => {
                            window.location.href = '?Cavite-State-University=verify';
                        }, 1500);
                    }
                })
                .catch(error => {
                    hideLoading();
                    showNotification('An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            }
        });
    </script>
</body>
</html>