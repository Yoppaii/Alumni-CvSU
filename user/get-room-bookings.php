<?php
require_once '../main_db.php';
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$roomId = isset($input['room_id']) ? intval($input['room_id']) : 0;

if (!$roomId) {
    echo json_encode(['error' => 'Invalid room ID']);
    exit;
}

$query = "SELECT 
            DATE(arrival_date) as arrival_date,
            DATE(departure_date) as departure_date,
            CONCAT(departure_date, ' ', departure_time) as departure_datetime
          FROM bookings 
          WHERE room_number = ? 
          AND status != 'cancelled'
          AND (
              (arrival_date <= CURRENT_DATE() AND departure_date >= CURRENT_DATE())
              OR arrival_date >= CURRENT_DATE()
          )";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();

$bookedDates = [];
$bookingTimes = [];

while ($row = $result->fetch_assoc()) {
    $current = strtotime($row['arrival_date']);
    $end = strtotime($row['departure_date']);

    while ($current < $end) {
        $bookedDates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }

    $bookingTimes[] = [
        'date' => $row['departure_date'],
        'departure_time' => $row['departure_datetime']
    ];
}

echo json_encode([
    'bookedDates' => array_values(array_unique($bookedDates)),
    'bookingTimes' => $bookingTimes
]);

$stmt->close();
$mysqli->close();
