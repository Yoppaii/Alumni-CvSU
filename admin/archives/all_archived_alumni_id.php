<?php
require_once 'main_db.php';
// Updated query to only show archived records
$query = "SELECT `alumni_id`, `alumni_id_card_no`, `last_name`, `first_name`, `middle_name`, `membership_type`, `created_at`
          FROM `alumni`
          WHERE `is_archived` = 1
          ORDER BY last_name ASC";
$result = $mysqli->query($query);
// Function to safely escape and format output
function formatOutput($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Management</title>
    <!-- CSS styles remain the same (omitted for brevity) -->
    <style>
        /* CSS styles from your original code */
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

        .AL-container {
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            border-radius: 20px;
        }

        .AL-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .AL-search {
            width: 300px;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .AL-table-container {
            overflow-x: auto;
        }

        .AL-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .AL-table th,
        .AL-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .AL-table th {
            background-color: var(--bg-secondary);
            font-weight: 600;
            color: var(--text-primary);
        }

        .AL-table tr:hover {
            background-color: var(--bg-secondary);
        }

        .AL-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .AL-badge-premium {
            background-color: #3b82f6;
            color: white;
        }

        .AL-badge-lifetime {
            background-color: #10b981;
            color: white;
        }

        .AL-badge-honorary {
            background-color: #8b5cf6;
            color: white;
        }

        .AL-actions {
            display: flex;
            gap: 0.5rem;
        }

        .AL-btn {
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
            height: auto;
            font-size: 0.875rem;
        }

        .AL-btn-restore {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .AL-btn-restore:hover {
            background-color: var(--primary-dark);
        }

        .AL-expiry-soon {
            color: var(--warning-color);
            font-weight: 500;
        }

        .AL-expiry-expired {
            color: var(--danger-color);
            font-weight: 500;
        }

        .AL-expiry-healthy {
            color: var(--success-color);
            font-weight: 500;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div id="notificationContainer" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>
    <div class="AL-container">
        <div class="AL-content">
            <div class="AL-header">
                <h2>Archived Alumni ID Cards</h2>
                <input type="search" id="alumni-search" class="AL-search" placeholder="Search Alumni...">
            </div>
            <div class="AL-table-container">
                <table class="AL-table">
                    <thead>
                        <tr>
                            <th>Alumni ID</th>
                            <th>Alumni ID Card No.</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Membership Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr data-alumni-id="<?php echo $row['alumni_id']; ?>"
                                data-alumni-id-card-no="<?php echo $row['alumni_id_card_no']; ?>">
                                <td>
                                    <?php echo formatOutput($row['alumni_id']); ?>
                                </td>
                                <td>
                                    <?php echo formatOutput($row['alumni_id_card_no']); ?>
                                </td>
                                <td>
                                    <?php echo formatOutput($row['last_name']); ?>
                                </td>
                                <td>
                                    <?php echo formatOutput($row['first_name']); ?>
                                </td>
                                <td>
                                    <?php echo formatOutput($row['middle_name']); ?>
                                </td>
                                <td>
                                    <?php echo formatOutput($row['membership_type']); ?>
                                </td>
                                <td class="AL-actions">
                                    <button class="AL-btn AL-btn-restore"
                                        onclick="alumniAction('<?php echo $row['alumni_id']; ?>', '<?php echo $row['alumni_id_card_no']; ?>', '<?php echo formatOutput($row['last_name']); ?>, <?php echo formatOutput($row['first_name']); ?>')">
                                        Action
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal HTML Structure -->
    <div class="AL-modal-overlay" id="alumniActionModal">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon" id="modalIcon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AL-modal-title" id="modalTitle">Alumni Action</h3>
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
                    Are you absolutely sure you want to permanently delete this alumni? 
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
        document.getElementById('alumni-search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.AL-table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        function showAlumniActionModal(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('alumniActionModal');
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
                    modalTitle.textContent = 'Restore Alumni';
                    modalContent.innerHTML = `
                <p>Are you sure you want to restore alumni <strong>${options.name}</strong>?</p>
                <p>Restored alumni records will appear in the main alumni list.</p>
                <br>
                <p><strong>Warning:</strong> You can also permanently delete this alumni record. This action cannot be undone.</p>
            `;
                } else if (options.action === 'delete') {
                    modalIcon.className = 'AL-modal-icon danger';
                    modalIcon.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    modalTitle.textContent = 'Delete Alumni Permanently';
                    modalContent.innerHTML = `
                <p><strong>Warning:</strong> You are about to permanently delete alumni <strong>${options.name}</strong>.</p>
                <p>This action cannot be undone and all alumni data will be completely removed from the system.</p>
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

        // Update the alumniAction function to work with the new modal system
        async function alumniAction(alumniId, alumniIdCardNo, name, initialAction = 'restore') {
            const action = await showAlumniActionModal({
                action: initialAction,
                name: name,
                alumniId: alumniId,
                alumniIdCardNo: alumniIdCardNo
            });

            if (action === 'restore') {
                restoreAlumni(alumniId);
            } else if (action === 'delete') {
                // Now calls our new deleteAlumniPermanently function with modal confirmation
                deleteAlumniPermanently(alumniIdCardNo);
            }
        }

        // Function to restore an alumni
        function restoreAlumni(alumniId) {
            fetch('/Alumni-CvSU/admin/archives/restore_alumni_id.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `alumni_id=${alumniId}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove the alumni row from the table
                        const alumniRow = document.querySelector(`tr[data-alumni-id="${alumniId}"]`);
                        if (alumniRow) {
                            alumniRow.style.animation = 'slideOut 0.3s ease-out forwards';
                            setTimeout(() => {
                                alumniRow.remove();
                            }, 300);
                        }
                        // Show success notification
                        showNotification('Alumni has been restored successfully.', 'success');
                    } else {
                        showNotification(`Failed to restore alumni: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while restoring the alumni: ' + error.message, 'error');
                });
        }

        // Modify the deleteAlumniPermanently function to use the new modal
        function deleteAlumniPermanently(alumniIdCardNo) {
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
                    performDeleteAlumni(alumniIdCardNo);
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
        function performDeleteAlumni(alumniIdCardNo) {
            fetch('/Alumni-CvSU/admin/archives/delete_id_permanent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `alumni_id_card_no=${encodeURIComponent(alumniIdCardNo)}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const alumniRow = document.querySelector(`tr[data-alumni-id-card-no="${alumniIdCardNo}"]`);
                        if (alumniRow) {
                            alumniRow.style.animation = 'slideOut 0.3s ease-out forwards';
                            setTimeout(() => {
                                alumniRow.remove();
                            }, 300);
                        }
                        showNotification('Alumni has been permanently deleted.', 'success');
                    } else {
                        showNotification(`Failed to delete alumni: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting the alumni: ' + error.message, 'error');
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