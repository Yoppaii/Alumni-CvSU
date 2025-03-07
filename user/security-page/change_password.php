<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            margin: 0;
        }

        .back-button {
            display: flex;
            align-items: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            margin: 20px auto;
            max-width: 1200px;
            padding: 0 20px;
        }

        .back-button i {
            margin-right: 8px;
        }

        .back-button:hover {
            color: #374151;
        }

        .pwd-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 20px;
        }

        .pwd-grids-container {
            display: flex;
            gap: 20px;
        }

        .pwd-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            flex: 1;
        }

        .pwd-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .pwd-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pwd-header h1 i {
            color: var(--primary-color);
        }

        .pwd-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .pwd-content {
            padding: 24px;
        }

        .pwd-form-group {
            margin-bottom: 20px;
        }

        .pwd-form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4b5563;
            font-weight: 500;
        }

        .pwd-input-container {
            position: relative;
        }

        .pwd-input-container input {
            width: 100%;
            padding: 12px 40px 12px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .pwd-input-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.1);
        }

        .pwd-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
        }

        .pwd-requirements {
            margin-top: 12px;
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 6px;
        }

        .pwd-requirements p {
            margin-bottom: 8px;
            color: #4b5563;
            font-size: 14px;
            font-weight: 500;
        }

        .pwd-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pwd-requirements li {
            margin: 4px 0;
            color: #6b7280;
            font-size: 14px;
            padding-left: 20px;
            position: relative;
        }

        .pwd-requirements li::before {
            content: "×";
            position: absolute;
            left: 0;
            color: #ef4444;
        }

        .pwd-requirements li.pwd-valid::before {
            content: "✓";
            color: #10b981;
        }

        .pwd-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .pwd-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .pwd-btn-primary {
            background-color: var(--primary-color);
            color: white;
            width: auto;
        }

        .pwd-btn-primary:hover {
            background-color: #245329;
        }

        #pwd-message {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
            display: none;
        }

        #pwd-message.pwd-success {
            background-color: #d1fae5;
            color: #065f46;
            display: block;
        }

        #pwd-message.pwd-error {
            background-color: #fee2e2;
            color: #991b1b;
            display: block;
        }

        .history-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .history-item {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-icon {
            width: 32px;
            height: 32px;
            background-color: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .history-details {
            flex: 1;
        }

        .history-date {
            font-size: 14px;
            color: #374151;
            font-weight: 500;
            margin: 0 0 4px 0;
        }

        .history-info {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }
        .pwd-loading-overlay {
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

        .pwd-loading-content {
            text-align: center;
        }

        .pwd-loading-content img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .pwd-loading-content p {
            margin: 0;
            color: #374151;
            font-size: 16px;
            font-weight: 500;
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
        .pwd-logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 3000;
        }

        .pwd-logout-modal-content {
            background: white;
            padding: 24px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .pwd-logout-modal-icon {
            color: var(--primary-color);
            font-size: 48px;
            margin-bottom: 16px;
        }

        .pwd-logout-modal-title {
            font-size: 24px;
            color: #111827;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .pwd-logout-modal-message {
            color: #6b7280;
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 1.5;
        }

        .pwd-logout-modal-button {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 14px;
        }

        .pwd-logout-modal-button:hover {
            background-color: #245329;
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

        .hidden {
            display: none !important;
        }

        @media (max-width: 1024px) {
            .pwd-grids-container {
                flex-direction: column;
            }
        }

        @media (max-width: 640px) {
            .pwd-actions {
                flex-direction: column;
            }
            
            .pwd-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="pwd-loading-overlay hidden" id="pwdLoadingOverlay">
        <div class="pwd-loading-content">
            <img src="/Alumni-CvSU/asset/GIF/Spinner-mo.gif" alt="Loading">
            <p>Please wait...</p>
        </div>
    </div>

    <div id="toast-container"></div>

    <a href="Account?section=security-settings" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Security Settings
    </a>

    <div class="pwd-container">
        <div class="pwd-grids-container">
            <div class="pwd-card">
                <div class="pwd-header">
                    <h1><i class="fas fa-lock"></i> Change Password</h1>
                    <p>Update your password to keep your account secure.</p>
                </div>
                <div class="pwd-content">
                    <div id="pwd-message"></div>
                    <form id="pwd-change-form" method="post">
                        <div class="pwd-form-group">
                            <label for="pwd-current">Current Password</label>
                            <div class="pwd-input-container">
                                <input type="password" id="pwd-current" name="current_password" required>
                                <i class="fas fa-eye pwd-toggle" data-target="pwd-current"></i>
                            </div>
                        </div>

                        <div class="pwd-form-group">
                            <label for="pwd-new">New Password</label>
                            <div class="pwd-input-container">
                                <input type="password" id="pwd-new" name="new_password" required>
                                <i class="fas fa-eye pwd-toggle" data-target="pwd-new"></i>
                            </div>
                            <div class="pwd-requirements">
                                <p>Password must contain:</p>
                                <ul>
                                    <li id="pwd-length">At least 8 characters</li>
                                    <li id="pwd-uppercase">At least one uppercase letter</li>
                                    <li id="pwd-lowercase">At least one lowercase letter</li>
                                    <li id="pwd-number">At least one number</li>
                                    <li id="pwd-special">At least one special character</li>
                                </ul>
                            </div>
                        </div>

                        <div class="pwd-form-group">
                            <label for="pwd-confirm">Confirm New Password</label>
                            <div class="pwd-input-container">
                                <input type="password" id="pwd-confirm" name="confirm_password" required>
                                <i class="fas fa-eye pwd-toggle" data-target="pwd-confirm"></i>
                            </div>
                        </div>

                        <div class="pwd-actions">
                            <button type="button" class="pwd-btn pwd-btn-primary" onclick="showConfirmModal()">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="pwd-card">
                <div class="pwd-header">
                    <h1><i class="fas fa-history"></i> Password Change History</h1>
                    <p>Recent password changes and security updates</p>
                </div>
                <div class="pwd-content">
                    <ul class="history-list" id="passwordHistory">
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="pwdLogoutModal" class="pwd-logout-modal-overlay" style="display: none;">
        <div class="pwd-logout-modal-content">
            <div class="pwd-logout-modal-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="pwd-logout-modal-title">Confirm Password Change</h2>
            <p class="pwd-logout-modal-message">Click below to update your password and log out. You'll need to log in again with your new password.</p>
            <a href="user/logout">
                <button class="pwd-logout-modal-button" onclick="handlePasswordChange()">Update Password and Log Out</button>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pwd-change-form');
            const newPasswordInput = document.getElementById('pwd-new');
            const passwordHistory = document.getElementById('passwordHistory');
            const requirements = {
                length: document.getElementById('pwd-length'),
                uppercase: document.getElementById('pwd-uppercase'),
                lowercase: document.getElementById('pwd-lowercase'),
                number: document.getElementById('pwd-number'),
                special: document.getElementById('pwd-special')
            };

            async function hideLoadingOverlay(minimumDelay = 1000) {
                const startTime = window._loadingStartTime || Date.now();
                const elapsedTime = Date.now() - startTime;
                
                if (elapsedTime < minimumDelay) {
                    await new Promise(resolve => setTimeout(resolve, minimumDelay - elapsedTime));
                }
                
                document.getElementById('pwdLoadingOverlay').classList.add('hidden');
            }

            function showLoadingOverlay() {
                window._loadingStartTime = Date.now();
                document.getElementById('pwdLoadingOverlay').classList.remove('hidden');
            }

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

            async function loadPasswordHistory() {
                const passwordHistory = document.getElementById('passwordHistory');
                
                try {
                    passwordHistory.innerHTML = `
                        <li class="history-item">
                            <div class="history-details">
                                <p class="history-info">Loading history...</p>
                            </div>
                        </li>
                    `;

                    const response = await fetch('user/security-page/get_password_history.php');
                    const data = await response.json();
                    
                    if (data.success && data.history.length > 0) {
                        passwordHistory.innerHTML = data.history.map(item => `
                            <li class="history-item">
                                <div class="history-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="history-details">
                                    <p class="history-date">${item.date}</p>
                                    <p class="history-info">${item.action}</p>
                                </div>
                            </li>
                        `).join('');
                    } else if (data.success && data.history.length === 0) {
                        passwordHistory.innerHTML = `
                            <li class="history-item">
                                <div class="history-details">
                                    <p class="history-info">No password changes found</p>
                                </div>
                            </li>
                        `;
                    } else {
                        throw new Error(data.message || 'Failed to load history');
                    }
                } catch (error) {
                    console.error('Error loading password history:', error);
                    passwordHistory.innerHTML = `
                        <li class="history-item">
                            <div class="history-details">
                                <p class="history-info">Unable to load password history</p>
                            </div>
                        </li>
                    `;
                }
            }

            loadPasswordHistory();

            document.querySelectorAll('.pwd-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.target);
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            });

            function checkPassword(password) {
                const checks = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password),
                    special: /[^A-Za-z0-9]/.test(password)
                };

                for (const [key, valid] of Object.entries(checks)) {
                    requirements[key].classList.toggle('pwd-valid', valid);
                }

                return Object.values(checks).every(Boolean);
            }

            async function checkPasswordChangeAllowed() {
                try {
                    const response = await fetch('user/security-page/get_password_history.php');
                    const data = await response.json();
                    
                    if (data.success && data.history.length > 0) {
                        const lastChange = new Date(data.history[0].date);
                        const waitingPeriod = 7 * 24 * 60 * 60 * 1000; 
                        const timeRemaining = lastChange.getTime() + waitingPeriod - Date.now();
                        
                        if (timeRemaining > 0) {
                            const daysRemaining = Math.ceil(timeRemaining / (24 * 60 * 60 * 1000));
                            const submitButton = form.querySelector('button[type="button"]');
                            
                            submitButton.disabled = true;
                            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                            
                            showNotification(
                                `Please wait ${daysRemaining} more days before changing your password again`,
                                'error'
                            );
                            return false;
                        }
                    }
                    return true;
                } catch (error) {
                    console.error('Error checking password change eligibility:', error);
                    return true; 
                }
            }

            window.showConfirmModal = async function() {
                const currentPassword = document.getElementById('pwd-current').value;
                const newPassword = document.getElementById('pwd-new').value;
                const confirmPassword = document.getElementById('pwd-confirm').value;

                if (!currentPassword || !newPassword || !confirmPassword) {
                    showNotification('All fields are required.', 'error');
                    return;
                }

                if (!checkPassword(newPassword)) {
                    showNotification('Please meet all password requirements.', 'error');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    showNotification('New passwords do not match.', 'error');
                    return;
                }

                const changeAllowed = await checkPasswordChangeAllowed();
                if (!changeAllowed) {
                    return;
                }

                document.getElementById('pwdLogoutModal').style.display = 'flex';
            }

            window.handlePasswordChange = async function() {
                const currentPassword = document.getElementById('pwd-current').value;
                const newPassword = document.getElementById('pwd-new').value;

                try {
                    showLoadingOverlay();

                    const response = await fetch('user/security-page/update_password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `current_password=${encodeURIComponent(currentPassword)}&new_password=${encodeURIComponent(newPassword)}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        window.location.href = 'Access-Point?Cavite-State-University=login';
                    } else {
                        hideLoadingOverlay();
                        document.getElementById('pwdLogoutModal').style.display = 'none';
                        showNotification(data.message || 'Error updating password.', 'error');
                    }
                } catch (error) {
                    hideLoadingOverlay();
                    document.getElementById('pwdLogoutModal').style.display = 'none';
                    console.error('Error:', error);
                    showNotification(error.message || 'An error occurred. Please try again.', 'error');
                }
            }

            newPasswordInput.addEventListener('input', function() {
                checkPassword(this.value);
            });

            window.addEventListener('click', function(event) {
                const modal = document.getElementById('pwdLogoutModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });

            window.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    document.getElementById('pwdLogoutModal').style.display = 'none';
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        });
</script>
</body>
</html>