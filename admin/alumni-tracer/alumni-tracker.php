<?php
require_once 'main_db.php';

$totalQuery = "SELECT COUNT(*) as total FROM personal_info";
$totalResult = $mysqli->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalResponses = $totalRow['total'];

$civilStatusQuery = "SELECT 
    COALESCE(civil_status, 'Not Specified') as civil_status, 
    COUNT(*) as count 
FROM personal_info 
GROUP BY civil_status";
$civilStatusResult = $mysqli->query($civilStatusQuery);
$civilStatusData = [];
while($row = $civilStatusResult->fetch_assoc()) {
    $civilStatusData[] = $row;
}

$sexQuery = "SELECT 
    COALESCE(sex, 'Not Specified') as sex, 
    COUNT(*) as count 
FROM personal_info 
GROUP BY sex";
$sexResult = $mysqli->query($sexQuery);
$sexData = [];
while($row = $sexResult->fetch_assoc()) {
    $sexData[] = $row;
}

$userQuery = "SELECT 
    pi.*,
    u.first_name,
    u.last_name,
    u.middle_name,
    u.position,
    u.address as user_address,
    u.telephone as user_telephone,
    u.phone_number,
    u.second_address,
    u.user_status,
    u.verified,
    us.email,
    us.username,
    us.created_at as account_created,
    'Yes' as has_responded,
    pi.created_at as response_date,
    CASE 
        WHEN us.id IS NULL THEN 'No matching account'
        ELSE 'Found'
    END as user_record_status
FROM personal_info pi
LEFT JOIN users us ON pi.user_id = us.id
LEFT JOIN user u ON us.id = u.user_id
ORDER BY pi.created_at DESC";

$userResult = $mysqli->query($userQuery);
$userDetails = [];
while($row = $userResult->fetch_assoc()) {
    $userDetails[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response Tracker</title>
<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--text-primary);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stats-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stats-card h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .stats-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .distribution-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .distribution-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .distribution-list li:last-child {
        border-bottom: none;
    }

    .distribution-list span {
        color: var(--text-primary);
    }

    .distribution-list span:last-child {
        font-weight: 600;
        color: var(--primary-color);
    }

    .table-container {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1rem;
        overflow-x: auto;
    }

    .responses-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .responses-table th {
        background: var(--bg-secondary);
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        transition: var(--transition);
    }

    .responses-table th:hover {
        background: var(--primary-light);
        color: var(--primary-color);
    }

    .responses-table td {
        padding: 1rem;
        border-top: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .responses-table tr:hover td {
        background: var(--bg-secondary);
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .badge-success {
        background: var(--primary-light);
        color: var(--primary-color);
    }

    .badge-warning {
        background: #fff7ed;
        color: #c2410c;
    }

    .no-match {
        color: var(--danger-color);
        font-style: italic;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-container {
            margin: 0 -1rem;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        .responses-table th,
        .responses-table td {
            padding: 0.75rem;
        }
    }

    [data-theme="dark"] .stats-card {
        background: var(--bg-primary);
        border-color: var(--border-color);
    }

    [data-theme="dark"] .table-container {
        background: var(--bg-primary);
        border-color: var(--border-color);
    }

    [data-theme="dark"] .responses-table th {
        background: var(--bg-light);
    }

    [data-theme="dark"] .responses-table td {
        border-color: var(--border-color);
    }

    [data-theme="dark"] .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #4ade80;
    }

    [data-theme="dark"] .badge-warning {
        background: rgba(234, 88, 12, 0.2);
        color: #fb923c;
    }

    [data-theme="dark"] .no-match {
        color: #f87171;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .view-all-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background-color: var(--primary-color, #2563eb);
        color: white;
        text-decoration: none;
        border-radius: var(--radius-lg, 0.5rem);
        font-size: 0.875rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }

    .view-all-btn:hover {
        background-color: var(--primary-dark, #1d4ed8);
    }

    [data-theme="dark"] .view-all-btn {
        background-color: var(--primary-color, #3b82f6);
    }

    [data-theme="dark"] .view-all-btn:hover {
        background-color: var(--primary-dark, #2563eb);
    }
</style>
</head>
<body>
    <div class="container">
        <h1 class="section-title">Alumni Response Tracker</h1>
        
        <div class="stats-grid">
            <div class="stats-card">
                <h3>Total Responses</h3>
                <div class="stats-number"><?php echo $totalResponses; ?></div>
                <p class="stats-label">Total form submissions</p>
            </div>
            
            <div class="stats-card">
                <h3>Civil Status Distribution</h3>
                <ul class="distribution-list">
                    <?php foreach($civilStatusData as $status): ?>
                    <li>
                        <span><?php echo ucfirst($status['civil_status']); ?></span>
                        <span><?php echo $status['count']; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="stats-card">
                <h3>Gender Distribution</h3>
                <ul class="distribution-list">
                    <?php foreach($sexData as $gender): ?>
                    <li>
                        <span><?php echo ucfirst($gender['sex']); ?></span>
                        <span><?php echo $gender['count']; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="table-container">
        <div class="section-header">
            <h2 class="section-title">Response Details</h2>
            <a href="?section=all-response" class="view-all-btn">View All Respondents</a>
        </div>
            <table class="responses-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact Info</th>
                        <th>Account Status</th>
                        <th>Response Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($userDetails)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">No responses found</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($userDetails as $user): ?>
                        <tr>
                            <td>
                                <?php 
                                    if ($user['user_record_status'] === 'No matching account') {
                                        echo '<span class="no-match">No account (ID: ' . htmlspecialchars($user['user_id']) . ')</span>';
                                    } else {
                                        echo htmlspecialchars($user['username'] ?? 'N/A');
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    $firstName = $user['first_name'] ?? '';
                                    $middleName = $user['middle_name'] ?? '';
                                    $lastName = $user['last_name'] ?? '';
                                    $fullName = trim("$lastName, $firstName $middleName");
                                    echo htmlspecialchars($fullName ?: 'N/A');
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>

                            <td>
                                Phone: <?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?><br>
                                Tel: <?php echo htmlspecialchars($user['user_telephone'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $user['user_record_status'] === 'Found' ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo htmlspecialchars($user['user_record_status']); ?>
                                </span>
                            </td>
                            <td><?php echo $user['response_date'] ? date('M d, Y', strtotime($user['response_date'])) : 'N/A'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
            
            const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
                v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
            )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

            document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
                const table = th.closest('table');
                const tbody = table.querySelector('tbody');
                Array.from(tbody.querySelectorAll('tr'))
                    .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                    .forEach(tr => tbody.appendChild(tr));
            })));
        });
    </script>
</body>
</html>