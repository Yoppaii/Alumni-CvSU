<?php
session_start();
header('Content-Type: application/json');
require_once '../../../main_db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        exit();
    }

    $stmt = $mysqli->prepare("SELECT admin_id, email, password_hash, first_name, last_name, role, is_active 
                            FROM admin_users 
                            WHERE email = ?");
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if ($user['is_active'] == 1 && password_verify($password, $user['password_hash'])) {
                $update_stmt = $mysqli->prepare("UPDATE admin_users SET last_login = NOW() WHERE admin_id = ?");
                $update_stmt->bind_param("i", $user['admin_id']);
                $update_stmt->execute();
                $update_stmt->close();

                $_SESSION['admin_id'] = $user['admin_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = $user['role'];

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/');
                    
                    $token_stmt = $mysqli->prepare("INSERT INTO remember_tokens (admin_id, token, expires_at) 
                                                  VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))");
                    $token_stmt->bind_param("is", $user['admin_id'], $token);
                    $token_stmt->execute();
                    $token_stmt->close();
                }

                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful! Redirecting...'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Invalid email or password'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid email or password'
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'An error occurred. Please try again.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method'
    ]);
}

$mysqli->close();
?>