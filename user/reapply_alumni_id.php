<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once '../main_db.php';

// Default response array
$response = [
    'success' => false,
    'message' => 'An error occurred while processing your request.'
];

// Get user ID from session
$user_id = $_SESSION['user_id'];

try {
    // Check if user has a declined application
    $check_stmt = $mysqli->prepare("SELECT id, last_name, first_name, middle_name, status FROM alumni_id_cards WHERE user_id = ? LIMIT 1");
    if (!$check_stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $application = $result->fetch_assoc();
    $check_stmt->close();

    // If no application found or status is not declined
    if (!$application || strtolower($application['status']) !== 'declined') {
        $response['message'] = 'No declined application found.';
        echo json_encode($response);
        exit();
    }

    // Delete the application record instead of resetting it
    $delete_stmt = $mysqli->prepare("DELETE FROM alumni_id_cards WHERE id = ?");
    if (!$delete_stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $delete_stmt->bind_param("i", $application['id']);
    $success = $delete_stmt->execute();
    $delete_stmt->close();


    if ($success) {
        // Log the reapplication
        $log_stmt = $mysqli->prepare("INSERT INTO alumni_id_logs ( user_id, last_name, first_name, middle_name, action, details, created_at) VALUES (?, ?, ?, ?, 'reapply', 'User reapplied after declined status', NOW())");
        if ($log_stmt) {
            $log_stmt->bind_param("isss", $application['id'], $application['last_name'], $application['first_name'], $application['middle_name']);
            $log_stmt->execute();
            $log_stmt->close();
        }

        $response['success'] = true;
        $response['message'] = 'Your application has been reset to Pending status.';
    } else {
        $response['message'] = 'Failed to update application status.';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
} finally {
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
