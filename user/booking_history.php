<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// Add this room name mapping array at the top with other arrays
$room_names = [
    '9' => 'Board Room',
    '10' => 'Conference Room',
    '11' => 'Lobby'
];

// Update the room display in the tables
function getRoomDisplay($room_number)
{
    global $room_names;
    if (isset($room_names[$room_number])) {
        return $room_names[$room_number];
    } else {
        return "Room " . $room_number;
    }
}

$status_icons = [
    'pending' => '<i class="fas fa-clock"></i>',
    'confirmed' => '<i class="fas fa-check"></i>',
    'checked_in' => '<i class="fas fa-door-open"></i>',
    'cancelled' => '<i class="fas fa-times-circle"></i>',
    'no_show' => '<i class="fas fa-user-slash"></i>',
    'completed' => '<i class="fas fa-check-double"></i>'
];

$invoice_allowed_statuses = ['confirmed', 'checked_in', 'completed'];

$user_id = $_SESSION['user_id'];
$active_sql = "SELECT *, created_at FROM bookings 
               WHERE user_id = ? 
               AND status IN ('pending', 'confirmed', 'checked_in') 
               ORDER BY created_at DESC";
$active_stmt = $mysqli->prepare($active_sql);
$active_stmt->bind_param("i", $user_id);
$active_stmt->execute();
$active_result = $active_stmt->get_result();
$history_sql = "SELECT * FROM bookings WHERE user_id = ? AND STATUS IN ('completed', 'cancelled', 'no_show') ORDER BY created_at DESC";
$history_stmt = $mysqli->prepare($history_sql);
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user/booking_history.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
</head>


<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing your request...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <!-- Active Booking -->
    <div class="booking-card">
        <div class="booking-header">
            <h1><i class="fas fa-calendar-alt"></i> Active Booking</h1>
        </div>
        <div class="booking-content">
            <div class="booking-section">
                <?php if ($active_result->num_rows > 0): ?>
                    <table class="booking-table center-text">
                        <thead>
                            <tr>
                                <th>Reference No.</th>
                                <th>Room</th>
                                <th>Occupancy</th>
                                <th>Mattress Fee</th>
                                <th>Price</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $active_result->fetch_assoc()): ?>
                                <?php $status = strtolower($booking['status']); ?>
                                <tr data-user-id="<?php echo htmlspecialchars($booking['user_id']); ?>">
                                    <td data-label="Reference No.">
                                        <?php echo htmlspecialchars($booking['reference_number']); ?>
                                    </td>
                                    <td data-label="Room">
                                        <?php echo getRoomDisplay($booking['room_number']); ?>
                                    </td>
                                    <td data-label="Occupancy">
                                        <?php echo htmlspecialchars($booking['occupancy']); ?> Person
                                    </td>
                                    <td data-label="Matress Fee">
                                        <?php echo number_format($booking['mattress_fee'], 2); ?>
                                    </td>
                                    <td data-label="Price">
                                        <?php echo number_format($booking['price'], 2); ?>
                                    </td>
                                    <td data-label="Check In">
                                        <?php echo date('M d, Y', strtotime($booking['arrival_date'])) . ' ' . date('h:i A', strtotime($booking['arrival_time'])); ?>
                                    </td>
                                    <td data-label="Check Out">
                                        <?php echo date('M d, Y', strtotime($booking['departure_date'])) . ' ' . date('h:i A', strtotime($booking['departure_time'])); ?>
                                    </td>
                                    <td data-label="Status">
                                        <span class="status-pill status-<?php echo $status; ?>">
                                            <?php
                                            $status_icons = [
                                                'pending' => '<i class="fas fa-clock"></i>',
                                                'confirmed' => '<i class="fas fa-check"></i>',
                                                'checked_in' => '<i class="fas fa-door-open"></i>',
                                                'cancelled' => '<i class="fas fa-times-circle"></i>',
                                                'no_show' => '<i class="fas fa-user-slash"></i>',
                                                'completed' => '<i class="fas fa-check-double"></i>'
                                            ];
                                            echo isset($status_icons[$status]) ? $status_icons[$status] . ' ' : '';
                                            echo ucfirst($status);
                                            ?>
                                        </span>
                                    </td>
                                    <td data-label="Action">
                                        <?php
                                        $allowed_statuses = ['pending', 'confirmed'];
                                        if (in_array($status, $allowed_statuses)) {
                                        ?>
                                            <div class="action-dropdown">
                                                <button class="action-btn">
                                                    Action <i class="fas fa-caret-down"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <button
                                                        onclick="openRescheduleModal('<?php echo $booking['id']; ?>', '<?php echo $booking['room_number']; ?>', '<?php echo $booking['arrival_date']; ?>', '<?php echo $booking['arrival_time']; ?>', '<?php echo $booking['departure_date']; ?>', '<?php echo $booking['departure_time']; ?>', '<?php echo $booking['price_per_day']; ?>')">Reschedule</button>
                                                    <button
                                                        onclick="showCancelModal('<?php echo $booking['id']; ?>', '<?php echo $booking['reference_number']; ?>')">Cancel</button>
                                                </div>
                                            </div>
                                        <?php
                                        } else {
                                            echo '<i class="fas fa-ban text-gray-400" title="No actions available"></i>';
                                        }
                                        ?>
                                    </td>
                                    <td data-label="Invoice">
                                        <?php if (in_array($status, $invoice_allowed_statuses)): ?>
                                            <button class="invoice-btn" onclick="generateInvoice(this.closest('tr'))">
                                                <i class="fas fa-file-invoice"></i>
                                            </button>
                                        <?php else: ?>
                                            <i class="fas fa-ban text-gray-400" title="Invoice not available"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-bookings">
                        <p>No active bookings found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="booking-card">
        <div class="booking-header">
            <h1><i class="fas fa-calendar-alt"></i> Booking History</h1>
        </div>
        <div class="booking-content">
            <div class="booking-section">
                <div class="search-toggle-wrapper">
                    <button id="toggle-history-btn" class="cta-btn">Show Booking History</button>

                    <div class="search-container" style="display: none;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="history-search" placeholder="Search bookings..." />
                    </div>
                </div>

                <div id="booking-history-section" style="display: none;">
                    <?php if ($history_result->num_rows > 0): ?>
                        <table class="booking-table center-text" id="booking-history">
                            <thead>
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Room</th>
                                    <th>Occupancy</th>
                                    <th>Mattress Fee</th>
                                    <th>Price</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($booking = $history_result->fetch_assoc()): ?>
                                    <?php $status = strtolower($booking['status']); ?>
                                    <tr data-user-id="<?php echo htmlspecialchars($booking['user_id']); ?>">
                                        <td data-label="Reference No."><?php echo htmlspecialchars($booking['reference_number']); ?></td>
                                        <td data-label="Room"><?php echo getRoomDisplay($booking['room_number']); ?></td>
                                        <td data-label="Occupancy"><?php echo htmlspecialchars($booking['occupancy']); ?> Person</td>
                                        <td data-label="Mattress Fee"><?php echo number_format($booking['mattress_fee'], 2); ?></td>
                                        <td data-label="Price"><?php echo number_format($booking['price'], 2); ?></td>
                                        <td data-label="Check In"><?php echo date('M d, Y', strtotime($booking['arrival_date'])) . ' ' . date('h:i A', strtotime($booking['arrival_time'])); ?></td>
                                        <td data-label="Check Out"><?php echo date('M d, Y', strtotime($booking['departure_date'])) . ' ' . date('h:i A', strtotime($booking['departure_time'])); ?></td>
                                        <td data-label="Status">
                                            <span class="status-pill status-<?php echo $status; ?>">
                                                <?php
                                                echo isset($status_icons[$status]) ? $status_icons[$status] . ' ' : '';
                                                echo ucfirst($status);
                                                ?>
                                            </span>
                                        </td>
                                        <td data-label="Created At">
                                            <?php echo date('M d, Y h:i A', strtotime($booking['created_at'])); ?>

                                        </td>
                                        <td data-label="Invoice">
                                            <?php if (in_array($status, $invoice_allowed_statuses)): ?>
                                                <button class="invoice-btn" onclick="generateInvoice(this.closest('tr'))">
                                                    <i class="fas fa-file-invoice"></i>
                                                </button>
                                            <?php else: ?>
                                                <i class="fas fa-ban text-gray-400" title="Invoice not available"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-bookings">
                            <p>No bookings history found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="cancelModal" class="cancel-modal">
        <div class="cancel-modal-content">
            <h3 class="cancel-modal-title">Cancel Booking</h3>
            <p>Are you sure you want to cancel booking <strong><span id="referenceNumber"></span></strong>?</p>
            <p id="cancelTimeInfo" class="cancel-time-info"></p> <!-- NEW LINE -->

            <form id="cancelForm" class="cancel-form">
                <input type="hidden" id="bookingId" name="booking_id">
                <div class="cancel-form-buttons">
                    <button type="button" class="btn btn-back" onclick="hideCancelModal()">Back</button>
                    <button type="submit" class="btn btn-cancel">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rescheduleModal" class="modal-overlay" style="display: none;">
        <div class="modal-content book-card">
            <div class="book-header">
                <h1><i class="fas fa-calendar-plus"></i> Reschedule Booking</h1>
            </div>

            <div class="book-content">
                <div class="book-step-indicator">
                    <div class="book-step active">1</div>
                    <div class="book-step">2</div>
                </div>

                <!-- Step 1: Departure datetime -->
                <div id="reschedule-step1" class="book-step-content">
                    <div class="book-date-time-container">
                        <div>
                            <label for="reschedule-arrival-datetime">New Check-in Date and Time</label>
                            <input type="text" class="book-date-time-input" id="reschedule-arrival-datetime"
                                placeholder="Select new check-in date and time">
                        </div>
                        <div>
                            <label for="reschedule-departure-datetime">New Check-out Date and Time</label>
                            <input type="text" class="book-date-time-input" id="reschedule-departure-datetime"
                                placeholder="Select new check-out date and time">
                        </div>
                    </div>
                </div>

                <!-- Step 2: Summary -->
                <div id="reschedule-step2" class="book-step-content" style="display: none;">
                    <div class="book-summary">
                        <h2>Summary</h2>
                        <div class="book-summary-item">
                            <span class="book-summary-label">Room:</span>
                            <span class="book-summary-value" id="reschedule-summary-room">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">Original Check-in:</span>
                            <span class="book-summary-value" id="reschedule-summary-old-arrival">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">Original Check-out:</span>
                            <span class="book-summary-value" id="reschedule-summary-old-departure">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">New Check-in:</span>
                            <span class="book-summary-value" id="reschedule-summary-new-arrival">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">New Check-out:</span>
                            <span class="book-summary-value" id="reschedule-summary-new-departure">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">Total Cost:</span>
                            <span class="book-summary-value" id="reschedule-summary-total-cost">-</span>
                        </div>
                    </div>
                </div>

                <div class="book-button-container">
                    <button id="reschedule-prev-button" class="book-nav-button book-prev-button"
                        style="display: none;">Previous</button>
                    <button id="reschedule-next-button" class="book-nav-button book-next-button">Next</button>
                </div>
            </div>

            <button class="modal-close-button" onclick="closeRescheduleModal()">×</button>
        </div>
    </div>


    <!-- <textarea id="cancellationReason" name="cancellation_reason" placeholder="Please provide a reason for cancellation" required></textarea> -->

    <img id="your-logo-id" src="/Alumni-CvSU/asset/images/res1.png" style="display: none;" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/polyfills.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>


    <script>
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationContainer');
            if (!container) {
                console.warn('Notification container not found');
                return;
            }

            const notification = document.createElement('div');
            notification.className = `notification ${type}`;

            notification.innerHTML = `
            <div>
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
            </div>
            <button type="button" class="notification-close" onclick="this.parentElement.remove()">&times;</button>
        `;

            container.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.animation = 'slideOut 0.3s ease-out forwards';
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }

        let currentStep = 1;
        let bookingData = {};

        const arrivalInput = document.getElementById('reschedule-arrival-datetime');
        const departureInput = document.getElementById('reschedule-departure-datetime');
        const step1Content = document.getElementById('reschedule-step1');
        const step2Content = document.getElementById('reschedule-step2');
        const prevBtn = document.getElementById('reschedule-prev-button');
        const nextBtn = document.getElementById('reschedule-next-button');
        const summaryRoom = document.getElementById('reschedule-summary-room');
        const summaryOldArrival = document.getElementById('reschedule-summary-old-arrival');
        const summaryOldDeparture = document.getElementById('reschedule-summary-old-departure');
        const summaryNewArrival = document.getElementById('reschedule-summary-new-arrival');
        const summaryNewDeparture = document.getElementById('reschedule-summary-new-departure');
        const summaryTotalCost = document.getElementById('reschedule-summary-total-cost');

        let bookingIdGlobal = null;
        let roomIdGlobal = null; // Added global variable
        let pricePerDayGlobal = null;
        let arrivalFlatpickr = null; // Define these variables globally
        let departureFlatpickr = null;

        function parseDateTime(dateTimeStr) {
            // More robust datetime parsing
            try {
                // Try direct parsing first
                const date = new Date(dateTimeStr.replace(' ', 'T'));
                // Check if date is valid
                if (isNaN(date.getTime())) throw new Error("Invalid date");
                return date;
            } catch (e) {
                // Fallback parsing for cross-browser compatibility
                const parts = dateTimeStr.split(/[- :]/);
                // parts[0]=year, parts[1]=month, parts[2]=day, parts[3]=hour, parts[4]=minute
                return new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4]);
            }
        }


        function openRescheduleModal(bookingId, roomId, arrivalDate, arrivalTime, departureDate, departureTime, pricePerDay) {
            bookingIdGlobal = bookingId;
            roomIdGlobal = roomId; // Store in global variable
            pricePerDayGlobal = parseFloat(pricePerDay); // Parse the price and store globally
            bookingData = {};
            document.getElementById('rescheduleModal').style.display = 'flex';
            currentStep = 1;

            // Reset UI
            step1Content.style.display = 'block';
            step2Content.style.display = 'none';
            prevBtn.style.display = 'none';
            nextBtn.textContent = 'Next';

            // Set initial values in the summary
            summaryRoom.textContent = 'Room Name'; // Replace with actual room name if available
            summaryOldArrival.textContent = new Date(arrivalDate + ' ' + arrivalTime).toLocaleString();
            summaryOldDeparture.textContent = new Date(departureDate + ' ' + departureTime).toLocaleString();
            summaryNewArrival.textContent = '-';
            summaryNewDeparture.textContent = '-';
            summaryTotalCost.textContent = '-';

            // Initialize calendars with the correct room ID
            try {
                initializeCalendars(roomIdGlobal);
            } catch (e) {
                console.error('Error initializing calendars:', e);
                showNotification('Failed to load booking calendar. Please try again.', 'error');
            }
        }

        function showLoading(message = 'Processing your request...') {
            const overlay = document.getElementById('loadingOverlay');
            const loadingText = overlay.querySelector('.loading-text');
            if (loadingText) {
                loadingText.textContent = message;
            }
            overlay.style.display = 'flex';
            overlay.classList.add('loading-overlay-show');
            overlay.classList.remove('loading-overlay-hide');
            document.body.style.overflow = 'hidden';
        }

        function hideLoading() {
            // Improved loading overlay removal
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
                // Optional: completely remove from DOM
                try {
                    document.body.removeChild(loadingOverlay);
                } catch (e) {
                    console.warn('Error removing loading overlay:', e);
                }
            }
        }

        function isDateBooked(date, bookings) {
            // Format the date in YYYY-MM-DD consistently
            const dateStr = date.toISOString().split('T')[0];
            return bookings.some(booking => {
                const arrivalDate = booking.arrival_date;
                const departureDate = booking.departure_date;
                // Consider the date booked if it falls within any booking period
                return dateStr >= arrivalDate && dateStr <= departureDate;
            });
        }

        function enforceTimeConstraints(selectedDate, calendarInstance, isArrival) {
            const hours = selectedDate.getHours();
            const minutes = selectedDate.getMinutes();

            if (isArrival) {
                // Check-in restrictions: 9:00 AM to 7:00 PM
                if (hours < 9) {
                    showNotification('Check-in time must be between 9:00 AM and 7:00 PM. Adjusted to 9:00 AM.',
                        'info');
                    selectedDate.setHours(9, 0, 0, 0);
                    calendarInstance.setDate(selectedDate);
                    return selectedDate;
                } else if (hours > 19) {
                    showNotification('Check-in time must be between 9:00 AM and 7:00 PM. Adjusted to 7:00 PM.',
                        'info');
                    selectedDate.setHours(18, 30, 0, 0); // 6:30 PM to ensure within boundary
                    calendarInstance.setDate(selectedDate);
                    return selectedDate;
                }
            } else {
                // Check-out restrictions: 7:00 AM to 5:00 PM
                if (hours < 7) {
                    showNotification('Check-out time must be between 7:00 AM and 5:00 PM. Adjusted to 7:00 AM.',
                        'info');
                    selectedDate.setHours(7, 0, 0, 0);
                    calendarInstance.setDate(selectedDate);
                    return selectedDate;
                } else if (hours >= 17) {
                    showNotification('Check-out time must be between 7:00 AM and 5:00 PM. Adjusted to 5:00 PM.',
                        'info');
                    selectedDate.setHours(17, 0, 0, 0);
                    calendarInstance.setDate(selectedDate);
                    return selectedDate;
                }
            }

            return selectedDate;
        }

        async function initializeCalendars(roomId) {
            try {
                if (!roomId) {
                    throw new Error('Room ID is missing');
                }
                const response = await fetch('user/get-room-bookings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        room_id: roomId
                    })
                });
                const data = await response.json();
                const bookedDates = data.bookedDates || [];
                const bookingTimes = data.bookingTimes || [];

                // Create a map of dates to their status for easier access
                const dateStatusMap = new Map();

                // Get all arrival and departure dates to understand boundaries
                const allBookings = [];
                bookingTimes.forEach(booking => {
                    if (booking.arrival_date && booking.departure_date) {
                        allBookings.push({
                            arrival_date: booking.arrival_date,
                            arrival_time: booking.arrival_time,
                            departure_date: booking.departure_date,
                            departure_time: booking.departure_time
                        });
                    }
                });

                // Mark fully booked dates
                bookedDates.forEach(date => {
                    dateStatusMap.set(date, {
                        status: 'fully-booked',
                        title: 'Fully booked',
                        class: 'fully-booked-date',
                        isSelectable: false
                    });
                });

                // Mark checkout dates
                bookingTimes.forEach(booking => {
                    const checkoutDate = booking.date;
                    const departureTime = new Date(booking.departure_time);
                    const availableTime = new Date(departureTime.getTime() + (2 * 60 * 60 * 1000)); // 2 hours for cleaning

                    // Check if the day after this checkout is booked
                    const nextDayStr = new Date(new Date(checkoutDate).getTime() + 86400000).toISOString().split('T')[0];
                    const isNextDayBooked = bookedDates.includes(nextDayStr) || allBookings.some(b => b.arrival_date === nextDayStr);

                    // If next day is booked, treat this as fully booked as requested
                    if (isNextDayBooked) {
                        dateStatusMap.set(checkoutDate, {
                            status: 'fully-booked',
                            title: 'Fully booked',
                            class: 'fully-booked-date',
                            isSelectable: false
                        });
                    } else {
                        // Otherwise, mark as a normal checkout day available for new check-ins
                        dateStatusMap.set(checkoutDate, {
                            status: 'checkout-date',
                            title: `Available after ${availableTime.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}`,
                            availableTime: availableTime,
                            class: 'checkout-available-date',
                            isSelectable: true
                        });
                    }
                });

                // Mark check-in dates
                allBookings.forEach(booking => {
                    const checkinDate = booking.arrival_date;
                    const checkinTime = new Date(booking.arrival_time);

                    if (dateStatusMap.has(checkinDate) && dateStatusMap.get(checkinDate).status === 'checkout-date') {
                        // This is both a checkout and checkin date
                        const currentData = dateStatusMap.get(checkinDate);
                        dateStatusMap.set(checkinDate, {
                            ...currentData,
                            status: 'transition-date',
                            class: 'transition-available-date',
                            title: `Transition day: Available after ${currentData.availableTime.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}`
                        });
                    } else if (!dateStatusMap.has(checkinDate)) {
                        dateStatusMap.set(checkinDate, {
                            status: 'checkin-date',
                            title: 'Check-in day',
                            class: 'checkin-date',
                            isSelectable: false // Can't check in on a day when someone else is checking in
                        });
                    }
                });

                const baseConfig = {
                    enableTime: true,
                    dateFormat: "Y-m-d h:i K",
                    minDate: "today",
                    inline: false, // Changed from window.innerWidth <= 768
                    time_24hr: false,
                    minuteIncrement: 30,
                    allowInput: true,
                    enableSeconds: false,
                    noCalendar: false,
                    disableMobile: "true",
                };

                function isCheckoutOnlyDate(dateStr) {

                    // Sample implementation - check if the next day has a check-in
                    const nextDayDate = new Date(dateStr);
                    nextDayDate.setDate(nextDayDate.getDate() + 1);
                    const nextDayStr = flatpickr.formatDate(nextDayDate, "Y-m-d");

                    // Check if the next day has a check-in scheduled
                    const hasNextDayCheckin = allBookings.some(booking => booking.arrival_date === nextDayStr);

                    // If the next day has a check-in, this day should only be available for checkout
                    return hasNextDayCheckin;
                }

                // Modify the onDayCreateHandler to use the new function
                const onDayCreateHandler = function(dObj, dStr, fp, dayElem) {
                    const dateStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Check if the date is in the past
                    if (dayElem.dateObj < today) {
                        dayElem.classList.add('past-date');
                        dayElem.title = 'Past date';
                        dayElem.classList.add('flatpickr-disabled');
                        return;
                    }

                    const dateStatus = dateStatusMap.get(dateStr);

                    if (dateStatus) {
                        dayElem.classList.add(dateStatus.class);
                        dayElem.title = dateStatus.title;

                        // Make sure checkout-available-date and transition-available-date are not disabled
                        if ((dateStatus.class === 'checkout-available-date' || dateStatus.class === 'transition-available-date') &&
                            dayElem.classList.contains('flatpickr-disabled')) {
                            dayElem.classList.remove('flatpickr-disabled');
                        }
                    } else {
                        // Check if this is a checkout-only date
                        if (isCheckoutOnlyDate(dateStr)) {
                            dayElem.classList.add('checkout-only-date');
                            dayElem.title = 'Available for CHECKOUT ONLY (not for check-in)';
                            // Optionally, disable this date for arrival selection
                            dayElem.classList.add('flatpickr-disabled');
                        } else {
                            dayElem.classList.add('available-date');
                            dayElem.title = 'Available for booking';
                        }
                    }
                };

                // Custom day create handler specifically for departure calendar
                const departureDayCreateHandler = function(dObj, dStr, fp, dayElem) {
                    const dateStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Check if the date is in the past
                    if (dayElem.dateObj < today) {
                        dayElem.classList.add('past-date');
                        dayElem.title = 'Past date';
                        dayElem.classList.add('flatpickr-disabled');
                        return;
                    }

                    const dateStatus = dateStatusMap.get(dateStr);

                    if (dateStatus) {
                        dayElem.classList.add(dateStatus.class);
                        dayElem.title = dateStatus.title;

                        // Make sure checkout-available-date and transition-available-date are not disabled
                        if ((dateStatus.class === 'checkout-available-date' || dateStatus.class === 'transition-available-date') &&
                            dayElem.classList.contains('flatpickr-disabled')) {
                            dayElem.classList.remove('flatpickr-disabled');
                        }
                    } else {
                        dayElem.classList.add('available-date');
                        dayElem.title = 'Available for booking';
                    }

                    // If we have an arrival date selected
                    if (bookingData.arrival) {
                        // Check if this date is valid for departure
                        if (!isValidDepartureDate(dayElem.dateObj, bookingData.arrival)) {
                            // Mark unavailable dates with a specific class for styling
                            dayElem.classList.add('unavailable-departure-date');
                            dayElem.classList.add('flatpickr-disabled');

                            // Next unavailable date check
                            const nextUnavailable = findNextUnavailableDateAfterDate(bookingData.arrival);
                            if (nextUnavailable) {
                                const nextUnavailableDate = new Date(nextUnavailable.date);
                                if (dateStr >= nextUnavailable.date) {
                                    dayElem.title = 'Unavailable - Conflict with another booking';
                                }
                            } else {
                                dayElem.title = 'Unavailable for checkout';
                            }
                        }
                    }
                };

                function isValidArrivalTime(date) {
                    // Check if the time is between 7 AM (07:00) and 7 PM (19:00)
                    const hours = date.getHours();
                    return hours >= 7 && hours < 19;
                }

                // Helper function to check if a date is valid for arrival
                // Find where in the code dates like April 23 are being incorrectly classified as available for check-in
                function isValidArrivalDate(date) {
                    // First check the 7 PM cutoff
                    if (!isValidArrivalTime(date)) {
                        return false;
                    }

                    // Check if date is in the past
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (date < today) {
                        return false;
                    }

                    const dateStr = flatpickr.formatDate(date, "Y-m-d");
                    const dateStatus = dateStatusMap.get(dateStr);

                    // If no status, it's available
                    if (!dateStatus) return true;

                    // If fully booked or checkin date, it's not available
                    if (dateStatus.status === 'fully-booked' || dateStatus.status === 'checkin-date') {
                        return false;
                    }

                    // If it's a checkout or transition date, it's always valid initially
                    if (dateStatus.status === 'checkout-date' || dateStatus.status === 'transition-date') {
                        return true;
                    }

                    return true;
                }

                // Helper function to find the next booking or fully booked date after a given date
                function findNextUnavailableDateAfterDate(date) {
                    if (!date) return null;

                    const dateStr = flatpickr.formatDate(date, "Y-m-d");

                    // Create an array of all dates that are unavailable (either fully booked or arrival dates)
                    const unavailableDates = [];

                    // Add fully booked dates
                    bookedDates.forEach(bookedDate => {
                        if (bookedDate > dateStr) {
                            unavailableDates.push({
                                date: bookedDate,
                                type: 'fully-booked'
                            });
                        }
                    });

                    // Add check-in dates
                    allBookings.forEach(booking => {
                        if (booking.arrival_date > dateStr) {
                            unavailableDates.push({
                                date: booking.arrival_date,
                                type: 'check-in',
                                arrival_time: booking.arrival_time
                            });
                        }
                    });

                    // Sort by date to find the closest one
                    unavailableDates.sort((a, b) => new Date(a.date) - new Date(b.date));

                    // Return the next unavailable date, or null if there are none
                    return unavailableDates.length > 0 ? unavailableDates[0] : null;
                }

                // Helper function to check if a date is valid for departure
                function isValidDepartureDate(date, arrivalDate) {
                    // If no arrival date is selected, no departure dates are valid
                    if (!arrivalDate) {
                        return false;
                    }

                    // Check if date is in the past
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (date < today) {
                        return false;
                    }

                    // Must be after arrival date
                    if (date <= arrivalDate) {
                        return false;
                    }

                    const dateStr = flatpickr.formatDate(date, "Y-m-d");
                    const dateStatus = dateStatusMap.get(dateStr);

                    // If fully booked, it's not available
                    if (dateStatus && dateStatus.status === 'fully-booked') {
                        return false;
                    }

                    // Find the next unavailable date after our arrival
                    const nextUnavailable = findNextUnavailableDateAfterDate(arrivalDate);

                    // If there's a next unavailable date, we can only select dates up to (but not including) it
                    if (nextUnavailable) {
                        const nextUnavailableDateStr = nextUnavailable.date;
                        const nextUnavailableDate = new Date(nextUnavailableDateStr);
                        nextUnavailableDate.setHours(0, 0, 0, 0);

                        // The date we're checking must be before the next unavailable date
                        // This is the key fix - we don't allow selecting dates beyond the next fully booked or check-in date
                        if (date >= nextUnavailableDate) {
                            return false;
                        }
                    }

                    // All other dates between arrival and next unavailable date are valid
                    return true;
                }

                // Fix for the calendar initialization sequence
                if (window.arrivalCalendar) {
                    window.arrivalCalendar.destroy();
                }

                // Make allBookings available in this scope
                window.allBookings = [];
                bookingTimes.forEach(booking => {
                    if (booking.arrival_date && booking.departure_date) {
                        window.allBookings.push({
                            arrival_date: booking.arrival_date,
                            arrival_time: booking.arrival_time,
                            departure_date: booking.departure_date,
                            departure_time: booking.departure_time
                        });
                    }
                });

                // First create the arrival calendar
                // Modify the onChange function in the arrival calendar initialization
                window.arrivalCalendar = flatpickr(arrivalInput, {
                    ...baseConfig,
                    minTime: "09:00", // 9am
                    maxTime: "19:00", // 7pm
                    onChange: function(selectedDates) {
                        if (selectedDates.length > 0) {
                            let selectedDate = selectedDates[0];
                            const dateStr = flatpickr.formatDate(selectedDate, "Y-m-d");
                            const dateStatus = dateStatusMap.get(dateStr);

                            // First enforce time constraints
                            selectedDate = enforceTimeConstraints(selectedDate, window.arrivalCalendar, true);

                            if (dateStatus && (dateStatus.status === 'checkout-date' || dateStatus.status === 'transition-date')) {
                                if (selectedDate < dateStatus.availableTime) {
                                    const formattedTime = dateStatus.availableTime.toLocaleTimeString('en-US', {
                                        hour: 'numeric',
                                        minute: '2-digit',
                                        hour12: true
                                    });
                                    showNotification(`Room will be available after cleaning. Time adjusted to ${formattedTime}`, 'info');
                                    window.arrivalCalendar.setDate(dateStatus.availableTime);
                                    bookingData.arrival = dateStatus.availableTime;
                                } else {
                                    bookingData.arrival = selectedDate;
                                }
                            } else if (!isValidArrivalDate(selectedDate)) {
                                showNotification('This date is not available for check-in. Please select another date.', 'error');
                                window.arrivalCalendar.clear();
                                return;
                            } else {
                                bookingData.arrival = selectedDate;
                            }

                            // Only update the departure calendar if it exists
                            if (window.departureCalendar && typeof window.departureCalendar.set === 'function') {
                                window.departureCalendar.set('minDate', bookingData.arrival);

                                // Clear the departure date when arrival changes
                                window.departureCalendar.clear();
                                bookingData.departure = null;

                                // Find the next unavailable date to show the user their options
                                const nextUnavailable = findNextUnavailableDateAfterDate(bookingData.arrival);
                                if (nextUnavailable) {
                                    const maxDate = new Date(nextUnavailable.date);
                                    maxDate.setDate(maxDate.getDate() - 1); // Day before the unavailable date
                                    const formattedMaxDate = flatpickr.formatDate(maxDate, "Y-m-d");

                                    // Inform the user about available departure dates
                                    const arrivalDateFormatted = flatpickr.formatDate(bookingData.arrival, "Y-m-d");
                                    if (formattedMaxDate > arrivalDateFormatted) {
                                        showNotification(`You can check out between ${arrivalDateFormatted} and ${formattedMaxDate}`, 'info');
                                    } else {
                                        showNotification(`Only ${formattedMaxDate} is available for checkout`, 'info');
                                    }
                                }

                                // Important: Redraw the calendar to update the disabled dates
                                window.departureCalendar.redraw();

                                // Update the disabled dates for departure calendar
                                window.departureCalendar.set('disable', [(date) => !isValidDepartureDate(date, bookingData.arrival)]);
                            }

                            updateSummary();
                        }
                    },
                    onDayCreate: onDayCreateHandler,
                    disable: [(date) => {
                        // Disable past dates
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        if (date < today) {
                            return true;
                        }

                        // Only disable dates that are fully booked or checkin dates
                        const dateStr = flatpickr.formatDate(date, "Y-m-d");
                        const dateStatus = dateStatusMap.get(dateStr);

                        if (!dateStatus) return false; // Available

                        // Only fully-booked and checkin dates are disabled
                        return dateStatus.status === 'fully-booked' || dateStatus.status === 'checkin-date';
                    }]
                });


                if (window.departureCalendar) {
                    window.departureCalendar.destroy();
                }

                window.departureCalendar = flatpickr(departureInput, {
                    ...baseConfig,
                    minTime: "07:00", // 7am
                    maxTime: "17:00", // 5pm
                    minDate: bookingData.arrival ? (() => {
                        // Create a new date for the day after arrival
                        const nextDay = new Date(bookingData.arrival);
                        nextDay.setDate(nextDay.getDate() + 1);
                        return nextDay;
                    })() : "today", // Replace the departure calendar onChange function with this
                    onChange: function(selectedDates) {
                        if (selectedDates.length > 0) {
                            let selectedDate = selectedDates[0];
                            const dateStr = flatpickr.formatDate(selectedDate, "Y-m-d");

                            // First enforce time constraints
                            selectedDate = enforceTimeConstraints(selectedDate, window.departureCalendar, false);

                            if (!isValidDepartureDate(selectedDate, bookingData.arrival)) {
                                showNotification('This date is not available for checkout. Please select another date.', 'error');
                                window.departureCalendar.clear();
                                return;
                            }

                            // Find the next unavailable date after our arrival
                            const nextUnavailable = findNextUnavailableDateAfterDate(bookingData.arrival);

                            // If this is a day adjacent to check-in date, enforce time constraints
                            if (nextUnavailable && nextUnavailable.type === 'check-in') {
                                const nextUnavailableDateStr = nextUnavailable.date;
                                const checkoutDateStr = flatpickr.formatDate(selectedDate, "Y-m-d");

                                // If the selected date is the day before the next check-in
                                if (checkoutDateStr === nextUnavailableDateStr) {
                                    showNotification('Cannot check out on a day that has an incoming guest. Please select an earlier date.', 'error');
                                    window.departureCalendar.clear();
                                    return;
                                }

                                // For the day before check-in, ensure checkout is early enough
                                const nextDay = new Date(checkoutDateStr);
                                nextDay.setDate(nextDay.getDate() + 1);
                                const nextDayStr = flatpickr.formatDate(nextDay, "Y-m-d");

                                if (nextDayStr === nextUnavailableDateStr) {
                                    const nextArrivalTime = new Date(nextUnavailable.arrival_time);
                                    // Allow checkout at least 2 hours before next check-in for cleaning
                                    const latestCheckoutTime = new Date(nextArrivalTime.getTime() - (2 * 60 * 60 * 1000));

                                    if (selectedDate > latestCheckoutTime) {
                                        const formattedTime = latestCheckoutTime.toLocaleTimeString('en-US', {
                                            hour: 'numeric',
                                            minute: '2-digit',
                                            hour12: true
                                        });
                                        showNotification(`You must check out at least 2 hours before the next guest arrival. Time adjusted to ${formattedTime}`, 'info');
                                        window.departureCalendar.setDate(latestCheckoutTime);
                                        bookingData.departure = latestCheckoutTime;
                                    } else {
                                        bookingData.departure = selectedDate;
                                    }
                                } else {
                                    bookingData.departure = selectedDate;
                                }
                            } else {
                                bookingData.departure = selectedDate;
                            }

                            updateSummary();
                        }
                    },
                    onDayCreate: departureDayCreateHandler, // Use our special handler for departure days
                    disable: [(date) => {
                        // Disable past dates
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        if (date < today) {
                            return true;
                        }

                        // Disable dates that aren't valid for departure
                        return !isValidDepartureDate(date, bookingData.arrival);
                    }]
                });

                // Add a MutationObserver to ensure checkout-available-date is never disabled
                // This is a safety measure in case flatpickr attempts to disable these dates
                const observer = new MutationObserver(mutations => {
                    mutations.forEach(mutation => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            const dayElem = mutation.target;

                            // Don't enable past dates
                            if (dayElem.classList.contains('past-date')) {
                                if (!dayElem.classList.contains('flatpickr-disabled')) {
                                    dayElem.classList.add('flatpickr-disabled');
                                }
                                return;
                            }

                            // Keep unavailable-departure-dates disabled
                            if (dayElem.classList.contains('unavailable-departure-date')) {
                                if (!dayElem.classList.contains('flatpickr-disabled')) {
                                    dayElem.classList.add('flatpickr-disabled');
                                }
                                return;
                            }

                            if (dayElem.classList.contains('checkout-available-date') ||
                                dayElem.classList.contains('transition-available-date')) {
                                if (dayElem.classList.contains('flatpickr-disabled')) {
                                    dayElem.classList.remove('flatpickr-disabled');
                                }
                            }
                        }
                    });
                });

                // Start observing after a short delay to ensure flatpickr has rendered
                setTimeout(() => {
                    const calendarDays = document.querySelectorAll('.flatpickr-day');
                    calendarDays.forEach(day => {
                        observer.observe(day, {
                            attributes: true
                        });
                    });
                }, 500);

                const calendarStyles = document.createElement('style');
                calendarStyles.textContent = `
                        /* Available date styling */
                        /* Add this to your calendarStyles.textContent CSS block */
                        .flatpickr-time input.flatpickr-hour,
                        .flatpickr-time input.flatpickr-minute {
                            font-weight: bold !important;
                        }

                        .flatpickr-time input.flatpickr-hour[disabled],
                        .flatpickr-time input.flatpickr-minute[disabled] {
                            background-color: #f1f1f1 !important;
                            color: #9e9e9e !important;
                            cursor: not-allowed !important;
                        }

                        /* Visual indicator for time restrictions */
                        .time-restrictions-notice {
                            text-align: center;
                            font-size: 0.85rem;
                            color: #ff9800;
                            background-color: #fff3e0;
                            padding: 3px;
                            border-radius: 3px;
                            margin-top: 5px;
                            border: 1px solid #ffe0b2;
                        }
                        .flatpickr-day.available-date {
                            background-color: #f1f8e9 !important;
                            color: #558b2f !important;
                            border-color: #c5e1a5 !important;
                            pointer-events: auto !important;
                        }
                        
                        .flatpickr-day.available-date:hover {
                            background-color: #dcedc8 !important;
                            color: #33691e !important;
                        }
                        
                        /* Past date styling */
                        .flatpickr-day.past-date {
                            background-color: #f5f5f5 !important;
                            color: #9e9e9e !important;
                            border-color: #e0e0e0 !important;
                            cursor: not-allowed !important;
                            pointer-events: none !important;
                            text-decoration: line-through !important;
                            opacity: 0.6 !important;
                        }
                        
                        /* Checkout but available after cleaning */
                        .flatpickr-day.checkout-available-date {
                            background-color: #fff3e0 !important;
                            color: #ff9800 !important;
                            border-color: #ffe0b2 !important;
                            text-decoration: none !important;
                            cursor: pointer !important;
                            pointer-events: auto !important;
                            /* Add a small checkout indicator */
                            background-image: linear-gradient(135deg, #ff980033 25%, transparent 25%) !important;
                            background-size: 10px 10px !important;
                        }

                        .flatpickr-day.checkout-available-date:hover {
                            background-color: #ffe0b2 !important;
                            color: #f57c00 !important;
                        }
                        
                        /* Make sure flatpickr-disabled is overridden for checkout-available-date */
                        .flatpickr-day.checkout-available-date.flatpickr-disabled {
                            color: #ff9800 !important;
                            background-color: #fff3e0 !important;
                            cursor: pointer !important;
                            opacity: 1 !important;
                        }
                        
                        /* Transition days - both checkout and checkin */
                        .flatpickr-day.transition-available-date {
                            background-color: #e3f2fd !important;
                            color: #1976d2 !important;
                            border-color: #bbdefb !important;
                            cursor: pointer !important;
                            pointer-events: auto !important;
                            /* Diagonal split background */
                            background-image: linear-gradient(135deg, #fff3e0 50%, #e3f2fd 50%) !important;
                        }

                        .flatpickr-day.transition-available-date:hover {
                            background-color: #bbdefb !important;
                            color: #0d47a1 !important;
                        }
                        
                        /* Make sure flatpickr-disabled is overridden for transition-available-date */
                        .flatpickr-day.transition-available-date.flatpickr-disabled {
                            color: #1976d2 !important;
                            background-color: #e3f2fd !important;
                            background-image: linear-gradient(135deg, #fff3e0 50%, #e3f2fd 50%) !important;
                            cursor: pointer !important;
                            opacity: 1 !important;
                        }
                        
                        /* Check-in days */
                        .flatpickr-day.checkin-date {
                            background-color: #e8f5e9 !important;
                            color: #388e3c !important;
                            border-color: #a5d6a7 !important;
                            background-image: linear-gradient(45deg, #a5d6a733 25%, transparent 25%) !important;
                            background-size: 10px 10px !important;
                        }
                        
                        /* Fully booked days */
                        .flatpickr-day.fully-booked-date {
                            background-color: #ffebee !important;
                            color: #d32f2f !important;
                            text-decoration: line-through !important;
                            border-color: #ffcdd2 !important;
                        }

                        .flatpickr-day.fully-booked-date:hover {
                            background-color: #ffebee !important;
                            color: #d32f2f !important;
                        }
                        
                        /* Unavailable departure dates */
                        .flatpickr-day.unavailable-departure-date {
                            background-color: #e0e0e0 !important;
                            color: #9e9e9e !important;
                            cursor: not-allowed !important;
                            border-color: #bdbdbd !important;
                            pointer-events: none !important;
                            opacity: 0.7 !important;
                        }
                        
                        /* Make sure these stay disabled */
                        .flatpickr-day.unavailable-departure-date.flatpickr-disabled {
                            background-color: #e0e0e0 !important;
                            color: #9e9e9e !important;
                            cursor: not-allowed !important;
                            pointer-events: none !important;
                        }

                        /* Calendar legend */
                        .calendar-legend {
                            display: flex;
                            flex-wrap: wrap;
                            margin-top: 10px;
                            font-size: 0.85rem;
                        }
                        
                        .legend-item {
                            display: flex;
                            align-items: center;
                            margin-right: 10px;
                            margin-bottom: 5px;
                        }
                        
                        .legend-color {
                            width: 15px;
                            height: 15px;
                            margin-right: 5px;
                            border-radius: 3px;
                            border: 1px solid #ddd;
                        }
                        
                        /* Responsive calendar */
                        @media (max-width: 768px) {
                            .flatpickr-calendar {
                                width: 100% !important;
                                max-width: 350px;
                                margin: 0 auto;
                            }

                            .flatpickr-days {
                                width: 100% !important;
                            }

                            .dayContainer {
                                width: 100% !important;
                                min-width: 100% !important;
                                max-width: 100% !important;
                            }

                            .flatpickr-day {
                                max-width: none !important;
                            }
                            
                            .calendar-legend {
                                justify-content: center;
                            }
                        }
                    `;
                document.head.appendChild(calendarStyles);

                // Add legend to explain colors
                const legend = document.createElement('div');
                legend.className = 'calendar-legend';
                legend.innerHTML = `
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #f1f8e9;"></div>
                            <span>Available</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #fff3e0; background-image: linear-gradient(135deg, #ff980033 25%, transparent 25%); background-size: 10px 10px;"></div>
                            <span>Checkout day (available after cleaning)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-image: linear-gradient(135deg, #fff3e0 50%, #e3f2fd 50%);"></div>
                            <span>Transition day (checkout/checkin)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #e8f5e9; background-image: linear-gradient(45deg, #a5d6a733 25%, transparent 25%); background-size: 10px 10px;"></div>
                            <span>Check-in day</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #ffebee;"></div>
                            <span>Fully booked</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #f5f5f5; opacity: 0.6; text-decoration: line-through;"></div>
                            <span>Past date</span>
                        </div>
                    `;




            } catch (error) {
                console.error('Error initializing calendars:', error);
                showNotification('Error loading calendar data', 'error');
            }

        }

        function updateSummary() {
            if (currentStep === 2 && bookingData.arrival && bookingData.departure) {
                summaryNewArrival.textContent = bookingData.arrival.toLocaleString();
                summaryNewDeparture.textContent = bookingData.departure.toLocaleString();
                // Calculate and display total cost here
                const numberOfDays = Math.ceil((bookingData.departure - bookingData.arrival) / (1000 * 60 * 60 * 24));
                const totalCost = numberOfDays * pricePerDayGlobal; // Use the global price per day
                summaryTotalCost.textContent = totalCost.toFixed(2);
            }
        }

        nextBtn.addEventListener('click', async function() {
            if (currentStep === 1) {
                // Basic validation
                if (!arrivalInput.value || !departureInput.value) {
                    showNotification('Please select both arrival and departure dates.', 'error');
                    return;
                }

                // Validation passed, move to step 2
                step1Content.style.display = 'none';
                step2Content.style.display = 'block';
                prevBtn.style.display = 'inline-block'; // Show the "Previous" button
                nextBtn.textContent = 'Confirm'; // Change "Next" to "Confirm"
                currentStep = 2;
                updateSummary();
            } else if (currentStep === 2) {

                submitReschedule();


                // After successful confirmation, close the modal or reset as needed
                closeRescheduleModal();
            }
        });

        function submitReschedule() {
            showLoading('Rescheduling your booking...');

            // Validate that bookingIdGlobal is set
            if (!bookingIdGlobal) {
                hideLoading();
                showNotification('Booking ID is missing. Please try again.', 'error');
                return;
            }

            // Prepare data for submission
            const rescheduleData = {
                booking_id: bookingIdGlobal,
                new_arrival: bookingData.arrival.toISOString(),
                new_departure: bookingData.departure.toISOString(),
                arrival_date: bookingData.arrival.toISOString().split('T')[0],
                arrival_time: bookingData.arrival.toTimeString().split(' ')[0],
                departure_date: bookingData.departure.toISOString().split('T')[0],
                departure_time: bookingData.departure.toTimeString().split(' ')[0]
            };

            console.log('Sending reschedule data:', rescheduleData);

            // Send the data to the server
            fetch('/Alumni-CvSU/user/reschedule_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(rescheduleData)
                })
                .then(response => {
                    console.log('Raw response:', response);
                    return response.text().then(text => {
                        console.log('Response text:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Invalid server response');
                        }
                    });
                })
                .then(data => {
                    console.log('Parsed response:', data);
                    hideLoading();
                    if (data.success) {
                        showNotification(data.message || 'Booking rescheduled successfully!',
                            'success');
                        setTimeout(() => {
                            window.location.href = 'Account?section=booking_history';
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Rescheduling failed.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error during rescheduling:', error);
                    hideLoading();
                    showNotification(error.message || 'An error occurred during rescheduling.',
                        'error');
                });
        }

        prevBtn.addEventListener('click', function() {
            if (currentStep === 2) {
                step2Content.style.display = 'none';
                step1Content.style.display = 'block';
                prevBtn.style.display = 'none'; // Hide the "Previous" button
                nextBtn.textContent = 'Next'; // Change "Confirm" back to "Next"
                currentStep = 1;
            }
        });


        // Function to format Date object to readable string with more details
        function formatDisplayDateTime(dateObj) {
            if (!dateObj) return '-';
            const options = {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            };
            return dateObj.toLocaleString('en-US', options);
        }

        // Function to format currency with ₱ symbol
        function formatCurrency(amount) {
            return `₱${amount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
        }

        // Function to calculate total cost with detailed breakdown
        function calculateTotalCost(arrival, departure) {
            if (!arrival || !departure) return {
                totalCost: 0,
                pricingDays: 0,
                remainingHours: 0,
                wholeDays: 0
            };
            // Calculate total time difference in milliseconds
            const diff = departure - arrival;
            // Calculate total hours
            const totalHours = Math.floor(diff / (1000 * 60 * 60));
            // Calculate days and remaining hours
            const wholeDays = Math.floor(totalHours / 24);
            const remainingHours = totalHours % 24;
            // For pricing purposes, we only charge for full days
            // According to Bahay ng Alumni rules, only charge for whole days
            const pricingDays = Math.max(1, wholeDays);
            // Use the stored price per day from openRescheduleModal
            const pricePerDay = pricePerDayGlobal || 100; // Use dynamic price or fall back to default
            const totalCost = pricingDays * pricePerDay;
            return {
                totalCost,
                pricingDays,
                remainingHours,
                wholeDays
            };
        }

        function openRescheduleModal(bookingId, roomId, arrivalDate, arrivalTime, departureDate, departureTime, pricePerDay) {
            bookingIdGlobal = bookingId;
            roomId = roomId; // Store the room ID globally
            pricePerDayGlobal = parseFloat(pricePerDay); // Parse the price and store globally
            bookingData = {};
            document.getElementById('rescheduleModal').style.display = 'flex'; // Corrected to 'flex'
            currentStep = 1;

            // Reset UI
            step1Content.style.display = 'block';
            step2Content.style.display = 'none';
            prevBtn.style.display = 'none';
            nextBtn.textContent = 'Next';

            // Set initial values in the summary
            summaryRoom.textContent = 'Room Name'; // Replace with actual room name if available
            summaryOldArrival.textContent = new Date(arrivalDate + ' ' + arrivalTime).toLocaleString();
            summaryOldDeparture.textContent = new Date(departureDate + ' ' + departureTime).toLocaleString();
            summaryNewArrival.textContent = '-';
            summaryNewDeparture.textContent = '-';
            summaryTotalCost.textContent = '-';

            // Fetch room ID and then initialize calendars

            initializeCalendars(roomId);

        }

        function closeRescheduleModal() {
            document.getElementById('rescheduleModal').style.display = 'none';
            // Destroy flatpickr instances to avoid conflicts
            if (arrivalFlatpickr) {
                arrivalFlatpickr.destroy();
                arrivalFlatpickr = null;
            }
            if (departureFlatpickr) {
                departureFlatpickr.destroy();
                departureFlatpickr = null;
            }
        }
    </script>

    <!-- Cancel and Invoice -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            function showLoading(message = 'Processing your request...') {
                const overlay = document.getElementById('loadingOverlay');
                const loadingText = overlay.querySelector('.loading-text');
                if (loadingText) {
                    loadingText.textContent = message;
                }
                overlay.style.display = 'flex';
                overlay.classList.add('loading-overlay-show');
                overlay.classList.remove('loading-overlay-hide');
                document.body.style.overflow = 'hidden';
            }

            function hideLoading() {
                const overlay = document.getElementById('loadingOverlay');
                overlay.classList.add('loading-overlay-hide');
                overlay.classList.remove('loading-overlay-show');
                setTimeout(() => {
                    overlay.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            }


            const cancelModal = document.getElementById('cancelModal');
            const cancelForm = document.getElementById('cancelForm');

            window.showCancelModal = function(bookingId, referenceNumber, remainingTimeText = '') {
                const bookingIdInput = document.getElementById('bookingId');
                const referenceNumberSpan = document.getElementById('referenceNumber');
                const cancelInfo = document.getElementById('cancelTimeInfo');

                if (bookingIdInput && referenceNumberSpan) {
                    bookingIdInput.value = bookingId;
                    referenceNumberSpan.textContent = referenceNumber;

                    if (cancelInfo) {
                        cancelInfo.textContent = remainingTimeText;
                        cancelInfo.style.display = remainingTimeText ? 'block' : 'none';
                    }

                    document.getElementById('cancelModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    console.error('Required modal elements not found');
                }
            };



            window.hideCancelModal = function() {
                cancelModal.style.display = 'none';
                document.body.style.overflow = '';
                if (cancelForm) {
                    cancelForm.reset();
                }
            };

            if (cancelModal) {
                cancelModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideCancelModal();
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && cancelModal.style.display === 'flex') {
                        hideCancelModal();
                    }
                });
            }

            if (cancelForm) {
                cancelForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const bookingId = document.getElementById('bookingId').value;

                    const submitButton = this.querySelector('button[type="submit"]');
                    const backButton = this.querySelector('button[type="button"]');

                    try {
                        submitButton.disabled = true;
                        backButton.disabled = true;
                        hideCancelModal();
                        showLoading('Processing cancellation...');

                        const formData = new FormData();
                        formData.append('booking_id', bookingId);
                        // formData.append('cancellation_reason', reason);

                        console.log('Sending data:', {
                            booking_id: bookingId,
                            // cancellation_reason: reason
                        });

                        const response = await fetch('user/cancel_booking.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();
                        console.log('Response:', data);

                        if (data.success) {
                            showNotification('Booking cancelled successfully', 'success');
                            setTimeout(() => {
                                window.location.href = 'Account?section=home&sidebar=1';
                            }, 1500);
                        } else {
                            hideLoading();
                            showNotification(data.message || 'Error cancelling booking', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        hideLoading();
                        showNotification('An error occurred while cancelling the booking', 'error');
                    } finally {
                        submitButton.disabled = false;
                        backButton.disabled = false;
                    }
                });
            }

            const addDataLabels = () => {
                const tables = document.querySelectorAll('.booking-table');
                tables.forEach(table => {
                    const headerTexts = Array.from(table.querySelectorAll('th')).map(th => th.textContent.trim());
                    const dataCells = table.querySelectorAll('tbody td');
                    dataCells.forEach((cell, index) => {
                        const headerIndex = index % headerTexts.length;
                        cell.setAttribute('data-label', headerTexts[headerIndex]);
                    });
                });
            };

            addDataLabels();
            window.addEventListener('resize', addDataLabels);

            window.jsPDF = window.jspdf.jsPDF;

            async function getLogo() {
                const img = document.querySelector('#your-logo-id');
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imageData.data;
                for (let i = 0; i < data.length; i += 4) {
                    if (data[i] === 255 && data[i + 1] === 255 && data[i + 2] === 255) {
                        data[i + 3] = 0;
                    }
                }
                ctx.putImageData(imageData, 0, 0);
                return canvas.toDataURL('image/png');
            }

            window.generateInvoice = async function(row) {
                try {
                    showLoading('Generating invoice...');

                    // Extract booking details from the row
                    const refNo = row.querySelector('[data-label="Reference No."]').textContent.trim();
                    const room = row.querySelector('[data-label="Room"]').textContent.trim();
                    const occupancy = row.querySelector('[data-label="Occupancy"]').textContent.trim();
                    const price = row.querySelector('[data-label="Price"]').textContent.trim();
                    const mattressFee = row.querySelector('[data-label="Mattress Fee"]').textContent.trim();
                    const checkIn = row.querySelector('[data-label="Check In"]').textContent.trim();
                    const checkOut = row.querySelector('[data-label="Check Out"]').textContent.trim();

                    const userId = row.getAttribute('data-user-id');

                    if (!userId) {
                        throw new Error('Missing user ID on row element.');
                    }

                    // Fetch user and booking details
                    const response = await fetch('user/get_user_details.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `user_id=${encodeURIComponent(userId)}`
                    });

                    const result = await response.json();

                    if (!result.success) throw new Error(result.message);

                    const userData = {
                        ...result.user_details,
                        ...result.booking
                    };

                    const doc = new jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: 'a4'
                    });

                    const leftMargin = 25;
                    const rightColumn = 120;
                    const pageWidth = 210;
                    let yPos = 20;

                    try {
                        const logoBase64 = await getLogo();
                        doc.addImage(logoBase64, 'PNG', pageWidth - 60, 10, 25, 25);
                    } catch (logoError) {
                        console.error('Error loading logo:', logoError);
                    }

                    yPos += 10;
                    doc.setFontSize(24);
                    doc.setFont('helvetica', 'bold');
                    doc.text('INVOICE', leftMargin, yPos);

                    yPos += 5;
                    doc.setDrawColor(70, 70, 70);
                    doc.setLineWidth(0.5);
                    doc.line(leftMargin, yPos, pageWidth - leftMargin, yPos);

                    yPos += 10;
                    doc.setFontSize(10);
                    doc.setFont('helvetica', 'bold');
                    doc.text('From:', leftMargin, yPos);
                    yPos += 6;
                    doc.setFont('helvetica', 'normal');
                    ['Cavite State University', 'Office of Alumni Affairs', 'Indang, Cavite', 'Philippines'].forEach(line => {
                        doc.text(line, leftMargin, yPos);
                        yPos += 5;
                    });

                    yPos += 5;
                    doc.setFont('helvetica', 'bold');
                    doc.text('Bill To:', leftMargin, yPos);
                    yPos += 6;
                    doc.setFont('helvetica', 'normal');
                    const billToDetails = [
                        `Full Name: ${userData.first_name} ${userData.middle_name || ''} ${userData.last_name}`,
                        `Position: ${userData.position || 'N/A'}`,
                        `Primary Address: ${userData.address || 'N/A'}`,
                        `Secondary Address: ${userData.second_address || 'N/A'}`,
                        `Phone: ${userData.phone_number || 'N/A'}`,
                        `Telephone: ${userData.telephone || 'N/A'}`,
                        `Status: ${userData.user_status || 'N/A'}`
                    ];
                    billToDetails.forEach(line => {
                        doc.text(line, leftMargin, yPos);
                        yPos += 5;
                    });

                    let rightYPos = 50;
                    doc.setFont('helvetica', 'bold');
                    doc.text('Invoice Details:', rightColumn, rightYPos);
                    rightYPos += 6;
                    doc.setFont('helvetica', 'normal');
                    [
                        ['Invoice Number:', refNo],
                        ['Date:', new Date().toLocaleDateString('en-US', {
                            month: 'long',
                            day: '2-digit',
                            year: 'numeric'
                        })],
                        ['Check In:', checkIn],
                        ['Check Out:', checkOut]
                    ].forEach(([label, value]) => {
                        doc.setFont('helvetica', 'bold');
                        doc.text(label, rightColumn, rightYPos);
                        doc.setFont('helvetica', 'normal');
                        doc.text(value, rightColumn + 30, rightYPos);
                        rightYPos += 5;
                    });

                    yPos = Math.max(yPos, rightYPos) + 20;
                    const tableHeaders = ['Description', 'Quantity', 'Unit Price', 'Amount'];
                    const columnWidths = [80, 25, 30, 30];
                    let xPos = leftMargin;

                    doc.setFillColor(240, 240, 240);
                    doc.rect(leftMargin, yPos - 5, pageWidth - (2 * leftMargin), 8, 'F');
                    doc.setFont('helvetica', 'bold');
                    tableHeaders.forEach((header, i) => {
                        doc.text(header, xPos, yPos);
                        xPos += columnWidths[i];
                    });

                    yPos += 10;
                    doc.setFont('helvetica', 'normal');

                    // Parse the prices correctly
                    const parsedRoomPrice = parseFloat(price.replace(/[₱,]/g, '')) || 0;
                    const parsedMattressFee = parseFloat(mattressFee.replace(/[₱,]/g, '')) || 0;

                    // Check if this is a special room (9, 10, or 11)
                    const isSpecialRoom = room === 'Board Room' || room === 'Conference Room' || room === 'Lobby';

                    // For special rooms, we don't subtract mattress fee since there's no mattress
                    const adjustedRoomPrice = isSpecialRoom ? parsedRoomPrice : (parsedRoomPrice - parsedMattressFee);
                    const totalAmount = parsedRoomPrice.toFixed(2);

                    // First row - Room price
                    xPos = leftMargin;
                    const roomContent = [
                        `${room} Accommodation`,
                        '1',
                        adjustedRoomPrice.toFixed(2),
                        adjustedRoomPrice.toFixed(2)
                    ];

                    roomContent.forEach((text, i) => {
                        doc.text(text.toString(), xPos, yPos);
                        xPos += columnWidths[i];
                    });

                    // Only add mattress row if there's a mattress fee AND it's not a special room
                    if (parsedMattressFee > 0 && !isSpecialRoom) {
                        yPos += 8;
                        xPos = leftMargin;
                        const mattressContent = [
                            'Extra Mattress Fee',
                            '1',
                            parsedMattressFee.toFixed(2),
                            parsedMattressFee.toFixed(2)
                        ];

                        mattressContent.forEach((text, i) => {
                            doc.text(text.toString(), xPos, yPos);
                            xPos += columnWidths[i];
                        });
                    }

                    yPos += 20;
                    doc.setDrawColor(200, 200, 200);
                    doc.setLineWidth(0.5);
                    doc.line(leftMargin, yPos, pageWidth - leftMargin, yPos);
                    yPos += 10;

                    // Summary totals - Adjust for special rooms
                    if (isSpecialRoom) {
                        [
                            ['Room Price:', parsedRoomPrice.toFixed(2)],
                            ['Total:', totalAmount]
                        ].forEach(([label, value]) => {
                            doc.setFont('helvetica', 'bold');
                            doc.text(label, pageWidth - 80, yPos);
                            doc.text(value.toString(), pageWidth - leftMargin, yPos, {
                                align: 'right'
                            });
                            yPos += 8;
                        });
                    } else {
                        [
                            ['Room Price:', adjustedRoomPrice.toFixed(2)],
                            ['Mattress Fee:', parsedMattressFee.toFixed(2)],
                            ['Total:', totalAmount]
                        ].forEach(([label, value]) => {
                            doc.setFont('helvetica', 'bold');
                            doc.text(label, pageWidth - 80, yPos);
                            doc.text(value.toString(), pageWidth - leftMargin, yPos, {
                                align: 'right'
                            });
                            yPos += 8;
                        });
                    }

                    yPos += 15;
                    doc.setFontSize(11);
                    doc.setFont('helvetica', 'bold');
                    doc.text('Terms & Policies:', leftMargin, yPos);
                    yPos += 8;
                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'normal');

                    const termsAndPolicies = [
                        '1. Please present this invoice along with a valid government-issued ID to the Bahay ng Alumni cashier upon check-in.',
                        '2. Cancellations must be made at least 24 hours prior to the scheduled check-in time to avoid penalties.',
                        '3. Additional charges may apply for late check-out beyond the designated check-out time.',
                        '4. The guest house is not responsible for any lost, stolen, or damaged personal belongings during your stay.'
                    ];


                    doc.setFillColor(248, 248, 248);
                    doc.rect(leftMargin - 3, yPos - 5, pageWidth - (2 * (leftMargin - 3)), termsAndPolicies.length * 6 + 6, 'F');

                    termsAndPolicies.forEach(term => {
                        doc.text(term, leftMargin, yPos);
                        yPos += 6;
                    });

                    yPos = 270;
                    doc.setFontSize(8);
                    doc.setFont('helvetica', 'normal');
                    doc.text('Thank you for your booking!', pageWidth / 2, yPos, {
                        align: 'center'
                    });

                    doc.save(`CVSU-Receipt-${refNo}.pdf`);
                    hideLoading();
                    showNotification('Receipt generated successfully', 'success');

                } catch (error) {
                    console.error('Error generating receipt:', error);
                    hideLoading();
                    showNotification('Error generating receipt', 'error');
                }
            };
        });
    </script>


    <!-- Search filter -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById("toggle-history-btn");
            const historySection = document.getElementById("booking-history-section");
            const searchContainer = document.querySelector(".search-container");
            const searchInput = document.getElementById("history-search");
            const table = document.getElementById("booking-history");

            // Toggle History Section & Search Bar
            toggleBtn.addEventListener("click", () => {
                const isVisible = historySection.style.display === "block";

                historySection.style.display = isVisible ? "none" : "block";
                searchContainer.style.display = isVisible ? "none" : "block";
                toggleBtn.textContent = isVisible ? "Show Booking History" : "Hide Booking History";
            });

            // Search Filter
            searchInput.addEventListener("input", function() {
                const filter = this.value.toLowerCase();
                const rows = table.querySelectorAll("tbody tr");

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                });
            });
        });
    </script>





</body>

</html>