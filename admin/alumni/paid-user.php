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
            --border-color: #e2e8f0;
            --danger-color: #ef4444;
            --success-color: #059669;
            --warning-color: #f59e0b;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --bg-light: #f8fafc;
            --white: #ffffff;
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
            margin: 1rem auto;
            max-width: 1400px;
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

        .alm-booking-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            padding: 1rem;
            overflow-y: auto;
        }

        .alm-modal-content {
            background: var(--white);
            border-radius: 0.75rem;
            margin: 2rem auto;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            animation: modalSlideIn 0.3s ease-out;
        }

        .alm-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .alm-modal-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alm-modal-close {
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.2s ease;
            background: none;
            border: none;
            padding: 0.25rem;
            border-radius: 0.375rem;
            line-height: 1;
        }

        .alm-modal-body {
            padding: 1.5rem;
        }

        .alm-button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .alm-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            transition: all 0.2s ease;
        }

        .alm-btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .alm-btn-secondary {
            background-color: var(--border-color);
            color: var(--text-dark);
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

            .alm-btn {
                width: 100%;
                justify-content: center;
            }
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
            <h2><i class="fas fa-credit-card"></i> Alumni ID Applications <span class="alm-booking-count"><?php echo $totalCount; ?></span></h2>
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
                        <?php while($application = $applicationsResult->fetch_assoc()): ?>
                            <tr data-user-id="<?php echo htmlspecialchars($application['user_id']); ?>">
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
                                    <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($application['email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($application['course']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['year_graduated']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['highschool_graduated']); ?></td>
                                <td class="alm-hide-mobile"><?php echo htmlspecialchars($application['membership_type']); ?></td>
                                <td>₱<?php echo number_format($application['price'], 2); ?></td>
                                <td>
                                    <span class="alm-status-badge alm-status-paid">
                                        <i class="fas fa-dollar-sign"></i> Paid
                                    </span>
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

    <div id="almDeleteConfirmModal" class="alm-booking-modal">
        <div class="alm-modal-content" style="max-width: 400px;">
            <div class="alm-modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
                <span class="alm-modal-close">&times;</span>
            </div>
            <div class="alm-modal-body">
                <p id="almDeleteConfirmMessage" class="text-center mb-4">
                    Are you sure you want to delete this application? This action cannot be undone.
                </p>
                <div class="alm-button-group">
                    <button id="almDeleteConfirmBtn" class="alm-btn alm-btn-primary" style="background-color: #ef4444;">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                    <button id="almDeleteCancelBtn" class="alm-btn alm-btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('almDeleteConfirmModal');

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

            window.deleteApplication = function(appId, event) {
                event.stopPropagation();
                
                const confirmBtn = document.getElementById('almDeleteConfirmBtn');
                const cancelBtn = document.getElementById('almDeleteCancelBtn');
                const closeBtn = deleteModal.querySelector('.alm-modal-close');
                
                deleteModal.style.display = "block";

                const handleDelete = async () => {
                    try {
                        deleteModal.style.display = "none";
                        showLoading('Deleting application...');

                        const formData = new FormData();
                        formData.append('application_id', appId);

                        const response = await fetch('/Alumni-CvSU/admin/delete-id-application.php', {
                            method: 'POST',
                            body: formData
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();
                        
                        if (data.success) {
                            const row = event.target.closest('tr');
                            if (row) {
                                row.remove();
                            }
                            hideLoading();
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            throw new Error(data.message || 'Failed to delete application');
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        hideLoading();
                        alert('Failed to delete application: ' + error.message);
                    }
                };

                confirmBtn.onclick = handleDelete;
                cancelBtn.onclick = () => deleteModal.style.display = "none";
                closeBtn.onclick = () => deleteModal.style.display = "none";
            };

            window.onclick = function(event) {
                if (event.target === deleteModal) {
                    deleteModal.style.display = "none";
                }
            };

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && deleteModal.style.display === "block") {
                    deleteModal.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>