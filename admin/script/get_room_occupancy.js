document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById('roomOccupancyChart');
    if (!chartElement) {
        console.error("Error: Chart element not found!");
        return;
    }

    fetch('/Alumni-CvSU/admin/analytics/get_room_occupancy.php')
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

            const labels = data.map(item => `Room ${item.room_number}`);
            const values = data.map(item => item.total_bookings);
            const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

            const ctx = chartElement.getContext('2d');
            new Chart(ctx, {
                type: 'bar', // Change to 'pie' for a pie chart
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Room Occupancy',
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Room Number'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Total Bookings'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Fetch error:", error.message));
});