<?php
require_once 'main_db.php';

$query = "SELECT room_number, arrival_date, departure_date FROM bookings WHERE status = 'confirmed'";
$result = $mysqli->query($query);

$bookings = array();
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$month = date('m');
$year = date('Y');
$firstDay = date('w', strtotime("$year-$month-01"));
$daysInMonth = date('t', strtotime("$year-$month-01"));

$book_rooms = [
    ['id' => 1, 'name' => 'Room 1'],
    ['id' => 2, 'name' => 'Room 2'],
    ['id' => 3, 'name' => 'Room 3'],
    ['id' => 4, 'name' => 'Room 4'],
    ['id' => 5, 'name' => 'Room 5'],
    ['id' => 6, 'name' => 'Room 6'],
    ['id' => 7, 'name' => 'Room 7'],
    ['id' => 8, 'name' => 'Room 8'],
    ['id' => 9, 'name' => 'Board Room'],
    ['id' => 10, 'name' => 'Conference Room'],
    ['id' => 11, 'name' => 'Lobby']
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Booking Calendar</title>
    <style>
        .calendar-container {
            margin: 20px;
            padding: 20px;
            background: #fff;
            max-width: auto;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .room-selector {
            margin-bottom: 20px;
            text-align: center;
        }

        .room-selector select {
            padding: 8px 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .room-selector select:hover {
            border-color: #006400;
        }

        .month-header {
            text-align: center;
            margin-bottom: 20px;
            color: #006400;
            font-size: 24px;
            font-weight: bold;
        }

        .calendar {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
        }

        .calendar th {
            background: #006400;
            color: white;
            padding: 12px;
            text-align: center;
            border: 1px solid #004d00;
        }

        .calendar td {
            border: 1px solid #ddd;
            padding: 15px 10px;
            text-align: center;
            height: 40px;
            position: relative;
            transition: background-color 0.3s;
        }

        .calendar-day {
            cursor: default;
        }

        .booked {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .available {
            background-color: #f1f8e9;
            color: #006400;
        }

        .date-number {
            font-size: 14px;
            font-weight: 500;
        }

        .booking-info {
            margin-top: 5px;
            font-size: 12px;
        }

        .calendar-legend {
            margin-top: 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 10px;
            background: #f8f8f8;
            border-radius: 4px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            border-radius: 4px;
            background: white;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .legend-available {
            background-color: #f1f8e9;
            border: 1px solid #006400;
        }

        .legend-booked {
            background-color: #ffebee;
            border: 1px solid #d32f2f;
        }

        .empty-cell {
            background-color: #f8f8f8;
        }

        .calendar td:hover {
            box-shadow: inset 0 0 0 2px #006400;
        }

        .booked:hover {
            box-shadow: inset 0 0 0 2px #d32f2f;
        }
    </style>
</head>
<body>
    <div class="calendar-container">
        <div class="room-selector">
            <select id="roomSelect" onchange="updateCalendar()">
                <option value="">Select a Room</option>
                <?php
                foreach ($book_rooms as $room) {
                    echo "<option value='{$room['id']}'>{$room['name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="month-header">
            <?php echo date('F Y'); ?>
        </div>
        
        <table class="calendar">
            <tr>
                <?php
                $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                foreach ($days as $day) {
                    echo "<th>$day</th>";
                }
                ?>
            </tr>

            <?php
            $weeksInMonth = ceil(($firstDay + $daysInMonth) / 7);
            $dayCount = 1;

            for ($week = 0; $week < $weeksInMonth; $week++) {
                echo "<tr>";
                for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                    if (($week === 0 && $dayOfWeek < $firstDay) || ($dayCount > $daysInMonth)) {
                        echo "<td class='empty-cell'></td>";
                    } else {
                        $currentDate = "$year-$month-" . str_pad($dayCount, 2, '0', STR_PAD_LEFT);
                        echo "<td class='calendar-day available' data-date='$currentDate'>";
                        echo "<div class='date-number'>$dayCount</div>";
                        echo "</td>";
                        $dayCount++;
                    }
                }
                echo "</tr>";
            }
            ?>
        </table>

        <div class="calendar-legend">
            <div class="legend-item">
                <div class="legend-color legend-available"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color legend-booked"></div>
                <span>Booked</span>
            </div>
        </div>
    </div>

    <script>
        const bookings = <?php echo json_encode($bookings); ?>;

        function updateCalendar() {
            const selectedRoom = document.getElementById('roomSelect').value;
            const calendarDays = document.querySelectorAll('.calendar-day');

            calendarDays.forEach(day => {
                day.className = 'calendar-day available';
            });

            if (selectedRoom) {
                calendarDays.forEach(day => {
                    const date = day.dataset.date;
                    if (date) {
                        const isBooked = bookings.some(booking => {
                            return booking.room_number === selectedRoom &&
                                new Date(date) >= new Date(booking.arrival_date) &&
                                new Date(date) <= new Date(booking.departure_date);
                        });
                        
                        if (isBooked) {
                            day.className = 'calendar-day booked';
                        }
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCalendar();
        });
    </script>
</body>
</html>