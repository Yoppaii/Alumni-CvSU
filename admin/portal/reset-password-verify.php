<?php
session_start();
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_otp'])) {
    header('Location: ?Cavite-State-University=login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Reset Code - CvSU</title>
    <link rel="icon" href="./asset/images/res1.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap&font-display=swap');

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
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        #ReLoadingOverlay {
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

        .ReLoadingSpinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--login-text-light);
            border-radius: 50%;
            animation: ReSpin 1s linear infinite;
        }

        .ReLoadingContent {
            text-align: center;
        }

        .ReLoadingText {
            margin-top: 15px;
            color: var(--login-text-light);
            font-size: 14px;
            font-weight: 500;
            animation: RePulse 1.5s ease-in-out infinite;
        }

        @keyframes ReSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes RePulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        body {
            background: url('./asset/images/bahay.jpg') no-repeat center center;
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

        .ReContainer {
            width: 100%;
            max-width: 400px;
            background: var(--login-text-light);
            border-radius: 10px;
            padding: 40px;
            box-shadow: var(--login-shadow-md);
        }

        .ReTopTitle {
            font-size: 1.75rem;
            color: var(--login-text-dark);
            margin-bottom: 8px;
            text-align: center;
            font-weight: 600;
        }

        .ReDescription {
            color: #666;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .ReOTPContainer {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 25px;
        }

        .ReOTPInput {
            width: 45px;
            height: 45px;
            border: 1px solid var(--login-border-color);
            border-radius: 6px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 600;
            transition: var(--login-transition);
        }

        .ReOTPInput:focus {
            outline: none;
            border-color: var(--login-primary);
            box-shadow: 0 0 0 3px rgba(0, 100, 0, 0.1);
        }

        .ReTimer {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .ReResendCode {
            display: none;
            text-align: center;
            color: var(--login-primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 15px;
            cursor: pointer;
            transition: var(--login-transition);
        }

        .ReResendCode:hover {
            color: var(--login-secondary);
        }

        .ReSubmitButton {
            width: 100%;
            padding: 12px;
            background: var(--login-primary);
            color: var(--login-text-light);
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--login-transition);
            margin-bottom: 15px;
        }

        .ReSubmitButton:hover {
            background: var(--login-secondary);
        }

        .ReBackToLogin {
            display: block;
            text-align: center;
            color: var(--login-primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--login-transition);
            margin-top: 15px;
        }

        .ReBackToLogin:hover {
            color: var(--login-secondary);
        }

        .ReNotification {
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
            animation: ReSlideIn 0.3s ease-out;
            z-index: 1000;
            border-left: 4px solid;
        }

        @keyframes ReSlideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 480px) {
            .ReContainer {
                max-width: 100%;
                padding: 20px;
            }

            .ReOTPContainer {
                gap: 8px;
            }

            .ReOTPInput {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }

            .ReNotification {
                left: 20px;
                right: 20px;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div id="ReLoadingOverlay">
        <div class="ReLoadingContent">
            <div class="ReLoadingSpinner"></div>
            <div class="ReLoadingText">Loading...</div>
        </div>
    </div>

    <div id="ReNotificationContainer"></div>

    <div class="ReContainer">
        <div class="ReTopTitle">Verify Reset Code</div>
        <p class="ReDescription">
            We've sent a verification code to your email address.<br>
            Please enter the code below.
        </p>
        <form id="ReVerifyForm" method="POST">
            <div class="ReOTPContainer">
                <input type="text" class="ReOTPInput" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="ReOTPInput" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="ReOTPInput" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="ReOTPInput" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="ReOTPInput" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="ReOTPInput" maxlength="1" pattern="[0-9]" required>
            </div>
            <div class="ReTimer">Code expires in: <span id="ReCountdown">05:00</span></div>
            <button type="submit" class="ReSubmitButton">Verify Code</button>
            <div id="ReResendCode" class="ReResendCode">Resend Code</div>
            <a href="?Cavite-State-University=login" class="ReBackToLogin">Back to Login</a>
        </form>
    </div>

    <script>
        function showReNotification(message, type = 'error') {
            const container = document.getElementById('ReNotificationContainer');
            const notification = document.createElement('div');
            notification.className = 'ReNotification';
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

        function showReLoading() {
            const overlay = document.getElementById('ReLoadingOverlay');
            overlay.style.display = 'flex';
        }

        function hideReLoading() {
            const overlay = document.getElementById('ReLoadingOverlay');
            overlay.style.display = 'none';
        }

        const inputs = document.querySelectorAll('.ReOTPInput');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value) {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });

            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = e.clipboardData.getData('text');
                if (/^\d+$/.test(pastedText)) {
                    const digits = pastedText.split('');
                    inputs.forEach((input, i) => {
                        if (digits[i]) {
                            input.value = digits[i];
                            if (i < inputs.length - 1) {
                                inputs[i + 1].focus();
                            }
                        }
                    });
                }
            });
        });

        function startTimer(duration, display) {
            let timer = duration, minutes, seconds;
            let countdown = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(countdown);
                    display.textContent = "00:00";
                    document.getElementById('ReResendCode').style.display = 'block';
                }
            }, 1000);
            return countdown;
        }

        let countdownInterval = null;
        window.onload = function () {
            let fiveMinutes = 60 * 5;
            let display = document.querySelector('#ReCountdown');
            countdownInterval = startTimer(fiveMinutes, display);
        };

        document.getElementById('ReVerifyForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const otp = Array.from(inputs).map(input => input.value).join('');
            if (otp.length !== 6) {
                showReNotification('Please enter all 6 digits');
                return;
            }

            showReLoading();
            const formData = new FormData();
            formData.append('otp', otp);

            fetch('admin/portal/verify-reset-code.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    showReNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    hideReLoading();
                    showReNotification(data.message || 'Verification failed');
                    inputs.forEach(input => input.value = '');
                    inputs[0].focus();
                }
            })
            .catch(error => {
                hideReLoading();
                showReNotification('Server error occurred. Please try again.');
                console.error('Error:', error);
            });
        });

        document.getElementById('ReResendCode').addEventListener('click', function() {
            showReLoading();
            
            fetch('admin/portal/resend-reset-code.php', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                hideReLoading();
                if (data.status === 'success') {
                    showReNotification('New code has been sent', 'success');
                    let display = document.querySelector('#ReCountdown');
                    if (countdownInterval) clearInterval(countdownInterval);
                    countdownInterval = startTimer(60 * 5, display);
                    this.style.display = 'none';
                    inputs.forEach(input => input.value = '');
                    inputs[0].focus();
                } else {
                    showReNotification(data.message || 'Failed to resend code');
                }
            })
            .catch(error => {
                hideReLoading();
                showReNotification('Server error occurred. Please try again.');
                console.error('Error:', error);
            });
        });

        document.querySelector('.ReBackToLogin').addEventListener('click', function(e) {
            e.preventDefault();
            showReLoading();
            setTimeout(() => {
                window.location.href = this.href;
            }, 500);
        });
    </script>
</body>
</html>
