<?php
include 'main_db.php';
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/home.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h5>Analytics</h5>
        <select class="form-select shadow-none bg-light w-auto" onchange="updateAnalytics(this.value)">
            <option value=" 1">Today</option>
            <option value="2">Past 7 Days</option>
            <option value="3">Past 30 Days</option>
            <option value="4">Past 90 Days</option>
            <option value="5">Past 1 Year</option>
            <option value="6">All time</option>
        </select>
    </div>
    <div class="dashboard-stats">
        <!-- Users Card -->
        <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </div>
            <div class="stat-content">
                <h3 id="total_users"></h3>
                <p id="user_footer"></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-users"></i>
                <span>Bookings</span>
            </div>
            <div class="stat-content">
                <h3 id="total_bookings"></h3>
                <p id="bookings_footer"></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-users"></i>
                <span>Alumni ID Cards</span>
            </div>
            <div class="stat-content">
                <h3 id="total_alumni_id_cards"></h3>
                <p id="alumni_id_cards_footer"></p>
            </div>
        </div>

        <!-- <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </div>
            <?php
            $query = "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = '$today'";
            $result = $mysqli->query($query);
            $newUsers = $result->fetch_assoc()['count'];

            $totalQuery = "SELECT COUNT(*) as count FROM users";
            $totalResult = $mysqli->query($totalQuery);
            $totalUsers = $totalResult->fetch_assoc()['count'];
            ?>
            <div class="stat-content">
                <h3><?php echo $newUsers; ?></h3>
                <p>New Today</p>
            </div>
            <div class="stat-footer">
                Total: <?php echo $totalUsers; ?>
            </div>
        </div> -->

        <!-- Bookings Card -->
        <!-- <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-calendar-check"></i>
                <span>Bookings</span>
            </div>
            <?php
            $query = "SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = '$today'";
            $result = $mysqli->query($query);
            $newBookings = $result->fetch_assoc()['count'];

            $totalQuery = "SELECT COUNT(*) as count FROM bookings";
            $totalResult = $mysqli->query($totalQuery);
            $totalBookings = $totalResult->fetch_assoc()['count'];
            ?>
            <div class="stat-content">
                <h3><?php echo $newBookings; ?></h3>
                <p>New Today</p>
            </div>
        </div> -->

        <!-- ID Cards -->
        <!-- <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-id-card"></i>
                <span>Alumni ID Cards</span>
            </div>
            <?php
            $query = "SELECT COUNT(*) as count FROM alumni_id_cards WHERE DATE(created_at) = '$today'";
            $result = $mysqli->query($query);
            $newCards = $result->fetch_assoc()['count'];

            $totalQuery = "SELECT COUNT(*) as count FROM alumni_id_cards";
            $totalResult = $mysqli->query($totalQuery);
            $totalCards = $totalResult->fetch_assoc()['count'];
            ?>
            <div class="stat-content">
                <h3><?php echo $newCards; ?></h3>
                <p>New Today</p>
            </div>
        </div> -->
    </div>

    <!-- Analytics Dashboard Section -->
    <div class="analytics-dashboard">
        <div class="analytics-summary">
            <div class="summary-card">
                <i class="fas fa-chart-line"></i>
                <div class="summary-content">
                    <h3>Monthly Revenue</h3>
                    <?php
                    $query = "SELECT ROUND(AVG(price), 2) AS monthly_revenue FROM bookings WHERE status = 'Completed'";
                    $result = $mysqli->query($query);
                    $monthly_revenue = $result->fetch_assoc()['monthly_revenue'] ?? 0;
                    ?>
                    <p>₱<?php echo number_format($monthly_revenue, 2); ?>
                </div>
            </div>

            <div class="summary-card">
                <i class="fas fa-calendar-check"></i>
                <div class="summary-content">
                    <h3>Completion Rate</h3>
                    <?php
                    $query = "SELECT 
                    COUNT(*) AS total_bookings, 
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_bookings,
                    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_bookings
                    FROM bookings";

                    $result = $mysqli->query($query);
                    $data = $result->fetch_assoc();

                    $total = $data['total_bookings'] ?? 0;
                    $completed = $data['completed_bookings'] ?? 0;
                    $cancelled = $data['cancelled_bookings'] ?? 0;

                    // Calculate completion rate (avoid division by zero)
                    $completion_rate = ($total > 0) ? ($completed / $total) * 100 : 0;
                    ?>
                    <p><?php echo number_format($completion_rate, 2); ?>%
                </div>
            </div>

            <div class="summary-card">
                <i class="fas fa-clock"></i>
                <div class="summary-content">
                    <h3>Avg Stay Duration</h3>
                    <?php
                    $query = "SELECT AVG(TIMESTAMPDIFF(HOUR, CONCAT(arrival_date, ' ', arrival_time), CONCAT(departure_date, ' ', departure_time))) AS avg_stay_hours
                    FROM bookings WHERE status = 'Completed'";

                    $result = $mysqli->query($query);
                    $data = $result->fetch_assoc();

                    $avg_stay_hours = $data['avg_stay_hours'] ?? 0; // Default to 0 if null
                    $avg_stay_days = $avg_stay_hours / 24; // Convert hours to days
                    ?>
                    <p><?php echo number_format($avg_stay_days, 2); ?> days (<?php echo number_format($avg_stay_hours, 2); ?> hours)</p>

                </div>
            </div>
            <div class="summary-card">
                <i class="fas fa-money-bill-wave"></i>
                <div class="summary-content">
                    <h3>Avg Price per Stay</h3>
                    <?php
                    $query = "SELECT AVG(price / NULLIF(TIMESTAMPDIFF(DAY, arrival_date, departure_date), 0)) AS avg_price_per_day
                    FROM bookings WHERE status = 'Completed'";

                    $result = $mysqli->query($query);
                    $data = $result->fetch_assoc();

                    $avg_price_per_day = $data['avg_price_per_day'] ?? 0; // Default to 0 if null
                    ?>
                    <p>₱<?php echo number_format($avg_price_per_day, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="dashboard-row">
            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Booking Trends & Revenue</h2>
                </div>
                <div class="analytics-content">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>
        </div>

        <div class="dashboard-row">
            <!-- Room Occupancy Chart -->
            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Room Occupancy Distribution</h2>
                </div>
                <div class="analytics-content">
                    <canvas id="roomOccupancyChart"></canvas>
                </div>
            </div>

            <!-- Booking Status Chart -->
            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Booking Status Distribution</h2>
                </div>
                <div class="analytics-content">
                    <canvas id="bookingStatusChart"></canvas>
                </div>
            </div>
        </div>


    </div>

    <div class="bookings-table-container">
        <div class="header-content">
            <h2>Recent Bookings</h2>
            <a href="?section=view-all-bookings" class="view-all-link">
                <i class="fas fa-external-link-alt"></i>
                View All
            </a>
        </div>
        <div class="table-responsive">
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Reference/User</th>
                        <th>Room</th>
                        <th class="hide-mobile">Check In</th>
                        <th class="hide-mobile">Check Out</th>
                        <th class="hide-mobile">Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Updated query to include user_id
                    $query = "SELECT b.*, u.id as user_id 
                             FROM bookings b 
                             LEFT JOIN users u ON b.user_id = u.id 
                             ORDER BY b.created_at DESC LIMIT 3";
                    $result = $mysqli->query($query);

                    if ($result && $result->num_rows > 0) {
                        while ($booking = $result->fetch_assoc()) {
                            $statusClass = '';
                            switch ($booking['status']) {
                                case 'pending':
                                    $statusClass = 'status-pending';
                                    break;
                                case 'approved':
                                    $statusClass = 'status-approved';
                                    break;
                                case 'completed':
                                    $statusClass = 'status-completed';
                                    break;
                                case 'cancelled':
                                    $statusClass = 'status-cancelled';
                                    break;
                            }
                    ?>
                            <tr data-user-id="<?php echo htmlspecialchars($booking['user_id']); ?>">
                                <td><?php echo htmlspecialchars($booking['reference_number']); ?></td>
                                <td>
                                    Room <?php echo htmlspecialchars($booking['room_number']); ?><br>
                                    <small>Occupancy: <?php echo htmlspecialchars($booking['occupancy']); ?></small>
                                </td>
                                <td class="hide-mobile">
                                    <?php
                                    echo date('M d, Y', strtotime($booking['arrival_date'])) . '<br>';
                                    echo '<small>' . htmlspecialchars($booking['arrival_time']) . '</small>';
                                    ?>
                                </td>
                                <td class="hide-mobile">
                                    <?php
                                    echo date('M d, Y', strtotime($booking['departure_date'])) . '<br>';
                                    echo '<small>' . htmlspecialchars($booking['departure_time']) . '</small>';
                                    ?>
                                </td>
                                <td class="hide-mobile">₱<?php echo number_format($booking['price'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="7" class="text-center">No bookings found</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="bookingUserModal" class="booking-user-modal">
        <div class="booking-modal-content">
            <div class="booking-modal-header">
                <h2>Booking Details</h2>
                <span class="booking-modal-close">&times;</span>
            </div>
            <div class="booking-modal-body">
                <div class="booking-user-details">
                    <h3>User Information</h3>
                    <div class="booking-detail-grid">
                        <div class="booking-detail-item">
                            <label>Username:</label>
                            <span id="booking-modal-username"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Email:</label>
                            <span id="booking-modal-email"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Full Name:</label>
                            <span id="booking-modal-fullname"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Position:</label>
                            <span id="booking-modal-position"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Address:</label>
                            <span id="booking-modal-address"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Second Address:</label>
                            <span id="booking-modal-second-address"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Telephone:</label>
                            <span id="booking-modal-telephone"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Phone Number:</label>
                            <span id="booking-modal-phone"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Accompanying Persons:</label>
                            <span id="booking-modal-accompanying"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>User Status:</label>
                            <span id="booking-modal-user-status"></span>
                        </div>
                        <div class="booking-detail-item">
                            <label>Verification Status:</label>
                            <span id="booking-modal-verified"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.querySelectorAll('.bookings-table tbody tr').forEach(row => {
            row.addEventListener('click', async function(e) {
                if (e.target.classList.contains('status-select') ||
                    e.target.closest('.status-select')) {
                    return;
                }

                const userId = this.getAttribute('data-user-id');
                console.log('Clicked row user ID:', userId);

                if (!userId) {
                    console.error('No user ID found for this booking');
                    return;
                }

                const modal = document.getElementById('bookingUserModal');
                modal.style.display = "block";

                try {
                    const formData = new FormData();
                    formData.append('user_id', userId);

                    console.log('Sending request to get user details...');
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

                    const updateField = (id, value, defaultValue = 'N/A') => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.textContent = value || defaultValue;
                        }
                    };

                    updateField('booking-modal-username', data.user.username);
                    updateField('booking-modal-email', data.user.email);

                    const fullName = [
                        data.user_details.first_name,
                        data.user_details.middle_name,
                        data.user_details.last_name
                    ].filter(Boolean).join(' ');
                    updateField('booking-modal-fullname', fullName);

                    updateField('booking-modal-position', data.user_details.position);
                    updateField('booking-modal-address', data.user_details.address);
                    updateField('booking-modal-second-address', data.user_details.second_address);
                    updateField('booking-modal-telephone', data.user_details.telephone);
                    updateField('booking-modal-phone', data.user_details.phone_number);
                    updateField('booking-modal-accompanying', data.user_details.accompanying_persons);
                    updateField('booking-modal-user-status', data.user_details.user_status);
                    updateField('booking-modal-verified', data.user_details.verified ? 'Verified' : 'Not Verified');

                } catch (error) {
                    console.error('Error fetching user details:', error);
                    const modalBody = document.querySelector('.booking-modal-body');

                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'error-message';
                    errorMessage.style.cssText = 'color: red; padding: 10px; margin: 10px; background-color: #fee2e2; border: 1px solid #ef4444; border-radius: 4px;';
                    errorMessage.textContent = `Error: ${error.message}. Please try again.`;

                    modalBody.insertBefore(errorMessage, modalBody.firstChild);
                }
            });
        });

        const bookingModal = document.getElementById('bookingUserModal');
        const closeBtn = document.querySelector('.booking-modal-close');

        closeBtn.onclick = function() {
            bookingModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == bookingModal) {
                bookingModal.style.display = "none";
            }
        }

        // Booking status update functionality
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', async function() {
                const bookingId = this.dataset.bookingId;
                const newStatus = this.value;

                try {
                    const response = await fetch('update_booking_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `booking_id=${bookingId}&status=${newStatus}`
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        // Update the status badge
                        const statusBadge = this.closest('tr').querySelector('.status-badge');
                        statusBadge.className = `status-badge status-${newStatus}`;
                        statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

                        // Show success message
                        alert('Status updated successfully!');
                    } else {
                        throw new Error(data.message || 'Error updating status');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error updating status. Please try again.');
                    // Reset select to previous value
                    this.value = this.querySelector('[selected]').value;
                }
            });
        });

        // Number animation for stat cards
        document.addEventListener('DOMContentLoaded', function() {
            const numbers = document.querySelectorAll('.stat-content h3');

            numbers.forEach(number => {
                const finalValue = parseInt(number.textContent);
                let startValue = 0;
                const duration = 1000;
                const increment = finalValue / (duration / 16);

                function updateNumber() {
                    startValue += increment;
                    if (startValue < finalValue) {
                        number.textContent = Math.floor(startValue);
                        requestAnimationFrame(updateNumber);
                    } else {
                        number.textContent = finalValue;
                    }
                }

                updateNumber();
            });
        });
    </script>

    <!-- Booking analytics -->

    <script src="/Alumni-CvSU/admin/script/dashboard_analytics.js"></script>


    <!-- Booking trends and revenue -->
    <script src="/Alumni-CvSU/admin/script/get_booking_trends.js"></script>

    <!-- Room Occupancy -->
    <script src="/Alumni-CvSU/admin/script/get_room_occupancy.js"></script>

    <!-- Booking Status -->
    <script src="/Alumni-CvSU/admin/script/get_booking_status.js"></script>



</body>

</html>