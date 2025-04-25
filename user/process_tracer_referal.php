<?php
require('../main_db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Manila');
// Set error handling to prevent HTML errors being output
ini_set('display_errors', 0);
error_reporting(E_ALL);
function sendTracerSubmissionEmail($email, $fullName)
{
    $mail = new PHPMailer(true);
    $message = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Alumni Tracer Form Invitation</title>
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
        <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="background: #4682B4; color: white; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">Alumni Tracer System Invitation</h2>
            </div>
            
            <div style="padding: 20px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h2 style="color: #4682B4; margin-top: 0;">Dear ' . htmlspecialchars($fullName) . ',</h2>
                    
                    <p style="font-size: 16px; line-height: 1.5;">You have been recommended by one of your friends to sign up for our Alumni Tracer System.</p>
                    
                    <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #eee;">
                        <h3 style="margin-top: 0; color: #333;">Join Our Alumni Network</h3>
                        <p style="margin: 5px 0;">We would like to invite you to register and fill up the Alumni Tracer Form in our system.</p>
                        <p style="margin: 5px 0;">Your participation will help our institution track the career paths of our graduates and improve our academic programs.</p>
                        <p style="margin: 5px 0;">Your input is valuable for our institution\'s continuous improvement efforts.</p>
                        
                        <!-- Website Link Button -->
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="https://alumnicvsu.com/" style="background-color: #4682B4; color: white; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;">Visit Our Website</a>
                        </div>
                    </div>
                    
                    <div style="background: #e8f4fd; border: 1px solid #d1e9ff; color: #0c5460; padding: 15px; border-radius: 4px; margin-top: 20px;">
                        <p style="margin: 0;">If you have any questions or need assistance with the registration process, please don\'t hesitate to contact us.</p>
                    </div>
                </div>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                <p style="margin: 5px 0;">This is an automated message, please do not reply to this email.</p>
                <p style="margin: 5px 0;">Alumni Tracer System</p>
                <p style="margin: 5px 0;"><a href="https://alumnicvsu.com/" style="color: #4682B4; text-decoration: none;">alumnicvsu.com</a></p>
                <p style="margin: 5px 0;">Time sent: ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'roomreservation.csumc@gmail.com'; // Use your email account
        $mail->Password = 'bpqazltzfyacofjd'; // Use your email password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('roomreservation.csumc@gmail.com', 'Alumni Tracer System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Invitation to Join Alumni Tracer System';
        $mail->Body = $message;
        return $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}
$logged_user_id = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $mysqli->begin_transaction();
        if (isset($_POST['graduate_name'])) {
            $stmt = $mysqli->prepare("INSERT INTO other_alumni (
                user_id, name, email, contact_number
            ) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }
            for ($i = 0; $i < count($_POST['graduate_name']); $i++) {
                if (!empty($_POST['graduate_name'][$i])) {
                    $name = $_POST['graduate_name'][$i];
                    $email = $_POST['graduate_address'][$i] ?? '';
                    $contact = $_POST['graduate_contact'][$i] ?? '';
                    $stmt->bind_param("isss", $logged_user_id, $name, $email, $contact);
                    $stmt->execute();
                    if (!empty($email)) {
                        sendTracerSubmissionEmail($email, $name);
                    }
                }
            }
            $stmt->close();
        }
        $mysqli->commit();
        echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
    } catch (Exception $e) {
        $mysqli->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
