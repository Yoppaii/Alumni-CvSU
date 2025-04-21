<?php
require_once('main_db.php');

// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug database connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = "SELECT `id`, `username`, `email`, `password`, `created_at`, `session_token`, `two_factor_auth`
        FROM `users` WHERE `is_archived` = 1 ORDER BY created_at DESC";
$result = $mysqli->query($sql);

// Debug SQL query execution
if (!$result) {
    die("Query failed: " . $mysqli->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>
<style>
    :root {
        --primary-color: #10b981;
        --primary-dark: #059669;
        --secondary-color: #64748b;
        --secondary-hover: #475569;
        --danger-color: #ef4444;
        --danger-hover: #dc2626;
        --success-color: #10b981;
        --success-hover: #059669;
        --warning-color: #f59e0b;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --bg-primary: #ffffff;
        --radius-md: 0.375rem;
        --radius-lg: 0.75rem;
    }

    .umain-container {
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        width: 100%;
        border-radius: 20px;
    }

    .usr-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .usr-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
    }

    .usr-search {
        width: 300px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: border-color 0.2s ease;
    }

    .usr-search:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .usr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .usr-table th {
        background: #f8fafc;
        padding: 1rem;
        text-align: left;
        color: #64748b;
        font-weight: 500;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .usr-table td {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
    }

    .usr-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .usr-table tbody tr:last-child td {
        border-bottom: none;
    }

    .usr-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .usr-badge-success {
        background-color: #dcfce7;
        color: #15803d;
    }

    .usr-badge-warning {
        background-color: #fef3c7;
        color: #d97706;
    }

    .usr-timestamp {
        color: #64748b;
        font-size: 0.75rem;
    }

    .usr-email {
        color: #2563eb;
        text-decoration: none;
    }

    .usr-email:hover {
        text-decoration: underline;
    }

    .usr-2fa-enabled {
        color: #15803d;
    }

    .usr-2fa-disabled {
        color: #dc2626;
    }

    .usr-active {
        color: #15803d;
    }

    .usr-inactive {
        color: #dc2626;
    }

    .usr-restore-btn {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .usr-restore-btn:hover {
        background-color: var(--primary-dark);
    }

    .usr-reactivate-btn {
        background-color: #10b981;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .usr-reactivate-btn:hover {
        background-color: #059669;
    }

    /* Generic confirm dialog styles */
    /* Updated Confirm dialog styles */
    .confirm-dialog {
        display: flex;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .confirm-dialog.active {
        opacity: 1;
        visibility: visible;
    }

    .confirm-content {
        background: var(--white);
        border-radius: 0.75rem;
        width: 100%;
        max-width: 500px;
        margin: auto;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: translateY(-20px);
        transition: transform 0.3s ease;
    }

    .confirm-dialog.active .confirm-content {
        transform: translateY(0);
    }

    .confirm-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        background-color: var(--bg-light);
        border-bottom: 1px solid var(--border-color);
    }

    .confirm-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .confirm-close {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        font-size: 1.5rem;
        color: var(--text-light);
        cursor: pointer;
        background: none;
        border: none;
        border-radius: 9999px;
        transition: all 0.2s ease;
        padding: 0;
        line-height: 1;
    }

    .confirm-close:hover {
        color: var(--text-dark);
        background-color: rgba(0, 0, 0, 0.05);
    }

    .confirm-body {
        padding: 1.5rem;
        color: var(--text-dark);
    }

    .confirm-body p {
        margin-top: 0;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .confirm-body p:last-child {
        margin-bottom: 0;
    }

    .confirm-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem;
        background-color: var(--bg-light);
        border-top: 1px solid var(--border-color);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-secondary {
        background-color: #e2e8f0;
        color: var(--text-dark);
    }

    .btn-secondary:hover {
        background-color: #cbd5e1;
    }

    .btn-danger {
        background-color: var(--danger-color);
        color: white;
    }

    .btn-danger:hover {
        background-color: #dc2626;
    }

    .btn-success {
        background-color: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background-color: #059669;
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

    /* Responsive styles */
    @media (max-width: 992px) {
        .usr-search {
            width: 250px;
        }
    }

    @media (max-width: 768px) {
        .umain-container {
            padding: 1rem;
        }

        .usr-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .usr-search {
            width: 100%;
        }

        .usr-table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .usr-table th,
        .usr-table td {
            min-width: 120px;
        }

        #notificationContainer {
            top: 10px;
            right: 10px;
            left: 10px;
            width: auto;
        }
    }

    .status-btn {
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .status-active {
        background-color: #dcfce7;
        color: #15803d;
    }

    .status-active:hover {
        background-color: #bbf7d0;
    }

    .status-inactive {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .status-inactive:hover {
        background-color: #fecaca;
    }

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
</style>

<body>
    <!-- Notification Container -->
    <div id="notificationContainer"></div>
    <div class="umain-container">
        <div class="usr-header">
            <h1 class="usr-title">User Records</h1>
            <input type="text" class="usr-search" id="userSearch" placeholder="Search users...">
        </div>
        <table class="usr-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>2FA Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-user-id="<?php echo htmlspecialchars($row['id']); ?>">
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="usr-email">
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="usr-timestamp">
                                    <?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['two_factor_auth']): ?>
                                    <span class="usr-2fa-enabled">Enabled</span>
                                <?php else: ?>
                                    <span class="usr-2fa-disabled">Disabled</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <button class="usr-restore-btn" onclick="userAction(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')">Actions</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            No users found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal HTML Structure -->
    <div class="AL-modal-overlay" id="userActionModal">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon" id="modalIcon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AL-modal-title" id="modalTitle">User Action</h3>
            </div>
            <div class="AL-modal-content" id="modalContent">
                Are you sure you want to perform this action?
            </div>
            <div class="AL-modal-actions">
                <button class="AL-modal-btn AL-modal-btn-secondary" data-action="cancel">Cancel</button>
                <button class="AL-modal-btn AL-modal-btn-success" id="restoreBtn" data-action="restore">Restore</button>
                <button class="AL-modal-btn AL-modal-btn-danger" id="deleteBtn" data-action="delete">Delete Permanently</button>
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
                    Are you absolutely sure you want to permanently delete this user? 
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
        // Search functionality
        document.getElementById('userSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.usr-table tbody tr');

            rows.forEach(row => {
                const username = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const shouldShow = username.includes(searchTerm) || email.includes(searchTerm);

                row.style.display = shouldShow ? '' : 'none';
            });
        });

        function showUserActionModal(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('userActionModal');
                const modalIcon = document.getElementById('modalIcon');
                const modalTitle = document.getElementById('modalTitle');
                const modalContent = document.getElementById('modalContent');
                const restoreBtn = document.getElementById('restoreBtn');
                const deleteBtn = document.getElementById('deleteBtn');
                const cancelBtn = modal.querySelector('[data-action="cancel"]');

                // Set modal content based on primary action
                if (options.action === 'restore') {
                    modalIcon.className = 'AL-modal-icon success';
                    modalIcon.innerHTML = '<i class="fas fa-undo"></i>';
                    modalTitle.textContent = 'Restore User';
                    modalContent.innerHTML = `
                    <p>Are you sure you want to restore user <strong>${options.username}</strong>?</p>
                    <p>Restored users will appear in the main user list.</p>
                    <br>
                    <p><strong>Warning:</strong> You can also permanently delete this user. This action cannot be undone.</p>
                `;
                } else if (options.action === 'delete') {
                    modalIcon.className = 'AL-modal-icon danger';
                    modalIcon.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    modalTitle.textContent = 'Delete User Permanently';
                    modalContent.innerHTML = `
                    <p><strong>Warning:</strong> You are about to permanently delete user <strong>${options.username}</strong>.</p>
                    <p>This action cannot be undone and all user data will be completely removed from the system.</p>
                    <p>Are you absolutely sure you want to proceed?</p>
                `;
                    // Hide restore button for delete-focused action
                    restoreBtn.style.display = 'none';
                }

                // Show the modal
                modal.classList.add('active');

                // Handle button clicks
                const cleanup = () => {
                    modal.classList.remove('active');
                    restoreBtn.style.display = ''; // Reset display
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
                    // Show a double confirmation for delete
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
        // Update the userAction function to work with the new modal system
        async function userAction(userId, username, initialAction = 'restore') {
            const action = await showUserActionModal({
                action: initialAction,
                username: username,
                userId: userId
            });

            if (action === 'restore') {
                restoreUser(userId);
            } else if (action === 'delete') {
                // Now calls our new deleteUserPermanently function with modal confirmation
                deleteUserPermanently(userId);
            }
        }
        // Function to restore a user
        function restoreUser(userId) {
            fetch('/Alumni-CvSU/admin/archives/restore_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `userId=${userId}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove the user row from the table
                        const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
                        if (userRow) {
                            userRow.style.animation = 'slideOut 0.3s ease-out forwards';
                            setTimeout(() => {
                                userRow.remove();
                            }, 300);
                        }

                        // Show success notification
                        showNotification('User has been restored successfully.', 'success');
                    } else {
                        showNotification(`Failed to restore user: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while restoring the user: ' + error.message, 'error');
                });
        }

        // Modify the deleteUserPermanently function to use the new modal
        function deleteUserPermanently(userId) {
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
                    performDeleteUser(userId);
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
        function performDeleteUser(userId) {
            fetch('/Alumni-CvSU/admin/archives/delete_user_permanent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `userId=${encodeURIComponent(userId)}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
                        if (userRow) {
                            userRow.style.animation = 'slideOut 0.3s ease-out forwards';
                            setTimeout(() => {
                                userRow.remove();
                            }, 300);
                        }
                        showNotification('User has been permanently deleted.', 'success');
                    } else {
                        showNotification(`Failed to delete user: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting the user: ' + error.message, 'error');
                });
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationContainer');
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
    </script>



</body>

</html>