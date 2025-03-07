<?php
session_start();
header('Content-Type: application/json');

require '../../main_db.php';

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Please log in to view history');
    }

    $userId = $_SESSION['user_id'];

    $sql = "SELECT id, change_date, action 
            FROM password_history 
            WHERE user_id = ? 
            ORDER BY change_date DESC 
            LIMIT 5";
            
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Database error: ' . $mysqli->error);
    }
    
    $stmt->bind_param('i', $userId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to fetch history: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $history = [];
    
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'date' => date('F j, Y g:i A', strtotime($row['change_date'])),
            'action' => $row['action']
        ];
    }
    
    $stmt->close();

    echo json_encode([
        'success' => true,
        'history' => $history
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}