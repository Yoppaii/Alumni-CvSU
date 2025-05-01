<?php
require_once 'main_db.php';

// Handle delete request
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $deleteQuery = "DELETE FROM institutional_info WHERE id = ?";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showNotification('Record deleted successfully!', 'success');
                setTimeout(function() {
                    window.location.href = '?section=Latest-Abouts';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNotification('Error deleting record!', 'error');
            }
        </script>";
    }
    $stmt->close();
}

// Handle delete request for core values
if (isset($_POST['delete_core_id'])) {
    $id = (int)$_POST['delete_core_id'];
    $stmt = $mysqli->prepare("DELETE FROM core_values WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showNotification('Core Value deleted!', 'success');
                setTimeout(function() { window.location.href = '?section=Latest-Abouts'; }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNotification('Error deleting core value!', 'error');
            }
        </script>";
    }
    $stmt->close();
}

// Handle insert/update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id']) && !isset($_POST['delete_core_id'])) {
    // Handle institutional info form
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'institutional') {
        $category = $mysqli->real_escape_string($_POST['category']);
        $title = $mysqli->real_escape_string($_POST['title']);
        $content = $mysqli->real_escape_string($_POST['content']);
        $translation_title = $mysqli->real_escape_string($_POST['translation_title']);
        $translation_content = $mysqli->real_escape_string($_POST['translation_content']);

        if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
            $id = (int)$_POST['edit_id'];
            $query = "UPDATE institutional_info SET category=?, title=?, content=?, translation_title=?, translation_content=? WHERE id=?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sssssi", $category, $title, $content, $translation_title, $translation_content, $id);
        } else {
            $query = "INSERT INTO institutional_info (category, title, content, translation_title, translation_content) VALUES (?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sssss", $category, $title, $content, $translation_title, $translation_content);
        }

        if ($stmt->execute()) {
            $action = isset($_POST['edit_id']) ? 'updated' : 'added';
            echo "<script>
                window.onload = function() {
                    showNotification('Record {$action} successfully!', 'success');
                    setTimeout(function() {
                        window.location.href = '?section=Latest-Abouts&tab=institutional';
                    }, 1500);
                }
            </script>";
        } else {
            echo "<script>
                window.onload = function() {
                    showNotification('Error saving record!', 'error');
                }
            </script>";
        }
        $stmt->close();
    }

    // Handle core values form
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'core_values') {
        $core_title = $mysqli->real_escape_string($_POST['core_title']);
        $core_description = $mysqli->real_escape_string($_POST['core_description']);
        $icon_class = $mysqli->real_escape_string($_POST['icon_class']);
        $display_order = (int)$_POST['display_order'];

        if (isset($_POST['edit_core_id']) && $_POST['edit_core_id']) {
            $id = (int)$_POST['edit_core_id'];
            $stmt = $mysqli->prepare("UPDATE core_values SET title=?, description=?, icon_class=?, display_order=? WHERE id=?");
            $stmt->bind_param("sssii", $core_title, $core_description, $icon_class, $display_order, $id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO core_values (title, description, icon_class, display_order) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $core_title, $core_description, $icon_class, $display_order);
        }

        if ($stmt->execute()) {
            $action = isset($_POST['edit_core_id']) && $_POST['edit_core_id'] ? 'updated' : 'added';
            echo "<script>
                window.onload = function() {
                    showNotification('Core Value {$action} successfully!', 'success');
                    setTimeout(function() { window.location.href = '?section=Latest-Abouts&tab=core_values'; }, 1500);
                }
            </script>";
        } else {
            echo "<script>
                window.onload = function() {
                    showNotification('Error saving core value!', 'error');
                }
            </script>";
        }
        $stmt->close();
    }
}

// Set the active tab based on GET parameter or edit actions
$activeTab = 'institutional';
if (isset($_GET['tab']) && ($_GET['tab'] === 'institutional' || $_GET['tab'] === 'core_values')) {
    $activeTab = $_GET['tab'];
} elseif (isset($_GET['edit_core'])) {
    $activeTab = 'core_values';
}

// Fetch record for editing if requested
$editRecord = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $mysqli->prepare("SELECT * FROM institutional_info WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $result = $stmt->get_result();
    $editRecord = $result->fetch_assoc();
    $stmt->close();
    $activeTab = 'institutional';
}

// Fetch all records for display
$stmt = $mysqli->prepare("SELECT * FROM institutional_info ORDER BY category, display_order");
$stmt->execute();
$result = $stmt->get_result();
$records = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch record for editing (core values)
$editCore = null;
if (isset($_GET['edit_core']) && is_numeric($_GET['edit_core'])) {
    $stmt = $mysqli->prepare("SELECT * FROM core_values WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_core']);
    $stmt->execute();
    $result = $stmt->get_result();
    $editCore = $result->fetch_assoc();
    $stmt->close();
    $activeTab = 'core_values';
}

// Fetch all core values
$stmt = $mysqli->prepare("SELECT * FROM core_values ORDER BY display_order");
$stmt->execute();
$result = $stmt->get_result();
$coreValues = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About - Institutional Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/Alumni-CvSU/admin/website/root.css">
    <style>
        .app-container {
            gap: 1rem;
            width: 100%;
            margin: auto;
        }

        .card {
            background-color: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            overflow: hidden;

        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            margin-bottom: 0;
            font-size: 1.25rem;
            color: var(--text-primary);
        }

        .card-body {
            padding: 1.5rem;
            color: var(--text-primary);
        }

        .card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-secondary);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
            overflow-x: auto;
        }

        .tab {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            color: var(--text-secondary);
            border-bottom: 2px solid transparent;
            font-weight: 500;
            white-space: nowrap;
            transition: var(--transition);
        }

        .tab:hover {
            color: var(--success-color);
        }

        .tab.active {
            color: var(--success-color);
            border-bottom-color: var(--success-hover);
        }

        .tab-content {
            padding: 0 0.5rem;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .grid {
                grid-template-columns: 5fr 3fr;
            }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--success-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
            /* softened success color */
        }

        select.form-control {
            appearance: none;
            padding-right: 2rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: var(--success-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--success-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--white);
        }

        .btn-secondary:hover {
            background-color: var(--secondary-hover);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: var(--white);
        }

        .btn-danger:hover {
            background-color: var(--danger-hover);
        }

        .btn-sm {
            padding: 0.35rem 0.7rem;
            font-size: 0.85rem;
        }

        .btn-icon {
            margin-right: 0.4rem;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 1rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
            color: var(--text-primary);
        }

        .table th,
        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }

        .table th {
            background-color: var(--bg-secondary);
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table tr:hover {
            background-color: var(--bg-secondary);
        }

        .table-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: nowrap;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-primary {
            background-color: var(--success-color);
            color: var(--white);
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

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
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 10px;
            animation: slideIn 0.3s ease-out forwards;
            min-width: 300px;
            max-width: 400px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .notification.error {
            border-left: 4px solid var(--danger-color);
        }

        .notification.success {
            border-left: 4px solid var(--success-color);
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .notification-close:hover {
            color: var(--text-primary);
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
                height: auto;
                padding-top: 16px;
                padding-bottom: 16px;
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

        /* Icon preview */
        .icon-preview {
            font-size: 1.5rem;
            margin-top: 0.5rem;
            color: var(--primary-color);
        }

        /* Responsive adjustments */
        @media (max-width: 767px) {
            .app-container {
                margin: 1rem auto;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .table td,
            .table th {
                padding: 0.6rem;
            }

            .table-actions {
                flex-direction: column;
                gap: 0.3rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div id="notificationContainer"></div>

    <div class="app-container">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-4 text-xl font-semibold">Institutional Information Management</h2>
            </div>

            <div class="card-body">
                <div class="tabs">
                    <div class="tab <?= $activeTab === 'institutional' ? 'active' : '' ?>"
                        onclick="changeTab('institutional')">
                        <i class="fas fa-building"></i> Institutional Info
                    </div>
                    <div class="tab <?= $activeTab === 'core_values' ? 'active' : '' ?>"
                        onclick="changeTab('core_values')">
                        <i class="fas fa-star"></i> Core Values
                    </div>
                </div>

                <div class="tab-content">
                    <!-- Institutional Information Tab -->
                    <div id="institutional" class="tab-pane <?= $activeTab === 'institutional' ? 'active' : '' ?>">
                        <div class="grid">
                            <div>
                                <div class="card">
                                    <div class="card-header">
                                        <h2 class="mb-4 text-xl font-semibold"><?= $editRecord ? 'Edit' : 'Add New' ?> Institutional Information</h2>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" action="" id="institutionalForm" novalidate>
                                            <input type="hidden" name="form_type" value="institutional">
                                            <input type="hidden" name="edit_id" value="<?= $editRecord ? $editRecord['id'] : '' ?>">

                                            <div class="form-group">
                                                <label for="category" class="form-label">Category</label>
                                                <select name="category" id="category" class="form-control" required>
                                                    <option value="">Select Category</option>
                                                    <option value="mandate" <?= $editRecord && $editRecord['category'] == 'mandate' ? 'selected' : '' ?>>Mandate</option>
                                                    <option value="mission" <?= $editRecord && $editRecord['category'] == 'mission' ? 'selected' : '' ?>>Mission</option>
                                                    <option value="vision" <?= $editRecord && $editRecord['category'] == 'vision' ? 'selected' : '' ?>>Vision</option>
                                                    <option value="quality_policy" <?= $editRecord && $editRecord['category'] == 'quality_policy' ? 'selected' : '' ?>>Quality Policy</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text" name="title" id="title" class="form-control" value="<?= $editRecord ? htmlspecialchars($editRecord['title']) : '' ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="content" class="form-label">Content</label>
                                                <textarea name="content" id="content" class="form-control" required><?= $editRecord ? htmlspecialchars($editRecord['content']) : '' ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="translation_title" class="form-label">Translation Title (Optional)</label>
                                                <input type="text" name="translation_title" id="translation_title" class="form-control" value="<?= $editRecord ? htmlspecialchars($editRecord['translation_title'] ?? '') : '' ?>">
                                            </div>

                                            <div class="form-group">
                                                <label for="translation_content" class="form-label">Translation Content (Optional)</label>
                                                <textarea name="translation_content" id="translation_content" class="form-control"><?= $editRecord ? htmlspecialchars($editRecord['translation_content'] ?? '') : '' ?></textarea>
                                            </div>

                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save btn-icon"></i>
                                                    <?= $editRecord ? 'Update Record' : 'Save Record' ?>
                                                </button>
                                                <?php if ($editRecord): ?>
                                                    <a href="?section=Latest-Abouts&tab=institutional" class="btn btn-secondary">
                                                        <i class="fas fa-times btn-icon"></i>
                                                        Cancel
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="card">
                                    <div class="card-header">
                                        <h2 class="mb-4 text-xl font-semibold">Existing Records</h2>
                                    </div>
                                    <div class="card-body">
                                        <?php if (count($records) === 0): ?>
                                            <p>No records found.</p>
                                        <?php else: ?>
                                            <div class="table-container">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Title</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($records as $record): ?>
                                                            <tr>
                                                                <td>
                                                                    <span class="badge badge-primary">
                                                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $record['category']))) ?>
                                                                    </span>
                                                                </td>
                                                                <td><?= htmlspecialchars($record['title']) ?></td>
                                                                <td>
                                                                    <div class="table-actions">
                                                                        <a href="?section=Latest-Abouts&tab=institutional&edit=<?= $record['id'] ?>" class="btn btn-primary btn-sm">
                                                                            <i class="fas fa-edit"></i> Edit
                                                                        </a>
                                                                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                                            <input type="hidden" name="delete_id" value="<?= $record['id'] ?>" />
                                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                                <i class="fas fa-trash"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Core Values Tab -->
                    <div id="core_values" class="tab-pane <?= $activeTab === 'core_values' ? 'active' : '' ?>">
                        <div class="grid">
                            <div>
                                <div class="card">
                                    <div class="card-header">
                                        <h2><?= $editCore ? 'Edit' : 'Add New' ?> Core Value</h2>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" action="" novalidate>
                                            <input type="hidden" name="form_type" value="core_values">
                                            <input type="hidden" name="edit_core_id" value="<?= $editCore ? $editCore['id'] : '' ?>">

                                            <div class="form-group">
                                                <label for="core_title" class="form-label">Title</label>
                                                <input type="text" name="core_title" id="core_title" class="form-control" value="<?= $editCore ? htmlspecialchars($editCore['title']) : '' ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="core_description" class="form-label">Description</label>
                                                <textarea name="core_description" id="core_description" class="form-control" required><?= $editCore ? htmlspecialchars($editCore['description']) : '' ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="icon_class" class="form-label">Icon Class (FontAwesome, etc.)</label>
                                                <input type="text" name="icon_class" id="icon_class" class="form-control" value="<?= $editCore ? htmlspecialchars($editCore['icon_class']) : '' ?>" required oninput="updateIconPreview()">
                                                <div id="icon_preview" class="icon-preview">
                                                    <?php if ($editCore): ?>
                                                        <i class="<?= htmlspecialchars($editCore['icon_class']) ?>"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="display_order" class="form-label">Display Order</label>
                                                <input type="number" name="display_order" id="display_order" class="form-control" value="<?= $editCore ? (int)$editCore['display_order'] : 1 ?>" required>
                                            </div>

                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save btn-icon"></i>
                                                    <?= $editCore ? 'Update Core Value' : 'Save Core Value' ?>
                                                </button>
                                                <?php if ($editCore): ?>
                                                    <a href="?section=Latest-Abouts&tab=core_values" class="btn btn-secondary">
                                                        <i class="fas fa-times btn-icon">
                                                        </i>
                                                        Cancel
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="card">
                                    <div class="card-header">
                                        <h2>Existing Core Values</h2>
                                    </div>
                                    <div class="card-body">
                                        <?php if (count($coreValues) === 0): ?>
                                            <p>No core values found.</p>
                                        <?php else: ?>
                                            <div class="table-container">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Order</th>
                                                            <th>Title</th>
                                                            <th>Icon</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($coreValues as $value): ?>
                                                            <tr>
                                                                <td><?= $value['display_order'] ?></td>
                                                                <td><?= htmlspecialchars($value['title']) ?></td>
                                                                <td><i class="<?= htmlspecialchars($value['icon_class']) ?>"></i></td>
                                                                <td>
                                                                    <div class="table-actions">
                                                                        <a href="?section=Latest-Abouts&tab=core_values&edit_core=<?= $value['id'] ?>" class="btn btn-primary btn-sm">
                                                                            <i class="fas fa-edit"></i> Edit
                                                                        </a>
                                                                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this core value?');">
                                                                            <input type="hidden" name="delete_core_id" value="<?= $value['id'] ?>" />
                                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                                <i class="fas fa-trash"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        function changeTab(tabId) {
            // Update URL to maintain tab state on page refresh
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', currentUrl);

            // Hide all tab panes
            const tabPanes = document.querySelectorAll('.tab-pane');
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
            });

            // Deactivate all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });

            // Activate the selected tab and its content
            document.getElementById(tabId).classList.add('active');
            document.querySelector(`.tab[onclick="changeTab('${tabId}')"]`).classList.add('active');
        }

        // Notification system
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close" onclick="closeNotification(this.parentNode)">&times;</button>
        `;
            container.appendChild(notification);

            // Auto close after 5 seconds
            setTimeout(() => {
                closeNotification(notification);
            }, 5000);
        }

        function closeNotification(notification) {
            notification.classList.add('slide-out');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }

        // Icon preview functionality
        function updateIconPreview() {
            const iconClass = document.getElementById('icon_class').value;
            const previewDiv = document.getElementById('icon_preview');
            previewDiv.innerHTML = iconClass ? `<i class="${iconClass}"></i>` : '';
        }

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('error');
                            field.style.borderColor = '#ef4444';

                            // Show error message if not already shown
                            let errorMsg = field.nextElementSibling;
                            if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                                errorMsg = document.createElement('div');
                                errorMsg.className = 'error-message';
                                errorMsg.style.color = '#ef4444';
                                errorMsg.style.fontSize = '0.85rem';
                                errorMsg.style.marginTop = '0.25rem';
                                errorMsg.textContent = 'This field is required';
                                field.parentNode.insertBefore(errorMsg, field.nextSibling);
                            }
                        } else {
                            field.classList.remove('error');
                            field.style.borderColor = '';

                            // Remove error message if exists
                            const errorMsg = field.nextElementSibling;
                            if (errorMsg && errorMsg.classList.contains('error-message')) {
                                errorMsg.remove();
                            }
                        }
                    });

                    if (!isValid) {
                        event.preventDefault();
                        showNotification('Please fill in all required fields', 'error');
                    }
                });
            });

            // Initialize icon preview if on core values tab
            if (document.getElementById('icon_class')) {
                updateIconPreview();
            }
        });
    </script>