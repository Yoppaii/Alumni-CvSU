<?php
session_start();
require_once '../../main_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$submitted_otp = $_POST['otp'] ?? '';
$stored_otp = $_SESSION['remove_otp'] ?? '';
$otp_expiry = $_SESSION['remove_otp_expiry'] ?? 0;

try {
    if (empty($stored_otp) || time() > $otp_expiry) {
        echo json_encode(['success' => false, 'error' => 'OTP has expired']);
        exit;
    }

    if ($submitted_otp !== $stored_otp) {
        echo json_encode(['success' => false, 'error' => 'Invalid OTP']);
        exit;
    }

    $stmt = $mysqli->prepare("DELETE FROM recovery_emails WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        unset($_SESSION['remove_otp']);
        unset($_SESSION['remove_otp_expiry']);
        
        echo json_encode(['success' => true, 'message' => 'Recovery email removed successfully']);
    } else {
        throw new Exception("Failed to remove recovery email");
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to verify OTP: ' . $e->getMessage()]);
}
?>