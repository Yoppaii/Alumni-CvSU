<?php
include 'main_db.php';
$today = date('Y-m-d');

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

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

$bookingsQuery = "SELECT * FROM bookings WHERE is_archived = 1";
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

$bookingsQuery .= " ORDER BY room_number, arrival_date ASC LIMIT $limit";
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
        :root {
            --primary-color: #10b981;
            --primary-dark: #059669;
            --secondary-color: #64748b;
            --secondary-hover: #4b5563;
            --border-color: #e2e8f0;
            --danger-color: #ef4444;
            --danger-hover: #dc2626;
            --success-color: #10b981;
            --success-hover: #059669;
            --warning-color: #f59e0b;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --white: #ffffff;
            --radius-sm: 4px;
            --radius-md: 6px;
            --radius-lg: 8px;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.2s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f0f0f0;
            color: #666;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .actions {
            display: flex;
            justify-content: flex-start;
            gap: 5px;
        }

        .alm-action-btn,
        .alm-danger-btn {
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            color: white;
            font-size: 0.875rem;
            transition: background-color 0.3s ease;
        }

        .alm-action-btn {
            background-color: var(--primary-color);
        }

        .alm-action-btn:hover {
            background-color: #2eb886;
        }

        .alm-danger-btn {
            background-color: #ef4444;
        }

        .alm-danger-btn:hover {
            background-color: #dc2626;
        }

        .tab-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .tab-button {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            cursor: pointer;
            text-decoration: none;
        }

        .tab-button.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .limit-select {
            margin-top: 20px;
            text-align: center;
        }

        .limit-select select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            cursor: pointer;
        }

        /* Modal Styles */
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

        /* Notification styles */
        #notificationContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 400px;
            width: 100%;
        }

        .notification {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            animation: slideIn 0.3s ease-out forwards;
            min-width: 300px;
            max-width: 400px;
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

        .notification.error {
            border-left: 4px solid #ef4444;
        }

        .notification.success {
            border-left: 4px solid #10b981;
        }

        .notification.warning {
            border-left: 4px solid #f59e0b;
        }

        .notification.info {
            border-left: 4px solid #3b82f6;
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
            color: #64748b;
        }

        .notification-close:hover {
            color: #1e293b;
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
                height: auto;
                padding-top: 12px;
                /* match your row padding */
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

    <div id="notificationContainer" style="position: fixed; top: 10px; right: 10px; z-index: 9999;"></div>

    <div class="alm-bookings-container">
        <div class="alm-header-content">
            <h2><i class="fas fa-calendar-check"></i> Archive Bookings Records</h2>
            <form method="get" class="alm-filter-form">
                <input type="hidden" name="section" value="archived-bookings">
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
                $countQuery = "SELECT COUNT(*) as count FROM bookings WHERE is_archived = 1";
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
                <a href="?section=archived-bookings&tab=<?php echo $tab_id; ?>" class="alm-booking-tab <?php echo ($current_tab === $tab_id) ? 'active' : ''; ?>">
                    <i class="<?php echo match ($tab_id) {
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
                        <th class="alm-hide-mobile"><i class="fas fa-cog"></i> Action</th>
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
                                data-user-id=" <?php echo htmlspecialchars($booking['user_id']); ?>"
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
                                <td class="alm-hide-mobile alm-actions-cell">
                                    <div class="alm-action-buttons">
                                        <button class="alm-action-btn" onclick="bookingAction('<?php echo $booking['id']; ?>', '<?php echo htmlspecialchars($booking['reference_number']); ?>')">
                                            Actions
                                        </button>
                                    </div>
                                </td>
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

    <!-- Booking Action Modal HTML Structure -->
    <div class="AL-modal-overlay" id="bookingActionModal">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon" id="bookingModalIcon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AL-modal-title" id="bookingModalTitle">Booking Action</h3>
            </div>
            <div class="AL-modal-content" id="bookingModalContent">
                Are you sure you want to perform this action?
            </div>
            <div class="AL-modal-actions">
                <button class="AL-modal-btn AL-modal-btn-secondary" data-action="cancel">Cancel</button>
                <button class="AL-modal-btn AL-modal-btn-success" id="bookingRestoreBtn" data-action="restore">Restore</button>
                <button class="AL-modal-btn AL-modal-btn-danger" id="bookingDeleteBtn" data-action="delete">Delete Permanently</button>
            </div>
        </div>
    </div>



    <script>
        // Add permanent delete confirmation modal HTML to the document body
        document.body.insertAdjacentHTML('beforeend', `
    <div id="almPermanentDeleteModal" class="AL-modal-overlay">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="AL-modal-title">Permanent Delete</h2>
            </div>
            <div class="AL-modal-content">
                <p style="color: red; font-weight: bold;">
                    Are you absolutely sure you want to permanently delete this booking? 
                    This action CANNOT be undone.
                </p>
                <p style="margin-top: 10px;">
                    Please type "DELETE" to confirm:
                </p>
                <input type="text" id="deleteConfirmInput" 
                       style="width: 100%; margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <div class="AL-modal-buttons" style="margin-top: 15px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button id="almPermanentDeleteCancelBtn" class="AL-modal-btn AL-modal-btn-secondary">
                        Cancel
                    </button>
                    <button id="almPermanentDeleteConfirmBtn" class="AL-modal-btn AL-modal-btn-danger" disabled>
                        Permanently Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
`);

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


        function showBookingActionModal(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('bookingActionModal');
                const modalIcon = document.getElementById('bookingModalIcon');
                const modalTitle = document.getElementById('bookingModalTitle');
                const modalContent = document.getElementById('bookingModalContent');
                const restoreBtn = document.getElementById('bookingRestoreBtn');
                const deleteBtn = document.getElementById('bookingDeleteBtn');
                const cancelBtn = modal.querySelector('[data-action="cancel"]');

                // Reset buttons visibility
                restoreBtn.style.display = '';
                deleteBtn.style.display = '';

                if (options.action === 'restore') {
                    modalIcon.className = 'AL-modal-icon success';
                    modalIcon.innerHTML = '<i class="fas fa-undo"></i>';
                    modalTitle.textContent = 'Restore Booking';
                    modalContent.innerHTML = `
                <p>Are you sure you want to restore booking <strong>${options.referenceNumber}</strong>?</p>
                <p>Restored bookings will appear in the main booking list.</p>
                <br>
                <p><strong>Warning:</strong> You can also permanently delete this booking. This action cannot be undone.</p>
            `;
                    // For restore action, hide delete button if you want
                    // deleteBtn.style.display = 'none'; // Optional
                } else if (options.action === 'delete') {
                    modalIcon.className = 'AL-modal-icon danger';
                    modalIcon.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    modalTitle.textContent = 'Delete Booking Permanently';
                    modalContent.innerHTML = `
                <p><strong>Warning:</strong> You are about to permanently delete booking <strong>${options.referenceNumber}</strong>.</p>
                <p>This action cannot be undone and all booking data will be completely removed from the system.</p>
                <p>Are you absolutely sure you want to proceed?</p>
            `;
                    // For delete action, hide restore button
                    restoreBtn.style.display = 'none';
                }

                // Show the modal
                modal.classList.add('active');

                // Handle button clicks
                const cleanup = () => {
                    modal.classList.remove('active');
                    restoreBtn.style.display = ''; // Reset display
                    deleteBtn.style.display = '';
                    restoreBtn.onclick = null;
                    deleteBtn.onclick = null;
                    cancelBtn.onclick = null;
                    modal.onclick = null;
                };

                restoreBtn.onclick = () => {
                    cleanup();
                    resolve('restore');
                };

                deleteBtn.onclick = () => {
                    //call deleteUserPermanently function with modal confirmation
                    cleanup();
                    resolve('delete');


                };

                cancelBtn.onclick = () => {
                    cleanup();
                    resolve('cancel');
                };

                // Close on clicking overlay
                modal.onclick = (e) => {
                    if (e.target === modal) {
                        cleanup();
                        resolve('cancel');
                    }
                };
            });
        }

        async function bookingAction(bookingId, referenceNumber, initialAction = 'restore') {
            const action = await showBookingActionModal({
                action: initialAction,
                referenceNumber: referenceNumber,
                bookingId: bookingId
            });

            if (action === 'restore') {
                restoreBooking(bookingId);
            } else if (action === 'delete') {
                // Now calls our new deleteBookingPermanently function with modal confirmation
                deleteBookingPermanently(bookingId);
            }
        }

        function restoreBooking(bookingId) {
            // Implement your restore booking logic here
            fetch('/Alumni-CvSU/admin/archives/restore_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `booking_id=${bookingId}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove the booking row from the table
                        const bookingRow = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
                        if (bookingRow) {
                            bookingRow.style.animation = 'slideOut 0.3s ease-out forwards';
                            setTimeout(() => {
                                bookingRow.remove();
                            }, 300);
                        }

                        showNotification('Booking has been restored successfully.', 'success');
                    } else {
                        showNotification(`Failed to restore booking: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while restoring the booking: ' + error.message, 'error');
                });
        }

        // Modify the deleteBookingPermanently function to use the new modal
        function deleteBookingPermanently(bookingId) {
            const modal = document.getElementById('almPermanentDeleteModal');
            const confirmInput = document.getElementById('deleteConfirmInput');
            const confirmBtn = document.getElementById('almPermanentDeleteConfirmBtn');
            const cancelBtn = document.getElementById('almPermanentDeleteCancelBtn');

            // Reset the input field and button state
            confirmInput.value = '';
            confirmBtn.disabled = true;

            // Show the modal
            modal.classList.add('active');

            // Handle the input field to enable/disable the confirm button
            confirmInput.addEventListener('input', function() {
                confirmBtn.disabled = this.value !== 'DELETE';
            });

            // Handle button clicks
            confirmBtn.onclick = function() {
                if (confirmInput.value === 'DELETE') {
                    modal.classList.remove('active');
                    performDeleteBooking(bookingId);
                }
            };

            cancelBtn.onclick = function() {
                modal.classList.remove('active');
            };

            // Close on clicking overlay
            modal.onclick = function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            };
        }
        // Function that performs the actual delete operation
        function performDeleteBooking(bookingId) {
            // Implement your permanent delete booking logic here
            fetch('/Alumni-CvSU/admin/archives/delete_booking_permanent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `booking_id=${bookingId}` // Changed from bookingId to booking_id
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const bookingRow = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
                        if (bookingRow) {
                            bookingRow.style.animation = 'slideOut 0.3s ease-out forwards';
                            setTimeout(() => {
                                bookingRow.remove();
                            }, 300);
                        }
                        showNotification('Booking has been permanently deleted.', 'success');
                    } else {
                        showNotification(`Failed to delete booking: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting the booking: ' + error.message, 'error');
                });
        }

        const bookingSearch = document.getElementById('bookingSearch');
        if (bookingSearch) {
            bookingSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.booking-table tbody tr'); // Adjust the selector

                rows.forEach(row => {
                    // Adjust the indices below to match the columns you want to search
                    const referenceNumber = row.cells[1].textContent.toLowerCase(); // Assuming reference number is in the second column
                    const customerName = row.cells[2].textContent.toLowerCase(); // Assuming customer name is in the third column
                    const shouldShow = referenceNumber.includes(searchTerm) || customerName.includes(searchTerm);

                    row.style.display = shouldShow ? '' : 'none';
                });
            });
        }
    </script>



</body>

</html>