<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$employmentStatus = isset($_GET['employmentStatus']) ? $mysqli->real_escape_string($_GET['employmentStatus']) : '';
$fromYear = isset($_GET['fromYear']) ? (int)$_GET['fromYear'] : 0;
$toYear = isset($_GET['toYear']) ? (int)$_GET['toYear'] : 0;

// Use personal_info for campus and course
$campusCondition = ($campus === '') ? "" : "AND pi.campus = '$campus'";
$courseCondition = ($course === '') ? "" : "AND pi.course = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";

// Year Condition Logic (still using educational_background for graduation year)
$yearCondition = "";
if (!empty($fromYear) && !empty($toYear)) {
    $yearCondition = "AND eb.year_graduated BETWEEN '$fromYear' AND '$toYear'";
} elseif (!empty($fromYear)) {
    $yearCondition = "AND eb.year_graduated >= '$fromYear'";
} elseif (!empty($toYear)) {
    $yearCondition = "AND eb.year_graduated <= '$toYear'";
}

error_log("Year condition: $yearCondition");

// Valid work locations
$validLocations = ["local", "abroad", "work_from_home", "hybrid"];

// Final query
$query = "SELECT 
            ed.work_place, 
            COUNT(DISTINCT ed.id) AS total_employees
          FROM employment_data ed
          LEFT JOIN educational_background eb ON ed.user_id = eb.user_id
          LEFT JOIN personal_info pi ON ed.user_id = pi.user_id
          WHERE ed.work_place IN ('" . implode("','", $validLocations) . "')
          $campusCondition
          $courseCondition
          $employmentStatusCondition
          $yearCondition
          GROUP BY ed.work_place
          ORDER BY total_employees DESC";

$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

// Human-readable labels
$locationLabels = [
    "local" => "Local",
    "abroad" => "Abroad",
    "work_from_home" => "Work From Home",
    "hybrid" => "Hybrid"
];

// Initialize output with default structure
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

echo json_encode(array_values($formattedData));
