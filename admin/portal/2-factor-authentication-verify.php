<?php
require_once 'main_db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_email'])) {
    header('Location: ?Cavite-State-University=login');
    exit;
}
$userEmail = htmlspecialchars($_SESSION['user_email']);

function getDeviceInfo() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = 'Unknown';
    $os = 'Unknown';
    $device_type = 'Desktop';

    if (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Edge';
    } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
        $browser = 'Opera';
    }

    if (preg_match('/Windows/i', $user_agent)) {
        $os = 'Windows';
    } elseif (preg_match('/Mac/i', $user_agent)) {
        $os = 'MacOS';
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $os = 'Linux';
    } elseif (preg_match('/Android/i', $user_agent)) {
        $os = 'Android';
        $device_type = 'Mobile';
    } elseif (preg_match('/iOS|iPhone|iPad|iPod/i', $user_agent)) {
        $os = 'iOS';
        $device_type = 'Mobile';
    }
    
    if (preg_match('/tablet|ipad/i', $user_agent)) {
        $device_type = 'Tablet';
    }
    
    return array(
        'device_type' => $device_type,
        'operating_system' => $os,
        'browser' => $browser
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => ''];
    
    $submitted_otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_time']) || !isset($_SESSION['user_id'])) {
        $response['message'] = 'OTP session has expired. Please request a new code.';
        echo json_encode($response);
        exit;
    }
    
    $time_elapsed = time() - $_SESSION['otp_time'];
    if ($time_elapsed > 600) {
        unset($_SESSION['otp']);
        unset($_SESSION['otp_time']);
        
        $response['message'] = 'OTP has expired. Please request a new code.';
        echo json_encode($response);
        exit;
    }
    
    if ($submitted_otp === $_SESSION['otp']) {
        $device_info = getDeviceInfo();
        $user_id = $_SESSION['user_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $current_time = date('Y-m-d H:i:s');

        $stmt = $mysqli->prepare("
            INSERT INTO device_history 
                (user_id, device_type, operating_system, browser, ip_address, last_active, created_at)
            VALUES 
                (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param('issssss', 
            $user_id,
            $device_info['device_type'],
            $device_info['operating_system'],
            $device_info['browser'],
            $ip_address,
            $current_time,
            $current_time
        );
        
        if ($stmt->execute()) {
            unset($_SESSION['otp']);
            unset($_SESSION['otp_time']);
            
            $response['status'] = 'success';
            $response['message'] = 'Verification successful!';
            $response['redirect'] = '../../../Alumni-CvSU/Account';
        } else {
            $response['message'] = 'Error saving device information. Please try again.';
        }
        
        $stmt->close();
    } else {
        $response['message'] = 'Invalid verification code. Please try again.';
    }
    
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2-Step Verification</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --tfa-primary-color: #388e3c;
            --tfa-primary-light: #ebf3eb;
            --tfa-text-dark: #333;
            --tfa-text-gray: #666;
            --tfa-border-color: #ddd;
            --tfa-bg-light: #f5f5f5;
            --tfa-bg-white: white;
            --tfa-shadow-color: rgba(0, 0, 0, 0.1);
            --tfa-overlay-color: rgba(0, 0, 0, 0.6);
            --tfa-font-family: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--tfa-font-family);
        }

        .tfa-body {
            background: url('asset/images/bahay.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(10px, 3vw, 20px);
            position: relative;
        }

        .tfa-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--tfa-overlay-color);
            z-index: -1;
        }

        .tfa-verification-modal {
            background: var(--tfa-bg-white);
            border-radius: clamp(8px, 2vw, 10px);
            padding: clamp(20px, 4vw, 32px);
            width: min(90%, 480px);
            text-align: center;
            box-shadow: 0 20px 40px var(--tfa-shadow-color);
            animation: tfaModalFade 0.3s ease-in-out;
        }

        .tfa-step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(4px, 1vw, 8px);
            margin-bottom: clamp(16px, 3vw, 24px);
        }

        .tfa-step {
            display: flex;
            align-items: center;
            gap: clamp(4px, 1vw, 8px);
            color: var(--tfa-text-gray);
            font-size: clamp(12px, 2.5vw, 14px);
            font-weight: 500;
        }

        .tfa-step.tfa-active {
            color: var(--tfa-primary-color);
        }

        .tfa-step-divider {
            width: clamp(16px, 3vw, 24px);
            height: 1px;
            background: var(--tfa-border-color);
            margin: 0 clamp(2px, 0.5vw, 4px);
        }

        .tfa-step-number {
            width: clamp(20px, 4vw, 24px);
            height: clamp(20px, 4vw, 24px);
            border-radius: 50%;
            background: var(--tfa-bg-light);
            border: 1px solid var(--tfa-border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(10px, 2vw, 12px);
        }

        .tfa-step.tfa-active .tfa-step-number {
            background: var(--tfa-primary-light);
            border-color: var(--tfa-primary-color);
            color: var(--tfa-primary-color);
        }

        .tfa-step.tfa-completed .tfa-step-number {
            background: var(--tfa-primary-color);
            border-color: var(--tfa-primary-color);
            color: var(--tfa-bg-white);
        }

        .tfa-modal-icon {
            width: clamp(48px, 8vw, 64px);
            height: clamp(48px, 8vw, 64px);
            background: var(--tfa-primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto clamp(16px, 3vw, 24px);
        }

        .tfa-modal-icon i {
            color: var(--tfa-primary-color);
            font-size: clamp(24px, 5vw, 32px);
        }

        .tfa-modal-title {
            font-size: clamp(20px, 4vw, 24px);
            color: var(--tfa-text-dark);
            margin-bottom: clamp(12px, 2vw, 16px);
            font-weight: 600;
        }

        .tfa-modal-description {
            color: var(--tfa-text-gray);
            font-size: clamp(13px, 2.5vw, 15px);
            margin-bottom: clamp(16px, 3vw, 24px);
            line-height: 1.6;
        }

        .tfa-email-display {
            background: var(--tfa-bg-light);
            padding: clamp(12px, 2vw, 16px);
            border-radius: clamp(6px, 1.5vw, 8px);
            margin-bottom: clamp(16px, 3vw, 24px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(8px, 1.5vw, 12px);
            font-size: clamp(13px, 2.5vw, 15px);
            color: var(--tfa-text-dark);
            border: 1px solid var(--tfa-border-color);
            word-break: break-all;
        }

        .tfa-input-group {
            margin-bottom: clamp(16px, 3vw, 24px);
            position: relative;
        }

        .tfa-input-field {
            width: 100%;
            padding: clamp(12px, 2vw, 14px);
            border: 1px solid var(--tfa-border-color);
            border-radius: clamp(6px, 1.5vw, 8px);
            font-size: clamp(13px, 2.5vw, 15px);
            transition: all 0.3s;
            text-align: center;
            letter-spacing: 2px;
        }

        .tfa-input-field:focus {
            outline: none;
            border-color: var(--tfa-primary-color);
            box-shadow: 0 0 0 3px rgba(56, 142, 60, 0.1);
        }

        .tfa-button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: clamp(12px, 2vw, 16px);
            margin-top: clamp(16px, 3vw, 24px);
        }

        .tfa-try-another {
            color: var(--tfa-primary-color);
            text-decoration: none;
            font-size: clamp(12px, 2.5vw, 14px);
            font-weight: 500;
            padding: clamp(6px, 1.5vw, 8px) clamp(12px, 2vw, 16px);
            border-radius: clamp(6px, 1.5vw, 8px);
            transition: background-color 0.3s;
            white-space: nowrap;
        }

        .tfa-try-another:hover {
            background: var(--tfa-primary-light);
        }

        .tfa-next-button {
            background: var(--tfa-primary-color);
            color: var(--tfa-bg-white);
            border: none;
            padding: clamp(10px, 2vw, 12px) clamp(20px, 4vw, 28px);
            border-radius: clamp(6px, 1.5vw, 8px);
            font-size: clamp(12px, 2.5vw, 14px);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tfa-next-button:hover {
            background: #2e7d32;
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
            border-top: 4px solid var(--tfa-bg-white);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-content {
            text-align: center;
        }

        .loading-text {
            margin-top: 15px;
            color: var(--tfa-bg-white);
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

        #notificationContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .notification {
            padding: 12px 20px;
            background: var(--tfa-bg-white);
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            animation: slideIn 0.3s ease-out;
            margin-bottom: 10px;
            transition: opacity 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 480px) {
            #notificationContainer {
                left: 20px;
                right: 20px;
            }

            .notification {
                text-align: center;
                justify-content: center;
            }
        }

        @keyframes tfaModalFade {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .tfa-button-group {
                flex-direction: column;
            }
            
            .tfa-next-button,
            .tfa-try-another {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .notification {
                width: auto;
                max-width: calc(100% - 40px);
                margin-right: 10px;
            }
        }

        @media (max-width: 360px) {
            .tfa-modal-title {
                font-size: clamp(18px, 5vw, 20px);
            }
            
            .tfa-modal-description,
            .tfa-input-field,
            .tfa-email-display {
                font-size: clamp(12px, 3vw, 14px);
            }
        }

        @media (max-height: 600px) {
            .tfa-verification-modal {
                margin: 20px 0;
            }
            
            .tfa-body {
                align-items: flex-start;
                overflow-y: auto;
                padding-top: max(20px, 5vh);
            }
        }
    </style>
</head>
<body class="tfa-body">

<div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Loading...</div>
        </div>
    </div>

    <div id="notificationContainer"></div>

    <div class="tfa-verification-modal">
        <div class="tfa-step-indicator">
            <div class="tfa-step tfa-completed">
                <div class="tfa-step-number">
                    <i class="fas fa-check"></i>
                </div>
                <span>Sign in</span>
            </div>
            <div class="tfa-step-divider"></div>
            <div class="tfa-step tfa-active">
                <div class="tfa-step-number">2</div>
                <span>Verify</span>
            </div>
        </div>

        <div class="tfa-modal-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h1 class="tfa-modal-title">2-Step Verification</h1>
        <p class="tfa-modal-description">To help keep your account secure, we need to verify your identity. Please enter the verification code sent to your email.</p>
        
        <div class="tfa-email-display">
            <i class="fas fa-envelope"></i>
            <span><?php echo htmlspecialchars($userEmail); ?></span>
        </div>
      
        <div class="tfa-input-group">
            <input type="text" class="tfa-input-field" placeholder="Enter verification code" maxlength="6" />
        </div>
        
        <div class="tfa-button-group">
            <a href="?Cavite-State-University=verify-step-another-options" class="tfa-try-another">
                <i class="fas fa-sync-alt"></i>
                Try another way
            </a>
            <button class="tfa-next-button">
                <i class="fas fa-arrow-right"></i>
                Verify
            </button>
        </div>
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const verifyButton = document.querySelector('.tfa-next-button');
        const otpInput = document.querySelector('.tfa-input-field');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
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

        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        verifyButton.addEventListener('click', function() {
            const otp = otpInput.value.trim();
            
            if (otp.length !== 6) {
                showNotification('Please enter a valid 6-digit code.');
                return;
            }
            
            showLoading();
            
            const formData = new FormData();
            formData.append('otp', otp);
            
            fetch('?Cavite-State-University=verify-step', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    hideLoading();
                    showNotification(data.message);
                    otpInput.value = '';
                    otpInput.focus();
                }
            })
            .catch(error => {
                hideLoading();
                showNotification('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        });

        document.querySelector('.tfa-try-another').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading();
            setTimeout(() => {
                window.location.href = this.href;
            }, 500);
        });
    });
</script>