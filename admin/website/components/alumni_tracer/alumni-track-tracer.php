<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Tracer</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

        .AT-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #f5f5f5;
            margin-bottom: 30px;
        }

        .AT-page-header h1 {
            color: #006400;
            font-size: 1.8em;
            margin: 0;
        }

        .AT-date-time {
            color: #666;
            font-size: 1.1em;
        }

        .AT-chart-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .AT-chart-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 30px;
        }

        .AT-chart-wrapper {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .AT-chart-wrapper h3 {
            color: #006400;
            margin: 0 0 20px 0;
            font-size: 1.3em;
            text-align: center;
        }

        .AT-chart-summary {
            display: flex;
            gap: 20px;
        }

        .AT-chart {
            flex: 1;
            height: 300px;
        }

        .AT-summary {
            width: 150px;
            padding: 10px;
            background: #f8f8f8;
            border-radius: 6px;
        }

        .AT-summary p {
            margin: 8px 0;
            font-size: 0.9em;
            color: #333;
        }

        canvas {
            width: 100% !important;
            height: 300px !important;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .AT-chart-row {
                gap: 20px;
            }
            
            .AT-chart-wrapper {
                padding: 15px;
            }
        }

        @media (max-width: 992px) {
            .AT-chart-row {
                grid-template-columns: 1fr;
            }

            .AT-chart {
                height: 250px;
            }

            canvas {
                height: 250px !important;
            }
        }

        @media (max-width: 768px) {
            .AT-alumni-hero-content h1 {
                font-size: 2em;
            }

            .AT-chart-summary {
                flex-direction: column;
            }

            .AT-summary {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .AT-page-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .AT-alumni-hero-content h1 {
                font-size: 1.8em;
            }

            .AT-chart-wrapper h3 {
                font-size: 1.1em;
            }
        }

        :root {
            --AT-primary: #006400;
            --AT-secondary: #004d00;
            --AT-success: #1cc88a;
            --AT-info: #36b9cc;
            --AT-warning: #f6c23e;
            --AT-danger: #e74a3b;
            --AT-light: #f8f9fc;
            --AT-dark: #5a5c69;
        }
    </style>
</head>
<body>

    <header class="AT-page-header">
        <h1>Alumni Tracer Information</h1>
        <div class="AT-date-time"></div>
    </header>

    <div class="AT-chart-container">
        <!-- Gender Distribution Row -->
        <div class="AT-chart-row">
            <div class="AT-chart-wrapper">
                <h3>Gender Distribution</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="genderRespondentChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Male: 50</p>
                        <p>Female: 45</p>
                        <p>Other: 5</p>
                    </div>
                </div>
            </div>
            <div class="AT-chart-wrapper">
                <h3>Civil Status</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="civilStatusChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Single: 60</p>
                        <p>Married: 30</p>
                        <p>Divorced: 5</p>
                        <p>Widowed: 5</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year Graduated Row -->
        <div class="AT-chart-row">
            <div class="AT-chart-wrapper">
                <h3>Year Graduated</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="yearGraduatedChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>2010: 20</p>
                        <p>2011: 25</p>
                        <p>2012: 30</p>
                        <p>2013: 25</p>
                    </div>
                </div>
            </div>
            <div class="AT-chart-wrapper">
                <h3>Program Distribution</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="programRespondentsChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Program A: 40</p>
                        <p>Program B: 35</p>
                        <p>Program C: 25</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Post Graduate Studies Row -->
        <div class="AT-chart-row">
            <div class="AT-chart-wrapper">
                <h3>Post Graduate Studies</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="postGraduateStudiesChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Yes: 30</p>
                        <p>No: 70</p>
                    </div>
                </div>
            </div>
            <div class="AT-chart-wrapper">
                <h3>Employment Status</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="statusEmploymentChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Employed: 80</p>
                        <p>Unemployed: 20</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Length of Stay Row -->
        <div class="AT-chart-row">
            <div class="AT-chart-wrapper">
                <h3>Length of Stay in Present Job</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="lengthOfStayChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Less than 1 year: 20</p>
                        <p>1-3 years: 50</p>
                        <p>More than 3 years: 30</p>
                    </div>
                </div>
            </div>
            <div class="AT-chart-wrapper">
                <h3>Present Employment Tenure</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="presentTenureChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Permanent: 40</p>
                        <p>Contractual: 30</p>
                        <p>Temporary: 30</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Relation Row -->
        <div class="AT-chart-row">
            <div class="AT-chart-wrapper">
                <h3>Employment Relation to Program</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="relationEmploymentChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Related: 70</p>
                        <p>Not Related: 30</p>
                    </div>
                </div>
            </div>
            <div class="AT-chart-wrapper">
                <h3>Employment Location</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="locationEmploymentChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Local: 80</p>
                        <p>Abroad: 20</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- First Job Row -->
        <div class="AT-chart-row">
            <div class="AT-chart-wrapper">
                <h3>First Job Status</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="firstJobChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Yes: 60</p>
                        <p>No: 40</p>
                    </div>
                </div>
            </div>
            <div class="AT-chart-wrapper">
                <h3>College Experience Relevance</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="collegeExperienceChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Yes: 85</p>
                        <p>No: 15</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time to First Job Row -->
        <div class="AT-chart-row">
            <div class="AT-chart-wrapper">
                <h3>Time to First Job</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="lengthOfTimeChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Less than 6 months: 50</p>
                        <p>6-12 months: 30</p>
                        <p>More than 1 year: 20</p>
                    </div>
                </div>
            </div>
            <div class="AT-chart-wrapper">
                <h3>First Job Finding Method</h3>
                <div class="AT-chart-summary">
                    <div class="AT-chart">
                        <canvas id="methodOfFindingFirstJobChart"></canvas>
                    </div>
                    <div class="AT-summary">
                        <p>Online Job Portal: 12</p>
                        <p>Campus Placement: 19</p>
                        <p>Referral: 3</p>
                        <p>Direct Application: 5</p>
                        <p>Others: 2</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    // Update date-time
    function updateDateTime() {
        const now = new Date();
        const dateTimeString = now.toLocaleString();
        document.querySelector('.AT-date-time').textContent = dateTimeString;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // Chart colors
    const colors = {
        primary: '#006400',
        secondary: '#004d00',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b'
    };

    // Common chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 10,
                    font: { size: 12 }
                }
            }
        }
    };

    // Bar chart specific options
    const barOptions = {
        ...commonOptions,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { font: { size: 12 } }
            },
            x: {
                ticks: { font: { size: 12 } }
            }
        }
    };

    // Line chart specific options
    const lineOptions = {
        ...barOptions,
        elements: {
            line: {
                tension: 0.3
            },
            point: {
                radius: 4
            }
        }
    };

    window.onload = function() {
        console.log('Initializing charts...');
        
        // Gender Distribution Chart
        new Chart(document.getElementById('genderRespondentChart'), {
            type: 'pie',
            data: {
                labels: ['Male', 'Female', 'Other'],
                datasets: [{
                    data: [50, 45, 5],
                    backgroundColor: [colors.primary, colors.success, colors.info]
                }]
            },
            options: commonOptions
        });

        // Civil Status Chart
        new Chart(document.getElementById('civilStatusChart'), {
            type: 'bar',
            data: {
                labels: ['Single', 'Married', 'Divorced', 'Widowed'],
                datasets: [{
                    label: 'Civil Status',
                    data: [60, 30, 5, 5],
                    backgroundColor: colors.primary
                }]
            },
            options: barOptions
        });

        // Year Graduated Chart
        new Chart(document.getElementById('yearGraduatedChart'), {
            type: 'line',
            data: {
                labels: ['2010', '2011', '2012', '2013'],
                datasets: [{
                    label: 'Graduates',
                    data: [20, 25, 30, 25],
                    borderColor: colors.success,
                    backgroundColor: colors.success
                }]
            },
            options: lineOptions
        });

        // Program Distribution Chart
        new Chart(document.getElementById('programRespondentsChart'), {
            type: 'bar',
            data: {
                labels: ['Program A', 'Program B', 'Program C'],
                datasets: [{
                    label: 'Students',
                    data: [40, 35, 25],
                    backgroundColor: colors.info
                }]
            },
            options: barOptions
        });

        // Post Graduate Studies Chart
        new Chart(document.getElementById('postGraduateStudiesChart'), {
            type: 'pie',
            data: {
                labels: ['Yes', 'No'],
                datasets: [{
                    data: [30, 70],
                    backgroundColor: [colors.primary, colors.danger]
                }]
            },
            options: commonOptions
        });

        // Employment Status Chart
        new Chart(document.getElementById('statusEmploymentChart'), {
            type: 'bar',
            data: {
                labels: ['Employed', 'Unemployed'],
                datasets: [{
                    label: 'Status',
                    data: [80, 20],
                    backgroundColor: colors.success
                }]
            },
            options: barOptions
        });

        // Length of Stay Chart
        new Chart(document.getElementById('lengthOfStayChart'), {
            type: 'bar',
            data: {
                labels: ['<1 year', '1-3 years', '>3 years'],
                datasets: [{
                    label: 'Length of Stay',
                    data: [20, 50, 30],
                    backgroundColor: colors.primary
                }]
            },
            options: barOptions
        });

        // Present Tenure Chart
        new Chart(document.getElementById('presentTenureChart'), {
            type: 'pie',
            data: {
                labels: ['Permanent', 'Contractual', 'Temporary'],
                datasets: [{
                    data: [40, 30, 30],
                    backgroundColor: [colors.success, colors.info, colors.primary]
                }]
            },
            options: commonOptions
        });

        // Relation Employment Chart
        new Chart(document.getElementById('relationEmploymentChart'), {
            type: 'pie',
            data: {
                labels: ['Related', 'Not Related'],
                datasets: [{
                    data: [70, 30],
                    backgroundColor: [colors.primary, colors.danger]
                }]
            },
            options: commonOptions
        });

        // Location Employment Chart
        new Chart(document.getElementById('locationEmploymentChart'), {
            type: 'bar',
            data: {
                labels: ['Local', 'Abroad'],
                datasets: [{
                    label: 'Location',
                    data: [80, 20],
                    backgroundColor: colors.success
                }]
            },
            options: barOptions
        });

        // First Job Chart
        new Chart(document.getElementById('firstJobChart'), {
            type: 'pie',
            data: {
                labels: ['Yes', 'No'],
                datasets: [{
                    data: [60, 40],
                    backgroundColor: [colors.primary, colors.danger]
                }]
            },
            options: commonOptions
        });

        // College Experience Chart
        new Chart(document.getElementById('collegeExperienceChart'), {
            type: 'bar',
            data: {
                labels: ['Very Helpful', 'Helpful', 'Neutral', 'Not Helpful', 'Not at All'],
                datasets: [{
                    label: 'Experience',
                    data: [40, 30, 20, 5, 5],
                    backgroundColor: colors.primary
                }]
            },
            options: barOptions
        });

        // Length of Time Chart
        new Chart(document.getElementById('lengthOfTimeChart'), {
            type: 'line',
            data: {
                labels: ['<1 month', '1-3 months', '3-6 months', '6-12 months', '>1 year'],
                datasets: [{
                    label: 'Time to First Job',
                    data: [10, 20, 30, 25, 15],
                    borderColor: colors.primary,
                    backgroundColor: colors.primary
                }]
            },
            options: lineOptions
        });

        // Method of Finding First Job Chart
        new Chart(document.getElementById('methodOfFindingFirstJobChart'), {
            type: 'bar',
            data: {
                labels: ['Online Portal', 'Campus Placement', 'Referral', 'Direct Application', 'Others'],
                datasets: [{
                    label: 'Method',
                    data: [12, 19, 3, 5, 2],
                    backgroundColor: [
                        colors.primary,
                        colors.success,
                        colors.info,
                        colors.warning,
                        colors.secondary
                    ]
                }]
            },
            options: barOptions
        });
    };
</script>
</body>
</html>