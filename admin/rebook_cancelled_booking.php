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

// Check if booking_id is provided
if (!isset($_POST['booking_id']) || empty($_POST['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

$booking_id = $mysqli->real_escape_string($_POST['booking_id']);

try {
    $mysqli->begin_transaction();

    // Get the cancelled booking details
    $query = "
        SELECT b.*, u.email, u.username, ud.first_name, ud.middle_name, ud.last_name 
        FROM bookings b
        JOIN users u ON b.user_id = u.id 
        JOIN user ud ON u.id = ud.user_id
        WHERE b.id = ? AND b.status = 'cancelled'";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Cancelled booking not found']);
        exit;
    }

    $booking = $result->fetch_assoc();

    $fullName = trim($booking['first_name'] . ' ' .
        ($booking['middle_name'] ? $booking['middle_name'] . ' ' : '') .
        $booking['last_name']);

    // Generate a new reference number
    $new_reference = 'RB-' . substr($booking['reference_number'], 0, 8) . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

    // Create a new booking with pending status
    $insert_query = "INSERT INTO bookings (
        user_id, room_number, reference_number, arrival_date, arrival_time, 
        departure_date, departure_time, occupancy, price, price_per_day, special_requests, 
        status, created_at, updated_at
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW()
    )";
    
    $insert_stmt = $mysqli->prepare($insert_query);
    $insert_stmt->bind_param(
        'iisssssidds',
        $booking['user_id'],
        $booking['room_number'],
        $new_reference,
        $booking['arrival_date'],
        $booking['arrival_time'],
        $booking['departure_date'],
        $booking['departure_time'],
        $booking['occupancy'],
        $booking['price'],
        $booking['price_per_day'],
        $booking['special_requests']
    );
    
    if ($insert_stmt->execute()) {
        $new_booking_id = $mysqli->insert_id;
        
        // Send email notification about rebooking
        $emailSent = sendBookingStatusEmail(
            $booking['email'],
            $fullName,
            $new_reference,
            'pending', // New booking starts as pending
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
                'message' => 'Booking successfully rebooked', 
                'reference_number' => $new_reference,
                'new_booking_id' => $new_booking_id
            ]);
        } else {
            throw new Exception("Failed to send email notification");
        }
    } else {
        throw new Exception("Failed to create new booking: " . $mysqli->error);
    }
} catch (Exception $e) {
    $mysqli->rollback();
    error_log("Error rebooking cancelled booking: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error occurred: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($insert_stmt)) $insert_stmt->close();
}