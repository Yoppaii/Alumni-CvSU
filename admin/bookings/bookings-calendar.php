<?php
// Get current month and year
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Calendar helper functions
function getMonthName($month) {
    return date('F', mktime(0, 0, 0, $month, 1));
}

function getDaysInMonth($month, $year) {
    return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}

function getFirstDayOfMonth($month, $year) {
    return date('w', strtotime("$year-$month-01"));
}
?>

<div class="calendar-container">
    <div class="calendar-header">
        <h2>Booking Calendar</h2>
        <div class="calendar-nav">
            <?php
            $prevMonth = $month - 1;
            $prevYear = $year;
            if ($prevMonth < 1) {
                $prevMonth = 12;
                $prevYear--;
            }
            
            $nextMonth = $month + 1;
            $nextYear = $year;
            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear++;
            }
            ?>
            <a href="?section=booking-calendar&month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="btn btn-outline">
                <i class="fas fa-chevron-left"></i>
            </a>
            <h3><?php echo getMonthName($month) . " " . $year; ?></h3>
            <a href="?section=booking-calendar&month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="btn btn-outline">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <div class="calendar-grid">
        <div class="calendar-weekdays">
            <div>Sunday</div>
            <div>Monday</div>
            <div>Tuesday</div>
            <div>Wednesday</div>
            <div>Thursday</div>
            <div>Friday</div>
            <div>Saturday</div>
        </div>
        <div class="calendar-days">
            <?php
            $daysInMonth = getDaysInMonth($month, $year);
            $firstDay = getFirstDayOfMonth($month, $year);

            // Add empty cells for days before the first day of the month
            for ($i = 0; $i < $firstDay; $i++) {
                echo '<div class="calendar-day empty"></div>';
            }

            // Add calendar days
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = date('Y-m-d', strtotime("$year-$month-$day"));
                $isToday = ($currentDate == date('Y-m-d'));
                
                echo '<div class="calendar-day' . ($isToday ? ' today' : '') . '">';
                echo '<div class="day-number">' . $day . '</div>';
                echo '<div class="add-booking" onclick="addBooking(\'' . $currentDate . '\')">+</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

<style>
.calendar-container {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    margin: 1rem;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.calendar-nav {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.calendar-nav h3 {
    min-width: 200px;
    text-align: center;
    font-size: 1.25rem;
    font-weight: 600;
}

.btn-outline {
    padding: 0.5rem 1rem;
    border: 1px solid var(--primary-color);
    border-radius: var(--radius-md);
    color: var(--primary-color);
    background: transparent;
    cursor: pointer;
    transition: var(--transition);
}

.btn-outline:hover {
    background: var(--primary-color);
    color: white;
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 8px;
}

.calendar-weekdays div {
    text-align: center;
    font-weight: 600;
    padding: 0.75rem;
    background: var(--primary-light);
    border-radius: var(--radius-sm);
    color: var(--primary-color);
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendar-day {
    aspect-ratio: 1;
    padding: 0.5rem;
    background: var(--bg-secondary);
    border-radius: var(--radius-sm);
    position: relative;
    transition: var(--transition);
}

.calendar-day:hover {
    background: var(--bg-primary);
    box-shadow: var(--shadow-sm);
}

.calendar-day.empty {
    background: transparent;
}

.calendar-day.today {
    background: var(--primary-light);
    border: 2px solid var(--primary-color);
}

.day-number {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    font-weight: 600;
    color: var(--text-secondary);
}

.add-booking {
    position: absolute;
    bottom: 0.5rem;
    right: 0.5rem;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: var(--transition);
}

.calendar-day:hover .add-booking {
    opacity: 1;
}

.add-booking:hover {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .calendar-container {
        padding: 1rem;
        margin: 0.5rem;
    }

    .calendar-weekdays div {
        padding: 0.5rem;
        font-size: 0.75rem;
    }

    .calendar-nav h3 {
        min-width: 150px;
        font-size: 1rem;
    }

    .day-number {
        font-size: 0.875rem;
    }

    .add-booking {
        opacity: 1;
        width: 20px;
        height: 20px;
        font-size: 0.875rem;
    }
}
</style>

<script>
function addBooking(date) {
    // This is a placeholder function - you can modify it to handle booking creation
    console.log('Add booking for date:', date);
    // You could redirect to the booking form or open a modal here
    alert('Add booking for ' + date);
}

document.addEventListener('DOMContentLoaded', function() {
    // Add any additional initialization code here
});
</script>