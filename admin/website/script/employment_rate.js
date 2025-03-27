function fetchEmploymentRate() {


    fetch(`/Alumni-CvSU/admin/website/ajax/employment_rate.php`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {


            const ctx = document.getElementById("employmentRateChart").getContext("2d");

            window.employmentRateChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: ["Employed", "Unemployed"],
                    datasets: [{
                        data: [data.employed, data.unemployed],
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
                                    const total = data.employed + data.unemployed;
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

window.onload = function () { fetchEmploymentRate(); };
