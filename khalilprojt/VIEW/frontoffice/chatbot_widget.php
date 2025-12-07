<!-- Widget Chatbot ImpactAble -->
<div id="chatbot-widget" class="chatbot-widget">
    <button id="chatbot-toggle" class="chatbot-toggle" onclick="toggleChatbot()">
        <img src="images/khalil.jpg" alt="Khalil" class="chatbot-toggle-img">
        <span class="chatbot-close">✕</span>
        <span class="chatbot-notification" id="chatbot-notification">1</span>
    </button>
    
    <div id="chatbot-window" class="chatbot-window">
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <img src="images/khalil.jpg" alt="Khalil" class="chatbot-avatar-img">
                <div class="chatbot-header-text">
                    <span class="chatbot-name">Khalil - Assistant</span>
                    <span class="chatbot-status"><span class="status-dot"></span> En ligne</span>
                </div>
            </div>
            <button class="chatbot-minimize" onclick="toggleChatbot()">−</button>
        </div>
        
        <div id="chatbot-messages" class="chatbot-messages"></div>
        
        <div id="chatbot-suggestions" class="chatbot-suggestions"></div>
        
        <div class="chatbot-input-container">
            <input type="text" id="chatbot-input" class="chatbot-input" placeholder="Écrivez votre message..." onkeypress="handleChatKeypress(event)">
            <button class="chatbot-send" onclick="sendChatMessage()">➤</button>
        </div>
    </div>
</div>

<style>
:root {
    --chat-primary: #1a1a1a;
    --chat-secondary: #5E6D3B;
    --chat-accent: #b47b47;
    --chat-bg: #F4ECDD;
    --chat-white: #FFFAF5;
}

.chatbot-widget {
    position: fixed;
    bottom: 25px;
    right: 25px;
    z-index: 9999;
    font-family: 'Inter', sans-serif;
}

.chatbot-toggle {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--chat-secondary), var(--chat-primary));
    border: none;
    cursor: pointer;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
}

.chatbot-toggle:hover { transform: scale(1.1); }

.chatbot-toggle-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}
.chatbot-close { font-size: 1.5em; color: white; display: none; position: absolute; }

.chatbot-widget.open .chatbot-toggle-img { display: none; }
.chatbot-widget.open .chatbot-close { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }

.chatbot-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #D32F2F;
    color: white;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 0.75em;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse-notification 2s infinite;
}

.chatbot-widget.open .chatbot-notification { display: none; }

@keyframes pulse-notification {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    height: 520px;
    background: var(--chat-white);
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

.chatbot-widget.open .chatbot-window { display: flex; }

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.chatbot-header {
    background: linear-gradient(135deg, var(--chat-secondary), var(--chat-primary));
    color: white;
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header-info { display: flex; align-items: center; gap: 12px; }

.chatbot-avatar {
    font-size: 2em;
    background: rgba(255,255,255,0.2);
    padding: 8px;
    border-radius: 50%;
}

.chatbot-avatar-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.3);
}

.chatbot-header-text { display: flex; flex-direction: column; }
.chatbot-name { font-weight: 700; font-size: 1.1em; }
.chatbot-status { font-size: 0.8em; opacity: 0.9; display: flex; align-items: center; gap: 5px; }

.status-dot {
    width: 8px;
    height: 8px;
    background: #4CAF50;
    border-radius: 50%;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.chatbot-minimize {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2em;
}

.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    background: var(--chat-bg);
}

.chat-message {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 18px;
    line-height: 1.5;
    font-size: 0.95em;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.chat-message.bot {
    background: var(--chat-white);
    color: var(--chat-primary);
    align-self: flex-start;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.chat-message.user {
    background: linear-gradient(135deg, var(--chat-secondary), var(--chat-primary));
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 5px;
}

.chat-message.bot .message-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--chat-secondary);
    font-size: 0.85em;
}

.msg-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
}

.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 15px;
    align-self: flex-start;
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    background: var(--chat-secondary);
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 100% { transform: translateY(0); opacity: 0.5; }
    50% { transform: translateY(-5px); opacity: 1; }
}

.chatbot-suggestions {
    padding: 10px 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    background: var(--chat-white);
    border-top: 1px solid var(--chat-bg);
}

.suggestion-btn {
    background: var(--chat-bg);
    border: 1px solid var(--chat-secondary);
    color: var(--chat-secondary);
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 0.8em;
    cursor: pointer;
    transition: all 0.3s;
}

.suggestion-btn:hover {
    background: var(--chat-secondary);
    color: white;
}

.chatbot-input-container {
    display: flex;
    padding: 15px;
    background: var(--chat-white);
    border-top: 1px solid var(--chat-bg);
    gap: 10px;
}

.chatbot-input {
    flex: 1;
    padding: 12px 18px;
    border: 2px solid var(--chat-bg);
    border-radius: 25px;
    font-size: 0.95em;
}

.chatbot-input:focus {
    outline: none;
    border-color: var(--chat-secondary);
}

.chatbot-send {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--chat-secondary), var(--chat-primary));
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    font-size: 1.2em;
}

.chatbot-send:hover { transform: scale(1.1); }

@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 30px);
        height: 70vh;
        bottom: 75px;
        right: -10px;
    }
}
</style>

<script>
let chatbotOpen = false;
let chatbotInitialized = false;

function toggleChatbot() {
    const widget = document.getElementById('chatbot-widget');
    chatbotOpen = !chatbotOpen;
    
    if (chatbotOpen) {
        widget.classList.add('open');
        if (!chatbotInitialized) {
            initializeChatbot();
            chatbotInitialized = true;
        }
        document.getElementById('chatbot-input').focus();
    } else {
        widget.classList.remove('open');
    }
}

function initializeChatbot() {
    fetch('api_chatbot.php?action=welcome')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                addBotMessage(data.data.response);
                updateSuggestions(data.data.suggestions);
            }
        })
        .catch(() => {
            addBotMessage("Bonjour ! 👋 Je suis Khalil. Comment puis-je vous aider ?");
        });
}

function addBotMessage(message) {
    const container = document.getElementById('chatbot-messages');
    
    const typingDiv = document.createElement('div');
    typingDiv.className = 'typing-indicator';
    typingDiv.innerHTML = '<span></span><span></span><span></span>';
    container.appendChild(typingDiv);
    container.scrollTop = container.scrollHeight;
    
    setTimeout(() => {
        typingDiv.remove();
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message bot';
        
        const formattedMessage = message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        
        messageDiv.innerHTML = `
            <div class="message-header"><img src="images/khalil.jpg" alt="Khalil" class="msg-avatar"> Khalil</div>
            <div class="message-content">${formattedMessage}</div>
        `;
        
        container.appendChild(messageDiv);
        container.scrollTop = container.scrollHeight;
    }, 800);
}

function addUserMessage(message) {
    const container = document.getElementById('chatbot-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message user';
    messageDiv.textContent = message;
    container.appendChild(messageDiv);
    container.scrollTop = container.scrollHeight;
}

function updateSuggestions(suggestions) {
    const container = document.getElementById('chatbot-suggestions');
    container.innerHTML = '';
    
    if (suggestions && suggestions.length > 0) {
        suggestions.forEach(suggestion => {
            const btn = document.createElement('button');
            btn.className = 'suggestion-btn';
            btn.textContent = suggestion;
            btn.onclick = () => sendSuggestion(suggestion);
            container.appendChild(btn);
        });
    }
}

function sendSuggestion(text) {
    document.getElementById('chatbot-input').value = text;
    sendChatMessage();
}

function sendChatMessage() {
    const input = document.getElementById('chatbot-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    addUserMessage(message);
    input.value = '';
    
    fetch('api_chatbot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            addBotMessage(data.data.response);
            if (data.data.suggestions) updateSuggestions(data.data.suggestions);
        } else {
            addBotMessage("Désolé, je n'ai pas pu traiter votre message. Réessayez ! 😅");
        }
    })
    .catch(() => {
        addBotMessage("Oups ! Une erreur s'est produite. Veuillez réessayer. 🔄");
    });
}

function handleChatKeypress(event) {
    if (event.key === 'Enter') sendChatMessage();
}
</script>
