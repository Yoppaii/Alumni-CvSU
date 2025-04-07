<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin/portal/login-admin");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Master</title>
    <link rel="icon" href="asset/images/res1.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    :root {
        --primary-color: #10b981;
        --primary-hover: #059669;
        --primary-light: #d1fae5;
        --secondary-color: #64748b;
        --success-color: #22c55e;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --sidebar-width: 280px;
        --header-height: 70px;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    html {
        height: 100%;
        overflow-x: hidden;
    }

    body {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 0.875rem;
        line-height: 1.5;
        min-height: 100%;
        overflow-x: hidden;
    }

    .dashboard {
        display: flex;
        min-height: 100vh;
        position: relative;
    }

    .sidebar {
        width: var(--sidebar-width);
        background: var(--bg-primary);
        border-right: 1px solid #e2e8f0;
        position: fixed;
        height: 100vh;
        display: flex;
        flex-direction: column;
        z-index: 1000;
        transition: var(--transition);
        overflow: hidden;
    }

    @supports (-webkit-touch-callout: none) {
        .sidebar {
            height: -webkit-fill-available;
        }
    }

    .logo {
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 1.125rem;
        color: var(--primary-color);
        min-height: var(--header-height);
    }

    .logo img {
        width: 32px;
        height: 32px;
    }

    nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 0;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-light) transparent;
        -webkit-overflow-scrolling: touch;
    }

    nav::-webkit-scrollbar {
        width: 4px;
    }

    nav::-webkit-scrollbar-track {
        background: transparent;
    }

    nav::-webkit-scrollbar-thumb {
        background-color: var(--primary-light);
        border-radius: 20px;
    }

    .nav-section {
        margin-bottom: 0.5rem;
        padding: 0 1rem;
    }

    .nav-section-title {
        color: var(--text-secondary);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 0.5rem;
    }

    .nav-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: var(--radius-md);
        margin: 0.25rem 0;
        transition: var(--transition);
        cursor: pointer;
        user-select: none;
    }

    .nav-item i {
        width: 1.5rem;
        font-size: 1rem;
        margin-right: 0.75rem;
    }

    .nav-item:hover {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .nav-item.active {
        background-color: var(--primary-color);
        color: white;
    }

    .dropdown {
        margin-left: 2.25rem;
        display: none;
        opacity: 0;
        transition: var(--transition);
    }

    .dropdown.show {
        display: block;
        opacity: 1;
        animation: slideDown 0.3s ease forwards;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 0.625rem 1rem;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: var(--radius-md);
        transition: var(--transition);
        font-size: 0.813rem;
    }

    .dropdown-item i {
        width: 1.25rem;
        font-size: 0.875rem;
        margin-right: 0.75rem;
    }

    .dropdown-item:hover {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .top-header {
        position: fixed;
        right: 0;
        top: 0;
        left: var(--sidebar-width);
        height: var(--header-height);
        background: var(--bg-primary);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 2rem;
        border-bottom: 1px solid #e2e8f0;
        z-index: 990;
        transition: var(--transition);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .mobile-toggle {
        display: none;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: var(--transition);
    }

    .mobile-toggle:hover {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .header-search {
        position: relative;
        width: 300px;
    }

    .header-search input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-lg);
        background-color: var(--bg-secondary);
        transition: var(--transition);
    }

    .header-search i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }

    .header-search input:focus {
        outline: none;
        border-color: var(--primary-color);
        background-color: white;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .header-icon {
        position: relative;
        color: var(--text-secondary);
        cursor: pointer;
        transition: var(--transition);
    }

    .header-icon:hover {
        color: var(--primary-color);
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: var(--danger-color);
        color: white;
        font-size: 0.75rem;
        padding: 0.125rem 0.375rem;
        border-radius: 999px;
    }

    .profile-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--primary-light);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    .main-content {
        margin-left: var(--sidebar-width);
        padding: calc(var(--header-height) + 1rem) 1rem 1rem;
        min-height: 100vh;
        width: calc(100% - var(--sidebar-width));
        transition: var(--transition);
    }

    .overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 995;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .overlay.active {
        display: block;
        opacity: 1;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 1024px) {
        :root {
            --sidebar-width: 240px;
        }

        .header-search {
            width: 250px;
        }
    }

    @media (max-width: 768px) {
        body.sidebar-active {
            overflow: hidden;
        }

        .sidebar {
            transform: translateX(-100%);
            width: 100%;
            max-width: var(--sidebar-width);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .mobile-toggle {
            display: flex;
        }

        .top-header {
            left: 0;
            padding: 0 1rem;
        }

        .main-content {
            margin-left: 0;
            width: 100%;
            padding: calc(var(--header-height) + 1rem) 1rem 1rem;
        }

        .header-search {
            display: none;
        }

        nav {
            overscroll-behavior: contain;
        }
    }

    @media print {

        .sidebar,
        .top-header {
            display: none;
        }

        .main-content {
            margin: 0;
            padding: 0;
            width: 100%;
        }
    }

    .theme-toggle {
        background: none;
        border: none;
        padding: 0.5rem;
        color: var(--text-secondary);
        cursor: pointer;
        border-radius: var(--radius-md);
        transition: var(--transition);
    }

    .theme-toggle:hover {
        color: var(--primary-color);
        background-color: var(--primary-light);
    }

    .profile-dropdown {
        position: relative;
    }

    .profile-img {
        cursor: pointer;
    }

    .profile-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: var(--bg-primary);
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        width: 240px;
        display: none;
        z-index: 1000;
    }

    .profile-menu.active {
        display: block;
        animation: slideDown 0.2s ease-out;
    }

    .profile-header {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .user-name {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .user-email {
        display: block;
        font-size: 0.813rem;
        color: var(--text-secondary);
    }

    .profile-links {
        padding: 0.5rem 0;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: var(--text-primary);
        text-decoration: none;
        transition: var(--transition);
    }

    .menu-item i {
        width: 1.25rem;
        margin-right: 0.75rem;
        font-size: 0.938rem;
    }

    .menu-item:hover {
        background-color: var(--bg-secondary);
    }

    .text-danger {
        color: var(--danger-color) !important;
    }

    .text-danger:hover {
        background-color: #fef2f2 !important;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    [data-theme="dark"] {
        --primary-color: #10b981;
        --primary-hover: #059669;
        --primary-light: rgba(16, 185, 129, 0.2);
        --text-primary: #ffffff;
        --text-secondary: #ffffff;
        --bg-primary: #000000;
        --bg-secondary: #000000;
        --bg-light: #121212;
        --border-color: #2c2c2c;
        --container-border: #333333;
        --hover-bg: #1a1a1a;
    }

    [data-theme="dark"] * {
        color: #ffffff;
    }

    [data-theme="dark"] body {
        background-color: #000000;
        color: #ffffff;
    }

    [data-theme="dark"] .dashboard {
        background-color: #000000;
    }

    [data-theme="dark"] .sidebar {
        background: #000000;
        border-right: 1px solid var(--container-border);
    }

    [data-theme="dark"] .logo {
        border-bottom: 1px solid var(--border-color);
        background: #000000;
    }

    [data-theme="dark"] .logo span {
        color: #ffffff;
    }

    [data-theme="dark"] .nav-section-title {
        color: #ffffff;
    }

    [data-theme="dark"] .nav-item {
        color: #ffffff;
        border: 1px solid var(--border-color);
    }

    [data-theme="dark"] .nav-item:hover {
        background-color: var(--hover-bg);
        color: var(--primary-color);
        border-color: var(--container-border);
    }

    [data-theme="dark"] .nav-item.active {
        background-color: var(--primary-color);
        color: #000000;
        border-color: var(--primary-color);
    }

    [data-theme="dark"] .nav-item i {
        color: #ffffff;
    }

    [data-theme="dark"] .dropdown {
        background: #000000;
        border: 1px solid var(--border-color);
    }

    [data-theme="dark"] .dropdown-item {
        color: #ffffff;
        border: 1px solid transparent;
    }

    [data-theme="dark"] .dropdown-item:hover {
        background-color: var(--hover-bg);
        color: var(--primary-color);
        border-color: var(--container-border);
    }

    [data-theme="dark"] .top-header {
        background: #000000;
        border-bottom: 1px solid var(--border-color);
    }

    [data-theme="dark"] .mobile-toggle {
        color: #ffffff;
    }

    [data-theme="dark"] .mobile-toggle:hover {
        background-color: var(--hover-bg);
        color: var(--primary-color);
    }

    [data-theme="dark"] .header-search input {
        background-color: #121212;
        border: 1px solid var(--border-color);
        color: #ffffff;
    }

    [data-theme="dark"] .header-search input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    [data-theme="dark"] .header-search i {
        color: #ffffff;
    }

    [data-theme="dark"] .theme-toggle {
        color: #ffffff;
        border: 1px solid var(--border-color);
    }

    [data-theme="dark"] .theme-toggle:hover {
        background-color: var(--hover-bg);
        color: var(--primary-color);
        border-color: var(--container-border);
    }

    [data-theme="dark"] .header-icon {
        color: #ffffff;
    }

    [data-theme="dark"] .notification-badge {
        background-color: var(--primary-color);
        color: #000000;
        border: 1px solid var(--primary-color);
    }

    [data-theme="dark"] .profile-img {
        background-color: #121212;
        border: 2px solid var(--primary-color);
        color: #ffffff;
    }

    [data-theme="dark"] .profile-menu {
        background: #000000;
        border: 1px solid var(--container-border);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
    }

    [data-theme="dark"] .profile-header {
        border-bottom: 1px solid var(--border-color);
        background: #000000;
    }

    [data-theme="dark"] .profile-header .user-name,
    [data-theme="dark"] .profile-header .user-email {
        color: #ffffff;
    }

    [data-theme="dark"] .menu-item {
        color: #ffffff;
        border: 1px solid transparent;
    }

    [data-theme="dark"] .menu-item:hover {
        background-color: var(--hover-bg);
        border-color: var(--container-border);
    }

    [data-theme="dark"] .text-danger {
        color: #ef4444 !important;
    }

    [data-theme="dark"] .menu-item.text-danger:hover {
        background-color: rgba(239, 68, 68, 0.1);
    }

    [data-theme="dark"] .main-content {
        background-color: #000000;
    }

    [data-theme="dark"] table {
        border: 1px solid var(--border-color);
    }

    [data-theme="dark"] th {
        background-color: #121212;
        border: 1px solid var(--border-color);
        color: #ffffff;
    }

    [data-theme="dark"] td {
        border: 1px solid var(--border-color);
        color: #ffffff;
    }

    [data-theme="dark"] tr:hover td {
        background-color: var(--hover-bg);
    }

    [data-theme="dark"] input,
    [data-theme="dark"] select,
    [data-theme="dark"] textarea {
        background-color: #121212;
        border: 1px solid var(--border-color);
        color: #ffffff;
    }

    [data-theme="dark"] input:focus,
    [data-theme="dark"] select:focus,
    [data-theme="dark"] textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }

    [data-theme="dark"] .card,
    [data-theme="dark"] .container {
        background: #000000;
        border: 1px solid var(--border-color);
    }

    [data-theme="dark"] .btn {
        border: 1px solid var(--border-color);
    }

    [data-theme="dark"] .btn-primary {
        background-color: var(--primary-color);
        color: #000000;
        border-color: var(--primary-color);
    }

    [data-theme="dark"] .btn-secondary {
        background-color: #121212;
        color: #ffffff;
    }

    [data-theme="dark"] ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    [data-theme="dark"] ::-webkit-scrollbar-track {
        background: #000000;
    }

    [data-theme="dark"] ::-webkit-scrollbar-thumb {
        background-color: var(--border-color);
        border-radius: 4px;
        border: 2px solid #000000;
    }

    [data-theme="dark"] ::-webkit-scrollbar-thumb:hover {
        background-color: var(--container-border);
    }

    [data-theme="dark"] .overlay {
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px);
    }

    [data-theme="dark"] .modal {
        background-color: #000000;
        border: 1px solid var(--container-border);
    }

    [data-theme="dark"] .modal-header {
        border-bottom: 1px solid var(--border-color);
    }

    [data-theme="dark"] .modal-footer {
        border-top: 1px solid var(--border-color);
    }

    [data-theme="dark"] .fas,
    [data-theme="dark"] .far,
    [data-theme="dark"] .fa-solid {
        color: #ffffff;
    }

    [data-theme="dark"] .status-pending {
        background-color: #332200;
        color: #fbbf24;
        border: 1px solid #854d0e;
    }

    [data-theme="dark"] .status-approved {
        background-color: #132517;
        color: #4ade80;
        border: 1px solid #15803d;
    }

    [data-theme="dark"] .status-completed {
        background-color: #172554;
        color: #60a5fa;
        border: 1px solid #1d4ed8;
    }

    [data-theme="dark"] .status-cancelled {
        background-color: #2a1215;
        color: #f87171;
        border: 1px solid #dc2626;
    }

    [data-theme="dark"] *:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    [data-theme="dark"] .border {
        border-color: var(--border-color) !important;
    }

    [data-theme="dark"] .shadow {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5) !important;
    }

    [data-theme="dark"] .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    @media print {
        [data-theme="dark"] {
            --text-primary: #000000;
            --text-secondary: #4a5568;
            --bg-primary: #ffffff;
            --border-color: #e2e8f0;
        }
    }
</style>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo">
                <img src="asset/images/res1.png" alt="CvSU Logo">
                <span>ADMIN</span>
            </div>
            <nav>
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="?section=Dashboard" class="nav-item <?php echo (!isset($_GET['section']) || $_GET['section'] == 'Dashboard') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i>Dashboard
                    </a>
                    <a href="?section=Room-Reservation" class="nav-item <?php echo (!isset($_GET['section']) || $_GET['section'] == 'Room-Reservation') ? 'active' : ''; ?>">
                        <i class="fas fa-bolt"></i> Walk-in Booking
                    </a>

                </div>
                <div class="nav-section">
                    <!-- <a href="?section=user-live-chat" class="nav-item <?php echo (isset($_GET['section']) && $_GET['section'] == 'user-live-chat') ? 'active' : ''; ?>">
                        <i class="fas fa-comment"></i>Live Chat
                    </a> -->
                    <a href="?section=Alumni-analytics" class="nav-item <?php echo (isset($_GET['section']) && $_GET['section'] == 'Alumni-analytics') ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i>Alumni Tracer
                    </a>
                    <!-- <div class="dropdown">
                        <a href="?section=Alumni-analytics" class="dropdown-item">
                            <i class="fas fa-user"></i>Tracer Analytics
                        </a> -->
                    <!-- <a href="?section=Alumni-tracker" class="dropdown-item">
                            <i class="fas fa-user"></i>Total Response
                        </a> -->
                    <!-- <a href="?section=charts" class="dropdown-item">
                            <i class="fas fa-newspaper"></i>Chart
                        </a>
                        <a href="?section=questions" class="dropdown-item">
                            <i class="fas fa-calendar-day"></i>Question Ratings
                        </a> -->
                    <!-- </div> -->
                    <a href="#" class="nav-item has-dropdown">
                        <i class="fas fa-id-card"></i>Alumni ID
                    </a>
                    <div class="dropdown">
                        <a href="?section=view-alumni-id-cards" class="nav-item <?php echo (isset($_GET['section']) && $_GET['section'] == 'user-live-chat') ? 'active' : ''; ?>">
                            <i class="fas fa-id-badge"></i>Pending Alumni ID Cards Applications
                        </a>
                        <a href="?section=alumni-id" class="dropdown-item">
                            <i class="fas fa-plus-circle"></i>Add Alumni ID
                        </a>
                        <a href="?section=paid-alumni-id" class="dropdown-item">
                            <i class="fas fa-plus-circle"></i>Paid Alumni ID
                        </a>
                        <a href="?section=manage-alumni-id-cards" class="dropdown-item">
                            <i class="fas fa-cogs"></i>Alumni ID Management
                        </a>
                    </div>
                    <div class="nav-section-title">Management</div>
                    <a href="?section=view-all-bookings" class="nav-item <?php echo (isset($_GET['section']) && $_GET['section'] == 'view-all-bookings') ? 'active' : ''; ?>">
                        <i class="fas fa-bed"></i>All Bookings
                    </a>
                    <!-- <a href="#" class="nav-item has-dropdown">
                        <i class="fas fa-graduation-cap"></i>Alumni Records
                    </a>
                    <div class="dropdown">
                        <a href="?section=view-alumni" class="dropdown-item">
                            <i class="fas fa-list"></i>View All Alumni
                        </a>
                        <a href="?section=alumni-status" class="dropdown-item">
                            <i class="fas fa-chart-pie"></i>Status Report
                        </a>
                    </div> -->

                    <!-- <a href="#" class="nav-item has-dropdown">
                        <i class="fas fa-users"></i>Guest Records
                    </a>
                    <div class="dropdown">
                        <a href="?section=view-guests" class="dropdown-item">
                            <i class="fas fa-list-alt"></i>View All Guests
                        </a>
                        <a href="?section=add-guest" class="dropdown-item">
                            <i class="fas fa-user-plus"></i>Add Guest
                        </a>
                        <a href="?section=guest-status" class="dropdown-item">
                            <i class="fas fa-chart-bar"></i>Guest Analytics
                        </a>
                    </div> -->

                    <a href="#" class="nav-item has-dropdown">
                        <i class="fas fa-calendar-alt"></i>Bookings Managements
                    </a>
                    <div class="dropdown">
                        <!-- <a href="?section=all-bookings" class="dropdown-item">
                            <i class="fas fa-calendar-check"></i>All Bookings
                        </a> -->
                        <a href="?section=booking-room" class="dropdown-item">
                            <i class="fas fa-bed"></i>Price Room
                        </a>
                        <a href="?section=room-images" class="dropdown-item">
                            <i class="fas fa-camera"></i> Room Images
                        </a>
                        <!-- <a href="?section=new-booking" class="dropdown-item">
                            <i class="fas fa-plus-circle"></i>New Booking
                        </a>
                        <a href="?section=booking-calendar" class="dropdown-item">
                            <i class="fas fa-calendar-week"></i>Calendar View -->
                        <!-- </a> -->
                    </div>
                    <a href="#" class="nav-item has-dropdown">
                        <i class="fas fa-globe"></i>Website Managements
                    </a>
                    <div class="dropdown">
                        <a href="?section=Latest-Announcements" class="dropdown-item">
                            <i class="fas fa-bullhorn"></i>Announcement
                        </a>
                        <a href="?section=Latest-News-and-Features" class="dropdown-item">
                            <i class="fas fa-newspaper"></i>News / Features
                        </a>
                        <a href="?section=CvSU-Events" class="dropdown-item">
                            <i class="fas fa-calendar-day"></i>Events
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <!-- <a href="#" class="nav-item has-dropdown">
                        <i class="fas fa-user-shield"></i>System User Management
                    </a>
                    <div class="dropdown">
                        <a href="?section=all-users" class="dropdown-item">
                            <i class="fas fa-users-cog"></i>All Users
                        </a>
                        <a href="?section=user-roles" class="dropdown-item">
                            <i class="fas fa-user-tag"></i>Roles & Permissions
                        </a>
                    </div> -->
                    <a href="?section=all-users-bookings" class="dropdown-item">
                        <i class="fas fa-users-cog"></i>All Users
                    </a>
                    <!-- <a href="#" class="nav-item has-dropdown">
                        <i class="fas fa-user-shield"></i>User Management
                    </a>
                    <div class="dropdown">
                        <a href="?section=all-users-bookings" class="dropdown-item">
                            <i class="fas fa-users-cog"></i>All Users
                        </a>
                    </div> -->

                    <!-- <a href="#" class="nav-item has-dropdown">
                        <i class="fas fa-cog"></i>Settings
                    </a>
                    <div class="dropdown">
                        <a href="?section=user-profile" class="dropdown-item">
                            <i class="fas fa-user-circle"></i>My Profile
                        </a>
                        <a href="?section=system-settings" class="dropdown-item">
                            <i class="fas fa-sliders-h"></i>System Settings
                        </a>
                        <a href="?section=backup" class="dropdown-item">
                            <i class="fas fa-database"></i>Backup & Restore
                        </a>
                    </div> -->
                </div>
            </nav>
        </aside>

        <div class="overlay"></div>

        <header class="top-header">
            <div class="header-left">
                <div class="mobile-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>

            <div class="header-right">
                <button class="theme-toggle" aria-label="Toggle theme">
                    <i class="fas fa-sun"></i>
                </button>
                <div class="header-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="profile-dropdown">
                    <div class="profile-img" onclick="toggleProfileMenu()">
                        <?php
                        $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
                        echo substr($name, 0, 2);
                        ?>
                    </div>
                    <div class="profile-menu" id="profileMenu">
                        <div class="profile-header">
                            <span class="user-name"><?php echo htmlspecialchars($name); ?></span>
                            <span class="user-email"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                        </div>
                        <div class="profile-links">
                            <a href="?section=user-profile" class="menu-item">
                                <i class="fas fa-user-circle"></i> Profile Settings
                            </a>
                            <a href="admin/portal/admin-function/logout.php" class="menu-item text-danger">
                                <i class="fas fa-sign-out-alt"></i> Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="main-content">
            <?php
            $section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
            switch ($section) {
                case 'Dashboard':
                    include 'admin/home.php';
                    break;
                case 'Check-Available-Rooms':
                    include 'admin/check-available-rooms.php';
                    break;
                case 'Room-Reservation':
                    include 'admin/walk-in-booking.php';
                    break;
                case 'room-details':
                    include 'admin/walk-in-room-details.php';
                    break;
                case 'Alumni-analytics':
                    include 'admin/alumni-tracer/alumni-analytics.php';
                    break;
                case 'Alumni-tracker':
                    include 'admin/alumni-tracer/alumni-tracker.php';
                    break;
                case 'alumni-charts':
                    include 'admin/alumni-tracer/alumni-charts.php';
                    break;
                case 'questions':
                    include 'admin/alumni-tracer/question-response.php';
                    break;
                case 'all-response':
                    include 'admin/alumni-tracer/all-response.php';
                    break;
                case 'gender':
                    include 'admin/alumni-tracer/gender.php';
                    break;
                case 'charts':
                    include 'admin/alumni-tracer/charts.php';
                    break;
                case 'view-analytics':
                    include 'admin/alumni-tracer/view_analytics.php';
                    break;
                case 'view-sex':
                    include 'admin/alumni-tracer/view/view-sex.php';
                    break;
                case 'view-location':
                    include 'admin/alumni-tracer/view/view-location.php';
                    break;
                case 'view-campus':
                    include 'admin/alumni-tracer/view/view-campus.php';
                    break;
                case 'view-course':
                    include 'admin/alumni-tracer/view/view-course.php';
                    break;
                case 'view-civil-status':
                    include 'admin/alumni-tracer/view/view-course.php';
                    break;
                case 'view-degree':
                    include 'admin/alumni-tracer/view/view-degree.php';
                    break;
                case 'view-college':
                    include 'admin/alumni-tracer/view/view-college.php';
                    break;
                case 'view-year-grad':
                    include 'admin/alumni-tracer/view/view-year.php';
                    break;
                case 'view-honors':
                    include 'admin/alumni-tracer/view/view-honors.php';
                    break;
                case 'view-exams':
                    include 'admin/alumni-tracer/view/view-exam.php';
                    break;
                case 'view-educ-level':
                    include 'admin/alumni-tracer/view/view-educ.php';
                    break;
                case 'view-course-reason':
                    include 'admin/alumni-tracer/view/view-reason.php';
                    break;
                case 'view-all-bookings':
                    include 'admin/view-all-bookings.php';
                    break;
                case 'Latest-Announcements':
                    include 'admin/website-managements/annoucements.php';
                    break;
                case 'CvSU-Events':
                    include 'admin/website-managements/events.php';
                    break;
                case 'Latest-News-and-Features':
                    include 'admin/website-managements/news.php';
                    break;
                case 'view-alumni-cards':
                    include 'admin/alumni/alumni-view-all.php';
                    break;
                case 'view-alumni-id-cards':
                    include 'admin/alumni-view-id.php';
                    break;
                case 'manage-alumni-id-cards':
                    include 'admin/alumni/alumni-id-managements.php';
                    break;
                case 'paid-alumni-id':
                    include 'admin/alumni/paid-user.php';
                    break;
                case 'update-alumni-status':
                    include 'admin/update-status.php';
                    break;
                case 'add-alumni':
                    include 'admin/alumni/alumni-add.php';
                    break;
                case 'alumni-id':
                    include 'admin/alumni/alumni-id-card.php';
                    break;
                case 'alumni-status':
                    include 'admin/alumni/alumni-status.php';
                    break;
                case 'view-guests':
                    include 'admin/guests/guests-view-all.php';
                    break;
                case 'add-guest':
                    include 'admin/guests/guests-add.php';
                    break;
                case 'guest-status':
                    include 'admin/guests/guests-status.php';
                    break;
                case 'all-bookings':
                    include 'admin/bookings/bookings-all.php';
                    break;
                case 'booking-room':
                    include 'admin/bookings/booking-room.php';
                    break;
                case 'new-booking':
                    include 'admin/bookings/bookings-new.php';
                    break;
                case 'booking-calendar':
                    include 'admin/bookings/bookings-calendar.php';
                    break;
                case 'room-images':
                    include 'admin/bookings/room-images.php';
                    break;
                case 'booking-status':
                    include 'admin/bookings/bookings-status.php';
                    break;
                case 'all-users':
                    include 'admin/users/users-all.php';
                    break;
                case 'user-roles':
                    include 'admin/users/user-roles.php';
                    break;
                case 'user-live-chat':
                    include 'admin/users/user-live-chat.php';
                    break;
                case 'all-users-bookings':
                    include 'admin/users/all-user.php';
                    break;
                case 'user-profile':
                    include 'admin/profile/view.php';
                    break;
                case 'system-settings':
                    include 'admin/settings/system.php';
                    break;
                case 'backup':
                    include 'admin/settings/backup.php';
                    break;
                case 'get_booking_trends':
                    include 'admin/analytics/get_booking_trends.php';
                    break;
                default:
                    include 'sections/404.php';
                    break;
            }
            ?>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.querySelector('.mobile-toggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            const body = document.body;
            const navItems = document.querySelectorAll('.nav-item');
            const themeToggle = document.querySelector('.theme-toggle');
            const themeIcon = themeToggle.querySelector('i');
            const searchInput = document.querySelector('.header-search input');
            const notificationIcon = document.querySelector('.header-icon .fa-bell');

            class ThemeManager {
                constructor() {
                    this.theme = localStorage.getItem('theme') || 'light';
                    this.init();
                }

                init() {
                    this.applyTheme();
                    this.bindEvents();
                }

                applyTheme() {
                    document.documentElement.setAttribute('data-theme', this.theme);
                    this.updateThemeIcon();
                }

                toggleTheme() {
                    this.theme = this.theme === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('theme', this.theme);
                    this.applyTheme();
                }

                updateThemeIcon() {
                    themeIcon.className = this.theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
                }

                bindEvents() {
                    themeToggle.addEventListener('click', () => this.toggleTheme());
                }
            }

            class SidebarManager {
                constructor() {
                    this.isMobile = window.innerWidth <= 768;
                    this.init();
                }

                init() {
                    this.bindEvents();
                    this.handleResize();
                    this.setActiveNavFromURL();
                }

                toggleSidebar() {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                    body.classList.toggle('sidebar-active');
                }

                closeSidebar() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    body.classList.remove('sidebar-active');
                }

                handleResize() {
                    const newIsMobile = window.innerWidth <= 768;
                    if (this.isMobile !== newIsMobile) {
                        this.isMobile = newIsMobile;
                        this.closeSidebar();
                    }
                }

                handleDropdowns(clickedItem) {
                    const wasActive = clickedItem.classList.contains('active');

                    navItems.forEach(item => {
                        item.classList.remove('active');
                        const dropdown = item.nextElementSibling;
                        if (dropdown?.classList.contains('dropdown')) {
                            dropdown.classList.remove('show');
                        }
                    });

                    if (!wasActive && clickedItem.classList.contains('has-dropdown')) {
                        clickedItem.classList.add('active');
                        const dropdown = clickedItem.nextElementSibling;
                        if (dropdown?.classList.contains('dropdown')) {
                            dropdown.classList.add('show');
                        }
                    }
                }

                setActiveNavFromURL() {
                    const currentPath = window.location.search;
                    if (!currentPath) return;

                    const activeNav = document.querySelector(`a[href="${currentPath}"]`);
                    if (!activeNav) return;

                    navItems.forEach(item => item.classList.remove('active'));
                    activeNav.classList.add('active');

                    if (activeNav.classList.contains('dropdown-item')) {
                        const parentDropdown = activeNav.closest('.dropdown');
                        const parentNav = parentDropdown?.previousElementSibling;
                        if (parentNav) {
                            parentNav.classList.add('active');
                            parentDropdown.classList.add('show');
                        }
                    }
                }

                bindEvents() {
                    mobileToggle.addEventListener('click', () => this.toggleSidebar());
                    overlay.addEventListener('click', () => this.closeSidebar());

                    window.addEventListener('resize', () => {
                        requestAnimationFrame(() => this.handleResize());
                    });

                    navItems.forEach(item => {
                        if (item.classList.contains('has-dropdown')) {
                            item.addEventListener('click', (e) => {
                                e.preventDefault();
                                this.handleDropdowns(item);
                            });
                        }
                    });
                }
            }

            class SearchManager {
                constructor() {
                    this.init();
                }

                init() {
                    this.bindEvents();
                }

                handleSearch(query) {
                    console.log('Searching for:', query);
                }

                bindEvents() {
                    searchInput?.addEventListener('input', (e) => {
                        const query = e.target.value.trim();
                        this.handleSearch(query);
                    });

                    searchInput?.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            const query = e.target.value.trim();
                            console.log('Search submitted:', query);
                        }
                    });
                }
            }

            class NotificationManager {
                constructor() {
                    this.init();
                }

                init() {
                    this.bindEvents();
                }

                handleNotificationClick() {
                    console.log('Notifications clicked');
                }

                updateNotificationCount(count) {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'block' : 'none';
                    }
                }

                bindEvents() {
                    notificationIcon?.addEventListener('click', () => {
                        this.handleNotificationClick();
                    });
                }
            }

            class ProfileManager {
                constructor() {
                    this.init();
                }

                init() {
                    this.bindEvents();
                }

                handleProfileClick() {
                    console.log('Profile clicked');
                }

                bindEvents() {
                    const profileImg = document.querySelector('.profile-img');
                    profileImg?.addEventListener('click', () => {
                        this.handleProfileClick();
                    });
                }
            }

            const managers = {
                theme: new ThemeManager(),
                sidebar: new SidebarManager(),
                search: new SearchManager(),
                notifications: new NotificationManager(),
                profile: new ProfileManager()
            };

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                    managers.sidebar.closeSidebar();
                }
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.nav-item') && !e.target.closest('.dropdown')) {
                    navItems.forEach(item => {
                        item.classList.remove('active');
                        const dropdown = item.nextElementSibling;
                        if (dropdown?.classList.contains('dropdown')) {
                            dropdown.classList.remove('show');
                        }
                    });
                }
            });
        });

        function toggleProfileMenu() {
            const profileMenu = document.getElementById('profileMenu');
            profileMenu.classList.toggle('active');
            document.addEventListener('click', function closeMenu(e) {
                const profile = document.querySelector('.profile-dropdown');
                if (!profile.contains(e.target)) {
                    profileMenu.classList.remove('active');
                    document.removeEventListener('click', closeMenu);
                }
            });
        }

        function loadMessages(chatId) {
            if (chatId) {
                let currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('chat_id', chatId);

                window.location.href = currentUrl.toString();
            }
        }
    </script>
</body>

</html>