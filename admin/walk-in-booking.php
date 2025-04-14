<?php
if (!isset($_SESSION)) {
    session_start();
}

require 'main_db.php';


$mattresses = isset($_GET['mattresses']) ? (int)$_GET['mattresses'] : 0;
$totalPrice = isset($_GET['total']) ? (int)$_GET['total'] : 0;
$guestCount = isset($_GET['guests']) ? (int)$_GET['guests'] : 1; // Default to 1 guest


function getRoomData($mysqli, $tableName)
{
    $query = "SELECT occupancy, price FROM {$tableName} ORDER BY occupancy DESC LIMIT 1";
    $result = $mysqli->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        return [
            'max_occupancy' => $row['occupancy'],
            'price' => $row['price']
        ];
    }
    return [
        'max_occupancy' => 0,
        'price' => 0
    ];
}

$roomData = getRoomData($mysqli, 'room_price');
$boardData = getRoomData($mysqli, 'board_pricing');
$conferenceData = getRoomData($mysqli, 'conference_pricing');
$lobbyData = getRoomData($mysqli, 'lobby_pricing');
$buildingData = getRoomData($mysqli, 'building_pricing');

$book_rooms = [
    ['id' => 1, 'name' => 'Room 1', 'max_occupancy' => $roomData['max_occupancy'], 'price' => $roomData['price']],
    ['id' => 2, 'name' => 'Room 2', 'max_occupancy' => $roomData['max_occupancy'], 'price' => $roomData['price']],
    ['id' => 3, 'name' => 'Room 3', 'max_occupancy' => $roomData['max_occupancy'], 'price' => $roomData['price']],
    ['id' => 4, 'name' => 'Room 4', 'max_occupancy' => $roomData['max_occupancy'], 'price' => $roomData['price']],
    ['id' => 5, 'name' => 'Room 5', 'max_occupancy' => $roomData['max_occupancy'], 'price' => $roomData['price']],
    ['id' => 6, 'name' => 'Room 6', 'max_occupancy' => $roomData['max_occupancy'], 'price' => $roomData['price']],
    ['id' => 7, 'name' => 'Room 7', 'max_occupancy' => $roomData['max_occupancy'], 'price' => $roomData['price']],
    ['id' => 8, 'name' => 'Room 8', 'max_occupancy' => $roomData['max_occupancy'], 'price' => $roomData['price']],
    ['id' => 9, 'name' => 'Board Room', 'max_occupancy' => $boardData['max_occupancy'], 'price' => $boardData['price']],
    ['id' => 10, 'name' => 'Conference Room', 'max_occupancy' => $conferenceData['max_occupancy'], 'price' => $conferenceData['price']],
    ['id' => 11, 'name' => 'Lobby', 'max_occupancy' => $lobbyData['max_occupancy'], 'price' => $lobbyData['price']],
    ['id' => 12, 'name' => 'Building', 'max_occupancy' => $buildingData['max_occupancy'], 'price' => $buildingData['price']],
];

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
</head>
<style>
    :root {
        --primary-color: #2d6936;
        --secondary-color: #1e40af;
        --background-color: #f4f6f8;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    body {
        background: var(--background-color);
        min-height: 100vh;
        padding: 10px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        margin: 0;
    }

    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .notification {
        background: white;
        padding: 15px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 300px;
        max-width: 450px;
        animation: slideIn 0.3s ease-out;
    }

    .notification.success {
        background: #2d6936;
        color: white;
        border-left: 4px solid #1a4721;
    }

    .notification.error {
        background: #dc2626;
        color: white;
        border-left: 4px solid #991b1b;
    }

    .notification-close {
        background: none;
        border: none;
        color: currentColor;
        cursor: pointer;
        padding: 0 5px;
        margin-left: 10px;
        font-size: 20px;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .book-card {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .book-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .book-header h1 {
        font-size: 24px;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .book-header h1 i {
        color: var(--primary-color);
    }

    .book-content {
        padding: 24px;
    }

    .book-step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
        padding: 0 20px;
    }

    .book-step-indicator::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 40px;
        right: 40px;
        height: 2px;
        background: #e5e7eb;
        transform: translateY(-50%);
        z-index: 1;
    }

    .book-step {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #6b7280;
        position: relative;
        z-index: 2;
    }

    .book-step.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .book-step.completed {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .book-room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .book-room-card {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .book-room-card:hover {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-sm);
    }

    .book-room-card.selected {
        border-color: var(--primary-color);
        background: #ecfdf5;
    }

    .book-room-card h3 {
        margin: 0 0 8px 0;
        color: #111827;
    }

    .book-room-info {
        color: #6b7280;
        font-size: 14px;
    }

    .book-room-actions {
        display: flex;
        gap: 10px;
        margin-top: 12px;
    }

    .book-view-details {
        padding: 8px 16px;
        background: var(--secondary-color);
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 14px;
    }

    .book-occupancy-select {
        width: 100%;
        padding: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 30px;
        font-size: 16px;
    }

    .book-date-time-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .book-date-time-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 16px;
    }

    .book-summary {
        background: #f9fafb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .book-summary-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .book-summary-item:last-child {
        border-bottom: none;
    }

    .book-summary-label {
        color: #6b7280;
        font-weight: 500;
    }

    .book-summary-value {
        color: #111827;
        font-weight: 600;
    }

    .book-button-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .book-nav-button {
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .book-prev-button {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        color: #4b5563;
    }

    .book-prev-button:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    .book-next-button {
        background: var(--primary-color);
        border: 1px solid var(--primary-color);
        color: white;
    }

    .book-next-button:hover {
        background: #235329;
    }

    @media (max-width: 768px) {

        .book-header,
        .book-content {
            padding: 16px;
        }

        .book-room-grid {
            grid-template-columns: 1fr;
        }

        .book-button-container {
            flex-direction: column;
        }

        .book-nav-button {
            width: 100%;
        }
    }

    .flatpickr-day.booked-date {
        background-color: #ffebee !important;
        color: #d32f2f !important;
        text-decoration: line-through;
        border-color: #ffcdd2 !important;
    }

    .flatpickr-day.booked-date:hover {
        background-color: #ffebee !important;
        color: #d32f2f !important;
    }

    .flatpickr-day.selected.booked-date {
        background-color: #d32f2f !important;
        color: white !important;
    }

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }

    .loading-content {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .loading-text {
        color: white;
        font-size: 14px;
        font-weight: 500;
        animation: pulse 1.5s ease-in-out infinite;
        margin: 0;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes pulse {
        0% {
            opacity: 0.6;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.6;
        }
    }

    .loading-overlay-show {
        animation: fadeIn 0.3s ease-in-out forwards;
    }

    .loading-overlay-hide {
        animation: fadeOut 0.3s ease-in-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }
</style>

<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing your booking...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="book-card">
        <div class="book-header">
            <h1><i class="fas fa-hotel"></i>Booking</h1>
        </div>

        <div class="book-content">
            <div class="book-step-indicator">
                <div class="book-step active">1</div>
                <div class="book-step">2</div>
                <div class="book-step">3</div>
                <div class="book-step">4</div>
            </div>

            <div id="book-step1" class="book-step-content">
                <div class="book-room-grid">
                    <?php foreach ($book_rooms as $room): ?>
                        <div class="book-room-card"
                            data-room-id="<?php echo $room['id']; ?>"
                            data-price="<?php echo $room['price']; ?>"
                            data-max-occupancy="<?php echo $room['max_occupancy']; ?>">
                            <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                            <div class="book-room-info">
                                <p>Max Occupancy: <?php echo $room['max_occupancy']; ?> persons</p>
                                <p class="book-room-price" style="color: var(--primary-color); font-weight: 600;">
                                    Price: ₱<?php echo number_format($room['price']); ?> / per day
                                </p>
                                <p class="book-calculated-price" style="display: none; margin-top: 8px; font-weight: 600; color: #111827;"></p>
                            </div>
                            <div class="book-room-actions">
                                <a href="?section=Walkin-details&id=<?php echo $room['id']; ?>" class="book-view-details">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="book-step2" class="book-step-content" style="display: none;">
                <div class="book-date-time-container">
                    <div>
                        <label>Arrival Date and Time</label>
                        <input type="text" class="book-date-time-input" id="book-arrival-datetime" placeholder="Select arrival date and time">
                    </div>
                </div>
            </div>
            <div id="book-step3" class="book-step-content" style="display: none;">
                <div class="book-date-time-container">
                    <div>
                        <label>Departure Date and Time</label>
                        <input type="text" class="book-date-time-input" id="book-departure-datetime" placeholder="Select departure date and time">
                    </div>
                </div>
            </div>

            <div id="book-step4" class="book-step-content" style="display: none;">
                <div class="book-summary" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px;">
                    <h2 style="margin-top: 0; color: #111827; font-size: 1.5em; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                        Booking Summary
                    </h2>

                    <div class="book-summary-item" style="margin-bottom: 10px;">
                        <span class="book-summary-label" style="font-weight: 600;">Selected Room:</span>
                        <span class="book-summary-value" id="book-summary-room">-</span>
                    </div>

                    <div class="book-summary-item" style="margin-bottom: 10px;">
                        <span class="book-summary-label" style="font-weight: 600;">Number of Guests:</span>
                        <span class="book-summary-value" id="book-summary-guests">-</span>
                    </div>

                    <div class="book-summary-item" style="margin-bottom: 10px;">
                        <span class="book-summary-label" style="font-weight: 600;">Check-in:</span>
                        <span class="book-summary-value" id="book-summary-checkin">-</span>
                    </div>

                    <div class="book-summary-item" style="margin-bottom: 10px;">
                        <span class="book-summary-label" style="font-weight: 600;">Check-out:</span>
                        <span class="book-summary-value" id="book-summary-checkout">-</span>
                    </div>

                    <div class="book-summary-item" style="margin-bottom: 10px;">
                        <span class="book-summary-label" style="font-weight: 600;">Duration:</span>
                        <span class="book-summary-value" id="book-summary-duration">-</span>
                    </div>

                    <div class="book-summary-item" style="margin-bottom: 10px;">
                        <span class="book-summary-label" style="font-weight: 600;">Price per Night:</span>
                        <span class="book-summary-value" id="book-summary-price">-</span>
                    </div>
                    <div class="book-summary-item" style="margin-bottom: 10px;">
                        <span class="book-summary-label" style="font-weight: 600;">Mattress Fee:</span>
                        <span class="book-summary-value" id="book-summary-mattresses">-</span>
                    </div>



                    <div class="book-summary-item" style="margin-top: 20px; padding-top: 10px;">
                        <span class="book-summary-label" style="font-size: 1.2em; font-weight: bold;">Total Price:</span>
                        <span class="book-summary-value" id="book-summary-total-price" style="font-size: 1.2em; color: #2563eb;">-</span>
                    </div>
                </div>
            </div>


            <div class="book-button-container">
                <button id="book-prev-button" class="book-nav-button book-prev-button" style="display: none;">Previous</button>
                <button id="book-next-button" class="book-nav-button book-next-button">Next</button>
            </div>
        </div>
    </div>

    <script>
        function getQueryParam(key) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(key);
        }

        const mattressCount = parseInt(getQueryParam('mattresses') || '0', 10);
        const totalPrice = parseFloat(getQueryParam('total') || '0');
        const basePrice = parseFloat(getQueryParam('price') || '0');
        const guests = parseInt(getQueryParam('guests') || '0');

        function showLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';
            overlay.classList.add('loading-overlay-show');
            overlay.classList.remove('loading-overlay-hide');
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('loading-overlay-hide');
            overlay.classList.remove('loading-overlay-show');
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300);
        }

        const NotificationSystem = {
            container: null,
            init: function() {
                this.container = document.getElementById('notificationContainer');
            },

            show: function(message, type = 'error', duration = 5000) {
                if (!this.container) return;

                const notification = document.createElement('div');
                notification.className = `notification ${type}`;

                const messageSpan = document.createElement('span');
                messageSpan.textContent = message;

                const closeButton = document.createElement('button');
                closeButton.className = 'notification-close';
                closeButton.innerHTML = '×';
                closeButton.onclick = () => this.remove(notification);

                notification.appendChild(messageSpan);
                notification.appendChild(closeButton);
                this.container.appendChild(notification);

                setTimeout(() => this.remove(notification), duration);
            },

            remove: function(notification) {
                notification.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(() => {
                    if (notification.parentElement === this.container) {
                        this.container.removeChild(notification);
                    }
                }, 300);
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            NotificationSystem.init();

            let currentStep = 1;
            const totalSteps = 4;
            let bookingData = {
                room: null,
                guests: null,
                mattresses: mattressCount,
                basePrice: basePrice,
                totalPrice: totalPrice,
                arrival: null,
                departure: null,
                basePrice: null,
                pricePerPerson: null,
                maxOccupancy: null,
                totalPrice: null
            };

            async function fetchBookings(roomId) {
                try {
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
                    return data.bookings || [];
                } catch (error) {
                    console.error('Error fetching bookings:', error);
                    return [];
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


            async function initializeCalendars(roomId) {
                try {
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
                        inline: window.innerWidth <= 768,
                        time_24hr: false,
                        minuteIncrement: 30,
                        allowInput: true,
                        enableSeconds: false,
                        noCalendar: false,
                        disableMobile: "true"
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
                        // Check if the time is after 7 PM (19:00)
                        const hours = date.getHours();
                        return hours < 19; // Returns false if hours is 19 or greater (7 PM or later)
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
                    window.arrivalCalendar = flatpickr("#book-arrival-datetime", {
                        ...baseConfig,
                        onChange: function(selectedDates) {
                            if (selectedDates.length > 0) {
                                const selectedDate = selectedDates[0];
                                const dateStr = flatpickr.formatDate(selectedDate, "Y-m-d");
                                const dateStatus = dateStatusMap.get(dateStr);

                                // First check for 7 PM cutoff
                                if (!isValidArrivalTime(selectedDate)) {
                                    NotificationSystem.show('Check-ins are not allowed after 7:00 PM. Please select an earlier time or a different date.', 'error');

                                    // Reset the time to 7:00 PM
                                    const adjustedDate = new Date(selectedDate);
                                    adjustedDate.setHours(19, 0, 0, 0);
                                    // Set to 7:00 PM of the previous day if possible
                                    adjustedDate.setDate(adjustedDate.getDate() - 1);

                                    // Only set this time if the previous day is valid
                                    const prevDayStr = flatpickr.formatDate(adjustedDate, "Y-m-d");
                                    const prevDayStatus = dateStatusMap.get(prevDayStr);

                                    if (isValidArrivalDate(adjustedDate) && (!prevDayStatus ||
                                            (prevDayStatus && (prevDayStatus.status === 'checkout-date' ||
                                                prevDayStatus.status === 'transition-date' ||
                                                !prevDayStatus.status)))) {
                                        window.arrivalCalendar.setDate(adjustedDate);
                                        bookingData.arrival = adjustedDate;
                                        NotificationSystem.show('Time adjusted to 7:00 PM of the previous day', 'info');
                                    } else {
                                        // If previous day isn't valid, just set to 7:00 PM
                                        adjustedDate.setDate(adjustedDate.getDate() + 1); // Move back to selected day
                                        adjustedDate.setHours(18, 59, 0, 0); // Set to 6:59 PM
                                        window.arrivalCalendar.setDate(adjustedDate);
                                        bookingData.arrival = adjustedDate;
                                        NotificationSystem.show('Time adjusted to 6:59 PM', 'info');
                                    }
                                    return;
                                }

                                if (dateStatus && (dateStatus.status === 'checkout-date' || dateStatus.status === 'transition-date')) {
                                    if (selectedDate < dateStatus.availableTime) {
                                        const formattedTime = dateStatus.availableTime.toLocaleTimeString('en-US', {
                                            hour: 'numeric',
                                            minute: '2-digit',
                                            hour12: true
                                        });
                                        NotificationSystem.show(`Room will be available after cleaning. Time adjusted to ${formattedTime}`, 'info');
                                        window.arrivalCalendar.setDate(dateStatus.availableTime);
                                        bookingData.arrival = dateStatus.availableTime;
                                    } else {
                                        bookingData.arrival = selectedDate;
                                    }
                                } else if (!isValidArrivalDate(selectedDate)) {
                                    NotificationSystem.show('This date is not available for check-in. Please select another date.', 'error');
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
                                            NotificationSystem.show(`You can check out between ${arrivalDateFormatted} and ${formattedMaxDate}`, 'info');
                                        } else {
                                            NotificationSystem.show(`Only ${formattedMaxDate} is available for checkout`, 'info');
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

                    window.departureCalendar = flatpickr("#book-departure-datetime", {
                        ...baseConfig,
                        minDate: bookingData.arrival || "today",
                        onChange: function(selectedDates) {
                            if (selectedDates.length > 0) {
                                const selectedDate = selectedDates[0];
                                const dateStr = flatpickr.formatDate(selectedDate, "Y-m-d");

                                if (!isValidDepartureDate(selectedDate, bookingData.arrival)) {
                                    NotificationSystem.show('This date is not available for checkout. Please select another date.', 'error');
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
                                        NotificationSystem.show('Cannot check out on a day that has an incoming guest. Please select an earlier date.', 'error');
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
                                            NotificationSystem.show(`You must check out at least 2 hours before the next guest arrival. Time adjusted to ${formattedTime}`, 'info');
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

                    // Insert the legend after each calendar
                    const arrivalCalendarContainer = document.querySelector('#book-arrival-datetime').closest('.flatpickr-calendar-container');
                    const departureCalendarContainer = document.querySelector('#book-departure-datetime').closest('.flatpickr-calendar-container');

                    if (arrivalCalendarContainer) {
                        arrivalCalendarContainer.appendChild(legend.cloneNode(true));
                    }

                    if (departureCalendarContainer) {
                        departureCalendarContainer.appendChild(legend.cloneNode(true));
                    }

                } catch (error) {
                    console.error('Error initializing calendars:', error);
                    NotificationSystem.show('Error loading calendar data', 'error');
                }
            }


            const urlParams = new URLSearchParams(window.location.search);
            const selectRoomId = urlParams.get('select_room');
            const guestCount = urlParams.get('guests');
            const selectedPrice = urlParams.get('price');

            if (guestCount) {
                bookingData.guests = parseInt(guestCount);
            }

            if (selectedPrice) {
                bookingData.totalPrice = parseFloat(selectedPrice);
            }

            if (selectRoomId) {
                const roomCard = document.querySelector(`.book-room-card[data-room-id="${selectRoomId}"]`);
                if (roomCard) {
                    handleRoomSelection(roomCard);
                }
            }

            function handleRoomSelection(roomCard) {
                document.querySelectorAll('.book-room-card').forEach(card => card.classList.remove('selected'));
                roomCard.classList.add('selected');

                bookingData.room = roomCard.querySelector('h3').textContent;
                bookingData.roomId = parseInt(roomCard.dataset.roomId);
                bookingData.basePrice = parseFloat(roomCard.dataset.basePrice);
                bookingData.pricePerPerson = parseFloat(roomCard.dataset.pricePerPerson);
                bookingData.maxOccupancy = parseInt(roomCard.dataset.maxOccupancy);

                initializeCalendars(bookingData.roomId);
                updatePricing();
                updateSummary();
            }

            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    if (bookingData.roomId) {
                        initializeCalendars(bookingData.roomId);
                    }
                }, 250);
            });

            document.querySelectorAll('.book-room-card').forEach(card => {
                card.addEventListener('click', () => handleRoomSelection(card));
            });

            function updatePricing() {
                const selectedRoom = document.querySelector('.book-room-card.selected');
                if (selectedRoom && bookingData.guests) {
                    if (bookingData.totalPrice === null) {
                        const guests = parseInt(bookingData.guests);
                        const basePrice = parseFloat(selectedRoom.dataset.basePrice);
                        const pricePerPerson = parseFloat(selectedRoom.dataset.pricePerPerson);
                        bookingData.totalPrice = basePrice + (pricePerPerson * guests);
                    }
                }
            }

            function updateSummary() {
                document.getElementById('book-summary-room').textContent = bookingData.room || '-';
                document.getElementById('book-summary-guests').textContent = bookingData.guests ?
                    `${bookingData.guests} ${bookingData.guests === 1 ? 'Guest' : 'Guests'}` : '-';
                document.getElementById('book-summary-checkin').textContent = bookingData.arrival ?
                    bookingData.arrival.toLocaleString() : '-';
                document.getElementById('book-summary-checkout').textContent = bookingData.departure ?
                    bookingData.departure.toLocaleString() : '-';

                if (bookingData.arrival && bookingData.departure) {
                    const diff = bookingData.departure - bookingData.arrival;
                    const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                    document.getElementById('book-summary-duration').textContent =
                        `${days} day${days !== 1 ? 's' : ''}`;

                    if (bookingData.totalPrice) {
                        const totalRoomPrice = bookingData.totalPrice * days;
                        // Multiply mattress price by the number of days
                        const mattressPrice = bookingData.mattresses * 500 * days;
                        const totalPriceWithExtras = totalRoomPrice + mattressPrice;

                        document.getElementById('book-summary-price').textContent =
                            `₱${bookingData.totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} per day`;
                        document.getElementById('book-summary-total-price').textContent =
                            `₱${totalPriceWithExtras.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${days} day${days !== 1 ? 's' : ''})`;
                    }
                } else {
                    document.getElementById('book-summary-duration').textContent = '-';
                    document.getElementById('book-summary-price').textContent =
                        bookingData.totalPrice ? `₱${bookingData.totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} per day` : '-';
                    document.getElementById('book-summary-total-price').textContent = '-';
                }

                // Update mattress line to show daily rate
                const mattressEl = document.getElementById('book-summary-mattresses');
                if (mattressEl) {
                    mattressEl.textContent = bookingData.mattresses ? `${bookingData.mattresses} × 500 per day` : 'None';
                }
            }


            function validateCurrentStep() {
                switch (currentStep) {
                    case 1:
                        if (!document.querySelector('.book-room-card.selected')) {
                            NotificationSystem.show('Please select a room', 'error');
                            return false;
                        }
                        if (!bookingData.guests) {
                            NotificationSystem.show('Please view room details and select the number of guests', 'error');
                            return false;
                        }
                        return true;
                    case 2:
                        if (!bookingData.arrival) {
                            NotificationSystem.show('Please select an arrival date and time', 'error');
                            return false;
                        }
                        return true;
                    case 3:
                        if (!bookingData.departure) {
                            NotificationSystem.show('Please select a departure date and time', 'error');
                            return false;
                        }
                        if (bookingData.departure <= bookingData.arrival) {
                            NotificationSystem.show('Departure time must be after arrival time', 'error');
                            return false;
                        }
                        return true;
                    case 4:
                        return true;
                    default:
                        return false;
                }
            }

            function updateStepDisplay() {
                document.querySelectorAll('.book-step-content').forEach((step, index) => {
                    step.style.display = index + 1 === currentStep ? 'block' : 'none';
                });

                document.querySelectorAll('.book-step').forEach((step, index) => {
                    if (index + 1 === currentStep) {
                        step.classList.add('active');
                        step.classList.remove('completed');
                    } else if (index + 1 < currentStep) {
                        step.classList.add('completed');
                        step.classList.remove('active');
                    } else {
                        step.classList.remove('active', 'completed');
                    }
                });

                document.getElementById('book-prev-button').style.display = currentStep === 1 ? 'none' : 'block';
                document.getElementById('book-next-button').textContent = currentStep === totalSteps ? 'Complete Booking' : 'Next';
            }

            function submitBooking() {
                const selectedRoom = document.querySelector('.book-room-card.selected');
                if (!selectedRoom) {
                    NotificationSystem.show('Please select a room', 'error');
                    return;
                }

                showLoading();

                const timeDiff = bookingData.departure - bookingData.arrival;
                const days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                const totalRoomPrice = bookingData.totalPrice * days;
                // Multiply mattress price by the number of days
                const mattressPrice = bookingData.mattresses * 500 * days;
                const totalPriceForStay = totalRoomPrice + mattressPrice;

                const bookingPayload = {
                    reference_number: 'BK' + Date.now().toString().slice(-8),
                    room_number: parseInt(selectedRoom.dataset.roomId),
                    occupancy: bookingData.guests,
                    price: totalPriceForStay,
                    price_per_day: bookingData.totalPrice,
                    arrival_date: bookingData.arrival.toISOString().split('T')[0],
                    arrival_time: bookingData.arrival.toTimeString().split(' ')[0],
                    departure_date: bookingData.departure.toISOString().split('T')[0],
                    departure_time: bookingData.departure.toTimeString().split(' ')[0],
                    mattresses: bookingData.mattresses
                };

                console.log('Sending booking payload:', bookingPayload);

                fetch('admin/walk-in-booking-save.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(bookingPayload)
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
                        if (data.success) {
                            NotificationSystem.show(`Booking confirmed! Reference: ${data.reference_number}`, 'success');
                            setTimeout(() => {
                                window.location.href = '?section=view-all-bookings&tab=pending';
                            }, 1500);
                        } else {
                            hideLoading();
                            throw new Error(data.message || 'Booking failed');
                        }
                    })
                    .catch(error => {
                        console.error('Booking error:', error);
                        hideLoading();
                        NotificationSystem.show(error.message || 'An error occurred', 'error');
                    });
            }

            document.getElementById('book-next-button').addEventListener('click', function() {
                if (!validateCurrentStep()) return;

                if (currentStep < totalSteps) {
                    currentStep++;
                    updateStepDisplay();
                } else if (currentStep === totalSteps) {
                    submitBooking();
                }
            });


            document.getElementById('book-prev-button').addEventListener('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateStepDisplay();
                }
            });


            const fromDetails = urlParams.get('select_room') && urlParams.get('guests');
            if (fromDetails) {
                currentStep = 2;
            }

            updateStepDisplay();
            if (bookingData.guests) {
                updatePricing();
                updateSummary();
            }
        });
    </script>
</body>


</html>