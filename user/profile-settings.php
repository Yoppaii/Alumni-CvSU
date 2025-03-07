<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$username = isset($user['username']) ? htmlspecialchars($user['username']) : 'Guest';
$email = isset($user['email']) ? htmlspecialchars($user['email']) : 'No email available';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
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

        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-header {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-picture-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            padding: 4px;
            text-align: center;
            color: white;
            font-size: 12px;
            cursor: pointer;
        }

        .profile-info h1 {
            font-size: 24px;
            color: #111827;
            margin: 0 0 4px 0;
        }

        .profile-info p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
        }

        @media (max-width: 640px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
        }

        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h2 {
            font-size: 20px;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h2 i {
            color: var(--primary-color);
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 24px;
        }

        .setting-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .setting-item label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .setting-item input,
        .setting-item select,
        .setting-item textarea {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 14px;
            color: #111827;
            background: #fff;
        }

        .setting-item textarea {
            resize: vertical;
            min-height: 100px;
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .setting-list {
            padding: 8px 0;
        }

        .setting-option {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .setting-option:last-child {
            border-bottom: none;
        }

        .setting-option:hover {
            background-color: #f9fafb;
        }

        .setting-icon {
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

        .setting-icon i {
            color: var(--primary-color);
            font-size: 16px;
        }

        .setting-content {
            flex-grow: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .setting-details h3 {
            color: #111827;
            font-size: 14px;
            font-weight: 500;
            margin: 0 0 4px 0;
        }

        .setting-details p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .setting-action {
            color: #9ca3af;
        }

        .profile-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }

        @media (max-width: 640px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .settings-grid {
                grid-template-columns: 1fr;
            }

            .profile-sections {
                grid-template-columns: 1fr;
            }
        }
    </style>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-picture">
                <img src="/api/placeholder/100/100" alt="Profile Picture">
                <div class="profile-picture-overlay">
                    Change Photo
                </div>
            </div>
            <div class="profile-info">
                <h1><?php echo $username; ?></h1>
                <p><?php echo $email; ?></p>
            </div>
        </div>

        <div class="profile-sections">
            <div class="profile-card">
                <div class="card-header">
                    <h2><i class="fas fa-user"></i> Personal Information</h2>
                </div>
                <div class="setting-list">
                    <div class="setting-option">
                        <div class="setting-icon">
                            <i class="fab fa-google"></i>
                        </div>
                        <div class="setting-content">
                            <div class="setting-details">
                                <h3>Google</h3>
                                <p>Connected</p>
                            </div>
                            <div class="setting-action">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Activity History</h2>
                </div>
                <div class="setting-list">
                    <div class="setting-option">
                        <div class="setting-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="setting-content">
                            <div class="setting-details">
                                <h3>Login History</h3>
                                <p>View recent login activity</p>
                            </div>
                            <div class="setting-action">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    </div>

                    <div class="setting-option">
                        <div class="setting-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="setting-content">
                            <div class="setting-details">
                                <h3>Notifications</h3>
                                <p>View all notifications</p>
                            </div>
                            <div class="setting-action">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="profile-card">
            <div class="card-header">
                <h2><i class="fas fa-question-circle"></i> Help & Support</h2>
            </div>
            <div class="setting-list">
                <div class="setting-option">
                    <div class="setting-icon">
                        <i class="fas fa-life-ring"></i>
                    </div>
                    <div class="setting-content">
                        <div class="setting-details">
                            <h3>Support Center</h3>
                            <p>Get help from our support team</p>
                        </div>
                        <div class="setting-action">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
                
                <div class="setting-option">
                    <div class="setting-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div class="setting-content">
                        <div class="setting-details">
                            <h3>Delete Account</h3>
                            <p>Permanently delete your account</p>
                        </div>
                        <div class="setting-action">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
                        
                <div class="setting-option">
                    <div class="setting-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="setting-content">
                        <div class="setting-details">
                            <h3>Terms & Privacy</h3>
                            <p>Review our terms and privacy policy</p>
                        </div>
                        <div class="setting-action">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const settingOptions = document.querySelectorAll('.setting-option');
            settingOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const settingName = this.querySelector('h3').textContent;
                    console.log(`Clicked setting: ${settingName}`);
                });
            });

            const profilePicture = document.querySelector('.profile-picture');
            profilePicture.addEventListener('click', function() {
                console.log('Profile picture clicked');
            });
        });
    </script>
</body>
</html>