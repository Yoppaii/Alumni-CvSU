<?php
include '../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$year = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
$month = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
$guestType = isset($_GET['guest_type']) ? $mysqli->real_escape_string($_GET['guest_type']) : '';
$roomNumber = isset($_GET['room_number']) ? $mysqli->real_escape_string($_GET['room_number']) : '';

// Apply guest type filter safely
$roomNumberCondition = ($roomNumber === '') ? "" : "AND b.room_number = '$roomNumber'";
$guestTypeCondition = ($guestType === '') ? "" : "AND u.user_status = '$guestType'";
$yearCondition = ($year === null) ? "" : "AND YEAR(b.arrival_date) = $year";
$monthCondition = ($month === null) ? "" : "AND MONTH(b.arrival_date) = $month";

$query = "WITH weekdays AS (
            SELECT 'Monday' AS booking_day
            UNION SELECT 'Tuesday'
            UNION SELECT 'Wednesday'
            UNION SELECT 'Thursday'
            UNION SELECT 'Friday'
            UNION SELECT 'Saturday'
            UNION SELECT 'Sunday'
        )
        SELECT 
            w.booking_day, 
            COALESCE(COUNT(b.id), 0) AS total
        FROM weekdays w
        LEFT JOIN bookings b 
            ON DAYNAME(b.arrival_date) = w.booking_day
            AND b.status = 'completed'
        LEFT JOIN user u 
            ON b.user_id = u.user_id
        WHERE b.is_archived = 0
        $yearCondition
        $monthCondition
        $guestTypeCondition
        $roomNumberCondition
        GROUP BY w.booking_day
        ORDER BY FIELD(w.booking_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error])); // Debug SQL errors
}

// Ensure all days are included, even if no bookings exist
$daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$data = array_fill_keys($daysOfWeek, 0);

// Fill data from SQL results
while ($row = $result->fetch_assoc()) {
    $data[$row['booking_day']] = (int) $row['total'];
}

// Convert back to JSON-friendly format
$output = [];
foreach ($data as $day => $total) {
    $output[] = ["booking_day" => $day, "total" => $total];
}

echo json_encode($output);
