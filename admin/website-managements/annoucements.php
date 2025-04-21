<?php
require_once 'main_db.php';
if (isset($_POST['delete_id'])) {
    $id = $mysqli->real_escape_string($_POST['delete_id']);
    $deleteQuery = "DELETE FROM announcements WHERE id = ?";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showNotification('Announcement deleted successfully!','success');
                setTimeout(function() {
                    window.location.href = '?section=Latest-Announcements';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNotification('Error deleting announcement!','error');
            }
        </script>";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $badge = $mysqli->real_escape_string($_POST['badge']);
    $title = $mysqli->real_escape_string($_POST['title']);
    $content = $mysqli->real_escape_string($_POST['content']);

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        $id = $mysqli->real_escape_string($_POST['edit_id']);
        $query = "UPDATE announcements SET badge=?, title=?, content=? WHERE id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssi", $badge, $title, $content, $id);
    } else {
        $query = "INSERT INTO announcements (badge, title, content) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sss", $badge, $title, $content);
    }

    if ($stmt->execute()) {
        $action = isset($_POST['edit_id']) ? 'updated' : 'posted';
        echo "<script>
            window.onload = function() {
                showNotification('Announcement {$action} successfully!', 'success');
                setTimeout(function() {
                    window.location.href = '?section=Latest-Announcements';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNotification('Error saving announcement!', 'error');
            }
        </script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements Management</title>
    <style>
        .AN-container {
            display: flex;
            gap: 1rem;
            width: 100%;
            margin: auto;
        }

        .AN-form-section {
            flex: 1;
            min-width: 0;
        }

        .AN-recent-section {
            width: 400px;
            min-width: 400px;
        }

        .AN-form {
            background-color: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }

        .AN-group {
            margin-bottom: 1.5rem;
        }

        .AN-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .AN-select {
            width: 200px;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .AN-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .AN-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 150px;
            resize: vertical;
        }

        .AN-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .AN-badge-new {
            background-color: #10b981;
            color: white;
        }

        .AN-badge-event {
            background-color: #f59e0b;
            color: white;
        }

        .AN-badge-update {
            background-color: #3b82f6;
            color: white;
        }

        .AN-badge-academic {
            background-color: #8b5cf6;
            color: white;
        }

        .AN-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .AN-submit:hover {
            background-color: var(--primary-hover);
        }

        .AN-recent-container {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .AN-announcement-item {
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 1rem;
            transition: var(--transition);
            border: 1px solid #e2e8f0;
        }

        .AN-announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .AN-actions {
            display: flex;
            gap: 0.5rem;
        }

        .AN-btn {
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

        .AN-btn-edit {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .AN-btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .AN-btn-delete {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        .AN-btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .AN-modal-overlay {
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

        .AN-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .AN-modal {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            width: 90%;
            max-width: 400px;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .AN-modal-overlay.active .AN-modal {
            transform: translateY(0);
        }

        .AN-modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .AN-modal-icon {
            width: 24px;
            height: 24px;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .AN-modal-icon.success {
            background-color: var(--success-color);
            color: white;
        }

        .AN-modal-icon.error {
            background-color: var(--danger-color);
            color: white;
        }

        .AN-modal-icon.warning {
            background-color: var(--warning-color);
            color: white;
        }

        .AN-modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .AN-modal-content {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .AN-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .AN-modal-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .AN-modal-btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .AN-modal-btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .AN-toast {
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

        .AN-toast.show {
            transform: translateX(0);
        }

        .AN-toast-icon {
            flex-shrink: 0;
        }

        .AN-toast-content {
            flex-grow: 1;
        }

        .AN-toast-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .AN-toast-message {
            color: var(--text-secondary);
            font-size: 0.875rem;
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
            border-top: 4px solid #10b981;
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

        [data-theme="dark"] .AN-select,
        [data-theme="dark"] .AN-input,
        [data-theme="dark"] .AN-textarea {
            border-color: #334155;
        }

        [data-theme="dark"] .AN-announcement-item {
            border-color: #334155;
        }

        [data-theme="dark"] .AN-btn-edit {
            background-color: rgba(16, 185, 129, 0.2);
        }

        [data-theme="dark"] .AN-btn-delete {
            background-color: rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 1024px) {
            .AN-container {
                flex-direction: column;
            }

            .AN-recent-section {
                width: 100%;
                min-width: 0;
            }
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

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
                height: auto;
                padding-top: 12px;
                /* match your row padding */
                padding-bottom: 12px;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
                height: 0;
                padding-top: 0;
                padding-bottom: 0;
                margin: 0;
                border: 0;
            }
        }

        .slide-out {
            animation: slideOut 0.3s ease-out forwards;
        }
    </style>
</head>

<body>
    <div id="notificationContainer" style="position: fixed; top: 10px; right: 10px; z-index: 9999;"></div>

    <div class="AN-container">
        <div class="AN-form-section">
            <div class="AN-form">
                <h2 class="mb-4 text-xl font-semibold">Post New Announcement</h2>
                <form method="POST" action="" id="AN-form">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="AN-group">
                        <label class="AN-label">Badge Type</label>
                        <select name="badge" class="AN-select" required>
                            <option value="">Select Badge</option>
                            <option value="New">New</option>
                            <option value="Event">Event</option>
                            <option value="Update">Update</option>
                            <option value="Academic">Academic</option>
                        </select>
                    </div>

                    <div class="AN-group">
                        <label class="AN-label">Title</label>
                        <input type="text" name="title" class="AN-input" required placeholder="Enter announcement title">
                    </div>

                    <div class="AN-group">
                        <label class="AN-label">Content</label>
                        <textarea name="content" class="AN-textarea" required placeholder="Enter announcement content"></textarea>
                    </div>

                    <div class="AN-button-group">
                        <button type="submit" class="AN-submit" id="AN-submit-btn">Post Announcement</button>
                        <button type="button" class="AN-submit" id="AN-reset-btn" style="background-color: var(--secondary-color); margin-left: 0.5rem;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="AN-recent-section">
            <div class="AN-recent-container">
                <h3 class="mb-4 text-xl font-semibold">Recent Announcements</h3>
                <?php
                $query = "SELECT * FROM announcements WHERE status = 1 ORDER BY created_at DESC LIMIT 5";
                $result = $mysqli->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $badgeClass = 'AN-badge-' . strtolower($row['badge']);
                        echo '<div class="AN-announcement-item">';
                        echo '<div class="AN-announcement-header">';
                        echo '<div class="AN-badge-container">';
                        echo '<span class="AN-badge ' . $badgeClass . '">' . htmlspecialchars($row['badge']) . '</span>';
                        echo '</div>';
                        echo '<div class="AN-actions">';
                        echo '<button onclick="editAnnouncement(' . $row['id'] . ', \'' . htmlspecialchars($row['badge'], ENT_QUOTES) . '\', \'' . htmlspecialchars($row['title'], ENT_QUOTES) . '\', \'' . htmlspecialchars($row['content'], ENT_QUOTES) . '\')" class="AN-btn AN-btn-edit" title="Edit">';
                        echo '<i class="fas fa-edit"></i>';
                        echo '</button>';
                        echo '<button onclick="deleteAnnouncement(' . $row['id'] . ')" class="AN-btn AN-btn-delete" title="Delete">';
                        echo '<i class="fas fa-trash"></i>';
                        echo '</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '<h4 class="text-lg font-semibold">' . htmlspecialchars($row['title']) . '</h4>';
                        echo '<p class="mt-2">' . nl2br(htmlspecialchars($row['content'])) . '</p>';
                        echo '<div class="text-sm text-gray-500 mt-2">';
                        echo date('F j, Y g:i A', strtotime($row['created_at']) + 8 * 3600); // UTC+8 for Asia time
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No announcements yet.</p>';
                }
                ?>
            </div>
        </div>
    </div>
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p class="loading-text">Processing your request...</p>
        </div>
    </div>
    <div class="AN-modal-overlay" id="AN-modal-overlay">
        <div class="AN-modal">
            <div class="AN-modal-header">
                <div class="AN-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="AN-modal-title"></h3>
            </div>
            <div class="AN-modal-content"></div>
            <div class="AN-modal-actions">
                <button class="AN-modal-btn AN-modal-btn-secondary" data-action="cancel">Cancel</button>
                <button class="AN-modal-btn AN-modal-btn-danger" data-action="confirm">Delete</button>
            </div>
        </div>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showANModal(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('AN-modal-overlay');
                const iconEl = modal.querySelector('.AN-modal-icon');
                const titleEl = modal.querySelector('.AN-modal-title');
                const contentEl = modal.querySelector('.AN-modal-content');
                const confirmBtn = modal.querySelector('[data-action="confirm"]');
                const cancelBtn = modal.querySelector('[data-action="cancel"]');

                iconEl.className = `AN-modal-icon ${options.type || 'warning'}`;
                iconEl.innerHTML = `<i class="fas fa-${options.icon || 'exclamation-triangle'}"></i>`;
                titleEl.textContent = options.title || '';
                contentEl.textContent = options.message || '';

                confirmBtn.textContent = options.confirmText || 'Confirm';
                confirmBtn.className = `AN-modal-btn ${options.confirmClass || 'AN-modal-btn-danger'}`;
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

        async function deleteAnnouncement(id) {
            const confirmed = await showANModal({
                type: 'warning',
                icon: 'trash',
                title: 'Delete Announcement',
                message: 'Are you sure you want to delete this announcement? This action cannot be undone.',
                confirmText: 'Delete',
                confirmClass: 'AN-modal-btn-danger',
                cancelText: 'Cancel'
            });

            if (confirmed) {
                showLoading();
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editAnnouncement(id, badge, title, content) {
            document.getElementById('edit_id').value = id;
            document.querySelector('[name="badge"]').value = badge;
            document.querySelector('[name="title"]').value = title;
            document.querySelector('[name="content"]').value = content;
            document.getElementById('AN-submit-btn').textContent = 'Update Announcement';
            document.querySelector('.AN-form').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function resetForm() {
            document.getElementById('AN-form').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('AN-submit-btn').textContent = 'Post Announcement';
        }

        function initializeForm() {
            const form = document.getElementById('AN-form');
            const resetBtn = document.getElementById('AN-reset-btn');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const badge = form.querySelector('[name="badge"]').value;
                const title = form.querySelector('[name="title"]').value;
                const content = form.querySelector('[name="content"]').value;

                if (!badge || !title || !content) {
                    showNotification('Please fill in all fields', 'error');
                    return;
                }

                const editId = document.getElementById('edit_id').value;
                if (editId) {
                    const confirmed = await showANModal({
                        type: 'warning',
                        icon: 'edit',
                        title: 'Update Announcement',
                        message: 'Are you sure you want to update this announcement?',
                        confirmText: 'Update',
                        confirmClass: 'AN-modal-btn-primary',
                        cancelText: 'Cancel'
                    });

                    if (!confirmed) {
                        return;
                    }
                }

                showLoading();
                form.submit();
            });

            resetBtn.addEventListener('click', function() {
                resetForm();
                showNotification('Form has been reset', 'success');
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && document.getElementById('edit_id').value) {
                    resetForm();
                }
            });
        }

        function initializeSearch() {
            const searchInput = document.getElementById('announcement-search');
            if (!searchInput) return;

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const announcements = document.querySelectorAll('.AN-announcement-item');

                announcements.forEach(item => {
                    const title = item.querySelector('h4').textContent.toLowerCase();
                    const content = item.querySelector('p').textContent.toLowerCase();
                    const badge = item.querySelector('.AN-badge').textContent.toLowerCase();

                    const matches = title.includes(searchTerm) ||
                        content.includes(searchTerm) ||
                        badge.includes(searchTerm);

                    item.style.display = matches ? '' : 'none';
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('loadingOverlay')) {
                const loadingHTML = `
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="loading-content">
                        <div class="loading-spinner"></div>
                        <p class="loading-text">Processing your request...</p>
                    </div>
                </div>
            `;
                document.body.insertAdjacentHTML('beforeend', loadingHTML);
            }

            initializeForm();
            initializeSearch();
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            const status = urlParams.get('status');


        });
    </script>
</body>

</html>