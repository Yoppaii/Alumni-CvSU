<?php
session_start();
require_once('../main_db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$stmt = $mysqli->prepare("SELECT two_factor_auth FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

echo json_encode([
    'enabled' => $user['two_factor_auth'] == 1
]);
?>