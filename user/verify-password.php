<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('../main_db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$section = isset($_POST['section']) ? trim($_POST['section']) : '';

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

try {
    $stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Database error: " . $mysqli->error);
    }

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['security_verified'] = true;
        $_SESSION['security_verified_time'] = time();
        $_SESSION['verified_section'] = $section;
        
        echo json_encode([
            'success' => true,
            'message' => 'Password verified successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Incorrect password'
        ]);
    }

} catch (Exception $e) {
    error_log("Error in verify-password.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while verifying the password'
    ]);
}

$mysqli->close();
?>