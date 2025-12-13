<?php

?>
<!-- Chatbot Container -->
<div id="impactbot-container">
    <!-- Toggle Button -->
    <button id="impactbot-toggle" class="impactbot-btn" aria-label="Open support chat">
        <i class="fas fa-comments"></i>
        <span class="impactbot-notification" id="impactbot-notification"></span>
    </button>
    
    <!-- Chat Window -->
    <div id="impactbot-window" class="impactbot-window">
        <!-- Header -->
        <div class="impactbot-header">
            <div class="impactbot-title">
                <div class="impactbot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h4>ImpactBot</h4>
                    <span class="impactbot-status online">
                        <i class="fas fa-circle"></i> En ligne
                    </span>
                </div>
            </div>
            <div class="impactbot-actions">
                <button class="impactbot-action-btn" title="Clear chat" id="impactbot-clear">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="impactbot-action-btn" title="Minimize" id="impactbot-minimize">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="impactbot-action-btn" title="Close" id="impactbot-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Chat Body -->
        <div class="impactbot-body">
            <!-- Welcome Message -->
            <div class="impactbot-welcome" id="impactbot-welcome">
                <div class="welcome-content">
                    <div class="welcome-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="welcome-text">
                        <strong>👋 Bonjour ! Je suis ImpactBot</strong>
                        <p>Votre assistant pour la plateforme ImpactAble. Je suis là pour vous aider avec :</p>
                        <ul>
                            <li><i class="fas fa-briefcase"></i> Offres d'emploi et candidatures</li>
                            <li><i class="fas fa-calendar"></i> Événements et formations</li>
                            <li><i class="fas fa-universal-access"></i> Accessibilité et inclusion</li>
                            <li><i class="fas fa-cog"></i> Utilisation de la plateforme</li>
                        </ul>
                        <p>Comment puis-je vous aider aujourd'hui ?</p>
                    </div>
                </div>
                
                <!-- Quick Questions -->
                <div class="quick-questions">
                    <button class="quick-question" data-question="Comment postuler à une offre ?">
                        <i class="fas fa-file-upload"></i>
                        <span>Postuler à une offre</span>
                    </button>
                    <button class="quick-question" data-question="Comment créer un événement ?">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Créer un événement</span>
                    </button>
                    <button class="quick-question" data-question="Comment gérer mon profil ?">
                        <i class="fas fa-user-cog"></i>
                        <span>Gérer mon profil</span>
                    </button>
                    <button class="quick-question" data-question="Quelles sont les fonctionnalités d'accessibilité ?">
                        <i class="fas fa-universal-access"></i>
                        <span>Accessibilité</span>
                    </button>
                </div>
            </div>
            
            <!-- Messages Container -->
            <div class="impactbot-messages" id="impactbot-messages"></div>
            
            <!-- Typing Indicator -->
            <div class="impactbot-typing" id="impactbot-typing">
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span>ImpactBot écrit...</span>
            </div>
        </div>
        
        <!-- Chat Footer -->
        <div class="impactbot-footer">

            <!-- Message Input -->
            <div class="impactbot-input-container">
                <textarea 
                    id="impactbot-input" 
                    placeholder="Écrivez votre message ici..." 
                    rows="1"
                    aria-label="Message to ImpactBot"
                ></textarea>
                <button id="impactbot-send" class="impactbot-send-btn" aria-label="Send message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            
            <!-- Footer Note -->
            <div class="impactbot-footer-note">
                <small>
                    <i class="fas fa-info-circle"></i>
                    ImpactBot peut faire des erreurs. Vérifiez les informations importantes.
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Chatbot Styles -->
<style>
/* ImpactBot Styles - Using your color palette */
:root {
    --impactbot-primary: #4b2e16; /* brown */
    --impactbot-secondary: #b47b47; /* copper */
    --impactbot-accent: #5e6d3b; /* moss */
    --impactbot-light: #f4ecdd; /* sand */
    --impactbot-dark: #3a4a2a; /* dark-green */
    --impactbot-success: #a9b97d; /* sage */
    --impactbot-white: #fffaf5; /* white */
}

/* Main Container */
#impactbot-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: 'Inter', sans-serif;
}

/* Toggle Button */
.impactbot-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--impactbot-primary), var(--impactbot-accent));
    border: 3px solid white;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 6px 25px rgba(75, 46, 22, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.impactbot-btn:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 8px 30px rgba(75, 46, 22, 0.4);
}

.impactbot-btn:active {
    transform: scale(0.95);
}

.impactbot-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 20px;
    height: 20px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    font-size: 0.7rem;
    display: none;
    align-items: center;
    justify-content: center;
}

/* Chat Window */
.impactbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 400px;
    height: 600px;
    background: var(--impactbot-white);
    border-radius: 20px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(75, 46, 22, 0.1);
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.3s ease;
}

.impactbot-window.open {
    display: flex;
    transform: translateY(0);
    opacity: 1;
}

.impactbot-window.minimized {
    height: 60px;
}

/* Header */
.impactbot-header {
    background: linear-gradient(135deg, var(--impactbot-primary), var(--impactbot-dark));
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.impactbot-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.impactbot-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.impactbot-title h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.impactbot-status {
    font-size: 0.75rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 5px;
}

.impactbot-status.online i {
    color: #2ecc71;
}

.impactbot-actions {
    display: flex;
    gap: 8px;
}

.impactbot-action-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.impactbot-action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Body */
.impactbot-body {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
}

.impactbot-welcome {
    padding: 20px;
    background: white;
    border-bottom: 1px solid rgba(75, 46, 22, 0.1);
}

.welcome-content {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.welcome-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--impactbot-accent), var(--impactbot-success));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.welcome-text {
    flex: 1;
}

.welcome-text strong {
    color: var(--impactbot-primary);
    font-size: 1.1rem;
    display: block;
    margin-bottom: 10px;
}

.welcome-text p {
    color: var(--impactbot-primary);
    margin: 8px 0;
    font-size: 0.9rem;
}

.welcome-text ul {
    margin: 10px 0;
    padding-left: 20px;
    color: var(--impactbot-primary);
}

.welcome-text li {
    margin: 5px 0;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.welcome-text li i {
    color: var(--impactbot-secondary);
    width: 16px;
}

.quick-questions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-top: 15px;
}

.quick-question {
    background: var(--impactbot-light);
    border: 1px solid rgba(75, 46, 22, 0.1);
    border-radius: 10px;
    padding: 10px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    color: var(--impactbot-primary);
    text-align: left;
}

.quick-question:hover {
    background: var(--impactbot-accent);
    color: white;
    transform: translateY(-2px);
}

.quick-question i {
    font-size: 0.9rem;
}

/* Messages Container */
.impactbot-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message {
    display: flex;
    gap: 10px;
    max-width: 85%;
    animation: messageIn 0.3s ease;
}

@keyframes messageIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 5px;
}

.message.user .message-avatar {
    background: var(--impactbot-primary);
    color: white;
}

.message.bot .message-avatar {
    background: var(--impactbot-accent);
    color: white;
}

.message-content {
    background: white;
    padding: 12px 16px;
    border-radius: 18px;
    border: 1px solid rgba(75, 46, 22, 0.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    line-height: 1.5;
}

.message.user .message-content {
    background: var(--impactbot-primary);
    color: white;
    border: none;
    border-bottom-right-radius: 5px;
}

.message.bot .message-content {
    border-bottom-left-radius: 5px;
}

.message-time {
    font-size: 0.7rem;
    color: #666;
    margin-top: 5px;
    padding: 0 5px;
    text-align: right;
}

.message.user .message-time {
    text-align: left;
}

/* Typing Indicator */
.impactbot-typing {
    display: none;
    align-items: center;
    gap: 10px;
    padding: 0 20px 20px;
}

.impactbot-typing.visible {
    display: flex;
}

.typing-indicator {
    display: flex;
    gap: 4px;
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    background: var(--impactbot-accent);
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.4;
    }
    30% {
        transform: translateY(-10px);
        opacity: 1;
    }
}

.impactbot-typing span {
    color: var(--impactbot-primary);
    font-size: 0.9rem;
}

/* Footer */
.impactbot-footer {
    border-top: 1px solid rgba(75, 46, 22, 0.1);
    background: white;
    padding: 15px;
    flex-shrink: 0;
}

.impactbot-attachments {
    margin-bottom: 10px;
}

.attachment-btn {
    background: none;
    border: none;
    color: var(--impactbot-primary);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
    transition: background 0.2s;
}

.attachment-btn:hover {
    background: var(--impactbot-light);
}

.impactbot-input-container {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

#impactbot-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid rgba(75, 46, 22, 0.1);
    border-radius: 12px;
    font-size: 0.95rem;
    resize: none;
    max-height: 120px;
    min-height: 44px;
    line-height: 1.5;
    transition: border 0.2s;
    font-family: inherit;
}

#impactbot-input:focus {
    outline: none;
    border-color: var(--impactbot-accent);
}

.impactbot-send-btn {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--impactbot-primary), var(--impactbot-accent));
    color: white;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.impactbot-send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(75, 46, 22, 0.2);
}

.impactbot-send-btn:active {
    transform: scale(0.95);
}

.impactbot-footer-note {
    text-align: center;
    color: #666;
    font-size: 0.75rem;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid rgba(75, 46, 22, 0.05);
}

.impactbot-footer-note i {
    margin-right: 5px;
}

/* Responsive */
@media (max-width: 768px) {
    #impactbot-container {
        bottom: 20px;
        right: 20px;
    }
    
    .impactbot-window {
        width: calc(100vw - 40px);
        height: 70vh;
        right: 10px;
        bottom: 70px;
    }
    
    .quick-questions {
        grid-template-columns: 1fr;
    }
}

/* Minimized State */
.impactbot-window.minimized .impactbot-body,
.impactbot-window.minimized .impactbot-footer {
    display: none;
}

.impactbot-window.minimized .impactbot-header {
    border-radius: 10px;
}
</style>

<!-- Chatbot JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const chatbot = {
        toggle: document.getElementById('impactbot-toggle'),
        window: document.getElementById('impactbot-window'),
        close: document.getElementById('impactbot-close'),
        minimize: document.getElementById('impactbot-minimize'),
        clear: document.getElementById('impactbot-clear'),
        input: document.getElementById('impactbot-input'),
        send: document.getElementById('impactbot-send'),
        messages: document.getElementById('impactbot-messages'),
        typing: document.getElementById('impactbot-typing'),
        welcome: document.getElementById('impactbot-welcome'),
        notification: document.getElementById('impactbot-notification')
        
    };
    
    // State
    let isOpen = false;
    let isMinimized = false;
    let chatHistory = [];
    let unreadCount = 0;
    const API_URL = 'index.php?action=chatbot';
    
    // Auto-resize textarea
    chatbot.input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Open/close chatbot
    chatbot.toggle.addEventListener('click', () => {
        if (!isOpen) {
            openChatbot();
        } else {
            closeChatbot();
        }
    });
    
    chatbot.close.addEventListener('click', closeChatbot);
    
    // Minimize/Restore
    chatbot.minimize.addEventListener('click', () => {
        if (isMinimized) {
            restoreChatbot();
        } else {
            minimizeChatbot();
        }
    });
    
    // Clear chat
    chatbot.clear.addEventListener('click', clearChat);
    
    // Send message
    chatbot.send.addEventListener('click', sendMessage);
    chatbot.input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Quick questions
    document.querySelectorAll('.quick-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const question = btn.getAttribute('data-question');
            chatbot.input.value = question;
            sendMessage();
        });
    });
    
    // Functions
    function openChatbot() {
        chatbot.window.classList.add('open');
        isOpen = true;
        isMinimized = false;
        chatbot.window.classList.remove('minimized');
        resetNotification();
        chatbot.input.focus();
    }
    
    function closeChatbot() {
        chatbot.window.classList.remove('open');
        isOpen = false;
    }
    
    function minimizeChatbot() {
        chatbot.window.classList.add('minimized');
        isMinimized = true;
    }
    
    function restoreChatbot() {
        chatbot.window.classList.remove('minimized');
        isMinimized = false;
        chatbot.input.focus();
    }
    
    function resetNotification() {
        unreadCount = 0;
        chatbot.notification.style.display = 'none';
    }
    
    function showNotification() {
        if (!isOpen) {
            unreadCount++;
            chatbot.notification.textContent = unreadCount > 9 ? '9+' : unreadCount;
            chatbot.notification.style.display = 'flex';
            
            // Animate button
            chatbot.toggle.style.animation = 'pulse 2s infinite';
        }
    }
    
    function clearChat() {
        if (confirm('Clear all messages?')) {
            chatbot.messages.innerHTML = '';
            chatHistory = [];
            chatbot.welcome.style.display = 'block';
            saveChatToStorage();
        }
    }
    
async function sendMessage() {
    // 1. Récupération et validation du message utilisateur
    const message = chatbot.input.value.trim();
    if (message === '') return;

    // 2. Mise à jour de l'interface utilisateur
    addMessage(message, 'user');
    chatHistory.push({ role: 'user', content: message });

    // Nettoyer l'input et afficher l'indicateur de frappe
    chatbot.input.value = '';
    chatbot.input.style.height = 'auto'; // Réinitialiser la hauteur du textarea
    chatbot.typing.classList.add('visible');
    
    // Sauvegarder l'historique après l'envoi de l'utilisateur
    saveChatToStorage(); 

    try {
        // 3. Appel de l'API
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message: message,
                history: chatHistory
            })
        });

        // 4. Vérification du statut HTTP pour les erreurs (Rate Limiting, etc.)
        if (!response.ok) {
            const errorText = await response.text();
            let errorMessage = 'Une erreur de connexion est survenue. Statut : ' + response.status;
            
            try {
                // Tenter de parser l'erreur JSON envoyée par PHP (ex: Rate Limiting)
                const errorData = JSON.parse(errorText);
                if (errorData.error) {
                    errorMessage = errorData.error; 
                }
            } catch (e) {
                // Si ce n'est pas du JSON, l'erreur est probablement une Notice/Warning de PHP.
                console.error("Réponse non-JSON reçue (Problème PHP/Notice):", errorText);
            }
            
            throw new Error(errorMessage);
        }

        // 5. Lecture et vérification de la réponse JSON
        const data = await response.json(); 
        chatbot.typing.classList.remove('visible');

        if (data.error) {
            throw new Error(data.error);
        }
        
        // 6. Afficher la réponse du bot et mettre à jour l'historique
        if (data.response) {
            addMessage(data.response, 'bot');
            chatHistory.push({ role: 'assistant', content: data.response });
            saveChatToStorage(); // Sauvegarder après la réponse du bot
        }

    } catch (error) {
        chatbot.typing.classList.remove('visible');
        // Afficher l'erreur à l'utilisateur
        addMessage('Désolé, je rencontre des difficultés techniques: ' + error.message, 'bot');
        console.error('Chatbot error:', error);
        
        // En cas d'échec de l'API, retirez le dernier message utilisateur de l'historique 
        // pour qu'il ne soit pas inclus dans la prochaine tentative.
        if (chatHistory.length > 0 && chatHistory[chatHistory.length - 1].role === 'user') {
            chatHistory.pop();
            saveChatToStorage();
        }
    }
}
    
    function addMessage(text, sender) {
        // Hide welcome message on first user message
        if (sender === 'user' && chatbot.welcome.style.display !== 'none') {
            chatbot.welcome.style.display = 'none';
        }
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        const avatarIcon = sender === 'user' ? 'fas fa-user' : 'fas fa-robot';
        
        messageDiv.innerHTML = `
            <div class="message-avatar">
                <i class="${avatarIcon}"></i>
            </div>
            <div class="message-content-wrapper">
                <div class="message-content">${escapeHtml(text)}</div>
                <div class="message-time">${time}</div>
            </div>
        `;
        
        chatbot.messages.appendChild(messageDiv);
        chatbot.messages.scrollTop = chatbot.messages.scrollHeight;
        
        // Show notification for bot messages if window is closed
        if (sender === 'bot' && !isOpen) {
            showNotification();
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function saveChatToStorage() {
        try {
            const chatData = {
                history: chatHistory,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem('impactbot_chat', JSON.stringify(chatData));
        } catch (e) {
            console.warn('Could not save chat to localStorage');
        }
    }
    
    function loadChatFromStorage() {
        try {
            const saved = localStorage.getItem('impactbot_chat');
            if (saved) {
                const data = JSON.parse(saved);
                // Optional: Clear if older than 24 hours
                const savedTime = new Date(data.timestamp);
                const hoursDiff = (new Date() - savedTime) / (1000 * 60 * 60);
                
                if (hoursDiff < 24) {
                    chatHistory = data.history || [];
                    // Replay messages
                    chatHistory.forEach((msg, index) => {
                        if (index < chatHistory.length - 1) {
                            const sender = msg.role === 'user' ? 'user' : 'bot';
                            addMessage(msg.content, sender);
                        }
                    });
                }
            }
        } catch (e) {
            console.warn('Could not load chat from localStorage');
        }
    }
    
    // Initialize
    loadChatFromStorage();
    
    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
    
    // Global function to open chatbot with question
    window.openImpactBot = function(question) {
        openChatbot();
        if (question) {
            setTimeout(() => {
                chatbot.input.value = question;
                sendMessage();
            }, 300);
        }
    };
});
</script>