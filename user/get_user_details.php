<?php
require_once '../main_db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, middle_name, position, address, telephone, 
        phone_number, second_address, accompanying_persons, user_status, verified 
        FROM user WHERE user_id = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($user);
?>