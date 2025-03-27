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

$query = "SELECT ed.employment_status, COUNT(ed.user_id) AS total 
            FROM employment_data ed 
            LEFT JOIN educational_background eb 
                ON ed.user_id = eb.user_id
            WHERE 1=1
            $campusCondition
            $courseCondition
            $employmentStatusCondition
            GROUP BY ed.employment_status";

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$employmentData = ["employed" => 0, "unemployed" => 0];

while ($row = $result->fetch_assoc()) {
    if (strtolower($row['employment_status']) === 'yes') {
        $employmentData["employed"] = (int) $row['total'];
    } elseif (strtolower($row['employment_status']) === 'no') {
        $employmentData["unemployed"] = (int) $row['total'];
    }
}

echo json_encode($employmentData);
