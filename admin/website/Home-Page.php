<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Cavite State University</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="admin/website/components/home/home-styles.css">
    <!-- Add animation library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

</head>
<style>
    :root {
        --cvsu-primary-green: #006400;
        --cvsu-hover-green: #008000;
    }

    /* Hero Swiper Styles */
    .hero-swiper-container {
        width: 100%;
        height: 500px;
        margin: 0;
        position: relative;
        overflow: hidden;
    }
    
    .swiper-slide {
        text-align: center;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    

    
    /* Individual slide backgrounds */
    .slide-1 {
        background-image: url('asset/images/bground2.jpg');

        
        
    }
    
    .slide-2 {
        background-image: url('asset/images/bground2.jpg');
    }
    
    .slide-3 {
        background-image: url('asset/images/bground2.jpg');
    }

    .slide-content {
        position: relative;
        z-index: 2;
        color: white;
        max-width: 800px;
        padding: 20px;
    }
    
    .slide-content h2 {
        font-size: 32px;
        margin-bottom: 15px;
        font-weight: 700;
        color: white;
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.8s ease;
    }
    
    .slide-content p {
        font-size: 16px;
        margin-bottom: 30px;
        line-height: 1.6;
        color: white;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease 0.3s;
    }
    
    .slide-content .cta-btn {
        padding: 12px 25px;
        background: var(--cvsu-primary-green);
        color: white;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
        opacity: 0;
        transform: scale(0.9);
        transition: all 0.8s ease 0.6s;
    }
    
    .slide-content .cta-btn:hover {
        background: var(--cvsu-hover-green);
        transform: translateY(-2px) scale(1.05);
    }
    
    /* Active slide animations */
    .swiper-slide-active .slide-content h2,
    .swiper-slide-active .slide-content p,
    .swiper-slide-active .slide-content .cta-btn {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    
    /* Navigation button styles */
    .swiper-button-next, 
    .swiper-button-prev {
        color: white;
    }
    
    /* Pagination styles */
    .swiper-pagination-bullet {
        background: white;
        opacity: 0.5;
    }
    
    .swiper-pagination-bullet-active {
        opacity: 1;
        background: white;
    }
    
    @media (max-width: 768px) {
        .hero-swiper-container {
            height: 400px;
        }
        
        .slide-content h2 {
            font-size: 28px;
        }
        
        .slide-content p {
            font-size: 15px;
        }
        
        .slide-content .cta-btn {
            padding: 10px 20px;
            font-size: 15px;
        }
    }
    
    @media (max-width: 480px) {
        .hero-swiper-container {
            height: 350px;
        }
        
        .slide-content h2 {
            font-size: 24px;
        }
        
        .slide-content p {
            font-size: 14px;
            line-height: 1.5;
        }
        
        .slide-content .cta-btn {
            padding: 8px 18px;
            font-size: 14px;
        }
    }

    /* Original herodaw for backward compatibility - can be removed if replaced */
    .herodaw {
        color: white;
        background-color: var(--cvsu-primary-green);
        padding: 80px 20px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        min-height: 300px;
        height: 500px;
    }

    .herodaw h2 {
        font-size: 32px;
        margin-bottom: 15px;
        font-weight: 700;
        position: relative;
        z-index: 2;
        color: white;
    }

    .herodaw p {
        font-size: 16px;
        margin-bottom: 20px;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
        position: relative;
        z-index: 2;
        color: white;
    }

    .herodaw .cta-btn {
        padding: 12px 25px;
        background: var(--cvsu-primary-green);
        color: white;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-top: 40px;
        position: absolute;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        border: 2px solid transparent;
    }

    .herodaw .cta-btn:hover {
        background: var(--cvsu-hover-green);
        transform: translateX(-50%) translateY(-2px);
    }

    .herodaw::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        height: 500px;
        background-image: url('asset/images/bground.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        filter: brightness(0.4);
        z-index: 1;
    }

    .slide-image {
    width: 100%; /* Make the image responsive */
    height: auto; /* Maintain aspect ratio */
    display: block; /* Remove bottom space */
    }

    .slide-content {
        position: absolute; /* Position content over the image */
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); /* Center the content */
        text-align: center; /* Center text */
        z-index: 2; /* Ensure content is above the image */
        color: white; /* Optional: Text color for contrast */
}

</style>

<body>
    <!-- Hero Swiper Section - Replace your original herodaw section with this -->
    <section class="hero-swiper-section">
        <div class="swiper hero-swiper-container">
            <div class="swiper-wrapper">
                <!-- First slide -->
                <div class="swiper-slide slide-1">
                    <div class="slide-content">
                        <h2 class="animate__animated animate__fadeInDown">Welcome to Office of Alumni Affairs</h2>
                        <p class="animate__animated animate__fadeInUp">Your easy solution for booking meeting rooms, events, and conferences at Cavite State University.</p>
                        <a href="Room-Accomodation.php" class="cta-btn animate__animated animate__zoomIn">Explore Our Services</a>
                    </div>
                </div>
                
                <!-- Second slide -->
                <div class="swiper-slide slide-2">
                    <div class="slide-content">
                        <h2 class="animate__animated animate__fadeInDown">Connect With Fellow Alumni</h2>
                        <p class="animate__animated animate__fadeInUp">Join our network of successful graduates and stay connected with your alma mater.</p>
                        <a href="#network" class="cta-btn animate__animated animate__zoomIn">Join Network</a>
                    </div>
                </div>
                
                <!-- Third slide -->
                <div class="swiper-slide slide-3">
                    <div class="slide-content">
                        <h2 class="animate__animated animate__fadeInDown">Alumni Services</h2>
                        <p class="animate__animated animate__fadeInUp">Discover exclusive benefits and services available to all Cavite State University alumni.</p>
                        <a href="#benefits" class="cta-btn animate__animated animate__zoomIn">View Benefits</a>
                    </div>
                </div>
            </div>
            
            <!-- Add navigation buttons -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            
            <!-- Add pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- Rest of your content remains unchanged -->
    <?php include('components/home/announcement.php'); ?>
    <?php include('components/home/features.php'); ?>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    
    <!-- Initialize Swiper -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heroSwiper = new Swiper('.hero-swiper-container', {
                // Optional parameters
                direction: 'horizontal',
                loop: true,
                grabCursor: true,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                
                // Auto play
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                
                // Speed
                speed: 1000,
                
                // Pagination
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                
                // Navigation arrows
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                
                // On slide change event - for custom animations
                on: {
                    slideChangeTransitionStart: function() {
                        // Remove animation classes from previous slide
                        const slides = document.querySelectorAll('.swiper-slide');
                        slides.forEach(slide => {
                            const h2 = slide.querySelector('h2');
                            const p = slide.querySelector('p');
                            const btn = slide.querySelector('.cta-btn');
                            
                            if (h2) h2.classList.remove('animate__fadeInDown');
                            if (p) p.classList.remove('animate__fadeInUp');
                            if (btn) btn.classList.remove('animate__zoomIn');
                        });
                    },
                    slideChangeTransitionEnd: function() {
                        // Add animation classes to current slide
                        const activeSlide = document.querySelector('.swiper-slide-active');
                        const h2 = activeSlide.querySelector('h2');
                        const p = activeSlide.querySelector('p');
                        const btn = activeSlide.querySelector('.cta-btn');
                        
                        if (h2) {
                            h2.classList.add('animate__fadeInDown');
                            h2.style.animationDelay = '0.3s';
                        }
                        if (p) {
                            p.classList.add('animate__fadeInUp');
                            p.style.animationDelay = '0.6s';
                        }
                        if (btn) {
                            btn.classList.add('animate__zoomIn');
                            btn.style.animationDelay = '0.9s';
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>