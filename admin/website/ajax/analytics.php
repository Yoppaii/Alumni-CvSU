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
$business = isset($_GET['business']) ? $_GET['business'] : null;

// Check if we should use real data
$useRealData = isset($_GET['useRealData']) && $_GET['useRealData'] === 'true';

// Create an empty result array
$result = [];

if ($useRealData) {
    // Use real database data
    try {
        // Build the base query with proper JOIN conditions
        $query = "SELECT eb.year_graduated, COUNT(DISTINCT eb.user_id) AS total_count 
                 FROM educational_background eb 
                 LEFT JOIN employment_data ed ON eb.user_id = ed.user_id 
                 LEFT JOIN personal_info pi ON eb.user_id = pi.user_id 
                 LEFT JOIN job_experience je ON eb.user_id = je.user_id";

        // Build WHERE clauses based on filters
        $conditions = [];
        $params = [];
        $types = "";

        if ($course) {
            $conditions[] = "eb.degree_program = ?";
            $params[] = $course;
            $types .= "s";
        }

        if ($campus) {
            $conditions[] = "eb.campus = ?";
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

        if ($employmentStatusFilter) {
            $conditions[] = "ed.employment_status = ?";
            $params[] = $employmentStatusFilter;
            $types .= "s";
        }

        if ($jobRelevance) {
            $relevanceValue = ($jobRelevance == 'yes') ? 1 : 0;
            $conditions[] = "je.job_relevant_to_course = ?";
            $params[] = $relevanceValue;
            $types .= "i";
        }

        if ($business) {
            $conditions[] = "je.business_line = ?";
            $params[] = $business;
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
            // Fixed: Create a proper parameter binding array with references
            $bindParams = array($types);

            // Create references to each parameter
            foreach ($params as $key => $value) {
                $bindParams[] = &$params[$key];
            }

            // Call bind_param with the array of references
            call_user_func_array(array($stmt, 'bind_param'), $bindParams);
        }

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }

        // Get results
        $result_set = $stmt->get_result();

        // Get filter options from database
        $courseQuery = "SELECT DISTINCT degree_program FROM educational_background";
        $campusQuery = "SELECT DISTINCT campus FROM educational_background";
        $businessQuery = "SELECT DISTINCT business_line FROM job_experience";

        $courseStmt = $mysqli->prepare($courseQuery);
        $campusStmt = $mysqli->prepare($campusQuery);
        $businessStmt = $mysqli->prepare($businessQuery);

        if ($courseStmt === false || $campusStmt === false || $businessStmt === false) {
            throw new Exception("Filter query preparation failed");
        }

        $courseStmt->execute();
        $campusStmt->execute();
        $businessStmt->execute();

        $course_result = $courseStmt->get_result();
        $campus_result = $campusStmt->get_result();
        $business_result = $businessStmt->get_result();

        $courses = [];
        $campuses = [];
        $industries = [];

        while ($row = $course_result->fetch_assoc()) {
            $courses[] = $row['degree_program'];
        }

        while ($row = $campus_result->fetch_assoc()) {
            $campuses[] = $row['campus'];
        }

        while ($row = $business_result->fetch_assoc()) {
            $industries[] = $row['business_line'];
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
                "courses" => $courses,
                "campuses" => $campuses,
                "industries" => $industries
            ],
            "appliedFilters" => [
                "course" => $course,
                "campus" => $campus,
                "startYear" => $startYear,
                "endYear" => $endYear,
                "employmentStatus" => $employmentStatusFilter,
                "jobRelevance" => $jobRelevance,
                "business" => $business
            ],
            "usingDummyData" => false
        ];

        // Close statements
        $stmt->close();
        $courseStmt->close();
        $campusStmt->close();
        $businessStmt->close();
    } catch (Exception $e) {
        // If there's a database error, fall back to sample data
        $result = [
            "error" => "Database error: " . $e->getMessage(),
            "usingDummyData" => true
        ];
        $useRealData = false; // Fall back to sample data
    }
}

// If not using real data or if there was an error, use the sample data
if (!$useRealData || isset($result["error"])) {
    // Your existing sample data code
    $sampleData = [
        [2000, 'BS Computer Science', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 18],
        [2000, 'BS Business Administration', 'Bacoor Campus', 'contractual', 'No', 'Wholesale and Retail Trade', 15],
        [2000, 'BS Hospitality Management', 'Naic Campus', 'temporary', 'Yes', 'Health and Social Work', 14],
        [2001, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 20],
        [2001, 'BS Mechanical Engineering', 'Silang Campus', 'self_employed', 'No', 'Manufacturing', 17],
        [2001, 'BS Psychology', 'Bacoor Campus', 'temporary', 'Yes', 'Education', 16],
        [2002, 'BS Computer Science', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 22],
        [2002, 'BS Psychology', 'Naic Campus', 'contractual', 'No', 'Health and Social Work', 19],
        [2002, 'BS Business Administration', 'Main Campus', 'temporary', 'Yes', 'Financial Intermediation', 18],
        [2003, 'BS Social Work', 'Bacoor Campus', 'regular', 'Yes', 'Health and Social Work', 25],
        [2003, 'BS Information Technology', 'Main Campus', 'self_employed', 'Yes', 'Transport Storage and Communication', 24],
        [2003, 'BS Civil Engineering', 'Silang Campus', 'contractual', 'No', 'Construction', 21],
        [2004, 'BS Business Administration', 'Main Campus', 'regular', 'Yes', 'Wholesale and Retail Trade', 28],
        [2004, 'BS Computer Science', 'Main Campus', 'temporary', 'Yes', 'Transport Storage and Communication', 27],
        [2004, 'BS Psychology', 'Bacoor Campus', 'contractual', 'Yes', 'Education', 23],
        [2005, 'BS Psychology', 'Naic Campus', 'temporary', 'No', 'Health and Social Work', 26],
        [2005, 'BS Electrical Engineering', 'Silang Campus', 'regular', 'Yes', 'Electricity, Gas and Water Supply', 30],
        [2005, 'BS Information Technology', 'Main Campus', 'self_employed', 'Yes', 'Transport Storage and Communication', 29],

        // 2006-2010 (Moderate growth)
        [2006, 'BS Social Work', 'Bacoor Campus', 'regular', 'Yes', 'Health and Social Work', 33],
        [2006, 'BS Business Administration', 'Main Campus', 'contractual', 'Yes', 'Financial Intermediation', 31],
        [2006, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 34],
        [2007, 'BS Mechanical Engineering', 'Silang Campus', 'temporary', 'No', 'Manufacturing', 32],
        [2007, 'BS Computer Science', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 38],
        [2007, 'BS Psychology', 'Naic Campus', 'self_employed', 'Yes', 'Health and Social Work', 30],
        [2008, 'BS Business Administration', 'Main Campus', 'regular', 'Yes', 'Financial Intermediation', 41],
        [2008, 'BS Information Technology', 'Main Campus', 'contractual', 'Yes', 'Transport Storage and Communication', 43],
        [2008, 'BS Psychology', 'Bacoor Campus', 'temporary', 'Yes', 'Education', 35],
        [2009, 'BS Social Work', 'Tanza Campus', 'regular', 'Yes', 'Health and Social Work', 44],
        [2009, 'BS Civil Engineering', 'Silang Campus', 'self_employed', 'No', 'Construction', 38],
        [2009, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 45],
        [2010, 'BS Psychology', 'Naic Campus', 'contractual', 'No', 'Health and Social Work', 40],
        [2010, 'BS Business Administration', 'Main Campus', 'regular', 'Yes', 'Wholesale and Retail Trade', 47],
        [2010, 'BS Computer Science', 'Main Campus', 'temporary', 'Yes', 'Transport Storage and Communication', 49],

        // 2011-2015 (Significant spike in 2013)
        [2011, 'BS Social Work', 'Bacoor Campus', 'regular', 'Yes', 'Health and Social Work', 48],
        [2011, 'BS Electrical Engineering', 'Silang Campus', 'contractual', 'Yes', 'Electricity, Gas and Water Supply', 44],
        [2011, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 52],
        [2012, 'BS Business Administration', 'Main Campus', 'self_employed', 'Yes', 'Financial Intermediation', 50],
        [2012, 'BS Computer Science', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 55],
        [2012, 'BS Psychology', 'Cavite City Campus', 'temporary', 'Yes', 'Education', 47],
        [2013, 'BS Psychology', 'Naic Campus', 'regular', 'No', 'Health and Social Work', 65], // Spike year
        [2013, 'BS Mechanical Engineering', 'Silang Campus', 'regular', 'Yes', 'Manufacturing', 70],
        [2013, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 80],
        [2013, 'BS Business Administration', 'Main Campus', 'regular', 'Yes', 'Financial Intermediation', 75],
        [2013, 'BS Hospitality Management', 'Bacoor Campus', 'regular', 'Yes', 'Hotels and Restaurants', 68],
        [2014, 'BS Computer Science', 'Main Campus', 'contractual', 'Yes', 'Transport Storage and Communication', 60],
        [2014, 'BS Business Administration', 'Main Campus', 'temporary', 'Yes', 'Wholesale and Retail Trade', 55],
        [2014, 'BS Civil Engineering', 'Silang Campus', 'self_employed', 'No', 'Construction', 50],
        [2015, 'BS Social Work', 'Bacoor Campus', 'regular', 'Yes', 'Health and Social Work', 57],
        [2015, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 62],
        [2015, 'BS Psychology', 'Naic Campus', 'contractual', 'No', 'Health and Social Work', 53],

        // 2016-2020 (Steady increase)
        [2016, 'BS Business Administration', 'Main Campus', 'regular', 'Yes', 'Wholesale and Retail Trade', 59],
        [2016, 'BS Computer Science', 'Main Campus', 'temporary', 'Yes', 'Transport Storage and Communication', 63],
        [2016, 'BS Psychology', 'Gen. Mariano Alvarez Campus', 'contractual', 'Yes', 'Education', 54],
        [2017, 'BS Electrical Engineering', 'Silang Campus', 'regular', 'Yes', 'Electricity, Gas and Water Supply', 61],
        [2017, 'BS Information Technology', 'Main Campus', 'self_employed', 'Yes', 'Transport Storage and Communication', 65],
        [2017, 'BS Hospitality Management', 'Tanza Campus', 'regular', 'Yes', 'Hotels and Restaurants', 60],
        [2018, 'BS Psychology', 'Naic Campus', 'contractual', 'No', 'Health and Social Work', 63],
        [2018, 'BS Business Administration', 'Main Campus', 'regular', 'Yes', 'Financial Intermediation', 66],
        [2018, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 70],
        [2019, 'BS Science in Accountancy', 'Cavite City Campus', 'temporary', 'Yes', 'Financial Intermediation', 64],
        [2019, 'BS Computer Science', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 73],
        [2019, 'BS Mechanical Engineering', 'Silang Campus', 'self_employed', 'No', 'Manufacturing', 65],
        [2020, 'BS Social Work', 'Bacoor Campus', 'regular', 'Yes', 'Health and Social Work', 68], // COVID year - slightly lower
        [2020, 'BS Information Technology', 'Main Campus', 'contractual', 'Yes', 'Transport Storage and Communication', 71],
        [2020, 'BS Business Administration', 'Main Campus', 'temporary', 'Yes', 'Wholesale and Retail Trade', 63],

        // 2021-2025 (Post-pandemic growth with 2023 spike)
        [2021, 'BS Psychology', 'Naic Campus', 'regular', 'No', 'Health and Social Work', 70],
        [2021, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 75],
        [2021, 'BS Civil Engineering', 'Silang Campus', 'regular', 'Yes', 'Construction', 72],
        [2022, 'BS Business Administration', 'Main Campus', 'self_employed', 'Yes', 'Financial Intermediation', 74],
        [2022, 'BS Computer Science', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 80],
        [2022, 'BS Hospitality Management', 'Tanza Campus', 'contractual', 'Yes', 'Hotels and Restaurants', 76],
        [2023, 'BS Psychology', 'Gen. Mariano Alvarez Campus', 'regular', 'Yes', 'Education', 90], // Another spike year
        [2023, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 95],
        [2023, 'BS Electrical Engineering', 'Silang Campus', 'regular', 'Yes', 'Electricity, Gas and Water Supply', 85],
        [2023, 'BS Business Administration', 'Main Campus', 'regular', 'Yes', 'Financial Intermediation', 88],
        [2024, 'BS Psychology', 'Naic Campus', 'temporary', 'No', 'Health and Social Work', 82],
        [2024, 'BS Information Technology', 'Main Campus', 'regular', 'Yes', 'Transport Storage and Communication', 87],
        [2024, 'BS Hospitality Management', 'Bacoor Campus', 'self_employed', 'Yes', 'Hotels and Restaurants', 80],
        [2025, 'BS Business Administration', 'Main Campus', 'regular', 'Yes', 'Wholesale and Retail Trade', 85],
        [2025, 'BS Computer Science', 'Main Campus', 'contractual', 'Yes', 'Transport Storage and Communication', 88],
        [2025, 'BS Mechanical Engineering', 'Silang Campus', 'regular', 'Yes', 'Manufacturing', 83]
    ];

    // Extract unique filter values from sample data
    $filterOptions = [
        "courses" => [],
        "campuses" => [],
        "industries" => []
    ];

    foreach ($sampleData as $row) {
        if (!in_array($row[1], $filterOptions["courses"])) {
            $filterOptions["courses"][] = $row[1];
        }
        if (!in_array($row[2], $filterOptions["campuses"])) {
            $filterOptions["campuses"][] = $row[2];
        }
        if (!in_array($row[5], $filterOptions["industries"])) {
            $filterOptions["industries"][] = $row[5];
        }
    }

    // Apply filters to sample data
    $filteredData = [];
    foreach ($sampleData as $row) {
        $include = true;

        // Filter by course
        if ($course && $row[1] != $course) {
            $include = false;
        }
        // Filter by campus
        if ($campus && $row[2] != $campus) {
            $include = false;
        }
        // Filter by year range
        if ($startYear && $row[0] < $startYear) {
            $include = false;
        }
        if ($endYear && $row[0] > $endYear) {
            $include = false;
        }
        // Filter by employment status
        if ($employmentStatusFilter && $row[3] != $employmentStatusFilter) {
            $include = false;
        }
        // Filter by job relevance
        if ($jobRelevance && $row[4] != ($jobRelevance == 'yes' ? 'Yes' : 'No')) {
            $include = false;
        }
        // Filter by business
        if ($business && $row[5] != $business) {
            $include = false;
        }

        if ($include) {
            $filteredData[] = $row;
        }
    }

    // Prepare data for chart
    $years = [];
    $yearCounts = [];

    // Group by year and count
    foreach ($filteredData as $row) {
        $year = $row[0];
        if (!isset($yearCounts[$year])) {
            $yearCounts[$year] = 0;
            $years[] = $year;
        }
        $yearCounts[$year] += $row[6]; // Add the count
    }

    sort($years);
    $counts = [];
    foreach ($years as $year) {
        $counts[] = $yearCounts[$year];
    }

    // Create dataset
    $datasets = [
        [
            "label" => "Total Graduates",
            "data" => $counts,
            "backgroundColor" => "#006400" // Primary green color
        ]
    ];

    // Return the data along with filter options
    $result = [
        "labels" => $years,
        "datasets" => $datasets,
        "filterOptions" => $filterOptions,
        "appliedFilters" => [
            "course" => $course,
            "campus" => $campus,
            "startYear" => $startYear,
            "endYear" => $endYear,
            "employmentStatus" => $employmentStatusFilter,
            "jobRelevance" => $jobRelevance,
            "business" => $business
        ],
        "usingDummyData" => true
    ];
}

// Return the final result
echo json_encode($result);
