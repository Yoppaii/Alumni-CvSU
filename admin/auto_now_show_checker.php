<?php
include '../main_db.php';
require_once 'email_notification.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// This script should be run via a cron job, for example:
// 0 * * * * php /path/to/auto_no_show_checker.php
// (This runs the script every hour)

$today = date('Y-m-d');
$current_time = date('H:i:s');

// Set the grace period (e.g., 3 hours after check-in time)
$grace_period_hours = 3;

// Find all confirmed bookings that have passed their check-in time plus grace period
$query = "
    SELECT b.*, u.email, u.username, ud.first_name, ud.middle_name, ud.last_name 
    FROM bookings b
    JOIN users u ON b.user_id = u.id 
    JOIN user ud ON u.id = ud.user_id
    WHERE b.status = 'confirmed' 
    AND b.arrival_date = ?
    AND TIME_TO_SEC(?) > TIME_TO_SEC(b.arrival_time) + (? * 3600)";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('ssi', $today, $current_time, $grace_period_hours);
$stmt->execute();
$result = $stmt->get_result();

while ($booking = $result->fetch_assoc()) {
    try {
        $mysqli->begin_transaction();

        // Update the booking status to no_show
        $update_query = "UPDATE bookings SET status = 'no_show', updated_at = NOW() WHERE id = ?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param('i', $booking['id']);

        if ($update_stmt->execute()) {
            $fullName = trim($booking['first_name'] . ' ' .
                ($booking['middle_name'] ? $booking['middle_name'] . ' ' : '') .
                $booking['last_name']);

            // Send email notification
            $emailSent = sendBookingStatusEmail(
                $booking['email'],
                $fullName,
                $booking['reference_number'],
                'no_show',
                $booking['room_number'],
                $booking['arrival_date'],
                $booking['departure_date'],
                $booking['price'],
                $booking['price_per_day'],
                $booking['arrival_time'],
                $booking['departure_time']
            );

            if ($emailSent) {
                $mysqli->commit();
                error_log("Booking ID: {$booking['id']} marked as no_show automatically.");
            } else {
                throw new Exception("Failed to send email notification for booking ID: {$booking['id']}");
            }
        } else {
            throw new Exception("Failed to update booking status for ID: {$booking['id']}");
        }
    } catch (Exception $e) {
        $mysqli->rollback();
        error_log("Error in auto no-show process: " . $e->getMessage());
    } finally {
        if (isset($update_stmt)) $update_stmt->close();
    }
}

$stmt->close();
$mysqli->close();

echo "Auto no-show check completed at " . date('Y-m-d H:i:s') . "\n";
