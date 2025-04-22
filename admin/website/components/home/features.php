<section class="features">
    <div class="container">
        <h2 class="features-title">
            <i class="fas fa-bed"></i>
            Available Rooms
        </h2>
 
        <div class="slider-wrapper">
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

                    foreach ($rooms as $index => $room) {
                        $room_id = $index + 1;
                        $stmt = $mysqli->prepare("SELECT image_path FROM room_images WHERE room_id = ? LIMIT 1");
                        $stmt->bind_param("i", $room_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $image = $result->fetch_assoc();
                        $image_path = $image ? "asset/uploads/" . $image['image_path'] : "user/bg/default-room.jpg";
                    ?>
                        <div class="swiper-slide">
                            <div class="feature-card">
                                <div class="image-wrapper">
                                    <img src="<?php echo $image_path; ?>" alt="<?php echo $room; ?>" class="feature-image">
                                    <div class="overlay">
                                        <i class="fas fa-expand-arrows-alt"></i>
                                    </div>
                                </div>
                                <div class="feature-content">
                                    <h3><?php echo $room; ?></h3>
                                    <p class="room-description"><?php echo $descriptions[$index]; ?></p>
                                    <!-- <a href="Alumni.php" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>  -->
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="swiper-button-prev"><i class="fas fa-chevron-left"></i></div>
                <div class="swiper-button-next"><i class="fas fa-chevron-right"></i></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Enhanced styles for the room slider */
    .features {
        padding: 60px 0;
        background-color: #f8f9fa;
    }

    .features-title {
        color: var(--cvsu-primary-green);
        font-size: 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--cvsu-light-green);
    }

    .features-title:after {
        content: '';
        display: block;
        width: 80px;
        height: 3px;
        background-color: #006400;
        margin: 15px auto 0;
    }

    .slider-wrapper {
        position: relative;
        padding: 0 10px;
        margin-bottom: 30px;
    }

    .feature-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .swiper-slide-active .feature-card {
        animation-delay: 0.2s;
    }

    .swiper-slide-next .feature-card {
        animation-delay: 0.4s;
    }

    .swiper-slide-next+.swiper-slide .feature-card {
        animation-delay: 0.6s;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .image-wrapper {
        position: relative;
        overflow: hidden;
        height: 200px;
    }

    .feature-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .feature-card:hover .feature-image {
        transform: scale(1.1);
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 100, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .overlay i {
        color: white;
        font-size: 24px;
    }

    .feature-card:hover .overlay {
        opacity: 1;
    }

    .feature-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .feature-content h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: #333;
        font-size: 20px;
        font-weight: 600;
    }

    .room-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
        flex: 1;
        display: block !important;
        overflow: visible !important;
        height: auto !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .feature-link {
        color: #006400;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        margin-top: auto;
        transition: color 0.3s ease;
    }

    .feature-link i {
        margin-left: 8px;
        transition: transform 0.3s ease;
    }

    .feature-link:hover {
        color: #008000;
    }

    .feature-link:hover i {
        transform: translateX(5px);
    }





    .swiper-button-prev i,
    .swiper-button-next i {
        font-size: 16px;
    }

    .swiper-pagination {
        position: relative;
        margin-top: 20px;
    }

    .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
        background: #ccc;
        opacity: 1;
    }

    .swiper-pagination-bullet-active {
        background: #006400;
    }

    /* Ensure responsive behavior */
    @media (max-width: 991px) {
        .feature-content h3 {
            font-size: 18px;
        }

        .room-description {
            font-size: 14px;
        }
    }

    @media (max-width: 767px) {
        .image-wrapper {
            height: 180px;
        }

        .feature-content {
            padding: 15px;
        }
    }

    @media (max-width: 480px) {
        .features-title {
            font-size: 26px;
        }

        .feature-card {
            max-width: 320px;
            margin: 0 auto;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roomSlider = new Swiper('.slider', {
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
            },
            on: {
                init: function() {
                    // Reset animations when slider initializes
                    document.querySelectorAll('.feature-card').forEach(card => {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                    });

                    // Delay to ensure DOM is ready
                    setTimeout(() => {
                        // Trigger animations for visible slides
                        this.animateSlides();
                    }, 100);
                },
                slideChangeTransitionStart: function() {
                    // Reset animations when slide changes
                    document.querySelectorAll('.swiper-slide:not(.swiper-slide-active):not(.swiper-slide-next):not(.swiper-slide-next + .swiper-slide) .feature-card').forEach(card => {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                    });
                },
                slideChangeTransitionEnd: function() {
                    // Trigger animations for newly visible slides
                    this.animateSlides();
                }
            }
        });

        // Add custom animation method to Swiper
        roomSlider.animateSlides = function() {
            // Get visible slides
            const visibleSlides = [
                document.querySelector('.swiper-slide-active'),
                document.querySelector('.swiper-slide-next'),
                document.querySelector('.swiper-slide-next + .swiper-slide')
            ].filter(slide => slide !== null);

            // Animate each visible slide with staggered delay
            visibleSlides.forEach((slide, index) => {
                const card = slide.querySelector('.feature-card');
                if (!card) return;

                setTimeout(() => {
                    card.style.animation = 'none';
                    void card.offsetWidth; // Trigger reflow
                    card.style.animation = `fadeInUp 0.8s forwards ${index * 0.2}s`;
                }, index * 50);
            });
        };

        // Ensure paragraphs remain visible
        const ensureParagraphsVisible = () => {
            document.querySelectorAll('.room-description').forEach(paragraph => {
                paragraph.style.display = 'block';
                paragraph.style.visibility = 'visible';
                paragraph.style.opacity = '1';
                paragraph.style.height = 'auto';
                paragraph.style.overflow = 'visible';
            });
        };

        // Call it initially
        ensureParagraphsVisible();

        // Also call after any potential slider events that might affect visibility
        roomSlider.on('slideChange', ensureParagraphsVisible);
        roomSlider.on('resize', ensureParagraphsVisible);

        // Set an interval to periodically check and ensure descriptions stay visible
        setInterval(ensureParagraphsVisible, 2000);
    });
</script>