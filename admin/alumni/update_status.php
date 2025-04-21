<?php
include '../../main_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'id' key exists in $_POST array
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing or empty application ID']);
        exit;
    }

    $id = intval($_POST['id']);
    $newStatus = $_POST['status'];
    $allowed = ['pending', 'paid', 'confirmed', 'declined'];

    if (!in_array($newStatus, $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid status']);
        exit;
    }

    $stmt = $mysqli->prepare("UPDATE alumni_id_cards SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $newStatus, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'newStatus' => $newStatus]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }

    $stmt->close();
    exit;
} else {
    // Handle cases where the request method is not POST
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}
