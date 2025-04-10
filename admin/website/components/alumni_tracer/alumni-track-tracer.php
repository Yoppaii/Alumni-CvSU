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
            --primary-color: #10b981;
            --primary-hover: #059669;
            --primary-light: #d1fae5;
            --secondary-color: #64748b;
            --success-color: #22c55e;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --neutral-gray: #6b7280;
            --dark-gray: #4b5563;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Page Header */
        .AT-page-header {

            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #f5f5f5;
            margin-bottom: 30px;
        }

        #AT-Tracer {
            color: var(--cvsu-primary-green);
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--cvsu-light-green);
        }

        .AT-date-time {
            flex-direction: column;
            display: flex;
            color: #666;
            font-size: 1.1em;
            padding-left: 85%;
            margin-top: 50px;
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
            color: var(--primary-color);
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
            max-width: 1600px;
            margin: 0 auto;
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
        }

        .analytics-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
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
            border: 1px solid var(--secondary-color);
            border-radius: 0.375rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.875rem;
            cursor: pointer;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .filter-select:hover,
        .filter-select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 4px var(--primary-light);
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
        }

        /* Reset Button */
        .reset-filter-btn {
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            background-color: var(--neutral-gray);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .reset-filter-btn:hover {
            background-color: var(--dark-gray);
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
            border: 1px solid var(--secondary-color);
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
            background-color: var(--primary-color);
            color: white;
        }

        .button-primary:hover {
            background-color: var(--primary-hover);
        }

        .button-secondary {
            background-color: white;
            color: var(--dark-gray);
            border: 1px solid var(--secondary-color);
        }

        .button-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .analytics-content {
            padding: 0 1.5rem 1.5rem 1.5rem;
            height: 300px;
            position: relative;
        }




        /* Print Report Button */
        #printReport {
            background-color: var(--primary-color);
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

        #printReport:hover {
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
            border: 1px solid var(--secondary-color);
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

            #AT-Tracer {
                font-size: 1.5rem;
                /* Adjust font size for smaller screens */
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

        @media (max-width: 480px) {
            #AT-Tracer {
                font-size: 1.2rem;
                /* Further adjust for mobile */
            }
        }
    </style>

</head>

<body>


    <header class="AT-page-header">
        <h2 id="AT-Tracer">
            <i class="fas fa-user-graduate"></i>
            Alumni Tracer Information
        </h2>
        <div class="AT-date-time"></div>
    </header>

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
                        <input type="number" id="fromYearFilter" min="1900" max="2099" placeholder="e.g. 2005">

                        <label for="toYearFilter">To:</label>
                        <input type="number" id="toYearFilter" min="1900" max="2099" placeholder="e.g. 2023">

                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label" for="employmentStatusFilter">Employment Status:</label>
                    <select id="employmentStatusFilter" class="filter-select"></select>
                </div>
                <div class="filter-actions">
                    <button class="button reset-filter-btn" id="resetFilters">Reset Filters</button>
                </div>
            </div>
        </div>
    </div>
    <div class="dashboard-row">
        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Total Repondents Per Campus</h2>
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
            </div>
            <div class="analytics-content">
                <canvas id="employmentRateChart"></canvas>
            </div>
        </div>

        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Course Relevance and Salary Range</h2>
            </div>
            <div class="analytics-content">
                <canvas id="jobRelevanceSalaryChart"></canvas>
            </div>
        </div>
        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Job Search Method</h2>
            </div>
            <div class="analytics-content">
                <canvas id="jobSearchChart"></canvas>
            </div>
        </div>

    </div>
    <div class="dashboard-row">
        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Time to Land First Job</h2>
            </div>
            <div class="analytics-content">
                <canvas id="employmentTimeChart"></canvas>
            </div>
        </div>
        <div class="analytics-card">
            <div class="analytics-header">
                <h2>Work Location Type</h2>
            </div>
            <div class="analytics-content">
                <canvas id="employmentLocationChart"></canvas>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Script Settings  -->
    <script>
        // Update date-time
        function updateDateTime() {
            const now = new Date();
            const dateTimeString = now.toLocaleString();
            document.querySelector('.AT-date-time').textContent = dateTimeString;
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>


    <script src="/Alumni-CvSU/admin/website/script/tracer_analytics.js"></script>

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
</body>

</html>