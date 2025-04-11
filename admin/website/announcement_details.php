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

// Increased limit to 3 for carousel
$query_latest = "SELECT `id`, `badge`, `title`, `content`, `created_at`, `status` 
                FROM `announcements` 
                WHERE `id` != ? 
                ORDER BY `created_at` DESC 
                LIMIT 3";
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

<style>
    :root {
        --cvsu-primary-green: #006400;
        --cvsu-hover-green: #004d00;
        --cvsu-light-green: #e8f5e8;
        --cvsu-text-dark: #333;
        --cvsu-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
        --cvsu-gold: #D4AF37; /* Gold color for badges */
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

    /* Carousel Styles */
    .announcement-carousel-container {
        position: relative;
    }

    .announcement-carousel {
        display: flex;
        overflow-x: hidden;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
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
        min-width: 100%;
        height: 300px;
        position: relative;
        scroll-snap-align: start;
    }

    .announcement-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .announcement-badge {
        background-color: var(--cvsu-gold);  /* Using gold color variable */
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
        margin: 0.5rem 0;
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
        margin: 0;
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
    
    /* Carousel Navigation */
    .carousel-navigation {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .carousel-prev-btn,
    .carousel-next-btn {
        background-color: var(--cvsu-primary-green);
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    
    .carousel-prev-btn:hover,
    .carousel-next-btn:hover {
        background-color: var(--cvsu-hover-green);
    }
    
    .carousel-indicators {
        display: flex;
        gap: 0.5rem;
    }
    
    .carousel-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ccc;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    
    .carousel-indicator.active {
        background-color: var(--cvsu-primary-green);
    }

    /* Touch swipe support */
    .announcement-carousel {
        touch-action: pan-x;
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
        
        .carousel-navigation {
            gap: 0.5rem;
        }
        
        .carousel-prev-btn,
        .carousel-next-btn {
            width: 28px;
            height: 28px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.announcement-carousel');
    const prevBtn = document.querySelector('.carousel-prev-btn');
    const nextBtn = document.querySelector('.carousel-next-btn');
    const indicators = document.querySelector('.carousel-indicators');
    const items = carousel.querySelectorAll('.announcement-item');
    
    let currentIndex = 0;
    
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
        const items = carousel.querySelectorAll('.announcement-item');
        if (index < 0) index = items.length - 1;
        if (index >= items.length) index = 0;
        
        currentIndex = index;
        const itemWidth = items[0].offsetWidth;
        carousel.scrollTo({
            left: itemWidth * currentIndex,
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
    
    // Touch swipe support
    let touchStartX = 0;
    let touchEndX = 0;
    
    carousel.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, {passive: true});
    
    carousel.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, {passive: true});
    
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
            const itemWidth = items[0].offsetWidth;
            currentIndex = Math.round(carousel.scrollLeft / itemWidth);
            updateIndicators();
        }, 100);
    }, {passive: true});
});
</script>