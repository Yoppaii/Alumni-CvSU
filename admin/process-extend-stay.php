<?php
require_once '../main_db.php';
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');


// Get and validate POST parameters
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$new_departure = isset($_POST['new_departure']) ? $_POST['new_departure'] : '';

// Input validation
if ($booking_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking ID'
    ]);
    exit;
}

if (empty($new_departure)) {
    echo json_encode([
        'success' => false,
        'message' => 'New departure date is required'
    ]);
    exit;
}

try {
    // Format the new departure date for database
    $new_departure_date = new DateTime($new_departure);
    $formatted_departure = $new_departure_date->format('Y-m-d H:i:s');

    // Get current booking details
    $query = "
        SELECT b.*, r.title as room_name, r.price
        FROM bookings b
        JOIN rooms r ON b.room_number = r.id
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

    // Ensure new departure is after current departure
    $current_departure = new DateTime($booking['departure_date']);

    if ($new_departure_date <= $current_departure) {
        throw new Exception('New departure date must be after current departure date');
    }

    // Check if room is available for the extended period
    $conflict_query = "
        SELECT COUNT(*) AS booking_count
        FROM bookings
        WHERE room_number = ?
        AND id != ?
        AND status NOT IN ('cancelled', 'no_show', 'completed')
        AND (
            (arrival_date <= ? AND departure_date >= ?)
        )
    ";

    $conflict_stmt = $mysqli->prepare($conflict_query);
    $conflict_stmt->bind_param("iiss", $room_id, $booking_id, $formatted_departure, $booking['departure_date']);
    $conflict_stmt->execute();
    $conflict_result = $conflict_stmt->get_result();
    $conflict_row = $conflict_result->fetch_assoc();

    if ($conflict_row['booking_count'] > 0) {
        throw new Exception('Room is not available for the selected extension period');
    }

    // Calculate additional cost
    $interval = $current_departure->diff($new_departure_date);
    $additional_days = $interval->days;
    if ($interval->h > 0 || $interval->i > 0) {
        $additional_days += 1; // Count partial days as full days
    }

    $additional_cost = $additional_days * $booking['price'];
    $new_total_price = $booking['total_price'] + $additional_cost;

    // Start transaction
    $mysqli->begin_transaction();

    // Update the booking
    $update_query = "
        UPDATE bookings
        SET departure_date = ?,
            total_price = ?,
            status = 'extend_stay',
            updated_at = NOW()
        WHERE id = ?
    ";

    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("sdi", $formatted_departure, $new_total_price, $booking_id);
    $update_result = $update_stmt->execute();

    if (!$update_result) {
        throw new Exception('Failed to update booking: ' . $mysqli->error);
    }

    // Add a log entry if you have a logging table
    $log_query = "
        INSERT INTO booking_logs
        (booking_id, action, details, performed_by, performed_at)
        VALUES (?, 'extend_stay', ?, ?, NOW())
    ";

    $details = json_encode([
        'previous_departure' => $booking['departure_date'],
        'new_departure' => $formatted_departure,
        'additional_days' => $additional_days,
        'additional_cost' => $additional_cost
    ]);

    $admin_id = $_SESSION['user_id'] ?? 0;

    $log_stmt = $mysqli->prepare($log_query);
    $log_stmt->bind_param("isi", $booking_id, $details, $admin_id);

    // Only execute if the booking_logs table exists
    try {
        $log_stmt->execute();
    } catch (Exception $e) {
        // Ignore errors with the log table, as it's not critical
    }

    // Commit the transaction
    $mysqli->commit();

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Stay extended successfully',
        'data' => [
            'booking_id' => $booking_id,
            'new_departure' => $formatted_departure,
            'additional_days' => $additional_days,
            'additional_cost' => $additional_cost,
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
