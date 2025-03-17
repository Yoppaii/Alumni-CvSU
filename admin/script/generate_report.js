document.getElementById("printReport").addEventListener("click", async function () {
    const { jsPDF } = window.jspdf;

    // Create PDF document
    const doc = new jsPDF("p", "mm", "a4");

    // Page dimensions
    const pageWidth = doc.internal.pageSize.width;
    const margin = 15;
    const contentWidth = pageWidth - (margin * 2);

    // Get filter values, default to "All" if empty
    const year = document.getElementById("yearFilter").value;
    const month = document.getElementById("monthFilter").value;
    const guestType = document.getElementById("userTypeFilter").value;
    const roomNumber = document.getElementById("roomFilter").value;

    // Starting position
    let y = margin;


    // Add actual logo instead of placeholder
    const logoPath = '/Alumni-CvSU/asset/images/res1.png';
    const logoWidth = 30;
    const logoHeight = 30;

    // Add logo image (using addImage method)
    try {
        doc.addImage(logoPath, 'PNG', margin, y, logoWidth, logoHeight);
    } catch (error) {
        console.error("Error loading logo:", error);
        // Fallback if image fails to load
        doc.setDrawColor(200, 200, 200);
        doc.setFillColor(245, 245, 245);
        doc.roundedRect(margin, y, logoWidth, logoHeight, 2, 2, 'FD');
        doc.setFontSize(8);
        doc.setTextColor(100, 100, 100);
        doc.text("LOGO", margin + (logoWidth / 2), y + (logoHeight / 2), { align: 'center', baseline: 'middle' });
    }

    // Title
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.setTextColor(0, 0, 0);
    doc.text("Bahay ng Alumni - Booking Report", margin + 35, y + (logoHeight / 2), { baseline: 'middle' });

    // Get current date & time in Manila timezone
    const today = new Date();
    const options = {
        timeZone: "Asia/Manila",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: true // 12-hour format
    };

    const formattedDate = new Intl.DateTimeFormat("en-US", options).format(today);

    // Add date generated in Manila time (without seconds)
    doc.setFontSize(9);
    doc.setFont("helvetica", "normal");
    doc.text(`Generated: ${formattedDate}`, pageWidth - margin, y + logoHeight, { align: 'right' });



    y += logoHeight + 10;

    // Helper function to convert numeric month to name
    function getMonthName(monthNum) {
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        return monthNum >= 1 && monthNum <= 12 ? monthNames[monthNum - 1] : "All";
    }

    // Filters Section - display as a table
    doc.setFillColor(230, 230, 230);
    doc.rect(margin, y, contentWidth, 8, 'F');
    doc.setFont("helvetica", "bold");
    doc.setFontSize(10);
    doc.text("REPORT FILTERS", margin + 2, y + 5);
    y += 8;

    // Format filters as key-value pairs in two columns
    doc.setFont("helvetica", "normal");
    doc.setFontSize(9);

    // Ensure empty values display as "All"
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


    // Utility function to draw section headers
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

    // Utility function to check if we need a new page
    function checkForNewPage(currentY, requiredSpace) {
        const pageHeight = doc.internal.pageSize.height;
        if (currentY + requiredSpace > pageHeight - margin) {
            doc.addPage();
            return margin; // Reset to top of new page
        }
        return currentY; // No new page needed
    }

    try {
        // Fetch data from APIs
        const [
            dailyBookings = [],
            monthlyBookings = [],
            leadTimeData = [],
            peakHoursData = [],
            cancellationData = {}
        ] = await Promise.allSettled([
            fetch(`/Alumni-CvSU/admin/analytics/booking_by_day.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
                .then(res => res.json()).catch(() => []),
            fetch(`/Alumni-CvSU/admin/analytics/booking_by_month.php?year=${year}&guest_type=${guestType}&room_number=${roomNumber}`)
                .then(res => res.json()).catch(() => []),
            fetch(`/Alumni-CvSU/admin/analytics/booking_lead_time.php?year=${year}&month=${month}&room_number=${roomNumber}`)
                .then(res => res.json()).catch(() => []),
            fetch(`/Alumni-CvSU/admin/analytics/booking_peak_hours.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
                .then(res => res.json()).catch(() => []),
            fetch(`/Alumni-CvSU/admin/analytics/cancellation_rate.php?year=${year}&month=${month}&guest_type=${guestType}&room_number=${roomNumber}`)
                .then(res => res.json()).catch(() => ({}))
        ]).then(results => results.map(r => (r.status === "fulfilled" ? r.value : [])));

        // Section: Booking by Day
        if (dailyBookings && dailyBookings.length > 0) {
            y = checkForNewPage(y, 40); // Check if we need a new page
            y = drawSectionHeader("BOOKINGS BY DAY OF THE WEEK", y);

            // Create a simple table for daily bookings
            doc.setFont("helvetica", "normal");
            doc.setFontSize(9);

            // Sort days to ensure they're in correct order (Sunday to Saturday)
            const daysOrder = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const sortedDailyBookings = [...dailyBookings].sort((a, b) =>
                daysOrder.indexOf(a.booking_day) - daysOrder.indexOf(b.booking_day)
            );

            // Calculate total for percentage
            const totalDailyBookings = sortedDailyBookings.reduce((sum, item) => sum + parseInt(item.total), 0);

            // Table header
            y += 5;
            doc.setFont("helvetica", "bold");
            doc.text("Day", margin + 2, y);
            doc.text("Bookings", margin + 60, y);
            doc.text("Percentage", margin + 100, y);
            doc.setLineWidth(0.1);
            doc.line(margin, y + 2, margin + contentWidth, y + 2);
            y += 6;

            // Table rows
            doc.setFont("helvetica", "normal");
            sortedDailyBookings.forEach(row => {
                const bookingCount = parseInt(row.total);
                const percentage = totalDailyBookings > 0 ? ((bookingCount / totalDailyBookings) * 100).toFixed(1) : 0;

                doc.text(row.booking_day, margin + 2, y);
                doc.text(bookingCount.toString(), margin + 60, y, { align: 'left' });
                doc.text(`${percentage}%`, margin + 100, y, { align: 'left' });
                y += 5;
            });

            doc.line(margin, y, margin + contentWidth, y);
            doc.setFont("helvetica", "bold");
            y += 5;
            doc.text("Total", margin + 2, y);
            doc.text(totalDailyBookings.toString(), margin + 60, y, { align: 'left' });
            doc.text("100.0%", margin + 100, y, { align: 'left' });

            y += 10;
        }

        // Section: Booking by Month
        if (monthlyBookings && monthlyBookings.length > 0) {
            y = checkForNewPage(y, 45); // Check if we need a new page
            y = drawSectionHeader("MONTHLY BOOKINGS", y);

            // Create a table for monthly bookings
            doc.setFont("helvetica", "normal");
            doc.setFontSize(9);

            // Sort months to ensure they're in correct order (January to December)
            const monthsOrder = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            const sortedMonthlyBookings = [...monthlyBookings].sort((a, b) =>
                monthsOrder.indexOf(a.month) - monthsOrder.indexOf(b.month)
            );

            // Calculate total for percentage
            const totalMonthlyBookings = sortedMonthlyBookings.reduce((sum, item) => sum + parseInt(item.total), 0);

            // Table header
            y += 5;
            doc.setFont("helvetica", "bold");
            doc.text("Month", margin + 2, y);
            doc.text("Bookings", margin + 60, y);
            doc.text("Percentage", margin + 100, y);
            doc.setLineWidth(0.1);
            doc.line(margin, y + 2, margin + contentWidth, y + 2);
            y += 6;

            // Table rows
            doc.setFont("helvetica", "normal");
            sortedMonthlyBookings.forEach(row => {
                const bookingCount = parseInt(row.total);
                const percentage = totalMonthlyBookings > 0 ? ((bookingCount / totalMonthlyBookings) * 100).toFixed(1) : 0;

                doc.text(row.month, margin + 2, y);
                doc.text(bookingCount.toString(), margin + 60, y, { align: 'left' });
                doc.text(`${percentage}%`, margin + 100, y, { align: 'left' });
                y += 5;
            });

            doc.line(margin, y, margin + contentWidth, y);
            doc.setFont("helvetica", "bold");
            y += 5;
            doc.text("Total", margin + 2, y);
            doc.text(totalMonthlyBookings.toString(), margin + 60, y, { align: 'left' });
            doc.text("100.0%", margin + 100, y, { align: 'left' });

            y += 10;
        }

        // Section: Lead Time Analysis
        if (leadTimeData && leadTimeData.length > 0) {
            y = checkForNewPage(y, 45); // Check if we need a new page
            y = drawSectionHeader("LEAD TIME BEFORE ARRIVAL", y);

            // Lead time bins
            const bins = {
                "0-1 Day": 0,
                "2-3 Days": 0,
                "4-7 Days": 0,
                "8-14 Days": 0,
                "15+ Days": 0
            };

            let totalBookings = leadTimeData.length;

            // Categorizing lead times into bins
            leadTimeData.forEach(leadTime => {
                if (leadTime <= 1) bins["0-1 Day"]++;
                else if (leadTime <= 3) bins["2-3 Days"]++;
                else if (leadTime <= 7) bins["4-7 Days"]++;
                else if (leadTime <= 14) bins["8-14 Days"]++;
                else bins["15+ Days"]++;
            });

            // Table header
            y += 5;
            doc.setFont("helvetica", "bold");
            doc.text("Lead Time", margin + 2, y);
            doc.text("Bookings", margin + 60, y);
            doc.text("Percentage", margin + 100, y);
            doc.setLineWidth(0.1);
            doc.line(margin, y + 2, margin + contentWidth, y + 2);
            y += 6;

            // Table rows
            doc.setFont("helvetica", "normal");
            Object.entries(bins).forEach(([label, count]) => {
                const percentage = totalBookings > 0 ? ((count / totalBookings) * 100).toFixed(1) : 0;

                doc.text(label, margin + 2, y);
                doc.text(count.toString(), margin + 60, y, { align: 'left' });
                doc.text(`${percentage}%`, margin + 100, y, { align: 'left' });
                y += 5;
            });

            doc.line(margin, y, margin + contentWidth, y);
            doc.setFont("helvetica", "bold");
            y += 5;
            doc.text("Total", margin + 2, y);
            doc.text(totalBookings.toString(), margin + 60, y, { align: 'left' });
            doc.text("100.0%", margin + 100, y, { align: 'left' });

            y += 10;
        }

        // Section: Peak Booking Hours
        if (peakHoursData && peakHoursData.length > 0) {
            y = checkForNewPage(y, 45); // Check if we need a new page
            y = drawSectionHeader("TOP 5 PEAK BOOKING HOURS", y);

            // Convert and sort by highest bookings
            let formattedPeakHours = peakHoursData
                .map(row => ({
                    hour: parseInt(row.hour, 10),
                    total: parseInt(row.total, 10)
                }))
                .sort((a, b) => b.total - a.total) // Sort by highest bookings
                .slice(0, 5); // Select top 5

            // Function to convert 24-hour format to 12-hour format
            function formatTo12Hour(hour) {
                let period = hour >= 12 ? "PM" : "AM";
                let formattedHour = hour % 12 || 12; // Convert 0 to 12
                return `${formattedHour}:00 ${period}`;
            }

            // Calculate total for percentage
            const totalPeakBookings = formattedPeakHours.reduce((sum, item) => sum + item.total, 0);

            // Table header
            y += 5;
            doc.setFont("helvetica", "bold");
            doc.text("Time", margin + 2, y);
            doc.text("Bookings", margin + 60, y);
            doc.text("Percentage", margin + 100, y);
            doc.setLineWidth(0.1);
            doc.line(margin, y + 2, margin + contentWidth, y + 2);
            y += 6;

            // Table rows
            doc.setFont("helvetica", "normal");
            formattedPeakHours.forEach(row => {
                const percentage = totalPeakBookings > 0 ? ((row.total / totalPeakBookings) * 100).toFixed(1) : 0;

                doc.text(formatTo12Hour(row.hour), margin + 2, y);
                doc.text(row.total.toString(), margin + 60, y, { align: 'left' });
                doc.text(`${percentage}%`, margin + 100, y, { align: 'left' });
                y += 5;
            });

            y += 10;
        }

        // Section: Cancellation & No-Show Rate
        if (cancellationData && Object.keys(cancellationData).length > 0) {
            y = checkForNewPage(y, 40); // Check if we need a new page
            y = drawSectionHeader("CANCELLATIONS & NO-SHOWS", y);

            const cancelled = cancellationData.cancelled ? parseInt(cancellationData.cancelled) : 0;
            const noShows = cancellationData.no_shows ? parseInt(cancellationData.no_shows) : 0;
            const totalBookings = cancellationData.total_bookings ? parseInt(cancellationData.total_bookings) : 0;
            const rate = isNaN(cancellationData.rate) ? 0 : parseFloat(cancellationData.rate).toFixed(2);

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
        y = checkForNewPage(y, 20);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(200, 0, 0);
        doc.setFontSize(12);
        doc.text("Error fetching report data.", margin, y + 10);
        doc.setTextColor(0, 0, 0);
    }

    // Save the PDF
    doc.save("Bahay_ng_Alumni_Booking_Report.pdf");
});