<?php
session_start();
require_once '../main_db.php';

try {
    $mysqli->begin_transaction();

    $user_id = $_SESSION['user_id'];
    $alumni_id = $_POST['alumni_id']; 
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $position = $_POST['position'];
    $address = $_POST['address'];
    $telephone = $_POST['telephone'];
    $phone_number = $_POST['phone_number'];
    $second_address = $_POST['second_address'];
    $accompanying_persons = $_POST['accompanying_persons'];
    $user_status = 'Alumni';
    $verified = 1;

    $sql = "INSERT INTO `user`(`user_id`, `alumni_id_card_no`, `first_name`, `last_name`, `middle_name`, `position`, `address`, `telephone`, `phone_number`, `second_address`, `accompanying_persons`, `user_status`, `verified`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("isssssssssssi", $user_id, $alumni_id, $first_name, $last_name, $middle_name, $position, $address, $telephone, $phone_number, $second_address, $accompanying_persons, $user_status, $verified);
    $stmt->execute();

    $update_sql = "UPDATE `alumni` SET `verify` = 'used' WHERE `alumni_id_card_no` = ?";
    $update_stmt = $mysqli->prepare($update_sql);
    $update_stmt->bind_param("s", $alumni_id);
    $update_stmt->execute();

    $mysqli->commit();
    echo json_encode(['success' => true, 'message' => 'Profile saved successfully.']);

} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => 'Error saving profile: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $update_stmt->close();
    $mysqli->close();
}
?>