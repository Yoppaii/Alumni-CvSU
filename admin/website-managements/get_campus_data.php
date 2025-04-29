<?php
require_once '../../main_db.php';

if (isset($_GET['id'])) {
    $campusId = $mysqli->real_escape_string($_GET['id']);
    $query = "SELECT id, name, url FROM campuses WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $campusId);
    $stmt->execute();
    $result = $stmt->get_result();
    $campus = $result->fetch_assoc();
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($campus);
} else {
    echo json_encode(['error' => 'Campus ID not provided.']);
}
