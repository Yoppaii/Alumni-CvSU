<?php
require_once 'main_db.php';

function handleFileUpload($file)
{
    $upload_dir = dirname(dirname(__DIR__)) . '/asset/images/campuses';

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

function handleAddCampus($mysqli)
{
    $name = $mysqli->real_escape_string($_POST['campus_name']);
    $url = $mysqli->real_escape_string($_POST['campus_url']);

    $image_path = '';
    if (isset($_FILES['campus_image']) && $_FILES['campus_image']['error'] === 0) {
        $upload_result = handleFileUpload($_FILES['campus_image']);

        if (!$upload_result['success']) {
            echo "<script>
                window.onload = function() {
                    showCampusNotification('error', '" . addslashes($upload_result['message']) . "');
                }
            </script>";
            return;
        }

        $image_path = $upload_result['filename'];
    }

    $query = "INSERT INTO campuses (name, url, image_path) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sss", $name, $url, $image_path);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showCampusNotification('success', 'Campus added successfully!');
                setTimeout(function() {
                    window.location.href = '?section=CvSU-Campuses';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showCampusNotification('error', 'Error adding campus: " . addslashes($mysqli->error) . "');
            }
        </script>";
    }
    $stmt->close();
}

function handleEditCampus($mysqli)
{
    $id = $mysqli->real_escape_string($_POST['campus_id']);
    $name = $mysqli->real_escape_string($_POST['campus_name']);
    $url = $mysqli->real_escape_string($_POST['campus_url']);

    $stmt = $mysqli->prepare("SELECT image_path FROM campuses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_image = $result->fetch_assoc()['image_path'];
    $stmt->close();

    $image_path = $current_image;

    if (isset($_FILES['campus_image']) && $_FILES['campus_image']['error'] === 0) {
        $upload_result = handleFileUpload($_FILES['campus_image']);

        if ($upload_result['success']) {
            if ($current_image && file_exists(dirname(dirname(__DIR__)) . '/asset/images/campuses/' . $current_image)) {
                unlink(dirname(dirname(__DIR__)) . '/asset/images/campuses/' . $current_image);
            }
            $image_path = $upload_result['filename'];
        } else {
            echo "<script>
                window.onload = function() {
                    showCampusNotification('error', '" . addslashes($upload_result['message']) . "');
                }
            </script>";
            return;
        }
    }

    $query = "UPDATE campuses SET name = ?, url = ?, image_path = ? WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssi", $name, $url, $image_path, $id);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showCampusNotification('success', 'Campus updated successfully!');
                setTimeout(function() {
                    window.location.href = '?section=CvSU-Campuses';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showCampusNotification('error', 'Error updating campus: " . addslashes($mysqli->error) . "');
            }
        </script>";
    }
    $stmt->close();
}

function handleDeleteCampus($mysqli)
{
    $id = $mysqli->real_escape_string($_POST['campus_id']);
    $stmt = $mysqli->prepare("SELECT image_path FROM campuses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row && !empty($row['image_path'])) {
        $upload_dir = dirname(dirname(__DIR__)) . '/asset/images/campuses';
        $full_path = $upload_dir . '/' . $row['image_path'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }

    $query = "DELETE FROM campuses WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showCampusNotification('success', 'Campus deleted successfully!');
                setTimeout(function() {
                    window.location.href = '?section=CvSU-Campuses';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showCampusNotification('error', 'Error deleting campus: " . addslashes($mysqli->error) . "');
            }
        </script>";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                handleAddCampus($mysqli);
                break;
            case 'edit':
                handleEditCampus($mysqli);
                break;
            case 'delete':
                handleDeleteCampus($mysqli);
                break;
        }
    }
}

$query = "SELECT * FROM campuses ORDER BY name ASC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Campus Management</title>
    <style>
        .campus-container {
            display: flex;
            gap: 1rem;
            max-width: auto;
            margin: 0 auto;
            flex-wrap: wrap;
        }

        .campus-form-section {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            flex: 1;
            min-width: 0;
        }

        .campus-form {
            display: flex;
            flex-direction: column;
        }

        .campus-group {
            margin-bottom: 1.25rem;
        }

        .campus-btn-group {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
            margin-right: 1rem;
        }

        .campus-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .campus-input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: var(--radius-md);
            font-size: 1rem;
            color: var(--text-primary);
            background-color: var(--bg-secondary);
            transition: border-color 0.3s ease;
        }

        .campus-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: #f0fdf4;
        }

        .campus-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .campus-submit:hover {
            background-color: var(--primary-hover);
        }

        .campus-recent-section {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            max-height: 80vh;
            overflow-y: auto;
            width: 400px;
        }

        .campus-recent-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .campus-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            padding: 1rem;
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .campus-card-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .campus-card-header h3 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--text-primary);
            flex-grow: 1;
            text-align: left;
            word-break: break-word;
        }

        .campus-actions {
            display: flex;
            gap: 0.5rem;
        }

        .campus-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .campus-btn-edit {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .campus-btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .campus-btn-cancel {
            width: 100px;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .campus-btn-delete {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        .campus-btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .campus-image {
            width: 100%;
            max-width: 320px;
            height: 160px;
            object-fit: cover;
            border-radius: var(--radius-md);
            margin: 0.75rem 0;
            box-shadow: var(--shadow-sm);
        }

        .campus-card a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            margin-top: 0.5rem;
            display: inline-block;
            transition: color 0.3s ease;
        }

        .campus-card a:hover {
            color: var(--primary-hover);
        }

        /* Modal styles */
        .campus-modal-overlay {
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

        .campus-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .campus-modal {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            width: 100%;
            max-width: 500px;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .campus-modal-overlay.active .campus-modal {
            transform: translateY(0);
        }

        .campus-modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .campus-modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .campus-modal-body {
            display: flex;
            flex-direction: column;
        }

        .campus-toast {
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
            z-index: 2000;
            transform: translateX(120%);
            transition: transform 0.3s ease;
        }

        .campus-toast.show {
            transform: translateX(0);
        }

        .campus-toast-message {
            color: var(--text-primary);
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .campus-container {
                flex-direction: column;
            }

            .campus-recent-section {
                max-height: none;
                margin-top: 2rem;
            }

            .campus-image {
                max-width: 100%;
                height: 180px;
            }
        }
    </style>
</head>

<body>

    <div class="campus-container">

        <!-- Add Campus Form -->
        <div class="campus-form-section">
            <h2>Add New Campus</h2>
            <form class="campus-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add" />
                <div class="campus-group">
                    <label class="campus-label" for="campus_name">Campus Name</label>
                    <input class="campus-input" type="text" id="campus_name" name="campus_name" required />
                </div>
                <div class="campus-group">
                    <label class="campus-label" for="campus_url">Campus URL</label>
                    <input class="campus-input" type="url" id="campus_url" name="campus_url" required />
                </div>
                <div class="campus-group">
                    <label class="campus-label" for="campus_image">Campus Image</label>
                    <input class="campus-input" type="file" id="campus_image" name="campus_image" accept="image/*" />
                </div>
                <button class="campus-submit" type="submit">Add Campus</button>
            </form>
        </div>

        <!-- Campus List -->
        <div class="campus-recent-section">
            <h2>Manage Campuses</h2>
            <div class="campus-recent-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="campus-card" data-campus-id="<?= $row['id'] ?>">
                        <div class="campus-card-header">
                            <h3><?= htmlspecialchars($row['name']) ?></h3>
                            <div class="campus-actions">
                                <button class="campus-btn campus-btn-edit" onclick="openEditModal(<?= $row['id'] ?>)" title="Edit">&#9998;</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this campus?');">
                                    <input type="hidden" name="action" value="delete" />
                                    <input type="hidden" name="campus_id" value="<?= $row['id'] ?>" />
                                    <button class="campus-btn campus-btn-delete" type="submit" title="Delete">&#10006;</button>
                                </form>
                            </div>
                        </div>
                        <img src="asset/images/campuses/<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="campus-image" />
                        <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" rel="noopener noreferrer">Visit Campus</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="campus-modal-overlay" id="editCampusModal">
        <div class="campus-modal">
            <div class="campus-modal-header">
                <h2 class="campus-modal-title">Edit Campus</h2>
            </div>
            <div class="campus-modal-body">
                <form class="campus-form" method="POST" enctype="multipart/form-data" id="editCampusForm">
                    <input type="hidden" name="action" value="edit" />
                    <input type="hidden" name="campus_id" id="edit_campus_id" />
                    <div class="campus-group">
                        <label class="campus-label" for="edit_campus_name">Campus Name</label>
                        <input class="campus-input" type="text" id="edit_campus_name" name="campus_name" required />
                    </div>
                    <div class="campus-group">
                        <label class="campus-label" for="edit_campus_url">Campus URL</label>
                        <input class="campus-input" type="url" id="edit_campus_url" name="campus_url" required />
                    </div>
                    <div class="campus-group">
                        <label class="campus-label" for="edit_campus_image">Campus Image (leave empty to keep current)</label>
                        <input class="campus-input" type="file" id="edit_campus_image" name="campus_image" accept="image/*" />
                    </div>
                    <div class="campus-btn-group">
                        <button class="campus-submit" type="submit">Update Campus</button>
                        <button type="button" class="campus-btn-cancel campus-btn-delete" onclick="closeEditModal()" style="margin-left: 1rem;">Cancel</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(campusId) {
            fetch(`/Alumni-CvSU/admin/website-managements/get_campus_data.php?id=${campusId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById('edit_campus_id').value = data.id;
                    document.getElementById('edit_campus_name').value = data.name;
                    document.getElementById('edit_campus_url').value = data.url;
                    document.getElementById('editCampusModal').classList.add('active');
                })
                .catch(() => alert('Failed to fetch campus data.'));
        }

        function closeEditModal() {
            document.getElementById('editCampusModal').classList.remove('active');
        }

        function showCampusNotification(type, message) {
            const notification = document.createElement('div');
            notification.classList.add('campus-toast', type);
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.add('show');
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }, 100);
        }
    </script>

</body>

</html>