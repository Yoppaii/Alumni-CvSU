<!-- Analytics Dashboard Section -->
<div class="analytics-dashboard">
    <!-- Summary Cards -->
    <div class="analytics-summary">
        <div class="summary-card">
            <i class="fas fa-chart-line"></i>
            <div class="summary-content">
                <h3>Monthly Revenue</h3>
                <p id="monthlyRevenue">₱0</p>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-calendar-check"></i>
            <div class="summary-content">
                <h3>Completion Rate</h3>
                <p id="completionRate">0%</p>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-clock"></i>
            <div class="summary-content">
                <h3>Avg Stay Duration</h3>
                <p id="avgStayDuration">0 days</p>
            </div>
        </div>
        <div class="summary-card">
            <i class="fas fa-money-bill-wave"></i>
            <div class="summary-content">
                <h3>Avg Price per Stay</h3>
                <p id="avgPrice">₱0</p>
            </div>
        </div>
    </div>

    <div class="dashboard-row">
        <!-- Booking Trends Chart -->
        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Booking Trends & Revenue</h2>
            </div>
            <div class="analytics-content">
                <canvas id="bookingTrendsChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="dashboard-row">
        <!-- Room Occupancy Chart -->
        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Room Occupancy Distribution</h2>
            </div>
            <div class="analytics-content">
                <canvas id="roomOccupancyChart"></canvas>
            </div>
        </div>
        
        <!-- Booking Status Chart -->
        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Booking Status Distribution</h2>
            </div>
            <div class="analytics-content">
                <canvas id="bookingStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Stay Duration Analysis -->
    <div class="dashboard-row">
        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Stay Duration & Pricing Analysis</h2>
            </div>
            <div class="analytics-content">
                <canvas id="stayAnalysisChart"></canvas>
            </div>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('get_analytics_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSummaryCards(data.data);
                initializeCharts(data.data);
            } else {
                console.error('Failed to load analytics data:', data.message);
            }
        })
        .catch(error => console.error('Error loading analytics:', error));
});

function updateSummaryCards(data) {
    // Get the latest month's data
    const latestMonth = data.trends[data.trends.length - 1] || {};
    const latestStayAnalysis = data.stayAnalysis[data.stayAnalysis.length - 1] || {};
    
    // Update summary cards
    document.getElementById('monthlyRevenue').textContent = 
        `₱${(latestMonth.total_revenue || 0).toLocaleString()}`;
    
    const completionRate = latestMonth.total_bookings ? 
        (latestMonth.completed_bookings / latestMonth.total_bookings * 100).toFixed(1) : 0;
    document.getElementById('completionRate').textContent = `${completionRate}%`;
    
    document.getElementById('avgStayDuration').textContent = 
        `${Math.round(latestStayAnalysis.avg_stay_duration || 0)} days`;
    
    document.getElementById('avgPrice').textContent = 
        `₱${Math.round(latestStayAnalysis.avg_price_per_stay || 0).toLocaleString()}`;
}

function initializeCharts(data) {
    // Booking Trends Chart
    new Chart(document.getElementById('bookingTrendsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: data.trends.map(item => item.month),
            datasets: [{
                label: 'Total Bookings',
                data: data.trends.map(item => item.total_bookings),
                borderColor: '#10b981',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Revenue (₱)',
                data: data.trends.map(item => item.total_revenue),
                borderColor: '#6366f1',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Room Occupancy Chart
    new Chart(document.getElementById('roomOccupancyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: data.roomOccupancy.map(item => `Room ${item.room_number} (${item.occupancy})`),
            datasets: [{
                label: 'Number of Bookings',
                data: data.roomOccupancy.map(item => item.booking_count),
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Booking Status Chart
    new Chart(document.getElementById('bookingStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: data.statuses.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
            datasets: [{
                data: data.statuses.map(item => item.count),
                backgroundColor: [
                    '#10b981', // Completed
                    '#6366f1', // Approved
                    '#f59e0b', // Pending
                    '#ef4444'  // Cancelled
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Stay Analysis Chart
    new Chart(document.getElementById('stayAnalysisChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: data.stayAnalysis.map(item => item.month),
            datasets: [{
                label: 'Avg Stay Duration (Days)',
                data: data.stayAnalysis.map(item => item.avg_stay_duration),
                borderColor: '#10b981',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Avg Price per Stay (₱)',
                data: data.stayAnalysis.map(item => item.avg_price_per_stay),
                borderColor: '#6366f1',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}
</script>