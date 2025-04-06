<?php
require_once '../main_db.php';
session_start();

header('Content-Type: application/json');

error_log('POST data received: ' . print_r($_POST, true));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

$missing_fields = [];
if (!isset($_POST['booking_id'])) {
    $missing_fields[] = 'booking_id';
}

if (!empty($missing_fields)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required data: ' . implode(', ', $missing_fields),
        'debug' => [
            'post_data' => $_POST,
            'missing_fields' => $missing_fields
        ]
    ]);
    exit();
}

$booking_id = trim($_POST['booking_id']);

if (empty($booking_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Booking ID cannot be empty',
        'debug' => [
            'booking_id' => $booking_id
        ]
    ]);
    exit();
}

try {
    $mysqli->begin_transaction();

    // Check if the booking exists
    $get_booking_sql = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
    $get_stmt = $mysqli->prepare($get_booking_sql);
    $get_stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $get_stmt->execute();
    $booking_result = $get_stmt->get_result();

    if ($booking_result->num_rows === 0) {
        throw new Exception('Booking not found');
    }

    $booking = $booking_result->fetch_assoc();
    error_log('Retrieved booking data: ' . print_r($booking, true));

    // Update the booking status to 'cancelled'
    $update_status_sql = "UPDATE bookings 
                          SET status = 'cancelled', cancelled_at = NOW() 
                          WHERE id = ? AND user_id = ?";

    $update_stmt = $mysqli->prepare($update_status_sql);

    if (!$update_stmt) {
        throw new Exception('Failed to prepare update statement: ' . $mysqli->error);
    }

    $update_stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);

    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update booking status: ' . $update_stmt->error);
    }

    $mysqli->commit();
    echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
} catch (Exception $e) {
    $mysqli->rollback();
    error_log('Booking cancellation error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}

if (isset($get_stmt)) $get_stmt->close();
if (isset($update_stmt)) $update_stmt->close();
$mysqli->close();
