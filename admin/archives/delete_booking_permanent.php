<?php
// Include database connection
include '../../main_db.php';

// Initialize response array
$response = array('success' => false, 'message' => '');

// Check if booking ID is provided
if (!isset($_POST['booking_id']) || empty($_POST['booking_id'])) {
    $response['message'] = 'Booking ID is required';
    echo json_encode($response);
    exit;
}

$booking_id = $mysqli->real_escape_string($_POST['booking_id']);

// Start transaction
$mysqli->begin_transaction();

try {
    // First, check if the booking exists and is archived
    $checkQuery = "SELECT id FROM bookings WHERE id = ? AND is_archived = 1";
    $stmt = $mysqli->prepare($checkQuery);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Booking not found or is not archived');
    }

    // Delete the booking permanently
    $deleteQuery = "DELETE FROM bookings WHERE id = ?";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception('Failed to delete booking');
    }

    // Commit transaction
    $mysqli->commit();

    $response['success'] = true;
    $response['message'] = 'Booking permanently deleted';
} catch (Exception $e) {
    // Rollback transaction on error
    $mysqli->rollback();
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit;
