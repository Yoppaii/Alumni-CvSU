<?php
require_once 'main_db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();
    
    $response = ['status' => 'error', 'message' => ''];

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare('SELECT id, username, email, password, first_login, two_factor_auth FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $dbEmail, $hashed_password, $first_login, $two_factor_auth);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $session_token = bin2hex(random_bytes(32));

        $update_stmt = $mysqli->prepare("UPDATE users SET session_token = ? WHERE id = ?");
        $update_stmt->bind_param("si", $session_token, $id);
        $update_stmt->execute();
        $update_stmt->close();

        $_SESSION['session_token'] = $session_token;
        $_SESSION['user_id'] = $id;
        $_SESSION['user_email'] = $dbEmail;
        $_SESSION['username'] = $username;

        if ($two_factor_auth == 1) {
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $check_device = $mysqli->prepare('SELECT id FROM device_history WHERE user_id = ? AND ip_address = ?');
            $check_device->bind_param('is', $id, $ip_address);
            $check_device->execute();
            $check_device->store_result();

            if ($check_device->num_rows > 0) {
                $response['status'] = 'success';
                $response['redirect'] = '/Alumni-CvSU/Account';
                $response['message'] = 'Login successful!';
            } else {
                $otp = sprintf("%06d", mt_rand(100000, 999999));
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_time'] = time();
                
                $internal_include = true;
                require_once '2-factor-send.php';
                
                $response['status'] = 'success';
                $response['redirect'] = '2-factor-authentication-verify.php';
                $response['message'] = 'Please check your email for verification code';
            }
            $check_device->close();
        } else {
            $response['status'] = 'success';
            $response['redirect'] = '../../../Alumni-CvSU/Account';
            $response['message'] = 'Login successful!';
        }
    } else {
        $response['message'] = 'Invalid email or password.';
    }

    $stmt->close();
    
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CvSU</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

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



                /* Update these loading overlay styles in your CSS */
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

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
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

        .login-container {
            width: 100%;
            max-width: 800px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--login-text-light);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--login-shadow-md);
        }

        .login-branding {
            background: white;
            padding: 0;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .brand-logo {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(1.2);
        }

        .login-branding::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 100, 0, 0.4);
            z-index: 2;
        }

        .brand-decoration {
            width: 50px;
            height: 3px;
            background: var(--login-text-light);
            margin-top: 15px;
        }

        .login-form-container {
            padding: 40px;
            background: var(--login-text-light);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-size: 1.75rem;
            color: var(--login-text-dark);
            margin-bottom: 8px;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .google-login {
            width: 100%;
            padding: 10px;
            background: var(--login-text-light);
            border: 1px solid var(--login-border-color);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            margin-bottom: 15px;
            transition: var(--login-transition);
            font-size: 0.9rem;
        }

        .google-login:hover {
            background: var(--login-gray-light);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 15px 0;
            gap: 12px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: var(--login-border-color);
        }

        .divider-text {
            color: #666;
            font-size: 0.85rem;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            color: var(--login-text-dark);
            font-size: 0.85rem;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--login-border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: var(--login-transition);
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--login-primary);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
        }

        .remember-me input[type="checkbox"] {
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        .forgot-password {
            color: var(--login-primary);
            text-decoration: none;
            font-size: 0.85rem;
            transition: var(--login-transition);
        }

        .forgot-password:hover {
            color: var(--login-secondary);
        }

        .login-button {
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
        }

        .login-button:hover {
            background: var(--login-secondary);
        }

        .signup-link {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 0.85rem;
        }

        .signup-link a {
            color: var(--login-primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--login-transition);
        }

        .signup-link a:hover {
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

        @media (max-width: 768px) {
            .login-container {
                max-width: 400px;
                grid-template-columns: 1fr;
            }

            .login-branding {
                display: none;
            }

            .login-form-container {
                padding: 30px;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
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
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Loading...</div>
        </div>
    </div>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-branding">
            <div class="brand-logo">
                <img src="asset/images/res1.png" alt="Logo" class="logo-image">
            </div>
        </div>

        <div class="login-form-container">
            <div class="login-header">
                <h1>Welcome Back!</h1>
                <p>Sign in to continue to CvSU</p>
            </div>

            <button class="google-login">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Login with Google
            </button>

            <div class="divider">
                <div class="divider-line"></div>
                <div class="divider-text">or</div>
                <div class="divider-line"></div>
            </div>

            <form>
                <div class="input-group">
                    <label for="loginEmail">Email address</label>
                    <input type="email" id="loginEmail" name="email" required>
                </div>

                <div class="input-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" required>
                </div>

                <div class="form-footer">
                    <label class="remember-me">
                        <input type="checkbox">
                        <span>Remember me</span>
                    </label>
                    <a href="?Cavite-State-University=reset-password" class="forgot-password">Forgot Password?</a>
                </div>

                <button type="submit" class="login-button">Sign In</button>

                <div class="signup-link">
                    Don't have an account? <a href="?Cavite-State-University=register">Sign up for free</a>
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
            overlay.classList.add('loading-overlay-show');
            overlay.classList.remove('loading-overlay-hide');
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('loading-overlay-hide');
            overlay.classList.remove('loading-overlay-show');
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300);
        }

        // Event Listeners
        document.querySelector('.forgot-password').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading();
            setTimeout(() => {
                window.location.href = this.href;
            }, 500);
        });

        document.querySelector('.signup-link a').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading();
            setTimeout(() => {
                window.location.href = this.href;
            }, 500);
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();

            const formData = new FormData(this);
            
            fetch('?Cavite-State-University=login', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    hideLoading();
                    showNotification(data.message, 'error');
                } else if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                }
            })
            .catch(error => {
                hideLoading();
                showNotification('An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            });
        });

        document.querySelector('.google-login').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading();
            window.location.href = 'auth-google/google_login.php';
        });

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('error') === 'google_auth_failed') {
            showNotification('Google authentication failed. Please try again.', 'error');
        }
    </script>
</body>
</html>