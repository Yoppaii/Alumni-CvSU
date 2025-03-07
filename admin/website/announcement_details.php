<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('main_db.php');

$selected_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query_selected = "SELECT * FROM `announcements` WHERE `id` = ?";
$stmt = $mysqli->prepare($query_selected);
$stmt->bind_param("i", $selected_id);
$stmt->execute();
$selected_announcement = $stmt->get_result()->fetch_assoc();

$query_latest = "SELECT `id`, `badge`, `title`, `content`, `created_at`, `status` 
                FROM `announcements` 
                WHERE `id` != ? 
                ORDER BY `created_at` DESC 
                LIMIT 2";
$stmt_latest = $mysqli->prepare($query_latest);
$stmt_latest->bind_param("i", $selected_id);
$stmt_latest->execute();
$latest_announcements = $stmt_latest->get_result();
?>

<div class="announcement-page-layout">
    <?php if ($selected_announcement): ?>
    <section id="announcement-details-section">
        <div class="announcement-details-container">
            <a href="javascript:history.back()" class="back-button">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <div class="announcement-details-header">
                <span class="announcement-badge"><?php echo htmlspecialchars($selected_announcement['badge']); ?></span>
                <h1><?php echo htmlspecialchars($selected_announcement['title']); ?></h1>
                <div class="announcement-meta">
                    <span class="announcement-date">
                        <i class="fas fa-calendar-alt"></i>
                        <?php 
                        $detail_date = new DateTime($selected_announcement['created_at']);
                        echo $detail_date->format('F d, Y'); 
                        ?>
                    </span>
                </div>
            </div>
            <div class="announcement-details-content">
                <?php echo nl2br(htmlspecialchars($selected_announcement['content'])); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section id="latest-announcements-section">
        <div class="announcement-container">
            <h2 class="announcement-heading">
                <i class="fas fa-bullhorn"></i>
                Other Announcements
            </h2>
            <div class="announcement-grid">
                <?php
                if ($latest_announcements->num_rows > 0) {
                    while ($row = $latest_announcements->fetch_assoc()) {
                        $date = new DateTime($row['created_at']);
                        $formatted_date = $date->format('M d, Y');
                        
                        $truncated_content = mb_substr($row['content'], 0, 150);
                        if (strlen($row['content']) > 150) {
                            $truncated_content .= '...';
                        }
                ?>
                        <div class="announcement-item">
                            <div class="announcement-header">
                                <span class="announcement-badge"><?php echo htmlspecialchars($row['badge']); ?></span>
                                <h3 class="announcement-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            </div>
                            <p class="announcement-content"><?php echo htmlspecialchars($truncated_content); ?></p>
                            <div class="announcement-footer">
                                <p class="announcement-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo $formatted_date; ?>
                                </p>
                                <a href="?pages=announcement&id=<?php echo $row['id']; ?>" class="announcement-link">
                                    Read more <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<div class="no-announcements">No other announcements available.</div>';
                }
                ?>
            </div>
        </div>
    </section>
</div>

<style>
    :root {
        --cvsu-primary-green: #006400;
        --cvsu-hover-green: #004d00;
        --cvsu-light-green: #e8f5e8;
        --cvsu-text-dark: #333;
        --cvsu-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .announcement-page-layout {
        display: flex;
        gap: 2rem;
        padding: 1rem;
        max-width: 1600px;
        margin: 0 auto;
    }

    #announcement-details-section {
        flex: 0 0 60%;
        max-width: 60%;
    }

    .announcement-details-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: var(--cvsu-shadow-sm);
        padding: 2rem;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--cvsu-primary-green);
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 1.5rem;
        transition: color 0.3s ease;
    }

    .back-button:hover {
        color: var(--cvsu-hover-green);
    }

    .announcement-details-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--cvsu-light-green);
    }

    .announcement-details-header h1 {
        color: var(--cvsu-primary-green);
        font-size: 2rem;
        margin: 1rem 0;
    }

    .announcement-details-content {
        line-height: 1.6;
        color: var(--cvsu-text-dark);
        font-size: 1rem;
    }

    #latest-announcements-section {
        flex: 0 0 38%;
        max-width: 38%;
    }

    .announcement-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: var(--cvsu-shadow-sm);
        padding: 1.25rem;
    }

    .announcement-heading {
        color: var(--cvsu-primary-green);
        font-size: 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--cvsu-light-green);
    }

    .announcement-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .announcement-item {
        background: white;
        border: 1px solid var(--cvsu-light-green);
        border-radius: 8px;
        padding: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 300px;
        position: relative;
    }

    .announcement-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .announcement-badge {
        background-color: var(--cvsu-primary-green);
        color: white;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
        align-self: flex-start;
    }

    .announcement-title {
        color: var(--cvsu-primary-green);
        font-size: 1rem;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .announcement-content {
        color: var(--cvsu-text-dark);
        line-height: 1.3;
        font-size: 0.85rem;
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.75rem;
    }

    .announcement-footer {
        border-top: 1px solid var(--cvsu-light-green);
        padding-top: 0.5rem;
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .announcement-date {
        color: #666;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .announcement-link {
        color: var(--cvsu-primary-green);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        transition: color 0.3s ease;
    }

    .announcement-link:hover {
        color: var(--cvsu-hover-green);
    }

    .no-announcements {
        text-align: center;
        padding: 2rem;
        color: #666;
        font-size: 0.9rem;
        background: var(--cvsu-light-green);
        border-radius: 8px;
    }

    @media (max-width: 992px) {
        .announcement-page-layout {
            flex-direction: column;
        }

        #announcement-details-section,
        #latest-announcements-section {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 576px) {
        .announcement-container,
        .announcement-details-container {
            padding: 0.75rem;
        }
        
        .announcement-heading {
            font-size: 1.25rem;
        }

        .announcement-item {
            height: 250px;
        }
        
        .announcement-details-header h1 {
            font-size: 1.5rem;
        }
    }
</style>