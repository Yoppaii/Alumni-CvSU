<?php
require_once '../../main_db.php';

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for JSON response
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate inputs
if (!isset($_POST['userId']) || !isset($_POST['isActive'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$userId = (int)$_POST['userId'];
$isActive = $_POST['isActive'] === 'true' ? 1 : 0;

// Input validation
if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

// Prepare and execute SQL statement
$stmt = $mysqli->prepare("UPDATE users SET is_active = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL prepare error: ' . $mysqli->error]);
    exit;
}

$stmt->bind_param("ii", $isActive, $userId);
$result = $stmt->execute();

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'User status updated successfully',
        'data' => [
            'userId' => $userId,
            'isActive' => (bool)$isActive
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update user status: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
