<?php
if (!isset($_GET['pages'])) {
    header('Location: index?pages=Home-Page');
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Cavite State University</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="asset/css/button-up-css/up_buttonss.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    :root {
        --cvsu-primary-green: #006400;
        --cvsu-light-green: #90EE90;
        --cvsu-hover-green: #008000;
        --cvsu-text-light: #ffffff;
        --cvsu-text-dark: #333333;
        --cvsu-gray-light: #f5f5f5;
        --cvsu-border-color: rgba(0, 0, 0, 0.1);
        --cvsu-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
        --cvsu-shadow-md: 0 2px 5px rgba(0, 0, 0, 0.10);
        --cvsu-transition: all 0.3s ease;
    }

    @keyframes cvsuFadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes cvsuSlideInFromLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes dropdownFade {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        background-color: var(--cvsu-gray-light);
        min-height: 100vh;
        line-height: 1.6;
    }

    .cvsu-header {
        background-image: url('https://png.pngtree.com/thumb_back/fh260/background/20210430/pngtree-modern-green-techno-background-image_689325.jpg');
        background-size: cover;
        background-position: center bottom;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 0;
        box-shadow: var(--cvsu-shadow-md);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .cvsu-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
        width: 100%;
    }

    .cvsu-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
    }

    .cvsu-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: var(--cvsu-text-light);
        gap: 1rem;
    }

    .cvsu-logo img {
        height: 50px;
        width: auto;
        object-fit: contain;
    }

    .cvsu-logo span {
        font-family: 'Montserrat';
        font-size: 1.1rem;
        /*font-weight: bold;*/
        white-space: nowrap;
        line-height: 1;

    }

    .cvsu-profile-dropdown {
        position: relative;
        margin-right: 1rem;
    }

    .cvsu-profile-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: none;
        border: none;
        color: var(--cvsu-text-light);
        padding: 0.5rem;
        cursor: pointer;
        transition: var(--cvsu-transition);
    }

    .cvsu-profile-icon {
        width: 32px;
        height: 32px;
        background-color: var(--cvsu-text-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cvsu-profile-icon i {
        color: var(--cvsu-primary-green);
        font-size: 0.9rem;
    }

    .cvsu-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--cvsu-text-light);
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 200px;
        padding: 0.5rem 0;
        display: none;
        z-index: 1000;
    }

    .cvsu-dropdown-menu.active {
        display: block;
        animation: dropdownFade 0.2s ease-out;
    }

    .cvsu-dropdown-menu a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--cvsu-text-dark);
        text-decoration: none;
        transition: var(--cvsu-transition);
    }

    .cvsu-dropdown-menu a:hover {
        background-color: rgba(0, 100, 0, 0.05);
        color: var(--cvsu-primary-green);
    }

    .cvsu-dropdown-menu i {
        width: 20px;
        text-align: center;
    }

    .cvsu-dropdown-divider {
        height: 1px;
        background-color: var(--cvsu-border-color);
        margin: 0.5rem 0;
    }

    .cvsu-profile-nav {
        display: none;
    }

    .cvsu-profile-nav .cvsu-profile-btn {
        width: 100%;
        justify-content: flex-start;
        padding: 0.75rem 1rem;
        color: var(--cvsu-text-dark);
        border-bottom: 1px solid var(--cvsu-border-color);
    }

    .cvsu-profile-nav .cvsu-profile-icon {
        background-color: var(--cvsu-primary-green);
    }

    .cvsu-profile-nav .cvsu-profile-icon i {
        color: var(--cvsu-text-light);
    }

    .cvsu-profile-nav .cvsu-dropdown-menu {
        position: static;
        width: 100%;
        box-shadow: none;
        border-radius: 0;
        border-bottom: 1px solid var(--cvsu-border-color);
    }

    .cvsu-profile-nav .cvsu-dropdown-menu a {
        padding: 0.75rem 2rem;
    }

    .cvsu-secondary-nav {
        background-color: white;
        padding: 0.5rem 0;
        box-shadow: var(--cvsu-shadow-sm);
        position: sticky;
        top: calc(40px + 1.5rem);
        z-index: 999;
    }

    .cvsu-nav-list {
        max-width: 1200px;
        margin: 0 auto;
        list-style: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 1.5rem;
        width: 100%;
    }

    .cvsu-nav-list li {
        animation: cvsuSlideInFromLeft 0.5s ease-out;
        animation-fill-mode: both;
    }

    .cvsu-nav-list li a {
        color: var(--cvsu-text-dark);
        text-decoration: none;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: var(--cvsu-transition);
        font-weight: 500;
        font-size: 0.95rem;
        white-space: nowrap;
    }

    .cvsu-nav-list li a:hover {
        color: var(--cvsu-hover-green);
        background-color: rgba(0, 100, 0, 0.05);
        transform: translateY(-2px);
    }

    .cvsu-auth-buttons {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .cvsu-nav-list .cvsu-auth-buttons {
        display: none;
    }

    .cvsu-auth-button {
        padding: 0.5rem 1.25rem;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        transition: var(--cvsu-transition);
        font-size: 0.9rem;
    }

    .cvsu-login-btn {
        background-color: #005bef;
        font-weight: bold;
        color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        /* Add shadow effect */
        transition: box-shadow 0.3s ease;
        /* Smooth transition for shadow */
    }

    .cvsu-login-btn:hover {
        background-color: rgb(0, 63, 163);
    }

    .cvsu-register-btn {
        background-color: #6a38df;
        color: white;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        /* Add shadow effect */
        transition: box-shadow 0.3s ease;
        /* Smooth transition for shadow */
    }


    .cvsu-register-btn:hover {
        background-color: rgb(72, 38, 150);


    }

    .cvsu-menu-toggle {
        display: none;
        background: none;
        border: none;
        color: var(--cvsu-text-light);
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        transition: var(--cvsu-transition);
    }

    @media (max-width: 768px) {
        .cvsu-profile-header {
            display: none;
        }

        .cvsu-profile-nav {
            display: block;
        }

        .cvsu-header {
            padding: 0.5rem 0;
        }

        .cvsu-container {
            padding: 0 1rem;
        }

        .cvsu-menu-toggle {
            display: block;
        }

        .cvsu-logo img {
            height: 35px;
        }

        .cvsu-logo span {
            font-size: 1.1rem;
        }

        .cvsu-secondary-nav {
            display: none;
            position: fixed;
            width: 100%;
            top: 57px;
            left: 0;
            background-color: white;
            box-shadow: var(--cvsu-shadow-sm);
            padding: 0;
            z-index: 999;
        }

        .cvsu-secondary-nav.cvsu-active {
            display: block;
        }


        .cvsu-nav-list {
            display: flex;
            flex-direction: column;
            padding: 0;
            margin-top: 20px;
        }

        .cvsu-nav-list li {
            width: 100%;
            border-bottom: 1px solid var(--cvsu-border-color);
        }

        .cvsu-nav-list li:last-child {
            border-bottom: none;
        }

        .cvsu-nav-list li a {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            width: 100%;
            justify-content: flex-start;
        }

        .cvsu-header .cvsu-auth-buttons {
            display: none;
        }

        .cvsu-nav-list .cvsu-auth-buttons {
            display: flex;
            width: 100%;
            padding: 0.75rem 1rem;
            border-top: 1px solid var(--cvsu-border-color);
        }

        .cvsu-nav-list .cvsu-auth-button {
            flex: 1;
            text-align: center;
        }

        .cvsu-nav-list .cvsu-login-btn {
            background-color: #005bef;
            border-color: var(--cvsu-border-color);
            color: white;
        }

        .cvsu-nav-list .cvsu-register-btn {
            background-color: #6a38df;
            border-color: var(--cvsu-primary-green);
            color: white;
        }

        .cvsu-secondary-nav {
            padding: 0.25rem 0;
            /* Reduce padding on smaller screens */
            top: calc(30px + 1rem);
            /* Adjust top position if needed */
        }
    }


    @media (max-width: 480px) {
        .cvsu-logo span {
            font-size: 1rem;
        }

        .cvsu-logo img {
            height: 30px;
        }

        .cvsu-secondary-nav {
            padding: 0.5rem 0;
            /* Maintain padding for very small screens */
            top: calc(20px + 0.5rem);
            /* Further adjust top position */
        }
    }
</style>

<body>
    <header class="cvsu-header">
        <div class="cvsu-container cvsu-header-content">
            <a href="index" class="cvsu-logo">
                <img src="asset/images/1.png" alt="CSU Logo">
                <img src="asset/images/2.png" alt="CSU Logo">
                <span>Cavite State University<br>Office of Alumni Affairs</span>
            </a>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="cvsu-auth-buttons">
                    <a href="Access-Point?Cavite-State-University=login" class="cvsu-auth-button cvsu-login-btn">Login</a>
                    <a href="Access-Point?Cavite-State-University=register" class="cvsu-auth-button cvsu-register-btn">Register</a>
                </div>
            <?php else: ?>
                <div class="cvsu-profile-dropdown cvsu-profile-header">
                    <button class="cvsu-profile-btn">
                        <div class="cvsu-profile-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="cvsu-dropdown-menu">
                        <a href="Account?section=profile-settings"><i class="fas fa-user-circle"></i> Profile</a>
                        <a href="Account?section=Room-Reservation"><i class="fas fa-calendar-alt"></i> Bookings</a>
                        <a href="Account?section=notification-settings"><i class="fas fa-bell"></i> Notifications</a>
                        <div class="cvsu-dropdown-divider"></div>
                        <a href="user/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php endif; ?>

            <button class="cvsu-menu-toggle" id="cvsuMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <nav class="cvsu-secondary-nav">
        <ul class="cvsu-nav-list">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="cvsu-profile-dropdown cvsu-profile-nav">
                    <button class="cvsu-profile-btn">
                        <div class="cvsu-profile-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="cvsu-dropdown-menu">
                        <a href="?section=profile-settings"><i class="fas fa-user-circle"></i> Profile</a>
                        <a href="?section=Room-Reservation"><i class="fas fa-calendar-alt"></i> Bookings</a>
                        <a href="?section=notification-settings"><i class="fas fa-bell"></i> Notifications</a>
                        <div class="cvsu-dropdown-divider"></div>
                        <a href="user/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>
            <?php endif; ?>
            <li><a href="?pages=Home-Page"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="?pages=Room-Accommodation"><i class="fas fa-bed"></i> Room Accommodation</a></li>
            <li><a href="?pages=alumni-tracer"><i class="fas fa-graduation-cap"></i> Alumni Tracer</a></li>
            <li><a href="?pages=events"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="?pages=careers"><i class="fas fa-briefcase"></i> Career</a></li>
            <li><a href="?pages=news-features"><i class="fas fa-newspaper"></i> News / Features</a></li>
            <li><a href="?pages=satellite-campus"><i class="fas fa-university"></i> Campus</a></li>
            <li><a href="?pages=About-CvSU"><i class="fas fa-info-circle"></i> About</a></li>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="cvsu-auth-buttons">
                    <a href="Access-Point?Cavite-State-University=login" class="cvsu-auth-button cvsu-login-btn">Login</a>
                    <a href="Access-Point?Cavite-State-University=register" class="cvsu-auth-button cvsu-register-btn">Register</a>
                </div>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="cvsu-main-content">
        <?php
        $section = isset($_GET['pages']) ? $_GET['pages'] : 'home';

        switch ($section) {
            case 'Home-Page':
                include 'admin/website/Home-Page.php';
                break;
            case 'Room-Accommodation':
                include 'admin/website/Room-Accomodation.php';
                break;
            case 'alumni-tracer':
                include 'admin/website/Alumni-Tracer.php';
                break;
            case 'careers':
                include 'admin/website/Employment.php';
                break;
            case 'events':
                include 'admin/website/Event.php';
                break;
            case 'announcement':
                include 'admin/website/announcement_details.php';
                break;
            case 'news-features':
                include 'admin/website/Article-News.php';
                break;
            case 'all-news':
                include 'admin/website/components/news/all-news.php';
                break;
            case 'terms-and-conditions':
                include 'admin/website/terms-and-conditions.php';
                break;
            case 'contact-us':
                include 'admin/website/contact-us.php';
                break;
            case 'privacy-policy':
                include 'admin/website/privacy-policy.php';
                break;
            case 'news-details':
                include 'admin/website/components/news/new-details.php';
                break;
            case 'room-details':
                include 'admin/website/components/home/room-features.php';
                break;
            case 'satellite-campus':
                include 'admin/website/Campus-Satellite.php';
                break;
            case 'About-CvSU':
                include 'admin/website/About.php';
                break;
        }
        ?>
    </main>

    <?php include 'user/chat-bot-index.php'; ?>

    <?php include('asset/OOP/pages-link/back-to-top.php'); ?>
    <?php include('asset/OOP/pages-link/footer.php'); ?>
    <script src="asset/javascript/button-up-js/up_button.js"></script>

    <script>
        document.getElementById('cvsuMenuToggle').addEventListener('click', function() {
            const secondaryNav = document.querySelector('.cvsu-secondary-nav');
            secondaryNav.classList.toggle('cvsu-active');

            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        document.addEventListener('click', function(event) {
            const secondaryNav = document.querySelector('.cvsu-secondary-nav');
            const menuToggle = document.getElementById('cvsuMenuToggle');

            if (!event.target.closest('.cvsu-secondary-nav') &&
                !event.target.closest('.cvsu-menu-toggle') &&
                secondaryNav.classList.contains('cvsu-active')) {
                secondaryNav.classList.remove('cvsu-active');
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const profileBtns = document.querySelectorAll('.cvsu-profile-btn');

            profileBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = this.nextElementSibling;
                    const chevron = this.querySelector('.fa-chevron-down');

                    document.querySelectorAll('.cvsu-dropdown-menu').forEach(menu => {
                        if (menu !== dropdown) {
                            menu.classList.remove('active');
                            const otherChevron = menu.previousElementSibling.querySelector('.fa-chevron-down');
                            if (otherChevron) {
                                otherChevron.style.transform = 'rotate(0)';
                            }
                        }
                    });

                    dropdown.classList.toggle('active');
                    if (chevron) {
                        chevron.style.transform = dropdown.classList.contains('active') ?
                            'rotate(180deg)' :
                            'rotate(0)';
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.cvsu-profile-dropdown')) {
                    document.querySelectorAll('.cvsu-dropdown-menu').forEach(menu => {
                        menu.classList.remove('active');
                        const chevron = menu.previousElementSibling.querySelector('.fa-chevron-down');
                        if (chevron) {
                            chevron.style.transform = 'rotate(0)';
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>