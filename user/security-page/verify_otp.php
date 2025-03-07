<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../main_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (!isset($_POST['otp']) || !isset($_POST['recovery_email'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}


if (!isset($_SESSION['otp']) || 
    $_SESSION['otp']['code'] !== $_POST['otp'] || 
    $_SESSION['otp']['email'] !== $_POST['recovery_email'] ||
    time() > $_SESSION['otp']['expires']) {
    echo json_encode(['error' => 'Invalid or expired OTP']);
    exit;
}

try {
    $recoveryEmail = filter_var($_POST['recovery_email'], FILTER_VALIDATE_EMAIL);
    
    $checkStmt = $mysqli->prepare("SELECT id FROM recovery_emails WHERE user_id = ?");
    $checkStmt->bind_param("i", $_SESSION['user_id']);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt = $mysqli->prepare("UPDATE recovery_emails SET recovery_email = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->bind_param("si", $recoveryEmail, $_SESSION['user_id']);
    } else {
        $stmt = $mysqli->prepare("INSERT INTO recovery_emails (user_id, recovery_email, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $_SESSION['user_id'], $recoveryEmail);
    }
    
    if ($stmt->execute()) {
        unset($_SESSION['otp']);
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Failed to save recovery email");
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to save recovery email: ' . $e->getMessage()]);
}
?>