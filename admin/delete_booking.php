<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../main_db.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['booking_id']) || empty($_POST['booking_id'])) {
        throw new Exception('Booking ID is required');
    }

    $bookingId = intval($_POST['booking_id']);

    $deleteQuery = "DELETE FROM bookings WHERE id = ?";
    $deleteStmt = $mysqli->prepare($deleteQuery);
    
    if (!$deleteStmt) {
        throw new Exception('Failed to prepare delete statement');
    }

    $deleteStmt->bind_param("i", $bookingId);
    $deleteResult = $deleteStmt->execute();

    if (!$deleteResult) {
        throw new Exception('Failed to delete booking');
    }

    $deleteStmt->close();
    $mysqli->close();

    echo json_encode([
        'success' => true,
        'message' => 'Booking successfully deleted'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>