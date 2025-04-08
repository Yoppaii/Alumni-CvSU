<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get and sanitize parameters
$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$employmentStatus = isset($_GET['employmentStatus']) ? $mysqli->real_escape_string($_GET['employmentStatus']) : '';
$fromYear = isset($_GET['fromYear']) && $_GET['fromYear'] !== '' ? $mysqli->real_escape_string($_GET['fromYear']) : '';
$toYear = isset($_GET['toYear']) && $_GET['toYear'] !== '' ? $mysqli->real_escape_string($_GET['toYear']) : '';

// Debug: Log received parameters
error_log("Received params - campus: $campus, course: $course, status: $employmentStatus, fromYear: $fromYear, toYear: $toYear");

// Filters (campus & course now from personal_info)
$campusCondition = ($campus === '') ? "" : "AND pi.campus = '$campus'";
$courseCondition = ($course === '') ? "" : "AND pi.course = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";

// Year filtering from educational_background
$yearCondition = "";
if (!empty($fromYear) && !empty($toYear)) {
    $yearCondition = "AND eb.year_graduated BETWEEN '$fromYear' AND '$toYear'";
} elseif (!empty($fromYear)) {
    $yearCondition = "AND eb.year_graduated >= '$fromYear'";
} elseif (!empty($toYear)) {
    $yearCondition = "AND eb.year_graduated <= '$toYear'";
}

// Debug: Log year condition
error_log("Year condition: $yearCondition");

// Query with LEFT JOIN on personal_info
$query = "SELECT 
        jd.time_to_land,
        COUNT(*) AS alumni_count
    FROM job_duration jd
    LEFT JOIN educational_background eb ON jd.user_id = eb.user_id
    LEFT JOIN employment_data ed ON ed.user_id = jd.user_id
    LEFT JOIN personal_info pi ON jd.user_id = pi.user_id
    WHERE jd.time_to_land IS NOT NULL
    $campusCondition
    $courseCondition
    $employmentStatusCondition
    $yearCondition
    GROUP BY jd.time_to_land
    ORDER BY FIELD(jd.time_to_land, 'less_than_1month', '1_6months', '7_11months', '1year_more')";

// Debug: Log final query
error_log("Final query: $query");

$result = $mysqli->query($query);
if (!$result) {
    error_log("SQL Error: " . $mysqli->error);
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

// Initialize expected categories
$categories = ['less_than_1month', '1_6months', '7_11months', '1year_more'];
$dataMap = array_fill_keys($categories, 0);
$totalAlumni = 0;

// Collect data
while ($row = $result->fetch_assoc()) {
    $dataMap[$row['time_to_land']] = (int)$row['alumni_count'];
    $totalAlumni += (int)$row['alumni_count'];
}

// Format response
$formattedData = [];
foreach ($dataMap as $timeCategory => $count) {
    $percentage = $totalAlumni > 0 ? round(($count / $totalAlumni) * 100, 1) : 0;
    $formattedData[] = [
        "time_to_land" => $timeCategory,
        "alumni_count" => $count,
        "percentage" => $percentage
    ];
}

echo json_encode($formattedData);
