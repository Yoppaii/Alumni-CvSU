<?php
ob_start(); // Start output buffering to prevent accidental output

require_once '../../main_db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    ob_end_flush();
    exit;
}

// Validate booking_id parameter
if (!isset($_POST['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    ob_end_flush();
    exit;
}

$bookingId = (int)$_POST['booking_id'];

if ($bookingId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    ob_end_flush();
    exit;
}

// Prepare the update statement
$stmt = $mysqli->prepare("UPDATE bookings SET is_archived = 0 WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL prepare error: ' . $mysqli->error]);
    ob_end_flush();
    exit;
}

$stmt->bind_param("i", $bookingId);

$result = $stmt->execute();

if ($result) {
    if ($stmt->affected_rows > 0) {
        // Successfully restored
        echo json_encode([
            'success' => true,
            'message' => 'Booking restored successfully',
            'data' => ['bookingId' => $bookingId]
        ]);
    } else {
        // No rows updated - booking may already be restored or ID not found
        echo json_encode([
            'success' => false,
            'message' => 'No booking was updated. It might already be restored or does not exist.'
        ]);
    }
} else {
    // Execution failed
    echo json_encode(['success' => false, 'message' => 'Failed to restore booking: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();

ob_end_flush(); // Flush output buffer and send output
