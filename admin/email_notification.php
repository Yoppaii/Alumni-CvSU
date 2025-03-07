<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function sendBookingStatusEmail($email, $fullName, $bookingRef, $status, $roomNumber, $checkIn, $checkOut, $price, $price_per_day, $arrivalTime, $departureTime) {
    $mail = new PHPMailer(true);

    $checkInDate = date('F d, Y', strtotime($checkIn));
    $checkOutDate = date('F d, Y', strtotime($checkOut));

    $statusColors = [
        'confirmed' => '#15803d',
        'cancelled' => '#dc2626',
        'completed' => '#1d4ed8',
    ];
    
    $statusMessages = [
        'confirmed' => 'Your booking has been confirmed! Please download the invoice PDF from your booking history and present it to the alumni office. We look forward to hosting you.',
        'cancelled' => 'Your booking has been cancelled. We hope to serve you another time.',
        'completed' => 'Your stay with us has been completed. Thank you for choosing our services!',
    ];
    
    $statusColor = $statusColors[$status] ?? '#1a73e8';
    $statusMessage = $statusMessages[$status] ?? 'Your booking status has been updated.';

    $message = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booking Status Update</title>
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
        <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="background: ' . $statusColor . '; color: white; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">Booking Status Update</h2>
            </div>
            
            <div style="padding: 20px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h2 style="color: ' . $statusColor . '; margin-top: 0;">Dear ' . htmlspecialchars($fullName) . ',</h2>
                    
                    <p style="font-size: 16px; line-height: 1.5;">' . $statusMessage . '</p>
                    
                    <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #eee;">
                        <h3 style="margin-top: 0; color: #333;">Booking Details:</h3>
                        <p style="margin: 5px 0;"><strong>Guest Name:</strong> ' . htmlspecialchars($fullName) . '</p>
                        <p style="margin: 5px 0;"><strong>Reference Number:</strong> ' . htmlspecialchars($bookingRef) . '</p>
                        <p style="margin: 5px 0;"><strong>Room:</strong> ' . htmlspecialchars($roomNumber) . '</p>
                        <p style="margin: 5px 0;"><strong>Check-in:</strong> ' . $checkInDate . ' at ' . htmlspecialchars($arrivalTime) . '</p>
                        <p style="margin: 5px 0;"><strong>Check-out:</strong> ' . $checkOutDate . ' at ' . htmlspecialchars($departureTime) . '</p>
                        <p style="margin: 5px 0;"><strong>Price per Day:</strong> ₱' . number_format($price_per_day, 2) . '</p>
                        <p style="margin: 5px 0;"><strong>Total Price:</strong> ₱' . number_format($price, 2) . '</p>
                        <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: ' . $statusColor . ';">' . ucfirst($status) . '</span></p>
                    </div>

                    <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #eee;">
                        <h3 style="margin-top: 0; color: #333;">Terms & Policies:</h3>
                        <ol style="margin: 10px 0; padding-left: 20px; color: #444;">
                            <li style="margin-bottom: 8px;">Present this invoice and valid ID upon check-in</li>
                            <li style="margin-bottom: 8px;">Cancellations must be made 24 hours before check-in</li>
                            <li style="margin-bottom: 8px;">Additional charges may apply for late check-out</li>
                            <li style="margin-bottom: 8px;">The guest house is not responsible for any lost items</li>
                        </ol>
                    </div>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; border-radius: 4px; margin-top: 20px;">
                        <p style="margin: 0;">If you have any questions about your booking, please don\'t hesitate to contact us.</p>
                    </div>
                </div>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                <p style="margin: 5px 0;">This is an automated message, please do not reply to this email.</p>
                <p style="margin: 5px 0;">CvSU Room Reservation System</p>
                <p style="margin: 5px 0;">Time sent: ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'roomreservation.csumc@gmail.com';
        $mail->Password = 'bpqazltzfyacofjd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('roomreservation.csumc@gmail.com', 'CvSU Room Reservation');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Booking Status Update - ' . ucfirst($status);
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}
?>