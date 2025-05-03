<?php
require_once 'main_db.php';

// Initialize variables
$error_message = '';
$success_message = '';
$alumni_details = null;
$verified_alumni = null; // Store verified alumni data temporarily
$redirect_to_home = false; // Flag to trigger JavaScript redirect

// Check if user is already an Alumni
if (isset($_SESSION['user_id'])) {
    $check_status = $mysqli->prepare("SELECT user_status FROM user WHERE user_id = ?");
    $check_status->bind_param("i", $_SESSION['user_id']);
    $check_status->execute();
    $result = $check_status->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        if ($user_data['user_status'] === 'Alumni') {
            // Set flag to redirect using JavaScript
            $redirect_to_home = true;
        }
    }
    $check_status->close();
}
 
// Get the current URL parameters
$current_url = '?section=re-apply-account';

// Process verification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $alumni_id_card_no = trim($_POST['alumni_id_card_no']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    
    if (empty($alumni_id_card_no) || empty($first_name) || empty($last_name)) {
        $error_message = 'All fields are required.';
    } else {
        // First, check if the alumni exists in the alumni table
        $alumni_stmt = $mysqli->prepare("SELECT * FROM alumni WHERE 
                                  alumni_id_card_no = ? AND 
                                  first_name = ? AND 
                                  last_name = ?");
        
        $alumni_stmt->bind_param("sss", $alumni_id_card_no, $first_name, $last_name);
        $alumni_stmt->execute();
        $alumni_result = $alumni_stmt->get_result();
        
        if ($alumni_result->num_rows > 0) {
            // Alumni verification successful, but DON'T update the database yet
            // Just retrieve and display user details
            if (isset($_SESSION['user_id'])) {
                // Retrieve current user details without updating anything
                $user_stmt = $mysqli->prepare("SELECT id, user_id, alumni_id_card_no, first_name, last_name, 
                                         middle_name, position, address, telephone, phone_number, 
                                         second_address, accompanying_persons, user_status, verified 
                                  FROM user WHERE user_id = ?");
                $user_stmt->bind_param("i", $_SESSION['user_id']);
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();
                
                if ($user_result->num_rows > 0) {
                    $alumni_details = $user_result->fetch_assoc();
                    // Store the verified alumni_id_card_no in session for later use
                    $_SESSION['verified_alumni_id_card_no'] = $alumni_id_card_no;
                    $success_message = 'Alumni verification successful! Click "Update Profile" to save changes.';
                } else {
                    $error_message = 'User details not found.';
                }
                
                $user_stmt->close();
            } else {
                $success_message = 'Alumni verification successful! Please log in to view your details.';
            }
        } else {
            $error_message = 'No matching alumni record found. Please check your information and try again.';
        }
        
        $alumni_stmt->close();
    }
}

// Handle updates directly from the Update Profile button
if (isset($_GET['update']) && $_GET['update'] == 'submit' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    // Get the verified alumni ID from session if available
    $alumni_id_card_no = isset($_SESSION['verified_alumni_id_card_no']) ? $_SESSION['verified_alumni_id_card_no'] : null;
    
    if ($alumni_id_card_no) {
        // Update BOTH user_status to Alumni AND alumni_id_card_no
        $update_status = $mysqli->prepare("UPDATE user SET user_status = 'Alumni', alumni_id_card_no = ? WHERE user_id = ?");
        $update_status->bind_param("si", $alumni_id_card_no, $user_id);
    } else {
        // Only update user_status if no alumni_id_card_no was verified
        $update_status = $mysqli->prepare("UPDATE user SET user_status = 'Alumni' WHERE user_id = ?");
        $update_status->bind_param("i", $user_id);
    }
    
    if ($update_status->execute()) {
        $success_message = 'Profile updated successfully! User status changed to Alumni.';
        
        // Clear the session variable after successful update
        if (isset($_SESSION['verified_alumni_id_card_no'])) {
            unset($_SESSION['verified_alumni_id_card_no']);
        }
        
        // Set flag to redirect using JavaScript
        $redirect_to_home = true;
    } else {
        $error_message = 'Failed to update profile. Please try again.';
    }
    
    $update_status->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Verification</title>
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
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .verification-form {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin: 20px auto;
            max-width: auto;
        }

        .form-header {
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px;
            text-align: center;
        }

        .form-header img {
            width: 80px;
            margin-bottom: 15px;
        }

        .form-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0;
        }

        .form-content {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #111827;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            color: #374151;
            transition: border-color 0.2s ease;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.1);
        }

        .submit-button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .submit-button:hover {
            background-color: #235329;
        }

        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .details-container {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .details-row {
            display: flex;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .details-label {
            font-weight: 500;
            width: 200px;
            color: #4b5563;
        }

        .details-value {
            flex: 1;
            color: #111827;
        }

        .action-button {
            display: inline-block;
            padding: 8px 16px;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
        }

        .update-button {
            background-color: var(--primary-color);
        }

        .update-button:hover {
            background-color: #235329;
        }

        .cancel-button {
            background-color: #6b7280;
            margin-right: 10px;
        }

        .cancel-button:hover {
            background-color: #4b5563;
        }

        .status-transition {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .status-from {
            padding: 4px 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            color: #4b5563;
        }

        .status-arrow {
            margin: 0 8px;
            color: var(--primary-color);
            font-size: 18px;
        }

        .status-to {
            padding: 4px 8px;
            background-color: var(--primary-color);
            border-radius: 4px;
            color: white;
        }

        @media (max-width: 640px) {
            .verification-form {
                margin: 10px;
            }

            .form-content {
                padding: 16px;
            }

            .details-row {
                flex-direction: column;
            }

            .details-label {
                width: 100%;
                margin-bottom: 4px;
            }

            .status-transition {
                margin-top: 8px;
            }
        }

        .actions-container {
            display: flex;
            justify-content: flex-end;
        }
        
        /* Loading Overlay and Notification Styles */
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
    </style>
</head>
<body>
    <?php if ($redirect_to_home): ?>
        <!-- JavaScript redirect to home page -->
        <script>
            window.location.href = 'Account?section=home';
        </script>
    <?php else: ?>
    <div class="verification-form">
        <div class="form-header">
            <img src="asset/images/res1.png" alt="Cavite State University Logo">
            <h1>
                <?php if ($alumni_details): ?>
                    Alumni Details
                <?php else: ?>
                    Please Input Your Alumni Information
                <?php endif; ?>
            </h1>
        </div>

        <div class="form-content">
            <?php if ($error_message): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!$alumni_details): ?>
                <!-- Initial Verification Form -->
                <form method="POST" action="<?php echo htmlspecialchars($current_url); ?>" id="verificationForm">
                    <div class="form-group">
                        <label for="alumni_id_card_no">Alumni ID Card Number</label>
                        <input type="text" id="alumni_id_card_no" name="alumni_id_card_no" required>
                    </div>

                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                    <button type="submit" class="submit-button">Verify Alumni Status</button>
                </form>
            <?php else: ?>
                <!-- Display Alumni Details -->
                <div class="details-container">
                    <div class="details-row">
                        <div class="details-label">ID</div>
                        <div class="details-value"><?php echo htmlspecialchars($alumni_details['id']); ?></div>
                    </div>
                    <div class="details-row">
                        <div class="details-label">User ID</div>
                        <div class="details-value"><?php echo htmlspecialchars($alumni_details['user_id']); ?></div>
                    </div>
                    <div class="details-row">
                        <div class="details-label">Alumni ID Card Number</div>
                        <div class="details-value">
                            <?php if (isset($_SESSION['verified_alumni_id_card_no'])): ?>
                                <span style="color: var(--primary-color); font-weight: bold;">
                                    <?php echo htmlspecialchars($_SESSION['verified_alumni_id_card_no']); ?> (Pending Update)
                                </span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($alumni_details['alumni_id_card_no']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="details-row">
                        <div class="details-label">Full Name</div>
                        <div class="details-value">
                            <?php echo htmlspecialchars($alumni_details['first_name'] . ' ' . 
                                    ($alumni_details['middle_name'] ? $alumni_details['middle_name'] . ' ' : '') . 
                                    $alumni_details['last_name']); ?>
                        </div>
                    </div>
                    <?php if (isset($alumni_details['position']) && $alumni_details['position']): ?>
                        <div class="details-row">
                            <div class="details-label">Position</div>
                            <div class="details-value"><?php echo htmlspecialchars($alumni_details['position']); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($alumni_details['address']) && $alumni_details['address']): ?>
                        <div class="details-row">
                            <div class="details-label">Primary Address</div>
                            <div class="details-value"><?php echo htmlspecialchars($alumni_details['address']); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($alumni_details['second_address']) && $alumni_details['second_address']): ?>
                        <div class="details-row">
                            <div class="details-label">Secondary Address</div>
                            <div class="details-value"><?php echo htmlspecialchars($alumni_details['second_address']); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($alumni_details['telephone']) && $alumni_details['telephone']): ?>
                        <div class="details-row">
                            <div class="details-label">Telephone</div>
                            <div class="details-value"><?php echo htmlspecialchars($alumni_details['telephone']); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($alumni_details['phone_number']) && $alumni_details['phone_number']): ?>
                        <div class="details-row">
                            <div class="details-label">Phone Number</div>
                            <div class="details-value"><?php echo htmlspecialchars($alumni_details['phone_number']); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($alumni_details['accompanying_persons']) && $alumni_details['accompanying_persons']): ?>
                        <div class="details-row">
                            <div class="details-label">Accompanying Persons</div>
                            <div class="details-value"><?php echo htmlspecialchars($alumni_details['accompanying_persons']); ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="details-row">
                        <div class="details-label">User Status</div>
                        <div class="details-value">
                            <div class="status-transition">
                                <span class="status-from"><?php echo htmlspecialchars($alumni_details['user_status']); ?></span>
                                <span class="status-arrow">→</span>
                                <span class="status-to">Alumni</span>
                            </div>
                        </div>
                    </div>
                    <div class="details-row">
                        <div class="details-label">Verified</div>
                        <div class="details-value"><?php echo htmlspecialchars($alumni_details['verified']); ?></div>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['verified_alumni_id_card_no'])): ?>
                    <div class="message success" style="margin-top: 15px;">
                        Alumni information verified. Click "Update Profile" to save changes and update your status to Alumni.
                    </div>
                <?php endif; ?>
                
                <!-- Update Profile Button -->
                <a href="<?php echo htmlspecialchars($current_url . '&update=submit&user_id=' . $alumni_details['user_id']); ?>" class="action-button update-button" id="updateProfileBtn">Update Profile</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text" id="loadingText">Verifying...</div>
        </div>
    </div>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <script>
        // Function to show loading overlay
        function showLoading(message) {
            const loadingText = document.getElementById('loadingText');
            loadingText.textContent = message;
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';
            overlay.classList.add('loading-overlay-show');
        }

        // Function to hide loading overlay
        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('loading-overlay-hide');
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('loading-overlay-show', 'loading-overlay-hide');
            }, 300);
        }

        // Function to show notification
        function showNotification(message, type) {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            
            const messageSpan = document.createElement('span');
            messageSpan.textContent = message;
            
            const closeButton = document.createElement('button');
            closeButton.className = 'notification-close';
            closeButton.innerHTML = '&times;';
            closeButton.addEventListener('click', () => {
                notification.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            });
            
            notification.appendChild(messageSpan);
            notification.appendChild(closeButton);
            container.appendChild(notification);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (container.contains(notification)) {
                    notification.style.animation = 'slideOut 0.3s ease-out forwards';
                    setTimeout(() => {
                        if (container.contains(notification)) {
                            container.removeChild(notification);
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Handle verification form submission
        const verificationForm = document.getElementById('verificationForm');
        if (verificationForm) {
            verificationForm.addEventListener('submit', function(e) {
                showLoading('Verifying...');
                
                // Let the form submit, but show the loading overlay
                // The server will process the form and return
            });
        }

        // Handle update profile button click
        const updateProfileBtn = document.getElementById('updateProfileBtn');
        if (updateProfileBtn) {
            updateProfileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showLoading('Updating...');
                
                // We need to navigate to the href after showing the loading overlay
                const href = this.getAttribute('href');
                
                // Add a small delay to ensure the loading overlay is visible
                setTimeout(() => {
                    // Send the update request
                    fetch(href)
                        .then(response => {
                            // After successful update, redirect to home
                            window.location.href = 'Account?section=home';
                        })
                        .catch(error => {
                            hideLoading();
                            showNotification('Update failed. Please try again.', 'error');
                        });
                }, 500);
            });
        }

        // Show notifications for success/error messages that might be present on page load
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($success_message): ?>
                // Show success message in notification only (not in the page)
                setTimeout(() => {
                    hideLoading();
                    showNotification('<?php echo addslashes($success_message); ?>', 'success');
                }, 1000);
            <?php elseif ($error_message && isset($_GET['update'])): ?>
                hideLoading();
                showNotification('<?php echo addslashes($error_message); ?>', 'error');
            <?php endif; ?>
        });
    </script>
</body>
</html>