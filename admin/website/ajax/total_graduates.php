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
$campusCondition = ($campus === '') ? "" : "AND eb.college_university = '$campus'";
$courseCondition = ($course === '') ? "" : "AND eb.degree_specialization = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";

$yearCondition = "";
if (!empty($fromYear) && !empty($toYear)) {
    // Both fromYear and toYear are provided - filter within range
    $yearCondition = "AND eb.year_graduated BETWEEN '$fromYear' AND '$toYear'";
} elseif (!empty($fromYear)) {
    // Only fromYear is provided - filter from that year onward
    $yearCondition = "AND eb.year_graduated >= '$fromYear'";
} elseif (!empty($toYear)) {
    // Only toYear is provided - filter up to that year
    $yearCondition = "AND eb.year_graduated <= '$toYear'";
}

// If no campus is specified, get total per campus
if ($campus === '') {
    $query = "SELECT eb.college_university AS campus, COUNT(DISTINCT eb.user_id) AS total_graduates
              FROM educational_background eb
              LEFT JOIN employment_data ed ON ed.user_id = eb.user_id
              WHERE eb.college_university != ''
              $courseCondition
              $employmentStatusCondition
              $yearCondition
              GROUP BY eb.college_university
              ORDER BY total_graduates DESC";
} else {
    // If campus is specified, return only that campus total
    $query = "SELECT eb.college_university AS campus, COUNT(DISTINCT eb.user_id) AS total_graduates
              FROM educational_background eb
              LEFT JOIN employment_data ed ON ed.user_id = eb.user_id
              WHERE eb.college_university = '$campus'
              $courseCondition
              $employmentStatusCondition
              $yearCondition
              GROUP BY eb.college_university";
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
