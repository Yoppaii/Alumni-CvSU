<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2-Step Verification - Backup Code</title>
    <link rel="icon" href="../asset/images/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            /* Colors */
            --backup-primary: #388e3c;
            --backup-primary-dark: #2e7d32;
            --backup-primary-light: #ebf3eb;
            --backup-text-dark: #333;
            --backup-text-gray: #666;
            --backup-border: #ddd;
            --backup-bg-light: #f5f5f5;
            --backup-bg-lighter: #f8f9fa;
            --backup-white: white;
            --backup-shadow: rgba(0, 0, 0, 0.1);
            --backup-overlay: rgba(0, 0, 0, 0.6);
            
            /* Typography */
            --backup-font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Spacing */
            --backup-spacing-xs: 4px;
            --backup-spacing-sm: 8px;
            --backup-spacing-md: 16px;
            --backup-spacing-lg: 24px;
            --backup-spacing-xl: 32px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--backup-font);
        }

        .backup-body {
            background: url('../asset/images/bahay.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: var(--backup-spacing-md);
            position: relative;
        }

        .backup-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--backup-overlay);
            backdrop-filter: blur(8px);
            z-index: -1;
        }

        .backup-step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--backup-spacing-md);
            margin-bottom: var(--backup-spacing-xl);
        }

        .backup-step {
            display: flex;
            align-items: center;
            gap: var(--backup-spacing-sm);
            color: var(--backup-text-gray);
            font-size: 14px;
            font-weight: 500;
        }

        .backup-step.backup-active {
            color: var(--backup-primary);
        }

        .backup-step-divider {
            width: 24px;
            height: 1px;
            background: var(--backup-border);
            margin: 0 var(--backup-spacing-xs);
        }

        .backup-step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--backup-bg-light);
            border: 1px solid var(--backup-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .backup-step.backup-active .backup-step-number {
            background: var(--backup-primary-light);
            border-color: var(--backup-primary);
            color: var(--backup-primary);
        }

        .backup-step.backup-completed .backup-step-number {
            background: var(--backup-primary);
            border-color: var(--backup-primary);
            color: var(--backup-white);
        }

        .backup-modal {
            background: var(--backup-white);
            border-radius: 10px;
            padding: var(--backup-spacing-xl);
            width: 100%;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 20px 40px var(--backup-shadow);
            animation: backupModalFade 0.3s ease-in-out;
        }

        @keyframes backupModalFade {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .backup-modal-icon {
            width: 64px;
            height: 64px;
            background: var(--backup-primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--backup-spacing-lg);
        }

        .backup-modal-icon i {
            color: var(--backup-primary);
            font-size: 32px;
        }

        .backup-modal-title {
            font-size: 24px;
            color: var(--backup-text-dark);
            margin-bottom: var(--backup-spacing-md);
            font-weight: 600;
        }

        .backup-modal-description {
            color: var(--backup-text-gray);
            font-size: 15px;
            margin-bottom: var(--backup-spacing-lg);
            line-height: 1.6;
        }

        .backup-verification-notice {
            color: var(--backup-text-gray);
            font-size: 14px;
            margin-bottom: 28px;
            background: var(--backup-bg-lighter);
            padding: var(--backup-spacing-sm);
            border-radius: 8px;
            border: 1px solid var(--backup-border);
            display: flex;
            align-items: start;
            text-align: left;
            gap: var(--backup-spacing-sm);
        }

        .backup-verification-notice i {
            margin-top: 3px;
            color: var(--backup-primary);
        }

        .backup-input-group {
            margin-bottom: var(--backup-spacing-lg);
            position: relative;
        }

        .backup-input-field {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--backup-border);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            text-align: center;
            letter-spacing: 2px;
        }

        .backup-input-field:focus {
            outline: none;
            border-color: var(--backup-primary);
            box-shadow: 0 0 0 3px rgba(56, 142, 60, 0.1);
        }

        .backup-button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--backup-spacing-md);
        }

        .backup-try-another {
            color: var(--backup-primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: var(--backup-spacing-sm) var(--backup-spacing-md);
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .backup-try-another:hover {
            background: var(--backup-primary-light);
        }

        .backup-next-button {
            background: var(--backup-primary);
            color: var(--backup-white);
            border: none;
            padding: var(--backup-spacing-sm) var(--backup-spacing-xl);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .backup-next-button:hover {
            background: var(--backup-primary-dark);
        }

        @media (max-width: 480px) {
            .backup-modal {
                padding: var(--backup-spacing-md);
            }
            
            .backup-button-group {
                flex-direction: column-reverse;
                gap: var(--backup-spacing-sm);
            }
            
            .backup-next-button {
                width: 100%;
            }
        }
    </style>
</head>
<body class="backup-body">
    <div class="backup-modal">
        <div class="backup-step-indicator">
            <div class="backup-step backup-completed">
                <div class="backup-step-number">
                    <i class="fas fa-check"></i>
                </div>
                <span>Sign in</span>
            </div>
            <div class="backup-step-divider"></div>
            <div class="backup-step backup-active">
                <div class="backup-step-number">2</div>
                <span>Verify</span>
            </div>
        </div>

        <div class="backup-modal-icon">
            <i class="fas fa-key"></i>
        </div>
        <h1 class="backup-modal-title">2-Step Verification</h1>
        <p class="backup-modal-description">Enter one of your 6-digit backup codes to verify your identity</p>
        
        <p class="backup-verification-notice">
            <i class="fas fa-info-circle"></i>
            <span>Each backup code can only be used once. Make sure to enter it correctly.</span>
        </p>
        
        <div class="backup-input-group">
            <input type="text" class="backup-input-field" placeholder="Enter 8-digit code" maxlength="8" />
        </div>
        
        <div class="backup-button-group">
            <a href="2-factor-authentication" class="backup-try-another">
                <i class="fas fa-sync-alt"></i>
                Try another way
            </a>
            <button class="backup-next-button">
                <i class="fas fa-check"></i>
                Verify
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('.backup-input-field');
            
            // Focus input field
            input.focus();
            
            // Only allow numbers and limit to 8 digits
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 8) {
                    this.value = this.value.slice(0, 8);
                }
            });
        });
    </script>
</body>
</html>