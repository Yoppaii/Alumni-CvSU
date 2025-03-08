<?php
include '../../../main_db.php';
header('Content-Type: application/json');

$query = "SELECT 
        eb.year_graduated,
        COALESCE(ed.present_employment_status, 'Unknown') AS present_employment_status,
        COUNT(ed.user_id) AS count_status
    FROM employment_data ed
    RIGHT JOIN educational_background eb ON ed.user_id = eb.user_id
    GROUP BY eb.year_graduated, ed.present_employment_status
    ORDER BY eb.year_graduated ASC, ed.present_employment_status ASC;";

$result = $mysqli->query($query);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $mysqli->error]);
    exit;
}

$employmentStatuses = ["regular", "temporary", "contractual", "self-employed", "casual"];

$years = [];
$statuses = [];

foreach ($employmentStatuses as $status) {
    $statuses[$status] = [];
}

while ($row = $result->fetch_assoc()) {
    $year = (int)$row['year_graduated'];
    $status = $row['present_employment_status'];
    $count = (int)$row['count_status'];

    if (!in_array($year, $years)) {
        $years[] = $year;
    }

    foreach ($employmentStatuses as $key) {
        if (!isset($statuses[$key][$year])) {
            $statuses[$key][$year] = 0;
        }
    }

    if (isset($statuses[$status])) {
        $statuses[$status][$year] = $count;
    }
}

// ADD EXTRA DATA TO THE CHART, REMOVE THIS LINE IF YOU DON'T NEED IT
$extraYears = [2002, 2003, 2004, 2005, 2008, 2009, 2015, 2019, 2024];
foreach ($extraYears as $year) {
    if (!in_array($year, $years)) {
        $years[] = $year;
    }
    foreach ($employmentStatuses as $status) {
        if (!isset($statuses[$status][$year])) {
            $statuses[$status][$year] = rand(5, 20);
        }
    }
}

sort($years);




$colors = [
    "regular" => "#006400",   // Deep Green (Primary)
    "temporary" => "#2D7D46", // Forest Green (Slightly lighter)
    "contractual" => "#5A9E68", // Muted Teal-Green (Soft transition)
    "self-employed" => "#86B49C", // Sage Green (Light, fresh)
    "casual" => "#BFD8B1"  // Pastel Green (Softest, blends well)
];




$datasets = [];
foreach ($statuses as $status => $data) {
    $datasets[] = [
        "label" => $status,
        "data" => array_map(fn($year) => $data[$year] ?? 0, $years),
        "backgroundColor" => $colors[$status]
    ];
}

echo json_encode([
    "labels" => $years,
    "datasets" => $datasets
]);
