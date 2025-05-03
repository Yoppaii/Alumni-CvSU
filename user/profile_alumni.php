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

        if ($isVerified && isset($_GET['section']) && $_GET['section'] === 'verify-alumni-user') {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Alumni Profile</title>
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

        .alumni-profile-section,
        .alumni-form-section,
        .alumni-review-section {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 20px;
            max-width: auto;
            margin-left: auto;
            margin-right: auto;
        }

        .alumni-profile-heading,
        .alumni-form-heading,
        .alumni-review-heading {
            font-size: 24px;
            color: #111827;
            margin: 0;
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alumni-profile-heading::before {
            content: '\f007';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--primary-color);
        }

        .alumni-input-group {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .alumni-input-group:last-child {
            border-bottom: none;
        }

        .alumni-label {
            display: block;
            color: #111827;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .alumni-input-field,
        .alumni-textarea-field {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            color: #374151;
            transition: border-color 0.2s ease;
        }

        .alumni-textarea-field {
            min-height: 100px;
            resize: vertical;
        }

        .alumni-input-field:focus,
        .alumni-textarea-field:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.1);
        }

        .alumni-submit-btn {
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

        .alumni-submit-btn:hover {
            background-color: #235329;
        }

        .alumni-submit-btn:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
        }



        .unique-alumni-id-header-container {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .unique-alumni-id-logo-container {
            width: 80px;
            margin-right: 20px;
        }

        .unique-alumni-id-logo {
            width: 100%;
            height: auto;
        }

        .unique-alumni-id-text-container {
            flex: 1;
        }

        .unique-alumni-id-text-container p {
            margin: 0;
            line-height: 1.4;
        }

        .unique-alumni-id-university-name {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .unique-alumni-id-campus-name,
        .unique-alumni-id-alumni-association,
        .unique-alumni-id-sec-registration,
        .unique-alumni-id-location {
            font-size: 14px;
            color: #6b7280;
        }



        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 640px) {
            .alumni-input-group {
                padding: 12px 16px;
            }

            .alumni-submit-btn {
                width: calc(100% - 32px);
                margin: 16px;
            }

            .unique-alumni-id-header-container {
                flex-direction: column;
                text-align: center;
            }

            .unique-alumni-id-logo-container {
                margin: 0 0 16px 0;
            }

            .alumni-profile-heading,
            .alumni-form-heading,
            .alumni-review-heading {
                padding: 16px;
                font-size: 20px;
            }
        }
        #alumni-form-section,
        #alumni-review-section {
            display: none;
        }

        #profile-form-section {
            display: block;
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
<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Verifying...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <section class="alumni-profile-section" id="profile-form-section">
        <h3 class="alumni-profile-heading">Fill Out Your Alumni Profile Information</h3>
        <div id="message-container"></div>

        <form id="alumni-profile-form">
            <div class="alumni-input-group">
                <label for="alumni-id-field" class="alumni-label">Alumni ID Card No:</label>
                <input type="text" id="alumni-id-field" name="alumni_id" class="alumni-input-field" placeholder="Enter your Alumni ID Card No." required>
            </div>

            <div class="alumni-input-group">
                <label for="alumni-last-name" class="alumni-label">Last Name:</label>
                <input type="text" id="alumni-last-name" name="alumni_last_name" class="alumni-input-field" placeholder="Enter your last name as per alumni records" required>
            </div>

            <div class="alumni-input-group">
                <label for="alumni-first-name" class="alumni-label">First Name:</label>
                <input type="text" id="alumni-first-name" name="alumni_first_name" class="alumni-input-field" placeholder="Enter your first name as per alumni records" required>
            </div>

            <button type="button" class="alumni-submit-btn" id="check-details-btn" onclick="checkAlumniDetails()">Check Details</button>

        </form>
    </section>

    <section class="alumni-form-section" id="alumni-form-section">
        <div class="unique-alumni-id-header-container">
            <div class="unique-alumni-id-logo-container">
                <img src="user/bg/res1.png" alt="Cavite State University Logo" class="unique-alumni-id-logo">
            </div>

            <div class="unique-alumni-id-text-container">
                <p class="unique-alumni-id-university-name">CAVITE STATE UNIVERSITY</p>
                <p class="unique-alumni-id-campus-name">DON SEVERINO DELAS ALAS CAMPUS</p>
                <p class="unique-alumni-id-alumni-association">ALUMNI ASSOCIATION, INC.</p>
                <p class="unique-alumni-id-sec-registration">SEC Registration No. 2023110126538-08</p>
                <p class="unique-alumni-id-location">Indang, Cavite, Philippines</p>
            </div>
        </div>
        <h3 class="alumni-form-heading">Complete Your Alumni Profile</h3>

        <div class="alumni-input-group">
            <label for="email-field" class="alumni-label">Email Address:</label>
            <input type="email" id="email-field" name="email" class="alumni-input-field" placeholder="Enter your email address" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ""); ?>" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="middle-name-field" class="alumni-label">Middle Name (Optional):</label>
            <input type="text" id="middle-name-field" name="middle_name" class="alumni-input-field" placeholder="Enter your middle name (if any)">
        </div>

        <div class="alumni-input-group">
            <label for="position-field" class="alumni-label">Position:</label>
            <input type="text" id="position-field" name="position" class="alumni-input-field" placeholder="Enter your position/title">
        </div>

        <div class="alumni-input-group">
            <label for="address-field" class="alumni-label">Address:</label>
            <textarea id="address-field" name="address" class="alumni-textarea-field" placeholder="Enter your address"></textarea>
        </div>

        <div class="alumni-input-group">
            <label for="telephone-field" class="alumni-label">Telephone Number:</label>
            <input type="tel" id="telephone-field" name="telephone" class="alumni-input-field" placeholder="Enter your telephone number">
        </div>

        <div class="alumni-input-group">
            <label for="phone-number-field" class="alumni-label">Mobile Number:</label>
            <input type="tel" id="phone-number-field" name="phone_number" class="alumni-input-field" placeholder="Enter your mobile number">
        </div>

        <div class="alumni-input-group">
            <label for="second-address-field" class="alumni-label">Second Address (Optional):</label>
            <textarea id="second-address-field" name="second_address" class="alumni-textarea-field" placeholder="Enter your second address (if any)"></textarea>
        </div>

        <div class="alumni-input-group">
            <label for="accompanying-persons-field" class="alumni-label">Accompanying Persons (Optional):</label>
            <textarea id="accompanying-persons-field" name="accompanying_persons" class="alumni-textarea-field" placeholder="Enter names of accompanying persons (if any)"></textarea>
        </div>


        <button class="alumni-submit-btn" onclick="showReviewSection()">Proceed</button>
    </section>

    <section class="alumni-review-section" id="alumni-review-section">
        <div class="unique-alumni-id-header-container">
            <div class="unique-alumni-id-logo-container">
                <img src="user/bg/res1.png" alt="Cavite State University Logo" class="unique-alumni-id-logo">
            </div>

            <div class="unique-alumni-id-text-container">
                <p class="unique-alumni-id-university-name">CAVITE STATE UNIVERSITY</p>
                <p class="unique-alumni-id-campus-name">DON SEVERINO DELAS ALAS CAMPUS</p>
                <p class="unique-alumni-id-alumni-association">ALUMNI ASSOCIATION, INC.</p>
                <p class="unique-alumni-id-sec-registration">SEC Registration No. 2023110126538-08</p>
                <p class="unique-alumni-id-location">Indang, Cavite, Philippines</p>
            </div>
        </div>
        <h3 class="alumni-review-heading">Review Your Alumni Profile</h3>

        <div class="alumni-input-group">
            <label for="review-email-field" class="alumni-label">Email Address:</label>
            <input type="text" id="review-email-field" class="alumni-input-field" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="review-alumni-id-field" class="alumni-label">Alumni ID Card No:</label>
            <input type="text" id="review-alumni-id-field" class="alumni-input-field" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="review-last-name-field" class="alumni-label">Last Name:</label>
            <input type="text" id="review-last-name-field" class="alumni-input-field" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="review-first-name-field" class="alumni-label">First Name:</label>
            <input type="text" id="review-first-name-field" class="alumni-input-field" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="review-middle-name-field" class="alumni-label">Middle Name:</label>
            <input type="text" id="review-middle-name-field" class="alumni-input-field" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="review-position-field" class="alumni-label">Position:</label>
            <input type="text" id="review-position-field" class="alumni-input-field" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="review-address-field" class="alumni-label">Address:</label>
            <textarea id="review-address-field" class="alumni-textarea-field" readonly></textarea>
        </div>

        <div class="alumni-input-group">
            <label for="review-telephone-field" class="alumni-label">Telephone Number:</label>
            <input type="tel" id="review-telephone-field" class="alumni-input-field" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="review-phone-number-field" class="alumni-label">Mobile Number:</label>
            <input type="tel" id="review-phone-number-field" class="alumni-input-field" readonly>
        </div>

        <div class="alumni-input-group">
            <label for="review-second-address-field" class="alumni-label">Second Address (Optional):</label>
            <textarea id="review-second-address-field" class="alumni-textarea-field" readonly></textarea>
        </div>

        <div class="alumni-input-group">
            <label for="review-accompanying-persons-field" class="alumni-label">Accompanying Persons:</label>
            <textarea id="review-accompanying-persons-field" class="alumni-textarea-field" readonly></textarea>
        </div>

        <button type="button" class="alumni-submit-btn" onclick="submitForm()">Confirm and Submit</button>
    </section>

    <script>
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        function deleteCookie(name) {
            document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
        }

        function showNotification(message, type) {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            
            const messageText = document.createElement('span');
            messageText.textContent = message;
            
            const closeButton = document.createElement('button');
            closeButton.className = 'notification-close';
            closeButton.innerHTML = '&times;';
            closeButton.onclick = () => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            };
            
            notification.appendChild(messageText);
            notification.appendChild(closeButton);
            container.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    if (container.contains(notification)) {
                        container.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }
        function showLoading(duration = 2000) {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';
            overlay.classList.add('loading-overlay-show');
            return new Promise(resolve => setTimeout(resolve, duration));
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('loading-overlay-hide');
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('loading-overlay-show', 'loading-overlay-hide');
            }, 300);
        }

        async function checkAlumniDetails() {
            const alumniId = document.getElementById("alumni-id-field").value;
            const lastName = document.getElementById("alumni-last-name").value;
            const firstName = document.getElementById("alumni-first-name").value;
            const checkDetailsBtn = document.getElementById("check-details-btn");

            if (!alumniId || !lastName || !firstName) {
                showNotification("Please fill in all required fields", "error");
                return;
            }

            checkDetailsBtn.innerHTML = '<div class="spinner"></div> Checking...';
            checkDetailsBtn.disabled = true;

            await showLoading(2000);
 
            const formData = new FormData();
            formData.append('alumni_id', alumniId);
            formData.append('last_name', lastName);
            formData.append('first_name', firstName);

            try {
                const response = await fetch('user/check_alumni_user.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });
                const data = await response.json();

                if (data.exists) {
                    setCookie('alumniVerified', 'true', 1);
                    setCookie('alumniId', alumniId, 1);
                    setCookie('lastName', lastName, 1);
                    setCookie('firstName', firstName, 1);

                    showNotification("Alumni details verified successfully", "success");
                    
                    setTimeout(() => {
                        document.getElementById("profile-form-section").style.display = "none";
                        document.getElementById("alumni-form-section").style.display = "block";
                    }, 1000);
                } else {
                    showNotification(data.message || "No matching records found. Please check your details.", "error");
                    deleteCookie('alumniVerified');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification("An error occurred while verifying details", "error");
                deleteCookie('alumniVerified');
            } finally {
                hideLoading();
                checkDetailsBtn.innerHTML = 'Check Details';
                checkDetailsBtn.disabled = false;
            }
        }

        function showReviewSection() {
            if (getCookie('alumniVerified') !== 'true') {
                showNotification("Please verify your alumni details first", "error");
                window.location.href = '#profile-form-section';
                return;
            }

            const requiredFields = {
                "email-field": "Email",
                "position-field": "Position",
                "address-field": "Address",
                "telephone-field": "Telephone Number",
                "phone-number-field": "Mobile Number"
            };

            for (const [fieldId, fieldName] of Object.entries(requiredFields)) {
                const value = document.getElementById(fieldId).value.trim();
                if (!value) {
                    showNotification(`Please fill in ${fieldName}`, "error");
                    return;
                }
            }

            showLoading();

            setTimeout(() => {
                Object.keys(requiredFields).forEach(fieldId => {
                    const reviewFieldId = `review-${fieldId}`;
                    const value = document.getElementById(fieldId).value;
                    if (document.getElementById(reviewFieldId)) {
                        document.getElementById(reviewFieldId).value = value;
                    }
                });

                document.getElementById("review-alumni-id-field").value = getCookie('alumniId') || '';
                document.getElementById("review-last-name-field").value = getCookie('lastName') || '';
                document.getElementById("review-first-name-field").value = getCookie('firstName') || '';
                document.getElementById("review-middle-name-field").value = document.getElementById("middle-name-field").value;
                document.getElementById("review-second-address-field").value = document.getElementById("second-address-field").value;
                document.getElementById("review-accompanying-persons-field").value = document.getElementById("accompanying-persons-field").value;

                hideLoading();
                document.getElementById("alumni-form-section").style.display = "none";
                document.getElementById("alumni-review-section").style.display = "block";
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (getCookie('alumniVerified') !== 'true') {
                document.getElementById("alumni-form-section").style.display = "none";
                document.getElementById("alumni-review-section").style.display = "none";
                document.getElementById("profile-form-section").style.display = "block";
            }
        });

        async function submitForm() {
            if (getCookie('alumniVerified') !== 'true') {
                showNotification("Please verify your alumni details first", "error");
                return;
            }

            await showLoading(2000);

            const formData = new URLSearchParams();
            formData.append('alumni_id', getCookie('alumniId'));
            formData.append('first_name', getCookie('firstName'));
            formData.append('last_name', getCookie('lastName'));
            formData.append('middle_name', document.getElementById("middle-name-field").value);
            formData.append('position', document.getElementById("position-field").value);
            formData.append('address', document.getElementById("address-field").value);
            formData.append('telephone', document.getElementById("telephone-field").value);
            formData.append('phone_number', document.getElementById("phone-number-field").value);
            formData.append('second_address', document.getElementById("second-address-field").value);
            formData.append('accompanying_persons', document.getElementById("accompanying-persons-field").value);
            formData.append('user_status', 'Alumni');

            try {
                const response = await fetch("user/save_alumni_profile.php", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                });
                const data = await response.json();

                if (data.success) {
                    deleteCookie('alumniVerified');
                    deleteCookie('alumniId');
                    deleteCookie('lastName');
                    deleteCookie('firstName');
                    
                    showNotification("Profile submitted successfully!", "success");
                    setTimeout(() => {
                        window.location.href = "Account?section=home&sidebar=1";
                    }, 1000);
                } else {
                    showNotification(data.message || "An error occurred", "error");
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification("An error occurred while submitting the form", "error");
            } finally {
                hideLoading();
            }
        }
    </script>
</body>
</html>