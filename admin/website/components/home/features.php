<!-- Modified rooms section for the main page (shows only 3 rooms) -->
<section id="rooms-section">
    <div class="rooms-container">
        <h2 class="rooms-heading">
            <i class="fas fa-bed"></i>
            Available Rooms
        </h2>
        <div class="rooms-grid">
            <div class="swiper slider">
                <div class="swiper-wrapper">
                    <?php
                    require_once 'main_db.php';
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

                    // Display only 3 rooms on the main page
                    $rooms_to_display = array_slice($rooms, 0, 3);
                    $descriptions_to_display = array_slice($descriptions, 0, 3);

                    foreach ($rooms_to_display as $index => $room) {
                        $room_id = $index + 1;
                        $stmt = $mysqli->prepare("SELECT image_path FROM room_images WHERE room_id = ? LIMIT 1");
                        $stmt->bind_param("i", $room_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $image = $result->fetch_assoc();
                        $image_path = $image ? "asset/uploads/" . $image['image_path'] : "user/bg/default-room.jpg";
                    ?>
                        <div class="swiper-slide">
                            <div class="room-item" data-id="<?php echo $room_id; ?>">
                                <div class="room-image-container">
                                    <img src="<?php echo $image_path; ?>" alt="<?php echo $room; ?>" class="room-image" onerror="this.src='user/bg/default-room.jpg';">
                                </div>
                                <div class="room-header">
                                    <h3 class="room-title"><?php echo htmlspecialchars($room); ?></h3>
                                </div>
                                <p class="room-content"><?php echo htmlspecialchars($descriptions[$index]); ?></p>
                                <div class="room-footer">
                                    <a href="?pages=all-rooms&id=<?php echo $room_id; ?>" class="room-link">
                                        View details <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="rooms-actions">
            <a href="?pages=all-rooms" class="rooms-view-all">
                See all available rooms
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</section>
<style>
    /* Enhanced Room Section Styles (matching announcement style) */
    #rooms-section {
        width: 100%;
        background-color: #f8f9fa;
        padding: 3rem 0;
    }

    .rooms-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .rooms-heading {
        font-size: 1.75rem;
        color: #212529;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 2px solid #eaeaea;
        padding-bottom: 0.75rem;
    }

    .rooms-grid {
        margin-bottom: 2rem;
        position: relative;
    }

    .room-item {
        border-radius: 8px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #eaeaea;
        background-color: #fff;
        cursor: pointer;
        margin: 0 5px;
    }

    .room-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .room-image-container {
        height: 200px;
        overflow: hidden;
        position: relative;
    }

    .room-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .room-item:hover .room-image {
        transform: scale(1.1);
    }

    .room-header {
        padding: 1.25rem 1.25rem 0.25rem;
    }

    .room-title {
        font-size: 1.25rem;
        margin: 0 0 0.75rem;
        line-height: 1.4;
        transition: color 0.3s ease;
    }

    .room-item:hover .room-title {
        color: #0d6efd;
    }

    .room-content {
        padding: 0 1.25rem 1rem;
        margin: 0;
        color: #495057;
        line-height: 1.6;
        flex-grow: 1;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
        overflow: visible !important;
    }

    .room-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: #f8f9fa;
        border-top: 1px solid #eaeaea;
        transition: background-color 0.3s ease;
    }

    .room-item:hover .room-footer {
        background-color: #f0f4ff;
    }

    .room-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        color: #0d6efd;
        transition: all 0.3s ease;
        position: relative;
    }

    .room-link:after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background-color: #0a58ca;
        transition: width 0.3s ease;
    }

    .room-link:hover:after {
        width: 100%;
    }

    .room-link:hover {
        color: #0a58ca;
    }

    .rooms-view-all {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        color: #0d6efd;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .rooms-view-all:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(13, 110, 253, 0.1);
        transition: all 0.3s ease;
        z-index: -1;
    }

    .rooms-view-all:hover:before {
        left: 0;
    }

    .rooms-view-all:hover {
        background-color: #f0f4ff;
    }

    .rooms-view-all i {
        transition: transform 0.3s ease;
    }

    .rooms-view-all:hover i {
        transform: translateX(3px);
    }

    .rooms-actions {
        display: flex;
        justify-content: center;
        margin-top: 1.5rem;
    }

    /* Swiper navigation styling */
    .swiper-button-prev,
    .swiper-button-next {
        width: 40px;
        height: 40px;
        background-color: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        transition: all 0.3s ease;
    }

    .swiper-button-prev:after,
    .swiper-button-next:after {
        content: '';
    }

    .swiper-button-prev:hover,
    .swiper-button-next:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
        background: #d1d1d1;
        opacity: 1;
    }

    .swiper-pagination-bullet-active {
        background: #0d6efd;
        transform: scale(1.2);
    }

    /* Fixed Swiper Slides */
    .swiper-slide {
        height: auto;
        display: flex;
    }

    /* Responsive styles */
    @media (max-width: 991px) {
        .room-title {
            font-size: 1.1rem;
        }

        .room-content {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 767px) {
        .room-image-container {
            height: 180px;
        }
    }

    @media (max-width: 480px) {
        .rooms-heading {
            font-size: 1.5rem;
        }

        .room-item {
            margin: 0 auto;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swiper
        const roomSwiper = new Swiper('.slider', {
            slidesPerView: 3,
            spaceBetween: 30,
            loop: true,
            speed: 800,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            effect: 'slide',
            grabCursor: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                0: {
                    slidesPerView: 1,
                    spaceBetween: 20
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 25
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30
                }
            }
        });

        // Fix for slides not showing
        setTimeout(() => {
            roomSwiper.update();
        }, 500);

        // Click event for room items
        document.querySelectorAll('.room-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Only redirect if not clicking on the link
                if (!e.target.closest('.room-link')) {
                    const id = this.getAttribute('data-id');
                    this.style.transition = 'opacity 0.3s ease';
                    this.style.opacity = '0';
                    setTimeout(() => {
                        window.location.href = `?pages=room&id=${id}`;
                    }, 300);
                }
            });
        });

        // Hover effect for "View details" links
        document.querySelectorAll('.room-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                const icon = this.querySelector('i.fa-arrow-right');
                if (icon) {
                    icon.style.transition = 'transform 0.3s ease';
                    icon.style.transform = 'translateX(4px)';
                }
            });
            link.addEventListener('mouseleave', function() {
                const icon = this.querySelector('i.fa-arrow-right');
                if (icon) {
                    icon.style.transform = 'translateX(0)';
                }
            });
        });

        // Hover effect for "See all available rooms" link
        const viewAllLink = document.querySelector('.rooms-view-all');
        if (viewAllLink) {
            viewAllLink.addEventListener('mouseenter', function() {
                const icon = this.querySelector('i.fa-chevron-right');
                if (icon) {
                    icon.style.transition = 'transform 0.3s ease';
                    icon.style.transform = 'translateX(4px)';
                }
            });
            viewAllLink.addEventListener('mouseleave', function() {
                const icon = this.querySelector('i.fa-chevron-right');
                if (icon) {
                    icon.style.transform = 'translateX(0)';
                }
            });
        }

        // Ensure room descriptions stay visible
        function ensureRoomContentVisible() {
            document.querySelectorAll('.room-content').forEach(content => {
                content.style.display = 'block';
                content.style.visibility = 'visible';
                content.style.opacity = '1';
                content.style.height = 'auto';
                content.style.overflow = 'visible';
            });
        }

        // Call initially and set interval
        ensureRoomContentVisible();
        setInterval(ensureRoomContentVisible, 2000);

        // Force swiper update on window resize to fix any display issues
        window.addEventListener('resize', function() {
            roomSwiper.update();
        });
    });
</script>