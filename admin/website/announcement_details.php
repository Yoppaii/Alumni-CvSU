<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('main_db.php');

// Check if we're viewing a specific announcement or the main announcements page
$is_detail_view = isset($_GET['id']);
$selected_id = $is_detail_view ? intval($_GET['id']) : 0;

// If we're viewing a specific announcement
if ($is_detail_view) {
    $query_selected = "SELECT * FROM `announcements` WHERE `id` = ?";
    $stmt = $mysqli->prepare($query_selected);
    $stmt->bind_param("i", $selected_id);
    $stmt->execute();
    $selected_announcement = $stmt->get_result()->fetch_assoc();

    // Get other announcements for the carousel
    $query_latest = "SELECT `id`, `badge`, `title`, `content`, `created_at`, `status` 
                    FROM `announcements` 
                    WHERE `id` != ? 
                    ORDER BY `created_at` DESC 
                    ";
    $stmt_latest = $mysqli->prepare($query_latest);
    $stmt_latest->bind_param("i", $selected_id);
    $stmt_latest->execute();
    $latest_announcements = $stmt_latest->get_result();
} else {
    // Main announcements page - get all announcements
    $query_all = "SELECT `id`, `badge`, `title`, `content`, `created_at`, `status` 
                 FROM `announcements` 
                 ORDER BY `created_at` DESC";
    $result_all = $mysqli->query($query_all);
}
?>

<?php if ($is_detail_view): ?>
    <!-- DETAIL VIEW LAYOUT (when viewing a specific announcement) -->
    <div class="announcement-page-layout detail-view">
        <?php if ($selected_announcement): ?>
            <section id="announcement-details-section">
                <div class="announcement-details-container">
                    <a href="?pages=announcement" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back to Announcements
                    </a>
                    <div class="announcement-details-header">
                        <span id="selected-announcement-badge"><?php echo htmlspecialchars($selected_announcement['badge']); ?></span>
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
                <!-- Carousel wrapper -->
                <div class="announcement-carousel-container">
                    <div class="announcement-carousel">
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

                    <!-- Carousel navigation -->
                    <div class="carousel-navigation">
                        <button class="carousel-prev-btn" aria-label="Previous announcement">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="carousel-indicators"></div>
                        <button class="carousel-next-btn" aria-label="Next announcement">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php else: ?>
    <!-- LISTING VIEW LAYOUT (when viewing all announcements) -->
    <div class="announcement-page-layout listing-view">
        <div class="announcements-grid-container">
            <div class="announcements-header">
                <h1 class="announcements-title">
                    <i class="fas fa-bullhorn"></i>
                    Campus Announcements
                </h1>
                <p class="announcements-subtitle">Stay updated with the latest news and events from our campus</p>
            </div>

            <div class="announcements-grid">
                <?php
                if ($result_all && $result_all->num_rows > 0) {
                    while ($row = $result_all->fetch_assoc()) {
                        $date = new DateTime($row['created_at']);
                        $formatted_date = $date->format('M d, Y');

                        $truncated_content = mb_substr($row['content'], 0, 200);
                        if (strlen($row['content']) > 200) {
                            $truncated_content .= '...';
                        }
                ?>
                        <div class="announcement-card">
                            <div class="announcement-card-header">
                                <span class="announcement-badge"><?php echo htmlspecialchars($row['badge']); ?></span>
                                <h2 class="announcement-card-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                            </div>
                            <div class="announcement-card-content">
                                <p><?php echo htmlspecialchars($truncated_content); ?></p>
                            </div>
                            <div class="announcement-card-footer">
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
                    echo '<div class="no-announcements">No announcements available at this time.</div>';
                }
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    :root {
        --cvsu-gold: #D4AF37;
        --cvsu-primary-green: #006400;
        --cvsu-hover-green: #004d00;
        --cvsu-light-green: #e8f5e8;
        --cvsu-text-dark: #333;
        --cvsu-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
        --cvsu-shadow-md: 0 4px 8px rgba(0, 0, 0, 0.12);
        --cvsu-badge-gradient: linear-gradient(135deg, #D4AF37, #FFD700);
        --cvsu-btn-gradient: linear-gradient(135deg, #006400, #008000);
        --cvsu-transition: all 0.3s ease;
        --font-primary: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
    }

    /* Global styles */
    .announcement-page-layout {
        padding: 2rem 1rem 10rem 1rem;
        max-width: 1200px;
        margin: 0 auto;
        font-family: var(--font-primary);
    }

    /* DETAIL VIEW STYLES */
    .announcement-page-layout.detail-view {
        display: flex;
        gap: 2.5rem;
    }

    #announcement-details-section {
        flex: 0 0 60%;
        max-width: 60%;
    }

    .announcement-details-container {
        background-color: white;
        border-radius: 12px;
        box-shadow: var(--cvsu-shadow-md);
        padding: 2.5rem;
        transition: var(--cvsu-transition);
        border-top: 4px solid var(--cvsu-primary-green);
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--cvsu-primary-green);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 1.5rem;
        transition: var(--cvsu-transition);
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        background-color: var(--cvsu-light-green);
        font-size: 0.9rem;
    }

    .back-button:hover {
        color: white;
        background-color: var(--cvsu-primary-green);
        transform: translateX(-3px);
    }

    .back-button i {
        transition: var(--cvsu-transition);
    }

    .back-button:hover i {
        transform: translateX(-3px);
    }

    .announcement-details-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--cvsu-light-green);
        position: relative;
    }

    .announcement-details-header h1 {
        color: var(--cvsu-primary-green);
        font-size: 2.2rem;
        margin: 1rem 0;
        line-height: 1.3;
        font-weight: 700;
    }

    .announcement-details-content {
        line-height: 1.8;
        color: var(--cvsu-text-dark);
        font-size: 1.05rem;
    }

    /* Sidebar with other announcements */
    #latest-announcements-section {
        flex: 0 0 38%;
        max-width: 38%;
    }

    .announcement-container {
        background-color: white;
        border-radius: 12px;
        box-shadow: var(--cvsu-shadow-md);
        padding: 1.75rem;
        border-top: 4px solid var(--cvsu-gold);
    }

    .announcement-heading {
        color: var(--cvsu-primary-green);
        font-size: 1.6rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--cvsu-light-green);
        font-weight: 700;
    }

    .announcement-heading i {
        color: var(--cvsu-gold);
    }

    /* Carousel Styles */
    .announcement-carousel-container {
        position: relative;
        margin-top: 1.5rem;
    }

    .announcement-carousel {
        display: flex;
        overflow-x: hidden;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        gap: 1rem;
        padding-bottom: 0.5rem;
    }

    .announcement-item {
        background: white;
        border: 1px solid rgba(0, 100, 0, 0.1);
        border-radius: 10px;
        padding: 1.5rem;
        transition: var(--cvsu-transition);
        display: flex;
        flex-direction: column;
        min-width: 100%;
        height: 320px;
        position: relative;
        scroll-snap-align: start;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .announcement-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border-color: var(--cvsu-primary-green);
    }

    #selected-announcement-badge,
    .announcement-badge {
        background: var(--cvsu-badge-gradient);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
        align-self: flex-start;
        display: inline-block;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(212, 175, 55, 0.3);
        text-transform: uppercase;
    }

    .announcement-title {
        color: var(--cvsu-primary-green);
        font-size: 1.2rem;
        margin: 0.75rem 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 700;
        line-height: 1.4;
    }

    .announcement-content {
        color: var(--cvsu-text-dark);
        line-height: 1.6;
        font-size: 0.95rem;
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 1rem;
    }

    .announcement-footer {
        border-top: 1px solid var(--cvsu-light-green);
        padding-top: 0.75rem;
        position: absolute;
        bottom: 1.5rem;
        left: 1.5rem;
        right: 1.5rem;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .announcement-date {
        color: #666;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        font-weight: 500;
    }

    .announcement-date i {
        color: var(--cvsu-gold);
    }

    .announcement-link {
        color: var(--cvsu-primary-green);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--cvsu-transition);
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
    }

    .announcement-link:hover {
        color: white;
        background-color: var(--cvsu-primary-green);
    }

    .announcement-link i {
        transition: var(--cvsu-transition);
    }

    .announcement-link:hover i {
        transform: translateX(3px);
    }

    .no-announcements {
        text-align: center;
        padding: 2.5rem;
        color: #666;
        font-size: 1rem;
        background: var(--cvsu-light-green);
        border-radius: 10px;
        font-weight: 500;
    }

    /* Carousel Navigation */
    .carousel-navigation {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .carousel-prev-btn,
    .carousel-next-btn {
        background: var(--cvsu-btn-gradient);
        color: white;
        border: none;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--cvsu-transition);
        box-shadow: 0 2px 6px rgba(0, 100, 0, 0.3);
    }

    .carousel-prev-btn:hover,
    .carousel-next-btn:hover {
        transform: scale(1.1);
    }

    .carousel-indicators {
        display: flex;
        gap: 0.75rem;
    }

    .carousel-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #ddd;
        cursor: pointer;
        transition: var(--cvsu-transition);
    }

    .carousel-indicator.active {
        background-color: var(--cvsu-primary-green);
        transform: scale(1.2);
    }

    /* LISTING VIEW STYLES (for page=announcement without id) */
    .announcement-page-layout.listing-view {
        padding: 2rem 1.5rem 4rem 1.5rem;
    }

    .announcements-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--cvsu-light-green);
    }

    .announcements-title {
        color: var(--cvsu-primary-green);
        font-size: 2.4rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .announcements-title i {
        color: var(--cvsu-gold);
    }

    .announcements-subtitle {
        color: #666;
        font-size: 1.1rem;
    }

    .announcements-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
    }

    .announcement-card {
        background: white;
        border-radius: 12px;
        box-shadow: var(--cvsu-shadow-md);
        border-top: 4px solid var(--cvsu-primary-green);
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        height: 380px;
        position: relative;
        transition: var(--cvsu-transition);
    }

    .announcement-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .announcement-card:nth-child(3n+1) {
        border-top-color: var(--cvsu-primary-green);
    }

    .announcement-card:nth-child(3n+2) {
        border-top-color: var(--cvsu-gold);
    }

    .announcement-card:nth-child(3n) {
        border-top-color: #3498db;
    }

    .announcement-card-header {
        margin-bottom: 1.25rem;
    }

    .announcement-card-title {
        color: var(--cvsu-primary-green);
        font-size: 1.4rem;
        margin: 0.75rem 0;
        line-height: 1.4;
        font-weight: 700;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .announcement-card-content {
        flex-grow: 1;
        overflow: hidden;
    }

    .announcement-card-content p {
        color: var(--cvsu-text-dark);
        line-height: 1.7;
        font-size: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 7;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .announcement-card-footer {
        border-top: 1px solid var(--cvsu-light-green);
        padding-top: 1rem;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Responsive styles */
    @media (max-width: 992px) {
        .announcement-page-layout.detail-view {
            flex-direction: column;
            padding: 1.5rem 1rem 6rem 1rem;
        }

        #announcement-details-section,
        #latest-announcements-section {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .announcement-details-container,
        .announcement-container {
            padding: 1.5rem;
        }

        .announcement-item {
            height: 300px;
        }

        .announcements-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .announcements-grid {
            grid-template-columns: 1fr;
            max-width: 500px;
            margin: 0 auto;
        }
    }

    @media (max-width: 576px) {

        .announcement-page-layout.detail-view,
        .announcement-page-layout.listing-view {
            padding: 1rem 0.75rem 4rem 0.75rem;
        }

        .announcement-container,
        .announcement-details-container {
            padding: 1.25rem;
            border-radius: 8px;
        }

        .announcement-heading {
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .announcement-details-header h1 {
            font-size: 1.6rem;
        }

        .announcement-item {
            height: 280px;
            padding: 1.25rem;
        }

        .announcement-footer {
            left: 1.25rem;
            right: 1.25rem;
            bottom: 1.25rem;
        }

        .carousel-navigation {
            gap: 1rem;
        }

        .carousel-prev-btn,
        .carousel-next-btn {
            width: 32px;
            height: 32px;
        }

        .announcements-title {
            font-size: 1.8rem;
        }

        .announcements-subtitle {
            font-size: 0.95rem;
        }

        .announcement-card {
            height: 350px;
            padding: 1.25rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize carousel if it exists (for detail view)
        const carousel = document.querySelector('.announcement-carousel');
        if (carousel) {
            const prevBtn = document.querySelector('.carousel-prev-btn');
            const nextBtn = document.querySelector('.carousel-next-btn');
            const indicators = document.querySelector('.carousel-indicators');
            const items = carousel.querySelectorAll('.announcement-item');

            let currentIndex = 0;

            // Show/hide navigation buttons based on items count
            if (items.length <= 1) {
                document.querySelector('.carousel-navigation').style.display = 'none';
            }

            // Create indicator dots
            items.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.classList.add('carousel-indicator');
                if (index === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(index));
                indicators.appendChild(dot);
            });

            // Update indicators
            function updateIndicators() {
                document.querySelectorAll('.carousel-indicator').forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }

            // Navigate to specific slide
            function goToSlide(index) {
                if (items.length === 0) return;

                if (index < 0) index = items.length - 1;
                if (index >= items.length) index = 0;

                currentIndex = index;
                const itemWidth = items[0].offsetWidth;
                const scrollPosition = itemWidth * currentIndex + (currentIndex * parseInt(getComputedStyle(carousel).gap));

                carousel.scrollTo({
                    left: scrollPosition,
                    behavior: 'smooth'
                });

                updateIndicators();
            }

            // Previous button
            prevBtn.addEventListener('click', () => {
                goToSlide(currentIndex - 1);
            });

            // Next button
            nextBtn.addEventListener('click', () => {
                goToSlide(currentIndex + 1);
            });

            // Auto slide every 5 seconds
            let autoSlideInterval = setInterval(() => {
                goToSlide(currentIndex + 1);
            }, 5000);

            // Pause auto slide on hover
            carousel.addEventListener('mouseenter', () => {
                clearInterval(autoSlideInterval);
            });

            carousel.addEventListener('mouseleave', () => {
                autoSlideInterval = setInterval(() => {
                    goToSlide(currentIndex + 1);
                }, 5000);
            });

            // Touch swipe support
            let touchStartX = 0;
            let touchEndX = 0;

            carousel.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
                // Pause auto slide on touch
                clearInterval(autoSlideInterval);
            }, {
                passive: true
            });

            carousel.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();

                // Resume auto slide after touch
                autoSlideInterval = setInterval(() => {
                    goToSlide(currentIndex + 1);
                }, 5000);
            }, {
                passive: true
            });

            function handleSwipe() {
                const swipeThreshold = 50;
                if (touchStartX - touchEndX > swipeThreshold) {
                    // Swipe left - go to next
                    goToSlide(currentIndex + 1);
                } else if (touchEndX - touchStartX > swipeThreshold) {
                    // Swipe right - go to previous
                    goToSlide(currentIndex - 1);
                }
            }

            // Detect scroll end to update indicators
            carousel.addEventListener('scroll', function() {
                clearTimeout(carousel.scrollTimer);
                carousel.scrollTimer = setTimeout(function() {
                    if (items.length === 0) return;

                    const itemWidth = items[0].offsetWidth;
                    const gapWidth = parseInt(getComputedStyle(carousel).gap);
                    const totalItemWidth = itemWidth + gapWidth;

                    currentIndex = Math.round(carousel.scrollLeft / totalItemWidth);
                    updateIndicators();
                }, 100);
            }, {
                passive: true
            });
        }
    });
</script>