<?php
require_once(__DIR__ . '/../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../controller/ReponseController.php');
require_once(__DIR__ . '/../../SERVICE/PrioriteIntelligente.php');

$reclamationController = new ReclamationController();
$reponseController = new ReponseController();

$reclamation = null;
$reponses = [];
$error = '';

// Recherche par ID
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $reclamation = $reclamationController->showReclamationById($id);
    
    if ($reclamation) {
        $reponses = $reponseController->getReponsesByReclamation($id);
    } else {
        $error = "Aucune réclamation trouvée avec le numéro #{$id}";
    }
}

// Fonction pour obtenir l'étape actuelle
function getEtapeActuelle($statut) {
    switch ($statut) {
        case 'En attente': return 1;
        case 'En cours': return 2;
        case 'Résolue': return 3;
        case 'Fermée': return 4;
        default: return 1;
    }
}

// Fonction pour obtenir la classe de statut
function getStatutClass($statut) {
    switch ($statut) {
        case 'En attente': return 'status-waiting';
        case 'En cours': return 'status-progress';
        case 'Résolue': return 'status-resolved';
        case 'Fermée': return 'status-closed';
        default: return 'status-waiting';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de Réclamation - ImpactAble</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    
    <style>
        /* Logo Brand */
        .logo-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        .logo-icon-box {
            position: relative;
            width: 55px;
            height: 55px;
            background: #1a1a1a;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-icon-box::before {
            content: '';
            position: absolute;
            top: 8px;
            right: 8px;
            width: 18px;
            height: 18px;
            background: #F4ECDD;
            border-radius: 0 8px 0 50%;
        }
        .logo-icon-box i {
            font-size: 1.4em;
            color: white;
            z-index: 1;
        }
        .logo-text-box {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .logo-name {
            font-size: 2.2em;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1;
            letter-spacing: -1px;
        }
        .logo-slogan {
            font-size: 0.85em;
            color: #1a1a1a;
            font-style: italic;
            margin-top: 3px;
        }
        /* Styles spécifiques pour le suivi */
        .tracking-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .search-section {
            background: white;
            border: 3px solid #A9B97D;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
        }
        
        .search-section h3 {
            color: #4B2E16;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .search-form input {
            flex: 1;
            min-width: 200px;
            padding: 14px 18px;
            border: 2px solid #A9B97D;
            border-radius: 12px;
            font-size: 1em;
        }
        
        .search-form input:focus {
            outline: none;
            border-color: #5E6D3B;
            box-shadow: 0 0 0 4px rgba(94, 109, 59, 0.1);
        }
        
        .search-form button {
            background: linear-gradient(135deg, #5E6D3B, #4B2E16);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .search-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(94, 109, 59, 0.4);
        }
        
        /* Timeline de suivi */
        .tracking-timeline {
            background: white;
            border: 3px solid #F4ECDD;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
        }
        
        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .timeline-header h3 {
            color: #4B2E16;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .reclamation-id-badge {
            background: linear-gradient(135deg, #5E6D3B, #4B2E16);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 1.2em;
        }
        
        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 40px 0;
        }
        
        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 35px;
            left: 10%;
            right: 10%;
            height: 4px;
            background: #F4ECDD;
            z-index: 1;
        }
        
        .timeline-step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .step-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #F4ECDD;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.8em;
            transition: all 0.3s;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(75, 46, 22, 0.1);
        }
        
        .step-icon.active {
            background: #FFC107;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .step-icon.completed {
            background: #4CAF50;
            color: white;
        }
        
        .step-icon.waiting {
            background: #F4ECDD;
            color: #9E9E9E;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3); }
            50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(255, 193, 7, 0.5); }
        }
        
        .step-label {
            font-weight: 600;
            color: #4B2E16;
            font-size: 0.95em;
        }
        
        .step-date {
            font-size: 0.85em;
            color: #5E6D3B;
            margin-top: 5px;
        }
        
        /* Détails de la réclamation */
        .reclamation-details {
            background: white;
            border: 3px solid #F4ECDD;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
        }
        
        .reclamation-details h3 {
            color: #4B2E16;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .detail-item {
            background: #FFF4F5;
            padding: 15px;
            border-radius: 12px;
            border-left: 4px solid #A9B97D;
        }
        
        .detail-item label {
            display: block;
            font-weight: 600;
            color: #5E6D3B;
            font-size: 0.85em;
            margin-bottom: 5px;
        }
        
        .detail-item span {
            color: #4B2E16;
            font-size: 1.05em;
        }
        
        .detail-item.full-width {
            grid-column: 1 / -1;
        }
        
        /* Priorité badge dans le suivi */
        .priority-badge-large {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 1em;
        }
        
        .priority-badge-large.urgente {
            background: #FFEBEE;
            color: #C62828;
        }
        
        .priority-badge-large.moyenne {
            background: #FFF3E0;
            color: #E65100;
        }
        
        .priority-badge-large.faible {
            background: #E8F5E9;
            color: #2E7D32;
        }
        
        /* Historique des réponses */
        .responses-history {
            background: white;
            border: 3px solid #F4ECDD;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
        }
        
        .responses-history h3 {
            color: #4B2E16;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .response-timeline {
            position: relative;
            padding-left: 40px;
        }
        
        .response-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #A9B97D, #5E6D3B);
            border-radius: 2px;
        }
        
        .response-item {
            position: relative;
            background: #FFF4F5;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #5E6D3B;
        }
        
        .response-item::before {
            content: '';
            position: absolute;
            left: -33px;
            top: 25px;
            width: 16px;
            height: 16px;
            background: #5E6D3B;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .response-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .response-author {
            font-weight: 600;
            color: #4B2E16;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .response-date {
            color: #5E6D3B;
            font-size: 0.9em;
        }
        
        .response-content {
            color: #4B2E16;
            line-height: 1.7;
        }
        
        /* IA Analysis Box */
        .ia-analysis-box {
            background: linear-gradient(135deg, #5E6D3B, #4B2E16);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .ia-analysis-box h4 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .ia-analysis-box .analysis-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .ia-stat {
            background: rgba(255,255,255,0.15);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        
        .ia-stat .value {
            font-size: 1.5em;
            font-weight: 700;
        }
        
        .ia-stat .label {
            font-size: 0.85em;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .error-message {
            background: #FFEBEE;
            color: #C62828;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            border: 2px solid #F44336;
        }
        
        .no-responses {
            background: #FFF4F5;
            padding: 40px;
            text-align: center;
            border-radius: 15px;
            color: #5E6D3B;
        }
        
        .no-responses i {
            font-size: 3em;
            margin-bottom: 15px;
            opacity: 0.5;
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
        
        @media (max-width: 768px) {
            .timeline-steps {
                flex-direction: column;
                gap: 30px;
            }
            
            .timeline-steps::before {
                top: 35px;
                bottom: 35px;
                left: 35px;
                right: auto;
                width: 4px;
                height: auto;
            }
            
            .timeline-step {
                display: flex;
                align-items: center;
                text-align: left;
                gap: 20px;
            }
            
            .step-icon {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container tracking-container">
        <header>
            <div class="logo-brand">
                <div class="logo-icon-box"><i class="fas fa-compress-arrows-alt"></i></div>
                <div class="logo-text-box">
                    <span class="logo-name">ImpactAble</span>
                    <span class="logo-slogan">Where Ability Meets Impact</span>
                </div>
            </div>
            <h2>Suivi de Réclamation</h2>
            <p class="subtitle">Suivez l'avancement de votre réclamation en temps réel</p>
        </header>

        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>

        <!-- Section de recherche -->
        <div class="search-section">
            <h3><i class="fas fa-search"></i> Rechercher votre réclamation</h3>
            <form class="search-form" method="GET">
                <input type="number" name="id" placeholder="Entrez le numéro de votre réclamation (ex: 1, 2, 3...)" 
                       value="<?= isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '' ?>" min="1" required>
                <button type="submit">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($reclamation): ?>
            <?php 
            $etapeActuelle = getEtapeActuelle($reclamation['statut']);
            $statutClass = getStatutClass($reclamation['statut']);
            ?>

            <!-- Timeline de suivi -->
            <div class="tracking-timeline">
                <div class="timeline-header">
                    <h3><i class="fas fa-route"></i> État de votre réclamation</h3>
                    <div class="reclamation-id-badge">
                        <i class="fas fa-hashtag"></i> <?= htmlspecialchars($reclamation['id']) ?>
                    </div>
                </div>

                <div class="timeline-steps">
                    <!-- Étape 1: En attente -->
                    <div class="timeline-step">
                        <div class="step-icon <?= $etapeActuelle >= 1 ? ($etapeActuelle == 1 ? 'active' : 'completed') : 'waiting' ?>">
                            <?= $etapeActuelle > 1 ? '<i class="fas fa-check"></i>' : '📥' ?>
                        </div>
                        <div class="step-label">Reçue</div>
                        <div class="step-date"><?= date('d/m/Y', strtotime($reclamation['dateCreation'])) ?></div>
                    </div>

                    <!-- Étape 2: En cours -->
                    <div class="timeline-step">
                        <div class="step-icon <?= $etapeActuelle >= 2 ? ($etapeActuelle == 2 ? 'active' : 'completed') : 'waiting' ?>">
                            <?= $etapeActuelle > 2 ? '<i class="fas fa-check"></i>' : '🔄' ?>
                        </div>
                        <div class="step-label">En traitement</div>
                        <div class="step-date"><?= $etapeActuelle >= 2 ? 'En cours' : 'À venir' ?></div>
                    </div>

                    <!-- Étape 3: Résolue -->
                    <div class="timeline-step">
                        <div class="step-icon <?= $etapeActuelle >= 3 ? ($etapeActuelle == 3 ? 'active' : 'completed') : 'waiting' ?>">
                            <?= $etapeActuelle > 3 ? '<i class="fas fa-check"></i>' : '✅' ?>
                        </div>
                        <div class="step-label">Résolue</div>
                        <div class="step-date"><?= $etapeActuelle >= 3 ? date('d/m/Y', strtotime($reclamation['derniereModification'])) : 'À venir' ?></div>
                    </div>

                    <!-- Étape 4: Fermée -->
                    <div class="timeline-step">
                        <div class="step-icon <?= $etapeActuelle >= 4 ? 'completed' : 'waiting' ?>">
                            <?= $etapeActuelle >= 4 ? '<i class="fas fa-check"></i>' : '📁' ?>
                        </div>
                        <div class="step-label">Clôturée</div>
                        <div class="step-date"><?= $etapeActuelle >= 4 ? date('d/m/Y', strtotime($reclamation['derniereModification'])) : 'À venir' ?></div>
                    </div>
                </div>
            </div>

            <!-- Détails de la réclamation -->
            <div class="reclamation-details">
                <h3><i class="fas fa-file-alt"></i> Détails de votre réclamation</h3>
                
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Sujet</label>
                        <span><?= htmlspecialchars($reclamation['sujet']) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <label>Catégorie</label>
                        <span><?= htmlspecialchars($reclamation['categorie']) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <label>Priorité</label>
                        <span class="priority-badge-large <?= strtolower($reclamation['priorite']) ?>">
                            <?= PrioriteIntelligente::getPrioriteIcon($reclamation['priorite']) ?>
                            <?= htmlspecialchars($reclamation['priorite']) ?>
                        </span>
                    </div>
                    
                    <div class="detail-item">
                        <label>Statut actuel</label>
                        <span class="badge badge-status badge-<?= strtolower(str_replace(' ', '-', $reclamation['statut'])) ?>">
                            <?= htmlspecialchars($reclamation['statut']) ?>
                        </span>
                    </div>
                    
                    <div class="detail-item">
                        <label>Date de création</label>
                        <span><?= date('d/m/Y à H:i', strtotime($reclamation['dateCreation'])) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <label>Dernière mise à jour</label>
                        <span><?= date('d/m/Y à H:i', strtotime($reclamation['derniereModification'])) ?></span>
                    </div>
                    
                    <?php if (!empty($reclamation['agentAttribue'])): ?>
                    <div class="detail-item">
                        <label>Agent en charge</label>
                        <span><i class="fas fa-user-tie"></i> <?= htmlspecialchars($reclamation['agentAttribue']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($reclamation['lieu'])): ?>
                    <div class="detail-item">
                        <label>Lieu de l'incident</label>
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($reclamation['lieu']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-item full-width">
                        <label>Description</label>
                        <span><?= nl2br(htmlspecialchars($reclamation['description'])) ?></span>
                    </div>
                </div>

                <!-- Analyse IA -->
                <?php
                $texteAnalyse = $reclamation['sujet'] . ' ' . $reclamation['description'];
                $analyseIA = PrioriteIntelligente::analyser($texteAnalyse, $reclamation['categorie']);
                ?>
                <div class="ia-analysis-box">
                    <h4><i class="fas fa-brain"></i> Analyse Intelligente</h4>
                    <div class="analysis-content">
                        <div class="ia-stat">
                            <div class="value"><?= $analyseIA['priorite'] ?></div>
                            <div class="label">Priorité détectée</div>
                        </div>
                        <div class="ia-stat">
                            <div class="value"><?= $analyseIA['score'] ?></div>
                            <div class="label">Score d'urgence</div>
                        </div>
                        <div class="ia-stat">
                            <div class="value"><?= $analyseIA['confiance'] ?>%</div>
                            <div class="label">Niveau de confiance</div>
                        </div>
                        <div class="ia-stat">
                            <div class="value"><?= count($analyseIA['motsDetectes']) ?></div>
                            <div class="label">Mots-clés détectés</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des réponses -->
            <div class="responses-history">
                <h3><i class="fas fa-comments"></i> Historique des réponses (<?= count($reponses) ?>)</h3>
                
                <?php if (empty($reponses)): ?>
                    <div class="no-responses">
                        <i class="fas fa-inbox"></i>
                        <h4>Aucune réponse pour le moment</h4>
                        <p>Notre équipe travaille sur votre réclamation. Vous serez notifié dès qu'une réponse sera disponible.</p>
                    </div>
                <?php else: ?>
                    <div class="response-timeline">
                        <?php foreach ($reponses as $reponse): ?>
                            <div class="response-item">
                                <div class="response-header">
                                    <div class="response-author">
                                        <i class="fas fa-user-tie"></i>
                                        <?= htmlspecialchars(($reponse['prenom'] ?? 'Agent') . ' ' . ($reponse['nom'] ?? '')) ?>
                                    </div>
                                    <div class="response-date">
                                        <i class="fas fa-clock"></i>
                                        <?= date('d/m/Y à H:i', strtotime($reponse['date_reponse'])) ?>
                                    </div>
                                </div>
                                <div class="response-content">
                                    <?= nl2br(htmlspecialchars($reponse['message'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>

    <!-- Chatbot Widget -->
    <?php include 'chatbot_widget.php'; ?>
</body>
</html>

