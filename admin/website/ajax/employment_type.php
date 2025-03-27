<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT e.employment_status, COUNT(e.user_id) AS total FROM employment_data e GROUP BY e.employment_status";

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
