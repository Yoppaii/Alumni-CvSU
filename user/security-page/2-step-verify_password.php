<?php
session_start();
require_once '../../main_db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$password = $data['password'];

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
}
?>