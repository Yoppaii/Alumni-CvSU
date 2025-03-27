document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById('employmentChart');
    const summaryElement = document.querySelector('.AT-summary p');
    const filterContainer = document.getElementById('filterContainer');

    // Current filter state
    let currentFilters = {
        course: '',
        campus: '',
        startYear: '',
        endYear: '',
        employmentStatus: '',
        relevance: '',
        business: '',
        timeToLand: '',
        jobFindingMethod: ''
    };

    // Store total graduates count (unfiltered)
    let totalGraduatesUnfiltered = 0;

    if (!chartElement) {
        console.error("Error: Chart element not found!");
        return;
    }

    // Initialize the chart
    let employmentChart;

    // Color palette for chart (more visually appealing)
    const colorPalette = [
        'rgba(54, 162, 235, 0.8)',   // Blue
        'rgba(75, 192, 192, 0.8)',   // Teal
        'rgba(255, 159, 64, 0.8)',   // Orange
        'rgba(153, 102, 255, 0.8)',  // Purple
        'rgba(255, 99, 132, 0.8)',   // Pink
        'rgba(255, 205, 86, 0.8)',   // Yellow
    ];

    // Chart options for grouped bar chart with enhanced visuals
    const groupedBarOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        weight: 'bold'
                    }
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    callback: function (value) {
                        return value;
                    }
                },
                grid: {
                    color: 'rgba(200, 200, 200, 0.3)'
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    padding: 20,
                    boxWidth: 15,
                    usePointStyle: true,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                padding: 15,
                cornerRadius: 6,
                callbacks: {
                    label: function (context) {
                        const value = context.raw;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                        return `${context.dataset.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        barPercentage: 0.8,
        categoryPercentage: 0.9,
        animation: {
            duration: 1000,
            easing: 'easeOutQuart'
        }
    };

    // Function to load data and update the chart
    window.loadChartData = function (isInitialLoad = false) {
        // Build query string from filters
        const queryParams = Object.entries(currentFilters)
            .filter(([_, value]) => value !== '')
            .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
            .join('&');

        // For initial load, we add a parameter to get unfiltered count
        const urlParams = isInitialLoad ?
            `${queryParams}&getUnfilteredTotal=true` :
            queryParams;

        const url = `/Alumni-CvSU/admin/website/ajax/analytics.php?${urlParams}`;

        // Add loading indicator
        const chartContainer = chartElement.parentElement;
        chartContainer.classList.add('loading');
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'chart-loading';
        loadingIndicator.innerHTML = '<div class="spinner"></div><p>Loading data...</p>';
        chartContainer.appendChild(loadingIndicator);

        // Fetch from the server
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Remove loading indicator
                chartContainer.classList.remove('loading');
                if (chartContainer.querySelector('.chart-loading')) {
                    chartContainer.removeChild(loadingIndicator);
                }

                // If this is initial load, store the unfiltered total
                if (isInitialLoad && data.unfilteredTotalGraduates) {
                    totalGraduatesUnfiltered = data.unfilteredTotalGraduates;
                }

                processData(data);
            })
            .catch(error => {
                console.warn('Error fetching data:', error);
                // Remove loading indicator on error
                chartContainer.classList.remove('loading');
                if (chartContainer.querySelector('.chart-loading')) {
                    chartContainer.removeChild(loadingIndicator);
                }
                // Show error message
                const errorMsg = document.createElement('div');
                errorMsg.className = 'chart-error';
                errorMsg.innerHTML = '<p>Failed to load data. Please try again.</p>';
                chartContainer.appendChild(errorMsg);
                setTimeout(() => {
                    if (chartContainer.querySelector('.chart-error')) {
                        chartContainer.removeChild(errorMsg);
                    }
                }, 3000);
            });
    };

    // Process and display data
    function processData(data) {
        console.log("Processing Data:", data);

        // Update filter dropdowns if this is the first load
        if (!document.querySelector('.filter-dropdown')) {
            populateFilterDropdowns(data.filterOptions);
        }

        // Get the filtered total graduates
        const filteredTotalGraduates = data.totalGraduates || data.datasets.reduce((acc, dataset) =>
            acc + dataset.data.reduce((sum, count) => sum + count, 0), 0);

        // If unfiltered total hasn't been set yet, use current total
        if (totalGraduatesUnfiltered === 0) {
            totalGraduatesUnfiltered = filteredTotalGraduates;
        }

        // Check if we're dealing with filtered data
        const isFiltered = Object.values(currentFilters).some(value => value !== '');

        // Use appropriate total for calculations
        const displayTotal = filteredTotalGraduates;
        const percentageBase = isFiltered ? totalGraduatesUnfiltered : filteredTotalGraduates;

        // Format the summary into a readable string with enhanced styling
        let summaryHTML = `
            <div class="summary-header">
                <h4>Summary</h4>
            </div>
        `;

        // Add total graduates first with proper percentage
        const totalPercentage = percentageBase > 0 ?
            ((displayTotal / percentageBase) * 100).toFixed(1) :
            '0.0';

        summaryHTML += `
            <div class="summary-card">
                <div class="status-label">Total Graduates</div>
                <div class="status-count">${displayTotal}</div>
                <div class="status-percentage">${totalPercentage}%</div>
            </div>
        `;

        summaryElement.innerHTML = summaryHTML;

        // Enhance the datasets with better colors
        const enhancedDatasets = data.datasets.map((dataset, index) => ({
            ...dataset,
            backgroundColor: colorPalette[index % colorPalette.length],
            borderColor: colorPalette[index % colorPalette.length].replace('0.8', '1'),
            borderWidth: 1,
            borderRadius: 4,
            hoverBackgroundColor: colorPalette[index % colorPalette.length].replace('0.8', '0.9'),
            hoverBorderColor: colorPalette[index % colorPalette.length].replace('0.8', '1'),
            hoverBorderWidth: 2
        }));

        // If chart already exists, update it
        if (employmentChart) {
            employmentChart.data.labels = data.labels;
            employmentChart.data.datasets = enhancedDatasets;
            employmentChart.update();
        } else {
            // Create a new chart - as a grouped bar chart
            employmentChart = new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: enhancedDatasets
                },
                options: groupedBarOptions
            });
        }
    }

    // Function to populate filter dropdowns
    function populateFilterDropdowns(filterOptions) {
        // Create filter UI with improved styling
        const filterHTML = `
            <div class="filter-section primary-filters">
                <h4>Primary Filters</h4>
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="courseFilter">Course:</label>
                        <select id="courseFilter" class="filter-dropdown">
                            <option value="">All Courses</option>
                            ${filterOptions.courses.map(course => `<option value="${course}">${course}</option>`).join('')}
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="campusFilter">Campus:</label>
                        <select id="campusFilter" class="filter-dropdown">
                            <option value="">All Campuses</option>
                            ${filterOptions.campuses.map(campus => `<option value="${campus}">${campus}</option>`).join('')}
                        </select>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Graduation Year:</label>
                        <div class="year-range">
                            <input type="number" id="startYearFilter" placeholder="From" min="1990" max="2025">
                            <span>to</span>
                            <input type="number" id="endYearFilter" placeholder="To" min="1990" max="2025">
                        </div>
                    </div>
                    <div class="filter-group">
                        <label for="employmentStatusFilter">Employment Status:</label>
                        <select id="employmentStatusFilter" class="filter-dropdown">
                            <option value="">All Statuses</option>
                            ${filterOptions.employmentStatuses ? filterOptions.employmentStatuses.map(employmentStatus =>
            `<option value="${employmentStatus}">${employmentStatus}</option>`).join('') : ''}                        </select>
                    </div>
                </div>
            </div>
            
            <div class="filter-section secondary-filters">
                <h4>Secondary Filters</h4>
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="relevanceFilter">Job Relevant to Course:</label>
                        <select id="relevanceFilter" class="filter-dropdown">
                            <option value="">All</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="businessFilter">Business:</label>
                        <select id="businessFilter" class="filter-dropdown">
                            <option value="">All Industries</option>
                            ${filterOptions.industries ? filterOptions.industries.map(business =>
                `<option value="${business}">${business}</option>`).join('') : ''}
                        </select>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="timeToLandFilter">Time to Land Job:</label>
                        <select id="timeToLandFilter" class="filter-dropdown">
                            <option value="">All</option>
                            ${filterOptions.timeToLand ? filterOptions.timeToLand.map(time =>
                    `<option value="${time}">${time}</option>`).join('') : ''}
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="jobFindingMethodFilter">Job Finding Method:</label>
                        <select id="jobFindingMethodFilter" class="filter-dropdown">
                            <option value="">All Methods</option>
                            ${filterOptions.jobFindingMethods ? filterOptions.jobFindingMethods.map(method =>
                        `<option value="${method}">${method}</option>`).join('') : ''}
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="filter-buttons">
                <button id="applyFiltersBtn" class="btn btn-primary">Apply Filters</button>
                <button id="resetFiltersBtn" class="btn btn-secondary">Reset Filters</button>
            </div>
        `;

        filterContainer.innerHTML = filterHTML;

        // Add event listeners for filter updates
        document.querySelectorAll('.filter-dropdown, #startYearFilter, #endYearFilter').forEach(element => {
            // Change to input event for immediate feedback but don't trigger load
            element.addEventListener('change', function () {
                updateFilterValues();
            });
        });

        // Add debounce to input fields to avoid too many updates while typing
        const yearInputs = document.querySelectorAll('#startYearFilter, #endYearFilter');
        yearInputs.forEach(input => {
            input.addEventListener('input', debounce(function () {
                updateFilterValues();
            }, 500));
        });

        // Add event listeners for buttons
        document.getElementById('applyFiltersBtn').addEventListener('click', function () {
            loadChartData(false);
        });

        document.getElementById('resetFiltersBtn').addEventListener('click', resetFilters);
    }

    // Function to just update filter values without reloading
    function updateFilterValues() {
        currentFilters.course = document.getElementById('courseFilter').value;
        currentFilters.campus = document.getElementById('campusFilter').value;
        currentFilters.startYear = document.getElementById('startYearFilter').value;
        currentFilters.endYear = document.getElementById('endYearFilter').value;
        currentFilters.employmentStatus = document.getElementById('employmentStatusFilter').value;
        currentFilters.relevance = document.getElementById('relevanceFilter').value;
        currentFilters.business = document.getElementById('businessFilter').value;
        currentFilters.timeToLand = document.getElementById('timeToLandFilter').value;
        currentFilters.jobFindingMethod = document.getElementById('jobFindingMethodFilter').value;
    }

    // Debounce function to limit how often a function can be called
    function debounce(func, delay) {
        let timeout;
        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Function to reset filters
    function resetFilters() {
        document.getElementById('courseFilter').value = '';
        document.getElementById('campusFilter').value = '';
        document.getElementById('startYearFilter').value = '';
        document.getElementById('endYearFilter').value = '';
        document.getElementById('employmentStatusFilter').value = '';
        document.getElementById('relevanceFilter').value = '';
        document.getElementById('businessFilter').value = '';
        document.getElementById('timeToLandFilter').value = '';
        document.getElementById('jobFindingMethodFilter').value = '';

        // Reset the currentFilters object
        Object.keys(currentFilters).forEach(key => {
            currentFilters[key] = '';
        });

        loadChartData(false);
    }

    // Initial data load - get both filtered and unfiltered totals
    loadChartData(true);
});