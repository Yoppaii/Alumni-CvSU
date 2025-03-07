<?php
require_once 'main_db.php';

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
    pi.created_at as response_date
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
    <title>All Response Details</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color, #2563eb);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-lg, 0.5rem);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .search-container {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .search-bar {
            flex: 1;
            max-width: 500px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-left: 2.5rem;
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: var(--radius-lg, 0.5rem);
            font-size: 0.875rem;
            background-color: var(--bg-primary, white);
            color: var(--text-primary, #1f2937);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary, #4b5563);
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary, #4b5563);
            background: var(--bg-primary, white);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: var(--radius-lg, 0.5rem);
            display: none;
        }

        .detail-card {
            background: var(--bg-primary, white);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: var(--radius-lg, 0.5rem);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            margin-bottom: 0.5rem;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-secondary, #4b5563);
        }

        .detail-value {
            color: var(--text-primary, #1f2937);
        }

        [data-theme="dark"] .detail-card,
        [data-theme="dark"] .search-input,
        [data-theme="dark"] .no-results {
            background: var(--bg-primary);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .search-input {
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="section-header">
            <h1>All Response Details</h1>
            <a href="?section=Alumni-tracker" class="back-btn">← Back to Dashboard</a>
        </div>

        <div class="search-container">
            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-input" placeholder="Search by name, email, position...">
            </div>
        </div>

        <div id="noResults" class="no-results">
            No matching results found.
        </div>

        <div id="detailsContainer">
            <?php foreach($userDetails as $user): ?>
                <div class="detail-card" data-searchable>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value">
                                <?php 
                                    $fullName = trim(($user['last_name'] ?? '') . ', ' . 
                                        ($user['first_name'] ?? '') . ' ' . 
                                        ($user['middle_name'] ?? ''));
                                    echo htmlspecialchars($fullName ?: 'N/A');
                                ?>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Position</div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['position'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Phone Number</div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Telephone</div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['user_telephone'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Address</div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['user_address'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Secondary Address</div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['second_address'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Account Created</div>
                            <div class="detail-value">
                                <?php echo $user['account_created'] ? date('M d, Y', strtotime($user['account_created'])) : 'N/A'; ?>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Response Date</div>
                            <div class="detail-value">
                                <?php echo $user['response_date'] ? date('M d, Y', strtotime($user['response_date'])) : 'N/A'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const detailCards = document.querySelectorAll('.detail-card');
            const noResults = document.getElementById('noResults');

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                let hasResults = false;

                detailCards.forEach(card => {
                    const content = card.textContent.toLowerCase();
                    if (content.includes(searchTerm)) {
                        card.style.display = 'block';
                        hasResults = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                noResults.style.display = hasResults ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>