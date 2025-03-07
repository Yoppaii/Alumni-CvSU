document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById('bookingStatusChart');
    if (!chartElement) {
        console.error("Error: Chart element not found!");
        return;
    }

    fetch('/Alumni-CvSU/admin/analytics/get_booking_status.php')
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

            const labels = data.map(item => item.status);
            const values = data.map(item => item.total_bookings);
            const colors = ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF', '#FF9F40'];

            const ctx = chartElement.getContext('2d');
            new Chart(ctx, {
                type: 'doughnut', // You can also use 'pie' if preferred
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Booking Status',
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
                    }
                }
            });
        })
        .catch(error => console.error("Fetch error:", error.message));
});