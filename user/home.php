<?php
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$recentBooking = null;
$recentCancelled = null;
$recentAlumniCard = null;
$recentCancelledAlumni = null;
$recentPasswordChange = null;

if ($userId) {
    $active_sql = "SELECT 
                    'active' as booking_type,
                    id,
                    user_id,
                    room_number,
                    occupancy,
                    price,
                    arrival_date,
                    arrival_time,
                    departure_date,
                    departure_time,
                    status,
                    created_at
                FROM bookings 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1";

    $cancelled_sql = "SELECT 
                        'cancelled' as booking_type,
                        id,
                        user_id,
                        room_number,
                        occupancy,
                        price,
                        arrival_date,
                        arrival_time,
                        departure_date,
                        departure_time,
                        cancellation_reason,
                        cancelled_at,
                        reference_number
                    FROM cancelled_bookings 
                    WHERE user_id = ? 
                    ORDER BY cancelled_at DESC 
                    LIMIT 1";

    $alumni_sql = "SELECT 
                    id,
                    user_id,
                    last_name,
                    first_name,
                    middle_name,
                    email,
                    course,
                    year_graduated,
                    highschool_graduated,
                    membership_type,
                    status,
                    created_at
                FROM alumni_id_cards 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1";

    $cancelled_alumni_sql = "SELECT 
                            id,
                            original_id,
                            user_id,
                            last_name,
                            first_name,
                            middle_name,
                            email,
                            course,
                            year_graduated,
                            highschool_graduated,
                            membership_type,
                            status,
                            cancellation_reason,
                            cancelled_at,
                            original_created_at
                        FROM cancelled_alumni_applications 
                        WHERE user_id = ? 
                        ORDER BY cancelled_at DESC 
                        LIMIT 1";

    $password_sql = "SELECT 
                        id,
                        user_id,
                        change_date,
                        action
                    FROM password_history 
                    WHERE user_id = ? 
                    ORDER BY change_date DESC 
                    LIMIT 1";

    if (!$mysqli) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $active_stmt = $mysqli->prepare($active_sql);
    if (!$active_stmt) {
        die("Prepare failed (active bookings): " . $mysqli->error);
    }

    $active_stmt->bind_param("i", $userId);
    $active_stmt->execute();
    $active_result = $active_stmt->get_result();
    $recentBooking = $active_result->fetch_assoc();
    $active_stmt->close();

    $cancelled_stmt = $mysqli->prepare($cancelled_sql);
    if (!$cancelled_stmt) {
        die("Prepare failed (cancelled bookings): " . $mysqli->error);
    }

    $cancelled_stmt->bind_param("i", $userId);
    $cancelled_stmt->execute();
    $cancelled_result = $cancelled_stmt->get_result();
    $recentCancelled = $cancelled_result->fetch_assoc();
    $cancelled_stmt->close();

    $alumni_stmt = $mysqli->prepare($alumni_sql);
    if (!$alumni_stmt) {
        die("Prepare failed (alumni cards): " . $mysqli->error);
    }

    $alumni_stmt->bind_param("i", $userId);
    $alumni_stmt->execute();
    $alumni_result = $alumni_stmt->get_result();
    $recentAlumniCard = $alumni_result->fetch_assoc();
    $alumni_stmt->close();

    $cancelled_alumni_stmt = $mysqli->prepare($cancelled_alumni_sql);
    if (!$cancelled_alumni_stmt) {
        die("Prepare failed (cancelled alumni): " . $mysqli->error);
    }

    $cancelled_alumni_stmt->bind_param("i", $userId);
    $cancelled_alumni_stmt->execute();
    $cancelled_alumni_result = $cancelled_alumni_stmt->get_result();
    $recentCancelledAlumni = $cancelled_alumni_result->fetch_assoc();
    $cancelled_alumni_stmt->close();

    $password_stmt = $mysqli->prepare($password_sql);
    if (!$password_stmt) {
        die("Prepare failed (password history): " . $mysqli->error);
    }

    $password_stmt->bind_param("i", $userId);
    $password_stmt->execute();
    $password_result = $password_stmt->get_result();
    $recentPasswordChange = $password_result->fetch_assoc();
    $password_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Recent Activities</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    :root {
        --primary-color: #2d6936;
        --secondary-color: #1e40af;
        --background-color: #f4f6f8;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --text-primary: #1f2937;
        --text-secondary: #4b5563;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
    }

    body {
        background: var(--background-color);
        min-height: 100vh;
        padding: 10px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .home-welcome-section {
        padding: 40px 24px;
        text-align: left;
        background: linear-gradient(to right, #ffffff, #f8f9fa);
        border-radius: 12px;
        box-shadow: var(--shadow-md);
        margin-bottom: 30px;
    }

    .home-welcome-section h1 {
        color: var(--primary-color);
        margin-bottom: 16px;
        font-size: 32px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .home-welcome-section p {
        color: var(--text-secondary);
        margin-bottom: 32px;
        font-size: 18px;
        line-height: 1.6;
        max-width: 600px;
    }

    .home-slider {
        position: relative;
        width: 100%;
        height: 400px;
        overflow: hidden;
        border-radius: 12px;
        margin: 20px 0;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        padding: 0;
    }

    .home-slides {
        display: flex;
        transition: transform 0.5s ease-in-out;
        height: 100%;
    }

    .home-slide {
        position: relative;
        height: 100%;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .home-slides {
            width: 400%;
        }

        .home-slide {
            width: 12.5%;
            padding: 0 8px;
            box-sizing: border-box;
        }
    }

    @media (max-width: 767px) {
        .home-slider {
            height: 300px;
        }

        .home-slides {
            width: 800%;
        }

        .home-slide {
            width: 12.5%;
            padding: 0;
        }
    }

    .home-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .home-room-number {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: rgba(45, 105, 54, 0.9);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 16px;
        backdrop-filter: blur(4px);
    }

    .home-nav-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(45, 105, 54, 0.8);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        cursor: pointer;
        font-size: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .home-nav-button:hover {
        background: var(--primary-color);
        transform: translateY(-50%) scale(1.1);
    }

    .home-prev {
        left: 20px;
    }

    .home-next {
        right: 20px;
    }

    .home-activity-card {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .home-activity-header {
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
        background: linear-gradient(to right, #f9fafb, #ffffff);
    }

    .home-activity-header h1 {
        font-size: 24px;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .home-activity-header h1 i {
        color: var(--primary-color);
    }

    .home-activity-content {
        padding: 24px;
    }

    .home-activity-section {
        margin-bottom: 32px;
    }

    .home-section-title {
        font-size: 18px;
        color: var(--text-primary);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--primary-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .home-section-title i {
        color: var(--primary-color);
    }

    .home-activity-item {
        display: flex;
        align-items: flex-start;
        padding: 20px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        margin-bottom: 16px;
        transition: all 0.3s ease;
        background: white;
    }

    .home-activity-item:hover {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .home-activity-icon {
        flex-shrink: 0;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #ecfdf5;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
        margin-right: 20px;
    }

    .home-activity-item:hover .home-activity-icon {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .home-activity-details {
        flex-grow: 1;
    }

    .home-activity-title {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .home-activity-info {
        color: var(--text-secondary);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 12px;
    }

    .home-activity-timestamp {
        color: var(--text-muted);
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .home-activity-timestamp i {
        font-size: 12px;
    }

    .tag {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        margin-left: 8px;
    }

    .tag-success {
        background: #ecfdf5;
        color: #065f46;
    }

    .tag-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .no-data {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }

    .no-data i {
        font-size: 48px;
        color: #d1d5db;
        margin-bottom: 16px;
        display: block;
    }

    @media print {

        .home-slider,
        .home-nav-button {
            display: none;
        }

        .home-activity-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .home-activity-item {
            break-inside: avoid;
        }
    }
</style>

<body>
    <div class="container">
        <div class="home-welcome-section">
            <h1>Welcome to Your Dashboard, <?php echo htmlspecialchars($userName); ?>!</h1>
            <p>Your one-stop solution for managing room reservations. Explore the options below to get started.</p>

            <div class="home-slider">
                <div class="home-slides">
                    <div class="home-slide">
                        <img src="asset/Room1/1.jpg" alt="Room 1">
                        <div class="home-room-number">Room 1</div>
                    </div>
                    <div class="home-slide">
                        <img src="asset/Room2/6314362091837965302.jpg" alt="Room 2">
                        <div class="home-room-number">Room 2</div>
                    </div>
                    <div class="home-slide">
                        <img src="asset/Room3/6314362091837965305.jpg" alt="Room 3">
                        <div class="home-room-number">Room 3</div>
                    </div>
                    <div class="home-slide">
                        <img src="asset/Room4/6314362091837965308.jpg" alt="Room 4">
                        <div class="home-room-number">Room 4</div>
                    </div>
                    <div class="home-slide">
                        <img src="asset/Room5/6314362091837965311.jpg" alt="Room 5">
                        <div class="home-room-number">Room 5</div>
                    </div>
                    <div class="home-slide">
                        <img src="asset/Room6/6314362091837965314.jpg" alt="Room 6">
                        <div class="home-room-number">Room 6</div>
                    </div>
                    <div class="home-slide">
                        <img src="asset/Room7/6314362091837965317.jpg" alt="Room 7">
                        <div class="home-room-number">Room 7</div>
                    </div>
                    <div class="home-slide">
                        <img src="asset/Room8/6314362091837965322.jpg" alt="Room 8">
                        <div class="home-room-number">Room 8</div>
                    </div>
                </div>
                <button class="home-nav-button home-prev" onclick="changeSlide(-1)">&#10094;</button>
                <button class="home-nav-button home-next" onclick="changeSlide(1)">&#10095;</button>
            </div>
        </div>

        <div class="home-activity-card">
            <div class="home-activity-header">
                <h1><i class="fas fa-history"></i> Recent Activities</h1>
            </div>

            <div class="home-activity-content">
                <div class="home-activity-section">
                    <h2 class="home-section-title">
                        <i class="fas fa-hotel"></i> Recent Bookings
                    </h2>
                    <?php if ($recentBooking): ?>
                        <div class="home-activity-item">
                            <div class="home-activity-icon">
                                <i class="fas fa-bed"></i>
                            </div>
                            <div class="home-activity-details">
                                <div class="home-activity-title">Active Booking - Room <?php echo htmlspecialchars($recentBooking['room_number']); ?></div>
                                <div class="home-activity-info">
                                    Arrival: <?php echo htmlspecialchars($recentBooking['arrival_date']); ?> at <?php echo htmlspecialchars($recentBooking['arrival_time']); ?><br>
                                    Departure: <?php echo htmlspecialchars($recentBooking['departure_date']); ?> at <?php echo htmlspecialchars($recentBooking['departure_time']); ?><br>
                                    Occupancy: <?php echo htmlspecialchars($recentBooking['occupancy']); ?> | Price: ₱<?php echo htmlspecialchars($recentBooking['price']); ?>
                                </div>
                                <div class="home-activity-timestamp">
                                    <i class="far fa-clock"></i>
                                    Booked on <?php echo date('M j, Y g:i A', strtotime($recentBooking['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-bed"></i>
                            <p>No active bookings found</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($recentCancelled): ?>
                    <div class="home-activity-section">
                        <h2 class="home-section-title">
                            <i class="fas fa-ban"></i> Recent Cancelled Bookings
                        </h2>
                        <div class="home-activity-item">
                            <div class="home-activity-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="home-activity-details">
                                <div class="home-activity-title">Cancelled Booking - Reference #<?php echo htmlspecialchars($recentCancelled['reference_number']); ?></div>
                                <div class="home-activity-info">
                                    Room: <?php echo htmlspecialchars($recentCancelled['room_number']); ?><br>
                                    Reason: <?php echo htmlspecialchars($recentCancelled['cancellation_reason']); ?>
                                </div>
                                <div class="home-activity-timestamp">
                                    <i class="far fa-clock"></i>
                                    Cancelled on <?php echo date('M j, Y g:i A', strtotime($recentCancelled['cancelled_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($recentAlumniCard): ?>
                    <div class="home-activity-section">
                        <h2 class="home-section-title">
                            <i class="fas fa-id-card"></i> Alumni ID Card
                        </h2>
                        <div class="home-activity-item">
                            <div class="home-activity-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="home-activity-details">
                                <div class="home-activity-title">Alumni ID Application</div>
                                <div class="home-activity-info">
                                    Name: <?php echo htmlspecialchars($recentAlumniCard['last_name'] . ', ' . $recentAlumniCard['first_name']); ?><br>
                                    Course: <?php echo htmlspecialchars($recentAlumniCard['course']); ?><br>
                                    Year Graduated: <?php echo htmlspecialchars($recentAlumniCard['year_graduated']); ?><br>
                                    Membership Type: <?php echo htmlspecialchars($recentAlumniCard['membership_type']); ?>
                                </div>
                                <div class="home-activity-timestamp">
                                    <i class="far fa-clock"></i>
                                    Applied on <?php echo date('M j, Y g:i A', strtotime($recentAlumniCard['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($recentPasswordChange): ?>
                    <div class="home-activity-section">
                        <h2 class="home-section-title">
                            <i class="fas fa-key"></i> Recent Security Activity
                        </h2>
                        <div class="home-activity-item">
                            <div class="home-activity-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="home-activity-details">
                                <div class="home-activity-title">Password Change</div>
                                <div class="home-activity-info">
                                    Action: <?php echo htmlspecialchars($recentPasswordChange['action']); ?>
                                </div>
                                <div class="home-activity-timestamp">
                                    <i class="far fa-clock"></i>
                                    Changed on <?php echo date('M j, Y g:i A', strtotime($recentPasswordChange['change_date'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentSlide = 0;
                const slides = document.querySelector('.home-slides');
                const totalSlides = document.querySelectorAll('.home-slide').length;

                window.changeSlide = function(direction) {
                    const slidesPerView = window.innerWidth >= 768 ? 2 : 1;
                    const maxSlides = Math.ceil(totalSlides / slidesPerView);

                    currentSlide = (currentSlide + direction + maxSlides) % maxSlides;

                    const slideWidth = (100 / totalSlides) * slidesPerView;
                    const translateX = -(currentSlide * slideWidth);
                    slides.style.transform = `translateX(${translateX}%)`;

                    document.querySelector('.home-prev').style.opacity = currentSlide === 0 ? '0.5' : '1';
                    document.querySelector('.home-next').style.opacity =
                        currentSlide === maxSlides - 1 ? '0.5' : '1';
                };

                let slideInterval = setInterval(() => {
                    const slidesPerView = window.innerWidth >= 768 ? 2 : 1;
                    const maxSlides = Math.ceil(totalSlides / slidesPerView);
                    currentSlide = (currentSlide + 1) % maxSlides;
                    changeSlide(0);
                }, 5000);

                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        currentSlide = 0;
                        slides.style.transform = 'translateX(0)';
                        changeSlide(0);
                    }, 250);
                });

                changeSlide(0);
            });
        </script>
    </div>
</body>

</html>