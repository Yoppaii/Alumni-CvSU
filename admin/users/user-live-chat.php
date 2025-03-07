<?php
require_once 'main_db.php';
$current_user_id = $_SESSION['user_id'] ?? null;
$chats_query = "SELECT sc.*, u.first_name, u.last_name 
                FROM support_chats sc 
                LEFT JOIN user u ON sc.user_id = u.user_id 
                WHERE sc.status = 'active' 
                ORDER BY sc.updated_at DESC";
$chats_result = $mysqli->query($chats_query);

if (!$chats_result) {
    error_log("Error in chats query: " . $mysqli->error);
    $chats_result = []; 
}

function getMessages($chat_id) {
    global $mysqli;
    $messages_query = "SELECT sm.*, DATE_FORMAT(sm.created_at, '%h:%i %p') as formatted_time  
                      FROM support_messages sm 
                      WHERE sm.chat_id = ? 
                      ORDER BY sm.created_at ASC";
    
    $stmt = $mysqli->prepare($messages_query);
    if (!$stmt) {
        error_log("Error preparing messages query: " . $mysqli->error);
        return false;
    }
    
    $stmt->bind_param("i", $chat_id);
    if (!$stmt->execute()) {
        error_log("Error executing messages query: " . $stmt->error);
        return false;
    }
    
    return $stmt->get_result();
}

function safeQuery($mysqli, $query, $default = []) {
    $result = $mysqli->query($query);
    if (!$result) {
        error_log("Query error: " . $mysqli->error . " in query: " . $query);
        return $default;
    }
    return $result;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .chat-container {
            display: flex;
            flex-direction: row-reverse; 
            height: calc(100vh - var(--header-height) - 2rem);
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .chat-sidebar {
            width: 320px;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }

        .chat-sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .chat-sidebar-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .chat-item:hover {
            background: #f8fafc;
        }

        .chat-item.active {
            background: #d1fae5;
        }

        .chat-item-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #10b981;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
        }

        .chat-item-info {
            flex: 1;
            min-width: 0;
        }

        .chat-item-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .chat-item-last-message {
            color: #64748b;
            font-size: 0.875rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-badge {
            background: #10b981;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 999px;
            margin-left: 0.5rem;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-user-info {
            display: flex;
            align-items: center;
        }

        .chat-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #10b981;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
        }

        .chat-user-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .chat-user-status {
            color: #64748b;
            font-size: 0.875rem;
        }

        .chat-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: none;
            background: none;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon:hover {
            background: #f8fafc;
            color: #10b981;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 80%;
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
            align-self: flex-start;
        }

        .message.outgoing {
            align-self: flex-end; 
        }

        .message-bubble {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            background: #e2e8f0; 
            position: relative;
        }

        .message.outgoing .message-bubble {
            background: #10b981; 
            color: white;
        }

        .message-time {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
            align-self: flex-start; 
        }

        .message.outgoing .message-time {
            align-self: flex-end; 
        }
        .chat-input {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .message-form {
            display: flex;
            gap: 1rem;
        }

        .message-form input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .btn-send {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: none;
            background: #10b981;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-send:hover {
            background: #059669;
        }

        .btn-send:disabled {
            background: #64748b;
            cursor: not-allowed;
        }

        [data-theme="dark"] {
            --primary-color: #10b981;
            --primary-hover: #059669;
            --primary-light: rgba(16, 185, 129, 0.2);
            --text-primary: #ffffff;
            --text-secondary: #ffffff;
            --bg-primary: #000000;
            --bg-secondary: #000000;
            --bg-light: #121212;
            --border-color: #2c2c2c;
        }

        [data-theme="dark"] .chat-container {
            background: #000000;
            border-color: #2c2c2c;
        }

        [data-theme="dark"] .chat-sidebar {
            background: #000000;
            border-color: #2c2c2c;
        }

        [data-theme="dark"] .message-bubble {
            background: #121212;
            color: #ffffff;
        }

        [data-theme="dark"] .chat-item:hover {
            background: #1a1a1a;
        }

        [data-theme="dark"] .chat-item.active {
            background: rgba(16, 185, 129, 0.2);
        }

        [data-theme="dark"] .message-form input {
            background: #121212;
            border-color: #2c2c2c;
            color: #ffffff;
        }

        [data-theme="dark"] * {
            border-color: #2c2c2c;
        }
        @media (max-width: 768px) {
            .chat-container {
                flex-direction: column-reverse; 
            }

            .chat-sidebar {
                width: 100%;
                height: 40%;
                border-left: none;
                border-top: 1px solid #e2e8f0;
            }

            .chat-main {
                height: 60%;
            }
        }
        .btn-send:disabled {
            background: #64748b;
            cursor: not-allowed;
        }

        .btn-delete {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        [data-theme="dark"] .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .loading-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: white;
            font-size: 14px;
            font-weight: 500;
            margin: 0;
        }

        .delete-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            padding: 1rem;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            max-width: 400px;
            margin: 2rem auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-close {
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0.25rem;
        }

        .modal-body {
            padding: 1rem;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn-confirm, .btn-cancel {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }

        .btn-confirm {
            background-color: #ef4444;
            color: white;
        }

        .btn-cancel {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        [data-theme="dark"] .modal-content {
            background: #000000;
            border: 1px solid #333333;
        }

        [data-theme="dark"] .modal-header {
            border-bottom: 1px solid #333333;
        }

        [data-theme="dark"] .btn-cancel {
            background-color: #333333;
            color: #ffffff;
        }
    </style>
</head>
<body>
<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <h2>Active Conversations</h2>
            <div class="search-box">
                <input type="text" id="searchChats" placeholder="Search conversations...">
                <i class="fas fa-search"></i>
            </div>
        </div>
        <div class="chat-list">
            <?php 
            if ($chats_result && !is_array($chats_result) && $chats_result->num_rows > 0):
                while ($chat = $chats_result->fetch_assoc()): 
            ?>
                <div class="chat-item <?php echo isset($_GET['chat_id']) && $_GET['chat_id'] == $chat['id'] ? 'active' : ''; ?>" 
                     data-chat-id="<?php echo htmlspecialchars($chat['id']); ?>">
                     <div class="chat-item-avatar">
                        <?php echo strtoupper(substr($chat['first_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="chat-item-info">
                        <div class="chat-item-name"><?php echo htmlspecialchars($chat['first_name'] . ' ' . $chat['last_name']); ?></div>
                        <?php
                        $last_msg_query = "SELECT message FROM support_messages 
                                         WHERE chat_id = " . $mysqli->real_escape_string($chat['id']) . "
                                         ORDER BY created_at DESC LIMIT 1";
                        $last_msg_result = safeQuery($mysqli, $last_msg_query);
                        $last_msg = $last_msg_result ? $last_msg_result->fetch_assoc() : null;
                        ?>
                        <div class="chat-item-last-message">
                            <?php echo $last_msg ? htmlspecialchars(substr($last_msg['message'], 0, 30)) . '...' : 'No messages yet'; ?>
                        </div>
                    </div>
                    <?php
                    $unread_query = "SELECT COUNT(*) as count FROM support_messages 
                                   WHERE chat_id = " . $mysqli->real_escape_string($chat['id']) . " 
                                   AND is_read = 0";
                    $unread_result = safeQuery($mysqli, $unread_query);
                    $unread = $unread_result ? $unread_result->fetch_assoc() : ['count' => 0];
                    if ($unread['count'] > 0):
                    ?>
                    <div class="chat-item-badge"><?php echo (int)$unread['count']; ?></div>
                    <?php endif; ?>
                    <button class="btn-delete" onclick="deleteChat(<?php echo htmlspecialchars($chat['id']); ?>)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="chat-item">
                    <div class="chat-item-info">
                        <div class="chat-item-name">No active chats</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="chat-main">
        <?php
        if (isset($_GET['chat_id'])):
            $chat_id = (int)$_GET['chat_id'];
            $messages = getMessages($chat_id);
        ?>
        <div class="chat-header">
            <div class="chat-user-info">
            <?php
                $user_query = "SELECT first_name, last_name FROM user WHERE user_id = (
                    SELECT user_id FROM support_chats WHERE id = ?)";
                $stmt = $mysqli->prepare($user_query);
                $stmt->bind_param("i", $chat_id);
                $stmt->execute();
                $user_result = $stmt->get_result();
                $user = $user_result->fetch_assoc();
                ?>
                <div class="chat-user-avatar">
                    <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="chat-user-details">
                    <div class="chat-user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                    <div class="chat-user-status">Active</div>
                </div>
            </div>
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <?php 
            if ($messages && $messages->num_rows > 0):
                while ($message = $messages->fetch_assoc()): 
                    $isAdmin = $message['sender_id'] == $_SESSION['admin_id'];
            ?>
                <div class="message <?php echo $isAdmin ? '' : 'outgoing'; ?>">
                    <div class="message-bubble">
                        <?php echo htmlspecialchars($message['message']); ?>
                    </div>
                    <div class="message-time">
                        <?php echo $message['formatted_time']; ?>
                    </div>
                </div>
            <?php 
                endwhile;
            endif; 
            ?>
        </div>
        <div class="chat-input">
            <form id="messageForm" class="message-form">
                <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars($chat_id); ?>">
                <input type="text" id="messageInput" name="message" placeholder="Type your message..." required>
                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p class="loading-text">Processing your request...</p>
    </div>
</div>

<div id="deleteConfirmModal" class="delete-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <p class="text-center mb-4">
                Are you sure you want to delete this chat? This action cannot be undone.
            </p>
            <div class="button-group">
                <button id="deleteConfirmBtn" class="btn-confirm">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <button id="deleteCancelBtn" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>
    <script>
        let lastMessageId = 0;
        let isPolling = false;
        let currentChatId = null;
        let pollInterval = null;
        function initChat() {
            const urlParams = new URLSearchParams(window.location.search);
            currentChatId = urlParams.get('chat_id');

            initChatList();

            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', handleMessageSubmit);
            }

            if (currentChatId) {
                startPolling();
                scrollToBottom(); 
            }

            const searchInput = document.getElementById('searchChats');
            if (searchInput) {
                searchInput.addEventListener('input', handleSearch);
            }
        }

        function initChatList() {
            document.querySelectorAll('.chat-item').forEach(item => {
                item.addEventListener('click', function() {
                    const chatId = this.dataset.chatId;
                    if (!chatId) return;
                    
                    document.querySelectorAll('.chat-item').forEach(chat => {
                        chat.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    currentChatId = chatId;
                    loadMessages(chatId);
                    
                    const url = new URL(window.location.href);
                    url.searchParams.set('chat_id', chatId);
                    window.history.pushState({}, '', url);
                });
            });
        }

        async function loadMessages(chatId) {
            if (!chatId) return;
            
            try {
                const response = await fetch(`?section=user-live-chat&chat_id=${chatId}`);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const chatHeader = doc.querySelector('.chat-header');
                if (chatHeader) {
                    const currentHeader = document.querySelector('.chat-header');
                    if (currentHeader) {
                        currentHeader.replaceWith(chatHeader);
                    }
                }
                
                const newMessages = doc.querySelector('.chat-messages');
                if (newMessages) {
                    const currentMessages = document.querySelector('.chat-messages');
                    if (currentMessages) {
                        currentMessages.innerHTML = newMessages.innerHTML;
                    }
                }
                
                const chatInput = doc.querySelector('.chat-input');
                if (chatInput) {
                    const currentInput = document.querySelector('.chat-input');
                    if (currentInput) {
                        currentInput.replaceWith(chatInput);
                    }
                }
                
                const messageForm = document.getElementById('messageForm');
                if (messageForm) {
                    messageForm.addEventListener('submit', handleMessageSubmit);
                }
                
                scrollToBottom();
                startPolling();
                
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        async function handleMessageSubmit(e) {
            e.preventDefault();
            
            const form = e.target;
            const messageInput = form.querySelector('#messageInput');
            const chatId = form.querySelector('input[name="chat_id"]').value;
            const submitButton = form.querySelector('.btn-send');
            
            if (!messageInput.value.trim()) return;

            try {
                submitButton.disabled = true;
                
                const formData = new FormData();
                formData.append('chat_id', chatId);
                formData.append('message', messageInput.value.trim());
                
                const response = await fetch('admin/users/send-message.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    messageInput.value = '';
                    
                    if (result.data.messages_updated) {
                        updateChatMessages(result.data.messages);
                    } else {
                        await checkNewMessages();
                    }
                    scrollToBottom();
                } else {
                    alert(result.message || 'Failed to send message. Please try again.');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please check your connection and try again.');
            } finally {
                submitButton.disabled = false;
            }
        }

        function updateChatMessages(messages) {
            const chatMessages = document.getElementById('chatMessages');
            if (!chatMessages) return;
            
            chatMessages.innerHTML = ''; 
            
            messages.forEach(message => {
                const isAdmin = message.sender_id == window.adminId; 
                const messageHtml = `
                    <div class="message ${isAdmin ? '' : 'outgoing'}">
                        <div class="message-bubble">
                            ${escapeHtml(message.message)}
                        </div>
                        <div class="message-time">
                            ${message.formatted_time}
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            });
        }

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        window.adminId = <?php echo json_encode($_SESSION['admin_id']); ?>;

        function startPolling() {
            if (!currentChatId || isPolling) return;
            
            isPolling = true;
            
            if (pollInterval) {
                clearInterval(pollInterval);
            }

            checkNewMessages();

            pollInterval = setInterval(checkNewMessages, 3000); 
        }

        async function checkNewMessages() {
            if (!currentChatId) return;
            
            try {
                const response = await fetch(`?section=user-live-chat&chat_id=${currentChatId}`);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const text = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                
                const newMessagesContainer = doc.querySelector('.chat-messages');
                if (!newMessagesContainer) return;
                
                const chatMessages = document.getElementById('chatMessages');
                if (!chatMessages) return;
                
                const currentMessages = chatMessages.querySelectorAll('.message');
                const newMessages = newMessagesContainer.querySelectorAll('.message');
                
                if (newMessages.length > currentMessages.length) {
                    for (let i = currentMessages.length; i < newMessages.length; i++) {
                        const newMessage = newMessages[i].cloneNode(true);
                        chatMessages.appendChild(newMessage);
                    }
                    
                    scrollToBottom();
                }
            } catch (error) {
                console.error('Error checking new messages:', error);
            }
        }

        function handleSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            const chatItems = document.querySelectorAll('.chat-item');
            
            chatItems.forEach(item => {
                const chatName = item.querySelector('.chat-item-name').textContent.toLowerCase();
                const lastMessage = item.querySelector('.chat-item-last-message').textContent.toLowerCase();
                
                if (chatName.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function scrollToBottom() {
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        async function deleteChat(chatId) {
            event.stopPropagation();
            
            const modal = document.getElementById('deleteConfirmModal');
            const confirmBtn = document.getElementById('deleteConfirmBtn');
            const cancelBtn = document.getElementById('deleteCancelBtn');
            const closeBtn = modal.querySelector('.modal-close');
            const loadingOverlay = document.getElementById('loadingOverlay');

            modal.style.display = "block";

            const handleDelete = async () => {
                try {
                    modal.style.display = "none";
                    loadingOverlay.style.display = "flex";

                    const response = await fetch('admin/users/delete-chat.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `chat_id=${chatId}`
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const result = await response.json();
                    
                    if (result.success) {
                        const chatItem = document.querySelector(`.chat-item[data-chat-id="${chatId}"]`);
                        if (chatItem) {
                            chatItem.remove();
                        }

                        if (currentChatId === chatId) {
                            const chatMain = document.querySelector('.chat-main');
                            chatMain.innerHTML = '';
                            currentChatId = null;
                            if (pollInterval) {
                                clearInterval(pollInterval);
                            }
                        }
                    } else {
                        throw new Error(result.message || 'Failed to delete chat');
                    }
                } catch (error) {
                    console.error('Error deleting chat:', error);
                    alert('Failed to delete chat. Please try again.');
                } finally {
                    loadingOverlay.style.display = "none";
                }
            };

            confirmBtn.onclick = handleDelete;
            cancelBtn.onclick = () => modal.style.display = "none";
            closeBtn.onclick = () => modal.style.display = "none";

            window.onclick = function(e) {
                if (e.target == modal) {
                    modal.style.display = "none";
                }
            };
        }
        document.addEventListener('DOMContentLoaded', initChat);
    </script>
</body>
</html>