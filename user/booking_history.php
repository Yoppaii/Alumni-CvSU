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
</head>


<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing your request...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

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
                                    <td data-label="Reference No."><?php echo htmlspecialchars($booking['reference_number']); ?></td>
                                    <td data-label="Room"><?php echo getRoomDisplay($booking['room_number']); ?></td>
                                    <td data-label="Occupancy"><?php echo htmlspecialchars($booking['occupancy']); ?> Person</td>
                                    <td data-label="Matress Fee"><?php echo number_format($booking['mattress_fee'], 2); ?></td>
                                    <td data-label="Price"><?php echo number_format($booking['price'], 2); ?></td>
                                    <td data-label="Check In"><?php echo date('M d, Y', strtotime($booking['arrival_date'])) . ' ' . date('h:i A', strtotime($booking['arrival_time'])); ?></td>
                                    <td data-label="Check Out"><?php echo date('M d, Y', strtotime($booking['departure_date'])) . ' ' . date('h:i A', strtotime($booking['departure_time'])); ?></td>
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
                                        $allowed_statuses = ['pending', 'confirmed', 'checked_in', 'completed'];
                                        if (in_array($status, $allowed_statuses)) {
                                            if ($status === 'confirmed') {
                                                $current_time = time();
                                                $booking_time = strtotime($booking['created_at']);
                                                $elapsed_seconds = $current_time - $booking_time;
                                                $hours_elapsed = $elapsed_seconds / 3600;

                                                if ($hours_elapsed <= 24) {
                                                    $remaining_seconds = (24 * 3600) - ($current_time - $booking_time);
                                                    $remaining_hours = floor($remaining_seconds / 3600);
                                                    $remaining_minutes = floor(($remaining_seconds % 3600) / 60);
                                                    $tooltip = "Cancellation available for {$remaining_hours} hrs {$remaining_minutes} mins more";

                                                    echo '<button class="cancel-btn" onclick="showCancelModal(\'' . $booking['id'] . '\', \'' . $booking['reference_number'] . '\', \'' . $tooltip . '\')" title="' . $tooltip . '">
                                                            Cancel
                                                        </button>';
                                                } else {
                                                    echo '<i class="fas fa-ban text-gray-400" title="Cannot be cancelled"></i>';
                                                }
                                            } elseif ($status === 'pending') {
                                                $tooltip = "You can cancel this booking";
                                                echo '<button class="cancel-btn" onclick="showCancelModal(\'' . $booking['id'] . '\', \'' . $booking['reference_number'] . '\', \'' . $tooltip . '\')" title="' . $tooltip . '">
                                                        Cancel
                                                    </button>';
                                            } else {
                                                echo '<i class="fas fa-ban text-gray-400" title="No actions available"></i>';
                                            }
                                        } else {
                                            echo '<i class="fas fa-ban text-gray-400" title="Not available"></i>';
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


    <!-- <textarea id="cancellationReason" name="cancellation_reason" placeholder="Please provide a reason for cancellation" required></textarea> -->

    <img id="your-logo-id" src="/Alumni-CvSU/asset/images/res1.png" style="display: none;" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/polyfills.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            NotificationSystem.init();

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
                    // const reason = document.getElementById('cancellationReason').value;

                    // if (!bookingId || !reason) {
                    //     NotificationSystem.show('Please fill in all required fields', 'error');
                    //     return;
                    // }

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
                            NotificationSystem.show('Booking cancelled successfully', 'success');
                            setTimeout(() => {
                                window.location.href = 'Account?section=home&sidebar=1';
                            }, 1500);
                        } else {
                            hideLoading();
                            NotificationSystem.show(data.message || 'Error cancelling booking', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        hideLoading();
                        NotificationSystem.show('An error occurred while cancelling the booking', 'error');
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
                        '1. Present this invoice and valid ID upon check-in',
                        '2. Check-in time | Check-out: time',
                        '3. Cancellations must be made 24 hours before check-in',
                        '4. Additional charges may apply for late check-out',
                        '5. The guest house is not responsible for any lost items'
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
                    NotificationSystem.show('Receipt generated successfully', 'success');

                } catch (error) {
                    console.error('Error generating receipt:', error);
                    hideLoading();
                    NotificationSystem.show('Error generating receipt', 'error');
                }
            };
            // const tableHeaders = document.querySelectorAll('.booking-table thead tr');
            // tableHeaders.forEach(header => {
            //     const th = document.createElement('th');
            //     th.textContent = 'Invoice';
            //     header.appendChild(th);
            // });

            // const tableRows = document.querySelectorAll('.booking-table tbody tr');
            // tableRows.forEach(row => {
            //     const td = document.createElement('td');

            //     const statusCell = row.querySelector('[data-label="Status"]');
            //     const status = statusCell?.textContent.trim().toLowerCase();

            //     // List of statuses that allow invoice generation
            //     const allowedStatuses = ['confirmed', 'checked_in', 'completed'];

            //     if (allowedStatuses.includes(status)) {
            //         const button = document.createElement('button');
            //         button.innerHTML = '<i class="fas fa-file-invoice"></i>';
            //         button.className = 'invoice-btn';
            //         button.onclick = () => generateInvoice(row);
            //         td.appendChild(button);
            //     } else {
            //         // Display a 'not available' icon (e.g., ban icon)
            //         const icon = document.createElement('i');
            //         icon.className = 'fas fa-ban text-gray-400';
            //         icon.style.color = '#ccc'; // optional styling for disabled effect
            //         td.appendChild(icon);
            //     }

            //     row.appendChild(td);
            // });

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