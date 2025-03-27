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

// Fetch data grouped by work location (work_place)
$query = "SELECT 
        ed.work_place AS location,
        COUNT(*) AS total_employees
    FROM employment_data ed
    LEFT JOIN educational_background eb 
        ON ed.user_id = eb.user_id
    WHERE 1=1
    $campusCondition
    $courseCondition
    $employmentStatusCondition
    GROUP BY ed.work_place
    ORDER BY total_employees DESC";

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

// Prepare the data array
$formattedData = [];
while ($row = $result->fetch_assoc()) {
    $formattedData[] = [
        "location" => $row['location'],
        "total_employees" => (int)$row['total_employees']
    ];
}

// Return JSON response
echo json_encode($formattedData);
