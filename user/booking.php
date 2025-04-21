<?php
if (!isset($_SESSION)) {
    session_start();
}

require 'main_db.php';



$room_id = isset($_GET['select_room']) ? (int)$_GET['select_room'] : 0;
$guestCount = isset($_GET['guests']) ? (int)$_GET['guests'] : 1;
$mattresses = isset($_GET['mattresses']) ? (int)$_GET['mattresses'] : 0;
$price = isset($_GET['price']) ? (float)$_GET['price'] : 0;
$totalPrice = isset($_GET['total']) ? (float)$_GET['total'] : $price; // fallback to price if total missing

// // Validate room_id and fetch room info here...
// echo "<h2>Booking: " . htmlspecialchars($room_id) . "</h2>";
// echo "<p>Guests: " . htmlspecialchars($guestCount) . "</p>";
// echo "<p>Mattresses: " . htmlspecialchars($mattresses) . "</p>";
// echo "<p>Price per day: ₱" . number_format($price, 2) . "</p>";
// echo "<p>Total Price: ₱" . number_format($totalPrice, 2) . "</p>";



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

                    <div class="book-summary-item" id="summary-guests-row" style="margin-bottom: 10px;">
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
                        <div style="display: flex; flex-direction: column; align-items: flex-end;">
                            <span class="book-summary-value" id="book-summary-total-price" style="font-size: 1.2em; color: #2563eb;">-</span>
                            <div id="excess-hours-note" class="text-sm text-green-600 mt-1" style="display: none;"></div>
                        </div>
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

            //             function enhanceTimePicker() {
            //                 // Create time picker containers
            //                 const timePickerContainers = document.querySelectorAll('.flatpickr-calendar');

            //                 timePickerContainers.forEach(container => {
            //                     // Create the time picker UI if it doesn't exist
            //                     if (!container.querySelector('.enhanced-time-picker')) {
            //                         const timePickerWrapper = document.createElement('div');
            //                         timePickerWrapper.className = 'enhanced-time-picker';

            //                         // Create preset buttons in a 2x2 grid layout
            //                         const presetContainer = document.createElement('div');
            //                         presetContainer.className = 'time-preset-container';

            //                         const presets = [{
            //                                 label: 'Morning',
            //                                 time: '09:00 AM'
            //                             },
            //                             {
            //                                 label: 'Noon',
            //                                 time: '12:00 PM'
            //                             },
            //                             {
            //                                 label: 'Afternoon',
            //                                 time: '03:00 PM'
            //                             },
            //                             {
            //                                 label: 'Evening',
            //                                 time: '06:00 PM'
            //                             }
            //                         ];

            //                         // Create the first row with Morning and Noon
            //                         const firstRow = document.createElement('div');
            //                         firstRow.className = 'preset-row';

            //                         // Create the second row with Afternoon and Evening
            //                         const secondRow = document.createElement('div');
            //                         secondRow.className = 'preset-row';

            //                         // Add buttons to appropriate rows
            //                         for (let i = 0; i < presets.length; i++) {
            //                             const preset = presets[i];
            //                             const button = document.createElement('button');
            //                             button.className = 'time-preset-btn';
            //                             button.textContent = preset.label;
            //                             button.dataset.time = preset.time;
            //                             button.onclick = function() {
            //                                 setTimeFromPreset(this, container);
            //                             };

            //                             // Add to first row if index is 0 or 1, otherwise to second row
            //                             if (i < 2) {
            //                                 firstRow.appendChild(button);
            //                             } else {
            //                                 secondRow.appendChild(button);
            //                             }
            //                         }

            //                         // Append rows to the preset container
            //                         presetContainer.appendChild(firstRow);
            //                         presetContainer.appendChild(secondRow);

            //                         // Create time range slider
            //                         const timelineContainer = document.createElement('div');
            //                         timelineContainer.className = 'time-timeline-container';

            //                         const timeline = document.createElement('div');
            //                         timeline.className = 'time-timeline';

            //                         // Create timeline markers for specific hours from 7 AM to 7 PM with 2-hour intervals
            //                         const displayHours = [7, 9, 11, 13, 15, 17, 19]; // 7am, 9am, 11am, 1pm, 3pm, 5pm, 7pm

            //                         displayHours.forEach(hour => {
            //                             const marker = document.createElement('div');
            //                             marker.className = 'time-marker';
            //                             marker.dataset.hour = hour;

            //                             const hourText = hour > 12 ? hour - 12 : hour;
            //                             const ampm = hour >= 12 ? 'PM' : 'AM';

            //                             marker.textContent = `${hourText}${ampm}`;
            //                             marker.style.left = `${(hour - 7) * (100 / 12)}%`; // Adjusted for new range (7am-7pm = 12 hours)

            //                             marker.onclick = function() {
            //                                 selectTimeFromTimeline(this, container);
            //                             };

            //                             timeline.appendChild(marker);
            //                         });

            //                         // Create slider handle
            //                         const sliderHandle = document.createElement('div');
            //                         sliderHandle.className = 'time-slider-handle';
            //                         sliderHandle.draggable = true;

            //                         // Set initial position (default to noon)
            //                         sliderHandle.style.left = `${(12 - 7) * (100 / 12)}%`; // Adjusted for new range

            //                         // Add drag events for the handle
            //                         setupDragEvents(sliderHandle, timeline, container);

            //                         timeline.appendChild(sliderHandle);
            //                         timelineContainer.appendChild(timeline);

            //                         // Create current selection display
            //                         const currentTimeDisplay = document.createElement('div');
            //                         currentTimeDisplay.className = 'current-time-display';
            //                         currentTimeDisplay.innerHTML = '<span>Selected Time: </span><span class="selected-time-value">12:00 PM</span>';

            //                         // Assemble the time picker
            //                         timePickerWrapper.appendChild(presetContainer);
            //                         timePickerWrapper.appendChild(timelineContainer);
            //                         timePickerWrapper.appendChild(currentTimeDisplay);

            //                         // Find the appropriate place to insert the time picker
            //                         const timeContainer = container.querySelector('.flatpickr-time');
            //                         if (timeContainer) {
            //                             // Hide the original time input or replace it
            //                             timeContainer.style.display = 'none';
            //                             container.insertBefore(timePickerWrapper, timeContainer.nextSibling);
            //                         } else {
            //                             // If no time container exists, append to the calendar
            //                             container.appendChild(timePickerWrapper);
            //                         }
            //                     }
            //                 });
            //             }

            //             function setTimeFromPreset(button, calendar) {
            //                 const timeStr = button.dataset.time;
            //                 const [time, period] = timeStr.split(' ');
            //                 const [hours, minutes] = time.split(':').map(Number);
            //                 // Convert to 24-hour format if needed
            //                 let hour24 = hours;
            //                 if (period === 'PM' && hours < 12) hour24 += 12;
            //                 if (period === 'AM' && hours === 12) hour24 = 0;

            //                 // Update the flatpickr instance
            //                 const flatpickrInstance = getFlatpickrInstance(calendar);
            //                 if (flatpickrInstance) {
            //                     const selectedDates = flatpickrInstance.selectedDates;
            //                     if (selectedDates.length > 0) {
            //                         const date = new Date(selectedDates[0]);
            //                         date.setHours(hour24, minutes, 0);

            //                         // Use setDate to update flatpickr (this triggers flatpickr's onChange)
            //                         flatpickrInstance.setDate(date);

            //                         // Update the visual display
            //                         updateTimeDisplay(calendar, `${timeStr}`);
            //                         updateSliderPosition(calendar, hour24);

            //                         // Update booking data based on which calendar was updated
            //                         updateBookingDataFromTime(calendar, date);
            //                     }
            //                 }

            //                 // Highlight the selected preset
            //                 const allPresets = calendar.querySelectorAll('.time-preset-btn');
            //                 allPresets.forEach(p => p.classList.remove('active'));
            //                 button.classList.add('active');
            //             }

            //             // Update selectTimeFromTimeline to update booking data
            //             function selectTimeFromTimeline(marker, calendar) {
            //                 const hour = parseInt(marker.dataset.hour);
            //                 const minutes = 0;

            //                 // Update the flatpickr instance
            //                 const flatpickrInstance = getFlatpickrInstance(calendar);
            //                 if (flatpickrInstance) {
            //                     const selectedDates = flatpickrInstance.selectedDates;
            //                     if (selectedDates.length > 0) {
            //                         const date = new Date(selectedDates[0]);
            //                         date.setHours(hour, minutes, 0);

            //                         // Update flatpickr
            //                         flatpickrInstance.setDate(date);

            //                         // Format the time for display
            //                         const displayHour = hour > 12 ? hour - 12 : hour;
            //                         const period = hour >= 12 ? 'PM' : 'AM';

            //                         // Update the visual display
            //                         updateTimeDisplay(calendar, `${displayHour}:00 ${period}`);
            //                         updateSliderPosition(calendar, hour);

            //                         // Update booking data based on which calendar was updated
            //                         updateBookingDataFromTime(calendar, date);
            //                     }
            //                 }
            //             }

            //             // Modify the moveHandle function in setupDragEvents to update booking data
            //             function setupDragEvents(handle, timeline, calendar) {
            //                 let isDragging = false;
            //                 handle.addEventListener('mousedown', startDrag);
            //                 handle.addEventListener('touchstart', startDrag, {
            //                     passive: false
            //                 });
            //                 document.addEventListener('mousemove', onDrag);
            //                 document.addEventListener('touchmove', onDrag, {
            //                     passive: false
            //                 });
            //                 document.addEventListener('mouseup', endDrag);
            //                 document.addEventListener('touchend', endDrag);

            //                 // Allow clicking directly on timeline to move handle
            //                 timeline.addEventListener('click', function(e) {
            //                     if (e.target === timeline) {
            //                         const rect = timeline.getBoundingClientRect();
            //                         const position = (e.clientX - rect.left) / rect.width;
            //                         moveHandle(position);
            //                     }
            //                 });

            //                 function startDrag(e) {
            //                     e.preventDefault();
            //                     isDragging = true;
            //                     handle.classList.add('dragging');
            //                 }

            //                 function onDrag(e) {
            //                     if (!isDragging) return;
            //                     e.preventDefault();
            //                     const rect = timeline.getBoundingClientRect();
            //                     const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
            //                     const position = (clientX - rect.left) / rect.width;
            //                     moveHandle(position);
            //                 }

            //                 function endDrag() {
            //                     if (isDragging) {
            //                         isDragging = false;
            //                         handle.classList.remove('dragging');
            //                     }
            //                 }

            //                 function moveHandle(position) {
            //                     // Clamp position between 0 and 1
            //                     position = Math.max(0, Math.min(1, position));

            //                     // Position the handle
            //                     handle.style.left = `${position * 100}%`;

            //                     // Calculate hour (between 7 AM and 7 PM)
            //                     const hourRange = 12; // 12 hours from 7AM to 7PM
            //                     const hourFloat = 7 + position * hourRange;

            //                     // Round to nearest hour (1-hour intervals)
            //                     const hour = Math.round(hourFloat);
            //                     const minutes = 0; // Always zero for hourly intervals

            //                     // Update the flatpickr instance
            //                     const flatpickrInstance = getFlatpickrInstance(calendar);
            //                     if (flatpickrInstance) {
            //                         const selectedDates = flatpickrInstance.selectedDates;
            //                         if (selectedDates.length > 0) {
            //                             const date = new Date(selectedDates[0]);
            //                             date.setHours(hour, minutes, 0);

            //                             // Update flatpickr
            //                             flatpickrInstance.setDate(date);

            //                             // Format the time for display
            //                             const displayHour = hour > 12 ? hour - 12 : hour;
            //                             const period = hour >= 12 ? 'PM' : 'AM';

            //                             // Update the visual display
            //                             updateTimeDisplay(calendar, `${displayHour}:00 ${period}`);

            //                             // Update booking data based on which calendar was updated
            //                             updateBookingDataFromTime(calendar, date);
            //                         }
            //                     }
            //                 }
            //             }

            //             // New helper function to update booking data based on which calendar was changed
            //             function updateBookingDataFromTime(calendar, date) {
            //                 const calendarIndex = Array.from(document.querySelectorAll('.flatpickr-calendar')).indexOf(calendar);

            //                 // If it's the arrival calendar
            //                 if (calendarIndex === 0) {
            //                     bookingData.arrival = date;
            //                 }
            //                 // If it's the departure calendar
            //                 else if (calendarIndex === 1) {
            //                     bookingData.departure = date;
            //                 }


            //                 // Update the booking summary with the new time information
            //                 updateSummary();
            //             }

            //             function getFlatpickrInstance(calendar) {
            //                 // Try to find the input that this calendar belongs to
            //                 const calendarIndex = Array.from(document.querySelectorAll('.flatpickr-calendar')).indexOf(calendar);

            //                 if (calendarIndex === 0) {
            //                     return window.arrivalCalendar;
            //                 } else if (calendarIndex === 1) {
            //                     return window.departureCalendar;
            //                 }

            //                 return null;
            //             }
            //             // Update the time display element with the given time string
            //             function updateTimeDisplay(calendar, timeStr) {
            //                 const display = calendar.querySelector('.selected-time-value');
            //                 if (display) {
            //                     display.textContent = timeStr;
            //                 }
            //             }


            //             function updateSliderPosition(calendar, hour) {
            //                 const handle = calendar.querySelector('.time-slider-handle');
            //                 if (handle) {
            //                     // Normalize hour to our 7AM-7PM range
            //                     const position = (hour - 7) / 12;
            //                     handle.style.left = `${position * 100}%`;
            //                 }
            //             }

            //             // Add these styles to your document
            //             function addTimePickerStyles() {
            //                 const styles = document.createElement('style');
            //                 styles.textContent = `
            //     .enhanced-time-picker {
            //         padding: 16px;
            //         background-color: #f9f9f9;
            //         border-top: 1px solid #e2e2e2;
            //         border-radius: 0 0 8px 8px;
            //         box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
            //     }

            //     .time-preset-container {
            //         display: flex;
            //         flex-direction: column;
            //         gap: 10px;
            //         margin-bottom: 20px;
            //     }

            //     .preset-row {
            //         display: flex;
            //         justify-content: space-between;
            //         width: 100%;
            //         gap: 10px;
            //     }

            //     .time-preset-btn {
            //         background-color: #f0f0f0;
            //         border: 1px solid #ddd;
            //         border-radius: 20px;
            //         padding: 8px 16px;
            //         cursor: pointer;
            //         transition: all 0.3s ease;
            //         font-size: 14px;
            //         font-weight: 500;
            //         box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            //         white-space: nowrap;
            //         flex: 1;
            //         text-align: center;
            //     }

            //     .time-preset-btn:hover {
            //         background-color: #e6e6e6;
            //         transform: translateY(-1px);
            //         box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            //     }

            //     .time-preset-btn.active {
            //         background-color: #4285f4;
            //         color: white;
            //         border-color: #3a76e3;
            //         box-shadow: 0 2px 4px rgba(66,133,244,0.3);
            //     }

            //     .time-timeline-container {
            //         position: relative;
            //         padding: 30px 0 20px;
            //         margin: 15px 0;
            //     }

            //     .time-timeline {
            //         position: relative;
            //         height: 8px;
            //         background: linear-gradient(to right, #e0e0e0, #c0c0c0);
            //         border-radius: 4px;
            //         margin: 0 10px;
            //     }

            //     .time-marker {
            //         position: absolute;
            //         transform: translateX(-50%);
            //         top: -25px;
            //         font-size: 12px;
            //         color: #555;
            //         cursor: pointer;
            //         text-align: center;
            //         font-weight: 500;
            //         transition: color 0.2s ease;
            //         white-space: nowrap;
            //     }

            //     .time-marker:hover {
            //         color: #4285f4;
            //     }

            //     .time-marker::after {
            //         content: '';
            //         position: absolute;
            //         bottom: -8px;
            //         left: 50%;
            //         transform: translateX(-50%);
            //         width: 2px;
            //         height: 8px;
            //         background-color: #888;
            //         transition: height 0.2s ease, background-color 0.2s ease;
            //     }

            //     .time-marker:hover::after {
            //         height: 12px;
            //         background-color: #4285f4;
            //     }

            //     .time-slider-handle {
            //         position: absolute;
            //         width: 20px;
            //         height: 20px;
            //         background: linear-gradient(135deg, #4285f4, #3a76e3);
            //         border-radius: 50%;
            //         top: 50%;
            //         transform: translate(-50%, -50%);
            //         cursor: grab;
            //         box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            //         z-index: 10;
            //         border: 2px solid #fff;
            //         transition: transform 0.2s ease, box-shadow 0.2s ease;
            //     }

            //     .time-slider-handle:hover {
            //         transform: translate(-50%, -50%) scale(1.1);
            //         box-shadow: 0 3px 8px rgba(0,0,0,0.4);
            //     }

            //     .time-slider-handle.dragging {
            //         cursor: grabbing;
            //         transform: translate(-50%, -50%) scale(1.15);
            //         box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            //     }

            //     .current-time-display {
            //         text-align: center;
            //         margin-top: 15px;
            //         font-size: 16px;
            //         color: #333;
            //         font-weight: 500;
            //         padding: 8px;
            //         background-color: #fff;
            //         border-radius: 20px;
            //         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            //     }

            //     .selected-time-value {
            //         color: #4285f4;
            //         font-weight: bold;
            //         margin-left: 5px;
            //     }

            //     /* Responsive styles for mobile */
            //     @media (max-width: 480px) {
            //         .time-preset-btn {
            //             padding: 6px 12px;
            //             font-size: 12px;
            //             text-align: center;
            //         }

            //         .time-marker {
            //             font-size: 10px;
            //         }

            //         .enhanced-time-picker {
            //             padding: 10px 8px;
            //         }

            //         /* Compact time display for mobile */
            //         .time-marker {
            //             letter-spacing: -0.5px;
            //         }
            //     }
            // `;
            //                 document.head.appendChild(styles);
            //             }

            //             // Initialize the enhanced time picker
            //             function initEnhancedTimePicker() {
            //                 addTimePickerStyles();
            //                 // First call to create the components
            //                 enhanceTimePicker();

            //                 // Monitor for changes to the calendar to reinitialize if needed
            //                 // (Flatpickr might recreate elements on month change or redraw)
            //                 const observer = new MutationObserver((mutations) => {
            //                     for (const mutation of mutations) {
            //                         if (mutation.type === 'childList' &&
            //                             (mutation.target.classList.contains('flatpickr-calendar') ||
            //                                 mutation.target.querySelector('.flatpickr-calendar'))) {
            //                             enhanceTimePicker();
            //                         }
            //                     }
            //                 });

            //                 observer.observe(document.body, {
            //                     childList: true,
            //                     subtree: true
            //                 });

            //                 // Also hook into the flatpickr month change event
            //                 if (window.arrivalCalendar) {
            //                     window.arrivalCalendar.config.onMonthChange = function() {
            //                         setTimeout(enhanceTimePicker, 100);
            //                     };
            //                 }

            //                 if (window.departureCalendar) {
            //                     window.departureCalendar.config.onMonthChange = function() {
            //                         setTimeout(enhanceTimePicker, 100);
            //                     };
            //                 }
            //             }

            //             // Call this after calendar initialization
            //             document.addEventListener('DOMContentLoaded', function() {
            //                 // Wait for flatpickr to initialize
            //                 setTimeout(initEnhancedTimePicker, 500);
            //             });

            //             // Add this line to your initializeCalendars function, at the end
            //             setTimeout(initEnhancedTimePicker, 500);

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

            // Maangas na time picker
            // async function initializeCalendars(roomId) {
            //     try {
            //         const arrivalInput = document.querySelector("#book-arrival-datetime");
            //         const departureInput = document.querySelector("#book-departure-datetime");

            //         if (!arrivalInput || !departureInput) {
            //             console.error('Calendar input elements not found');
            //             return;
            //         }
            //         const response = await fetch('user/get-room-bookings.php', {
            //             method: 'POST',
            //             headers: {
            //                 'Content-Type': 'application/json',
            //             },
            //             body: JSON.stringify({
            //                 room_id: roomId
            //             })
            //         });
            //         const data = await response.json();
            //         const bookedDates = data.bookedDates || [];
            //         const bookingTimes = data.bookingTimes || [];

            //         // Create a map of dates to their status for easier access
            //         const dateStatusMap = new Map();
            //         const allBookings = []

            //         bookingTimes.forEach(booking => {
            //             if (booking.arrival_date && booking.departure_date) {
            //                 allBookings.push({
            //                     arrival_date: booking.arrival_date,
            //                     arrival_time: booking.arrival_time,
            //                     departure_date: booking.departure_date,
            //                     departure_time: booking.departure_time
            //                 });
            //             }
            //         });

            //         // Mark fully booked dates
            //         bookedDates.forEach(date => {
            //             dateStatusMap.set(date, {
            //                 status: 'fully-booked',
            //                 title: 'Fully booked',
            //                 class: 'fully-booked-date',
            //                 isSelectable: false
            //             });
            //         });

            //         // Mark checkout dates
            //         bookingTimes.forEach(booking => {
            //             const checkoutDate = booking.date;
            //             const departureTime = new Date(booking.departure_time);
            //             const availableTime = new Date(departureTime.getTime() + (2 * 60 * 60 * 1000)); // 2 hours for cleaning

            //             // Check if the day after this checkout is booked
            //             const nextDayStr = new Date(new Date(checkoutDate).getTime() + 86400000).toISOString().split('T')[0];
            //             const isNextDayBooked = bookedDates.includes(nextDayStr) || allBookings.some(b => b.arrival_date === nextDayStr);

            //             // Check if the previous day is available
            //             const prevDayDate = new Date(new Date(checkoutDate).getTime() - 86400000);
            //             const prevDayStr = prevDayDate.toISOString().split('T')[0];
            //             const isPrevDayAvailable = !bookedDates.includes(prevDayStr) &&
            //                 !allBookings.some(b => b.arrival_date === prevDayStr) &&
            //                 !dateStatusMap.has(prevDayStr);

            //             // If next day is booked or previous day is not available, treat this as fully booked
            //             if (isNextDayBooked || !isPrevDayAvailable) {
            //                 dateStatusMap.set(checkoutDate, {
            //                     status: 'fully-booked',
            //                     title: 'Fully booked',
            //                     class: 'fully-booked-date',
            //                     isSelectable: false
            //                 });
            //             } else {
            //                 // Otherwise, mark as a normal checkout day available for new check-ins
            //                 dateStatusMap.set(checkoutDate, {
            //                     status: 'checkout-date',
            //                     title: `Available after ${availableTime.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}`,
            //                     availableTime: availableTime,
            //                     class: 'checkout-available-date',
            //                     isSelectable: true
            //                 });
            //             }
            //         });

            //         // Mark check-in dates
            //         allBookings.forEach(booking => {
            //             const checkinDate = booking.arrival_date;
            //             const checkinTime = new Date(booking.arrival_time);

            //             if (dateStatusMap.has(checkinDate) && dateStatusMap.get(checkinDate).status === 'checkout-date') {
            //                 // This is both a checkout and checkin date
            //                 const currentData = dateStatusMap.get(checkinDate);
            //                 dateStatusMap.set(checkinDate, {
            //                     ...currentData,
            //                     status: 'transition-date',
            //                     class: 'transition-available-date',
            //                     title: `Transition day: Available after ${currentData.availableTime.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}`
            //                 });
            //             } else if (!dateStatusMap.has(checkinDate)) {
            //                 dateStatusMap.set(checkinDate, {
            //                     status: 'checkin-date',
            //                     title: 'Check-in day',
            //                     class: 'checkin-date',
            //                     isSelectable: false // Can't check in on a day when someone else is checking in
            //                 });
            //             }
            //         });

            //         const baseConfig = {
            //             enableTime: true,
            //             dateFormat: "Y-m-d h:i K",
            //             minDate: "today",
            //             inline: window.innerWidth <= 768,
            //             time_24hr: false,
            //             minuteIncrement: 30,
            //             allowInput: true,
            //             enableSeconds: false,
            //             noCalendar: false,
            //             disableMobile: "true"
            //         };

            //         function isCheckoutOnlyDate(dateStr) {
            //             const nextDayDate = new Date(dateStr);
            //             nextDayDate.setDate(nextDayDate.getDate() + 1);
            //             const nextDayStr = flatpickr.formatDate(nextDayDate, "Y-m-d");

            //             const hasNextDayCheckin = allBookings.some(booking => booking.arrival_date === nextDayStr);

            //             return hasNextDayCheckin;
            //         }

            //         // Modify the onDayCreateHandler function to include partially booked status
            //         const onDayCreateHandler = function(dObj, dStr, fp, dayElem) {
            //             const dateStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
            //             const today = new Date();
            //             today.setHours(0, 0, 0, 0);

            //             // Check if the date is in the past
            //             if (dayElem.dateObj < today) {
            //                 dayElem.classList.add('past-date');
            //                 dayElem.title = 'Past date';
            //                 dayElem.classList.add('flatpickr-disabled');
            //                 return;
            //             }

            //             const dateStatus = dateStatusMap.get(dateStr);

            //             if (dateStatus) {
            //                 dayElem.classList.add(dateStatus.class);
            //                 dayElem.title = dateStatus.title;

            //                 // Make sure checkout-available-date and transition-available-date are not disabled
            //                 if ((dateStatus.class === 'checkout-available-date' ||
            //                         dateStatus.class === 'transition-available-date' ||
            //                         dateStatus.class === 'partially-booked-date') &&
            //                     dayElem.classList.contains('flatpickr-disabled')) {
            //                     dayElem.classList.remove('flatpickr-disabled');
            //                 }
            //             } else {
            //                 // Check if this is a partially booked date
            //                 if (isPartiallyBookedDate(dateStr)) {
            //                     // Find the relevant booking to get available times
            //                     const dayBookings = bookingTimes.filter(booking => booking.date === dateStr);
            //                     let availableBefore = null;
            //                     let availableAfter = null;

            //                     dayBookings.forEach(booking => {
            //                         const arrivalTime = new Date(booking.arrival_time);
            //                         const departureTime = new Date(booking.departure_time);

            //                         // Available before check-in (minus 2 hours for cleaning)
            //                         if (arrivalTime) {
            //                             const cleaningBuffer = new Date(arrivalTime.getTime() - (2 * 60 * 60 * 1000));
            //                             if (!availableBefore || cleaningBuffer < availableBefore) {
            //                                 availableBefore = cleaningBuffer;
            //                             }
            //                         }

            //                         // Available after check-out (plus 2 hours for cleaning)
            //                         if (departureTime) {
            //                             const availableTime = new Date(departureTime.getTime() + (2 * 60 * 60 * 1000));
            //                             if (!availableAfter || availableTime > availableAfter) {
            //                                 availableAfter = availableTime;
            //                             }
            //                         }
            //                     });

            //                     // Create availability message
            //                     let availabilityMessage = "Partially booked: ";

            //                     if (availableBefore && availableBefore.getHours() >= 7) {
            //                         availabilityMessage += `Available until ${availableBefore.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}`;
            //                     }

            //                     if (availableBefore && availableAfter) {
            //                         availabilityMessage += " and ";
            //                     }

            //                     if (availableAfter && availableAfter.getHours() < 19) {
            //                         availabilityMessage += `Available after ${availableAfter.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}`;
            //                     }

            //                     dayElem.classList.add('partially-booked-date');
            //                     dayElem.title = availabilityMessage;

            //                     // Store this information in the dateStatusMap for later use
            //                     dateStatusMap.set(dateStr, {
            //                         status: 'partially-booked',
            //                         title: availabilityMessage,
            //                         availableBefore: availableBefore,
            //                         availableAfter: availableAfter,
            //                         class: 'partially-booked-date',
            //                         isSelectable: true
            //                     });

            //                 } else if (isCheckoutOnlyDate(dateStr)) {
            //                     dayElem.classList.add('checkout-only-date');
            //                     dayElem.title = 'Available for CHECKOUT ONLY (not for check-in)';
            //                     // Optionally, disable this date for arrival selection
            //                     dayElem.classList.add('flatpickr-disabled');
            //                 } else {
            //                     dayElem.classList.add('available-date');
            //                     dayElem.title = 'Available for booking';
            //                 }
            //             }
            //         };

            //         // Custom day create handler specifically for departure calendar
            //         const departureDayCreateHandler = function(dObj, dStr, fp, dayElem) {
            //             const dateStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
            //             const today = new Date();
            //             today.setHours(0, 0, 0, 0);

            //             // Check if the date is in the past
            //             if (dayElem.dateObj < today) {
            //                 dayElem.classList.add('past-date');
            //                 dayElem.title = 'Past date';
            //                 dayElem.classList.add('flatpickr-disabled');
            //                 return;
            //             }

            //             const dateStatus = dateStatusMap.get(dateStr);

            //             if (dateStatus) {
            //                 dayElem.classList.add(dateStatus.class);
            //                 dayElem.title = dateStatus.title;

            //                 // Make sure checkout-available-date and transition-available-date are not disabled
            //                 if ((dateStatus.class === 'checkout-available-date' || dateStatus.class === 'transition-available-date') &&
            //                     dayElem.classList.contains('flatpickr-disabled')) {
            //                     dayElem.classList.remove('flatpickr-disabled');
            //                 }
            //             } else {
            //                 dayElem.classList.add('available-date');
            //                 dayElem.title = 'Available for booking';
            //             }

            //             // If we have an arrival date selected
            //             if (bookingData.arrival) {
            //                 // Check if this date is valid for departure
            //                 if (!isValidDepartureDate(dayElem.dateObj, bookingData.arrival)) {
            //                     // Mark unavailable dates with a specific class for styling
            //                     dayElem.classList.add('unavailable-departure-date');
            //                     dayElem.classList.add('flatpickr-disabled');

            //                     // Next unavailable date check
            //                     const nextUnavailable = findNextUnavailableDateAfterDate(bookingData.arrival);
            //                     if (nextUnavailable) {
            //                         const nextUnavailableDate = new Date(nextUnavailable.date);
            //                         if (dateStr >= nextUnavailable.date) {
            //                             dayElem.title = 'Unavailable - Conflict with another booking';
            //                         }
            //                     } else {
            //                         dayElem.title = 'Unavailable for checkout';
            //                     }
            //                 }
            //             }
            //         };

            //         function isValidArrivalTime(date) {
            //             // Check if the time is between 7 AM (7:00) and 7 PM (19:00)
            //             const hours = date.getHours();
            //             return hours >= 7 && hours < 19;
            //         }

            //         // Also add a new function to validate checkout time
            //         function isValidDepartureTime(date) {
            //             // Check if the time is between 7 AM (7:00) and 7 PM (19:00)
            //             const hours = date.getHours();
            //             return hours >= 7 && hours < 19;
            //         }

            //         // Update isValidArrivalDate function to handle partially booked dates
            //         function isValidArrivalDate(date) {
            //             // First check the time constraints (7 AM - 7 PM)
            //             if (!isValidArrivalTime(date)) {
            //                 return false;
            //             }

            //             // Check if date is in the past
            //             const today = new Date();
            //             today.setHours(0, 0, 0, 0);
            //             if (date < today) {
            //                 return false;
            //             }

            //             const dateStr = flatpickr.formatDate(date, "Y-m-d");
            //             const dateStatus = dateStatusMap.get(dateStr);

            //             // If no status, it's available
            //             if (!dateStatus) return true;

            //             // If fully booked or checkin date, it's not available
            //             if (dateStatus.status === 'fully-booked' || dateStatus.status === 'checkin-date') {
            //                 return false;
            //             }

            //             // Handle partially booked dates
            //             if (dateStatus.status === 'partially-booked') {
            //                 // Check if the selected time is within available hours
            //                 const selectedHour = date.getHours() + (date.getMinutes() / 60);

            //                 if (dateStatus.availableAfter) {
            //                     const availableAfterHour = dateStatus.availableAfter.getHours() +
            //                         (dateStatus.availableAfter.getMinutes() / 60);

            //                     // If selected time is after available time, it's valid
            //                     if (selectedHour >= availableAfterHour) {
            //                         return true;
            //                     }
            //                 }

            //                 if (dateStatus.availableBefore) {
            //                     const availableBeforeHour = dateStatus.availableBefore.getHours() +
            //                         (dateStatus.availableBefore.getMinutes() / 60);

            //                     // If selected time is before available time, it's valid
            //                     if (selectedHour <= availableBeforeHour) {
            //                         return true;
            //                     }
            //                 }

            //                 // If we reach here, the selected time is not within available hours
            //                 return false;
            //             }

            //             // If it's a checkout or transition date, make sure the previous day is available
            //             if (dateStatus.status === 'checkout-date' || dateStatus.status === 'transition-date') {
            //                 // Check previous day availability
            //                 const prevDay = new Date(date);
            //                 prevDay.setDate(prevDay.getDate() - 1);
            //                 const prevDayStr = flatpickr.formatDate(prevDay, "Y-m-d");
            //                 const prevDayStatus = dateStatusMap.get(prevDayStr);

            //                 // If previous day is booked, this checkout day should not be available for check-in
            //                 if (prevDayStatus && (prevDayStatus.status === 'fully-booked' || prevDayStatus.status === 'checkin-date')) {
            //                     return false;
            //                 }

            //                 return true;
            //             }

            //             return true;
            //         }

            //         // Helper function to find the next booking or fully booked date after a given date
            //         function findNextUnavailableDateAfterDate(date) {
            //             if (!date) return null;

            //             const dateStr = flatpickr.formatDate(date, "Y-m-d");

            //             // Create an array of all dates that are unavailable (either fully booked or arrival dates)
            //             const unavailableDates = [];

            //             // Add fully booked dates
            //             bookedDates.forEach(bookedDate => {
            //                 if (bookedDate > dateStr) {
            //                     unavailableDates.push({
            //                         date: bookedDate,
            //                         type: 'fully-booked'
            //                     });
            //                 }
            //             });

            //             // Add check-in dates
            //             allBookings.forEach(booking => {
            //                 if (booking.arrival_date > dateStr) {
            //                     unavailableDates.push({
            //                         date: booking.arrival_date,
            //                         type: 'check-in',
            //                         arrival_time: booking.arrival_time
            //                     });
            //                 }
            //             });

            //             // Sort by date to find the closest one
            //             unavailableDates.sort((a, b) => new Date(a.date) - new Date(b.date));

            //             // Return the next unavailable date, or null if there are none
            //             return unavailableDates.length > 0 ? unavailableDates[0] : null;
            //         }

            //         // Enhanced isPartiallyBookedDate function
            //         function isPartiallyBookedDate(dateStr) {
            //             // Check if this date has any bookings
            //             const dayBookings = bookingTimes.filter(booking => booking.date === dateStr);

            //             if (dayBookings.length === 0) return false;

            //             // For each time slot in the day (from 7am to 7pm with 2-hour cleaning buffer)
            //             // We'll track which time slots are occupied
            //             const timeSlots = new Array(13).fill(false); // 7am to 7pm in hourly slots (13 total)

            //             dayBookings.forEach(booking => {
            //                 if (booking.arrival_time && booking.departure_time) {
            //                     const arrivalTime = new Date(booking.arrival_time);
            //                     const departureTime = new Date(booking.departure_time);

            //                     // Convert to hours for easier comparison (0 = 7am, 12 = 7pm)
            //                     const arrivalHour = Math.max(0, arrivalTime.getHours() - 7);
            //                     const departureHour = Math.min(12, departureTime.getHours() - 7);

            //                     // Add cleaning buffer (2 hours)
            //                     const cleaningBeforeStart = Math.max(0, arrivalHour - 2);
            //                     const cleaningAfterEnd = Math.min(12, departureHour + 2);

            //                     // Mark occupied slots including cleaning buffer
            //                     for (let i = cleaningBeforeStart; i <= cleaningAfterEnd; i++) {
            //                         timeSlots[i] = true;
            //                     }
            //                 }
            //             });

            //             // If all slots are occupied, it's fully booked
            //             if (timeSlots.every(slot => slot === true)) {
            //                 return false; // Not partially booked, it's fully booked
            //             }

            //             // If some slots are free and some are occupied, it's partially booked
            //             if (timeSlots.some(slot => slot === true)) {
            //                 return true;
            //             }

            //             return false;
            //         }


            //         // Helper function to check if a date is valid for departure
            //         function isValidDepartureDate(date, arrivalDate) {
            //             // If no arrival date is selected, no departure dates are valid
            //             if (!arrivalDate) {
            //                 return false;
            //             }

            //             // Check if date is in the past
            //             const today = new Date();
            //             today.setHours(0, 0, 0, 0);
            //             if (date < today) {
            //                 return false;
            //             }

            //             // Must be after arrival date
            //             if (date <= arrivalDate) {
            //                 return false;
            //             }

            //             const dateStr = flatpickr.formatDate(date, "Y-m-d");
            //             const dateStatus = dateStatusMap.get(dateStr);

            //             // If fully booked, it's not available
            //             if (dateStatus && dateStatus.status === 'fully-booked') {
            //                 return false;
            //             }

            //             // Find the next unavailable date after our arrival
            //             const nextUnavailable = findNextUnavailableDateAfterDate(arrivalDate);

            //             // If there's a next unavailable date, we can only select dates up to (but not including) it
            //             if (nextUnavailable) {
            //                 const nextUnavailableDateStr = nextUnavailable.date;
            //                 const nextUnavailableDate = new Date(nextUnavailableDateStr);
            //                 nextUnavailableDate.setHours(0, 0, 0, 0);

            //                 // The date we're checking must be before the next unavailable date
            //                 // This is the key fix - we don't allow selecting dates beyond the next fully booked or check-in date
            //                 if (date >= nextUnavailableDate) {
            //                     return false;
            //                 }
            //             }

            //             // All other dates between arrival and next unavailable date are valid
            //             return true;
            //         }

            //         // Fix for the calendar initialization sequence
            //         if (window.arrivalCalendar) {
            //             window.arrivalCalendar.destroy();
            //         }

            //         // Make allBookings available in this scope
            //         window.allBookings = [];
            //         bookingTimes.forEach(booking => {
            //             if (booking.arrival_date && booking.departure_date) {
            //                 window.allBookings.push({
            //                     arrival_date: booking.arrival_date,
            //                     arrival_time: booking.arrival_time,
            //                     departure_date: booking.departure_date,
            //                     departure_time: booking.departure_time
            //                 });
            //             }
            //         });

            //         // Update the onChange function in the arrival calendar to handle partially booked dates
            //         window.arrivalCalendar = flatpickr("#book-arrival-datetime", {
            //             ...baseConfig,
            //             onChange: function(selectedDates) {
            //                 if (selectedDates.length > 0) {
            //                     const selectedDate = selectedDates[0];
            //                     const dateStr = flatpickr.formatDate(selectedDate, "Y-m-d");
            //                     const dateStatus = dateStatusMap.get(dateStr);

            //                     // First check for 7 PM cutoff
            //                     if (!isValidArrivalTime(selectedDate)) {
            //                         NotificationSystem.show('Check-ins are not allowed after 7:00 PM. Please select an earlier time or a different date.', 'error');

            //                         // Reset the time to 7:00 PM
            //                         const adjustedDate = new Date(selectedDate);
            //                         adjustedDate.setHours(19, 0, 0, 0);
            //                         // Set to 7:00 PM of the previous day if possible
            //                         adjustedDate.setDate(adjustedDate.getDate() - 1);

            //                         // Only set this time if the previous day is valid
            //                         const prevDayStr = flatpickr.formatDate(adjustedDate, "Y-m-d");
            //                         const prevDayStatus = dateStatusMap.get(prevDayStr);

            //                         if (isValidArrivalDate(adjustedDate) && (!prevDayStatus ||
            //                                 (prevDayStatus && (prevDayStatus.status === 'checkout-date' ||
            //                                     prevDayStatus.status === 'transition-date' ||
            //                                     prevDayStatus.status === 'partially-booked' ||
            //                                     !prevDayStatus.status)))) {
            //                             window.arrivalCalendar.setDate(adjustedDate);
            //                             bookingData.arrival = adjustedDate;
            //                             NotificationSystem.show('Time adjusted to 7:00 PM of the previous day', 'info');
            //                         } else {
            //                             // If previous day isn't valid, just set to 7:00 PM
            //                             adjustedDate.setDate(adjustedDate.getDate() + 1); // Move back to selected day
            //                             adjustedDate.setHours(18, 59, 0, 0); // Set to 6:59 PM
            //                             window.arrivalCalendar.setDate(adjustedDate);
            //                             bookingData.arrival = adjustedDate;
            //                             NotificationSystem.show('Time adjusted to 6:59 PM', 'info');
            //                         }
            //                         return;
            //                     }

            //                     if (dateStatus) {
            //                         if (dateStatus.status === 'checkout-date' || dateStatus.status === 'transition-date') {
            //                             if (selectedDate < dateStatus.availableTime) {
            //                                 const formattedTime = dateStatus.availableTime.toLocaleTimeString('en-US', {
            //                                     hour: 'numeric',
            //                                     minute: '2-digit',
            //                                     hour12: true
            //                                 });
            //                                 NotificationSystem.show(`Room will be available after cleaning. Time adjusted to ${formattedTime}`, 'info');
            //                                 window.arrivalCalendar.setDate(dateStatus.availableTime);
            //                                 bookingData.arrival = dateStatus.availableTime;
            //                             } else {
            //                                 bookingData.arrival = selectedDate;
            //                             }
            //                         } else if (dateStatus.status === 'partially-booked') {
            //                             // Check if selected time is within available times
            //                             if (dateStatus.availableAfter && selectedDate < dateStatus.availableAfter) {
            //                                 const formattedTime = dateStatus.availableAfter.toLocaleTimeString('en-US', {
            //                                     hour: 'numeric',
            //                                     minute: '2-digit',
            //                                     hour12: true
            //                                 });
            //                                 NotificationSystem.show(`Room will be available after cleaning. Time adjusted to ${formattedTime}`, 'info');
            //                                 window.arrivalCalendar.setDate(dateStatus.availableAfter);
            //                                 bookingData.arrival = dateStatus.availableAfter;
            //                             } else if (dateStatus.availableBefore && selectedDate > dateStatus.availableBefore) {
            //                                 // If selected time is after availableBefore, this isn't valid
            //                                 // Need to check for a different day or use availableAfter if it exists
            //                                 if (dateStatus.availableAfter) {
            //                                     const formattedTime = dateStatus.availableAfter.toLocaleTimeString('en-US', {
            //                                         hour: 'numeric',
            //                                         minute: '2-digit',
            //                                         hour12: true
            //                                     });
            //                                     NotificationSystem.show(`Selected time not available. Time adjusted to ${formattedTime}`, 'info');
            //                                     window.arrivalCalendar.setDate(dateStatus.availableAfter);
            //                                     bookingData.arrival = dateStatus.availableAfter;
            //                                 } else {
            //                                     NotificationSystem.show('This time is not available for check-in. Please select another time or date.', 'error');
            //                                     window.arrivalCalendar.clear();
            //                                     return;
            //                                 }
            //                             } else {
            //                                 bookingData.arrival = selectedDate;
            //                             }
            //                         } else if (!isValidArrivalDate(selectedDate)) {
            //                             NotificationSystem.show('This date is not available for check-in. Please select another date.', 'error');
            //                             window.arrivalCalendar.clear();
            //                             return;
            //                         } else {
            //                             bookingData.arrival = selectedDate;
            //                         }
            //                     } else {
            //                         bookingData.arrival = selectedDate;
            //                     }

            //                     // Only update the departure calendar if it exists
            //                     if (window.departureCalendar && typeof window.departureCalendar.set === 'function') {
            //                         window.departureCalendar.set('minDate', bookingData.arrival);

            //                         // Clear the departure date when arrival changes
            //                         window.departureCalendar.clear();
            //                         bookingData.departure = null;

            //                         // Find the next unavailable date to show the user their options
            //                         const nextUnavailable = findNextUnavailableDateAfterDate(bookingData.arrival);
            //                         if (nextUnavailable) {
            //                             const maxDate = new Date(nextUnavailable.date);
            //                             maxDate.setDate(maxDate.getDate() - 1); // Day before the unavailable date
            //                             const formattedMaxDate = flatpickr.formatDate(maxDate, "Y-m-d");

            //                             // Inform the user about available departure dates
            //                             const arrivalDateFormatted = flatpickr.formatDate(bookingData.arrival, "Y-m-d");
            //                             if (formattedMaxDate > arrivalDateFormatted) {
            //                                 NotificationSystem.show(`You can check out between ${arrivalDateFormatted} and ${formattedMaxDate}`, 'info');
            //                             } else {
            //                                 NotificationSystem.show(`Only ${formattedMaxDate} is available for checkout`, 'info');
            //                             }
            //                         }

            //                         // Important: Redraw the calendar to update the disabled dates
            //                         window.departureCalendar.redraw();

            //                         // Update the disabled dates for departure calendar
            //                         window.departureCalendar.set('disable', [(date) => !isValidDepartureDate(date, bookingData.arrival)]);
            //                     }

            //                     updateSummary();
            //                 }
            //             },
            //             onDayCreate: onDayCreateHandler,
            //             disable: [(date) => {
            //                 // Disable past dates
            //                 const today = new Date();
            //                 today.setHours(0, 0, 0, 0);
            //                 if (date < today) {
            //                     return true;
            //                 }

            //                 // Only disable dates that are fully booked or checkin dates
            //                 const dateStr = flatpickr.formatDate(date, "Y-m-d");
            //                 const dateStatus = dateStatusMap.get(dateStr);

            //                 if (!dateStatus) return false; // Available

            //                 // Only fully-booked and checkin dates are disabled
            //                 return dateStatus.status === 'fully-booked' || dateStatus.status === 'checkin-date';
            //             }]
            //         });

            //         if (window.departureCalendar) {
            //             window.departureCalendar.destroy();
            //         }

            //         window.departureCalendar = flatpickr("#book-departure-datetime", {
            //             ...baseConfig,
            //             minDate: bookingData.arrival || "today",
            //             onChange: function(selectedDates) {
            //                 if (selectedDates.length > 0) {
            //                     const selectedDate = selectedDates[0];
            //                     const dateStr = flatpickr.formatDate(selectedDate, "Y-m-d");

            //                     if (!isValidDepartureDate(selectedDate, bookingData.arrival)) {
            //                         NotificationSystem.show('This date is not available for checkout. Please select another date.', 'error');
            //                         window.departureCalendar.clear();
            //                         return;
            //                     }

            //                     // Find the next unavailable date after our arrival
            //                     const nextUnavailable = findNextUnavailableDateAfterDate(bookingData.arrival);

            //                     // If this is a day adjacent to check-in date, enforce time constraints
            //                     if (nextUnavailable && nextUnavailable.type === 'check-in') {
            //                         const nextUnavailableDateStr = nextUnavailable.date;
            //                         const checkoutDateStr = flatpickr.formatDate(selectedDate, "Y-m-d");

            //                         // If the selected date is the day before the next check-in
            //                         if (checkoutDateStr === nextUnavailableDateStr) {
            //                             NotificationSystem.show('Cannot check out on a day that has an incoming guest. Please select an earlier date.', 'error');
            //                             window.departureCalendar.clear();
            //                             return;
            //                         }

            //                         // For the day before check-in, ensure checkout is early enough
            //                         const nextDay = new Date(checkoutDateStr);
            //                         nextDay.setDate(nextDay.getDate() + 1);
            //                         const nextDayStr = flatpickr.formatDate(nextDay, "Y-m-d");

            //                         if (nextDayStr === nextUnavailableDateStr) {
            //                             const nextArrivalTime = new Date(nextUnavailable.arrival_time);
            //                             // Allow checkout at least 2 hours before next check-in for cleaning
            //                             const latestCheckoutTime = new Date(nextArrivalTime.getTime() - (2 * 60 * 60 * 1000));

            //                             if (selectedDate > latestCheckoutTime) {
            //                                 const formattedTime = latestCheckoutTime.toLocaleTimeString('en-US', {
            //                                     hour: 'numeric',
            //                                     minute: '2-digit',
            //                                     hour12: true
            //                                 });
            //                                 NotificationSystem.show(`You must check out at least 2 hours before the next guest arrival. Time adjusted to ${formattedTime}`, 'info');
            //                                 window.departureCalendar.setDate(latestCheckoutTime);
            //                                 bookingData.departure = latestCheckoutTime;
            //                             } else {
            //                                 bookingData.departure = selectedDate;
            //                             }
            //                         } else {
            //                             bookingData.departure = selectedDate;
            //                         }
            //                     } else {
            //                         bookingData.departure = selectedDate;
            //                     }

            //                     updateSummary();
            //                 }
            //             },
            //             onDayCreate: departureDayCreateHandler, // Use our special handler for departure days
            //             disable: [(date) => {
            //                 // Disable past dates
            //                 const today = new Date();
            //                 today.setHours(0, 0, 0, 0);
            //                 if (date < today) {
            //                     return true;
            //                 }

            //                 // Disable dates that aren't valid for departure
            //                 return !isValidDepartureDate(date, bookingData.arrival);
            //             }]
            //         });

            //         // Add a MutationObserver to ensure checkout-available-date is never disabled
            //         // This is a safety measure in case flatpickr attempts to disable these dates
            //         const observer = new MutationObserver(mutations => {
            //             mutations.forEach(mutation => {
            //                 if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
            //                     const dayElem = mutation.target;

            //                     // Don't enable past dates
            //                     if (dayElem.classList.contains('past-date')) {
            //                         if (!dayElem.classList.contains('flatpickr-disabled')) {
            //                             dayElem.classList.add('flatpickr-disabled');
            //                         }
            //                         return;
            //                     }

            //                     // Keep unavailable-departure-dates disabled
            //                     if (dayElem.classList.contains('unavailable-departure-date')) {
            //                         if (!dayElem.classList.contains('flatpickr-disabled')) {
            //                             dayElem.classList.add('flatpickr-disabled');
            //                         }
            //                         return;
            //                     }

            //                     if (dayElem.classList.contains('checkout-available-date') ||
            //                         dayElem.classList.contains('transition-available-date')) {
            //                         if (dayElem.classList.contains('flatpickr-disabled')) {
            //                             dayElem.classList.remove('flatpickr-disabled');
            //                         }
            //                     }
            //                 }
            //             });
            //         });

            //         // Start observing after a short delay to ensure flatpickr has rendered
            //         setTimeout(() => {
            //             const calendarDays = document.querySelectorAll('.flatpickr-day');
            //             calendarDays.forEach(day => {
            //                 observer.observe(day, {
            //                     attributes: true
            //                 });
            //             });
            //         }, 500);

            //         // Update the CSS styles to include partially booked status
            //         const calendarStyles = document.createElement('style');
            //         calendarStyles.textContent = `
            //             /* Available date styling */
            //             .flatpickr-day.available-date {
            //                 background-color: #f1f8e9 !important;
            //                 color: #558b2f !important;
            //                 border-color: #c5e1a5 !important;
            //                 pointer-events: auto !important;
            //             }

            //             .flatpickr-day.available-date:hover {
            //                 background-color: #dcedc8 !important;
            //                 color: #33691e !important;
            //             }

            //             /* Past date styling */
            //             .flatpickr-day.past-date {
            //                 background-color: #f5f5f5 !important;
            //                 color: #9e9e9e !important;
            //                 border-color: #e0e0e0 !important;
            //                 cursor: not-allowed !important;
            //                 pointer-events: none !important;
            //                 text-decoration: line-through !important;
            //                 opacity: 0.6 !important;
            //             }

            //             /* Checkout but available after cleaning */
            //             .flatpickr-day.checkout-available-date {
            //                 background-color: #fff3e0 !important;
            //                 color: #ff9800 !important;
            //                 border-color: #ffe0b2 !important;
            //                 text-decoration: none !important;
            //                 cursor: pointer !important;
            //                 pointer-events: auto !important;
            //                 /* Add a small checkout indicator */
            //                 background-image: linear-gradient(135deg, #ff980033 25%, transparent 25%) !important;
            //                 background-size: 10px 10px !important;
            //             }

            //             .flatpickr-day.checkout-available-date:hover {
            //                 background-color: #ffe0b2 !important;
            //                 color: #f57c00 !important;
            //             }

            //             /* Partially booked days */
            //             .flatpickr-day.partially-booked-date {
            //                 background-color: #ffe0b2 !important;
            //                 color: #e65100 !important;
            //                 border-color: #ffcc80 !important;
            //                 text-decoration: none !important;
            //                 cursor: pointer !important;
            //                 pointer-events: auto !important;
            //                 /* Half-filled pattern to indicate partial booking */
            //                 background-image: linear-gradient(45deg, #e6510033 50%, transparent 50%) !important;
            //                 background-size: 10px 10px !important;
            //             }

            //             .flatpickr-day.partially-booked-date:hover {
            //                 background-color: #ffcc80 !important;
            //                 color: #bf360c !important;
            //             }

            //             /* Make sure flatpickr-disabled is overridden for partially-booked-date */
            //             .flatpickr-day.partially-booked-date.flatpickr-disabled {
            //                 color: #e65100 !important;
            //                 background-color: #ffe0b2 !important;
            //                 cursor: pointer !important;
            //                 opacity: 1 !important;
            //             }

            //             /* Make sure flatpickr-disabled is overridden for checkout-available-date */
            //             .flatpickr-day.checkout-available-date.flatpickr-disabled {
            //                 color: #ff9800 !important;
            //                 background-color: #fff3e0 !important;
            //                 cursor: pointer !important;
            //                 opacity: 1 !important;
            //             }

            //             /* Transition days - both checkout and checkin */
            //             .flatpickr-day.transition-available-date {
            //                 background-color: #e3f2fd !important;
            //                 color: #1976d2 !important;
            //                 border-color: #bbdefb !important;
            //                 cursor: pointer !important;
            //                 pointer-events: auto !important;
            //                 /* Diagonal split background */
            //                 background-image: linear-gradient(135deg, #fff3e0 50%, #e3f2fd 50%) !important;
            //             }

            //             .flatpickr-day.transition-available-date:hover {
            //                 background-color: #bbdefb !important;
            //                 color: #0d47a1 !important;
            //             }

            //             /* Make sure flatpickr-disabled is overridden for transition-available-date */
            //             .flatpickr-day.transition-available-date.flatpickr-disabled {
            //                 color: #1976d2 !important;
            //                 background-color: #e3f2fd !important;
            //                 background-image: linear-gradient(135deg, #fff3e0 50%, #e3f2fd 50%) !important;
            //                 cursor: pointer !important;
            //                 opacity: 1 !important;
            //             }

            //             /* Check-in days */
            //             .flatpickr-day.checkin-date {
            //                 background-color: #e8f5e9 !important;
            //                 color: #388e3c !important;
            //                 border-color: #a5d6a7 !important;
            //                 background-image: linear-gradient(45deg, #a5d6a733 25%, transparent 25%) !important;
            //                 background-size: 10px 10px !important;
            //             }

            //             /* Fully booked days */
            //             .flatpickr-day.fully-booked-date {
            //                 background-color: #ffebee !important;
            //                 color: #d32f2f !important;
            //                 text-decoration: line-through !important;
            //                 border-color: #ffcdd2 !important;
            //             }

            //             .flatpickr-day.fully-booked-date:hover {
            //                 background-color: #ffebee !important;
            //                 color: #d32f2f !important;
            //             }

            //             /* Unavailable departure dates */
            //             .flatpickr-day.unavailable-departure-date {
            //                 background-color: #e0e0e0 !important;
            //                 color: #9e9e9e !important;
            //                 cursor: not-allowed !important;
            //                 border-color: #bdbdbd !important;
            //                 pointer-events: none !important;
            //                 opacity: 0.7 !important;
            //             }

            //             /* Make sure these stay disabled */
            //             .flatpickr-day.unavailable-departure-date.flatpickr-disabled {
            //                 background-color: #e0e0e0 !important;
            //                 color: #9e9e9e !important;
            //                 cursor: not-allowed !important;
            //                 pointer-events: none !important;
            //             }

            //             /* Calendar legend */
            //             .calendar-legend {
            //                 display: flex;
            //                 flex-wrap: wrap;
            //                 margin-top: 10px;
            //                 font-size: 0.85rem;
            //             }

            //             .legend-item {
            //                 display: flex;
            //                 align-items: center;
            //                 margin-right: 10px;
            //                 margin-bottom: 5px;
            //             }

            //             .legend-color {
            //                 width: 15px;
            //                 height: 15px;
            //                 margin-right: 5px;
            //                 border-radius: 3px;
            //                 border: 1px solid #ddd;
            //             }

            //             /* Responsive calendar */
            //             @media (max-width: 768px) {
            //                 .flatpickr-calendar {
            //                     width: 100% !important;
            //                     max-width: 350px;
            //                     margin: 0 auto;
            //                 }

            //                 .dayContainer {
            //                     width: 100% !important;
            //                     min-width: 100% !important;
            //                     max-width: 100% !important;
            //                 }

            //                 .flatpickr-day {
            //                     max-width: none !important;
            //                 }

            //                 .calendar-legend {
            //                     justify-content: center;
            //                 }
            //             }
            //         `;
            //         document.head.appendChild(calendarStyles);

            //         // Update the legend to include partially booked status
            //         const legend = document.createElement('div');
            //         legend.className = 'calendar-legend';
            //         legend.innerHTML = `
            //             <div class="legend-item">
            //                 <div class="legend-color" style="background-color: #f1f8e9;"></div>
            //                 <span>Available</span>
            //             </div>
            //             <div class="legend-item">
            //                 <div class="legend-color" style="background-color: #fff3e0; background-image: linear-gradient(135deg, #ff980033 25%, transparent 25%); background-size: 10px 10px;"></div>
            //                 <span>Checkout day (available after cleaning)</span>
            //             </div>
            //             <div class="legend-item">
            //                 <div class="legend-color" style="background-color: #ffe0b2; background-image: linear-gradient(45deg, #e6510033 50%, transparent 50%); background-size: 10px 10px;"></div>
            //                 <span>Partially booked (limited hours)</span>
            //             </div>
            //             <div class="legend-item">
            //                 <div class="legend-color" style="background-image: linear-gradient(135deg, #fff3e0 50%, #e3f2fd 50%);"></div>
            //                 <span>Transition day (checkout/checkin)</span>
            //             </div>
            //             <div class="legend-item">
            //                 <div class="legend-color" style="background-color: #e8f5e9; background-image: linear-gradient(45deg, #a5d6a733 25%, transparent 25%); background-size: 10px 10px;"></div>
            //                 <span>Check-in day</span>
            //             </div>
            //             <div class="legend-item">
            //                 <div class="legend-color" style="background-color: #ffebee;"></div>
            //                 <span>Fully booked</span>
            //             </div>
            //             <div class="legend-item">
            //                 <div class="legend-color" style="background-color: #f5f5f5; opacity: 0.6; text-decoration: line-through;"></div>
            //                 <span>Past date</span>
            //             </div>
            //         `;

            //         // Insert the legend after each calendar
            //         const arrivalCalendarContainer = document.querySelector('#book-arrival-datetime').closest('.flatpickr-calendar-container');
            //         const departureCalendarContainer = document.querySelector('#book-departure-datetime').closest('.flatpickr-calendar-container');

            //         if (arrivalCalendarContainer) {
            //             arrivalCalendarContainer.appendChild(legend.cloneNode(true));
            //         }

            //         if (departureCalendarContainer) {
            //             departureCalendarContainer.appendChild(legend.cloneNode(true));
            //         }

            //     } catch (error) {
            //         console.error('Error initializing calendars:', error);
            //         NotificationSystem.show('Error loading calendar data', 'error');
            //     }
            // }

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

            document.addEventListener('DOMContentLoaded', function() {
                // Wait for flatpickr to initialize
                setTimeout(initEnhancedTimePicker, 500);
            });


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
                // Helper function to format dates with custom format
                function formatDateTime(date) {
                    if (!date) return '-';
                    const options = {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    };
                    return date.toLocaleString('en-US', options);
                }



                // Update room and guests info
                document.getElementById('book-summary-room').textContent = bookingData.room || '-';
                const guestsRow = document.getElementById('summary-guests-row');

                if (bookingData.roomId >= 1 && bookingData.roomId <= 8) {
                    // Standard rooms: show guests info
                    guestsRow.style.display = 'flex-end';
                    document.getElementById('book-summary-guests').textContent = bookingData.guests && bookingData.guests > 0 ?
                        `${bookingData.guests} ${bookingData.guests === 1 ? 'Guest' : 'Guests'}` : '-';
                } else {
                    // Conference, Board, Lobby: hide the guests row entirely
                    guestsRow.style.display = 'none';
                }


                // Update check-in/check-out with better formatting
                document.getElementById('book-summary-checkin').textContent =
                    bookingData.arrival ? formatDateTime(bookingData.arrival) : '-';
                document.getElementById('book-summary-checkout').textContent =
                    bookingData.departure ? formatDateTime(bookingData.departure) : '-';

                // Calculate duration and pricing
                if (bookingData.arrival && bookingData.departure) {
                    // Calculate total time difference in milliseconds
                    const diff = bookingData.departure - bookingData.arrival;

                    // Calculate total hours
                    const totalHours = Math.floor(diff / (1000 * 60 * 60));

                    // Calculate days and remaining hours
                    const wholeDays = Math.floor(totalHours / 24);
                    const remainingHours = totalHours % 24;

                    // Create simple duration display that includes days and hours
                    let durationText = '';
                    if (wholeDays > 0) {
                        durationText = `${wholeDays} ${wholeDays === 1 ? 'day' : 'days'}`;
                        if (remainingHours > 0) {
                            durationText += ` and ${remainingHours} ${remainingHours === 1 ? 'hour' : 'hours'}`;
                        }
                    } else {
                        // Handle case when less than 24 hours, but ensure minimum 1 day for pricing
                        durationText = `${remainingHours} ${remainingHours === 1 ? 'hour' : 'hours'}`;
                    }
                    document.getElementById('book-summary-duration').textContent = durationText;

                    // For pricing purposes, we only charge for full days
                    // According to Bahay ng Alumni rules, only charge for whole days
                    const pricingDays = Math.max(1, wholeDays);

                    if (bookingData.totalPrice) {
                        // Calculate room price based on pricing days (whole days only)
                        const totalRoomPrice = bookingData.totalPrice * pricingDays;

                        // Calculate mattress cost (₱500 per mattress per day)
                        const mattressCount = bookingData.mattresses || 0;
                        const MATTRESS_RATE = 500; // ₱500 per mattress per day
                        const mattressPrice = mattressCount * MATTRESS_RATE * pricingDays;

                        // Calculate final total
                        const totalPriceWithExtras = totalRoomPrice + mattressPrice;

                        // Format currency with ₱ symbol
                        const formatCurrency = (amount) => {
                            return `₱${amount.toLocaleString('en-US', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                })}`;
                        };

                        // Update pricing elements
                        document.getElementById('book-summary-price').textContent =
                            `${formatCurrency(bookingData.totalPrice)} per day`;

                        // Update total price with day information (using pricing days)
                        document.getElementById('book-summary-total-price').textContent =
                            `${formatCurrency(totalPriceWithExtras)} (${pricingDays} ${pricingDays === 1 ? 'day' : 'days'})`;

                        // Add note about free excess hours if applicable
                        if (remainingHours > 0 && wholeDays > 0) {
                            const noteElement = document.getElementById('excess-hours-note');
                            if (noteElement) {
                                noteElement.textContent = `Note: The additional ${remainingHours} ${remainingHours === 1 ? 'hour' : 'hours'} beyond ${pricingDays} ${pricingDays === 1 ? 'day' : 'days'} are free of charge.`;
                                noteElement.style.display = 'block';
                            } else {
                                // Create a note element if it doesn't exist
                                // Find the right container - this should be the div containing the total price span
                                const priceContainer = document.getElementById('book-summary-total-price').closest('div');
                                const noteEl = document.createElement('div');
                                noteEl.id = 'excess-hours-note';
                                noteEl.className = 'text-sm text-green-600 mt-1';
                                noteEl.textContent = `Note: The additional ${remainingHours} ${remainingHours === 1 ? 'hour' : 'hours'} beyond ${pricingDays} ${pricingDays === 1 ? 'day' : 'days'} are free of charge.`;
                                priceContainer.appendChild(noteEl);
                            }
                        } else {
                            // Hide note if no excess hours
                            const noteElement = document.getElementById('excess-hours-note');
                            if (noteElement) {
                                noteElement.style.display = 'none';
                            }
                        }
                    }
                } else {
                    // Default values when dates are not selected
                    document.getElementById('book-summary-duration').textContent = '-';
                    document.getElementById('book-summary-price').textContent =
                        bookingData.totalPrice ?
                        `₱${bookingData.totalPrice.toLocaleString('en-US', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            })} per day` : '-';
                    document.getElementById('book-summary-total-price').textContent = '-';

                    // Hide excess hours note
                    const noteElement = document.getElementById('excess-hours-note');
                    if (noteElement) {
                        noteElement.style.display = 'none';
                    }
                }

                // Update mattress line with daily rate information
                const mattressEl = document.getElementById('book-summary-mattresses');
                if (mattressEl) {
                    if (bookingData.mattresses && bookingData.mattresses > 0) {
                        mattressEl.textContent = `${bookingData.mattresses} extra ${
                bookingData.mattresses === 1 ? 'mattress' : 'mattresses'
            } × ₱500 per day`;
                    } else {
                        mattressEl.textContent = 'None';
                    }
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
                    occupancy: (bookingData.guests && bookingData.guests > 0) ? bookingData.guests : bookingData.maxOccupancy,
                    price: totalPriceForStay,
                    price_per_day: bookingData.totalPrice,
                    arrival_date: bookingData.arrival.toISOString().split('T')[0],
                    arrival_time: bookingData.arrival.toTimeString().split(' ')[0],
                    departure_date: bookingData.departure.toISOString().split('T')[0],
                    departure_time: bookingData.departure.toTimeString().split(' ')[0],
                    mattresses: bookingData.mattresses
                };

                console.log('Sending booking payload:', bookingPayload);

                fetch('/Alumni-CvSU/user/booking-save.php', {
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


            const fromDetails = urlParams.get('select_room');
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