<?php
require_once('../../main_db.php'); // Adjust path as needed

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if alumni_id_card_no is provided
if (!isset($_POST['alumni_id_card_no']) || empty($_POST['alumni_id_card_no'])) {
    echo json_encode(['success' => false, 'message' => 'Alumni ID Card Number is required']);
    exit;
}

$alumniIdCardNo = $_POST['alumni_id_card_no'];

// Begin transaction to ensure data integrity
$mysqli->begin_transaction();

try {
    // Delete the alumni from the database permanently
    $stmt = $mysqli->prepare("DELETE FROM `alumni` WHERE `alumni_id_card_no` = ?");
    $stmt->bind_param("s", $alumniIdCardNo);

    if ($stmt->execute()) {
        // Check if any row was affected
        if ($stmt->affected_rows > 0) {
            // Commit the transaction
            $mysqli->commit();
            echo json_encode(['success' => true, 'message' => 'Alumni permanently deleted']);
        } else {
            // No rows affected, alumni might not exist
            throw new Exception("Alumni not found");
        }
    } else {
        throw new Exception("Failed to execute query: " . $mysqli->error);
    }
} catch (Exception $e) {
    // Rollback the transaction on error
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close statement and connection
if (isset($stmt)) {
    $stmt->close();
}
$mysqli->close();
