<?php
session_start();
require_once '../../main_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    
    $query = "SELECT COUNT(*) as code_count FROM backup_codes WHERE user_id = ? AND used = 0";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $hasBackupCodes = $data['code_count'] > 0;
    
    echo json_encode([
        'success' => true,
        'hasBackupCodes' => $hasBackupCodes
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error'
    ]);
}

$mysqli->close();
?>