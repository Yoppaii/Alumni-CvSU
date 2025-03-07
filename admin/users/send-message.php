<?php
//send-message.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

session_start();
require_once '../../main_db.php';

function sendJsonResponse($success, $message = '', $data = null) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

if ($mysqli->connect_error) {
    sendJsonResponse(false, 'Database connection failed: ' . $mysqli->connect_error);
}

if (!isset($_SESSION['admin_id'])) {
    sendJsonResponse(false, 'Admin session not found. Please log in again.');
}

$chat_id = isset($_POST['chat_id']) ? (int)$_POST['chat_id'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$admin_id = (int)$_SESSION['admin_id'];

if (!$chat_id) {
    sendJsonResponse(false, 'Invalid chat ID: ' . $chat_id);
}
if (empty($message)) {
    sendJsonResponse(false, 'Message cannot be empty');
}
if (!$admin_id) {
    sendJsonResponse(false, 'Invalid admin ID: ' . $admin_id);
}

try {
    $mysqli->begin_transaction();
    
    // Verify chat exists and get details
    $verify_chat = $mysqli->prepare("SELECT id, user_id, admin_id, status FROM support_chats WHERE id = ?");
    if (!$verify_chat) {
        throw new Exception('Failed to prepare chat verification: ' . $mysqli->error);
    }
    
    $verify_chat->bind_param('i', $chat_id);
    $verify_chat->execute();
    $verify_result = $verify_chat->get_result();
    
    if ($verify_result->num_rows === 0) {
        throw new Exception('Chat ID does not exist: ' . $chat_id);
    }
    
    $chat_data = $verify_result->fetch_assoc();
    $verify_chat->close();

    // Insert new message
    $insert_query = "INSERT INTO support_messages (chat_id, sender_id, message, is_read, created_at) 
                    VALUES (?, ?, ?, 0, CURRENT_TIMESTAMP)";
    $stmt = $mysqli->prepare($insert_query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare insert statement: ' . $mysqli->error);
    }
    
    $stmt->bind_param('iis', $chat_id, $admin_id, $message);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute insert: ' . $stmt->error);
    }

    // Check admin message count
    $count_query = "SELECT COUNT(*) as msg_count FROM support_messages 
                   WHERE chat_id = ? AND sender_id = ?";
    $count_stmt = $mysqli->prepare($count_query);
    
    if (!$count_stmt) {
        throw new Exception('Failed to prepare count query: ' . $mysqli->error);
    }
    
    $count_stmt->bind_param('ii', $chat_id, $admin_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $msg_count = $count_result->fetch_assoc()['msg_count'];

    if ($msg_count >= 20) {
        $delete_query = "DELETE FROM support_messages 
                        WHERE chat_id = ? 
                        AND sender_id = ? 
                        AND id NOT IN (
                            SELECT id FROM (
                                SELECT id 
                                FROM support_messages 
                                WHERE chat_id = ? 
                                AND sender_id = ? 
                                ORDER BY created_at DESC 
                                LIMIT 1
                            ) as recent_msgs
                        )";
        
        $delete_stmt = $mysqli->prepare($delete_query);
        if (!$delete_stmt) {
            throw new Exception('Failed to prepare delete query: ' . $mysqli->error);
        }
        
        $delete_stmt->bind_param('iiii', $chat_id, $admin_id, $chat_id, $admin_id);
        if (!$delete_stmt->execute()) {
            throw new Exception('Failed to delete old messages: ' . $delete_stmt->error);
        }
    }

    $update_query = "UPDATE support_chats 
                    SET updated_at = CURRENT_TIMESTAMP,
                        admin_id = COALESCE(admin_id, ?),
                        status = CASE 
                            WHEN status = 'pending' THEN 'active'
                            ELSE status 
                        END
                    WHERE id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    
    if (!$update_stmt) {
        throw new Exception('Failed to prepare update: ' . $mysqli->error);
    }
    
    $update_stmt->bind_param('ii', $admin_id, $chat_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update chat: ' . $update_stmt->error);
    }

            $mysqli->commit();
    
    $messages_query = "SELECT sm.*, DATE_FORMAT(sm.created_at, '%h:%i %p') as formatted_time
                      FROM support_messages sm 
                      WHERE sm.chat_id = ? 
                      ORDER BY sm.created_at ASC";
    $messages_stmt = $mysqli->prepare($messages_query);
    $messages_stmt->bind_param('i', $chat_id);
    $messages_stmt->execute();
    $messages_result = $messages_stmt->get_result();
    
    $messages = [];
    while ($row = $messages_result->fetch_assoc()) {
        $messages[] = [
            'id' => $row['id'],
            'message' => $row['message'],
            'sender_id' => $row['sender_id'],
            'formatted_time' => $row['formatted_time']
        ];
    }
    
    sendJsonResponse(true, 'Message sent successfully', [
        'chat_id' => $chat_id,
        'message_id' => $mysqli->insert_id,
        'timestamp' => date('Y-m-d H:i:s'),
        'messages' => $messages,
        'messages_updated' => ($msg_count >= 10)
    ]);
    
} catch (Exception $e) {
    $mysqli->rollback();
    error_log("Chat message error: " . $e->getMessage());
    sendJsonResponse(false, 'Error: ' . $e->getMessage());
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($count_stmt)) $count_stmt->close();
    if (isset($delete_stmt)) $delete_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($verify_chat)) $verify_chat->close();
}