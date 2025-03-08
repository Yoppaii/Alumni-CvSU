<?php
if (!file_exists('../../main_db.php')) {
    die("Error: main_db.php not found!");
}
include '../../main_db.php';

header('Content-Type: application/json');

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

$query = "SELECT 
            status, 
            COUNT(*) AS total_bookings
          FROM bookings 
          WHERE YEAR(created_at) = ?
          GROUP BY status
          ORDER BY total_bookings DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
