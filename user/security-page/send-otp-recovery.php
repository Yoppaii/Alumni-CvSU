<?php
session_start();
require_once '../../main_db.php';
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$action = $_POST['action'];

if (!$email) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
    exit;
}

try {
    $checkUserEmailStmt = $mysqli->prepare("SELECT email FROM users WHERE id = ?");
    $checkUserEmailStmt->bind_param("i", $_SESSION['user_id']);
    $checkUserEmailStmt->execute();
    $userResult = $checkUserEmailStmt->get_result();
    $userData = $userResult->fetch_assoc();
    
    if ($userData && $userData['email'] === $email) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You cannot use your login email as your recovery email. Please use a different email address.'
        ]);
        exit;
    }
    
    $device_stmt = $mysqli->prepare("
        SELECT device_type, operating_system, browser, ip_address, last_active, created_at 
        FROM device_history 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $device_stmt->bind_param("i", $_SESSION['user_id']);
    $device_stmt->execute();
    $device_result = $device_stmt->get_result();
    $device_data = $device_result->fetch_assoc();
    
    $otp = sprintf("%06d", mt_rand(0, 999999));
    $timestamp = date('Y-m-d H:i:s');

    $_SESSION['otp'] = [
        'code' => $otp,
        'email' => $email,
        'expires' => time() + (10 * 60),
        'action' => $action
    ];

    $actionTitle = $action === 'add' ? 'Recovery Email Addition' : 'Recovery Email Change';
    
    $htmlBody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>' . $actionTitle . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: #1a73e8; color: white; padding: 20px; text-align: center;">
            <h2 style="margin: 0;">' . $actionTitle . '</h2>
        </div>
        
        <!-- Content -->
        <div style="padding: 20px;">
            <!-- Verification Section -->
            <div style="background: #e8f0fe; padding: 20px; border-radius: 8px;">
                <h2 style="text-align: center; color: #1a73e8; margin-top: 0;">Email Verification</h2>
                <p style="text-align: center;">Please use this code to verify your ' . ($action === 'add' ? 'new' : 'updated') . ' recovery email:</p>
                
                <div style="font-size: 32px; letter-spacing: 5px; background: white; padding: 15px; margin: 20px 0; border-radius: 4px; font-weight: bold; color: #1a73e8; text-align: center;">
                    ' . $otp . '
                </div>
                
                <p style="text-align: center;"><strong>This code will expire in 10 minutes.</strong></p>
                
                <div style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 10px; border-radius: 4px; margin: 15px 0;">
                    If you didn\'t request this change, please secure your account immediately.
                </div>
            </div>

            <!-- Divider -->
            <div style="border-top: 2px solid #e9ecef; margin: 30px 0;"></div>

            <!-- Device Info -->
            <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin: 15px 0;">
                <div style="margin: 8px 0; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                    <span style="font-weight: bold; color: #555;">Device Type:</span>
                    <span style="color: #333;">' . htmlspecialchars($device_data['device_type']) . '</span>
                </div>
                <div style="margin: 8px 0; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                    <span style="font-weight: bold; color: #555;">Operating System:</span>
                    <span style="color: #333;">' . htmlspecialchars($device_data['operating_system']) . '</span>
                </div>
                <div style="margin: 8px 0; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                    <span style="font-weight: bold; color: #555;">Browser:</span>
                    <span style="color: #333;">' . htmlspecialchars($device_data['browser']) . '</span>
                </div>
                <div style="margin: 8px 0; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                    <span style="font-weight: bold; color: #555;">IP Address:</span>
                    <span style="color: #333;">' . htmlspecialchars($device_data['ip_address']) . '</span>
                </div>
                <div style="margin: 8px 0; padding-bottom: 8px;">
                    <span style="font-weight: bold; color: #555;">Last Active:</span>
                    <span style="color: #333;">' . date('Y-m-d H:i:s', strtotime($device_data['last_active'])) . '</span>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
            <p style="margin: 5px 0;">This is an automated message, please do not reply to this email.</p>
            <p style="margin: 5px 0;">CvSU Alumni Portal - Keeping your account secure</p>
            <p style="margin: 5px 0;">Time sent: ' . $timestamp . '</p>
        </div>
    </div>
</body>
</html>';

    $plainText = $actionTitle . "\n" .
                 "Your verification code is: " . $otp . "\n" .
                 "This code will expire in 10 minutes.\n\n" .
                 "Device Information:\n" .
                 "Device Type: " . $device_data['device_type'] . "\n" .
                 "Operating System: " . $device_data['operating_system'] . "\n" .
                 "Browser: " . $device_data['browser'] . "\n" .
                 "IP Address: " . $device_data['ip_address'] . "\n" .
                 "Last Active: " . date('Y-m-d H:i:s', strtotime($device_data['last_active'])) . "\n\n" .
                 "If you didn't request this change, please secure your account immediately.\n\n" .
                 "Time sent: " . $timestamp;

    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'roomreservation.csumc@gmail.com';
    $mail->Password = 'bpqazltzfyacofjd';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('roomreservation.csumc@gmail.com', 'CvSU Alumni Portal');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = $actionTitle;
    $mail->Body = $htmlBody;
    $mail->AltBody = $plainText;
    $mail->CharSet = 'UTF-8';

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Failed to send OTP: {$mail->ErrorInfo}"]);
}
?>