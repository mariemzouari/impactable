<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🤖 Démo - Système de Réponse Intelligente | ImpactAble</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --brown: #4B2E16;
            --copper: #B47B47;
            --moss: #5E6D3B;
            --sage: #A9B97D;
            --sand: #F4ECDD;
            --white: #FFFAF5;
            --pink: #FFF4F5;
            --danger: #D32F2F;
            --radius: 14px;
            --shadow: 0 8px 20px rgba(75, 46, 22, 0.12);
            --shadow-lg: 0 15px 40px rgba(75, 46, 22, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--sand) 0%, var(--pink) 100%);
            min-height: 100vh;
            color: var(--brown);
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Hero Section */
        .hero {
            text-align: center;
            padding: 3rem 0;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--brown);
        }
        
        .hero h1 i {
            color: var(--moss);
        }
        
        .hero p {
            font-size: 1.1rem;
            color: var(--moss);
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        
        .features-badges {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .feature-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--white);
            border: 2px solid var(--sage);
            border-radius: 30px;
            font-size: 0.9rem;
            color: var(--brown);
            font-weight: 600;
        }
        
        .feature-badge i {
            color: var(--moss);
        }
        
        /* Main Content */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        @media (max-width: 900px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Cards */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 3px solid var(--sand);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--moss), var(--brown));
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .card-header.header-copper {
            background: linear-gradient(135deg, var(--copper), var(--brown));
        }
        
        .card-header i {
            font-size: 1.5rem;
        }
        
        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--brown);
            margin-bottom: 0.5rem;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--sage);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--white);
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--moss);
            box-shadow: 0 0 0 4px rgba(94, 109, 59, 0.1);
        }
        
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .btn-generate {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--moss), var(--brown));
            color: white;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .btn-generate:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(94, 109, 59, 0.4);
        }
        
        .btn-generate:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Results */
        .result-section {
            display: none;
        }
        
        .result-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Sentiment Display */
        .sentiment-display {
            text-align: center;
            padding: 2rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
        }
        
        .sentiment-emoji {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .sentiment-label {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .sentiment-intensity {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .keywords-list {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        
        .keyword-tag {
            background: rgba(0, 0, 0, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-family: monospace;
        }
        
        /* Quality Score */
        .quality-display {
            text-align: center;
            padding: 1.5rem;
        }
        
        .quality-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: var(--white);
            box-shadow: var(--shadow);
            border: 8px solid;
        }
        
        .quality-circle .score {
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .quality-circle .label {
            font-size: 0.9rem;
            color: var(--moss);
        }
        
        .criteria-list {
            text-align: left;
            margin-top: 1.5rem;
        }
        
        .criteria-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--pink);
            border-radius: var(--radius);
            margin-bottom: 0.5rem;
        }
        
        .criteria-item i {
            font-size: 1.1rem;
        }
        
        .criteria-ok i { color: var(--moss); }
        .criteria-warning i { color: var(--copper); }
        .criteria-error i { color: var(--danger); }
        
        /* Response Preview */
        .response-preview {
            background: var(--pink);
            padding: 1.5rem;
            border-radius: var(--radius);
            border-left: 4px solid var(--moss);
            margin-bottom: 1.5rem;
            white-space: pre-wrap;
            font-size: 0.95rem;
            line-height: 1.7;
            max-height: 400px;
            overflow-y: auto;
            color: var(--brown);
        }
        
        /* Copy Button */
        .btn-copy {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--brown);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-copy:hover {
            background: var(--copper);
            transform: scale(1.02);
        }
        
        .btn-copy.copied {
            background: var(--moss);
        }
        
        /* Loading */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(75, 46, 22, 0.9);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .loader {
            width: 60px;
            height: 60px;
            border: 4px solid var(--sand);
            border-top-color: var(--sage);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Back Link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--brown);
            text-decoration: none;
            font-size: 0.95rem;
            margin-bottom: 1rem;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: var(--moss);
        }
        
        /* Info Panel */
        .info-panel {
            background: var(--white);
            border: 3px solid var(--sage);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .info-panel h3 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: var(--brown);
        }
        
        .info-panel h3 i {
            color: var(--moss);
        }
        
        .info-panel ul {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 0.75rem;
        }
        
        .info-panel li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--brown);
        }
        
        .info-panel li i {
            color: var(--moss);
        }
        
        /* Placeholder */
        .placeholder-box {
            text-align: center;
            padding: 2rem;
            color: var(--moss);
        }
        
        .placeholder-box i {
            font-size: 2rem;
            margin-bottom: 1rem;
            display: block;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader"></div>
        <p>Génération en cours...</p>
    </div>

    <div class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>
        
        <!-- Hero -->
        <div class="hero">
            <h1><i class="fas fa-robot"></i> Réponse Intelligente</h1>
            <p>Testez notre système d'IA qui génère automatiquement des réponses personnalisées aux réclamations</p>
            <div class="features-badges">
                <span class="feature-badge"><i class="fas fa-brain"></i> Analyse de sentiment</span>
                <span class="feature-badge"><i class="fas fa-magic"></i> Génération automatique</span>
                <span class="feature-badge"><i class="fas fa-chart-line"></i> Score de qualité</span>
                <span class="feature-badge"><i class="fas fa-layer-group"></i> Templates adaptatifs</span>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="main-grid">
            <!-- Input Form -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-edit"></i>
                    <h2>Simuler une Réclamation</h2>
                </div>
                <div class="card-body">
                    <form id="demoForm">
                        <div class="form-group">
                            <label for="sujet">Sujet de la réclamation</label>
                            <input type="text" id="sujet" placeholder="Ex: Problème d'accessibilité à l'entrée">
                        </div>
                        
                        <div class="form-group">
                            <label for="categorie">Catégorie</label>
                            <select id="categorie">
                                <option value="accessibilite">♿ Accessibilité</option>
                                <option value="discrimination">⚖️ Discrimination</option>
                                <option value="technique">🔧 Technique</option>
                                <option value="facturation">💰 Facturation</option>
                                <option value="transport">🚌 Transport</option>
                                <option value="sante">🏥 Santé</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="priorite">Priorité</label>
                            <select id="priorite">
                                <option value="Faible">🟢 Faible</option>
                                <option value="Moyenne" selected>🟠 Moyenne</option>
                                <option value="Urgente">🔴 Urgente</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description détaillée</label>
                            <textarea id="description" placeholder="Décrivez le problème en détail... Plus vous donnez d'informations, plus l'IA sera précise."></textarea>
                        </div>
                        
                        <button type="button" class="btn-generate" onclick="genererReponse()">
                            <i class="fas fa-magic"></i>
                            Générer la Réponse
                        </button>
                    </form>
                </div>
            </div>

            <!-- Results -->
            <div>
                <!-- Sentiment Analysis -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-header header-copper">
                        <i class="fas fa-heart-pulse"></i>
                        <h2>Analyse de Sentiment</h2>
                    </div>
                    <div class="card-body">
                        <div class="result-section" id="sentimentResult">
                            <div class="sentiment-display" id="sentimentDisplay">
                                <div class="sentiment-emoji" id="sentimentEmoji">😐</div>
                                <div class="sentiment-label" id="sentimentLabel">En attente d'analyse</div>
                                <div class="sentiment-intensity" id="sentimentIntensity">Entrez une description</div>
                                <div class="keywords-list" id="keywordsList"></div>
                            </div>
                        </div>
                        <div id="sentimentPlaceholder" class="placeholder-box">
                            <i class="fas fa-search"></i>
                            <p>L'analyse de sentiment apparaîtra ici</p>
                        </div>
                    </div>
                </div>

                <!-- Quality Score -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-star"></i>
                        <h2>Score de Qualité</h2>
                    </div>
                    <div class="card-body">
                        <div class="result-section" id="qualityResult">
                            <div class="quality-display">
                                <div class="quality-circle" id="qualityCircle" style="border-color: var(--sage);">
                                    <span class="score" id="qualityScoreValue">--</span>
                                    <span class="label">sur 100</span>
                                </div>
                                <div class="criteria-list" id="criteriaList"></div>
                            </div>
                        </div>
                        <div id="qualityPlaceholder" class="placeholder-box">
                            <i class="fas fa-chart-pie"></i>
                            <p>Le score de qualité apparaîtra ici</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generated Response -->
        <div class="card result-section" id="responseCard" style="margin-top: 2rem;">
            <div class="card-header">
                <i class="fas fa-robot"></i>
                <h2>Réponse Générée par l'IA</h2>
            </div>
            <div class="card-body">
                <div class="response-preview" id="responsePreview"></div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button class="btn-copy" onclick="copyResponse()">
                        <i class="fas fa-copy"></i>
                        Copier la réponse
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="info-panel">
            <h3><i class="fas fa-info-circle"></i> Fonctionnalités du Système</h3>
            <ul>
                <li><i class="fas fa-check"></i> Génération automatique par catégorie</li>
                <li><i class="fas fa-check"></i> Détection du sentiment (colère, frustration, urgence...)</li>
                <li><i class="fas fa-check"></i> Adaptation du ton selon l'émotion détectée</li>
                <li><i class="fas fa-check"></i> Score de qualité en temps réel</li>
                <li><i class="fas fa-check"></i> Suggestions de solutions contextuelles</li>
                <li><i class="fas fa-check"></i> Modèles personnalisables par secteur</li>
                <li><i class="fas fa-check"></i> Réponses rapides prédéfinies</li>
                <li><i class="fas fa-check"></i> Estimation du délai de traitement</li>
            </ul>
        </div>
    </div>

    <script>
        const sentimentStyles = {
            colere: { emoji: '😠', color: '#D32F2F', bg: '#FFEBEE', label: 'Colère' },
            frustration: { emoji: '😤', color: '#B47B47', bg: '#FFF4F5', label: 'Frustration' },
            urgence: { emoji: '⚡', color: '#E65100', bg: '#FFF3E0', label: 'Urgence' },
            detresse: { emoji: '😰', color: '#4B2E16', bg: '#F4ECDD', label: 'Détresse' },
            neutre: { emoji: '😐', color: '#5E6D3B', bg: '#F4ECDD', label: 'Neutre' },
            positif: { emoji: '😊', color: '#5E6D3B', bg: '#E8F5E9', label: 'Positif' }
        };

        async function genererReponse() {
            const sujet = document.getElementById('sujet').value.trim();
            const categorie = document.getElementById('categorie').value;
            const priorite = document.getElementById('priorite').value;
            const description = document.getElementById('description').value.trim();

            if (!sujet || !description) {
                alert('Veuillez remplir le sujet et la description.');
                return;
            }

            document.getElementById('loadingOverlay').classList.add('active');

            try {
                const response = await fetch('api_reponse_intelligente.php?action=generate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        reclamation: {
                            sujet: sujet,
                            categorie: categorie,
                            priorite: priorite,
                            description: description
                        }
                    })
                });

                const data = await response.json();

                if (data.success) {
                    displaySentiment(data.data.sentiment);
                    displayQuality(data.data.score_qualite);
                    displayResponse(data.data.texte);
                } else {
                    alert('Erreur: ' + (data.error || 'Échec de la génération'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de connexion à l\'API');
            } finally {
                document.getElementById('loadingOverlay').classList.remove('active');
            }
        }

        function displaySentiment(sentiment) {
            const style = sentimentStyles[sentiment.type] || sentimentStyles.neutre;
            
            document.getElementById('sentimentPlaceholder').style.display = 'none';
            document.getElementById('sentimentResult').classList.add('active');
            
            const display = document.getElementById('sentimentDisplay');
            display.style.background = style.bg;
            display.style.color = style.color;
            
            document.getElementById('sentimentEmoji').textContent = style.emoji;
            document.getElementById('sentimentLabel').textContent = style.label;
            document.getElementById('sentimentIntensity').textContent = `Intensité: ${sentiment.intensite} (score: ${sentiment.score})`;
            
            const keywordsList = document.getElementById('keywordsList');
            keywordsList.innerHTML = '';
            if (sentiment.mots_detectes && sentiment.mots_detectes.length > 0) {
                sentiment.mots_detectes.forEach(mot => {
                    const tag = document.createElement('span');
                    tag.className = 'keyword-tag';
                    tag.textContent = mot;
                    keywordsList.appendChild(tag);
                });
            }
        }

        function displayQuality(quality) {
            document.getElementById('qualityPlaceholder').style.display = 'none';
            document.getElementById('qualityResult').classList.add('active');
            
            // Couleurs ImpactAble
            let couleur = '#A9B97D';
            if (quality.score >= 80) couleur = '#5E6D3B';
            else if (quality.score >= 60) couleur = '#A9B97D';
            else if (quality.score >= 40) couleur = '#B47B47';
            else couleur = '#D32F2F';
            
            const circle = document.getElementById('qualityCircle');
            circle.style.borderColor = couleur;
            
            document.getElementById('qualityScoreValue').textContent = quality.score;
            document.getElementById('qualityScoreValue').style.color = couleur;
            
            const criteriaList = document.getElementById('criteriaList');
            criteriaList.innerHTML = '';
            
            for (const [key, value] of Object.entries(quality.criteres)) {
                let iconClass = 'fa-check-circle';
                let statusClass = 'criteria-ok';
                
                if (value.status === 'warning') {
                    iconClass = 'fa-exclamation-circle';
                    statusClass = 'criteria-warning';
                } else if (value.status === 'error') {
                    iconClass = 'fa-times-circle';
                    statusClass = 'criteria-error';
                }
                
                const item = document.createElement('div');
                item.className = `criteria-item ${statusClass}`;
                item.innerHTML = `
                    <i class="fas ${iconClass}"></i>
                    <span>${value.message}</span>
                `;
                criteriaList.appendChild(item);
            }
        }

        function displayResponse(texte) {
            document.getElementById('responseCard').classList.add('active');
            document.getElementById('responsePreview').textContent = texte;
        }

        function copyResponse() {
            const text = document.getElementById('responsePreview').textContent;
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.querySelector('.btn-copy');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copié !';
                btn.classList.add('copied');
                
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('copied');
                }, 2000);
            });
        }

        // Live sentiment analysis while typing
        let analysisTimeout;
        document.getElementById('description').addEventListener('input', function() {
            clearTimeout(analysisTimeout);
            analysisTimeout = setTimeout(async () => {
                const texte = this.value.trim();
                if (texte.length < 20) return;
                
                try {
                    const response = await fetch('api_reponse_intelligente.php?action=analyze_sentiment', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ texte: texte })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        displaySentiment(data.data);
                    }
                } catch (error) {}
            }, 500);
        });
    </script>
</body>
</html>
