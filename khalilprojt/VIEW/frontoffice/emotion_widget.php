<!-- 
🧠 WIDGET DE DÉTECTION D'ÉMOTIONS EN TEMPS RÉEL
ImpactAble - Composant Visuel Animé
Incluez ce fichier dans vos pages pour activer la détection d'émotions
-->

<style>
/* ==================== EMOTION WIDGET STYLES ==================== */
.emotion-detector-widget {
    background: linear-gradient(135deg, #FFFAF5 0%, #FFF4F5 100%);
    border: 3px solid #F4ECDD;
    border-radius: 20px;
    padding: 25px;
    margin: 20px 0;
    transition: all 0.5s ease;
    position: relative;
    overflow: hidden;
}

.emotion-detector-widget::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #5E6D3B, #B47B47, #4B2E16);
    transition: all 0.3s;
}

.emotion-detector-widget.emotion-colere { border-color: #E53935; }
.emotion-detector-widget.emotion-colere::before { background: #E53935; }

.emotion-detector-widget.emotion-frustration { border-color: #FB8C00; }
.emotion-detector-widget.emotion-frustration::before { background: #FB8C00; }

.emotion-detector-widget.emotion-tristesse { border-color: #5C6BC0; }
.emotion-detector-widget.emotion-tristesse::before { background: #5C6BC0; }

.emotion-detector-widget.emotion-anxiete { border-color: #7E57C2; }
.emotion-detector-widget.emotion-anxiete::before { background: #7E57C2; }

.emotion-detector-widget.emotion-detresse { border-color: #8E24AA; }
.emotion-detector-widget.emotion-detresse::before { background: #8E24AA; }

.emotion-detector-widget.emotion-positif { border-color: #43A047; }
.emotion-detector-widget.emotion-positif::before { background: #43A047; }

.emotion-widget-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.emotion-widget-header i {
    color: #5E6D3B;
    font-size: 1.3em;
}

.emotion-widget-header h4 {
    color: #4B2E16;
    font-size: 1.1em;
    font-weight: 600;
    margin: 0;
}

.emotion-widget-header .beta-badge {
    background: linear-gradient(135deg, #5E6D3B, #A9B97D);
    color: white;
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 0.7em;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Main Emotion Display */
.emotion-main-display {
    display: flex;
    align-items: center;
    gap: 25px;
    padding: 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(75, 46, 22, 0.08);
    margin-bottom: 20px;
}

.emotion-face {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5em;
    background: #F4ECDD;
    transition: all 0.5s ease;
    animation: pulse 2s infinite;
    flex-shrink: 0;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.emotion-face.analyzing {
    animation: analyzing 1s infinite;
}

@keyframes analyzing {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.emotion-info {
    flex: 1;
}

.emotion-label {
    font-size: 1.8em;
    font-weight: 700;
    color: #4B2E16;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.emotion-intensity {
    font-size: 0.95em;
    color: #5E6D3B;
    margin-bottom: 12px;
}

/* Intensity Bar */
.intensity-bar-container {
    background: #F4ECDD;
    border-radius: 10px;
    height: 12px;
    overflow: hidden;
    margin-bottom: 10px;
}

.intensity-bar {
    height: 100%;
    border-radius: 10px;
    transition: width 0.5s ease, background 0.3s;
    background: linear-gradient(90deg, #A9B97D, #5E6D3B);
}

.intensity-bar.high {
    background: linear-gradient(90deg, #FB8C00, #E53935);
}

.intensity-bar.medium {
    background: linear-gradient(90deg, #FFB74D, #FB8C00);
}

/* Keywords */
.emotion-keywords {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 15px;
}

.keyword-chip {
    background: #F4ECDD;
    color: #4B2E16;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.keyword-chip i {
    font-size: 0.8em;
    color: #B47B47;
}

/* Priority Suggestion */
.priority-suggestion {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    background: white;
    border-radius: 12px;
    border-left: 4px solid #5E6D3B;
    margin-bottom: 15px;
}

.priority-suggestion i {
    font-size: 1.5em;
    color: #5E6D3B;
}

.priority-suggestion.urgent {
    border-left-color: #E53935;
    background: #FFEBEE;
}

.priority-suggestion.urgent i {
    color: #E53935;
}

.priority-text {
    flex: 1;
}

.priority-text strong {
    color: #4B2E16;
    display: block;
    margin-bottom: 3px;
}

.priority-text span {
    color: #5E6D3B;
    font-size: 0.9em;
}

/* Agent Conseil Box */
.agent-conseil-box {
    background: linear-gradient(135deg, #5E6D3B 0%, #4B2E16 100%);
    color: white;
    padding: 20px;
    border-radius: 15px;
    margin-top: 15px;
}

.agent-conseil-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    font-weight: 600;
}

.agent-conseil-header i {
    font-size: 1.2em;
}

.agent-conseil-content p {
    margin: 8px 0;
    font-size: 0.95em;
    line-height: 1.5;
}

.agent-conseil-content .eviter {
    opacity: 0.8;
    font-size: 0.85em;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,0.2);
}

/* Auto Response Preview */
.auto-response-preview {
    background: #FFF4F5;
    border: 2px dashed #A9B97D;
    border-radius: 12px;
    padding: 15px;
    margin-top: 15px;
}

.auto-response-preview h5 {
    color: #4B2E16;
    font-size: 0.9em;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.auto-response-preview p {
    color: #5E6D3B;
    font-size: 0.95em;
    line-height: 1.6;
    font-style: italic;
}

/* Waiting State */
.emotion-waiting {
    text-align: center;
    padding: 30px;
    color: #5E6D3B;
}

.emotion-waiting i {
    font-size: 2.5em;
    margin-bottom: 15px;
    opacity: 0.5;
    display: block;
}

.emotion-waiting p {
    font-size: 1em;
}

/* All Emotions Display */
.all-emotions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 10px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #F4ECDD;
}

.emotion-mini-card {
    text-align: center;
    padding: 10px;
    border-radius: 10px;
    background: #F4ECDD;
    transition: all 0.3s;
    opacity: 0.5;
}

.emotion-mini-card.active {
    opacity: 1;
    transform: scale(1.05);
}

.emotion-mini-card .mini-emoji {
    font-size: 1.5em;
    display: block;
    margin-bottom: 5px;
}

.emotion-mini-card .mini-label {
    font-size: 0.75em;
    color: #4B2E16;
    font-weight: 600;
}

.emotion-mini-card .mini-score {
    font-size: 0.7em;
    color: #5E6D3B;
}

/* Responsive */
@media (max-width: 600px) {
    .emotion-main-display {
        flex-direction: column;
        text-align: center;
    }
    
    .emotion-label {
        justify-content: center;
    }
}
</style>

<div class="emotion-detector-widget" id="emotionWidget">
    <div class="emotion-widget-header">
        <i class="fas fa-brain"></i>
        <h4>Détection d'Émotion</h4>
        <span class="beta-badge">IA</span>
    </div>
    
    <div id="emotionContent">
        <div class="emotion-waiting">
            <i class="fas fa-comment-dots"></i>
            <p>Commencez à écrire pour voir l'analyse émotionnelle...</p>
        </div>
    </div>
</div>

<script>
// ==================== EMOTION DETECTOR WIDGET ====================

const EmotionWidget = {
    container: null,
    currentEmotion: null,
    debounceTimer: null,
    
    init(containerId = 'emotionWidget', textareaId = 'description') {
        this.container = document.getElementById(containerId);
        this.contentDiv = document.getElementById('emotionContent');
        
        const textarea = document.getElementById(textareaId);
        if (textarea) {
            textarea.addEventListener('input', (e) => this.onTextChange(e.target.value));
        }
    },
    
    onTextChange(text) {
        clearTimeout(this.debounceTimer);
        
        if (text.length < 15) {
            this.showWaiting();
            return;
        }
        
        this.showAnalyzing();
        
        this.debounceTimer = setTimeout(() => {
            this.analyzeText(text);
        }, 500);
    },
    
    async analyzeText(text) {
        try {
            const response = await fetch('api_emotion.php?action=analyze', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ texte: text })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.displayResult(data.data);
            }
        } catch (error) {
            console.error('Erreur analyse émotion:', error);
        }
    },
    
    showWaiting() {
        this.container.className = 'emotion-detector-widget';
        this.contentDiv.innerHTML = `
            <div class="emotion-waiting">
                <i class="fas fa-comment-dots"></i>
                <p>Commencez à écrire pour voir l'analyse émotionnelle...</p>
            </div>
        `;
    },
    
    showAnalyzing() {
        this.contentDiv.innerHTML = `
            <div class="emotion-main-display">
                <div class="emotion-face analyzing">🔍</div>
                <div class="emotion-info">
                    <div class="emotion-label">Analyse en cours...</div>
                    <div class="emotion-intensity">Détection des émotions</div>
                    <div class="intensity-bar-container">
                        <div class="intensity-bar" style="width: 50%; animation: pulse 1s infinite;"></div>
                    </div>
                </div>
            </div>
        `;
    },
    
    displayResult(result) {
        this.currentEmotion = result;
        
        // Update widget class for styling
        this.container.className = 'emotion-detector-widget emotion-' + result.emotion;
        
        // Determine bar class
        let barClass = '';
        if (result.score >= 15) barClass = 'high';
        else if (result.score >= 8) barClass = 'medium';
        
        // Keywords HTML
        let keywordsHTML = '';
        if (result.mots_detectes && result.mots_detectes.length > 0) {
            keywordsHTML = `
                <div class="emotion-keywords">
                    ${result.mots_detectes.map(mot => `
                        <span class="keyword-chip">
                            <i class="fas fa-tag"></i> ${mot}
                        </span>
                    `).join('')}
                </div>
            `;
        }
        
        // Priority suggestion
        const isUrgent = result.priorite_suggeree === 'Urgente';
        const priorityHTML = `
            <div class="priority-suggestion ${isUrgent ? 'urgent' : ''}">
                <i class="fas fa-${isUrgent ? 'exclamation-triangle' : 'flag'}"></i>
                <div class="priority-text">
                    <strong>Priorité suggérée : ${result.priorite_suggeree}</strong>
                    <span>${isUrgent ? 'Ce message nécessite une attention immédiate' : 'Traitement dans les délais normaux'}</span>
                </div>
            </div>
        `;
        
        // Agent conseil
        const conseil = result.conseil_agent;
        const conseilHTML = `
            <div class="agent-conseil-box">
                <div class="agent-conseil-header">
                    <i class="fas fa-user-tie"></i>
                    ${conseil.icon} ${conseil.titre}
                </div>
                <div class="agent-conseil-content">
                    <p><strong>💡 Conseil :</strong> ${conseil.conseil}</p>
                    ${conseil.a_eviter !== '-' ? `<p class="eviter">⚠️ À éviter : ${conseil.a_eviter}</p>` : ''}
                </div>
            </div>
        `;
        
        // Auto response
        const autoResponseHTML = `
            <div class="auto-response-preview">
                <h5><i class="fas fa-robot"></i> Suggestion de réponse automatique</h5>
                <p>"${result.reponse_auto}"</p>
            </div>
        `;
        
        // All emotions mini grid
        let emotionsGridHTML = '<div class="all-emotions-grid">';
        const allEmotions = result.toutes_emotions || {};
        const emotionsList = ['colere', 'frustration', 'tristesse', 'anxiete', 'positif', 'neutre'];
        const emotionEmojis = {
            colere: '😠', frustration: '😤', tristesse: '😢', 
            anxiete: '😰', detresse: '😭', positif: '😊', neutre: '😐'
        };
        const emotionLabels = {
            colere: 'Colère', frustration: 'Frustration', tristesse: 'Tristesse',
            anxiete: 'Anxiété', detresse: 'Détresse', positif: 'Positif', neutre: 'Neutre'
        };
        
        emotionsList.forEach(em => {
            const score = allEmotions[em] || 0;
            const isActive = em === result.emotion;
            emotionsGridHTML += `
                <div class="emotion-mini-card ${isActive ? 'active' : ''}" style="${isActive ? 'background:' + result.couleur_bg : ''}">
                    <span class="mini-emoji">${emotionEmojis[em]}</span>
                    <span class="mini-label">${emotionLabels[em]}</span>
                    <span class="mini-score">${score > 0 ? score + ' pts' : '-'}</span>
                </div>
            `;
        });
        emotionsGridHTML += '</div>';
        
        // Main display
        this.contentDiv.innerHTML = `
            <div class="emotion-main-display">
                <div class="emotion-face" style="background: ${result.couleur_bg}">
                    ${result.emoji}
                </div>
                <div class="emotion-info">
                    <div class="emotion-label">
                        ${result.label}
                        <span style="font-size: 0.5em; background: ${result.couleur}; color: white; padding: 3px 8px; border-radius: 10px;">
                            Score: ${result.score}
                        </span>
                    </div>
                    <div class="emotion-intensity">Intensité : ${result.intensite}</div>
                    <div class="intensity-bar-container">
                        <div class="intensity-bar ${barClass}" style="width: ${result.intensite_pourcent}%; background: ${result.couleur};"></div>
                    </div>
                    ${keywordsHTML}
                </div>
            </div>
            ${priorityHTML}
            ${conseilHTML}
            ${autoResponseHTML}
            ${emotionsGridHTML}
        `;
    },
    
    getResult() {
        return this.currentEmotion;
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Try to initialize with default IDs
    if (document.getElementById('emotionWidget')) {
        EmotionWidget.init('emotionWidget', 'description');
    }
});
</script>

