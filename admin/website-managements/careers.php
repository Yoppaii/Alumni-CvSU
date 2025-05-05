<?php
require_once 'main_db.php';

// Handle Delete
if (isset($_POST['delete_id'])) {
    $id = $mysqli->real_escape_string($_POST['delete_id']);
    $stmt = $mysqli->prepare("DELETE FROM jobs WHERE job_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showNotification('Job deleted successfully!','success');
                setTimeout(function() {
                    window.location.href = '?section=CvSU-Careers';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNotification('Error deleting job!','error');
            }
        </script>";
    }
    $stmt->close();
}

// Handle Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $company = $mysqli->real_escape_string($_POST['company']);
    $job_title = $mysqli->real_escape_string($_POST['job_title']);
    $job_desc = $mysqli->real_escape_string($_POST['job_desc']);
    $job_type = $mysqli->real_escape_string($_POST['job_type']);
    $location = $mysqli->real_escape_string($_POST['location']);

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        $id = $mysqli->real_escape_string($_POST['edit_id']);
        $stmt = $mysqli->prepare("UPDATE jobs SET company=?, job_title=?, job_desc=?, job_type=?, location=? WHERE job_id=?");
        $stmt->bind_param("sssssi", $company, $job_title, $job_desc, $job_type, $location, $id);
        $action = 'updated';
    } else {
        $stmt = $mysqli->prepare("INSERT INTO jobs (company, job_title, job_desc, job_type, location) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $company, $job_title, $job_desc, $job_type, $location);
        $action = 'posted';
    }

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                showNotification('Job {$action} successfully!', 'success');
                setTimeout(function() {
                    window.location.href = '?section=CvSU-Careers';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                showNotification('Error saving job!', 'error');
            }
        </script>";
    }
    $stmt->close();
}

// Handle Edit fetch
$edit_job = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $mysqli->query("SELECT * FROM jobs WHERE job_id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_job = $result->fetch_assoc();
    }
}

// Fetch recent jobs
$jobs = $mysqli->query("SELECT * FROM jobs ORDER BY posted_date DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers Management</title>

    <style>
        .CR-container {
            display: flex;
            gap: 1rem;
            width: 100%;
            margin: auto;
        }

        .CR-form-section {
            flex: 1;
            min-width: 0;
        }

        .CR-recent-section {
            width: 600px;
            min-width: 600px;
        }

        .CR-recent-container {
            max-height: 600px;
            overflow-y: auto;
            padding: 1rem;
            background: var(--bg-primary);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .CR-form {
            background-color: var(--bg-primary, #fff);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .CR-group {
            margin-bottom: 1.5rem;
        }

        .CR-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary, #333);
            font-weight: 600;
        }

        .CR-input,
        .CR-select,
        .CR-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: var(--bg-primary, #fff);
            color: var(--text-primary, #333);
            font-size: 1rem;
            font-family: inherit;
        }

        .CR-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .CR-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .CR-btn:hover {
            background-color: var(--primary-hover);
        }




        .form-btn-cancel {
            background: var(--secondary-color);
            color: white;
            border: 1px solid #d1fae5;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            margin-left: 1rem;
            cursor: pointer;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
        }




        /* Recent jobs list */
        .CR-job-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: var(--bg-secondary, #f9fafb);
            transition: box-shadow 0.2s ease;
        }

        .CR-job-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .CR-job-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .CR-job-title {
            font-weight: 700;
            color: var(--primary-color, #006400);
            font-size: 1.1rem;
            margin: 0;
        }

        .CR-job-company {
            font-weight: 600;
            color: #4b5563;
            font-size: 0.9rem;
        }

        .CR-job-meta {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .CR-job-desc {
            font-size: 0.9rem;
            color: #374151;
            margin-bottom: 0.5rem;
            white-space: pre-wrap;
        }

        .CR-job-actions {
            display: flex;
            gap: 0.5rem;
        }

        .CR-btn-edit,
        .CR-btn-delete {
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

        .CR-btn-edit {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .CR-btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .CR-btn-delete {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        .CR-btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }


        @media (max-width: 1024px) {
            .CR-container {
                flex-direction: column;
            }

            .CR-recent-section {
                width: 100%;
                min-width: 0;
                max-height: none;
                margin-top: 2rem;
            }
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
    </style>
</head>

<body>
    <div id="notificationContainer"></div>
    <div class="CR-container">
        <!-- Add/Edit Job Form -->
        <section class="CR-form-section">
            <form class="CR-form" method="POST" action="">
                <input type="hidden" name="edit_id" value="<?= $edit_job ? $edit_job['job_id'] : '' ?>">
                <div class="CR-group">
                    <label class="CR-label" for="company">Company</label>
                    <input class="CR-input" type="text" id="company" name="company" required value="<?= $edit_job ? htmlspecialchars($edit_job['company']) : '' ?>" placeholder="Company name">
                </div>
                <div class="CR-group">
                    <label class="CR-label" for="job_title">Job Title</label>
                    <input class="CR-input" type="text" id="job_title" name="job_title" required value="<?= $edit_job ? htmlspecialchars($edit_job['job_title']) : '' ?>" placeholder="Job title">
                </div>
                <div class="CR-group">
                    <label class="CR-label" for="job_desc">Job Description</label>
                    <textarea class="CR-textarea" id="job_desc" name="job_desc" required placeholder="Job description"><?= $edit_job ? htmlspecialchars($edit_job['job_desc']) : '' ?></textarea>
                </div>
                <div class="CR-group">
                    <label class="CR-label" for="job_type">Job Type</label>
                    <select class="CR-select" id="job_type" name="job_type" required>
                        <?php
                        $types = ['Full-time', 'Part-time', 'Contract'];
                        foreach ($types as $type) {
                            $selected = ($edit_job && $edit_job['job_type'] === $type) ? 'selected' : '';
                            echo "<option value=\"$type\" $selected>$type</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="CR-group">
                    <label class="CR-label" for="location">Location</label>
                    <input class="CR-input" type="text" id="location" name="location" value="<?= $edit_job ? htmlspecialchars($edit_job['location']) : '' ?>" placeholder="Location">
                </div>
                <button type="submit" class="CR-btn"><?= $edit_job ? "Update Job" : "Post Job" ?></button>
                <button type="button" class="form-btn-cancel" onclick="window.location.href='?section=CvSU-Careers'">Cancel</button>

            </form>
        </section>
        <!-- Recent Jobs List -->
        <section class="CR-recent-section">
            <div class="CR-recent-container">
                <h2>Recent Jobs</h2>
                <?php if ($jobs && $jobs->num_rows > 0): ?>
                    <?php while ($job = $jobs->fetch_assoc()): ?>
                        <div class="CR-job-item">
                            <div class="CR-job-header">
                                <h3 class="CR-job-title"><?= htmlspecialchars($job['job_title']) ?></h3>
                                <div class="CR-job-actions">
                                    <a href="?section=CvSU-Careers&edit=<?= $job['job_id'] ?>" class="CR-btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this job?');">
                                        <input type="hidden" name="delete_id" value="<?= $job['job_id'] ?>">
                                        <button type="submit" class="CR-btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>
                            <div class="CR-job-company"><?= htmlspecialchars($job['company']) ?></div>
                            <div class="CR-job-meta">
                                <span><?= htmlspecialchars($job['job_type']) ?></span> |
                                <span><?= htmlspecialchars($job['location']) ?></span> |
                                <span><?= date('M d, Y', strtotime($job['posted_date'])) ?></span>
                            </div>
                            <div class="CR-job-desc"><?= nl2br(htmlspecialchars($job['job_desc'])) ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No jobs posted yet.</p>
                <?php endif; ?>
            </div>

        </section>
    </div>
    <script>
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
    </script>
</body>

</html>