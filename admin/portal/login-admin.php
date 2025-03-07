<?php
session_start();
if(isset($_SESSION['admin_id'])) {
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
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
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

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            display: none;
            z-index: 1000;
            animation: slideIn 0.5s ease-in-out;
        }

        .notification.success {
            background-color: var(--primary-color);
        }

        .notification.error {
            background-color: var(--danger-color);
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
    </style>
</head>
<body>

    <div id="notification" class="notification"></div>

    <button class="theme-toggle" aria-label="Toggle theme">
        <i class="fas fa-sun"></i>
    </button>

    <div class="login-container">
        <div class="logo-section">
            <img src="/Alumni-CvSU/asset/images/res1.png" alt="Logo">
            <h2>Welcome Back</h2>
            <p>Please sign in to continue</p>
        </div>

        <div class="login-form-container">
            <form id="loginForm" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                    <div class="error-message">Please enter a valid email address</div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message">Password is required</div>
                </div>

                <div class="form-footer">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="submit-btn">Sign In</button>
            </form>

            <div class="signup-link">
                Don't have an account? <a href="#">Sign up</a>
            </div>
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
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleBtn.className = 'fas fa-eye';
            }
        }

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';

            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
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