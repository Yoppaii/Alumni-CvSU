<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/main_db.php';
require_once 'walk-in-email-notif.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('Invalid request data');
    }

    $isWalkin = 'yes';

    $userId = 73; // Hardcoded user ID for keithjoshuabungalso123@gmail.com

    $userStmt = $mysqli->prepare("
        SELECT u.email, 
               CONCAT(ud.first_name, ' ', ud.last_name) as full_name 
        FROM users u 
        JOIN user ud ON u.id = ud.user_id 
        WHERE u.id = ?
    ");

    if (!$userStmt) {
        throw new Exception($mysqli->error);
    }

    $userStmt->bind_param("i", $userId);
    if (!$userStmt->execute()) {
        throw new Exception($userStmt->error);
    }

    $userResult = $userStmt->get_result();
    $userData = $userResult->fetch_assoc();

    if (!$userData) {
        throw new Exception('Please Verify Your Account');
    }

    // Resolve room name for email
    $roomName = match ($data['room_number']) {
        9 => "Board Room",
        10 => "Conference Room",
        11 => "Lobby",
        default => ($data['room_number'] >= 1 && $data['room_number'] <= 8) ? "Room " . $data['room_number'] : "Unknown Room"
    };

    // Prepare data
    $mattress_fee = $data['mattresses'] * 500;
    $total_price = $data['price']; // Optional: recalculate server-side for extra security

    // Prepare INSERT
    $bookingStmt = $mysqli->prepare("
        INSERT INTO bookings 
            (reference_number, user_id, room_number, occupancy, price, price_per_day,
             mattress_fee, total_price,
             arrival_date, arrival_time, departure_date, departure_time, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    if (!$bookingStmt) {
        throw new Exception($mysqli->error);
    }

    $bookingStmt->bind_param(
        "siiiidddssss",
        $data['reference_number'],
        $userId,
        $data['room_number'],
        $data['occupancy'],
        $data['price'],
        $data['price_per_day'],
        $mattress_fee,
        $total_price,
        $data['arrival_date'],
        $data['arrival_time'],
        $data['departure_date'],
        $data['departure_time']
    );


    if (!$bookingStmt->execute()) {
        throw new Exception($bookingStmt->error);
    }

    // Send confirmation email
    $emailSent = sendBookingStatusEmail(
        $userData['email'],
        $userData['full_name'],
        $data['reference_number'],
        'pending',
        $roomName,
        $data['arrival_date'],
        $data['departure_date'],
        $data['price'],
        $data['price_per_day'],
        $mattress_fee,
        $total_price,
        $data['arrival_time'],
        $data['departure_time']
    );

    // Clean up
    $userStmt->close();
    $bookingStmt->close();
    $mysqli->close();

    echo json_encode([
        'success' => true,
        'reference_number' => $data['reference_number'],
        'email_sent' => $emailSent
    ]);
} catch (Exception $e) {
    if (isset($userStmt)) $userStmt->close();
    if (isset($bookingStmt)) $bookingStmt->close();
    if (isset($mysqli)) $mysqli->close();

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
