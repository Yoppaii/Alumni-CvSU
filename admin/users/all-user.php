<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        --success-color: #059669;
        --warning-color: #f59e0b;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --bg-light: #f8fafc;
        --white: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --bg-primary: #ffffff;
        --radius-md: 0.375rem;
        --radius-lg: 0.5rem;
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

    .usr-delete-btn {
        background-color: #ef4444;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .usr-delete-btn:hover {
        background-color: #dc2626;
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

    /* Modal styles based on AL-modal */
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
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
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
        width: 24px;
        height: 24px;
        margin-right: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
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
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .AL-modal-content {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
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
    }

    .AL-modal-btn-secondary {
        background-color: var(--secondary-color);
        color: white;
    }

    .AL-modal-btn-danger {
        background-color: var(--danger-color);
        color: white;
    }

    .AL-modal-btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .AL-modal-btn-secondary:hover {
        background-color: var(--secondary-hover);
    }

    .AL-modal-btn-danger:hover {
        background-color: var(--danger-hover);
    }

    .AL-modal-btn-primary:hover {
        background-color: var(--primary-dark);
    }
</style>

<body>
    <?php
    require_once('main_db.php');

    // Add error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Debug database connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $sql = "SELECT `id`, `username`, `email`, `password`, `created_at`, `session_token`, `two_factor_auth`, `is_active` 
            FROM `users` WHERE `is_archived` = 0 ORDER BY created_at DESC";
    $result = $mysqli->query($sql);

    // Debug SQL query execution
    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }
    ?>

    <!-- Notification Container -->
    <div id="notificationContainer"></div>

    <div class="umain-container">
        <div class="usr-header">
            <h1 class="usr-title">User Management</h1>
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
                    <th>Status</th>
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
                                <button class="status-btn <?php echo $row['is_active'] ? 'status-active' : 'status-inactive'; ?>"
                                    onclick="confirmToggleStatus(<?php echo $row['id']; ?>, <?php echo $row['is_active'] ? 'true' : 'false'; ?>, '<?php echo htmlspecialchars($row['username']); ?>')">
                                    <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                </button>
                            </td>
                            <td>
                                <button class="usr-delete-btn" onclick="confirmDeleteUser(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')">Delete</button>
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

    <!-- New Confirmation Modal using AL-modal styling -->
    <div class="AL-modal-overlay" id="confirmModal">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon" id="confirmModalIcon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AL-modal-title" id="confirmModalTitle">Confirm Action</h3>
            </div>
            <div class="AL-modal-content" id="confirmModalMessage">
                Are you sure you want to proceed with this action?
            </div>
            <div class="AL-modal-actions">
                <button class="AL-modal-btn AL-modal-btn-secondary" id="cancelActionBtn">Cancel</button>
                <button class="AL-modal-btn AL-modal-btn-danger" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>

    <script>
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

        // Modal functionality using the AL-modal approach
        const confirmationModal = {
            modal: document.getElementById('confirmModal'),
            title: document.getElementById('confirmModalTitle'),
            icon: document.getElementById('confirmModalIcon'),
            message: document.getElementById('confirmModalMessage'),
            confirmBtn: document.getElementById('confirmActionBtn'),
            cancelBtn: document.getElementById('cancelActionBtn'),
            currentAction: null,
            currentParams: null,

            show(options) {
                // Set modal content based on options
                this.title.textContent = options.title || 'Confirm Action';
                this.message.innerHTML = options.message || 'Are you sure you want to proceed with this action?';

                // Update icon
                this.icon.className = 'AL-modal-icon';
                if (options.icon) {
                    const iconElement = this.icon.querySelector('i');
                    iconElement.className = options.icon;

                    // Set icon background color based on type
                    if (options.confirmColor === '#ef4444' || options.confirmColor === 'var(--danger-color)') {
                        this.icon.classList.add('danger');
                    } else if (options.confirmColor === '#22c55e' || options.confirmColor === 'var(--primary-color)') {
                        this.icon.classList.add('success');
                    } else {
                        this.icon.classList.add('warning');
                    }
                }

                // Set confirm button text and color
                this.confirmBtn.textContent = options.confirmText || 'Confirm';
                if (options.confirmColor) {
                    this.confirmBtn.style.backgroundColor = options.confirmColor;
                } else {
                    this.confirmBtn.style.backgroundColor = ''; // Reset to default
                }

                // Store the action to be executed when confirmed
                this.currentAction = options.action;
                this.currentParams = options.params;

                // Show the modal
                this.modal.classList.add('active');

                // Set up event listeners
                this.setupEventListeners();
            },

            hide() {
                this.modal.classList.remove('active');
                this.removeEventListeners();
            },

            setupEventListeners() {
                this.confirmBtn.addEventListener('click', this.handleConfirm);
                this.cancelBtn.addEventListener('click', this.handleCancel);
                this.modal.addEventListener('click', this.handleOutsideClick);
            },

            removeEventListeners() {
                this.confirmBtn.removeEventListener('click', this.handleConfirm);
                this.cancelBtn.removeEventListener('click', this.handleCancel);
                this.modal.removeEventListener('click', this.handleOutsideClick);
            },

            handleConfirm: function() {
                const action = confirmationModal.currentAction;
                const params = confirmationModal.currentParams;

                confirmationModal.hide();

                if (action && typeof action === 'function') {
                    action(params);
                }
            },

            handleCancel: function() {
                confirmationModal.hide();
            },

            handleOutsideClick: function(e) {
                if (e.target === confirmationModal.modal) {
                    confirmationModal.hide();
                }
            }
        };

        // Bind the handlers properly for the modal
        confirmationModal.handleConfirm = confirmationModal.handleConfirm.bind(confirmationModal);
        confirmationModal.handleCancel = confirmationModal.handleCancel.bind(confirmationModal);
        confirmationModal.handleOutsideClick = confirmationModal.handleOutsideClick.bind(confirmationModal);

        function confirmDeleteUser(userId, username) {
            confirmationModal.show({
                title: 'Delete User',
                icon: 'fas fa-trash',
                message: `Are you sure you want to delete user <strong>${username}</strong>?`,
                confirmText: 'Delete',
                confirmColor: '#ef4444',
                action: archiveUser,
                params: userId
            });
        }

        function confirmToggleStatus(userId, isCurrentlyActive, username) {
            const newStatus = isCurrentlyActive ? 'inactive' : 'active';

            confirmationModal.show({
                title: `Change User Status`,
                icon: isCurrentlyActive ? 'fas fa-user-slash' : 'fas fa-user-check',
                message: `Are you sure you want to mark <strong>${username}</strong> as ${newStatus}?`,
                confirmText: `Mark as ${newStatus}`,
                confirmColor: isCurrentlyActive ? '#ef4444' : '#22c55e',
                action: toggleStatus,
                params: {
                    userId,
                    isCurrentlyActive
                }
            });
        }

        // Toggle user status function
        function toggleStatus(params) {
            const {
                userId,
                isCurrentlyActive
            } = params;
            const newStatus = isCurrentlyActive ? 'inactive' : 'active';

            // Send AJAX request to update status
            fetch('/Alumni-CvSU/admin/users/update_user_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `userId=${userId}&isActive=${!isCurrentlyActive}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the button in the UI
                        const statusBtn = document.querySelector(`tr[data-user-id="${userId}"] .status-btn`);
                        statusBtn.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        statusBtn.classList.remove(isCurrentlyActive ? 'status-active' : 'status-inactive');
                        statusBtn.classList.add(isCurrentlyActive ? 'status-inactive' : 'status-active');
                        statusBtn.onclick = function() {
                            confirmToggleStatus(userId, !isCurrentlyActive,
                                document.querySelector(`tr[data-user-id="${userId}"]`).cells[1].textContent);
                        };

                        // Show success notification
                        showNotification(`User status updated to ${newStatus}.`, 'success');
                    } else {
                        showNotification('Failed to update user status.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while updating user status.', 'error');
                });
        }

        // Archive user function
        function archiveUser(userId) {
            fetch('/Alumni-CvSU/admin/users/archive_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `userId=${userId}`
                })
                .then(response => {
                    // Add error checking for the response
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
                        showNotification('User has been deleted successfully.', 'success');
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