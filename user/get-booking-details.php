<?php
require_once '../main_db.php';
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if the booking_id is set
if (!isset($_POST['booking_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

// Sanitize the booking ID
$booking_id = intval($_POST['booking_id']);

// Check if the booking_id is valid
if ($booking_id <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

try {
    // Use prepared statements to prevent SQL injection
    $sql = "SELECT id, price_per_day FROM bookings WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        throw new Exception("Failed to prepare statement: " . $mysqli->error);
    }

    $stmt->bind_param("i", $booking_id);

    if (!$stmt->execute()) {
        throw new Exception("Statement execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    $booking = $result->fetch_assoc();
    $room_id = intval($booking['room_number']);
    $price_per_day = floatval($booking['price_per_day']); // Fetch price per day

    // Return the room_id and price_per_day as a JSON response
    echo json_encode([
        'success' => true,
        'room_id' => $room_id,
        'price_per_day' => $price_per_day // Include price per day in the response
    ]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    // Close statement and connection
    if (isset($stmt)) $stmt->close();
    if (isset($mysqli)) $mysqli->close();
}
