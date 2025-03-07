<?php
session_start();
require_once 'main_db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if news ID is provided
if (!isset($_POST['news_id'])) {
    echo json_encode(['success' => false, 'message' => 'News ID not provided']);
    exit;
}

$user_id = $_SESSION['user_id'];
$news_id = (int)$_POST['news_id'];

try {
    // Begin transaction
    $mysqli->begin_transaction();

    // Check if the user has already liked this news
    $check_stmt = $mysqli->prepare("SELECT id FROM news_likes WHERE news_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $news_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $exists = $result->num_rows > 0;
    $check_stmt->close();

    if ($exists) {
        // Unlike - Delete the existing like
        $stmt = $mysqli->prepare("DELETE FROM news_likes WHERE news_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $news_id, $user_id);
        $stmt->execute();
        $liked = false;
    } else {
        // Like - Insert new like
        $stmt = $mysqli->prepare("INSERT INTO news_likes (news_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $news_id, $user_id);
        $stmt->execute();
        $liked = true;

        // Optional: Add notification for the news author
        // This is where you could add code to notify the author of the news
        // about the new like if you want to implement that feature
    }

    // Get updated like count
    $count_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM news_likes WHERE news_id = ?");
    $count_stmt->bind_param("i", $news_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $like_count = $count_result->fetch_assoc()['count'];
    $count_stmt->close();

    // Store like activity in user_activity table if you want to track user actions
    $activity_stmt = $mysqli->prepare("INSERT INTO user_activity (user_id, activity_type, content_id, created_at) 
                                     VALUES (?, ?, ?, NOW())");
    $activity_type = $liked ? 'like' : 'unlike';
    $activity_stmt->bind_param("isi", $user_id, $activity_type, $news_id);
    $activity_stmt->execute();

    // Commit transaction
    $mysqli->commit();

    // Send success response
    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'likeCount' => $like_count,
        'message' => $liked ? 'Article liked successfully' : 'Article unliked successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $mysqli->rollback();
    
    // Log the error (you should implement proper error logging)
    error_log("Error in handle_like.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request',
        'debug' => $e->getMessage() // Remove this in production
    ]);
} finally {
    // Close any remaining open statements
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($activity_stmt)) {
        $activity_stmt->close();
    }
}

// Close database connection
$mysqli->close();
?>