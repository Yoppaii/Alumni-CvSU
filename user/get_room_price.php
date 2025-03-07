<?php
require 'main_db.php';

header('Content-Type: application/json');

$occupancy = isset($_GET['occupancy']) ? (int)$_GET['occupancy'] : 0;

if (!$occupancy) {
    echo json_encode(['success' => false, 'error' => 'Invalid occupancy']);
    exit;
}

$query = "SELECT price FROM room_price WHERE occupancy = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $occupancy);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'price' => $row['price']]);
} else {
    echo json_encode(['success' => false, 'error' => 'Price not found']);
}

$stmt->close();
$mysqli->close();
?>