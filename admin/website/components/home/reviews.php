<?php
require 'main_db.php'; 

if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

$sql_feedback = "SELECT id, user_email, room_rating, staff_rating, cleanliness_rating, comment, created_at FROM feedback ORDER BY created_at DESC LIMIT 12";
$stmt_feedback = $mysqli->query($sql_feedback);

if (!$stmt_feedback) {
    die("Query failed: " . $mysqli->error);  
}

$feedbacks = $stmt_feedback->fetch_all(MYSQLI_ASSOC); 

function mask_email($email) {
    list($name, $domain) = explode('@', $email);
    return $name;
}

function display_stars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $rating) ? '★' : '☆';
    }
    return $stars;
}

function truncate_comment($comment, $max_length = 150) {
    return (strlen($comment) > $max_length) ? substr($comment, 0, $max_length) . '...' : $comment;
}
?>

<section class="reviews">
    <div class="reviews-header">
        <h2>What Our Alumni Are Saying</h2>
    </div>
    
    <div class="review-carousel" id="reviewCarousel">
        <div class="review-cards-container" id="reviewContainer">
            <?php foreach ($feedbacks as $feedback): ?>
            <div class="review-card">
                <div class="review-header">
                    <div class="review-rating room-rating">
                        <?php echo display_stars($feedback['room_rating']); ?> 
                    </div>
                    <div class="profile-picture">
                        <img src="https://via.placeholder.com/80" alt="Profile Picture">
                    </div>
                    <div class="review-email"><?php echo htmlspecialchars(mask_email($feedback['user_email'])); ?></div>
                </div>

                <p class="review-comment" id="comment-<?php echo $feedback['id']; ?>">
                    <?php echo htmlspecialchars(truncate_comment($feedback['comment'])); ?>
                </p>

                <?php if (strlen($feedback['comment']) > 150): ?>
                <a href="javascript:void(0);" class="see-full-review" onclick="toggleFullReview(<?php echo $feedback['id']; ?>)">See Full Review</a>
                <?php endif; ?>

                <div class="review-post-date">Posted on: <?php echo date("F j, Y", strtotime($feedback['created_at'])); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="carousel-indicators">
            <?php for($i = 0; $i < ceil(count($feedbacks) / 4); $i++): ?>
            <span class="indicator <?php echo $i === 0 ? 'active' : ''; ?>"></span>
            <?php endfor; ?>
        </div>
    </div>
</section>

<style>
html {
    font-size: 14px;
}

.reviews {
    background-color: #f9f9f9;
    padding: 2.857rem 1.428rem;
    text-align: center;
    overflow: hidden;
}

.reviews-header {
    margin-bottom: 2.143rem;
}

.reviews h2 {
    font-size: 2rem;
    color: #333;
    font-weight: 700;
}

.review-carousel {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    overflow: hidden;
}

.review-cards-container {
    display: flex;
    transition: transform 0.3s ease-out;
    width: 100%;
    touch-action: pan-y pinch-zoom;
}

.review-card {
    background: #ffffff;
    padding: 1.429rem;
    border-radius: 0.571rem;
    box-shadow: 0 0.286rem 0.857rem rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
}

.review-header {
    margin-bottom: 1.071rem;
    text-align: center;
}

.review-rating.room-rating {
    font-size: 2.143rem;
    color: #F6BE00;
    margin-bottom: 0.714rem;
    display: flex;
    justify-content: center;
}

.profile-picture {
    margin-bottom: 0.714rem;
    display: flex;
    justify-content: center;
}

.profile-picture img {
    width: 5.714rem;
    height: 5.714rem;
    border-radius: 50%;
    object-fit: cover;
}

.review-email {
    font-weight: bold;
    color: #388e3c;
    margin-bottom: 1.071rem;
}

.review-comment {
    font-size: 1.143rem;
    color: #333;
    margin-bottom: 1.071rem;
}

.see-full-review {
    font-size: 1rem;
    color: #388e3c;
    text-decoration: underline;
    cursor: pointer;
}

.review-post-date {
    font-size: 0.857rem;
    color: #81c784;
    margin-top: 1.071rem;
}

.carousel-indicators {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.indicator {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background-color: #ddd;
    cursor: pointer;
}

.indicator.active {
    background-color: #388e3c;
}

@media (min-width: 1024px) {
    .review-card {
        flex: 0 0 25%; 
        padding: 1.429rem;
        margin: 0 0.714rem;
    }
}

@media (min-width: 768px) and (max-width: 1023px) {
    .review-card {
        flex: 0 0 33.333%; 
        padding: 1.429rem;
        margin: 0 0.714rem;
    }
}

@media (max-width: 767px) {
    .review-card {
        flex: 0 0 100%; 
        padding: 1rem;
        margin: 0;
    }
    
    .reviews {
        padding: 2rem 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('reviewContainer');
    const carousel = document.getElementById('reviewCarousel');
    const cards = document.querySelectorAll('.review-card');
    const indicators = document.querySelectorAll('.indicator');
    let currentIndex = 0;
    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    function getCardsPerView() {
        if (window.innerWidth >= 1024) return 4;
        if (window.innerWidth >= 768) return 3;
        return 1;
    }

    container.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
        container.style.transition = 'none';
    });

    container.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        
        currentX = e.touches[0].clientX;
        const diff = currentX - startX;
        const cardsPerView = getCardsPerView();
        const offset = -(currentIndex * (100 / cardsPerView)) + (diff / carousel.offsetWidth * 100);
        
        container.style.transform = `translateX(${offset}%)`;
    });

    container.addEventListener('touchend', (e) => {
        isDragging = false;
        container.style.transition = 'transform 0.3s ease-out';
        
        const diff = currentX - startX;
        const threshold = carousel.offsetWidth / 4;
        const cardsPerView = getCardsPerView();
        const maxIndex = Math.ceil(cards.length / cardsPerView) - 1;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0 && currentIndex > 0) {
                currentIndex--;
            } else if (diff < 0 && currentIndex < maxIndex) {
                currentIndex++;
            }
        }
        
        updateCarousel();
    });

    container.addEventListener('mousedown', (e) => {
        startX = e.clientX;
        isDragging = true;
        container.style.transition = 'none';
    });

    container.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        
        currentX = e.clientX;
        const diff = currentX - startX;
        const cardsPerView = getCardsPerView();
        const offset = -(currentIndex * (100 / cardsPerView)) + (diff / carousel.offsetWidth * 100);
        
        container.style.transform = `translateX(${offset}%)`;
    });

    container.addEventListener('mouseup', () => {
        isDragging = false;
        container.style.transition = 'transform 0.3s ease-out';
        
        const diff = currentX - startX;
        const threshold = carousel.offsetWidth / 4;
        const cardsPerView = getCardsPerView();
        const maxIndex = Math.ceil(cards.length / cardsPerView) - 1;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0 && currentIndex > 0) {
                currentIndex--;
            } else if (diff < 0 && currentIndex < maxIndex) {
                currentIndex++;
            }
        }
        
        updateCarousel();
    });

    container.addEventListener('mouseleave', () => {
        if (isDragging) {
            isDragging = false;
            container.style.transition = 'transform 0.3s ease-out';
            updateCarousel();
        }
    });

    function updateCarousel() {
        const cardsPerView = getCardsPerView();
        const offset = -(currentIndex * (100 / cardsPerView));
        container.style.transform = `translateX(${offset}%)`;
        
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentIndex);
        });
    }

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentIndex = index;
            updateCarousel();
        });
    });
    window.addEventListener('resize', () => {
        const cardsPerView = getCardsPerView();
        const maxIndex = Math.ceil(cards.length / cardsPerView) - 1;
        currentIndex = Math.min(currentIndex, maxIndex);
        updateCarousel();
    });
    updateCarousel();
});
</script>