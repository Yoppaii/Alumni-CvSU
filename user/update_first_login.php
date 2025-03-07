<?php
session_start();
require_once '../main_db.php';

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $stmt = $mysqli->prepare("UPDATE users SET first_login = 0 WHERE id = ? AND first_login = 1");
    
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => $mysqli->error]);
        exit;
    }
    
    $stmt->bind_param("i", $user_id);
    $result = $stmt->execute();

    if ($result && $stmt->affected_rows > 0) {
        $_SESSION['first_login'] = 0;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No update needed or error occurred']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'No user ID provided']);
}