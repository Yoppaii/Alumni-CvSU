<?php
session_start();
require_once 'db-config.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'send_code':
            // Get email and password
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = ['status' => 'error', 'message' => 'Invalid email format'];
                break;
            }
            
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $response = ['status' => 'error', 'message' => 'Email already registered'];
                break;
            }
            
            // Generate verification code
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store in session
            $_SESSION['registration'] = [
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'code' => $code,
                'expires' => time() + 600 // 10 minutes
            ];
            
            // Send email (implement your email sending logic)
            // mail($email, "Verification Code", "Your verification code is: $code");
            
            $response = ['status' => 'success', 'message' => 'Verification code sent'];
            break;
            
        case 'verify_code':
            $email = $_POST['email'] ?? '';
            $code = $_POST['code'] ?? '';
            
            if (!isset($_SESSION['registration']) || $_SESSION['registration']['email'] !== $email) {
                $response = ['status' => 'error', 'message' => 'Invalid session'];
                break;
            }
            
            if ($_SESSION['registration']['expires'] < time()) {
                $response = ['status' => 'error', 'message' => 'Verification code expired'];
                break;
            }
            
            if ($_SESSION['registration']['code'] !== $code) {
                $response = ['status' => 'error', 'message' => 'Invalid verification code'];
                break;
            }
            
            $_SESSION['registration']['email_verified'] = true;
            $response = ['status' => 'success', 'message' => 'Email verified'];
            break;
            
        case 'create_account':
            // Ensure email is verified
            if (!isset($_SESSION['registration']) || !isset($_SESSION['registration']['email_verified'])) {
                $response = ['status' => 'error', 'message' => 'Email not verified'];
                break;
            }
            
            // Get form data
            $userType = $_POST['user_type'] ?? '';
            $email = $_SESSION['registration']['email'];
            $password = $_SESSION['registration']['password']; // Already hashed
            
            try {
                $pdo->beginTransaction();
                
                // Insert into users table
                $stmt = $pdo->prepare("INSERT INTO users (email, password, user_type, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$email, $password, $userType]);
                $userId = $pdo->lastInsertId();
                
                // Insert user details based on type
                if ($userType === 'alumni') {
                    $stmt = $pdo->prepare("INSERT INTO user_details (user_id, first_name, last_name, alumni_id, graduation_year, course, address, contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $userId,
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['alumni_id'],
                        $_POST['graduation_year'],
                        $_POST['course'],
                        $_POST['address'],
                        $_POST['contact']
                    ]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO user_details (user_id, first_name, last_name, address, contact) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $userId,
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['address'],
                        $_POST['contact']
                    ]);
                }
                
                $pdo->commit();
                
                // Clear registration session data
                unset($_SESSION['registration']);
                
                $response = ['status' => 'success', 'message' => 'Account created successfully'];
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $response = ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
            }
            break;
    }
}

echo json_encode($response);
