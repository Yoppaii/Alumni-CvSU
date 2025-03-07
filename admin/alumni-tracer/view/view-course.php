<?php
//view-course.php
require_once 'main_db.php';

// Update the SQL query to group by course instead of residence
$query = "SELECT course, COUNT(*) as count 
          FROM personal_info 
          GROUP BY course 
          ORDER BY count DESC";
$result = $mysqli->query($query);

$labels = [];
$data = [];
$colors = [
    'rgba(54, 162, 235, 0.8)',   
    'rgba(255, 99, 132, 0.8)',
    'rgba(75, 192, 192, 0.8)' 
];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['course']; // Change to course
    $data[] = $row['count'];
    $total += $row['count'];
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<div class="course-distribution-analytics">

    <div class="analytics-header">
        <a href="?section=charts" class="analytics-back-btn">
            <i class="fas fa-arrow-left"></i> Back to Survey Results
        </a>
        <h2 class="analytics-title">Course Distribution Analysis</h2> <!-- Change title -->
    </div>

    <div class="chart-tabs">
        <button class="tab-button active" data-chart="pie">Pie Chart</button>
        <button class="tab-button" data-chart="bar">Bar Graph</button>
        <button class="tab-button" data-chart="line">Line Graph</button>
    </div>
    
    <div class="analytics-content">
        <div class="analytics-chart-container">
            <canvas id="courseDistribution"></canvas> <!-- Change canvas id -->
        </div>

        <div class="analytics-stats-container">
            <div class="analytics-total-card">
                <h3>Total Respondents</h3>
                <div class="total-number"><?php echo $total; ?></div>
            </div>

            <div class="analytics-stats-grid">
                <?php
                foreach ($labels as $index => $label) {
                    $percentage = number_format(($data[$index] / $total) * 100, 1);
                    echo "<div class='analytics-stat-card'>";
                    echo "<h3>" . htmlspecialchars($label) . "</h3>";
                    echo "<div class='stat-number'>" . $data[$index] . "</div>";
                    echo "<div class='stat-percentage'>" . $percentage . "% of total</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
<style>
    .course-distribution-analytics {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .chart-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .tab-button {
        padding: 0.75rem 1.5rem;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
        color: var(--text-primary);
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .tab-button:hover {
        background: var(--bg-secondary);
    }

    .tab-button.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .analytics-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        gap: 1rem;
    }

    .analytics-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: start;
    }
    .analytics-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        gap: 1rem;
    }

    .analytics-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: start;
    }

    .analytics-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: var(--radius-md);
        transition: var(--transition);
    }

    .analytics-back-btn:hover {
        background-color: var(--primary-hover);
    }

    .analytics-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .analytics-chart-container {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        height: 100%;
        min-height: 500px;
        box-shadow: var(--shadow-sm);
    }

    .analytics-stats-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .analytics-total-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        text-align: center;
        box-shadow: var(--shadow-sm);
    }

    .analytics-total-card h3 {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0 0 0.5rem 0;
    }

    .total-number {
        font-size: 2rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .analytics-stats-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .analytics-stat-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        text-align: center;
        box-shadow: var(--shadow-sm);
    }

    .analytics-stat-card h3 {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0;
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0.75rem 0;
    }

    .stat-percentage {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    [data-theme="dark"] .analytics-chart-container,
    [data-theme="dark"] .analytics-stat-card,
    [data-theme="dark"] .analytics-total-card {
        background: var(--bg-primary);
        border-color: var(--border-color);
    }

    @media (max-width: 992px) {
        .analytics-content {
            grid-template-columns: 1fr;
        }
        
        .analytics-chart-container {
            min-height: 400px;
        }
    }

    @media (max-width: 768px) {
        .analytics-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .analytics-chart-container {
            min-height: 300px;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentChart = null;

        function createChart(type) {
            const ctx = document.getElementById('courseDistribution').getContext('2d'); // Change canvas id

            if (currentChart) {
                currentChart.destroy();
            }

            const datasetConfig = {
                label: 'Distribution',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: <?php echo json_encode($colors); ?>,
                borderColor: type === 'line' ? <?php echo json_encode($colors); ?> : 'white',
                borderWidth: type === 'line' ? 3 : 2
            };

            if (type === 'line') {
                datasetConfig.tension = 0.3;
                datasetConfig.fill = false;
                datasetConfig.pointBackgroundColor = <?php echo json_encode($colors); ?>;
                datasetConfig.pointRadius = 6;
            }

            const config = {
                type: type,
                data: {
                    labels: <?php echo json_encode($labels); ?>, // Course names
                    datasets: [datasetConfig]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: type === 'pie' ? 'bottom' : 'top',
                            display: type === 'pie',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 14
                                },
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary')
                            }
                        },
                        title: {
                            display: true,
                            text: 'Distribution by Course', // Change title
                            font: {
                                size: 18
                            },
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary')
                        }
                    }
                }
            };

            if (type !== 'pie') {
                config.options.scales = {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--border-color')
                        },
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary')
                        }
                    },
                    x: {
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--border-color')
                        },
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary')
                        }
                    }
                };
            }

            currentChart = new Chart(ctx, config);
        }

        createChart('pie');

        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                e.target.classList.add('active');

                createChart(e.target.dataset.chart);
            });
        });
    });
</script>