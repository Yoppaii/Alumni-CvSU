<?php
require('main_db.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['verified' => false]);
    exit();
}

$sql = "SELECT verified FROM user WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode(['verified' => ($user && $user['verified'] == 1)]);
?>