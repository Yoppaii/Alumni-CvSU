<?php
require_once '../../main_db.php';

function sanitize_input($data) {
    global $mysqli;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $mysqli->real_escape_string($data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = sanitize_input($_POST['firstName']);
    $lastName = sanitize_input($_POST['lastName']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    $username = strtolower($firstName . "." . $lastName);

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $role = 'admin';
    $is_active = 1;

    $check_email = $mysqli->prepare("SELECT admin_id FROM admin_users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $response = array(
            'status' => 'error',
            'message' => 'Email already exists'
        );
    } else {
        $stmt = $mysqli->prepare("INSERT INTO admin_users (username, first_name, last_name, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssssi", $username, $firstName, $lastName, $email, $password_hash, $role, $is_active);
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success',
                'message' => 'Registration successful!'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Registration failed: ' . $mysqli->error
            );
        }

        $stmt->close();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>