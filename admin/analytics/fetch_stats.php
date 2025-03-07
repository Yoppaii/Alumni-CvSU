<?php
include '../../main_db.php';

$filter = $_POST['filter'] ?? 'today';

// Define date conditions
switch ($filter) {
    case 'today':
        $dateCondition = "DATE(created_at) = CURDATE()";
        break;
    case 'this_week':
        $dateCondition = "YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'this_month':
        $dateCondition = "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        break;
    case 'this_year':
        $dateCondition = "YEAR(created_at) = YEAR(CURDATE())";
        break;
    case 'all_time':
        $dateCondition = "1"; // No filter (all records)
        break;
    default:
        $dateCondition = "DATE(created_at) = CURDATE()";
}

// Function to get counts
function getCount($mysqli, $table, $condition)
{
    $query = "SELECT COUNT(*) as count FROM $table WHERE $condition";
    $result = $mysqli->query($query);
    return $result->fetch_assoc()['count'];
}

// Fetch counts
$data = [
    'newUsers' => getCount($mysqli, 'users', $dateCondition),
    'totalUsers' => getCount($mysqli, 'users', '1'),
    'newBookings' => getCount($mysqli, 'bookings', $dateCondition),
    'totalBookings' => getCount($mysqli, 'bookings', '1'),
    'newCards' => getCount($mysqli, 'alumni_id_cards', $dateCondition),
    'totalCards' => getCount($mysqli, 'alumni_id_cards', '1')
];

// Send JSON response
header('Content-Type: application/json');
echo json_encode($data);
