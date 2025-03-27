<?php
include '../../../main_db.php';
header('Content-Type: application/json');

// Get filter parameters from request
$course = isset($_GET['course']) ? $_GET['course'] : null;
$campus = isset($_GET['campus']) ? $_GET['campus'] : null;
$startYear = isset($_GET['startYear']) ? (int)$_GET['startYear'] : null;
$endYear = isset($_GET['endYear']) ? (int)$_GET['endYear'] : null;
$employmentStatus = isset($_GET['employmentStatus']) ? $_GET['employmentStatus'] : null;
$relevance = isset($_GET['relevance']) ? $_GET['relevance'] : null;
$business = isset($_GET['business']) ? $_GET['business'] : null;
$timeToLand = isset($_GET['timeToLand']) ? $_GET['timeToLand'] : null;
$jobFindingMethod = isset($_GET['jobFindingMethod']) ? $_GET['jobFindingMethod'] : null;

// Create an empty result array
$result = [];

try {
    // Build the base query with proper JOIN conditions
    $query = "SELECT eb.year_graduated, COUNT(DISTINCT eb.user_id) AS total_count 
             FROM educational_background eb 
             LEFT JOIN employment_data ed ON eb.user_id = ed.user_id 
             LEFT JOIN personal_info pi ON eb.user_id = pi.user_id 
             LEFT JOIN job_duration jd ON eb.user_id = jd.user_id
             LEFT JOIN job_experience je ON eb.user_id = je.user_id";

    // Build WHERE clauses based on filters
    $conditions = [];
    $params = [];
    $types = "";

    if ($course) {
        $conditions[] = "eb.degree_specialization = ?";
        $params[] = $course;
        $types .= "s";
    }

    if ($campus) {
        $conditions[] = "eb.college_university = ?";
        $params[] = $campus;
        $types .= "s";
    }

    if ($startYear) {
        $conditions[] = "eb.year_graduated >= ?";
        $params[] = $startYear;
        $types .= "i";
    }

    if ($endYear) {
        $conditions[] = "eb.year_graduated <= ?";
        $params[] = $endYear;
        $types .= "i";
    }

    if ($employmentStatus) {
        $conditions[] = "ed.present_employment_status  = ?";
        $params[] = $employmentStatus;
        $types .= "s";
    }

    if ($relevance) {
        $conditions[] = "je.course_related = ?";
        $params[] = $relevance;
        $types .= "s";
    }

    if ($business) {
        $conditions[] = "ed.business_line = ?";
        $params[] = $business;
        $types .= "s";
    }

    if ($timeToLand) {
        $conditions[] = "jd.time_to_land = ?";
        $params[] = $timeToLand;
        $types .= "s";
    }

    if ($jobFindingMethod) {
        $conditions[] = "jd.job_finding_method = ?";
        $params[] = $jobFindingMethod;
        $types .= "s";
    }

    // Add conditions to query if there are any
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Group by year for the chart
    $query .= " GROUP BY eb.year_graduated ORDER BY eb.year_graduated";

    // Prepare and execute the statement
    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        throw new Exception("Query preparation failed: " . $mysqli->error);
    }

    // Bind parameters if there are any
    if (!empty($params)) {
        $bindParams = array($types);
        foreach ($params as $key => $value) {
            $bindParams[] = &$params[$key];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindParams);
    }

    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    // Get results
    $result_set = $stmt->get_result();

    // Get filter options from database
    $filterQueries = [
        'courses' => "SELECT DISTINCT degree_specialization FROM educational_background",
        'campuses' => "SELECT DISTINCT college_university FROM educational_background",
        'industries' => "SELECT DISTINCT business_line FROM employment_data",
        'employmentStatuses' => "SELECT DISTINCT present_employment_status FROM employment_data",
        'timeToLand' => "SELECT DISTINCT time_to_land FROM job_duration",
        'jobFindingMethods' => "SELECT DISTINCT job_finding_method FROM job_duration",
        'relevance' => "SELECT DISTINCT course_related FROM job_experience"
    ];

    $filterResults = [];

    // Execute each filter query
    foreach ($filterQueries as $key => $query) {
        $filterStmt = $mysqli->prepare($query);
        if ($filterStmt === false) {
            throw new Exception("Filter query preparation failed for {$key}");
        }

        $filterStmt->execute();
        $result = $filterStmt->get_result();

        $filterResults[$key] = [];
        while ($row = $result->fetch_assoc()) {
            // Extract the first column value regardless of its name
            $filterResults[$key][] = reset($row);
        }

        $filterStmt->close();
    }

    // Format results for chart.js
    $years = [];
    $counts = [];

    while ($row = $result_set->fetch_assoc()) {
        $years[] = $row['year_graduated'];
        $counts[] = (int)$row['total_count'];
    }

    $datasets = [
        [
            "label" => "Total Graduates",
            "data" => $counts,
            "backgroundColor" => "#006400" // Primary green color
        ]
    ];

    $result = [
        "labels" => $years,
        "datasets" => $datasets,
        "filterOptions" => [
            "courses" => $filterResults['courses'],
            "campuses" => $filterResults['campuses'],
            "industries" => $filterResults['industries'],
            "employmentStatuses" => $filterResults['employmentStatuses'],
            "timeToLand" => $filterResults['timeToLand'],
            "jobFindingMethods" => $filterResults['jobFindingMethods'],
            "relevance" => $filterResults['relevance']
        ]
        // "appliedFilters" => [
        //     "course" => $course,
        //     "campus" => $campus,
        //     "startYear" => $startYear,
        //     "endYear" => $endYear,
        //     "employmentStatus" => $employmentStatus,
        //     "relevance" => $relevance,
        //     "business" => $business,
        //     "timeToLand" => $timeToLand,
        //     "jobFindingMethod" => $jobFindingMethod
        // ]
    ];

    // Close the main statement
    $stmt->close();
} catch (Exception $e) {
    // Return error message
    $result = [
        "error" => "Database error: " . $e->getMessage()
    ];

    // Log the error server-side
    error_log("Database error in graduate chart API: " . $e->getMessage());
}

// Return the final result
echo json_encode($result);
