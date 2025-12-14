<!-- Widget Chatbot ImpactAble v2.0 -->
<div id="chatbot-widget" class="chatbot-widget">
    <button id="chatbot-toggle" class="chatbot-toggle" onclick="toggleChatbot()">
        <span class="chatbot-toggle-avatar">K</span>
        <span class="chatbot-close">✕</span>
        <span class="chatbot-notification" id="chatbot-notification">1</span>
    </button>
    
    <div id="chatbot-window" class="chatbot-window">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar-circle">K</div>
                <div class="chatbot-header-text">
                    <span class="chatbot-name">Khalil - Assistant</span>
                    <span class="chatbot-status"><span class="status-dot"></span> En ligne</span>
                </div>
            </div>
            <div class="chatbot-header-actions">
                <button class="chatbot-action-btn" onclick="resetChat()" title="Nouvelle conversation">🔄</button>
                <button class="chatbot-minimize" onclick="toggleChatbot()">−</button>
            </div>
        </div>
        
        <!-- Quick Actions Bar -->
        <div id="chatbot-quick-actions" class="chatbot-quick-actions">
            <button class="quick-action-btn" onclick="quickAction('new_reclamation')">
                <span>📝</span> Réclamation
            </button>
            <button class="quick-action-btn" onclick="quickAction('track')">
                <span>🔍</span> Suivi
            </button>
            <button class="quick-action-btn" onclick="quickAction('demo_ia')">
                <span>🧠</span> Démo IA
            </button>
        </div>
        
        <!-- Messages -->
        <div id="chatbot-messages" class="chatbot-messages"></div>
        
        <!-- Suggestions -->
        <div id="chatbot-suggestions" class="chatbot-suggestions"></div>
        
        <!-- Input -->
        <div class="chatbot-input-container">
            <input type="text" id="chatbot-input" class="chatbot-input" 
                   placeholder="Écrivez votre message..." 
                   onkeypress="handleChatKeypress(event)"
                   maxlength="1000">
            <button class="chatbot-send" onclick="sendChatMessage()">➤</button>
        </div>
        
        <!-- Footer -->
        <div class="chatbot-footer">
            <span>Propulsé par ImpactAble AI</span>
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
    --chat-success: #4CAF50;
    --chat-warning: #FF9800;
    --chat-danger: #f44336;
}

.chatbot-widget {
    position: fixed;
    bottom: 25px;
    right: 25px;
    z-index: 9999;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Toggle Button */
.chatbot-toggle {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--chat-secondary), var(--chat-primary));
    border: none;
    cursor: pointer;
    box-shadow: 0 10px 40px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
}

.chatbot-toggle:hover { 
    transform: scale(1.1); 
    box-shadow: 0 15px 50px rgba(0,0,0,0.3);
}

.chatbot-toggle-avatar {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2em;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
}

.chatbot-close { 
    font-size: 1.5em; 
    color: white; 
    display: none; 
    position: absolute; 
}

.chatbot-widget.open .chatbot-toggle-avatar { display: none; }
.chatbot-widget.open .chatbot-close { 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    width: 100%; 
    height: 100%; 
}

/* Notification Badge */
.chatbot-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--chat-danger);
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

/* Chat Window */
.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 400px;
    height: 580px;
    background: var(--chat-white);
    border-radius: 20px;
    box-shadow: 0 15px 50px rgba(0,0,0,0.25);
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

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, var(--chat-secondary), var(--chat-primary));
    color: white;
    padding: 15px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header-info { display: flex; align-items: center; gap: 12px; }

.chatbot-avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #A9B97D, #5E6D3B);
    border: 3px solid rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3em;
    font-weight: 700;
    color: white;
}

.chatbot-header-text { display: flex; flex-direction: column; }
.chatbot-name { font-weight: 700; font-size: 1em; }
.chatbot-status { font-size: 0.75em; opacity: 0.9; display: flex; align-items: center; gap: 5px; }

.status-dot {
    width: 8px;
    height: 8px;
    background: var(--chat-success);
    border-radius: 50%;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.chatbot-header-actions {
    display: flex;
    gap: 8px;
}

.chatbot-action-btn, .chatbot-minimize {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1em;
    transition: all 0.3s;
}

.chatbot-action-btn:hover, .chatbot-minimize:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}

/* Quick Actions */
.chatbot-quick-actions {
    display: flex;
    gap: 8px;
    padding: 10px 15px;
    background: linear-gradient(135deg, rgba(94,109,59,0.1), rgba(75,46,22,0.05));
    border-bottom: 1px solid var(--chat-bg);
    overflow-x: auto;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 14px;
    background: white;
    border: 1px solid var(--chat-secondary);
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: 600;
    color: var(--chat-secondary);
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.3s;
}

.quick-action-btn:hover {
    background: var(--chat-secondary);
    color: white;
    transform: translateY(-2px);
}

.quick-action-btn span {
    font-size: 1.1em;
}

/* Messages */
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: var(--chat-bg);
}

.chat-message {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 18px;
    line-height: 1.5;
    font-size: 0.9em;
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
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, #A9B97D, #5E6D3B);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65em;
    font-weight: 700;
    color: white;
}

.message-content {
    word-wrap: break-word;
}

/* Message Actions (Feedback) */
.message-actions {
    display: flex;
    gap: 5px;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.feedback-btn {
    background: transparent;
    border: 1px solid var(--chat-bg);
    border-radius: 15px;
    padding: 4px 10px;
    font-size: 0.75em;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 4px;
}

.feedback-btn:hover {
    background: var(--chat-bg);
}

.feedback-btn.positive:hover { border-color: var(--chat-success); color: var(--chat-success); }
.feedback-btn.negative:hover { border-color: var(--chat-danger); color: var(--chat-danger); }

/* Typing Indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 15px;
    align-self: flex-start;
    background: var(--chat-white);
    border-radius: 18px;
    border-bottom-left-radius: 5px;
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

/* Suggestions */
.chatbot-suggestions {
    padding: 10px 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    background: var(--chat-white);
    border-top: 1px solid var(--chat-bg);
    max-height: 80px;
    overflow-y: auto;
}

.suggestion-btn {
    background: var(--chat-bg);
    border: 1px solid transparent;
    color: var(--chat-primary);
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.75em;
    cursor: pointer;
    transition: all 0.3s;
}

.suggestion-btn:hover {
    background: var(--chat-secondary);
    color: white;
    transform: scale(1.05);
}

/* Input Container */
.chatbot-input-container {
    display: flex;
    padding: 12px 15px;
    background: var(--chat-white);
    border-top: 1px solid var(--chat-bg);
    gap: 10px;
}

.chatbot-input {
    flex: 1;
    padding: 12px 18px;
    border: 2px solid var(--chat-bg);
    border-radius: 25px;
    font-size: 0.9em;
    transition: all 0.3s;
}

.chatbot-input:focus {
    outline: none;
    border-color: var(--chat-secondary);
    box-shadow: 0 0 0 3px rgba(94,109,59,0.1);
}

.chatbot-send {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--chat-secondary), var(--chat-primary));
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    font-size: 1.1em;
    transition: all 0.3s;
}

.chatbot-send:hover { 
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(94,109,59,0.3);
}

/* Footer */
.chatbot-footer {
    padding: 8px;
    text-align: center;
    font-size: 0.7em;
    color: #999;
    background: var(--chat-white);
    border-top: 1px solid var(--chat-bg);
}

/* Special Action Button */
.special-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 10px;
    padding: 8px 16px;
    background: linear-gradient(135deg, var(--chat-secondary), var(--chat-accent));
    color: white;
    border: none;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.special-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Responsive */
@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 20px);
        height: 75vh;
        bottom: 75px;
        right: -10px;
        border-radius: 15px;
    }
    
    .chatbot-widget {
        bottom: 15px;
        right: 15px;
    }
    
    .chatbot-toggle {
        width: 55px;
        height: 55px;
    }
}
</style>

<script>
let chatbotOpen = false;
let chatbotInitialized = false;
let currentMessageId = null;

// Toggle chatbot
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

// Initialize chatbot
function initializeChatbot() {
    fetch('api_chatbot.php?action=welcome')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                addBotMessage(data.data.response, data.data.message_id);
                updateSuggestions(data.data.suggestions);
            }
        })
        .catch(() => {
            addBotMessage("Bonjour ! 👋 Je suis Khalil. Comment puis-je vous aider ?");
        });
}

// Add bot message
function addBotMessage(message, messageId = null) {
    const container = document.getElementById('chatbot-messages');
    
    // Typing indicator
    const typingDiv = document.createElement('div');
    typingDiv.className = 'typing-indicator';
    typingDiv.innerHTML = '<span></span><span></span><span></span>';
    container.appendChild(typingDiv);
    container.scrollTop = container.scrollHeight;
    
    setTimeout(() => {
        typingDiv.remove();
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message bot';
        if (messageId) messageDiv.dataset.messageId = messageId;
        
        // Format message
        let formattedMessage = message
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
        
        messageDiv.innerHTML = `
            <div class="message-header">
                <span class="msg-avatar">K</span> Khalil
            </div>
            <div class="message-content">${formattedMessage}</div>
            <div class="message-actions">
                <button class="feedback-btn positive" onclick="sendFeedback('${messageId}', 5)">👍 Utile</button>
                <button class="feedback-btn negative" onclick="sendFeedback('${messageId}', 1)">👎</button>
            </div>
        `;
        
        container.appendChild(messageDiv);
        container.scrollTop = container.scrollHeight;
        currentMessageId = messageId;
    }, 600 + Math.random() * 400);
}

// Add user message
function addUserMessage(message) {
    const container = document.getElementById('chatbot-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message user';
    messageDiv.textContent = message;
    container.appendChild(messageDiv);
    container.scrollTop = container.scrollHeight;
}

// Update suggestions
function updateSuggestions(suggestions) {
    const container = document.getElementById('chatbot-suggestions');
    container.innerHTML = '';
    
    if (suggestions && suggestions.length > 0) {
        suggestions.slice(0, 4).forEach(suggestion => {
            const btn = document.createElement('button');
            btn.className = 'suggestion-btn';
            btn.textContent = suggestion;
            btn.onclick = () => sendSuggestion(suggestion);
            container.appendChild(btn);
        });
    }
}

// Send suggestion
function sendSuggestion(text) {
    document.getElementById('chatbot-input').value = text;
    sendChatMessage();
}

// Send message
function sendChatMessage() {
    const input = document.getElementById('chatbot-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    addUserMessage(message);
    input.value = '';
    
    fetch('api_chatbot.php?action=message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            addBotMessage(data.data.response, data.data.message_id);
            
            if (data.data.suggestions) {
                updateSuggestions(data.data.suggestions);
            }
            
            // Handle special actions
            if (data.data.special_action) {
                handleSpecialAction(data.data.special_action);
            }
        } else {
            addBotMessage("Désolé, je n'ai pas pu traiter votre message. Réessayez ! 😅");
        }
    })
    .catch(() => {
        addBotMessage("Oups ! Une erreur s'est produite. Veuillez réessayer. 🔄");
    });
}

// Handle keypress
function handleChatKeypress(event) {
    if (event.key === 'Enter') sendChatMessage();
}

// Quick actions
function quickAction(actionId) {
    const actions = {
        'new_reclamation': { url: 'index.php#reclamation-form', message: 'Je vous redirige vers le formulaire de réclamation...' },
        'track': { url: 'suivi_reclamation.php', message: 'Je vous redirige vers la page de suivi...' },
        'demo_ia': { url: 'demo_ia.php', message: 'Je vous redirige vers la démo IA...' }
    };
    
    const action = actions[actionId];
    if (action) {
        addBotMessage(action.message);
        setTimeout(() => {
            window.location.href = action.url;
        }, 1000);
    }
}

// Handle special actions
function handleSpecialAction(action) {
    const container = document.getElementById('chatbot-messages');
    const lastMessage = container.lastElementChild;
    
    if (action.type === 'navigate' && lastMessage) {
        const btn = document.createElement('button');
        btn.className = 'special-action-btn';
        btn.innerHTML = `➡️ ${action.label}`;
        btn.onclick = () => window.location.href = action.url;
        lastMessage.querySelector('.message-content').appendChild(btn);
    }
    
    if (action.type === 'contact') {
        const btn = document.createElement('button');
        btn.className = 'special-action-btn';
        btn.innerHTML = `📧 ${action.label}`;
        btn.onclick = () => window.location.href = 'mailto:' + action.email;
        lastMessage.querySelector('.message-content').appendChild(btn);
    }
}

// Send feedback
function sendFeedback(messageId, rating) {
    fetch('api_chatbot.php?action=feedback', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message_id: messageId, rating: rating })
    })
    .then(response => response.json())
    .then(data => {
        // Visual feedback
        const btn = event.target;
        btn.style.background = rating >= 4 ? 'var(--chat-success)' : 'var(--chat-warning)';
        btn.style.color = 'white';
        btn.textContent = '✓ Merci !';
        btn.disabled = true;
    })
    .catch(() => {});
}

// Reset chat
function resetChat() {
    fetch('api_chatbot.php?action=reset')
        .then(() => {
            document.getElementById('chatbot-messages').innerHTML = '';
            chatbotInitialized = false;
            initializeChatbot();
        });
}

// Hide notification after delay
setTimeout(() => {
    const notification = document.getElementById('chatbot-notification');
    if (notification && !chatbotOpen) {
        notification.style.animation = 'none';
    }
}, 5000);
</script>
