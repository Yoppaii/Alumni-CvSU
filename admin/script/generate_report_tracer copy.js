document.getElementById("printReportTracer").addEventListener("click", async function () {
    // Utility functions - place at the beginning
    function hasNonZeroData(data, valueKey = 'total') {
        if (!data || !Array.isArray(data) || data.length === 0) return false;
        return data.some(item => parseInt(item[valueKey] || 0) > 0);
    }

    function shortenCampusName(campusName) {
        if (campusName && campusName.includes("Cavite State University - ")) {
            return campusName.replace("Cavite State University - ", "");
        }
        return campusName;
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

    function drawTable(headers, rows, startY, includeTotal = false) {
        let y = startY + 5;

        // Table header
        doc.setFont("helvetica", "bold");
        headers.forEach(header => {
            doc.text(header.text, header.x, y);
        });

        doc.setLineWidth(0.1);
        doc.line(margin, y + 2, margin + contentWidth, y + 2);
        y += 6;

        // Table rows
        doc.setFont("helvetica", "normal");
        let total = 0;
        rows.forEach(row => {
            row.forEach(cell => {
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

    // Fix 2: Declare dataDisplayed variable
    let dataDisplayed = false;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("p", "mm", "a4");

    // Page dimensions
    const pageWidth = doc.internal.pageSize.width;
    const margin = 15;
    const contentWidth = pageWidth - (margin * 2);

    // Fix 1: Correct variable declaration syntax
    const campus = document.getElementById("campusFilter")?.value;
    const course = document.getElementById("courseFilter")?.value;
    const fromYear = document.getElementById("fromYearFilter")?.value;
    const toYear = document.getElementById("toYearFilter")?.value;
    const employmentStatus = document.getElementById("employmentStatusFilter")?.value;

    // Get selected charts
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
    doc.text("Bahay ng Alumni - Total Graduates Report", margin + 35, y + 15, { baseline: 'middle' });

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

    // Fix 3: Correct the typo in variable name
    const filters = [
        { label: "Campus", value: campus || "All Campus" },
        { label: "Course", value: course || "All Course" },
        { label: "From Year", value: fromYear || "All From Years" },
        { label: "To Year", value: toYear || "All To Years" },
        { label: "Employment Status", value: employmentStatus || "All Employment Status" }
    ];

    const params = new URLSearchParams({
        campus: campus,
        course: course,
        employmentStatus: employmentStatus,
        fromYear: fromYear,
        toYear: toYear
    });

    doc.text(`Campus: ${shortenCampusName(filters[0].value) || "All Campus"}`, margin + 2, y + 5);
    doc.text(`Employment Status: ${filters[4].value || "All Status"}`, margin + (contentWidth / 2), y + 5);
    y += 8;

    doc.text(`Course: ${filters[1].value || "All Course"}`, margin + 2, y + 5);
    doc.text(`From Year: ${filters[2].value || "All Year"}  To Year: ${filters[3].value || "All Year"}`, margin + (contentWidth / 2), y + 5);
    y += 10;


    try {
        // Define API endpoints using the filters
        const apiEndpoints = {
            totalGraduates: `/Alumni-CvSU/admin/website/ajax/total_graduates.php?${params.toString()}`,
            employmentRate: `/Alumni-CvSU/admin/website/ajax/employment_rate.php?${params.toString()}`,
            workLocations: `/Alumni-CvSU/admin/website/ajax/employment_location.php?${params.toString()}`,
            jobSearchMethod: `/Alumni-CvSU/admin/website/ajax/job_search_methods.php?${params.toString()}`,
            timeToLandFirstJob: `/Alumni-CvSU/admin/website/ajax/employment_time.php?${params.toString()}`,
            courseRelevanceImpactOnSalary: `/Alumni-CvSU/admin/website/ajax/course_relevance_salary.php?${params.toString()}`
        };

        // Only fetch selected data
        const apiPromises = {};

        selectedCharts.forEach(chart => {
            if (apiEndpoints[chart]) {
                apiPromises[chart] = fetch(apiEndpoints[chart])
                    .then(response => {
                        console.log(`Response for ${chart}:`, response); // Debug logging
                        return response.ok ? response.json() : [];
                    })
                    .catch(error => {
                        console.error(`Error fetching ${chart}:`, error); // Debug logging
                        return [];
                    });
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
            { text: "Count", x: margin + 60 },
            { text: "Percentage", x: margin + 100 }
        ];

        // Section: Graduates by Campus
        if (data.graduatesData && hasNonZeroData(data.graduatesData, 'total_graduates')) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("TOTAL GRADUATES PER CAMPUS", y);

            // Calculate total for percentage
            const totalGraduates = data.graduatesData.reduce((sum, item) => {
                return sum + parseInt(item.total || item.total_graduates || 0);
            }, 0);

            // Prepare table rows
            const rows = data.graduatesData.map(row => {
                const totalValue = parseInt(row.total || row.total_graduates || 0);
                const percentage = totalGraduates > 0
                    ? ((totalValue / totalGraduates) * 100).toFixed(1)
                    : "0.0";
                return [
                    { text: shortenCampusName(row.campus) || "Unknown", x: margin + 2 },
                    { text: totalValue.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                ];
            });
            y = drawTable(standardHeaders, rows, y, false);
        }

        // Section: Employment Rate
        if (data.employmentData && typeof data.employmentData.employed === "number" &&
            typeof data.employmentData.unemployed === "number" &&
            (data.employmentData.employed > 0 || data.employmentData.unemployed > 0)) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("EMPLOYMENT RATE SUMMARY", y);
            const total = data.employmentData.employed + data.employmentData.unemployed;
            const employmentRate = total > 0 ? ((data.employmentData.employed / total) * 100).toFixed(2) : "0.00";
            const unemploymentRate = (100 - parseFloat(employmentRate)).toFixed(2);

            // Prepare rows - only include rows with non-zero values
            const rows = [];
            if (data.employmentData.employed > 0) {
                rows.push([
                    { text: "Employed", x: margin + 2 },
                    { text: data.employmentData.employed.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${employmentRate}%`, x: margin + 100, options: { align: 'left' } }
                ]);
            }
            if (data.employmentData.unemployed > 0) {
                rows.push([
                    { text: "Unemployed", x: margin + 2 },
                    { text: data.employmentData.unemployed.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${unemploymentRate}%`, x: margin + 100, options: { align: 'left' } }
                ]);
            }
            y = drawTable(standardHeaders, rows, y, false);
        }

        // Section: Employment by Location
        if (data.locationData && hasNonZeroData(data.locationData, 'total_employees')) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("EMPLOYMENT BY LOCATION", y);

            // Define the expected order of locations
            const locationOrder = ["Local", "Abroad", "Work From Home", "Hybrid"];
            // Ensure the data is in the correct order
            const orderedData = locationOrder.map(location => {
                const found = data.locationData.find(item => item.location === location);
                return found ? found : { location, total_employees: 0 };
            });

            // Calculate total for percentage (only from non-zero items)
            const totalEmployees = orderedData.reduce((sum, item) => {
                const count = parseInt(item.total_employees || 0);
                return count > 0 ? sum + count : sum;
            }, 0);

            // Prepare table rows
            const rows = orderedData.map(item => {
                const count = parseInt(item.total_employees || 0);
                const percentage = totalEmployees > 0
                    ? ((count / totalEmployees) * 100).toFixed(1)
                    : "0.0";
                return [
                    { text: item.location, x: margin + 2 },
                    { text: count.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                ];
            });
            y = drawTable(standardHeaders, rows, y, false);
        }

        // Section: Job Search Method
        if (data.jobData) {
            // Check if any job search method has data
            const jobMethods = ["job_fair", "advertisement", "recommendation", "walk_in", "online"];
            const hasJobData = jobMethods.some(method => parseInt(data.jobData[method] || 0) > 0);
            if (hasJobData) {
                dataDisplayed = true;
                y = checkForNewPage(y, 40);
                y = drawSectionHeader("JOB SEARCH METHOD", y);

                // Define job search method labels
                const labels = ["Job Fair", "Advertisement", "Recommendation", "Walk-in Application", "Online Job Portal"];
                // Calculate total graduates from non-zero entries
                const totalGraduates = jobMethods.reduce((sum, method) => {
                    const count = parseInt(data.jobData[method] || 0);
                    return sum + count;
                }, 0);

                // Prepare table rows
                const rows = jobMethods.map((method, index) => {
                    const count = parseInt(data.jobData[method] || 0);
                    const percentage = totalGraduates > 0
                        ? ((count / totalGraduates) * 100).toFixed(1)
                        : "0.0";
                    return [
                        { text: labels[index], x: margin + 2 }, // Get corresponding label
                        { text: count.toString(), x: margin + 60, options: { align: 'left' } },
                        { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                    ];
                });
                y = drawTable(standardHeaders, rows, y, false);
            }
        }

        // Section: Employment Time
        if (data.employmentTimeData && hasNonZeroData(data.employmentTimeData, 'alumni_count')) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("TIME TO LAND FIRST JOB", y);

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
            timeKeys.forEach((key) => {
                const found = data.employmentTimeData.find(item => item.time_to_land === key);
                const count = found ? parseInt(found.alumni_count) : 0;
                totalAlumni += count;
                processedData.push({
                    time_period: keyToLabel[key],
                    alumni_count: count
                });
            });

            // Prepare table rows
            const rows = processedData.map(item => {
                const percentage = totalAlumni > 0
                    ? ((item.alumni_count / totalAlumni) * 100).toFixed(1)
                    : "0.0";
                return [
                    { text: item.time_period, x: margin + 2 },
                    { text: item.alumni_count.toString(), x: margin + 60, options: { align: 'left' } },
                    { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                ];
            });
            const timeHeaders = [
                { text: "Time Period", x: margin + 2 },
                { text: "Count", x: margin + 60 },
                { text: "Percentage", x: margin + 100 }
            ];
            y = drawTable(timeHeaders, rows, y, false);
        }

        // Section: Course Relevance and Salary
        if (data.courseRelevanceSalaryData && Array.isArray(data.courseRelevanceSalaryData)) {
            // First, let's check if we have any non-zero data to display
            let hasRelevanceData = false;
            const salaryLabels = ["<20,000", "20,000 - 30,000", "30,000 - 40,000", "40,000+"];
            // Process data by course relevance and salary range
            const relatedByRange = {};
            const unrelatedByRange = {};
            let totalAlumni = 0;
            salaryLabels.forEach(range => {
                relatedByRange[range] = 0;
                unrelatedByRange[range] = 0;
            });
            // Process the data and check for non-zero values
            data.courseRelevanceSalaryData.forEach(item => {
                const count = parseInt(item.alumni_count || 0);
                if (count > 0 && salaryLabels.includes(item.salary_range)) {
                    hasRelevanceData = true;
                    totalAlumni += count;
                    if (item.course_related && item.course_related.toLowerCase() === "yes") {
                        relatedByRange[item.salary_range] += count;
                    } else if (item.course_related && item.course_related.toLowerCase() === "no") {
                        unrelatedByRange[item.salary_range] += count;
                    }
                }
            });
            // Only display the table if we have non-zero data
            if (hasRelevanceData) {
                dataDisplayed = true;
                y = checkForNewPage(y, 50); // Increased required space
                y = drawSectionHeader("COURSE RELEVANCE BY SALARY RANGE", y);
                // Define headers for the table
                const relevanceHeaders = [
                    { text: "Salary Range", x: margin + 2 },
                    { text: "Relevance", x: margin + 60 },
                    { text: "Count", x: margin + 100 },
                    { text: "Percentage", x: margin + 130 }
                ];
                // Prepare rows with combined data (only include rows with non-zero values)
                const combinedRows = [];
                salaryLabels.forEach(range => {
                    const relatedCount = relatedByRange[range];
                    const unrelatedCount = unrelatedByRange[range];
                    // Only add related row if count is greater than 0
                    if (relatedCount > 0) {
                        const relatedPercentage = totalAlumni > 0
                            ? ((relatedCount / totalAlumni) * 100).toFixed(1)
                            : "0.0";
                        combinedRows.push([
                            { text: range, x: margin + 2 },
                            { text: "Related", x: margin + 60 },
                            { text: relatedCount.toString(), x: margin + 100, options: { align: 'left' } },
                            { text: `${relatedPercentage}%`, x: margin + 130, options: { align: 'left' } }
                        ]);
                    }
                    // Only add unrelated row if count is greater than 0
                    if (unrelatedCount > 0) {
                        const unrelatedPercentage = totalAlumni > 0
                            ? ((unrelatedCount / totalAlumni) * 100).toFixed(1)
                            : "0.0";
                        combinedRows.push([
                            { text: range, x: margin + 2 },
                            { text: "Unrelated", x: margin + 60 },
                            { text: unrelatedCount.toString(), x: margin + 100, options: { align: 'left' } },
                            { text: `${unrelatedPercentage}%`, x: margin + 130, options: { align: 'left' } }
                        ]);
                    }
                });
                // Custom function for drawing this specific table
                function drawRelevanceTable(headers, rows, startY) {
                    let y = startY + 5;
                    // Table header
                    doc.setFont("helvetica", "bold");
                    headers.forEach(header => {
                        doc.text(header.text, header.x, y);
                    });
                    doc.setLineWidth(0.1);
                    doc.line(margin, y + 2, margin + contentWidth, y + 2);
                    y += 6;
                    // Table rows
                    doc.setFont("helvetica", "normal");
                    rows.forEach(row => {
                        row.forEach(cell => {
                            doc.text(cell.text, cell.x, y, cell.options || {});
                        });
                        y += 5;
                    });
                    // No total at the bottom
                    return y;
                }
                // Only draw the table if we have rows to display
                if (combinedRows.length > 0) {
                    y = drawRelevanceTable(relevanceHeaders, combinedRows, y);
                } else {
                    // If we somehow got here but have no rows, add a message
                    doc.setFont("helvetica", "italic");
                    doc.setTextColor(100, 100, 100);
                    doc.setFontSize(10);
                    doc.text("No course relevance data available for the selected filters.", pageWidth / 2, y + 10, { align: 'center' });
                    y += 15;
                }
            }
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
        console.error("Error generating report:", error);
        y = checkForNewPage(y, 20);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(200, 0, 0);
        doc.setFontSize(12);
        doc.text("Error fetching report data.", margin, y + 10);
        doc.setTextColor(0, 0, 0);
    }

    // Save the PDF
    doc.save("Total_Graduates_Report.pdf");
});