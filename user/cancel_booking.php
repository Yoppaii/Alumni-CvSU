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
if (!isset($_POST['cancellation_reason'])) {
    $missing_fields[] = 'cancellation_reason';
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
$reason = trim($_POST['cancellation_reason']);

if (empty($booking_id) || empty($reason)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Booking ID and cancellation reason cannot be empty',
        'debug' => [
            'booking_id' => $booking_id,
            'reason_length' => strlen($reason)
        ]
    ]);
    exit();
}

try {
    $mysqli->begin_transaction();

    $count_sql = "SELECT COUNT(*) as total FROM cancelled_bookings WHERE user_id = ?";
    $count_stmt = $mysqli->prepare($count_sql);
    $count_stmt->bind_param("i", $_SESSION['user_id']);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    
    if ($count_row['total'] >= 3) {
        $delete_oldest_sql = "DELETE FROM cancelled_bookings 
                            WHERE user_id = ? 
                            AND cancelled_at = (
                                SELECT MIN(cancelled_at) 
                                FROM cancelled_bookings 
                                WHERE user_id = ?
                            )";
        $delete_oldest_stmt = $mysqli->prepare($delete_oldest_sql);
        $delete_oldest_stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
        $delete_oldest_stmt->execute();
    }

    error_log("Attempting to fetch booking ID: $booking_id for user ID: {$_SESSION['user_id']}");
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

    $arrival_date = date('Y-m-d', strtotime($booking['arrival_date']));
    $departure_date = date('Y-m-d', strtotime($booking['departure_date']));
    $arrival_time = date('H:i:s', strtotime($booking['arrival_time']));
    $departure_time = date('H:i:s', strtotime($booking['departure_time']));

    $insert_sql = "INSERT INTO cancelled_bookings (
        original_booking_id,
        reference_number,
        user_id,
        room_number,
        occupancy,
        price,
        price_per_day,
        arrival_date,
        arrival_time,
        departure_date,
        departure_time,
        cancellation_reason,
        cancelled_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $insert_stmt = $mysqli->prepare($insert_sql);
    
    if (!$insert_stmt) {
        throw new Exception('Failed to prepare insert statement: ' . $mysqli->error);
    }
    
    $insert_stmt->bind_param(
        "isisiidsssss",
        $booking['id'],
        $booking['reference_number'],
        $booking['user_id'],
        $booking['room_number'],
        $booking['occupancy'],
        $booking['price'],
        $booking['price_per_day'],
        $arrival_date,
        $arrival_time,
        $departure_date,
        $departure_time,
        $reason
    );

    if (!$insert_stmt->execute()) {
        throw new Exception('Failed to insert cancelled booking: ' . $insert_stmt->error);
    }

    $delete_sql = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
    $delete_stmt = $mysqli->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    
    if (!$delete_stmt->execute()) {
        throw new Exception('Failed to delete original booking: ' . $delete_stmt->error);
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

if (isset($count_stmt)) $count_stmt->close();
if (isset($delete_oldest_stmt)) $delete_oldest_stmt->close();
if (isset($get_stmt)) $get_stmt->close();
if (isset($insert_stmt)) $insert_stmt->close();
if (isset($delete_stmt)) $delete_stmt->close();
$mysqli->close();