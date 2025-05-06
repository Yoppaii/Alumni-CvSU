<?php

/**
 * Configuration for booking notification recipients
 * Add all email addresses that should receive booking notifications
 */

// Define an array of notification recipients
$bookingNotificationRecipients = [
    [
        'email' => 'bahayngalumni.reservations@gmail.com',
        'name' => 'Admin'
    ],
    [
        'email' => 'sacmacrossxxv@gmail.com', // Replace with your personal email
        'name' => 'Macross'                // Replace with your name
    ],
    // You can add more recipients as needed:
    // [
    //     'email' => 'another.email@example.com', 
    //     'name' => 'Another Recipient'
    // ],
];

/**
 * Function to get all notification recipients
 * 
 * @return array Array of recipient information (email and name)
 */
function getNotificationRecipients()
{
    global $bookingNotificationRecipients;
    return $bookingNotificationRecipients;
}
