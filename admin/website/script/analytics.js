document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById('employmentChart');
    const summaryElement = document.querySelector('.AT-summary p'); // Get the summary container

    if (!chartElement) {
        console.error("Error: Chart element not found!");
        return;
    }

    fetch('/Alumni-CvSU/admin/website/ajax/analytics.php')
        .then(response => response.json())
        .then(data => {
            console.log("Received Data:", data);

            // Compute the total for each employment status
            const summary = {};
            data.datasets.forEach(dataset => {
                summary[dataset.label] = dataset.data.reduce((acc, count) => acc + count, 0);
            });

            // Format the summary into a readable string
            let summaryHTML = Object.entries(summary)
                .map(([status, count]) =>
                    `<div class="summary-item">${status.replace(/\b\w/g, c => c.toUpperCase())} : ${count}</div>`
                )
                .join(""); // Remove the separator since CSS will handle spacing

            summaryElement.innerHTML = summaryHTML;


            new Chart(document.getElementById('employmentChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: data.datasets,
                },
                options: barOptions
            });


        })

        .catch(error => console.error('Error fetching data:', error));
});
