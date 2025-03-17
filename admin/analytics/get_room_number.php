<?php
include '../../main_db.php';
header('Content-Type: application/json');

$query = "SELECT DISTINCT room_number FROM bookings WHERE room_number IS NOT NULL ORDER BY room_number";
$result = $mysqli->query($query);

$rooms = [];

while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode($rooms);
?>
