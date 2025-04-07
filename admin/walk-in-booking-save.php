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

    $roomName = "";
    if ($data['room_number'] >= 1 && $data['room_number'] <= 8) {
        $roomName = "Room " . $data['room_number'];
    } elseif ($data['room_number'] == 9) {
        $roomName = "Board Room";
    } elseif ($data['room_number'] == 10) {
        $roomName = "Conference Room";
    } elseif ($data['room_number'] == 11) {
        $roomName = "Lobby";
    }

    $bookingStmt = $mysqli->prepare("INSERT INTO bookings 
    (reference_number, user_id, room_number, occupancy, price, price_per_day,
     arrival_date, arrival_time, departure_date, departure_time, status, is_walkin) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");

    $bookingStmt->bind_param(
        "siiiidsssss",
        $data['reference_number'],
        $userId,
        $data['room_number'],
        $data['occupancy'],
        $data['price'],
        $data['price_per_day'],
        $data['arrival_date'],
        $data['arrival_time'],
        $data['departure_date'],
        $data['departure_time'],
        $isWalkin
    );


    if (!$bookingStmt->execute()) {
        throw new Exception($bookingStmt->error);
    }

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
        $data['arrival_time'],
        $data['departure_time']
    );

    if (isset($userStmt)) $userStmt->close();
    if (isset($bookingStmt)) $bookingStmt->close();
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
