<?php
require_once 'main_db.php';

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['final_submission'])) {
    // Check if email has been verified
    if (!isset($_COOKIE['email_verified']) || $_COOKIE['email_verified'] !== '1') {
        $response['status'] = 'error';
        $response['message'] = 'Email not verified. Please complete verification first.';
        echo json_encode($response);
        exit();
    }

    // Get email and password from cookies
    if (!isset($_COOKIE['email']) || !isset($_COOKIE['password'])) {
        $response['status'] = 'error';
        $response['message'] = 'Session expired. Please restart the registration process.';
        echo json_encode($response);
        exit();
    }

    $email = $_COOKIE['email'];
    $password = $_COOKIE['password']; // This should be already hashed from Sending-Code.php

    // Prepare basic user data
    $stmt = $mysqli->prepare("INSERT INTO `users`(`email`, `password`) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        $userId = $mysqli->insert_id; // Get the newly inserted user ID

        // Now insert additional profile data based on user type
        $userType = $_POST['user_type'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $phone = $_POST['phone'];

        // Common profile data
        $profileStmt = $mysqli->prepare("INSERT INTO `user_profiles`(`user_id`, `firstname`, `lastname`, `phone`) VALUES (?, ?, ?, ?, ?)");
        $profileStmt->bind_param("issss", $userId, $firstname, $lastname, $phone, $userType);

        if ($profileStmt->execute()) {
            // Handle specific data based on user type
            if ($userType === 'alumni') {
                $gradYear = $_POST['graduation_year'];
                $course = $_POST['course'];
                $studentId = isset($_POST['student_id']) ? $_POST['student_id'] : '';

                $alumniStmt = $mysqli->prepare("INSERT INTO `alumni_data`(`user_id`, `graduation_year`, `course`, `student_id`) VALUES (?, ?, ?, ?)");
                $alumniStmt->bind_param("isss", $userId, $gradYear, $course, $studentId);
                $alumniStmt->execute();
            } else if ($userType === 'guest') {
                $position = $_POST['position'];

                $guestStmt = $mysqli->prepare("INSERT INTO `user`(`user_id``, `position`) VALUES (?, ?, ?)");
                $guestStmt->bind_param("iss", $userId, $position);
                $guestStmt->execute();
            }

            // Clear all cookies related to registration
            setcookie('email_verified', '', time() - 3600, '/');
            setcookie('email', '', time() - 3600, '/');
            setcookie('password', '', time() - 3600, '/');

            $response['status'] = 'success';
            $response['message'] = 'Account successfully created.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to save profile information.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to create account. Email may already be registered.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
exit();
