<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../main_db.php';

$year = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
$month = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
$guestType = isset($_GET['guest_type']) ? $mysqli->real_escape_string($_GET['guest_type']) : '';
$roomNumber = isset($_GET['room_number']) ? $mysqli->real_escape_string($_GET['room_number']) : '';

// Apply filters 
$yearCondition = ($year === null) ? "" : "AND YEAR(b.arrival_date) = $year";
$monthCondition = ($month === null) ? "" : "AND MONTH(b.arrival_date) = $month";
$guestTypeCondition = ($guestType === '') ? "" : "AND u.user_status = '$guestType'";
$roomNumberCondition = ($roomNumber === '') ? "" : "AND b.room_number = '$roomNumber'";

$query = "SELECT 
            HOUR(b.created_at) AS hour, 
            COUNT(*) AS total
          FROM bookings b
          LEFT JOIN user u 
            ON b.user_id = u.user_id
          WHERE b.status = 'completed'
          $yearCondition
          $monthCondition
          $guestTypeCondition
          $roomNumberCondition
          GROUP BY HOUR(b.created_at)
          ORDER BY hour";

$result = $mysqli->query($query);

if (!$result) {
  die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

// JSON response
header('Content-Type: application/json');
echo json_encode($data);
