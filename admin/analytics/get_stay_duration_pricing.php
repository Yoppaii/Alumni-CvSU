<?php
if (!file_exists('../../main_db.php')) {
    die("Error: main_db.php not found!");
}
include '../../main_db.php';

header('Content-Type: application/json');

// Query to calculate stay duration and average price
$query = "SELECT 
            DATEDIFF(departure_date, arrival_date) AS stay_duration, 
            COUNT(*) AS total_bookings,
            AVG(price) AS avg_price
          FROM bookings 
          WHERE status = 'Completed'
          GROUP BY stay_duration
          ORDER BY stay_duration ASC";

$result = $mysqli->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
