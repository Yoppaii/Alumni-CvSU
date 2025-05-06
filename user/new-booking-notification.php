<?php
// Save this file as 'new-booking-notification.php' in the same directory as walk-in-booking-save.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'notification-config.php'; // Include notification configuration

/**
 * Send email notification to admin when a new booking is created
 * 
 * @param int $bookingId The ID of the new booking
 * @return bool True if email sent successfully, false otherwise
 */
function sendNewBookingNotification($bookingId)
{
    global $mysqli;

    // Fetch the booking details
    $stmt = $mysqli->prepare("
        SELECT b.reference_number, b.room_number, b.occupancy, b.price, 
               b.price_per_day, b.mattress_fee, b.total_price, b.arrival_date, b.arrival_time, 
               b.departure_date, b.departure_time, b.status,b.is_walkin,
               u.email, ud.first_name, ud.last_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN user ud ON u.id = ud.user_id
        WHERE b.id = ?
    ");

    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        error_log("Booking with ID $bookingId not found");
        return false;
    }

    $booking = $result->fetch_assoc();
    $stmt->close();

    // Format guest name
    $guestName = $booking['first_name'] . ' ' . $booking['last_name'];

    // Format dates
    $arrivalDate = date('F d, Y', strtotime($booking['arrival_date']));
    $departureDate = date('F d, Y', strtotime($booking['departure_date']));

    // Resolve room name for email
    $roomName = match ($booking['room_number']) {
        9 => "Board Room",
        10 => "Conference Room",
        11 => "Lobby",
        default => ($booking['room_number'] >= 1 && $booking['room_number'] <= 8) ? "Room " . $booking['room_number'] : "Unknown Room"
    };

    // Create the email
    $mail = new PHPMailer(true);

    $message = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Booking Notification</title>
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
        <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="background: #1d4ed8; color: white; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">New Booking Notification</h2>
            </div>
            
            <div style="padding: 20px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h2 style="color: #1d4ed8; margin-top: 0;">New Reservation Alert!</h2>
                    
                    <p style="font-size: 16px; line-height: 1.5;">A new booking has been made in the system. Please review the details below:</p>
                    
                    <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #eee;">
                        <h3 style="margin-top: 0; color: #333;">Booking Details:</h3>
                        <p style="margin: 5px 0;"><strong>Guest Name:</strong> ' . htmlspecialchars($guestName) . '</p>
                        <p style="margin: 5px 0;"><strong>Guest Email:</strong> ' . htmlspecialchars($booking['email']) . '</p>';



    $message .= '
                        <p style="margin: 5px 0;"><strong>Reference Number:</strong> ' . htmlspecialchars($booking['reference_number']) . '</p>
                        <p style="margin: 5px 0;"><strong>Room:</strong> ' . htmlspecialchars($roomName) . '</p>
                        <p style="margin: 5px 0;"><strong>Occupancy:</strong> ' . htmlspecialchars($booking['occupancy']) . ' person(s)</p>
                        <p style="margin: 5px 0;"><strong>Check-in:</strong> ' . $arrivalDate . ' at ' . htmlspecialchars($booking['arrival_time']) . '</p>
                        <p style="margin: 5px 0;"><strong>Check-out:</strong> ' . $departureDate . ' at ' . htmlspecialchars($booking['departure_time']) . '</p>
                        <p style="margin: 5px 0;"><strong>Price per Day:</strong> ₱' . number_format($booking['price_per_day'], 2) . '</p>
                        <p style="margin: 5px 0;"><strong>Base Price:</strong> ₱' . number_format($booking['price'], 2) . '</p>';

    // Add mattress fee if available
    if (!empty($booking['mattress_fee']) && $booking['mattress_fee'] > 0) {
        $message .= '<p style="margin: 5px 0;"><strong>Mattress Fee:</strong> ₱' . number_format($booking['mattress_fee'], 2) . '</p>';
    }

    $message .= '
                        <p style="margin: 5px 0;"><strong>Total Price:</strong> ₱' . number_format($booking['total_price'], 2) . '</p>
                        <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: #1d4ed8;">' . (empty($booking['status']) ? 'Pending' : ucfirst($booking['status'])) . '</span></p>
                        <p style="margin: 5px 0;"><strong>Booking Type:</strong> ' . ($booking['is_walkin'] == 'yes' ? 'Walk-in' : 'Online') . '</p>
                    </div>

                    <div style="background: #e8f4fd; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 4px; margin-top: 20px;">
                        <p style="margin: 0;">Please log in to the admin panel to review and confirm this booking.</p>
                    </div>
                </div>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                <p style="margin: 5px 0;">This is an automated message from the reservation system.</p>
                <p style="margin: 5px 0;">CvSU Room Reservation System - Bahay Ng Alumni</p>
                <p style="margin: 5px 0;">Time sent: ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bahayngalumni.reservations@gmail.com';
        $mail->Password = 'fbcf mkmy awck koqi';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('bahayngalumni.reservations@gmail.com', 'Bahay Ng Alumni');

        // Add all notification recipients from config
        $recipients = getNotificationRecipients();
        foreach ($recipients as $recipient) {
            $mail->addAddress($recipient['email'], $recipient['name']);
        }

        $mail->isHTML(true);
        $mail->Subject = 'New Booking Alert - ' . $booking['reference_number'];
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("New booking notification email failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Function to trigger the notification when a new booking is created
 * 
 * @param int $bookingId The ID of the newly created booking
 * @return bool True if notification was sent successfully
 */
function triggerNewBookingNotification($bookingId)
{
    // Send email notification to admin
    $emailSent = sendNewBookingNotification($bookingId);

    if (!$emailSent) {
        error_log("Failed to send new booking notification for booking ID: $bookingId");
    }

    return $emailSent;
}
