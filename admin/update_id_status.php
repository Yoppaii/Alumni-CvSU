<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (ob_get_level()) ob_end_clean();
ob_start();

require '../main_db.php';

header('Content-Type: application/json');

function sendJsonResponse($success, $message, $data = null) {
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

if (empty($_POST['application_id']) || empty($_POST['status'])) {
    sendJsonResponse(false, 'Missing required parameters');
}

$applicationId = filter_var($_POST['application_id'], FILTER_VALIDATE_INT);
if ($applicationId === false) {
    sendJsonResponse(false, 'Invalid application ID');
}

$newStatus = trim($_POST['status']);

$allowedStatuses = ['pending', 'confirmed', 'declined', 'paid'];
if (!in_array($newStatus, $allowedStatuses)) {
    sendJsonResponse(false, 'Invalid status value');
}

try {
    $mysqli->begin_transaction();

    $stmt = $mysqli->prepare("UPDATE alumni_id_cards SET status = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception($mysqli->error);
    }

    $stmt->bind_param('si', $newStatus, $applicationId);
    
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No application found with ID: $applicationId");
    }

    $mysqli->commit();

    sendJsonResponse(true, 'Status updated successfully', [
        'new_status' => $newStatus,
        'application_id' => $applicationId
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
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $mysqli->close();
    }
}