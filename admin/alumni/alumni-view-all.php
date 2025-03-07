<?php
require_once 'main_db.php';

$sql = "SELECT id, first_name, last_name, middle_name, position, address, 
        telephone, phone_number, second_address, accompanying_persons, 
        user_status, verified 
        FROM user 
        WHERE user_status = 'alumni'
        ORDER BY last_name ASC";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni All Record</title>
    <style>
        :root {
            --alumni-bg: #ffffff;
            --alumni-text: #1e293b;
            --alumni-text-secondary: #64748b;
            --alumni-border: #e2e8f0;
            --alumni-hover: #f8fafc;
            --alumni-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --alumni-card-bg: #ffffff;
            --alumni-stat-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --alumni-input-bg: #ffffff;
            --alumni-table-header: #f8fafc;
            --alumni-verified-bg: #dcfce7;
            --alumni-verified-text: #15803d;
            --alumni-icon-color: #10b981;
        }

        [data-theme="dark"] {
            --alumni-bg: #1e293b;
            --alumni-text: #e2e8f0;
            --alumni-text-secondary: #94a3b8;
            --alumni-border: #334155;
            --alumni-hover: #0f172a;
            --alumni-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            --alumni-card-bg: #1e293b;
            --alumni-stat-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            --alumni-input-bg: #0f172a;
            --alumni-table-header: #1e293b;
            --alumni-verified-bg: rgba(16, 185, 129, 0.2);
            --alumni-verified-text: #10b981;
            --alumni-icon-color: #10b981;
        }

        .amain-container {
            background: var(--alumni-bg);
            box-shadow: var(--alumni-shadow);
            padding: 2rem;
            width: 100%;
            border-radius: 20px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .alumni-header {
            margin-bottom: 2rem;
        }

        .alumni-title {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--alumni-text);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: color 0.3s ease;
        }

        .alumni-title i {
            color: var(--alumni-icon-color);
        }

        .alumni-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .alumni-stats .stat-card {
            background: var(--alumni-card-bg);
            padding: 1.25rem;
            border-radius: 0.75rem;
            box-shadow: var(--alumni-stat-shadow);
            transition: transform 0.2s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .alumni-stats .stat-card:hover {
            transform: translateY(-2px);
        }

        .alumni-stats .stat-card h3 {
            color: var(--alumni-text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }

        .alumni-stats .stat-card p {
            color: var(--alumni-text);
            font-size: 1.5rem;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .filter-section {
            margin-bottom: 2rem;
        }

        .filter-input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            border: 1px solid var(--alumni-border);
            border-radius: 0.5rem;
            background-color: var(--alumni-input-bg);
            color: var(--alumni-text);
            transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }

        .filter-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .filter-input::placeholder {
            color: var(--alumni-text-secondary);
        }

        .alumni-table-container {
            background: var(--alumni-card-bg);
            border-radius: 0.75rem;
            box-shadow: var(--alumni-stat-shadow);
            overflow: hidden;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .alumni-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .alumni-table th {
            background: var(--alumni-table-header);
            color: var(--alumni-text-secondary);
            font-weight: 500;
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid var(--alumni-border);
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        .alumni-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--alumni-border);
            color: var(--alumni-text);
            vertical-align: top;
            transition: border-color 0.3s ease, color 0.3s ease;
        }

        .alumni-table tbody tr:last-child td {
            border-bottom: none;
        }

        .alumni-table tbody tr:hover {
            background-color: var(--alumni-hover);
        }

        .verified-badge {
            display: inline-block;
            margin-left: 0.5rem;
            padding: 0.25rem 0.5rem;
            background-color: var(--alumni-verified-bg);
            color: var(--alumni-verified-text);
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
            vertical-align: middle;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        @media (max-width: 1024px) {
            .amain-container {
                padding: 1.5rem;
            }
            
            .alumni-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .amain-container {
                padding: 1rem;
            }
            
            .alumni-stats {
                grid-template-columns: 1fr;
            }
            
            .alumni-table {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .alumni-title {
                font-size: 1.5rem;
            }
            
            .alumni-table th,
            .alumni-table td {
                padding: 0.75rem;
                min-width: 120px;
            }
        }

        @media (max-width: 480px) {
            .amain-container {
                padding: 0.75rem;
            }
            
            .filter-input {
                padding: 0.625rem 0.75rem;
            }
        }

        @supports (-webkit-touch-callout: none) {
            .sidebar {
                height: -webkit-fill-available;
            }
        }

        .alumni-table-container {
            scrollbar-width: thin;
            scrollbar-color: var(--alumni-text-secondary) transparent;
        }

        .alumni-table-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .alumni-table-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .alumni-table-container::-webkit-scrollbar-thumb {
            background-color: var(--alumni-text-secondary);
            border-radius: 3px;
        }

        @media print {
            .amain-container {
                padding: 0;
                box-shadow: none;
            }

            .filter-section {
                display: none;
            }

            .alumni-table-container {
                box-shadow: none;
            }

            .alumni-table th {
                background-color: white !important;
                color: black !important;
            }

            .alumni-table td {
                color: black !important;
            }

            .verified-badge {
                border: 1px solid #15803d;
            }
        }
    </style>
    <body>
        <div class="amain-container">
            <div class="alumni-header">
                <h1 class="alumni-title">
                    <i class="fas fa-graduation-cap"></i> Alumni
                </h1>
                <div class="alumni-stats">
                    <div class="stat-card">
                        <h3>Total Alumni</h3>
                        <p><?php echo $result->num_rows; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Verified Members</h3>
                        <p><?php 
                            $verified_count = 0;
                            if ($result->num_rows > 0) {
                                $result->data_seek(0);
                                while($row = $result->fetch_assoc()) {
                                    if ($row['verified'] == 1) $verified_count++;
                                }
                                $result->data_seek(0);
                            }
                            echo $verified_count;
                        ?></p>
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <input type="text" id="alumniSearch" class="filter-input" 
                    placeholder="Search alumni by name, position, or location...">
            </div>

            <div class="alumni-table-container">
                <table class="alumni-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Status</th>
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
                                    <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No alumni records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            document.getElementById('alumniSearch').addEventListener('keyup', function(e) {
                const searchText = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('.alumni-table tbody tr');
                
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