// Replace the existing event listener function with this optimized version
document.getElementById("printReport").addEventListener("click", async function () {
    // Utility functions - place at the beginning
    function hasNonZeroData(data, valueKey = 'total') {
        if (!data || !Array.isArray(data) || data.length === 0) return false;
        return data.some(item => parseInt(item[valueKey] || 0) > 0);
    }

    function hasCancellationData(data) {
        if (!data || typeof data !== 'object') return false;
        const cancelled = parseInt(data.cancelled || 0);
        const noShows = parseInt(data.no_shows || 0);
        return cancelled > 0 || noShows > 0;
    }

    function getMonthName(monthNum) {
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        return monthNum >= 1 && monthNum <= 12 ? monthNames[monthNum - 1] : "All";
    }

    function drawSectionHeader(title, yPos) {
        doc.setFillColor(52, 73, 94); // Dark blue background
        doc.rect(margin, yPos, contentWidth, 8, 'F');
        doc.setFont("helvetica", "bold");
        doc.setTextColor(255, 255, 255); // White text
        doc.setFontSize(10);
        doc.text(title, margin + 2, yPos + 5.5);
        doc.setTextColor(0, 0, 0); // Reset text color
        return yPos + 8; // Return the new y position
    }

    function checkForNewPage(currentY, requiredSpace) {
        const pageHeight = doc.internal.pageSize.height;
        if (currentY + requiredSpace > pageHeight - margin) {
            doc.addPage();
            return margin; // Reset to top of new page
        }
        return currentY; // No new page needed
    }

    function drawTable(headers, rows, startY, includeTotal = true) {
        let y = startY + 5;

        // Table header
        doc.setFont("helvetica", "bold");
        headers.forEach((header, index) => {
            doc.text(header.text, header.x, y);
        });

        doc.setLineWidth(0.1);
        doc.line(margin, y + 2, margin + contentWidth, y + 2);
        y += 6;

        // Table rows
        doc.setFont("helvetica", "normal");

        let total = 0;
        rows.forEach(row => {
            row.forEach((cell, index) => {
                doc.text(cell.text, cell.x, y, cell.options || {});
            });

            // Track totals if the second column contains numeric values
            if (row.length > 1 && !isNaN(parseInt(row[1].text))) {
                total += parseInt(row[1].text);
            }

            y += 5;
        });

        // Total row if requested
        if (includeTotal) {
            doc.line(margin, y, margin + contentWidth, y);
            doc.setFont("helvetica", "bold");
            y += 5;
            doc.text("Total", margin + 2, y);
            doc.text(total.toString(), margin + 60, y, { align: 'left' });
            doc.text("100.0%", margin + 100, y, { align: 'left' });
            y += 10;
        }

        return y;
    }

    function formatTo12Hour(hour) {
        let period = hour >= 12 ? "PM" : "AM";
        let formattedHour = hour % 12 || 12; // Convert 0 to 12
        return `${formattedHour}:00 ${period}`;
    }

    // Initialize PDF document
    let dataDisplayed = false;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("p", "mm", "a4");

    // Page dimensions
    const pageWidth = doc.internal.pageSize.width;
    const margin = 15;
    const contentWidth = pageWidth - (margin * 2);

    // Get filter values
    const year = document.getElementById("yearFilter").value;
    const month = document.getElementById("monthFilter").value;
    const guestType = document.getElementById("userTypeFilter").value;
    const roomNumber = document.getElementById("roomFilter").value;

    // Get selected charts from checkboxes
    const selectedCharts = Array.from(document.querySelectorAll('.report-checkbox:checked')).map(cb => cb.value);

    // Starting position
    let y = margin;

    // Add document header (logo and title)
    try {
        const logoPath = '/Alumni-CvSU/asset/images/res1.png';
        doc.addImage(logoPath, 'PNG', margin, y, 30, 30);
    } catch (error) {
        // Fallback if image fails to load
        doc.setDrawColor(200, 200, 200);
        doc.setFillColor(245, 245, 245);
        doc.roundedRect(margin, y, 30, 30, 2, 2, 'FD');
        doc.setFontSize(8);
        doc.setTextColor(100, 100, 100);
        doc.text("LOGO", margin + 15, y + 15, { align: 'center', baseline: 'middle' });
    }

    // Title
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.setTextColor(0, 0, 0);
    doc.text("Bahay ng Alumni - Booking Report", margin + 35, y + 15, { baseline: 'middle' });

    // Get current date & time in Manila timezone
    const today = new Date();
    const options = {
        timeZone: "Asia/Manila",
        year: "numeric", month: "long", day: "numeric",
        hour: "2-digit", minute: "2-digit", hour12: true
    };
    const formattedDate = new Intl.DateTimeFormat("en-US", options).format(today);

    // Add date generated
    doc.setFontSize(9);
    doc.setFont("helvetica", "normal");
    doc.text(`Generated: ${formattedDate}`, pageWidth - margin, y + 30, { align: 'right' });
    y += 40;

    // Filters Section
    doc.setFillColor(230, 230, 230);
    doc.rect(margin, y, contentWidth, 8, 'F');
    doc.setFont("helvetica", "bold");
    doc.setFontSize(10);
    doc.text("REPORT FILTERS", margin + 2, y + 5);
    y += 8;

    // Format filters as key-value pairs
    doc.setFont("helvetica", "normal");
    doc.setFontSize(9);

    const filters = [
        { label: "Year", value: year || "All" },
        { label: "Month", value: month ? getMonthName(parseInt(month)) : "All" },
        { label: "Guest Type", value: guestType || "All" },
        { label: "Room Number", value: roomNumber || "All" }
    ];

    // First row
    doc.text(`Year: ${filters[0].value}`, margin + 2, y + 5);
    doc.text(`Month: ${filters[1].value}`, margin + (contentWidth / 2), y + 5);
    y += 8;

    // Second row
    doc.text(`Guest Type: ${filters[2].value}`, margin + 2, y + 5);
    doc.text(`Room Number: ${filters[3].value}`, margin + (contentWidth / 2), y + 5);
    y += 10;

    try {
        // Improved data fetching with efficient API calls
        const apiEndpoints = {
            dailyBookings: `/Alumni-CvSU/admin/analytics/booking_by_day.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`,
            monthlyBookings: `/Alumni-CvSU/admin/analytics/booking_by_month.php?year=${year}&guest_type=${guestType}&room_number=${roomNumber}`,
            leadTimeData: `/Alumni-CvSU/admin/analytics/booking_lead_time.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`,
            peakHoursData: `/Alumni-CvSU/admin/analytics/booking_peak_hours.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`,
            cancellationData: `/Alumni-CvSU/admin/analytics/cancellation_rate.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`
        };

        // Only fetch selected data
        const apiPromises = {};

        selectedCharts.forEach(chart => {
            if (apiEndpoints[chart]) {
                apiPromises[chart] = fetch(apiEndpoints[chart])
                    .then(res => res.json())
                    .catch(() => chart === 'cancellationData' ? {} : []);
            }
        });

        // Fetch data in parallel
        const results = await Promise.all(
            Object.values(apiPromises).map(p => p.catch(err => null))
        );

        // Map results back to named properties
        const data = {};
        Object.keys(apiPromises).forEach((key, index) => {
            data[key] = results[index];
        });

        // Common table headers for similar sections
        const standardHeaders = [
            { text: "Category", x: margin + 2 },
            { text: "Bookings", x: margin + 60 },
            { text: "Percentage", x: margin + 100 }
        ];

        // Section: Booking by Day
        if (data.dailyBookings && hasNonZeroData(data.dailyBookings)) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("BOOKINGS BY DAY", y);

            // Sort days
            const daysOrder = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const sortedDailyBookings = [...data.dailyBookings].sort((a, b) =>
                daysOrder.indexOf(a.booking_day) - daysOrder.indexOf(b.booking_day)
            );

            // Calculate total for percentage
            const totalDailyBookings = sortedDailyBookings.reduce((sum, item) => sum + parseInt(item.total), 0);

            // Prepare table rows
            const rows = sortedDailyBookings.map(row => {
                const bookingCount = parseInt(row.total);
                const percentage = totalDailyBookings > 0
                    ? ((bookingCount / totalDailyBookings) * 100).toFixed(1)
                    : "0.0";

                return [
                    { text: row.booking_day, x: margin + 2 },
                    { text: bookingCount.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                ];
            });

            y = drawTable(standardHeaders, rows, y);
        }

        // Section: Booking by Month
        if (data.monthlyBookings && hasNonZeroData(data.monthlyBookings)) {
            dataDisplayed = true;
            y = checkForNewPage(y, 45);
            y = drawSectionHeader("MONTHLY BOOKINGS", y);

            // Sort months
            const monthsOrder = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            const sortedMonthlyBookings = [...data.monthlyBookings].sort((a, b) =>
                monthsOrder.indexOf(a.month) - monthsOrder.indexOf(b.month)
            );

            // Calculate total for percentage
            const totalMonthlyBookings = sortedMonthlyBookings.reduce((sum, item) => sum + parseInt(item.total), 0);

            // Prepare table rows
            const rows = sortedMonthlyBookings.map(row => {
                const bookingCount = parseInt(row.total);
                const percentage = totalMonthlyBookings > 0
                    ? ((bookingCount / totalMonthlyBookings) * 100).toFixed(1)
                    : "0.0";

                return [
                    { text: row.month, x: margin + 2 },
                    { text: bookingCount.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                ];
            });

            y = drawTable(standardHeaders, rows, y);
        }

        // Section: Lead Time Analysis
        if (data.leadTimeData && data.leadTimeData.length > 0) {
            dataDisplayed = true;
            y = checkForNewPage(y, 45);
            y = drawSectionHeader("LEAD TIME BEFORE ARRIVAL", y);

            // Lead time bins
            const bins = {
                "0-1 Day": 0,
                "2-3 Days": 0,
                "4-7 Days": 0,
                "8-14 Days": 0,
                "15+ Days": 0
            };

            let totalBookings = data.leadTimeData.length;

            // Categorizing lead times into bins
            data.leadTimeData.forEach(leadTime => {
                if (leadTime <= 1) bins["0-1 Day"]++;
                else if (leadTime <= 3) bins["2-3 Days"]++;
                else if (leadTime <= 7) bins["4-7 Days"]++;
                else if (leadTime <= 14) bins["8-14 Days"]++;
                else bins["15+ Days"]++;
            });

            // Prepare table rows
            const rows = Object.entries(bins).map(([label, count]) => {
                const percentage = totalBookings > 0
                    ? ((count / totalBookings) * 100).toFixed(1)
                    : "0.0";

                return [
                    { text: label, x: margin + 2 },
                    { text: count.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                ];
            });

            y = drawTable(standardHeaders, rows, y);
        }

        // Section: Peak Booking Hours
        if (data.peakHoursData && hasNonZeroData(data.peakHoursData)) {
            dataDisplayed = true;
            y = checkForNewPage(y, 45);
            y = drawSectionHeader("TOP 5 PEAK BOOKING HOURS", y);

            // Convert and sort by highest bookings
            let formattedPeakHours = data.peakHoursData
                .map(row => ({
                    hour: parseInt(row.hour, 10),
                    total: parseInt(row.total, 10)
                }))
                .sort((a, b) => b.total - a.total);

            // Calculate total for percentage
            const totalPeakBookings = formattedPeakHours.reduce((sum, item) => sum + item.total, 0);

            // Prepare table rows
            const rows = formattedPeakHours.map(row => {
                const percentage = totalPeakBookings > 0
                    ? ((row.total / totalPeakBookings) * 100).toFixed(1)
                    : "0.0";

                return [
                    { text: formatTo12Hour(row.hour), x: margin + 2 },
                    { text: row.total.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                ];
            });

            y = drawTable(standardHeaders, rows, y);
        }

        // Section: Cancellation & No-Show Rate
        if (data.cancellationData && hasCancellationData(data.cancellationData)) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("CANCELLATIONS & NO-SHOWS", y);

            const cancelled = data.cancellationData.cancelled ? parseInt(data.cancellationData.cancelled) : 0;
            const noShows = data.cancellationData.no_shows ? parseInt(data.cancellationData.no_shows) : 0;
            const totalBookings = data.cancellationData.total_bookings ? parseInt(data.cancellationData.total_bookings) : 0;
            const rate = isNaN(data.cancellationData.rate) ? 0 : parseFloat(data.cancellationData.rate).toFixed(2);

            // Table style presentation
            y += 10;
            doc.setFont("helvetica", "normal");
            doc.setFontSize(9);

            const tableData = [
                { label: "Total Bookings", value: totalBookings },
                { label: "Cancellations", value: cancelled },
                { label: "No-Shows", value: noShows },
                { label: "Combined Rate", value: `${rate}%` },
            ];

            tableData.forEach((row, index) => {
                // Alternate row colors for better readability
                if (index % 2 === 0) {
                    doc.setFillColor(245, 245, 245);
                    doc.rect(margin, y - 4, contentWidth, 6, 'F');
                }
                doc.text(row.label, margin + 2, y);
                doc.text(row.value.toString(), margin + contentWidth - 2, y, { align: 'right' });
                y += 6;
            });

            y += 8;
        }

        // No data message
        if (!selectedCharts.length || !dataDisplayed) {
            y = checkForNewPage(y, 20);
            doc.setFont("helvetica", "italic");
            doc.setTextColor(100, 100, 100);
            doc.setFontSize(11);

            const message = !selectedCharts.length
                ? "No charts were selected for this report."
                : "No data available for the selected filters.";

            doc.text(message, pageWidth / 2, y + 15, { align: 'center' });
            y += 30;
        }

        // Add page number footer on each page
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(100, 100, 100);
            doc.text(`Page ${i} of ${pageCount}`, pageWidth - margin, doc.internal.pageSize.height - 10, { align: 'right' });
        }
    } catch (error) {
        console.error("Error fetching report data:", error);
        y = pageHeight(y, 20);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(200, 0, 0);
        doc.setFontSize(12);
        doc.text("Error fetching report data.", margin, y + 10);
        doc.setTextColor(0, 0, 0);
    }

    // Save the PDF
    doc.save("Bahay_ng_Alumni_Booking_Report.pdf");
});