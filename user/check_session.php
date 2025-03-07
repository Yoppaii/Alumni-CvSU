<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

try {
    session_start();
    require_once '../main_db.php';
    if (isset($_SESSION['username']) && isset($_SESSION['session_token']) && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $session_token = $_SESSION['session_token'];

        $stmt = $mysqli->prepare("SELECT session_token FROM user WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($dbSessionToken);
                $stmt->fetch();
                if ($session_token !== $dbSessionToken) {
                    echo json_encode([
                        'logout' => true,
                        'message' => 'Session expired due to login from another device'
                    ]);
                    session_destroy();
                    exit;
                }
            }
            $stmt->close();
        }
        echo json_encode([
            'logout' => false,
            'message' => 'Session valid'
        ]);
    } else {
        echo json_encode([
            'logout' => false,
            'message' => 'No session to check'
        ]);
    }

} catch (Exception $e) {
    error_log('Session check error: ' . $e->getMessage());
    
    echo json_encode([
        'logout' => false,
        'message' => 'Error checking session'
    ]);
}
?>