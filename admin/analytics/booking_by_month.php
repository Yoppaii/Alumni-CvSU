<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../main_db.php';

$year = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
$guestType = isset($_GET['guest_type']) ? $mysqli->real_escape_string($_GET['guest_type']) : '';
$roomNumber = isset($_GET['room_number']) ? $mysqli->real_escape_string($_GET['room_number']) : '';

// Apply guest type filter safely
$roomNumberCondition = ($roomNumber === '') ? "" : "AND b.room_number = '$roomNumber'";
$guestTypeCondition = ($guestType === '') ? "" : "AND u.user_status = '$guestType'";
$yearCondition = ($year === null) ? "" : "AND YEAR(b.arrival_date) = $year";

$query = "SELECT 
            MONTHNAME(b.arrival_date) AS month, 
            COUNT(*) AS total
          FROM bookings b
          LEFT JOIN user u 
            ON b.user_id = u.user_id
          WHERE b.status = 'completed' 
          $yearCondition
          $guestTypeCondition
          $roomNumberCondition
          GROUP BY MONTH(b.arrival_date)
          ORDER BY MONTH(b.arrival_date)";

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error])); // Debug SQL errors
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Ensure proper JSON response
header('Content-Type: application/json');
echo json_encode($data);
