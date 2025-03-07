<?php
//delete-chat.php
require_once '../../main_db.php';

header('Content-Type: application/json');

if (!isset($_POST['chat_id']) || !is_numeric($_POST['chat_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid chat ID']);
    exit;
}

$chat_id = (int)$_POST['chat_id'];

// Start transaction
$mysqli->begin_transaction();

try {
    // Delete messages first (due to foreign key constraints)
    $delete_messages = $mysqli->prepare("DELETE FROM support_messages WHERE chat_id = ?");
    $delete_messages->bind_param("i", $chat_id);
    $delete_messages->execute();

    // Then delete the chat
    $delete_chat = $mysqli->prepare("DELETE FROM support_chats WHERE id = ?");
    $delete_chat->bind_param("i", $chat_id);
    $delete_chat->execute();

    // Commit transaction
    $mysqli->commit();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback on error
    $mysqli->rollback();
    error_log("Error deleting chat: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to delete chat']);
}