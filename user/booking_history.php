<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$active_sql = "SELECT *, created_at FROM bookings WHERE user_id = ? ORDER BY created_at DESC";
$active_stmt = $mysqli->prepare($active_sql);
$active_stmt->bind_param("i", $user_id);
$active_stmt->execute();
$active_result = $active_stmt->get_result();
$cancelled_sql = "SELECT * FROM cancelled_bookings WHERE user_id = ? ORDER BY cancelled_at DESC";
$cancelled_stmt = $mysqli->prepare($cancelled_sql);
$cancelled_stmt->bind_param("i", $user_id);
$cancelled_stmt->execute();
$cancelled_result = $cancelled_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        }

        .booking-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .booking-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .booking-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .booking-header h1 i {
            color: var(--primary-color);
        }

        .booking-content {
            padding: 24px;
        }

        .booking-section {
            margin-bottom: 32px;
        }

        .booking-section h2 {
            color: #374151;
            font-size: 18px;
            margin: 0 0 16px 0;
            font-weight: 600;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .booking-table th,
        .booking-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .booking-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
        }

        .status-pill {
            display: inline-flex;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-completed { background: #dbeafe; color: #1e40af; }

        .cancel-btn {
            padding: 6px 12px;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cancel-btn:hover {
            background-color: #dc2626;
        }

        .no-bookings {
            text-align: center;
            padding: 32px;
            color: #6b7280;
            font-size: 14px;
        }

        .cancel-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .cancel-modal-content {
            background: white;
            border-radius: 8px;
            padding: 24px;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-md);
        }

        .cancel-modal-title {
            color: #111827;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 16px 0;
        }

        .cancel-form {
            display: grid;
            gap: 16px;
        }

        .cancel-form textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            min-height: 100px;
            font-size: 14px;
        }

        .cancel-form-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }

        .btn-cancel {
            background-color: #ef4444;
            color: white;
        }

        .btn-back {
            background-color: #9ca3af;
            color: white;
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        .loading-overlay-show {
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        .loading-overlay-hide {
            animation: fadeOut 0.3s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
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

        @media (max-width: 768px) {
            .booking-table {
                display: block;
            }
            
            .booking-table thead {
                display: none;
            }
            
            .booking-table tbody {
                display: block;
            }
            
            .booking-table tr {
                display: block;
                margin-bottom: 16px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 12px;
            }
            
            .booking-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border: none;
            }
            
            .booking-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #374151;
                margin-right: 16px;
            }

            .cancel-form-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
        .invoice-btn {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .invoice-btn:hover {
            background-color: #45a049;
            transform: translateY(-1px);
        }

        .invoice-btn i {
            margin-right: 4px;
        }

        @media (max-width: 768px) {
            .invoice-btn {
                width: 100%;
                margin-top: 8px;
            }
        }
        .text-gray-500 {
            color: #6b7280;
            font-size: 12px;
            font-style: italic;
        }
    </style>
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
                <h1><i class="fas fa-calendar-alt"></i> Booking History</h1>
            </div>

            <div class="booking-content">
                <div class="booking-section">
                    <h2>Active Bookings</h2>
                    <?php if ($active_result->num_rows > 0): ?>
                        <table class="booking-table">
                            <thead>
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Room</th>
                                    <th>Occupancy</th>
                                    <th>Price</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($booking = $active_result->fetch_assoc()): ?>
                                    <tr>
                                        <td data-label="Reference No."><?php echo htmlspecialchars($booking['reference_number']); ?></td>
                                        <td data-label="Room">Room <?php echo htmlspecialchars($booking['room_number']); ?></td>
                                        <td data-label="Occupancy"><?php echo htmlspecialchars($booking['occupancy']); ?> Person</td>
                                        <td data-label="Price"><?php echo number_format($booking['price'], 2); ?></td>
                                        <td data-label="Check In"><?php echo date('M d, Y', strtotime($booking['arrival_date'])) . ' ' . date('h:i A', strtotime($booking['arrival_time'])); ?></td>
                                        <td data-label="Check Out"><?php echo date('M d, Y', strtotime($booking['departure_date'])) . ' ' . date('h:i A', strtotime($booking['departure_time'])); ?></td>
                                        <td data-label="Status">
                                            <span class="status-pill status-<?php echo strtolower($booking['status']); ?>">
                                                <?php echo ucfirst(strtolower($booking['status'])); ?>
                                            </span>
                                        </td>

                                        <td data-label="Action">
                                            <?php 
                                                if (strtolower($booking['status']) === 'confirmed') {
                                                    $current_time = time();
                                                    $booking_time = strtotime($booking['created_at']);
                                                    $hours_elapsed = ($current_time - $booking_time) / 3600;

                                                    if ($hours_elapsed <= 24) {
                                                        echo '<button class="cancel-btn" onclick="showCancelModal(\'' . $booking['id'] . '\', \'' . $booking['reference_number'] . '\')">
                                                                Cancel
                                                            </button>';
                                                    } else {
                                                        echo '<span class="text-gray-500">Cancellation period expired</span>';
                                                    }
                                                }
                                            ?>
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

                <div class="booking-section">
                    <h2>Cancelled Bookings</h2>
                    <?php if ($cancelled_result->num_rows > 0): ?>
                        <table class="booking-table">
                            <thead>
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Room</th>
                                    <th>Occupancy</th>
                                    <th>Price</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Cancelled On</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($booking = $cancelled_result->fetch_assoc()): ?>
                                    <tr>
                                        <td data-label="Reference No."><?php echo htmlspecialchars($booking['reference_number']); ?></td>
                                        <td data-label="Room">Room <?php echo htmlspecialchars($booking['room_number']); ?></td>
                                        <td data-label="Occupancy"><?php echo htmlspecialchars($booking['occupancy']); ?> Person</td>
                                        <td data-label="Price"><?php echo number_format($booking['price'], 2); ?></td>
                                        <td data-label="Check In"><?php echo date('M d, Y', strtotime($booking['arrival_date'])) . ' ' . date('h:i A', strtotime($booking['arrival_time'])); ?></td>
                                        <td data-label="Check Out"><?php echo date('M d, Y', strtotime($booking['departure_date'])) . ' ' . date('h:i A', strtotime($booking['departure_time'])); ?></td>
                                        <td data-label="Cancelled On"><?php echo date('M d, Y h:i A', strtotime($booking['cancelled_at'])); ?></td>
                                        <td data-label="Reason"><?php echo htmlspecialchars($booking['cancellation_reason']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-bookings">
                            <p>No cancelled bookings found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="cancelModal" class="cancel-modal">
            <div class="cancel-modal-content">
                <h3 class="cancel-modal-title">Cancel Booking</h3>
                <p>Are you sure you want to cancel booking <span id="referenceNumber"></span>?</p>
                <form id="cancelForm" class="cancel-form">
                    <input type="hidden" id="bookingId" name="booking_id">
                    <textarea id="cancellationReason" name="cancellation_reason" placeholder="Please provide a reason for cancellation" required></textarea>
                    <div class="cancel-form-buttons">
                        <button type="button" class="btn btn-back" onclick="hideCancelModal()">Back</button>
                        <button type="submit" class="btn btn-cancel">Confirm Cancellation</button>
                    </div>
                </form>
            </div>
        </div>


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

            window.showCancelModal = function(bookingId, referenceNumber) {
                const bookingIdInput = document.getElementById('bookingId');
                const referenceNumberSpan = document.getElementById('referenceNumber');
                
                if (bookingIdInput && referenceNumberSpan) {
                    bookingIdInput.value = bookingId;
                    referenceNumberSpan.textContent = referenceNumber;
                    cancelModal.style.display = 'flex';
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
                    const reason = document.getElementById('cancellationReason').value;
                    
                    if (!bookingId || !reason) {
                        NotificationSystem.show('Please fill in all required fields', 'error');
                        return;
                    }
                    
                    const submitButton = this.querySelector('button[type="submit"]');
                    const backButton = this.querySelector('button[type="button"]');
                    
                    try {
                        submitButton.disabled = true;
                        backButton.disabled = true;
                        hideCancelModal();
                        showLoading('Processing cancellation...');
                        
                        const formData = new FormData();
                        formData.append('booking_id', bookingId);
                        formData.append('cancellation_reason', reason);
                        
                        console.log('Sending data:', {
                            booking_id: bookingId,
                            cancellation_reason: reason
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
                    
                    const refNo = row.querySelector('[data-label="Reference No."]').textContent;
                    const room = row.querySelector('[data-label="Room"]').textContent;
                    const occupancy = row.querySelector('[data-label="Occupancy"]').textContent;
                    const price = row.querySelector('[data-label="Price"]').textContent;
                    const checkIn = row.querySelector('[data-label="Check In"]').textContent;
                    const checkOut = row.querySelector('[data-label="Check Out"]').textContent;

                    const response = await fetch('user/get_user_details.php');
                    const userData = await response.json();

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
                    const organizationDetails = [
                        'Cavite State University',
                        'Office of Alumni Affairs',
                        'Indang, Cavite',
                        'Philippines'
                    ];
                    organizationDetails.forEach(line => {
                        doc.text(line, leftMargin, yPos);
                        yPos += 5;
                    });

                    yPos += 5;
                    doc.setFont('helvetica', 'bold');
                    doc.text('Bill To:', leftMargin, yPos);
                    yPos += 6;
                    doc.setFont('helvetica', 'normal');
                    const billToDetails = [
                        `Full Name: ${userData.first_name} ${userData.middle_name} ${userData.last_name}`,
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
                    const invoiceDetails = [
                        ['Invoice Number:', refNo],
                        ['Date:', new Date().toLocaleDateString('en-US', { 
                            month: 'long', 
                            day: '2-digit', 
                            year: 'numeric' 
                        })],
                        ['Check In:', checkIn],
                        ['Check Out:', checkOut]
                    ];
                    invoiceDetails.forEach(([label, value]) => {
                        doc.setFont('helvetica', 'bold');
                        doc.text(label, rightColumn, rightYPos);
                        doc.setFont('helvetica', 'normal');
                        doc.text(value, rightColumn + 30, rightYPos);
                        rightYPos += 5;
                    });

                    yPos = Math.max(yPos, rightYPos) + 20;
                    const tableHeaders = ['Description', 'Quantity', 'Unit Price', 'Amount'];
                    const columnWidths = [80, 25, 30, 30];
                    const firstColumn = leftMargin;
                    let xPos = firstColumn;

                    doc.setFillColor(240, 240, 240);
                    doc.rect(leftMargin, yPos - 5, pageWidth - (2 * leftMargin), 8, 'F');
                    doc.setFont('helvetica', 'bold');
                    tableHeaders.forEach((header, i) => {
                        doc.text(header, xPos, yPos);
                        xPos += columnWidths[i];
                    });

                    yPos += 10;
                    doc.setFont('helvetica', 'normal');
                    xPos = firstColumn;
                    const tableContent = [
                        room,
                        '1',
                        price,
                        price
                    ];
                    tableContent.forEach((text, i) => {
                        doc.text(text, xPos, yPos);
                        xPos += columnWidths[i];
                    });

                    yPos += 20;
                    doc.setDrawColor(200, 200, 200);
                    doc.setLineWidth(0.5);
                    doc.line(leftMargin, yPos, pageWidth - leftMargin, yPos);
                    yPos += 10;

                    const totalsSection = [
                        ['Subtotal:', price],
                        ['Total:', price],
                        ['Amount Paid:', price]
                    ];
                    totalsSection.forEach(([label, value]) => {
                        doc.setFont('helvetica', 'bold');
                        doc.text(label, pageWidth - 80, yPos);
                        doc.text(value, pageWidth - leftMargin, yPos, { align: 'right' });
                        yPos += 8;
                    });

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
                    doc.rect(leftMargin - 3, yPos - 5, pageWidth - (2 * (leftMargin - 3)), 
                            termsAndPolicies.length * 6 + 6, 'F');

                    termsAndPolicies.forEach(term => {
                        doc.text(term, leftMargin, yPos);
                        yPos += 6;
                    });

                    yPos = 270;
                    doc.setFontSize(8);
                    doc.setFont('helvetica', 'normal');
                    doc.text('Thank you for your business!', pageWidth / 2, yPos, { align: 'center' });

                    doc.save(`CVSU-Receipt-${refNo}.pdf`);
                    hideLoading();
                    NotificationSystem.show('Receipt generated successfully', 'success');

                } catch (error) {
                    console.error('Error generating receipt:', error);
                    hideLoading();
                    NotificationSystem.show('Error generating receipt', 'error');
                }
            };

            const tableHeaders = document.querySelectorAll('.booking-table thead tr');
            tableHeaders.forEach(header => {
                const th = document.createElement('th');
                th.textContent = 'Invoice';
                header.appendChild(th);
            });

            const tableRows = document.querySelectorAll('.booking-table tbody tr');
            tableRows.forEach(row => {
                const td = document.createElement('td');
                td.setAttribute('data-label', 'Invoice');
                
                const statusCell = row.querySelector('[data-label="Status"]');

                if (statusCell && statusCell.textContent.trim().toLowerCase() === 'confirmed') {
                    const button = document.createElement('button');
                    button.innerHTML = '<i class="fas fa-file-invoice"></i>';
                    button.className = 'invoice-btn';
                    button.onclick = () => generateInvoice(row);
                    td.appendChild(button);
                } else {
                    td.textContent = '-';
                }
                
                row.appendChild(td);
            });
        });
    </script>
</body>
</html>