<?php
if (!file_exists('../../main_db.php')) {
    die("Error: main_db.php not found!");
}
include '../../main_db.php';

header('Content-Type: application/json');

$query = "SELECT 
            room_number, 
            COUNT(*) AS total_bookings
          FROM bookings 
          WHERE status = 'Completed'
          GROUP BY room_number
          ORDER BY total_bookings DESC";

$result = $mysqli->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
