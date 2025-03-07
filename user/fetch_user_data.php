<?php
session_start();
require_once '../main_db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT user_id, first_name, middle_name, last_name, position, address, telephone, 
        phone_number, email, user_status FROM user WHERE user_id = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'user' => $row]);
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
}

$stmt->close();
$mysqli->close();
?>