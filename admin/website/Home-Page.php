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
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .slide-content h2 {
        font-family: 'Times New Roman', Times, serif;
        font-size: 35px;
        margin-bottom: 15px;
        font-weight: 700;
        color:rgb(223, 226, 30);
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.8s ease;
        letter-spacing: 1px;
        line-height: 1;
    }
    .slide-content2 h2 {
        font-family: 'Times New Roman', Times, serif;
        font-size: 35px;
        margin-bottom: 15px;
        font-weight: 700;
        color:rgb(223, 226, 30);
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.8s ease;
        letter-spacing: 1px;
        line-height: 1;
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
    .swiper-slide-active .slide-content {
        opacity: 1;
    }
    
    .swiper-slide-active .slide-content h2,
    .swiper-slide-active .slide-content p,
    .swiper-slide-active .slide-content .cta-btn,
    .swiper-slide-active .slide-content2 h2 {
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
        
        .slide-content h2,
        .slide-content2 h2 {
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
        
        .slide-content h2,
        .slide-content2 h2 {
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
        color:rgb(209, 230, 22);
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
        left: 35%;
        transform: translate(-50%, -50%); /* Center the content */
        text-align: right; /* Center text */
        z-index: 2; /* Ensure content is above the image */
        color: white; /* Optional: Text color for contrast */
    }


    .slide-content2 {
        position: absolute; /* Position content over the image */
        top: 50%;
        left: 65%;
        transform: translate(-50%, -50%); /* Center the content */
        text-align: left; /* Center text */
        z-index: 2; /* Ensure content is above the image */
        color: white; /* Optional: Text color for contrast */
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    .swiper-slide-active .slide-content2 {
        opacity: 1;
    }


    .support-icon {
        display: flex;
        width: 300px; /* Set width of the image */
        height: 300px; /* Set height of the image */
        margin-left: 700px; /* Space between image and text */
        transition: transform 0.8s ease;
        opacity: 0;
        transform: translateX(-50px);
        transition: all 1s ease;
    }
    
    .support-icons {
        display: flex;
        width: 300px; /* Set width of the image */
        height: 300px; /* Set height of the image */    
        transition: transform 0.8s ease;
        opacity: 0;
        transform: translateX(50px);
        transition: all 1s ease;
    }
    
    .support-icon2 {
        display: flex;
        width: 400px; /* Set width of the image */
        height: 400px; /* Set height of the image */
        margin-right: 700px; /* Space between image and text */
        transition: transform 0.8s ease;
        opacity: 0;
        transform: translateY(50px);
        transition: all 1s ease;
    }

    /* Active slide image animations */
    .swiper-slide-active .support-icon {
        opacity: 1;
        transform: translateX(0);
    }
    
    .swiper-slide-active .support-icons {
        opacity: 1;
        transform: translateX(0);
    }
    
    .swiper-slide-active .support-icon2 {
        opacity: 1;
        transform: translateY(0);
    }

    .cta-btn {
        display: inline-block; /* Make it behave like a button */
        padding: 10px 20px; /* Add padding for size */
        background-color: gold; /* Gold background color */
        color: #000005; /* Text color */
        font-size: 18px; /* Font size */
        font-weight: bold; /* Bold text */
        border: none; /* Remove border */
        border-radius: 5px; /* Rounded corners */
        text-align: center; /* Center text */
        text-decoration: none; /* Remove underline */
        transition: background-color 0.3s ease, transform 0.3s ease; /* Transition effects */
    }

    .cta-btn:hover {
        background-color: darkgoldenrod; /* Darker gold on hover */
        transform: scale(1.05); /* Scale effect on hover */
    }

    /* Adding animation class */
    .animate__animated.animate__zoomIn {
        animation-duration: 0.5s; /* Set animation duration */
    }



    .check-icon {
        display: inline-block;
        width: 30px; /* Width of the circle */
        height: 24px; /* Height of the circle */
        border: 2px solid gold; /* Circle outline */
        border-radius: 50%; /* Make it circular */
        color: gold; /* Set check mark color to gold */
        text-align: center; /* Center the check mark */
        line-height: 24px; /* Center vertically */
        margin-right: 10px; /* Space between the icon and text */
        font-size: 18px; /* Adjust font size if needed */
        padding-right: 20px;
    }

    .check-icon::after{
        content: '✔';
        margin-left: 5px; /* Add margin to move it to the right */
        color: gold; /* Keep check mark color gold */
    }


    /* Enhanced Media Queries for Responsiveness */
    @media (max-width: 1200px) {
        .support-icon, .support-icons {
            margin-left: auto;
            margin-right: auto;
            position: relative;
            top: -50px;
        }
        
        .support-icon2 {
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }
    }

    @media (max-width: 992px) {
        .slide-content, .slide-content2 {
            width: 80%;
        }
        
        .support-icon, .support-icons, .support-icon2 {
            width: 250px;
            height: 250px;
        }
    }

    @media (max-width: 768px) {
        .slide-content, .slide-content2 {
            left: 50%; /* Center content on smaller screens */
            text-align: center; /* Center text */
            width: 90%;
        }

        .support-icon, .support-icon2, .support-icons {
            width: 200px; /* Smaller on medium screens */
            height: 200px;
            margin: 0 auto;
            position: relative;
            top: -60px;
        }

        .cta-btn {
            font-size: 16px; /* Adjust font size for smaller screens */
        }
    }

    @media (max-width: 576px) {
        .support-icon, .support-icon2, .support-icons {
            width: 150px; /* Even smaller on small screens */
            height: 150px;
            top: -40px;
        }
        
        .slide-content h2, .slide-content2 h2 {
            font-size: 22px;
        }
        
        .slide-content p {
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .check-icon {
            width: 25px; /* Adjust width for small screens */
            height: 25px; /* Adjust height for small screens */
        }

        .cta-btn {
            padding: 8px 16px; /* Adjust padding for smaller buttons */
            font-size: 14px;
        }
        
        .support-icon, .support-icon2, .support-icons {
            width: 120px; /* Smallest size */
            height: 120px;
            top: -30px;
        }
        
        .hero-swiper-container {
            height: 450px; /* Give more height on small screens */
        }
    }
</style>

<body>
    <!-- Hero Swiper Section - Replace your original herodaw section with this -->
    <section class="hero-swiper-section">
        <div class="swiper hero-swiper-container">
            <div class="swiper-wrapper">
                <!-- First slide -->
                <div class="swiper-slide slide-1">
                    <img src="asset/images/1.png" alt="Support Icon" class="support-icon">
                    <img src="asset/images/2.png" alt="Support Icon" class="support-icons">
                    <div class="slide-content">
                        <h2 class="animate__animated animate__fadeInDown">OFFICE OF <br> ALUMNI AFFAIRS<br><br></h2>
                        <p class="animate__animated animate__fadeInUp">The Office of Alumni Affairs at Cavite State University is here to strengthen our connection with fellow graduates. We provide resources for networking, professional development, and community engagement, ensuring that our alumni remain involved and support each other. Together, we celebrate our achievements and foster a lasting sense of pride in our university.</p>
                        <!--<a href="Room-Accomodation.php" class="cta-btn animate__animated animate__zoomIn">Explore Our Services</a> -->
                    </div>
                </div>
                
                <!-- Second slide -->
                <div class="swiper-slide slide-2">
                    <img src ="asset/images/signup1.png" alt="Support Icon" class="support-icon2">
                    <div class="slide-content2">
                        <h2 class="animate__animated animate__fadeInDown">REGISTER & UPDATE<br>YOUR ALUMNI PROFILE<br></h2>
                        <p class="animate__animated animate__fadeInUp"> To access exclusive alumni features: <br>
                            <span class="check-icon">   </span>      Alumni ID<br>
                            <span class="check-icon">   </span>      Alumni Tracer<br>
                            <span class="check-icon">   </span>      Room Booking<br><br></p>
                        <a href="Account.php" class="cta-btn animate__animated animate__zoomIn">Sign-Up Here</a> 
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
                effect: 'slide', // Changed from fade to slide animation
                
                // Auto play - ensure it's always running
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
                    init: function() {
                        setTimeout(() => {
                            // Add animation classes to first slide on init
                            const activeSlide = document.querySelector('.swiper-slide-active');
                            this.animateSlide(activeSlide);
                        }, 100);
                    },
                    
                    slideChangeTransitionStart: function() {
                        // Hide all slide content first
                        const slides = document.querySelectorAll('.swiper-slide');
                        slides.forEach(slide => {
                            const h2 = slide.querySelector('h2');
                            const p = slide.querySelector('p');
                            const btn = slide.querySelector('.cta-btn');
                            const icons = slide.querySelectorAll('img[class^="support"]');
                            
                            if (h2) {
                                h2.classList.remove('animate__fadeInDown');
                                h2.style.opacity = 0;
                            }
                            if (p) {
                                p.classList.remove('animate__fadeInUp');
                                p.style.opacity = 0;
                            }
                            if (btn) {
                                btn.classList.remove('animate__zoomIn');
                                btn.style.opacity = 0;
                            }
                            if (icons) {
                                icons.forEach(icon => {
                                    icon.style.opacity = 0;
                                });
                            }
                        });
                    },
                    
                    slideChangeTransitionEnd: function() {
                        // Animate the active slide
                        const activeSlide = document.querySelector('.swiper-slide-active');
                        this.animateSlide(activeSlide);
                    }
                }
            });
            
            // Add custom animation method to Swiper instance
            heroSwiper.animateSlide = function(slide) {
                if (!slide) return;
                
                const h2 = slide.querySelector('h2');
                const p = slide.querySelector('p');
                const btn = slide.querySelector('.cta-btn');
                const icons = slide.querySelectorAll('img[class^="support"]');
                
                // First animate the icons with a small delay
                if (icons) {
                    icons.forEach((icon, index) => {
                        setTimeout(() => {
                            icon.style.opacity = 1;
                            icon.style.transform = 'translateX(0) translateY(0)';
                        }, index * 200);
                    });
                }
                
                // Then animate text content with sequential delays
                setTimeout(() => {
                    if (h2) {
                        h2.classList.add('animate__fadeInDown');
                        h2.style.opacity = 1;
                        h2.style.animationDuration = '0.8s';
                    }
                    
                    setTimeout(() => {
                        if (p) {
                            p.classList.add('animate__fadeInUp');
                            p.style.opacity = 1;
                            p.style.animationDuration = '0.8s';
                        }
                        
                        setTimeout(() => {
                            if (btn) {
                                btn.classList.add('animate__zoomIn');
                                btn.style.opacity = 1;
                                btn.style.animationDuration = '0.5s';
                            }
                        }, 300);
                    }, 300);
                }, 500);
            };
        });
    </script>
</body>
</html>