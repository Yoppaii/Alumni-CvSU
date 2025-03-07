<?php
include '../../main_db.php';
header('Content-Type: application/json');

// Get monthly booking trends and revenue
$bookingTrendsQuery = "
    SELECT 
        DATE_FORMAT(created_at, '%b %Y') as month,
        COUNT(*) as total_bookings,
        SUM(price) as total_revenue,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_bookings
    FROM bookings 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%b %Y'), MONTH(created_at)
    ORDER BY created_at ASC
";

// Get room occupancy distribution
$roomOccupancyQuery = "
    SELECT 
        room_number,
        occupancy,
        COUNT(*) as booking_count,
        AVG(price) as avg_price
    FROM bookings 
    GROUP BY room_number, occupancy
    ORDER BY room_number
";

// Get booking status distribution
$statusQuery = "
    SELECT 
        status,
        COUNT(*) as count,
        SUM(price) as total_revenue
    FROM bookings 
    GROUP BY status
";

// Get average stay duration and revenue per stay
$stayAnalysisQuery = "
    SELECT 
        DATE_FORMAT(created_at, '%b %Y') as month,
        AVG(DATEDIFF(departure_date, arrival_date)) as avg_stay_duration,
        AVG(price) as avg_price_per_stay
    FROM bookings 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%b %Y')
    ORDER BY created_at ASC
";

try {
    $trends = $mysqli->query($bookingTrendsQuery)->fetch_all(MYSQLI_ASSOC);
    $roomOccupancy = $mysqli->query($roomOccupancyQuery)->fetch_all(MYSQLI_ASSOC);
    $statuses = $mysqli->query($statusQuery)->fetch_all(MYSQLI_ASSOC);
    $stayAnalysis = $mysqli->query($stayAnalysisQuery)->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'trends' => $trends,
            'roomOccupancy' => $roomOccupancy,
            'statuses' => $statuses,
            'stayAnalysis' => $stayAnalysis
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}