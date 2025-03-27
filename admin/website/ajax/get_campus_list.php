<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT DISTINCT college_university FROM educational_background ORDER BY college_university ASC";
$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$campuses = [];
while ($row = $result->fetch_assoc()) {
    $campuses[] = $row['college_university'];
}

echo json_encode($campuses);
