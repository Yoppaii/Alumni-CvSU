<?php
include '../../main_db.php'; // Use your database connection file

header('Content-Type: application/json');

$query = "SELECT DISTINCT user_status FROM user WHERE user_status IS NOT NULL ORDER BY user_status";
$result = $mysqli->query($query);

$userTypes = [];

while ($row = $result->fetch_assoc()) {
    $userTypes[] = $row;
}

echo json_encode($userTypes);
