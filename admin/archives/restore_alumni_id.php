<?php
require_once '../../main_db.php';

// Check if ID was provided
if (!isset($_POST['alumni_id']) || empty($_POST['alumni_id'])) {
    echo json_encode(['success' => false, 'message' => 'No alumni ID provided']);
    exit;
}

// Get the alumni ID and sanitize it
$alumni_id = $mysqli->real_escape_string($_POST['alumni_id']);

// Update the record to set is_archived = 0 to restore it
$query = "UPDATE `alumni` SET `is_archived` = 0 WHERE `alumni_id` = '$alumni_id'";
$result = $mysqli->query($query);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Alumni record restored successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error restoring record: ' . $mysqli->error]);
}

$mysqli->close();
