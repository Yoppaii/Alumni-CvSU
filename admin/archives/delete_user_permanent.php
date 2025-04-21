<?php
require_once('../../main_db.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['userId']) || empty($_POST['userId'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$userId = (int)$_POST['userId'];

$mysqli->begin_transaction();

try {
    // 1. Get all personal_info IDs for this user
    $stmt = $mysqli->prepare("SELECT id FROM personal_info WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $personalInfoIds = [];
    while ($row = $result->fetch_assoc()) {
        $personalInfoIds[] = $row['id'];
    }
    $stmt->close();

    // 2. Delete from unemployment_reasons for each personal_info_id
    if (!empty($personalInfoIds)) {
        $stmt = $mysqli->prepare("DELETE FROM unemployment_reasons WHERE personal_info_id = ?");
        foreach ($personalInfoIds as $pid) {
            $stmt->bind_param("i", $pid);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete unemployment_reasons for personal_info_id $pid: " . $stmt->error);
            }
        }
        $stmt->close();
    }

    // 3. Delete from other tables referencing personal_info_id
    $tablesByPersonalInfo = [
        "other_alumni",
        "competencies",
        "training_studies",
        "job_experience",
        "job_duration",
        "employment_data",
        "educational_background",
        "accepting_reasons",
        "staying_reasons"
    ];
    foreach ($tablesByPersonalInfo as $table) {
        if (!empty($personalInfoIds)) {
            $stmt = $mysqli->prepare("DELETE FROM $table WHERE personal_info_id = ?");
            foreach ($personalInfoIds as $pid) {
                $stmt->bind_param("i", $pid);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to delete from $table for personal_info_id $pid: " . $stmt->error);
                }
            }
            $stmt->close();
        }
    }

    // 4. Delete from personal_info for this user
    $stmt = $mysqli->prepare("DELETE FROM personal_info WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete personal_info: " . $stmt->error);
    }
    $stmt->close();

    // 5. Delete from tables referencing user_id
    $tablesByUserId = [
        "accepting_reasons",
        "educational_background",
        "employment_data",
        "job_duration",
        "job_experience",
        "staying_reasons",
        "training_studies",
        "unemployment_reasons"
    ];
    foreach ($tablesByUserId as $table) {
        $stmt = $mysqli->prepare("DELETE FROM $table WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete from $table for user_id: " . $stmt->error);
        }
        $stmt->close();
    }

    // 6. Finally, delete the user
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete user: " . $stmt->error);
    }
    $stmt->close();

    $mysqli->commit();

    echo json_encode(['success' => true, 'message' => 'User and all related records deleted successfully']);
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$mysqli->close();
