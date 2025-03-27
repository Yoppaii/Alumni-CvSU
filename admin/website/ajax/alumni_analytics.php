<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../../main_db.php';
header('Content-Type: application/json');

// Get filter parameters
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$startYear = isset($_GET['startYear']) ? intval($_GET['startYear']) : null;
$endYear = isset($_GET['endYear']) ? intval($_GET['endYear']) : null;
$employmentStatus = isset($_GET['employmentStatus']) ? $mysqli->real_escape_string($_GET['employmentStatus']) : '';
$relevance = isset($_GET['relevance']) ? $mysqli->real_escape_string($_GET['relevance']) : '';
$business = isset($_GET['business']) ? $mysqli->real_escape_string($_GET['business']) : '';
$timeToLand = isset($_GET['timeToLand']) ? $mysqli->real_escape_string($_GET['timeToLand']) : '';
$jobFindingMethod = isset($_GET['jobFindingMethod']) ? $mysqli->real_escape_string($_GET['jobFindingMethod']) : '';

// Apply filters
$courseCondition = ($course === '') ? "" : "AND eb.degree_specialization = '$course'";
$campusCondition = ($campus === '') ? "" : "AND eb.college_university = '$campus'";
$startYearCondition = ($startYear === null) ? "" : "AND eb.year_graduated >= $startYear";
$endYearCondition = ($endYear === null) ? "" : "AND eb.year_graduated <= $endYear";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";
$relevanceCondition = ($relevance === '') ? "" : "AND je.course_related = '$relevance'";
$businessCondition = ($business === '') ? "" : "AND ed.business_line = '$business'";
$timeToLandCondition = ($timeToLand === '') ? "" : "AND jd.time_to_land = '$timeToLand'";
$jobFindingMethodCondition = ($jobFindingMethod === '') ? "" : "AND jd.job_finding_method = '$jobFindingMethod'";

$query = "SELECT 
            eb.year_graduated, 
            COUNT(DISTINCT eb.user_id) AS total_count 
          FROM educational_background eb 
          LEFT JOIN employment_data ed ON eb.user_id = ed.user_id 
          LEFT JOIN job_duration jd ON eb.user_id = jd.user_id
          LEFT JOIN job_experience je ON eb.user_id = je.user_id
          WHERE 1=1 
          $courseCondition
          $campusCondition
          $startYearCondition
          $endYearCondition
          $employmentStatusCondition
          $relevanceCondition
          $businessCondition
          $timeToLandCondition
          $jobFindingMethodCondition
          GROUP BY eb.year_graduated
          ORDER BY eb.year_graduated";

$result = $mysqli->query($query);
if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
