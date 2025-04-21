<?php
// Database connection
require_once 'main_db.php';
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
// Get the year parameter
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$year = $mysqli->real_escape_string($year);
// Get current date for upcoming/past filtering
$current_date = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CvSU Alumni Events</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f9f9f9;
        }

        /* Hero Section Styles */
        .ev-hero {
            color: white;
            padding: 80px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            min-height: 300px;
            height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .ev-hero h2 {
            font-size: 36px;
            margin-bottom: 15px;
            font-weight: 700;
            position: relative;
            z-index: 2;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .ev-hero p {
            font-size: 18px;
            margin-bottom: 20px;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            position: relative;
            z-index: 2;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .ev-hero .cta-btn {
            padding: 12px 28px;
            background: gold;
            color: black;
            border-radius: 30px;
            font-size: 17px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 40px;
            position: relative;
            z-index: 2;
            border: 2px solid transparent;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .ev-hero .cta-btn:hover {
            background: #e6c700;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .ev-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 100%;
            background-image: url('asset/images/6.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            filter: brightness(0.4);
            z-index: 1;
        }

        /* Events Section Styles */
        .ev-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .ev-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
            font-size: 32px;
            position: relative;
            padding-bottom: 15px;
        }

        .ev-container h2::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background-color: #006400;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .ev-container h2 i {
            margin-right: 10px;
            color: #006400;
        }

        .ev-filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .ev-year-select {
            padding: 12px 20px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 16px;
            cursor: pointer;
            background-color: white;
            color: #333;
            min-width: 120px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .ev-year-select:hover {
            border-color: #006400;
        }

        .ev-year-select:focus {
            outline: none;
            border-color: #006400;
            box-shadow: 0 0 0 2px rgba(0, 100, 0, 0.2);
        }

        .ev-filter-buttons {
            display: flex;
            gap: 10px;
        }

        .ev-filter-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            background: #f0f0f0;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .ev-filter-btn:hover {
            background: #e5e5e5;
        }

        .ev-filter-btn.active {
            background: #006400;
            color: white;
        }

        .ev-events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .ev-event-card {
            display: flex;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            background: white;
        }

        .ev-event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .ev-event-date {
            background: #006400;
            color: white;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 90px;
        }

        .ev-day {
            font-size: 28px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 5px;
        }

        .ev-month {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ev-event-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .ev-event-title {
            margin-top: 0;
            margin-bottom: 12px;
            color: #333;
            font-size: 18px;
            line-height: 1.4;
        }

        .ev-event-venue {
            color: #666;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .ev-event-venue i {
            margin-right: 5px;
            color: #006400;
        }

        .ev-view-details {
            display: inline-block;
            padding: 8px 18px;
            background: #006400;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 15px;
            transition: all 0.3s ease;
            text-align: center;
            margin-top: 10px;
            align-self: flex-start;
        }

        .ev-view-details:hover {
            background: #004d00;
            transform: translateY(-2px);
        }

        .ev-no-events {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            font-size: 18px;
            color: #666;
        }

        .ev-no-events i {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 20px;
            display: block;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .ev-hero {
                padding: 60px 15px;
                min-height: 300px;
                height: auto;
            }

            .ev-hero h2 {
                font-size: 28px;
            }

            .ev-hero p {
                font-size: 16px;
                max-width: 100%;
                padding: 0 10px;
            }

            .ev-hero .cta-btn {
                padding: 10px 20px;
                font-size: 16px;
                margin-top: 30px;
                position: relative;
                bottom: auto;
                left: auto;
                transform: none;
            }

            .ev-container {
                padding: 40px 15px;
            }

            .ev-filters {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
                padding: 15px;
            }

            .ev-filter-buttons {
                width: 100%;
                justify-content: space-between;
            }

            .ev-filter-btn {
                flex: 1;
                text-align: center;
                font-size: 14px;
                padding: 10px 5px;
            }

            .ev-year-select {
                width: 100%;
            }

            .ev-events-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .ev-hero {
                padding: 50px 10px;
                min-height: 250px;
            }

            .ev-hero h2 {
                font-size: 24px;
            }

            .ev-hero p {
                font-size: 14px;
                line-height: 1.5;
            }

            .ev-hero .cta-btn {
                padding: 8px 18px;
                font-size: 14px;
            }

            .ev-container h2 {
                font-size: 24px;
            }

            .ev-event-title {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <section class="ev-hero">
        <h2>CvSU Alumni Events & Activities</h2>
        <p>Stay connected with alumni gatherings, workshops, and special events at Cavite State University. Join us to network with fellow graduates and share experiences. Don't miss out on opportunities to engage and grow within our vibrant community!</p>
        <a href="#Events" class="cta-btn" id="scroll-btn">View Events</a>
    </section>
    <div class="ev-container">
        <h2 id="Events">
            <i class="fas fa-calendar-alt"></i>
            CvSU OAA Events
        </h2>
        <div class="ev-filters">
            <select class="ev-year-select" id="yearSelect">
                <?php
                // Get available years from database
                $years_query = "SELECT DISTINCT YEAR(created_at) as year FROM events ORDER BY year DESC";
                $years_result = $mysqli->query($years_query);
                if ($years_result && $years_result->num_rows > 0) {
                    while ($year_row = $years_result->fetch_assoc()) {
                        $selected = ($year_row['year'] == $year) ? 'selected' : '';
                        echo "<option value='{$year_row['year']}' {$selected}>{$year_row['year']}</option>";
                    }
                } else {
                    // Fallback to current year if no events in database
                    echo "<option value='" . date('Y') . "'>" . date('Y') . "</option>";
                }
                ?>
            </select>
            <div class="ev-filter-buttons">
                <button class="ev-filter-btn active" data-filter="all">ALL</button>
                <button class="ev-filter-btn" data-filter="upcoming">Upcoming</button>
                <button class="ev-filter-btn" data-filter="past">Past</button>
            </div>
        </div>
        <div class="ev-events-grid" id="eventsGrid">
            <?php
            // Get events for the selected year
            $query = "SELECT `id`, `day`, `month`, `title`, `venue`, `description`, `created_at` 
                    FROM `events`
                    WHERE YEAR(created_at) = $year
                    ORDER BY FIELD(month, 'January', 'February', 'March', 'April', 'May', 'June',
                                'July', 'August', 'September', 'October', 'November', 'December'),
                            day ASC";
            $result = $mysqli->query($query);
            // Store events data for JavaScript
            $events_data = [];
            // Check if there are events
            if ($result && $result->num_rows > 0) {
                // Loop through each event and generate event cards
                while ($event = $result->fetch_assoc()) {
                    // Create a date string for filtering
                    $event_date = date('Y', strtotime($event['created_at'])) . '-' .
                        date('m', strtotime($event['month'] . ' 1')) . '-' .
                        sprintf('%02d', $event['day']);
                    // Determine if event is upcoming or past
                    $event_type = ($event_date >= $current_date) ? 'upcoming' : 'past';
                    echo '<div class="ev-event-card" data-id="' . $event['id'] . '" data-type="' . $event_type . '">';
                    echo '    <div class="ev-event-date">';
                    echo '        <div class="ev-day">' . $event['day'] . '</div>';
                    echo '        <div class="ev-month">' . substr($event['month'], 0, 3) . '</div>';
                    echo '    </div>';
                    echo '    <div class="ev-event-info">';
                    echo '        <h3 class="ev-event-title">' . htmlspecialchars($event['title']) . '</h3>';
                    echo '        <p class="ev-event-venue"><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($event['venue']) . '</p>';
                    echo '        <a href="?pages=events-detail&id=' . $event['id'] . '" class="ev-view-details">View Details</a>';
                    echo '    </div>';
                    echo '</div>';
                    // Add to JS array
                    $events_data[] = [
                        'id' => $event['id'],
                        'day' => $event['day'],
                        'month' => substr($event['month'], 0, 3),
                        'title' => $event['title'],
                        'venue' => $event['venue'],
                        'type' => $event_type
                    ];
                }
            } else {
                echo '<div class="ev-no-events">';
                echo '    <i class="far fa-calendar-times"></i>';
                echo '    <p>No events found for ' . $year . '.</p>';
                echo '    <p>Please check back later or select a different year.</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <script>
        // Store events data from PHP
        const events = <?php echo json_encode($events_data); ?>;

        // Year selection change
        document.getElementById('yearSelect').addEventListener('change', function() {
            const selectedYear = this.value;
            window.location.href = '?year=' + selectedYear + '#Events';
        });

        // Filter button functionality
        const filterButtons = document.querySelectorAll('.ev-filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                // Apply filter
                const filterType = button.getAttribute('data-filter');
                filterEvents(filterType);
            });
        });

        // Filter events based on type
        function filterEvents(filterType) {
            const eventCards = document.querySelectorAll('.ev-event-card');
            let visibleCount = 0;

            // Hide any existing "no events" message
            const existingNoEvents = document.querySelector('.ev-no-events');
            if (existingNoEvents) {
                existingNoEvents.parentNode.removeChild(existingNoEvents);
            }

            eventCards.forEach(card => {
                if (filterType === 'all' || card.getAttribute('data-type') === filterType) {
                    card.style.display = 'flex';
                    setTimeout(() => {
                        card.style.opacity = '1';
                    }, 50);
                    visibleCount++;
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });

            // Show "no events" message if no events match the filter
            if (visibleCount === 0) {
                const eventsGrid = document.getElementById('eventsGrid');
                const noEventsDiv = document.createElement('div');
                noEventsDiv.className = 'ev-no-events';
                noEventsDiv.innerHTML = `
                    <i class="far fa-calendar-times"></i>
                    <p>No ${filterType === 'all' ? '' : filterType} events found for ${document.getElementById('yearSelect').value}.</p>
                    <p>Please try a different filter or select another year.</p>
                `;
                eventsGrid.appendChild(noEventsDiv);
            }
        }

        // Smooth scroll functionality
        document.getElementById('scroll-btn').addEventListener('click', function(event) {
            event.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            targetElement.scrollIntoView({
                behavior: 'smooth'
            });
        });
    </script>
    <?php
    // Close the database connection
    $mysqli->close();
    ?>
</body>

</html>