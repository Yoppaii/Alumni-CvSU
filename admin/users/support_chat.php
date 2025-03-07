<?php
// support_chat.php
require_once 'main_db.php';

class SupportChat {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getActiveChats($admin_id = null) {
        $query = "SELECT 
                    sc.id as chat_id,
                    u.name as user_name,
                    SUBSTRING(UPPER(u.name), 1, 2) as initials,
                    (
                        SELECT message 
                        FROM support_messages 
                        WHERE chat_id = sc.id 
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ) as last_message,
                    (
                        SELECT COUNT(*) 
                        FROM support_messages 
                        WHERE chat_id = sc.id 
                        AND is_read = 0 
                        AND sender_id != ?
                    ) as unread_count
                 FROM support_chats sc
                 JOIN users u ON sc.user_id = u.id
                 WHERE sc.status = 'active'
                 ORDER BY sc.updated_at DESC";
                 
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $chats = [];
        while ($row = $result->fetch_assoc()) {
            $chats[] = [
                'chat_id' => $row['chat_id'],
                'user_name' => $row['user_name'],
                'initials' => $row['initials'],
                'last_message' => $row['last_message'],
                'unread_count' => $row['unread_count']
            ];
        }
        
        return $chats;
    }
    
    public function getMessages($chat_id) {
        $chat_id = (int)$chat_id;
        $query = "SELECT 
                    sm.*,
                    u.name as sender_name,
                    SUBSTRING(UPPER(u.name), 1, 2) as sender_initials,
                    sc.admin_id
                 FROM support_messages sm
                 JOIN support_chats sc ON sm.chat_id = sc.id
                 JOIN users u ON sm.sender_id = u.id
                 WHERE sm.chat_id = ?
                 ORDER BY sm.created_at ASC";
                 
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $chat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'id' => $row['id'],
                'message' => $row['message'],
                'sender_name' => $row['sender_name'],
                'sender_initials' => $row['sender_initials'],
                'created_at' => $row['created_at'],
                'is_admin' => ($row['sender_id'] == $row['admin_id'])
            ];
        }
        
        return $messages;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'get_messages') {
    $chat_id = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;
    if ($chat_id > 0) {
        $supportChat = new SupportChat($mysqli);
        $messages = $supportChat->getMessages($chat_id);
        $output = '';
        foreach ($messages as $message) {
            $messageClass = $message['is_admin'] ? 'message outgoing' : 'message';
            $time = date('g:i A', strtotime($message['created_at']));
            
            $output .= sprintf(
                '<div class="%s">
                    <div class="message-bubble">%s</div>
                    <div class="message-time">%s</div>
                </div>',
                $messageClass,
                htmlspecialchars($message['message']),
                $time
            );
        }
        
        echo $output;
        exit;
    }
}
?>