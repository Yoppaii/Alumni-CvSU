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
    $sql = "SELECT code FROM backup_codes WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $codes = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'codes' => $codes
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}