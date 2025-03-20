<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Analytics Dashboard</title>
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #eef2ff;
      --secondary: #2b2d42;
      --light-gray: #f8f9fa;
      --medium-gray: #e9ecef;
      --dark-gray: #6c757d;
      --success: #38b000;
      --danger: #d90429;
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      --radius: 12px;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    body {
      background-color: #f5f7fb;
      color: var(--secondary);
      padding: 24px;
    }
    
    .dashboard-container {
      max-width: 1400px;
      margin: 0 auto;
    }
    
    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }
    
    .dashboard-title {
      font-size: 24px;
      font-weight: 700;
    }
    
    .dashboard-actions {
      display: flex;
      gap: 12px;
    }
    
    .filter-bar {
      background-color: white;
      border-radius: var(--radius);
      padding: 16px 24px;
      box-shadow: var(--shadow);
      margin-bottom: 24px;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      align-items: center;
    }
    
    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
      min-width: 160px;
    }
    
    .filter-label {
      font-size: 14px;
      font-weight: 500;
      color: var(--dark-gray);
    }
    
    .filter-select {
      padding: 10px 14px;
      border: 1px solid var(--medium-gray);
      border-radius: 8px;
      background-color: white;
      font-size: 14px;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      cursor: pointer;
      transition: border-color 0.2s ease;
    }
    
    .filter-select:hover {
      border-color: var(--primary);
    }
    
    .filter-actions {
      display: flex;
      gap: 8px;
      margin-left: auto;
    }
    
    .button {
      padding: 10px 16px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .button-primary {
      background-color: var(--primary);
      color: white;
    }
    
    .button-primary:hover {
      background-color: #3051d3;
    }
    
    .button-secondary {
      background-color: white;
      color: var(--secondary);
      border: 1px solid var(--medium-gray);
    }
    
    .button-secondary:hover {
      background-color: var(--light-gray);
    }
    
    .charts-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
      gap: 24px;
      margin-bottom: 24px;
    }
    
    .chart-card {
      background-color: white;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }
    
    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 24px;
      border-bottom: 1px solid var(--medium-gray);
    }
    
    .chart-title {
      font-size: 16px;
      font-weight: 600;
    }
    
    .chart-actions {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    
    .toggle-report {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }
    
    .toggle-input {
      appearance: none;
      width: 36px;
      height: 20px;
      background-color: var(--medium-gray);
      border-radius: 20px;
      position: relative;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }
    
    .toggle-input:checked {
      background-color: var(--primary);
    }
    
    .toggle-input:before {
      content: '';
      position: absolute;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background-color: white;
      top: 2px;
      left: 2px;
      transition: transform 0.2s ease;
    }
    
    .toggle-input:checked:before {
      transform: translateX(16px);
    }
    
    .toggle-label {
      font-size: 13px;
      color: var(--dark-gray);
    }
    
    .chart-content {
      padding: 24px;
      height: 300px;
      position: relative;
    }
    
    .chart-placeholder {
      width: 100%;
      height: 100%;
      background-color: var(--light-gray);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--dark-gray);
    }
    
    @media (max-width: 768px) {
      .charts-container {
        grid-template-columns: 1fr;
      }
      
      .filter-bar {
        flex-direction: column;
        align-items: stretch;
      }
      
      .filter-actions {
        margin-left: 0;
        justify-content: space-between;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <h1 class="dashboard-title">Booking Analytics</h1>
      <div class="dashboard-actions">
        <button class="button button-secondary" id="exportData">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          Export Data
        </button>
        <button class="button button-primary" id="printReport">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
          Print Report
        </button>
      </div>
    </div>
    
    <div class="filter-bar">
      <div class="filter-group">
        <label class="filter-label" for="yearFilter">Year</label>
        <select id="yearFilter" class="filter-select">
          <option value="2025">2025</option>
          <option value="2024">2024</option>
          <option value="2023">2023</option>
        </select>
      </div>
      <div class="filter-group">
        <label class="filter-label" for="monthFilter">Month</label>
        <select id="monthFilter" class="filter-select">
          <option value="all">All Months</option>
          <option value="01">January</option>
          <option value="02">February</option>
          <option value="03">March</option>
          <option value="04">April</option>
          <option value="05">May</option>
          <option value="06">June</option>
          <option value="07">July</option>
          <option value="08">August</option>
          <option value="09">September</option>
          <option value="10">October</option>
          <option value="11">November</option>
          <option value="12">December</option>
        </select>
      </div>
      <div class="filter-group">
        <label class="filter-label" for="userTypeFilter">User Type</label>
        <select id="userTypeFilter" class="filter-select">
          <option value="all">All Users</option>
          <option value="regular">Regular</option>
          <option value="premium">Premium</option>
          <option value="business">Business</option>
        </select>
      </div>
      <div class="filter-group">
        <label class="filter-label" for="roomFilter">Room Number</label>
        <select id="roomFilter" class="filter-select">
          <option value="all">All Rooms</option>
          <option value="101">Room 101</option>
          <option value="102">Room 102</option>
          <option value="103">Room 103</option>
          <option value="104">Room 104</option>
          <option value="105">Room 105</option>
        </select>
      </div>
      <div class="filter-actions">
        <button class="button button-secondary" id="resetFilters">Reset Filters</button>
        <button class="button button-secondary" id="selectAllCharts">Select All</button>
        <button class="button button-secondary" id="deselectAllCharts">Deselect All</button>
      </div>
    </div>
    
    <div class="charts-container">
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Total Bookings by Day of the Week</h3>
          <div class="chart-actions">
            <label class="toggle-report">
              <input type="checkbox" class="toggle-input report-checkbox" value="dailyBookings" checked>
              <span class="toggle-label">Include in Report</span>
            </label>
          </div>
        </div>
        <div class="chart-content">
          <canvas id="bookingByDayChart"></canvas>
        </div>
      </div>
      
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Total Bookings by Month</h3>
          <div class="chart-actions">
            <label class="toggle-report">
              <input type="checkbox" class="toggle-input report-checkbox" value="monthlyBookings" checked>
              <span class="toggle-label">Include in Report</span>
            </label>
          </div>
        </div>
        <div class="chart-content">
          <canvas id="bookingByMonthChart"></canvas>
        </div>
      </div>
      
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Cancellation & No-Show Rate</h3>
          <div class="chart-actions">
            <label class="toggle-report">
              <input type="checkbox" class="toggle-input report-checkbox" value="cancellationData" checked>
              <span class="toggle-label">Include in Report</span>
            </label>
          </div>
        </div>
        <div class="chart-content">
          <canvas id="cancellationChart"></canvas>
        </div>
      </div>
      
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Booking Lead Time</h3>
          <div class="chart-actions">
            <label class="toggle-report">
              <input type="checkbox" class="toggle-input report-checkbox" value="leadTimeData" checked>
              <span class="toggle-label">Include in Report</span>
            </label>
          </div>
        </div>
        <div class="chart-content">
          <canvas id="bookingLeadTimeChart"></canvas>
        </div>
      </div>
      
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Booking Peak Hours</h3>
          <div class="chart-actions">
            <label class="toggle-report">
              <input type="checkbox" class="toggle-input report-checkbox" value="peakHoursData" checked>
              <span class="toggle-label">Include in Report</span>
            </label>
          </div>
        </div>
        <div class="chart-content">
          <canvas id="peakBookingChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script>
    // This would be where you initialize your charts with Chart.js
    // For this example, we're just showing the HTML/CSS structure
    document.addEventListener('DOMContentLoaded', function() {
      // Placeholder for chart initialization
      console.log('Dashboard loaded');
    });
  </script>
</body>
</html>