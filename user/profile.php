<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'main_db.php';
$isVerified = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    $stmt = $mysqli->prepare("SELECT verified FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $isVerified = $row['verified'] == 1;

        if ($isVerified) {
            echo '<script>window.location.href = "?section=home";</script>';
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Selection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
    <style>
        :root {
            --SEC-primary-color: #2d6936;
            --SEC-secondary-color: #1e40af;
            --SEC-background-color: #f4f6f8;
            --SEC-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --SEC-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            background: var(--SEC-background-color);
            min-height: 100vh;
            padding: 10px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .SEC-profile-section {
            background: white;
            border-radius: 8px;
            box-shadow: var(--SEC-shadow-md);
            overflow: hidden;
            margin: 20px auto;
            max-width: auto;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .SEC-profile-section h3 {
            text-align: center;
            color: #111827;
            font-size: 18px;
            font-weight: 500;
            padding: 24px;
            margin: 0;
            border-bottom: 1px solid #e5e7eb;
            line-height: 1.6;
        }

        .SEC-button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 24px;
        }

        .SEC-button-group button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            min-width: 160px;
        }

        .SEC-alumni-btn {
            background-color: var(--SEC-primary-color);
            color: white;
        }

        .SEC-alumni-btn:hover {
            background-color: #235c2b;
        }

        .SEC-guest-btn {
            background-color: white;
            color: var(--SEC-primary-color);
            border: 2px solid var(--SEC-primary-color) !important;
        }

        .SEC-guest-btn:hover {
            background-color: #f9fafb;
        }


        @keyframes SECfadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .SEC-form {
            padding: 24px;
        }

        .SEC-form-group {
            margin-bottom: 20px;
        }

        .SEC-form-group label {
            display: block;
            margin-bottom: 8px;
            color: #111827;
            font-weight: 500;
        }

        .SEC-form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
        }

        .SEC-form-group input:focus {
            outline: none;
            border-color: var(--SEC-primary-color);
            box-shadow: 0 0 0 3px rgba(45, 105, 54, 0.1);
        }

        .SEC-privacy-notice {
            padding: 16px 24px;
            margin: 0 24px;
            background-color: #f8fafc;
            border-radius: 6px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border: 1px solid #e5e7eb;
        }

        .SEC-privacy-notice i {
            margin-top: 3px;
        }

        .SEC-privacy-notice p {
            margin: 0;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.5;
        }

        @media (max-width: 640px) {
            .SEC-button-group {
                flex-direction: column;
                padding: 16px;
            }

            .SEC-button-group button {
                width: 100%;
            }

            .SEC-profile-section h3 {
                padding: 16px;
                font-size: 16px;
            }

            .SEC-form {
                padding: 16px;
            }

            .SEC-privacy-notice {
                margin: 0 16px;
                padding: 12px 16px;
            }
            
            .SEC-privacy-notice p {
                font-size: 13px;
            }
        }
        .SEC-privacy-notice {
            padding: 20px 24px;
            margin: 24px;
            background-color: #f8fafc;
            border-radius: 8px;
            display: flex;
            gap: 16px;
            border: 1px solid #e5e7eb;
        }

        .SEC-privacy-notice > i {
            margin-top: 3px;
            font-size: 18px;
        }

        .SEC-privacy-content {
            flex: 1;
        }

        .SEC-privacy-main {
            margin: 0 0 16px 0;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.5;
        }

        .SEC-privacy-points {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .SEC-privacy-point {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 13px;
            color: #4b5563;
        }

        .SEC-privacy-point i {
            color: var(--SEC-primary-color);
            font-size: 14px;
            margin-top: 2px;
        }

        @media (max-width: 768px) {
            .SEC-privacy-notice {
                margin: 16px;
                padding: 16px;
            }
            
            .SEC-privacy-points {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .SEC-privacy-point {
                font-size: 12px;
            }
        }
    </style>
<body>
    <section class="SEC-profile-section" id="SEC-profile-selection">
        <h3>
            <i class="fas fa-user-circle fa-2x" style="color: #2d6936; margin-bottom: 1rem;"></i><br>
            Choose your profile type to complete the account verification process and proceed with making your booking.
        </h3>
        
        <div class="SEC-privacy-notice">
            <i class="fas fa-shield-alt" style="color: #2d6936;"></i>
            <div class="SEC-privacy-content">
                <p class="SEC-privacy-main">The Data Privacy Act of 2012, also known as Republic Act 10173, protects the privacy of individuals in the Philippines. The law regulates how personal data is collected, stored, and used in both the private and public sectors.</p>
                <div class="SEC-privacy-points">
                    <div class="SEC-privacy-point">
                        <i class="fas fa-check-circle"></i>
                        <span>Protects individuals' personal information</span>
                    </div>
                    <div class="SEC-privacy-point">
                        <i class="fas fa-check-circle"></i>
                        <span>Ensures the free flow of information</span>
                    </div>
                    <div class="SEC-privacy-point">
                        <i class="fas fa-check-circle"></i>
                        <span>Regulates how personal data is handled</span>
                    </div>
                    <div class="SEC-privacy-point">
                        <i class="fas fa-check-circle"></i>
                        <span>Ensures compliance with international data protection standards</span>
                    </div>
                    <div class="SEC-privacy-point">
                        <i class="fas fa-check-circle"></i>
                        <span>Gives individuals control over their personal information</span>
                    </div>
                    <div class="SEC-privacy-point">
                        <i class="fas fa-check-circle"></i>
                        <span>Imposes responsibilities on businesses and organizations</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="SEC-button-group">
            <button class="SEC-alumni-btn" onclick="window.location.href='?section=verify-alumni-user'">
                <i class="fas fa-graduation-cap"></i> Alumni
            </button>
            <button class="SEC-guest-btn" onclick="window.location.href='?section=verify-guest-user'">
                <i class="fas fa-user"></i> Guest
            </button>
        </div>
    </section>
</body>
</html>