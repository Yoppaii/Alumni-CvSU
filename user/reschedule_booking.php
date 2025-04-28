<?php
require_once '../main_db.php';
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

// Check if request is JSON or FormData
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';

if (strpos($contentType, 'application/json') !== false) {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
} else {
    // Get POST data (FormData)
    $data = $_POST;
}

// Get and validate parameters
$booking_id = isset($data['booking_id']) ? intval($data['booking_id']) : 0;
$new_arrival = isset($data['new_arrival']) ? $data['new_arrival'] : '';
$new_departure = isset($data['new_departure']) ? $data['new_departure'] : '';
$arrival_date = isset($data['arrival_date']) ? $data['arrival_date'] : '';
$arrival_time = isset($data['arrival_time']) ? $data['arrival_time'] : '';
$departure_date = isset($data['departure_date']) ? $data['departure_date'] : '';
$departure_time = isset($data['departure_time']) ? $data['departure_time'] : '';


// For debugging
error_log("Received booking_id: " . print_r($booking_id, true));
error_log("Received new_arrival: " . print_r($new_arrival, true));
error_log("Received new_departure: " . print_r($new_departure, true));
error_log("Received arrival_date: " . print_r($arrival_date, true));
error_log("Received arrival_time: " . print_r($arrival_time, true));
error_log("Received departure_date: " . print_r($departure_date, true));
error_log("Received departure_time: " . print_r($departure_time, true));

// Input validation
if ($booking_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking ID'
    ]);
    exit;
}

if (empty($new_arrival) || empty($new_departure)) {
    echo json_encode([
        'success' => false,
        'message' => 'New arrival and departure dates are required'
    ]);
    exit;
}

try {
    // Format the new arrival and departure dates for the database
    $new_arrival_date = new DateTime($new_arrival);
    // $arrival_date = $new_arrival_date->format('Y-m-d');  // no longer needed
    // $arrival_time = $new_arrival_date->format('H:i:s'); // no longer needed

    $new_departure_date = new DateTime($new_departure);
    // $departure_date = $new_departure_date->format('Y-m-d'); // no longer needed
    // $departure_time = $new_departure_date->format('H:i:s'); // no longer needed

    // Get current booking details
    $query = "
        SELECT *
        FROM bookings b
        WHERE b.id = ?
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Booking not found');
    }

    $booking = $result->fetch_assoc();
    $room_id = $booking['room_number'];


    // Calculate new total price based on the new duration
    $interval = $new_arrival_date->diff($new_departure_date);
    $days = $interval->days;
    // Consider hours and minutes for partial days
    if ($interval->h > 0 || $interval->i > 0) {
        $days += 1; // Add one day if there are any hours or minutes
    }

    $new_total_price = $days * $booking['price_per_day'];

    // Start transaction
    $mysqli->begin_transaction();

    // Update the booking
    $update_query = "
        UPDATE bookings
        SET arrival_date = ?,
            arrival_time = ?,
            departure_date = ?,
            departure_time = ?,
            total_price = ?,
            status = 'pending'
        WHERE id = ?
    ";

    $update_stmt = $mysqli->prepare($update_query);
    // $arrivalTime = $new_arrival_date->format('H:i:s'); // no longer needed
    // $departureTime = $new_departure_date->format('H:i:s'); // no longer needed
    $update_stmt->bind_param(
        "ssssdi",
        $arrival_date,
        $arrival_time,
        $departure_date,
        $departure_time,
        $new_total_price,
        $booking_id
    );
    $update_result = $update_stmt->execute();

    if (!$update_result) {
        throw new Exception('Failed to update booking: ' . $mysqli->error);
    }

    // Commit the transaction
    $mysqli->commit();

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Booking rescheduled successfully',
        'data' => [
            'booking_id' => $booking_id,
            'arrival_date' => $arrival_date,
            'arrival_time' =>  $arrival_time,
            'departure_date' => $departure_date,
            'departure_time' => $departure_time,
            'new_total_price' => $new_total_price
        ]
    ]);
} catch (Exception $e) {
    // Roll back the transaction on error
    if ($mysqli->error) {
        $mysqli->rollback();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    // Close all statements and connection
    if (isset($stmt)) $stmt->close();
    if (isset($conflict_stmt)) $conflict_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($log_stmt)) $log_stmt->close();
    $mysqli->close();
}
