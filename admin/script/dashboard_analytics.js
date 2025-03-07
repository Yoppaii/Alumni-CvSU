function user_analytics(period = 1) {
    fetch("/Alumni-CvSU/admin/analytics/dashboard_analytics.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ user_analytics: true, period }),
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);

            // Ensure data is valid before updating DOM
            let totalUsers = data.count ?? 0;
            let displayText = totalUsers; // Default display

            switch (parseInt(period)) {
                case 1:
                    displayText = `New today`;
                    break;
                case 2:
                    displayText = `in the past 7 days`;
                    break;
                case 3:
                    displayText = `in the past 30 days`;
                    break;
                case 4:
                    displayText = `in the past 90 days`;
                    break;
                case 5:
                    displayText = `in the past year`;
                    break;
                case 6:
                    displayText = `users (All time)`;
                    break;
                default:
                    displayText = "Invalid selection";
            }

            document.getElementById('total_users').textContent = totalUsers;
            document.getElementById('user_footer').textContent = displayText;
        })
        .catch(error => console.error("Error fetching user analytics:", error));
}

function booking_analytics(period = 1) {
    fetch("/Alumni-CvSU/admin/analytics/dashboard_analytics.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ booking_analytics: true, period }),
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);

            // Ensure data is valid before updating DOM
            let displayData = data.count ?? 0;
            let displayText = ""; // Default display

            switch (parseInt(period)) {
                case 1:
                    displayText = `New today`;
                    break;
                case 2:
                    displayText = `in the past 7 days`;
                    break;
                case 3:
                    displayText = `in the past 30 days`;
                    break;
                case 4:
                    displayText = `in the past 90 days`;
                    break;
                case 5:
                    displayText = `in the past year`;
                    break;
                case 6:
                    displayText = `bookings (All time)`;
                    break;
                default:
                    displayText = "Invalid selection";
            }

            document.getElementById('total_bookings').textContent = displayData;
            document.getElementById('bookings_footer').textContent = displayText;
        })
        .catch(error => console.error("Error fetching booking analytics:", error));
}

function alumni_id_cards_analytics(period = 1) {
    fetch("/Alumni-CvSU/admin/analytics/dashboard_analytics.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ alumni_id_cards_analytics: true, period }),
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);

            // Ensure data is valid before updating DOM
            let displayData = data.count ?? 0;
            let displayText = ""; // Default display

            switch (parseInt(period)) {
                case 1:
                    displayText = `New today`;
                    break;
                case 2:
                    displayText = `in the past 7 days`;
                    break;
                case 3:
                    displayText = `in the past 30 days`;
                    break;
                case 4:
                    displayText = `in the past 90 days`;
                    break;
                case 5:
                    displayText = `in the past year`;
                    break;
                case 6:
                    displayText = `alumni id cards (All time)`;
                    break;
                default:
                    displayText = "Invalid selection";
            }

            document.getElementById('total_alumni_id_cards').textContent = displayData;
            document.getElementById('alumni_id_cards_footer').textContent = displayText;
        })
        .catch(error => console.error("Error fetching alumni id cards analytics:", error));
}

function updateAnalytics(value) {
    user_analytics(value);
    booking_analytics(value);
    alumni_id_cards_analytics(value);
}

window.onload = function () {
    user_analytics();
    booking_analytics();
    alumni_id_cards_analytics();
}
