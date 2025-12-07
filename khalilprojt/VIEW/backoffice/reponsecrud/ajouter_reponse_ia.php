<?php
/**
 * Interface Avancée d'Ajout de Réponse avec IA
 * ImpactAble - Version 2.0
 */

require_once(__DIR__ . '/../../../controller/ReponseController.php');
require_once(__DIR__ . '/../../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../../MODEL/reponce.php');
require_once(__DIR__ . '/../../../SERVICE/ReponseIntelligente.php');

$reclamationId = isset($_GET['reclamation_id']) ? intval($_GET['reclamation_id']) : 0;

if ($reclamationId <= 0) {
    header('Location: ../admin_dashboard.php');
    exit();
}

$reclamationController = new ReclamationController();
$reclamation = $reclamationController->showReclamationById($reclamationId);

if (!$reclamation) {
    header('Location: ../admin_dashboard.php');
    exit();
}

// Pré-analyser la réclamation
$analyseSentiment = ReponseIntelligente::analyserSentiment(
    ($reclamation['sujet'] ?? '') . ' ' . ($reclamation['description'] ?? '')
);
$reponseGeneree = ReponseIntelligente::genererReponse($reclamation);

$error = '';
$success = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 1;
    
    if (empty($message) || strlen($message) < 10) {
        $error = 'Le message doit contenir au moins 10 caractères';
    } else {
        try {
            $reponse = new Reponse(
                null,
                $reclamationId,
                $userId,
                $message,
                new DateTime(),
                new DateTime()
            );
            
            $reponseController = new ReponseController();
            $result = $reponseController->addReponse($reponse);
            
            if ($result) {
                $success = 'Réponse ajoutée avec succès !';
                header('refresh:2;url=liste_reponses.php?reclamation_id=' . $reclamationId);
            }
        } catch (Exception $e) {
            $error = 'Erreur: ' . $e->getMessage();
        }
    }
}

// Labels sentiment avec palette ImpactAble
$sentimentLabels = [
    'colere' => ['label' => 'Colère', 'emoji' => '😠', 'couleur' => '#D32F2F', 'bg' => '#FFEBEE'],
    'frustration' => ['label' => 'Frustration', 'emoji' => '😤', 'couleur' => '#B47B47', 'bg' => '#FFF4F5'],
    'urgence' => ['label' => 'Urgence', 'emoji' => '⚡', 'couleur' => '#E65100', 'bg' => '#FFF3E0'],
    'detresse' => ['label' => 'Détresse', 'emoji' => '😰', 'couleur' => '#4B2E16', 'bg' => '#F4ECDD'],
    'neutre' => ['label' => 'Neutre', 'emoji' => '😐', 'couleur' => '#5E6D3B', 'bg' => '#F4ECDD'],
    'positif' => ['label' => 'Positif', 'emoji' => '😊', 'couleur' => '#5E6D3B', 'bg' => '#E8F5E9']
];
$sentimentInfo = $sentimentLabels[$analyseSentiment['type']] ?? $sentimentLabels['neutre'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🤖 Réponse IA - Réclamation #<?= $reclamationId ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            padding: 2rem;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--white);
            padding: 1.5rem 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 3px solid var(--sand);
        }
        
        .header h1 {
            color: var(--brown);
            font-size: 1.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .header h1 i {
            color: var(--moss);
        }
        
        .header h1 .ai-badge {
            background: linear-gradient(135deg, var(--moss), var(--brown));
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--sage);
            color: white;
            border: none;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: var(--moss);
            transform: translateY(-2px);
        }
        
        /* Main Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
        }
        
        @media (max-width: 1200px) {
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
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .card-header i {
            font-size: 1.25rem;
        }
        
        .card-header h2 {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Réclamation Info */
        .reclamation-card {
            margin-bottom: 1.5rem;
        }
        
        .reclamation-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .meta-item {
            background: var(--pink);
            padding: 1rem;
            border-radius: var(--radius);
            text-align: center;
        }
        
        .meta-item label {
            display: block;
            font-size: 0.75rem;
            color: var(--moss);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        
        .meta-item span {
            font-weight: 600;
            color: var(--brown);
        }
        
        .meta-item .priority-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .priority-urgente { background: #F8D7DA; color: #721C24; }
        .priority-moyenne { background: #FFE082; color: #856404; }
        .priority-faible { background: #D4EDDA; color: #155724; }
        
        .reclamation-content {
            background: var(--pink);
            padding: 1.25rem;
            border-radius: var(--radius);
            border-left: 4px solid var(--moss);
        }
        
        .reclamation-content h4 {
            color: var(--brown);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .reclamation-content p {
            color: var(--moss);
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        /* Sentiment Badge */
        .sentiment-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .sentiment-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--sand);
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .sentiment-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .sentiment-keywords {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .keyword-tag {
            background: var(--sand);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-family: monospace;
            color: var(--brown);
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
        
        .form-group .required {
            color: var(--danger);
        }
        
        textarea {
            width: 100%;
            min-height: 300px;
            padding: 1rem;
            border: 2px solid var(--sage);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 0.95rem;
            line-height: 1.6;
            resize: vertical;
            transition: all 0.3s;
        }
        
        textarea:focus {
            outline: none;
            border-color: var(--moss);
            box-shadow: 0 0 0 4px rgba(94, 109, 59, 0.1);
        }
        
        /* Quality Score */
        .quality-panel {
            background: var(--pink);
            border-radius: var(--radius);
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .quality-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .quality-header h4 {
            font-size: 0.9rem;
            color: var(--moss);
        }
        
        .quality-score {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .quality-bar {
            height: 8px;
            background: var(--sand);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .quality-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease, background 0.3s;
        }
        
        .quality-criteria {
            display: grid;
            gap: 0.5rem;
        }
        
        .criteria-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--brown);
        }
        
        .criteria-item i {
            width: 16px;
            text-align: center;
        }
        
        .criteria-ok i { color: var(--moss); }
        .criteria-warning i { color: var(--copper); }
        .criteria-error i { color: var(--danger); }
        
        /* AI Actions */
        .ai-actions {
            display: grid;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-ai {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-ai-primary {
            background: linear-gradient(135deg, var(--moss), var(--brown));
            color: white;
        }
        
        .btn-ai-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(94, 109, 59, 0.4);
        }
        
        .btn-ai-secondary {
            background: var(--pink);
            color: var(--brown);
            border: 2px solid var(--sage);
        }
        
        .btn-ai-secondary:hover {
            border-color: var(--moss);
            background: var(--sand);
        }
        
        /* Quick Responses */
        .quick-responses {
            display: grid;
            gap: 0.5rem;
        }
        
        .quick-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: var(--pink);
            border: 2px solid var(--sand);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
        }
        
        .quick-btn:hover {
            background: var(--sand);
            border-color: var(--sage);
        }
        
        .quick-btn i {
            color: var(--moss);
        }
        
        .quick-btn span {
            font-size: 0.85rem;
            color: var(--brown);
        }
        
        /* Templates */
        .templates-grid {
            display: grid;
            gap: 0.75rem;
        }
        
        .template-card {
            background: var(--pink);
            padding: 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }
        
        .template-card:hover {
            border-color: var(--sage);
            background: var(--sand);
        }
        
        .template-card h5 {
            font-size: 0.9rem;
            color: var(--brown);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .template-card p {
            font-size: 0.8rem;
            color: var(--moss);
        }
        
        /* Solutions */
        .solutions-list {
            display: grid;
            gap: 0.5rem;
        }
        
        .solution-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--pink);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .solution-item:hover {
            background: var(--sand);
        }
        
        .solution-item i {
            color: var(--moss);
        }
        
        .solution-item span {
            font-size: 0.85rem;
            color: var(--brown);
        }
        
        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--sand);
        }
        
        .btn-submit {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--moss), var(--brown));
            color: white;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(94, 109, 59, 0.4);
        }
        
        .btn-cancel {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background: #B71C1C;
            transform: translateY(-2px);
        }
        
        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background: #E8F5E9;
            color: #2E7D32;
            border: 2px solid #4CAF50;
        }
        
        .alert-error {
            background: #FFEBEE;
            color: #C62828;
            border: 2px solid #F44336;
        }
        
        /* Char counter */
        .char-counter {
            text-align: right;
            font-size: 0.8rem;
            color: var(--moss);
            margin-top: 0.5rem;
        }
        
        /* Sidebar sticky */
        .sidebar {
            position: sticky;
            top: 2rem;
            height: fit-content;
        }
        
        .sidebar .card {
            margin-bottom: 1.5rem;
        }
        
        /* Animation */
        .fade-in {
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .reclamation-meta {
                grid-template-columns: 1fr;
            }
            
            .sentiment-section {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-robot"></i>
                Réponse Intelligente
                <span class="ai-badge">IA v2.0</span>
            </h1>
            <a href="liste_reponses.php?reclamation_id=<?= $reclamationId ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error fade-in">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success fade-in">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
                <span style="margin-left: auto;">Redirection...</span>
            </div>
        <?php else: ?>

        <div class="main-grid">
            <!-- Main Content -->
            <div class="main-content">
                <!-- Réclamation Info -->
                <div class="card reclamation-card">
                    <div class="card-header">
                        <i class="fas fa-file-alt"></i>
                        <h2>Réclamation #<?= htmlspecialchars($reclamationId) ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="reclamation-meta">
                            <div class="meta-item">
                                <label>Catégorie</label>
                                <span><?= htmlspecialchars($reclamation['categorie'] ?? 'Non définie') ?></span>
                            </div>
                            <div class="meta-item">
                                <label>Priorité</label>
                                <span class="priority-badge priority-<?= strtolower($reclamation['priorite'] ?? 'faible') ?>">
                                    <?= htmlspecialchars($reclamation['priorite'] ?? 'Non définie') ?>
                                </span>
                            </div>
                            <div class="meta-item">
                                <label>Statut</label>
                                <span><?= htmlspecialchars($reclamation['statut'] ?? 'En attente') ?></span>
                            </div>
                        </div>
                        
                        <div class="reclamation-content">
                            <h4><?= htmlspecialchars($reclamation['sujet'] ?? 'Sans sujet') ?></h4>
                            <p><?= nl2br(htmlspecialchars($reclamation['description'] ?? 'Pas de description')) ?></p>
                        </div>
                        
                        <div class="sentiment-section">
                            <div class="sentiment-info">
                                <span>Sentiment détecté:</span>
                                <span class="sentiment-badge" style="background: <?= $sentimentInfo['bg'] ?>; color: <?= $sentimentInfo['couleur'] ?>;">
                                    <?= $sentimentInfo['emoji'] ?> <?= $sentimentInfo['label'] ?>
                                    (<?= $analyseSentiment['intensite'] ?>)
                                </span>
                            </div>
                            <?php if (!empty($analyseSentiment['mots_detectes'])): ?>
                            <div class="sentiment-keywords">
                                <?php foreach (array_slice($analyseSentiment['mots_detectes'], 0, 5) as $mot): ?>
                                    <span class="keyword-tag"><?= htmlspecialchars($mot) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-edit"></i>
                        <h2>Rédiger la Réponse</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="reponseForm">
                            <input type="hidden" name="userId" value="1">
                            
                            <!-- AI Actions -->
                            <div class="ai-actions">
                                <button type="button" class="btn-ai btn-ai-primary" onclick="genererReponseIA()">
                                    <i class="fas fa-magic"></i>
                                    Générer une réponse avec l'IA
                                </button>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <button type="button" class="btn-ai btn-ai-secondary" onclick="optimiserReponse()">
                                        <i class="fas fa-wand-magic-sparkles"></i>
                                        Optimiser
                                    </button>
                                    <button type="button" class="btn-ai btn-ai-secondary" onclick="verifierQualite()">
                                        <i class="fas fa-chart-line"></i>
                                        Vérifier qualité
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message">
                                    Message de la réponse <span class="required">*</span>
                                </label>
                                <textarea 
                                    id="message" 
                                    name="message" 
                                    placeholder="Rédigez votre réponse ici ou utilisez l'IA pour générer automatiquement..."
                                    oninput="updateQualityScore()"
                                ><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                                <div class="char-counter">
                                    <span id="charCount">0</span> / 2000 caractères
                                </div>
                            </div>

                            <!-- Quality Score Panel -->
                            <div class="quality-panel" id="qualityPanel">
                                <div class="quality-header">
                                    <h4><i class="fas fa-star"></i> Score de Qualité</h4>
                                    <span class="quality-score" id="qualityScore" style="color: var(--moss);">--</span>
                                </div>
                                <div class="quality-bar">
                                    <div class="quality-bar-fill" id="qualityBarFill" style="width: 0%; background: var(--sage);"></div>
                                </div>
                                <div class="quality-criteria" id="qualityCriteria">
                                    <div class="criteria-item">
                                        <i class="fas fa-circle" style="color: var(--sage);"></i>
                                        <span>Commencez à écrire pour voir l'analyse...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="liste_reponses.php?reclamation_id=<?= $reclamationId ?>" class="btn-cancel">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-paper-plane"></i> Envoyer la réponse
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Quick Responses -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        <h2>Réponses Rapides</h2>
                    </div>
                    <div class="card-body">
                        <div class="quick-responses">
                            <button class="quick-btn" onclick="insertQuickResponse('accuse_reception')">
                                <i class="fas fa-inbox"></i>
                                <span>Accusé de réception</span>
                            </button>
                            <button class="quick-btn" onclick="insertQuickResponse('demande_info')">
                                <i class="fas fa-question-circle"></i>
                                <span>Demande d'infos</span>
                            </button>
                            <button class="quick-btn" onclick="insertQuickResponse('en_cours')">
                                <i class="fas fa-hourglass-half"></i>
                                <span>En cours de traitement</span>
                            </button>
                            <button class="quick-btn" onclick="insertQuickResponse('resolution')">
                                <i class="fas fa-check-circle"></i>
                                <span>Résolution</span>
                            </button>
                            <button class="quick-btn" onclick="insertQuickResponse('escalade')">
                                <i class="fas fa-arrow-up"></i>
                                <span>Escalade</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Solutions Suggérées -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-lightbulb"></i>
                        <h2>Solutions Suggérées</h2>
                    </div>
                    <div class="card-body">
                        <div class="solutions-list">
                            <?php foreach ($reponseGeneree['solutions_disponibles'] as $solution): ?>
                            <div class="solution-item" onclick="insertSolution('<?= htmlspecialchars(addslashes($solution)) ?>')">
                                <i class="fas fa-plus-circle"></i>
                                <span><?= htmlspecialchars($solution) ?></span>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($reponseGeneree['solutions_disponibles'])): ?>
                            <p style="color: var(--moss); font-size: 0.9rem;">Aucune solution spécifique pour cette catégorie.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Templates -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-code"></i>
                        <h2>Modèles</h2>
                    </div>
                    <div class="card-body">
                        <div class="templates-grid">
                            <div class="template-card" onclick="loadTemplate('empathique')">
                                <h5><i class="fas fa-heart"></i> Empathique</h5>
                                <p>Ton compréhensif et bienveillant</p>
                            </div>
                            <div class="template-card" onclick="loadTemplate('professionnel')">
                                <h5><i class="fas fa-briefcase"></i> Professionnel</h5>
                                <p>Ton formel et structuré</p>
                            </div>
                            <div class="template-card" onclick="loadTemplate('technique')">
                                <h5><i class="fas fa-cogs"></i> Technique</h5>
                                <p>Détails techniques et solutions</p>
                            </div>
                            <div class="template-card" onclick="loadTemplate('urgent')">
                                <h5><i class="fas fa-exclamation-triangle"></i> Urgent</h5>
                                <p>Réponse prioritaire rapide</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <script>
        // Données PHP vers JS
        const reclamationId = <?= $reclamationId ?>;
        const reclamationData = <?= json_encode($reclamation) ?>;
        const reponseGeneree = <?= json_encode($reponseGeneree) ?>;
        
        // Éléments DOM
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        
        // Compteur de caractères
        if (messageTextarea && charCount) {
            messageTextarea.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length;
                
                if (length > 2000) {
                    charCount.style.color = '#D32F2F';
                } else if (length > 1500) {
                    charCount.style.color = '#B47B47';
                } else {
                    charCount.style.color = '#5E6D3B';
                }
            });
            charCount.textContent = messageTextarea.value.length;
        }

        // Générer réponse IA
        async function genererReponseIA() {
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
            btn.disabled = true;
            
            try {
                const response = await fetch('../../frontoffice/api_reponse_intelligente.php?action=generate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ reclamation_id: reclamationId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageTextarea.value = data.data.texte;
                    messageTextarea.dispatchEvent(new Event('input'));
                    updateQualityScore();
                } else {
                    alert('Erreur: ' + (data.error || 'Échec de la génération'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                messageTextarea.value = reponseGeneree.texte;
                messageTextarea.dispatchEvent(new Event('input'));
                updateQualityScore();
            } finally {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        }

        // Optimiser la réponse
        async function optimiserReponse() {
            const texte = messageTextarea.value.trim();
            if (!texte) {
                alert('Veuillez d\'abord écrire ou générer une réponse.');
                return;
            }
            
            let optimise = texte;
            
            if (!texte.toLowerCase().includes('bonjour') && !texte.toLowerCase().includes('madame') && !texte.toLowerCase().includes('monsieur')) {
                optimise = 'Bonjour,\n\n' + optimise;
            }
            
            if (!texte.toLowerCase().includes('cordialement') && !texte.toLowerCase().includes('sincèrement')) {
                optimise = optimise + '\n\nCordialement,\nL\'équipe ImpactAble';
            }
            
            messageTextarea.value = optimise;
            messageTextarea.dispatchEvent(new Event('input'));
            updateQualityScore();
        }

        // Vérifier qualité
        async function verifierQualite() {
            const texte = messageTextarea.value.trim();
            if (!texte) {
                alert('Veuillez d\'abord écrire une réponse.');
                return;
            }
            
            try {
                const response = await fetch('../../frontoffice/api_reponse_intelligente.php?action=quality_score', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ texte: texte, reclamation: reclamationData })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    displayQualityScore(data.data);
                }
            } catch (error) {
                console.error('Erreur:', error);
                updateQualityScore();
            }
        }

        // Afficher le score de qualité
        function displayQualityScore(scoreData) {
            const scoreEl = document.getElementById('qualityScore');
            const barEl = document.getElementById('qualityBarFill');
            const criteriaEl = document.getElementById('qualityCriteria');
            
            // Couleurs ImpactAble
            let couleur = '#A9B97D'; // sage
            if (scoreData.score >= 80) couleur = '#5E6D3B'; // moss
            else if (scoreData.score >= 60) couleur = '#A9B97D'; // sage
            else if (scoreData.score >= 40) couleur = '#B47B47'; // copper
            else couleur = '#D32F2F'; // danger
            
            scoreEl.textContent = scoreData.score + '/100';
            scoreEl.style.color = couleur;
            
            barEl.style.width = scoreData.score + '%';
            barEl.style.background = couleur;
            
            let criteriaHTML = '';
            for (const [key, value] of Object.entries(scoreData.criteres)) {
                let iconClass = 'fa-check-circle';
                let statusClass = 'criteria-ok';
                if (value.status === 'warning') {
                    iconClass = 'fa-exclamation-circle';
                    statusClass = 'criteria-warning';
                }
                if (value.status === 'error') {
                    iconClass = 'fa-times-circle';
                    statusClass = 'criteria-error';
                }
                
                criteriaHTML += `
                    <div class="criteria-item ${statusClass}">
                        <i class="fas ${iconClass}"></i>
                        <span>${value.message}</span>
                    </div>
                `;
            }
            criteriaEl.innerHTML = criteriaHTML;
        }

        // Mise à jour score en temps réel
        let qualityTimeout;
        function updateQualityScore() {
            clearTimeout(qualityTimeout);
            qualityTimeout = setTimeout(async () => {
                const texte = messageTextarea.value.trim();
                if (texte.length < 20) return;
                
                try {
                    const response = await fetch('../../frontoffice/api_reponse_intelligente.php?action=quality_score', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ texte: texte })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        displayQualityScore(data.data);
                    }
                } catch (error) {}
            }, 500);
        }

        // Insérer réponse rapide
        async function insertQuickResponse(type) {
            const quickResponses = {
                'accuse_reception': `Nous accusons réception de votre réclamation n°${reclamationId}.\n\nVotre dossier est en cours de traitement.\nDélai estimé : 48 heures\n\nCordialement,\nL'équipe ImpactAble`,
                'demande_info': `Bonjour,\n\nPour traiter votre réclamation n°${reclamationId}, nous avons besoin d'informations complémentaires :\n• [Précisez les informations nécessaires]\n\nMerci de nous les communiquer dans les meilleurs délais.\n\nCordialement,\nL'équipe ImpactAble`,
                'en_cours': `Bonjour,\n\nVotre réclamation n°${reclamationId} est actuellement en cours de traitement par notre équipe.\n\nNous vous tiendrons informé de l'avancement.\n\nCordialement,\nL'équipe ImpactAble`,
                'resolution': `Bonjour,\n\nNous avons le plaisir de vous informer que votre réclamation n°${reclamationId} a été résolue.\n\n[Détails de la résolution]\n\nN'hésitez pas à nous contacter si vous avez des questions.\n\nCordialement,\nL'équipe ImpactAble`,
                'escalade': `Bonjour,\n\nVotre dossier n°${reclamationId} a été transmis à un responsable pour un traitement prioritaire.\n\nVous serez recontacté sous 24h.\n\nCordialement,\nL'équipe ImpactAble`
            };
            
            messageTextarea.value = quickResponses[type] || '';
            messageTextarea.dispatchEvent(new Event('input'));
            updateQualityScore();
            messageTextarea.focus();
        }

        // Insérer une solution
        function insertSolution(solution) {
            const currentValue = messageTextarea.value;
            const cursorPos = messageTextarea.selectionStart;
            const textToInsert = '\n• ' + solution;
            
            messageTextarea.value = currentValue.slice(0, cursorPos) + textToInsert + currentValue.slice(cursorPos);
            messageTextarea.dispatchEvent(new Event('input'));
            messageTextarea.focus();
            messageTextarea.setSelectionRange(cursorPos + textToInsert.length, cursorPos + textToInsert.length);
        }

        // Charger un template
        function loadTemplate(type) {
            const templates = {
                'empathique': `Bonjour,

Nous avons bien reçu votre message et nous comprenons parfaitement votre préoccupation.

Votre situation nous touche et nous tenons à vous assurer que nous mettons tout en œuvre pour trouver une solution adaptée à vos besoins.

Notre équipe est mobilisée et vous tiendra informé(e) de l'avancement de votre dossier.

N'hésitez pas à nous recontacter si vous avez besoin de quoi que ce soit.

Avec toute notre considération,
L'équipe ImpactAble`,

                'professionnel': `Madame, Monsieur,

Nous accusons réception de votre réclamation référencée sous le numéro ${reclamationId}.

Votre demande a été transmise au service compétent pour analyse et traitement.

Conformément à nos procédures, vous recevrez une réponse dans un délai de 5 jours ouvrés.

Nous restons à votre disposition pour toute information complémentaire.

Veuillez agréer, Madame, Monsieur, l'expression de nos salutations distinguées.

L'équipe ImpactAble`,

                'technique': `Bonjour,

Suite à votre signalement, notre équipe technique a analysé le problème.

DIAGNOSTIC :
[Description du problème identifié]

SOLUTION :
[Détail de la solution mise en place]

ACTIONS EFFECTUÉES :
• [Action 1]
• [Action 2]
• [Action 3]

Si le problème persiste, n'hésitez pas à nous recontacter avec les détails suivants :
- Date et heure du problème
- Message d'erreur exact
- Captures d'écran si possible

Cordialement,
L'équipe technique ImpactAble`,

                'urgent': `RÉPONSE PRIORITAIRE

Bonjour,

Votre réclamation a été classée comme URGENTE et est traitée en priorité absolue.

Notre équipe est actuellement mobilisée pour résoudre votre situation dans les plus brefs délais.

Un responsable vous recontactera dans les 24 heures maximum.

En cas d'urgence, vous pouvez nous joindre directement à : support@impactable.tn

Cordialement,
L'équipe ImpactAble`
            };
            
            messageTextarea.value = templates[type] || '';
            messageTextarea.dispatchEvent(new Event('input'));
            updateQualityScore();
            messageTextarea.focus();
        }

        // Validation formulaire
        document.getElementById('reponseForm')?.addEventListener('submit', function(e) {
            const message = messageTextarea.value.trim();
            
            if (message.length < 10) {
                e.preventDefault();
                alert('Le message doit contenir au moins 10 caractères.');
                messageTextarea.focus();
                return false;
            }
            
            if (message.length > 2000) {
                e.preventDefault();
                alert('Le message ne peut pas dépasser 2000 caractères.');
                messageTextarea.focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>
