<?php
if (!file_exists('../../main_db.php')) {
    die("Error: main_db.php not found!");
}
include '../../main_db.php';

header('Content-Type: application/json');

$query = "SELECT 
            DATE_FORMAT(arrival_date, '%Y-%m') AS month, 
            COUNT(*) AS total_bookings, 
            SUM(price) AS total_revenue
          FROM bookings 
          WHERE status = 'Completed'
          GROUP BY month
          ORDER BY month ASC";

$result = $mysqli->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
