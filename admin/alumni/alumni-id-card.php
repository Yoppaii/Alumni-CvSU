<?php
require_once 'main_db.php';

// Initialize variables
$message = '';
$messageType = '';
$editMode = false;
$editId = null;
$editData = [
    'alumni_id_card_no' => '',
    'last_name' => '',
    'first_name' => '',
    'middle_name' => '',
    'membership_type' => ''
];

// Check if in edit mode
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $edit_sql = "SELECT * FROM alumni WHERE alumni_id = ?";
    $edit_stmt = $mysqli->prepare($edit_sql);
    $edit_stmt->bind_param("i", $editId);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();

    if ($edit_result->num_rows > 0) {
        $editMode = true;
        $editData = $edit_result->fetch_assoc();
    }
    $edit_stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete handling
    if (isset($_POST['delete']) && isset($_POST['alumni_id'])) {
        $alumni_id = (int)$_POST['alumni_id'];
        $delete_sql = "DELETE FROM alumni WHERE alumni_id = ?";
        $stmt = $mysqli->prepare($delete_sql);
        $stmt->bind_param("i", $alumni_id);
        if ($stmt->execute()) {
            $message = "Record deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting record: " . $stmt->error;
            $messageType = "error";
        }
        $stmt->close();
    }
    // Add/Edit handling
    else {
        $alumni_id_card_no = $mysqli->real_escape_string($_POST['alumni_id_card_no']);
        $last_name = $mysqli->real_escape_string($_POST['last_name']);
        $first_name = $mysqli->real_escape_string($_POST['first_name']);
        $middle_name = empty($_POST['middle_name']) ? NULL : $mysqli->real_escape_string($_POST['middle_name']);
        $membership_type = $mysqli->real_escape_string($_POST['membership_type']);

        // Edit mode
        if (isset($_POST['edit']) && isset($_POST['alumni_id'])) {
            $editId = (int)$_POST['alumni_id'];
            $update_sql = "UPDATE alumni SET 
                alumni_id_card_no=?, 
                last_name=?, 
                first_name=?, 
                middle_name=?, 
                membership_type=? 
                WHERE alumni_id=?";
            $stmt = $mysqli->prepare($update_sql);
            $stmt->bind_param("sssssi", $alumni_id_card_no, $last_name, $first_name, $middle_name, $membership_type, $editId);

            if ($stmt->execute()) {
                $message = "Record updated successfully!";
                $messageType = "success";
                $editMode = false;
            } else {
                $message = "Error updating record: " . $stmt->error;
                $messageType = "error";
            }
            $stmt->close();
        }
        // Add mode
        else {
            $check_sql = "SELECT alumni_id_card_no FROM alumni WHERE alumni_id_card_no = ?";
            $check_stmt = $mysqli->prepare($check_sql);
            $check_stmt->bind_param("s", $alumni_id_card_no);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $message = "Error: Alumni ID already exists!";
                $messageType = "error";
                $check_stmt->close();
            } else {
                $check_stmt->close();
                $insert_sql = "INSERT INTO alumni (alumni_id_card_no, last_name, first_name, middle_name, membership_type, verify) 
                              VALUES (?, ?, ?, ?, ?, 'unused')";
                $stmt = $mysqli->prepare($insert_sql);
                $stmt->bind_param("sssss", $alumni_id_card_no, $last_name, $first_name, $middle_name, $membership_type);

                if ($stmt->execute()) {
                    $message = "Record added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error adding record: " . $stmt->error;
                    $messageType = "error";
                }
                $stmt->close();
            }
        }
    }
}

// Search functionality - only apply if section is alumni-id
$search = (isset($_GET['search']) && isset($_GET['section']) && $_GET['section'] === 'alumni-id')
    ? $mysqli->real_escape_string($_GET['search']) : '';
$searchCondition = $search ?
    "WHERE last_name LIKE '%$search%' OR first_name LIKE '%$search%' OR alumni_id_card_no LIKE '%$search%'" : "";

// Fetch all records with search
$sql = "SELECT * FROM alumni $searchCondition ORDER BY alumni_id DESC";
$result = $mysqli->query($sql);

// Get next ID
$last_id_sql = "SELECT MAX(CAST(alumni_id_card_no AS UNSIGNED)) AS max_id FROM alumni";
$last_id_result = $mysqli->query($last_id_sql);
$next_alumni_id_card_no = 1;
if ($last_id_result) {
    $row = $last_id_result->fetch_assoc();
    if ($row && $row['max_id'] !== null) {
        $next_alumni_id_card_no = (int)$row['max_id'] + 1;
    }
}

// Auto-fill form with next ID if not in edit mode
if (!$editMode) {
    $editData['alumni_id_card_no'] = $next_alumni_id_card_no;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni ID Card Management</title>
</head>
<style>
    /* Root Variables */
    :root {
        /* Dashboard Variables */
        --primary-color: #10b981;
        --primary-hover: #059669;
        --primary-light: #d1fae5;
        --secondary-color: #64748b;
        --success-color: #22c55e;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --sidebar-width: 280px;
        --header-height: 70px;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --transition: all 0.3s ease;
    }

    /* Dark Theme Variables */
    [data-theme="dark"] {
        --primary-color: #10b981;
        --primary-hover: #059669;
        --primary-light: rgba(16, 185, 129, 0.2);
        --secondary-color: #94a3b8;
        --text-primary: #e2e8f0;
        --text-secondary: #94a3b8;
        --bg-primary: #1e293b;
        --bg-secondary: #0f172a;
    }

    /* Reset and Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    html {
        height: 100%;
        overflow-x: hidden;
    }

    body {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 0.875rem;
        line-height: 1.5;
        min-height: 100%;
        overflow-x: hidden;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Alumni Container Styles */
    .alumni-container {
        display: flex;
        gap: 1rem;
        width: 100%;
        margin: auto;
    }

    /* Form Container */
    .alumni-form-section {
        flex: 1;
        min-width: 0;
        background-color: var(--bg-primary);
        border-radius: var(--radius-lg);
        padding: 2rem;
        box-shadow: var(--shadow-md);
    }

    .alumni-id-form-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        transition: color 0.3s ease;
    }

    /* Message Styles */
    .alumni-id-message {
        padding: 1rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .alumni-id-message.success {
        background-color: var(--alumni-success-bg, #dcfce7);
        color: var(--alumni-success-text, #15803d);
        border: 1px solid var(--alumni-success-border, #86efac);
    }

    .alumni-id-message.error {
        background-color: var(--alumni-error-bg, #fee2e2);
        color: var(--alumni-error-text, #dc2626);
        border: 1px solid var(--alumni-error-border, #fca5a5);
    }

    /* Form Styles */
    .alumni-id-form {
        display: grid;
        gap: 1.25rem;
    }

    .alumni-form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .alumni-form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .alumni-form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        color: var(--text-primary);
        background-color: var(--bg-secondary);
        transition: all 0.3s ease;
    }

    .alumni-form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        background-color: #f0fdf4;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .alumni-form-input::placeholder {
        color: var(--alumni-placeholder, #94a3b8);
    }

    /* Submit Button */
    .alumni-submit-btn {
        background-color: var(--primary-color);
        color: #ffffff;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 0.5rem;
    }

    .alumni-submit-btn:hover {
        background-color: var(--primary-hover);
    }

    .alumni-submit-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    /* Records Section */
    .alumni-records-section {
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        max-height: 80vh;
        overflow-y: auto;
        width: 600px;
        min-width: 600px;
    }

    .alumni-records-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        transition: color 0.3s ease;
    }

    /* Search Styles */
    .search-container {
        margin-bottom: 1.5rem;
    }

    .search-form {
        display: flex;
        gap: 0.5rem;
    }

    .search-form input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        color: var(--text-primary);
        background-color: var(--bg-secondary);
    }

    .search-form input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .search-form button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: var(--transition);
    }

    .search-form button:hover {
        background-color: var(--primary-hover);
    }

    .alumni-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-md);
        padding: 1rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    .alumni-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .alumni-card:last-child {
        margin-bottom: 0;
    }

    .alumni-card-header {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .alumni-card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        color: var(--text-primary);
        font-weight: 600;
    }

    .alumni-card p {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin: 0.25rem 0;
    }

    .alumni-card p strong {
        color: var(--text-primary);
        font-weight: 500;
    }

    .alumni-empty {
        text-align: center;
        padding: 2rem;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    /* Action Buttons */
    .alumni-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .alumni-btn {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .alumni-btn-edit {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .alumni-btn-edit:hover {
        background-color: var(--primary-color);
        color: white;
    }

    .alumni-btn-delete {
        background-color: #fee2e2;
        color: var(--danger-color);
    }

    .alumni-btn-delete:hover {
        background-color: var(--danger-color);
        color: white;
    }

    /* Select Element Specific Styles */
    select.alumni-form-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }

    [data-theme="dark"] select.alumni-form-input {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .alumni-container {
            padding: 1.5rem;
            gap: 1.5rem;
        }
    }

    @media (max-width: 900px) {
        .alumni-container {
            flex-direction: column;
        }

        .alumni-records-section {
            max-height: none;
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 480px) {
        .alumni-container {
            padding: 0.75rem;
        }

        .alumni-form-section,
        .alumni-records-section {
            padding: 1rem;
        }

        .alumni-form-input,
        .alumni-submit-btn {
            padding: 0.625rem 1rem;
        }
    }
</style>

<body>

    <div class="alumni-container">
        <!-- Add/Edit Form -->
        <section class="alumni-form-section">
            <h2 class="alumni-id-form-title"><?= $editMode ? 'Edit' : 'Add' ?> Alumni Record</h2>
            <?php if ($message): ?>
                <div class="alumni-id-message <?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" action="?section=alumni-id" class="alumni-id-form">
                <?php if ($editMode): ?>
                    <input type="hidden" name="alumni_id" value="<?= $editId ?>">
                <?php endif; ?>
                <div class="alumni-form-group">
                    <label class="alumni-form-label" for="alumni_id_card_no">Alumni ID Card Number</label>
                    <input type="text" id="alumni_id_card_no" name="alumni_id_card_no" class="alumni-form-input" required
                        value="<?= htmlspecialchars($editData['alumni_id_card_no']) ?>">
                </div>

                <div class="alumni-form-group">
                    <label class="alumni-form-label" for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="alumni-form-input" required
                        value="<?= htmlspecialchars($editData['last_name']) ?>">
                </div>

                <div class="alumni-form-group">
                    <label class="alumni-form-label" for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="alumni-form-input" required
                        value="<?= htmlspecialchars($editData['first_name']) ?>">
                </div>

                <div class="alumni-form-group">
                    <label class="alumni-form-label" for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" class="alumni-form-input"
                        value="<?= htmlspecialchars($editData['middle_name'] ?? '') ?>">
                </div>

                <div class="alumni-form-group">
                    <label class="alumni-form-label" for="membership_type">Membership Type</label>
                    <select id="membership_type" name="membership_type" class="alumni-form-input" required>
                        <option value="">Select Membership Type</option>
                        <option value="Premium" <?= ($editData['membership_type'] === 'Premium') ? 'selected' : '' ?>>Premium</option>
                        <option value="Lifetime" <?= ($editData['membership_type'] === 'Lifetime') ? 'selected' : '' ?>>Lifetime</option>
                    </select>
                </div>

                <button type="submit" class="alumni-submit-btn" name="<?= $editMode ? 'edit' : 'submit' ?>">
                    <?= $editMode ? 'Update' : 'Add' ?> Record
                </button>
            </form>
        </section>

        <!-- Records Section -->
        <section class="alumni-records-section">
            <div class="search-container">
                <form method="GET" class="search-form">
                    <input type="hidden" name="section" value="alumni-id">
                    <input type="text" name="search" placeholder="Search alumni..."
                        value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <h2 class="alumni-records-title">Alumni Records</h2>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="alumni-card">
                        <div class="alumni-card-header">
                            <h3><?= htmlspecialchars($row['last_name']) ?>, <?= htmlspecialchars($row['first_name']) ?></h3>
                        </div>
                        <p><strong>ID:</strong> <?= htmlspecialchars($row['alumni_id_card_no']) ?></p>
                        <p><strong>Membership:</strong> <?= htmlspecialchars($row['membership_type']) ?></p>

                        <div class="alumni-actions">
                            <a href="?section=alumni-id&edit=<?= $row['alumni_id'] ?>" class="alumni-btn alumni-btn-edit">Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="alumni_id" value="<?= $row['alumni_id'] ?>">
                                <button type="submit" name="delete" class="alumni-btn alumni-btn-delete"
                                    onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alumni-empty">No records found</div>
            <?php endif; ?>
        </section>
    </div>

    <script>
        document.querySelector('.alumni-id-form').addEventListener('submit', function(e) {
            const cardNo = document.getElementById('alumni_id_card_no').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const firstName = document.getElementById('first_name').value.trim();

            if (!cardNo || !lastName || !firstName) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return;
            }

            if (!/^[A-Za-z0-9-]+$/.test(cardNo)) {
                e.preventDefault();
                alert('ID Card Number should only contain letters, numbers, and hyphens');
                return;
            }

            if (!/^[A-Za-z\s]+$/.test(lastName) || !/^[A-Za-z\s]+$/.test(firstName)) {
                e.preventDefault();
                alert('Names should only contain letters and spaces');
                return;
            }
        });
    </script>

</body>

</html>