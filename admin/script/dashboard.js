let bookingByDayChart = null;
let bookingByMonthChart = null;
let cancellationChart = null;
let bookingLeadTimeChart = null;
let peakBookingChart = null;

// Helper function to convert hex to rgba with transparency
function hexToRgba(hex, alpha = 1) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Helper function to remove loading indicators
function removeLoadingIndicator(chartId) {
    const container = document.getElementById(chartId).closest('.analytics-content');
    const loader = container.querySelector('.chart-loading');
    if (loader) loader.remove();
}

// Helper function to show error on charts
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

function fetchBookingByDay(year, month, guestType, roomNumber) {
    const chartContainer = document.getElementById('bookingByDayChart').closest('.analytics-content');
    const loader = document.createElement('div');
    loader.className = 'chart-loading';
    chartContainer.appendChild(loader);

    fetch(`/Alumni-CvSU/admin/analytics/booking_by_day.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const weekDays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
            const colors = {
                "Monday": "#4361ee",
                "Tuesday": "#3a0ca3",
                "Wednesday": "#7209b7",
                "Thursday": "#f72585",
                "Friday": "#4cc9f0",
                "Saturday": "#4895ef",
                "Sunday": "#560bad"
            };

            const dayCounts = Object.fromEntries(weekDays.map(day => [day, 0]));

            let totalBookings = 0;

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    if (dayCounts.hasOwnProperty(item.booking_day)) {
                        dayCounts[item.booking_day] = parseInt(item.total);
                        totalBookings += parseInt(item.total);
                    }
                });
            } else {
                console.warn("No booking data available for the selected filters.");
            }

            const labels = Object.keys(dayCounts);
            const values = Object.values(dayCounts);

            if (window.bookingByDayChart instanceof Chart) {
                window.bookingByDayChart.destroy();
            }

            const ctx = document.getElementById("bookingByDayChart").getContext("2d");

            // Create gradient backgrounds
            const gradients = labels.map(day => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                const baseColor = colors[day];
                gradient.addColorStop(0, baseColor);
                gradient.addColorStop(1, hexToRgba(baseColor, 0.5));
                return gradient;
            });

            window.bookingByDayChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: `Total Bookings per Day (${guestType})`,
                        data: values,
                        backgroundColor: gradients,
                        borderWidth: 0,
                        borderRadius: 4,
                        hoverBorderWidth: 1,
                        hoverBorderColor: labels.map(day => colors[day])
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                drawBorder: false,
                                color: 'rgba(200, 200, 200, 0.15)',
                            },
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 11
                                }
                            },
                            title: {
                                display: true,
                                text: "Number of Bookings",
                                padding: {
                                    top: 10,
                                    bottom: 10
                                },
                                font: {
                                    size: 12,
                                    weight: 'normal'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            title: {
                                display: true,
                                text: "Day of the Week",
                                padding: {
                                    top: 10
                                },
                                font: {
                                    size: 12,
                                    weight: 'normal'
                                }
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
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
                            displayColors: true,
                            boxWidth: 10,
                            boxHeight: 10,
                            boxPadding: 3,
                            padding: 10,
                            callbacks: {
                                label: function (tooltipItem) {
                                    const count = tooltipItem.raw;
                                    const percentage = totalBookings > 0
                                        ? ((count / totalBookings) * 100).toFixed(1)
                                        : 0;
                                    return `${count} bookings (${percentage}%)`;
                                }
                            }
                        }
                        
                    }
                }
            });

            // Remove loading indicator
            removeLoadingIndicator('bookingByDayChart');
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            // Show error state
            removeLoadingIndicator('bookingByDayChart');
            showErrorMessage('bookingByDayChart', 'Failed to load booking data');
        });
}

function fetchBookingByMonth(year, guestType, roomNumber) {
    // Add loading state
    const chartContainer = document.getElementById('bookingByMonthChart').closest('.analytics-content');
    const loader = document.createElement('div');
    loader.className = 'chart-loading';
    chartContainer.appendChild(loader);

    fetch(`/Alumni-CvSU/admin/analytics/booking_by_month.php?year=${year}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            const monthCounts = Object.fromEntries(monthNames.map(month => [month, 0]));
            let totalBookings = 0;

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    if (monthCounts.hasOwnProperty(item.month)) {
                        const bookingCount = Number(item.total) || 0;
                        monthCounts[item.month] = bookingCount;
                        totalBookings += bookingCount;
                    }
                });
            } else {
                console.warn("No booking data available for the selected filters.");
            }

            const labels = Object.keys(monthCounts);
            const values = Object.values(monthCounts);

            if (window.bookingByMonthChart instanceof Chart) {
                window.bookingByMonthChart.destroy();
            }

            const ctx = document.getElementById("bookingByMonthChart").getContext("2d");

            // Create gradient for line chart
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, "rgba(54, 162, 235, 0.4)");
            gradient.addColorStop(1, "rgba(54, 162, 235, 0)");

            window.bookingByMonthChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [{
                        label: `Total Bookings per Month (${guestType})`,
                        data: values,
                        backgroundColor: gradient,
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: values.map(value => (value === 0 ? 0 : 5)),
                        pointBackgroundColor: "rgba(54, 162, 235, 1)",
                        borderDash: values.every(value => value === 0) ? [5, 5] : []
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: Math.max(...values) + 1,
                            grid: {
                                display: true,
                                color: 'rgba(200, 200, 200, 0.15)'
                            },
                            title: {
                                display: true,
                                text: "Number of Bookings",
                                font: { size: 12, weight: 'normal' }
                            }
                        },
                        x: {
                            grid: { display: false },
                            title: {
                                display: true,
                                text: "Month",
                                font: { size: 12, weight: 'normal' }
                            }
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
                            padding: 10,
                            callbacks: {
                                label: function (tooltipItem) {
                                    const count = tooltipItem.raw || 0;
                                    const percentage = totalBookings > 0
                                        ? ((count / totalBookings) * 100).toFixed(1)
                                        : 0;
                                    return `${count} bookings (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Remove loading indicator
            removeLoadingIndicator('bookingByMonthChart');
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            // Show error state
            removeLoadingIndicator('bookingByMonthChart');
            showErrorMessage('bookingByMonthChart', 'Failed to load booking data');
        });
}

function fetchCancellationRate(year, month, guestType, roomNumber) {

    fetch(`/Alumni-CvSU/admin/analytics/cancellation_rate.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => response.json())
        .then(data => {
            if (!data || typeof data.rate !== "number" || typeof data.cancelled !== "number" || typeof data.successful !== "number") {
                console.warn("No data available.");
                return;
            }

            const rate = Math.min(100, Math.max(0, data.rate)).toFixed(2);
            const cancelledCount = data.cancelled;
            const successfulCount = data.successful;
            const remaining = (100 - rate).toFixed(2);

            const ctx = document.getElementById("cancellationChart").getContext("2d");

            let color = "#FFDB7D"; // Soft Yellow-Orange (Lighter than FFCD56)
            if (rate >= 30) color = "#FF914D"; // Deep Orange (Smoother than FF9F40)
            if (rate >= 50) color = "#FF4D6D"; // Strong Coral Red (More balanced than FF6384)


            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color);
            gradient.addColorStop(1, hexToRgba(color, 0.6)); // Add transparency effect



            if (window.cancellationChart instanceof Chart) {
                window.cancellationChart.destroy();
            }


            window.cancellationChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: ["Cancelled & No-Show", "Successful Bookings"],
                    datasets: [{
                        data: [rate, remaining],
                        backgroundColor: [gradient, "#36A2EB"], // Apply dynamic gradient color
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
                            callbacks: {
                                label: function (tooltipItem) {
                                    const index = tooltipItem.dataIndex;
                                    return index === 0
                                        ? `Cancelled: ${cancelledCount} (${rate}%)`
                                        : `Successful: ${successfulCount} (${remaining}%)`;
                                }
                            }
                        }
                    }
                }
            });

            const chartContainer = document.getElementById("cancellationChart").parentNode;
            let centerText = document.getElementById("cancellationRateText");

            if (!centerText) {
                centerText = document.createElement("div");
                centerText.id = "cancellationRateText";
                centerText.style.position = "absolute";
                centerText.style.top = "50%";
                centerText.style.left = "50%";
                centerText.style.transform = "translate(-50%, -50%)";
                centerText.style.fontWeight = "bold";
                centerText.style.textAlign = "center";
                centerText.style.pointerEvents = "none";
                chartContainer.appendChild(centerText);
            }

            centerText.style.color = color;
            centerText.style.fontSize = rate >= 50 ? "45px" : "40px";
            centerText.innerHTML = `${rate}%`;
        })
        .catch(error => console.error("Fetch error:", error.message));
}


function fetchBookingLeadTime(year, month, guestType, roomNumber) {
    const chartContainer = document.getElementById("bookingLeadTimeChart").closest(".analytics-content");
    const ctx = document.getElementById("bookingLeadTimeChart").getContext("2d");

    // Show loading indicator
    const loader = document.createElement("div");
    loader.className = "chart-loading";
    chartContainer.appendChild(loader);

    fetch(`/Alumni-CvSU/admin/analytics/booking_lead_time.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            const bins = {
                "0-1 Day": 0,
                "2-3 Days": 0,
                "4-7 Days": 0,
                "8-14 Days": 0,
                "15+ Days": 0
            };

            let totalBookings = 0;

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(leadTime => {
                    if (leadTime <= 1) bins["0-1 Day"]++;
                    else if (leadTime <= 3) bins["2-3 Days"]++;
                    else if (leadTime <= 7) bins["4-7 Days"]++;
                    else if (leadTime <= 14) bins["8-14 Days"]++;
                    else bins["15+ Days"]++;
                });
                totalBookings = data.length;
            } else {
                console.warn("No booking lead time data available for the selected filters.");
            }

            const labels = Object.keys(bins);
            const values = Object.values(bins);

            if (window.bookingLeadTimeChart instanceof Chart) {
                window.bookingLeadTimeChart.destroy();
            }

            // Create gradient backgrounds
            const colors = ["#FF4D6D", "#FF914D", "#FFDB7D", "#4FC0A6", "#5A9AE5"];
            const gradients = labels.map((_, index) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, colors[index]);
                gradient.addColorStop(1, hexToRgba(colors[index], 0.5));
                return gradient;
            });

            window.bookingLeadTimeChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Bookings by Lead Time",
                        data: values,
                        backgroundColor: gradients,
                        borderWidth: 0,
                        borderRadius: 4,
                        hoverBorderWidth: 1,
                        hoverBorderColor: colors,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                drawBorder: false,
                                color: 'rgba(200, 200, 200, 0.15)',
                            },
                            ticks: { stepSize: 1 },
                            title: { display: true, text: "Number of Bookings" }
                        },
                        x: {
                            grid: { display: false },
                            title: { display: true, text: "Lead Time Range" }
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
                                    const percentage = totalBookings > 0
                                        ? ((count / totalBookings) * 100).toFixed(1)
                                        : 0;
                                    return `${count} bookings (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Remove loading indicator
            removeLoadingIndicator("bookingLeadTimeChart");
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            removeLoadingIndicator("bookingLeadTimeChart");
            showErrorMessage("bookingLeadTimeChart", "Failed to load booking lead time data");
        });
}

function fetchPeakBookingHours(year, month, guestType, roomNumber) {
    const chartContainer = document.getElementById("peakBookingChart");
    const ctx = chartContainer.getContext("2d");

    fetch(`/Alumni-CvSU/admin/analytics/booking_peak_hours.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => response.json())
        .then(data => {
            const totalBookings = Array.isArray(data) ? data.reduce((sum, item) => sum + (parseInt(item.total, 10) || 0), 0) : 0;

            const formattedData = Array.from({ length: 24 }, (_, i) => ({
                hour: i.toString().padStart(2, "0") + ":00",
                total: 0
            }));

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    let h = parseInt(item.hour, 10);
                    if (!isNaN(h) && h >= 0 && h < 24) {
                        formattedData[h].total = parseInt(item.total, 10);
                    }
                });
            }

            const labels = formattedData.map(item => item.hour);
            const values = formattedData.map(item => item.total);

            if (window.peakBookingChart instanceof Chart) {
                window.peakBookingChart.destroy();
            }


            // Create dynamic gradient background for each bar
            const gradients = labels.map(hour => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                const h = parseInt(hour.split(":")[0], 10);

                const baseColor =
                    h < 3 ? "#102A43" :  // Midnight (Deep Navy Blue)
                        h < 6 ? "#1F3A5F" :  // Pre-Dawn (Cool Dark Blue)
                            h < 8 ? "#FFB547" :  // Sunrise (Soft Golden Orange)
                                h < 11 ? "#FFD23F" : // Morning Sun (Warm Yellow)
                                    h < 14 ? "#FFC300" : // Midday Sun (Deep Gold)
                                        h < 16 ? "#FF8F32" : // Early Afternoon (Burnt Orange)
                                            h < 18 ? "#E76F51" : // Late Afternoon (Muted Reddish Orange)
                                                h < 20 ? "#C05621" : // Sunset Glow (Rich Deep Orange)
                                                    h < 22 ? "#7B2CBF" : // Evening Twilight (Deep Purple)
                                                        "#344E5C";  // Late Night (Muted Steel Blue)

                gradient.addColorStop(0, baseColor);
                gradient.addColorStop(1, hexToRgba(baseColor, 0.5));
                return gradient;
            });


            window.peakBookingChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Total Bookings per Hour",
                        data: values,
                        backgroundColor: gradients, // Use the dynamically generated gradients
                        borderColor: "rgba(0, 0, 0, 0.1)",
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: "Number of Bookings" },
                            ticks: { stepSize: 1 },
                            grid: {
                                display: true,
                                drawBorder: false,
                                color: 'rgba(200, 200, 200, 0.15)',
                            },
                        },
                        x: {
                            grid: { display: false },
                            title: { display: true, text: "Time of Day (24-hour format)" }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    const count = tooltipItem.raw;
                                    const percentage = totalBookings > 0
                                        ? ((count / totalBookings) * 100).toFixed(1)
                                        : "0.0";
                                    return `${count} bookings (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
        });
}


// fetchBookingByDay(new Date().getFullYear(), new Date().getMonth() + 1, "All", "");
// fetchBookingByMonth(new Date().getFullYear(), "All", "");
// fetchCancellationRate(new Date().getFullYear(), new Date().getMonth() + 1, "All", "");
// fetchBookingLeadTime(new Date().getFullYear(), new Date().getMonth() + 1, "");
// fetchPeakBookingHours(new Date().getFullYear(), new Date().getMonth() + 1, "All", "");

document.getElementById("yearFilter").addEventListener("change", updateChart);
document.getElementById("monthFilter").addEventListener("change", updateChart);
document.getElementById("userTypeFilter").addEventListener("change", updateChart);
document.getElementById("roomFilter").addEventListener("change", updateChart);

function updateChart() {
    const selectedYear = document.getElementById("yearFilter").value;
    const selectedMonth = document.getElementById("monthFilter").value;
    const selecteduserType = document.getElementById("userTypeFilter").value;
    const selectedRoomNumber = document.getElementById("roomFilter").value;

    fetchBookingByDay(selectedYear, selectedMonth, selecteduserType, selectedRoomNumber);
    fetchBookingByMonth(selectedYear, selecteduserType, selectedRoomNumber);
    fetchCancellationRate(selectedYear, selectedMonth, selecteduserType, selectedRoomNumber);
    fetchBookingLeadTime(selectedYear, selectedMonth, selecteduserType, selectedRoomNumber);
    fetchPeakBookingHours(selectedYear, selectedMonth, selecteduserType, selectedRoomNumber);
}

window.onload = function () {
    updateChart();
}