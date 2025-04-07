<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CvSU Alumni Affairs Events</title>
</head>
<body>
    <div class="ev-container">
        <h1 class="ev-page-title">CvSU Alumni Affairs Events</h1>
        <div class="ev-filters">
            <select class="ev-year-select" id="yearSelect">
                <option value="2024">2024</option>
                <option value="2023">2023</option>
            </select>
            <div class="ev-filter-buttons">
                <button class="ev-filter-btn active" data-filter="all">ALL</button>
                <button class="ev-filter-btn" data-filter="upcoming">Upcoming</button>
                <button class="ev-filter-btn" data-filter="past">Past</button>
            </div>
        </div>

        <div class="ev-events-grid" id="eventsGrid">
            <!-- Events will be dynamically inserted here -->
        </div>
    </div>

    <script>
        const events = [
            {
                day: '15',
                month: 'Sep',
                title: 'CvSU Alumni Homecoming 2024',
                venue: 'CvSU Main Campus, Indang, Cavite'
            },
            {
                day: '22',
                month: 'Sep',
                title: 'Alumni Career Development Workshop',
                venue: 'CvSU Convention Center'
            },
            {
                day: '28',
                month: 'Sep',
                title: 'Alumni Community Outreach Program',
                venue: 'Various Locations in Cavite'
            },
            {
                day: '05',
                month: 'Oct',
                title: 'Distinguished Alumni Awards Night',
                venue: 'CvSU Gymnasium'
            },
            {
                day: '12',
                month: 'Oct',
                title: 'Alumni Business Network Forum',
                venue: 'CvSU College of Business'
            }
        ];

        function createEventCard(event) {
            return `
                <div class="ev-event-card">
                    <div class="ev-event-date">
                        <div class="ev-day">${event.day}</div>
                        <div class="ev-month">${event.month}</div>
                    </div>
                    <div class="ev-event-info">
                        <h3 class="ev-event-title">${event.title}</h3>
                        <p class="ev-event-venue">${event.venue}</p>
                        <a href="#" class="ev-view-details">View Details</a>
                    </div>
                </div>
            `;
        }

        function renderEvents() {
            const eventsGrid = document.getElementById('eventsGrid');
            eventsGrid.innerHTML = events.map(event => createEventCard(event)).join('');
        }

        // Filter button functionality
        const filterButtons = document.querySelectorAll('.ev-filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                // Add filter logic here
            });
        });

        // Initial render
        renderEvents();
    </script>
</body>
</html>
<style>
    /* HTML classes need to be updated to match these CSS selectors */

.ev-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.ev-page-title {
    color: #006400;
    text-align: center;
    font-size: 2.5em;
    margin-bottom: 40px;
}

/* Filters Section */
.ev-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.ev-year-select {
    padding: 10px 20px;
    border: 2px solid #006400;
    border-radius: 6px;
    font-size: 16px;
    color: #006400;
    background-color: white;
    cursor: pointer;
    outline: none;
}

.ev-year-select:focus {
    box-shadow: 0 0 0 2px rgba(0, 100, 0, 0.1);
}

.ev-filter-buttons {
    display: flex;
    gap: 15px;
}

.ev-filter-btn {
    padding: 10px 25px;
    border: none;
    border-radius: 6px;
    background-color: #f0f0f0;
    color: #333;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.ev-filter-btn:hover {
    background-color: #e0e0e0;
}

.ev-filter-btn.active {
    background-color: #006400;
    color: white;
}

/* Events Grid */
.ev-events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    padding: 20px 0;
}

.ev-event-card {
    display: flex;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.ev-event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.ev-event-date {
    background-color: #006400;
    color: white;
    padding: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 80px;
}

.ev-event-date .ev-day {
    font-size: 24px;
    font-weight: bold;
    line-height: 1;
}

.ev-event-date .ev-month {
    font-size: 14px;
    text-transform: uppercase;
    margin-top: 5px;
}

.ev-event-info {
    padding: 15px;
    flex-grow: 1;
}

.ev-event-title {
    color: #006400;
    font-size: 18px;
    margin: 0 0 10px 0;
    line-height: 1.4;
}

.ev-event-venue {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
}

.ev-view-details {
    display: inline-block;
    color: #006400;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    padding: 5px 0;
    position: relative;
}

.ev-view-details::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: #006400;
    transition: width 0.3s ease;
}

.ev-view-details:hover::after {
    width: 100%;
}

/* Responsive Design */
@media (max-width: 768px) {
    .ev-filters {
        flex-direction: column;
        align-items: stretch;
    }

    .ev-filter-buttons {
        justify-content: center;
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
    .ev-page-title {
        font-size: 2em;
    }

    .ev-filter-buttons {
        flex-wrap: wrap;
        justify-content: center;
    }

    .ev-filter-btn {
        flex: 1;
        min-width: 100px;
        text-align: center;
    }

    .ev-event-card {
        flex-direction: column;
    }

    .ev-event-date {
        flex-direction: row;
        justify-content: center;
        gap: 10px;
        padding: 10px;
    }
}
</style>