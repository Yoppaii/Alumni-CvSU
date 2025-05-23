<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'main_db.php';
$isVerified = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $stmt = $mysqli->prepare("SELECT verified, user_status FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['user_status'] === 'Guest' && $row['verified'] == 1) {
            if (isset($_GET['section']) && $_GET['section'] === 'verify-guest-user') {
                echo '<script>window.location.href = "?section=home";</script>';
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Profile</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<style>
    :root {
        --primary-color: #2d6936;
        --secondary-color: #1e40af;
        --background-color: #f4f6f8;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --required-color: #e11d48;
    }

    body {
        background: var(--background-color);
        min-height: 100vh;
        padding: 10px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .profile-guest {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 20px;
        max-width: auto;
        margin-left: auto;
        margin-right: auto;
    }

    .header-container-guest {
        display: flex;
        align-items: center;
        padding: 20px;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .logo-container-guest {
        width: 80px;
        margin-right: 20px;
    }

    .logo-guest {
        width: 100%;
        height: auto;
    }

    .text-container-guest {
        flex: 1;
    }

    .text-container-guest p {
        margin: 0;
        line-height: 1.4;
    }

    .university-name-guest {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
    }

    .campus-name-guest,
    .alumni-association-guest,
    .sec-registration-guest,
    .location-guest {
        font-size: 14px;
        color: #6b7280;
    }

    .profile-guest h3 {
        font-size: 24px;
        color: #111827;
        margin: 0;
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .input-group {
        padding: 16px 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .input-group:last-child {
        border-bottom: none;
    }

    .input-group label {
        display: block;
        color: #111827;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    /* Required field indicator */
    .required-indicator {
        color: var(--required-color);
        margin-left: 4px;
        font-weight: bold;
    }

    .input-group input,
    .input-group textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        color: #374151;
        transition: border-color 0.2s ease;
    }

    .input-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .input-group input:focus,
    .input-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.1);
    }

    .input-group input.invalid,
    .input-group textarea.invalid {
        border-color: var(--required-color);
        background-color: #fef2f2;
    }

    .input-group-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        padding: 16px 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .submit-btn {
        display: block;
        width: calc(100% - 48px);
        margin: 24px;
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

    .submit-btn:hover {
        background-color: #235329;
    }

    .submit-btn:disabled {
        background-color: #9ca3af;
        cursor: not-allowed;
    }

    .loading-overlay {
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

    /* Notification styles */
    #notificationContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        max-width: 400px;
        width: 100%;
    }

    .notification {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-bottom: 10px;
        animation: slideIn 0.3s ease-out forwards;
        min-width: 300px;
        max-width: 400px;
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

    .notification.error {
        border-left: 4px solid #ef4444;
    }

    .notification.success {
        border-left: 4px solid #10b981;
    }

    .notification.warning {
        border-left: 4px solid #f59e0b;
    }

    .notification.info {
        border-left: 4px solid #3b82f6;
    }

    .notification-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        padding: 4px;
        color: #64748b;
    }

    .notification-close:hover {
        color: #1e293b;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes pulse {
        0% {
            opacity: 0.6;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.6;
        }
    }

    .loading-overlay-show {
        animation: fadeIn 0.3s ease-in-out forwards;
    }

    .loading-overlay-hide {
        animation: fadeOut 0.3s ease-in-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    @media (max-width: 640px) {
        .header-container-guest {
            flex-direction: column;
            text-align: center;
        }

        .logo-container-guest {
            margin: 0 0 16px 0;
        }

        .input-group,
        .input-group-group {
            padding: 12px 16px;
        }

        .submit-btn {
            width: calc(100% - 32px);
            margin: 16px;
        }

        .profile-guest h3 {
            padding: 16px;
            font-size: 20px;
        }
    }
</style>

<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing...</div>
        </div>
    </div>

    <div id="notificationContainer"></div>

    <section class="profile-guest">
        <div class="header-container-guest">
            <div class="logo-container-guest">
                <img src="asset/images/1.png" alt="Cavite State University Logo" class="logo-guest">
            </div>

            <div class="text-container-guest">
                <p class="university-name-guest">CAVITE STATE UNIVERSITY</p>
                <p class="campus-name-guest">DON SEVERINO DELAS ALAS CAMPUS</p>
                <p class="alumni-association-guest">ALUMNI ASSOCIATION, INC.</p>
                <p class="sec-registration-guest">SEC Registration No. 2023110126538-08</p>
                <p class="location-guest">Indang, Cavite, Philippines</p>
            </div>
        </div>

        <h3>Fill Out Your Guest Profile Information</h3>
        <form id="guest-profile-form">
            <input type="hidden" name="user_status" value="Guest">

            <div class="input-group-group">
                <div class="input-group">
                    <label for="last-name-field">Last Name<span class="required-indicator">*</span>:</label>
                    <input type="text" id="last-name-field" name="last_name" required placeholder="Enter your last name">
                </div>

                <div class="input-group">
                    <label for="first-name-field">First Name<span class="required-indicator">*</span>:</label>
                    <input type="text" id="first-name-field" name="first_name" required placeholder="Enter your first name">
                </div>

                <div class="input-group">
                    <label for="middle-name-field">Middle Name (Optional):</label>
                    <input type="text" id="middle-name-field" name="middle_name" placeholder="Enter your middle name">
                </div>
            </div>

            <div class="input-group-group">
                <div class="input-group">
                    <label for="position-field">Job Position<span class="required-indicator">*</span>:</label>
                    <input type="text" id="position-field" name="position" required placeholder="Enter your work position/title">
                </div>
            </div>

            <div class="input-group-group">
                <div class="input-group">
                    <label for="address-field">Address<span class="required-indicator">*</span>:</label>
                    <textarea id="address-field" name="address" required placeholder="Enter your address"></textarea>
                </div>

                <div class="input-group">
                    <label for="telephone-field">Telephone Number<span class="required-indicator">*</span>:</label>
                    <input type="tel" id="telephone-field" name="telephone" required placeholder="Enter your telephone number">
                </div>

                <div class="input-group">
                    <label for="phone-number-field">Mobile Number<span class="required-indicator">*</span>:</label>
                    <input type="tel" id="phone-number-field" name="phone_number" required placeholder="Enter your phone number">
                </div>
            </div>

            <div class="input-group-group">
                <div class="input-group">
                    <label for="second-address-field">Second Address (Optional):</label>
                    <textarea id="second-address-field" name="second_address" placeholder="Enter your second address (if any)"></textarea>
                </div>

                <div class="input-group">
                    <label for="accompanying-persons-field">Accompanying Person(s) (Optional):</label>
                    <input type="text" id="accompanying-persons-field" name="accompanying_persons" placeholder="Enter names of accompanying persons (if any)">
                </div>
            </div>

            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </section>

    <script>
        $(document).ready(function() {
            // Notification system
            function showNotification(message, type = 'info') {
                const container = document.getElementById('notificationContainer');
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;

                notification.innerHTML = `
                    <div>
                        <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
                    </div>
                    <button type="button" class="notification-close" onclick="this.parentElement.remove()">&times;</button>
                `;

                container.appendChild(notification);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.style.animation = 'slideOut 0.3s ease-out forwards';
                        setTimeout(() => {
                            if (notification.parentElement) {
                                notification.remove();
                            }
                        }, 300);
                    }
                }, 5000);
            }

            // Form validation
            function validateForm() {
                let isValid = true;
                const requiredFields = $('#guest-profile-form [required]');

                // First clear all previous error states
                requiredFields.removeClass('invalid');

                // Check all required fields
                requiredFields.each(function() {
                    if (!$(this).val().trim()) {
                        isValid = false;
                        $(this).addClass('invalid');
                    }
                });

                // Validate phone number format
                const phoneField = $('#phone-number-field');
                const phoneNumber = phoneField.val().trim();
                if (phoneNumber) {
                    if (!/^\d{11}$/.test(phoneNumber)) {
                        isValid = false;
                        phoneField.addClass('invalid');
                        showNotification('Please enter a valid 11-digit phone number', 'error');
                    }
                }

                // Validate telephone number format
                const telField = $('#telephone-field');
                const telNumber = telField.val().trim();
                if (telNumber) {
                    if (!/^\d{7,11}$/.test(telNumber)) {
                        isValid = false;
                        telField.addClass('invalid');
                        showNotification('Please enter a valid telephone number (7-11 digits)', 'error');
                    }
                }

                // Show summary error if any validation failed
                if (!isValid) {
                    showNotification('Please fill in all required fields correctly', 'error');
                }

                return isValid;
            }

            // Remove invalid class on input
            $('#guest-profile-form input, #guest-profile-form textarea').on('input', function() {
                $(this).removeClass('invalid');
            });

            // Handle form submission
            $('#guest-profile-form').on('submit', function(e) {
                e.preventDefault(); // Always prevent default submission

                // Run validation
                if (!validateForm()) {
                    return false; // Stop form submission if validation fails
                }

                // Show loading overlay
                $('#loadingOverlay').css('display', 'flex');

                // Form submission via AJAX
                const formData = $(this).serialize();

                $.ajax({
                        url: 'user/submit_guests.php',
                        method: 'POST',
                        data: formData,
                        dataType: 'json'
                    })
                    .done(function(response) {
                        if (response.status === 'success') {
                            showNotification(response.message, 'success');
                            setTimeout(function() {
                                window.location.href = '?section=home&sidebar=1';
                            }, 3000);
                        } else {
                            $('#loadingOverlay').css('display', 'none');
                            showNotification(response.message || 'Error submitting profile', 'error');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#loadingOverlay').css('display', 'none');
                        showNotification('Error submitting profile. Please try again.', 'error');
                        console.error('Submission error:', textStatus, errorThrown);
                    });
            });
        });
    </script>
</body>

</html>