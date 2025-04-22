<?php
require_once('../../main_db.php');

// Check if alumni ID card number is provided
if (!isset($_POST['alumni_id_card_no']) || empty($_POST['alumni_id_card_no'])) {
    echo json_encode(['success' => false, 'message' => 'Alumni ID Card Number is required']);
    exit;
}

$alumniIdCardNo = $_POST['alumni_id_card_no'];

// Begin transaction to ensure data integrity
$mysqli->begin_transaction();

try {
    // If there's a user_id associated with this alumni, delete the user record first
    $userStmt = $mysqli->prepare("DELETE FROM `user` WHERE `alumni_id_card_no` = ?");
    $userStmt->bind_param("i", $alumniIdCardNo);
    if (!$userStmt->execute()) {
        throw new Exception("Failed to delete associated user: " . $mysqli->error);
    }
    $userStmt->close();


    // Now delete the alumni record
    $alumniStmt = $mysqli->prepare("DELETE FROM `alumni` WHERE `alumni_id_card_no` = ?");
    $alumniStmt->bind_param("s", $alumniIdCardNo);

    if ($alumniStmt->execute()) {
        // Check if any row was affected
        if ($alumniStmt->affected_rows > 0) {
            // Commit the transaction
            $mysqli->commit();
            echo json_encode(['success' => true, 'message' => 'Alumni and associated user records permanently deleted']);
        } else {
            // No rows affected, alumni might not exist
            throw new Exception("Alumni not found");
        }
    } else {
        throw new Exception("Failed to execute query: " . $mysqli->error);
    }
    $alumniStmt->close();
} catch (Exception $e) {
    // Rollback the transaction on error
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close connection
$mysqli->close();
