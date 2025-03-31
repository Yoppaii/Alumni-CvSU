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

// Define possible work locations
$validLocations = ["local", "abroad", "work_from_home", "hybrid"];

// Fetch data grouped by work location
$query = "SELECT 
            ed.work_place, 
            COUNT(*) AS total_employees
        FROM employment_data ed
        LEFT JOIN educational_background eb ON ed.user_id = eb.user_id
        WHERE ed.work_place IN ('" . implode("','", $validLocations) . "')
        $campusCondition
        $courseCondition
        $employmentStatusCondition
        GROUP BY ed.work_place
        ORDER BY total_employees DESC";

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

// Map work_place values to readable labels
$locationLabels = [
    "local" => "Local",
    "abroad" => "Abroad",
    "work_from_home" => "Work From Home",
    "hybrid" => "Hybrid"
];

// Initialize data array with all locations to ensure order
$formattedData = array_fill_keys(array_keys($locationLabels), ["location" => "", "total_employees" => 0]);

while ($row = $result->fetch_assoc()) {
    $workPlaceKey = $row['work_place'];
    if (isset($locationLabels[$workPlaceKey])) {
        $formattedData[$workPlaceKey] = [
            "location" => $locationLabels[$workPlaceKey],
            "total_employees" => (int)$row['total_employees']
        ];
    }
}

// Return JSON response with sorted data
echo json_encode(array_values($formattedData));
