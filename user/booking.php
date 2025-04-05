<?php
if (!isset($_SESSION)) {
    session_start();
}

require 'main_db.php';

$lobby_query = "SELECT MAX(occupancy) as max_occupancy FROM lobby_pricing";
$conference_query = "SELECT MAX(occupancy) as max_occupancy FROM conference_pricing";
$board_query = "SELECT MAX(occupancy) as max_occupancy FROM board_pricing";
$room_query = "SELECT MAX(occupancy) as max_occupancy FROM room_price";
$building_query = "SELECT MAX(occupancy) as max_occupancy FROM building_pricing"; // Added building query

$lobby_result = $mysqli->query($lobby_query);
$conference_result = $mysqli->query($conference_query);
$board_result = $mysqli->query($board_query);
$room_result = $mysqli->query($room_query);
$building_result = $mysqli->query($building_query); // Added building result

$lobby_max = $lobby_result->fetch_assoc()['max_occupancy'];
$conference_max = $conference_result->fetch_assoc()['max_occupancy'];
$board_max = $board_result->fetch_assoc()['max_occupancy'];
$room_max = $room_result->fetch_assoc()['max_occupancy'];
$building_max = $building_result->fetch_assoc()['max_occupancy']; // Added building max

$book_rooms = [
    ['id' => 1, 'name' => 'Room 1', 'max_occupancy' => $room_max, 'base_price' => 1800, 'price_per_person' => 25],
    ['id' => 2, 'name' => 'Room 2', 'max_occupancy' => $room_max, 'base_price' => 1800, 'price_per_person' => 25],
    ['id' => 3, 'name' => 'Room 3', 'max_occupancy' => $room_max, 'base_price' => 1800, 'price_per_person' => 30],
    ['id' => 4, 'name' => 'Room 4', 'max_occupancy' => $room_max, 'base_price' => 1800, 'price_per_person' => 30],
    ['id' => 5, 'name' => 'Room 5', 'max_occupancy' => $room_max, 'base_price' => 1800, 'price_per_person' => 20],
    ['id' => 6, 'name' => 'Room 6', 'max_occupancy' => $room_max, 'base_price' => 1800, 'price_per_person' => 25],
    ['id' => 7, 'name' => 'Room 7', 'max_occupancy' => $room_max, 'base_price' => 1800, 'price_per_person' => 28],
    ['id' => 8, 'name' => 'Room 8', 'max_occupancy' => $room_max, 'base_price' => 1800, 'price_per_person' => 30],
    ['id' => 9, 'name' => 'Board Room', 'max_occupancy' => $board_max, 'base_price' => 2500, 'price_per_person' => 15],
    ['id' => 10, 'name' => 'Conference Room', 'max_occupancy' => $conference_max, 'base_price' => 5000, 'price_per_person' => 10],
    ['id' => 11, 'name' => 'Lobby', 'max_occupancy' => $lobby_max, 'base_price' => 5000, 'price_per_person' => 8],
    ['id' => 12, 'name' => 'Building', 'max_occupancy' => $building_max, 'base_price' => 10000, 'price_per_person' => 5] // Added building entry
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
                            data-base-price="<?php echo $room['base_price']; ?>"
                            data-price-per-person="<?php echo $room['price_per_person']; ?>"
                            data-max-occupancy="<?php echo $room['max_occupancy']; ?>">
                            <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                            <div class="book-room-info">
                                <p>Max Occupancy: <?php echo $room['max_occupancy']; ?> persons</p>
                                <p class="book-room-price" style="color: var(--primary-color); font-weight: 600;">
                                    Base Price: ₱<?php echo $room['base_price']; ?>/per day
                                </p>
                                <p class="book-calculated-price" style="display: none; margin-top: 8px; font-weight: 600; color: #111827;"></p>
                            </div>
                            <div class="book-room-actions">
                                <a href="?section=room-details&id=<?php echo $room['id']; ?>" class="book-view-details">
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
                const dateStr = date.toISOString().split('T')[0];
                return bookings.some(booking => {
                    const arrivalDate = booking.arrival_date;
                    const departureDate = booking.departure_date;
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

                    if (window.arrivalCalendar) {
                        window.arrivalCalendar.destroy();
                    }
                    window.arrivalCalendar = flatpickr("#book-arrival-datetime", {
                        ...baseConfig,
                        onChange: function(selectedDates) {
                            if (selectedDates.length > 0) {
                                const selectedDate = selectedDates[0];
                                const dateStr = flatpickr.formatDate(selectedDate, "Y-m-d");
                                const dateBooking = bookingTimes.find(b => b.date === dateStr);

                                if (dateBooking) {
                                    const departureTime = new Date(dateBooking.departure_time);
                                    const availableTime = new Date(departureTime.getTime() + (2 * 60 * 60 * 1000));

                                    if (selectedDate < availableTime) {
                                        // Instead of showing error, automatically adjust time to after cleaning
                                        const formattedTime = availableTime.toLocaleTimeString('en-US', {
                                            hour: 'numeric',
                                            minute: '2-digit',
                                            hour12: true
                                        });
                                        NotificationSystem.show(`Room will be available after cleaning. Time adjusted to ${formattedTime}`, 'success');

                                        // Set the time to after cleaning is complete
                                        window.arrivalCalendar.setDate(availableTime);
                                        bookingData.arrival = availableTime;
                                    } else {
                                        bookingData.arrival = selectedDate;
                                    }
                                } else if (bookedDates.includes(dateStr)) {
                                    NotificationSystem.show('This date is fully booked. Please select another date.', 'error');
                                    window.arrivalCalendar.clear();
                                    return;
                                } else {
                                    bookingData.arrival = selectedDate;
                                }

                                if (window.departureCalendar) {
                                    window.departureCalendar.set('minDate', bookingData.arrival);
                                    if (bookingData.departure && bookingData.departure <= bookingData.arrival) {
                                        window.departureCalendar.clear();
                                        bookingData.departure = null;
                                    }
                                }
                                updateSummary();
                            }
                        },
                        onDayCreate: function(dObj, dStr, fp, dayElem) {
                            const dateStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                            const dateBooking = bookingTimes.find(b => b.date === dateStr);

                            if (dateBooking) {
                                dayElem.classList.add('checkout-date');

                                // Update tooltip to show it's available after cleaning
                                const departureTime = new Date(dateBooking.departure_time);
                                const availableTime = new Date(departureTime.getTime() + (2 * 60 * 60 * 1000));
                                const formattedTime = availableTime.toLocaleTimeString('en-US', {
                                    hour: 'numeric',
                                    minute: '2-digit',
                                    hour12: true
                                });
                                dayElem.title = `Available after ${formattedTime}`;
                            } else if (bookedDates.includes(dateStr)) {
                                dayElem.classList.add('booked-date');
                                dayElem.title = "Fully booked";
                            }
                        }
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

                                if (bookingData.arrival && selectedDate <= bookingData.arrival) {
                                    NotificationSystem.show('Departure time must be after arrival time', 'error');
                                    window.departureCalendar.clear();
                                } else {
                                    bookingData.departure = selectedDate;
                                    updateSummary();
                                }
                            }
                        }
                    });

                    const calendarStyles = document.createElement('style');
                    calendarStyles.textContent = `
                        .flatpickr-day.checkout-date {
                            background-color: #fff3e0 !important;
                            color: #ff9800 !important;
                            border-color: #ffe0b2 !important;
                            text-decoration: none !important;
                            cursor: pointer !important;
                        }

                        .flatpickr-day.checkout-date:hover {
                            background-color: #ffe0b2 !important;
                            color: #f57c00 !important;
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
                            }
                        `;
                    document.head.appendChild(calendarStyles);

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
                        const totalPriceWithDuration = bookingData.totalPrice * days;
                        document.getElementById('book-summary-price').textContent =
                            `₱${bookingData.totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} per day`;
                        document.getElementById('book-summary-total-price').textContent =
                            `₱${totalPriceWithDuration.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${days} day${days !== 1 ? 's' : ''})`;
                    }
                } else {
                    document.getElementById('book-summary-duration').textContent = '-';
                    document.getElementById('book-summary-price').textContent =
                        bookingData.totalPrice ? `₱${bookingData.totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} per day` : '-';
                    document.getElementById('book-summary-total-price').textContent = '-';
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
                const totalPriceForStay = bookingData.totalPrice * days;

                const bookingPayload = {
                    reference_number: 'BK' + Date.now().toString().slice(-8),
                    room_number: parseInt(selectedRoom.dataset.roomId),
                    occupancy: bookingData.guests,
                    price: totalPriceForStay,
                    price_per_day: bookingData.totalPrice,
                    arrival_date: bookingData.arrival.toISOString().split('T')[0],
                    arrival_time: bookingData.arrival.toTimeString().split(' ')[0],
                    departure_date: bookingData.departure.toISOString().split('T')[0],
                    departure_time: bookingData.departure.toTimeString().split(' ')[0]
                };

                console.log('Sending booking payload:', bookingPayload);

                fetch('user/booking-save.php', {
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
                                window.location.href = '?section=booking_history';
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