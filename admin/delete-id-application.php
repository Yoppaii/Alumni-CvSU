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

if (empty($_POST['application_id'])) {
    sendJsonResponse(false, 'Application ID is required');
}

$applicationId = filter_var($_POST['application_id'], FILTER_VALIDATE_INT);
if ($applicationId === false) {
    sendJsonResponse(false, 'Invalid application ID format');
}

try {
    $mysqli->begin_transaction();

    $checkStmt = $mysqli->prepare("SELECT id FROM alumni_id_cards WHERE id = ?");
    if (!$checkStmt) {
        throw new Exception($mysqli->error);
    }

    $checkStmt->bind_param('i', $applicationId);
    
    if (!$checkStmt->execute()) {
        throw new Exception($checkStmt->error);
    }
    
    $result = $checkStmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Application not found");
    }
    
    $checkStmt->close();

    $deleteStmt = $mysqli->prepare("DELETE FROM alumni_id_cards WHERE id = ?");
    if (!$deleteStmt) {
        throw new Exception($mysqli->error);
    }

    $deleteStmt->bind_param('i', $applicationId);
    
    if (!$deleteStmt->execute()) {
        throw new Exception($deleteStmt->error);
    }

    if ($deleteStmt->affected_rows === 0) {
        throw new Exception("Failed to delete application");
    }

    $mysqli->commit();

    sendJsonResponse(true, 'Application deleted successfully', [
        'application_id' => $applicationId
    ]);

} catch (Exception $e) {
    if ($mysqli->connect_errno === 0) {
        $mysqli->rollback();
    }
    
    sendJsonResponse(false, 'Error: ' . $e->getMessage());

} finally {
    if (isset($checkStmt) && $checkStmt instanceof mysqli_stmt) {
        $checkStmt->close();
    }
    if (isset($deleteStmt) && $deleteStmt instanceof mysqli_stmt) {
        $deleteStmt->close();
    }
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $mysqli->close();
    }
}