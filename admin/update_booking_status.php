<?php
include '../main_db.php';
require_once 'email_notification.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

error_log("Received booking_id: $booking_id, status: $status");

if ($booking_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

$valid_statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show', 'completed', 'cancelled', 'early_checkout'];
if (!in_array(strtolower($status), $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $mysqli->begin_transaction();

    $check_query = "
        SELECT b.*, u.email, u.username, ud.first_name, ud.middle_name, ud.last_name 
        FROM bookings b
        JOIN users u ON b.user_id = u.id 
        JOIN user ud ON u.id = ud.user_id
        WHERE b.id = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('i', $booking_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    $booking = $result->fetch_assoc();

    $fullName = trim($booking['first_name'] . ' ' .
        ($booking['middle_name'] ? $booking['middle_name'] . ' ' : '') .
        $booking['last_name']);

    if ($status === 'early_checkout') {
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');

        $update_query = "UPDATE bookings SET status = ?, departure_date = ?, departure_time = ? WHERE id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('sssi', $status, $current_date, $current_time, $booking_id);

        $booking['departure_date'] = $current_date;
        $booking['departure_time'] = $current_time;
    } else {
        $update_query = "UPDATE bookings SET status = ? WHERE id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('si', $status, $booking_id);
    }

    if ($update_stmt->execute()) {
        $emailSent = sendBookingStatusEmail(
            $booking['email'],
            $fullName,
            $booking['reference_number'],
            $status,
            $booking['room_number'],
            $booking['arrival_date'],
            $booking['departure_date'],
            $booking['price'],
            $booking['price_per_day'],
            $booking['arrival_time'],
            $booking['departure_time']
        );

        if ($emailSent) {
            $mysqli->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Status updated and notification sent successfully',
                'data' => [
                    'booking_id' => $booking_id,
                    'new_status' => $status,
                    'departure_date' => $status === 'early_checkout' ? $booking['departure_date'] : null,
                    'departure_time' => $status === 'early_checkout' ? $booking['departure_time'] : null
                ]
            ]);
        } else {
            throw new Exception("Failed to send email notification");
        }
    } else {
        throw new Exception("Error executing update query");
    }
} catch (Exception $e) {
    $mysqli->rollback();
    error_log("Error updating booking status: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error occurred: ' . $e->getMessage()
    ]);
} finally {
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($mysqli)) $mysqli->close();
}
