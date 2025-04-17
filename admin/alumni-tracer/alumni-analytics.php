<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Tracer</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Root Variables - Color Theme */
        :root {
            --primary: #10b981;
            --primary-hover: #0d8c65;
            --secondary: #64748b;
            --success: #22c55e;
            --danger: #dc3545;
            --warning: #f59e0b;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --neutral-gray: #6c757d;
            --medium-gray: #e9ecef;
            --dark-gray: #495057;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Page Header */
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

        /* Dashboard Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .dashboard-title {
            font-size: 24px;
            font-weight: 700;
        }

        /* Analytics Dashboard */
        .analytics-dashboard {
            margin: 2rem 0;
        }

        /* Summary Cards */
        .analytics-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: var(--bg-primary);
            border-radius: 0.75rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--shadow);
        }

        .summary-card i {
            font-size: 2rem;
            color: var(--primary);
        }

        .summary-content h3 {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin: 0;
        }

        .summary-content p {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-top: 0.25rem;
        }

        /* Dashboard Layout */
        .dashboard-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Analytics Cards */
        .analytics-card {
            background: var(--bg-primary);
            border-radius: 0.75rem;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .analytics-header {
            padding: 1rem;
            border-bottom: 1px solid var(--bg-secondary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .analytics-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .analytics-content {
            padding: 0 1.5rem 1.5rem 1.5rem;
            height: 300px;
            position: relative;
        }

        /* Filters & Dashboard */
        .filter-bar {
            background-color: white;
            padding: 16px 24px;
            margin-bottom: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
        }

        .filters-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: 0.5rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            flex: 1;
            min-width: 150px;
        }

        .filter-select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--secondary);
            border-radius: 0.375rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.875rem;
            cursor: pointer;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .filter-select:hover,
        .filter-select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 4px var(--primary-light);
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
        }

        .year-range {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .year-range input {
            flex: 1;
            width: calc(50% - 15px);
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--secondary);
            border-radius: 0.375rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.875rem;
            cursor: pointer;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .year-range span {
            color: #777;
        }

        /* Toggle Switch */
        .toggle-report {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .toggle-input {
            appearance: none;
            width: 36px;
            height: 20px;
            background-color: var(--medium-gray);
            border-radius: 20px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .toggle-input:checked {
            background-color: var(--primary);
        }

        .toggle-input:before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: white;
            top: 2px;
            left: 2px;
            transition: transform 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .toggle-input:checked:before {
            transform: translateX(16px);
        }

        .toggle-label {
            font-size: 1rem;
            color: var(--dark-gray);
        }

        /* Report Controls */
        .report-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 0.85rem;
        }

        /* Buttons */
        .button {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .button-primary {
            background-color: var(--primary);
            color: white;
        }

        .button-primary:hover {
            background-color: var(--primary-hover);
        }

        .button-secondary {
            background-color: white;
            color: var(--dark-gray);
            border: 1px solid var(--secondary);
        }

        .button-secondary:hover {
            background-color: var(--secondary);
            color: white;
        }

        .reset-filter-btn {
            background-color: var(--neutral-gray);
            color: white;
        }

        .reset-filter-btn:hover {
            background-color: var(--dark-gray);
        }

        .select-all-btn {
            background-color: var(--primary);
            color: white;
        }

        .select-all-btn:hover {
            background-color: #0d8c65;
        }

        .select-none-btn {
            background-color: var(--danger);
            color: white;
        }

        .select-none-btn:hover {
            background-color: #c82333;
        }

        /* Print Report Button */
        #printReportTracer {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        #printReportTracer:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Dark Mode Support */
        [data-theme="dark"] .analytics-card,
        [data-theme="dark"] .summary-card {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] .filters-container {
            background: var(--bg-primary);
            border: 1px solid var(--secondary);
        }

        [data-theme="dark"] .filter-select {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .filters-container {
                flex-direction: column;
            }

            .analytics-summary {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-row {
                padding: 1rem;
                grid-template-columns: 1fr;
            }

            .analytics-content {
                padding: 1rem;
                height: 250px;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Analytics</h1>
            <div class="report-controls">
                <button id="printReportTracer">Print Report</button>
            </div>
        </div>
        <div class="dashboard-row">
            <div class="analytics-card">
                <div class="filter-bar">
                    <div class="filter-group">
                        <label class="filter-label" for="campusFilter">Campus:</label>
                        <select id="campusFilter" class="filter-select"></select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label" for="courseFilter">Course:</label>
                        <select id="courseFilter" class="filter-select"></select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Graduation Year:</label>
                        <div class="year-range">
                            <label for="fromYearFilter">From:</label>
                            <input class="year-input" type="number" id="fromYearFilter" min="1900" max="2099" placeholder="e.g. 2005">

                            <label for="toYearFilter">To:</label>
                            <input class="year-input" type="number" id="toYearFilter" min="1900" max="2099" placeholder="e.g. 2023">
                        </div>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label" for="employmentStatusFilter">Employment Status:</label>
                        <select id="employmentStatusFilter" class="filter-select"></select>
                    </div>
                    <div class="filter-actions">
                        <button class="button reset-filter-btn" id="resetFilters">Reset Filters</button>
                        <button class="button select-all-btn" id="selectAllCharts">Select All</button>
                        <button class="button select-none-btn" id="deselectAllCharts">Deselect All</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-row">
            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Total Respondents Per Campus</h2>
                    <label class="toggle-report">
                        <input type="checkbox" class="toggle-input report-checkbox" value="totalGraduates" checked>
                        <span class="toggle-label">Include in Report</span>
                    </label>
                </div>
                <div class="analytics-content">
                    <canvas id="totalGraduatesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="dashboard-row">
            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Employment Rate</h2>
                    <label class="toggle-report">
                        <input type="checkbox" class="toggle-input report-checkbox" value="employmentRate" checked>
                        <span class="toggle-label">Include in Report</span>
                    </label>
                </div>
                <div class="analytics-content">
                    <canvas id="employmentRateChart"></canvas>
                </div>
            </div>
            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Work Location</h2>
                    <label class="toggle-report">
                        <input type="checkbox" class="toggle-input report-checkbox" value="workLocations" checked>
                        <span class="toggle-label">Include in Report</span>
                    </label>
                </div>
                <div class="analytics-content">
                    <canvas id="employmentLocationChart"></canvas>
                </div>
            </div>

            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Job Search Method</h2>
                    <label class="toggle-report">
                        <input type="checkbox" class="toggle-input report-checkbox" value="jobSearchMethod" checked>
                        <span class="toggle-label">Include in Report</span>
                    </label>
                </div>
                <div class="analytics-content">
                    <canvas id="jobSearchChart"></canvas>
                </div>
            </div>

        </div>
        <div class="dashboard-row">
            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Time to Land First Job </h2>
                    <label class="toggle-report">
                        <input type="checkbox" class="toggle-input report-checkbox" value="timeToLandFirstJob" checked>
                        <span class="toggle-label">Include in Report</span>
                    </label>
                </div>
                <div class="analytics-content">
                    <canvas id="employmentTimeChart"></canvas>
                </div>
            </div>
            <div class="analytics-card">
                <div class="analytics-header">
                    <h2>Course Relevance Impact on Salary</h2>
                    <label class="toggle-report">
                        <input type="checkbox" class="toggle-input report-checkbox" value="courseRelevanceImpactOnSalary" checked>
                        <span class="toggle-label">Include in Report</span>
                    </label>
                </div>
                <div class="analytics-content">
                    <canvas id="courseRelevanceSalaryChart"></canvas>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="/Alumni-CvSU/admin/script/generate_report_tracer.js"></script>
    <script src="/Alumni-CvSU/admin/script/tracer_analytics.js"></script>


    <!-- Populate Filter -->
    <script>
        function fetchCampuses() {
            fetch(`/Alumni-CvSU/admin/website/ajax/get_campus_list.php`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const campusFilter = document.getElementById("campusFilter");
                    campusFilter.innerHTML = "<option value=''>All Campus </option>";

                    data.forEach(campus => {
                        // Shorten the campus name if it contains "Cavite State University - "
                        let shortenedCampus = campus;
                        if (campus.includes("Cavite State University - ")) {
                            shortenedCampus = campus.replace("Cavite State University - ", "");
                        }

                        const option = document.createElement("option");
                        option.value = campus;
                        option.textContent = shortenedCampus;
                        campusFilter.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error("Fetch error:", error.message);
                });
        }

        function fetchCourses() {
            fetch(`/Alumni-CvSU/admin/website/ajax/get_course_list.php`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const courseFilter = document.getElementById("courseFilter");
                    courseFilter.innerHTML = "<option value=''>All Course</option>";
                    data.forEach(course => {
                        const option = document.createElement("option");
                        option.value = course;
                        option.textContent = course;
                        courseFilter.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error("Fetch error:", error.message);
                });
        }

        function fetchEmploymentStatus() {
            fetch(`/Alumni-CvSU/admin/website/ajax/get_employment_status_list.php`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const employmentStatusFilter = document.getElementById("employmentStatusFilter");
                    employmentStatusFilter.innerHTML = "<option value=''>All Status</option>";
                    data.forEach(employmentStatus => {
                        const option = document.createElement("option");
                        option.value = employmentStatus;
                        option.textContent = employmentStatus;
                        employmentStatusFilter.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error("Fetch error:", error.message);
                });
        }

        fetchCampuses();
        fetchCourses();
        fetchEmploymentStatus();
    </script>

    <!-- Reset Filter -->
    <script>
        document.getElementById("resetFilters").addEventListener("click", function() {
            // Reset all filter dropdowns to default (assuming first option is default)
            document.querySelectorAll(".filter-select").forEach(select => {
                if (select.id === "yearFilter") {
                    select.selectedIndex = 1;
                } else {
                    select.selectedIndex = 0;
                }
            });

            // Empty all inputs
            document.querySelectorAll(".year-input").forEach(year => {
                year.value = " ";
            });

            // Reset all toggles
            document.querySelectorAll(".toggle-input").forEach(toggle => {
                toggle.checked = true;
            });

            // Reset all checkboxes
            document.querySelectorAll(".report-checkbox-container input[type='checkbox']").forEach(checkbox => {
                checkbox.checked = true;
            });

            updateChart();
        });
    </script>

    <!-- Select and Deselect -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set up Select All / Deselect All buttons
            document.getElementById('selectAllCharts').addEventListener('click', function() {
                document.querySelectorAll('.report-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
            });

            document.getElementById('deselectAllCharts').addEventListener('click', function() {
                document.querySelectorAll('.report-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
            });
        });
    </script>
</body>

</html>