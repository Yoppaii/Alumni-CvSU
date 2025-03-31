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

// Debugging: Log constructed year condition
error_log("Year condition: $yearCondition");
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
        $yearCondition
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
