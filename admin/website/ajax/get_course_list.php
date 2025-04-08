<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT DISTINCT course FROM personal_info ORDER BY course ASC";
$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$course = [];
while ($row = $result->fetch_assoc()) {
    $course[] = $row['course'];
}

echo json_encode($course);
