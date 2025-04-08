<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$employmentStatus = isset($_GET['employmentStatus']) ? $mysqli->real_escape_string($_GET['employmentStatus']) : '';

$fromYear = isset($_GET['fromYear']) && $_GET['fromYear'] !== '' ? $mysqli->real_escape_string($_GET['fromYear']) : '';
$toYear = isset($_GET['toYear']) && $_GET['toYear'] !== '' ? $mysqli->real_escape_string($_GET['toYear']) : '';

// Filter conditions using personal_info
$campusCondition = ($campus === '') ? "" : "AND pi.campus = '$campus'";
$courseCondition = ($course === '') ? "" : "AND pi.course = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";

// Year Condition Logic
$yearCondition = "";
if (!empty($fromYear) && !empty($toYear)) {
  $yearCondition = "AND eb.year_graduated BETWEEN '$fromYear' AND '$toYear'";
} elseif (!empty($fromYear)) {
  $yearCondition = "AND eb.year_graduated >= '$fromYear'";
} elseif (!empty($toYear)) {
  $yearCondition = "AND eb.year_graduated <= '$toYear'";
}

$query = "SELECT 
            SUM(job_finding_method = 'job_fair') AS job_fair,
            SUM(job_finding_method = 'advertisement') AS advertisement,
            SUM(job_finding_method = 'recommendation') AS recommendation,
            SUM(job_finding_method = 'walk_in') AS walk_in,
            SUM(job_finding_method = 'online') AS online
          FROM job_duration jd
          LEFT JOIN educational_background eb ON jd.user_id = eb.user_id
          LEFT JOIN employment_data ed ON ed.user_id = jd.user_id
          LEFT JOIN personal_info pi ON pi.user_id = jd.user_id
          WHERE 1=1 
          $campusCondition
          $courseCondition
          $employmentStatusCondition
          $yearCondition";

$result = $mysqli->query($query);

if (!$result) {
  die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$data = $result->fetch_assoc();
echo json_encode($data);
