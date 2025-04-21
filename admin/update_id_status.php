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

    // First, get the user_id for this application
    $stmtSelect = $mysqli->prepare("SELECT user_id FROM alumni_id_cards WHERE id = ?");
    if (!$stmtSelect) {
        throw new Exception($mysqli->error);
    }
    $stmtSelect->bind_param('i', $applicationId);
    if (!$stmtSelect->execute()) {
        throw new Exception($stmtSelect->error);
    }
    $result = $stmtSelect->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("No application found with ID: $applicationId");
    }
    $row = $result->fetch_assoc();
    $userId = $row['user_id'];
    $stmtSelect->close();

    // Now update the status
    $stmtUpdate = $mysqli->prepare("UPDATE alumni_id_cards SET status = ? WHERE id = ?");
    if (!$stmtUpdate) {
        throw new Exception($mysqli->error);
    }
    $stmtUpdate->bind_param('si', $newStatus, $applicationId);
    if (!$stmtUpdate->execute()) {
        throw new Exception($stmtUpdate->error);
    }
    if ($stmtUpdate->affected_rows === 0) {
        throw new Exception("No application updated with ID: $applicationId");
    }
    $stmtUpdate->close();

    $mysqli->commit();

    sendJsonResponse(true, 'Status updated successfully', [
        'new_status' => $newStatus,
        'application_id' => $applicationId,
        'user_id' => $userId
    ]);
} catch (Exception $e) {
    if ($mysqli->connect_errno === 0) {
        $mysqli->rollback();
    }

    sendJsonResponse(false, 'Database error: ' . $e->getMessage());
} finally {
    if (isset($stmtSelect) && $stmtSelect instanceof mysqli_stmt) {
        $stmtSelect->close();
    }
    if (isset($stmtUpdate) && $stmtUpdate instanceof mysqli_stmt) {
        $stmtUpdate->close();
    }
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $mysqli->close();
    }
}
