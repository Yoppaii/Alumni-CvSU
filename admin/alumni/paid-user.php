<?php
include 'main_db.php';
$today = date('Y-m-d');

$baseQuery = "SELECT a.*, u.email as user_email 
             FROM alumni_id_cards a 
             LEFT JOIN users u ON a.user_id = u.id
             WHERE a.status = 'paid'
             ORDER BY a.created_at DESC LIMIT 20";
$applicationsResult = $mysqli->query($baseQuery);

$countQuery = "SELECT COUNT(*) as count FROM alumni_id_cards WHERE status = 'paid'";
$totalCount = $mysqli->query($countQuery)->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni ID Cards</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
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

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        line-height: 1.5;
        color: var(--text-dark);
        background-color: #f1f5f9;
    }

    .alm-bookings-container {
        padding: 1.5rem;
        background: var(--white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin: auto;
        width: 100%;
    }

    .alm-header-content {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .alm-header-content h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alm-header-content h2 i {
        color: var(--primary-color);
    }

    .alm-booking-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.5rem;
        height: 1.5rem;
        padding: 0 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: var(--primary-color);
        color: var(--white);
        border-radius: 9999px;
        margin-left: 0.5rem;
    }

    .alm-table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .alm-bookings-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
    }

    .alm-bookings-table th {
        background: var(--bg-light);
        color: var(--text-light);
        font-weight: 500;
        text-align: left;
        padding: 1rem;
        white-space: nowrap;
        border-bottom: 1px solid var(--border-color);
    }

    .alm-bookings-table th:first-child {
        border-top-left-radius: 0.5rem;
    }

    .alm-bookings-table th:last-child {
        border-top-right-radius: 0.5rem;
    }

    .alm-bookings-table th i {
        margin-right: 0.5rem;
        color: var(--primary-color);
    }

    .alm-bookings-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-dark);
        vertical-align: top;
        background-color: var(--white);
        transition: background-color 0.2s ease;
    }

    .alm-bookings-table tr:last-child td:first-child {
        border-bottom-left-radius: 0.5rem;
    }

    .alm-bookings-table tr:last-child td:last-child {
        border-bottom-right-radius: 0.5rem;
    }

    .alm-bookings-table tbody tr:hover td {
        background-color: var(--bg-light);
    }

    .alm-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .alm-status-paid {
        background-color: #dcfce7;
        color: #15803d;
    }

    .alm-delete-btn {
        background-color: #ef4444;
        color: white;
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .alm-delete-btn:hover {
        background-color: #dc2626;
        transform: translateY(-1px);
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
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

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .alm-hide-mobile {
            display: none;
        }

        .alm-bookings-container {
            padding: 1rem;
            margin: 0.5rem;
        }

        .alm-bookings-table td,
        .alm-bookings-table th {
            padding: 0.75rem;
        }

        .alm-button-group {
            flex-direction: column;
        }

        .AL-modal-actions {
            flex-direction: column;
            /* Adjust for AL-modal-actions */
        }

        .AL-modal-btn {
            width: 100%;
            justify-content: center;
        }
    }

    .alm-status-select {
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        border: 1px solid var(--border-color);
        font-size: 0.85rem;
        background: var(--bg-light);
        color: var(--text-dark);
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
</style>
<!-- Add some CSS for animations if needed -->
<style>
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
        0% {
            opacity: 1;
            transform: translateX(0);
        }

        100% {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    @keyframes statusChangeHighlight {
        0% {
            background-color: transparent;
        }

        50% {
            background-color: rgba(255, 255, 0, 0.2);
        }

        100% {
            background-color: transparent;
        }
    }

    /* Disable delete button when input is empty */
    #almDeleteConfirmBtn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<body>
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p class="loading-text">Processing your request...</p>
        </div>
    </div>

    <div class="alm-bookings-container">
        <div class="alm-header-content">
            <h2><i class="fas fa-credit-card"></i> Paid Alumni ID Applications <span class="alm-booking-count"><?php echo $totalCount; ?></span></h2>
        </div>
        <div class="alm-table-responsive">
            <table class="alm-bookings-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i> Applicant</th>
                        <th><i class="fas fa-graduation-cap"></i> Course</th>
                        <th class="alm-hide-mobile"><i class="fas fa-calendar"></i> Year Graduated</th>
                        <th class="alm-hide-mobile"><i class="fas fa-school"></i> High School</th>
                        <th class="alm-hide-mobile"><i class="fas fa-tag"></i> Type</th>
                        <th><i class="fas fa-dollar-sign"></i> Amount</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th class="alm-hide-mobile"><i class="fas fa-cog"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($applicationsResult && $applicationsResult->num_rows > 0): ?>
                        <?php while ($application = $applicationsResult->fetch_assoc()): ?>
                            <tr data-user-id="<?php echo htmlspecialchars($application['id']); ?>">
                                <td>
                                    <?php
                                    $fullName = implode(' ', array_filter([
                                        $application['first_name'],
                                        $application['middle_name'],
                                        $application['last_name']
                                    ]));
                                    echo htmlspecialchars($fullName);
                                    ?>
                                    <br>
                                    <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($application['user_email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($application['course']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['year_graduated']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['highschool_graduated']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['membership_type']); ?></td>
                                <td>₱<?php echo number_format($application['price'], 2); ?></td>
                                <td>
                                    <select class="alm-status-select" data-id="<?php echo $application['id']; ?>">
                                        <option value="pending" <?php if ($application['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="paid" <?php if ($application['status'] == 'paid') echo 'selected'; ?>>Paid</option>
                                        <option value="confirmed" <?php if ($application['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                                        <option value="declined" <?php if ($application['status'] == 'declined') echo 'selected'; ?>>Declined</option>
                                    </select>
                                </td>
                                <td class="alm-hide-mobile">
                                    <button class="alm-delete-btn" onclick="deleteApplication('<?php echo $application['id']; ?>', event)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="alm-text-center">
                                <i class="fas fa-inbox fa-2x"></i><br>
                                No paid ID card applications found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>



    <!-- Updated Delete Confirmation Modal -->
    <div class="AL-modal-overlay" id="almDeleteConfirmModal" role="dialog" aria-modal="true" aria-labelledby="AL-delete-modal-title" aria-describedby="AL-delete-modal-desc">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AL-modal-title" id="AL-delete-modal-title">Delete Alumni</h3>
                <button class="AL-modal-close">&times;</button>
            </div>
            <div class="AL-modal-content" id="AL-delete-modal-desc">
                <p style="color: red; font-weight: bold;">
                    Are you absolutely sure you want to delete this alumni record?
                    This action CANNOT be undone.
                </p>
                <p style="margin-top: 10px;">
                    Please type "DELETE" to confirm:
                </p>
                <input type="text" id="deleteConfirmInput"
                    style="width: 100%; margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="AL-modal-actions">
                <button id="almDeleteCancelBtn" class="AL-modal-btn AL-modal-btn-secondary" data-action="cancel">Cancel</button>
                <button id="almDeleteConfirmBtn" class="AL-modal-btn AL-modal-btn-danger" data-action="confirm">Delete Permanently</button>
            </div>
        </div>
    </div>
    <!-- JavaScript for handling the delete confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('almDeleteConfirmModal');
            const confirmDeleteBtn = document.getElementById('almDeleteConfirmBtn');
            const cancelDeleteBtn = document.getElementById('almDeleteCancelBtn');
            const modalCloseBtn = document.querySelector('.AL-modal-close');
            const deleteConfirmInput = document.getElementById('deleteConfirmInput');
            let applicationIdToDelete = null;

            function showLoading(message = 'Processing your request...') {
                const overlay = document.getElementById('loadingOverlay');
                const loadingText = overlay.querySelector('.loading-text');
                if (loadingText) {
                    loadingText.textContent = message;
                }
                overlay.style.display = 'flex';
            }

            function hideLoading() {
                const overlay = document.getElementById('loadingOverlay');
                overlay.style.display = 'none';
            }

            function showDeleteModal() {
                deleteModal.classList.add('active');
                if (deleteConfirmInput) {
                    deleteConfirmInput.value = '';
                    deleteConfirmInput.focus();
                }
            }

            function hideDeleteModal() {
                deleteModal.classList.remove('active');
            }

            // Enable or disable the delete button based on confirmation input
            if (deleteConfirmInput) {
                deleteConfirmInput.addEventListener('input', function() {
                    confirmDeleteBtn.disabled = this.value !== 'DELETE';
                });
            }

            // Update delete application function to use the DELETE confirmation
            window.deleteApplication = function(applicationId, event) {
                event.preventDefault();
                applicationIdToDelete = applicationId;
                showDeleteModal();
            };

            // Update the confirm delete button to check for "DELETE" text
            confirmDeleteBtn.addEventListener('click', function() {
                if (!applicationIdToDelete) {
                    showNotification('No application selected for deletion', 'error');
                    return;
                }

                // Check if DELETE was typed correctly
                if (deleteConfirmInput && deleteConfirmInput.value !== 'DELETE') {
                    showNotification('Please type DELETE in all capitals to confirm', 'error');
                    return;
                }

                showLoading('Deleting application...');

                // AJAX request to delete record
                fetch('/Alumni-CvSU/admin/delete-id-application.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'application_id=' + encodeURIComponent(applicationIdToDelete)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        hideLoading();
                        hideDeleteModal();

                        if (data.success) {
                            const rowToDelete = document.querySelector(`tr[data-user-id="${applicationIdToDelete}"]`);
                            if (rowToDelete) {
                                rowToDelete.style.animation = 'slideOut 0.3s ease-out forwards';
                                setTimeout(() => {
                                    rowToDelete.remove();
                                }, 300);
                            }
                            showNotification('Application deleted successfully', 'success');
                        } else {
                            showNotification('Failed to delete application: ' + (data.message || 'Unknown error'), 'error');
                        }

                        applicationIdToDelete = null; // Reset
                    })
                    .catch(error => {
                        hideLoading();
                        hideDeleteModal();
                        showNotification('An error occurred: ' + error.message, 'error');
                        applicationIdToDelete = null;
                    });
            });

            cancelDeleteBtn.addEventListener('click', function() {
                hideDeleteModal();
                applicationIdToDelete = null;
            });

            if (modalCloseBtn) {
                modalCloseBtn.addEventListener('click', function() {
                    hideDeleteModal();
                    applicationIdToDelete = null;
                });
            }

            // Close modal on outside click
            window.addEventListener('click', function(event) {
                if (event.target == deleteModal) {
                    hideDeleteModal();
                    applicationIdToDelete = null;
                }
            });

            // Close modal on pressing escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    hideDeleteModal();
                    applicationIdToDelete = null;
                }
            });

            // Notification system
            window.showNotification = function(message, type = 'info') {
                const container = document.getElementById('notificationContainer');
                if (!container) {
                    // Create notification container if it doesn't exist
                    const newContainer = document.createElement('div');
                    newContainer.id = 'notificationContainer';
                    newContainer.style.position = 'fixed';
                    newContainer.style.top = '20px';
                    newContainer.style.right = '20px';
                    newContainer.style.zIndex = '9999';
                    document.body.appendChild(newContainer);
                }

                const notificationContainer = document.getElementById('notificationContainer');
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerHTML = `
                <div>
                    <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
                </div>
                <button type="button" class="notification-close" onclick="this.parentElement.remove()">&times;</button>
            `;
                notificationContainer.appendChild(notification);

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
            };
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('almDeleteConfirmModal');
            const confirmDeleteBtn = document.getElementById('almDeleteConfirmBtn');
            const cancelDeleteBtn = document.getElementById('almDeleteCancelBtn');
            const modalCloseBtn = document.querySelector('.AL-modal-close');
            const deleteConfirmInput = document.getElementById('deleteConfirmInput');
            let applicationIdToDelete = null;

            function showLoading(message = 'Processing your request...') {
                const overlay = document.getElementById('loadingOverlay');
                const loadingText = overlay.querySelector('.loading-text');
                if (loadingText) {
                    loadingText.textContent = message;
                }
                overlay.style.display = 'flex';
            }

            function hideLoading() {
                const overlay = document.getElementById('loadingOverlay');
                overlay.style.display = 'none';
            }



            function showALModal(options) {
                return new Promise((resolve) => {
                    const modal = document.getElementById('AL-modal-overlay');
                    const confirmBtn = modal.querySelector('[data-action="confirm"]');
                    const cancelBtn = modal.querySelector('[data-action="cancel"]');

                    // Update modal title and content if provided
                    if (options.title) {
                        modal.querySelector('.AL-modal-title').textContent = options.title;
                    }
                    if (options.message) {
                        modal.querySelector('.AL-modal-content').textContent = options.message;
                    }

                    modal.classList.add('active');

                    const cleanup = () => {
                        modal.classList.remove('active');
                        confirmBtn.onclick = null;
                        cancelBtn.onclick = null;
                        modal.onclick = null;
                    };

                    confirmBtn.onclick = () => {
                        cleanup();
                        resolve(true);
                    };

                    cancelBtn.onclick = () => {
                        cleanup();
                        resolve(false);
                    };

                    modal.onclick = (e) => {
                        if (e.target === modal) {
                            cleanup();
                            resolve(false);
                        }
                    };
                });
            }

            cancelDeleteBtn.addEventListener('click', function() {
                hideDeleteModal();
                applicationIdToDelete = null;
            });

            if (modalCloseBtn) {
                modalCloseBtn.addEventListener('click', function() {
                    hideDeleteModal();
                    applicationIdToDelete = null;
                });
            }


            // Close modal on pressing escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    hideDeleteModal();
                    applicationIdToDelete = null;
                }
            });

            // Handle status select changes with improved animation
            const statusSelects = document.querySelectorAll('.alm-status-select');
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const status = this.value;
                    const previousValue = this.getAttribute('data-previous-value') || 'pending';

                    if (!id) {
                        showNotification('Application ID not found', 'error');
                        return;
                    }

                    // Get the parent row for animation
                    const parentRow = this.closest('tr');
                    if (parentRow) {
                        parentRow.style.animation = 'statusChangeHighlight 1s ease';
                    }

                    showLoading(`Updating status to ${status}...`);

                    // AJAX request to update status
                    fetch('/Alumni-CvSU/admin/alumni/update_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(status)
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            hideLoading();

                            if (data.success) {
                                const user = document.querySelector(`tr[data-user-id="${id}"]`);

                                if (user) {
                                    user.style.animation = 'slideOut 0.3s ease-out forwards';
                                    setTimeout(() => {
                                        user.remove();
                                    }, 300);
                                }

                                showNotification(`Status successfully updated to ${status}`, 'success');
                            } else {
                                showNotification('Failed to update status: ' + (data.message || 'Unknown error'), 'error');
                                // Reset the select to its previous value
                                this.value = previousValue;
                            }
                        })
                        .catch(error => {
                            hideLoading();
                            showNotification('An error occurred: ' + error.message, 'error');
                            // Reset the select to its previous value
                            this.value = previousValue;
                        });

                    // Store the current value as previous value for potential rollback
                    this.setAttribute('data-previous-value', status);
                });

                // Store initial value
                select.setAttribute('data-previous-value', select.value);
            });

            // Initialize archiveAlumni function from the original code
            window.archiveAlumni = async function(id) {
                const confirmed = await showALModal({
                    type: 'warning',
                    title: 'Archive Alumni',
                    message: 'Are you sure you want to archive this alumni record? The record will be removed from the active list.'
                });

                if (confirmed) {
                    // Send AJAX request to archive the alumni record
                    const formData = new FormData();
                    formData.append('alumni_id', id);

                    fetch('/Alumni-CvSU/admin/alumni/archive-alumni.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove the row from the table on success
                                const row = document.querySelector(`button[onclick="archiveAlumni('${id}')"]`).closest('tr');
                                if (row) {
                                    row.style.animation = 'slideOut 0.3s ease-out forwards';
                                    setTimeout(() => {
                                        row.remove();
                                    }, 300);
                                }

                                // Show a success notification
                                showNotification('Alumni record archived successfully', 'success');
                            } else {
                                showNotification('Error: ' + data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('An error occurred while archiving the record', 'error');
                        });
                }
            };

            // Initialize search functionality if needed
            function initializeSearch() {
                const searchInput = document.getElementById('alumni-search');
                if (!searchInput) return;

                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('.AL-table tbody tr');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Initialize search
            initializeSearch();
        });
    </script>
</body>

</html>