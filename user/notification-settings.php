<?php
if (!isset($_SESSION)) {
    session_start();
}

$notifications_query = "SELECT b.id, b.reference_number, b.room_number, b.status, b.created_at 
                       FROM bookings b 
                       WHERE b.user_id = ? 
                       AND b.status IN ('confirmed', 'cancelled', 'completed') 
                       ORDER BY b.created_at DESC";

$stmt = $mysqli->prepare($notifications_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
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

    .notifications-card {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .notifications-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notifications-header h1 {
        font-size: 24px;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .notifications-header h1 i {
        color: var(--primary-color);
    }

    .notifications-content {
        padding: 24px;
    }

    .notification-filters {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .filter-btn {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: white;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .filter-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .notifications-list {
        max-width: auto;
        margin: 20px auto;
        padding: 20px;
    }

    .notification-empty {
        text-align: center;
        padding: 20px;
        color: #666;
        background: #f9f9f9;
        border-radius: 8px;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        background: white;
        border: 1px solid #e5e7eb;
    }

    .notification-item.confirmed {
        border-left: 4px solid #10B981;
        background: #F0FDF4;
    }

    .notification-item.cancelled {
        border-left: 4px solid #EF4444;
        background: #FEF2F2;
    }

    .notification-item.completed {
        border-left: 4px solid #3B82F6;
        background: #EFF6FF;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
    }

    .notification-icon.confirmed {
        background: #D1FAE5;
        color: #059669;
    }

    .notification-icon.cancelled {
        background: #FEE2E2;
        color: #DC2626;
    }

    .notification-icon.completed {
        background: #DBEAFE;
        color: #2563EB;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-weight: 600;
        margin-bottom: 4px;
        color: #111827;
    }

    .notification-message {
        color: #6B7280;
        margin-bottom: 8px;
    }

    .notification-timestamp {
        font-size: 12px;
        color: #9CA3AF;
    }

    .action-btn {
        padding: 8px;
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-btn:hover {
        color: var(--primary-color);
    }

    .load-more {
        display: block;
        width: 100%;
        padding: 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .load-more:hover {
        background: #f3f4f6;
        border-color: var(--primary-color);
    }

    .notifications-empty {
        text-align: center;
        padding: 40px;
        color: #6b7280;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        body {
            padding: 10px;
        }

        .notifications-header,
        .notifications-content {
            padding: 16px;
        }

        .notification-filters {
            padding-bottom: 8px;
        }

        .notification-item {
            flex-direction: column;
        }

        .notification-actions {
            margin-left: 0;
            margin-top: 12px;
            justify-content: flex-end;
        }
    }
</style>

<body>
    <div class="notifications-card">
        <div class="notifications-header">
            <h1><i class="fas fa-bell"></i> Notifications</h1>
            <button class="action-btn" title="Mark all as read">
                <i class="fas fa-check-double"></i>
            </button>
        </div>

        <div class="notifications-content">
            <!-- <div class="notification-filters">
                <button class="filter-btn active">All</button>
                <button class="filter-btn">System</button>
                <button class="filter-btn">Account</button>
                <button class="filter-btn">Security</button>
                <button class="filter-btn">Unread</button>
            </div> -->
            <div class="notifications-list">
                <?php if (empty($notifications)): ?>
                    <div class="notification-empty">
                        No booking status notifications yet
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?php echo $notification['status']; ?>">
                            <div class="notification-icon <?php echo $notification['status']; ?>">
                                <i class="fas <?php
                                                echo $notification['status'] === 'confirmed' ? 'fa-check' : ($notification['status'] === 'cancelled' ? 'fa-times' : ($notification['status'] === 'completed' ? 'fa-flag-checkered' : 'fa-info'));
                                                ?>"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    Booking <?php echo htmlspecialchars($notification['reference_number']); ?> - <?php echo ucfirst($notification['status']); ?>
                                </div>
                                <div class="notification-message">
                                    Your booking for Room <?php echo htmlspecialchars($notification['room_number']); ?> has been <?php echo $notification['status']; ?>.
                                </div>
                                <div class="notification-timestamp">
                                    <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- <button class="load-more">Load More</button> -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            const actionButtons = document.querySelectorAll('.action-btn');
            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // const loadMoreButton = document.querySelector('.load-more');
            // loadMoreButton.addEventListener('click', function() {});

            // const notificationItems = document.querySelectorAll('.notification-item');
            // notificationItems.forEach(item => {
            //     item.addEventListener('click', function() {});
            // });
        });
    </script>
</body>

</html>