<?php
require_once('main_db.php');

// Fetch all bookings
$query = "SELECT `id`, `reference_number`, `user_id`, `room_number`, `occupancy`, `price`, 
          `arrival_date`, `arrival_time`, `departure_date`, `departure_time`, `status`, `created_at` 
          FROM `bookings` ORDER BY created_at DESC";
$result = $mysqli->query($query);
?>

<div class="booking-container">
    <div class="booking-header">
        <h2>All Bookings</h2>
        <button onclick="window.location.href='?section=new-booking'" class="new-booking-btn">
            <i class="fas fa-plus"></i> New Booking
        </button>
    </div>

    <div class="table-container">
        <table class="booking-table">
            <thead>
                <tr>
                    <th>Reference Number</th>
                    <th>Arrival Date</th>
                    <th>Departure Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr onclick="showBookingDetails(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                        <td><?php echo htmlspecialchars($row['reference_number']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['arrival_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['departure_date'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo $row['status']; ?>">
                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Booking Details</h3>
            <button onclick="closeModal()" class="close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modalContent"></div>
        <div class="modal-footer">
            <button onclick="closeModal()" class="close-modal-btn">Close</button>
        </div>
    </div>
</div>

<style>
/* Booking Container */
.booking-container {
    padding: 20px;
    margin: 20px;
    background: var(--bg-primary);
    color: var(--text-primary);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

[data-theme="dark"] .booking-container {
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Header Styles */
.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.booking-header h2 {
    font-size: 24px;
    color: var(--text-primary);
}

.new-booking-btn {
    background-color: #10b981;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
}

.new-booking-btn:hover {
    background-color: #059669;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
}

.booking-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.booking-table th,
.booking-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid var(--bg-secondary);
    color: var(--text-primary);
}

.booking-table th {
    background-color: var(--bg-secondary);
    font-weight: 600;
}

[data-theme="dark"] .booking-table th {
    background-color: rgba(255, 255, 255, 0.05);
}

[data-theme="dark"] .booking-table td {
    border-bottom-color: rgba(255, 255, 255, 0.1);
}

.booking-table tbody tr:hover {
    background-color: var(--bg-secondary);
    cursor: pointer;
}

[data-theme="dark"] .booking-table tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

/* Status Badge Styles */
.status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 14px;
}

.status-badge.confirmed {
    background-color: #dcfce7;
    color: #166534;
}

.status-badge.pending {
    background-color: #fef3c7;
    color: #92400e;
}

.status-badge.cancelled {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-content {
    background-color: var(--bg-primary);
    color: var(--text-primary);
    margin: 10% auto;
    padding: 20px;
    width: 90%;
    max-width: 600px;
    border-radius: 8px;
    position: relative;
}

[data-theme="dark"] .modal-content {
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--bg-secondary);
}

.close-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: var(--text-secondary);
}

.modal-footer {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--bg-secondary);
    text-align: right;
}

.close-modal-btn {
    background-color: var(--bg-secondary);
    color: var(--text-primary);
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
}

.close-modal-btn:hover {
    background-color: var(--text-secondary);
    color: var(--bg-primary);
}

/* Detail Styles */
.booking-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.detail-item {
    margin-bottom: 15px;
}

.detail-label {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 4px;
}

.detail-value {
    font-weight: 600;
    color: var(--text-primary);
}
</style>

<script>
    function showBookingDetails(booking) {
    const modal = document.getElementById('bookingModal');
    const content = document.getElementById('modalContent');
    
    const modalHTML = `
        <div class="booking-details">
            <div class="detail-item">
                <div class="detail-label">Reference Number</div>
                <div class="detail-value">${booking.reference_number}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Room Number</div>
                <div class="detail-value">${booking.room_number}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Occupancy</div>
                <div class="detail-value">${booking.occupancy}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Price</div>
                <div class="detail-value">₱${parseFloat(booking.price).toLocaleString()}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Arrival</div>
                <div class="detail-value">${formatDate(booking.arrival_date)} ${booking.arrival_time}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Departure</div>
                <div class="detail-value">${formatDate(booking.departure_date)} ${booking.departure_time}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Created At</div>
                <div class="detail-value">${formatDate(booking.created_at)}</div>
            </div>
        </div>
    `;
    
    content.innerHTML = modalHTML;
    modal.style.display = 'flex';
}

function closeModal() {
    const modal = document.getElementById('bookingModal');
    modal.style.display = 'none';
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Close modal when clicking outside
document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>