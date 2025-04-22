<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get and sanitize parameters
$campus = isset($_GET['campus']) ? $mysqli->real_escape_string($_GET['campus']) : '';
$course = isset($_GET['course']) ? $mysqli->real_escape_string($_GET['course']) : '';
$employmentStatus = isset($_GET['employmentStatus']) ? $mysqli->real_escape_string($_GET['employmentStatus']) : '';
$fromYear = isset($_GET['fromYear']) && $_GET['fromYear'] !== '' ? $mysqli->real_escape_string($_GET['fromYear']) : '';
$toYear = isset($_GET['toYear']) && $_GET['toYear'] !== '' ? $mysqli->real_escape_string($_GET['toYear']) : '';

// Debugging: Log received parameters
error_log("Received params - campus: $campus, course: $course, status: $employmentStatus, fromYear: $fromYear, toYear: $toYear");

// Update filters using personal_info
$campusCondition = ($campus === '') ? "" : "AND pi.campus = '$campus'";
$courseCondition = ($course === '') ? "" : "AND pi.course = '$course'";
$employmentStatusCondition = ($employmentStatus === '') ? "" : "AND ed.present_employment_status = '$employmentStatus'";

// Year Condition Logic
$yearCondition = "";
if (!empty($fromYear) && !empty($toYear)) {
    $yearCondition = "AND eb.year_graduated BETWEEN '$fromYear' AND '$toYear'";
} elseif (!empty($fromYear)) {
    $yearCondition = "AND eb.year_graduated >= '$fromYear'";
} elseif (!empty($toYear)) {
    $yearCondition = "AND eb.year_graduated <= '$toYear'";
}

// Debugging: Log constructed year condition
error_log("Year condition: $yearCondition");

// Build final query
$query = "SELECT ed.employment_status, COUNT(ed.user_id) AS total 
          FROM employment_data ed 
          LEFT JOIN educational_background eb ON ed.user_id = eb.user_id
          LEFT JOIN personal_info pi ON ed.user_id = pi.user_id
          WHERE 1=1
          $campusCondition
          $courseCondition
          $employmentStatusCondition
          $yearCondition
          GROUP BY ed.employment_status";

// Debugging: Log final query
error_log("Final query: $query");

// Execute query
$result = $mysqli->query($query);

// Handle query errors
if (!$result) {
    error_log("SQL Error: " . $mysqli->error);
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

// Initialize employment data
$employmentData = ["employed" => 0, "unemployed" => 0];

// Process results
while ($row = $result->fetch_assoc()) {
    if (strtolower($row['employment_status']) === 'yes') {
        $employmentData["employed"] = (int) $row['total'];
    } elseif (strtolower($row['employment_status']) === 'no') {
        $employmentData["unemployed"] = (int) $row['total'];
    }
}

// Output JSON response
echo json_encode($employmentData);
