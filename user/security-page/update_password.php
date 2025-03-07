<?php
session_start();
header('Content-Type: application/json');
require '../../main_db.php';

function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    }
    
    return empty($errors) ? true : $errors;
}

function checkWaitingPeriod($userId, $mysqli) {
    $sql = "SELECT change_date FROM password_history 
            WHERE user_id = ? 
            ORDER BY change_date DESC 
            LIMIT 1";
            
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error: ' . $mysqli->error);
    }
    
    $stmt->bind_param('i', $userId);
    if (!$stmt->execute()) {
        throw new Exception('Failed to check waiting period: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $lastChange = $result->fetch_assoc();
    $stmt->close();
    
    if ($lastChange) {
        $lastChangeDate = strtotime($lastChange['change_date']);
        $waitingPeriod = 7 * 24 * 60 * 60; 
        $timeRemaining = $lastChangeDate + $waitingPeriod - time();
        
        if ($timeRemaining > 0) {
            $daysRemaining = ceil($timeRemaining / (24 * 60 * 60));
            throw new Exception("Please wait {$daysRemaining} more days before changing your password again");
        }
    }
    
    return true;
}

function cleanupPasswordHistory($userId, $mysqli, $limit = 5) {
    $sql = "DELETE FROM password_history 
            WHERE user_id = ? 
            AND id NOT IN (
                SELECT id FROM (
                    SELECT id 
                    FROM password_history 
                    WHERE user_id = ? 
                    ORDER BY change_date DESC 
                    LIMIT ?
                ) temp
            )";
    
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error during cleanup: ' . $mysqli->error);
    }
    
    $stmt->bind_param('iii', $userId, $userId, $limit);
    if (!$stmt->execute()) {
        throw new Exception('Failed to cleanup password history: ' . $stmt->error);
    }
    $stmt->close();
}

function logPasswordChange($userId, $mysqli) {
    $sql = "INSERT INTO password_history (user_id, change_date, action) VALUES (?, NOW(), 'Password changed')";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error: ' . $mysqli->error);
    }
    $stmt->bind_param('i', $userId);
    if (!$stmt->execute()) {
        throw new Exception('Failed to log password change: ' . $stmt->error);
    }
    $stmt->close();

    cleanupPasswordHistory($userId, $mysqli);
}

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Please log in to change your password');
    }

    $userId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    checkWaitingPeriod($userId, $mysqli);

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword)) {
        throw new Exception('All fields are required');
    }

    $passwordValidation = validatePassword($newPassword);
    if ($passwordValidation !== true) {
        throw new Exception('Password requirements not met: ' . implode(', ', $passwordValidation));
    }

    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error: ' . $mysqli->error);
    }
    
    $stmt->bind_param('i', $userId);
    if (!$stmt->execute()) {
        throw new Exception('Failed to verify current password: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception('User not found');
    }

    if (!password_verify($currentPassword, $user['password'])) {
        throw new Exception('Current password is incorrect');
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error: ' . $mysqli->error);
    }
    
    $stmt->bind_param('si', $hashedPassword, $userId);
    if (!$stmt->execute()) {
        throw new Exception('Failed to update password: ' . $stmt->error);
    }
    $stmt->close();

    logPasswordChange($userId, $mysqli);

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}