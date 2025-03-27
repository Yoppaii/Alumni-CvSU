<?php
include '../../../main_db.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT DISTINCT degree_specialization FROM educational_background ORDER BY degree_specialization ASC";
$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(["error" => "SQL Error: " . $mysqli->error]));
}

$campuses = [];
while ($row = $result->fetch_assoc()) {
    $campuses[] = $row['degree_specialization'];
}

echo json_encode($campuses);
