<?php
include '../../main_db.php';
header('Content-Type: application/json');

if (isset($_POST['user_analytics'])) {

    $frm_data = $_POST;
    $condition = "";

    switch ($frm_data['period']) {
        case 1: // Today
            $condition = "WHERE DATE(created_at) = CURDATE()";
            break;
        case 2: // Last 7 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 7 DAY AND NOW()";
            break;
        case 3: // Last 30 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
            break;
        case 4: // Last 90 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
            break;
        case 5: // Last 1 year
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
            break;
        case 6: // All Time
            $condition = ""; // No condition, fetch all records
            break;
        default:
            echo json_encode(["error" => "Invalid period"]);
            exit;
    }

    // Period 5 (All time) should have no condition
    $query = "SELECT COUNT(id) AS count FROM users $condition";

    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();

    // Ensure a valid response
    echo json_encode(["count" => $row['count'] ?? 0]);
}

if (isset($_POST['booking_analytics'])) {

    $frm_data = $_POST;
    $condition = "";

    switch ($frm_data['period']) {
        case 1: // Today
            $condition = "WHERE DATE(created_at) = CURDATE()";
            break;
        case 2: // Last 7 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 7 DAY AND NOW()";
            break;
        case 3: // Last 30 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
            break;
        case 4: // Last 90 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
            break;
        case 5: // Last 1 year
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
            break;
        case 6: // All Time
            $condition = ""; // No condition, fetch all records
            break;
        default:
            echo json_encode(["error" => "Invalid period"]);
            exit;
    }



    // Period 5 (All time) should have no condition
    $query = "SELECT COUNT(id) as count FROM bookings $condition";

    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();

    // Ensure a valid response
    echo json_encode(["count" => $row['count'] ?? 0]);
}

if (isset($_POST['alumni_id_cards_analytics'])) {

    $frm_data = $_POST;
    $condition = "";

    switch ($frm_data['period']) {
        case 1: // Today
            $condition = "WHERE DATE(created_at) = CURDATE()";
            break;
        case 2: // Last 7 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 7 DAY AND NOW()";
            break;
        case 3: // Last 30 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
            break;
        case 4: // Last 90 days
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
            break;
        case 5: // Last 1 year
            $condition = "WHERE created_at BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
            break;
        case 6: // All Time
            $condition = ""; // No condition, fetch all records
            break;
        default:
            echo json_encode(["error" => "Invalid period"]);
            exit;
    }





    // Period 5 (All time) should have no condition
    $query = "SELECT COUNT(id) as count FROM alumni_id_cards $condition";

    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();

    // Ensure a valid response
    echo json_encode(["count" => $row['count'] ?? 0]);
}
