<?php
require_once '../main_db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = intval($_POST['user_id']);


// Get user detail and latest booking
$detailsStmt = $mysqli->prepare("SELECT first_name, last_name, middle_name, position, address, telephone, 
                                        phone_number, second_address, accompanying_persons, user_status, verified 
                                 FROM user WHERE user_id = ?");
$detailsStmt->bind_param("i", $user_id);
$detailsStmt->execute();
$detailsResult = $detailsStmt->get_result();
$userDetails = $detailsResult->fetch_assoc();

// Get latest booking with reference_number, mattress_fee, total_price
$bookingStmt = $mysqli->prepare("SELECT reference_number, mattress_fee, total_price 
                                 FROM bookings 
                                 WHERE user_id = ? 
                                 ORDER BY id DESC LIMIT 1");
$bookingStmt->bind_param("i", $user_id);
$bookingStmt->execute();
$bookingResult = $bookingStmt->get_result();
$booking = $bookingResult->fetch_assoc();

echo json_encode([
    'success' => true,
    'user_details' => $userDetails,
    'booking' => $booking
]);
