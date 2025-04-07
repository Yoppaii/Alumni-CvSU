
<section class="features">
    <div class="container">
        <h2 class="features-title">Available Rooms</h2>

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
                                    <p><?php echo $descriptions[$index]; ?></p>
                                    <a href="#" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="swiper-button-prev"><i class="fas fa-chevron-left"></i></div>
                <div class="swiper-button-next"><i class="fas fa-chevron-right"></i></div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.slider', {
            slidesPerView: 3,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                0: {
                    slidesPerView: 1
                },
                768: {
                    slidesPerView: 2
                },
                1024: {
                    slidesPerView: 3
                }
            }
        });
    });
</script>