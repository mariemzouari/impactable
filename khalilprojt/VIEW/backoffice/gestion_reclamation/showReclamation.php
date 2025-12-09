<?php
require_once(__DIR__ . '/../../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../../CONFIGRRATION/config.php');
require_once(__DIR__ . '/../../../SERVICE/EmotionDetector.php');

// Rediriger vers le dashboard si aucun ID n'est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../admin_dashboard.php?error=no_id');
    exit;
}

$controller = new ReclamationController();
$reclamation = null;
$emotionAnalysis = null;

$reclamation = $controller->showReclamationById($_GET['id']);

// Récupérer les informations de l'utilisateur
$user = null;
if ($reclamation) {
    // Analyser l'émotion du texte
    $texteComplet = ($reclamation['sujet'] ?? '') . ' ' . ($reclamation['description'] ?? '');
    $emotionAnalysis = EmotionDetector::analyser($texteComplet);
    
    try {
        $db = config::getConnexion();
        // Vérifier si la table utilisateur existe
        $checkTable = $db->query("SHOW TABLES LIKE 'utilisateur'");
        if ($checkTable->rowCount() > 0) {
            $userQuery = $db->prepare("SELECT * FROM utilisateur WHERE Id_utilisateur = :id");
            $userQuery->execute(['id' => $reclamation['utilisateurId']]);
            $user = $userQuery->fetch();
        }
    } catch (Exception $e) {
        // Table utilisateur n'existe pas, continuer sans
        $user = null;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Réclamation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>
<body class="without-sidebar">
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> Détails de la Réclamation</h1>
            <a href="../admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if ($reclamation): ?>
            <div class="info-section">
                <h3><i class="fas fa-info-circle"></i> Informations Générales</h3>
                <p><strong>ID:</strong> #<?= htmlspecialchars($reclamation['id']) ?></p>
                <p><strong>Date de création:</strong> <?= date('d/m/Y à H:i', strtotime($reclamation['dateCreation'])) ?></p>
                <p><strong>Dernière modification:</strong> <?= date('d/m/Y à H:i', strtotime($reclamation['derniereModification'])) ?></p>
                <p><strong>Statut:</strong> 
                    <span class="badge status-<?= htmlspecialchars($reclamation['statut']) ?>">
                        <?= htmlspecialchars($reclamation['statut']) ?>
                    </span>
                </p>
                <p><strong>Priorité:</strong> 
                    <span class="badge priority-<?= htmlspecialchars($reclamation['priorite']) ?>">
                        <?= htmlspecialchars($reclamation['priorite']) ?>
                    </span>
                </p>
            </div>

            <?php if ($user): ?>
            <div class="info-section">
                <h3><i class="fas fa-user"></i> Informations Utilisateur</h3>
                <p><strong>Nom complet:</strong> <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Téléphone:</strong> <?= htmlspecialchars($user['numero_tel'] ?? 'Non renseigné') ?></p>
                <p><strong>ID Utilisateur:</strong> <?= htmlspecialchars($reclamation['utilisateurId']) ?></p>
            </div>
            <?php endif; ?>

            <div class="info-section">
                <h3><i class="fas fa-file-alt"></i> Détails de la Réclamation</h3>
                <p><strong>Sujet:</strong> <?= htmlspecialchars($reclamation['sujet']) ?></p>
                <p><strong>Catégorie:</strong> <?= htmlspecialchars($reclamation['categorie']) ?></p>
                <p><strong>Description:</strong></p>
                <div style="background: white; padding: 15px; border-radius: 8px; margin-top: 10px; color: #4B2E16;">
                    <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
                </div>
            </div>

            <!-- 🧠 SECTION ANALYSE ÉMOTIONNELLE -->
            <?php if ($emotionAnalysis): ?>
            <div class="info-section" style="background: <?= $emotionAnalysis['couleur_bg'] ?>; border-left: 5px solid <?= $emotionAnalysis['couleur'] ?>;">
                <h3 style="color: <?= $emotionAnalysis['couleur'] ?>;">
                    <i class="fas fa-brain"></i> Analyse Émotionnelle IA
                </h3>
                
                <div style="display: flex; align-items: center; gap: 20px; margin: 15px 0; flex-wrap: wrap;">
                    <div style="font-size: 3em;"><?= $emotionAnalysis['emoji'] ?></div>
                    <div>
                        <div style="font-size: 1.5em; font-weight: 700; color: <?= $emotionAnalysis['couleur'] ?>;">
                            <?= $emotionAnalysis['label'] ?>
                        </div>
                        <div style="color: #5E6D3B;">Intensité : <?= $emotionAnalysis['intensite'] ?> (Score: <?= $emotionAnalysis['score'] ?>)</div>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <div style="background: rgba(255,255,255,0.5); border-radius: 10px; height: 15px; overflow: hidden;">
                            <div style="height: 100%; width: <?= $emotionAnalysis['intensite_pourcent'] ?>%; background: <?= $emotionAnalysis['couleur'] ?>; border-radius: 10px;"></div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($emotionAnalysis['mots_detectes'])): ?>
                <p><strong>Mots-clés détectés :</strong></p>
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin: 10px 0;">
                    <?php foreach ($emotionAnalysis['mots_detectes'] as $mot): ?>
                        <span style="background: white; color: <?= $emotionAnalysis['couleur'] ?>; padding: 5px 12px; border-radius: 20px; font-size: 0.9em; font-weight: 500;">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($mot) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div style="background: white; padding: 15px; border-radius: 10px; margin-top: 15px;">
                    <p style="margin: 0 0 10px 0;"><strong><?= $emotionAnalysis['conseil_agent']['icon'] ?> <?= $emotionAnalysis['conseil_agent']['titre'] ?></strong></p>
                    <p style="margin: 5px 0; color: #5E6D3B;"><i class="fas fa-lightbulb"></i> <?= $emotionAnalysis['conseil_agent']['conseil'] ?></p>
                    <?php if ($emotionAnalysis['conseil_agent']['a_eviter'] !== '-'): ?>
                        <p style="margin: 5px 0; color: #B47B47; font-size: 0.9em;"><i class="fas fa-exclamation-triangle"></i> À éviter : <?= $emotionAnalysis['conseil_agent']['a_eviter'] ?></p>
                    <?php endif; ?>
                </div>

                <div style="background: linear-gradient(135deg, #5E6D3B, #4B2E16); color: white; padding: 15px; border-radius: 10px; margin-top: 15px;">
                    <p style="margin: 0 0 8px 0; font-weight: 600;"><i class="fas fa-robot"></i> Suggestion de réponse :</p>
                    <p style="margin: 0; font-style: italic; opacity: 0.95;">"<?= $emotionAnalysis['reponse_auto'] ?>"</p>
                </div>

                <p style="margin-top: 15px;">
                    <strong>Priorité suggérée par l'IA :</strong>
                    <span class="badge priority-<?= $emotionAnalysis['priorite_suggeree'] ?>" style="margin-left: 10px;">
                        <?= $emotionAnalysis['priorite_suggeree'] ?>
                    </span>
                </p>
            </div>
            <?php endif; ?>

            <div class="info-section">
                <h3><i class="fas fa-user-tie"></i> Agent Attribué</h3>
                <p><strong>Agent:</strong> <?= htmlspecialchars($reclamation['agentAttribue'] ?? 'Non attribué') ?></p>
            </div>

            <div class="action-buttons">
                <a href="../reponsecrud/ajouter_reponse.php?reclamation_id=<?= $reclamation['id'] ?>" class="btn btn-success">
                    <i class="fas fa-reply"></i> Répondre
                </a>
                <a href="../reponsecrud/liste_reponses.php?reclamation_id=<?= $reclamation['id'] ?>" class="btn btn-info">
                    <i class="fas fa-comments"></i> Voir les Réponses
                </a>
                <a href="updateReclamation.php?id=<?= $reclamation['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>
        <?php else: ?>
            <div class="error" style="text-align: center; padding: 40px;">
                <i class="fas fa-exclamation-circle" style="font-size: 3em; margin-bottom: 15px; display: block;"></i>
                <h3 style="margin-bottom: 10px;">Réclamation #<?= htmlspecialchars($_GET['id']) ?> non trouvée</h3>
                <p style="margin-bottom: 20px; opacity: 0.8;">Cette réclamation n'existe pas ou a été supprimée.</p>
                <a href="../admin_dashboard.php" class="btn btn-success" style="margin-top: 10px;">
                    <i class="fas fa-home"></i> Retour au Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
