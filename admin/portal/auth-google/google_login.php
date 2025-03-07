<?php
session_start();
require_once '../../main_db.php'; 

define('GOOGLE_CLIENT_ID', '1092831642406-360f9vquupdb0vk06ueij1vjgkqu601c.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/RS/portal/auth-google/google_login.php');

if (isset($_GET['code'])) {
    $token_url = 'https://oauth2.googleapis.com/token';
    $data = array(
        'code' => $_GET['code'],
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    );

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        header('Location: ../login.php?error=google_auth_failed');
        exit;
    }
    curl_close($ch);

    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        $user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $user_info_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token_data['access_token']
        ));
        $user_info = curl_exec($ch);
        
        if(curl_errno($ch)) {
            error_log('Curl error: ' . curl_error($ch));
            header('Location: ../login.php?error=google_auth_failed');
            exit;
        }
        curl_close($ch);

        $google_user = json_decode($user_info, true);

        if (isset($google_user['email'])) {
            $email = $google_user['email'];
            $name = $google_user['name'] ?? '';
            $stmt = $mysqli->prepare('SELECT id, username, email FROM users WHERE email = ?');
            if (!$stmt) {
                error_log('Database prepare error: ' . $mysqli->error);
                header('Location: ../login.php?error=database_error');
                exit;
            }
            
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $session_token = bin2hex(random_bytes(32));
                
                $update_stmt = $mysqli->prepare("UPDATE users SET session_token = ? WHERE id = ?");
                $update_stmt->bind_param("si", $session_token, $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                $_SESSION['session_token'] = $session_token;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['username'] = $user['username'];
                
                header('Location: /RS/Account');
                exit;
            } else {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)) . rand(100, 999);
                $random_password = bin2hex(random_bytes(8));
                $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
                
                $insert_stmt = $mysqli->prepare("INSERT INTO users (username, email, password, registration_method) VALUES (?, ?, ?, 'google')");
                if (!$insert_stmt) {
                    error_log('Database prepare error: ' . $mysqli->error);
                    header('Location: ../login.php?error=database_error');
                    exit;
                }
                
                $insert_stmt->bind_param("sss", $username, $email, $hashed_password);
                
                if ($insert_stmt->execute()) {
                    $new_user_id = $mysqli->insert_id;
                    $session_token = bin2hex(random_bytes(32));
                    
                    $update_stmt = $mysqli->prepare("UPDATE users SET session_token = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $session_token, $new_user_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    $_SESSION['session_token'] = $session_token;
                    $_SESSION['user_id'] = $new_user_id;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['username'] = $username;
                    
                    header('Location: /RS/Account');
                    exit;
                }
                $insert_stmt->close();
            }
            $stmt->close();
        }
    }
    
    header('Location: ../login.php?error=google_auth_failed');
    exit;
}

$params = array(
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'online'
);

$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header('Location: ' . $auth_url);
exit;
?>