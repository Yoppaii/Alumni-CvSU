<?php
if (!isset($_SESSION)) {
    session_start();
}

require 'main_db.php';

$book_rooms = [
    [
        'id' => 1,
        'name' => 'Room 1',
        'max_occupancy' => 4,
        'description' => 'A spacious and comfortable room perfect for families or small groups. Featuring modern amenities and stylish furnishings.',
        'amenities' => ['Free Wi-Fi', 'Smart TV', 'Air Conditioning', 'Coffee Maker', 'Private Bathroom', 'Room Safe', 'Mini Fridge', 'Work Desk']
    ],
    [
        'id' => 2,
        'name' => 'Room 2',
        'max_occupancy' => 4,
        'description' => 'Contemporary design meets comfort in this well-appointed room. Perfect for both business and leisure travelers.',
        'amenities' => ['Free Wi-Fi', 'Smart TV', 'Air Conditioning', 'Coffee Maker', 'Private Bathroom', 'Room Safe', 'Mini Fridge', 'Work Desk']
    ],
    [
        'id' => 3,
        'name' => 'Room 3',
        'max_occupancy' => 2,
        'description' => 'Cozy and intimate room perfect for solo travelers or couples. Modern amenities in a compact space.',
        'amenities' => ['Free Wi-Fi', 'Smart TV', 'Air Conditioning', 'Coffee Maker', 'Private Bathroom', 'Room Safe']
    ],
    [
        'id' => 4,
        'name' => 'Room 4',
        'max_occupancy' => 2,
        'description' => 'Elegant room with a view, perfect for a romantic getaway or business stay.',
        'amenities' => ['Free Wi-Fi', 'Smart TV', 'Air Conditioning', 'Coffee Maker', 'Private Bathroom', 'Room Safe']
    ],
    [
        'id' => 5,
        'name' => 'Room 5',
        'max_occupancy' => 6,
        'description' => 'Our largest suite, perfect for families or groups. Spacious living area with premium amenities.',
        'amenities' => ['Free Wi-Fi', 'Smart TV', 'Air Conditioning', 'Kitchen', 'Private Bathroom', 'Room Safe', 'Mini Fridge', 'Work Desk', 'Lounge Area']
    ],
    [
        'id' => 6,
        'name' => 'Room 6',
        'max_occupancy' => 4,
        'description' => 'Modern and well-equipped room suitable for families or small groups. Comfortable space with all essential amenities.',
        'amenities' => ['Free Wi-Fi', 'Smart TV', 'Air Conditioning', 'Coffee Maker', 'Private Bathroom', 'Room Safe', 'Mini Fridge', 'Work Desk']
    ],
    [
        'id' => 7,
        'name' => 'Room 7',
        'max_occupancy' => 3,
        'description' => 'Stylish mid-sized room perfect for small families or friends. Balance of comfort and functionality.',
        'amenities' => ['Free Wi-Fi', 'Smart TV', 'Air Conditioning', 'Coffee Maker', 'Private Bathroom', 'Room Safe', 'Mini Fridge']
    ],
    [
        'id' => 8,
        'name' => 'Room 8',
        'max_occupancy' => 2,
        'description' => 'Cozy and efficient room designed for couples or solo travelers. Modern amenities in an intimate setting.',
        'amenities' => ['Free Wi-Fi', 'Smart TV', 'Air Conditioning', 'Coffee Maker', 'Private Bathroom', 'Room Safe']
    ],
    [
        'id' => 9,
        'name' => 'Board Room',
        'max_occupancy' => 20,
        'description' => 'Professional meeting space equipped with modern presentation technology. Perfect for corporate meetings and executive gatherings.',
        'amenities' => ['Free Wi-Fi', 'Projector', 'Video Conference System', 'White Board', 'Air Conditioning', 'Coffee Service', 'Water Service', 'Conference Phone']
    ],
    [
        'id' => 10,
        'name' => 'Conference Room',
        'max_occupancy' => 50,
        'description' => 'Large-scale meeting venue with state-of-the-art audio/visual equipment. Ideal for conferences, seminars, and large corporate events.',
        'amenities' => ['Free Wi-Fi', 'Dual Projectors', 'Sound System', 'Video Conference System', 'White Board', 'Air Conditioning', 'Coffee Service', 'Water Service', 'Conference Phone', 'Stage Area']
    ],
    [
        'id' => 11,
        'name' => 'Lobby',
        'max_occupancy' => 100,
        'description' => 'Spacious and elegant lobby area perfect for receptions, networking events, and social gatherings. Features modern décor and flexible layout options.',
        'amenities' => ['Free Wi-Fi', 'Reception Desk', 'Lounge Seating', 'Air Conditioning', 'Coffee Service', 'Water Service', 'Background Music System', 'Display Screens', 'Security Service']
    ],
    [
        'id' => 12,
        'name' => 'Building',
        'max_occupancy' => 500,
        'description' => 'Entire building rental for large-scale events, corporate functions, or private celebrations. Full access to all facilities and amenities.',
        'amenities' => ['All Rooms Access', 'Free Wi-Fi', 'Full Security Service', 'Parking Area', 'Reception Services', 'Cleaning Services', 'All Utilities Included', 'Event Planning Support', 'Technical Support']
    ]
];

$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Find selected room
$room = null;
foreach ($book_rooms as $r) {
    if ($r['id'] === $room_id) {
        $room = $r;
        break;
    }
}

// Redirect if room not found
if (!$room) {
    header('Location: index.php');
    exit;
}

// Determine correct pricing table
$room_name = $room['name'];
$pricing_table = match ($room_name) {
    'Lobby' => 'lobby_pricing',
    'Conference Room' => 'conference_pricing',
    'Board Room' => 'board_pricing',
    'Building' => 'building_pricing',
    default => 'room_price'
};

// Fetch occupancy and price options
$occupancies = [];
$query = "SELECT DISTINCT occupancy, price FROM {$pricing_table} ORDER BY occupancy ASC";
if ($result = $mysqli->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $occupancies[] = $row;
    }
}

// Extract max occupancy and price
$maxOccupancy = null;
$maxPrice = null;

if (!empty($occupancies)) {
    $maxOccupancy = max(array_column($occupancies, 'occupancy'));
    foreach ($occupancies as $occ) {
        if ($occ['occupancy'] == $maxOccupancy) {
            $maxPrice = $occ['price'];
            break;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['name']); ?> Details - Room Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/min/tiny-slider.js"></script>
</head>
<style>
    :root {
        --primary-color: #2d6936;
        --secondary-color: #1e40af;
        --background-color: #f4f6f8;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .room-details-body {
        background: var(--background-color);
        min-height: 100vh;
        padding: 20px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        margin: 0;
    }

    .room-details-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .room-details-card {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .room-details-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .room-details-header h1 {
        font-size: 24px;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .room-details-content {
        padding: 24px;
    }

    .room-details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .room-details-images {
        margin-bottom: 24px;
    }

    .room-details-image-wrapper {
        aspect-ratio: 4/3;
    }

    .room-details-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: opacity 0.3s;
    }

    .room-details-image:hover {
        opacity: 0.8;
    }

    .room-details-description {
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .room-details-amenities {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .room-details-amenity {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4b5563;
    }

    .room-details-amenity i {
        color: var(--primary-color);
        width: 20px;
    }

    .room-details-sidebar {
        background: #f9fafb;
        padding: 24px;
        border-radius: 8px;
    }

    .room-details-price {
        font-size: 24px;
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 16px;
    }

    .room-details-info {
        margin-bottom: 24px;
    }

    .room-details-info p {
        margin: 8px 0;
        color: #4b5563;
    }

    .room-details-guest-selection {
        margin-bottom: 24px;
    }

    .room-details-guest-select {
        width: 100%;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 16px;
        color: #4b5563;
        background-color: white;
        margin-bottom: 12px;
    }

    .room-details-guest-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.2);
    }

    .room-details-price-display {
        padding: 12px;
        border-radius: 6px;
        display: block;
        font-weight: 400;
    }

    .room-details-button {
        display: inline-block;
        width: 100%;
        padding: 12px 24px;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        text-align: center;
        border-radius: 6px;
        font-weight: 600;
        margin-bottom: 12px;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .room-details-button:hover {
        background: #235329;
    }

    .room-details-button:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .room-details-back {
        display: inline-block;
        width: 100%;
        padding: 12px 24px;
        background: #f3f4f6;
        color: #4b5563;
        text-decoration: none;
        text-align: center;
        border-radius: 6px;
        font-weight: 600;
        transition: background-color 0.2s;
    }

    .room-details-back:hover {
        background: #e5e7eb;
    }

    .room-details-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 1050;
        padding: 20px;
        box-sizing: border-box;
    }

    .room-details-modal-content {
        max-width: 90%;
        max-height: 90vh;
        margin: auto;
        display: block;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }

    .room-details-modal-close {
        position: absolute;
        top: 15px;
        right: 25px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .room-details-grid {
            grid-template-columns: 1fr;
        }

        .room-details-image-wrapper {
            width: 100%;
        }

        .room-details-amenities {
            grid-template-columns: 1fr;
        }
    }
</style>

<body class="room-details-body">
    <div id="room-details-modal" class="room-details-modal">
        <span class="room-details-modal-close">&times;</span>
        <img class="room-details-modal-content" id="room-details-modal-image">
    </div>
    <div class="room-details-container">
        <div class="room-details-card">
            <div class="room-details-header">
                <h1><i class="fas fa-hotel"></i> <?php echo htmlspecialchars($room['name']); ?></h1>
            </div>

            <div class="room-details-content">
                <div class="room-details-grid">
                    <div class="room-details-main">
                        <div class="room-details-images" id="room-details-slider">
                            <?php
                            $image_query = "SELECT image_path FROM room_images WHERE room_id = ?";
                            if ($stmt = $mysqli->prepare($image_query)) {
                                $stmt->bind_param("i", $room_id);
                                $stmt->execute();
                                $image_result = $stmt->get_result();

                                if ($image_result->num_rows > 0) {
                                    while ($image = $image_result->fetch_assoc()) {
                                        echo '<div class="room-details-image-wrapper">';
                                        echo '<img src="asset/uploads/' . htmlspecialchars($image['image_path']) .
                                            '" alt="Room View" class="room-details-image">';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="room-details-image-wrapper">';
                                    echo '<img src="/api/placeholder/600/400" alt="Room View 1" class="room-details-image">';
                                    echo '</div>';
                                    echo '<div class="room-details-image-wrapper">';
                                    echo '<img src="/api/placeholder/600/400" alt="Room View 2" class="room-details-image">';
                                    echo '</div>';
                                }
                                $stmt->close();
                            }
                            ?>
                        </div>

                        <div class="room-details-description">
                            <?php echo htmlspecialchars($room['description']); ?>
                        </div>

                        <h2>Room Amenities</h2>
                        <div class="room-details-amenities">
                            <?php foreach ($room['amenities'] as $amenity): ?>
                                <div class="room-details-amenity">
                                    <?php
                                    $icon = '';
                                    switch ($amenity) {
                                        case 'Free Wi-Fi':
                                            $icon = 'wifi';
                                            break;
                                        case 'TV':
                                            $icon = 'tv';
                                            break;
                                        case 'Air Conditioning':
                                            $icon = 'snowflake';
                                            break;
                                        case 'Coffee Maker':
                                            $icon = 'coffee';
                                            break;
                                        case 'Private Bathroom':
                                            $icon = 'bath';
                                            break;
                                        case 'Room Safe':
                                            $icon = 'shield-alt';
                                            break;
                                        case 'Mini Fridge':
                                            $icon = 'box';
                                            break;
                                        case 'Work Desk':
                                            $icon = 'desk';
                                            break;
                                        case 'Kitchen':
                                            $icon = 'utensils';
                                            break;
                                        case 'Lounge Area':
                                            $icon = 'couch';
                                            break;
                                    }
                                    ?>
                                    <i class="fas fa-<?php echo $icon; ?>"></i>
                                    <span><?php echo htmlspecialchars($amenity); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
 
                    <div class="room-details-sidebar">
                        <div class="room-details-guest-selection">
                            <label style="display: block; margin-bottom: 8px; color: #4b5563; font-weight: 500;">
                                Room Details
                            </label>

                            <?php if ($maxOccupancy && $maxPrice): ?>
                                <p class="room-details-price-display">
                                    👥 Max Capacity: <?php echo $maxOccupancy; ?> <?php echo $maxOccupancy === 1 ? 'Guest' : 'Guests'; ?>
                                </p>
                                <p id="room-details-price-display"
                                    class="room-details-price-display"
                                    data-price="<?php echo $maxPrice; ?>"
                                    data-guests="<?php echo $maxOccupancy; ?>"
                                    style="display: block;">
                                    💰 Price: ₱<?php echo number_format($maxPrice); ?> per day
                                </p>
                            <?php else: ?>
                                <p style="color: red;">Room details not available.</p>
                            <?php endif; ?>
                        </div>

                        <button id="room-details-book-button" class="room-details-button" disabled>Book This Room</button>
                        <a href="?section=Room-Reservation" class="room-details-back">Back to Room Selection</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('room-details-modal');
            const modalImg = document.getElementById('room-details-modal-image');
            const closeBtn = document.getElementsByClassName('room-details-modal-close')[0];
            const mainSidebar = document.querySelector('.main-sidebar');
            const mainHeader = document.querySelector('.main-header');

            document.querySelectorAll('.room-details-image').forEach(img => {
                img.onclick = function() {
                    modal.style.display = 'block';
                    modalImg.src = this.src;
                    if (mainSidebar) mainSidebar.style.zIndex = '1000';
                    if (mainHeader) mainHeader.style.zIndex = '1000';
                }
            });

            closeBtn.onclick = function() {
                modal.style.display = 'none';
                if (mainSidebar) mainSidebar.style.zIndex = '1001';
                if (mainHeader) mainHeader.style.zIndex = '1000';
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                    if (mainSidebar) mainSidebar.style.zIndex = '1001';
                    if (mainHeader) mainHeader.style.zIndex = '1000';
                }
            }

            const slider = tns({
                container: '#room-details-slider',
                items: 1,
                slideBy: 1,
                autoplay: false,
                controls: false,
                nav: true,
                responsive: {
                    769: {
                        items: 2
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const priceDisplay = document.getElementById('room-details-price-display');
            const bookButton = document.getElementById('room-details-book-button');

            if (!priceDisplay || !bookButton) return;

            const price = priceDisplay.dataset.price;
            const guests = priceDisplay.dataset.guests;

            console.log('Loaded values → Price:', price, '| Guests:', guests);

            if (price && guests) {
                bookButton.disabled = false;
                sessionStorage.setItem('selectedPrice', price);
                sessionStorage.setItem('selectedGuests', guests);
                // Update the text content to ensure it's displaying correctly
                priceDisplay.textContent = `💰 Price: ₱${Number(price).toLocaleString()} per day`;
            } else {
                priceDisplay.style.color = 'red';
                priceDisplay.textContent = '⚠️ Error: Price not available';
                bookButton.disabled = true;
            }

            bookButton.addEventListener('click', function() {
                if (!guests || !price) return;
                window.location.href = `?section=Room-Reservation&select_room=<?php echo $room_id; ?>&guests=${guests}&price=${price}`;
            });
        });
    </script>
</body>

</html>