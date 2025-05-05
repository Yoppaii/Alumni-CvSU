<?php
// Include database connection
include '../main_db.php';

// Check if ID parameter is set
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing application ID']);
    exit;
}

// Get application ID
$application_id = $_GET['id'];

// Validate application ID
if (!is_numeric($application_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
    exit;
}

// Get decline reason
$query = "SELECT r.reason, r.created_at, r.declined_by, u.username as declined_by_name 
          FROM alumni_id_declined_reasons r
          LEFT JOIN users u ON r.declined_by = u.id
          WHERE r.application_id = ?
          ORDER BY r.created_at DESC
          LIMIT 1";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Format date
    $date = new DateTime($row['created_at']);
    $formatted_date = $date->format('F j, Y \a\t g:i A');

    // Response
    $response = [
        'success' => true,
        'reason' => $row['reason'],
        'declined_at' => $formatted_date
    ];

    // Add declined by if available
    if ($row['declined_by_name']) {
        $response['declined_by'] = $row['declined_by_name'];
    }

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'No decline reason found']);
}

// Close the statement and connection
$stmt->close();
$mysqli->close();
