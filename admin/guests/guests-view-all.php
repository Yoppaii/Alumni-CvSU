<?php
require_once 'main_db.php';

$sql = "SELECT id, first_name, last_name, middle_name, position, address, 
        telephone, phone_number, second_address, accompanying_persons, 
        user_status, verified 
        FROM user 
        WHERE user_status = 'guest'
        ORDER BY last_name ASC";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest All Records</title>
<style>
/* Main Container Style */
.gmain-container {
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    width: 100%;
    border-radius: 20px;
}

/* Header Section */
.guest-header {
    margin-bottom: 2rem;
}

.guest-title {
    font-size: 1.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1.5rem;
}

.guest-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.guest-stats .stat-card {
    background: #ffffff;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.guest-stats .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.guest-stats .stat-card h3 {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.guest-stats .stat-card p {
    color: #1e293b;
    font-size: 1.75rem;
    font-weight: 600;
}

.filter-section {
    margin-bottom: 2rem;
}

.filter-input {
    width: 100%;
    padding: 0.875rem 1.25rem;
    font-size: 0.875rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    background-color: #ffffff;
    transition: all 0.2s ease;
}

.filter-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.guest-table-container {
    background: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.guest-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.guest-table th {
    background: #f8fafc;
    color: #64748b;
    font-weight: 500;
    text-align: left;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.guest-table td {
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    color: #1e293b;
    vertical-align: top;
}

.guest-table tbody tr:last-child td {
    border-bottom: none;
}

.guest-table tbody tr:hover {
    background-color: #f8fafc;
}

.verified-badge {
    display: inline-block;
    margin-left: 0.5rem;
    padding: 0.25rem 0.5rem;
    background-color: #dcfce7;
    color: #15803d;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
    vertical-align: middle;
}

.guest-table td small {
    display: block;
    color: #64748b;
    margin-top: 0.375rem;
    font-size: 0.75rem;
}
.guest-title {
    font-size: 1.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.guest-title i {
    color: #10b981; /* This is a green color that matches your theme */
}
/* Responsive Adjustments */
@media (max-width: 1200px) {
    .gmain-container {
        padding: 1.5rem;
    }
    
    .guest-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .gmain-container {
        padding: 1rem;
    }
    
    .guest-title {
        font-size: 1.5rem;
    }
    
    .guest-stats {
        grid-template-columns: 1fr;
    }
    
    .guest-table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .guest-table th,
    .guest-table td {
        padding: 0.75rem;
        min-width: 120px;
    }
    
    .filter-input {
        padding: 0.75rem 1rem;
    }
}

@media (max-width: 480px) {
    .gmain-container {
        padding: 0.75rem;
    }
    
    .verified-badge {
        display: block;
        margin: 0.25rem 0 0 0;
        text-align: center;
    }
}
</style>
<body>
    <div class="gmain-container">
        <div class="guest-header">
            <h1 class="guest-title">
                <i class="fas fa-users"></i> Guest
            </h1>
            <div class="guest-stats">
                <div class="stat-card">
                    <h3>Total Guests</h3>
                    <p><?php echo $result->num_rows; ?></p>
                </div>
                <div class="stat-card">
                    <h3>With Accompanying Persons</h3>
                    <p><?php 
                        $accompanying_count = 0;
                        if ($result->num_rows > 0) {
                            $result->data_seek(0);
                            while($row = $result->fetch_assoc()) {
                                if (!empty($row['accompanying_persons'])) $accompanying_count++;
                            }
                            $result->data_seek(0);
                        }
                        echo $accompanying_count;
                    ?></p>
                </div>
            </div>
        </div>

        <div class="filter-section">
            <input type="text" id="guestSearch" class="filter-input" 
                   placeholder="Search guests by name, position, or location...">
        </div>

        <div class="guest-table-container">
            <table class="guest-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Accompanying Persons</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php 
                                        echo htmlspecialchars($row['first_name'] . ' ' . 
                                            ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . 
                                            $row['last_name']);
                                        if ($row['verified'] == 1) {
                                            echo '<span class="verified-badge">Verified</span>';
                                        }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['position'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                        if ($row['phone_number']) {
                                            echo htmlspecialchars($row['phone_number']);
                                        }
                                        if ($row['telephone']) {
                                            echo $row['phone_number'] ? '<br>' : '';
                                            echo htmlspecialchars($row['telephone']);
                                        }
                                        if (!$row['phone_number'] && !$row['telephone']) {
                                            echo 'N/A';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        echo htmlspecialchars($row['address'] ?? 'N/A');
                                        if ($row['second_address']) {
                                            echo '<br><small>Secondary: ' . htmlspecialchars($row['second_address']) . '</small>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        echo !empty($row['accompanying_persons']) 
                                            ? htmlspecialchars($row['accompanying_persons']) 
                                            : 'None';
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No guest records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('guestSearch').addEventListener('keyup', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.guest-table tbody tr');
            
            rows.forEach(row => {
                const content = row.textContent.toLowerCase();
                if (content.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>