<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Job Opportunities</title>
    <style>
        .cr-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Montserrat', sans-serif;
        }

        .cr-search-header {
            margin-bottom: 20px;
        }

        .cr-search-header h1 {
            color: #006400;
            font-size: 2em;
            margin: 0 0 10px 0;
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
        }

        .cr-job-card:hover {
            border-color: #006400;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            background: rgba(0,100,0,0.1);
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

            @media (max-width: 768px) {
            .cr-search-header h1 {
                font-size: 1.5em; /* Smaller font size on smaller screens */
                margin: 0 0 8px 0; /* Adjust bottom margin */
                }
            }

            @media (max-width: 480px) {
                .cr-search-header h1 {
                    font-size: 1.2em; /* Further reduce font size for very small screens */
                    margin: 0 0 5px 0; /* Adjust bottom margin */
                }
            }
        }
    </style>
</head>
<body>
    <div class="cr-container">
        <div class="cr-search-header">
            <h1>Job Opportunities</h1>
        </div>

        <!-- Google Custom Search Box -->
        <div class="cr-search-box">
            <div class="gcse-search" data-enableHistory="true" data-autoCompleteMaxCompletions="5"></div>
        </div>

        <div class="cr-filter-bar">
            <button class="cr-filter active">All Jobs</button>
            <button class="cr-filter">Past 3 days</button>
            <button class="cr-filter">Work from home</button>
            <button class="cr-filter">Full-time</button>
            <button class="cr-filter">Part-time</button>
            <button class="cr-filter">Contract</button>
        </div>

        <!-- Search Results Container -->
        <div id="search-results" class="cr-job-list">
            <!-- Default job listings shown when no search is performed -->
            <div class="cr-job-card">
                <div class="cr-company-logo">B</div>
                <div class="cr-job-details">
                    <a href="#" class="cr-job-title">Administrative Specialist (Philippines - Remote)</a>
                    <div class="cr-company">Baldan Group</div>
                    <div class="cr-location">Work from home</div>
                    <div class="cr-job-meta">
                        <span class="cr-job-type">Full-time</span>
                        <span class="cr-posted-date">8 days ago</span>
                        <span class="cr-source">via LinkedIn</span>
                    </div>
                </div>
            </div>

            <!-- More job cards... -->
        </div>
    </div>

    <!-- Google Custom Search Script -->
    <script async src="https://cse.google.com/cse.js?cx=845b26e2054b246fb"></script>
    
    <script>
        // Filter button functionality
        const filters = document.querySelectorAll('.cr-filter');
        filters.forEach(filter => {
            filter.addEventListener('click', () => {
                filters.forEach(f => f.classList.remove('active'));
                filter.classList.add('active');
            });
        });

        // Wait for Google Custom Search to load
        window.addEventListener('load', () => {
            // Create a mutation observer to watch for search results
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        const searchResults = document.querySelector('.gsc-results');
                        if (searchResults) {
                            // Format the search results to match your job card style
                            formatSearchResults();
                        }
                    }
                });
            });

            // Start observing the search results container
            const searchResultsContainer = document.querySelector('#search-results');
            observer.observe(searchResultsContainer, { childList: true, subtree: true });
        });

        function formatSearchResults() {
            const searchResults = document.querySelectorAll('.gsc-result');
            const jobList = document.querySelector('#search-results');
            
            // Clear existing job cards
            jobList.innerHTML = '';

            searchResults.forEach(result => {
                const title = result.querySelector('.gs-title').textContent;
                const snippet = result.querySelector('.gs-snippet').textContent;
                
                // Create job card from search result
                const jobCard = document.createElement('div');
                jobCard.className = 'cr-job-card';
                jobCard.innerHTML = `
                    <div class="cr-company-logo">${title[0]}</div>
                    <div class="cr-job-details">
                        <a href="#" class="cr-job-title">${title}</a>
                        <div class="cr-company">Found in search</div>
                        <div class="cr-location">${snippet}</div>
                        <div class="cr-job-meta">
                            <span class="cr-job-type">Search Result</span>
                            <span class="cr-source">via Search</span>
                        </div>
                    </div>
                `;
                
                jobList.appendChild(jobCard);
            });
        }
    </script>
</body>
</html>