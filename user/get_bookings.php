<?php
require_once '../main_db.php';
header('Content-Type: application/json');

$query = "SELECT room_number, arrival_date FROM bookings WHERE status != 'cancelled'";
$result = $mysqli->query($query);

$bookings = [];
if ($result) {
    while ($booking = $result->fetch_assoc()) {
        $bookings[] = $booking;
    }
}

echo json_encode(['success' => true, 'bookings' => $bookings]);
?>