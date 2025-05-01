<?php
require_once 'main_db.php';

$query = "SELECT * FROM jobs
        
          ORDER BY posted_date DESC";
$result = $mysqli->query($query);


?>




<style>
    .cr-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .cr-search-header {
        margin-bottom: 20px;
    }

    #Job {
        color: var(--cvsu-primary-green);
        font-size: 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--cvsu-light-green);
    }

    .cr-search-box {
        margin-bottom: 20px;
        width: 100%;
    }

    /* Style for Google Custom Search */
    .gsc-control-cse {
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }

    .gsc-search-box {
        margin-bottom: 20px !important;
    }

    /* Hide certain Google CSE elements */
    .gcse-searchresults-only {
        display: none;
    }

    .cr-filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .cr-filter {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 20px;
        background: white;
        cursor: pointer;
        font-size: 14px;
        color: #444;
        transition: all 0.3s ease;
    }

    .cr-filter:hover {
        border-color: #006400;
        color: #006400;
    }

    .cr-filter.active {
        background: #006400;
        color: white;
        border-color: #006400;
    }

    .cr-job-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .cr-job-card {
        display: flex;
        gap: 20px;
        padding: 20px;
        border: 1px solid #eee;
        border-radius: 8px;
        background: white;
        transition: all 0.3s ease;
        margin-bottom: 10px;
    }

    .cr-job-card:hover {
        border-color: #006400;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .cr-company-logo {
        width: 60px;
        height: 60px;
        background: #f0f0f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #006400;
    }

    .cr-job-details {
        flex: 1;
    }

    .cr-job-title {
        font-size: 16px;
        color: #006400;
        margin: 0 0 8px 0;
        text-decoration: none;
        font-weight: 600;
    }

    .cr-job-title:hover {
        text-decoration: underline;
    }

    .cr-company {
        font-size: 14px;
        color: #666;
        margin-bottom: 4px;
    }

    .cr-location {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
    }

    .cr-job-meta {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .cr-job-type {
        font-size: 12px;
        color: #006400;
        background: rgba(0, 100, 0, 0.1);
        padding: 4px 12px;
        border-radius: 12px;
    }

    .cr-posted-date {
        font-size: 12px;
        color: #666;
    }

    .cr-source {
        font-size: 12px;
        color: #999;
    }

    @media (max-width: 768px) {
        .cr-job-card {
            flex-direction: column;
        }

        .cr-company-logo {
            align-self: flex-start;
        }

        .cr-filter-bar {
            justify-content: center;
        }
    }
</style>

<body>
    <div class="cr-container">
        <div class="cr-search-header">
            <h2 id="Job">
                <i class="fas fa-job"></i>
                Job Opportunities
            </h2>
        </div>
        <?php
        if ($result && $result->num_rows > 0): ?>
            <?php while ($job = $result->fetch_assoc()): ?>
                <div class="cr-job-card">
                    <div class="cr-company-logo"><?= htmlspecialchars($job['company'][0]) ?></div>
                    <div class="cr-job-details">
                        <a href="#" class="cr-job-title"><?= htmlspecialchars($job['job_title']) ?></a>
                        <div class="cr-company"><?= htmlspecialchars($job['company']) ?></div>
                        <div class="cr-location"><?= htmlspecialchars($job['location']) ?></div>
                        <div class="cr-job-meta">
                            <span class="cr-job-type"><?= htmlspecialchars($job['job_type']) ?></span>
                            <span class="cr-posted-date"><?= date('M d, Y', strtotime($job['posted_date'])) ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No jobs found.</p>
        <?php endif; ?>
    </div>
</body>

</html>