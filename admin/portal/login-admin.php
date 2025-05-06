<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: ../../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Bahay ng Alumni System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #008000;
            --primary-hover: #006400;
            --primary-light: rgba(0, 128, 0, 0.1);
            --secondary-color: #64748b;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --danger-color: #ef4444;
            --radius-sm: 0.25rem;
            --radius-md: 0.375rem;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        [data-theme="dark"] {
            --primary-color: #00c853;
            --primary-hover: #00b34a;
            --primary-light: rgba(0, 200, 83, 0.2);
            --secondary-color: #94a3b8;
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --bg-primary: rgba(18, 18, 18, 0.95);
            --bg-secondary: rgba(30, 30, 30, 0.9);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-image: url('/Alumni-CvSU/asset/images/bground.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-container {
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-img {
            width: 80px;
            height: 80px;
            padding: 0;
            margin-bottom: 0.5rem;
        }

        .admin-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.35rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .system-title {
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0.75rem 0 0.5rem;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .system-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .login-form-container {
            background-color: var(--bg-primary);
            width: 100%;
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            padding: 0.25rem;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .forgot-password-link {
            display: block;
            text-align: right;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            margin: -0.5rem 0 1.5rem;
        }

        .forgot-password-link:hover {
            text-decoration: underline;
            color: var(--primary-hover);
        }

        .signin-btn {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .signin-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .signin-btn:active {
            transform: translateY(0);
        }

        .error-message {
            color: var(--danger-color);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: none;
        }

        .error .error-message {
            display: block;
        }

        .error .form-input {
            border-color: var(--danger-color);
        }

        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #ffffff;
            cursor: pointer;
            padding: 0.75rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Notification styles */
        #notificationContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 400px;
            width: 100%;
        }

        .notification {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: white;
            border-radius: var(--radius-md);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            animation: slideIn 0.3s ease-out forwards;
            min-width: 300px;
            max-width: 400px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
                height: auto;
                padding-top: 16px;
                padding-bottom: 16px;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
                height: 0;
                padding-top: 0;
                padding-bottom: 0;
                margin: 0;
                border: 0;
            }
        }

        .notification.error {
            border-left: 4px solid #ef4444;
        }

        .notification.success {
            border-left: 4px solid #10b981;
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
            color: #64748b;
        }

        .notification-close:hover {
            color: #1e293b;
        }

        .copyright {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            text-align: center;
            margin-top: 1.5rem;
            position: relative;
            z-index: 2;
        }

        @media (max-width: 480px) {
            .login-form-container {
                padding: 1.5rem;
            }

            .system-title {
                font-size: 1.5rem;
            }

            .notification {
                left: 20px;
                right: 20px;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="notification-container" id="notificationContainer"></div>

    <button class="theme-toggle" aria-label="Toggle theme">
        <i class="fas fa-sun"></i>
    </button>

    <div class="login-container">
        <div class="logo-container">
            <img src="/Alumni-CvSU/asset/images/2.png" alt="Logo" class="logo-img">
            <span class="admin-badge">ADMIN PORTAL</span>
        </div>

        <h1 class="system-title">Bahay ng Alumni System</h1>
        <p class="system-subtitle">Enter your credentials to access the dashboard</p>

        <div class="login-form-container">
            <form id="loginForm" method="post">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your admin email">
                        <div class="error-message">Please enter a valid email address</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-input" required placeholder="Enter your password">
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="error-message">Password is required</div>
                    </div>
                </div>

                <a href="#" class="forgot-password-link">Forgot password?</a>

                <button type="submit" class="signin-btn">Sign In</button>
            </form>
        </div>
    </div>

    <div class="copyright">
        &copy; <?php echo date('Y'); ?> Bahay ng Alumni System - CvSU. All rights reserved.
    </div>

    <script>
        const themeToggle = document.querySelector('.theme-toggle');
        const themeIcon = themeToggle.querySelector('i');
        let currentTheme = localStorage.getItem('theme') || 'light';

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            themeIcon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }

        applyTheme(currentTheme);

        themeToggle.addEventListener('click', () => {
            currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', currentTheme);
            applyTheme(currentTheme);
        });

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.password-toggle i');

            // Store the current selection and focus state
            const isFocused = document.activeElement === passwordInput;
            const selectionStart = passwordInput.selectionStart;
            const selectionEnd = passwordInput.selectionEnd;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleBtn.className = 'fas fa-eye';
            }

            // Restore focus and selection if it was focused before
            if (isFocused) {
                passwordInput.focus();
                passwordInput.setSelectionRange(selectionStart, selectionEnd);
            }
        }

        // Add event listener to the password toggle button
        document.querySelector('.password-toggle').addEventListener('click', togglePassword);

        function showNotification(message, type) {
            const notificationContainer = document.getElementById('notificationContainer');

            // Create a new notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;

            // Create the content for the notification
            notification.innerHTML = `
                <div>${message}</div>
                <button class="notification-close">&times;</button>
            `;

            // Add to container
            notificationContainer.appendChild(notification);

            // Add event listener to close button
            notification.querySelector('.notification-close').addEventListener('click', function() {
                notification.style.animation = 'slideOut 0.3s forwards';
                setTimeout(() => {
                    notificationContainer.removeChild(notification);
                }, 300);
            });

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (notification.parentNode === notificationContainer) {
                    notification.style.animation = 'slideOut 0.3s forwards';
                    setTimeout(() => {
                        if (notification.parentNode === notificationContainer) {
                            notificationContainer.removeChild(notification);
                        }
                    }, 300);
                }
            }, 5000);
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            this.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error');
            });

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.get('email'))) {
                this.querySelector('#email').parentElement.parentElement.classList.add('error');
                return;
            }

            if (!formData.get('password')) {
                this.querySelector('#password').parentElement.parentElement.classList.add('error');
                return;
            }

            fetch('admin-function/process_login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '../../Dashboard?section=Dashboard';
                        }, 1500);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred. Please try again.', 'error');
                });
        });
    </script>
</body>

</html>