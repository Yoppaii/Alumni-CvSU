<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'user/check_security_access.php';
require('main_db.php');

$sql = "SELECT id, user_id, first_name, last_name, middle_name, position, address, telephone, 
        phone_number, second_address, accompanying_persons, user_status, verified, alumni_id_card_no 
        FROM user WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: Access-Point?Cavite-State-University=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
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

    .account-card {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .account-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .account-header h1 {
        font-size: 24px;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .account-header h1 i {
        color: var(--primary-color);
    }

    .account-content {
        padding: 24px;
    }

    .account-details-section {
        max-width: auto;
        margin: 20px auto;
    }

    .detail-group {
        display: flex;
        flex-direction: column;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        background: white;
        border: 1px solid #e5e7eb;
    }

    .detail-group.verified {
        border-left: 4px solid #10B981;
    }

    .detail-group.unverified {
        border-left: 4px solid #EF4444;
    }

    .detail-group.active {
        border-left: 4px solid #3B82F6;
    }

    .detail-label {
        font-size: 14px;
        color: #6B7280;
        margin-bottom: 4px;
    }

    .detail-value {
        font-size: 16px;
        font-weight: 500;
        color: #111827;
    }

    .detail-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        background: white;
        border: 1px solid #e5e7eb;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }

    .profile-info {
        flex: 1;
    }

    .profile-name {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .profile-meta {
        color: #6B7280;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 8px;
    }

    .profile-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-badge.verified {
        background: #D1FAE5;
        color: #059669;
    }

    .status-badge.unverified {
        background: #FEE2E2;
        color: #DC2626;
    }

    .status-badge.active {
        background: #DBEAFE;
        color: #2563EB;
    }
    
    .status-badge.tracer {
        background: #FEF3C7;
        color: #B45309;
    }
    
    .status-badge.booking {
        background: #E0E7FF;
        color: #4F46E5;
    }
    
    .edit-btn {
        padding: 8px 16px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .edit-btn:hover {
        background: #1a4428;
    }

    @media (max-width: 768px) {
        body {
            padding: 10px;
        }

        .account-header,
        .account-content {
            padding: 16px;
        }

        .detail-row {
            grid-template-columns: 1fr;
        }

        .profile-header {
            flex-direction: column;
            text-align: center;
        }

        .profile-meta {
            justify-content: center;
        }
    }
</style>
<body>
    <div class="account-card">
        <div class="account-content">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <div class="profile-name">
                        <?php echo htmlspecialchars($user['first_name'] . ' ' . ($user['middle_name'] ? $user['middle_name'] . ' ' : '') . $user['last_name']); ?>
                    </div>
                    <div class="profile-meta">
                        <div class="profile-meta-item">
                            <i class="fas fa-id-card"></i> ID: <?php echo htmlspecialchars($user['alumni_id_card_no'] ?: 'Not set'); ?>
                        </div>
                        <div class="profile-meta-item">
                            <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($user['position'] ?: 'No position'); ?>
                        </div>
                        <div class="profile-meta-item">
                            <span class="status-badge <?php echo $user['verified'] ? 'verified' : 'unverified'; ?>">
                                <i class="fas <?php echo $user['verified'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                <?php echo $user['verified'] ? 'Verified' : 'Unverified'; ?>
                            </span>
                        </div>
                        <div class="profile-meta-item">
                            <span class="status-badge <?php echo $user['user_status'] === 'active' ? 'active' : 'unverified'; ?>">
                                <i class="fas <?php echo $user['user_status'] === 'active' ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                <?php echo ucfirst($user['user_status']); ?>
                            </span>
                        </div>
                        <?php if ($hasSubmittedTracer): ?>
                        <div class="profile-meta-item">
                            <span class="status-badge tracer">
                                <i class="fas fa-clipboard-check"></i>
                                Tracer Submitted
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if ($hasBooking): ?>
                        <div class="profile-meta-item">
                            <span class="status-badge booking">
                                <i class="fas fa-calendar-check"></i>
                                Has Booking
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="account-details-section">
                <h2>Personal Information</h2>
                <div class="detail-row">
                    <div class="detail-group">
                        <span class="detail-label">First Name</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['first_name']); ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Middle Name</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['middle_name'] ?: 'Not provided'); ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Last Name</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['last_name']); ?></span>
                    </div>
                </div>

                <h2>Contact Information</h2>
                <div class="detail-row">
                    <div class="detail-group">
                        <span class="detail-label">Phone Number</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['phone_number'] ?: 'Not provided'); ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Telephone</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['telephone'] ?: 'Not provided'); ?></span>
                    </div>
                </div>

                <h2>Address Information</h2>
                <div class="detail-row">
                    <div class="detail-group">
                        <span class="detail-label">Primary Address</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Secondary Address</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['second_address'] ?: 'Not provided'); ?></span>
                    </div>
                </div>

                <h2>Additional Information</h2>
                <div class="detail-row">
                    <div class="detail-group">
                        <span class="detail-label">Position</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['position'] ?: 'Not provided'); ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Accompanying Persons</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['accompanying_persons'] ?: '0'); ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Alumni ID Card Number</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['alumni_id_card_no'] ?: 'Not provided'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>