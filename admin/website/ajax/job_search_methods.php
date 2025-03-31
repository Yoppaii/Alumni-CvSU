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


$campusCondition = ($campus === '') ? "" : "AND eb.college_university = '$campus'";
$courseCondition = ($course === '') ? "" : "AND eb.degree_specialization = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";


// Year Condition Logic
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

$query = "SELECT 
            SUM(job_finding_method = 'job_fair') AS job_fair,
            SUM(job_finding_method = 'advertisement') AS advertisement,
            SUM(job_finding_method = 'recommendation') AS recommendation,
            SUM(job_finding_method = 'walk_in') AS walk_in,
            SUM(job_finding_method = 'online') AS online
          FROM job_duration jd
          LEFT JOIN educational_background eb 
            ON jd.user_id = eb.user_id
          LEFT JOIN employment_data ed 
            ON ed.user_id = eb.user_id
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
