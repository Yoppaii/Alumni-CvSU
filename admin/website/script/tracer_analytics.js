let employmentRateChart = null;
let courseRelevanceChart = null;
let EmploymentByLocation = null;
let employmentTimeChart = null;

function hexToRgba(hex, alpha = 1) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function showErrorMessage(chartId, message) {
    const container = document.getElementById(chartId).closest('.analytics-content');

    // Remove existing error message if any
    const existingError = container.querySelector('.chart-error');
    if (existingError) existingError.remove();

    // Create error display
    const errorDiv = document.createElement('div');
    errorDiv.className = 'chart-error';
    errorDiv.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <p>${message}</p>
    `;

    container.appendChild(errorDiv);
}
function fetchEmploymentRate(campus, course, employmentStatus, fromYear, toYear) {
    const chartContainer = document.getElementById("employmentRateChart").closest(".analytics-content");
    const ctx = document.getElementById("employmentRateChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    // Construct query parameters
    const params = new URLSearchParams({
        campus: campus,
        course: course,
        employmentStatus: employmentStatus,
        fromYear: fromYear || '',  // Keep as empty string if null/undefined
        toYear: toYear || ''       // Keep as empty string if null/undefined
    });

    fetch(`/Alumni-CvSU/admin/website/ajax/employment_rate.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            if (!data || typeof data.employed !== "number" || typeof data.unemployed !== "number") {
                console.warn("No employment rate data available for the selected filters.");
                loader.remove();
                return;
            }

            const total = data.employed + data.unemployed;
            const employmentRate = total > 0 ? ((data.employed / total) * 100).toFixed(2) : 0;
            const unemploymentRate = (100 - employmentRate).toFixed(2);

            const gradientEmployed = ctx.createLinearGradient(0, 0, 0, 300);
            gradientEmployed.addColorStop(0, "#4CAF50");
            gradientEmployed.addColorStop(1, hexToRgba("#4CAF50", 0.6));

            const gradientUnemployed = ctx.createLinearGradient(0, 0, 0, 300);
            gradientUnemployed.addColorStop(0, "#FF5733");
            gradientUnemployed.addColorStop(1, hexToRgba("#FF5733", 0.6));

            if (window.employmentRateChart instanceof Chart) {
                window.employmentRateChart.destroy();
            }

            window.employmentRateChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: ["Employed", "Unemployed"],
                    datasets: [{
                        data: [employmentRate, unemploymentRate],
                        backgroundColor: [gradientEmployed, gradientUnemployed],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: "70%",
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#333',
                            bodyColor: '#666',
                            borderColor: 'rgba(200, 200, 200, 0.5)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (tooltipItem) {
                                    return tooltipItem.dataIndex === 0
                                        ? `${data.employed} alumni (${employmentRate}%)`
                                        : `${data.unemployed} alumni (${unemploymentRate}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Add percentage text in the center
            let centerText = document.getElementById("employmentRateText");
            if (!centerText) {
                centerText = document.createElement("div");
                centerText.id = "employmentRateText";
                centerText.style.position = "absolute";
                centerText.style.top = "50%";
                centerText.style.left = "50%";
                centerText.style.transform = "translate(-50%, -50%)";
                centerText.style.fontWeight = "bold";
                centerText.style.textAlign = "center";
                centerText.style.pointerEvents = "none";
                chartContainer.appendChild(centerText);
            }

            // Determine color based on employment rate
            let color = "#FF4D6D"; // Default to red
            if (employmentRate >= 50) {
                color = "#4CAF50"; // Green
            } else if (employmentRate >= 30) {
                color = "#FFDB7D"; // Yellow
            }

            centerText.style.color = color;
            centerText.style.fontSize = "40px";
            centerText.innerHTML = `${employmentRate}%`;

            // Remove loading indicator
            loader.remove();
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            loader.remove();
        });
}

function fetchJobRelevanceSalary(campus, course, employmentStatus, fromYear, toYear) {
    const chartContainer = document.getElementById("jobRelevanceSalaryChart").closest(".analytics-content");
    const ctx = document.getElementById("jobRelevanceSalaryChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    const params = new URLSearchParams({
        campus: campus,
        course: course,
        employmentStatus: employmentStatus,
        fromYear: fromYear || '',  // Keep as empty string if null/undefined
        toYear: toYear || ''       // Keep as empty string if null/undefined
    });

    fetch(`/Alumni-CvSU/admin/website/ajax/course_relevance_salary.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            const salaryLabels = ["<20,000", "20,000 - 30,000", "30,000 - 40,000", "40,000+"];
            const relatedData = [0, 0, 0, 0];
            const unrelatedData = [0, 0, 0, 0];

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    let index = salaryLabels.indexOf(item.salary_range);
                    if (index !== -1) {
                        if (item.course_related.toLowerCase() === "yes") {
                            relatedData[index] = parseInt(item.alumni_count);
                        } else if (item.course_related.toLowerCase() === "no") {
                            unrelatedData[index] = parseInt(item.alumni_count);
                        }
                    }
                });
            } else {
                console.warn("No job relevance salary data available for the selected filters.");
            }

            // Destroy existing chart if it exists
            if (window.jobRelevanceSalaryChart instanceof Chart) {
                window.jobRelevanceSalaryChart.destroy();
            }

            // Create gradient backgrounds
            const colors = ["#4CAF50", "#facc15", "#f97316", "#ef4444"];
            const gradients = colors.map(color => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, hexToRgba(color, 0.5));
                return gradient;
            });

            // Create the Chart
            window.jobRelevanceSalaryChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: salaryLabels,
                    datasets: [
                        {
                            label: "Related to Course",
                            backgroundColor: gradients[0],
                            data: relatedData,
                            borderRadius: 4
                        },
                        {
                            label: "Unrelated to Course",
                            backgroundColor: gradients[3],
                            data: unrelatedData,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: {
                                display: true,
                                drawBorder: false,
                                color: 'rgba(200, 200, 200, 0.15)',
                            },
                            title: { display: true, text: "Number of Alumni" }
                        },
                        x: {
                            grid: { display: false },
                            title: { display: true, text: "Salary Range" }
                        }
                    },
                    plugins: {
                        legend: { display: true },

                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#333',
                            bodyColor: '#666',
                            borderColor: 'rgba(200, 200, 200, 0.5)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (tooltipItem) {
                                    const datasetIndex = tooltipItem.datasetIndex;
                                    const count = tooltipItem.raw;
                                    const total = relatedData.reduce((a, b) => a + b, 0) + unrelatedData.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                                    const relevance = datasetIndex === 0 ? "Related to Course" : "Unrelated to Course";
                                    return `${relevance}: ${count} alumni (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Remove loading indicator
            loader.remove();
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            loader.remove();
            showErrorMessage("jobRelevanceSalaryChart", "Failed to load job relevance salary data");
        });
}

function fetchJobSearchMethods(campus, course, employmentStatus, fromYear, toYear) {
    const chartContainer = document.getElementById("jobSearchChart").closest(".analytics-content");
    const ctx = document.getElementById("jobSearchChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    const params = new URLSearchParams({
        campus: campus,
        course: course,
        employmentStatus: employmentStatus,
        fromYear: fromYear || '',  // Keep as empty string if null/undefined
        toYear: toYear || ''       // Keep as empty string if null/undefined
    });

    fetch(`/Alumni-CvSU/admin/website/ajax/job_search_methods.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (!data || Object.values(data).every(value => value === 0)) {
                console.warn("No job search method data available.");
                loader.remove();
                return;
            }

            const labels = ["Job Fair", "Advertisement", "Recommendation", "Walk-in Application", "Online Job Portal"];
            const jobMethods = ["job_fair", "advertisement", "recommendation", "walk_in", "online"];

            // Ensure dataset map and total count are correctly initialized
            let datasetMap = {};
            let totalAlumni = 0;

            jobMethods.forEach(method => {
                datasetMap[method] = Number(data[method]) || 0;
                totalAlumni += datasetMap[method]; // Sum correctly
            });


            // Define colors and create gradients
            const colors = ["#4CAF50", "#FFC107", "#FF5733", "#36A2EB", "#9C27B0"];
            const gradients = colors.map((color) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, hexToRgba(color, 0.6)); // Smooth transition
                return gradient;
            });

            // Destroy previous chart if it exists
            if (window.jobSearchChart instanceof Chart) {
                window.jobSearchChart.destroy();
            }

            window.jobSearchChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: labels,
                    datasets: [{
                        data: jobMethods.map(method => datasetMap[method]),
                        backgroundColor: gradients
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    const methodIndex = tooltipItem.dataIndex;
                                    const count = datasetMap[jobMethods[methodIndex]];
                                    const percentage = totalAlumni > 0 ? ((count / totalAlumni) * 100).toFixed(1) : 0;
                                    return `${labels[methodIndex]}: ${count} alumni (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Remove loading indicator
            loader.remove();
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            loader.remove();
        });
}

function fetchEmploymentTime(campus, course, employmentStatus, fromYear, toYear) {
    const chartContainer = document.getElementById("employmentTimeChart").closest(".analytics-content");
    const ctx = document.getElementById("employmentTimeChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    // Construct query parameters
    const params = new URLSearchParams({
        campus: campus,
        course: course,
        employmentStatus: employmentStatus,
        fromYear: fromYear || '',
        toYear: toYear || ''
    });

    fetch(`/Alumni-CvSU/admin/website/ajax/employment_time.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                console.warn("No employment time data available for the selected filters.");
                loader.remove();
                return;
            }

            // Define the expected order of time periods
            const timeOrder = ["Less than 1 Month", "1-6 Months", "7-11 Months", "1 Year or More"];
            const timeKeys = ["less_than_1month", "1_6months", "7_11months", "1year_more"];

            // Map API keys to display labels
            const keyToLabel = {
                "less_than_1month": "Less than 1 Month",
                "1_6months": "1-6 Months",
                "7_11months": "7-11 Months",
                "1year_more": "1 Year or More"
            };

            // Process data to standard format
            const processedData = [];
            let totalAlumni = 0;

            timeKeys.forEach((key, index) => {
                const found = data.find(item => item.time_to_land === key);
                const count = found ? parseInt(found.alumni_count) : 0;
                totalAlumni += count;

                processedData.push({
                    time_period: keyToLabel[key],
                    alumni_count: count
                });
            });

            // Extract time periods and counts
            const timePeriods = processedData.map(item => item.time_period);
            const counts = processedData.map(item => item.alumni_count);

            // Destroy existing chart if it exists
            if (window.employmentTimeChart instanceof Chart) {
                window.employmentTimeChart.destroy();
            }

            // Define colors for each time period
            const colors = ["#4CAF50", "#facc15", "#f97316", "#ef4444"];

            // Create gradient backgrounds
            const gradients = colors.map((color) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, hexToRgba(color, 0.5));
                return gradient;
            });

            // Store visibility state and original data
            const dataVisibility = [true, true, true, true];
            const originalData = [...counts];

            // Create the Chart with a single dataset
            window.employmentTimeChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: timePeriods,
                    datasets: [{
                        data: counts,
                        backgroundColor: gradients,
                        borderRadius: 4,
                        barPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { display: true, drawBorder: false, color: 'rgba(200, 200, 200, 0.15)' },
                            title: { display: true, text: "Number of Alumni" }
                        },
                        x: {
                            grid: { display: false },
                            title: { display: true, text: "Time to Land First Job" }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: false,
                                boxWidth: 40,
                                boxHeight: 10,
                                padding: 20,
                                generateLabels: function (chart) {
                                    return timeOrder.map((time, index) => {
                                        return {
                                            text: time,
                                            fillStyle: gradients[index],
                                            strokeStyle: colors[index],
                                            lineWidth: 1,
                                            hidden: !dataVisibility[index],
                                            index: index
                                        };
                                    });
                                }
                            },
                            onClick: function (e, legendItem, legend) {
                                const index = legendItem.index;
                                const ci = legend.chart;

                                // Toggle visibility state
                                dataVisibility[index] = !dataVisibility[index];

                                // Apply animation
                                if (dataVisibility[index]) {
                                    // Showing with animation - start from 0 and animate to actual value
                                    ci.data.datasets[0].data[index] = 0;
                                    ci.update('none');

                                    setTimeout(() => {
                                        ci.data.datasets[0].data[index] = originalData[index];
                                        ci.update();
                                    }, 50);
                                } else {
                                    // Hiding with animation - animate to 0
                                    ci.data.datasets[0].data[index] = 0;
                                    ci.update();
                                }

                                // Update legend to reflect new state
                                ci.update();
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#333',
                            bodyColor: '#666',
                            borderColor: 'rgba(200, 200, 200, 0.5)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (context) {
                                    const count = context.raw;
                                    const timePeriod = timePeriods[context.dataIndex];
                                    const percentage = totalAlumni > 0 ? ((count / totalAlumni) * 100).toFixed(1) : 0;
                                    return `${timePeriod}: ${count} alumni (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Remove loading indicator
            loader.remove();
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            loader.remove();
            showErrorMessage("employmentTimeChart", "Failed to load employment time data");
        });
}

function fetchEmploymentByLocation(campus, course, employmentStatus, fromYear, toYear) {
    const chartContainer = document.getElementById("employmentLocationChart").closest(".analytics-content");
    const ctx = document.getElementById("employmentLocationChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    // Construct query parameters
    const params = new URLSearchParams({
        campus: campus,
        course: course,
        employmentStatus: employmentStatus,
        fromYear: fromYear || '',
        toYear: toYear || ''
    });

    fetch(`/Alumni-CvSU/admin/website/ajax/employment_location.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                console.warn("No employment data available for the selected filters.");
                loader.remove();
                return;
            }

            // Define the expected order of locations
            const locationOrder = ["Local", "Abroad", "Work From Home", "Hybrid"];

            // Ensure the data is in the correct order
            const orderedData = locationOrder.map(location => {
                const found = data.find(item => item.location === location);
                return found ? found : { location, total_employees: 0 };
            });

            // Extract locations and counts
            const locations = orderedData.map(item => item.location);
            const counts = orderedData.map(item => parseInt(item.total_employees));

            // Calculate total employees
            const totalEmployees = counts.reduce((sum, count) => sum + count, 0);

            // Destroy existing chart if it exists
            if (window.employmentLocationChart instanceof Chart) {
                window.employmentLocationChart.destroy();
            }

            // Define colors for each location
            const colors = ["#4CAF50", "#facc15", "#f97316", "#ef4444"];

            // Create gradient backgrounds
            const gradients = colors.map((color) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, hexToRgba(color, 0.5));
                return gradient;
            });

            // Store visibility state and original data
            const dataVisibility = [true, true, true, true];
            const originalData = [...counts];

            // Create the Chart with a single dataset
            window.employmentLocationChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: locations,
                    datasets: [{
                        data: counts,
                        backgroundColor: gradients,
                        borderRadius: 4,
                        barPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { display: true, drawBorder: false, color: 'rgba(200, 200, 200, 0.15)' },
                            title: { display: true, text: "Number of Employees" }
                        },
                        x: {
                            grid: { display: false },
                            title: { display: true, text: "Work Locations" }
                        }
                    },

                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: false,
                                boxWidth: 40,
                                boxHeight: 10,
                                padding: 20,
                                generateLabels: function (chart) {
                                    return locationOrder.map((location, index) => {
                                        return {
                                            text: location,
                                            fillStyle: gradients[index],
                                            strokeStyle: colors[index],
                                            lineWidth: 1,
                                            hidden: !dataVisibility[index],
                                            index: index
                                        };
                                    });
                                }
                            },
                            onClick: function (e, legendItem, legend) {
                                const index = legendItem.index;
                                const ci = legend.chart;

                                // Toggle visibility state
                                dataVisibility[index] = !dataVisibility[index];

                                // Apply animation
                                if (dataVisibility[index]) {
                                    // Showing with animation - start from 0 and animate to actual value
                                    ci.data.datasets[0].data[index] = 0;
                                    ci.update('none');

                                    setTimeout(() => {
                                        ci.data.datasets[0].data[index] = originalData[index];
                                        ci.update();
                                    }, 50);
                                } else {
                                    // Hiding with animation - animate to 0
                                    ci.data.datasets[0].data[index] = 0;
                                    ci.update();
                                }

                                // Update legend to reflect new state
                                ci.update();
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#333',
                            bodyColor: '#666',
                            borderColor: 'rgba(200, 200, 200, 0.5)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (context) {
                                    const count = context.raw;
                                    const location = locations[context.dataIndex];
                                    const percentage = totalEmployees > 0 ? ((count / totalEmployees) * 100).toFixed(1) : 0;
                                    return `${location}: ${count} alumni (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Remove loading indicator
            loader.remove();
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            loader.remove();
            showErrorMessage("employmentLocationChart", "Failed to load employment data");
        });
}
// Function to fetch and display total graduates data
function fetchTotalGraduates(campus, course, employmentStatus, fromYear, toYear) {
    const chartContainer = document.getElementById("totalGraduatesChart").closest(".analytics-content");
    const ctx = document.getElementById("totalGraduatesChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    // Debug the filter parameters
    console.log("Filter parameters:", {
        campus, course, employmentStatus, fromYear, toYear
    });

    // First, fetch the raw data directly
    const params = new URLSearchParams({
        campus: campus,
        course: course,
        employmentStatus: employmentStatus,
        fromYear: fromYear || '',
        toYear: toYear || ''
    });

    fetch(`/Alumni-CvSU/admin/website/ajax/total_graduates.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            // Log the raw data
            console.log("Raw graduates data:", data);

            // Then fetch the campus list
            return fetch('/Alumni-CvSU/admin/website/ajax/get_campus_list.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Failed to fetch campus list");
                    }
                    return response.json();
                })
                .then(availableCampuses => {
                    // Log the available campuses
                    console.log("Available campuses:", availableCampuses);

                    let campusLabels = [...availableCampuses]; // Create a copy

                    // Make sure Silang campus is included if it exists in the data but not in the campus list
                    const dataContainsSilang = data.some(item =>
                        item.campus.toLowerCase().includes('silang'));
                    const listContainsSilang = campusLabels.some(campus =>
                        campus.toLowerCase().includes('silang'));

                    if (dataContainsSilang && !listContainsSilang) {
                        const silangEntry = data.find(item =>
                            item.campus.toLowerCase().includes('silang'));
                        if (silangEntry) {
                            campusLabels.push(silangEntry.campus);
                            console.log("Added missing Silang campus:", silangEntry.campus);
                        }
                    }

                    // Sort the campus labels alphabetically for consistency
                    campusLabels.sort();

                    // Initialize graduates data with zeros for all campuses
                    let graduatesData = Array(campusLabels.length).fill(0);

                    // Update with actual data if available
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(item => {
                            // Try to find an exact match first
                            let index = campusLabels.indexOf(item.campus);

                            // If not found, try a case-insensitive match
                            if (index === -1) {
                                index = campusLabels.findIndex(campus =>
                                    campus.toLowerCase() === item.campus.toLowerCase());
                            }

                            // If still not found, try a partial match
                            if (index === -1) {
                                index = campusLabels.findIndex(campus =>
                                    campus.toLowerCase().includes(item.campus.toLowerCase()) ||
                                    item.campus.toLowerCase().includes(campus.toLowerCase()));
                            }

                            if (index !== -1) {
                                graduatesData[index] = parseInt(item.total_graduates);
                                console.log(`Matched campus "${item.campus}" to "${campusLabels[index]}" with ${item.total_graduates} graduates`);
                            } else {
                                console.warn(`Could not match campus "${item.campus}" to any label`);
                            }
                        });
                    }

                    // Calculate total graduates
                    const totalGraduates = graduatesData.reduce((sum, count) => sum + count, 0);

                    // Destroy existing chart if it exists
                    if (window.totalGraduatesChart instanceof Chart) {
                        window.totalGraduatesChart.destroy();
                    }

                    // Rest of the chart creation code remains mostly the same
                    const baseColors = [
                        "#4CAF50", "#FFC107", "#FF5733", "#36A2EB",
                        "#9C27B0", "#F44336", "#3F51B5", "#FF9800", "#009688"
                    ];

                    const colors = [];
                    for (let i = 0; i < campusLabels.length; i++) {
                        colors.push(baseColors[i % baseColors.length]);
                    }

                    const gradients = colors.map((color) => {
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, color);
                        gradient.addColorStop(1, hexToRgba(color, 0.5));
                        return gradient;
                    });

                    window.totalGraduatesChart = new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: campusLabels,
                            datasets: [{
                                data: graduatesData,
                                backgroundColor: gradients,
                                borderRadius: 4,
                                barPercentage: 0.8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 800,
                                easing: 'easeOutQuart'
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 },
                                    grid: { display: true, drawBorder: false, color: 'rgba(200, 200, 200, 0.15)' },
                                    title: { display: true, text: "Number of Graduates" }
                                },
                                x: {
                                    grid: { display: false },
                                    title: { display: true, text: "Campus" },
                                    ticks: {
                                        callback: function (value, index) {
                                            const campusName = campusLabels[index];
                                            if (campusName && campusName.includes("Cavite State University - ")) {
                                                return campusName.replace("Cavite State University - ", "");
                                            }
                                            return campusName;
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                    titleColor: '#333',
                                    bodyColor: '#666',
                                    borderColor: 'rgba(200, 200, 200, 0.5)',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    callbacks: {
                                        title: function (tooltipItems) {
                                            return campusLabels[tooltipItems[0].dataIndex];
                                        },
                                        label: function (tooltipItem) {
                                            const count = tooltipItem.raw;
                                            const percentage = totalGraduates > 0 ? ((count / totalGraduates) * 100).toFixed(1) : 0;
                                            return `Graduates: ${count} (${percentage}%)`;
                                        }
                                    }
                                },
                            }
                        }
                    });
                });
        })
        .catch(error => {
            console.error("Error:", error.message);
            showErrorMessage("totalGraduatesChart", "Failed to load data: " + error.message);
        })
        .finally(() => {
            // Make sure the loader is removed in all cases
            loader.remove();
        });
}


// Initialize filter event listeners
document.addEventListener('DOMContentLoaded', function () {
    // Get filter elements
    const campusFilter = document.getElementById('campusFilter');
    const courseFilter = document.getElementById('courseFilter');
    const employmentFilter = document.getElementById('employmentFilter');
    const fromYearFilter = document.getElementById('fromYearFilter');
    const toYearFilter = document.getElementById('toYearFilter');
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');

    // Apply filters button click event
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function () {
            fetchTotalGraduates(
                campusFilter ? campusFilter.value : '',
                courseFilter ? courseFilter.value : '',
                employmentFilter ? employmentFilter.value : '',
                fromYearFilter ? fromYearFilter.value : '',
                toYearFilter ? toYearFilter.value : ''
            );
        });
    }


});

document.getElementById("campusFilter").addEventListener("change", updateChart);
document.getElementById("courseFilter").addEventListener("change", updateChart);
document.getElementById("employmentStatusFilter").addEventListener("change", updateChart);
document.getElementById("fromYearFilter").addEventListener("change", updateChart);
document.getElementById("toYearFilter").addEventListener("change", updateChart);


function updateChart() {
    const selectedCampus = document.getElementById("campusFilter").value;
    const selectedCourse = document.getElementById("courseFilter").value;
    const selectedEmploymentStatus = document.getElementById("employmentStatusFilter").value;
    let selectedFromYear = document.getElementById("fromYearFilter").value;
    let selectedToYear = document.getElementById("toYearFilter").value;

    // Ensure years are numbers and valid
    selectedFromYear = selectedFromYear ? parseInt(selectedFromYear) : null;
    selectedToYear = selectedToYear ? parseInt(selectedToYear) : null;

    fetchEmploymentRate(selectedCampus, selectedCourse, selectedEmploymentStatus, selectedFromYear, selectedToYear);
    fetchJobRelevanceSalary(selectedCampus, selectedCourse, selectedEmploymentStatus, selectedFromYear, selectedToYear);
    fetchJobSearchMethods(selectedCampus, selectedCourse, selectedEmploymentStatus, selectedFromYear, selectedToYear);
    fetchEmploymentByLocation(selectedCampus, selectedCourse, selectedEmploymentStatus, selectedFromYear, selectedToYear);
    fetchEmploymentTime(selectedCampus, selectedCourse, selectedEmploymentStatus, selectedFromYear, selectedToYear);
    fetchTotalGraduates(selectedCampus, selectedCourse, selectedEmploymentStatus, selectedFromYear, selectedToYear);

}


window.onload = function () {
    updateChart();
}