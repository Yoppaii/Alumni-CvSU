<?php
include 'main_db.php';
$today = date('Y-m-d');

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';

$tabs = [
    'pending' => 'Pending',
    'confirmed' => 'Confirmed',
    'checked_in' => 'Checked In',
    'cancelled' => 'Cancelled',
    'no_show' => 'No Show',
    'completed' => 'Completed',
];

$booking_type = isset($_GET['booking_type']) ? $_GET['booking_type'] : 'all';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
$limit = in_array($limit, [5, 25, 50, 100, 500]) ? $limit : 25;

$bookingsQuery = "SELECT * FROM bookings WHERE is_archived = 0";
$conditions = [];

if ($current_tab !== 'all') {
    $conditions[] = "status = '" . $mysqli->real_escape_string($current_tab) . "'";
}

if ($booking_type !== 'all') {
    $conditions[] = "is_walkin = '" . $mysqli->real_escape_string($booking_type) . "'";
}

if (!empty($conditions)) {
    $bookingsQuery .= " AND " . implode(" AND ", $conditions);
}


$bookingsQuery .= " ORDER BY room_number, arrival_date DESC LIMIT $limit";

$bookingsResult = $mysqli->query($bookingsQuery);

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="admin/view_all_bookings.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>

    <style>
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
            justify-content: flex-end;
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

    <style>
        /* Make sure this applies globally */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            /* dark overlay */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            /* put it above everything */
        }

        #extend-stay-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            /* Dark transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-content {
            background-color: white;
            border-radius: 10px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
            position: relative;
        }


        /* Close button */
        .modal-close-button {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #374151;
        }


        /* Optional animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .flatpickr-calendar {
            z-index: 9999 !important;
        }
    </style>

    <style>
        .AL-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .AL-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .AL-modal {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            width: 90%;
            max-width: 400px;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .AL-modal-overlay.active .AL-modal {
            transform: translateY(0);
        }

        .AL-modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .AL-modal-icon {
            width: 32px;
            height: 32px;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1rem;
        }

        .AL-modal-icon.warning {
            background-color: var(--warning-color);
            color: white;
        }

        .AL-modal-icon.danger {
            background-color: var(--danger-color);
            color: white;
        }

        .AL-modal-icon.success {
            background-color: var(--success-color);
            color: white;
        }

        .AL-modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .AL-modal-content {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .AL-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .AL-modal-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 0.875rem;
        }

        .AL-modal-btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .AL-modal-btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .AL-modal-btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .AL-modal-btn-secondary:hover {
            background-color: var(--secondary-hover);
        }

        .AL-modal-btn-danger:hover {
            background-color: var(--danger-hover);
        }

        .AL-modal-btn-success:hover {
            background-color: var(--success-hover);
        }

        /* Dark theme support */
        [data-theme="dark"] .AL-modal {
            background-color: #1e293b;
        }

        [data-theme="dark"] .AL-modal-title {
            color: #f1f5f9;
        }

        [data-theme="dark"] .AL-modal-content {
            color: #cbd5e1;
        }

        .alm-action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
                height: auto;
                padding-top: 12px;
                /* adjust to your row padding */
                padding-bottom: 12px;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
                height: 0;
                padding-top: 0;
                padding-bottom: 0;
                margin: 0;
                border: 0;
            }
        }

        .slide-out {
            animation: slideOut 0.3s ease-out forwards;
        }
    </style>
</head>

<body>
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p class="loading-text">Processing your request...</p>
        </div>
    </div>

    <div id="view-booking-toast" class="view-booking-toast" style="display: none;">
        <div class="view-booking-toast-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="view-booking-toast-message"></div>
        <button class="view-booking-toast-close">&times;</button>
    </div>

    <div class="alm-bookings-container">
        <div class="alm-header-content">
            <h2><i class="fas fa-calendar-check"></i> View Bookings</h2>
            <form method="get" class="alm-filter-form">
                <input type="hidden" name="section" value="view-all-bookings">
                <input type="hidden" name="tab" value="<?php echo htmlspecialchars($current_tab); ?>">
                <div class="booking-filters">
                    <div class="booking-type-filter">
                        <label for="limit">Entries:</label>
                        <select name="limit" id="limit" class="booking-type-select" onchange="this.form.submit()">
                            <?php foreach ([5, 25, 50, 100, 500] as $val): ?>
                                <option value="<?php echo $val; ?>" <?php echo ($limit === $val) ? 'selected' : ''; ?>>
                                    <?php echo $val; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="booking-type-filter">
                        <label for="booking_type">Booking Type:</label>
                        <select name="booking_type" id="booking_type" class="booking-type-select" onchange="this.form.submit()">
                            <option value="all" <?php echo ($booking_type === 'all') ? 'selected' : ''; ?>>All</option>
                            <option value="yes" <?php echo ($booking_type === 'yes') ? 'selected' : ''; ?>>Walk-in</option>
                            <option value="no" <?php echo ($booking_type === 'no') ? 'selected' : ''; ?>>Online</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="alm-booking-tabs">
            <?php foreach ($tabs as $tab_id => $tab_name): ?>
                <?php
                $countQuery = "SELECT COUNT(*) as count FROM bookings WHERE is_archived = 0";
                $countConditions = [];

                if ($tab_id !== 'all') {
                    $countConditions[] = "status = '" . $mysqli->real_escape_string($tab_id) . "'";
                }

                if ($booking_type !== 'all') {
                    $countConditions[] = "is_walkin = '" . $mysqli->real_escape_string($booking_type) . "'";
                }

                if (!empty($countConditions)) {
                    $countQuery .= " AND " . implode(" AND ", $countConditions);
                }

                $countResult = $mysqli->query($countQuery);
                $count = $countResult->fetch_assoc()['count'];
                ?>
                <a href="?section=view-all-bookings&tab=<?php echo $tab_id; ?>" class="alm-booking-tab <?php echo ($current_tab === $tab_id) ? 'active' : ''; ?>">
                    <i class="<?php echo match ($tab_id) {
                                    // 'all' => 'fas fa-list',
                                    'pending' => 'fas fa-clock',
                                    'confirmed' => 'fas fa-check',
                                    'checked_in' => 'fas fa-door-open',
                                    'cancelled' => 'fas fa-times-circle',
                                    'no_show' => 'fas fa-user-slash',
                                    'completed' => 'fas fa-check-double',
                                    default => 'fas fa-bookmark'
                                }; ?>"></i>
                    <?php echo $tab_name; ?>
                    <span class="alm-booking-count"><?php echo $count; ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="alm-table-responsive">
            <table class="alm-bookings-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> Reference/User</th>
                        <th><i class="fas fa-hotel"></i> Room</th>
                        <th class="alm-hide-mobile"><i class="fas fa-sign-in-alt"></i> Check In</th>
                        <th class="alm-hide-mobile"><i class="fas fa-sign-out-alt"></i> Check Out</th>
                        <th class="alm-hide-mobile"><i class="fas fa-tags"></i> Price</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <!-- <?php if ($current_tab !== 'all'): ?> -->
                        <th class="alm-hide-mobile"><i class="fas fa-cog"></i> Action</th>
                        <!-- <?php endif; ?> -->
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookingsResult && $bookingsResult->num_rows > 0): ?>
                        <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                            <?php
                            $statusClass = 'alm-status-' . $booking['status'];
                            $statusIcon = match ($booking['status']) {
                                'pending' => '<i class="fas fa-clock"></i>',
                                'confirmed' => '<i class="fas fa-check"></i>',
                                'checked_in' => '<i class="fas fa-door-open"></i>',
                                'cancelled' => '<i class="fas fa-times-circle"></i>',
                                'no_show' => '<i class="fas fa-user-slash"></i>',
                                'completed' => '<i class="fas fa-check-double"></i>',
                                default => ''
                            };
                            ?>
                            <tr data-booking-id="<?php echo $booking['id']; ?>"
                                data-user-id="<?php echo htmlspecialchars($booking['user_id']); ?>"
                                data-reference-number="<?php echo htmlspecialchars($booking['reference_number']); ?>"
                                data-occupancy="<?php echo htmlspecialchars($booking['occupancy']); ?>"
                                data-price-per-day="<?php echo htmlspecialchars($booking['price_per_day']); ?>"
                                data-mattress-fee="<?php echo htmlspecialchars($booking['mattress_fee']); ?>"
                                data-total-price="<?php echo htmlspecialchars($booking['total_price']); ?>">
                                <td>
                                    <i class=" fas fa-bookmark"></i>
                                    <?php echo htmlspecialchars($booking['reference_number']); ?>
                                </td>
                                <td>
                                    <i class="fas fa-bed"></i> <?php echo getRoomDisplay(htmlspecialchars($booking['room_number'])); ?><br>
                                    <small><i class="fas fa-users"></i> Occupancy: <?php echo htmlspecialchars($booking['occupancy']); ?></small>
                                </td>
                                <td class="alm-hide-mobile">
                                    <i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking['arrival_date'])); ?><br>
                                    <small><i class="far fa-clock"></i> <?php echo htmlspecialchars($booking['arrival_time']); ?></small>
                                </td>
                                <td class="alm-hide-mobile">
                                    <i class="far fa-calendar-check"></i> <?php echo date('M d, Y', strtotime($booking['departure_date'])); ?><br>
                                    <small><i class="far fa-clock"></i> <?php echo htmlspecialchars($booking['departure_time']); ?></small>
                                </td>
                                <td class="alm-hide-mobile">
                                    <i class="fas fa-peso-sign"></i> <?php echo number_format($booking['price'], 2); ?>
                                </td>
                                <td>
                                    <span class="alm-status-badge <?php echo $statusClass; ?>">
                                        <?php echo $statusIcon . ' ' . ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <?php if ($current_tab !== 'all'): ?>
                                    <td class="alm-hide-mobile alm-actions-cell">
                                        <div class="alm-action-buttons">
                                            <?php if (!in_array($booking['status'], ['cancelled', 'completed'])): ?>
                                                <select class="alm-status-select" data-booking-id="<?php echo $booking['id']; ?>">
                                                    <option value="<?php echo $booking['status']; ?>" selected>
                                                        Change Status
                                                    </option>
                                                    <?php
                                                    $statuses = match ($booking['status']) {
                                                        'pending' => ['confirmed', 'cancelled'],
                                                        'confirmed' => ['checked_in', 'cancelled', 'no_show'],
                                                        'checked_in' => ['completed', 'extend_stay', 'early_checkout'],
                                                        'no_show' => ['cancelled', 'confirmed'],
                                                        default => []
                                                    };

                                                    foreach ($statuses as $newStatus):
                                                    ?>
                                                        <option value="<?php echo $newStatus; ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $newStatus)); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>

                                            <button class="alm-action-btn alm-delete-btn" onclick="archiveBooking('<?php echo $booking['id']; ?>', event)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($current_tab === 'all') ? '6' : '7'; ?>" class="alm-text-center">
                                <i class="fas fa-inbox fa-2x"></i><br>
                                No bookings found for this status
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="almBookingUserModal" class="alm-booking-modal">
        <div class="alm-modal-content">
            <div class="alm-modal-header">
                <h2><i class="fas fa-user-circle"></i> Booking Details</h2>
                <span class="alm-modal-close">&times;</span>
            </div>

            <div class="alm-modal-body">
                <div class="alm-user-details">
                    <div class="alm-detail-grid">
                        <div class="alm-detail-section">
                            <h3><i class="fas fa-bed"></i> Booking Details</h3>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-hashtag"></i> Reference Number:</label>
                                <span id="alm-modal-reference-number"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-money-bill-wave"></i> Price Per Day:</label>
                                <span id="alm-modal-price-per-day"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-users"></i> Occupancy:</label>
                                <span id="alm-modal-occupancy"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-money-bill-wave"></i> Mattress Fee:</label>
                                <span id="alm-modal-mattress-fee"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-receipt"></i> Total Price:</label>
                                <span id="alm-modal-total-price"></span>
                            </div>
                        </div>
                        <div class="alm-detail-section">
                            <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-user"></i> Username:</label>
                                <span id="alm-modal-username"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-envelope"></i> Email:</label>
                                <span id="alm-modal-email"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-id-card"></i> Full Name:</label>
                                <span id="alm-modal-fullname"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-briefcase"></i> Position:</label>
                                <span id="alm-modal-position"></span>
                            </div>
                        </div>

                        <div class="alm-detail-section">
                            <h3><i class="fas fa-address-book"></i> Contact Details</h3>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-map-marker-alt"></i> Address:</label>
                                <span id="alm-modal-address"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-phone"></i> Phone:</label>
                                <span id="alm-modal-phone"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-phone-alt"></i> Telephone:</label>
                                <span id="alm-modal-telephone"></span>
                            </div>
                        </div>

                        <div class="alm-detail-section">
                            <h3><i class="fas fa-user-check"></i> Status Information</h3>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-user-shield"></i> User Status:</label>
                                <span id="alm-modal-user-status"></span>
                            </div>
                            <div class="alm-detail-item">
                                <label><i class="fas fa-check-circle"></i> Verification:</label>
                                <span id="alm-modal-verified"></span>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="almStatusConfirmModal" class="AL-modal-overlay">
        <div class="AL-modal" role="dialog" aria-modal="true" aria-labelledby="AL-modal-title" aria-describedby="AL-modal-desc">
            <div class="AL-modal-header">
                <div class="AL-modal-icon warning">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h2 id="AL-modal-title" class="AL-modal-title">Confirm Status Change</h2>
            </div>

            <div id="AL-modal-desc" class="AL-modal-content text-center mb-4">
                Are you sure you want to change the status?
            </div>

            <div class="AL-modal-actions">
                <button id="almStatusCancelBtn" class="AL-modal-btn AL-modal-btn-secondary">
                    Cancel
                </button>
                <button id="almStatusConfirmBtn" class="AL-modal-btn AL-modal-btn-success">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <div id="almArchiveConfirmModal" class="AL-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="AL-archive-modal-title" aria-describedby="AL-archive-modal-desc">
        <div class="AL-modal" style="max-width: 400px;">
            <div class="AL-modal-header">
                <div class="AL-modal-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 id="AL-archive-modal-title" class="AL-modal-title">Confirm Delete</h2>
            </div>

            <div id="AL-archive-modal-desc" class="AL-modal-content text-center mb-4">
                Are you sure you want to delete this booking? It will be removed from the active listings.
            </div>

            <div class="AL-modal-actions">
                <button id="almArchiveCancelBtn" class="AL-modal-btn AL-modal-btn-secondary">
                    Cancel
                </button>
                <button id="almArchiveConfirmBtn" class="AL-modal-btn AL-modal-btn-danger">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <div id="extend-stay-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content book-card">
            <div class="book-header">
                <h1><i class="fas fa-calendar-plus"></i> Extend Stay</h1>
            </div>

            <div class="book-content">
                <div class="book-step-indicator">
                    <div class="book-step active">1</div>
                    <div class="book-step">2</div>
                </div>

                <!-- Step 1: Departure datetime -->
                <div id="extend-step1" class="book-step-content">
                    <div class="book-date-time-container">
                        <div>
                            <label>New Departure Date and Time</label>
                            <input type="text" class="book-date-time-input" id="extend-departure-datetime" placeholder="Select new departure date and time">
                        </div>
                    </div>
                </div>

                <!-- Step 2: Summary -->
                <div id="extend-step2" class="book-step-content" style="display: none;">
                    <div class="book-summary" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px;">
                        <h2>Summary</h2>
                        <div class="book-summary-item">
                            <span class="book-summary-label">Room:</span>
                            <span class="book-summary-value" id="extend-summary-room">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">Current Checkout:</span>
                            <span class="book-summary-value" id="extend-summary-old-checkout">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">New Checkout:</span>
                            <span class="book-summary-value" id="extend-summary-new-checkout">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">Extra Duration:</span>
                            <span class="book-summary-value" id="extend-summary-extra-duration">-</span>
                        </div>
                        <div class="book-summary-item">
                            <span class="book-summary-label">Additional Cost:</span>
                            <span class="book-summary-value" id="extend-summary-extra-cost" style="color: #2563eb;">-</span>
                        </div>
                    </div>
                </div>

                <div class="book-button-container">
                    <button id="extend-prev-button" class="book-nav-button book-prev-button" style="display: none;">Previous</button>
                    <button id="extend-next-button" class="book-nav-button book-next-button">Next</button>
                </div>
            </div>

            <button class="modal-close-button" onclick="closeExtendModal()">×</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize notification system
            // const NotificationSystem = {
            //     container: null,
            //     init: function() {
            //         this.container = document.getElementById('notificationContainer');
            //     },

            //     show: function(message, type = 'error', duration = 5000) {
            //         if (!this.container) return;

            //         const notification = document.createElement('div');
            //         notification.className = `notification ${type}`;

            //         const messageSpan = document.createElement('span');
            //         messageSpan.textContent = message;

            //         const closeButton = document.createElement('button');
            //         closeButton.className = 'notification-close';
            //         closeButton.innerHTML = '×';
            //         closeButton.onclick = () => this.remove(notification);

            //         notification.appendChild(messageSpan);
            //         notification.appendChild(closeButton);
            //         this.container.appendChild(notification);

            //         setTimeout(() => this.remove(notification), duration);
            //     },

            //     remove: function(notification) {
            //         notification.style.animation = 'slideOut 0.3s ease-out forwards';
            //         setTimeout(() => {
            //             if (notification.parentElement === this.container) {
            //                 this.container.removeChild(notification);
            //             }
            //         }, 300);
            //     }
            // };
            // NotificationSystem.init();

            // --- Utility Functions ---

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

            function showToast(message, isSuccess = true) {
                const toast = document.getElementById('view-booking-toast');
                const toastMessage = toast.querySelector('.view-booking-toast-message');
                toastMessage.textContent = message;

                toast.className = 'view-booking-toast';
                if (isSuccess) {
                    toast.classList.add('view-booking-toast-success');
                } else {
                    toast.classList.add('view-booking-toast-error');
                }

                toast.style.display = 'flex';

                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);

                const closeToast = toast.querySelector('.view-booking-toast-close');
                closeToast.onclick = () => {
                    toast.style.display = 'none';
                };
            }

            function showErrorMessage(modalBody, error) {
                const existingError = modalBody.querySelector('.alm-error-message');
                if (existingError) {
                    existingError.remove();
                }
                const errorMessage = document.createElement('div');
                errorMessage.className = 'alm-error-message';
                errorMessage.textContent = `Error: ${error.message}. Please try again.`;

                modalBody.insertBefore(errorMessage, modalBody.firstChild);
            }

            const updateField = (id, value, defaultValue = 'N/A') => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value || defaultValue;
                }
            };

            const getStatusIcon = (status) => {
                switch (status) {
                    case 'pending':
                        return '<i class="fas fa-clock"></i> ';
                    case 'confirmed':
                        return '<i class="fas fa-check"></i> ';
                    case 'checked_in':
                        return '<i class="fas fa-door-open"></i> ';
                    case 'checked_out':
                        return '<i class="fas fa-door-closed"></i> ';
                    case 'extend_stay':
                        return '<i class="fas fa-door-closed"></i> ';
                    case 'early_checkout':
                        return '<i class="fas fa-sign-out-alt"></i> ';
                    case 'cancelled':
                        return '<i class="fas fa-times-circle"></i> ';
                    case 'no_show':
                        return '<i class="fas fa-user-slash"></i> ';
                    case 'completed':
                        return '<i class="fas fa-check-double"></i> ';
                    default:
                        return '';
                }
            };

            function isDateBooked(date, bookings) {
                const dateStr = date.toISOString().split('T')[0];
                return bookings.some(booking => {
                    const arrivalDate = booking.arrival_date;
                    const departureDate = booking.departure_date;
                    return dateStr >= arrivalDate && dateStr <= departureDate;
                });
            }

            // --- Booking Calendar Initialization ---

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

                    const onDayCreateHandler = function(dObj, dStr, fp, dayElem) {
                        const dateStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                        const dateBooking = bookingTimes.find(b => b.date === dateStr);

                        if (dateBooking) {
                            dayElem.classList.add('checkout-date');

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
                    };

                    if (window.departureCalendar) {
                        window.departureCalendar.destroy();
                    }

                    window.departureCalendar = flatpickr("#extend-departure-datetime", {
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
                                    // Update both data structures
                                    bookingData.newCheckout = selectedDate;
                                    extendData.newDeparture = selectedDate;
                                    updateSummary();
                                }
                            }
                        },
                        onDayCreate: onDayCreateHandler
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


            // --- Main Booking Modal Logic ---

            const bookingModal = document.getElementById('almBookingUserModal');
            const statusConfirmModal = document.getElementById('almStatusConfirmModal');
            const deleteModal = document.getElementById('almDeleteConfirmModal');
            let bookingId, newStatus, originalValue, select;

            // Initialize booking state
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

            function updateSummary() {
                // Get the extend summary elements
                document.getElementById('extend-summary-room').textContent = bookingData.room || '-';
                document.getElementById('extend-summary-old-checkout').textContent = bookingData.currentCheckout ?
                    bookingData.currentCheckout.toLocaleString() : '-';
                document.getElementById('extend-summary-new-checkout').textContent = bookingData.newCheckout ?
                    bookingData.newCheckout.toLocaleString() : '-';

                // Calculate extra duration if both dates are available
                if (bookingData.currentCheckout && bookingData.newCheckout) {
                    const diff = bookingData.newCheckout - bookingData.currentCheckout;
                    const extraDays = Math.ceil(diff / (1000 * 60 * 60 * 24));
                    document.getElementById('extend-summary-extra-duration').textContent =
                        `${extraDays} day${extraDays !== 1 ? 's' : ''}`;

                    // Calculate extra cost if daily rate is available
                    if (bookingData.dailyRate) {
                        const extraCost = bookingData.dailyRate * extraDays;
                        document.getElementById('extend-summary-extra-cost').textContent =
                            `₱${extraCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${extraDays} day${extraDays !== 1 ? 's' : ''})`;
                    } else {
                        document.getElementById('extend-summary-extra-cost').textContent = '-';
                    }
                } else {
                    document.getElementById('extend-summary-extra-duration').textContent = '-';
                    document.getElementById('extend-summary-extra-cost').textContent = '-';
                }
            }

            // --- User and Booking Details Event Handlers ---

            document.querySelectorAll('.alm-bookings-table tbody tr').forEach(row => {
                row.addEventListener('click', async function(e) {
                    if (e.target.classList.contains('alm-status-select') ||
                        e.target.closest('.alm-status-select') ||
                        e.target.classList.contains('alm-delete-btn') ||
                        e.target.closest('.alm-delete-btn')) {
                        return;
                    }

                    const userId = this.getAttribute('data-user-id');
                    if (!userId) {
                        console.error('No user ID found for this booking');
                        return;
                    }

                    bookingModal.style.display = "block";

                    try {
                        const formData = new FormData();
                        formData.append('user_id', userId);

                        const response = await fetch('/Alumni-CvSU/admin/get_user_details.php', {
                            method: 'POST',
                            body: formData
                        });

                        if (!response.ok) {
                            throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                        }

                        const data = await response.json();

                        if (!data.success) {
                            throw new Error(data.message || 'Failed to load user details');
                        }

                        updateField('alm-modal-username', data.user.username);
                        updateField('alm-modal-email', data.user.email);

                        const referenceNumber = this.getAttribute('data-reference-number') || 'N/A';
                        const occupancy = this.getAttribute('data-occupancy') || '0';
                        const pricePerDay = this.getAttribute('data-price-per-day') || '0';
                        const mattressFee = this.getAttribute('data-mattress-fee') || '0';
                        const totalPrice = this.getAttribute('data-total-price') || '0';

                        const fullName = [
                            data.user_details.first_name,
                            data.user_details.middle_name,
                            data.user_details.last_name
                        ].filter(Boolean).join(' ');
                        updateField('alm-modal-reference-number', referenceNumber);
                        updateField('alm-modal-occupancy', occupancy);
                        updateField('alm-modal-price-per-day', pricePerDay);
                        updateField('alm-modal-mattress-fee', `₱${parseFloat(mattressFee).toFixed(2)}`);
                        updateField('alm-modal-total-price', `₱${parseFloat(totalPrice).toFixed(2)}`);
                        updateField('alm-modal-fullname', fullName);
                        updateField('alm-modal-position', data.user_details.position);
                        updateField('alm-modal-address', data.user_details.address);
                        updateField('alm-modal-phone', data.user_details.phone_number);
                        updateField('alm-modal-telephone', data.user_details.telephone);
                        updateField('alm-modal-user-status', data.user_details.user_status);
                        updateField('alm-modal-verified', data.user_details.verified ? 'Verified' : 'Not Verified');

                    } catch (error) {
                        console.error('Error fetching user details:', error);
                        showErrorMessage(document.querySelector('.alm-modal-body'), error);
                    }
                });
            });

            // --- Booking Status Management ---

            // --- Booking Status Management ---

            document.querySelectorAll('.alm-status-select').forEach(selectElement => {
                selectElement.addEventListener('change', function(e) {
                    e.stopPropagation();

                    if (this.selectedIndex === 0) {
                        return;
                    }

                    bookingId = this.getAttribute('data-booking-id');
                    newStatus = this.value;
                    originalValue = this.options[0].value; // Store the original status
                    select = this;

                    // Update confirmation message
                    const confirmMessage = document.getElementById('AL-modal-desc'); // updated ID in HTML
                    confirmMessage.innerHTML = `Are you sure you want to change the status to <strong>${newStatus.replace('_', ' ')}</strong>?`;

                    // Show modal by adding 'active' class
                    statusConfirmModal.classList.add('active');
                });
            });

            const statusConfirmBtn = document.getElementById('almStatusConfirmBtn');
            const statusCancelBtn = document.getElementById('almStatusCancelBtn');
            const statusCloseBtn = statusConfirmModal.querySelector('.AL-modal-close'); // updated class

            if (statusConfirmBtn) {
                statusConfirmBtn.onclick = async function() {
                    try {
                        // Hide modal by removing 'active' class
                        statusConfirmModal.classList.remove('active');

                        if (newStatus === 'extend_stay') {
                            showLoading('Checking room availability...');

                            const checkResponse = await fetch('/Alumni-CvSU/admin/extend-booking-stay.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    booking_id: bookingId
                                })
                            });

                            const checkData = await checkResponse.json();
                            hideLoading();

                            if (!checkData.available) {
                                showToast('Extension not possible: ' + (checkData.message || 'Room is not available the next day.'), false);
                                return;
                            }

                            window.initExtendStay(
                                bookingId,
                                checkData.roomId,
                                checkData.roomName || `Room ${checkData.roomId}`,
                                checkData.currentDepartureDate,
                                checkData.currentDepartureTime,
                                checkData.pricePerDay
                            );

                            await initializeCalendars(checkData.roomId);
                            return;
                        }

                        showLoading('Updating booking status...');

                        const formData = new FormData();
                        formData.append('booking_id', bookingId);
                        formData.append('status', newStatus);

                        const response = await fetch('/Alumni-CvSU/admin/update_booking_status.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        hideLoading();

                        if (data.success) {
                            showToast('Booking status updated successfully', true);
                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            throw new Error(data.message || 'Failed to update booking status');
                        }
                    } catch (error) {
                        console.error('Error updating booking status:', error);
                        hideLoading();
                        showToast('Failed to update booking status: ' + error.message, false);
                        if (select) select.selectedIndex = 0;
                    }
                };
            }

            if (statusCancelBtn) {
                statusCancelBtn.onclick = function() {
                    statusConfirmModal.classList.remove('active');
                    if (select) select.selectedIndex = 0;
                };
            }

            if (statusCloseBtn) {
                statusCloseBtn.onclick = function() {
                    statusConfirmModal.classList.remove('active');
                    if (select) select.selectedIndex = 0;
                };
            }

            const closeBookingBtn = bookingModal.querySelector('.alm-modal-close');
            if (closeBookingBtn) {
                closeBookingBtn.onclick = function() {
                    bookingModal.style.display = "none";
                }
            }

            // --- Other Booking Status Management Functions ---

            window.cancelBooking = function(bookingId, event) {
                event.stopPropagation();
                select = null;
                const confirmMessage = document.getElementById('almStatusConfirmMessage');
                confirmMessage.innerHTML = `Are you sure you want to cancel this booking?`;

                statusConfirmModal.style.display = "block";

                statusConfirmBtn.onclick = async function() {
                    try {
                        const formData = new FormData();
                        formData.append('booking_id', bookingId);
                        formData.append('status', 'cancelled');

                        statusConfirmModal.style.display = "none";
                        showLoading('Cancelling booking...');

                        const response = await fetch('/Alumni-CvSU/admin/update_booking_status.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (!data.success) {
                            throw new Error(data.message || 'Failed to cancel booking');
                        }

                        hideLoading();
                        showToast('Booking cancelled successfully', true);

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);

                    } catch (error) {
                        console.error('Error cancelling booking:', error);
                        hideLoading();
                        showToast('Failed to cancel booking: ' + error.message, false);
                    }
                };
            };

            window.earlyCheckout = function(bookingId, event) {
                event.stopPropagation();
                changeBookingStatus(bookingId, 'completed', 'Processing checkout as completed...', event);
            };

            window.completeBooking = function(bookingId, event) {
                event.stopPropagation();
                changeBookingStatus(bookingId, 'completed', 'Completing booking...', event);
            };

            window.markNoShow = function(bookingId, event) {
                event.stopPropagation();
                changeBookingStatus(bookingId, 'no_show', 'Marking as no-show...', event);
            };

            async function changeBookingStatus(bookingId, status, loadingMessage, event) {
                try {
                    showLoading(loadingMessage);

                    const formData = new FormData();
                    formData.append('booking_id', bookingId);
                    formData.append('status', status);

                    const response = await fetch('/Alumni-CvSU/admin/update_booking_status.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || `Failed to update booking to ${status}`);
                    }

                    hideLoading();
                    showToast(`Booking status updated to ${status.replace('_', ' ')}`, true);

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);

                } catch (error) {
                    console.error(`Error updating booking to ${status}:`, error);
                    hideLoading();
                    showToast(`Failed to update booking: ${error.message}`, false);
                }
            }

            // --- Extend Stay Functionality ---

            const extendModal = document.getElementById('extend-stay-modal');
            const prevExtendButton = document.getElementById('extend-prev-button');
            const nextExtendButton = document.getElementById('extend-next-button');

            // Initialize variables for the extend stay feature
            let currentExtendStep = 1;
            const totalExtendSteps = 2;
            let extendData = {
                bookingId: null,
                roomId: null,
                currentDeparture: null,
                newDeparture: null,
                pricePerDay: null
            };

            // Update which step is displayed
            function updateExtendStepDisplay() {
                document.querySelectorAll('#extend-stay-modal .book-step-content').forEach((step, index) => {
                    step.style.display = index + 1 === currentExtendStep ? 'block' : 'none';
                });

                document.querySelectorAll('#extend-stay-modal .book-step').forEach((step, index) => {
                    if (index + 1 === currentExtendStep) {
                        step.classList.add('active');
                        step.classList.remove('completed');
                    } else if (index + 1 < currentExtendStep) {
                        step.classList.add('completed');
                        step.classList.remove('active');
                    } else {
                        step.classList.remove('active', 'completed');
                    }
                });

                prevExtendButton.style.display = currentExtendStep === 1 ? 'none' : 'block';
                nextExtendButton.textContent = currentExtendStep === totalExtendSteps ? 'Confirm Extension' : 'Next';
            }

            function updateExtendSummary() {
                if (!extendData.currentDeparture || !extendData.newDeparture) return;

                const oldCheckout = new Date(extendData.currentDeparture);
                const newCheckout = new Date(extendData.newDeparture);

                document.getElementById('extend-summary-old-checkout').textContent = oldCheckout.toLocaleString();
                document.getElementById('extend-summary-new-checkout').textContent = newCheckout.toLocaleString();

                const diffMs = newCheckout - oldCheckout;
                if (diffMs <= 0) {
                    document.getElementById('extend-summary-extra-duration').textContent = 'Invalid time range';
                    document.getElementById('extend-summary-extra-cost').textContent = '₱0.00';
                    return;
                }

                // Calculate the time difference in hours
                const diffHours = Math.round(diffMs / (1000 * 60 * 60));

                // Check if the new checkout is on a different day
                const isNextDay = newCheckout.getDate() !== oldCheckout.getDate() ||
                    newCheckout.getMonth() !== oldCheckout.getMonth() ||
                    newCheckout.getFullYear() !== oldCheckout.getFullYear();

                let durationText = '';
                let cost = 0;

                if (isNextDay) {
                    durationText = `${diffHours} hours`; // Display the hours
                    // Calculate cost based on hours
                    if (extendData.pricePerDay) {
                        cost = (diffHours / 24) * extendData.pricePerDay;
                    } else {
                        document.getElementById('extend-summary-extra-cost').textContent = 'Price not available';
                        return;
                    }
                } else {
                    durationText = 'Less than a day';
                    cost = 0;
                }

                document.getElementById('extend-summary-extra-duration').textContent = durationText;
                document.getElementById('extend-summary-extra-cost').textContent = `₱${cost.toFixed(2)}`;
            }




            // Handle navigation between steps
            if (prevExtendButton) {
                prevExtendButton.addEventListener('click', function() {
                    if (currentExtendStep > 1) {
                        currentExtendStep--;
                        updateExtendStepDisplay();
                    }
                });
            }

            if (nextExtendButton) {
                nextExtendButton.addEventListener('click', function() {
                    if (currentExtendStep < totalExtendSteps) {
                        // Validate current step before proceeding
                        if (currentExtendStep === 1) {
                            const departureInput = document.getElementById('extend-departure-datetime');
                            if (!departureInput.value) {
                                NotificationSystem.show('Please select a new departure date and time', 'error');
                                return;
                            }
                            extendData.newDeparture = new Date(departureInput.value);
                            updateExtendSummary();
                        }

                        currentExtendStep++;
                        updateExtendStepDisplay();
                    } else {
                        // Final step - submit the extension request
                        confirmExtension();
                    }
                });
            }

            // Function to submit the extension request
            async function confirmExtension() {
                try {
                    showLoading('Processing extension request...');

                    // Format the new departure date and time
                    const newDepartureDate = extendData.newDeparture.toISOString().split('T')[0];
                    const newDepartureTime = extendData.newDeparture.toTimeString().split(' ')[0];

                    const formData = new FormData();
                    formData.append('booking_id', extendData.bookingId);
                    formData.append('new_departure_date', newDepartureDate);
                    formData.append('new_departure_time', newDepartureTime);

                    const response = await fetch('/Alumni-CvSU/admin/extend-booking-stay.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    hideLoading();

                    if (data.success) {
                        showToast('Stay extended successfully', true);
                        closeExtendModal();

                        // Reload the page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Failed to extend stay');
                    }
                } catch (error) {
                    console.error('Error extending stay:', error);
                    hideLoading();
                    showToast('Failed to extend stay: ' + error.message, false);
                }
            }

            // Initialize the extend stay functionality when modal is opened
            window.initExtendStay = function(bookingId, roomId, roomName, currentDepartureDate, currentDepartureTime, pricePerDay) {
                // Reset to step 1
                currentExtendStep = 1;
                updateExtendStepDisplay();

                // Set the extension data
                extendData.bookingId = bookingId;
                extendData.roomId = roomId;
                extendData.roomName = roomName;
                extendData.currentDepartureDate = currentDepartureDate;
                extendData.currentDepartureTime = currentDepartureTime;
                extendData.pricePerDay = pricePerDay;

                // Create a combined date string for display purposes
                const combinedDeparture = `${currentDepartureDate}T${currentDepartureTime}`;
                extendData.currentDeparture = combinedDeparture;

                // Set the corresponding values in bookingData for updateSummary()
                bookingData.room = roomName;
                bookingData.currentCheckout = new Date(combinedDeparture);
                bookingData.dailyRate = pricePerDay;

                // Clear any previous values
                const departureInput = document.getElementById('extend-departure-datetime');
                if (departureInput && window.departureCalendar) {
                    window.departureCalendar.clear();
                }

                // Show the modal
                extendModal.style.display = 'flex';
            };

            // Define the closeExtendModal function globally
            window.closeExtendModal = function() {
                if (extendModal) {
                    extendModal.style.display = 'none';
                }
            };

            // --- Global Modal Click Handlers ---

            window.addEventListener('click', function(event) {
                if (event.target === bookingModal) {
                    bookingModal.style.display = "none";
                }
                if (event.target === statusConfirmModal) {
                    statusConfirmModal.style.display = "none";
                    if (select) select.selectedIndex = 0;
                }
                if (event.target === deleteModal) {
                    deleteModal.style.display = "none";
                }
                if (event.target === extendModal) {
                    closeExtendModal();
                }
            });
        });


        // Your existing showToast function here
        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('view-booking-toast');
            const toastMessage = toast.querySelector('.view-booking-toast-message');
            toastMessage.textContent = message;

            toast.className = 'view-booking-toast';
            if (isSuccess) {
                toast.classList.add('view-booking-toast-success');
            } else {
                toast.classList.add('view-booking-toast-error');
            }

            toast.style.display = 'flex';

            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);

            const closeToast = toast.querySelector('.view-booking-toast-close');
            closeToast.onclick = () => {
                toast.style.display = 'none';
            };
        }

        // Archive modal code here (after showToast is defined)
        const archiveModal = document.getElementById('almArchiveConfirmModal');
        const archiveConfirmBtn = document.getElementById('almArchiveConfirmBtn');
        const archiveCancelBtn = document.getElementById('almArchiveCancelBtn');
        const archiveCloseBtn = archiveModal.querySelector('.AL-modal-close');

        let archiveBookingId = null;
        let archiveEvent = null;

        function archiveBooking(bookingId, event) {
            event.preventDefault();
            archiveBookingId = bookingId;
            archiveEvent = event;

            archiveModal.classList.add('active');
        }

        // Close modal on close button click if it exists
        if (archiveCloseBtn) {
            archiveCloseBtn.onclick = () => {
                archiveModal.classList.remove('active');
                archiveBookingId = null;
                archiveEvent = null;
            };
        }

        archiveConfirmBtn.onclick = async () => {
            if (!archiveBookingId || !archiveEvent) {
                archiveModal.classList.remove('active');
                return;
            }

            // Disable confirm button to prevent multiple clicks
            archiveConfirmBtn.disabled = true;

            archiveModal.classList.remove('active');

            try {
                const formData = new FormData();
                formData.append('booking_id', archiveBookingId);
                formData.append('action', 'archive');

                const response = await fetch('/Alumni-CvSU/admin/archive_booking.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    const row = document.querySelector(`[data-booking-id="${archiveBookingId}"]`);
                    if (row) {
                        // Add the slide-out class to trigger CSS animation
                        row.classList.add('slide-out');

                        // Remove the row after animation completes
                        row.addEventListener('animationend', () => {
                            row.remove();
                        }, {
                            once: true
                        });
                    }

                    // Update active tab counter
                    const activeTab = document.querySelector('.alm-booking-tab.active');
                    if (activeTab) {
                        const countSpan = activeTab.querySelector('.alm-booking-count');
                        if (countSpan) {
                            const currentCount = parseInt(countSpan.textContent, 10);
                            countSpan.textContent = currentCount > 0 ? currentCount - 1 : 0;
                        }
                    }

                    showToast('Booking deleted successfully', 'success');
                } else {
                    showToast('Error: ' + data.message, false);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while deleting the booking', false);
            }

            archiveBookingId = null;
            archiveEvent = null;
            archiveConfirmBtn.disabled = false;
        };

        archiveCancelBtn.onclick = () => {
            archiveModal.classList.remove('active');
            archiveBookingId = null;
            archiveEvent = null;
        };

        function closeExtendModal() {
            const modal = document.getElementById('extend-stay-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</body>

</html>