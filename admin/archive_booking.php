<?php
require_once '../main_db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

$bookingId = (int)$_POST['booking_id'];

if ($bookingId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

$stmt = $mysqli->prepare("UPDATE bookings SET is_archived = 1 WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL prepare error: ' . $mysqli->error]);
    exit;
}

$stmt->bind_param("i", $bookingId);
$result = $stmt->execute();

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Booking archived successfully',
        'data' => ['bookingId' => $bookingId]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to archive booking: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
