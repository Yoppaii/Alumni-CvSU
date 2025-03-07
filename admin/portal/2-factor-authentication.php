<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2-Step Verification Options</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            /* Colors */
            --verify-primary: #388e3c;
            --verify-primary-light: #ebf3eb;
            --verify-text-dark: #333;
            --verify-text-gray: #666;
            --verify-border: #ddd;
            --verify-bg-light: #f5f5f5;
            --verify-bg-lighter: #f8f9fa;
            --verify-white: white;
            --verify-shadow: rgba(0, 0, 0, 0.1);
            --verify-overlay: rgba(0, 0, 0, 0.6);
            
            /* Typography */
            --verify-font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Spacing */
            --verify-spacing-xs: 4px;
            --verify-spacing-sm: 8px;
            --verify-spacing-md: 16px;
            --verify-spacing-lg: 24px;
            --verify-spacing-xl: 32px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--verify-font);
        }

        .verify-body {
            background: url('asset/images/bahay.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: var(--verify-spacing-md);
            position: relative;
        }

        .verify-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--verify-overlay);
            z-index: -1;
        }

        .verify-modal {
            background: var(--verify-white);
            border-radius: 10px;
            padding: var(--verify-spacing-xl);
            width: 100%;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 20px 40px var(--verify-shadow);
            animation: verifyModalFade 0.3s ease-in-out;
        }

        @keyframes verifyModalFade {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .verify-modal-icon {
            width: 64px;
            height: 64px;
            background: var(--verify-primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--verify-spacing-lg);
        }

        .verify-modal-icon i {
            color: var(--verify-primary);
            font-size: 32px;
        }

        .verify-modal-title {
            font-size: 24px;
            color: var(--verify-text-dark);
            margin-bottom: var(--verify-spacing-md);
            font-weight: 600;
        }

        .verify-modal-description {
            color: var(--verify-text-gray);
            font-size: 15px;
            margin-bottom: var(--verify-spacing-xl);
            line-height: 1.6;
        }

        .verify-options {
            display: flex;
            flex-direction: column;
            gap: var(--verify-spacing-md);
            margin-bottom: var(--verify-spacing-lg);
        }

        .verify-option-button {
            display: flex;
            align-items: center;
            padding: var(--verify-spacing-md);
            background: var(--verify-bg-lighter);
            border: 1px solid var(--verify-border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: left;
            text-decoration: none;
            color: inherit;
        }

        .verify-option-button:hover {
            background: var(--verify-primary-light);
            border-color: var(--verify-primary);
        }

        .verify-option-icon {
            width: 40px;
            height: 40px;
            background: var(--verify-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: var(--verify-spacing-md);
            flex-shrink: 0;
        }

        .verify-option-icon i {
            color: var(--verify-primary);
            font-size: 20px;
        }

        .verify-option-content {
            flex: 1;
        }

        .verify-option-title {
            font-weight: 600;
            color: var(--verify-text-dark);
            margin-bottom: var(--verify-spacing-xs);
            font-size: 15px;
        }

        .verify-option-description {
            color: var(--verify-text-gray);
            font-size: 13px;
            line-height: 1.4;
        }

        .verify-cancel-button {
            display: inline-block;
            color: var(--verify-text-gray);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: var(--verify-spacing-sm) var(--verify-spacing-md);
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .verify-cancel-button:hover {
            background: var(--verify-bg-light);
        }

        @media (max-width: 480px) {
            .verify-modal {
                padding: var(--verify-spacing-md);
            }
            
            .verify-option-button {
                padding: 12px;
            }
            
            .verify-option-icon {
                width: 32px;
                height: 32px;
                margin-right: 12px;
            }
            
            .verify-option-icon i {
                font-size: 16px;
            }
        }
    </style>
</head>
<body class="verify-body">
    <div class="verify-modal">
        <div class="verify-modal-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h1 class="verify-modal-title">2-Step Verification</h1>
        <p class="verify-modal-description">Choose a way to verify it's you</p>

        <div class="verify-options">
            <a href="#" class="verify-option-button">
                <div class="verify-option-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="verify-option-content">
                    <div class="verify-option-title">Tap on your device</div>
                    <div class="verify-option-description">Tap the notification sent to your enrolled device</div>
                </div>
            </a>

            <a href="2-factor-authentication-recovery-email" class="verify-option-button">
                <div class="verify-option-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="verify-option-content">
                    <div class="verify-option-title">Use your secondary email</div>
                    <div class="verify-option-description">Get a verification code sent to your recovery email</div>
                </div>
            </a>

            <a href="2-factor-authentication-backup-codes" class="verify-option-button">
                <div class="verify-option-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="verify-option-content">
                    <div class="verify-option-title">Enter a backup code</div>
                    <div class="verify-option-description">Use one of your 6-digit backup codes</div>
                </div>
            </a>
        </div>

        <a href="?Cavite-State-University=login" class="verify-cancel-button">Cancel</a>
    </div>
</body>
</html>