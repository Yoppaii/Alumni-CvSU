<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$employmentStatus = isset($_GET['employmentStatus']) ? $mysqli->real_escape_string($_GET['employmentStatus']) : '';

$campusCondition = ($campus === '') ? "" : "AND eb.college_university = '$campus'";
$courseCondition = ($course === '') ? "" : "AND eb.degree_specialization = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";

$query = "SELECT 
        jd.time_to_land,
        COUNT(*) AS alumni_count
    FROM job_duration jd
    LEFT JOIN educational_background eb 
        ON jd.user_id = eb.user_id
    LEFT JOIN employment_data ed 
        ON ed.user_id = eb.user_id
    WHERE jd.time_to_land IS NOT NULL
    $campusCondition
    $courseCondition
    $employmentStatusCondition
    GROUP BY jd.time_to_land
    ORDER BY FIELD(jd.time_to_land, 'less_than_1month', '1_6months', '7_11months', '1year_more')";

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$categories = ['less_than_1month', '1_6months', '7_11months', '1year_more'];
$dataMap = array_fill_keys($categories, 0);
$totalAlumni = 0;

while ($row = $result->fetch_assoc()) {
    $dataMap[$row['time_to_land']] = (int)$row['alumni_count'];
    $totalAlumni += (int)$row['alumni_count'];
}

// Calculate percentages
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
