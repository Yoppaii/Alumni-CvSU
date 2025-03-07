<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

ob_start();

try {
    require_once '../main_db.php';
    
    header('Content-Type: application/json');

    if (!isset($_POST['user_id'])) {
        throw new Exception('User ID not provided');
    }

    $userId = intval($_POST['user_id']);

    $query = "SELECT 
        u.id,
        u.username,
        u.email,
        ud.first_name,
        ud.middle_name,
        ud.last_name,
        ud.position,
        ud.address,
        ud.second_address,
        ud.telephone,
        ud.phone_number,
        ud.accompanying_persons,
        ud.user_status,
        ud.verified
    FROM users u
    LEFT JOIN user ud ON u.id = ud.user_id
    WHERE u.id = ?";

    $stmt = $mysqli->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }
    
    $stmt->bind_param("i", $userId);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    if (!$userData) {
        throw new Exception("No user found with ID: " . $userId);
    }

    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $userData['id'],
            'username' => $userData['username'],
            'email' => $userData['email']
        ],
        'user_details' => [
            'first_name' => $userData['first_name'],
            'middle_name' => $userData['middle_name'],
            'last_name' => $userData['last_name'],
            'position' => $userData['position'],
            'address' => $userData['address'],
            'second_address' => $userData['second_address'],
            'telephone' => $userData['telephone'],
            'phone_number' => $userData['phone_number'],
            'accompanying_persons' => $userData['accompanying_persons'],
            'user_status' => $userData['user_status'],
            'verified' => $userData['verified']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
ob_end_flush();
?>