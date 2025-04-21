<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('main_db.php');

$selected_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query_selected = "SELECT * FROM `events` WHERE `id` = ?";
$stmt = $mysqli->prepare($query_selected);
$stmt->bind_param("i", $selected_id);
$stmt->execute();
$selected_event = $stmt->get_result()->fetch_assoc();

// Get latest 3 events for carousel (excluding the current one)
$query_latest = "SELECT `id`, `day`, `month`, `title`, `venue`, `description`, `created_at` 
                FROM `events` 
                WHERE `id` != ? 
                ORDER BY `created_at` DESC 
                LIMIT 3";
$stmt_latest = $mysqli->prepare($query_latest);
$stmt_latest->bind_param("i", $selected_id);
$stmt_latest->execute();
$latest_events = $stmt_latest->get_result();
?>

<div class="events-page-layout">

    <?php if ($selected_event): ?>
        <section id="event-details-section">
            <div class="event-details-container">
                <a href="javascript:history.back()" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <div class="event-details-header">
                    <div class="event-date-badge">
                        <div class="ev-day"><?php echo htmlspecialchars($selected_event['day']); ?></div>
                        <div class="ev-month"><?php echo htmlspecialchars(substr($selected_event['month'], 0, 3)); ?></div>
                    </div>
                    <h1><?php echo htmlspecialchars($selected_event['title']); ?></h1>
                    <div class="event-meta">
                        <span class="event-venue">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($selected_event['venue']); ?>
                        </span>
                        <span class="event-date">
                            <i class="fas fa-calendar-alt"></i>
                            <?php
                            $month_name = $selected_event['month'];
                            $day = $selected_event['day'];
                            $year = date('Y', strtotime($selected_event['created_at']));
                            echo "$month_name $day, $year";
                            ?>
                        </span>
                    </div>
                </div>
                <div class="event-details-content">
                    <?php echo nl2br(htmlspecialchars($selected_event['description'])); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section id="latest-events-section">
        <div class="event-container">
            <h2 class="event-heading">
                <i class="fas fa-calendar-alt"></i>
                Other Events
            </h2>
            <!-- Carousel wrapper -->
            <div class="event-carousel-container">
                <div class="event-carousel">
                    <?php
                    if ($latest_events->num_rows > 0) {
                        while ($row = $latest_events->fetch_assoc()) {
                            $truncated_description = mb_substr($row['description'], 0, 150);
                            if (strlen($row['description']) > 150) {
                                $truncated_description .= '...';
                            }
                    ?>
                            <div class="event-item">
                                <div class="event-header">
                                    <div class="event-date-badge small">
                                        <div class="ev-day"><?php echo htmlspecialchars($row['day']); ?></div>
                                        <div class="ev-month"><?php echo htmlspecialchars(substr($row['month'], 0, 3)); ?></div>
                                    </div>
                                    <h3 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                </div>
                                <p class="event-venue-small"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['venue']); ?></p>
                                <p class="event-content"><?php echo htmlspecialchars($truncated_description); ?></p>
                                <div class="event-footer">
                                    <p class="event-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo $row['month'] . ' ' . $row['day'] . ', ' . date('Y', strtotime($row['created_at'])); ?>
                                    </p>
                                    <a href="?pages=events-detail&id=<?php echo $row['id']; ?>" class="event-link">
                                        View Details <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<div class="no-events">No other events available.</div>';
                    }
                    ?>
                </div>

                <!-- Carousel navigation -->
                <div class="carousel-navigation">
                    <button class="carousel-prev-btn" aria-label="Previous event">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="carousel-indicators"></div>
                    <button class="carousel-next-btn" aria-label="Next event">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    :root {
        --cvsu-gold: rgb(230, 233, 71);
        --cvsu-primary-green: #006400;
        --cvsu-hover-green: #004d00;
        --cvsu-light-green: #e8f5e8;
        --cvsu-text-dark: #333;
        --cvsu-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
        --cvsu-gold: #D4AF37;
    }

    .events-page-layout {
        display: flex;
        gap: 2rem;
        padding: 1rem 0 10rem 0;
        max-width: 1200px;
        margin: 0 auto;
    }

    #event-details-section {
        flex: 0 0 60%;
        max-width: 60%;
    }

    .event-details-container {
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

    .event-details-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--cvsu-light-green);
        position: relative;
    }

    .event-date-badge {
        background: var(--cvsu-primary-green);
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        text-align: center;
        display: inline-block;
        margin-bottom: 1rem;
    }

    .event-date-badge.small {
        padding: 5px 10px;
        font-size: 0.85rem;
    }

    .ev-day {
        font-size: 1.5rem;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 5px;
    }

    .ev-month {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .event-date-badge.small .ev-day {
        font-size: 1.2rem;
    }

    .event-date-badge.small .ev-month {
        font-size: 0.7rem;
    }

    .event-details-header h1 {
        color: var(--cvsu-primary-green);
        font-size: 2rem;
        margin: 1rem 0;
    }

    .event-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        font-size: 0.95rem;
        color: #666;
    }

    .event-venue,
    .event-date {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .event-venue i,
    .event-date i {
        color: var(--cvsu-primary-green);
    }

    .event-venue-small {
        color: #666;
        font-size: 0.85rem;
        margin: 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .event-venue-small i {
        color: var(--cvsu-primary-green);
    }

    .event-details-content {
        line-height: 1.6;
        color: var(--cvsu-text-dark);
        font-size: 1rem;
    }

    #latest-events-section {
        flex: 0 0 38%;
        max-width: 38%;
    }

    .event-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: var(--cvsu-shadow-sm);
        padding: 1.25rem;
    }

    .event-heading {
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
    .event-carousel-container {
        position: relative;
    }

    .event-carousel {
        display: flex;
        overflow-x: hidden;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        gap: 0.75rem;
    }

    .event-item {
        background: white;
        border: 1px solid var(--cvsu-light-green);
        border-radius: 8px;
        padding: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        min-width: 100%;
        height: 320px;
        position: relative;
        scroll-snap-align: start;
    }

    .event-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .event-title {
        color: var(--cvsu-primary-green);
        font-size: 1rem;
        margin: 0.5rem 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .event-content {
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

    .event-footer {
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

    .event-date {
        color: #666;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin: 0;
    }

    .event-link {
        color: var(--cvsu-primary-green);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        transition: color 0.3s ease;
    }

    .event-link:hover {
        color: var(--cvsu-hover-green);
    }

    .no-events {
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
    .event-carousel {
        touch-action: pan-x;
    }

    @media (max-width: 992px) {
        .events-page-layout {
            flex-direction: column;
        }

        #event-details-section,
        #latest-events-section {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 576px) {

        .event-container,
        .event-details-container {
            padding: 0.75rem;
        }

        .event-heading {
            font-size: 1.25rem;
        }

        .event-item {
            height: 280px;
        }

        .event-details-header h1 {
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
        const carousel = document.querySelector('.event-carousel');
        const prevBtn = document.querySelector('.carousel-prev-btn');
        const nextBtn = document.querySelector('.carousel-next-btn');
        const indicators = document.querySelector('.carousel-indicators');
        const items = carousel.querySelectorAll('.event-item');

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
            const items = carousel.querySelectorAll('.event-item');
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
        }, {
            passive: true
        });

        carousel.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
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
                const itemWidth = items[0].offsetWidth;
                currentIndex = Math.round(carousel.scrollLeft / itemWidth);
                updateIndicators();
            }, 100);
        }, {
            passive: true
        });
    });
</script>