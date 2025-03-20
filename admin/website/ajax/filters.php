<?php
include '../../../main_db.php';
header('Content-Type: application/json');

// Get filter parameters from request
$course = isset($_GET['course']) ? $_GET['course'] : null;
$campus = isset($_GET['campus']) ? $_GET['campus'] : null;
$startYear = isset($_GET['startYear']) ? (int)$_GET['startYear'] : null;
$endYear = isset($_GET['endYear']) ? (int)$_GET['endYear'] : null;
$employmentStatusFilter = isset($_GET['employmentStatus']) ? $_GET['employmentStatus'] : null;
$jobRelevance = isset($_GET['jobRelevance']) ? $_GET['jobRelevance'] : null;
$industry = isset($_GET['industry']) ? $_GET['industry'] : null;


// Build WHERE clause based on filters
$whereConditions = [];

if ($course) {
    $whereConditions[] = "pi.course = '" . $mysqli->real_escape_string($course) . "'";
}

if ($campus) {
    $whereConditions[] = "pi.campus = '" . $mysqli->real_escape_string($campus) . "'";
}

if ($startYear && $endYear) {
    $whereConditions[] = "eb.year_graduated BETWEEN " . $startYear . " AND " . $endYear;
} elseif ($startYear) {
    $whereConditions[] = "eb.year_graduated >= " . $startYear;
} elseif ($endYear) {
    $whereConditions[] = "eb.year_graduated <= " . $endYear;
}

if ($employmentStatusFilter) {
    $whereConditions[] = "ed.present_employment_status = '" . $mysqli->real_escape_string($employmentStatusFilter) . "'";
}

if ($jobRelevance) {
    $whereConditions[] = "je.course_related = '" . $mysqli->real_escape_string($jobRelevance) . "'";
}

if ($industry) {
    $whereConditions[] = "ed.business_line = '" . $mysqli->real_escape_string($industry) . "'";
}

// Add WHERE clause to query if we have conditions
if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
}

// Get available filters for dropdown options
$filterOptions = [
    "courses" => [],
    "campuses" => []
];

// Query for available courses
$coursesQuery = "SELECT DISTINCT course FROM personal_info WHERE course IS NOT NULL ORDER BY course";
$coursesResult = $mysqli->query($coursesQuery);
if ($coursesResult) {
    while ($row = $coursesResult->fetch_assoc()) {
        $filterOptions["courses"][] = $row['course'];
    }
}

// Query for available campuses
$campusesQuery = "SELECT DISTINCT campus FROM personal_info WHERE campus IS NOT NULL ORDER BY campus";
$campusesResult = $mysqli->query($campusesQuery);
if ($campusesResult) {
    while ($row = $campusesResult->fetch_assoc()) {
        $filterOptions["campuses"][] = $row['campus'];
    }
}

// Query for available industries/business lines
$industriesQuery = "SELECT DISTINCT business_line FROM employment_data WHERE business_line IS NOT NULL ORDER BY business_line";
$industriesResult = $mysqli->query($industriesQuery);
if ($industriesResult) {
    while ($row = $industriesResult->fetch_assoc()) {
        $filterOptions["industries"][] = $row['business_line'];
    }
}
