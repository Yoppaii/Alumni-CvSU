<?php
header('Content-Type: application/json');
require_once '../../../main_db.php';

// Get input data
$data = json_decode(file_get_contents('php://input'), true);

$alumni_id = $data['alumni_id'] ?? '';
$alumni_first_name = $data['alumni_first_name'] ?? '';
$alumni_middle_name = $data['alumni_middle_name'] ?? '';
$alumni_last_name = $data['alumni_last_name'] ?? '';

// Validate required fields
if (!$alumni_id || !$alumni_first_name || !$alumni_last_name) {
    echo json_encode([
        'verified' => false,
        'message' => 'Please provide Alumni ID, First Name, and Last Name.'
    ]);
    exit;
}

// Prepare and execute query
$query = "SELECT * FROM alumni 
          WHERE alumni_id_card_no = ? 
          AND last_name = ? 
          AND first_name = ? 
          AND middle_name = ?";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    echo json_encode([
        'verified' => false,
        'message' => 'Database preparation error: ' . $mysqli->error
    ]);
    exit;
}

$stmt->bind_param(
    'ssss',
    $alumni_id,
    $alumni_last_name,
    $alumni_first_name,
    $alumni_middle_name
);

if (!$stmt->execute()) {
    echo json_encode([
        'verified' => false,
        'message' => 'Execution error: ' . $stmt->error
    ]);
    exit;
}

$result = $stmt->get_result();

$response = [];
if ($result->num_rows > 0) {
    $response['verified'] = true;
} else {
    $response = [
        'verified' => false,
        'message' => 'Alumni not found or details do not match.'
    ];
}

echo json_encode($response);

// Cleanup
$stmt->close();
$mysqli->close();
