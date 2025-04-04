<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (ob_get_level()) ob_end_clean();
ob_start();

require '../main_db.php';

header('Content-Type: application/json');

function sendJsonResponse($success, $message, $data = null)
{
    $response = [
        'success' => $success,
        'message' => $message
    ];

    if ($data !== null) {
        $response = array_merge($response, $data);
    }

    if (ob_get_length()) ob_end_clean();
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Invalid request method');
}

if (empty($_POST['booking_id'])) {
    sendJsonResponse(false, 'Missing required parameters');
}

$bookingId = filter_var($_POST['booking_id'], FILTER_VALIDATE_INT);
if ($bookingId === false) {
    sendJsonResponse(false, 'Invalid booking ID');
}

$currentTime = date("Y-m-d H:i:s");

try {
    $mysqli->begin_transaction();

    // Check if booking status is "Confirmed"
    $stmtCheck = $mysqli->prepare("SELECT status FROM bookings WHERE id = ?");
    if (!$stmtCheck) {
        throw new Exception($mysqli->error);
    }

    $stmtCheck->bind_param('i', $bookingId);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    $row = $result->fetch_assoc();

    if (!$row || $row['status'] !== 'Confirmed') {
        throw new Exception("Booking is not in Confirmed status");
    }

    // Update departure time
    $stmt = $mysqli->prepare("UPDATE bookings SET departure_time = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception($mysqli->error);
    }

    $stmt->bind_param('si', $currentTime, $bookingId);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No booking found with ID: $bookingId");
    }

    $mysqli->commit();

    sendJsonResponse(true, 'Departure time updated successfully', [
        'booking_id' => $bookingId,
        'departure_time' => $currentTime
    ]);
} catch (Exception $e) {
    if ($mysqli->connect_errno === 0) {
        $mysqli->rollback();
    }

    sendJsonResponse(false, 'Database error: ' . $e->getMessage());
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($stmtCheck) && $stmtCheck instanceof mysqli_stmt) {
        $stmtCheck->close();
    }
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $mysqli->close();
    }
}
