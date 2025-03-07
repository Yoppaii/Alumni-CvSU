<?php
//login.php
require_once 'main_db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();
    
    $response = ['status' => 'error', 'message' => ''];

    $email = $_POST['email'];
    $password = $_POST['password'];
    $current_ip = $_SERVER['REMOTE_ADDR'];

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = get_browser_name($user_agent);
    $operating_system = get_os($user_agent);
    $device_type = is_mobile($user_agent) ? 'Mobile Device' : 'Desktop Computer';

    $stmt = $mysqli->prepare('SELECT id, username, email, password, two_factor_auth FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $dbEmail, $hashed_password, $two_factor_auth);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {

        $ip_check = $mysqli->prepare('SELECT id FROM device_history WHERE user_id = ? AND ip_address = ? AND last_active >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
        $ip_check->bind_param('is', $id, $current_ip);
        $ip_check->execute();
        $ip_check->store_result();
        
        $session_token = bin2hex(random_bytes(32));

        $update_stmt = $mysqli->prepare("UPDATE users SET session_token = ? WHERE id = ?");
        $update_stmt->bind_param("si", $session_token, $id);
        $update_stmt->execute();
        $update_stmt->close();

        $_SESSION['session_token'] = $session_token;
        $_SESSION['user_id'] = $id;
        $_SESSION['user_email'] = $dbEmail;
        $_SESSION['username'] = $username;

        if ($two_factor_auth == 1 && $ip_check->num_rows == 0) {
            $otp = sprintf("%06d", random_int(0, 999999));
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_time'] = time();

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'roomreservation.csumc@gmail.com';
                $mail->Password = 'bpqazltzfyacofjd'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('roomreservation.csumc@gmail.com', 'Alumni CvSU');
                $mail->addAddress($dbEmail);

                $mail->isHTML(true);
                $mail->Subject = 'Two-Factor Authentication Code - New Login Attempt';

                $login_time = date('F j, Y \a\t g:i a');

                $message = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Two-Factor Authentication</title>
                </head>
                <body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
                    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                        <div style="background: #006400; color: white; padding: 20px; text-align: center;">
                            <h2 style="margin: 0;">Two-Factor Authentication Code</h2>
                        </div>
                        
                        <div style="padding: 20px;">
                            <div style="background: #e8f0fe; padding: 20px; border-radius: 8px;">
                                <h2 style="text-align: center; color: #006400; margin-top: 0;">Hello, ' . htmlspecialchars($username) . '!</h2>
                                <p style="text-align: center;">A login attempt was detected from a new location. Here is your verification code to complete the login process:</p>
                                
                                <div style="font-size: 32px; letter-spacing: 5px; background: white; padding: 15px; margin: 20px 0; border-radius: 4px; font-weight: bold; color: #006400; text-align: center;">
                                    ' . $otp . '
                                </div>
                                
                                <p style="text-align: center;"><strong>This code will expire in 10 minutes.</strong></p>

                                <div style="background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 4px;">
                                    <h3 style="color: #006400; margin-top: 0;">Login Details:</h3>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        <li style="margin-bottom: 10px;"><strong>Time:</strong> ' . $login_time . '</li>
                                        <li style="margin-bottom: 10px;"><strong>IP Address:</strong> ' . $current_ip . '</li>
                                        <li style="margin-bottom: 10px;"><strong>Device:</strong> ' . $device_type . '</li>
                                        <li style="margin-bottom: 10px;"><strong>Browser:</strong> ' . $browser . '</li>
                                        <li style="margin-bottom: 10px;"><strong>Operating System:</strong> ' . $operating_system . '</li>
                                    </ul>
                                </div>
                                
                                <div style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 10px; border-radius: 4px; margin: 15px 0;">
                                    <strong>Security Notice:</strong> If you did not attempt this login, please:
                                    <ol style="margin: 10px 0 0 20px;">
                                        <li>Do not share this code with anyone</li>
                                        <li>Change your password immediately</li>
                                        <li>Contact our support team</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                            <p style="margin: 5px 0;">This is an automated message, please do not reply to this email.</p>
                            <p style="margin: 5px 0;">CvSU Alumni Portal - Security Notice</p>
                            <p style="margin: 5px 0;">Time sent: ' . date('Y-m-d H:i:s') . '</p>
                        </div>
                    </div>
                </body>
                </html>';

                $mail->Body = $message;
                $mail->send();

                $response['status'] = 'success';
                $response['redirect'] = '?Cavite-State-University=verify-step';
                $response['message'] = 'Please verify your identity. Check your email for the verification code.';
            } catch (Exception $e) {
                $response['status'] = 'error';
                $response['message'] = 'Could not send verification code. Please try again.';
            }
        } else {
            
            $response['status'] = 'success';
            $response['redirect'] = '../../../Alumni-CvSU/Account';
            $response['message'] = 'Login successful!';
        }
        
        $ip_check->close();
    } else {
        $response['message'] = 'Invalid email or password.';
    }

    $stmt->close();
    
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function get_browser_name($user_agent) {
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    return 'Unknown';
}

function get_os($user_agent) {
    $os_platform = "Unknown";
    $os_array = array(
        '/windows nt 10/i'      =>  'Windows 10',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'             =>  'Windows 98',
        '/win95/i'             =>  'Windows 95',
        '/win16/i'             =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'       =>  'Mac OS 9',
        '/linux/i'             =>  'Linux',
        '/ubuntu/i'            =>  'Ubuntu',
        '/iphone/i'            =>  'iPhone',
        '/ipod/i'              =>  'iPod',
        '/ipad/i'              =>  'iPad',
        '/android/i'           =>  'Android',
        '/blackberry/i'        =>  'BlackBerry',
        '/webos/i'             =>  'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }
    return $os_platform;
}

function is_mobile($user_agent) {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $user_agent);
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
            max-width: 400px;
            background: var(--login-text-light);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--login-shadow-md);
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
        .login-header img {
            height: 30px;
            width: auto;
            vertical-align: middle;
            margin-left: 5px;
        }
        
        .login-header p {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
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

    <div class="login-container">
        <div class="login-form-container">
            <div class="login-header">
                <h1>Welcome Back!</h1>
                <p>Sign in to continue to <img src="asset/images/res1.png" alt="Logo"></p>
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

        function saveCredentials(email, password, remember) {
            if (remember) {

                const encryptedPass = btoa(password); 
                localStorage.setItem('rememberedEmail', email);
                localStorage.setItem('rememberedPass', encryptedPass);
                localStorage.setItem('rememberedLogin', 'true');
            } else {

                localStorage.removeItem('rememberedEmail');
                localStorage.removeItem('rememberedPass');
                localStorage.removeItem('rememberedLogin');
            }
        }

        function loadSavedCredentials() {
            const rememberedLogin = localStorage.getItem('rememberedLogin');
            if (rememberedLogin === 'true') {
                const email = localStorage.getItem('rememberedEmail');
                const encryptedPass = localStorage.getItem('rememberedPass');
                if (email && encryptedPass) {

                    const password = atob(encryptedPass); 
                    document.getElementById('loginEmail').value = email;
                    document.getElementById('loginPassword').value = password;
                    document.querySelector('input[type="checkbox"]').checked = true;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', loadSavedCredentials);
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();

            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const remember = document.querySelector('input[type="checkbox"]').checked;

            saveCredentials(email, password, remember);

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

        function handleLogout() {
            const remember = localStorage.getItem('rememberedLogin');
            if (!remember || remember !== 'true') {
                localStorage.removeItem('rememberedEmail');
                localStorage.removeItem('rememberedPass');
                localStorage.removeItem('rememberedLogin');
            }
        }
    </script>
</body>
</html>