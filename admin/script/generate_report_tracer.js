document.getElementById("printReportTracer").addEventListener("click", async function () {
    // Utility functions - place at the beginning
    function hasNonZeroData(data, valueKey = 'total') {
        if (!data || !Array.isArray(data) || data.length === 0) return false;
        return data.some(item => {
            const value = parseInt(item[valueKey] || item.total_graduates || item.total_employees || 0);
            return value > 0;
        });
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
        return yPos + 8;
    }

    function checkForNewPage(currentY, requiredSpace) {
        const pageHeight = doc.internal.pageSize.height;
        if (currentY + requiredSpace > pageHeight - margin) {
            doc.addPage();
            return margin;
        }
        return currentY;
    }

    function drawTable(headers, rows, startY, includeTotal = false) {
        // Filter out rows with zero values
        const nonZeroRows = rows.filter(row => {
            return row.length > 1 && parseInt(row[1].text || 0) > 0;
        });
        // If no non-zero rows, return the current y position
        if (nonZeroRows.length === 0) {
            return startY;
        }
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
        nonZeroRows.forEach(row => {
            row.forEach(cell => {
                doc.text(cell.text, cell.x, y, cell.options || {});
            });

            if (row.length > 1 && !isNaN(parseInt(row[1].text))) {
                total += parseInt(row[1].text);
            }
            y += 5;
        });
        y += 5;

        return { yPos: y, totalCount: total };
    }

    function buildQueryString(filters) {
        const params = new URLSearchParams();
        if (filters.campus) params.append('campus', filters.campus);
        if (filters.course) params.append('course', filters.course);
        if (filters.employmentStatus) params.append('employmentStatus', filters.employmentStatus);
        if (filters.fromYear) params.append('fromYear', filters.fromYear);
        if (filters.toYear) params.append('toYear', filters.toYear);
        return params.toString();
    }

    // Initialize PDF document
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("p", "mm", "a4");

    // Page dimensions
    const pageWidth = doc.internal.pageSize.width;
    const margin = 15;
    const contentWidth = pageWidth - (margin * 2);

    // Get filter values with validation for year ranges
    const fromYearInput = document.getElementById("fromYearFilter");
    const toYearInput = document.getElementById("toYearFilter");

    let fromYear = fromYearInput?.value?.trim() || "";
    let toYear = toYearInput?.value?.trim() || "";

    // Convert to integers if values exist
    if (fromYear) fromYear = parseInt(fromYear);
    if (toYear) toYear = parseInt(toYear);

    // Validate year range (if both are provided)
    if (fromYear && toYear && fromYear > toYear) {

        [fromYear, toYear] = [toYear, fromYear];

        if (fromYearInput) fromYearInput.value = fromYear;
        if (toYearInput) toYearInput.value = toYear;
    }

    const filters = {
        campus: document.getElementById("campusFilter")?.value || "",
        course: document.getElementById("courseFilter")?.value || "",
        fromYear: fromYear,
        toYear: toYear,
        employmentStatus: document.getElementById("employmentStatusFilter")?.value || ""
    };
    // Get all selected charts
    const selectedCharts = Array.from(document.querySelectorAll('.report-checkbox:checked')).map(cb => cb.value);
    // console.log("Selected charts:", selectedCharts); // For debugging

    let dataDisplayed = false;

    let y = margin;
    // Add document header (logo and title)
    try {
        const logoPath = '/Alumni-CvSU/asset/images/res1.png';
        doc.addImage(logoPath, 'PNG', margin, y, 30, 30);
    } catch (error) {

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
    // First row
    doc.text(`Campus: ${shortenCampusName(filters.campus) || "All"}`, margin + 2, y + 5);
    doc.text(`Employment Status: ${filters.employmentStatus || "All"}`, margin + (contentWidth / 2), y + 5);
    y += 8;
    // Second row
    doc.text(`Course: ${filters.course || "All"}`, margin + 2, y + 5);
    doc.text(`From Year: ${filters.fromYear || "All"}  -  To Year: ${filters.toYear || "All"}`, margin + (contentWidth / 2), y + 5);
    y += 10;
    try {
        // Define API endpoints using the filters
        const queryString = buildQueryString(filters);
        const apiEndpoints = {
            graduatesData: `/Alumni-CvSU/admin/website/ajax/total_graduates.php?${queryString}`,
            employmentData: `/Alumni-CvSU/admin/website/ajax/employment_rate.php?${queryString}`,
            locationData: `/Alumni-CvSU/admin/website/ajax/employment_location.php?${queryString}`,
            jobData: `/Alumni-CvSU/admin/website/ajax/job_search_methods.php?${queryString}`,
            employmentTimeData: `/Alumni-CvSU/admin/website/ajax/employment_time.php?${queryString}`,
            courseRelevanceSalaryData: `/Alumni-CvSU/admin/website/ajax/course_relevance_salary.php?${queryString}`
        };

        const apiPromises = {};
        Object.keys(apiEndpoints).forEach(key => {
            apiPromises[key] = fetch(apiEndpoints[key])
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error ${response.status} when fetching ${key}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    // console.error(`Error fetching ${key}:`, error);
                    if (key === 'graduatesData') {

                        return fetch('/Alumni-CvSU/admin/website/ajax/get_campus_list.php')
                            .then(response => response.ok ? response.json() : [])
                            .catch(() => []);
                    }
                    return key === 'employmentData' ? null : [];
                });
        });
        // Fetch all data
        const results = await Promise.all(Object.values(apiPromises));
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
        // Calculate summary data for the summary section
        const summary = {
            totalGraduates: 0,
            employedGraduates: 0,
            unemployedGraduates: 0,
            employmentRate: 0,
            courseRelatedJobs: 0,
            avgTimeToLandJob: ""
        };
        // Calculate total graduates
        if (data.graduatesData && hasNonZeroData(data.graduatesData, 'total_graduates')) {
            summary.totalGraduates = data.graduatesData.reduce((sum, item) => {
                return sum + parseInt(item.total || item.total_graduates || 0);
            }, 0);
        }
        // Calculate employment data
        if (data.employmentData && typeof data.employmentData.employed === "number" && typeof data.employmentData.unemployed === "number") {
            summary.employedGraduates = data.employmentData.employed;
            summary.unemployedGraduates = data.employmentData.unemployed;
            const totalGrads = summary.employedGraduates + summary.unemployedGraduates;
            summary.employmentRate = totalGrads > 0 ? ((summary.employedGraduates / totalGrads) * 100).toFixed(2) : 0;
        }
        // Calculate course relevance
        if (data.courseRelevanceSalaryData && Array.isArray(data.courseRelevanceSalaryData)) {
            let relatedCount = 0;
            let totalCount = 0;
            data.courseRelevanceSalaryData.forEach(item => {
                const count = parseInt(item.alumni_count || 0);
                if (count > 0) {
                    totalCount += count;
                    if (item.course_related && item.course_related.toLowerCase() === "yes") {
                        relatedCount += count;
                    }
                }
            });
            if (totalCount > 0) {
                summary.courseRelatedJobs = ((relatedCount / totalCount) * 100).toFixed(2);
            }
        }
        // Calculate average time to land job (simplified representation)
        if (data.employmentTimeData && Array.isArray(data.employmentTimeData) && data.employmentTimeData.length > 0) {

            let maxCount = 0;
            let mostCommonPeriod = "";
            const keyToLabel = {
                "less_than_1month": "Less than 1 Month",
                "1_6months": "1-6 Months",
                "7_11months": "7-11 Months",
                "1year_more": "1 Year or More"
            };
            data.employmentTimeData.forEach(item => {
                const count = parseInt(item.alumni_count || 0);
                if (count > maxCount) {
                    maxCount = count;
                    mostCommonPeriod = keyToLabel[item.time_to_land] || item.time_to_land;
                }
            });
            summary.avgTimeToLandJob = mostCommonPeriod || "N/A";
        }
        // Draw Summary Section if we have data
        if (summary.totalGraduates > 0) {
            const summaryItems = [];

            if (selectedCharts.includes('totalGraduates')) {
                y = checkForNewPage(y, 40);
                y = drawSectionHeader("REPORT SUMMARY", y);
                doc.setFont("helvetica", "bold");
                doc.setFontSize(9);

                summaryItems.push({
                    label: "Total Graduates:",
                    value: summary.totalGraduates.toLocaleString()
                });

            }

            if (selectedCharts.includes('employmentRate')) {
                summaryItems.push({
                    label: "Employed Graduates:",
                    value: summary.employedGraduates.toLocaleString()
                });
                summaryItems.push({
                    label: "Employment Rate:",
                    value: `${summary.employmentRate}%`
                });
                summaryItems.push({
                    label: "Unemployed Graduates:",
                    value: summary.unemployedGraduates.toLocaleString()
                });
            }

            if (selectedCharts.includes('courseRelevanceImpactOnSalary')) {
                summaryItems.push({
                    label: "Course-Related Jobs:",
                    value: `${summary.courseRelatedJobs}%`
                });
            }

            if (selectedCharts.includes('timeToLandFirstJob')) {
                summaryItems.push({
                    label: "Most Common Time to Job:",
                    value: summary.avgTimeToLandJob
                });
            }

            if (summaryItems.length > 0) {
                const rowsNeeded = Math.ceil(summaryItems.length / 2);
                const rowHeight = 10;
                const boxHeight = rowsNeeded * rowHeight;

                doc.rect(margin, y, contentWidth, boxHeight, 'F');


                for (let i = 0; i < summaryItems.length; i++) {
                    const row = Math.floor(i / 2);
                    const col = i % 2;
                    const xPos = margin + (col * (contentWidth / 2)) + 5;
                    const yPos = y + (row * rowHeight) + 8;

                    doc.text(summaryItems[i].label, xPos, yPos);
                    doc.text(summaryItems[i].value, xPos + 45, yPos);
                }

                doc.setFont("helvetica", "normal");
                y += boxHeight + 10;
            }
        }
        // Section: Graduates by Campus
        if (data.graduatesData && hasNonZeroData(data.graduatesData, 'total_graduates') && selectedCharts.includes('totalGraduates')) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("TOTAL GRADUATES PER CAMPUS", y);

            const totalGraduates = data.graduatesData.reduce((sum, item) => {
                const total = parseInt(item.total || item.total_graduates || 0);
                return sum + total;
            }, 0);

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
            const result = drawTable(standardHeaders, rows, y, false);
            y = result.yPos;
        }
        // Section: Employment Rate
        if (data.employmentData &&
            typeof data.employmentData.employed === "number" &&
            typeof data.employmentData.unemployed === "number" &&
            (data.employmentData.employed > 0 || data.employmentData.unemployed > 0) &&
            selectedCharts.includes('employmentRate')) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("EMPLOYMENT RATE SUMMARY", y);
            const total = data.employmentData.employed + data.employmentData.unemployed;
            const employmentRate = total > 0 ? ((data.employmentData.employed / total) * 100).toFixed(2) : "0.00";
            const unemploymentRate = (100 - parseFloat(employmentRate)).toFixed(2);

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
            const result = drawTable(standardHeaders, rows, y, false);
            y = result.yPos;
        }
        // Section: Employment by Location
        if (data.locationData && hasNonZeroData(data.locationData, 'total_employees') && selectedCharts.includes('workLocations')) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("EMPLOYMENT BY LOCATION", y);

            const locationOrder = ["Local", "Abroad", "Work From Home", "Hybrid"];

            const orderedData = locationOrder.map(location => {
                const found = data.locationData.find(item => item.location === location);
                return found ? found : { location, total_employees: 0 };
            });

            const totalEmployees = orderedData.reduce((sum, item) => {
                const count = parseInt(item.total_employees || 0);
                return count > 0 ? sum + count : sum;
            }, 0);

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
            const result = drawTable(standardHeaders, rows, y, false);
            y = result.yPos;
        }
        // Section: Job Search Method
        if (data.jobData && selectedCharts.includes('jobSearchMethod')) {

            const jobMethods = ["job_fair", "advertisement", "recommendation", "walk_in", "online"];
            const hasJobData = jobMethods.some(method => parseInt(data.jobData[method] || 0) > 0);
            if (hasJobData) {
                dataDisplayed = true;
                y = checkForNewPage(y, 40);
                y = drawSectionHeader("JOB SEARCH METHOD", y);

                const labels = ["Job Fair", "Advertisement", "Recommendation", "Walk-in Application", "Online Job Portal"];

                const totalGraduates = jobMethods.reduce((sum, method) => {
                    const count = parseInt(data.jobData[method] || 0);
                    return sum + count;
                }, 0);

                const rows = jobMethods.map((method, index) => {
                    const count = parseInt(data.jobData[method] || 0);
                    const percentage = totalGraduates > 0
                        ? ((count / totalGraduates) * 100).toFixed(1)
                        : "0.0";
                    return [
                        { text: labels[index], x: margin + 2 },
                        { text: count.toString(), x: margin + 60, options: { align: 'left' } },
                        { text: `${percentage}%`, x: margin + 100, options: { align: 'left' } }
                    ];
                });
                const result = drawTable(standardHeaders, rows, y, false);
                y = result.yPos;
            }
        }
        // Section: Employment Time
        if (data.employmentTimeData && hasNonZeroData(data.employmentTimeData, 'alumni_count') && selectedCharts.includes('timeToLandFirstJob')) {
            dataDisplayed = true;
            y = checkForNewPage(y, 40);
            y = drawSectionHeader("TIME TO LAND FIRST JOB", y);

            const timeOrder = ["Less than 1 Month", "1-6 Months", "7-11 Months", "1 Year or More"];
            const timeKeys = ["less_than_1month", "1_6months", "7_11months", "1year_more"];

            const keyToLabel = {
                "less_than_1month": "Less than 1 Month",
                "1_6months": "1-6 Months",
                "7_11months": "7-11 Months",
                "1year_more": "1 Year or More"
            };
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
            const result = drawTable(timeHeaders, rows, y, false);
            y = result.yPos;
        }
        // Section: Course Relevance and Salary
        if (data.courseRelevanceSalaryData && Array.isArray(data.courseRelevanceSalaryData) && selectedCharts.includes('courseRelevanceImpactOnSalary')) {
            let hasRelevanceData = false;
            const salaryLabels = ["<20,000", "20,000 - 30,000", "30,000 - 40,000", "40,000+"];

            const relatedByRange = {};
            const unrelatedByRange = {};
            let totalAlumni = 0;

            salaryLabels.forEach(range => {
                relatedByRange[range] = 0;
                unrelatedByRange[range] = 0;
            });
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
            if (hasRelevanceData) {
                dataDisplayed = true;
                y = checkForNewPage(y, 50);
                y = drawSectionHeader("COURSE RELEVANCE BY SALARY RANGE", y);
                const relevanceHeaders = [
                    { text: "Salary Range", x: margin + 2 },
                    { text: "Relevance", x: margin + 60 },
                    { text: "Count", x: margin + 100 },
                    { text: "Percentage", x: margin + 130 }
                ];
                const combinedRows = [];
                salaryLabels.forEach(range => {
                    const relatedCount = relatedByRange[range];
                    const unrelatedCount = unrelatedByRange[range];
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
                function drawRelevanceTable(headers, rows, startY) {
                    let y = startY + 5;

                    doc.setFont("helvetica", "bold");
                    headers.forEach(header => {
                        doc.text(header.text, header.x, y);
                    });
                    doc.setLineWidth(0.1);
                    doc.line(margin, y + 2, margin + contentWidth, y + 2);
                    y += 6;

                    doc.setFont("helvetica", "normal");
                    rows.forEach(row => {
                        row.forEach(cell => {
                            doc.text(cell.text, cell.x, y, cell.options || {});
                        });
                        y += 5;
                    });

                    return y;
                }

                if (combinedRows.length > 0) {
                    y = drawRelevanceTable(relevanceHeaders, combinedRows, y);
                } else {

                    doc.setFont("helvetica", "italic");
                    doc.setTextColor(100, 100, 100);
                    doc.setFontSize(10);
                    doc.text("No course relevance data available for the selected filters.", pageWidth / 2, y + 10, { align: 'center' });
                    y += 15;
                }
            }
        }

        // Display a message if no data or no charts were selected
        if (!dataDisplayed) {
            y = checkForNewPage(y, 20);
            doc.setFont("helvetica", "italic");
            doc.setTextColor(100, 100, 100);
            doc.setFontSize(11);
            const message = selectedCharts.length === 0
                ? "No charts were selected for this report."
                : "No data available for the selected charts and filters.";
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
        // console.error("Error generating report:", error);
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