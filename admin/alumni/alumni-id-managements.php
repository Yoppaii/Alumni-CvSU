<?php
require_once 'main_db.php';

// Updated query to include created_at field
$query = "SELECT `alumni_id`, `alumni_id_card_no`, `last_name`, `first_name`, `middle_name`, `membership_type`, `created_at` 
          FROM `alumni` 
          WHERE `is_archived` = 0 OR `is_archived` IS NULL
          ORDER BY alumni_id_card_no ASC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Management</title>
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
            width: 100%;
            margin: auto;
        }

        .AL-content {
            background-color: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
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

        .AL-btn-delete {
            background-color: #ef4444;
            color: white;
        }

        .AL-btn-delete:hover {
            background-color: var(--danger-hover);
            color: white;
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
            color: white;
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
        @media (max-width: 768px) {
            .AL-header {
                flex-direction: column;
                gap: 1rem;
            }

            .AL-search {
                width: 100%;
            }

            #notificationContainer {
                top: 10px;
                right: 10px;
                left: 10px;
                width: auto;
            }
        }

        /*status button */
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
    </style>

</head>

<body>
    <div id="notificationContainer" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

    <div class="AL-container">
        <div class="AL-content">
            <div class="AL-header">
                <h2 class="text-xl font-semibold">Alumni Directory</h2>
                <input type="text" id="alumni-search" class="AL-search" placeholder="Search alumni...">
            </div>

            <div class="AL-table-container">
                <table class="AL-table">
                    <thead>
                        <tr>
                            <th>ID Card No.</th>
                            <th>Name</th>
                            <th>Membership</th>
                            <th>Expiration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                $fullName = htmlspecialchars($row['last_name']) . ', ' . htmlspecialchars($row['first_name']);
                                if (!empty($row['middle_name'])) {
                                    $fullName .= ' ' . htmlspecialchars($row['middle_name'][0]) . '.';
                                }

                                $membershipType = htmlspecialchars($row['membership_type']);
                                $membershipClass = 'AL-badge-' . strtolower($row['membership_type']);

                                // Calculate expiration date for premium members
                                $expirationDate = "";
                                $expiryClass = "";

                                if (strtolower($membershipType) === 'premium' && !empty($row['created_at'])) {
                                    $createdDate = new DateTime($row['created_at']);
                                    $expirationDateTime = clone $createdDate;
                                    $expirationDateTime->modify('+5 years');
                                    $expirationDate = $expirationDateTime->format('M d, Y');

                                    // Calculate days remaining
                                    $today = new DateTime();
                                    $daysRemaining = $today->diff($expirationDateTime)->days;
                                    $isPast = $today > $expirationDateTime;

                                    if ($isPast) {
                                        $expiryClass = "AL-expiry-expired";
                                        $expirationDate .= " (Expired)";
                                    } elseif ($daysRemaining <= 90) { // 3 months warning
                                        $expiryClass = "AL-expiry-soon";
                                        $expirationDate .= " ({$daysRemaining} days left)";
                                    } else {
                                        $expiryClass = "AL-expiry-healthy";
                                    }
                                } elseif (strtolower($membershipType) === 'lifetime') {
                                    $expirationDate = "Lifetime Membership";
                                    $expiryClass = "AL-expiry-healthy";
                                } else {
                                    $expirationDate = "N/A";
                                }
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['alumni_id_card_no']) ?></td>
                                    <td><?= $fullName ?></td>
                                    <td>
                                        <span class="AL-badge <?= $membershipClass ?>">
                                            <?= $membershipType ?>
                                        </span>
                                    </td>
                                    <td class="<?= $expiryClass ?>">
                                        <?= $expirationDate ?>
                                    </td>
                                    <td>
                                        <div class="AL-actions">
                                            <button onclick="archiveAlumni('<?= $row['alumni_id'] ?>')"
                                                class="AL-btn AL-btn-delete" title="Archive">
                                                <span>Delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No alumni records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div class="AL-modal-overlay" id="AL-modal-overlay">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AL-modal-title">Delete Alumni</h3>
            </div>
            <div class="AL-modal-content">
                Are you sure you want to delete this alumni record? The record will be removed from the active list.
            </div>
            <div class="AL-modal-actions">
                <button class="AL-modal-btn AL-modal-btn-secondary" data-action="cancel">Cancel</button>
                <button class="AL-modal-btn AL-modal-btn-danger" data-action="confirm">Delete</button>
            </div>
        </div>
    </div>


    <script>
        // Notification system
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationContainer');
            if (!container) {
                console.warn('Notification container not found');
                return;
            }

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

        async function archiveAlumni(id) {
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
        }

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

        document.addEventListener('DOMContentLoaded', function() {
            initializeSearch();
        });
    </script>

</body>

</html>