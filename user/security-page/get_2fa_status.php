<?php
session_start();
require_once '../../main_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit;
}

$stmt = $mysqli->prepare("SELECT two_factor_auth FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'status' => 'success',
        'two_factor_auth' => (int)$row['two_factor_auth']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$stmt->close();
$mysqli->close();
?>