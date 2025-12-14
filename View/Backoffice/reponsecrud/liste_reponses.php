<?php
require_once(__DIR__ . '/../../../controller/ReponseController.php');
require_once(__DIR__ . '/../../../controller/ReclamationController.php');

$reclamationId = isset($_GET['reclamation_id']) ? intval($_GET['reclamation_id']) : 0;

if ($reclamationId <= 0) {
    header('Location: ../../reclamation_back.php');
    exit();
}

$reclamationController = new ReclamationController();
$reclamation = $reclamationController->showReclamationById($reclamationId);

if (!$reclamation) {
    header('Location: ../../reclamation_back.php');
    exit();
}

$reponseController = new ReponseController();
$reponses = $reponseController->getReponsesByReclamation($reclamationId);
$totalReponses = count($reponses);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponses - Réclamation #<?= $reclamationId ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>


<body class="without-sidebar">
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-comments"></i> Réponses - Réclamation #<?= $reclamationId ?></h1>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="../reclamation_back.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Réponse supprimée avec succès !
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Réponse modifiée avec succès !
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-value"><?= $totalReponses ?></div>
                <div class="stat-label">Réponses Totales</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">
                    <span class="badge status-<?= htmlspecialchars($reclamation['statut']) ?>">
                        <?= htmlspecialchars($reclamation['statut']) ?>
                    </span>
                </div>
                <div class="stat-label">Statut Actuel</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">
                    <span class="badge priority-<?= htmlspecialchars($reclamation['priorite']) ?>">
                        <?= htmlspecialchars($reclamation['priorite']) ?>
                    </span>
                </div>
                <div class="stat-label">Priorité</div>
            </div>
        </div>

        <div class="reclamation-summary">
            <h2><i class="fas fa-file-alt"></i> Résumé de la Réclamation</h2>
            <div class="summary-grid">
                <div class="summary-item">
                    <strong>Sujet:</strong>
                    <span><?= htmlspecialchars($reclamation['sujet']) ?></span>
                </div>
                <div class="summary-item">
                    <strong>Catégorie:</strong>
                    <span><?= htmlspecialchars($reclamation['categorie']) ?></span>
                </div>
                <div class="summary-item">
                    <strong>Date de création:</strong>
                    <span><?= date('d/m/Y à H:i', strtotime($reclamation['dateCreation'])) ?></span>
                </div>
                <div class="summary-item">
                    <strong>Agent attribué:</strong>
                    <span><?= htmlspecialchars($reclamation['agentAttribue'] ?? 'Non attribué') ?></span>
                </div>
            </div>
        </div>

        <div class="reponses-container">
            <h2><i class="fas fa-list"></i> Liste des Réponses (<?= $totalReponses ?>)</h2>

            <?php if (empty($reponses)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucune réponse pour le moment</h3>
                    <p>Soyez le premier à répondre à cette réclamation !</p>
                    <div style="display: flex; gap: 10px; margin-top: 20px; justify-content: center; flex-wrap: wrap;">
                        <!-- Buttons removed as per request to move them to global list -->
                        <span style="color: #666; font-style: italic;">Utilisez la liste globale pour répondre.</span>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($reponses as $index => $rep): ?>
                    <div class="reponse-card">
                        <div class="reponse-header">
                            <div class="reponse-author">
                                <i class="fas fa-user-tie"></i>
                                <?= htmlspecialchars($rep['prenom'] ?? 'Admin') ?>         <?= htmlspecialchars($rep['nom'] ?? '') ?>
                                <span style="font-size: 0.8em; color: #5E6D38; font-weight: normal;">
                                    (Réponse #<?= $totalReponses - $index ?>)
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div class="reponse-date">
                                    <i class="fas fa-clock"></i>
                                    <?= date('d/m/Y à H:i', strtotime($rep['date_reponse'])) ?>
                                </div>
                                <div class="reponse-actions" style="display: flex; gap: 8px;">
                                    <a href="modifier_reponse.php?id=<?= $rep['Id_reponse'] ?>&reclamation_id=<?= $reclamationId ?>"
                                        class="btn-edit-reponse" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="supprimer_reponse.php?id=<?= $rep['Id_reponse'] ?>&reclamation_id=<?= $reclamationId ?>"
                                        class="btn-delete-reponse"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')"
                                        title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="reponse-content">
                            <?= nl2br(htmlspecialchars($rep['message'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>