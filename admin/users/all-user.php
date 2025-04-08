<?php
require_once 'main_db.php';

$sql = "SELECT `id`, `username`, `email`, `password`, `created_at`, 
        `first_login`, `session_token`, `two_factor_auth` 
        FROM `users` ORDER BY created_at DESC";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>
<style>
.umain-container {
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    width: 100%;
    border-radius: 20px;
}

.usr-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.usr-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
}

.usr-search {
    width: 300px;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: border-color 0.2s ease;
}

.usr-search:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.usr-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.usr-table th {
    background: #f8fafc;
    padding: 1rem;
    text-align: left;
    color: #64748b;
    font-weight: 500;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.usr-table td {
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    color: #1e293b;
}

.usr-table tbody tr:hover {
    background-color: #f8fafc;
}

.usr-table tbody tr:last-child td {
    border-bottom: none;
}

.usr-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.usr-badge-success {
    background-color: #dcfce7;
    color: #15803d;
}

.usr-badge-warning {
    background-color: #fef3c7;
    color: #d97706;
}

.usr-timestamp {
    color: #64748b;
    font-size: 0.75rem;
}

.usr-email {
    color: #2563eb;
    text-decoration: none;
}

.usr-email:hover {
    text-decoration: underline;
}

.usr-2fa-enabled {
    color: #15803d;
}

.usr-2fa-disabled {
    color: #dc2626;
}

/* Responsive styles */
@media (max-width: 992px) {
    .usr-search {
        width: 250px;
    }
}

@media (max-width: 768px) {
    .umain-container {
        padding: 1rem;
    }
    
    .usr-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .usr-search {
        width: 100%;
    }
    
    .usr-table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .usr-table th,
    .usr-table td {
        min-width: 120px;
    }
}
</style>
<body>
    <div class="umain-container">
        <div class="usr-header">
            <h1 class="usr-title">User Management</h1>
            <input type="text" class="usr-search" id="userSearch" placeholder="Search users...">
        </div>

        <table class="usr-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>First Login</th>
                    <th>2FA Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="usr-email">
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="usr-timestamp">
                                    <?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['first_login']): ?>
                                    <span class="usr-badge usr-badge-success">Completed</span>
                                <?php else: ?>
                                    <span class="usr-badge usr-badge-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['two_factor_auth']): ?>
                                    <span class="usr-2fa-enabled">Enabled</span>
                                <?php else: ?>
                                    <span class="usr-2fa-disabled">Disabled</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">
                            No users found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('userSearch').addEventListener('keyup', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.usr-table tbody tr');
            
            rows.forEach(row => {
                const content = row.textContent.toLowerCase();
                row.style.display = content.includes(searchText) ? '' : 'none';
            });
        });
    </script>
</body>
</html>