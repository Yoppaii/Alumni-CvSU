<?php
require_once '../main_db.php';
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

// Get input data - check both JSON and form data methods
$input = json_decode(file_get_contents('php://input'), true);

// First try to get booking_id from JSON input
$bookingId = isset($input['booking_id']) ? intval($input['booking_id']) : 0;

// If not found in JSON, check POST data
if (!$bookingId && isset($_POST['booking_id'])) {
    $bookingId = intval($_POST['booking_id']);
}

// Check if we're handling an extension confirmation
$isConfirmation = false;
$newDepartureDate = null;
$newDepartureTime = null;

if ((isset($input['new_departure_date']) || isset($_POST['new_departure_date'])) &&
    (isset($input['new_departure_time']) || isset($_POST['new_departure_time']))
) {
    $isConfirmation = true;
    $newDepartureDate = isset($input['new_departure_date']) ? $input['new_departure_date'] : $_POST['new_departure_date'];
    $newDepartureTime = isset($input['new_departure_time']) ? $input['new_departure_time'] : $_POST['new_departure_time'];
}

// Validate booking ID
if (!$bookingId) {
    echo json_encode([
        'success' => false,
        'available' => false,
        'message' => 'Invalid booking ID',
        'debug' => ['input' => $input, 'post' => $_POST]
    ]);
    exit;
}

// Database connection check
if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'available' => false, 'message' => 'Database connection failed']);
    exit;
}

// If this is a confirmation request to actually extend the stay
if ($isConfirmation && $newDepartureDate && $newDepartureTime) {
    // Update the booking with the new departure date and time
    $updateQuery = "
        UPDATE bookings 
        SET departure_date = ?, departure_time = ?, status = 'checked_in'
        WHERE id = ?
    ";

    $updateStmt = $mysqli->prepare($updateQuery);
    $formattedDate = date('Y-m-d', strtotime($newDepartureDate));
    $formattedTime = date('H:i:s', strtotime($newDepartureTime));
    $updateStmt->bind_param("ssi", $formattedDate, $formattedTime, $bookingId);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Stay extended successfully']);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update booking: ' . $mysqli->error
        ]);
    }

    $updateStmt->close();
    $mysqli->close();
    exit;
}

// If this is the initial check for availability
// Step 1: Get current booking info
$query = "
    SELECT b.id, b.room_number, b.departure_date, b.departure_time, b.price_per_day
    FROM bookings b
    WHERE b.id = ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'available' => false, 'message' => 'Booking not found']);
    exit;
}

$booking = $result->fetch_assoc();
$roomId = intval($booking['room_number']);
$currentDepartureDate = $booking['departure_date'];
$currentDepartureTime = $booking['departure_time'];
$pricePerDay = floatval($booking['price_per_day']);

// Format next day - find the day after current departure
$nextDay = date('Y-m-d', strtotime($currentDepartureDate . ' +1 day'));

// Step 2: Check for conflicts on the next day
$conflictQuery = "
    SELECT id 
    FROM bookings 
    WHERE room_number = ? 
    AND status NOT IN ('cancelled', 'completed', 'no_show')
    AND arrival_date <= ? 
    AND departure_date >= ?
";

$conflictStmt = $mysqli->prepare($conflictQuery);
$conflictStmt->bind_param("iss", $roomId, $nextDay, $nextDay);
$conflictStmt->execute();
$conflictResult = $conflictStmt->get_result();

$isAvailable = $conflictResult->num_rows === 0;

// Return all the necessary data for the UI
echo json_encode([
    'success' => true,
    'available' => $isAvailable,
    'bookingId' => $bookingId,
    'roomId' => $roomId,
    'currentDepartureDate' => $currentDepartureDate,
    'currentDepartureTime' => $currentDepartureTime,
    'pricePerDay' => $pricePerDay,
    'message' => $isAvailable ? 'Room is available for extension' : 'Room is not available for extension on the next day'
]);

$conflictStmt->close();
$stmt->close();
$mysqli->close();
