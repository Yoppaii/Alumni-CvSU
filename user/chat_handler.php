<?php
session_start();
require_once '../main_db.php';

class ChatHandler {
    private $mysqli;
    private $user_id;
    
    public function __construct($mysqli, $user_id) {
        $this->mysqli = $mysqli;
        $this->user_id = $user_id;
    }
    
    public function handleRequest() {
        if (!isset($this->user_id)) {
            return $this->jsonResponse(['error' => 'Not authenticated'], 401);
        }

        $action = $_POST['action'] ?? '';
        
        try {
            switch ($action) {
                case 'init_chat':
                    return $this->initializeChat();
                case 'send':
                    return $this->sendMessage();
                case 'get_messages':
                    return $this->getNewMessages();
                case 'get_previous_messages':
                    return $this->getPreviousMessages();
                default:
                    return $this->jsonResponse(['error' => 'Invalid action'], 400);
            }
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    private function initializeChat() {
        $stmt = $this->mysqli->prepare("
            SELECT id 
            FROM support_chats 
            WHERE user_id = ? AND status = 'active' 
            LIMIT 1
        ");
        
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $chat = $result->fetch_assoc();
            return $this->jsonResponse(['chat_id' => $chat['id']]);
        }

        $stmt = $this->mysqli->prepare("
            INSERT INTO support_chats (user_id, status, created_at) 
            VALUES (?, 'active', NOW())
        ");
        
        $stmt->bind_param("i", $this->user_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create chat session');
        }
        
        return $this->jsonResponse(['chat_id' => $this->mysqli->insert_id]);
    }
    
    private function sendMessage() {
        $message = trim($_POST['message'] ?? '');
        $chat_id = (int)$_POST['chat_id'];
        
        if (empty($message)) {
            return $this->jsonResponse(['error' => 'Message cannot be empty'], 400);
        }
        
        if (!$this->verifyChatOwnership($chat_id)) {
            return $this->jsonResponse(['error' => 'Invalid chat session'], 403);
        }
        
        // Insert message
        $stmt = $this->mysqli->prepare("
            INSERT INTO support_messages (
                chat_id, 
                sender_id, 
                message, 
                created_at
            ) VALUES (?, ?, ?, NOW())
        ");
        
        $stmt->bind_param("iis", $chat_id, $this->user_id, $message);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to send message');
        }
        
        return $this->jsonResponse([
            'success' => true,
            'message_id' => $this->mysqli->insert_id
        ]);
    }
    
    private function getNewMessages() {
        $chat_id = (int)$_POST['chat_id'];
        $last_id = (int)($_POST['last_id'] ?? 0);
        
        if (!$this->verifyChatOwnership($chat_id)) {
            return $this->jsonResponse(['error' => 'Invalid chat session'], 403);
        }
        
        $stmt = $this->mysqli->prepare("
            SELECT 
                m.id,
                m.message,
                m.sender_id,
                m.created_at,
                u.username as sender_name
            FROM support_messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.chat_id = ? AND m.id > ?
            ORDER BY m.created_at ASC
        ");
        
        $stmt->bind_param("ii", $chat_id, $last_id);
        $stmt->execute();
        
        return $this->jsonResponse([
            'messages' => $this->fetchMessages($stmt->get_result())
        ]);
    }
    
    private function getPreviousMessages() {
        $chat_id = (int)$_POST['chat_id'];
        
        if (!$this->verifyChatOwnership($chat_id)) {
            return $this->jsonResponse(['error' => 'Invalid chat session'], 403);
        }
        
        $stmt = $this->mysqli->prepare("
            SELECT 
                m.id,
                m.message,
                m.sender_id,
                m.created_at,
                u.username as sender_name
            FROM support_messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.chat_id = ?
            ORDER BY m.created_at DESC
            LIMIT 50
        ");
        
        $stmt->bind_param("i", $chat_id);
        $stmt->execute();
        
        $messages = $this->fetchMessages($stmt->get_result());
        return $this->jsonResponse(['messages' => array_reverse($messages)]);
    }
    
    private function verifyChatOwnership($chat_id) {
        $stmt = $this->mysqli->prepare("
            SELECT id 
            FROM support_chats 
            WHERE id = ? AND user_id = ? AND status = 'active'
        ");
        
        $stmt->bind_param("ii", $chat_id, $this->user_id);
        $stmt->execute();
        
        return $stmt->get_result()->num_rows > 0;
    }
    
    private function fetchMessages($result) {
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'id' => $row['id'],
                'message' => $row['message'],
                'sender_id' => $row['sender_id'],
                'sender_name' => $row['sender_name'],
                'created_at' => $row['created_at'],
                'is_user' => $row['sender_id'] == $this->user_id
            ];
        }
        return $messages;
    }
    
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        return true;
    }
}


try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not authenticated');
    }

    $handler = new ChatHandler($mysqli, $_SESSION['user_id']);
    $handler->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}