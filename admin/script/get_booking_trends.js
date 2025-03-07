document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById('bookingChart');
    if (!chartElement) {
        console.error("Error: Chart element not found!");
        return;
    }

    fetch('/Alumni-CvSU/admin/analytics/get_booking_trends.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                throw new Error("Invalid or empty data received.");
            }

            const labels = data.map(item => item.month);
            const bookingCounts = data.map(item => item.total_bookings);
            const revenueValues = data.map(item => item.total_revenue);

            const ctx = chartElement.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Bookings',
                        data: bookingCounts,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        borderWidth: 2,
                        tension: 0.4 // Spline effect
                    },
                    {
                        label: 'Total Revenue',
                        data: revenueValues,
                        borderColor: 'green',
                        backgroundColor: 'rgba(0, 255, 0, 0.2)',
                        borderWidth: 2,
                        tension: 0.4 // Spline effect
                    }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Count & Revenue'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Fetch error:", error.message));
});