<?php
header('Content-Type: application/json');

session_start();

$response = ['sessionExpired' => false];

try {
    if (!isset($_SESSION['user_id'])) {
        $response['sessionExpired'] = true;
    }
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred while checking the session.']);
}
?>