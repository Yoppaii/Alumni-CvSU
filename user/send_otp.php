<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../main_db.php';  
require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];
$otp = $data['otp'];

$timestamp = date('F j, Y \a\t g:i A');

$subject = 'Two-Step Verification Code - CvSU Alumni Portal';
$message = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>
<body style='margin: 0; padding: 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f4f4;'>
    <table role='presentation' cellspacing='0' cellpadding='0' border='0' align='center' width='100%' style='max-width: 600px; margin: 0 auto; padding: 20px;'>

        <tr>
            <td style='background-color: #ffffff; padding: 40px 30px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <h1 style='color: #2d6936; margin: 0 0 20px; font-size: 24px; font-weight: bold;'>Verify Your Identity</h1>
                
                <p style='margin: 0 0 15px;'>To protect your CvSU Alumni Portal account, we need to verify your identity. Enter the following verification code:</p>
                
                <!-- OTP Container -->
                <div style='background-color: #ecfdf5; padding: 20px; border-radius: 6px; margin: 25px 0; text-align: center;'>
                    <span style='font-size: 32px; letter-spacing: 5px; color: #2d6936; font-weight: bold; font-family: monospace;'>$otp</span>
                </div>
                
                <!-- Security Notice -->
                <div style='background-color: #fff8dc; padding: 15px; border-left: 4px solid #ffd700; margin: 20px 0;'>
                    <p style='margin: 0; font-size: 14px;'>
                        <strong>Security Notice:</strong> This code will expire in 10 minutes. Never share this code with anyone, including CvSU staff.
                    </p>
                </div>
                
                <!-- Additional Info -->
                <p style='margin: 20px 0 10px; font-size: 14px; color: #666666;'>
                    Request made from: [IP Address]<br>
                    Date: $timestamp
                </p>
                
                <p style='margin: 20px 0; font-size: 14px;'>
                    If you didn't request this code, please secure your account by:
                    <br>1. Changing your password immediately
                    <br>2. Contacting our support team
                </p>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style='padding: 20px 30px; text-align: center; font-size: 12px; color: #666666;'>
                <p style='margin: 0 0 10px;'>This is an automated message, please do not reply.</p>
                <p style='margin: 0;'>
                    &copy; " . date('Y') . " CvSU Alumni Portal. All rights reserved.<br>
                    Cavite State University - Main Campus
                </p>
            </td>
        </tr>
    </table>
</body>
</html>";

$mail = new PHPMailer(true);
try {
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
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->send();
    
    $response['status'] = 'success';
    $response['message'] = 'Verification code sent successfully.';
    echo json_encode($response);
    exit();
    
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    echo json_encode($response);
    exit();
}
?>