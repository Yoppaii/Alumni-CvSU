<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$graduationYear = isset($_GET['graduationYear']) ? $mysqli->real_escape_string($_GET['graduationYear']) : '';

$campusCondition = ($campus === '') ? "" : "AND eb.college_university = '$campus'";
$courseCondition = ($course === '') ? "" : "AND eb.degree_specialization = '$course'";
$gradYearCondition = ($graduationYear === '') ? "" : "AND eb.year_graduated = '$graduationYear'";

$query = "SELECT 
            SUM(job_finding_method = 'job_fair') AS job_fair,
            SUM(job_finding_method = 'advertisement') AS advertisement,
            SUM(job_finding_method = 'recommendation') AS recommendation,
            SUM(job_finding_method = 'walk_in') AS walk_in,
            SUM(job_finding_method = 'online') AS online
          FROM job_duration jd
          LEFT JOIN educational_background eb ON jd.user_id = eb.user_id
          WHERE 1=1 
          $campusCondition
          $courseCondition
          $gradYearCondition";

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$data = $result->fetch_assoc();
echo json_encode($data);
