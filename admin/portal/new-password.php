<?php
session_start();
if (!isset($_SESSION['reset_verified']) || !isset($_SESSION['reset_email'])) {
    header('Location: ?Cavite-State-University=login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password - CvSU</title>
    <link rel="icon" href="./asset/images/res1.png" type="image/x-icon">
</head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap&font-display=swap');

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
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        #ReLoadingOverlay {
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

        .ReLoadingSpinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--login-text-light);
            border-radius: 50%;
            animation: ReSpin 1s linear infinite;
        }

        .ReLoadingContent {
            text-align: center;
        }

        .ReLoadingText {
            margin-top: 15px;
            color: var(--login-text-light);
            font-size: 14px;
            font-weight: 500;
            animation: RePulse 1.5s ease-in-out infinite;
        }

        @keyframes ReSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes RePulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        body {
            background: url('./asset/images/bahay.jpg') no-repeat center center;
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

        .ReContainer {
            width: 100%;
            max-width: 400px;
            background: var(--login-text-light);
            border-radius: 10px;
            padding: 40px;
            box-shadow: var(--login-shadow-md);
        }

        .ReTopTitle {
            font-size: 1.75rem;
            color: var(--login-text-dark);
            margin-bottom: 8px;
            text-align: center;
            font-weight: 600;
        }

        .ReDescription {
            color: #666;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .ReInputGroup {
            position: relative;
            margin-bottom: 20px;
        }

        .ReInputGroup input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--login-border-color);
            border-radius: 6px;
            font-size: 0.95rem;
            transition: var(--login-transition);
        }

        .ReInputGroup input:focus {
            outline: none;
            border-color: var(--login-primary);
            box-shadow: 0 0 0 3px rgba(0, 100, 0, 0.1);
        }

        .ReInputGroup label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            transition: var(--login-transition);
            pointer-events: none;
            font-size: 0.95rem;
        }

        .ReInputGroup input:focus ~ label,
        .ReInputGroup input:not(:placeholder-shown) ~ label {
            top: -10px;
            left: 10px;
            font-size: 0.8rem;
            padding: 0 4px;
            background: var(--login-text-light);
            color: var(--login-primary);
        }

        .ReSubmitButton {
            width: 100%;
            padding: 12px;
            background: var(--login-primary);
            color: var(--login-text-light);
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--login-transition);
            margin-bottom: 15px;
        }

        .ReSubmitButton:hover {
            background: var(--login-secondary);
        }

        .ReBackToLogin {
            display: block;
            text-align: center;
            color: var(--login-primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--login-transition);
            margin-top: 15px;
        }

        .ReBackToLogin:hover {
            color: var(--login-secondary);
        }

        .ReNotification {
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
            animation: ReSlideIn 0.3s ease-out;
            z-index: 1000;
            border-left: 4px solid;
        }

        @keyframes ReSlideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 480px) {
            .ReContainer {
                max-width: 100%;
                padding: 20px;
            }

            .ReNotification {
                left: 20px;
                right: 20px;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
<body>
    <div id="ReLoadingOverlay">
        <div class="ReLoadingContent">
            <div class="ReLoadingSpinner"></div>
            <div class="ReLoadingText">Loading...</div>
        </div>
    </div>

    <div id="ReNotificationContainer"></div>

    <div class="ReContainer">
        <div class="ReTopTitle">Create New Password</div>
        <p class="ReDescription">
            Please enter your new password below.
        </p>
        <form id="ReNewPasswordForm">
            <div class="ReInputGroup">
                <input type="password" id="ReNewPassword" name="password" required placeholder=" " minlength="8">
                <label for="ReNewPassword">New Password</label>
            </div>
            <div class="ReInputGroup">
                <input type="password" id="ReConfirmPassword" name="confirmPassword" required placeholder=" " minlength="8">
                <label for="ReConfirmPassword">Confirm Password</label>
            </div>
            <button type="submit" class="ReSubmitButton">Update Password</button>
            <a href="?Cavite-State-University=login" class="ReBackToLogin">Back to Login</a>
        </form>
    </div>

    <script>
        function showReNotification(message, type = 'error') {
            const container = document.getElementById('ReNotificationContainer');
            const notification = document.createElement('div');
            notification.className = 'ReNotification';
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

        function showReLoading() {
            const overlay = document.getElementById('ReLoadingOverlay');
            overlay.style.display = 'flex';
        }

        function hideReLoading() {
            const overlay = document.getElementById('ReLoadingOverlay');
            overlay.style.display = 'none';
        }

        document.getElementById('ReNewPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('ReNewPassword').value;
            const confirmPassword = document.getElementById('ReConfirmPassword').value;

            if (password !== confirmPassword) {
                showReNotification('Passwords do not match');
                return;
            }

            if (password.length < 8) {
                showReNotification('Password must be at least 8 characters long');
                return;
            }

            showReLoading();
            const formData = new FormData(this);
            
            fetch('admin/portal/update-password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideReLoading();
                if (data.status === 'success') {
                    showReNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || '?Cavite-State-University=login';
                    }, 2000);
                } else {
                    showReNotification(data.message || 'An error occurred');
                }
            })
            .catch(error => {
                hideReLoading();
                showReNotification('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>