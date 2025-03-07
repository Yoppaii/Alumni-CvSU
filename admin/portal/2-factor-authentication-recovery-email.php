<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2-Step Verification</title>
    <link rel="icon" href="../asset/images/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            /* Colors */
            --auth-primary: #388e3c;
            --auth-primary-dark: #2e7d32;
            --auth-primary-light: #ebf3eb;
            --auth-text-dark: #333;
            --auth-text-gray: #666;
            --auth-border: #ddd;
            --auth-bg-light: #f5f5f5;
            --auth-bg-lighter: #f8f9fa;
            --auth-white: white;
            --auth-shadow: rgba(0, 0, 0, 0.1);
            --auth-overlay: rgba(0, 0, 0, 0.6);
            
            /* Typography */
            --auth-font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Spacing */
            --auth-spacing-xs: 8px;
            --auth-spacing-sm: 12px;
            --auth-spacing-md: 16px;
            --auth-spacing-lg: 24px;
            --auth-spacing-xl: 32px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--auth-font);
        }

        .auth-body {
            background: url('../asset/images/bahay.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: var(--auth-spacing-md);
            position: relative;
        }

        .auth-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--auth-overlay);
            backdrop-filter: blur(8px);
            z-index: -1;
        }

        .auth-verification-modal {
            background: var(--auth-white);
            border-radius: 10px;
            padding: var(--auth-spacing-xl);
            width: 100%;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 20px 40px var(--auth-shadow);
            animation: authModalFade 0.3s ease-in-out;
        }

        .auth-step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--auth-spacing-md);
            margin-bottom: var(--auth-spacing-xl);
        }

        .auth-step {
            display: flex;
            align-items: center;
            gap: var(--auth-spacing-xs);
            color: var(--auth-text-gray);
            font-size: 14px;
            font-weight: 500;
        }

        .auth-step.auth-active {
            color: var(--auth-primary);
        }

        .auth-step-divider {
            width: 24px;
            height: 1px;
            background: var(--auth-border);
            margin: 0 4px;
        }

        .auth-step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--auth-bg-light);
            border: 1px solid var(--auth-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .auth-step.auth-active .auth-step-number {
            background: var(--auth-primary-light);
            border-color: var(--auth-primary);
            color: var(--auth-primary);
        }

        .auth-step.auth-completed .auth-step-number {
            background: var(--auth-primary);
            border-color: var(--auth-primary);
            color: var(--auth-white);
        }

        @keyframes authModalFade {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-modal-icon {
            width: 64px;
            height: 64px;
            background: var(--auth-primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--auth-spacing-lg);
        }

        .auth-modal-icon i {
            color: var(--auth-primary);
            font-size: 32px;
        }

        .auth-modal-title {
            font-size: 24px;
            color: var(--auth-text-dark);
            margin-bottom: var(--auth-spacing-md);
            font-weight: 600;
        }

        .auth-modal-description {
            color: var(--auth-text-gray);
            font-size: 15px;
            margin-bottom: var(--auth-spacing-lg);
            line-height: 1.6;
        }

        .auth-email-display {
            background: var(--auth-bg-lighter);
            padding: var(--auth-spacing-md);
            border-radius: 8px;
            margin-bottom: var(--auth-spacing-lg);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--auth-spacing-xs);
            font-size: 15px;
            color: var(--auth-text-dark);
            border: 1px solid var(--auth-border);
        }

        .auth-email-label {
            color: var(--auth-text-gray);
            font-size: 13px;
            font-weight: 500;
        }

        .auth-email-address {
            display: flex;
            align-items: center;
            gap: var(--auth-spacing-xs);
            color: var(--auth-text-dark);
            font-weight: 500;
        }

        .auth-verification-notice {
            color: var(--auth-text-gray);
            font-size: 14px;
            margin-bottom: 28px;
            background: var(--auth-bg-lighter);
            padding: var(--auth-spacing-sm);
            border-radius: 8px;
            border: 1px solid var(--auth-border);
            display: flex;
            align-items: start;
            text-align: left;
            gap: var(--auth-spacing-xs);
        }

        .auth-verification-notice i {
            margin-top: 3px;
            color: var(--auth-primary);
        }

        .auth-input-group {
            margin-bottom: var(--auth-spacing-lg);
            position: relative;
        }

        .auth-input-field {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--auth-border);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            text-align: center;
            letter-spacing: 2px;
        }

        .auth-input-field:focus {
            outline: none;
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 3px rgba(56, 142, 60, 0.1);
        }

        .auth-button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--auth-spacing-md);
        }

        .auth-try-another {
            color: var(--auth-primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: var(--auth-spacing-xs) var(--auth-spacing-md);
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .auth-try-another:hover {
            background: var(--auth-primary-light);
        }

        .auth-next-button {
            background: var(--auth-primary);
            color: var(--auth-white);
            border: none;
            padding: var(--auth-spacing-sm) var(--auth-spacing-xl);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .auth-next-button:hover {
            background: var(--auth-primary-dark);
        }

        @media (max-width: 480px) {
            .auth-verification-modal {
                padding: var(--auth-spacing-md);
            }
            
            .auth-button-group {
                flex-direction: column-reverse;
                gap: var(--auth-spacing-sm);
            }
            
            .auth-next-button {
                width: 100%;
            }
        }
    </style>
</head>
<body class="auth-body">
    <div class="auth-verification-modal">
        <div class="auth-step-indicator">
            <div class="auth-step auth-completed">
                <div class="auth-step-number">
                    <i class="fas fa-check"></i>
                </div>
                <span>Sign in</span>
            </div>
            <div class="auth-step-divider"></div>
            <div class="auth-step auth-active">
                <div class="auth-step-number">2</div>
                <span>Verify</span>
            </div>
        </div>

        <div class="auth-modal-icon">
            <i class="fas fa-envelope-open-text"></i>
        </div>
        <h1 class="auth-modal-title">2-Step Verification</h1>
        <p class="auth-modal-description">To help keep your account secure, please enter the verification code sent to your email address</p>
        
        <div class="auth-email-display">
            <div class="auth-email-label">Email Address</div>
            <div class="auth-email-address">
                <i class="fas fa-envelope"></i>
                <span>john.doe@gmail.com</span>
            </div>
        </div>

        <p class="auth-verification-notice">
            <i class="fas fa-info-circle"></i>
            <span>We've sent a 6-digit verification code to your email address. Please check your inbox and enter the code below.</span>
        </p>
        
        <div class="auth-input-group">
            <input type="text" class="auth-input-field" placeholder="Enter 6-digit code" maxlength="6" />
        </div>
        
        <div class="auth-button-group">
            <a href="2-factor-authentication" class="auth-try-another">
                <i class="fas fa-sync-alt"></i>
                Try another way
            </a>
            <button class="auth-next-button">
                <i class="fas fa-check"></i>
                Verify
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('.auth-input-field');
            
            // Focus input field
            input.focus();
            
            // Only allow numbers and limit to 6 digits
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 6) {
                    this.value = this.value.slice(0, 6);
                }
            });
        });
    </script>
</body>
</html>