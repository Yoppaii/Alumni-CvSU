<?php
require_once 'admin/Security-Files.php';
require_once 'main_db.php';

// Function to generate unique reference number
function generateReferenceNumber() {
    return 'BK' . date('Ymd') . substr(str_shuffle("0123456789"), 0, 4);
}

$success = '';
$error = '';

// Fetch available rooms
$roomQuery = "SELECT room_number FROM rooms WHERE status = 'available' ORDER BY room_number";
$roomResult = $mysqli->query($roomQuery);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $room_number = $mysqli->real_escape_string($_POST['room_number']);
    $occupancy = (int)$_POST['occupancy'];
    $price = (float)$_POST['price'];
    $price_per_day = (float)$_POST['price_per_day'];
    $arrival_date = $mysqli->real_escape_string($_POST['arrival_date']);
    $arrival_time = $mysqli->real_escape_string($_POST['arrival_time']);
    $departure_date = $mysqli->real_escape_string($_POST['departure_date']);
    $departure_time = $mysqli->real_escape_string($_POST['departure_time']);

    // Validate dates
    $today = date('Y-m-d');
    if ($arrival_date < $today) {
        $error = "Arrival date cannot be in the past.";
    } else {
        // Generate reference number
        $reference_number = generateReferenceNumber();
        
        // Check if room is available for selected dates
        $checkQuery = "SELECT id FROM bookings 
                      WHERE room_number = '$room_number' 
                      AND (
                          (arrival_date BETWEEN '$arrival_date' AND '$departure_date')
                          OR (departure_date BETWEEN '$arrival_date' AND '$departure_date')
                      )
                      AND status != 'cancelled'";
        
        $checkResult = $mysqli->query($checkQuery);
        
        if ($checkResult->num_rows > 0) {
            $error = "Room is not available for the selected dates.";
        } else {
            // Insert booking
            $insertQuery = "INSERT INTO bookings (
                reference_number, user_id, room_number, occupancy, 
                price, price_per_day, arrival_date, arrival_time,
                departure_date, departure_time, status, created_at
            ) VALUES (
                '$reference_number', $user_id, '$room_number', $occupancy,
                $price, $price_per_day, '$arrival_date', '$arrival_time',
                '$departure_date', '$departure_time', 'pending', NOW()
            )";
            
            if ($mysqli->query($insertQuery)) {
                $success = "Booking created successfully! Reference number: " . $reference_number;
                // Clear form data after successful submission
                $_POST = array();
            } else {
                $error = "Error creating booking: " . $mysqli->error;
            }
        }
    }
}
?>

<div class="container p-4">
    <div class="card">
        <div class="card-header">
            <h4>Create New Booking</h4>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="bookingForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="room_number">Room Number</label>
                            <select name="room_number" id="room_number" class="form-control" required>
                                <option value="">Select Room</option>
                                <?php while ($room = $roomResult->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($room['room_number']); ?>">
                                        Room <?php echo htmlspecialchars($room['room_number']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="occupancy">Occupancy</label>
                            <input type="number" class="form-control" name="occupancy" id="occupancy" 
                                   min="1" max="4" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="arrival_date">Arrival Date</label>
                            <input type="date" class="form-control" name="arrival_date" id="arrival_date" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="arrival_time">Arrival Time</label>
                            <input type="time" class="form-control" name="arrival_time" id="arrival_time" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="departure_date">Departure Date</label>
                            <input type="date" class="form-control" name="departure_date" id="departure_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="departure_time">Departure Time</label>
                            <input type="time" class="form-control" name="departure_time" id="departure_time" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price_per_day">Price Per Day</label>
                            <input type="number" class="form-control" name="price_per_day" id="price_per_day" 
                                   step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price">Total Price</label>
                            <input type="number" class="form-control" name="price" id="price" 
                                   step="0.01" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Create Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #059669;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.form-group {
    margin-bottom: 1rem;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
}

.btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.card {
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 0.25rem;
}

.card-header {
    padding: 0.75rem 1.25rem;
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.card-body {
    padding: 1.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    const arrivalDate = document.getElementById('arrival_date');
    const departureDate = document.getElementById('departure_date');
    const pricePerDay = document.getElementById('price_per_day');
    const totalPrice = document.getElementById('price');

    // Update minimum departure date when arrival date changes
    arrivalDate.addEventListener('change', function() {
        departureDate.min = this.value;
        if (departureDate.value && departureDate.value < this.value) {
            departureDate.value = this.value;
        }
        calculatePrice();
    });

    // Recalculate price when dates or price per day changes
    departureDate.addEventListener('change', calculatePrice);
    pricePerDay.addEventListener('input', calculatePrice);

    function calculatePrice() {
        if (arrivalDate.value && departureDate.value && pricePerDay.value) {
            const start = new Date(arrivalDate.value);
            const end = new Date(departureDate.value);
            const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            const price = days * parseFloat(pricePerDay.value);
            totalPrice.value = price.toFixed(2);
        }
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        const arrival = new Date(arrivalDate.value);
        const departure = new Date(departureDate.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (arrival < today) {
            e.preventDefault();
            alert('Arrival date cannot be in the past.');
            return;
        }

        if (departure <= arrival) {
            e.preventDefault();
            alert('Departure date must be after arrival date.');
            return;
        }
    });
});
</script>