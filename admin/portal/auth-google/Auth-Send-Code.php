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
    $email = $_POST['email'];

    $stmt = $mysqli->prepare("SELECT `id` FROM `google-users` WHERE `email` = ?");
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
    setcookie('email', $email, time() + 600, '/');

    $subject = 'Email Verification Code';
    $message = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f9;
                    color: #333;
                    padding: 20px;
                }
                .container {
                    background-color: #fff;
                    border-radius: 8px;
                    padding: 20px;
                    max-width: 600px;
                    margin: 0 auto;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                h2 {
                    color: #5c6bc0;
                }
                p {
                    font-size: 16px;
                    line-height: 1.6;
                }
                .cta {
                    display: inline-block;
                    background-color: #5c6bc0;
                    color: white;
                    padding: 10px 20px;
                    border-radius: 5px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-top: 20px;
                }
                .footer {
                    font-size: 14px;
                    color: #888;
                    text-align: center;
                    margin-top: 40px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Welcome to Alumni Cavite State University, $username!</h2>
                <p>Thank you for registering with us. We are excited to have you as part of our community!</p>
                <p>To complete your registration, please use the verification code below:</p>
                <h3><strong>$verificationCode</strong></h3>
                <p>This code is valid for the next 10 minutes, so be sure to enter it on the verification page as soon as possible.</p>
                <p>If you didn’t request this registration or believe this email was sent by mistake, please ignore it.</p>
                <a href='#' class='cta'>Verify Your Account</a>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Alumni Cavite State University. All rights reserved.</p>
            </div>
        </body>
        </html>
    ";

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
