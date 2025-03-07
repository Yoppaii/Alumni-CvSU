<?php
require_once 'main_db.php';

// Initialize message variables
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize inputs
    $alumni_id_card_no = $mysqli->real_escape_string($_POST['alumni_id_card_no']);
    $last_name = $mysqli->real_escape_string($_POST['last_name']);
    $first_name = $mysqli->real_escape_string($_POST['first_name']);
    $middle_name = empty($_POST['middle_name']) ? NULL : $mysqli->real_escape_string($_POST['middle_name']);
    $membership_type = $mysqli->real_escape_string($_POST['membership_type']);
    $verify = 'unused'; // Default value for verify field
    
    // Check if the alumni ID card number already exists
    $check_sql = "SELECT alumni_id_card_no FROM alumni WHERE alumni_id_card_no = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("s", $alumni_id_card_no);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $message = "Error: Alumni ID Card Number already exists!";
        $messageType = "error";
    } else {
        // Prepare INSERT statement
        $insert_sql = "INSERT INTO alumni (alumni_id_card_no, last_name, first_name, middle_name, membership_type, verify) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $mysqli->prepare($insert_sql);
        
        // Use different bind_param based on whether middle_name is NULL
        if ($middle_name === NULL) {
            $stmt->bind_param("ssssss", 
                $alumni_id_card_no,
                $last_name,
                $first_name,
                $middle_name,
                $membership_type,
                $verify
            );
        } else {
            $stmt->bind_param("ssssss", 
                $alumni_id_card_no,
                $last_name,
                $first_name,
                $middle_name,
                $membership_type,
                $verify
            );
        }
        
        // Execute the statement
        if ($stmt->execute()) {
            $message = "Alumni ID Card added successfully!";
            $messageType = "success";
            
            // Clear form data on successful submission
            $_POST = array();
        } else {
            $message = "Error: " . $stmt->error;
            $messageType = "error";
        }
        
        $stmt->close();
    }
    $check_stmt->close();
}

// Fetch the latest 3 alumni entries
$recent_alumni_sql = "SELECT alumni_id, alumni_id_card_no, last_name, first_name, middle_name, membership_type, verify 
                     FROM alumni 
                     ORDER BY alumni_id DESC 
                     LIMIT 3";
$recent_alumni_result = $mysqli->query($recent_alumni_sql);
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

    /* Alumni ID Form Variables */
    --alumni-bg-primary: #ffffff;
    --alumni-text-primary: #1e293b;
    --alumni-text-secondary: #64748b;
    --alumni-border-color: #e2e8f0;
    --alumni-input-bg: #ffffff;
    --alumni-card-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    --alumni-success-bg: #dcfce7;
    --alumni-success-text: #15803d;
    --alumni-success-border: #86efac;
    --alumni-error-bg: #fee2e2;
    --alumni-error-text: #dc2626;
    --alumni-error-border: #fca5a5;
    --alumni-hover-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    --alumni-button-bg: #10b981;
    --alumni-button-hover: #059669;
    --alumni-input-focus: rgba(16, 185, 129, 0.1);
    --alumni-placeholder: #94a3b8;
}

/* Dark Theme Variables */
[data-theme="dark"] {
    /* Dashboard Dark Theme */
    --primary-color: #10b981;
    --primary-hover: #059669;
    --primary-light: rgba(16, 185, 129, 0.2);
    --secondary-color: #94a3b8;
    --text-primary: #e2e8f0;
    --text-secondary: #94a3b8;
    --bg-primary: #1e293b;
    --bg-secondary: #0f172a;

    /* Alumni ID Form Dark Theme */
    --alumni-bg-primary: #1e293b;
    --alumni-text-primary: #e2e8f0;
    --alumni-text-secondary: #94a3b8;
    --alumni-border-color: #334155;
    --alumni-input-bg: #0f172a;
    --alumni-card-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    --alumni-success-bg: rgba(16, 185, 129, 0.2);
    --alumni-success-text: #10b981;
    --alumni-success-border: #059669;
    --alumni-error-bg: rgba(220, 38, 38, 0.2);
    --alumni-error-text: #ef4444;
    --alumni-error-border: #dc2626;
    --alumni-hover-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    --alumni-button-bg: #10b981;
    --alumni-button-hover: #059669;
    --alumni-input-focus: rgba(16, 185, 129, 0.2);
    --alumni-placeholder: #64748b;
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

/* Theme Toggle Button */
.theme-toggle {
    background: none;
    border: none;
    padding: 0.5rem;
    color: var(--text-secondary);
    cursor: pointer;
    border-radius: var(--radius-md);
    transition: var(--transition);
}

.theme-toggle:hover {
    color: var(--primary-color);
    background-color: var(--primary-light);
}

/* Alumni Container Styles */
.alumni-container {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 1rem;
}

/* Form Container */
.alumni-id-form-container {
    background: var(--alumni-bg-primary);
    border-radius: 0.75rem;
    padding: 2rem;
    box-shadow: var(--alumni-card-shadow);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.alumni-id-form-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--alumni-text-primary);
    margin-bottom: 1.5rem;
    transition: color 0.3s ease;
}

/* Message Styles */
.alumni-id-message {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.alumni-id-message.success {
    background-color: var(--alumni-success-bg);
    color: var(--alumni-success-text);
    border: 1px solid var(--alumni-success-border);
}

.alumni-id-message.error {
    background-color: var(--alumni-error-bg);
    color: var(--alumni-error-text);
    border: 1px solid var(--alumni-error-border);
}

/* Form Styles */
.alumni-id-form {
    display: grid;
    gap: 1.5rem;
}

.alumni-id-form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.alumni-id-form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--alumni-text-primary);
    transition: color 0.3s ease;
}

.alumni-id-form-input {
    padding: 0.75rem 1rem;
    border: 1px solid var(--alumni-border-color);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: var(--alumni-text-primary);
    background-color: var(--alumni-input-bg);
    transition: all 0.3s ease;
}

.alumni-id-form-input:focus {
    outline: none;
    border-color: var(--alumni-button-bg);
    box-shadow: 0 0 0 3px var(--alumni-input-focus);
}

.alumni-id-form-input::placeholder {
    color: var(--alumni-placeholder);
}

/* Submit Button */
.alumni-id-submit-btn {
    background-color: var(--alumni-button-bg);
    color: #ffffff;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 0.5rem;
}

.alumni-id-submit-btn:hover {
    background-color: var(--alumni-button-hover);
}

.alumni-id-submit-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px var(--alumni-input-focus);
}

/* History Section */
.alumni-history-container {
    background: var(--alumni-bg-primary);
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: var(--alumni-card-shadow);
    transition: all 0.3s ease;
}

.alumni-history-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--alumni-text-primary);
    margin-bottom: 1.5rem;
    transition: color 0.3s ease;
}

.alumni-history-card {
    padding: 1rem;
    border: 1px solid var(--alumni-border-color);
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
    background: var(--alumni-bg-primary);
}

.alumni-history-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--alumni-hover-shadow);
}

.alumni-history-card:last-child {
    margin-bottom: 0;
}

.alumni-history-card h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--alumni-text-primary);
    margin-bottom: 0.5rem;
    transition: color 0.3s ease;
}

.alumni-history-card p {
    font-size: 0.875rem;
    color: var(--alumni-text-secondary);
    margin: 0.25rem 0;
    transition: color 0.3s ease;
}

.alumni-history-card p strong {
    color: var(--alumni-text-primary);
    font-weight: 500;
}

.alumni-history-empty {
    text-align: center;
    padding: 2rem;
    color: var(--alumni-text-secondary);
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

/* Select Element Specific Styles */
select.alumni-id-form-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.25rem;
    padding-right: 2.5rem;
}

[data-theme="dark"] select.alumni-id-form-input {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
}

/* Responsive Design */
@media (max-width: 1024px) {
    .alumni-container {
        padding: 1.5rem;
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .alumni-container {
        grid-template-columns: 1fr;
        padding: 1rem;
    }

    .alumni-id-form-container,
    .alumni-history-container {
        padding: 1.5rem;
    }

    .alumni-id-form-title,
    .alumni-history-title {
        font-size: 1.25rem;
        margin-bottom: 1.25rem;
    }
}

@media (max-width: 480px) {
    .alumni-container {
        padding: 0.75rem;
    }

    .alumni-id-form-container,
    .alumni-history-container {
        padding: 1rem;
    }

    .alumni-id-form-input,
    .alumni-id-submit-btn {
        padding: 0.625rem 1rem;
    }
}

/* Print Styles */
@media print {
    .alumni-container {
        display: block;
    }

    .alumni-id-form-container,
    .alumni-history-container {
        box-shadow: none;
        padding: 0;
        margin-bottom: 2rem;
    }

    .alumni-id-form {
        display: none;
    }

    .alumni-history-card {
        break-inside: avoid;
        border: 1px solid #000;
    }
}
</style>
<body>

<div class="alumni-container">
    <div class="alumni-id-form-container">
        <h2 class="alumni-id-form-title">Add Alumni ID Card</h2>
        
        <?php if ($message): ?>
            <div class="alumni-id-message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="alumni-id-form">
            <div class="alumni-id-form-group">
                <label class="alumni-id-form-label" for="alumni_id_card_no">Alumni ID Card Number</label>
                <input type="text" id="alumni_id_card_no" name="alumni_id_card_no" class="alumni-id-form-input" required>
            </div>

            <div class="alumni-id-form-group">
                <label class="alumni-id-form-label" for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="alumni-id-form-input" required>
            </div>

            <div class="alumni-id-form-group">
                <label class="alumni-id-form-label" for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="alumni-id-form-input" required>
            </div>

            <div class="alumni-id-form-group">
                <label class="alumni-id-form-label" for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name" class="alumni-id-form-input">
            </div>

            <div class="alumni-id-form-group">
                <label class="alumni-id-form-label" for="membership_type">Membership Type</label>
                <select id="membership_type" name="membership_type" class="alumni-id-form-input" required>
                    <option value="">Select Membership Type</option>
                    <option value="Regular">Regular</option>
                    <option value="Premium">Premium</option>
                    <option value="Lifetime">Lifetime</option>
                </select>
            </div>

            <button type="submit" class="alumni-id-submit-btn">Add Alumni ID Card</button>
        </form>
    </div>

    <div class="alumni-history-container">
        <h2 class="alumni-history-title">Recently Added Alumni</h2>
        
        <?php if ($recent_alumni_result && $recent_alumni_result->num_rows > 0): ?>
            <?php while ($row = $recent_alumni_result->fetch_assoc()): ?>
                <div class="alumni-history-card">
                    <h3><?php echo htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']); ?></h3>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($row['alumni_id_card_no']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($row['membership_type']); ?></p>
                    <?php if ($row['middle_name']): ?>
                        <p><strong>Middle Name:</strong> <?php echo htmlspecialchars($row['middle_name']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alumni-history-empty">
                <p>No alumni records found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelector('.alumni-id-form').addEventListener('submit', function(e) {
    const cardNo = document.getElementById('alumni_id_card_no').value;
    const lastName = document.getElementById('last_name').value;
    const firstName = document.getElementById('first_name').value;
    
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