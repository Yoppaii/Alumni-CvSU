<?php
include 'main_db.php';
$today = date('Y-m-d');

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

$tabs = [
    'all' => 'All Bookings',
    'pending' => 'Pending',
    'confirmed' => 'Confirmed',
    'checked_in' => 'Checked In',
    'cancelled' => 'Cancelled',
    'no_show' => 'No Show',
    'completed' => 'Completed',
];

// $baseQuery = "SELECT b.*, u.id as user_id FROM bookings b LEFT JOIN users u ON b.user_id = u.id";
// if ($current_tab !== 'all') {
//     $baseQuery .= " WHERE b.status = '$current_tab'";
// }
// $baseQuery .= " ORDER BY b.created_at DESC LIMIT 20";
// $bookingsResult = $mysqli->query($baseQuery);


$booking_type = isset($_GET['booking_type']) ? $_GET['booking_type'] : 'all';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
$limit = in_array($limit, [5, 25, 50, 100, 500]) ? $limit : 25;

$bookingsQuery = "SELECT * FROM bookings";
$conditions = [];

if ($current_tab !== 'all') {
    $conditions[] = "status = '" . $mysqli->real_escape_string($current_tab) . "'";
}

if ($booking_type !== 'all') {
    $conditions[] = "is_walkin = '" . $mysqli->real_escape_string($booking_type) . "'";
}

if (!empty($conditions)) {
    $bookingsQuery .= " WHERE " . implode(" AND ", $conditions);
}

$bookingsQuery .= " ORDER BY created_at DESC LIMIT $limit";
$bookingsResult = $mysqli->query($bookingsQuery);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="admin/view_all_bookings.css">
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
                $countQuery = "SELECT COUNT(*) as count FROM bookings";
                $countConditions = [];

                if ($tab_id !== 'all') {
                    $countConditions[] = "status = '" . $mysqli->real_escape_string($tab_id) . "'";
                }

                if ($booking_type !== 'all') {
                    $countConditions[] = "is_walkin = '" . $mysqli->real_escape_string($booking_type) . "'";
                }

                if (!empty($countConditions)) {
                    $countQuery .= " WHERE " . implode(" AND ", $countConditions);
                }

                $countResult = $mysqli->query($countQuery);
                $count = $countResult->fetch_assoc()['count'];
                ?>
                <a href="?section=view-all-bookings&tab=<?php echo $tab_id; ?>" class="alm-booking-tab <?php echo ($current_tab === $tab_id) ? 'active' : ''; ?>">
                    <i class="<?php echo match ($tab_id) {
                                    'all' => 'fas fa-list',
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
                        <?php if ($current_tab !== 'all'): ?>
                            <th class="alm-hide-mobile"><i class="fas fa-cog"></i> Action</th>
                        <?php endif; ?>
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
                            <tr data-user-id="<?php echo htmlspecialchars($booking['user_id']); ?>">
                                <td>
                                    <i class="fas fa-bookmark"></i>
                                    <?php echo htmlspecialchars($booking['reference_number']); ?>
                                </td>
                                <td>
                                    <i class="fas fa-bed"></i> Room <?php echo htmlspecialchars($booking['room_number']); ?><br>
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
                                                        'checked_in' => ['completed', 'early_checkout'],
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

                                            <button class="alm-delete-btn" onclick="deleteBooking('<?php echo $booking['id']; ?>', event)">
                                                <i class="fas fa-trash-alt"></i> Delete
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
                                <label><i class="fas fa-users"></i> Accompanying Persons:</label>
                                <span id="alm-modal-accompanying"></span>
                            </div>
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

    <div id="almStatusConfirmModal" class="alm-booking-modal">
        <div class="alm-modal-content" style="max-width: 400px;">
            <div class="alm-modal-header">
                <h2><i class="fas fa-question-circle"></i> Confirm Status Change</h2>
                <span class="alm-modal-close">&times;</span>
            </div>

            <div class="alm-modal-body">
                <p id="almStatusConfirmMessage" class="text-center mb-4">
                    Are you sure you want to change the status?
                </p>
                <div class="alm-button-group">
                    <button id="almStatusConfirmBtn" class="alm-btn alm-btn-primary">
                        <i class="fas fa-check"></i> Confirm
                    </button>
                    <button id="almStatusCancelBtn" class="alm-btn alm-btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="almDeleteConfirmModal" class="alm-booking-modal">
        <div class="alm-modal-content" style="max-width: 400px;">
            <div class="alm-modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
                <span class="alm-modal-close">&times;</span>
            </div>

            <div class="alm-modal-body">
                <p id="almDeleteConfirmMessage" class="text-center mb-4">
                    Are you sure you want to delete this booking? This action cannot be undone.
                </p>
                <div class="alm-button-group">
                    <button id="almDeleteConfirmBtn" class="alm-btn alm-btn-primary" style="background-color: #ef4444;">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                    <button id="almDeleteCancelBtn" class="alm-btn alm-btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookingModal = document.getElementById('almBookingUserModal');
            const statusConfirmModal = document.getElementById('almStatusConfirmModal');
            const deleteModal = document.getElementById('almDeleteConfirmModal');
            let bookingId, newStatus, originalValue, select;

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

            const showToast = (message, isSuccess = true) => {
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
            };

            const showErrorMessage = (modalBody, error) => {
                const existingError = modalBody.querySelector('.alm-error-message');
                if (existingError) {
                    existingError.remove();
                }
                const errorMessage = document.createElement('div');
                errorMessage.className = 'alm-error-message';
                errorMessage.textContent = `Error: ${error.message}. Please try again.`;

                modalBody.insertBefore(errorMessage, modalBody.firstChild);
            };

            window.deleteBooking = function(bookingId, event) {
                event.stopPropagation();

                const confirmBtn = document.getElementById('almDeleteConfirmBtn');
                const cancelBtn = document.getElementById('almDeleteCancelBtn');
                const closeBtn = deleteModal.querySelector('.alm-modal-close');

                deleteModal.style.display = "block";

                const handleDelete = async () => {
                    try {
                        deleteModal.style.display = "none";
                        showLoading('Deleting booking...');

                        const formData = new FormData();
                        formData.append('booking_id', bookingId);

                        console.log('Sending delete request for booking ID:', bookingId);

                        const response = await fetch('/Alumni-CvSU/admin/delete_booking.php', {
                            method: 'POST',
                            body: formData
                        });

                        console.log('Response status:', response.status);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();
                        console.log('Response data:', data);

                        if (data.success) {
                            const row = event.target.closest('tr');
                            if (row) {
                                row.remove();
                            }
                            hideLoading();
                            showToast('Booking successfully deleted', true);

                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Failed to delete booking');
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        hideLoading();
                        showToast('Failed to delete booking: ' + error.message, false);
                    }
                };

                const handleCancel = () => {
                    deleteModal.style.display = "none";
                };

                confirmBtn.onclick = handleDelete;
                cancelBtn.onclick = handleCancel;
                closeBtn.onclick = handleCancel;

                window.onclick = function(e) {
                    if (e.target == deleteModal) {
                        handleCancel();
                    }
                };
            };

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

                        const fullName = [
                            data.user_details.first_name,
                            data.user_details.middle_name,
                            data.user_details.last_name
                        ].filter(Boolean).join(' ');

                        updateField('alm-modal-fullname', fullName);
                        updateField('alm-modal-position', data.user_details.position);
                        updateField('alm-modal-address', data.user_details.address);
                        updateField('alm-modal-phone', data.user_details.phone_number);
                        updateField('alm-modal-telephone', data.user_details.telephone);
                        updateField('alm-modal-accompanying', data.user_details.accompanying_persons);
                        updateField('alm-modal-user-status', data.user_details.user_status);
                        updateField('alm-modal-verified', data.user_details.verified ? 'Verified' : 'Not Verified');

                    } catch (error) {
                        console.error('Error fetching user details:', error);
                        showErrorMessage(document.querySelector('.alm-modal-body'), error);
                    }
                });
            });

            document.querySelectorAll('.alm-status-select').forEach(selectElement => {
                selectElement.addEventListener('change', function(e) {
                    e.stopPropagation();

                    // If default option is selected, do nothing
                    if (this.selectedIndex === 0) {
                        return;
                    }

                    bookingId = this.getAttribute('data-booking-id');
                    newStatus = this.value;
                    originalValue = this.options[0].value; // Store the original status
                    select = this;

                    // Update confirmation message
                    const confirmMessage = document.getElementById('almStatusConfirmMessage');
                    confirmMessage.innerHTML = `Are you sure you want to change the status to <strong>${newStatus.replace('_', ' ')}</strong>?`;

                    statusConfirmModal.style.display = "block";
                });
            });

            const statusConfirmBtn = document.getElementById('almStatusConfirmBtn');
            const statusCancelBtn = document.getElementById('almStatusCancelBtn');
            const statusCloseBtn = statusConfirmModal.querySelector('.alm-modal-close');

            if (statusConfirmBtn) {
                statusConfirmBtn.onclick = async function() {
                    try {
                        const formData = new FormData();
                        formData.append('booking_id', bookingId);
                        formData.append('status', newStatus);

                        statusConfirmModal.style.display = "none";
                        showLoading('Updating booking status...');

                        const response = await fetch('/Alumni-CvSU/admin/update_booking_status.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (!data.success) {
                            throw new Error(data.message || 'Failed to update status');
                        }

                        const statusBadge = select.closest('tr').querySelector('.alm-status-badge');
                        if (statusBadge) {
                            const statusIcon = getStatusIcon(newStatus);
                            statusBadge.className = `alm-status-badge alm-status-${newStatus}`;
                            statusBadge.innerHTML = `${statusIcon}${newStatus.charAt(0).toUpperCase() + newStatus.slice(1).replace('_', ' ')}`;
                        }

                        hideLoading();
                        showToast(`Booking status updated to ${newStatus.replace('_', ' ')}`, true);

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);

                    } catch (error) {
                        console.error('Error updating booking status:', error);
                        hideLoading();
                        showToast('Failed to update booking status: ' + error.message, false);
                        select.selectedIndex = 0;
                    }
                };
            }

            if (statusCancelBtn) {
                statusCancelBtn.onclick = function() {
                    statusConfirmModal.style.display = "none";
                    // Reset select to first option
                    select.selectedIndex = 0;
                };
            }

            if (statusCloseBtn) {
                statusCloseBtn.onclick = function() {
                    statusConfirmModal.style.display = "none";
                    // Reset select to first option
                    select.selectedIndex = 0;
                };
            }

            const closeBookingBtn = bookingModal.querySelector('.alm-modal-close');
            if (closeBookingBtn) {
                closeBookingBtn.onclick = function() {
                    bookingModal.style.display = "none";
                }
            }

            window.onclick = function(event) {
                if (event.target == bookingModal) {
                    bookingModal.style.display = "none";
                }
                if (event.target == statusConfirmModal) {
                    statusConfirmModal.style.display = "none";
                    if (select) select.selectedIndex = 0;
                }
                if (event.target == deleteModal) {
                    deleteModal.style.display = "none";
                }
            };

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

            // window.rebook = function(bookingId, event) {
            //     event.stopPropagation();
            //     changeBookingStatus(bookingId, 'confirmed', 'Rebooking guest...', event);
            // };

            function changeBookingStatus(bookingId, newStatus, loadingMessage, event) {
                select = null;
                const confirmMessage = document.getElementById('almStatusConfirmMessage');

                if (newStatus === 'rebook') {
                    confirmMessage.innerHTML = `Are you sure you want to rebook this cancelled reservation?`;
                } else {
                    confirmMessage.innerHTML = `Are you sure you want to change this booking status to <strong>${newStatus.replace('_', ' ')}</strong>?`;
                }

                statusConfirmModal.style.display = "block";

                statusConfirmBtn.onclick = async function() {
                    try {
                        const formData = new FormData();
                        formData.append('booking_id', bookingId);
                        formData.append('status', newStatus);

                        statusConfirmModal.style.display = "none";
                        showLoading(loadingMessage || 'Processing your request...');

                        let endpoint = '/Alumni-CvSU/admin/update_booking_status.php';

                        if (newStatus === 'rebook') {
                            endpoint = '/Alumni-CvSU/admin/rebook_cancelled_booking.php';
                        }

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (!data.success) {
                            throw new Error(data.message || `Failed to ${newStatus === 'rebook' ? 'rebook' : 'update status to ' + newStatus}`);
                        }

                        hideLoading();

                        if (newStatus === 'rebook') {
                            showToast(`Booking successfully rebooked with reference number: ${data.reference_number}`, true);
                        } else {
                            const row = event.target.closest('tr');
                            if (row) {
                                const statusBadge = row.querySelector('.alm-status-badge');
                                if (statusBadge) {
                                    const statusIcon = getStatusIcon(newStatus);
                                    statusBadge.className = `alm-status-badge alm-status-${newStatus}`;
                                    statusBadge.innerHTML = `${statusIcon}${newStatus.charAt(0).toUpperCase() + newStatus.slice(1).replace('_', ' ')}`;
                                }
                            }
                            showToast(`Booking status updated to ${newStatus.replace('_', ' ')}`, true);
                        }

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);

                    } catch (error) {
                        console.error(`Error processing booking action:`, error);
                        hideLoading();
                        showToast(`Operation failed: ${error.message}`, false);
                    }
                };

                statusCancelBtn.onclick = function() {
                    statusConfirmModal.style.display = "none";
                };

                statusCloseBtn.onclick = function() {
                    statusConfirmModal.style.display = "none";
                };
            }

            window.rebookCancelled = function(bookingId, event) {
                event.stopPropagation();
                select = null;
                const confirmMessage = document.getElementById('almStatusConfirmMessage');
                confirmMessage.innerHTML = `Are you sure you want to rebook this cancelled reservation?`;

                statusConfirmModal.style.display = "block";

                statusConfirmBtn.onclick = async function() {
                    try {
                        const formData = new FormData();
                        formData.append('booking_id', bookingId);

                        statusConfirmModal.style.display = "none";
                        showLoading('Processing rebooking...');

                        const response = await fetch('/Alumni-CvSU/admin/rebook_cancelled_booking.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (!data.success) {
                            throw new Error(data.message || 'Failed to rebook the cancelled booking');
                        }

                        hideLoading();
                        showToast(`Booking successfully rebooked with reference number: ${data.reference_number}`, true);

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);

                    } catch (error) {
                        console.error('Error rebooking cancelled booking:', error);
                        hideLoading();
                        showToast('Failed to rebook: ' + error.message, false);
                    }
                };

                statusCancelBtn.onclick = function() {
                    statusConfirmModal.style.display = "none";
                };

                statusCloseBtn.onclick = function() {
                    statusConfirmModal.style.display = "none";
                };
            };

            window.markNoShow = function(bookingId, event) {
                event.stopPropagation();
                changeBookingStatus(bookingId, 'no_show', 'Marking as no-show...', event);
            };
        });
    </script>

</body>

</html>