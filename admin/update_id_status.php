<?php
// session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 0);

// if (ob_get_level()) ob_end_clean();
// ob_start();

// require '../main_db.php';

// header('Content-Type: application/json');

// function sendJsonResponse($success, $message, $data = null)
// {
//     $response = [
//         'success' => $success,
//         'message' => $message
//     ];

//     if ($data !== null) {
//         $response = array_merge($response, $data);
//     }

//     if (ob_get_length()) ob_end_clean();
//     echo json_encode($response);
//     exit;
// }

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     sendJsonResponse(false, 'Invalid request method');
// }

// if (empty($_POST['application_id']) || empty($_POST['status'])) {
//     sendJsonResponse(false, 'Missing required parameters');
// }

// $applicationId = filter_var($_POST['application_id'], FILTER_VALIDATE_INT);
// if ($applicationId === false) {
//     sendJsonResponse(false, 'Invalid application ID');
// }

// $newStatus = trim($_POST['status']);

// $allowedStatuses = ['pending', 'confirmed', 'declined', 'paid'];
// if (!in_array($newStatus, $allowedStatuses)) {
//     sendJsonResponse(false, 'Invalid status value');
// }

// try {
//     $mysqli->begin_transaction();

//     // First, get the user_id for this application
//     $stmtSelect = $mysqli->prepare("SELECT user_id FROM alumni_id_cards WHERE id = ?");
//     if (!$stmtSelect) {
//         throw new Exception($mysqli->error);
//     }
//     $stmtSelect->bind_param('i', $applicationId);
//     if (!$stmtSelect->execute()) {
//         throw new Exception($stmtSelect->error);
//     }
//     $result = $stmtSelect->get_result();
//     if ($result->num_rows === 0) {
//         throw new Exception("No application found with ID: $applicationId");
//     }
//     $row = $result->fetch_assoc();
//     $userId = $row['user_id'];
//     $stmtSelect->close();

//     // Now update the status
//     $stmtUpdate = $mysqli->prepare("UPDATE alumni_id_cards SET status = ? WHERE id = ?");
//     if (!$stmtUpdate) {
//         throw new Exception($mysqli->error);
//     }
//     $stmtUpdate->bind_param('si', $newStatus, $applicationId);
//     if (!$stmtUpdate->execute()) {
//         throw new Exception($stmtUpdate->error);
//     }
//     if ($stmtUpdate->affected_rows === 0) {
//         throw new Exception("No application updated with ID: $applicationId");
//     }
//     $stmtUpdate->close();

//     $mysqli->commit();

//     sendJsonResponse(true, 'Status updated successfully', [
//         'new_status' => $newStatus,
//         'application_id' => $applicationId,
//         'user_id' => $userId
//     ]);
// } catch (Exception $e) {
//     if ($mysqli->connect_errno === 0) {
//         $mysqli->rollback();
//     }

//     sendJsonResponse(false, 'Database error: ' . $e->getMessage());
// } finally {
//     if (isset($stmtSelect) && $stmtSelect instanceof mysqli_stmt) {
//         $stmtSelect->close();
//     }
//     if (isset($stmtUpdate) && $stmtUpdate instanceof mysqli_stmt) {
//         $stmtUpdate->close();
//     }
//     if (isset($mysqli) && $mysqli instanceof mysqli) {
//         $mysqli->close();
//     }
// }

// Include database connection
include '../main_db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if the required parameters are set
if (!isset($_POST['application_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Get parameters
$application_id = $_POST['application_id'];
$status = $_POST['status'];
$decline_reason = isset($_POST['decline_reason']) ? $_POST['decline_reason'] : null;

// Validate application_id
if (!is_numeric($application_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
    exit;
}

// Validate status
$allowed_statuses = ['pending', 'confirmed', 'declined', 'paid'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Start transaction
$mysqli->begin_transaction();

try {
    // Update application status
    $update_query = "UPDATE alumni_id_cards SET status = ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param('si', $status, $application_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update application status: " . $mysqli->error);
    }

    // If declining and reason provided, store the reason
    if ($status === 'declined' && $decline_reason) {
        // Current admin user ID (assuming it's stored in session)
        $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

        $reason_query = "INSERT INTO alumni_id_declined_reasons (application_id, reason, declined_by) VALUES (?, ?, ?)";
        $reason_stmt = $mysqli->prepare($reason_query);
        $reason_stmt->bind_param('isi', $application_id, $decline_reason, $admin_id);

        if (!$reason_stmt->execute()) {
            throw new Exception("Failed to store decline reason: " . $mysqli->error);
        }

        $reason_stmt->close();
    }

    // Send email notification about status change
    // Get user email from application
    $email_query = "SELECT a.*, u.email FROM alumni_id_cards a 
                    LEFT JOIN users u ON a.user_id = u.id 
                    WHERE a.id = ?";
    $email_stmt = $mysqli->prepare($email_query);
    $email_stmt->bind_param('i', $application_id);
    $email_stmt->execute();
    $result = $email_stmt->get_result();

    if ($application = $result->fetch_assoc()) {
        $to = $application['email'];
        $subject = "Your Alumni ID Card Application Status Updated";

        // Different email content based on status
        switch ($status) {
            case 'confirmed':
                $message = "Dear {$application['first_name']},\n\n";
                $message .= "Your Alumni ID Card application has been confirmed. ";
                $message .= "Please proceed with the payment to complete the process.\n\n";
                $message .= "Thank you for your patience.\n\n";
                $message .= "Best regards,\nCvSU Alumni Association";
                break;

            case 'declined':
                $message = "Dear {$application['first_name']},\n\n";
                $message .= "We regret to inform you that your Alumni ID Card application has been declined.\n\n";

                // Include the reason if available
                if ($decline_reason) {
                    $message .= "Reason: $decline_reason\n\n";
                }

                $message .= "If you have any questions, please contact our support team.\n\n";
                $message .= "Best regards,\nCvSU Alumni Association";
                break;

            case 'paid':
                $message = "Dear {$application['first_name']},\n\n";
                $message .= "We have confirmed your payment for the Alumni ID Card application. ";
                $message .= "Your ID card is now being processed and will be ready for pickup soon.\n\n";
                $message .= "Thank you for your support.\n\n";
                $message .= "Best regards,\nCvSU Alumni Association";
                break;

            default:
                $message = "Dear {$application['first_name']},\n\n";
                $message .= "Your Alumni ID Card application status has been updated to: " . ucfirst($status) . ".\n\n";
                $message .= "Thank you for your patience.\n\n";
                $message .= "Best regards,\nCvSU Alumni Association";
                break;
        }

        // Set email headers
        $headers = "From: noreply@cvsu-alumni.edu.ph\r\n";
        $headers .= "Reply-To: support@cvsu-alumni.edu.ph\r\n";

        // Send email (commented out for testing)
        // mail($to, $subject, $message, $headers);
    }

    $email_stmt->close();

    // Commit transaction
    $mysqli->commit();

    echo json_encode(['success' => true, 'message' => 'Application status updated successfully']);
} catch (Exception $e) {
    // Rollback transaction on error
    $mysqli->rollback();

    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close the statement and connection
if (isset($stmt)) {
    $stmt->close();
}
$mysqli->close();
