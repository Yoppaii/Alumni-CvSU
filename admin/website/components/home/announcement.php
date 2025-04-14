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
        <a href="?pages=news-features" class="announcement-view-all">
            See all announcements
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</section>