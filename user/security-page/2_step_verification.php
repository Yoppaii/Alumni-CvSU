<?php
$userEmail = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $userEmail = $row['email'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2-Step Verification</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .tsv-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh; 
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 3000; 
            justify-content: center;
            align-items: center;
        }

        .tsv-modal-content {
            background-color: white;
            padding: 24px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            position: relative;
            z-index: 3001; 
        }

        .tsv-modal-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #111827;
        }

        .tsv-input-group {
            margin-bottom: 16px;
        }

        .tsv-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #374151;
        }

        .tsv-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
        }

        .tsv-input:focus {
            outline: none;
            border-color: var(--tsv-primary-color);
            box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.2);
        }

        .tsv-btn {
            background-color: var(--tsv-primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .tsv-btn:hover {
            background-color: #235228;
        }

        .tsv-btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .tsv-btn-secondary:hover {
            background-color: #d1d5db;
        }

        .tsv-modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 24px;
        }

        .tsv-otp-inputs {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 16px;
        }

        .tsv-otp-input {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 18px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }

        :root {
            --tsv-primary-color: #2d6936;
            --tsv-secondary-color: #1e40af;
            --tsv-background-color: #f4f6f8;
            --tsv-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --tsv-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .tsv-body {
            background: var(--tsv-background-color);
            min-height: 100vh;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .tsv-back-button {
            display: flex;
            align-items: center;
            color: #6b7280;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .tsv-back-button i {
            margin-right: 8px;
        }

        .tsv-back-button:hover {
            color: #374151;
        }

        .tsv-main-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--tsv-shadow-md);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .tsv-card-content {
            padding: 24px;
            display: flex;
            gap: 24px;
        }

        .tsv-content-left {
            flex: 1;
        }

        .tsv-content-right {
            width: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tsv-verification-image {
            width: 160px;
            height: 160px;
            object-fit: contain;
        }

        .tsv-heading {
            font-size: 24px;
            color: #111827;
            margin: 0 0 12px 0;
        }

        .tsv-description {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .tsv-toggle-button {
            background-color: var(--tsv-primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .tsv-toggle-button:hover {
            background-color: #235228;
        }

        .tsv-toggle-button.tsv-off {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .tsv-toggle-button.tsv-off:hover {
            background-color: #fecaca;
        }

        .tsv-methods-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--tsv-shadow-md);
            padding: 24px;
        }

        .tsv-methods-title {
            font-size: 18px;
            color: #111827;
            margin: 0 0 16px 0;
        }

        .tsv-method-item {
            display: flex;
            align-items: start;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            transition: background-color 0.2s;
        }

        .tsv-method-item.clickable {
            cursor: pointer;
        }

        .tsv-method-item.clickable:hover {
            background-color: #f9fafb;
        }

        .tsv-method-item.disabled {
            opacity: 0.7;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
        }

        .tsv-method-icon {
            width: 48px;
            height: 48px;
            background-color: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .tsv-method-icon i {
            color: var(--tsv-primary-color);
            font-size: 20px;
        }

        .tsv-method-content {
            flex: 1;
        }

        .tsv-method-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .tsv-method-title {
            font-weight: 500;
            color: #111827;
            font-size: 14px;
            margin: 0;
        }

        .tsv-recommended-badge {
            background-color: #ecfdf5;
            color: var(--tsv-primary-color);
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .tsv-unavailable-badge {
            background-color: #fee2e2;
            color: #dc2626;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 500;
        }

        .tsv-method-description {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .tsv-method-arrow {
            color: #9ca3af;
            margin-left: 16px;
        }

        .loading-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: white;
            font-size: 14px;
            font-weight: 500;
            animation: pulse 1.5s ease-in-out infinite;
            margin: 0;
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
            z-index: 4000;
        }

        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 5000; 
        }

        .notification {
            background: white;
            padding: 15px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            max-width: 450px;
            animation: slideIn 0.3s ease-out;
        }

        .notification.success {
            background: #2d6936;
            color: white;
            border-left: 4px solid #1a4721;
        }

        .notification.error {
            background: #dc2626;
            color: white;
            border-left: 4px solid #991b1b;
        }

        .notification-close {
            background: none;
            border: none;
            color: currentColor;
            cursor: pointer;
            padding: 0 5px;
            margin-left: 10px;
            font-size: 20px;
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
        @media screen and (max-width: 768px) {
            .tsv-content-right {
                display: none; 
            }

            .tsv-card-content {
                gap: 0;  
            }

            .tsv-content-left {
                width: 100%; 
            }
        }

        @media screen and (max-width: 768px) {
            .tsv-card-content {
                padding: 16px;
            }
        }
    </style>
</head>
<body class="tsv-body">

<div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="tsv-container">
        <a href="Account?section=security-settings" class="tsv-back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Security Settings
        </a>

        <div class="tsv-main-card">
            <div class="tsv-card-content">
                <div class="tsv-content-left">
                    <h1 class="tsv-heading">Your account is not protected by 2-Step Verification</h1>
                    <p class="tsv-description">
                        Prevent hackers from accessing your account with an additional layer of security.
                    </p>
                    <p class="tsv-description">
                        Unless you're signing in with a passkey, you'll be asked to complete the most secure second step available on your account. 
                        You can update your second steps and sign-in options any time in your settings.
                    </p>
                    <button class="tsv-toggle-button" id="tsv-toggle-button">
                        Turn on 2-Step Verification
                    </button>
                </div>
                <div class="tsv-content-right">
                    <img src="asset/images/res1.png" alt="2-Step Verification" class="tsv-verification-image">
                </div>
            </div>
        </div>

        <div class="tsv-methods-card" id="tsv-methods-card">
            <h2 class="tsv-methods-title">Choose a second verification step</h2>
            <div class="tsv-methods-list">
                <div class="tsv-method-item disabled">
                    <div class="tsv-method-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="tsv-method-content">
                        <div class="tsv-method-header">
                            <h3 class="tsv-method-title">Authenticator app</h3>
                            <span class="tsv-unavailable-badge">Unavailable</span>
                        </div>
                        <p class="tsv-method-description">
                            Use an authenticator app like Google Authenticator to get verification codes
                        </p>
                    </div>
                    <i class="fas fa-chevron-right tsv-method-arrow"></i>
                </div>

                <div class="tsv-method-item clickable">
                    <div class="tsv-method-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="tsv-method-content">
                        <div class="tsv-method-header">
                            <h3 class="tsv-method-title">Email</h3>
                            <span class="tsv-recommended-badge">Recommended</span>
                        </div>
                        <p class="tsv-method-description">
                            Get verification codes sent to your recovery email
                        </p>
                    </div>
                    <i class="fas fa-chevron-right tsv-method-arrow"></i>
                </div>

                <div class="tsv-method-item disabled">
                    <div class="tsv-method-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="tsv-method-content">
                        <div class="tsv-method-header">
                            <h3 class="tsv-method-title">Security key</h3>
                            <span class="tsv-unavailable-badge">Unavailable</span>
                        </div>
                        <p class="tsv-method-description">
                            Use a physical security key when signing in
                        </p>
                    </div>
                    <i class="fas fa-chevron-right tsv-method-arrow"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="tsv-modal" id="tsv-email-modal">
        <div class="tsv-modal-content">
            <h3 class="tsv-modal-title">Set up Email Verification</h3>
            <div class="tsv-input-group">
                <label class="tsv-label" for="tsv-email">Email Address</label>
                <input type="email" id="tsv-email" class="tsv-input" value="<?php echo htmlspecialchars($userEmail); ?>" readonly>
            </div>
            <div class="tsv-input-group">
                <label class="tsv-label" for="tsv-password">Password</label>
                <input type="password" id="tsv-password" class="tsv-input" placeholder="Enter your password">
                <span id="password-error" style="color: #dc2626; font-size: 12px; margin-top: 4px; display: none;">
                    Incorrect password. Please try again.
                </span>
            </div>
            <div class="tsv-modal-buttons">
                <button class="tsv-btn tsv-btn-secondary" onclick="closeEmailModal()">Cancel</button>
                <button class="tsv-btn" onclick="verifyEmailAndPassword()">Send OTP</button>
            </div>
        </div>
    </div>

    <div class="tsv-modal" id="tsv-confirm-disable-modal">
        <div class="tsv-modal-content">
            <h3 class="tsv-modal-title">Turn off 2-Step Verification?</h3>
            <p style="margin-bottom: 16px; font-size: 14px; color: #6b7280;">
                This will make your account less secure. You'll only need your password to sign in.
            </p>
            <div class="tsv-input-group">
                <label class="tsv-label" for="tsv-disable-password">Confirm your password</label>
                <input type="password" id="tsv-disable-password" class="tsv-input" placeholder="Enter your password" autocomplete="current-password">
                <span id="disable-password-error" style="color: #dc2626; font-size: 12px; margin-top: 4px; display: none;">
                    Incorrect password. Please try again.
                </span>
            </div>
            <div class="tsv-modal-buttons">
                <button class="tsv-btn tsv-btn-secondary" onclick="closeDisableModal()">Cancel</button>
                <button class="tsv-btn" style="background-color: #dc2626;" onclick="confirmDisable2FA()">Turn off</button>
            </div>
        </div>
    </div>


    <div class="tsv-modal" id="tsv-otp-modal">
        <div class="tsv-modal-content">
            <h3 class="tsv-modal-title">Enter Verification Code</h3>
            <p style="margin-bottom: 16px; font-size: 14px; color: #6b7280;">
                Please enter the verification code sent to your email
            </p>
            <div class="tsv-otp-inputs">
                <input type="text" maxlength="1" class="tsv-otp-input" data-index="1">
                <input type="text" maxlength="1" class="tsv-otp-input" data-index="2">
                <input type="text" maxlength="1" class="tsv-otp-input" data-index="3">
                <input type="text" maxlength="1" class="tsv-otp-input" data-index="4">
                <input type="text" maxlength="1" class="tsv-otp-input" data-index="5">
                <input type="text" maxlength="1" class="tsv-otp-input" data-index="6">
            </div>
            <div class="tsv-modal-buttons">
                <button class="tsv-btn tsv-btn-secondary" onclick="closeOTPModal()">Cancel</button>
                <button class="tsv-btn" onclick="verifyOTP()">Verify</button>
            </div>
        </div>
    </div>

    <script>
    let tsvIsEnabled = false;
    let isEmailSetup = false;
    let currentOTP = '';

    const tsvToggleButton = document.getElementById('tsv-toggle-button');
    const tsvMethodsCard = document.getElementById('tsv-methods-card');
    const emailModal = document.getElementById('tsv-email-modal');
    const otpModal = document.getElementById('tsv-otp-modal');
    const confirmDisableModal = document.getElementById('tsv-confirm-disable-modal');

    document.addEventListener('DOMContentLoaded', async function() {
        showLoading();
        try {
            const response = await fetch('user/security-page/get_2fa_status.php');
            const data = await response.json();
            
            if (data.status === 'success') {
                tsvIsEnabled = data.two_factor_auth === 1;
                isEmailSetup = data.two_factor_auth === 1; 
                updateUIBasedOnStatus(tsvIsEnabled);
            } else {
                throw new Error('Failed to fetch 2FA status');
            }
        } catch (error) {
            console.error('Error initializing 2FA status:', error);
            showNotification('Error loading 2FA status. Please refresh the page.', 5000);
        } finally {
            hideLoading();
        }

        setupEventListeners();
    });

    function setupEventListeners() {
        document.querySelector('.tsv-method-item.clickable').addEventListener('click', function(e) {
            e.preventDefault();
            emailModal.style.display = 'flex';
        });

        document.querySelectorAll('.tsv-otp-input').forEach(input => {
            input.addEventListener('keyup', handleOTPInput);
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        tsvToggleButton.addEventListener('click', toggle2FA);
    }

    function updateUIBasedOnStatus(enabled) {
        if (enabled) {
            tsvToggleButton.textContent = 'Turn off 2-Step Verification';
            tsvToggleButton.classList.add('tsv-off');
            tsvMethodsCard.style.display = 'none';
            document.querySelector('.tsv-heading').textContent = 'Your account is protected with 2-Step Verification';
        } else {
            tsvToggleButton.textContent = 'Turn on 2-Step Verification';
            tsvToggleButton.classList.remove('tsv-off');
            tsvMethodsCard.style.display = 'block';
            document.querySelector('.tsv-heading').textContent = 'Your account is not protected by 2-Step Verification';
        }
    }

    function showNotification(message, type = 'success', duration = 3000) {
        const container = document.getElementById('notificationContainer');
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out forwards';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, duration);
    }

    function showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }
        
    function closeEmailModal() {
        emailModal.style.display = 'none';
        document.getElementById('tsv-password').value = '';
        const passwordError = document.getElementById('password-error');
        if (passwordError) {
            passwordError.style.display = 'none';
        }
    }

    function closeOTPModal() {
        otpModal.style.display = 'none';
        document.querySelectorAll('.tsv-otp-input').forEach(input => input.value = '');
    }

    function confirmDisable2FA() {
        const password = document.getElementById('tsv-disable-password').value.trim();
        const passwordError = document.getElementById('disable-password-error');

        if (!password) {
            showNotification('Please enter your password.', 3000);
            return;
        }

        showLoading();
        fetch('user/security-page/2-step-verify_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                passwordError.style.display = 'none';
                updateTwoFactorStatus(0);
            } else {
                hideLoading();
                showNotification('Invalid password. Please try again.', 3000);
                document.getElementById('tsv-disable-password').value = '';
                throw new Error('Invalid password');
            }
        })
        .catch(error => {
            hideLoading();
            if (error.message !== 'Invalid password') {
                console.error('Error:', error);
                showNotification('An error occurred while disabling 2-Step Verification. Please try again.', 3000);
            }
        });
    }

    function closeDisableModal() {
        const confirmDisableModal = document.getElementById('tsv-confirm-disable-modal');
        confirmDisableModal.style.display = 'none';
        document.getElementById('tsv-disable-password').value = '';
        document.getElementById('disable-password-error').style.display = 'none';
    }

    function handleOTPInput(e) {
        const index = parseInt(this.getAttribute('data-index'));
        
        if (e.key >= '0' && e.key <= '9') {
            if (index < 6) {
                const nextInput = document.querySelector(`[data-index="${index + 1}"]`);
                if (nextInput) {
                    nextInput.focus();
                }
            }
        } else if (e.key === 'Backspace') {
            if (index > 1 && this.value === '') {
                const prevInput = document.querySelector(`[data-index="${index - 1}"]`);
                if (prevInput) {
                    prevInput.focus();
                }
            }
        }
    }

    function verifyEmailAndPassword() {
        const email = document.getElementById('tsv-email').value;
        const password = document.getElementById('tsv-password').value;
        const passwordError = document.getElementById('password-error');

        if (!email || !password) {
            showNotification('Please enter your password.', 3000);
            return;
        }

        showLoading();

        fetch('user/security-page/2-step-verify_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                passwordError.style.display = 'none';
                
                currentOTP = Math.floor(100000 + Math.random() * 900000).toString();

                return fetch('user/send_otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: email,
                        otp: currentOTP
                    })
                });
            } else {
                hideLoading();
                showNotification('Invalid password. Please try again.', 3000);
                document.getElementById('tsv-password').value = '';
                throw new Error('Invalid password');
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.status === 'success') {
                showNotification('OTP sent successfully!');
                setTimeout(() => {
                    emailModal.style.display = 'none';
                    otpModal.style.display = 'flex';
                }, 1000);
            } else {
                throw new Error('Failed to send OTP');
            }
        })
        .catch(error => {
            hideLoading();
            if (error.message !== 'Invalid password') {
                showNotification('Error sending verification code. Please try again.', 3000);
            }
            console.error('Error:', error);
        });
    }

    function verifyOTP() {
        const enteredOTP = Array.from(document.querySelectorAll('.tsv-otp-input'))
            .map(input => input.value)
            .join('');

        showLoading();

        setTimeout(() => {
            hideLoading();
            if (enteredOTP === currentOTP) {
                isEmailSetup = true;
                closeOTPModal();
                showNotification('2-Step Verification setup completed successfully!');
            } else {
                showNotification('Invalid verification code. Please try again.', 3000);
                document.querySelectorAll('.tsv-otp-input').forEach(input => input.value = '');
                document.querySelector('[data-index="1"]').focus();
            }
        }, 1000);
    }


    function toggle2FA() {
        if (!tsvIsEnabled) {
            if (!isEmailSetup) {
                showNotification('Please set up email verification first before enabling 2-Step Verification.', 3000);
                return;
            }
            updateTwoFactorStatus(1);
        } else {
            confirmDisableModal.style.display = 'flex';
        }
    }

    function updateTwoFactorStatus(status) {
        showLoading();
        
        fetch('user/security-page/update_2fa_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.status === 'success') {
                tsvIsEnabled = status === 1;
                updateUIBasedOnStatus(tsvIsEnabled);
                if (status === 0) {
                    closeDisableModal();
                    showNotification('2-Step Verification disabled successfully!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showNotification('2-Step Verification enabled successfully!');
                }
            } else {
                throw new Error(data.message || 'Failed to update 2FA status');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showNotification('An error occurred while updating 2-Step Verification status. Please try again.', 3000);
        });
    }
</script>
</body>
</html>