<?php
session_start();
require_once '../main_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    $reason = $data['reason'] ?? '';

    if (empty($reason)) {
        throw new Exception('Cancellation reason is required');
    }
    $mysqli->begin_transaction();
    $select_stmt = $mysqli->prepare("SELECT * FROM alumni_id_cards WHERE user_id = ?");
    $select_stmt->bind_param("i", $user_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    $application = $result->fetch_assoc();
    $select_stmt->close();

    if (!$application) {
        throw new Exception('Application not found');
    }

    $create_table_sql = "CREATE TABLE IF NOT EXISTS cancelled_alumni_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        original_id INT,
        user_id INT NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        first_name VARCHAR(255) NOT NULL,
        middle_name VARCHAR(255),
        email VARCHAR(255) NOT NULL,
        course VARCHAR(255) NOT NULL,
        year_graduated INT NOT NULL,
        highschool_graduated VARCHAR(255) NOT NULL,
        membership_type VARCHAR(50) NOT NULL,
        cancellation_reason TEXT NOT NULL,
        cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        original_created_at TIMESTAMP
    )";
    
    $mysqli->query($create_table_sql);

    $insert_stmt = $mysqli->prepare("INSERT INTO cancelled_alumni_applications 
        (original_id, user_id, last_name, first_name, middle_name, email, course, 
        year_graduated, highschool_graduated, membership_type, cancellation_reason, original_created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $insert_stmt->bind_param("iisssssissss",
        $application['id'],
        $application['user_id'],
        $application['last_name'],
        $application['first_name'],
        $application['middle_name'],
        $application['email'],
        $application['course'],
        $application['year_graduated'],
        $application['highschool_graduated'],
        $application['membership_type'],
        $reason,
        $application['created_at']
    );

    if (!$insert_stmt->execute()) {
        throw new Exception('Error saving cancelled application');
    }

    $delete_stmt = $mysqli->prepare("DELETE FROM alumni_id_cards WHERE user_id = ?");
    $delete_stmt->bind_param("i", $user_id);
    
    if (!$delete_stmt->execute()) {
        throw new Exception('Error deleting original application');
    }

    $mysqli->commit();

    echo json_encode(['success' => true, 'message' => 'Application cancelled successfully']);

} catch (Exception $e) {
    if ($mysqli->connect_errno != 0) {
        $mysqli->rollback();
    }
    
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>