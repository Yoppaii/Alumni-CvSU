<?php
require_once('main_db.php');

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin/portal/login-admin");
    exit();
}

// Employment Status
$employment_query = "SELECT 
    CASE 
        WHEN employment_status = 'yes' THEN 'Employed'
        WHEN employment_status = 'no' THEN 'Unemployed'
        ELSE employment_status 
    END as status,
    COUNT(*) as count 
    FROM employment_data 
    GROUP BY employment_status";
$employment_result = $mysqli->query($employment_query);
$employment_data = [];
while($row = $employment_result->fetch_assoc()) {
    $employment_data[] = $row;
}

// Course Distribution
$course_query = "SELECT 
    course,
    COUNT(*) as count 
    FROM personal_info 
    GROUP BY course";
$course_result = $mysqli->query($course_query);
$course_data = [];
while($row = $course_result->fetch_assoc()) {
    $course_data[] = $row;
}

// Job Alignment
$alignment_query = "SELECT 
    CASE 
        WHEN course_related = 'yes' THEN 'Related to Course'
        WHEN course_related = 'no' THEN 'Not Related to Course'
        ELSE course_related 
    END as alignment,
    COUNT(*) as count 
    FROM job_experience 
    GROUP BY course_related";
$alignment_result = $mysqli->query($alignment_query);
$alignment_data = [];
while($row = $alignment_result->fetch_assoc()) {
    $alignment_data[] = $row;
}

// Employment Level
$level_query = "SELECT 
    job_level,
    COUNT(*) as count 
    FROM job_duration 
    GROUP BY job_level";
$level_result = $mysqli->query($level_query);
$level_data = [];
while($row = $level_result->fetch_assoc()) {
    $level_data[] = $row;
}
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Alumni Analytics Overview</h1>
    
    <!-- First Row -->
    <div class="row">
        <!-- Employment Status Chart -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-briefcase me-1"></i>
                    Employment Status
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="employmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Distribution Chart -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-graduation-cap me-1"></i>
                    Course Distribution
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="courseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row">
        <!-- Job-Course Alignment Chart -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-check-circle me-1"></i>
                    Job-Course Alignment
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="alignmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Level Chart -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-layer-group me-1"></i>
                    Employment Level Distribution
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="levelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
}

.card-header {
    background-color: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    padding: 1rem;
    font-weight: 600;
}

.card-body {
    padding: 1rem;
}

.chart-container {
    position: relative;
    height: 350px;
    width: 100%;
}

@media (max-width: 992px) {
    .chart-container {
        height: 300px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartConfig = {
        type: 'pie',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    };

    // Employment Status Chart
    new Chart(document.getElementById('employmentChart').getContext('2d'), {
        ...chartConfig,
        data: {
            labels: <?php echo json_encode(array_column($employment_data, 'status')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($employment_data, 'count')); ?>,
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6']
            }]
        }
    });

    // Course Distribution Chart
    new Chart(document.getElementById('courseChart').getContext('2d'), {
        ...chartConfig,
        data: {
            labels: <?php echo json_encode(array_column($course_data, 'course')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($course_data, 'count')); ?>,
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']
            }]
        }
    });

    // Job Alignment Chart
    new Chart(document.getElementById('alignmentChart').getContext('2d'), {
        ...chartConfig,
        data: {
            labels: <?php echo json_encode(array_column($alignment_data, 'alignment')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($alignment_data, 'count')); ?>,
                backgroundColor: ['#10b981', '#ef4444']
            }]
        }
    });

    // Employment Level Chart
    new Chart(document.getElementById('levelChart').getContext('2d'), {
        ...chartConfig,
        data: {
            labels: <?php echo json_encode(array_column($level_data, 'job_level')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($level_data, 'count')); ?>,
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
        }
    });
});
</script>