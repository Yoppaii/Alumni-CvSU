<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../main_db.php';  
require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $mysqli->prepare("SELECT `id` FROM `users` WHERE `email` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['message'] = 'This email address is already registered.';
        echo json_encode($response);
        exit();
    }

    $stmt->close();

    $verificationCode = rand(100000, 999999);

    setcookie('otp', $verificationCode, time() + 600, '/'); 
    setcookie('username', $username, time() + 600, '/');
    setcookie('email', $email, time() + 600, '/');
    setcookie('password', $password, time() + 600, '/');

    $subject = 'Email Verification Code';
    $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Email Verification</title>
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
        <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background: #1a73e8; color: white; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">Email Verification</h2>
            </div>
            
            <!-- Content -->
            <div style="padding: 20px;">
                <!-- Verification Section -->
                <div style="background: #e8f0fe; padding: 20px; border-radius: 8px;">
                    <h2 style="text-align: center; color: #1a73e8; margin-top: 0;">Welcome to Alumni Cavite State University, ' . htmlspecialchars($username) . '!</h2>
                    <p style="text-align: center;">Thank you for registering with us. To complete your registration, please use this verification code:</p>
                    
                    <div style="font-size: 32px; letter-spacing: 5px; background: white; padding: 15px; margin: 20px 0; border-radius: 4px; font-weight: bold; color: #1a73e8; text-align: center;">
                        ' . $verificationCode . '
                    </div>
                    
                    <p style="text-align: center;"><strong>This code will expire in 10 minutes.</strong></p>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 10px; border-radius: 4px; margin: 15px 0;">
                        If you didn\'t request this registration, please ignore this email.
                    </div>
                </div>

                <!-- Additional Information -->
                <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin: 15px 0;">
                    <p style="margin: 8px 0; color: #555;">We\'re excited to have you as part of our community! After verifying your email, you\'ll have access to:</p>
                    <ul style="color: #333; margin: 10px 0; padding-left: 20px;">
                        <li>Alumni network and resources</li>
                        <li>Community updates and events</li>
                        <li>Career opportunities</li>
                    </ul>
                </div>
            </div>
            
            <!-- Footer -->
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                <p style="margin: 5px 0;">This is an automated message, please do not reply to this email.</p>
                <p style="margin: 5px 0;">CvSU Alumni Portal - Welcome to our community</p>
                <p style="margin: 5px 0;">Time sent: ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'roomreservation.csumc@gmail.com';
        $mail->Password = 'bpqazltzfyacofjd'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('roomreservation.csumc@gmail.com', 'Alumni CvSU');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        
        $response['status'] = 'success';
        $response['message'] = 'Verification code sent successfully.';
        echo json_encode($response);
        exit();
        
    } catch (Exception $e) {
        $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        echo json_encode($response);
        exit();
    }
}
?>
