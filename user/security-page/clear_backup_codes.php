<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../main_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $stmt = $mysqli->prepare("DELETE FROM backup_codes WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Backup codes cleared successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}