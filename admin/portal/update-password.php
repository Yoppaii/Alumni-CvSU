<?php
session_start();
require_once '../../main_db.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['reset_verified']) || !isset($_SESSION['reset_email'])) {
        $response['message'] = 'Invalid reset request. Please try again.';
        echo json_encode($response);
        exit();
    }

    $email = $_SESSION['reset_email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $password, $email);

    if ($stmt->execute()) {
        unset($_SESSION['reset_otp']);
        unset($_SESSION['reset_otp_time']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_verified']);

        $response['status'] = 'success';
        $response['message'] = 'Password updated successfully!';
        $response['redirect'] = '?Cavite-State-University=login';
    } else {
        $response['message'] = 'Failed to update password. Please try again.';
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>