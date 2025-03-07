<?php
require_once 'main_db.php';
function handleFileUpload($file) {
    $upload_dir = dirname(dirname(__DIR__)) . '/asset/uploads';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return array(
            'success' => false,
            'message' => 'Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.'
        );
    }
    
    $unique_filename = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $upload_dir . '/' . $unique_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return array(
            'success' => true,
            'filename' => $unique_filename 
        );
    }
    
    return array(
        'success' => false,
        'message' => 'Error uploading file.'
    );
}
function handleAddNews($mysqli) {
    $category = $mysqli->real_escape_string($_POST['news_category']);
    $title = $mysqli->real_escape_string($_POST['news_title']);
    $description = $mysqli->real_escape_string($_POST['news_description']);
    $date = $mysqli->real_escape_string($_POST['news_date']);
    
    $image_path = '';
    if (isset($_FILES['news_image']) && $_FILES['news_image']['error'] === 0) {
        $upload_result = handleFileUpload($_FILES['news_image']);
        
        if (!$upload_result['success']) {
            echo "<script>
                window.onload = function() {
                    showNEWSNotification('error', '" . addslashes($upload_result['message']) . "');
                }
            </script>";
            return;
        }
        
        $image_path = $upload_result['filename'];
    }
    
    $query = "INSERT INTO news (category, title, description, date, image_path) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssss", $category, $title, $description, $date, $image_path);
    
    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showNEWSNotification('success', 'News added successfully!');
                setTimeout(function() {
                    window.location.href = '?section=Latest-News-and-Features';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNEWSNotification('error', 'Error adding news: " . addslashes($mysqli->error) . "');
            }
        </script>";
    }
    $stmt->close();
}

function handleEditNews($mysqli) {
    $id = $mysqli->real_escape_string($_POST['news_id']);
    $category = $mysqli->real_escape_string($_POST['news_category']);
    $title = $mysqli->real_escape_string($_POST['news_title']);
    $description = $mysqli->real_escape_string($_POST['news_description']);
    $date = $mysqli->real_escape_string($_POST['news_date']);

    $stmt = $mysqli->prepare("SELECT image_path FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_image = $result->fetch_assoc()['image_path'];
    $stmt->close();
    
    $image_path = $current_image;
    
    if (isset($_FILES['news_image']) && $_FILES['news_image']['error'] === 0) {
        $upload_result = handleFileUpload($_FILES['news_image']);
        
        if ($upload_result['success']) {
            if ($current_image && file_exists('../uploads/' . $current_image)) {
                unlink('../uploads/' . $current_image);
            }
            $image_path = $upload_result['filename'];
        } else {
            echo "<script>
                window.onload = function() {
                    showNEWSNotification('error', '" . addslashes($upload_result['message']) . "');
                }
            </script>";
            return;
        }
    }
    
    $query = "UPDATE news 
              SET category = ?, title = ?, description = ?, date = ?, image_path = ? 
              WHERE id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssssi", $category, $title, $description, $date, $image_path, $id);
    
    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showNEWSNotification('success', 'News updated successfully!');
                setTimeout(function() {
                    window.location.href = '?section=Latest-News-and-Features';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNEWSNotification('error', 'Error updating news: " . addslashes($mysqli->error) . "');
            }
        </script>";
    }
    $stmt->close();
}
function handleDeleteNews($mysqli) {
    $id = $mysqli->real_escape_string($_POST['news_id']);

    $stmt = $mysqli->prepare("SELECT image_path FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row && !empty($row['image_path'])) {
        $upload_dir = dirname(dirname(__DIR__)) . '/asset/uploads';
        $full_path = $upload_dir . '/' . $row['image_path'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }

    $query = "DELETE FROM news WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showNEWSNotification('success', 'News deleted successfully!');
                setTimeout(function() {
                    window.location.href = '?section=Latest-News-and-Features';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNEWSNotification('error', 'Error deleting news: " . addslashes($mysqli->error) . "');
            }
        </script>";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                handleAddNews($mysqli);
                break;
            case 'edit':
                handleEditNews($mysqli);
                break;
            case 'delete':
                handleDeleteNews($mysqli);
                break;
        }
    }
}

$query = "SELECT * FROM news ORDER BY date DESC";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management</title>
    <style>
        .NEWS-container {
            display: flex;
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem;
        }

        .NEWS-form-section {
            flex: 1;
            min-width: 0;
        }

        .NEWS-recent-section {
            width: 400px;
            min-width: 400px;
        }

        .NEWS-form {
            background-color: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }

        .NEWS-group {
            margin-bottom: 1.5rem;
        }

        .NEWS-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .NEWS-select {
            width: 200px;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .NEWS-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .NEWS-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 150px;
            resize: vertical;
        }

        .NEWS-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .NEWS-submit:hover {
            background-color: var(--primary-hover);
        }

        .NEWS-recent-container {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .NEWS-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }

        .NEWS-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .NEWS-category {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            text-align: center;
            width: fit-content;
        }

        .NEWS-actions {
            display: flex;
            gap: 0.5rem;
        }

        .NEWS-btn {
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

        .NEWS-btn-edit {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .NEWS-btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .NEWS-btn-delete {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        .NEWS-btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .NEWS-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--radius-md);
            margin: 1rem 0;
        }

        .NEWS-description {
            color: var(--text-secondary);
            margin: 1rem 0;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            line-height: 1.5;
        }

        .NEWS-date {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .NEWS-modal-overlay {
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

        .NEWS-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .NEWS-modal {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            width: 90%;
            max-width: 400px;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .NEWS-modal-overlay.active .NEWS-modal {
            transform: translateY(0);
        }

        .NEWS-modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .NEWS-modal-icon {
            width: 24px;
            height: 24px;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .NEWS-modal-icon.success { background-color: var(--success-color); color: white; }
        .NEWS-modal-icon.error { background-color: var(--danger-color); color: white; }
        .NEWS-modal-icon.warning { background-color: var(--warning-color); color: white; }

        .NEWS-modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .NEWS-modal-content {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .NEWS-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .NEWS-modal-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .NEWS-modal-btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .NEWS-modal-btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .NEWS-toast {
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

        .NEWS-toast.show {
            transform: translateX(0);
        }

        @media (max-width: 1024px) {
            .NEWS-container {
                flex-direction: column;
            }
            .NEWS-recent-section {
                width: 100%;
                min-width: 0;
            }
        }

        .NEWS-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--shadow-lg);
            z-index: 2000;
            transform: translateX(120%);
            transition: transform 0.3s ease;
        }

        .NEWS-toast.show {
            transform: translateX(0);
        }

        .NEWS-toast-icon {
            font-size: 1.25rem;
        }

        .NEWS-toast-message {
            color: var(--text-primary);
        }

        [data-theme="dark"] .NEWS-toast {
            background: var(--bg-secondary);
            border-color: var(--border-color);
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="NEWS-container">
        <div class="NEWS-form-section">
            <div class="NEWS-form">
                <h2 class="mb-4 text-xl font-semibold">Post New News</h2>
                <form method="POST" action="" id="NEWS-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="news_id" id="edit_id">
                    
                    <div class="NEWS-group">
                        <label class="NEWS-label">Category</label>
                        <select name="news_category" id="news_category" class="NEWS-select" required>
                            <option value="">Select Category</option>
                            <option value="Campus News">Campus News</option>
                            <option value="Academic">Academic</option>
                            <option value="Events">Events</option>
                            <option value="Sports">Sports</option>
                            <option value="Alumni">Alumni</option>
                        </select>
                    </div>

                    <div class="NEWS-group">
                        <label class="NEWS-label">Title</label>
                        <input type="text" name="news_title" id="news_title" class="NEWS-input" required>
                    </div>

                    <div class="NEWS-group">
                        <label class="NEWS-label">Description</label>
                        <textarea name="news_description" id="news_description" class="NEWS-textarea" required></textarea>
                    </div>

                    <div class="NEWS-group">
                        <label class="NEWS-label">Date</label>
                        <input type="date" name="news_date" id="news_date" class="NEWS-input" required>
                    </div>

                    <div class="NEWS-group">
                        <label class="NEWS-label">Image</label>
                        <input type="file" name="news_image" id="news_image" class="NEWS-input" accept="image/*">
                    </div>

                    <div class="NEWS-button-group">
                        <button type="submit" class="NEWS-submit" id="NEWS-submit-btn">Post News</button>
                        <button type="button" class="NEWS-submit" id="NEWS-reset-btn" style="background-color: var(--secondary-color); margin-left: 0.5rem;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="NEWS-recent-section">
            <div class="NEWS-recent-container">
                <h3 class="mb-4 text-xl font-semibold">Recent News</h3>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="NEWS-card">
                        <div class="NEWS-card-header">
                            <div class="NEWS-category">
                                <?php echo htmlspecialchars($row['category']); ?>
                            </div>
                            <div class="NEWS-actions">
                                <button onclick="editNews(
                                    <?php echo $row['id']; ?>,
                                    '<?php echo htmlspecialchars($row['category']); ?>',
                                    '<?php echo htmlspecialchars($row['title']); ?>',
                                    '<?php echo htmlspecialchars($row['description']); ?>',
                                    '<?php echo $row['date']; ?>'
                                )" class="NEWS-btn NEWS-btn-edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteNews(<?php echo $row['id']; ?>)" class="NEWS-btn NEWS-btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php if ($row['image_path']): ?>
                            <img src="asset/uploads/<?php echo htmlspecialchars($row['image_path']); ?>" 
                                alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                class="NEWS-image">
                        <?php endif; ?>
                        <h4 class="text-lg font-semibold"><?php echo htmlspecialchars($row['title']); ?></h4>
                        <p class="NEWS-description"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                        <div class="NEWS-date"><?php echo date('F d, Y', strtotime($row['date'])); ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="NEWS-modal-overlay" id="NEWS-modal-overlay">
        <div class="NEWS-modal">
            <div class="NEWS-modal-header">
                <div class="NEWS-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="NEWS-modal-title"></h3>
            </div>
            <div class="NEWS-modal-content"></div>
            <div class="NEWS-modal-actions">
                <button class="NEWS-modal-btn NEWS-modal-btn-secondary" data-action="cancel">Cancel</button>
                <button class="NEWS-modal-btn NEWS-modal-btn-danger" data-action="confirm">Delete</button>
            </div>
        </div>
    </div>
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p class="loading-text">Processing your request...</p>
        </div>
    </div>
    <script>
        function showNEWSModal(options) {
            return new Promise((resolve) => {
                const modal = document.getElementById('NEWS-modal-overlay');
                const iconEl = modal.querySelector('.NEWS-modal-icon');
                const titleEl = modal.querySelector('.NEWS-modal-title');
                const contentEl = modal.querySelector('.NEWS-modal-content');
                const confirmBtn = modal.querySelector('[data-action="confirm"]');
                const cancelBtn = modal.querySelector('[data-action="cancel"]');

                iconEl.className = `NEWS-modal-icon ${options.type || 'warning'}`;
                iconEl.innerHTML = `<i class="fas fa-${options.icon || 'exclamation-triangle'}"></i>`;
                titleEl.textContent = options.title || '';
                contentEl.textContent = options.message || '';

                confirmBtn.textContent = options.confirmText || 'Confirm';
                confirmBtn.className = `NEWS-modal-btn ${options.confirmClass || 'NEWS-modal-btn-danger'}`;
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

        function showNEWSNotification(type, message) {
            const toast = document.createElement('div');
            toast.className = 'NEWS-toast';
            toast.innerHTML = `
                <div class="NEWS-toast-icon">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" 
                    style="color: var(--${type === 'success' ? 'primary' : 'danger'}-color)"></i>
                </div>
                <div class="NEWS-toast-content">
                    <div class="NEWS-toast-message">${message}</div>
                </div>
            `;

            document.body.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }

        async function deleteNews(id) {
            const confirmed = await showNEWSModal({
                type: 'warning',
                icon: 'trash',
                title: 'Delete News',
                message: 'Are you sure you want to delete this news item? This action cannot be undone.',
                confirmText: 'Delete',
                confirmClass: 'NEWS-modal-btn-danger',
                cancelText: 'Cancel'
            });

            if (confirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="news_id" value="${id}">
                `;
                document.body.appendChild(form);
                document.getElementById('loadingOverlay').style.display = 'flex';
                form.submit();
            }
        }

        function editNews(id, category, title, description, date) {
            document.querySelector('[name="action"]').value = 'edit';
            document.getElementById('edit_id').value = id;
            document.getElementById('news_category').value = category;
            document.getElementById('news_title').value = title;
            document.getElementById('news_description').value = description;
            document.getElementById('news_date').value = date;
            document.getElementById('news_image').required = false;
            document.getElementById('NEWS-submit-btn').textContent = 'Update News';
            document.querySelector('.NEWS-form').scrollIntoView({ behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('NEWS-form').reset();
            document.querySelector('[name="action"]').value = 'add';
            document.getElementById('edit_id').value = '';
            document.getElementById('news_image').required = true;
            document.getElementById('NEWS-submit-btn').textContent = 'Post News';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('NEWS-form');
            const resetBtn = document.getElementById('NEWS-reset-btn');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const title = form.querySelector('[name="news_title"]').value;
                const category = form.querySelector('[name="news_category"]').value;
                const description = form.querySelector('[name="news_description"]').value;
                const date = form.querySelector('[name="news_date"]').value;

                if (!title || !category || !description || !date) {
                    showNEWSNotification('error', 'Please fill in all required fields');
                    return;
                }

                const editId = document.getElementById('edit_id').value;

                if (editId) {
                    const confirmed = await showNEWSModal({
                        type: 'warning',
                        icon: 'edit',
                        title: 'Update News',
                        message: 'Are you sure you want to update this news item?',
                        confirmText: 'Update',
                        confirmClass: 'NEWS-modal-btn-primary',
                        cancelText: 'Cancel'
                    });

                    if (!confirmed) {
                        return;
                    }
                }

                loadingOverlay.style.display = 'flex';
                form.submit();
            });

            resetBtn.addEventListener('click', function() {
                resetForm();
                showNEWSNotification('success', 'Form reset successfully');
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