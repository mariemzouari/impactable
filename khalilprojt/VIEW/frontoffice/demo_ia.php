<?php
require_once(__DIR__ . '/../../SERVICE/PrioriteIntelligente.php');

$resultatAnalyse = null;
$texteAnalyse = '';
$categorieAnalyse = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['texte'])) {
    $texteAnalyse = trim($_POST['texte']);
    $categorieAnalyse = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';
    
    if (!empty($texteAnalyse)) {
        $resultatAnalyse = PrioriteIntelligente::analyser($texteAnalyse, $categorieAnalyse);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Démo IA - Priorisation Intelligente | ImpactAble</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    
    <style>
        /* Logo Brand */
        .logo-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 25px;
        }
        .logo-icon-box {
            background: #1a1a1a;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
        }
        .logo-text-box {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .logo-name {
            font-size: 1.8em;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1;
        }
        .logo-slogan {
            font-size: 0.75em;
            color: #555;
            font-style: italic;
            margin-top: 2px;
        }
        .demo-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .ia-hero {
            background: linear-gradient(135deg, #5E6D3B 0%, #4B2E16 100%);
            border-radius: 25px;
            padding: 50px;
            color: white;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 15px 40px rgba(75, 46, 22, 0.25);
        }
        
        .ia-hero h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .ia-hero p {
            font-size: 1.2em;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }
        
        .feature-card {
            background: rgba(255,255,255,0.15);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .feature-card i {
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        
        .feature-card h4 {
            margin-bottom: 10px;
        }
        
        .feature-card p {
            font-size: 0.95em;
            opacity: 0.85;
        }
        
        .demo-section {
            background: white;
            border: 3px solid #F4ECDD;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(75, 46, 22, 0.15);
        }
        
        .demo-section h3 {
            color: #4B2E16;
            margin-bottom: 25px;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .demo-form .form-group {
            margin-bottom: 25px;
        }
        
        .demo-form label {
            display: block;
            margin-bottom: 10px;
            color: #4B2E16;
            font-weight: 600;
        }
        
        .demo-form textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #A9B97D;
            border-radius: 12px;
            font-size: 1em;
            min-height: 150px;
            resize: vertical;
            font-family: 'Inter', sans-serif;
        }
        
        .demo-form textarea:focus {
            outline: none;
            border-color: #5E6D3B;
            box-shadow: 0 0 0 4px rgba(94, 109, 59, 0.1);
        }
        
        .demo-form select {
            width: 100%;
            padding: 15px;
            border: 2px solid #A9B97D;
            border-radius: 12px;
            font-size: 1em;
            font-family: 'Inter', sans-serif;
        }
        
        .demo-form button {
            background: linear-gradient(135deg, #5E6D3B, #4B2E16);
            color: white;
            border: none;
            padding: 18px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 auto;
        }
        
        .demo-form button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(94, 109, 59, 0.4);
        }
        
        /* Résultat de l'analyse */
        .result-section {
            margin-top: 40px;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .result-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .priority-result {
            padding: 20px 40px;
            border-radius: 50px;
            font-size: 1.5em;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .priority-result.urgente {
            background: linear-gradient(135deg, #FFEBEE, #FFCDD2);
            color: #C62828;
            border: 3px solid #EF5350;
        }
        
        .priority-result.moyenne {
            background: linear-gradient(135deg, #FFF3E0, #FFE0B2);
            color: #E65100;
            border: 3px solid #FF9800;
        }
        
        .priority-result.faible {
            background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
            color: #2E7D32;
            border: 3px solid #4CAF50;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: #FFF4F5;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            border-left: 4px solid #5E6D3B;
        }
        
        .stat-box .value {
            font-size: 2.5em;
            font-weight: 700;
            color: #4B2E16;
        }
        
        .stat-box .label {
            color: #5E6D3B;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .keywords-section {
            background: #F4ECDD;
            border-radius: 15px;
            padding: 25px;
        }
        
        .keywords-section h4 {
            color: #4B2E16;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .keywords-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .keyword-tag {
            padding: 10px 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .keyword-tag.urgent {
            background: #FFEBEE;
            color: #C62828;
        }
        
        .keyword-tag.important {
            background: #FFF3E0;
            color: #E65100;
        }
        
        .keyword-tag.normal {
            background: #E8F5E9;
            color: #2E7D32;
        }
        
        .interpretation-box {
            background: linear-gradient(135deg, #5E6D3B, #4B2E16);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 25px;
        }
        
        .interpretation-box h4 {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .interpretation-box p {
            line-height: 1.7;
            opacity: 0.95;
        }
        
        .examples-section {
            margin-top: 50px;
        }
        
        .examples-section h3 {
            color: #4B2E16;
            margin-bottom: 25px;
        }
        
        .examples-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .example-card {
            background: white;
            border: 2px solid #F4ECDD;
            border-radius: 15px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .example-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(75, 46, 22, 0.15);
            border-color: #A9B97D;
        }
        
        .example-card h5 {
            color: #4B2E16;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .example-card p {
            color: #5E6D3B;
            font-size: 0.95em;
            line-height: 1.6;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #5E6D3B;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            color: #4B2E16;
            transform: translateX(-5px);
        }
    </style>
</head>
<body>
    <div class="container demo-container">
        <header>
            <div class="logo-brand">
                <div class="logo-icon-box"><i class="fas fa-universal-access"></i></div>
                <div class="logo-text-box">
                    <span class="logo-name">ImpactAble</span>
                    <span class="logo-slogan">Where Ability Meets Impact</span>
                </div>
            </div>
        </header>

        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>

        <!-- Hero Section -->
        <div class="ia-hero">
            <h2><i class="fas fa-brain"></i> Priorisation Intelligente</h2>
            <p>Notre système d'Intelligence Artificielle analyse automatiquement le contenu de vos réclamations pour déterminer leur niveau de priorité. Testez-le ici !</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-search"></i>
                    <h4>Analyse Sémantique</h4>
                    <p>Détection de plus de 100 mots-clés liés à l'urgence et l'importance</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h4>Scoring Intelligent</h4>
                    <p>Attribution de points selon la gravité des termes détectés</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-percent"></i>
                    <h4>Niveau de Confiance</h4>
                    <p>Indication de la fiabilité de l'analyse automatique</p>
                </div>
            </div>
        </div>

        <!-- Section de démo -->
        <div class="demo-section">
            <h3><i class="fas fa-flask"></i> Testez l'analyse automatique</h3>
            
            <form method="POST" class="demo-form">
                <div class="form-group">
                    <label for="texte">Entrez le texte de votre réclamation :</label>
                    <textarea name="texte" id="texte" placeholder="Exemple: Je suis bloqué dans l'ascenseur de l'immeuble depuis 30 minutes. C'est urgent, j'ai besoin d'aide immédiate !"><?= htmlspecialchars($texteAnalyse) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="categorie">Catégorie (optionnel) :</label>
                    <select name="categorie" id="categorie">
                        <option value="">-- Sélectionner --</option>
                        <option value="Accessibilité" <?= $categorieAnalyse === 'Accessibilité' ? 'selected' : '' ?>>Accessibilité</option>
                        <option value="Discrimination" <?= $categorieAnalyse === 'Discrimination' ? 'selected' : '' ?>>Discrimination</option>
                        <option value="Sécurité" <?= $categorieAnalyse === 'Sécurité' ? 'selected' : '' ?>>Sécurité</option>
                        <option value="Santé" <?= $categorieAnalyse === 'Santé' ? 'selected' : '' ?>>Santé</option>
                        <option value="Technique" <?= $categorieAnalyse === 'Technique' ? 'selected' : '' ?>>Technique</option>
                        <option value="Service" <?= $categorieAnalyse === 'Service' ? 'selected' : '' ?>>Service</option>
                        <option value="Transport" <?= $categorieAnalyse === 'Transport' ? 'selected' : '' ?>>Transport</option>
                        <option value="Autre" <?= $categorieAnalyse === 'Autre' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>
                
                <button type="submit">
                    <i class="fas fa-magic"></i> Analyser avec l'IA
                </button>
            </form>

            <?php if ($resultatAnalyse): ?>
                <div class="result-section">
                    <div class="result-header">
                        <div class="priority-result <?= strtolower($resultatAnalyse['priorite']) ?>">
                            <?= PrioriteIntelligente::getPrioriteIcon($resultatAnalyse['priorite']) ?>
                            Priorité: <?= htmlspecialchars($resultatAnalyse['priorite']) ?>
                        </div>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-box">
                            <div class="value"><?= $resultatAnalyse['score'] ?></div>
                            <div class="label">Score d'urgence</div>
                        </div>
                        <div class="stat-box">
                            <div class="value"><?= $resultatAnalyse['confiance'] ?>%</div>
                            <div class="label">Confiance</div>
                        </div>
                        <div class="stat-box">
                            <div class="value"><?= count($resultatAnalyse['motsDetectes']) ?></div>
                            <div class="label">Mots détectés</div>
                        </div>
                    </div>
                    
                    <?php if (!empty($resultatAnalyse['motsDetectes'])): ?>
                        <div class="keywords-section">
                            <h4><i class="fas fa-tags"></i> Mots-clés détectés</h4>
                            <div class="keywords-list">
                                <?php foreach ($resultatAnalyse['motsDetectes'] as $mot): ?>
                                    <span class="keyword-tag <?= $mot['type'] ?>">
                                        <?php if ($mot['type'] === 'urgent'): ?>
                                            🔴
                                        <?php elseif ($mot['type'] === 'important'): ?>
                                            🟠
                                        <?php else: ?>
                                            🟢
                                        <?php endif; ?>
                                        "<?= htmlspecialchars($mot['mot']) ?>"
                                        <small>(<?= $mot['points'] > 0 ? '+' : '' ?><?= $mot['points'] ?> pts)</small>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="interpretation-box">
                        <h4><i class="fas fa-lightbulb"></i> Interprétation</h4>
                        <p>
                            <?php
                            $priorite = $resultatAnalyse['priorite'];
                            $confiance = $resultatAnalyse['confiance'];
                            
                            if ($priorite === 'Urgente') {
                                echo "⚠️ <strong>Attention !</strong> Cette réclamation nécessite une intervention IMMÉDIATE. Le système a détecté des indicateurs d'urgence élevés.";
                            } elseif ($priorite === 'Moyenne') {
                                echo "📋 Cette réclamation est d'importance modérée. Elle sera traitée dans les délais standards avec une attention particulière.";
                            } else {
                                echo "✅ Cette réclamation est de priorité basse. Elle sera traitée selon la file d'attente normale.";
                            }
                            
                            if ($confiance >= 80) {
                                echo " <em>(Analyse très fiable - Confiance: {$confiance}%)</em>";
                            } elseif ($confiance >= 50) {
                                echo " <em>(Analyse fiable - Confiance: {$confiance}%)</em>";
                            } else {
                                echo " <em>(Vérification manuelle recommandée - Confiance: {$confiance}%)</em>";
                            }
                            ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Exemples -->
        <div class="examples-section">
            <h3><i class="fas fa-lightbulb"></i> Exemples à tester</h3>
            <div class="examples-grid">
                <div class="example-card" onclick="fillExample('urgent')">
                    <h5>🔴 Cas Urgent</h5>
                    <p>"Je suis bloqué dans l'ascenseur depuis 20 minutes ! C'est une urgence, j'ai besoin d'aide immédiatement !"</p>
                </div>
                <div class="example-card" onclick="fillExample('moyen')">
                    <h5>🟠 Cas Important</h5>
                    <p>"J'ai un problème avec l'accessibilité de votre bâtiment. La rampe d'accès est en mauvais état et je n'arrive pas à passer avec mon fauteuil roulant."</p>
                </div>
                <div class="example-card" onclick="fillExample('faible')">
                    <h5>🟢 Cas Normal</h5>
                    <p>"Je voudrais suggérer une amélioration pour le site web. Ce serait bien d'avoir une option pour agrandir le texte. Merci d'avance."</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const exemples = {
            urgent: "Je suis bloqué dans l'ascenseur depuis 20 minutes ! C'est une urgence, j'ai besoin d'aide immédiatement ! Le téléphone d'urgence ne fonctionne pas !",
            moyen: "J'ai un problème avec l'accessibilité de votre bâtiment. La rampe d'accès est en mauvais état et je n'arrive pas à passer avec mon fauteuil roulant. Ce problème est récurrent depuis plusieurs semaines.",
            faible: "Je voudrais suggérer une amélioration pour le site web. Ce serait bien d'avoir une option pour agrandir le texte. Merci d'avance pour votre considération."
        };
        
        function fillExample(type) {
            document.getElementById('texte').value = exemples[type];
            document.getElementById('texte').focus();
            
            // Scroll vers le formulaire
            document.querySelector('.demo-form').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>

