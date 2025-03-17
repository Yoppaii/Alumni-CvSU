let bookingByDayChart = null;
let bookingByMonthChart = null;
let cancellationChart = null;
let bookingLeadTimeChart = null;
let peakBookingChart = null;

function fetchBookingByDay(year, month, guestType, roomNumber) {
    fetch(`/Alumni-CvSU/admin/analytics/booking_by_day.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => response.json())
        .then(data => {
            const weekDays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
            const colors = {
                "Monday": "#4BC0C0",
                "Tuesday": "#FF6384",
                "Wednesday": "#36A2EB",
                "Thursday": "#FFCE56",
                "Friday": "#9966FF",
                "Saturday": "#FF9F40",
                "Sunday": "#8BC34A"
            };

            const dayCounts = Object.fromEntries(weekDays.map(day => [day, 0]));

            let totalBookings = 0;

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    if (dayCounts.hasOwnProperty(item.booking_day)) {
                        dayCounts[item.booking_day] = item.total;
                        totalBookings += item.total;
                    }
                });
            } else {
                console.warn("No booking data available.");
            }

            const labels = Object.keys(dayCounts);
            const values = Object.values(dayCounts);

            if (window.bookingByDayChart instanceof Chart) {
                window.bookingByDayChart.destroy();
            }

            const ctx = document.getElementById("bookingByDayChart").getContext("2d");

            window.bookingByDayChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: `Total Bookings per Day (${guestType})`,
                        data: values,
                        backgroundColor: labels.map(day => colors[day]),
                        borderWidth: 1
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
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            title: { display: true, text: "Day of the Week" }
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
                                        : 0;
                                    return `${count} bookings (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

        })
        .catch(error => console.error("Fetch error:", error.message));
}


function fetchBookingByMonth(year, guestType, roomNumber) {
    fetch(`/Alumni-CvSU/admin/analytics/booking_by_month.php?year=${year}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => response.json())
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
                console.warn("No booking data available.");
            }

            console.log("Total Bookings:", totalBookings);
            console.log("Month Counts:", monthCounts);

            const labels = Object.keys(monthCounts);
            const values = Object.values(monthCounts);

            if (window.bookingByMonthChart instanceof Chart) {
                window.bookingByMonthChart.destroy();
            }

            const ctx = document.getElementById("bookingByMonthChart").getContext("2d");

            window.bookingByMonthChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Total Bookings per Month",
                        data: values,
                        backgroundColor: "rgba(54, 162, 235, 0.2)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: values.map(value => (value === 0 ? 0 : 5)),
                        borderDash: values.every(value => value === 0) ? [5, 5] : []
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
                            suggestedMax: Math.max(...values) + 1,
                            title: { display: true, text: "Number of Bookings" },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            title: { display: true, text: "Month" }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    const count = Number(tooltipItem.raw) || 0;
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
        })
        .catch(error => console.error("Fetch error:", error.message));
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


            let color = "#36A2EB";
            if (rate >= 30) color = "#FF9F40";
            if (rate >= 50) color = "#FF6384";

            if (window.cancellationChart instanceof Chart) {
                window.cancellationChart.destroy();
            }

            const ctx = document.getElementById("cancellationChart").getContext("2d");

            window.cancellationChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: ["Cancelled & No-Show", "Successful Bookings"],
                    datasets: [{
                        data: [rate, remaining],
                        backgroundColor: ["#FF6384", "#36A2EB"],
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
    const chartContainer = document.getElementById("bookingLeadTimeChart");
    const ctx = chartContainer.getContext("2d");

    fetch(`/Alumni-CvSU/admin/analytics/booking_lead_time.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => response.json())
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
            }

            const labels = Object.keys(bins);
            const values = Object.values(bins);

            if (window.bookingLeadTimeChart instanceof Chart) {
                window.bookingLeadTimeChart.destroy();
            }

            window.bookingLeadTimeChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Bookings by Lead Time",
                        data: values,
                        backgroundColor: ["#FF6384", "#FF9F40", "#FFCD56", "#4BC0C0", "#36A2EB"],
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
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            title: { display: true, text: "Lead Time Range" }
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
                                        : 0;
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

function fetchPeakBookingHours(year, month, guestType, roomNumber) {
    fetch(`/Alumni-CvSU/admin/analytics/booking_peak_hours.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
        .then(response => response.json())
        .then(data => {
            console.log("Raw Data from PHP:", data); // Debugging

            if (!Array.isArray(data) || data.length === 0) {
                console.warn("No booking data available.");
                return;
            }

            let formattedData = Array.from({ length: 24 }, (_, i) => ({
                hour: i.toString().padStart(2, "0") + ":00",
                total: 0
            }));

            data.forEach(item => {
                let h = parseInt(item.hour, 10);
                if (!isNaN(h) && h >= 0 && h < 24) {
                    formattedData[h].total = parseInt(item.total, 10);
                }
            });

            const labels = formattedData.map(item => item.hour);
            const values = formattedData.map(item => item.total);
            const totalBookings = values.reduce((sum, val) => sum + Number(val), 0);
            console.log("Total Bookings:", totalBookings); // Debugging

            if (window.peakBookingChart instanceof Chart) {
                window.peakBookingChart.destroy();
            }

            const ctx = document.getElementById("peakBookingChart").getContext("2d");

            window.peakBookingChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Total Bookings per Hour",
                        data: values,
                        backgroundColor: labels.map(hour => {
                            const h = parseInt(hour.split(":")[0], 10);
                            return h < 3 ? "#1B2631" :  // Midnight (Deep Dark Blue)
                                h < 6 ? "#283747" :  // Pre-Dawn (Faint Blue-Black)
                                    h < 8 ? "#F39C12" :  // Sunrise (Golden Orange)
                                        h < 11 ? "#F1C40F" : // Morning Sun (Bright Yellow)
                                            h < 14 ? "#FFD700" : // Midday Sun (Intense Yellow)
                                                h < 16 ? "#FFA500" : // Early Afternoon (Deep Orange)
                                                    h < 18 ? "#FF8C00" : // Late Afternoon (Reddish Orange)
                                                        h < 20 ? "#D35400" : // Sunset Glow (Deep Orange-Red)
                                                            h < 22 ? "#8E44AD" : // Evening Twilight (Purple-Blue)
                                                                "#2C3E50";  // Late Night (Dark Blue)
                        }),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: "Number of Bookings" },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            title: { display: true, text: "Time of Day (24-hour format)" }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    const count = tooltipItem.raw || 0;
                                    console.log(`Tooltip Debug: count=${count}, totalBookings=${totalBookings}`);

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
        .catch(error => console.error("Fetch error:", error.message));
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