function fetchCourseRelevance() {
    fetch(`/Alumni-CvSU/admin/website/ajax/course_to_job_relevance.php`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (window.courseRelevanceChart instanceof Chart) {
                window.courseRelevanceChart.destroy();
            }

            const ctx = document.getElementById("courseRelevanceChart").getContext("2d");

            window.courseRelevanceChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: ["Related", "Not Related"],
                    datasets: [{
                        data: [data.related, data.not_related],
                        backgroundColor: ["#4CAF50", "#FF5733"],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: "70%",
                    plugins: {
                        legend: { position: false },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function (tooltipItem) {
                                    const value = tooltipItem.raw;
                                    const total = data.related + data.not_related;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${value} (${percentage}%)`;
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

window.onload = function () { fetchCourseRelevance(); };
