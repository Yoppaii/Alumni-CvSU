<?php
require_once 'main_db.php';

if (isset($_POST['delete_id'])) {
    $id = $mysqli->real_escape_string($_POST['delete_id']);
    $deleteQuery = "DELETE FROM events WHERE id = ?";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>showEVNotification('success', 'Event deleted successfully!', 'Success');</script>";
    } else {
        echo "<script>showEVNotification('error', 'Error deleting event!', 'Error');</script>";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $day = $mysqli->real_escape_string($_POST['day']);
    $month = $mysqli->real_escape_string($_POST['month']);
    $title = $mysqli->real_escape_string($_POST['title']);
    $venue = $mysqli->real_escape_string($_POST['venue']);
    $description = $mysqli->real_escape_string($_POST['description']);

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        $id = $mysqli->real_escape_string($_POST['edit_id']);
        $query = "UPDATE events SET day=?, month=?, title=?, venue=?, description=? WHERE id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssssi", $day, $month, $title, $venue, $description, $id);
    } else {
        $query = "INSERT INTO events (day, month, title, venue, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssss", $day, $month, $title, $venue, $description);
    }

    if ($stmt->execute()) {
        echo "<script>showEVNotification('success', 'Event " . (isset($_POST['edit_id']) ? 'updated' : 'posted') . " successfully!', 'Success');</script>";
    } else {
        echo "<script>showEVNotification('error', 'Error saving event!', 'Error');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
    <style>
        .EV-container {
            display: flex;
            gap: 1rem;
            width: 100%;
            margin: 0 auto;
        }

        .EV-form-section {
            flex: 1;
            min-width: 0;
        }

        .EV-recent-section {
            width: 400px;
            min-width: 400px;
        }

        .EV-form {
            background-color: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }

        .EV-group {
            margin-bottom: 1.5rem;
        }

        .EV-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .EV-select {
            width: 200px;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .EV-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .EV-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 150px;
            resize: vertical;
        }

        .EV-event-date {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            text-align: center;
            width: fit-content;
        }

        .EV-event-item {
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 1rem;
            transition: var(--transition);
            border: 1px solid #e2e8f0;
        }

        .EV-event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .EV-venue {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #10b981;
            color: white;
            margin-top: 0.5rem;
        }

        .EV-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .EV-submit:hover {
            background-color: var(--primary-hover);
        }

        .EV-actions {
            display: flex;
            gap: 0.5rem;
        }

        .EV-btn {
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

        .EV-btn-edit {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .EV-btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .EV-btn-delete {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        .EV-btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .EV-modal-overlay {
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

        .EV-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .EV-modal {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            width: 90%;
            max-width: 400px;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .EV-modal-overlay.active .EV-modal {
            transform: translateY(0);
        }

        .EV-modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .EV-modal-icon {
            width: 24px;
            height: 24px;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .EV-modal-icon.success {
            background-color: var(--success-color);
            color: white;
        }

        .EV-modal-icon.error {
            background-color: var(--danger-color);
            color: white;
        }

        .EV-modal-icon.warning {
            background-color: var(--warning-color);
            color: white;
        }

        .EV-modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .EV-modal-content {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .EV-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .EV-modal-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .EV-modal-btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .EV-modal-btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .EV-modal-btn-secondary {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
        }

        .EV-modal-btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .EV-modal-btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .EV-modal-btn-danger:hover {
            background-color: #dc2626;
        }

        .EV-toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            background: var(--bg-primary);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1000;
            transform: translateX(120%);
            transition: all 0.3s ease;
        }

        .EV-toast.show {
            transform: translateX(0);
        }

        .EV-toast-icon {
            flex-shrink: 0;
        }

        .EV-toast-content {
            flex-grow: 1;
        }

        .EV-toast-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .EV-toast-message {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .EV-recent-container {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        [data-theme="dark"] .EV-select,
        [data-theme="dark"] .EV-input,
        [data-theme="dark"] .EV-textarea {
            border-color: #334155;
        }

        [data-theme="dark"] .EV-event-item {
            border-color: #334155;
        }

        [data-theme="dark"] .EV-btn-edit {
            background-color: rgba(16, 185, 129, 0.2);
        }

        [data-theme="dark"] .EV-btn-delete {
            background-color: rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 1024px) {
            .EV-container {
                flex-direction: column;
            }

            .EV-recent-section {
                width: 100%;
                min-width: 0;
            }

            .EV-form {
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 640px) {
            .EV-event-header {
                flex-direction: column;
                gap: 1rem;
            }

            .EV-actions {
                align-self: flex-end;
            }

            .EV-form {
                padding: 1rem;
            }

            .EV-select {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="EV-container">
        <div class="EV-form-section">
            <div class="EV-form">
                <h2 class="mb-4 text-xl font-semibold">Post New Event</h2>
                <form method="POST" action="" id="EV-form">
                    <input type="hidden" name="edit_id" id="edit_id">

                    <div class="EV-group">
                        <label class="EV-label">Day</label>
                        <input type="number" name="day" min="1" max="31" class="EV-input" required placeholder="Enter day (1-31)">
                    </div>

                    <div class="EV-group">
                        <label class="EV-label">Month</label>
                        <select name="month" class="EV-select" required>
                            <option value="">Select Month</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select>
                    </div>

                    <div class="EV-group">
                        <label class="EV-label">Title</label>
                        <input type="text" name="title" class="EV-input" required placeholder="Enter event title">
                    </div>

                    <div class="EV-group">
                        <label class="EV-label">Venue</label>
                        <input type="text" name="venue" class="EV-input" required placeholder="Enter event venue">
                    </div>

                    <div class="EV-group">
                        <label class="EV-label">Description</label>
                        <textarea name="description" class="EV-textarea" required placeholder="Enter event description"></textarea>
                    </div>

                    <div class="EV-button-group">
                        <button type="submit" class="EV-submit" id="EV-submit-btn">Post Event</button>
                        <button type="button" class="EV-submit" id="EV-reset-btn" style="background-color: var(--secondary-color); margin-left: 0.5rem;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="EV-recent-section">
            <div class="EV-recent-container">
                <h3 class="mb-4 text-xl font-semibold">Upcoming Events</h3>
                <?php
                $query = "SELECT * FROM events ORDER BY 
                     CASE month 
                        WHEN 'January' THEN 1 
                        WHEN 'February' THEN 2 
                        WHEN 'March' THEN 3 
                        WHEN 'April' THEN 4 
                        WHEN 'May' THEN 5 
                        WHEN 'June' THEN 6 
                        WHEN 'July' THEN 7 
                        WHEN 'August' THEN 8 
                        WHEN 'September' THEN 9 
                        WHEN 'October' THEN 10 
                        WHEN 'November' THEN 11 
                        WHEN 'December' THEN 12 
                     END, day ASC LIMIT 5";
                $result = $mysqli->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="EV-event-item">';
                        echo '<div class="EV-event-header">';
                        echo '<div class="EV-event-date">';
                        echo htmlspecialchars($row['day']) . ' ' . htmlspecialchars($row['month']);
                        echo '</div>';
                        echo '<div class="EV-actions">';
                        echo '<button onclick="editEvent(' . $row['id'] . ', ' . $row['day'] . ', \'' . htmlspecialchars($row['month'], ENT_QUOTES) . '\', \'' . htmlspecialchars($row['title'], ENT_QUOTES) . '\', \'' . htmlspecialchars($row['venue'], ENT_QUOTES) . '\', \'' . htmlspecialchars($row['description'], ENT_QUOTES) . '\')" class="EV-btn EV-btn-edit" title="Edit">';
                        echo '<i class="fas fa-edit"></i>';
                        echo '</button>';
                        echo '<button onclick="deleteEvent(' . $row['id'] . ')" class="EV-btn EV-btn-delete" title="Delete">';
                        echo '<i class="fas fa-trash"></i>';
                        echo '</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '<h4 class="text-lg font-semibold">' . htmlspecialchars($row['title']) . '</h4>';
                        echo '<div class="EV-venue">' . htmlspecialchars($row['venue']) . '</div>';
                        echo '<p class="mt-2">' . nl2br(htmlspecialchars($row['description'])) . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No events scheduled.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="EV-modal-overlay" id="EV-modal-overlay">
        <div class="EV-modal">
            <div class="EV-modal-header">
                <div class="EV-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="EV-modal-title"></h3>
            </div>
            <div class="EV-modal-content"></div>
            <div class="EV-modal-actions">
                <button class="EV-modal-btn EV-modal-btn-secondary" data-action="cancel">Cancel</button>
                <button class="EV-modal-btn EV-modal-btn-danger" data-action="confirm">Delete</button>
            </div>
        </div>
    </div>

    <script>
        // JavaScript functions for events system
        function showEVModal(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('EV-modal-overlay');
                const iconEl = modal.querySelector('.EV-modal-icon');
                const titleEl = modal.querySelector('.EV-modal-title');
                const contentEl = modal.querySelector('.EV-modal-content');
                const confirmBtn = modal.querySelector('[data-action="confirm"]');
                const cancelBtn = modal.querySelector('[data-action="cancel"]');

                iconEl.className = `EV-modal-icon ${options.type || 'warning'}`;
                iconEl.innerHTML = `<i class="fas fa-${options.icon || 'exclamation-triangle'}"></i>`;
                titleEl.textContent = options.title || '';
                contentEl.textContent = options.message || '';

                confirmBtn.textContent = options.confirmText || 'Confirm';
                confirmBtn.className = `EV-modal-btn ${options.confirmClass || 'EV-modal-btn-danger'}`;
                cancelBtn.textContent = options.cancelText || 'Cancel';

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

                const handleEscape = (e) => {
                    if (e.key === 'Escape') {
                        cleanup();
                        resolve(false);
                        document.removeEventListener('keydown', handleEscape);
                    }
                };
                document.addEventListener('keydown', handleEscape);
            });
        }

        function showEVNotification(type, message, title = '') {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };

            const toast = document.createElement('div');
            toast.className = 'EV-toast';
            toast.innerHTML = `
        <div class="EV-toast-icon">
            <i class="fas fa-${icons[type]}" style="color: var(--${type === 'error' ? 'danger' : type}-color)"></i>
        </div>
        <div class="EV-toast-content">
            ${title ? `<div class="EV-toast-title">${title}</div>` : ''}
            <div class="EV-toast-message">${message}</div>
        </div>
    `;

            document.body.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }

        async function deleteEvent(id) {
            const confirmed = await showEVModal({
                type: 'warning',
                icon: 'trash',
                title: 'Delete Event',
                message: 'Are you sure you want to delete this event? This action cannot be undone.',
                confirmText: 'Delete',
                confirmClass: 'EV-modal-btn-danger',
                cancelText: 'Cancel'
            });

            if (confirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editEvent(id, day, month, title, venue, description) {
            document.getElementById('edit_id').value = id;
            document.querySelector('[name="day"]').value = day;
            document.querySelector('[name="month"]').value = month;
            document.querySelector('[name="title"]').value = title;
            document.querySelector('[name="venue"]').value = venue;
            document.querySelector('[name="description"]').value = description;
            document.getElementById('EV-submit-btn').textContent = 'Update Event';
            document.querySelector('.EV-form').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function resetForm() {
            document.getElementById('EV-form').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('EV-submit-btn').textContent = 'Post Event';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('EV-form');
            const resetBtn = document.getElementById('EV-reset-btn');

            form.addEventListener('submit', async function(e) {
                const day = form.querySelector('[name="day"]').value;
                const month = form.querySelector('[name="month"]').value;
                const title = form.querySelector('[name="title"]').value;
                const venue = form.querySelector('[name="venue"]').value;
                const description = form.querySelector('[name="description"]').value;

                if (!day || !month || !title || !venue || !description) {
                    e.preventDefault();
                    showEVNotification('error', 'Please fill in all fields', 'Validation Error');
                    return;
                }

                // Validate day range
                if (day < 1 || day > 31) {
                    e.preventDefault();
                    showEVNotification('error', 'Day must be between 1 and 31', 'Validation Error');
                    return;
                }

                const editId = document.getElementById('edit_id').value;
                if (editId) {
                    const confirmed = await showEVModal({
                        type: 'warning',
                        icon: 'edit',
                        title: 'Update Event',
                        message: 'Are you sure you want to update this event?',
                        confirmText: 'Update',
                        confirmClass: 'EV-modal-btn-primary',
                        cancelText: 'Cancel'
                    });

                    if (!confirmed) {
                        e.preventDefault();
                    }
                }
            });

            resetBtn.addEventListener('click', function() {
                resetForm();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && document.getElementById('edit_id').value) {
                    resetForm();
                }
            });
        });
    </script>
</body>

</html>