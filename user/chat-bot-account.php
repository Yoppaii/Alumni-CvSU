<?php 
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CvSU Alumni Chatbot</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.1/marked.min.js"></script>
</head>
<style>
    :root {
        --primary-color: #2d6936;
        --secondary-color: #1e40af;
        --background-color: #f4f6f8;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --text-primary: #111827;
        --text-secondary: #4b5563;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
    }

    body {
        background: var(--background-color);
        min-height: 100vh;
        padding: 10px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        margin: 0;
    }

    .bot-container {
        width: 100%;
        min-height: 100vh;
        position: relative;
        background: var(--background-color);
        padding: 10px;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .bot-container * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    .bot-chatbot-card {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        width: calc(100% - 20px);
        max-width: 1200px;
        margin: 0 auto;
        height: calc(100vh - 20px);
        display: flex;
        flex-direction: column;
    }

    .bot-chatbot-header {
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .bot-chatbot-header h1 {
        font-size: 24px;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .bot-chatbot-header h1 i {
        color: var(--primary-color);
    }

    .bot-past-chat-button {
        background: transparent;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bot-past-chat-button:hover {
        background: var(--primary-color);
        color: white;
    }

    .bot-chat-messages {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        background: var(--background-color);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .bot-message {
        display: flex;
        align-items: flex-start;
        max-width: 75%;
    }

    .bot-message.bot {
        margin-right: auto;
    }

    .bot-message.user {
        margin-left: auto;
        flex-direction: row-reverse;
    }

    .bot-message-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin: 0 16px;
        background: #ecfdf5;
        color: var(--primary-color);
    }

    .bot-message.user .bot-message-icon {
        background: var(--primary-color);
        color: white;
    }
    .bot-message-content {
        background: white;
        padding: 12px 16px;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        font-size: 14px;
        line-height: 1.5;
        color: var(--text-secondary);
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
        max-width: 100%;
        hyphens: auto;
    }

    .bot-message.user .bot-message-content {
        background: var(--primary-color);
        color: white;
    }

    .bot-message-content p {
        margin: 0 0 1em 0;
    }

    .bot-message-content p:last-child {
        margin-bottom: 0;
    }

    .bot-message-content strong {
        font-weight: 600;
    }

    .bot-message-content em {
        font-style: italic;
    }

    .bot-message-content code {
        background-color: #f3f4f6;
        padding: 2px 4px;
        border-radius: 4px;
        font-family: monospace;
    }

    .bot-message-content pre {
        background-color: #f3f4f6;
        padding: 1em;
        border-radius: 4px;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
        margin: 1em 0;
        font-size: 12px;
    }

    .bot-message-content code {
        background-color: #f3f4f6;
        padding: 2px 4px;
        border-radius: 4px;
        font-family: monospace;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 12px;
    }

    .bot-message-content ul, 
    .bot-message-content ol {
        margin: 1em 0;
        padding-left: 2em;
    }

    .bot-message-content li {
        margin: 0.5em 0;
    }

    .bot-message-content h1,
    .bot-message-content h2,
    .bot-message-content h3,
    .bot-message-content h4,
    .bot-message-content h5,
    .bot-message-content h6 {
        margin: 1em 0 0.5em 0;
        font-weight: 600;
    }

    .bot-chat-input {
        padding: 16px;
        background: white;
        border-top: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .bot-input-container {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .bot-input-container input {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: white;
    }

    .bot-input-container input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(45, 105, 54, 0.1);
    }

    .bot-send-button {
        padding: 12px 20px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s ease;
        white-space: nowrap;
    }

    .bot-send-button:hover {
        background: #235029;
    }

    .bot-typing-indicator {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        align-items: center;
    }

    .bot-typing-dot {
        width: 8px;
        height: 8px;
        background: #9ca3af;
        border-radius: 50%;
        animation: bot-typing 1.4s infinite ease-in-out;
    }

    .bot-typing-dot:nth-child(1) { animation-delay: 0s; }
    .bot-typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .bot-typing-dot:nth-child(3) { animation-delay: 0.4s; }

    .bot-powered-by {
        text-align: right;
        padding: 8px 16px;
        color: var(--text-muted);
        font-size: 12px;
        background: white;
        border-top: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .bot-powered-by img {
        height: 16px;
        vertical-align: middle;
        margin-left: 4px;
    }

    @keyframes bot-typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-4px); }
    }

    @media (max-width: 768px) {
        .bot-message {
            max-width: 85%;
        }

        .bot-message-content {
            font-size: 13px;
        }

        .bot-message-content pre,
        .bot-message-content code {
            font-size: 11px;
        }

        .bot-message-content table {
            display: block;
            width: 100%;
            overflow-x: auto;
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .bot-message {
            max-width: 90%;
        }

        .bot-message-content {
            font-size: 12px;
            padding: 10px 12px;
        }

        .bot-message-content pre,
        .bot-message-content code {
            font-size: 10px;
        }
    }

    .bot-message-content a {
        word-break: break-all;
    }

    .bot-message-content table {
        max-width: 100%;
        margin: 1em 0;
        border-collapse: collapse;
    }

    .bot-message-content th,
    .bot-message-content td {
        padding: 6px;
        border: 1px solid var(--border-color);
        word-break: break-word;
    }
</style>

<body>
    <div class="bot-container">
        <div class="bot-chatbot-card">
            <div class="bot-chatbot-header">
                <h1><i class="fas fa-robot"></i> AI Assistant</h1>
                <button class="bot-past-chat-button">
                    Past Chat <i class="fas fa-history"></i>
                </button>
            </div>
            
            <div class="bot-chat-messages" id="chat-messages">
            </div>
            
            <div class="bot-chat-input">
                <div class="bot-input-container">
                    <input type="text" id="user-input" placeholder="Type your message..." autocomplete="off">
                    <button class="bot-send-button" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send</span>
                    </button>
                </div>
            </div>
            
            <div class="bot-powered-by">
                Powered by: <img src="https://th.bing.com/th/id/R.7e557f1c0864829c54c300d15bee69f4?rik=fjZN1AYH30vXIw&riu=http%3a%2f%2fpngimg.com%2fuploads%2fgoogle%2fgoogle_PNG19635.png&ehk=ZmsumEtoeJQhKoUzQTZO2TEbYPBu0%2b7EFdjmJ3qljls%3d&risl=&pid=ImgRaw&r=0" alt="Google Gemini">
            </div>
        </div>
    </div>

    <script>
        const GEMINI_API_KEY = 'AIzaSyBF0IHFGp4sWrvU5IX7Mim5LqtO7xvMOR4';
        const chatMessages = document.getElementById('chat-messages');
        const userInput = document.getElementById('user-input');

        const username = '<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : "Guest"; ?>';

        marked.setOptions({
            headerIds: false,
            mangle: false,
            breaks: true
        });

        addMessage(`Hello ${username}! How can I assist you today?`, 'bot');

        function sanitizeHTML(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }


        function addMessage(content, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `bot-message ${type}`;
            
            const iconDiv = document.createElement('div');
            iconDiv.className = 'bot-message-icon';
            iconDiv.innerHTML = type === 'bot' ? 
                '<i class="fas fa-robot"></i>' : 
                '<i class="fas fa-user"></i>';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'bot-message-content';
            
            if (type === 'bot') {
                const sanitizedContent = sanitizeHTML(content);
                contentDiv.innerHTML = marked.parse(sanitizedContent);
            } else {
                contentDiv.textContent = content;
            }

            messageDiv.appendChild(iconDiv);
            messageDiv.appendChild(contentDiv);
            chatMessages.appendChild(messageDiv);

            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'bot-message bot';
            typingDiv.id = 'typing-indicator';

            const iconDiv = document.createElement('div');
            iconDiv.className = 'bot-message-icon';
            iconDiv.innerHTML = '<i class="fas fa-robot"></i>';

            const indicatorDiv = document.createElement('div');
            indicatorDiv.className = 'bot-typing-indicator';
            indicatorDiv.innerHTML = `
                <div class="bot-typing-dot"></div>
                <div class="bot-typing-dot"></div>
                <div class="bot-typing-dot"></div>
            `;

            typingDiv.appendChild(iconDiv);
            typingDiv.appendChild(indicatorDiv);
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function removeTypingIndicator() {
            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        async function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;

            addMessage(message, 'user');
            userInput.value = '';

            showTypingIndicator();

            try {
                const response = await callGeminiAPI(message);
                removeTypingIndicator();
                addMessage(response, 'bot');
            } catch (error) {
                console.error('Error:', error);
                removeTypingIndicator();
                addMessage('Sorry, I encountered an error. Please try again.', 'bot');
            }
        }

        async function callGeminiAPI(userInput) {
            const developerQuestions = [
                'who developed you',
                'who created you',
                'who made you',
                'who is your developer',
                'who is your creator',
                'who built you',
                'who programmed you',
                'who create you'
            ];

            const isAskingAboutDeveloper = developerQuestions.some(question => 
                userInput.toLowerCase().includes(question)
            );

            if (isAskingAboutDeveloper) {
                return "I was developed by **Keith Joshua T. Bungalso**. I'm powered by Google's Gemini technology.";
            }

            try {
                const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${GEMINI_API_KEY}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        contents: [{
                            parts: [{ 
                                text: `You are a helpful AI assistant. Act professionally and format your responses using markdown.
                                
                                User query: ${userInput}` 
                            }]
                        }],
                        generationConfig: {
                            temperature: 0.7,
                            topK: 40,
                            topP: 0.95,
                            maxOutputTokens: 1024,
                        },
                        safetySettings: [
                            {
                                category: "HARM_CATEGORY_HARASSMENT",
                                threshold: "BLOCK_MEDIUM_AND_ABOVE"
                            },
                            {
                                category: "HARM_CATEGORY_HATE_SPEECH",
                                threshold: "BLOCK_MEDIUM_AND_ABOVE"
                            },
                            {
                                category: "HARM_CATEGORY_SEXUALLY_EXPLICIT",
                                threshold: "BLOCK_MEDIUM_AND_ABOVE"
                            },
                            {
                                category: "HARM_CATEGORY_DANGEROUS_CONTENT",
                                threshold: "BLOCK_MEDIUM_AND_ABOVE"
                            }
                        ]
                    })
                });

                if (!response.ok) {
                    throw new Error('API request failed');
                }

                const data = await response.json();

                if (data.candidates && data.candidates[0] && data.candidates[0].content && data.candidates[0].content.parts && data.candidates[0].content.parts[0]) {
                    return data.candidates[0].content.parts[0].text;
                } else {
                    throw new Error('Invalid response structure');
                }
            } catch (error) {
                console.error('Error calling Gemini API:', error);
                return 'Sorry, I encountered an error. Please try again.';
            }
        }

        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        document.querySelector('.bot-past-chat-button').addEventListener('click', function() {
            console.log('Past chat clicked');
        });

        function saveChatHistory() {
            const messages = Array.from(chatMessages.children).map(msg => {
                const content = msg.querySelector('.bot-message-content').textContent;
                const type = msg.classList.contains('bot') ? 'bot' : 'user';
                return { content, type };
            });
            
            localStorage.setItem('chatHistory', JSON.stringify(messages));
        }

        function loadChatHistory() {
            const history = localStorage.getItem('chatHistory');
            if (history) {
                const messages = JSON.parse(history);
                messages.forEach(msg => {
                    addMessage(msg.content, msg.type);
                });
            }
        }

    </script>
</body>
</html>