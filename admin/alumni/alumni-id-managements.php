<?php
require_once 'main_db.php';

$query = "SELECT `alumni_id`, `alumni_id_card_no`, `last_name`, `first_name`, `middle_name`, `membership_type` 
          FROM `alumni` 
          WHERE 1 
          ORDER BY last_name ASC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Management</title>
    <style>
        .AL-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem;
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
            border: 1px solid #e2e8f0;
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
            border-bottom: 1px solid #e2e8f0;
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

        .AL-badge-regular { background-color: #3b82f6; color: white; }
        .AL-badge-lifetime { background-color: #10b981; color: white; }
        .AL-badge-honorary { background-color: #8b5cf6; color: white; }

        .AL-actions {
            display: flex;
            gap: 0.5rem;
        }

        .AL-btn {
            padding: 0.25rem;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        .AL-btn-view {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .AL-btn-view:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .AL-btn-delete {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        .AL-btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
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

        .AL-modal-btn-secondary:hover {
            background-color: var(--secondary-hover);
        }

        .AL-modal-btn-danger:hover {
            background-color: var(--danger-hover);
        }

        [data-theme="dark"] .AL-search,
        [data-theme="dark"] .AL-table td,
        [data-theme="dark"] .AL-table th {
            border-color: #334155;
        }

        [data-theme="dark"] .AL-btn-delete {
            background-color: rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 768px) {
            .AL-header {
                flex-direction: column;
                gap: 1rem;
            }

            .AL-search {
                width: 100%;
            }
        }
    </style>
</head>
<body>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $fullName = $row['last_name'] . ', ' . $row['first_name'];
                                if (!empty($row['middle_name'])) {
                                    $fullName .= ' ' . $row['middle_name'][0] . '.';
                                }

                                $membershipClass = 'AL-badge-' . strtolower($row['membership_type']);
                                
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['alumni_id_card_no']) . '</td>';
                                echo '<td>' . htmlspecialchars($fullName) . '</td>';
                                echo '<td><span class="AL-badge ' . $membershipClass . '">' 
                                     . htmlspecialchars($row['membership_type']) . '</span></td>';
                                echo '<td>
                                        <div class="AL-actions">
                                            <button onclick="deleteAlumni(\'' . $row['alumni_id'] . '\')" 
                                                    class="AL-btn AL-btn-delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center">No alumni records found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="AL-modal-overlay" id="AL-modal-overlay">
        <div class="AL-modal">
            <div class="AL-modal-header">
                <div class="AL-modal-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AL-modal-title">Delete Alumni</h3>
            </div>
            <div class="AL-modal-content">
                Are you sure you want to delete this alumni record? This action cannot be undone.
            </div>
            <div class="AL-modal-actions">
                <button class="AL-modal-btn AL-modal-btn-secondary" data-action="cancel">Cancel</button>
                <button class="AL-modal-btn AL-modal-btn-danger" data-action="confirm">Delete</button>
            </div>
        </div>
    </div>


    <script>
        function showALModal(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('AL-modal-overlay');
                const confirmBtn = modal.querySelector('[data-action="confirm"]');
                const cancelBtn = modal.querySelector('[data-action="cancel"]');

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

        async function deleteAlumni(id) {
            const confirmed = await showALModal({
                type: 'warning',
                title: 'Delete Alumni',
                message: 'Are you sure you want to delete this alumni record? This action cannot be undone.'
            });

            if (confirmed) {
                // Here you would typically make an AJAX call or form submission to delete the record
                console.log('Deleting alumni:', id);
                // Add your delete logic here
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