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
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #006400;
            --primary-hover: #008000;
            --primary-light: rgba(144, 238, 144, 0.2);
            --secondary-color: #64748b;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --danger-color: #ef4444;
            --radius-md: 0.5rem;
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --transition: all 0.3s ease;
        }

        [data-theme="dark"] {
            --primary-color: #10b981;
            --primary-hover: #059669;
            --primary-light: rgba(16, 185, 129, 0.2);
            --secondary-color: #94a3b8;
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --bg-primary: #000000;
            --bg-secondary: #111111;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section img {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
        }

        .logo-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .logo-section p {
            color: var(--text-secondary);
        }

        .login-form-container {
            background-color: var(--bg-primary);
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.625rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .password-field {
            position: relative;
            display: flex;
        }

        .password-input {
            width: 100%;
            padding: 0.625rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: var(--transition);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.25rem;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .form-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            accent-color: var(--primary-color);
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
        }

        .forgot-password:hover {
            color: var(--primary-hover);
        }

        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .submit-btn:hover {
            background-color: var(--primary-hover);
        }

        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .signup-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .signup-link a:hover {
            color: var(--primary-hover);
        }

        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius-md);
        }

        .theme-toggle:hover {
            color: var(--primary-color);
            background-color: var(--primary-light);
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
            border-radius: 8px;
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
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .notification.error {
            border-left: 4px solid #ef4444;
        }

        .notification.success {
            border-left: 4px solid #10b981;
        }

        .notification.warning {
            border-left: 4px solid #f59e0b;
        }

        .notification.info {
            border-left: 4px solid #3b82f6;
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

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
                height: auto;
                padding-top: 12px;
                /* match your row padding */
                padding-bottom: 12px;
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

        .error-message {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: none;
        }

        .error .error-message {
            display: block;
        }

        .error input {
            border-color: var(--danger-color);
        }

        @media (max-width: 480px) {


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
        <div class="login-form-container">

            <div class="logo-section">
                <img src="/Alumni-CvSU/asset/images/2.png" alt="Logo">
                <h2>Welcome Back</h2>
                <p>Please sign in to continue</p>
            </div>

            <form id="loginForm" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                    <div class="error-message">Please enter a valid email address</div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" required placeholder="Enter your password" class="password-input">
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message">Password is required</div>
                </div>

                <div class="form-footer">
                    <!-- <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div> -->
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="submit-btn">Sign In</button>
            </form>

            <!-- <div class="signup-link">
                Don't have an account? <a href="#">Sign up</a>
            </div> -->
        </div>
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
                this.querySelector('#email').parentElement.classList.add('error');
                return;
            }

            if (!formData.get('password')) {
                this.querySelector('#password').parentElement.classList.add('error');
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