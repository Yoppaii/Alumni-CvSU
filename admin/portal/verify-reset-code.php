<?php
session_start();
require_once '../../main_db.php';

ob_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

$response = [
    'status' => 'error',
    'message' => '',
    'redirect' => ''
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_SESSION['reset_otp']) || 
        !isset($_SESSION['reset_otp_time']) || 
        !isset($_SESSION['reset_email'])) {
        throw new Exception('No reset request found. Please try again.');
    }

    if (!isset($_POST['otp'])) {
        throw new Exception('No OTP provided');
    }

    $submitted_otp = trim($_POST['otp']);
    $stored_otp = trim($_SESSION['reset_otp']);

    if (time() - $_SESSION['reset_otp_time'] > 600) {
        unset($_SESSION['reset_otp']);
        unset($_SESSION['reset_otp_time']);
        unset($_SESSION['reset_email']);
        throw new Exception('Verification code has expired. Please request a new one.');
    }

    if ($submitted_otp === $stored_otp) {
        $_SESSION['reset_verified'] = true;
        $response['status'] = 'success';
        $response['message'] = 'Code verified successfully!';
        $response['redirect'] = '?Cavite-State-University=new-password';
    } else {
        throw new Exception('Invalid verification code.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Reset verification error: " . $e->getMessage());
}

while (ob_get_level()) {
    ob_end_clean();
}

echo json_encode($response);
exit();
?>