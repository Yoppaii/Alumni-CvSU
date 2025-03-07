<?php
session_start();
require_once '../main_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to submit your profile.'
    ]);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $middle_name = !empty($_POST['middle_name']) ? trim($_POST['middle_name']) : null;
    $position = trim($_POST['position']);
    $address = trim($_POST['address']);
    $telephone = trim($_POST['telephone']);
    $phone_number = trim($_POST['phone_number']);
    $second_address = !empty($_POST['second_address']) ? trim($_POST['second_address']) : null;
    $accompanying_persons = !empty($_POST['accompanying_persons']) ? trim($_POST['accompanying_persons']) : null;
    $user_status = $_POST['user_status'];
    $verified = 0;

    if (empty($first_name) || empty($last_name) || empty($position) || 
        empty($address) || empty($telephone) || empty($phone_number)) {
        throw new Exception('All required fields must be filled out.');
    }

    if (!preg_match("/^[0-9\-\(\)\/\+\s]*$/", $telephone)) {
        throw new Exception('Invalid telephone number format.');
    }
    if (!preg_match("/^[0-9\-\(\)\/\+\s]*$/", $phone_number)) {
        throw new Exception('Invalid mobile number format.');
    }

    $check_sql = "SELECT COUNT(*) as count FROM `user` WHERE `user_id` = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $update_sql = "UPDATE `user` SET 
            `first_name` = ?,
            `last_name` = ?,
            `middle_name` = ?,
            `position` = ?,
            `address` = ?,
            `telephone` = ?,
            `phone_number` = ?,
            `second_address` = ?,
            `accompanying_persons` = ?,
            `user_status` = ?,
            `verified` = 1
            WHERE `user_id` = ?";
            
        $stmt = $mysqli->prepare($update_sql);
        $stmt->bind_param(
            "ssssssssssi",
            $first_name, $last_name, $middle_name, $position,
            $address, $telephone, $phone_number, $second_address,
            $accompanying_persons, $user_status, $user_id
        );
    } else {
        $insert_sql = "INSERT INTO `user` (
            `user_id`, `first_name`, `last_name`, `middle_name`, `position`, 
            `address`, `telephone`, `phone_number`, `second_address`, 
            `accompanying_persons`, `user_status`, `verified`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $mysqli->prepare($insert_sql);
        $stmt->bind_param(
            "issssssssssi",
            $user_id, $first_name, $last_name, $middle_name, $position,
            $address, $telephone, $phone_number, $second_address,
            $accompanying_persons, $user_status, $verified
        );
    }

    $mysqli->begin_transaction();

    if (!$stmt->execute()) {
        throw new Exception('Error processing profile: ' . $stmt->error);
    }

    $verify_sql = "UPDATE `user` SET `verified` = 1 WHERE `user_id` = ?";
    $verify_stmt = $mysqli->prepare($verify_sql);
    $verify_stmt->bind_param("i", $user_id);
    
    if (!$verify_stmt->execute()) {
        throw new Exception('Error updating verification status: ' . $verify_stmt->error);
    }

    $mysqli->commit();

    $_SESSION['user_verified'] = 1;
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;

    echo json_encode([
        'status' => 'success',
        'message' => 'Profile submitted Successfully',
        'email' => $_SESSION['email'] ?? null 
    ]);

} catch (Exception $e) {
    if ($mysqli && $mysqli->connect_errno == 0) {
        $mysqli->rollback();
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);

} finally {
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($stmt)) $stmt->close();
    if (isset($verify_stmt)) $verify_stmt->close();
    if (isset($mysqli)) $mysqli->close();
}
?>