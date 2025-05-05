<?php
require_once 'main_db.php';

if (!$mysqli) {
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT `email` FROM `users` WHERE id = ?");
if ($stmt === false) {
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();
 
$check_pre_fill = $mysqli->prepare("SELECT * FROM user WHERE user_id = ? LIMIT 1");
if ($check_pre_fill === false) {
    exit("Prepare failed: " . $mysqli->error);
}
$check_pre_fill->bind_param("i", $user_id);
$check_pre_fill->execute();
$pre_fill_result = $check_pre_fill->get_result();
$pre_fill = $pre_fill_result->fetch_assoc();
$check_pre_fill->close();


$check_existing = $mysqli->prepare("SELECT * FROM alumni_id_cards WHERE user_id = ? LIMIT 1");
if ($check_existing === false) {
    exit();
}

$check_existing->bind_param("i", $user_id);
$check_existing->execute();
$result = $check_existing->get_result();
$existing_application = $result->fetch_assoc();
$check_existing->close();


// Check if there's a decline reason for declined applications
$decline_reason = '';
$declined_at = '';
$declined_by = '';

if ($existing_application && strtolower($existing_application['status']) === 'declined') {
    $get_decline_reason = $mysqli->prepare("SELECT r.reason, r.created_at, r.declined_by, u.username as declined_by_name 
                                           FROM alumni_id_declined_reasons r
                                           LEFT JOIN users u ON r.declined_by = u.id
                                           WHERE r.application_id = ?
                                           ORDER BY r.created_at DESC
                                           LIMIT 1");
    if ($get_decline_reason) {
        $get_decline_reason->bind_param("i", $existing_application['id']);
        $get_decline_reason->execute();
        $decline_result = $get_decline_reason->get_result();
        if ($decline_row = $decline_result->fetch_assoc()) {
            $decline_reason = $decline_row['reason'];

            // Format date
            $date = new DateTime($decline_row['created_at']);
            $declined_at = $date->format('F j, Y \a\t g:i A');

            // Get declined by info if available
            $declined_by = isset($decline_row['declined_by_name']) ? $decline_row['declined_by_name'] : 'Administrator';
        }
        $get_decline_reason->close();
    }

    // Add these decline details to the existing_application array so they're available to JavaScript
    $existing_application['decline_reason'] = $decline_reason;
    $existing_application['declined_at'] = $declined_at;
    $existing_application['declined_by'] = $declined_by;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni ID Application</title>
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

    .unique-alumni-id-form {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin: 20px auto;
        max-width: auto;
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

    h2 {
        font-size: 24px;
        color: #111827;
        margin: 0;
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .unique-form-row {
        display: flex;
        gap: 24px;
        padding: 24px;
    }

    .unique-form-column {
        flex: 1;
        min-width: 0;
    }

    label {
        display: block;
        color: #111827;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        color: #374151;
        margin-bottom: 16px;
        transition: border-color 0.2s ease;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="number"]:focus,
    select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.1);
    }

    button[type="submit"] {
        display: block;
        width: calc(100% - 48px);
        margin: 0 24px 24px;
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

    button[type="submit"]:hover {
        background-color: #235329;
    }

    button[type="submit"]:disabled {
        background-color: #a8a8a8 !important;
        cursor: not-allowed !important;
        opacity: 0.7;
    }

    .alumni-details {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        margin: 20px auto;
        max-width: auto;
    }

    .details-container {
        display: flex;
        gap: 24px;
        padding: 24px;
    }

    .details-column {
        flex: 1;
    }

    .detail-item {
        margin-bottom: 16px;
    }

    .detail-item label {
        font-weight: 600;
        color: #111827;
        margin-right: 8px;
    }

    .status-message {
        padding: 24px;
        border-top: 1px solid #e5e7eb;
        text-align: center;
    }

    .application-status {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        margin-top: 12px;
    }

    .application-status.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .application-status.approved {
        background-color: #d1fae5;
        color: #065f46;
    }

    .application-status.rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .action-buttons {
        padding: 0 24px 24px;
        text-align: center;
    }

    .cancel-button {
        padding: 8px 16px;
        background-color: #ef4444;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .cancel-button:hover {
        background-color: #dc2626;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        position: relative;
        z-index: 2001;
    }

    .modal-content h3 {
        margin: 0 0 16px;
        color: #111827;
    }

    .modal-content textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 16px;
        resize: vertical;
    }

    .modal-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .back-button,
    .confirm-button {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .back-button {
        background-color: #9ca3af;
        color: white;
    }

    .confirm-button {
        background-color: var(--primary-color);
        color: white;
    }

    @media (max-width: 640px) {
        .unique-form-row {
            flex-direction: column;
            gap: 0;
        }

        .unique-alumni-id-header-container {
            flex-direction: column;
            text-align: center;
        }

        .unique-alumni-id-logo-container {
            margin: 0 0 16px 0;
        }

        .details-container {
            flex-direction: column;
            gap: 0;
        }

        button[type="submit"] {
            width: calc(100% - 32px);
            margin: 0 16px 16px;
        }
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

    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
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

    f .application-status {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        margin-top: 12px;
    }

    .application-status.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .application-status.declined {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .application-status.paid {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .application-status.confirmed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .download-button {
        padding: 8px 16px;
        background-color: var(--secondary-color);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        margin-left: 10px;
    }

    .download-button:hover {
        background-color: #1e3a8a;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 0 24px 24px;
    }

    .apply-alumni-button {
        padding: 8px 16px;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        margin-left: 10px;
    }

    .apply-alumni-button:hover {
        background-color: #235329;
    }
</style>

<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Loading...</div>
        </div>
    </div>

    <div id="notificationContainer" class="notification-container"></div>

    <form action="process_alumni_id.php" method="POST" class="unique-alumni-id-form" style="display: <?php echo $existing_application ? 'none' : 'block'; ?>">
        <div class="unique-alumni-id-header-container">
            <div class="unique-alumni-id-logo-container">
                <img src="asset/images/res1.png" alt="Cavite State University Logo" class="unique-alumni-id-logo">
            </div>

            <div class="unique-alumni-id-text-container">
                <p class="unique-alumni-id-university-name">CAVITE STATE UNIVERSITY</p>
                <p class="unique-alumni-id-campus-name">DON SEVERINO DELAS ALAS CAMPUS</p>
                <p class="unique-alumni-id-alumni-association">ALUMNI ASSOCIATION, INC.</p>
                <p class="unique-alumni-id-sec-registration">SEC Registration No. 2023110126538-08</p>
                <p class="unique-alumni-id-location">Indang, Cavite, Philippines</p>
            </div>
        </div>

        <h2>Alumni ID Card Form</h2>

        <div class="unique-form-row">
            <div class="unique-form-column">
                <label for="unique-last-name">Last Name<span style="color: red;">*</span></label>
                <input type="text" id="unique-last-name" name="last_name" value="<?php echo htmlspecialchars($pre_fill['last_name'] ?? ''); ?>" required readonly>

                <label for="unique-first-name">First Name<span style="color: red;">*</span></label>
                <input type="text" id="unique-first-name" name="first_name" value="<?php echo htmlspecialchars($pre_fill['first_name'] ?? ''); ?>" required readonly>

                <label for="unique-middle-name">Middle Name</label>
                <input type="text" id="unique-middle-name" name="middle_name"
                    value="<?php echo htmlspecialchars($pre_fill['middle_name'] ?? ''); ?>"
                    <?php echo (!empty($pre_fill['middle_name'])) ? 'readonly' : ''; ?>>

                <label for="unique-email">Email<span style="color: red;">*</label>
                <input type="email" id="unique-email" name="email" value="<?php echo htmlspecialchars($email ?? 'N/A'); ?>" readonly>
            </div>
            <div class="unique-form-column">
                <label for="unique-course-name">Course<span style="color: red;">*</span></label>
                <input type="text" id="unique-course-name" name="course" required>

                <label for="unique-year-graduated">Year Graduated<span style="color: red;">*</span></label>
                <input type="number" id="unique-year-graduated" name="year_graduated" required>

                <label for="unique-highschool-graduated">High School Graduated<span style="color: red;">*</span></label>
                <input type="text" id="unique-highschool-graduated" name="highschool_graduated" required>

                <label for="unique-membership-type">Membership Type<span style="color: red;">*</span></label>
                <select id="unique-membership-type" name="membership_type" required onchange="updatePrice()">
                    <option value="5_years">5 Years (₱500.00)</option>
                    <option value="lifetime">Lifetime (₱1,500.00)</option>
                </select>
                <div class="price-display" style="margin-bottom: 16px;">
                    <label>Membership Fee:</label>
                    <span id="membership-price" style="font-weight: bold; color: #2d6936;">₱500.00</span>
                    <input type="hidden" name="price" id="price-input" value="500.00">
                </div>
            </div>
        </div>

        <!-- Payment Instructions Box -->
        <div style="margin: 0 24px 24px; padding: 15px; background-color: #f0f7ff; border-left: 4px solid #2d6936; border-radius: 4px;">
            <h4 style="margin-top: 0; color: #2d6936;">Payment Instructions:</h4>
            <p style="margin-bottom: 8px;">After form submission and confirmation, please follow these steps to complete your application:</p>
            <ol style="margin-left: 20px; padding-left: 0;">
                <li>Visit the <strong>Bahay ng Alumni Office</strong> to make your payment</li>
                <li>Bring a valid ID and a copy of your application confirmation</li>
                <li>Once payment is processed, your application status will be updated to "Paid"</li>
                <li>You can then update your account from Guest to Alumni User</li>
                <li>As an Alumni User, you will receive discounts on room reservations</li>
            </ol>
        </div>
        <div style="margin: 0 24px 16px;">
            <label class="terms-checkbox-container" style="display: flex; align-items: flex-start; margin-bottom: 10px; cursor: pointer;">
                <input type="checkbox" id="terms-checkbox" name="terms_accepted" style="margin-top: 3px; margin-right: 8px;" required>
                <span style="font-size: 14px; line-height: 1.4;">
                    I have read and agree to the <a href="#" onclick="showTermsModal(); return false;">Terms and Policies</a> of the Cavite State University Alumni Association.
                </span>
            </label>
        </div>
        <button type="submit" id="submit-button" disabled>Submit</button>

    </form>

    <div id="alumniDetails" class="alumni-details" style="display: <?php echo $existing_application ? 'block' : 'none'; ?>">
        <div class="unique-alumni-id-header-container">
            <div class="unique-alumni-id-logo-container">
                <img src="asset/images/res1.png" alt="Cavite State University Logo" class="unique-alumni-id-logo">
            </div>

            <div class="unique-alumni-id-text-container">
                <p class="unique-alumni-id-university-name">CAVITE STATE UNIVERSITY</p>
                <p class="unique-alumni-id-campus-name">DON SEVERINO DELAS ALAS CAMPUS</p>
                <p class="unique-alumni-id-alumni-association">ALUMNI ASSOCIATION, INC.</p>
                <p class="unique-alumni-id-sec-registration">SEC Registration No. 2023110126538-08</p>
                <p class="unique-alumni-id-location">Indang, Cavite, Philippines</p>
            </div>
        </div>

        <h2>Alumni ID Application Details</h2>

        <div class="details-container">
            <div class="details-column">
                <div class="detail-item">
                    <label>Last Name:</label>
                    <span id="display-last-name"></span>
                </div>
                <div class="detail-item">
                    <label>First Name:</label>
                    <span id="display-first-name"></span>
                </div>
                <div class="detail-item">
                    <label>Middle Name:</label>
                    <span id="display-middle-name"></span>
                </div>
                <div class="detail-item">
                    <label>Email:</label>
                    <span id="display-email"></span>
                </div>
            </div>

            <div class="details-column">
                <div class="detail-item">
                    <label>Course:</label>
                    <span id="display-course"></span>
                </div>
                <div class="detail-item">
                    <label>Year Graduated:</label>
                    <span id="display-year-graduated"></span>
                </div>
                <div class="detail-item">
                    <label>High School Graduated:</label>
                    <span id="display-highschool-graduated"></span>
                </div>
                <div class="detail-item">
                    <label>Membership Type:</label>
                    <span id="display-membership-type"></span>
                </div>
            </div>
        </div>
        <!-- Payment Instructions Box -->
        <div style="margin: 0 24px 24px; padding: 15px; background-color: #f0f7ff; border-left: 4px solid #2d6936; border-radius: 4px;">
            <h4 style="margin-top: 0; color: #2d6936;">Payment Instructions:</h4>
            <p style="margin-bottom: 8px;">After form submission and confirmation, please follow these steps to complete your application:</p>
            <ol style="margin-left: 20px; padding-left: 0;">
                <li>Visit the <strong>Bahay ng Alumni Office</strong> to make your payment</li>
                <li>Bring a valid ID and a copy of your application confirmation</li>
                <li>Once payment is processed, your application status will be updated to "Paid"</li>
                <li>You can then update your account from Guest to Alumni User</li>
                <li>As an Alumni User, you will receive discounts on room reservations</li>
            </ol>
        </div>

        <!-- Modified Payment Instructions Box Section (inside Alumni Details) -->
        <div class="status-message">
            <?php if ($existing_application): ?>
                <?php
                $status = strtolower($existing_application['status'] ?? 'pending');
                $statusMessage = '';
                ?>

                <?php if ($status === 'pending'): ?>
                    <p>Your Alumni ID application has been submitted successfully. Please wait for further instructions via email.</p>

                <?php elseif ($status === 'declined'): ?>
                    <p>Your Alumni ID application has been declined. Please contact the alumni office for more information.</p>

                <?php elseif ($status === 'paid'): ?>
                    <p>Your payment has been received. Your Alumni ID is being processed. You can now apply for an Alumni User account.</p>

                <?php elseif ($status === 'confirmed'): ?>
                    <p>Your Alumni ID application has been confirmed. You may now claim your ID at the alumni office.</p>

                <?php else: ?>
                    <p>Your application status is unknown. Please contact the alumni office.</p>
                <?php endif; ?>

                <div class="application-status <?php echo $status; ?>">
                    Status: <?php echo ucfirst($status); ?>
                </div>

            <?php else: ?>
                <p>No application found. Please submit an Alumni ID application.</p>
            <?php endif; ?>
        </div>

        <div class="action-buttons">
            <?php if ($existing_application && (strtolower($existing_application['status']) === 'pending' || strtolower($existing_application['status']) === 'confirmed')): ?>
                <button type="button" class="cancel-button" onclick="showCancelModal()">Cancel Application</button>
            <?php endif; ?>
            <?php if ($existing_application && strtolower($existing_application['status']) === 'confirmed'): ?>
                <button type="button" class="download-button" onclick="generatePDF()">Download Summary</button>
            <?php endif; ?>
            <?php if ($existing_application && strtolower($existing_application['status']) === 'paid'): ?>
                <button type="button" class="apply-alumni-button" onclick="window.location.href='?section=re-apply-account'">Apply for Alumni User Account</button>
            <?php endif; ?>
            <?php if ($existing_application && strtolower($existing_application['status']) === 'declined'): ?>
                <button type="button" class="apply-alumni-button" onclick="reapplyForAlumniID()">Re-apply for Alumni ID</button>
            <?php endif; ?>
        </div>

        <div id="cancelModal" class="modal">
            <div class="modal-content">
                <h3>Cancel Application</h3>
                <p>Please provide a reason for cancellation:</p>
                <textarea id="cancellationReason" rows="4" placeholder="Enter your reason here..."></textarea>
                <div class="modal-buttons">
                    <button type="button" class="back-button" onclick="hideCancelModal()">Back</button>
                    <button type="button" class="confirm-button" onclick="confirmCancellation()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div id="termsModal" class="modal">
        <div class="modal-content" style="max-width: 600px; max-height: 80vh; overflow-y: auto;">
            <h3>Terms and Policies</h3>
            <div style="margin-bottom: 20px; text-align: left;">
                <p><strong>Alumni ID Card Terms and Policies</strong></p>
                <p>By applying for a Cavite State University Alumni ID Card, you agree to the following terms and policies:</p>
                <ol style="padding-left: 20px;">
                    <li>The information provided in this application must be accurate and complete.</li>
                    <li>The Alumni ID Card remains the property of Cavite State University Alumni Association, Inc.</li>
                    <li>The membership fee is non-refundable once the application is processed.</li>
                    <li>The Alumni ID Card is non-transferable and for personal use only.</li>
                    <li>You must comply with all university policies when using your Alumni ID Card.</li>
                    <li>Your personal information will be handled in accordance with applicable privacy laws.</li>
                    <li>The university reserves the right to revoke the Alumni ID Card for violation of terms.</li>
                    <li>Benefits associated with the Alumni ID Card are subject to change without prior notice.</li>
                </ol>
            </div>
            <div class="modal-buttons">
                <button type="button" class="back-button" onclick="hideTermsModal()">Close</button>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.unique-alumni-id-form');
            if (form) {
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        validateInput(this);
                    });
                });
            }

            const existingApplicationData = document.getElementById('existingApplicationData');
            if (existingApplicationData) {
                try {
                    const applicationData = JSON.parse(existingApplicationData.textContent);
                    displayAlumniDetails(applicationData);
                } catch (error) {
                    console.error('Error parsing existing application data:', error);
                }
            }
        });

        function updatePrice() {
            const membershipType = document.getElementById('unique-membership-type').value;
            const priceDisplay = document.getElementById('membership-price');
            const priceInput = document.getElementById('price-input');

            const price = membershipType === 'lifetime' ? 1500.00 : 500.00;
            priceDisplay.textContent = `₱${price.toFixed(2)}`;
            priceInput.value = price.toFixed(2);
        }

        function showLoading(message = 'Loading...') {
            const overlay = document.getElementById('loadingOverlay');
            const loadingText = overlay.querySelector('.loading-text');

            loadingText.textContent = message;
            overlay.style.display = 'flex';
            overlay.offsetHeight;
            overlay.classList.add('loading-overlay-show');
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('loading-overlay-hide');

            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('loading-overlay-show', 'loading-overlay-hide');
            }, 300);
        }

        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;

            const messageSpan = document.createElement('span');
            messageSpan.textContent = message;

            const closeButton = document.createElement('button');
            closeButton.className = 'notification-close';
            closeButton.innerHTML = '×';
            closeButton.onclick = () => {
                notification.classList.remove('visible');
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            };

            notification.appendChild(messageSpan);
            notification.appendChild(closeButton);
            container.appendChild(notification);

            notification.offsetHeight;

            notification.classList.add('visible');

            setTimeout(() => {
                if (notification.parentNode === container) {
                    notification.classList.remove('visible');
                    setTimeout(() => {
                        if (notification.parentNode === container) {
                            container.removeChild(notification);
                        }
                    }, 300);
                }
            }, 5000);
        }

        function validateForm(formData) {
            const required = ['last_name', 'first_name', 'email', 'course', 'year_graduated', 'highschool_graduated', 'membership_type'];
            const errors = [];

            required.forEach(field => {
                if (!formData.get(field)) {
                    errors.push(`${field.replace('_', ' ')} is required`);
                }
            });

            const yearGraduated = parseInt(formData.get('year_graduated'));
            const currentYear = new Date().getFullYear();
            if (yearGraduated > currentYear || yearGraduated < 1900) {
                errors.push('Please enter a valid graduation year');
            }

            const email = formData.get('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                errors.push('Please enter a valid email address');
            }

            return errors;
        }

        function validateInput(input) {
            const value = input.value.trim();
            const name = input.name;
            let isValid = true;
            let errorMessage = '';

            switch (name) {
                case 'year_graduated':
                    const year = parseInt(value);
                    const currentYear = new Date().getFullYear();
                    if (year > currentYear || year < 1900) {
                        isValid = false;
                        errorMessage = 'Please enter a valid graduation year';
                    }
                    break;
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Please enter a valid email address';
                    }
                    break;
            }

            if (!isValid) {
                input.classList.add('invalid');
                showInputError(input, errorMessage);
            } else {
                input.classList.remove('invalid');
                clearInputError(input);
            }

            return isValid;
        }

        function showInputError(input, message) {
            let errorDiv = input.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('error-message')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }
            errorDiv.textContent = message;
        }

        function clearInputError(input) {
            const errorDiv = input.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains('error-message')) {
                errorDiv.remove();
            }
        }

        function displayAlumniDetails(formData) {
            document.getElementById('alumniDetails').style.display = 'block';
            document.getElementById('display-last-name').textContent = formData.last_name;
            document.getElementById('display-first-name').textContent = formData.first_name;
            document.getElementById('display-middle-name').textContent = formData.middle_name || '';
            document.getElementById('display-email').textContent = formData.email;
            document.getElementById('display-course').textContent = formData.course;
            document.getElementById('display-year-graduated').textContent = formData.year_graduated;
            document.getElementById('display-highschool-graduated').textContent = formData.highschool_graduated;
            document.getElementById('display-membership-type').textContent =
                `${formData.membership_type === 'lifetime' ? 'Lifetime' : '5 Years'} (${parseFloat(formData.price).toFixed(2)})`;

            let statusMessage = '';
            let statusHtml = '';

            switch (formData.status.toLowerCase()) {
                case 'pending':
                    statusMessage = 'Please wait, your application is pending. We will review your application. Once your application is reviewed, your status will change to confirmed.';
                    break;
                case 'declined':
                    statusMessage = 'Your Alumni ID application has been declined. Please contact the alumni office for more information.';

                    // Add decline reason HTML if available
                    if (formData.decline_reason) {
                        statusHtml = `
                <div style="margin-top: 20px; padding: 15px; background-color: #fee2e2; border-radius: 6px; text-align: left;">
                    <h4 style="margin-top: 0; color: #991b1b;">Reason for Decline:</h4>
                    <p style="margin-bottom: 5px;">${formData.decline_reason}</p>
                    <p style="font-size: 12px; color: #64748b; margin-top: 10px; margin-bottom: 0;">
                        Declined on ${formData.declined_at} by ${formData.declined_by}
                    </p>
                </div>`;
                    }
                    break;
                case 'paid':
                    statusMessage = 'Thank you for purchasing an Alumni ID! You can now apply to change your account from Guest to Alumni User Account.';
                    break;
                case 'confirmed':
                    statusMessage = 'Your application is confirmed! Please download the PDF file and carefully read the instructions.';
                    break;
                default:
                    statusMessage = 'Application status unknown. Please contact the alumni office.';
            }

            let statusMessageDiv = document.querySelector('.status-message');
            if (!statusMessageDiv) {
                statusMessageDiv = document.createElement('div');
                statusMessageDiv.className = 'status-message';
                document.getElementById('alumniDetails').appendChild(statusMessageDiv);
            }

            statusMessageDiv.innerHTML = `
        <p>${statusMessage}</p>
        ${statusHtml}
        <div class="application-status ${formData.status.toLowerCase()}">
            Status: ${formData.status.charAt(0).toUpperCase() + formData.status.slice(1)}
        </div>
    `;

            const cancelButton = document.querySelector('.cancel-button');
            if (cancelButton) {
                cancelButton.style.display = ['pending', 'confirmed'].includes(formData.status.toLowerCase()) ? 'block' : 'none';
            }

            const downloadButton = document.querySelector('.download-button');
            if (downloadButton) {
                downloadButton.style.display = formData.status.toLowerCase() === 'confirmed' ? 'block' : 'none';
            }
        }

        function showCancelModal() {
            document.getElementById('cancelModal').style.display = 'flex';
        }

        function hideCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
            document.getElementById('cancellationReason').value = '';
        }

        function confirmCancellation() {
            const reason = document.getElementById('cancellationReason').value.trim();

            if (!reason) {
                showNotification('Please provide a reason for cancellation', 'warning');
                return;
            }

            showLoading('Processing cancellation...');
            hideCancelModal();

            fetch('/Alumni-CvSU/user/cancel_alumni_id.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        reason: reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    setTimeout(() => {
                        hideLoading();
                        if (data.success) {
                            showNotification('Application cancelled successfully', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            showNotification(data.message || 'Error cancelling application', 'error');
                        }
                    }, 1000);
                })
                .catch(error => {
                    setTimeout(() => {
                        hideLoading();
                        showNotification('An error occurred. Please try again.', 'error');
                        console.error('Error:', error);
                    }, 1000);
                });
        }

        window.jsPDF = window.jspdf.jsPDF;

        async function getLogo() {
            const img = document.querySelector('img.unique-alumni-id-logo');
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            for (let i = 0; i < data.length; i += 4) {
                if (data[i] === 255 && data[i + 1] === 255 && data[i + 2] === 255) {
                    data[i + 3] = 0;
                }
            }
            ctx.putImageData(imageData, 0, 0);
            return canvas.toDataURL('image/png');
        }

        async function generatePDF() {
            try {
                showLoading('Generating PDF...');

                const details = {
                    lastName: document.getElementById('display-last-name').textContent,
                    firstName: document.getElementById('display-first-name').textContent,
                    middleName: document.getElementById('display-middle-name').textContent,
                    email: document.getElementById('display-email').textContent,
                    course: document.getElementById('display-course').textContent,
                    yearGraduated: document.getElementById('display-year-graduated').textContent,
                    highSchool: document.getElementById('display-highschool-graduated').textContent,
                    membershipType: document.getElementById('display-membership-type').textContent
                };

                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const leftMargin = 25;
                const pageWidth = 210;
                let yPos = 20;

                try {
                    const logoBase64 = await getLogo();
                    doc.addImage(logoBase64, 'PNG', leftMargin, yPos, 25, 25);
                } catch (logoError) {
                    console.error('Error loading logo:', logoError);
                }

                doc.setFontSize(16);
                doc.setFont('helvetica', 'bold');
                doc.text('CAVITE STATE UNIVERSITY', pageWidth / 2, yPos + 10, {
                    align: 'center'
                });

                yPos += 15;
                doc.setFontSize(12);
                doc.setFont('helvetica', 'normal');
                doc.text('DON SEVERINO DELAS ALAS CAMPUS', pageWidth / 2, yPos + 10, {
                    align: 'center'
                });
                doc.text('ALUMNI ASSOCIATION, INC.', pageWidth / 2, yPos + 15, {
                    align: 'center'
                });
                doc.text('SEC Registration No. 2023110126538-08', pageWidth / 2, yPos + 20, {
                    align: 'center'
                });
                doc.text('Indang, Cavite, Philippines', pageWidth / 2, yPos + 25, {
                    align: 'center'
                });

                yPos += 45;

                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('ALUMNI ID APPLICATION', pageWidth / 2, yPos, {
                    align: 'center'
                });

                yPos += 5;
                doc.setDrawColor(70, 70, 70);
                doc.setLineWidth(0.5);
                doc.line(leftMargin, yPos, pageWidth - leftMargin, yPos);

                yPos += 15;
                doc.setFontSize(12);
                doc.setFont('helvetica', 'bold');
                doc.setTextColor(0, 0, 150);
                doc.text('Application Status: CONFIRMED', leftMargin, yPos);
                doc.setTextColor(0, 0, 0);

                yPos += 15;
                doc.setFontSize(12);
                doc.setFont('helvetica', 'bold');
                doc.text('Personal Information', leftMargin, yPos);

                yPos += 10;
                doc.setFont('helvetica', 'normal');
                const personalInfo = [
                    ['Full Name:', `${details.lastName}, ${details.firstName} ${details.middleName}`],
                    ['Email:', details.email],
                    ['Course:', details.course],
                    ['Year Graduated:', details.yearGraduated],
                    ['High School:', details.highSchool],
                    ['Membership Type:', details.membershipType]
                ];

                personalInfo.forEach(([label, value]) => {
                    doc.setFont('helvetica', 'bold');
                    doc.text(label, leftMargin, yPos);
                    doc.setFont('helvetica', 'normal');
                    doc.text(value, leftMargin + 40, yPos);
                    yPos += 8;
                });

                yPos += 15;
                doc.setFontSize(12);
                doc.setFont('helvetica', 'bold');
                doc.text('Next Steps:', leftMargin, yPos);

                yPos += 8;
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                doc.setFillColor(248, 248, 248);
                doc.rect(leftMargin - 3, yPos - 3, pageWidth - (2 * (leftMargin - 3)), 45, 'F');

                const instructions = [
                    'Please follow these steps to complete your Alumni ID application:',
                    '1. Print this application summary',
                    '2. Visit the Bahay ng Alumni Office to make your payment',
                    '3. Once payment is processed, your status will change from "Confirmed" to "Paid"',
                    '4. You can then update your account from Guest to Alumni User',
                    '5. As an Alumni User, you will receive discounts on room reservations'
                ];

                instructions.forEach(instruction => {
                    doc.text(instruction, leftMargin, yPos);
                    yPos += 6;
                });

                yPos += 10;
                doc.setFontSize(12);
                doc.setFont('helvetica', 'bold');
                doc.text('Alumni Benefits:', leftMargin, yPos);

                yPos += 8;
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                doc.text('• Special room reservation discounts for Alumni User account holders', leftMargin, yPos);

                yPos += 15;
                const currentDate = new Date().toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                doc.text(`Date Generated: ${currentDate}`, leftMargin, yPos);

                yPos += 15;
                doc.line(leftMargin, yPos, leftMargin + 60, yPos);
                doc.text('Applicant\'s Signature', leftMargin + 15, yPos + 5);

                doc.save('Alumni_ID_Application.pdf');
                hideLoading();
                showNotification('PDF generated successfully', 'success');

            } catch (error) {
                console.error('Error generating PDF:', error);
                hideLoading();
                showNotification('Error generating PDF', 'error');
            }
        }

        document.querySelector('.unique-alumni-id-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading('Processing. Please Wait...');

            const formData = new FormData(this);
            const formValues = {};
            formData.forEach((value, key) => {
                formValues[key] = value;
            });

            const errors = validateForm(formData);
            if (errors.length > 0) {
                hideLoading();
                errors.forEach(error => showNotification(error, 'error'));
                return;
            }

            fetch('/Alumni-CvSU/user/process_alumni_id.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setTimeout(() => {
                        hideLoading();
                        if (data.success) {
                            showNotification(data.message, 'success');
                            this.style.display = 'none';
                            formValues.status = 'pending';
                            displayAlumniDetails(formValues);
                        } else {
                            showNotification(data.message || 'An error occurred', 'error');
                        }
                    }, 2000);
                })
                .catch(error => {
                    setTimeout(() => {
                        hideLoading();
                        showNotification('An error occurred. Please try again.', 'error');
                        console.error('Error:', error);
                    }, 1000);
                });
        });

        <?php if ($existing_application): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const applicationData = <?php echo json_encode($existing_application); ?>;
                displayAlumniDetails(applicationData);
            });
        <?php endif; ?>
    </script>

    <!-- Terms and Policies -->
    <script>
        function showTermsModal() {
            document.getElementById('termsModal').style.display = 'flex';
        }

        function hideTermsModal() {
            document.getElementById('termsModal').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const termsCheckbox = document.getElementById('terms-checkbox');
            const submitButton = document.getElementById('submit-button');

            if (termsCheckbox && submitButton) {
                termsCheckbox.addEventListener('change', function() {
                    submitButton.disabled = !this.checked;
                });
            }
        });
    </script>

    <!-- Reapplying -->
    <script>
        function reapplyForAlumniID() {
            showLoading('Processing your re-application...');

            fetch('/Alumni-CvSU/user/reapply_alumni_id.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showNotification('Your application has been reset. You can now re-apply.', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showNotification(data.message || 'Error re-applying for Alumni ID', 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showNotification('An error occurred. Please try again.', 'error');
                    console.error('Error:', error);
                });
        }
    </script>
</body>

</html>