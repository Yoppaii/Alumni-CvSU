<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'user/check_security_access.php';
require('main_db.php');


$hasSubmittedTracer = false;
if (isset($_SESSION['user_id'])) {
    $check_tracer_sql = "SELECT id FROM personal_info WHERE user_id = ?";
    $check_tracer_stmt = $mysqli->prepare($check_tracer_sql);
    $check_tracer_stmt->bind_param("i", $_SESSION['user_id']);
    $check_tracer_stmt->execute();
    $check_tracer_result = $check_tracer_stmt->get_result();
    $hasSubmittedTracer = ($check_tracer_result->num_rows > 0);
    $check_tracer_stmt->close();
}

function isLoggedIn() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
        return false;
    }
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT session_token FROM users WHERE id = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user || $user['session_token'] !== $_SESSION['session_token']) {
        return false;
    }
    
    return true;
}

if (!isLoggedIn()) {
    session_unset();
    session_destroy();
    header("Location: Access-Point?Cavite-State-University=login");
    exit();
}

$verified = isset($_SESSION['verified']) ? $_SESSION['verified'] : 0;
$userStatus = isset($_SESSION['user_status']) ? $_SESSION['user_status'] : 'Guest';
$hasBooking = isset($_SESSION['has_booking']) ? $_SESSION['has_booking'] : false;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$firstLogin = 0;

if (isset($_SESSION['user_id'])) {
    $login_sql = "SELECT first_login FROM users WHERE id = ?";
    $login_stmt = $mysqli->prepare($login_sql);
    $login_stmt->bind_param("i", $_SESSION['user_id']);
    $login_stmt->execute();
    $login_result = $login_stmt->get_result();
    $login_data = $login_result->fetch_assoc();
    
    if ($login_data) {
        $firstLogin = ($login_data['first_login'] === 1 || $login_data['first_login'] === '1') ? 1 : 0;
        $_SESSION['first_login'] = $firstLogin;
    }
}

$section = isset($_GET['section']) ? $_GET['section'] : 'home';

if (requiresSecurityVerification($section) && !isSecurityVerified()) {
    header("Location: Account?section=security-settings");
    exit();
}    

if (isset($_GET['section']) && $_GET['section'] === 'Room-Reservation') {
    $check_booking = $mysqli->prepare("SELECT id FROM bookings WHERE user_id = ? AND status = 'pending'");
    $check_booking->bind_param("i", $_SESSION['user_id']);
    $check_booking->execute();
    $result = $check_booking->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: Account?section=booking_history");
        exit();
    }
}

$sql = "SELECT id, user_id, first_name, last_name, middle_name, position, address, telephone, phone_number, second_address, accompanying_persons, user_status, verified, alumni_id_card_no FROM user WHERE user_id = ?";
$stmt = $mysqli->prepare($sql); 
$stmt->bind_param("i", $_SESSION['user_id']); 
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $verified = $user['verified'];
    $userStatus = $user['user_status'];
} else {
    $verified = 0;
    $userStatus = 'Guest';
}

$booking_sql = "SELECT id FROM bookings WHERE user_id = ?";
$booking_stmt = $mysqli->prepare($booking_sql);
$booking_stmt->bind_param("i", $_SESSION['user_id']);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();
$hasBooking = $booking_result->num_rows > 0;
$_SESSION['has_booking'] = $hasBooking;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Home'; ?></title>
    <link rel="icon" href="user/bg/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
    <style>
        :root {
            --primary-color: #006400;
            --secondary-color: #008000;
            --background-color: #f5f5f5;
            --text-color: #333333;
            --border-color: #e0e0e0;
            --header-height: 60px;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--background-color);
            min-height: 100vh;
        }

        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--text-color);
            display: none;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
            padding-right: 1rem;
        }

        .header-right > * {
            display: flex;
            align-items: center;
        }

        @media (min-width: 769px) {
            .header-right {
                margin-right: 0;
            }
        }

        .notification-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--text-color);
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
        }

        .main-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
            z-index: 1001;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .close-sidebar {
            display: none;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--text-color);
            position: absolute;
            right: 1rem;
            top: 1rem;
        }

        .user-profile {
            text-align: center;
            padding: 1rem 0;
        }

        .profile-image-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }

        .online-indicator {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 12px;
            height: 12px;
            background: #28a745;
            border-radius: 50%;
            border: 2px solid white;
        }

        .user-name {
            font-weight: bold;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .user-status {
            margin-top: 0.5rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge.alumni {
            background: var(--primary-color);
            color: white;
        }

        .status-badge.guest {
            background: #6c757d;
            color: white;
        }

        .verify-section {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .verify-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }

        .verify-button:hover {
            background: var(--secondary-color);
        }

        .nav-menu {
            padding: 1rem 0;
            height: calc(100vh - 200px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 100, 0, 0.5) transparent;
        }

        .nav-menu::-webkit-scrollbar {
            width: 6px;
        }

        .nav-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .nav-menu::-webkit-scrollbar-thumb {
            background-color: rgba(0, 100, 0, 0.5);
            border-radius: 3px;
        }

        .nav-menu::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 100, 0, 0.7);
        }

        .nav-menu ul {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            background: rgba(0, 100, 0, 0.05);
            color: var(--primary-color);
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
            margin-top: var(--header-height);
            min-height: calc(100vh - var(--header-height));
            background-color: #f5f5f5;
            position: relative;
        }

        .main-content > div,
        .main-content > section {
            padding: 0;
        }

        .main-content > *:first-child {
            margin-top: 0;
        }

        .main-content > *:last-child {
            margin-bottom: 0;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1100;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .modal-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .modal-body {
            margin-bottom: 1.5rem;
            color: #666;
        }

        .modal-button {
            padding: 0.75rem 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .modal-button:hover {
            background: var(--secondary-color);
        }

        .new-user-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1100;
            align-items: center;
            justify-content: center;
        }

        .new-user-modal-content {
            max-width: 800px;
            width: 90%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .user-video {
            width: 100%;
            display: block;
        }

        #playVideoButton {
            display: none;
            margin: 1rem auto;
            padding: 0.75rem 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .chat-floating-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .chat-floating-btn:hover {
            transform: scale(1.1);
            background: var(--secondary-color);
        }

        .version-info {
            position: fixed;
            bottom: 1rem;
            left: 1rem;
            font-size: 0.8rem;
            color: #666;
            z-index: 900;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;

            }

            .main-sidebar {
                transform: translateX(-100%);
            }

            .menu-toggle {
                display: block;
            }

            .close-sidebar {
                display: block;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .sidebar-open .sidebar-overlay {
                display: block;
                opacity: 1;
            }

            .version-info {
                display: none;
            }
        }

        .user-dropdown {
            position: relative;
        }

        .dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .dropdown-trigger i {
            transition: transform 0.3s ease;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            min-width: 180px;
            display: none;
            z-index: 1000;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--text-color);
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .dropdown-menu a:hover {
            background: rgba(0, 100, 0, 0.05);
            color: var(--primary-color);
        }
            .nav-link i {
            width: 28px;  
        }

        .settings-dropdown {
            position: relative;
        }

        .settings-trigger {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .settings-arrow {
            transition: transform 0.3s ease;
            width: auto !important;
            margin-left: auto;
        }

        .settings-dropdown.active .settings-arrow {
            transform: rotate(180deg);
        }

        .settings-menu {
            display: none;
            background: #f8f9fa;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease-out;
        }

        .settings-dropdown.active .settings-menu {
            display: block;
            max-height: 500px;
        }

        .settings-menu li {
            list-style: none;
        }

        .settings-menu a {
            display: flex;
            align-items: center;
            padding: 12px 32px 12px 48px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            gap: 12px;
        }

        .settings-menu a:hover {
            background: #f3f4f6;
            color: var(--primary-color);
        }
        .alumni-id {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #666;
        }
    </style>
<body>
    <header class="main-header">
        <button class="menu-toggle" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="header-right">
            <button class="notification-btn">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" style="display: none;">0</span>
            </button>
            <div class="user-dropdown">
                <div class="dropdown-trigger">
                    <span><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></span>
                    <i class="fas fa-caret-down"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="#">Profile</a>
                    <a href="#">Settings</a>
                    <a href="/Alumni-CvSU/user/logout.php">Log Out</a>
                </div>
            </div>
        </div>
    </header>

    <div class="sidebar-overlay"></div>
    
    <aside class="main-sidebar">
        <div class="sidebar-header">
            <button class="close-sidebar" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
            <div class="user-profile">
                <div class="profile-image-container">
                    <img src="asset/images/res1.png" alt="Profile Picture" class="profile-image">
                    <div class="online-indicator"></div>
                </div>
                <div class="user-name"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></div>
                <?php if ($verified == 1): ?>
                    <div class="user-status">
                        <span class="status-badge <?php echo $userStatus == 'Alumni' ? 'alumni' : 'guest'; ?>">
                            <?php echo $userStatus == 'Alumni' ? 'Alumni' : 'Guest'; ?>
                        </span>
                    </div>
                    <?php if ($userStatus == 'Alumni' && isset($user['alumni_id_card_no'])): ?>
                        <div class="alumni-id">
                            ID: <?php echo $user['alumni_id_card_no']; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($verified == 0): ?>
            <div class="verify-section">
                <a href="?section=Verify-Account" class="verify-button">Verify Now</a>
            </div>
        <?php endif; ?>

        <nav class="nav-menu">
            <ul>
                <li class="nav-item"><a href="index?pages=Home-Page" class="nav-link"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a href="?section=home" class="nav-link"><i class="fas fa-columns"></i> Dashboard</a></li>
                <li class="nav-item"><a href="?section=alumni-cvsu-chat-bot" class="nav-link"><i class="fas fa-robot"></i> Chatbot</a></li>
                <?php if ($userStatus != 'Alumni'): ?>
                    <li class="nav-item"><a href="?section=alumni-id" class="nav-link"><i class="fas fa-id-card"></i> Alumni ID Card</a></li>
                <?php endif; ?>
                <?php if (!$hasBooking): ?>
                    <li class="nav-item"><a href="?section=Room-Reservation" class="nav-link booking-link"><i class="fas fa-calendar-check"></i> Booking</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="?section=booking_history" class="nav-link"><i class="fas fa-calendar-check"></i> Booking History</a></li>
                <?php endif; ?>

                <?php if (!$hasSubmittedTracer && $verified == 1): ?>
                    <li class="nav-item"><a href="?section=Alumni-Tracer-Form" class="nav-link"><i class="fas fa-user"></i> Alumni Tracer Form</a></li>
                <?php endif; ?>

                <li class="nav-item settings-dropdown">
                    <a href="#" class="nav-link settings-trigger">
                        <i class="fas fa-cogs"></i> Settings
                        <i class="fas fa-chevron-down settings-arrow"></i>
                    </a>
                    <ul class="settings-menu">
                        <li><a href="?section=profile-settings"><i class="fas fa-user-cog"></i> Profile</a></li>
                        <li><a href="?section=account-information-settings"><i class="fas fa-cogs"></i> Account</a></li>
                        <li><a href="?section=security-settings"><i class="fas fa-user-shield"></i> Security</a></li>
                        <li><a href="?section=notification-settings"><i class="fas fa-bell"></i> Notification</a></li>
                        <li><a href="?section=privacy-settings"><i class="fas fa-shield-alt"></i> Privacy</a></li>
                        <li><a href="?section=support-settings"><i class="fas fa-phone"></i> Support</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a href="user/logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <?php 
        switch ($section) {
            case 'Verify-Account':
                include 'user/profile.php';
                break;
            case 'alumni-id':
                include 'user/alumni-id.php';
                break;
            case 'Room-Reservation':
                if (!$hasBooking) {
                    include 'user/booking.php';
                }
                break;
            case 'booking_history':
                if ($hasBooking) {
                    include 'user/booking_history.php';
                }
                break;
            case 'account-information-settings':
                include 'user/information-settings.php';
                break;
            case 'room-details':
                include 'user/room-details.php';
                break;
            case 're-apply-account':
                include 'user/re-apply-account.php';
                break;
            case 'security-settings':
                include 'user/security-settings.php';
                break;
            case 'alumni-cvsu-chat-bot':
                include 'user/chat-bot-account.php';
                break;
            case 'verify-alumni-user':
                include 'user/profile_alumni.php';
                break;
            case 'verify-guest-user':
                include 'user/profile_guest.php';
                break;
            case 'notification-settings':
                include 'user/notification-settings.php';
                break;
            case 'support-settings':
                include 'user/support-settings.php';
                break;
            case 'profile-settings':
                include 'user/profile-settings.php';
                break;
            case 'privacy-settings':
                include 'user/privacy-settings.php';
                break;
            case 'Alumni-Tracer-Form':
                    include 'user/alumni-tracer.php';
                    break;
            case '2-step-verification':
                include 'user/security-page/2_step_verification.php';
                break;
            case 'backup-codes':
                include 'user/security-page/backup_codes.php';
                break;
            case 'change-password':
                include 'user/security-page/change_password.php';
                break;
            case 'recovery-email':
                include 'user/security-page/recovery_emails.php';
                break;
            case 'Alumni-Tracer-Form':
                if (!$hasSubmittedTracer && $verified == 1) {
                    include 'user/alumni-tracer.php';
                } else {
                    header("Location: Account?section=home");
                    exit();
                }
                break;
            case 'home':
            default:
                include 'user/home.php';
                break;
        }
        ?>
    </main>
    <div id="verificationModal" class="modal">
        <div class="modal-content">
            <h4 class="modal-title">Account Verification Required</h4>
            <p class="modal-body">Please verify your account before making a booking.</p>
            <button id="verificationModalBtn" class="modal-button">Verify</button>
        </div>
    </div>

    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h4 class="modal-title">Session Expired</h4>
            <p class="modal-body">Your session has been logged out from another browser. Redirecting to login...</p>
            <button id="closeModalBtn" class="modal-button">OK</button>
        </div>
    </div>

    <div id="newUserModal" class="new-user-modal">
        <div class="new-user-modal-content">
            <video id="userIntroVideo" class="user-video">
                <source src="/Alumni-CvSU/asset/clip-video/Welcome.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <button id="playVideoButton">Play Video</button>
        </div>
    </div>

    <script src="user/booking_functions/session-expired.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const closeSidebar = document.querySelector('.close-sidebar');
            const sidebar = document.querySelector('.main-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const body = document.body;
            const settingsDropdown = document.querySelector('.settings-dropdown');
            const settingsTrigger = document.querySelector('.settings-trigger');

            if (settingsTrigger) {
                settingsTrigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    settingsDropdown.classList.toggle('active');
                });
            }

            function showSidebarForDesktop() {
                if (window.innerWidth > 768) {
                    sidebar.style.transform = 'translateX(0)';
                    overlay.style.display = 'none';
                    overlay.style.opacity = '0';
                    body.classList.remove('sidebar-open');
                }
            }

            function hideSidebarForMobile() {
                if (window.innerWidth <= 768) {
                    sidebar.style.transform = 'translateX(-100%)';
                    overlay.style.display = 'none';
                    overlay.style.opacity = '0';
                    body.classList.remove('sidebar-open');
                }
            }

            if (window.innerWidth > 768) {
                showSidebarForDesktop();
            } else {
                hideSidebarForMobile();
            }

            if (menuToggle) {
                menuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (sidebar && overlay) {
                        sidebar.style.transform = 'translateX(0)';
                        overlay.style.display = 'block';
                        setTimeout(() => {
                            overlay.style.opacity = '1';
                        }, 10);
                        body.classList.add('sidebar-open');
                    }
                });
            }

            function closeSidebarFunction() {
                if (sidebar && overlay) {
                    sidebar.style.transform = 'translateX(-100%)';
                    overlay.style.opacity = '0';
                    setTimeout(() => {
                        overlay.style.display = 'none';
                    }, 300);
                    body.classList.remove('sidebar-open');
                }
            }

            if (closeSidebar) {
                closeSidebar.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSidebarFunction();
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSidebarFunction();
                });
            }


            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    if (window.innerWidth > 768) {
                        showSidebarForDesktop();
                    } else {
                        hideSidebarForMobile();
                    }
                }, 250); 
            });

            const dropdownTrigger = document.querySelector('.dropdown-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            dropdownTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('active');
            });

            document.addEventListener('click', function() {
                dropdownMenu.classList.remove('active');
            });

            const bookingLinks = document.querySelectorAll('.booking-link');
            const verificationModal = document.getElementById('verificationModal');
            const verificationModalBtn = document.getElementById('verificationModalBtn');
            const verified = <?php echo $verified; ?>;

            bookingLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (verified == 0) {
                        e.preventDefault();
                        verificationModal.style.display = 'flex';
                    }
                });
            });

            verificationModalBtn.addEventListener('click', function() {
                window.location.href = '?section=Verify-Account&sidebar=1';
            });

            window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.style.display = 'none';
                }
            });

            <?php if ($firstLogin === 1): ?>
                const newUserModal = document.getElementById('newUserModal');
                const userVideo = document.getElementById('userIntroVideo');
                const playButton = document.getElementById('playVideoButton');

                function closeVideoAndUpdate() {
                    newUserModal.style.opacity = '0';
                    fetch('user/update_first_login.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'user_id=<?php echo $user_id; ?>'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            <?php $_SESSION['first_login'] = 0; ?>
                            setTimeout(() => {
                                newUserModal.style.display = 'none';
                                newUserModal.style.opacity = '1';
                            }, 500);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        newUserModal.style.display = 'none';
                    });
                }
                if (<?php echo $firstLogin; ?> === 1) {
                    newUserModal.style.display = 'flex';
                    userVideo.play().catch(function(error) {
                        console.log('Error trying to play the video:', error);
                        playButton.style.display = 'block';
                    });

                    playButton.addEventListener('click', function() {
                        playButton.style.display = 'none';
                        userVideo.play();
                    });

                    userVideo.addEventListener('ended', closeVideoAndUpdate);
                }
                <?php endif; ?>


            function checkSessionStatus() {
                fetch('/Alumni-CvSU/user/check_session.php', {
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.logout) {
                        document.getElementById('logoutModal').style.display = 'flex';
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error checking session:', error);
                });
            }

            setInterval(checkSessionStatus, 30000);
            checkSessionStatus();

            let windowWidth = window.innerWidth;
            window.addEventListener('resize', function() {
                if (window.innerWidth !== windowWidth) {
                    windowWidth = window.innerWidth;
                    if (windowWidth > 768) {
                        sidebar.classList.remove('active');
                        overlay.classList.remove('active');
                        body.classList.remove('sidebar-open');
                    }
                }
            });
        });
    </script>
</body>
</html>