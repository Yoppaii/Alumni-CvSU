<!-- Room Slider Section -->
<section class="cvsu-features">
    <div class="cvsu-container">
        <h2 class="cvsu-features-title">Available Rooms</h2>
        
        <div class="cvsu-slider-container">
            <div class="cvsu-slider-wrapper">
                <div class="cvsu-slider">
                <?php
                require_once 'main_db.php'; // Include the database connection file

                $rooms = [
                    'Room 1', 'Room 2', 'Room 3', 'Room 4', 'Room 5', 
                    'Room 6', 'Room 7', 'Room 8', 'Board Room', 
                    'Conference Room', 'Lobby'
                ];

                $room_descriptions = [
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
                    <div class="cvsu-slide">
                        <div class="cvsu-feature-card">
                            <div class="cvsu-feature-image-wrapper">
                                <img src="<?php echo $image_path; ?>" alt="<?php echo $room; ?>" class="cvsu-feature-image">
                                <div class="cvsu-feature-overlay">
                                    <i class="fas fa-expand-arrows-alt"></i>
                                </div>
                            </div>
                            <div class="cvsu-feature-content">
                                <h3><?php echo $room; ?></h3>
                                <p><?php echo $room_descriptions[$index]; ?></p>
                                <a href="#" class="cvsu-feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
            
            <button class="cvsu-slider-arrow cvsu-prev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="cvsu-slider-arrow cvsu-next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<style>
.cvsu-features {
    padding: 4rem 0;
    background-color: #f8f9fa;
}

.cvsu-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.cvsu-features-title {
    text-align: center;
    color: #006400;
    font-size: 2.5rem;
    margin-bottom: 3rem;
    position: relative;
    padding-bottom: 1rem;
}

.cvsu-features-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background-color: #006400;
}

.cvsu-slider-container {
    position: relative;
    padding: 0 60px;
}

.cvsu-slider-wrapper {
    overflow: hidden;
}

.cvsu-slider {
    display: flex;
    transition: transform 0.5s ease;
}

.cvsu-slide {
    min-width: calc(33.333% - 30px); /* Show 3 slides in desktop */
    padding: 0 15px;
    transition: min-width 0.3s ease;
}

.cvsu-feature-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.cvsu-feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.cvsu-feature-image-wrapper {
    position: relative;
    overflow: hidden;
    padding-top: 66.67%; /* 3:2 Aspect Ratio */
}

.cvsu-feature-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.cvsu-feature-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 100, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.cvsu-feature-overlay i {
    color: white;
    font-size: 2rem;
    transform: scale(0.8);
    transition: transform 0.3s ease;
}

.cvsu-feature-card:hover .cvsu-feature-overlay {
    opacity: 1;
}

.cvsu-feature-card:hover .cvsu-feature-overlay i {
    transform: scale(1);
}

.cvsu-feature-card:hover .cvsu-feature-image {
    transform: scale(1.05);
}

.cvsu-feature-content {
    padding: 1.5rem;
}

.cvsu-feature-content h3 {
    color: #006400;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.cvsu-feature-content p {
    color: #333;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.cvsu-feature-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #006400;
    text-decoration: none;
    font-weight: 500;
    transition: gap 0.3s ease;
}

.cvsu-feature-link:hover {
    gap: 0.8rem;
}

.cvsu-slider-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #006400;
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease;
    z-index: 10;
}

.cvsu-slider-arrow:hover {
    background: #004d00;
}

.cvsu-prev {
    left: 0;
}

.cvsu-next {
    right: 0;
}

/* Responsive styles */
@media (max-width: 1024px) {
    .cvsu-slide {
        min-width: calc(50% - 30px); /* Show 2 slides in tablet */
    }
}

@media (max-width: 768px) {
    .cvsu-slide {
        min-width: calc(100% - 30px); /* Show 1 slide in mobile */
    }
    
    .cvsu-features-title {
        font-size: 2rem;
        margin-bottom: 2rem;
    }

    .cvsu-slider-container {
        padding: 0 40px;
    }

    .cvsu-feature-content h3 {
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .cvsu-features-title {
        font-size: 1.75rem;
    }

    .cvsu-slider-container {
        padding: 0 30px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.cvsu-slider');
    const slides = document.querySelectorAll('.cvsu-slide');
    const prevBtn = document.querySelector('.cvsu-prev');
    const nextBtn = document.querySelector('.cvsu-next');
    let currentSlide = 0;
    
    function getSlidesPerView() {
        if (window.innerWidth <= 768) {
            return 1; // Mobile: 1 slide
        } else if (window.innerWidth <= 1024) {
            return 2; // Tablet: 2 slides
        }
        return 3; // Desktop: 3 slides
    }

    function updateSlider() {
        const slidesPerView = getSlidesPerView();
        const totalSlides = slides.length;
        const maxSlides = totalSlides - slidesPerView;
        
        // Prevent scrolling past the last set of slides
        if (currentSlide > maxSlides) {
            currentSlide = maxSlides;
        }
        
        const offset = (currentSlide * (100 / slidesPerView));
        slider.style.transform = `translateX(-${offset}%)`;
    }

    // Initialize first slide
    updateSlider();

    // Add click handlers for navigation buttons
    prevBtn.addEventListener('click', () => {
        if (currentSlide > 0) {
            currentSlide--;
            updateSlider();
        }
    });

    nextBtn.addEventListener('click', () => {
        const slidesPerView = getSlidesPerView();
        const totalSlides = slides.length;
        const maxSlides = totalSlides - slidesPerView;
        
        if (currentSlide < maxSlides) {
            currentSlide++;
            updateSlider();
        }
    });

    // Auto advance slides every 5 seconds
    let autoSlide = setInterval(autoAdvance, 5000);

    // Pause auto-advance on hover
    slider.addEventListener('mouseenter', () => {
        clearInterval(autoSlide);
    });

    slider.addEventListener('mouseleave', () => {
        autoSlide = setInterval(autoAdvance, 5000);
    });

    function autoAdvance() {
        const slidesPerView = getSlidesPerView();
        const totalSlides = slides.length;
        const maxSlides = totalSlides - slidesPerView;
        
        if (currentSlide < maxSlides) {
            currentSlide++;
        } else {
            currentSlide = 0;
        }
        updateSlider();
    }

    // Update slider on window resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            updateSlider();
        }, 100);
    });

    // Touch support for mobile devices
    let touchStartX = 0;
    let touchEndX = 0;

    slider.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    });

    slider.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const difference = touchStartX - touchEndX;
        const slidesPerView = getSlidesPerView();
        const totalSlides = slides.length;
        const maxSlides = totalSlides - slidesPerView;

        if (Math.abs(difference) > 50) { // Minimum swipe distance
            if (difference > 0 && currentSlide < maxSlides) {
                // Swipe left
                currentSlide++;
            } else if (difference < 0 && currentSlide > 0) {
                // Swipe right
                currentSlide--;
            }
            updateSlider();
        }
    }
});
</script>