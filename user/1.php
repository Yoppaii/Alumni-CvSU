<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'user/check_security_access.php';
require('main_db.php');

// Modified query to get ALL users instead of just the current user
$sql = "SELECT id, user_id, first_name, last_name, middle_name, position, address, telephone, 
        phone_number, second_address, accompanying_persons, user_status, verified, alumni_id_card_no 
        FROM user";
$result = $mysqli->query($sql);

// Check if we have any users
if ($result->num_rows == 0) {
    echo "No users found in the database.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
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

.account-card {
    background: white;
    border-radius: 8px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: 20px;
    max-width: auto;
    margin: 20px auto;
}

.account-details-section {
    padding: 24px;
}

.account-details-section h2 {
    font-size: 18px;
    color: #111827;
    margin: 0 0 16px 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
}

.account-details-section h2::before {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    color: var(--primary-color);
}

.account-details-section h2:nth-of-type(1)::before {
    content: "\f007"; /* user icon */
}

.account-header {
    padding: 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.account-header h1 {
    font-size: 24px;
    color: #111827;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.account-header h1 i {
    color: var(--primary-color);
}

/* Table styles */
.users-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 14px;
}

.users-table th,
.users-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.users-table th {
    background-color: #f9fafb;
    color: #4b5563;
    font-weight: 600;
    position: sticky;
    top: 0;
}

.users-table tr:nth-child(even) {
    background-color: #f9fafb;
}

.users-table tr:hover {
    background-color: #f3f4f6;
}

/* Responsive table */
.table-container {
    overflow-x: auto;
}

/* Pagination controls */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 8px;
}

.pagination-btn {
    padding: 8px 12px;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.pagination-btn:hover {
    background-color: #f9fafb;
}

.pagination-btn.active {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Search field */
.search-container {
    margin-bottom: 20px;
}

.search-field {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
}
</style>
<body>
    <div class="account-card">
        <div class="account-header">
            <h1><i class="fas fa-users"></i> All Users</h1>
        </div>
        <div class="account-details-section">
            <div class="search-container">
                <input type="text" id="searchField" class="search-field" placeholder="Search users...">
            </div>
            
            <div class="table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Alumni ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td>
                                <?php 
                                    echo htmlspecialchars($user['first_name']) . ' ';
                                    if (!empty($user['middle_name'])) {
                                        echo htmlspecialchars($user['middle_name'][0]) . '. ';
                                    }
                                    echo htmlspecialchars($user['last_name']);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['position'] ?: 'Not provided'); ?></td>
                            <td><?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></td>
                            <td><?php echo htmlspecialchars($user['phone_number'] ?: 'Not provided'); ?></td>
                            <td><?php echo htmlspecialchars($user['user_status']); ?></td>
                            <td><?php echo $user['verified'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($user['alumni_id_card_no'] ?: 'Not provided'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn">Next <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <script>
        // Basic search functionality
        document.getElementById('searchField').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.querySelector('.users-table');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) { // Start at 1 to skip header row
                let found = false;
                const cells = rows[i].getElementsByTagName('td');
                
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.includes(searchValue)) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });
    </script>
</body>
</html>