<?php
if (!file_exists('../../main_db.php')) {
    die("Error: main_db.php not found!");
}
include '../../main_db.php';

header('Content-Type: application/json');

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$query = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS month, 
            COUNT(*) AS total_bookings, 
            SUM(price) AS total_revenue
          FROM bookings 
          WHERE status = 'Completed' 
          AND YEAR(created_at) = ?
          GROUP BY month
          ORDER BY month ASC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
