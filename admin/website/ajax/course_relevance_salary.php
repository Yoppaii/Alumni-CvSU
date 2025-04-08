<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sanitize GET parameters
$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$employmentStatus = isset($_GET['employmentStatus']) ? $mysqli->real_escape_string($_GET['employmentStatus']) : '';
$fromYear = isset($_GET['fromYear']) && $_GET['fromYear'] !== '' ? $mysqli->real_escape_string($_GET['fromYear']) : '';
$toYear = isset($_GET['toYear']) && $_GET['toYear'] !== '' ? $mysqli->real_escape_string($_GET['toYear']) : '';

// Updated filters to use personal_info
$campusCondition = ($campus === '') ? "" : "AND pi.campus = '$campus'";
$courseCondition = ($course === '') ? "" : "AND pi.course = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";

// Year filter still from educational_background
$yearCondition = "";
if (!empty($fromYear) && !empty($toYear)) {
    $yearCondition = "AND eb.year_graduated BETWEEN '$fromYear' AND '$toYear'";
} elseif (!empty($fromYear)) {
    $yearCondition = "AND eb.year_graduated >= '$fromYear'";
} elseif (!empty($toYear)) {
    $yearCondition = "AND eb.year_graduated <= '$toYear'";
}

$query = "SELECT 
        CASE 
            WHEN jd.initial_earning = '10k_20k' THEN '<20,000'
            WHEN jd.initial_earning = '21k_30k' THEN '20,000 - 30,000'
            WHEN jd.initial_earning = '31k_40k' THEN '30,000 - 40,000'
            WHEN jd.initial_earning = 'above_40k' THEN '40,000+'
            ELSE 'Unknown' 
        END AS salary_range,
        je.course_related,
        COUNT(*) AS alumni_count
    FROM job_experience je
    LEFT JOIN job_duration jd ON jd.user_id = je.user_id
    LEFT JOIN educational_background eb ON eb.user_id = je.user_id
    LEFT JOIN employment_data ed ON ed.user_id = je.user_id
    LEFT JOIN personal_info pi ON je.user_id = pi.user_id
    WHERE (je.course_related = 'yes' OR je.course_related = 'no')
    $campusCondition
    $courseCondition
    $employmentStatusCondition
    $yearCondition
    GROUP BY salary_range, je.course_related
    ORDER BY salary_range";

// Run the query
$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

// Build the result array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
