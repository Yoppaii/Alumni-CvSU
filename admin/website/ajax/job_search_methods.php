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

// Filters (campus & course from personal_info)
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

// Query grouping by job_finding_method
$query = "SELECT 
            jd.job_finding_method AS method,
            COUNT(DISTINCT jd.user_id) AS alumni_count
          FROM job_duration jd
          LEFT JOIN educational_background eb ON jd.user_id = eb.user_id
          LEFT JOIN employment_data ed ON ed.user_id = jd.user_id
          LEFT JOIN personal_info pi ON pi.user_id = jd.user_id
          WHERE jd.job_finding_method IS NOT NULL
          $campusCondition
          $courseCondition
          $employmentStatusCondition
          $yearCondition
          GROUP BY jd.job_finding_method
          ORDER BY FIELD(jd.job_finding_method, 'job_fair', 'advertisement', 'recommendation', 'walk_in', 'online')";

$result = $mysqli->query($query);

if (!$result) {
  error_log("SQL Error: " . $mysqli->error);
  die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

// Initialize expected job search methods with zero counts
$methods = ['job_fair', 'advertisement', 'recommendation', 'walk_in', 'online'];
$dataMap = array_fill_keys($methods, 0);
$totalAlumni = 0;

// Collect data from query result
while ($row = $result->fetch_assoc()) {
  $method = $row['method'];
  $count = (int)$row['alumni_count'];
  if (in_array($method, $methods)) {
    $dataMap[$method] = $count;
    $totalAlumni += $count;
  }
}

// Format response with percentages
$formattedData = [];
foreach ($dataMap as $method => $count) {
  $percentage = $totalAlumni > 0 ? round(($count / $totalAlumni) * 100, 1) : 0;
  $formattedData[] = [
    "method" => $method,
    "alumni_count" => $count,
    "percentage" => $percentage
  ];
}

echo json_encode($formattedData);
