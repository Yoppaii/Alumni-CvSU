<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'main_db.php';

$currentRecoveryEmail = '';
$hasEmail = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("SELECT recovery_email FROM recovery_emails WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $currentRecoveryEmail = $row['recovery_email'];
        $hasEmail = true;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recovery Email</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2d6936;
            --text-gray: #6b7280;
            --text-dark: #111827;
            --background-color: #f4f6f8;
            --border-color: #e5e7eb;
            --button-gray: #f3f4f6;
        }

        body {
            background-color: var(--background-color);
            min-height: 100vh;
            padding: 20px;
        }

        .rec-page-layout {
            max-width: auto;
            margin: 0 auto;
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .rec-container {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 0;
        }

        .rec-info-container {
            width: 380px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }

        .rec-info-title {
            font-size: 18px;
            color: var(--text-dark);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rec-info-title i {
            color: var(--primary-color);
        }

        .rec-info-list {
            list-style: none;
        }

        .rec-info-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 16px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .rec-info-item:last-child {
            border-bottom: none;
        }

        .rec-info-icon-wrapper {
            width: 32px;
            height: 32px;
            background-color: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rec-info-icon-wrapper i {
            color: var(--primary-color);
            font-size: 14px;
        }

        .rec-info-text-wrapper {
            flex: 1;
            font-size: 14px;
            color: var(--text-gray);
            line-height: 1.5;
        }

        .rec-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .rec-back-button {
            background: none;
            border: none;
            cursor: pointer;
            color: #374151;
            display: flex;
            align-items: center;
            font-size: 20px;
        }

        .rec-header h1 {
            font-size: 20px;
            color: var(--text-dark);
            font-weight: 500;
        }

        .rec-content {
            padding: 24px;
        }

        .rec-info-text {
            color: var(--text-gray);
            margin-bottom: 16px;
            line-height: 1.5;
            font-size: 14px;
        }

        .rec-learn-more {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 24px;
        }

        .rec-form-section {
            padding: 24px 0;
        }

        .rec-form-section h2 {
            color: var(--text-dark);
            font-size: 16px;
            margin-bottom: 16px;
            font-weight: 500;
        }

        .rec-input-field {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .rec-input-field:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.1);
        }

        .rec-button-group {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .rec-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s;
        }

        .rec-btn-cancel {
            background-color: var(--button-gray);
            color: #374151;
        }

        .rec-btn-save {
            background-color: var(--primary-color);
            color: white;
        }

        .rec-btn:hover {
            opacity: 0.9;
        }

        .rec-info-icon {
            color: var(--primary-color);
            font-size: 16px;
        }
        .back-button {
            display: flex;
            align-items: center;
            color: #6b7280;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .back-button i {
            margin-right: 8px;
        }

        .back-button:hover {
            color: #374151;
        }
        .current-email-text {
            color: var(--text-gray);
            font-size: 14px;
            margin-bottom: 12px;
            padding: 8px;
            background-color: #f3f4f6;
            border-radius: 6px;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            z-index: 1000;
            animation: slideIn 0.3s ease-in-out;
            max-width: 350px;
        }

        .notification.success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .notification.error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .notification-close {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            font-size: 18px;
            padding: 0;
            display: flex;
            align-items: center;
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

        .email-display {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            background-color: #f3f4f6;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .email-display .remove-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .email-display .remove-btn:hover {
            background-color: #dc2626;
        }

        .hidden {
            display: none !important;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 24px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .otp-container {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 24px 0;
        }

        .otp-input {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            text-align: center;
            font-size: 18px;
        }

        .otp-input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .timer {
            color: var(--text-gray);
            font-size: 14px;
            margin: 16px 0;
        }

        .error-message {
            color: #ef4444;
            margin: 8px 0;
            font-size: 14px;
        }

        .resend-text {
            margin-top: 16px;
            font-size: 14px;
            color: var(--text-gray);
        }

        .resend-button {
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            text-decoration: underline;
        }

        .hidden {
            display: none !important;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
        }

        @media (max-width: 1024px) {
            .rec-page-layout {
                flex-direction: column;
                padding: 20px;
                max-width: auto;
            }

            .rec-info-container {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .rec-page-layout {
                padding: 16px;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 15px;
                background-color: var(--background-color);
            }
            
            .rec-page-layout {
                padding: 0;
                margin: 16px;
            }

            .rec-container, .rec-info-container {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                margin-bottom: 16px;
            }

            .rec-page-layout {
                gap: 16px;
            }
        }
        .recovery-email-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .recovery-email-loading-content {
            text-align: center;
        }

        .recovery-email-loading-content img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .recovery-email-loading-content p {
            margin: 0;
            color: #374151;
            font-size: 16px;
            font-weight: 500;
        }

        .remove-otp-input {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            text-align: center;
            font-size: 18px;
        }

        .remove-otp-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.1);
        }

        .remove-otp-input:hover {
            border-color: var(--primary-color);
        }

        .remove-otp-input:disabled {
            background-color: var(--button-gray);
            cursor: not-allowed;
        }

        #removeOtpModal .otp-container {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 24px 0;
        }

        #removeOtpModal .modal-content {
            position: relative;
            padding: 32px;
        }

        #removeOtpError {
            color: #ef4444;
            margin: 8px 0;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    
    <div id="toast-container"></div>

    <div class="recovery-email-loading-overlay hidden" id="recoveryEmailLoadingOverlay">
        <div class="recovery-email-loading-content">
            <img src="/Alumni-CvSU/asset/GIF/Spinner-mo.gif" alt="Loading">
            <p>Please wait...</p>
        </div>
    </div>

    <a href="Account?section=security-settings" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Security Settings
    </a>
    <div class="rec-page-layout">
        <div class="rec-container">
            <div class="rec-header">
                <h1>Recovery email</h1>
            </div>
            <div class="rec-content">
                <p class="rec-info-text">
                    Your recovery email is used to reach you in case we detect unusual activity in your account or you accidentally get locked out.
                </p>
                <p class="rec-info-text">
                    When you change your recovery email, you may be able to choose to get sign-in codes sent to your previous recovery email for one week.
                </p>
                <a href="#" class="rec-learn-more">
                    Learn more 
                    <i class="fas fa-info-circle rec-info-icon"></i>
                </a>
                
                <div class="rec-form-section">
                    <h2>Your recovery email</h2>
                    <div id="emailContainer" class="<?php echo $hasEmail ? '' : 'hidden'; ?>">
                        <div class="email-display">
                            <span><?php echo htmlspecialchars($currentRecoveryEmail); ?></span>
                            <button class="remove-btn" onclick="removeEmail()">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    
                    <div id="emailInputContainer" class="<?php echo $hasEmail ? 'hidden' : ''; ?>">
                        <input type="email" 
                            id="recovery-email" 
                            class="rec-input-field" 
                            placeholder="Enter recovery email">
                        <div class="rec-button-group">
                            <button class="rec-btn rec-btn-cancel">Cancel</button>
                            <button class="rec-btn rec-btn-save" id="saveRecoveryEmail">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rec-info-container">
            <h2 class="rec-info-title">
                <i class="fas fa-shield-alt"></i>
                Recovery Email Features
            </h2>
            <ul class="rec-info-list">
                <li class="rec-info-item">
                    <div class="rec-info-icon-wrapper">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="rec-info-text-wrapper">
                        <strong>Account Recovery:</strong> If you forget your password or lose access to your account, the recovery email allows you to reset your password or recover your account.
                    </div>
                </li>
                <li class="rec-info-item">
                    <div class="rec-info-icon-wrapper">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="rec-info-text-wrapper">
                        <strong>Security Alerts:</strong> The recovery email can be used to send notifications about suspicious activity, login attempts, or security breaches.
                    </div>
                </li>
                <li class="rec-info-item">
                    <div class="rec-info-icon-wrapper">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="rec-info-text-wrapper">
                        <strong>Access Restoration:</strong> In case your primary email is compromised, the recovery email provides an alternative method to regain control.
                    </div>
                </li>
                <li class="rec-info-item">
                    <div class="rec-info-icon-wrapper">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="rec-info-text-wrapper">
                        <strong>Verification and Backup:</strong> Acts as a backup method to verify your identity when logging in from unfamiliar devices.
                    </div>
                </li>
                <li class="rec-info-item">
                    <div class="rec-info-icon-wrapper">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="rec-info-text-wrapper">
                        <strong>Account Lockouts:</strong> If you get locked out due to failed login attempts, use recovery email to unlock your account.
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div id="otpModal" class="modal-overlay hidden">
        <div class="modal-content">
            <h2>Email Verification</h2>
            <p>Please enter the 6-digit code sent to <span id="otpEmail"></span></p>
            <div class="otp-container">
                <input type="text" class="otp-input" maxlength="1" data-index="0">
                <input type="text" class="otp-input" maxlength="1" data-index="1">
                <input type="text" class="otp-input" maxlength="1" data-index="2">
                <input type="text" class="otp-input" maxlength="1" data-index="3">
                <input type="text" class="otp-input" maxlength="1" data-index="4">
                <input type="text" class="otp-input" maxlength="1" data-index="5">
            </div>
            <p class="timer">Time remaining: <span id="otpTimer">10:00</span></p>
            <div class="error-message hidden" id="otpError"></div>
            <div class="modal-buttons">
                <button class="rec-btn rec-btn-cancel" onclick="closeOTPModal()">Cancel</button>
                <button class="rec-btn rec-btn-save" onclick="verifyOTP()">Verify</button>
            </div>
            
            <p class="resend-text">
                Didn't receive the code? 
                <button class="resend-button" onclick="resendOTP()">Resend</button>
            </p>
        </div>
    </div>

    <div id="removeOtpModal" class="modal-overlay hidden">
        <div class="modal-content">
            <h2>Remove Recovery Email</h2>
            <p>Please enter the 6-digit code sent to <span id="removeOtpEmail"></span> to confirm removal</p>
            <div class="otp-container">
                <input type="text" class="remove-otp-input" maxlength="1" data-index="0">
                <input type="text" class="remove-otp-input" maxlength="1" data-index="1">
                <input type="text" class="remove-otp-input" maxlength="1" data-index="2">
                <input type="text" class="remove-otp-input" maxlength="1" data-index="3">
                <input type="text" class="remove-otp-input" maxlength="1" data-index="4">
                <input type="text" class="remove-otp-input" maxlength="1" data-index="5">
            </div>
            <p class="timer">Time remaining: <span id="removeOtpTimer">10:00</span></p>
            <div class="error-message hidden" id="removeOtpError"></div>
            <div class="modal-buttons">
                <button class="rec-btn rec-btn-cancel" onclick="closeRemoveOTPModal()">Cancel</button>
                <button class="rec-btn rec-btn-save" onclick="verifyOTPForRemoval()">Remove</button>
            </div>
            <p class="resend-text">
                Didn't receive the code? 
                <button class="resend-button" onclick="resendRemoveOTP()">Resend</button>
            </p>
        </div>
    </div>
    
    <script>
    function showRecoveryEmailLoading() {
        document.getElementById('recoveryEmailLoadingOverlay').classList.remove('hidden');
    }

    function hideRecoveryEmailLoading() {
        document.getElementById('recoveryEmailLoadingOverlay').classList.add('hidden');
    }

    document.querySelector('.rec-btn-cancel').addEventListener('click', () => {
        history.back();
    });

    function showNotification(message, type) {
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }


    let removeOtpTimer;

    async function removeEmail() {
        try {
            showRecoveryEmailLoading();
            
            const response = await fetch('user/security-page/send-otp-remove.php', {
                method: 'POST'
            });

            const result = await response.json();
            hideRecoveryEmailLoading();

            if (result.status === 'success') {
                document.getElementById('removeOtpEmail').textContent = result.email;
                document.getElementById('removeOtpModal').classList.remove('hidden');
                startRemoveOTPTimer();
                document.querySelector('.remove-otp-input').focus();
            } else {
                showNotification(result.message || 'Failed to send OTP', 'error');
            }
        } catch (error) {
            hideRecoveryEmailLoading();
            showNotification('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    }

    function startRemoveOTPTimer() {
        let timeLeft = 600; 
        const timerElement = document.getElementById('removeOtpTimer');
        
        clearInterval(removeOtpTimer);
        removeOtpTimer = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(removeOtpTimer);
                closeRemoveOTPModal();
                showNotification('OTP has expired. Please try again.', 'error');
            }
        }, 1000);
    }

    function closeRemoveOTPModal() {
        document.getElementById('removeOtpModal').classList.add('hidden');
        clearInterval(removeOtpTimer);

        document.querySelectorAll('.remove-otp-input').forEach(input => input.value = '');
        document.getElementById('removeOtpError').classList.add('hidden');
    }

    document.querySelectorAll('.remove-otp-input').forEach((input, index) => {
        input.addEventListener('keyup', function(e) {
            const value = this.value;
            
            if (value.length === 1) {
                if (index < 5) {
                    document.querySelector(`.remove-otp-input[data-index="${index + 1}"]`).focus();
                }
            } else if (value.length === 0 && e.key === 'Backspace' && index > 0) {
                document.querySelector(`.remove-otp-input[data-index="${index - 1}"]`).focus();
            }
        });

        input.addEventListener('keypress', function(e) {
            if (!/^\d$/.test(e.key)) {
                e.preventDefault();
            }
        });
    });

    async function verifyOTPForRemoval() {
        const otpInputs = document.querySelectorAll('.remove-otp-input');
        const otp = Array.from(otpInputs).map(input => input.value).join('');
        
        if (otp.length !== 6) {
            document.getElementById('removeOtpError').textContent = 'Please enter all 6 digits';
            document.getElementById('removeOtpError').classList.remove('hidden');
            return;
        }

        try {
            showRecoveryEmailLoading();
            
            const formData = new FormData();
            formData.append('otp', otp);
            formData.append('action', 'remove');

            const response = await fetch('user/security-page/verify_otp_remove.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            hideRecoveryEmailLoading();

            if (result.success) {
                closeRemoveOTPModal();
                document.getElementById('emailContainer').classList.add('hidden');
                document.getElementById('emailInputContainer').classList.remove('hidden');
                showNotification('Recovery email removed successfully', 'success');
            } else {
                document.getElementById('removeOtpError').textContent = result.error || 'Invalid OTP';
                document.getElementById('removeOtpError').classList.remove('hidden');
            }
        } catch (error) {
            hideRecoveryEmailLoading();
            showNotification('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    }

    async function resendRemoveOTP() {
        try {
            showRecoveryEmailLoading();
            
            const response = await fetch('user/security-page/send-otp-remove.php', {
                method: 'POST'
            });

            const result = await response.json();
            hideRecoveryEmailLoading();

            if (result.status === 'success') {
                showNotification('OTP sent successfully', 'success');
                startRemoveOTPTimer();
            } else {
                showNotification(result.message || 'Failed to send OTP', 'error');
            }
        } catch (error) {
            hideRecoveryEmailLoading();
            showNotification('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    }

    document.getElementById('saveRecoveryEmail').addEventListener('click', async function() {
        const emailInput = document.getElementById('recovery-email');
        const email = emailInput.value;
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showNotification('Please enter a valid email address', 'error');
            return;
        }

        try {
            showRecoveryEmailLoading(); 
            
            const formData = new FormData();
            formData.append('email', email);
            formData.append('action', 'add');

            const response = await fetch('user/security-page/send-otp-recovery.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            hideRecoveryEmailLoading(); 

            if (result.status === 'success') {
                document.getElementById('otpEmail').textContent = email;
                document.getElementById('otpModal').classList.remove('hidden');
                startOTPTimer();
                document.querySelector('.otp-input').focus();
            } else {
                showNotification(result.message || 'Failed to send OTP', 'error');
                if (result.message.includes('already')) {
                    emailInput.value = '';
                }
            }
        } catch (error) {
            hideRecoveryEmailLoading(); 
            showNotification('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    });

    function startOTPTimer() {
        let timeLeft = 600; 
        const timerElement = document.getElementById('otpTimer');
        
        clearInterval(otpTimer);
        otpTimer = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(otpTimer);
                closeOTPModal();
                showNotification('OTP has expired. Please try again.', 'error');
            }
        }, 1000);
    }

    function closeOTPModal() {
        document.getElementById('otpModal').classList.add('hidden');
        clearInterval(otpTimer);
        document.querySelectorAll('.otp-input').forEach(input => input.value = '');
        document.getElementById('otpError').classList.add('hidden');
    }

    document.querySelectorAll('.otp-input').forEach((input, index) => {
        input.addEventListener('keyup', function(e) {
            const value = this.value;
            
            if (value.length === 1) {
                if (index < 5) {
                    document.querySelector(`[data-index="${index + 1}"]`).focus();
                }
            } else if (value.length === 0 && e.key === 'Backspace' && index > 0) {
                document.querySelector(`[data-index="${index - 1}"]`).focus();
            }
        });

        input.addEventListener('keypress', function(e) {
            if (!/^\d$/.test(e.key)) {
                e.preventDefault();
            }
        });
    });

    async function verifyOTP() {
        const otpInputs = document.querySelectorAll('.otp-input');
        const otp = Array.from(otpInputs).map(input => input.value).join('');
        
        if (otp.length !== 6) {
            document.getElementById('otpError').textContent = 'Please enter all 6 digits';
            document.getElementById('otpError').classList.remove('hidden');
            return;
        }

        try {
            showRecoveryEmailLoading(); 
            
            const email = document.getElementById('recovery-email').value;
            const formData = new FormData();
            formData.append('recovery_email', email);
            formData.append('otp', otp);

            const response = await fetch('user/security-page/verify_otp.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            hideRecoveryEmailLoading(); 

            if (result.success) {
                closeOTPModal();
                showNotification('Recovery email verified and saved successfully!', 'success');
                
                const emailContainer = document.getElementById('emailContainer');
                emailContainer.querySelector('span').textContent = email;
                emailContainer.classList.remove('hidden');
                document.getElementById('emailInputContainer').classList.add('hidden');
            } else {
                document.getElementById('otpError').textContent = result.error || 'Invalid OTP';
                document.getElementById('otpError').classList.remove('hidden');
            }
        } catch (error) {
            hideRecoveryEmailLoading();
            showNotification('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    }

    async function resendOTP() {
        const email = document.getElementById('recovery-email').value;
        
        try {
            showRecoveryEmailLoading(); 
            
            const formData = new FormData();
            formData.append('email', email);
            formData.append('action', 'add');

            const response = await fetch('user/security-page/send-otp-recovery.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            hideRecoveryEmailLoading(); 

            if (result.status === 'success') {
                showNotification('OTP sent successfully', 'success');
                startOTPTimer();
            } else {
                showNotification(result.message || 'Failed to send OTP', 'error');
            }
        } catch (error) {
            hideRecoveryEmailLoading(); 
            showNotification('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    }
    </script>
</body>
</html>