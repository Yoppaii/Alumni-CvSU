let employmentRateChart = null;
let courseRelevanceChart = null;
let EmploymentByLocation = null;

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
                        legend: { display: false },
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
                                        ? `Employed: ${data.employed} (${employmentRate}%)`
                                        : `Unemployed: ${data.unemployed} (${unemploymentRate}%)`;
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
                        legend: { display: false },
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
                                    const total = relatedData.reduce((a, b) => a + b, 0) + unrelatedData.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                                    return `${count} alumni (${percentage}%)`;
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

            const locations = data.map(item => item.location || "Unknown"); // Handle null values
            const employeeCounts = data.map(item => item.total_employees);

            // Destroy existing chart if it exists
            if (window.employmentLocationChart instanceof Chart) {
                window.employmentLocationChart.destroy();
            }

            // Define colors and gradient backgrounds
            const colors = ["#4CAF50", "#facc15", "#f97316", "#ef4444", "#3b82f6", "#8b5cf6", "#ec4899"];
            const gradients = locations.map((_, index) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                const color = colors[index % colors.length]; // Cycle through colors
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, hexToRgba(color, 0.5));
                return gradient;
            });

            // Create the Chart
            window.employmentLocationChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: locations,
                    datasets: [{
                        label: "Total Employees",
                        backgroundColor: gradients,
                        data: employeeCounts,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                        legend: { display: false },
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
                                    return `${count} employees`;
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
}

window.onload = function () {
    updateChart();
}