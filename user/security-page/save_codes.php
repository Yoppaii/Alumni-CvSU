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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$codes = $data['codes'] ?? [];

if (empty($codes) || count($codes) !== 12) {
    echo json_encode(['success' => false, 'message' => 'Invalid codes provided']);
    exit;
}

try {
    $mysqli->begin_transaction();
    $delete_sql = "DELETE FROM backup_codes WHERE user_id = ? AND used = 0";
    $delete_stmt = $mysqli->prepare($delete_sql);
    $delete_stmt->bind_param("i", $_SESSION['user_id']);
    $delete_stmt->execute();
    $insert_sql = "INSERT INTO backup_codes (user_id, code, created_at) VALUES (?, ?, NOW())";
    $insert_stmt = $mysqli->prepare($insert_sql);
    
    foreach ($codes as $code) {
        $insert_stmt->bind_param("is", $_SESSION['user_id'], $code);
        $insert_stmt->execute();
    }

    $mysqli->commit();
    
    echo json_encode(['success' => true, 'message' => 'Backup codes saved successfully']);
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}