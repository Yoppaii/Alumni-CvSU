<div class="organizational-container">
    <section class="organizational-chart">
        <h2>Organizational Chart</h2>
        <p>Our organizational chart provides a visual representation of the key departments and leadership hierarchy within our company. It helps clarify reporting lines and responsibilities, ensuring effective communication and collaboration across teams.</p>
        <div class="chart">
            <img src="CvSU/main/about/organization_chart/Organization.jpg" alt="Organizational Chart" class="org-chart-img">
        </div>
    </section>
</div>

<div class="objectives-container">
    <section class="objectives">
        <h2>Our Objectives</h2>
        <p>Our objectives are the core pillars that guide our operations and strategic decisions. They define our commitment to growth, innovation, and excellence. Each objective aligns with our mission to provide the best solutions to our customers and stakeholders.</p>
        <div class="objectives-image">
            <img src="CvSU/main/about/organization_chart/Association.jpg" alt="Objectives Image" class="objectives-img">
        </div>
    </section>
</div>
<style>
.organizational-container, .objectives-container {
    width: 90%;
    margin: 0 auto;
    padding: 20px;
}

.organizational-container section, .objectives-container section {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 40px;
}

.organizational-container section h2, .objectives-container section h2 {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #388e3c; 
}

.organizational-container .organizational-chart {
    display: flex;
    justify-content: space-between;
}

.organizational-container .organizational-chart p {
    max-width: 50%;
    margin-left: 20px;
    font-size: 16px;
    line-height: 1.6;
}

.organizational-container .org-chart-img {
    max-width: 45%;
    height: auto;
    border: 2px solid #388e3c;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.organizational-container .org-chart-img:hover {
    transform: scale(1.05);
}

.objectives-container .objectives {
    display: flex;
    justify-content: space-between;
}

.objectives-container .objectives p {
    max-width: 50%;
    margin-right: 20px;
    font-size: 16px;
    line-height: 1.6;
}

.objectives-container .objectives-img {
    max-width: 45%;
    height: auto;
    border: 2px solid #388e3c;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.objectives-container .objectives-img:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .organizational-container section,
    .objectives-container section {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .organizational-container .organizational-chart p,
    .objectives-container .objectives p {
        max-width: 90%;
        margin-right: 0;
    }

    .organizational-container .org-chart-img,
    .objectives-container .objectives-img {
        max-width: 80%;
        margin-top: 20px;
    }
}

</style>