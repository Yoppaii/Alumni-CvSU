<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require('main_db.php');

$twoFactorStatus = 0; 
if (isset($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("SELECT two_factor_auth FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $twoFactorStatus = $row['two_factor_auth'];
    }
    $stmt->close();
}

$lastPasswordChange = 'Never changed';
if (isset($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("SELECT change_date FROM password_history WHERE user_id = ? ORDER BY change_date DESC LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $changeDate = new DateTime($row['change_date']);
        $now = new DateTime();
        $interval = $changeDate->diff($now);
        
        if ($interval->y > 0) {
            $lastPasswordChange = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        } elseif ($interval->m > 0) {
            $lastPasswordChange = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        } elseif ($interval->d > 0) {
            $lastPasswordChange = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        } else {
            $lastPasswordChange = 'Today';
        }
    }
    $stmt->close();
}

$recoveryEmail = 'Not set'; 
if (isset($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("SELECT recovery_email FROM recovery_emails WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $recoveryEmail = $row['recovery_email'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
    <style>
        :root {
            --primary-color: #2d6936;
            --secondary-color: #1e40af;
            --background-color: #f4f6f8;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            background: var(--background-color);
            min-height: 100vh;
            padding: 10px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .security-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .security-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .security-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .security-header h1 i {
            color: var(--primary-color);
        }

        .security-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .security-options {
            padding: 8px 0;
        }

        .security-option {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .security-option:last-child {
            border-bottom: none;
        }

        .security-option:hover {
            background-color: #f9fafb;
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            background-color: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .icon-wrapper i {
            color: var(--primary-color);
            font-size: 16px;
        }

        .option-content {
            flex-grow: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .option-details {
            flex-grow: 1;
        }

        .option-details h3 {
            color: #111827;
            font-size: 14px;
            font-weight: 500;
            margin: 0 0 4px 0;
        }

        .option-details p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .option-value {
            color: #6b7280;
            font-size: 14px;
            margin-right: 16px;
            text-align: right;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .option-value.active {
            color: #16a34a;
            font-weight: 500;
        }

        .option-value.inactive {
            color: #dc2626;
            font-weight: 500;
        }

        .option-value.placeholder {
            color: #9ca3af;
        }

        .chevron {
            color: #9ca3af;
            margin-left: 16px;
            flex-shrink: 0;
        }

        .devices-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .devices-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .devices-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0 0 8px 0;
        }

        .devices-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .devices-list {
            padding: 8px 0;
        }

        .device-item {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .device-item:last-child {
            border-bottom: none;
        }

        .device-item:hover {
            background-color: #f9fafb;
        }

        .device-icon-wrapper {
            width: 40px;
            height: 40px;
            background-color: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .device-icon-wrapper i {
            color: var(--primary-color);
            font-size: 16px;
        }

        .device-content {
            flex-grow: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .device-details {
            flex-grow: 1;
        }

        .device-details h3 {
            color: #111827;
            font-size: 14px;
            font-weight: 500;
            margin: 0 0 4px 0;
        }

        .device-details p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .device-status {
            color: #6b7280;
            font-size: 14px;
            margin-right: 16px;
            text-align: right;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .device-arrow {
            color: #9ca3af;
            margin-left: 16px;
            flex-shrink: 0;
        }

        .sec-verify-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .sec-verify-modal-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 2001;
        }

        .sec-verify-modal-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sec-verify-modal-header h2 {
            margin: 0;
            font-size: 18px;
            color: #111827;
        }

        .sec-verify-close {
            cursor: pointer;
            font-size: 24px;
            color: #6b7280;
        }

        .sec-verify-modal-body {
            padding: 20px;
        }

        .sec-verify-input-group {
            position: relative;
            margin: 20px 0;
        }

        .sec-verify-input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 14px;
        }

        .sec-verify-password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
        }

        .sec-verify-modal-footer {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .sec-verify-cancel-btn, 
        .sec-verify-submit-btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }

        .sec-verify-cancel-btn {
            background-color: #f3f4f6;
            color: #374151;
        }

        .sec-verify-submit-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .sec-verify-error {
            color: #dc2626;
            font-size: 14px;
            margin-top: 8px;
            display: none;
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

        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @media (max-width: 640px) {
            .security-option,
            .device-item {
                padding: 12px 16px;
            }

            .icon-wrapper,
            .device-icon-wrapper {
                width: 32px;
                height: 32px;
            }

            .option-value.active,
            .option-value.inactive,
            #backupCodesStatus {
                min-width: 65px;
                max-width: none;
                font-size: 12px;
                white-space: nowrap;
            }

            .option-value:not(.active):not(.inactive):not(#backupCodesStatus) {
                max-width: 120px;
                font-size: 12px;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .option-details,
            .device-details {
                flex: 1;
                min-width: 0;
            }

            .option-details h3,
            .device-details h3 {
                font-size: 13px;
                margin-bottom: 2px;
            }

            .option-details p,
            .device-details p {
                font-size: 11px;
                line-height: 1.3;
            }

            .device-details small {
                font-size: 10px;
                line-height: 1.2;
            }

            .security-option,
            .device-item {
                gap: 8px;
            }

            .chevron,
            .device-arrow {
                font-size: 12px;
            }

            .device-status {
                font-size: 12px;
                min-width: 65px;
            }
        }
    </style>
<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Verifying...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="main-container">
        <div class="security-card">
            <div class="security-header">
                <h1><i class="fas fa-shield-alt"></i> Security Settings</h1>
                <p>Manage your account security and authentication methods</p>
            </div>
            <div class="security-options">

            <div class="security-option">
                <div class="icon-wrapper">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="option-content">
                    <div class="option-details">
                        <h3>2-Step Verification</h3>
                        <p>Add an extra layer of security to your account</p>
                    </div>
                    <div class="option-value <?php echo $twoFactorStatus == 1 ? 'active' : 'inactive'; ?>">
                        <?php echo $twoFactorStatus == 1 ? 'Enabled' : 'Not enabled'; ?>
                    </div>
                </div>
                <i class="fas fa-chevron-right chevron"></i>
            </div>

            <div class="security-option">
                <div class="icon-wrapper">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="option-content">
                    <div class="option-details">
                        <h3>Password</h3>
                        <p>Change or update your password</p>
                    </div>
                    <div class="option-value">
                        Last changed <?php echo htmlspecialchars($lastPasswordChange); ?>
                    </div>
                </div>
                <i class="fas fa-chevron-right chevron"></i>
            </div>

                <div class="security-option">
                    <div class="icon-wrapper">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="option-content">
                        <div class="option-details">
                            <h3>Recovery email</h3>
                            <p>Add an email address for account recovery</p>
                        </div>
                        <div class="option-value <?php echo $recoveryEmail === 'Not set' ? 'placeholder' : ''; ?>">
                            <?php echo htmlspecialchars($recoveryEmail); ?>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>

                <div class="security-option">
                    <div class="icon-wrapper">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div class="option-content">
                        <div class="option-details">
                            <h3>Backup codes</h3>
                            <p>Generate and manage backup codes</p>
                        </div>
                        <div id="backupCodesStatus" class="option-value placeholder">
                            Loading...
                        </div>
                    </div>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
            </div>
        </div>

        <div class="devices-card">
            <div class="devices-header">
                <h1>Your devices</h1>
                <p>Where you're signed in</p>
            </div>
            <div class="devices-list">
                <div class="device-item">
                    <div class="device-icon-wrapper">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="device-content">
                        <div class="device-details">
                            <h3>Windows</h3>
                            <p>Chrome • Windows</p>
                        </div>
                        <div class="device-status">
                            1 active session
                        </div>
                    </div>
                    <i class="fas fa-chevron-right device-arrow"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div id="passwordVerificationModal" class="sec-verify-modal">
        <div class="sec-verify-modal-content">
            <div class="sec-verify-modal-header">
                <h2>Verify Password</h2>
                <span class="sec-verify-close">&times;</span>
            </div>
            <div class="sec-verify-modal-body">
                <p>Please enter your password to continue</p>
                <div class="sec-verify-input-group">
                    <input type="password" id="verificationPassword" placeholder="Enter your password">
                </div>
                <div id="passwordError" class="sec-verify-error"></div>
            </div>
            <div class="sec-verify-modal-footer">
                <button class="sec-verify-cancel-btn" onclick="closePasswordModal()">Cancel</button>
                <button class="sec-verify-submit-btn" onclick="verifyPassword()">Verify</button>
            </div>
        </div>
    </div>
    <script>
            async function checkTwoFactorStatus() {
            try {
                const response = await fetch('user/check-2fa-status.php');
                const data = await response.json();
                
                const statusElement = document.querySelector('.security-option:first-child .option-value');
                
                if (data.enabled) {
                    statusElement.textContent = 'Enabled';
                    statusElement.classList.remove('placeholder', 'inactive');
                    statusElement.classList.add('active');
                } else {
                    statusElement.textContent = 'Not enabled';
                    statusElement.classList.remove('placeholder', 'active');
                    statusElement.classList.add('inactive');
                }
            } catch (error) {
                console.error('Error checking 2FA status:', error);
            }
        }

        async function checkBackupCodesStatus() {
            try {
                const response = await fetch('user/config/check-backup-codes.php');
                const data = await response.json();
                
                const statusElement = document.getElementById('backupCodesStatus');
                
                if (data.hasBackupCodes) {
                    statusElement.textContent = 'Enabled';
                    statusElement.classList.remove('placeholder', 'inactive');
                    statusElement.classList.add('active');
                } else {
                    statusElement.textContent = 'Not enabled';
                    statusElement.classList.remove('placeholder', 'active');
                    statusElement.classList.add('inactive');
                }
            } catch (error) {
                console.error('Error checking backup codes status:', error);
                const statusElement = document.getElementById('backupCodesStatus');
                statusElement.textContent = 'Error checking status';
                statusElement.classList.add('error');
            }
        }

        function detectDeviceInfo() {
            const userAgent = navigator.userAgent;
            
            let deviceInfo = {
                deviceType: 'Unknown',
                operatingSystem: 'Unknown',
                browser: 'Unknown',
                isMobile: false
            };

            if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(userAgent)) {
                deviceInfo.deviceType = 'Mobile';
                deviceInfo.isMobile = true;
            } else if (/iPad|tablet|Tablet|Nexus 7/.test(userAgent)) {
                deviceInfo.deviceType = 'Tablet';
            } else {
                deviceInfo.deviceType = 'Desktop';
            }

            if (/(Windows)/.test(userAgent)) {
                deviceInfo.operatingSystem = 'Windows';
            } else if (/(Mac OS X)/.test(userAgent)) {
                deviceInfo.operatingSystem = 'MacOS';
            } else if (/(Linux)/.test(userAgent)) {
                deviceInfo.operatingSystem = 'Linux';
            } else if (/(Android)/.test(userAgent)) {
                deviceInfo.operatingSystem = 'Android';
            } else if (/(iPhone|iPad|iPod)/.test(userAgent)) {
                deviceInfo.operatingSystem = 'iOS';
            }

            if (/Chrome/.test(userAgent) && !/Chromium|Edge|Edg|OPR/.test(userAgent)) {
                deviceInfo.browser = 'Chrome';
            } else if (/Firefox/.test(userAgent)) {
                deviceInfo.browser = 'Firefox';
            } else if (/Safari/.test(userAgent) && !/Chrome/.test(userAgent)) {
                deviceInfo.browser = 'Safari';
            } else if (/Edge|Edg/.test(userAgent)) {
                deviceInfo.browser = 'Edge';
            } else if (/OPR/.test(userAgent)) {
                deviceInfo.browser = 'Opera';
            }

            return deviceInfo;
        }

        function updateDevicesList() {
            const deviceInfo = detectDeviceInfo();
            const devicesList = document.querySelector('.devices-list');
            
            const deviceHTML = `
                <div class="device-item current-device">
                    <div class="device-icon-wrapper">
                        <i class="fas ${deviceInfo.deviceType.toLowerCase() === 'mobile' ? 'fa-mobile-alt' : 
                                    deviceInfo.deviceType.toLowerCase() === 'tablet' ? 'fa-tablet-alt' : 
                                    'fa-laptop'}"></i>
                    </div>
                    <div class="device-content">
                        <div class="device-details">
                            <h3>${deviceInfo.operatingSystem}</h3>
                            <p>${deviceInfo.browser} • ${deviceInfo.deviceType}</p>
                            <small style="color: #6b7280;">Current session</small>
                        </div>
                        <div class="device-status">
                            Active now
                        </div>
                    </div>
                    <i class="fas fa-chevron-right device-arrow"></i>
                </div>`;

            devicesList.innerHTML = deviceHTML;
        }

        const securityOptions = document.querySelectorAll('.security-option');
        const passwordModal = document.getElementById('passwordVerificationModal');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const pageMap = {
            '2-Step Verification': 'Account?section=2-step-verification',
            'Password': 'Account?section=change-password',
            'Recovery email': 'Account?section=recovery-email',
            'Backup codes': 'Account?section=backup-codes'
        };

        let targetPage = '';

        securityOptions.forEach(option => {
            option.addEventListener('click', function() {
                const title = this.querySelector('h3').textContent;
                targetPage = pageMap[title];
                
                if (targetPage) {
                    passwordModal.style.display = 'flex';
                }
            });
        });

        function closePasswordModal() {
            passwordModal.style.display = 'none';
            document.getElementById('verificationPassword').value = '';
            document.getElementById('passwordError').style.display = 'none';
        }

        passwordModal.addEventListener('click', function(event) {
            if (event.target === passwordModal) {
                closePasswordModal();
            }
        });

        document.querySelector('.sec-verify-close').addEventListener('click', closePasswordModal);

        async function verifyPassword() {
            const password = document.getElementById('verificationPassword').value;
            const errorDiv = document.getElementById('passwordError');
            
            if (!password) {
                errorDiv.textContent = 'Please enter your password';
                errorDiv.style.display = 'block';
                return;
            }

            loadingOverlay.style.display = 'flex';
            
            try {
                const section = targetPage.split('section=')[1];
                
                const response = await fetch('user/verify-password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `password=${encodeURIComponent(password)}&section=${encodeURIComponent(section)}`
                });

                const data = await response.json();

                if (data.success) {
                    passwordModal.style.display = 'none';
                    setTimeout(() => {
                        loadingOverlay.style.display = 'none';
                        if (targetPage) {
                            window.location.href = targetPage;
                        }
                    }, 2000); 
                    
                    document.getElementById('verificationPassword').value = '';
                    errorDiv.style.display = 'none';
                } else {
                    errorDiv.textContent = data.message || 'Incorrect password';
                    errorDiv.style.display = 'block';
                    loadingOverlay.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.style.display = 'block';
                loadingOverlay.style.display = 'none';
            }
        }

        document.getElementById('verificationPassword').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifyPassword();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            checkTwoFactorStatus();
            checkBackupCodesStatus();
            updateDevicesList();

            if (typeof securityError !== 'undefined' && securityError) {
                alert(securityError);
            }
        });
    </script>
</body>
</html>