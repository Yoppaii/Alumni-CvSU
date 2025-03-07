<?php
session_start();
header('Content-Type: application/json');
require_once '../main_db.php';

function sendResponse($success, $message) {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

try {
    if (!isset($mysqli)) {
        throw new Exception("Database connection not established");
    }

    if (!isset($_SESSION['user_id'])) {
        sendResponse(false, "Please log in to submit the form.");
    }

    if (empty($_POST)) {
        sendResponse(false, "No form data received");
    }

    $user_id = $_SESSION['user_id'];
    $last_name = $mysqli->real_escape_string(trim($_POST['last_name'] ?? ''));
    $first_name = $mysqli->real_escape_string(trim($_POST['first_name'] ?? ''));
    $middle_name = $mysqli->real_escape_string(trim($_POST['middle_name'] ?? ''));
    $email = $mysqli->real_escape_string(trim($_POST['email'] ?? ''));
    $course = $mysqli->real_escape_string(trim($_POST['course'] ?? ''));
    $year_graduated = isset($_POST['year_graduated']) ? (int)$_POST['year_graduated'] : 0;
    $highschool_graduated = $mysqli->real_escape_string(trim($_POST['highschool_graduated'] ?? ''));
    $membership_type = $mysqli->real_escape_string(trim($_POST['membership_type'] ?? ''));
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 
        ($membership_type === 'lifetime' ? 1500.00 : 500.00);

    if (empty($last_name) || empty($first_name) || empty($email) || 
        empty($course) || empty($year_graduated) || empty($highschool_graduated)) {
        sendResponse(false, "All required fields must be filled out.");
    }

    $sql = "INSERT INTO alumni_id_cards (user_id, last_name, first_name, middle_name, 
            email, course, year_graduated, highschool_graduated, membership_type, price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("isssssissd",
        $user_id,
        $last_name,
        $first_name,
        $middle_name,
        $email,
        $course,
        $year_graduated,
        $highschool_graduated,
        $membership_type,
        $price
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    $mysqli->close();
    
    sendResponse(true, "Alumni ID application submitted successfully!");

} catch (Exception $e) {
    sendResponse(false, "Error processing request");
}
?>