<?php
require_once('main_db.php');

$query = "SELECT `id`, `badge`, `title`, `content`, `created_at`, `status` FROM `announcements` WHERE 1";
$result = $mysqli->query($query);
?>

<section id="announcement-section">
    <div class="announcement-container">
        <h2 class="announcement-heading">
            <i class="fas fa-bullhorn"></i>
            Important Announcements
        </h2>
        <div class="announcement-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
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
                echo '<div class="no-announcements">No announcements available.</div>';
            }
            ?>
        </div>
        <a href="all_announcements.php" class="announcement-view-all">
            See all announcements
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</section>

<style>
:root {
    --cvsu-primary-green: #006400;
    --cvsu-hover-green: #004d00;
    --cvsu-light-green: #e8f5e8;
    --cvsu-text-dark: #333;
    --cvsu-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
}

#announcement-section {
    max-width: auto;
    margin: 1rem auto;
    padding: 0 1rem;
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
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
    overflow-x: auto;
}

.announcement-item {
    background: white;
    border: 1px solid var(--cvsu-light-green);
    border-radius: 8px;
    padding: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    min-width: 250px;
    height: 300px;
    position: relative;
}

.announcement-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.announcement-header {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
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

.announcement-view-all {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: var(--cvsu-primary-green);
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border: 2px solid var(--cvsu-light-green);
    border-radius: 8px;
    transition: all 0.3s ease;
    margin: 0 auto;
    width: fit-content;
    font-size: 0.9rem;
}

.announcement-view-all:hover {
    background-color: var(--cvsu-light-green);
    transform: translateY(-2px);
}

.no-announcements {
    text-align: center;
    padding: 2rem;
    color: #666;
    font-size: 0.9rem;
    background: var(--cvsu-light-green);
    border-radius: 8px;
    grid-column: 1 / -1;
}

@media (max-width: 1200px) {
    .announcement-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .announcement-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .announcement-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .announcement-item {
        min-width: 200px;
    }
}

@media (max-width: 576px) {
    .announcement-grid {
        grid-template-columns: 1fr;
    }
    
    .announcement-container {
        padding: 0.75rem;
    }
    
    .announcement-heading {
        font-size: 1.25rem;
    }

    .announcement-item {
        height: 250px;
    }
}
</style>