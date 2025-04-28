<?php
if (!isset($_SESSION)) {
    session_start();
}

require 'main_db.php';

$amenityIcons = [
    'Free Wi-Fi' => 'wifi',
    'TV' => 'tv',
    'Air Conditioning' => 'snowflake',
    'Coffee Maker' => 'coffee',
    'Private Bathroom' => 'bath',
    'Room Safe' => 'shield-alt',
    'Mini Fridge' => 'box',
    'Work Desk' => 'briefcase',
    'Kitchen' => 'utensils',
    'Lounge Area' => 'couch',
    'Hair Dryer' => 'wind',
    'Iron' => 'magnet',
    'Balcony' => 'tree',
    'Telephone' => 'phone',
    'Heater' => 'fire',
    'Towels' => 'tshirt',
    'Toiletries' => 'soap',
    'Microwave' => 'burn',
    'Closet' => 'archive',
    'Alarm Clock' => 'clock',
];

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
    default => 'room_price'
};

// Fetch all occupancy and price options
$occupancies = [];
$query = "SELECT occupancy, price FROM {$pricing_table} ORDER BY occupancy ASC";
if ($result = $mysqli->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $occupancies[] = $row;
    }
}

// Extract max occupancy for display
$maxOccupancy = !empty($occupancies) ? max(array_column($occupancies, 'occupancy')) : null;

// Encode pricing data for JavaScript
$pricingJson = json_encode($occupancies);

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

    .selection-section {
        margin: 16px 0;
    }

    .section-label {
        display: block;
        margin-bottom: 6px;
        color: #374151;
        font-weight: 600;
        font-size: 14px;
    }

    .counter-container {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f3f4f6;
        padding: 8px 12px;
        border-radius: 8px;
        width: fit-content;
    }

    .counter-btn {
        background-color: #4f46e5;
        color: #ffffff;
        border: none;
        padding: 6px 12px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .counter-btn:hover {
        background-color: #4338ca;
    }

    .counter-display {
        min-width: 24px;
        text-align: center;
        font-weight: 600;
        font-size: 16px;
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
                                    $icon = $amenityIcons[$amenity] ?? 'check'; // default to 'check' if not listed
                                    ?>
                                    <i class="fas fa-<?php echo $icon; ?>"></i>
                                    <span><?php echo htmlspecialchars($amenity); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="room-details-sidebar">
                        <div class="room-details-guest-selection">
                            <label class="section-label" style="margin-bottom: 8px;">Room Details</label>

                            <?php if ($maxOccupancy && !empty($occupancies)): ?>
                                <?php
                                // Check if the room is a standard room (1-8) or a special room
                                $isStandardRoom = ($room_id >= 1 && $room_id <= 8);

                                if ($isStandardRoom):
                                ?>
                                    <p class="room-details-price-display">
                                        👥 Max Capacity: <span id="max-capacity-count"><?php echo $maxOccupancy; ?></span> <?php echo $maxOccupancy === 1 ? 'Guest' : 'Guests'; ?>
                                    </p>

                                    <p id="room-details-price-display"
                                        class="room-details-price-display"
                                        data-price-options='<?php echo htmlspecialchars($pricingJson, ENT_QUOTES, 'UTF-8'); ?>'
                                        data-max-guests="<?php echo $maxOccupancy; ?>"
                                        data-room-id="<?php echo $room_id; ?>">
                                        💰 Base Price: ₱<span id="base-price-display"><?php echo !empty($occupancies) ? number_format($occupancies[0]['price']) : '0'; ?></span> per day
                                    </p>

                                    <!-- Guest Selection -->
                                    <div class="selection-section">
                                        <label class="section-label">Number of Guests:</label>
                                        <div class="counter-container">
                                            <button type="button" id="guest-decrease" class="counter-btn">−</button>
                                            <span id="guest-count" class="counter-display">1</span>
                                            <button type="button" id="guest-increase" class="counter-btn">+</button>
                                        </div>
                                    </div>
                                    <!-- Mattress Selection -->
                                    <div class="selection-section">
                                        <label class="section-label">
                                            Add Mattress <small style="color: #6b7280;">(₱500)</small>:
                                        </label>
                                        <div class="counter-container">
                                            <button type="button" id="mattress-decrease" class="counter-btn">−</button>
                                            <span id="mattress-count" class="counter-display">0</span>
                                            <button type="button" id="mattress-increase" class="counter-btn">+</button>
                                        </div>
                                    </div>

                                    <p class="info-note room-details-price-display">🛏️ Each extra mattress adds 2 guest slots.</p>

                                    <!-- Total Price Display -->
                                    <p id="room-details-total-display" class="room-details-price-display">
                                        🧾 Total: ₱<span id="total-price">0</span> per day
                                    </p>
                                <?php else: ?>
                                    <!-- For special rooms (Board Room, Conference Room, Lobby) -->
                                    <p class="room-details-price-display">
                                        👥 Max Capacity: <span id="max-capacity-count"><?php echo $maxOccupancy; ?></span> <?php echo $maxOccupancy === 1 ? 'Guest' : 'Guests'; ?>
                                    </p>
                                    <p id="room-details-price-display"
                                        class="room-details-price-display"
                                        data-price-options='<?php echo htmlspecialchars($pricingJson, ENT_QUOTES, 'UTF-8'); ?>'
                                        data-room-id="<?php echo $room_id; ?>">
                                        💰 Price: ₱<span id="base-price-display"><?php echo !empty($occupancies) ? number_format($occupancies[0]['price']) : '0'; ?></span> per day
                                    </p>

                                    <p class="room-details-price-display">
                                        <i class="fas fa-info-circle"></i> This is a special purpose room with fixed pricing.
                                    </p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p style="color: red;">Room details not available.</p>
                            <?php endif; ?>
                        </div>

                        <button id="room-details-book-button" class="room-details-button" <?php echo (!$maxOccupancy || empty($occupancies)) ? 'disabled' : ''; ?>>Book This Room</button>
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

        document.addEventListener('DOMContentLoaded', () => {
            const priceDisplay = document.getElementById('room-details-price-display');
            const basePriceDisplay = document.getElementById('base-price-display');
            const totalPriceSpan = document.getElementById('total-price');
            const bookButton = document.getElementById('room-details-book-button');

            if (!priceDisplay || !bookButton) return;

            // Get room_id from data attribute
            const room_id = parseInt(priceDisplay.dataset.roomId, 10) || 0;
            const isStandardRoom = (room_id >= 1 && room_id <= 8);

            // Get pricing options from data attribute
            let priceOptions = [];
            try {
                priceOptions = JSON.parse(priceDisplay.dataset.priceOptions || '[]');
            } catch (e) {
                console.error('Error parsing price options:', e);
            }

            // For standard rooms (1-8), set up the guest and mattress functionality
            if (isStandardRoom) {
                const maxCapacitySpan = document.getElementById('max-capacity-count');
                const mattressCountSpan = document.getElementById('mattress-count');
                const mattressIncrease = document.getElementById('mattress-increase');
                const mattressDecrease = document.getElementById('mattress-decrease');
                const guestCountSpan = document.getElementById('guest-count');
                const guestIncrease = document.getElementById('guest-increase');
                const guestDecrease = document.getElementById('guest-decrease');

                if (!maxCapacitySpan || !totalPriceSpan) return;

                // Get the base max guests from the data attribute
                const baseMaxGuests = parseInt(priceDisplay.dataset.maxGuests, 10);
                const mattressPrice = 500;
                const maxMattresses = 1;

                let mattressCount = 0;
                let guestCount = 1;
                let basePrice = getBasePrice(guestCount);

                // Function to get the base price for a given guest count
                function getBasePrice(guests) {
                    // Find the appropriate price tier
                    let price = 0;

                    // Sort price options by occupancy (ascending)
                    const sortedOptions = [...priceOptions].sort((a, b) => a.occupancy - b.occupancy);

                    // Find the appropriate price tier
                    for (const option of sortedOptions) {
                        if (guests <= option.occupancy) {
                            price = parseInt(option.price, 10);
                            break;
                        }
                    }

                    // If guest count is higher than any tier, use the highest tier's price
                    if (price === 0 && sortedOptions.length > 0) {
                        price = parseInt(sortedOptions[sortedOptions.length - 1].price, 10);
                    }

                    return price;
                }

                // Calculate the current maximum guests based on base limit and mattresses
                const getCurrentMaxGuests = () => baseMaxGuests + mattressCount * 2;

                const updateDisplay = () => {
                    // Update base price based on current guest count
                    basePrice = getBasePrice(guestCount);
                    basePriceDisplay.textContent = basePrice.toLocaleString();

                    const total = basePrice + mattressCount * mattressPrice;
                    totalPriceSpan.textContent = total.toLocaleString();
                    maxCapacitySpan.textContent = getCurrentMaxGuests();

                    // Store values in session for booking process
                    sessionStorage.setItem('selectedTotalPrice', total);
                    sessionStorage.setItem('selectedBasePrice', basePrice);
                    sessionStorage.setItem('selectedMattresses', mattressCount);
                    sessionStorage.setItem('selectedGuests', guestCount);

                    // Disable mattress increase if max is reached
                    mattressIncrease.disabled = mattressCount >= maxMattresses;
                    mattressDecrease.disabled = mattressCount <= 0;

                    // Disable guest increase if max capacity is reached
                    guestIncrease.disabled = guestCount >= getCurrentMaxGuests();
                    guestDecrease.disabled = guestCount <= 1;
                };

                const updateGuestCount = (newCount) => {
                    const maxGuests = getCurrentMaxGuests();
                    guestCount = Math.max(1, Math.min(newCount, maxGuests));
                    guestCountSpan.textContent = guestCount;
                    updateDisplay();
                };

                if (priceOptions.length > 0) {
                    bookButton.disabled = false;
                    updateDisplay();
                } else {
                    priceDisplay.style.color = 'red';
                    priceDisplay.textContent = '⚠️ Error: Price not available';
                    bookButton.disabled = true;
                }

                mattressIncrease.addEventListener('click', () => {
                    if (mattressCount < maxMattresses) {
                        mattressCount++;
                        mattressCountSpan.textContent = mattressCount;
                        updateDisplay();
                    }
                });

                mattressDecrease.addEventListener('click', () => {
                    if (mattressCount > 0) {
                        mattressCount--;
                        mattressCountSpan.textContent = mattressCount;

                        const newMax = getCurrentMaxGuests();
                        if (guestCount > newMax) {
                            updateGuestCount(newMax);
                        } else {
                            updateDisplay();
                        }
                    }
                });

                guestIncrease.addEventListener('click', () => {
                    if (guestCount < getCurrentMaxGuests()) {
                        updateGuestCount(guestCount + 1);
                    }
                });

                guestDecrease.addEventListener('click', () => {
                    if (guestCount > 1) {
                        updateGuestCount(guestCount - 1);
                    }
                });

                bookButton.addEventListener('click', () => {
                    const total = basePrice + mattressCount * mattressPrice;
                    const url = `?section=Room-Reservation&select_room=${room_id}&guests=${guestCount}&price=${basePrice}&mattresses=${mattressCount}&total=${total}`;
                    window.location.href = url;
                });

            } else {
                // For special rooms (Board Room, Conference Room, Lobby)
                // Simplified booking with fixed price
                if (priceOptions.length > 0) {
                    const basePrice = parseInt(priceOptions[0].price, 10);

                    // Store fixed values in session
                    sessionStorage.setItem('selectedTotalPrice', basePrice);
                    sessionStorage.setItem('selectedBasePrice', basePrice);
                    sessionStorage.setItem('selectedMattresses', 0);
                    sessionStorage.setItem('selectedGuests', 1); // Default to 1 even though not relevant

                    bookButton.disabled = false;
                    console.log('room_id:', room_id);
                    console.log('basePrice:', basePrice);

                    bookButton.addEventListener('click', () => {
                        const url = `?section=Room-Reservation&select_room=${room_id}&price=${basePrice}&total=${basePrice}`;
                        window.location.href = url;
                    });
                } else {
                    priceDisplay.style.color = 'red';
                    priceDisplay.textContent = '⚠️ Error: Price not available';
                    bookButton.disabled = true;
                }
            }
        });
    </script>
</body>

</html>