<?php
session_start();
require_once '../main_db.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (!isset($internal_include)) {
    header('Content-Type: application/json');
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['otp']) || !isset($_SESSION['user_email'])) {
    $result = [
        'status' => 'error',
        'message' => 'Missing required session data'
    ];
    return $result;
}

try {
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'roomreservation.csumc@gmail.com';
    $mail->Password = 'bpqazltzfyacofjd';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('roomreservation.csumc@gmail.com', 'CvSU Alumni Portal');
    $mail->addAddress($_SESSION['user_email']);
    $mail->Subject = 'CvSU Alumni Portal - Two-Factor Authentication Code';

    date_default_timezone_set('Asia/Manila');

    $plainText = "Two-Factor Authentication Verification\n" .
                 "Your verification code is: " . $_SESSION['otp'] . "\n" .
                 "This code will expire in 10 minutes.\n\n";

    $mail->AltBody = $plainText;

    $htmlBody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Two-Factor Authentication</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: #1a73e8; color: white; padding: 20px; text-align: center;">
            <h2 style="margin: 0;">Two-Factor Authentication</h2>
        </div>
        
        <!-- Content -->
        <div style="padding: 20px;">
            <!-- Verification Section -->
            <div style="background: #e8f0fe; padding: 20px; border-radius: 8px;">
                <h2 style="text-align: center; color: #1a73e8; margin-top: 0;">Two-Factor Authentication Code</h2>
                <p style="text-align: center;">Please use this code to verify your identity:</p>
                
                <div style="font-size: 32px; letter-spacing: 5px; background: white; padding: 15px; margin: 20px 0; border-radius: 4px; font-weight: bold; color: #1a73e8; text-align: center;">
                    ' . $_SESSION['otp'] . '
                </div>
                
                <p style="text-align: center;"><strong>This code will expire in 10 minutes.</strong></p>
                
                <div style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 10px; border-radius: 4px; margin: 15px 0;">
                    If you didn\'t request this code, please secure your account immediately.
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
            <p style="margin: 5px 0;">This is an automated message, please do not reply to this email.</p>
            <p style="margin: 5px 0;">CvSU Alumni Portal - Keeping your account secure</p>
            <p style="margin: 5px 0;">Time sent: ' . date('Y-m-d H:i:s') . ' (Philippine Time)</p>
        </div>
    </div>
</body>
</html>';

    $mail->MsgHTML($htmlBody);
    $mail->send();
    
    $result = [
        'status' => 'success',
        'message' => 'Verification code sent successfully'
    ];
} catch (Exception $e) {
    error_log("2FA Email Error: " . $e->getMessage());
    $result = [
        'status' => 'error',
        'message' => 'Failed to send verification code: ' . $e->getMessage()
    ];
}

if (!isset($internal_include)) {
    echo json_encode($result);
}

return $result;
?>