<?php
$page_title = "Support Center";

$support_hours = [
    'weekdays' => '9:00 AM - 6:00 PM',
    'weekends' => '10:00 AM - 4:00 PM'
];

$faqs = [
    [
        'question' => 'How do I reset my password?',
        'answer' => 'You can reset your password by clicking the "Forgot Password" link on the login page and following the instructions sent to your email.'
    ],
    [
        'question' => 'What are your response times?',
        'answer' => 'We aim to respond to all inquiries within 24 hours during business days. Priority support tickets are handled within 4 hours.'
    ]
];

$knowledge_base_articles = [
    [
        'title' => 'Getting Started Guide',
        'excerpt' => 'Learn the basics of our platform and how to set up your account for success.',
        'url' => '/kb/getting-started'
    ],
    [
        'title' => 'Troubleshooting Common Issues',
        'excerpt' => 'Solutions to frequently encountered problems and error messages.',
        'url' => '/kb/troubleshooting'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
    <style>
        :root {
            --primary-color: #2d6936;
            --secondary-color: #1e40af;
            --background-color: #f4f6f8;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            background: var(--background-color);
            min-height: 100vh;
            padding: 10px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .support-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .support-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .support-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .support-header h1 i {
            color: var(--primary-color);
        }

        .support-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .operation-hours {
            background-color: #ecfdf5;
            border-radius: 6px;
            padding: 16px;
            margin-top: 16px;
        }

        .operation-hours p {
            margin: 8px 0;
            color: var(--primary-color);
            font-size: 14px;
        }

        .support-content {
            padding: 24px;
        }

        .support-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .support-block {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }

        .support-block h2 {
            color: #111827;
            font-size: 18px;
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .support-block h2 i {
            color: var(--primary-color);
        }

        .help-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .help-option-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            text-align: center;
        }

        .help-option-card h3 {
            color: #111827;
            font-size: 16px;
            margin: 0 0 8px 0;
        }

        .availability-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .availability-dot.available {
            background-color: var(--primary-color);
        }

        .faq-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .faq-item {
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .faq-item:last-child {
            margin-bottom: 0;
        }

        .faq-item h3 {
            color: #111827;
            font-size: 16px;
            margin: 0 0 8px 0;
        }

        .faq-item p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .help-request-form {
            display: grid;
            gap: 16px;
        }

        .input-group {
            display: grid;
            gap: 8px;
        }

        .input-label {
            color: #374151;
            font-size: 14px;
            font-weight: 500;
        }

        .input-field,
        .text-area-field,
        .select-field {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            color: #111827;
        }

        .text-area-field {
            min-height: 120px;
            resize: vertical;
        }

        .action-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .action-button:hover {
            background-color: #235c2b;
        }

        .chat-overlay {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }

            .support-header {
                padding: 16px;
            }

            .support-content {
                padding: 16px;
            }

            .support-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .help-options {
                grid-template-columns: 1fr;
            }

            .chat-overlay {
                width: 100%;
                height: 100%;
                bottom: 0;
                right: 0;
                border-radius: 0;
            }
        }
        .live-chat-modal {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 30px;
            width: 380px;
            height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            overflow: hidden;
        }

        .live-chat-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .live-chat-header {
            padding: 16px 20px;
            background: #006400;
            color: white;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .live-chat-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.9);
            font-size: 24px;
            cursor: pointer;
            padding: 4px;
            line-height: 1;
            transition: all 0.2s ease;
        }

        .live-chat-close:hover {
            transform: rotate(90deg);
            color: white;
        }

        .live-chat-messages {
            flex-grow: 1;
            padding: 16px;
            overflow-y: auto;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .live-chat-message {
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-width: 75%;
            margin-bottom: 16px;
            animation: messageSlide 0.3s ease-out;
        }

        .live-chat-message.support {
            align-self: flex-start;
        }

        .live-chat-message.user {
            align-self: flex-end;
        }

        .message-content-wrapper {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .support .message-content-wrapper {
            flex-direction: row;
        }

        .user .message-content-wrapper {
            flex-direction: row-reverse;
        }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
            width: fit-content;
            font-size: 14px;
            line-height: 1.4;
        }

        .support .message-bubble {
            background: white;
            color: #333;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .user .message-bubble {
            background: #006400;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .live-chat-bot-logo {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .message-time {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
            padding: 0 4px;
        }

        .support .message-time {
            margin-left: 36px;
        }

        .user .message-time {
            text-align: right;
        }

        .live-chat-input-container {
            padding: 12px 16px;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .live-chat-input {
            flex-grow: 1;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            resize: none;
            font-size: 14px;
            line-height: 1.4;
            max-height: 120px;
            min-height: 24px;
            outline: none;
            transition: all 0.2s ease;
        }

        .live-chat-input:focus {
            border-color: #006400;
            box-shadow: 0 0 0 2px rgba(0, 100, 0, 0.1);
        }

        .live-chat-send {
            background: #006400;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .live-chat-send:hover {
            background: #008000;
            transform: scale(1.05);
        }

        .live-chat-send:active {
            transform: scale(0.95);
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .live-chat-modal {
                width: 100%;
                height: 100%;
                bottom: 0;
                right: 0;
                border-radius: 0;
            }
            
            .live-chat-message {
                max-width: 85%;
            }
            
            .live-chat-input-container {
                padding: 12px;
            }
        }
    </style>
<body>
    <div class="main-container">
        <div class="support-card">
            <div class="support-header">
                <h1><i class="fas fa-headset"></i> Support Center</h1>
                <p>We're here to help! Find the support you need through our various channels.</p>
                <div class="operation-hours">
                    <p><strong>Support Hours:</strong></p>
                    <p>Weekdays: <?php echo $support_hours['weekdays']; ?></p>
                    <p>Weekends: <?php echo $support_hours['weekends']; ?></p>
                </div>
            </div>

            <div class="support-content">
                <div class="support-grid">
                    <div class="support-block">
                        <h2><i class="fas fa-comments"></i> Quick Help</h2>
                        <div class="help-options">
                            <div class="help-option-card">
                                <h3>Live Chat</h3>
                                <p><span class="availability-dot available"></span> Available Now</p>
                                <button onclick="initializeChat()" class="action-button">Start Chat</button>
                            </div>
                            <div class="help-option-card">
                                <h3>Phone Support</h3>
                                <p>1-800-SUPPORT</p>
                                <p>24/7 Emergency Support</p>
                            </div>
                        </div>
                    </div>

                    <div class="support-block">
                        <h2><i class="fas fa-question-circle"></i> FAQ</h2>
                        <ul class="faq-list">
                            <?php foreach ($faqs as $faq): ?>
                            <li class="faq-item">
                                <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                                <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="support-block">
                        <h2><i class="fas fa-ticket-alt"></i> Submit a Ticket</h2>
                        <form action="process_ticket.php" method="POST" class="help-request-form">
                            <div class="input-group">
                                <label class="input-label" for="ticket-subject">Subject</label>
                                <input type="text" id="ticket-subject" name="subject" class="input-field" placeholder="Brief description of your issue" required>
                            </div>
                            <div class="input-group">
                                <label class="input-label" for="ticket-description">Description</label>
                                <textarea id="ticket-description" name="description" class="text-area-field" placeholder="Please provide detailed information about your issue..." required></textarea>
                            </div>
                            <div class="input-group">
                                <label class="input-label" for="ticket-urgency">Priority</label>
                                <select id="ticket-urgency" name="priority" class="select-field" required>
                                    <option value="low">Low Priority</option>
                                    <option value="medium">Medium Priority</option>
                                    <option value="high">High Priority</option>
                                </select>
                            </div>
                            <button type="submit" class="action-button">Submit Ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="liveChatModal" class="live-chat-modal">
        <div class="live-chat-wrapper">
            <div class="live-chat-header">
                Live Support Chat
                <button class="live-chat-close" onclick="closeLiveChat()">&times;</button>
            </div>
            <div class="live-chat-messages" id="liveChatMessages">
                <div class="live-chat-message support">
                    <img src="asset/images/res1.png" alt="Support" class="live-chat-bot-logo">
                    <div class="message-content">Hello! How can I help you today?</div>
                    <span class="message-time">10:00 AM</span>
                </div>
                <div class="live-chat-message user">
                    <div class="message-content">Hi, I need help with...</div>
                    <span class="message-time">10:01 AM</span>
                </div>
            </div>
            <div class="live-chat-input-container">
                <textarea class="live-chat-input" id="liveChatInput" 
                        placeholder="Type your message..." rows="1"></textarea>
                <button class="live-chat-send" id="liveChatSend">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
<script>
    class ChatSystem {
        constructor() {
            this.state = {
                chatId: null,
                lastMessageId: 0,
                pollingInterval: null,
                isPolling: false,
                messageQueue: [],
                isProcessing: false,
                hasUserSentFirstMessage: false
            };

            this.elements = {
                modal: document.getElementById('liveChatModal'),
                messages: document.getElementById('liveChatMessages'),
                input: document.getElementById('liveChatInput'),
                sendButton: document.getElementById('liveChatSend')
            };

            this.messages = {
                greeting: "👋 Hi there! Welcome to our support chat.",
                followUp: "How can I assist you today?",
                waitingResponse: "Please wait while we connect you with one of our support representatives. Someone will assist you shortly."
            };

            this.elements.input.addEventListener('input', () => {
                this.elements.input.style.height = 'auto';
                this.elements.input.style.height = Math.min(this.elements.input.scrollHeight, 100) + 'px';
            });
        }

        bindEvents() {
            this.elements.sendButton.addEventListener('click', () => this.sendMessage());
            this.elements.input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            window.addEventListener('click', (event) => {
                if (event.target === this.elements.modal) {
                    this.closeChat();
                }
            });

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.pausePolling();
                } else {
                    this.resumePolling();
                }
            });
        }

        async initialize() {
            try {
                this.elements.modal.style.display = 'block';

                const response = await this.makeRequest('init_chat');
                
                if (response.chat_id) {
                    this.state.chatId = response.chat_id;
                    const hasMessages = await this.loadPreviousMessages();

                    if (!hasMessages) {
                        this.showGreeting();
                    }
                    
                    this.startMessagePolling();
                    this.bindEvents();
                    return true;
                }
                
                throw new Error('Failed to initialize chat');
            } catch (error) {
                this.handleError('Chat initialization failed', error);
                return false;
            }
        }

        showGreeting() {
            this.elements.messages.innerHTML = '';
            setTimeout(() => {
                this.appendMessage(this.messages.greeting, false);

                setTimeout(() => {
                    this.appendMessage(this.messages.followUp, false);
                }, 1000);
            }, 500);
        }

        async loadPreviousMessages() {
            try {
                const response = await this.makeRequest('get_previous_messages', {
                    chat_id: this.state.chatId
                });

                if (response.messages && Array.isArray(response.messages)) {
                    this.elements.messages.innerHTML = '';

                    response.messages.forEach(msg => {
                        this.appendMessage(msg.message, msg.is_user, msg.created_at);
                        if (msg.id > this.state.lastMessageId) {
                            this.state.lastMessageId = msg.id;
                        }
                    });

                    this.scrollToBottom();
                    return response.messages.length > 0;
                }
                return false;
            } catch (error) {
                this.handleError('Failed to load previous messages', error);
                return false;
            }
        }

        async sendMessage() {
            const message = this.elements.input.value.trim();
            
            if (!message || !this.state.chatId) return;

            try {
                this.state.messageQueue.push({
                    message,
                    timestamp: new Date().toISOString()
                });

                this.elements.input.value = '';
                this.elements.input.style.height = 'auto';

                this.appendMessage(message, true);
                this.scrollToBottom();

                if (!this.state.hasUserSentFirstMessage) {
                    this.state.hasUserSentFirstMessage = true;
                    setTimeout(() => {
                        this.appendMessage(this.messages.waitingResponse, false);
                        this.scrollToBottom();
                    }, 1000);
                }

                await this.processMessageQueue();
            } catch (error) {
                this.handleError('Failed to send message', error);
            }
        }

        async processMessageQueue() {
            if (this.state.isProcessing || this.state.messageQueue.length === 0) return;

            this.state.isProcessing = true;

            try {
                while (this.state.messageQueue.length > 0) {
                    const { message, timestamp } = this.state.messageQueue[0];
                    
                    const response = await this.makeRequest('send', {
                        chat_id: this.state.chatId,
                        message: message,
                        timestamp: timestamp
                    });

                    if (!response.success) {
                        throw new Error('Failed to send message');
                    }

                    this.state.messageQueue.shift();
                }
            } catch (error) {
                this.handleError('Error processing message queue', error);
            } finally {
                this.state.isProcessing = false;
            }
        }

        startMessagePolling() {
            if (!this.state.isPolling) {
                this.state.isPolling = true;
                this.pollMessages();
                this.state.pollingInterval = setInterval(() => this.pollMessages(), 2000);
            }
        }

        async pollMessages() {
            if (!this.state.chatId || !this.state.isPolling) return;

            try {
                const response = await this.makeRequest('get_messages', {
                    chat_id: this.state.chatId,
                    last_id: this.state.lastMessageId
                });

                if (response.messages && Array.isArray(response.messages)) {
                    response.messages.forEach(msg => {
                        if (msg.id > this.state.lastMessageId) {
                            this.state.lastMessageId = msg.id;
                            if (!msg.is_user) {
                                this.appendMessage(msg.message, false, msg.created_at);
                                this.scrollToBottom();
                            }
                        }
                    });
                }
            } catch (error) {
                this.handleError('Error polling messages', error);
            }
        }

        pausePolling() {
            this.state.isPolling = false;
            if (this.state.pollingInterval) {
                clearInterval(this.state.pollingInterval);
            }
        }

        resumePolling() {
            if (this.state.chatId) {
                this.startMessagePolling();
            }
        }

        appendMessage(message, isUser, timestamp = null) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `live-chat-message ${isUser ? 'user' : 'support'}`;

            const contentWrapper = document.createElement('div');
            contentWrapper.className = 'message-content-wrapper';

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.textContent = message;

            if (!isUser) {
                const botLogo = document.createElement('img');
                botLogo.className = 'live-chat-bot-logo';
                botLogo.src = 'asset/images/res1.png';
                botLogo.alt = 'Support';
                contentWrapper.appendChild(botLogo);
            }
            
            contentWrapper.appendChild(bubble);
            messageDiv.appendChild(contentWrapper);

            const timeSpan = document.createElement('div');
            timeSpan.className = 'message-time';
            timeSpan.textContent = timestamp ? this.formatTime(new Date(timestamp)) : this.formatTime(new Date());
            messageDiv.appendChild(timeSpan);

            this.elements.messages.appendChild(messageDiv);
            this.scrollToBottom();
        }

        formatTime(date) {
            return date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }).replace(/\s/g, '').toLowerCase(); 
        }

        scrollToBottom() {
            this.elements.messages.scrollTop = this.elements.messages.scrollHeight;
        }

        closeChat() {
            this.elements.modal.style.display = 'none';
            this.pausePolling();
        }

        async makeRequest(action, params = {}) {
            try {
                const formData = new URLSearchParams({
                    action,
                    ...params
                });

                const response = await fetch('user/chat_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }

                return data;
            } catch (error) {
                throw error;
            }
        }

        handleError(context, error) {
            console.error(`${context}:`, error);
        }
    }

    const chatSystem = new ChatSystem();
    function initializeChat() {
        chatSystem.initialize();
    }

    function closeLiveChat() {
        chatSystem.closeChat();
    }
</script>
</body>
</html>