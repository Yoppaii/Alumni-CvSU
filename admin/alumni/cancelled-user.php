<?php
include 'main_db.php';
$today = date('Y-m-d');

$baseQuery = "SELECT c.*, u.email as user_email 
             FROM cancelled_alumni_applications c 
             LEFT JOIN users u ON c.user_id = u.id
             ORDER BY c.cancelled_at DESC LIMIT 20";
$applicationsResult = $mysqli->query($baseQuery);

$countQuery = "SELECT COUNT(*) as count FROM cancelled_alumni_applications";
$totalCount = $mysqli->query($countQuery)->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelled Alumni ID Applications</title>
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
        color: var(--danger-color);
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
        background-color: var(--danger-color);
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
        color: var(--danger-color);
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

    .alm-status-cancelled {
        background-color: #fee2e2;
        color: #b91c1c;
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

    .alm-view-btn {
        background-color: #3b82f6;
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
        margin-bottom: 0.5rem;
    }

    .alm-view-btn:hover {
        background-color: #2563eb;
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
        }

        .AL-modal-btn {
            width: 100%;
            justify-content: center;
        }
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
        max-width: 600px;
        transform: translateY(-20px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 80vh;
        overflow-y: auto;
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

    .AL-modal-icon.info {
        background-color: #3b82f6;
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

    .reason-container {
        padding: 1rem;
        background-color: #f8fafc;
        border-radius: 0.5rem;
        border-left: 4px solid var(--danger-color);
        margin-top: 0.5rem;
    }

    .reason-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .reason-text {
        color: var(--text-secondary);
        white-space: pre-wrap;
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

    .modal-info-row {
        display: flex;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 0.75rem;
    }

    .modal-info-label {
        font-weight: 600;
        width: 170px;
        color: var(--text-primary);
    }

    .modal-info-value {
        flex: 1;
        color: var(--text-secondary);
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
            <h2><i class="fas fa-ban"></i> Cancelled Alumni ID Applications <span class="alm-booking-count"><?php echo $totalCount; ?></span></h2>
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
                        <th><i class="fas fa-calendar-times"></i> Cancelled Date</th>
                        <th><i class="fas fa-cog"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($applicationsResult && $applicationsResult->num_rows > 0): ?>
                        <?php while ($application = $applicationsResult->fetch_assoc()): ?>
                            <tr data-application-id="<?php echo htmlspecialchars($application['id']); ?>">
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
                                    <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($application['user_email'] ?? $application['email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($application['course']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['year_graduated']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['highschool_graduated']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['membership_type']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($application['cancelled_at'])); ?></td>
                                <td>
                                    <button class="alm-view-btn" onclick="viewApplication('<?php echo $application['id']; ?>', '<?php echo addslashes(htmlspecialchars($fullName)); ?>', '<?php echo addslashes(htmlspecialchars($application['cancellation_reason'])); ?>')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="alm-delete-btn" onclick="deleteApplication('<?php echo $application['id']; ?>', event)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">
                                <i class="fas fa-inbox fa-2x" style="color: #64748b;"></i><br>
                                <p style="margin-top: 1rem; color: #64748b;">No cancelled ID card applications found</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="AL-modal-overlay" id="almDeleteConfirmModal">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AL-modal-title">Delete Cancelled Application</h3>
            </div>
            <div class="AL-modal-content">
                <p style="color: red; font-weight: bold;">
                    Are you absolutely sure you want to delete this cancelled application record?
                    This action CANNOT be undone.
                </p>
                <p style="margin-top: 10px;">
                    Please type "DELETE" to confirm:
                </p>
                <input type="text" id="deleteConfirmInput"
                    style="width: 100%; margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="AL-modal-actions">
                <button id="almDeleteCancelBtn" class="AL-modal-btn AL-modal-btn-secondary">Cancel</button>
                <button id="almDeleteConfirmBtn" class="AL-modal-btn AL-modal-btn-danger" disabled>Delete Permanently</button>
            </div>
        </div>
    </div>

    <!-- View Application Modal -->
    <div class="AL-modal-overlay" id="viewApplicationModal">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon info">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3 class="AL-modal-title">Application Details</h3>
            </div>
            <div class="AL-modal-content" id="applicationDetails">
                <!-- Application details will be inserted here by JavaScript -->
            </div>
            <div class="AL-modal-actions">
                <button id="closeViewModalBtn" class="AL-modal-btn AL-modal-btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <div id="notificationContainer"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('almDeleteConfirmModal');
            const viewModal = document.getElementById('viewApplicationModal');
            const confirmDeleteBtn = document.getElementById('almDeleteConfirmBtn');
            const cancelDeleteBtn = document.getElementById('almDeleteCancelBtn');
            const closeViewModalBtn = document.getElementById('closeViewModalBtn');
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

            function showViewModal() {
                viewModal.classList.add('active');
            }

            function hideViewModal() {
                viewModal.classList.remove('active');
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

            // View application details
            window.viewApplication = function(applicationId, applicantName, cancellationReason) {
                const detailsContainer = document.getElementById('applicationDetails');

                // Fetch application details from server
                showLoading('Loading application details...');

                // Using the data we already have for demonstration
                // In a real implementation, you might want to fetch more details via AJAX
                setTimeout(() => {
                    hideLoading();

                    detailsContainer.innerHTML = `
                        <h4 style="margin-bottom: 1rem;">${applicantName}</h4>
                        
                        <div class="reason-container">
                            <div class="reason-label">Cancellation Reason:</div>
                            <div class="reason-text">${cancellationReason || 'No reason provided'}</div>
                        </div>
                    `;

                    showViewModal();
                }, 500);
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
                fetch('/Alumni-CvSU/admin/delete-cancelled-application.php', {
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
                            const rowToDelete = document.querySelector(`tr[data-application-id="${applicationIdToDelete}"]`);
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

            closeViewModalBtn.addEventListener('click', function() {
                hideViewModal();
            });

            // Close modals on outside click
            window.addEventListener('click', function(event) {
                if (event.target == deleteModal) {
                    hideDeleteModal();
                    applicationIdToDelete = null;
                }
                if (event.target == viewModal) {
                    hideViewModal();
                }
            });

            // Close modals on pressing escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    hideDeleteModal();
                    hideViewModal();
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
</body>

</html>