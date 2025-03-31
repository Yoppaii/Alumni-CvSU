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

function fetchEmploymentRate(campus, course, employmentStatus) {
    const chartContainer = document.getElementById("employmentRateChart").closest(".analytics-content");
    const ctx = document.getElementById("employmentRateChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    fetch(`/Alumni-CvSU/admin/website/ajax/employment_rate.php?campus=${campus}&course=${course}&employmentStatus=${employmentStatus}`)
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


// function fetchCourseRelevance(campus, course, employmentStatus) {
//     fetch(`/Alumni-CvSU/admin/website/ajax/course_to_job_relevance.php?campus=${campus}&course=${course}&employmentStatus=${employmentStatus}`)
//         .then(response => {
//             if (!response.ok) {
//                 throw new Error('Network response was not ok');
//             }
//             return response.json();
//         })
//         .then(data => {
//             const ctx = document.getElementById("courseRelevanceChart").getContext("2d");

//             if (window.courseRelevanceChart instanceof Chart) {
//                 window.courseRelevanceChart.destroy();
//             }

//             window.courseRelevanceChart = new Chart(ctx, {
//                 type: "doughnut",
//                 data: {
//                     labels: ["Related", "Not Related"],
//                     datasets: [{
//                         data: [data.related, data.not_related],
//                         backgroundColor: ["#4CAF50", "#FF5733"],
//                         borderWidth: 1
//                     }]
//                 },
//                 options: {
//                     responsive: true,
//                     maintainAspectRatio: false,
//                     cutout: "70%",
//                     plugins: {
//                         legend: { position: false },
//                         tooltip: {
//                             enabled: true,
//                             callbacks: {
//                                 label: function (tooltipItem) {
//                                     const value = tooltipItem.raw;
//                                     const total = data.related + data.not_related;
//                                     const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
//                                     return `${value} (${percentage}%)`;
//                                 }
//                             }
//                         }
//                     }
//                 }
//             });
//         })
//         .catch(error => {
//             console.error("Fetch error:", error.message);
//         });
// }



function fetchEmploymentByLocation(campus, course, employmentStatus) {
    const chartContainer = document.getElementById("employmentLocationChart").closest(".analytics-content");
    const ctx = document.getElementById("employmentLocationChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    fetch(`/Alumni-CvSU/admin/website/ajax/employment_location.php?campus=${campus}&course=${course}&employmentStatus=${employmentStatus}`)
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

            // Apply custom rectangle legends after chart initialization
            const originalLegendAfterDraw = Chart.Legend.prototype.afterDraw;
            Chart.Legend.prototype.afterDraw = function () {
                originalLegendAfterDraw.apply(this, arguments);

                const chart = this.chart;
                const legendItems = chart.legend.legendItems;

                if (legendItems && legendItems.length > 0) {
                    const legendContainer = chart.legend.legendHitBoxes;

                    legendContainer.forEach((item, index) => {
                        const ctx = chart.ctx;
                        const x = item.left - 10;  // Adjusting position
                        const y = item.top;
                        const width = 20;
                        const height = 10;

                        // Only redraw if visible
                        if (dataVisibility[index]) {
                            // Create gradient for the rectangle
                            const gradient = ctx.createLinearGradient(x, y, x, y + height);
                            gradient.addColorStop(0, colors[index]);
                            gradient.addColorStop(1, hexToRgba(colors[index], 0.5));

                            // Draw the rectangle
                            ctx.fillStyle = gradient;
                            ctx.fillRect(x, y, width, height);
                            ctx.strokeStyle = colors[index];
                            ctx.strokeRect(x, y, width, height);
                        }
                    });
                }
            };

            // Remove loading indicator
            loader.remove();
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            loader.remove();
            showErrorMessage("employmentLocationChart", "Failed to load employment data");
        });
}

function fetchEmploymentTime(campus, course, employmentStatus) {
    const chartContainer = document.getElementById("employmentTimeChart").closest(".analytics-content");
    const ctx = document.getElementById("employmentTimeChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    fetch(`/Alumni-CvSU/admin/website/ajax/employment_time.php?campus=${campus}&course=${course}&employmentStatus=${employmentStatus}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            // Define time categories and their display labels
            const labels = ["Less than 1 Month", "1-6 Months", "7-11 Months", "1 Year or More"];
            const timeCategories = ["less_than_1month", "1_6months", "7_11months", "1year_more"];

            // Initialize data map and total count
            let datasetMap = {};
            let totalAlumni = 0;

            // Initialize all categories with zero
            timeCategories.forEach(category => {
                datasetMap[category] = 0;
            });

            // Process data from API
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    if (timeCategories.includes(item.time_to_land)) {
                        datasetMap[item.time_to_land] = parseInt(item.alumni_count);
                        totalAlumni += parseInt(item.alumni_count);
                    }
                });
            } else {
                console.warn("No employment time data available for the selected filters.");
            }

            console.log("Total Alumni:", totalAlumni);
            console.log("Dataset Map:", datasetMap);

            // Destroy existing chart if it exists
            if (window.employmentTimeChart instanceof Chart) {
                window.employmentTimeChart.destroy();
            }

            // Define colors for each time period
            const colors = ["#4CAF50", "#facc15", "#f97316", "#ef4444"];

            // Create gradient backgrounds
            const gradients = colors.map(color => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, hexToRgba(color, 0.5));
                return gradient;
            });

            // Extract the data into separate arrays for each time category
            const lessOneMonthData = [datasetMap["less_than_1month"], 0, 0, 0];
            const oneToSixMonthsData = [0, datasetMap["1_6months"], 0, 0];
            const sevenToElevenMonthsData = [0, 0, datasetMap["7_11months"], 0];
            const oneYearMoreData = [0, 0, 0, datasetMap["1year_more"]];

            // Create the Chart with multiple datasets
            window.employmentTimeChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Less than 1 Month",
                            backgroundColor: gradients[0],
                            data: lessOneMonthData,
                            borderRadius: 4
                        },
                        {
                            label: "1-6 Months",
                            backgroundColor: gradients[1],
                            data: oneToSixMonthsData,
                            borderRadius: 4
                        },
                        {
                            label: "7-11 Months",
                            backgroundColor: gradients[2],
                            data: sevenToElevenMonthsData,
                            borderRadius: 4
                        },
                        {
                            label: "1 Year or More",
                            backgroundColor: gradients[3],
                            data: oneYearMoreData,
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
                            title: { display: true, text: "Time to Land First Job" }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#333',
                            bodyColor: '#666',
                            borderColor: 'rgba(200, 200, 200, 0.5)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (tooltipItem) {
                                    const count = tooltipItem.raw;
                                    if (count === 0) return null;
                                    const percentage = totalAlumni > 0 ? ((count / totalAlumni) * 100).toFixed(1) : 0;
                                    return `${tooltipItem.dataset.label}: ${count} alumni (${percentage}%)`;
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

function fetchJobRelevanceSalary(campus, course, employmentStatus) {
    const chartContainer = document.getElementById("jobRelevanceSalaryChart").closest(".analytics-content");
    const ctx = document.getElementById("jobRelevanceSalaryChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    fetch(`/Alumni-CvSU/admin/website/ajax/job_relevance_salary.php?campus=${campus}&course=${course}&employmentStatus=${employmentStatus}`)
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


function fetchJobSearchMethods(campus, course, graduationYear) {
    const chartContainer = document.getElementById("jobSearchChart").closest(".analytics-content");
    const ctx = document.getElementById("jobSearchChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    fetch(`/Alumni-CvSU/admin/website/ajax/job_search_methods.php?campus=${campus}&course=${course}&graduationYear=${graduationYear}`)
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

            console.log("Total Alumni:", totalAlumni);
            console.log("Dataset Map:", datasetMap);

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



document.getElementById("campusFilter").addEventListener("change", updateChart);
document.getElementById("courseFilter").addEventListener("change", updateChart);
document.getElementById("employmentStatusFilter").addEventListener("change", updateChart);


function updateChart() {
    const selectedCampus = document.getElementById("campusFilter").value;
    const selectedCourse = document.getElementById("courseFilter").value;
    const selectedEmploymentStatus = document.getElementById("employmentStatusFilter").value;


    fetchEmploymentRate(selectedCampus, selectedCourse, selectedEmploymentStatus);
    // fetchCourseRelevance(selectedCampus, selectedCourse, selectedEmploymentStatus);
    fetchJobRelevanceSalary(selectedCampus, selectedCourse, selectedEmploymentStatus);
    fetchEmploymentByLocation(selectedCampus, selectedCourse, selectedEmploymentStatus);
    fetchEmploymentTime(selectedCampus, selectedCourse, selectedEmploymentStatus);
    fetchJobSearchMethods(selectedCampus, selectedCourse, selectedEmploymentStatus);
}

window.onload = function () {
    updateChart();
}