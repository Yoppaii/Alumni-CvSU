<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Cavite State University</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="admin/website/components/home/home-styles.css">
    <link rel="stylesheet" href="admin/website/root.css">

    <!-- Add animation library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

</head>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('main_db.php');

// Check if we're viewing a specific room or the main rooms page
$is_detail_view = isset($_GET['id']);
$selected_id = $is_detail_view ? intval($_GET['id']) : 0;

// Room data (in a real implementation, this would come from the database)
$rooms = [
    'Room 1',
    'Room 2',
    'Room 3',
    'Room 4',
    'Room 5',
    'Room 6',
    'Room 7',
    'Room 8',
    'Board Room',
    'Conference Room',
    'Lobby'
];

$descriptions = [
    'Spacious room perfect for small gatherings and meetings. Equipped with modern amenities and comfortable seating arrangements.',
    'Versatile space ideal for workshops and training sessions. Features state-of-the-art presentation equipment.',
    'Cozy meeting space with natural lighting. Perfect for team discussions and small group activities.',
    'Modern conference setup with advanced audio-visual capabilities. Ideal for presentations and seminars.',
    'Flexible space that can be customized for various events and activities. Complete with modern amenities.',
    'Professional meeting space with ergonomic furniture. Suitable for extended working sessions.',
    'Well-lit room with modern décor. Perfect for professional meetings and small conferences.',
    'Spacious area with configurable seating. Ideal for workshops and group activities.',
    'Executive meeting space with premium furnishings. Features high-end conferencing equipment and professional atmosphere.',
    'Large-scale meeting space with advanced presentation systems. Perfect for corporate events and large gatherings.',
    'Welcoming reception area with comfortable seating. Ideal for casual meetings and guest reception.'
];



// If we're viewing a specific room
if ($is_detail_view) {
    // Get the selected room information
    $room_index = $selected_id - 1;

    if ($room_index >= 0 && $room_index < count($rooms)) {
        $selected_room = [
            'id' => $selected_id,
            'name' => $rooms[$room_index],
            'description' => $descriptions[$room_index]
        ];
    } else {
        // Invalid room ID
        $selected_room = null;
    }

    // Get image for the selected room
    $stmt = $mysqli->prepare("SELECT image_path FROM room_images WHERE room_id = ? LIMIT 1");
    $stmt->bind_param("i", $selected_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();
    $selected_image_path = $image ? "asset/uploads/" . $image['image_path'] : "user/bg/default-room.jpg";

    // Get other rooms for the carousel (excluding the currently viewed room)
    $other_rooms = [];
    for ($i = 0; $i < count($rooms); $i++) {
        if ($i != $room_index) {
            $other_room_id = $i + 1;

            // Get image for this room
            $stmt = $mysqli->prepare("SELECT image_path FROM room_images WHERE room_id = ? LIMIT 1");
            $stmt->bind_param("i", $other_room_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $image = $result->fetch_assoc();
            $image_path = $image ? "asset/uploads/" . $image['image_path'] : "user/bg/default-room.jpg";

            $other_rooms[] = [
                'id' => $other_room_id,
                'name' => $rooms[$i],
                'description' => $descriptions[$i],
                'image_path' => $image_path
            ];
        }
    }
}
?>

<?php if ($is_detail_view): ?>
    <!-- DETAIL VIEW LAYOUT (when viewing a specific room) -->
    <div class="room-page-layout detail-view">
        <?php if ($selected_room): ?>
            <section id="room-details-section">
                <div class="room-details-container">
                    <a href="?pages=all-rooms" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back to All Rooms
                    </a>

                    <div class="room-image-hero">
                        <img src="<?php echo $selected_image_path; ?>" alt="<?php echo htmlspecialchars($selected_room['name']); ?>" class="detail-room-image" onerror="this.src='user/bg/default-room.jpg';">
                    </div>

                    <div class="room-details-header">
                        <h1><?php echo htmlspecialchars($selected_room['name']); ?></h1>
                    </div>

                    <div class="room-details-content">
                        <p class="room-description"><?php echo nl2br(htmlspecialchars($selected_room['description'])); ?></p>

                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section id="other-rooms-section">
            <div class="other-rooms-container">
                <h2 class="other-rooms-heading">
                    <i class="fas fa-door-open"></i>
                    More Rooms to Explore
                </h2>

                <!-- Carousel wrapper -->
                <div class="rooms-carousel-container">
                    <div class="rooms-carousel">
                        <?php
                        if (!empty($other_rooms)) {
                            foreach ($other_rooms as $index => $room) {
                                $truncated_description = mb_substr($room['description'], 0, 120);
                                if (strlen($room['description']) > 120) {
                                    $truncated_description .= '...';
                                }
                        ?>
                                <div class="room-carousel-item" data-index="<?php echo $index; ?>">
                                    <div class="carousel-room-image">
                                        <img src="<?php echo $room['image_path']; ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" onerror="this.src='user/bg/default-room.jpg';">
                                    </div>
                                    <div class="carousel-room-header">
                                        <h3 class="carousel-room-title"><?php echo htmlspecialchars($room['name']); ?></h3>
                                    </div>
                                    <p class="carousel-room-content"><?php echo htmlspecialchars($truncated_description); ?></p>
                                    <div class="carousel-room-footer">
                                        <a href="?pages=all-rooms&id=<?php echo $room['id']; ?>" class="room-link">
                                            View details <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo '<div class="no-rooms">No other rooms available.</div>';
                        }
                        ?>
                    </div>

                    <!-- Carousel navigation -->
                    <div class="carousel-navigation">
                        <button class="carousel-prev-btn" aria-label="Previous room" id="carouselPrevBtn">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="carousel-indicators" id="carouselIndicators">
                            <?php
                            if (!empty($other_rooms)) {
                                foreach ($other_rooms as $index => $room) {
                                    echo '<div class="carousel-indicator' . ($index === 0 ? ' active' : '') . '" data-index="' . $index . '"></div>';
                                }
                            }
                            ?>
                        </div>
                        <button class="carousel-next-btn" aria-label="Next room" id="carouselNextBtn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php else: ?>
    <!-- LISTING VIEW LAYOUT (when viewing all rooms) -->
    <div class="room-page-layout listing-view">
        <div class="all-rooms-container">
            <div class="all-rooms-header">
                <h1 class="all-rooms-title">
                    <i class="fas fa-door-open"></i>
                    Campus Rooms & Facilities
                </h1>
                <p class="all-rooms-subtitle">Explore our versatile spaces designed for meetings, events, and activities</p>
            </div>

            <div class="all-rooms-grid">
                <?php
                for ($i = 0; $i < count($rooms); $i++) {
                    $room_id = $i + 1;

                    // Get image for this room
                    $stmt = $mysqli->prepare("SELECT image_path FROM room_images WHERE room_id = ? LIMIT 1");
                    $stmt->bind_param("i", $room_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $image = $result->fetch_assoc();
                    $image_path = $image ? "asset/uploads/" . $image['image_path'] : "user/bg/default-room.jpg";

                    $truncated_description = mb_substr($descriptions[$i], 0, 150);
                    if (strlen($descriptions[$i]) > 150) {
                        $truncated_description .= '...';
                    }
                ?>
                    <div class="room-card" data-id="<?php echo $room_id; ?>">
                        <div class="room-image-container">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($rooms[$i]); ?>" class="room-image" onerror="this.src='user/bg/default-room.jpg';">
                        </div>
                        <div class="room-header">
                            <h3 class="room-title"><?php echo htmlspecialchars($rooms[$i]); ?></h3>
                        </div>
                        <p class="room-content"><?php echo htmlspecialchars($truncated_description); ?></p>
                        <div class="room-footer">

                            <a href="?pages=all-rooms&id=<?php echo $room_id; ?>" class="room-link">
                                View details <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="rooms-actions return-action">
                <a href="index.php" class="back-to-home">
                    <i class="fas fa-chevron-left"></i>
                    Back to home
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Add JavaScript for carousel functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only run this code on the detail view with carousel
        if (document.querySelector('.rooms-carousel')) {
            const carousel = document.querySelector('.rooms-carousel');
            const carouselItems = document.querySelectorAll('.room-carousel-item');
            const prevBtn = document.getElementById('carouselPrevBtn');
            const nextBtn = document.getElementById('carouselNextBtn');
            const indicators = document.querySelectorAll('.carousel-indicator');

            let currentIndex = 0;
            const itemCount = carouselItems.length;

            // Skip if no items
            if (itemCount === 0) return;

            // Function to update carousel position
            function updateCarousel() {
                // Update carousel scroll position
                const itemWidth = carouselItems[0].offsetWidth;
                carousel.scrollTo({
                    left: currentIndex * itemWidth,
                    behavior: 'smooth'
                });

                // Update active indicator
                indicators.forEach((indicator, i) => {
                    if (i === currentIndex) {
                        indicator.classList.add('active');
                    } else {
                        indicator.classList.remove('active');
                    }
                });
            }

            // Initialize first slide
            updateCarousel();

            // Previous button click
            prevBtn.addEventListener('click', function() {
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : itemCount - 1;
                updateCarousel();
            });

            // Next button click
            nextBtn.addEventListener('click', function() {
                currentIndex = (currentIndex < itemCount - 1) ? currentIndex + 1 : 0;
                updateCarousel();
            });

            // Indicator clicks
            indicators.forEach((indicator, i) => {
                indicator.addEventListener('click', function() {
                    currentIndex = i;
                    updateCarousel();
                });
            });

            // Handle manual scrolling
            let isScrolling;
            carousel.addEventListener('scroll', function() {
                window.clearTimeout(isScrolling);

                isScrolling = setTimeout(function() {
                    // After scrolling stops, snap to nearest item
                    const itemWidth = carouselItems[0].offsetWidth;
                    const scrollLeft = carousel.scrollLeft;
                    currentIndex = Math.round(scrollLeft / itemWidth);
                    // Ensure index is within bounds
                    currentIndex = Math.max(0, Math.min(currentIndex, itemCount - 1));
                    updateCarousel();
                }, 100);
            });

            // Optional: Auto-rotate carousel
            let autoRotate = setInterval(function() {
                currentIndex = (currentIndex < itemCount - 1) ? currentIndex + 1 : 0;
                updateCarousel();
            }, 6000); // Change slide every 6 seconds

            // Pause auto-rotate when interacting with carousel
            carousel.addEventListener('mouseenter', function() {
                clearInterval(autoRotate);
            });

            carousel.addEventListener('mouseleave', function() {
                autoRotate = setInterval(function() {
                    currentIndex = (currentIndex < itemCount - 1) ? currentIndex + 1 : 0;
                    updateCarousel();
                }, 6000);
            });
        }
    });
</script>

<style>
    /* Global styles */
    .room-page-layout {
        padding: 2rem 1rem 6rem 1rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* DETAIL VIEW STYLES */
    .room-page-layout.detail-view {
        display: flex;
        gap: 2.5rem;
    }

    #room-details-section {
        flex: 0 0 60%;
        max-width: 60%;
    }

    .room-details-container {
        background-color: var(--bg-primary);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: var(--transition);
        border-top: 4px solid var(--primary-dark);
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        margin: 1.5rem;
        transition: var(--transition);
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-md);
        background-color: var(--bg-secondary);
        font-size: 0.9rem;
    }

    .back-button:hover {
        color: var(--white);
        background-color: var(--primary-color);
        transform: translateX(-3px);
    }

    .back-button i {
        transition: var(--transition);
    }

    .back-button:hover i {
        transform: translateX(-3px);
    }

    .room-image-hero {
        width: 100%;
        height: 300px;
        overflow: hidden;
    }

    .detail-room-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .room-details-container:hover .detail-room-image {
        transform: scale(1.05);
    }

    .room-details-header {
        margin: 0 2rem;
        padding: 1.5rem 0;
        border-bottom: 2px solid var(--bg-secondary);
        position: relative;
    }

    .room-details-header h1 {
        color: var(--primary-color);
        font-size: 2.2rem;
        margin: 1rem 0;
        line-height: 1.3;
        font-weight: 700;
    }

    #selected-room-badge,
    .room-status-badge {
        background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
        color: var(--white);
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
        align-self: flex-start;
        display: inline-block;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0, 100, 0, 0.3);
        text-transform: uppercase;
    }

    .room-details-content {
        padding: 1.5rem 2rem 2.5rem;
    }

    .room-description {
        line-height: 1.8;
        color: var(--text-primary);
        font-size: 1.05rem;
        margin-bottom: 2rem;
    }

    .booking-cta {
        text-align: center;
        margin-top: 2rem;
    }

    .book-now-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
        color: var(--white);
        text-decoration: none;
        padding: 0.875rem 2rem;
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: 1.1rem;
        transition: var(--transition);
        box-shadow: 0 4px 8px rgba(0, 100, 0, 0.25);
    }

    .book-now-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 100, 0, 0.3);
    }

    /* Sidebar with other rooms */
    #other-rooms-section {
        flex: 0 0 38%;
        max-width: 38%;
    }

    .other-rooms-container {
        background-color: var(--bg-primary);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        padding: 1.75rem;
        border-top: 4px solid var(--secondary-color);
    }

    .other-rooms-heading {
        color: var(--text-primary);
        font-size: 1.6rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--bg-secondary);
        font-weight: 700;
    }

    .other-rooms-heading i {
        color: var(--text-primary);
    }

    /* Carousel Styles */
    .rooms-carousel-container {
        position: relative;
        margin-top: 1.5rem;
    }

    .rooms-carousel {
        display: flex;
        overflow-x: hidden;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        gap: 1rem;
        padding-bottom: 0.5rem;
    }

    .room-carousel-item {
        background: var(--bg-primary);
        border: 1px solid rgba(0, 100, 0, 0.1);
        border-radius: var(--radius-lg);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        min-width: 100%;
        height: 360px;
        position: relative;
        scroll-snap-align: start;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .room-carousel-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    .carousel-room-image {
        height: 160px;
        overflow: hidden;
    }

    .carousel-room-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .room-carousel-item:hover .carousel-room-image img {
        transform: scale(1.1);
    }

    .carousel-room-header {
        padding: 1.25rem 1.25rem 0.25rem;
    }

    .carousel-room-title {
        color: var(--primary-color);
        font-size: 1.2rem;
        margin: 0.5rem 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 700;
        line-height: 1.4;
    }

    .carousel-room-content {
        padding: 0 1.25rem;
        color: var(--text-primary);
        line-height: 1.6;
        font-size: 0.95rem;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .carousel-room-footer {
        border-top: 1px solid var(--bg-secondary);
        padding: 1.5rem 1.25rem;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--bg-primary);
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    /* LISTING VIEW STYLES */
    .room-page-layout.listing-view {
        padding: 2rem 1.5rem 4rem 1.5rem;
    }

    .all-rooms-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--bg-secondary);
    }

    .all-rooms-title {
        color: var(--text-primary);
        font-size: 2.4rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .all-rooms-title i {
        color: var(--text-primary);
    }

    .all-rooms-subtitle {
        color: var(--text-secondary);
        font-size: 1.1rem;
    }

    .all-rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
    }

    .room-card {
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 400px;
        position: relative;
        transition: var(--transition);
    }

    .room-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .room-image-container {
        height: 200px;
        overflow: hidden;
    }

    .room-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .room-card:hover .room-image {
        transform: scale(1.1);
    }

    .room-header {
        padding: 1.25rem 1.25rem 0.25rem;
    }

    .room-title {
        color: var(--primary-color);
        font-size: 1.4rem;
        margin: 0.75rem 0;
        line-height: 1.4;
        font-weight: 700;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .room-content {
        padding: 0 1.25rem;
        flex-grow: 1;
        color: var(--text-primary);
        line-height: 1.7;
        font-size: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .room-footer {
        border-top: 1px solid var(--bg-secondary);
        padding: 1rem 1.25rem;
        background: var(--bg-secondary);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .room-capacity {
        color: var(--text-secondary);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .room-capacity i {
        color: var(--primary-color);
    }

    .room-link {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-md);
    }

    .room-link:hover {
        color: var(--white);
        background-color: var(--primary-color);
    }

    .room-link i {
        color: var(--text-primary);
        transition: var(--transition);
    }

    .room-link:hover i {
        transform: translateX(3px);
    }

    .no-rooms {
        text-align: center;
        padding: 2.5rem;
        color: var(--text-secondary);
        font-size: 1rem;
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
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
        background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
        color: var(--white);
        border: none;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
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
        transition: var(--transition);
    }

    .carousel-indicator.active {
        background-color: var(--primary-color);
        transform: scale(1.2);
    }

    .rooms-actions {
        text-align: center;
        margin-top: 3rem;
    }

    .back-to-home {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-lg);
        background-color: var(--bg-secondary);
    }

    .back-to-home:hover {
        background-color: var(--primary-color);
        color: var(--white);
    }

    .back-to-home i {
        transition: var(--transition);
    }

    .back-to-home:hover i {
        transform: translateX(-3px);
    }

    /* Mobile responsiveness */
    @media (max-width: 992px) {
        .room-page-layout.detail-view {
            flex-direction: column;
        }

        #room-details-section,
        #other-rooms-section {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .all-rooms-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }
</style>