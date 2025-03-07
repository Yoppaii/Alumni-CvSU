<?php
session_start();
require_once '../../main_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['status']) || !in_array($input['status'], [0, 1], true)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status value']);
    exit;
}

$status = (int)$input['status'];

try {
    $stmt = $mysqli->prepare("UPDATE users SET two_factor_auth = ? WHERE id = ?");
    if ($stmt === false) {
        throw new Exception($mysqli->error);
    }
    
    $stmt->bind_param("ii", $status, $_SESSION['user_id']);
    
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes made']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>