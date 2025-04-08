<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get filter parameters
$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$employmentStatus = isset($_GET['employmentStatus']) ? $mysqli->real_escape_string($_GET['employmentStatus']) : '';
$fromYear = isset($_GET['fromYear']) && $_GET['fromYear'] !== '' ? $mysqli->real_escape_string($_GET['fromYear']) : '';
$toYear = isset($_GET['toYear']) && $_GET['toYear'] !== '' ? $mysqli->real_escape_string($_GET['toYear']) : '';

// Build filter conditions
$campusCondition = ($campus === '') ? "" : "AND pi.campus = '$campus'";
$courseCondition = ($course === '') ? "" : "AND pi.course = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";

$yearCondition = "";
if (!empty($fromYear) && !empty($toYear)) {
    $yearCondition = "AND eb.year_graduated BETWEEN '$fromYear' AND '$toYear'";
} elseif (!empty($fromYear)) {
    $yearCondition = "AND eb.year_graduated >= '$fromYear'";
} elseif (!empty($toYear)) {
    $yearCondition = "AND eb.year_graduated <= '$toYear'";
}

// If no campus is specified, get total per campus
if ($campus === '') {
    $query = "SELECT pi.campus, COUNT(DISTINCT eb.user_id) AS total_graduates
              FROM personal_info pi
              LEFT JOIN educational_background eb ON eb.user_id = pi.user_id
              LEFT JOIN employment_data ed ON ed.user_id = pi.user_id
              WHERE pi.campus != ''
              $courseCondition
              $employmentStatusCondition
              $yearCondition
              GROUP BY pi.campus
              ORDER BY total_graduates DESC";
} else {
    // If campus is specified, return only that campus total
    $query = "SELECT pi.campus, COUNT(DISTINCT eb.user_id) AS total_graduates
              FROM personal_info pi
              LEFT JOIN educational_background eb ON eb.user_id = pi.user_id
              LEFT JOIN employment_data ed ON ed.user_id = pi.user_id
              WHERE pi.campus = '$campus'
              $courseCondition
              $employmentStatusCondition
              $yearCondition
              GROUP BY pi.campus";
}

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$campusData = [];
while ($row = $result->fetch_assoc()) {
    $campusData[] = [
        'campus' => $row['campus'],
        'total_graduates' => (int) $row['total_graduates']
    ];
}

echo json_encode($campusData);
