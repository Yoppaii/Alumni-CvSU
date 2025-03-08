<?php
if (!file_exists('../../main_db.php')) {
    die("Error: main_db.php not found!");
}
include '../../main_db.php';

header('Content-Type: application/json');

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

$query = "SELECT 
            room_number, 
            COUNT(*) AS total_bookings
          FROM bookings 
          WHERE status = 'Completed' AND YEAR(created_at) = ?
          GROUP BY room_number
          ORDER BY total_bookings DESC";

$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    die(json_encode(["error" => "Query preparation failed."]));
}

$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
