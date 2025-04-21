<?php
include '../../main_db.php';

$year = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
$month = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
$guestType = isset($_GET['guest_type']) ? $mysqli->real_escape_string($_GET['guest_type']) : '';
$roomNumber = isset($_GET['room_number']) ? $mysqli->real_escape_string($_GET['room_number']) : '';

// Apply guest type filter safely
$roomNumberCondition = ($roomNumber === '') ? "" : "AND b.room_number = '$roomNumber'";
$guestTypeCondition = ($guestType === '') ? "" : "AND u.user_status = '$guestType'";
$yearCondition = ($year === null) ? "" : "AND YEAR(b.arrival_date) = $year";
$monthCondition = ($month === null) ? "" : "AND MONTH(b.arrival_date) = $month";

$query = "SELECT 
            COUNT(*) AS total_bookings,
            SUM(CASE WHEN b.status IN ('cancelled') THEN 1 ELSE 0 END) AS cancelled,
            SUM(CASE WHEN b.status IN ('no_show') THEN 1 ELSE 0 END) AS no_show,
            SUM(CASE WHEN b.status = 'completed' THEN 1 ELSE 0 END) AS successful,
            (SUM(CASE WHEN b.status IN ('cancelled', 'no_show') THEN 1 ELSE 0 END) * 100 / COUNT(*)) AS rate
          FROM bookings b
          LEFT JOIN user u ON b.user_id = u.user_id
          WHERE b.is_archived = 0
          $yearCondition
          $monthCondition
          $guestTypeCondition
          $roomNumberCondition";

$result = $mysqli->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $rate = is_null($row['rate']) ? 0 : floatval($row['rate']); // Default 0% if no data
    $total_bookings = is_null($row['total_bookings']) ? 0 : intval($row['total_bookings']); // Default 0 if no data
    $cancelled = is_null($row['cancelled']) ? 0 : intval($row['cancelled']); // Default 0 if no data
    $no_show = is_null($row['no_show']) ? 0 : intval($row['no_show']); // Default 0 if no data
    $successful = is_null($row['successful']) ? 0 : intval($row['successful']); // Default 0 if no data
    echo json_encode(["rate" => $rate, "total_bookings" => $total_bookings, "cancelled" => $cancelled, "no_show" => $no_show, "successful" => $successful]);
} else {
    echo json_encode(["rate" => 0, "cancelled" => 0, "no_show" => 0, "successful" => 0]);
}
