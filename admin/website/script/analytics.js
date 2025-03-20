document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById('employmentChart');
    const summaryElement = document.querySelector('.AT-summary p');
    const filterContainer = document.getElementById('filterContainer');

    let useRealData = false;

    // Current filter state
    let currentFilters = {
        course: '',
        campus: '',
        startYear: '',
        endYear: '',
        employmentStatus: '',
        jobRelevance: '',
        business: ''
    };

    if (!chartElement) {
        console.error("Error: Chart element not found!");
        return;
    }

    // Initialize the chart
    let employmentChart;

    // Chart options for grouped bar chart
    const groupedBarOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        return `${context.dataset.label}: ${context.raw}`;
                    }
                }
            }
        },
        barPercentage: 0.8,
        categoryPercentage: 0.9
    };

    function addDataSourceButtons() {
        // Create buttons container
        const buttonsContainer = document.createElement('div');
        buttonsContainer.className = 'data-source-buttons';
        buttonsContainer.innerHTML = `
            <div class="data-source-label">Data Source:</div>
            <div class="button-group">
                <button id="useRealDataBtn" class="data-btn ${useRealData ? 'active' : ''}">Real Data</button>
                <button id="useSampleDataBtn" class="data-btn ${!useRealData ? 'active' : ''}">Sample Data</button>
            </div>
        `;

        // Insert before the filter container
        filterContainer.parentNode.insertBefore(buttonsContainer, filterContainer);

        // Add event listeners
        document.getElementById('useRealDataBtn').addEventListener('click', () => setDataSource(true));
        document.getElementById('useSampleDataBtn').addEventListener('click', () => setDataSource(false));

        // Add some CSS for the buttons
        const style = document.createElement('style');
        style.textContent = `
            .data-source-buttons {
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                justify-content: flex-end;
            }
            
            .data-source-label {
                margin-right: 10px;
                font-weight: bold;
            }
            
            .button-group {
                display: flex;
            }
            
            .data-btn {
                padding: 8px 12px;
                cursor: pointer;
                font-weight: bold;
                transition: all 0.3s ease;
                border: 1px solid #ccc;
                background-color: #f8f8f8;
                color: #333;
            }
            
            .data-btn:first-child {
                border-radius: 4px 0 0 4px;
            }
            
            .data-btn:last-child {
                border-radius: 0 4px 4px 0;
            }
            
            .data-btn.active {
                background-color: #4CAF50;
                color: white;
                border-color: #3e8e41;
            }
            
            .data-btn:hover:not(.active) {
                background-color: #e7e7e7;
            }
        `;
        document.head.appendChild(style);
    }

    // Set data source and update active button
    function setDataSource(useReal) {
        useRealData = useReal;

        // Update button styling
        const realButton = document.getElementById('useRealDataBtn');
        const sampleButton = document.getElementById('useSampleDataBtn');

        if (useRealData) {
            realButton.classList.add('active');
            sampleButton.classList.remove('active');
        } else {
            realButton.classList.remove('active');
            sampleButton.classList.add('active');
        }

        // Reload data with new source
        loadChartData();
    }

    const originalLoadChartData = window.loadChartData || function () { };

    // Function to load data and update the chart
    window.loadChartData = function () {
        // Build query string from filters
        const queryParams = Object.entries(currentFilters)
            .filter(([_, value]) => value !== '')
            .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
            .join('&');

        // Add the dataSource parameter
        const dataSourceParam = useRealData ? 'useRealData=true' : 'useRealData=false';
        const separator = queryParams ? '&' : '';

        const url = `/Alumni-CvSU/admin/website/ajax/analytics.php?${dataSourceParam}${separator}${queryParams}`;

        // Fetch from the server
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                processData(data);

                // Add the buttons if they don't exist yet
                if (!document.querySelector('.data-source-buttons')) {
                    addDataSourceButtons();
                }
            })
            .catch(error => {
                console.warn('Error fetching data:', error);
            });
    };

    // Process and display data from either source
    function processData(data) {
        console.log("Processing Data:", data);

        // Update filter dropdowns if this is the first load
        if (!document.querySelector('.filter-dropdown')) {
            populateFilterDropdowns(data.filterOptions);
        }

        // Compute the total for each employment status
        const summary = {};
        data.datasets.forEach(dataset => {
            summary[dataset.label] = dataset.data.reduce((acc, count) => acc + count, 0);
        });

        // Format the summary into a readable string
        let summaryHTML = Object.entries(summary)
            .map(([status, count]) =>
                `<div class="summary-item">${status.replace(/\b\w/g, c => c.toUpperCase())} : ${count}</div>`
            )
            .join("");

        summaryElement.innerHTML = summaryHTML;

        // If chart already exists, update it
        if (employmentChart) {
            employmentChart.data.labels = data.labels;
            employmentChart.data.datasets = data.datasets;
            employmentChart.update();
        } else {
            // Create a new chart - as a grouped bar chart
            employmentChart = new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: data.datasets.map(dataset => ({
                        ...dataset,
                        // Ensure each dataset has a different color
                        borderWidth: 1
                    }))
                },
                options: groupedBarOptions
            });
        }
    }

    // Function to populate filter dropdowns
    function populateFilterDropdowns(filterOptions) {
        // Create filter UI
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
                            <option value="regular">Regular</option>
                            <option value="temporary">Temporary</option>
                            <option value="contractual">Contractual</option>
                            <option value="self_employed">Self-employed</option>
                            <option value="casual">Casual</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="filter-section secondary-filters">
                <h4>Secondary Filters</h4>
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="jobRelevanceFilter">Job Relevant to Course:</label>
                        <select id="jobRelevanceFilter" class="filter-dropdown">
                            <option value="">All</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="businessFilter">business:</label>
                        <select id="businessFilter" class="filter-dropdown">
                            <option value="">All Industries</option>
                            ${filterOptions.industries ? filterOptions.industries.map(business =>
            `<option value="${business}">${business}</option>`).join('') : ''}
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="filter-buttons">
                <button id="resetFiltersBtn" class="btn">Reset Filters</button>
            </div>
        `;

        filterContainer.innerHTML = filterHTML;

        // Add event listeners for automatic filter updates
        document.querySelectorAll('.filter-dropdown, #startYearFilter, #endYearFilter').forEach(element => {
            element.addEventListener('change', updateFilters);
        });

        // Add debounce to input fields to avoid too many updates while typing
        const yearInputs = document.querySelectorAll('#startYearFilter, #endYearFilter');
        yearInputs.forEach(input => {
            input.addEventListener('input', debounce(updateFilters, 500));
        });

        // Add event listeners for buttons
        document.getElementById('resetFiltersBtn').addEventListener('click', resetFilters);
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

    // Function to update filters and refresh data
    function updateFilters() {
        currentFilters.course = document.getElementById('courseFilter').value;
        currentFilters.campus = document.getElementById('campusFilter').value;
        currentFilters.startYear = document.getElementById('startYearFilter').value;
        currentFilters.endYear = document.getElementById('endYearFilter').value;
        currentFilters.employmentStatus = document.getElementById('employmentStatusFilter').value;
        currentFilters.jobRelevance = document.getElementById('jobRelevanceFilter').value;
        currentFilters.business = document.getElementById('businessFilter').value;

        // Log filters to help with debugging
        console.log("Applied filters:", currentFilters);

        loadChartData();
    }

    // Function to reset filters
    function resetFilters() {
        document.getElementById('courseFilter').value = '';
        document.getElementById('campusFilter').value = '';
        document.getElementById('startYearFilter').value = '';
        document.getElementById('endYearFilter').value = '';
        document.getElementById('employmentStatusFilter').value = '';
        document.getElementById('jobRelevanceFilter').value = '';
        document.getElementById('businessFilter').value = '';

        // Reset the currentFilters object
        Object.keys(currentFilters).forEach(key => {
            currentFilters[key] = '';
        });

        loadChartData();
    }

    // Initial data load
    loadChartData();
});